<?php
if (!isset($baseUrl)) {
    $baseUrl = '/Aptech_E_Project_02/sound_management';
}
if (!isset($websiteBase)) {
    $websiteBase = $baseUrl . '/frontend/website';
}
if (!isset($currentPage)) {
    $currentPage = 'home';
}

require_once dirname(__DIR__, 5) . '/backend/includes/website-settings.php';
$ws = getWebsiteSettings();
$wsWebsiteName = htmlspecialchars($ws['website_name']);
$wsLogoPath    = $ws['site_logo'];

require_once dirname(__DIR__, 5) . '/backend/includes/user-auth.php';
$siteUserLoggedIn = isUserLoggedIn();
$siteUserName = $siteUserLoggedIn ? getCurrentUserName() : '';
$siteUserImage = $siteUserLoggedIn ? getCurrentUserProfileImage() : null;
$siteUserInitial = $siteUserLoggedIn ? strtoupper(mb_substr($siteUserName, 0, 1)) : '';
$siteUserInitialColor = ['#8b5cf6', '#ec4899', '#06b6d4', '#f59e0b', '#10b981', '#ef4444'];
$siteUserColorIndex = $siteUserLoggedIn ? (ord($siteUserName[0]) % count($siteUserInitialColor)) : 0;
$siteUserAvatarColor = $siteUserLoggedIn ? $siteUserInitialColor[$siteUserColorIndex] : '#8b5cf6';
$logoutHandlerUrl = $baseUrl . '/backend/handlers/user-logout-handler.php';
$profileHandlerUrl = $baseUrl . '/backend/handlers/user-profile-handler.php';

