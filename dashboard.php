<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $bio = sanitize($_POST['bio']);
    $instagram = sanitize($_POST['instagram']);
    $linkedin = sanitize($_POST['linkedin']);
    $twitter = sanitize($_POST['twitter']);

    // Sanitize phone: Remove all characters except digits and +
    $phone = preg_replace('/[^0-9+]/', '', $_POST['phone']);
    $github = sanitize($_POST['github']);
    $codeforces = sanitize($_POST['codeforces']);
    $leetcode = sanitize($_POST['leetcode']);
    $user_id = $_SESSION['user_id'];
    
    // START Image Upload Logic
    $avatar = null;
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $fileTmpPath = $_FILES['avatar_file']['tmp_name'];
        $fileName = $_FILES['avatar_file']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Fetch old avatar to delete
                $stmt_old = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
                $stmt_old->execute([$user_id]);
                $old_user_data = $stmt_old->fetch();
                
                if ($old_user_data && !empty($old_user_data['avatar'])) {
                    $old_avatar_path = $old_user_data['avatar'];
                    // Only delete if it's a local file in uploads directory
                    if (file_exists($old_avatar_path) && strpos($old_avatar_path, 'uploads/') !== false) {
                        @unlink($old_avatar_path);
                    }
                }

                $avatar = $dest_path; 
            }
        }
    }
    // END Image Upload Logic

    try {
        // Dynamic Update Query
        $sql = "UPDATE users SET full_name = ?, bio = ?, instagram = ?, facebook = ?, linkedin = ?, twitter = ?, phone = ?, github = ?, codeforces = ?, leetcode = ?";
        $params = [$full_name, $bio, $instagram, sanitize($_POST['facebook']), $linkedin, $twitter, $phone, $github, $codeforces, $leetcode];
        
        if ($avatar) {
            $sql .= ", avatar = ?";
            $params[] = $avatar;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $user_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $success_msg = "Profile updated successfully!";
    } catch (PDOException $e) {
        $error_msg = "Error updating profile: " . $e->getMessage();
    }
}

// Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch View Count
$stmt = $pdo->prepare("SELECT COUNT(*) as views FROM profile_views WHERE user_id = ?");
$stmt->execute([$user_id]);
$viewCount = $stmt->fetch()['views'];

$baseDir = dirname($_SERVER['PHP_SELF']);
$baseDir = str_replace('\\', '/', $baseDir); // Fix for Windows paths acts as URL
$profileUrl = "http://" . $_SERVER['HTTP_HOST'] . $baseDir . "/profile.php?id=" . $user['slug'];

