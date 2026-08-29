<?php
/**
 * SOUND Group — Admin Layout (Closing)
 *
 * This file closes everything opened by admin-layout.php.
 */

$baseUrl = baseUrl();
$jsBase  = $baseUrl . '/frontend/admin/js';
$cssBase = $baseUrl . '/frontend/admin/css';
$jsFs    = dirname(__DIR__, 1) . '/js';
?>
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay Toggle Script -->
    <script>
    (function () {
        var sidebar     = document.getElementById('adminSidebar');
        var overlay     = document.getElementById('adminSidebarOverlay');
        var toggleBtns  = document.querySelectorAll('#sidebarToggle, #adminSidebarOverlay');

        toggleBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                sidebar.classList.toggle('is-open');
                overlay.classList.toggle('is-visible');
            });
        });
    })();
    </script>

    <!-- Notification Component -->
    <?php include __DIR__ . '/../components/notifications/notification.php'; ?>

    <!-- Button Spinner Component -->
    <?php include __DIR__ . '/../components/loaders/button-spinner.php'; ?>

    <!-- Account Modals -->
    <?php include __DIR__ . '/../components/modals/change-email-modal.php'; ?>
    <?php include __DIR__ . '/../components/modals/change-password-modal.php'; ?>
    <?php include __DIR__ . '/../components/modals/my-profile-modal.php'; ?>

    <!-- Admin JS -->
    <script>window.APP_BASE_URL = '<?php echo baseUrl(); ?>';</script>
    <script src="<?php echo $jsBase; ?>/components/layouts/sidebar-collapse.js?v=<?php echo filemtime($jsFs . '/components/layouts/sidebar-collapse.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/layouts/admin-navbar.js?v=<?php echo filemtime($jsFs . '/components/layouts/admin-navbar.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/notifications/notification.js?v=<?php echo filemtime($jsFs . '/components/notifications/notification.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/loaders/button-spinner.js?v=<?php echo filemtime($jsFs . '/components/loaders/button-spinner.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/modals/change-email-modal.js?v=<?php echo filemtime($jsFs . '/components/modals/change-email-modal.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/modals/change-password-modal.js?v=<?php echo filemtime($jsFs . '/components/modals/change-password-modal.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/modals/my-profile-modal.js?v=<?php echo filemtime($jsFs . '/components/modals/my-profile-modal.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/music-management/music-management.js?v=<?php echo filemtime($jsFs . '/components/music-management/music-management.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/video-management/video-management.js?v=<?php echo filemtime($jsFs . '/components/video-management/video-management.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/category-management/category-management.js?v=<?php echo filemtime($jsFs . '/components/category-management/category-management.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/user-management/user-management.js?v=<?php echo filemtime($jsFs . '/components/user-management/user-management.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/review-management/review-management.js?v=<?php echo filemtime($jsFs . '/components/review-management/review-management.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/website-info/website-info.js?v=<?php echo filemtime($jsFs . '/components/website-info/website-info.js'); ?>"></script>
    <script src="<?php echo $jsBase; ?>/components/contact-messages/contact-messages.js?v=<?php echo filemtime($jsFs . '/components/contact-messages/contact-messages.js'); ?>"></script>

    <!-- Extra Scripts -->
    <?php if (!empty($extraScripts)) { echo $extraScripts; } ?>
</body>
</html>