$homeHref = ($currentPage === 'home') ? '#hero' : $websiteBase . '/index.php';
$musicHref = $websiteBase . '/music/music.php';
$videosHref = $websiteBase . '/video/video.php';
$searchHref = $websiteBase . '/search/search.php';
$aboutHref = $websiteBase . '/about/about.php';
$contactHref = $websiteBase . '/contact/contact.php';
$signupModalCss = $websiteBase . '/components/signup_modal/signup_modal.css';
$signupModalJs = $websiteBase . '/components/signup_modal/signup_modal.js';
$loginModalJs = $websiteBase . '/components/login_modal/login_modal.js';
$profileModalCss = $websiteBase . '/components/profile_modal/profile_modal.css';
$profileModalJs = $websiteBase . '/components/profile_modal/profile_modal.js';
$notificationCss = $websiteBase . '/css/components/notifications/notification.css';
$notificationJs = $websiteBase . '/js/components/notifications/notification.js';
$buttonSpinnerCss = $websiteBase . '/css/components/loaders/button-spinner.css';
$buttonSpinnerJs = $websiteBase . '/js/components/loaders/button-spinner.js';
?>
<!-- HEADER / NAVBAR -->
<header class="wg-header" id="wgHeader">
    <div class="wg-header__inner">
        <a href="<?php echo $websiteBase; ?>/index.php" class="wg-logo">
            <span class="wg-logo__icon">
                <?php if ($wsLogoPath): ?>
                    <img src="<?php echo htmlspecialchars($wsLogoPath); ?>" alt="<?php echo $wsWebsiteName; ?>" style="width:28px;height:28px;object-fit:contain;">
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M9 18V5l12-2v13" />
                        <circle cx="6" cy="18" r="3" />
                        <circle cx="18" cy="16" r="3" />
                    </svg>
                <?php endif; ?>
            </span>
            <span class="wg-logo__text"><?php echo $wsWebsiteName; ?></span>
        </a>

        <nav class="wg-nav" id="wgNav">
            <a href="<?php echo $homeHref; ?>"
                class="wg-nav__link <?php echo ($currentPage === 'home') ? 'wg-nav__link--active' : ''; ?>">Home</a>
            <a href="<?php echo $musicHref; ?>"
                class="wg-nav__link <?php echo ($currentPage === 'music') ? 'wg-nav__link--active' : ''; ?>">Music</a>
            <a href="<?php echo $videosHref; ?>"
                class="wg-nav__link <?php echo ($currentPage === 'videos') ? 'wg-nav__link--active' : ''; ?>">Videos</a>
            <a href="<?php echo $searchHref; ?>"
                class="wg-nav__link <?php echo ($currentPage === 'search') ? 'wg-nav__link--active' : ''; ?>">Search</a>
            <a href="<?php echo $aboutHref; ?>"
                class="wg-nav__link <?php echo ($currentPage === 'about') ? 'wg-nav__link--active' : ''; ?>">About</a>
            <a href="<?php echo $contactHref; ?>"
                class="wg-nav__link <?php echo ($currentPage === 'contact') ? 'wg-nav__link--active' : ''; ?>">Contact</a>
        </nav>

        <div class="wg-header__actions">
            <?php if ($siteUserLoggedIn): ?>
                <div class="wg-user-menu" id="wgUserMenu">
                    <button class="wg-user-menu__trigger" type="button" aria-expanded="false" aria-label="User menu">
                        <?php if ($siteUserImage): ?>
                            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($siteUserImage); ?>"
                                alt="<?php echo htmlspecialchars($siteUserName); ?>" class="wg-user-menu__avatar">
                        <?php else: ?>
                            <span class="wg-user-menu__initial"
                                style="background-color:<?php echo $siteUserAvatarColor; ?>;"><?php echo $siteUserInitial; ?></span>
                        <?php endif; ?>
                        <span class="wg-user-menu__name"><?php echo htmlspecialchars($siteUserName); ?></span>
                        <svg class="wg-user-menu__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" width="16" height="16">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </button>
                    <div class="wg-user-menu__dropdown" id="wgUserDropdown">
                        <button class="wg-user-menu__item" id="wgUserProfileBtn" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"
                                height="16">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Profile
                        </button>
                        <button class="wg-user-menu__item wg-user-menu__item--logout" id="wgUserLogoutBtn" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"
                                height="16">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <a href="#" class="wg-btn wg-btn--ghost">Login</a>
                <a href="#" class="wg-btn wg-btn--primary">Sign Up</a>
            <?php endif; ?>
        </div>

        <button class="wg-header__toggle" id="wgMenuToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- MOBILE DRAWER OVERLAY -->
    <div class="wg-drawer-overlay" id="wgDrawerOverlay"></div>

    <!-- MOBILE DRAWER -->
    <div class="wg-drawer" id="wgDrawer">
        <div class="wg-drawer__header">
            <a href="<?php echo $websiteBase; ?>/index.php" class="wg-logo">
                <span class="wg-logo__icon">
                    <?php if ($wsLogoPath): ?>
                        <img src="<?php echo htmlspecialchars($wsLogoPath); ?>" alt="<?php echo $wsWebsiteName; ?>" style="width:28px;height:28px;object-fit:contain;">
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M9 18V5l12-2v13" />
                            <circle cx="6" cy="18" r="3" />
                            <circle cx="18" cy="16" r="3" />
                        </svg>
                    <?php endif; ?>
                </span>
                <span class="wg-logo__text"><?php echo $wsWebsiteName; ?></span>
            </a>
            <button class="wg-drawer__close" id="wgDrawerClose" aria-label="Close menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" width="20" height="20">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <nav class="wg-drawer__nav">
            <a href="<?php echo $homeHref; ?>"
                class="wg-drawer__link <?php echo ($currentPage === 'home') ? 'wg-drawer__link--active' : ''; ?>">Home</a>
            <a href="<?php echo $musicHref; ?>"
                class="wg-drawer__link <?php echo ($currentPage === 'music') ? 'wg-drawer__link--active' : ''; ?>">Music</a>
            <a href="<?php echo $videosHref; ?>"
                class="wg-drawer__link <?php echo ($currentPage === 'videos') ? 'wg-drawer__link--active' : ''; ?>">Videos</a>
            <a href="<?php echo $searchHref; ?>"
                class="wg-drawer__link <?php echo ($currentPage === 'search') ? 'wg-drawer__link--active' : ''; ?>">Search</a>
            <a href="<?php echo $aboutHref; ?>"
                class="wg-drawer__link <?php echo ($currentPage === 'about') ? 'wg-drawer__link--active' : ''; ?>">About</a>
            <a href="<?php echo $contactHref; ?>"
                class="wg-drawer__link <?php echo ($currentPage === 'contact') ? 'wg-drawer__link--active' : ''; ?>">Contact</a>
        </nav>

        <div class="wg-drawer__footer">
            <?php if ($siteUserLoggedIn): ?>
                <div class="wg-drawer__user">
                    <div class="wg-drawer__user-info">
                        <?php if ($siteUserImage): ?>
                            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($siteUserImage); ?>"
                                alt="<?php echo htmlspecialchars($siteUserName); ?>" class="wg-drawer__user-avatar">
                        <?php else: ?>
                            <span class="wg-drawer__user-initial"
                                style="background-color:<?php echo $siteUserAvatarColor; ?>;"><?php echo $siteUserInitial; ?></span>
                        <?php endif; ?>
                        <div class="wg-drawer__user-details">
                            <span class="wg-drawer__user-name"><?php echo htmlspecialchars($siteUserName); ?></span>
                        </div>
                    </div>
                    <div class="wg-drawer__user-actions">
                        <button class="wg-btn wg-btn--ghost wg-btn--block" id="wgDrawerProfileBtn" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Profile
                        </button>
                        <button class="wg-btn wg-btn--ghost wg-btn--block wg-drawer__logout-btn" id="wgDrawerLogoutBtn"
                            type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="wg-drawer__auth">
                    <button class="wg-btn wg-btn--ghost wg-btn--block wg-drawer__login-btn" id="wgDrawerLoginBtn" type="button">Login</button>
                    <button class="wg-btn wg-btn--primary wg-btn--block wg-drawer__signup-btn" id="wgDrawerSignupBtn" type="button">Sign Up</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php include __DIR__ . '/../../signup_modal/signup_modal.php'; ?>
