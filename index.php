<?php
require_once 'config.php';
$pageTitle = 'Share your world with a tap';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'head.php'; ?>
</head>
<body>
    <?php include 'nav.php'; ?>

    <main>
        <section class="container hero">
            <div class="fade-in">
                <span class="eyebrow"><i class="fas fa-bolt"></i> Your digital business card</span>
                <h1>Connect with<br>just a <span class="accent">tap.</span></h1>
                <p class="hero-lead">Create one profile with all your links, put it on an NFC card or QR code, and share it in a second. No app needed.</p>
                <div class="hero-cta">
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-primary btn-lg">Open dashboard <i class="fas fa-arrow-right"></i></a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary btn-lg">Create free profile <i class="fas fa-arrow-right"></i></a>
                        <a href="login.php" class="btn btn-secondary btn-lg">Log in</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hero-visual fade-in" style="animation-delay: .1s">
                <div class="card mock" aria-hidden="true">
                    <div class="mock-tap"><i class="fas fa-wifi fa-rotate-90"></i></div>
                    <div class="mock-avatar">JD</div>
                    <h3>Jane Doe</h3>
                    <p>Product designer · Dhaka</p>
                    <div class="icon-row">
                        <span class="icon-btn"><i class="fab fa-instagram"></i></span>
                        <span class="icon-btn"><i class="fab fa-linkedin-in"></i></span>
                        <span class="icon-btn"><i class="fab fa-github"></i></span>
                        <span class="icon-btn"><i class="fab fa-x-twitter"></i></span>
                    </div>
                    <span class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> Save contact</span>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-head">
                    <h2>Everything in one link</h2>
                    <p>A simple profile that works on every phone, built for real-world networking.</p>
                </div>
                <div class="features">
                    <div class="card feature">
                        <div class="feature-icon"><i class="fas fa-wifi fa-rotate-90"></i></div>
                        <h3>Tap to share</h3>
                        <p>Write your link to any NFC card or tag and share by tapping a phone.</p>
                    </div>
                    <div class="card feature">
                        <div class="feature-icon"><i class="fas fa-qrcode"></i></div>
                        <h3>QR code built in</h3>
                        <p>Every profile comes with a QR code for phones without NFC.</p>
                    </div>
                    <div class="card feature">
                        <div class="feature-icon"><i class="fas fa-address-card"></i></div>
                        <h3>One-tap save</h3>
                        <p>Visitors save your details, photo included, straight to their contacts.</p>
                    </div>
                    <div class="card feature">
                        <div class="feature-icon"><i class="fas fa-link"></i></div>
                        <h3>All your links</h3>
                        <p>Socials, GitHub, LeetCode, Codeforces, phone and email in one place.</p>
                    </div>
                    <div class="card feature">
                        <div class="feature-icon"><i class="fas fa-chart-simple"></i></div>
                        <h3>View insights</h3>
                        <p>See how many people opened your profile.</p>
                    </div>
                    <div class="card feature">
                        <div class="feature-icon"><i class="fas fa-mobile-screen"></i></div>
                        <h3>Mobile first</h3>
                        <p>Fast, clean and readable on any screen, in light or dark mode.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-head">
                    <h2>Ready in three steps</h2>
                </div>
                <div class="steps">
                    <div class="step">
                        <div class="step-num">1</div>
                        <h3>Sign up</h3>
                        <p>Create your free account in seconds.</p>
                    </div>
                    <div class="step">
                        <div class="step-num">2</div>
                        <h3>Add your info</h3>
                        <p>Upload a photo, write a bio and add your links.</p>
                    </div>
                    <div class="step">
                        <div class="step-num">3</div>
                        <h3>Share</h3>
                        <p>Tap your NFC card or show your QR code.</p>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!isLoggedIn()): ?>
        <section class="section">
            <div class="container">
                <div class="card cta-band">
                    <h2>Make your first impression count</h2>
                    <p>Free to use. No app needed.</p>
                    <a href="register.php" class="btn btn-primary btn-lg">Get started <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <span>&copy; <?php echo date('Y'); ?> NFC Hub</span>
            <span><a href="login.php">Log in</a> &nbsp;·&nbsp; <a href="register.php">Sign up</a></span>
        </div>
    </footer>
</body>
</html>
