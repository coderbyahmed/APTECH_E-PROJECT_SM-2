<?php
if (!isset($baseUrl)) {
    $baseUrl = '/Aptech_E_Project_02/sound_management';
}
if (!isset($websiteBase)) {
    $websiteBase = $baseUrl . '/frontend/website';
}
require_once dirname(__DIR__, 5) . '/backend/includes/website-settings.php';
$ws = getWebsiteSettings();
$wsWebsiteName  = htmlspecialchars($ws['website_name']);
$wsLogoPath     = $ws['site_logo'];
$wsFooterDesc   = htmlspecialchars($ws['footer_description']);
$wsCopyright    = $ws['copyright_text'];
$wsFacebook     = htmlspecialchars(ensureProtocol($ws['facebook_url']));
$wsTiktok       = htmlspecialchars(ensureProtocol($ws['tiktok_url']));
$wsLinkedin     = htmlspecialchars(ensureProtocol($ws['linkedin_url']));
$wsGithub       = htmlspecialchars(ensureProtocol($ws['github_url']));
$footerHomeHref = ($currentPage === 'home') ? '#hero' : $websiteBase . '/index.php';
$footerMusicHref = $websiteBase . '/music/music.php';
$footerVideosHref = $websiteBase . '/video/video.php';
?>
<!-- FOOTER -->
<footer class="wg-footer">
    <div class="wg-footer__inner">
        <div class="wg-footer__top">
            <!-- Brand -->
            <div class="wg-footer__brand">
                <a href="<?php echo $footerHomeHref; ?>" class="wg-logo">
                    <span class="wg-logo__icon">
                        <?php if ($wsLogoPath): ?>
                            <img src="<?php echo htmlspecialchars($wsLogoPath); ?>" alt="<?php echo $wsWebsiteName; ?>" style="width:28px;height:28px;object-fit:contain;">
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        <?php endif; ?>
                    </span>
                    <span class="wg-logo__text"><?php echo $wsWebsiteName; ?></span>
                </a>
                <p class="wg-footer__desc"><?php echo $wsFooterDesc; ?></p>
            </div>

            <!-- Main Navigation -->
            <div class="wg-footer__col">
                <h4 class="wg-footer__heading">Main Pages</h4>
                <a href="<?php echo $footerHomeHref; ?>" class="wg-footer__link">Home</a>
                <a href="<?php echo $footerMusicHref; ?>" class="wg-footer__link">Music</a>
                <a href="<?php echo $footerVideosHref; ?>" class="wg-footer__link">Videos</a>
                <a href="<?php echo $websiteBase; ?>/search/search.php" class="wg-footer__link">Search</a>
                <a href="<?php echo $websiteBase; ?>/about/about.php" class="wg-footer__link">About</a>
                <a href="<?php echo $websiteBase; ?>/contact/contact.php" class="wg-footer__link">Contact</a>
            </div>

            <!-- Account -->
            <div class="wg-footer__col">
                <h4 class="wg-footer__heading">Account</h4>
                <a href="#" class="wg-footer__link wg-login-trigger">Login</a>
                <a href="#" class="wg-footer__link wg-signup-trigger">Sign Up</a>
            </div>

            <!-- Social -->
            <div class="wg-footer__col">
                <h4 class="wg-footer__heading">Follow Us</h4>
                <div class="wg-footer__social">
                    <a href="<?php echo $wsFacebook; ?>" class="wg-footer__social-link" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="<?php echo $wsTiktok; ?>" class="wg-footer__social-link" aria-label="TikTok" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3.15 15.2a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.75a8.18 8.18 0 0 0 4.76 1.52V6.8a4.83 4.83 0 0 1-1-.11z"/></svg>
                    </a>
                    <a href="<?php echo $wsLinkedin; ?>" class="wg-footer__social-link" aria-label="LinkedIn" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                    <a href="<?php echo $wsGithub; ?>" class="wg-footer__social-link" aria-label="GitHub" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="wg-footer__bottom">
            <p class="wg-footer__copy"><?php echo $wsCopyright; ?></p>
        </div>
    </div>
</footer>
