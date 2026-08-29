<?php
/**
 * SOUND Group — Sidebar Component (PHP)
 * Props: $activeItem (string)
 */

if (!isset($activeItem)) {
    $activeItem = '';
}

$baseUrl = baseUrl();
$dashboardUrl = $baseUrl . '/frontend/admin/dashboard/index.php';
$musicManagementUrl = $baseUrl . '/frontend/admin/music-management/index.php';
$videoManagementUrl = $baseUrl . '/frontend/admin/video-management/index.php';
$categoryManagementUrl = $baseUrl . '/frontend/admin/category-management/index.php';
$userManagementUrl = $baseUrl . '/frontend/admin/user-management/index.php';
$reviewsManagementUrl = $baseUrl . '/frontend/admin/review-management/index.php';
$websiteInfoUrl = $baseUrl . '/frontend/admin/website-info/index.php';
$contactMessagesUrl = $baseUrl . '/frontend/admin/contact-messages/index.php';
$logoutUrl = $baseUrl . '/frontend/admin/authentication/logout.php';

require_once __DIR__ . '/../../../../backend/includes/website-settings.php';
$wsSidebar = getWebsiteSettings();
$wsSidebarName  = htmlspecialchars($wsSidebar['website_name']);
$wsSidebarLogo  = $wsSidebar['site_logo'];
?>
<div class="sidebar-brand">
    <div class="sidebar-brand__icon">
        <?php if ($wsSidebarLogo): ?>
            <img src="<?php echo htmlspecialchars($wsSidebarLogo); ?>" alt="<?php echo $wsSidebarName; ?>" style="width:24px;height:24px;object-fit:contain;">
        <?php else: ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18V5l12-2v13"/>
                <circle cx="6" cy="18" r="3"/>
                <circle cx="18" cy="16" r="3"/>
            </svg>
        <?php endif; ?>
    </div>
    <div class="sidebar-brand__text">
        <span class="sidebar-brand__name"><?php echo $wsSidebarName; ?></span>
        <span class="sidebar-brand__label">Admin Panel</span>
    </div>
    <button type="button" class="sidebar-collapse-toggle" id="sidebarCollapseToggle" aria-label="Toggle sidebar">
        <span class="sidebar-collapse-toggle__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </span>
    </button>
</div>

<ul class="sidebar-nav">
    <li class="sidebar-nav__item">
        <a href="<?php echo $dashboardUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'dashboard' ? 'is-active' : ''; ?>"
           data-tooltip="Dashboard">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">Dashboard</span>
        </a>
    </li>
    <li class="sidebar-nav__item">
        <a href="<?php echo $musicManagementUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'music-management' ? 'is-active' : ''; ?>"
           data-tooltip="Music Management">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18V5l12-2v13"/>
                    <circle cx="6" cy="18" r="3"/>
                    <circle cx="18" cy="16" r="3"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">Music Management</span>
        </a>
    </li>
    <li class="sidebar-nav__item">
        <a href="<?php echo $videoManagementUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'video-management' ? 'is-active' : ''; ?>"
           data-tooltip="Video Management">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="23 7 16 12 23 17 23 7"/>
                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">Video Management</span>
        </a>
    </li>
    <li class="sidebar-nav__item">
        <a href="<?php echo $categoryManagementUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'category-management' ? 'is-active' : ''; ?>"
           data-tooltip="Category Management">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">Category Management</span>
        </a>
    </li>
    <li class="sidebar-nav__item">
        <a href="<?php echo $userManagementUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'user-management' ? 'is-active' : ''; ?>"
           data-tooltip="User Management">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">User Management</span>
        </a>
    </li>
    <li class="sidebar-nav__item">
        <a href="<?php echo $reviewsManagementUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'review-management' ? 'is-active' : ''; ?>"
           data-tooltip="Reviews &amp; Ratings">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">Reviews &amp; Ratings</span>
        </a>
    </li>
    <li class="sidebar-nav__item">
        <a href="<?php echo $websiteInfoUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'website-info' ? 'is-active' : ''; ?>"
           data-tooltip="Website Info">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">Website Info</span>
        </a>
    </li>
    <li class="sidebar-nav__item">
        <a href="<?php echo $contactMessagesUrl; ?>"
           class="sidebar-nav__link <?php echo $activeItem === 'contact-messages' ? 'is-active' : ''; ?>"
           data-tooltip="Contact Messages">
            <span class="sidebar-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </span>
            <span class="sidebar-nav__text">Contact Messages</span>
        </a>
    </li>
</ul>

<!-- Sidebar Bottom Actions -->
<div class="sidebar-bottom">
    <a href="<?php echo $baseUrl; ?>/frontend/website/index.php"
       target="_blank"
       class="sidebar-bottom__action sidebar-bottom__action--website"
       id="sidebarWebsiteVisit"
       aria-label="Website Visit"
       data-tooltip="Website Visit">
        <span class="sidebar-bottom__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
        </span>
        <span class="sidebar-bottom__text">Website Visit</span>
    </a>
    <form method="POST" action="<?php echo $logoutUrl; ?>" class="sidebar-bottom__form">
        <button type="submit"
                class="sidebar-bottom__action sidebar-bottom__action--logout"
                aria-label="Logout"
                data-tooltip="Logout">
            <span class="sidebar-bottom__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </span>
            <span class="sidebar-bottom__text">Logout</span>
        </button>
    </form>
</div>
