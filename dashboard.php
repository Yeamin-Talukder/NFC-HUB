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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NFC Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#00ff88',
                    }
                }
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-[#0f0f0f] text-white">
    <?php include 'nav.php'; ?>
    
    <div class="max-w-7xl mx-auto p-4 md:p-8 pt-24 md:pt-32">
        
        <!-- Mobile Tabs -->
        <div class="md:hidden flex mb-6 glass-panel rounded-xl p-1">
            <button onclick="switchTab('edit')" id="tab-edit" class="flex-1 py-3 rounded-lg text-sm font-bold transition-all bg-accent text-black">
                Edit Profile
            </button>
            <button onclick="switchTab('preview')" id="tab-preview" class="flex-1 py-3 rounded-lg text-sm font-bold text-gray-400 transition-all">
                Card Preview
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-[1fr_2fr] gap-8">
            
            <!-- Sidebar / Preview (Hidden on mobile by default unless tab selected) -->
            <div id="view-preview" class="hidden md:block">
                <div class="glass-panel p-6 rounded-2xl sticky top-24 text-center">
                    <h3 class="text-xl font-bold mb-2">Your NFC Card</h3>
                    <p class="text-gray-500 text-sm mb-6">Scan to view profile</p>
                    
                    <div class="flex justify-center mb-6">
                        <img src="<?php echo !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name='.$user['username']; ?>" 
                             class="w-24 h-24 rounded-full object-cover border-2 border-accent">
                    </div>

                    <div id="qrcode" class="bg-white p-4 rounded-xl inline-block"></div>
                    
                    <div class="mt-6">
                        <p class="text-accent font-bold text-lg">
                            <i class="fas fa-eye"></i> <?php echo number_format($viewCount); ?> Views
                        </p>
                    </div>
                    
                    <div class="mt-6 text-left">
                        <label class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Public Link</label>
                        <div class="flex gap-2 mt-2">
                            <input type="text" id="profileLink" value="<?php echo $profileUrl; ?>" readonly 
                                class="w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-300">
                            <button class="copy-btn px-3 py-2 bg-white/10 rounded-lg hover:bg-accent hover:text-black transition-colors" data-target="profileLink">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <a href="<?php echo $profileUrl; ?>" target="_blank" class="block w-full text-center mt-4 py-2 border border-accent text-accent rounded-lg hover:bg-accent hover:text-black transition-all font-semibold">
                            View Live Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content: Edit Form -->
            <div id="view-edit" class="block">
                <div class="glass-panel p-6 md:p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-3">
                        <i class="fas fa-user-edit text-accent"></i> Edit Profile
                    </h3>
                    
                    <?php if(isset($success_msg)) echo "<div class='bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-xl mb-6'>$success_msg</div>"; ?>
                    <?php if(isset($error_msg)) echo "<div class='bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6'>$error_msg</div>"; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <!-- Photo Upload -->
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Profile Photo</label>
                            <div class="flex items-center gap-4">
                                <img src="<?php echo !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name='.$user['username']; ?>" 
                                     class="w-16 h-16 rounded-full object-cover border border-white/10">
                                <input type="file" name="avatar_file" accept="image/*" 
                                    class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent file:text-black hover:file:bg-green-400 cursor-pointer">
                            </div>
                        </div>

                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm text-gray-400 mb-2">Full Name</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-2"><i class="fas fa-phone"></i> Phone Number (for WhatsApp/Call)</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="+1234567890"
                                       oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent transition-all">
                                <p class="text-xs text-gray-500 mt-1">Enter digits only (e.g. 01984493596)</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Bio</label>
                            <textarea name="bio" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent transition-all"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                        </div>

                        <div class="border-t border-white/10 pt-6">
                            <h4 class="text-accent font-bold mb-4">Social Links</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php
                                $socialInputs = [
                                    ['name'=>'instagram', 'icon'=>'fab fa-instagram', 'label'=>'Instagram'],
                                    ['name'=>'facebook', 'icon'=>'fab fa-facebook', 'label'=>'Facebook'],
                                    ['name'=>'linkedin', 'icon'=>'fab fa-linkedin', 'label'=>'LinkedIn'],
                                    ['name'=>'twitter', 'icon'=>'fab fa-twitter', 'label'=>'X / Twitter'],
                                    ['name'=>'github', 'icon'=>'fab fa-github', 'label'=>'GitHub'],
                                    ['name'=>'codeforces', 'icon'=>'fas fa-code', 'label'=>'Codeforces'],
                                    ['name'=>'leetcode', 'icon'=>'fas fa-terminal', 'label'=>'LeetCode']
                                ];
                                foreach($socialInputs as $s) {
                                    $val = htmlspecialchars($user[$s['name']] ?? '');
                                    echo "
                                    <div class='relative'>
                                        <div class='absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none'>
                                            <i class='{$s['icon']} text-gray-500'></i>
                                        </div>
                                        <input type='text' name='{$s['name']}' value='$val' placeholder='{$s['label']} URL'
                                            class='w-full bg-white/5 border border-white/10 rounded-lg pl-10 pr-4 py-3 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent transition-all placeholder-gray-600 text-sm'>
                                    </div>";
                                }
                                ?>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-accent text-black font-bold rounded-xl hover:shadow-[0_0_20px_rgba(0,255,136,0.4)] transition-all transform hover:-translate-y-1">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            const editView = document.getElementById('view-edit');
            const previewView = document.getElementById('view-preview');
            const tabEdit = document.getElementById('tab-edit');
            const tabPreview = document.getElementById('tab-preview');

            if (tab === 'edit') {
                editView.style.display = 'block';
                previewView.classList.add('hidden'); // Ensure hidden on mobile
                // Reset desktop state issues? No, responsive classes handle it.
                
                tabEdit.classList.add('bg-accent', 'text-black');
                tabEdit.classList.remove('text-gray-400');
                
                tabPreview.classList.remove('bg-accent', 'text-black');
                tabPreview.classList.add('text-gray-400');
            } else {
                editView.style.display = 'none';
                previewView.classList.remove('hidden');
                
                tabPreview.classList.add('bg-accent', 'text-black');
                tabPreview.classList.remove('text-gray-400');
                
                tabEdit.classList.remove('bg-accent', 'text-black');
                tabEdit.classList.add('text-gray-400');
            }
        }
    </script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo $profileUrl; ?>",
            width: 128,
            height: 128
        });
    </script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/bg-animation.js"></script>
</body>
</html>
