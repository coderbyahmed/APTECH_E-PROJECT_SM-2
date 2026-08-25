<?php
/**
 * SOUND Group — Admin Dashboard
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireAuth();

$pageTitle = 'Dashboard';
$activeItem = 'dashboard';

include __DIR__ . '/../layout/admin-layout.php';
?>

    <div class="dashboard-welcome">
        <h2 class="dashboard-welcome__title">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h2>
        <p class="dashboard-welcome__subtitle">Here's what's happening with your platform today.</p>
    </div>

    <div class="dashboard-stats">
        <!-- Total Songs -->
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-card__icon dashboard-stat-card__icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <path d="M9 18V5l12-2v13"/>
                    <circle cx="6" cy="18" r="3"/>
                    <circle cx="18" cy="16" r="3"/>
                </svg>
            </div>
            <div class="dashboard-stat-card__info">
                <span class="dashboard-stat-card__label">Total Songs</span>
                <span class="dashboard-stat-card__value">--</span>
            </div>
        </div>

        <!-- Total Videos -->
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-card__icon dashboard-stat-card__icon--pink">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <polygon points="23 7 16 12 23 17 23 7"/>
                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                </svg>
            </div>
            <div class="dashboard-stat-card__info">
                <span class="dashboard-stat-card__label">Total Videos</span>
                <span class="dashboard-stat-card__value">--</span>
            </div>
        </div>

        <!-- Artists -->
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-card__icon dashboard-stat-card__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="dashboard-stat-card__info">
                <span class="dashboard-stat-card__label">Artists</span>
                <span class="dashboard-stat-card__value">--</span>
            </div>
        </div>

        <!-- Albums -->
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-card__icon dashboard-stat-card__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="dashboard-stat-card__info">
                <span class="dashboard-stat-card__label">Albums</span>
                <span class="dashboard-stat-card__value">--</span>
            </div>
        </div>
    </div>

<?php
include __DIR__ . '/../layout/admin-layout-end.php';
?>
