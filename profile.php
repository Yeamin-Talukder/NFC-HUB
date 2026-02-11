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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['full_name']); ?> - NFC Profile</title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #0f0f0f;
            display: flex;
            align-items: center; /* Vertically center */
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .profile-container {
            width: 100%;
            max-width: 800px; /* Wider for landscape */
            padding: 3rem;
            position: relative;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 3rem;
            align-items: start;
            text-align: left;
        }
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding-right: 2rem;
            height: 100%;
        }
        .profile-content {
            width: 100%;
        }
        .avatar-container {
            width: 150px;
            height: 150px;
            margin-bottom: 1.5rem;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--accent-color);
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
        }
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .user-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #fff, var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .user-bio {
            color: #ccc;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-top: 10px;
        }
        .save-contact-btn {
            display: inline-block;
            width: 100%;
            padding: 12px 20px;
            background: var(--accent-color);
            color: black;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
        }
        .save-contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.4);
            color: #fff;
        }
        .social-links {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .social-btn {
            display: flex;
            align-items: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.9rem;
        }
        .social-btn:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 136, 0.3);
        }
        .social-btn i {
            margin-right: 10px;
            font-size: 1.2rem;
            color: var(--accent-color);
            transition: 0.3s;
        }
        .social-btn:hover i {
            color: #fff;
        }
        /* Responsive scaling */
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
                padding: 2rem;
            }
            .profile-sidebar {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                padding-right: 0;
                padding-bottom: 2rem;
            }
            .social-links {
                grid-template-columns: 1fr;
            }
        }
    </style>
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="profile-container fade-in">
        <!-- Sidebar: Identity -->
        <div class="profile-sidebar">
            <div class="avatar-container">
                <?php 
                    $avatar = !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name='.urlencode($user['full_name']).'&background=0D8ABC&color=fff';
                ?>
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar-img">
            </div>

            <h1 class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p class="user-bio"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
            
            <form id="vcardForm" action="vcard.php?id=<?php echo $user['id']; ?>" method="POST" style="display:none;">
                <input type="hidden" name="avatar_b64" id="vcard_avatar_input">
            </form>
            
            <button onclick="downloadResizedVCard()" class="save-contact-btn border-none cursor-pointer w-full">
                <i class="fas fa-address-book" style="margin-right:8px;"></i> Save Contact
            </button>
            <p id="vcard-loading" style="display:none; text-align:center; color:#888; font-size:0.8rem; margin-top:5px;">Compressing image...</p>

            <script>
                function downloadResizedVCard() {
                    const btn = document.querySelector('.save-contact-btn');
                    const loading = document.getElementById('vcard-loading');
                    const form = document.getElementById('vcardForm');
                    const avatarImg = document.querySelector('.avatar-img');
                    const input = document.getElementById('vcard_avatar_input');

                    // If no avatar image found or it's remote svg, just submit
                    if (!avatarImg || avatarImg.src.includes('ui-avatars')) {
                        form.submit();
                        return;
                    }

                    // Visual Feedback
                    btn.style.opacity = '0.7';
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing...';
                    loading.style.display = 'block';

                    // Create Canvas for Resizing
                    const img = new Image();
                    img.crossOrigin = "Anonymous"; // Try checking cross-origin
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        const maxDim = 300;
                        let width = img.width;
                        let height = img.height;

                        // Calculate aspect ratio
                        if (width > height) {
                            if (width > maxDim) {
                                height *= maxDim / width;
                                width = maxDim;
                            }
                        } else {
                            if (height > maxDim) {
                                width *= maxDim / height;
                                height = maxDim;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        
                        // Fill white background (for transparent PNGs)
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, width, height);
                        
                        // Draw image
                        ctx.drawImage(img, 0, 0, width, height);

                        // Export as JPEG 70%
                        const dataUrl = canvas.toDataURL('image/jpeg', 0.7);
                        input.value = dataUrl;

                        // Submit
                        form.submit();

                        // Reset UI
                        setTimeout(() => {
                            btn.style.opacity = '1';
                            btn.innerHTML = '<i class="fas fa-address-book" style="margin-right:8px;"></i> Save Contact';
                            loading.style.display = 'none';
                        }, 2000);
                    };

                    img.onerror = function() {
                        // Fallback if CORS or load fails
                        form.submit(); 
                    };

                    img.src = avatarImg.src;
                }
            </script>
        </div>

        <!-- Content: Links -->
        <div class="profile-content">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom:10px; color: #fff;">Connect Me</h3>
            <div class="social-links">
                <?php 
                function formatUrl($url) {
                    if (empty($url)) return '';
                    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                        return "https://" . $url;
                    }
                    return $url;
                }
                ?>
                
                <?php if(!empty($user['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user['instagram'])); ?>" target="_blank" class="social-btn"><i class="fab fa-instagram"></i> Instagram</a>
                <?php endif; ?>

                <?php if(!empty($user['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user['facebook'])); ?>" target="_blank" class="social-btn"><i class="fab fa-facebook"></i> Facebook</a>
                <?php endif; ?>
                
                <?php if(!empty($user['linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user['linkedin'])); ?>" target="_blank" class="social-btn"><i class="fab fa-linkedin"></i> LinkedIn</a>
                <?php endif; ?>
                
                <?php if(!empty($user['twitter'])): ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user['twitter'])); ?>" target="_blank" class="social-btn"><i class="fab fa-twitter"></i> Twitter/X</a>
                <?php endif; ?>
                
                <?php if(!empty($user['phone'])): ?>
                    <a href="tel:<?php echo htmlspecialchars($user['phone']); ?>" class="social-btn"><i class="fas fa-phone"></i> Call / WhatsApp</a>
                <?php endif; ?>
                
                <?php if(!empty($user['github'])): ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user['github'])); ?>" target="_blank" class="social-btn"><i class="fab fa-github"></i> GitHub</a>
                <?php endif; ?>
                
                <?php if(!empty($user['codeforces'])): ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user['codeforces'])); ?>" target="_blank" class="social-btn"><i class="fas fa-code"></i> Codeforces</a>
                <?php endif; ?>
                
                <?php if(!empty($user['leetcode'])): ?>
                    <a href="<?php echo htmlspecialchars(formatUrl($user['leetcode'])); ?>" target="_blank" class="social-btn"><i class="fas fa-terminal"></i> LeetCode</a>
                <?php endif; ?>
                
                <button onclick="handleEmailClick('<?php echo htmlspecialchars($user['email']); ?>', this)" class="social-btn" style="border:none; cursor:pointer; font-family:inherit; font-size:0.9rem;">
                    <i class="fas fa-envelope"></i> <span>Email Me</span>
                </button>
            </div>
            
            <script>
                function handleEmailClick(email, btn) {
                    const originalContent = btn.innerHTML;
                    
                    // Visual feedback function
                    const showFeedback = () => {
                        btn.innerHTML = '<i class="fas fa-check"></i> <span>Opening...</span>';
                        btn.style.background = 'var(--accent-color)';
                        btn.style.color = '#fff';
                        setTimeout(() => {
                            btn.innerHTML = originalContent;
                            btn.style.background = ''; 
                            btn.style.color = '';
                        }, 2000);
                    };

                    showFeedback();

                    // 1. Try to copy (Enhancement, failing silently is ok)
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(email).catch(err => {});
                    }

                    // 2. Open Mail Client (Primary Action for Mobile)
                    // Use a small timeout to let the UI update first
                    setTimeout(() => {
                        window.location.href = 'mailto:' + email;
                    }, 300);
                }
            </script>
            </div>
            
            <footer style="margin-top: 3rem; color: #555; font-size: 0.8rem; text-align: center;">
                <p>Powered by <a href="index.php" style="color:#555;">NFC Hub</a></p>
            </footer>
        </div>
    </div>
    <script src="assets/js/bg-animation.js"></script>
</body>
</html>