<script src="<?php echo $signupModalJs; ?>"></script>
<?php include __DIR__ . '/../../login_modal/login_modal.php'; ?>
<script src="<?php echo $loginModalJs; ?>"></script>
<?php if ($siteUserLoggedIn): ?>
    <?php include __DIR__ . '/../../profile_modal/profile_modal.php'; ?>
    <script src="<?php echo $profileModalJs; ?>"></script>
<?php endif; ?>
<?php include __DIR__ . '/../../notifications/notification.php'; ?>
<script src="<?php echo $notificationJs; ?>"></script>
<?php include __DIR__ . '/../../loaders/button-spinner.php'; ?>
<script src="<?php echo $buttonSpinnerJs; ?>"></script>
<?php if ($siteUserLoggedIn): ?>
    <script>
        (function () {
            'use strict';

            // User menu dropdown toggle (desktop)
            var trigger = document.getElementById('wgUserMenu') ? document.getElementById('wgUserMenu').querySelector('.wg-user-menu__trigger') : null;
            var dropdown = document.getElementById('wgUserDropdown');
            if (trigger && dropdown) {
                trigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var expanded = trigger.getAttribute('aria-expanded') === 'true';
                    trigger.setAttribute('aria-expanded', !expanded);
                    dropdown.classList.toggle('is-open');
                });
                document.addEventListener('click', function () {
                    trigger.setAttribute('aria-expanded', 'false');
                    dropdown.classList.remove('is-open');
                });
                dropdown.addEventListener('click', function (e) { e.stopPropagation(); });
            }

            // Logout handler
            function handleLogout(e) {
                e.preventDefault();
                closeDrawer();
                var btn = e.currentTarget;
                startButtonLoading(btn, 'Logging out...');
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $logoutHandlerUrl; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.responseType = 'json';
                xhr.onload = function () {
                    showSuccess('You have been logged out.');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                };
                xhr.onerror = function () {
                    stopButtonLoading(btn);
                    showError('Logout failed. Please try again.');
                };
                xhr.send('action=logout');
            }

            var logoutBtn = document.getElementById('wgUserLogoutBtn');
            var logoutBtnMobile = document.getElementById('wgDrawerLogoutBtn');
            if (logoutBtn) logoutBtn.addEventListener('click', handleLogout);
            if (logoutBtnMobile) logoutBtnMobile.addEventListener('click', handleLogout);

            // Profile button (drawer)
            var profileBtnDrawer = document.getElementById('wgDrawerProfileBtn');
            if (profileBtnDrawer) {
                profileBtnDrawer.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeDrawer();
                    if (typeof window.openProfileModal === 'function') {
                        window.openProfileModal();
                    }
                });
            }

            // Profile sync — fetch latest user data from DB (not session)
            function syncUserProfile() {
                var fd = new FormData();
                fd.append('action', 'sync');
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $profileHandlerUrl; ?>', true);
                xhr.responseType = 'json';
                xhr.onload = function () {
                    var r = xhr.response;
                    if (!r || !r.success || !r.user) return;
                    var u = r.user;
                    var baseUrl = '<?php echo $baseUrl; ?>';
                    var bust = '?v=' + Date.now();

                    // Update navbar name (desktop + drawer)
                    var nameEls = document.querySelectorAll('.wg-user-menu__name, .wg-drawer__user-name');
                    nameEls.forEach(function (el) { el.textContent = u.full_name; });

                    // Update navbar avatar images with cache-bust
                    if (u.profile_image) {
                        var src = u.profile_image.indexOf('/') === 0 ? u.profile_image : '/' + u.profile_image;
                        var newUrl = baseUrl + src + bust;
                        var imgs = document.querySelectorAll('.wg-user-menu__avatar, .wg-drawer__user-avatar');
                        imgs.forEach(function (img) { img.src = newUrl; });
                    }

                    // Update initial avatar color when no image
                    var initials = document.querySelectorAll('.wg-user-menu__initial, .wg-drawer__user-initial');
                    if (!u.profile_image && initials.length) {
                        var initial = (u.full_name || '?').charAt(0).toUpperCase();
                        initials.forEach(function (el) { el.textContent = initial; });
                    }
                };
                xhr.send(fd);
            }

            // Sync on page load
            syncUserProfile();

            // Sync when tab becomes visible again
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) syncUserProfile();
            });
        })();
    </script>
