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
$homeHref = ($currentPage === 'home') ? '#hero' : $websiteBase . '/index.php';
$musicHref = $websiteBase . '/music/music.php';
$videosHref = $websiteBase . '/video/video.php';
if ($currentPage === 'home') {
    $videosHref = '#videos';
}
?>
<!-- HEADER / NAVBAR -->
<header class="wg-header" id="wgHeader">
    <div class="wg-header__inner">
        <a href="<?php echo $websiteBase; ?>/index.php" class="wg-logo">
            <span class="wg-logo__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18V5l12-2v13"/>
                    <circle cx="6" cy="18" r="3"/>
                    <circle cx="18" cy="16" r="3"/>
                </svg>
            </span>
            <span class="wg-logo__text">Sound Group</span>
        </a>

        <nav class="wg-nav" id="wgNav">
            <a href="<?php echo $homeHref; ?>" class="wg-nav__link <?php echo ($currentPage === 'home') ? 'wg-nav__link--active' : ''; ?>">Home</a>
            <a href="<?php echo $musicHref; ?>" class="wg-nav__link <?php echo ($currentPage === 'music') ? 'wg-nav__link--active' : ''; ?>">Music</a>
            <a href="<?php echo $videosHref; ?>" class="wg-nav__link <?php echo ($currentPage === 'videos') ? 'wg-nav__link--active' : ''; ?>">Videos</a>
            <a href="#search" class="wg-nav__link">Search</a>
            <a href="#about" class="wg-nav__link">About</a>
            <a href="#contact" class="wg-nav__link">Contact</a>
        </nav>

        <div class="wg-header__actions">
            <a href="#" class="wg-btn wg-btn--ghost">Login</a>
            <a href="#" class="wg-btn wg-btn--primary">Sign Up</a>
        </div>

        <button class="wg-header__toggle" id="wgMenuToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <div class="wg-mobile-menu" id="wgMobileMenu">
        <nav class="wg-mobile-nav">
            <a href="<?php echo $homeHref; ?>" class="wg-mobile-nav__link <?php echo ($currentPage === 'home') ? 'wg-mobile-nav__link--active' : ''; ?>">Home</a>
            <a href="<?php echo $musicHref; ?>" class="wg-mobile-nav__link <?php echo ($currentPage === 'music') ? 'wg-mobile-nav__link--active' : ''; ?>">Music</a>
            <a href="<?php echo $videosHref; ?>" class="wg-mobile-nav__link <?php echo ($currentPage === 'videos') ? 'wg-mobile-nav__link--active' : ''; ?>">Videos</a>
            <a href="#search" class="wg-mobile-nav__link">Search</a>
            <a href="#about" class="wg-mobile-nav__link">About</a>
            <a href="#contact" class="wg-mobile-nav__link">Contact</a>
        </nav>
        <div class="wg-mobile-actions">
            <a href="#" class="wg-btn wg-btn--ghost wg-btn--block">Login</a>
            <a href="#" class="wg-btn wg-btn--primary wg-btn--block">Sign Up</a>
        </div>
    </div>
</header>