?>
<?php
$pageTitle = 'Dashboard';
$avatarUrl = avatarUrl($user['avatar'], $user['full_name'] ?: $user['username']);
$socialInputs = [
    ['name' => 'instagram',  'icon' => 'fab fa-instagram',   'label' => 'Instagram'],
    ['name' => 'facebook',   'icon' => 'fab fa-facebook-f',  'label' => 'Facebook'],
    ['name' => 'linkedin',   'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'],
    ['name' => 'twitter',    'icon' => 'fab fa-x-twitter',   'label' => 'X / Twitter'],
    ['name' => 'github',     'icon' => 'fab fa-github',      'label' => 'GitHub'],
    ['name' => 'codeforces', 'icon' => 'fas fa-code',        'label' => 'Codeforces'],
    ['name' => 'leetcode',   'icon' => 'fas fa-terminal',    'label' => 'LeetCode'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'head.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <?php include 'nav.php'; ?>

    <main class="page">
        <div class="container">
            <div class="page-head fade-in">
                <div>
                    <h1>Hi, <?php echo htmlspecialchars(explode(' ', $user['full_name'] ?: $user['username'])[0]); ?> 👋</h1>
                    <p>Edit your profile and share it anywhere.</p>
                </div>
                <a href="<?php echo htmlspecialchars($profileUrl); ?>" target="_blank" class="btn btn-secondary btn-sm hide-sm">
                    <i class="fas fa-arrow-up-right-from-square"></i> View profile
                </a>
            </div>

            <div class="tabs" role="tablist">
                <button class="tab active" data-tab="edit" role="tab"><i class="fas fa-pen"></i> Edit</button>
                <button class="tab" data-tab="share" role="tab"><i class="fas fa-qrcode"></i> Share</button>
            </div>

            <div class="dash">
                <aside class="dash-aside" data-panel="share">
                    <div class="card stat">
                        <div class="stat-icon"><i class="fas fa-eye"></i></div>
                        <div>
                            <div class="stat-value"><?php echo number_format($viewCount); ?></div>
                            <div class="stat-label">Profile views</div>
                        </div>
                    </div>

                    <div class="card card-pad share-card">
                        <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="" class="share-avatar">
                        <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <p class="subtle" style="font-size:.85rem">Scan or tap to open profile</p>

                        <div class="qr" id="qrcode"></div>

                        <div class="field" style="text-align:left">
                            <label class="label" for="profileLink">Public link</label>
                            <div class="copy-row">
                                <input class="input" type="text" id="profileLink" value="<?php echo htmlspecialchars($profileUrl); ?>" readonly>
                                <button type="button" class="icon-btn" style="height:44px;width:44px;flex-shrink:0" data-copy="profileLink" aria-label="Copy link"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($profileUrl); ?>" target="_blank" class="btn btn-secondary btn-block" style="margin-top:12px">
                            <i class="fas fa-arrow-up-right-from-square"></i> Open live profile
                        </a>
                    </div>
                </aside>

                <section class="card card-pad active" data-panel="edit">
                    <?php if (isset($success_msg)): ?>
                        <div class="alert alert-success" style="margin-bottom:20px"><i class="fas fa-circle-check"></i> <?php echo $success_msg; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_msg)): ?>
                        <div class="alert alert-error" style="margin-bottom:20px"><i class="fas fa-circle-exclamation"></i> <?php echo htmlspecialchars($error_msg); ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">

                        <h2 class="section-title"><i class="fas fa-user"></i> Basic info</h2>

                        <div class="form-grid">
                            <div class="avatar-upload">
                                <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Profile photo" id="avatarPreview">
                                <div>
                                    <label for="avatarFile" class="btn btn-secondary btn-sm"><i class="fas fa-camera"></i> Change photo</label>
                                    <input type="file" id="avatarFile" name="avatar_file" accept="image/*" class="file-input">
                                    <div class="file-name" id="fileName">JPG, PNG, GIF or WEBP</div>
                                </div>
                            </div>

                            <div class="form-grid-2">
                                <div class="field">
                                    <label class="label" for="full_name">Full name</label>
                                    <input class="input" type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                <div class="field">
                                    <label class="label" for="phone">Phone (call / WhatsApp)</label>
                                    <div class="input-icon">
                                        <i class="fas fa-phone"></i>
                                        <input class="input" type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                               placeholder="+8801XXXXXXXXX" oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="bio">Bio</label>
                                <textarea class="textarea" id="bio" name="bio" rows="3" placeholder="A short line about you"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <hr class="divider">

                        <h2 class="section-title"><i class="fas fa-link"></i> Links</h2>
                        <div class="form-grid-2">
                            <?php foreach ($socialInputs as $s): ?>
                                <div class="field">
                                    <label class="label" for="<?php echo $s['name']; ?>"><?php echo $s['label']; ?></label>
                                    <div class="input-icon">
                                        <i class="<?php echo $s['icon']; ?>"></i>
                                        <input class="input" type="text" id="<?php echo $s['name']; ?>" name="<?php echo $s['name']; ?>"
                                               value="<?php echo htmlspecialchars($user[$s['name']] ?? ''); ?>" placeholder="https://">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Save changes</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <script>
        new QRCode(document.getElementById('qrcode'), {
            text: <?php echo json_encode($profileUrl); ?>,
            width: 148,
            height: 148
        });

        document.getElementById('avatarFile').addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) return;
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('avatarPreview').src = URL.createObjectURL(file);
        });
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