<?php endif; ?>
<script>
    (function () {
        'use strict';

        var toggle = document.getElementById('wgMenuToggle');
        var drawer = document.getElementById('wgDrawer');
        var overlay = document.getElementById('wgDrawerOverlay');
        var closeBtn = document.getElementById('wgDrawerClose');
        var drawerLinks = drawer ? drawer.querySelectorAll('.wg-drawer__link, .wg-drawer__auth button') : [];

        function openDrawer() {
            if (!drawer || !overlay) return;
            drawer.classList.add('is-open');
            overlay.classList.add('is-open');
            if (toggle) toggle.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            if (!drawer || !overlay) return;
            drawer.classList.remove('is-open');
            overlay.classList.remove('is-open');
            if (toggle) toggle.classList.remove('is-active');
            document.body.style.overflow = '';
        }

        // Expose for logged-in script
        window.closeDrawer = closeDrawer;

        if (toggle) {
            toggle.addEventListener('click', function () {
                if (drawer && drawer.classList.contains('is-open')) {
                    closeDrawer();
                } else {
                    openDrawer();
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeDrawer);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeDrawer);
        }

        // Close on link click
        drawerLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                closeDrawer();
            });
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) {
                closeDrawer();
            }
        });

        // Guest login/signup buttons in drawer
        var drawerLoginBtn = document.getElementById('wgDrawerLoginBtn');
        var drawerSignupBtn = document.getElementById('wgDrawerSignupBtn');
        if (drawerLoginBtn) {
            drawerLoginBtn.addEventListener('click', function () {
                closeDrawer();
                if (typeof window.openLoginModal === 'function') {
                    window.openLoginModal();
                }
            });
        }
        if (drawerSignupBtn) {
            drawerSignupBtn.addEventListener('click', function () {
                closeDrawer();
                if (typeof window.openSignupModal === 'function') {
                    window.openSignupModal();
                }
            });
        }
    })();
</script>