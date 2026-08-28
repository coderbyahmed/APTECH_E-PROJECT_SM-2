<?php
/**
 * SOUND Group — Admin Activity Log Helper
 *
 * Provides logAdminActivity() to record admin actions in the admin_activity_logs table.
 * This is ONLY for admin panel actions — website user activities are never logged here.
 */

/**
 * Log an admin activity to the database.
 *
 * @param PDO    $db       Database connection
 * @param string $action   Action type: 'created', 'updated', 'deleted', 'status_changed', 'toggled_read', 'published', 'hidden'
 * @param string $module   Module name: 'music', 'video', 'category', 'user', 'review', 'contact', etc.
 * @param string $itemName Name/title of the affected item
 * @param int    $itemId   ID of the affected item
 */
function logAdminActivity($db, $action, $module, $itemName, $itemId = 0) {
    if (!isAdminLoggedIn()) return;

    $adminId   = (int) $_SESSION['admin_id'];
    $adminName = $_SESSION['admin_name'] ?? 'Admin';

    $stmt = $db->prepare("INSERT INTO `admin_activity_logs` (`admin_id`, `admin_name`, `action`, `module`, `item_name`, `item_id`, `created_at`) VALUES (:admin_id, :admin_name, :action, :module, :item_name, :item_id, :created_at)");
    $stmt->execute([
        ':admin_id'   => $adminId,
        ':admin_name' => $adminName,
        ':action'     => $action,
        ':module'     => $module,
        ':item_name'  => $itemName,
        ':item_id'    => $itemId,
        ':created_at' => date('Y-m-d H:i:s'),
    ]);
}
