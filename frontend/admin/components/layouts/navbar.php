<?php
/**
 * SOUND Group — Navbar Component (PHP)
 */

$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$adminInitial = strtoupper(mb_substr($adminName, 0, 1));
$adminProfileImage = isset($_SESSION['admin_profile_image']) ? $_SESSION['admin_profile_image'] : null;

$baseUrl = '/Aptech_E_Project_02/sound_management';
$logoutUrl = $baseUrl . '/frontend/admin/authentication/logout.php';

// Normalize profile image path to always be absolute
if ($adminProfileImage && strpos($adminProfileImage, '/') !== 0) {
    $adminProfileImage = $baseUrl . '/' . ltrim($adminProfileImage, '/');
}

require_once __DIR__ . '/../../../../backend/includes/website-settings.php';
$wsNavbar = getWebsiteSettings();
$wsNavbarName = htmlspecialchars($wsNavbar['website_name']);
$wsNavbarLogo = $wsNavbar['site_logo'];
?>
<div class="admin-navbar__left">
    <button type="button" class="admin-navbar__toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>
    <div class="admin-navbar__brand">
        <div class="admin-navbar__brand-icon">
            <?php if ($wsNavbarLogo): ?>
                <img src="<?php echo htmlspecialchars($wsNavbarLogo); ?>" alt="<?php echo $wsNavbarName; ?>" style="width:22px;height:22px;object-fit:contain;">
            <?php else: ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18V5l12-2v13"/>
                    <circle cx="6" cy="18" r="3"/>
                    <circle cx="18" cy="16" r="3"/>
                </svg>
            <?php endif; ?>
        </div>
        <div class="admin-navbar__brand-text">
            <span class="admin-navbar__brand-name"><?php echo $wsNavbarName; ?></span>
            <span class="admin-navbar__brand-label">Admin Panel</span>
        </div>
    </div>
</div>

<div class="admin-navbar__center">
    <div class="admin-navbar__search">
        <span class="admin-navbar__search-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </span>
        <input type="text" class="admin-navbar__search-input" placeholder="Search...">
    </div>
    <span class="admin-navbar__date" id="adminNavbarDate"></span>
    <span class="admin-navbar__time" id="adminNavbarTime"></span>
</div>

<div class="admin-navbar__right">
    <!-- Profile Dropdown -->
    <div class="admin-navbar__profile" id="adminProfileDropdown">
        <button type="button" class="admin-navbar__profile-toggle" id="adminProfileToggle" aria-haspopup="true" aria-expanded="false">
            <span class="admin-navbar__avatar">
                <?php if ($adminProfileImage): ?>
                    <img src="<?php echo htmlspecialchars($adminProfileImage); ?>" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                <?php else: ?>
                    <?php echo htmlspecialchars($adminInitial); ?>
                <?php endif; ?>
            </span>
            <span class="admin-navbar__name"><?php echo htmlspecialchars($adminName); ?></span>
            <span class="admin-navbar__arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </span>
        </button>

        <div class="admin-navbar__dropdown" id="adminProfileMenu">
            <a href="#" class="admin-navbar__dropdown-item" id="myProfileTrigger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                My Profile
            </a>
            <a href="#" class="admin-navbar__dropdown-item" id="changePasswordTrigger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Change Password
            </a>
            <a href="#" class="admin-navbar__dropdown-item" id="changeEmailTrigger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                Change Email
            </a>
            <div class="admin-navbar__dropdown-divider"></div>
            <form method="POST" action="<?php echo $logoutUrl; ?>">
                <button type="submit" class="admin-navbar__dropdown-item admin-navbar__dropdown-item--logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
