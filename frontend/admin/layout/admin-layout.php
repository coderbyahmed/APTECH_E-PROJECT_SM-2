<?php
/**
 * SOUND Group — Admin Layout (Opening)
 *
 * Usage:
 *   $pageTitle = 'Dashboard';
 *   $activeItem = 'dashboard';
 *   include __DIR__ . '/../layout/admin-layout.php';
 *   // ... page content ...
 *   include __DIR__ . '/../layout/admin-layout-end.php';
 */

if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel';
}

$baseUrl = '/Aptech_E_Project_02/sound_management';
$cssBase = $baseUrl . '/frontend/admin/css';
$jsBase  = $baseUrl . '/frontend/admin/js';

require_once __DIR__ . '/../../../backend/includes/session.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrfToken = csrfToken();

require_once __DIR__ . '/../../../backend/includes/website-settings.php';
$wsLayout = getWebsiteSettings();
$wsLayoutName = htmlspecialchars($wsLayout['website_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken); ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?> — <?php echo $wsLayoutName; ?></title>

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/layout/admin-layout.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/layouts/admin-sidebar.css?v=<?php echo filemtime(__DIR__ . '/../css/components/layouts/admin-sidebar.css'); ?>">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/layouts/admin-navbar.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/loaders/button-spinner.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/modals/modal.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/modals/change-email-modal.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/modals/change-password-modal.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/components/modals/my-profile-modal.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/music-management/music-management.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/video-management/video-management.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/category-management/category-management.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/user-management/user-management.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/review-management/review-management.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/website-info/website-info.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/contact-messages/contact-messages.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/dashboard/dashboard.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <?php include __DIR__ . '/../components/layouts/sidebar.php'; ?>
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

        <!-- Main Area -->
        <div class="admin-main">
            <!-- Top Navbar -->
            <header class="admin-navbar">
                <?php include __DIR__ . '/../components/layouts/navbar.php'; ?>
            </header>

            <!-- Page Content -->
            <main class="admin-content">
