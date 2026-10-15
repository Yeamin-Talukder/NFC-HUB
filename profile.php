<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    die("User not specified.");
}

$slug = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE slug = ?");
$stmt->execute([$slug]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// Increment View Counter
$stmt = $pdo->prepare("INSERT INTO profile_views (user_id) VALUES (?)");
$stmt->execute([$user['id']]);

// Generate vCard URL
$vcardUrl = "vcard.php?id=" . $user['id'];

?>
<?php
function formatUrl($url) {
    if (empty($url)) return '';
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) return "https://" . $url;
    return $url;
}

$avatar = avatarUrl($user['avatar'], $user['full_name'], 256);

$links = [
    ['key' => 'instagram',  'icon' => 'fab fa-instagram',   'label' => 'Instagram'],
    ['key' => 'facebook',   'icon' => 'fab fa-facebook-f',  'label' => 'Facebook'],
    ['key' => 'linkedin',   'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'],
    ['key' => 'twitter',    'icon' => 'fab fa-x-twitter',   'label' => 'X / Twitter'],
    ['key' => 'github',     'icon' => 'fab fa-github',      'label' => 'GitHub'],
    ['key' => 'codeforces', 'icon' => 'fas fa-code',        'label' => 'Codeforces'],
    ['key' => 'leetcode',   'icon' => 'fas fa-terminal',    'label' => 'LeetCode'],
];

$pageTitle = $user['full_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'head.php'; ?>
    <meta name="description" content="<?php echo htmlspecialchars(mb_substr($user['bio'] ?? '', 0, 150)); ?>">
</head>
<body>
    <main class="profile-wrap">
        <div class="profile fade-in">
            <div class="card profile-head">
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="<?php echo htmlspecialchars($user['full_name']); ?>" class="profile-avatar avatar-img">
                <h1 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <?php if (!empty($user['bio'])): ?>
                    <p class="profile-bio"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                <?php endif; ?>

                <form id="vcardForm" action="vcard.php?id=<?php echo $user['id']; ?>" method="POST" hidden>
                    <input type="hidden" name="avatar_b64" id="vcard_avatar_input">
                </form>

                <div class="profile-actions">
                    <button type="button" id="saveContactBtn" class="btn btn-primary btn-lg" onclick="downloadResizedVCard()">
                        <i class="fas fa-user-plus"></i> Save contact
                    </button>
                    <button type="button" class="icon-btn" style="width:50px;height:50px" onclick="shareProfile()" aria-label="Share profile">
                        <i class="fas fa-share-nodes"></i>
                    </button>
                </div>
            </div>

            <div class="links">
                <?php if (!empty($user['phone'])): ?>
                    <a href="tel:<?php echo htmlspecialchars($user['phone']); ?>" class="link-item">
                        <span class="link-icon"><i class="fas fa-phone"></i></span>
                        <span class="link-text">Call</span>
                        <i class="fas fa-chevron-right link-arrow"></i>
                    </a>
                    <a href="https://wa.me/<?php echo htmlspecialchars(ltrim($user['phone'], '+')); ?>" target="_blank" rel="noopener" class="link-item">
                        <span class="link-icon"><i class="fab fa-whatsapp"></i></span>
                        <span class="link-text">WhatsApp</span>
                        <i class="fas fa-chevron-right link-arrow"></i>
                    </a>
                <?php endif; ?>

                <?php foreach ($links as $l): if (empty($user[$l['key']])) continue; ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user[$l['key']])); ?>" target="_blank" rel="noopener" class="link-item">
                        <span class="link-icon"><i class="<?php echo $l['icon']; ?>"></i></span>
                        <span class="link-text"><?php echo $l['label']; ?></span>
                        <i class="fas fa-chevron-right link-arrow"></i>
                    </a>
                <?php endforeach; ?>

                <button type="button" class="link-item" onclick="handleEmailClick(<?php echo htmlspecialchars(json_encode($user['email'])); ?>)">
                    <span class="link-icon"><i class="fas fa-envelope"></i></span>
                    <span class="link-text">Email</span>
                    <i class="fas fa-chevron-right link-arrow"></i>
                </button>
            </div>

            <footer class="profile-foot">
                <a href="index.php"><i class="fas fa-wifi fa-rotate-90"></i> Made with NFC Hub</a>
            </footer>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
    <script>
        function downloadResizedVCard() {
            const btn = document.getElementById('saveContactBtn');
            const form = document.getElementById('vcardForm');
            const avatarImg = document.querySelector('.avatar-img');
            const input = document.getElementById('vcard_avatar_input');

            if (!avatarImg || avatarImg.src.includes('ui-avatars')) {
                form.submit();
                return;
            }

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing…';

            const reset = () => setTimeout(() => { btn.disabled = false; btn.innerHTML = original; }, 1500);

            // Shrink the photo client-side so the vCard stays small enough for phones
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            img.onload = function () {
                const maxDim = 300;
                let { width, height } = img;
                if (width > height && width > maxDim) { height *= maxDim / width; width = maxDim; }
                else if (height > maxDim) { width *= maxDim / height; height = maxDim; }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, width, height);
                ctx.drawImage(img, 0, 0, width, height);

                input.value = canvas.toDataURL('image/jpeg', 0.7);
                form.submit();
                reset();
            };
            img.onerror = function () { form.submit(); reset(); };
            img.src = avatarImg.src;
        }

        function handleEmailClick(email) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(email).catch(() => {});
            }
            showToast('Opening mail…', 'fa-envelope');
            setTimeout(() => { window.location.href = 'mailto:' + email; }, 300);
        }

        function shareProfile() {
            const data = { title: document.title, url: location.href };
            if (navigator.share) {
                navigator.share(data).catch(() => {});
            } else if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(location.href).then(() => showToast('Link copied'));
            } else {
                showToast(location.href, 'fa-link');
            }
        }
    </script>
</body>
</html>
