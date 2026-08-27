<?php
/**
 * SOUND Group — User Management
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireAuth();

$pageTitle = 'User Management';
$activeItem = 'user-management';

$db = getDb();
$csrfToken = csrfToken();
$stmt = $stmt = $db->query("SELECT * FROM `users` ORDER BY `id` DESC");
$users = $stmt->fetchAll();

function umFormatTimestamp($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return null;
    return date('M d, Y, h:i A', strtotime($ts));
}

function umInitials($name) {
    $parts = explode(' ', trim($name));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= strtoupper($p[0] ?? '?');
    }
    return $initials;
}

$avatarColors = ['violet','blue','pink','green','amber','rose','teal','indigo'];

include __DIR__ . '/../layout/admin-layout.php';
?>

    <div class="um-header">
        <div class="um-header__left">
            <h1 class="um-header__title">User Management</h1>
            <p class="um-header__subtitle">Manage registered users and their account information.</p>
        </div>
    </div>

    <div class="um-toolbar">
        <div class="um-toolbar__search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="um-search-input" placeholder="Search by name or user ID..." id="umSearchInput">
        </div>
        <div class="um-toolbar__filter">
            <label class="um-toolbar__filter-label" for="umStatusFilter">Status</label>
            <select class="um-toolbar__filter-select" id="umStatusFilter">
                <option value="all">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <section class="um-grid-section">
        <div class="um-grid-section__header">
            <h2 class="um-grid-section__title">All Users</h2>
        </div>

        <div class="um-user-grid" id="umCardGrid" data-csrf="<?php echo htmlspecialchars($csrfToken); ?>" data-base-url="<?php echo htmlspecialchars($baseUrl); ?>">
            <?php if (empty($users)): ?>
            <?php else: ?>
                <?php foreach ($users as $i => $user): ?>
                    <?php
                        $colorIdx = $i % count($avatarColors);
                        $color = $avatarColors[$colorIdx];
                        $initials = umInitials($user['full_name']);
                        $regDate = umFormatTimestamp($user['created_at']);
                        $loginDate = umFormatTimestamp($user['last_login']);
                        $logoutDate = umFormatTimestamp($user['last_logout']);
                        $imgPath = $user['profile_image'] ? ($baseUrl . '/' . ltrim($user['profile_image'], '/')) : '';
                    ?>
                    <div class="um-user-card"
                         data-user-id="<?php echo htmlspecialchars($user['user_id']); ?>"
                         data-db-id="<?php echo (int) $user['id']; ?>"
                         data-name="<?php echo htmlspecialchars($user['full_name']); ?>"
                         data-email="<?php echo htmlspecialchars($user['email']); ?>"
                         data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                         data-address="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                         data-registered="<?php echo $regDate ? htmlspecialchars($regDate) : ''; ?>"
                         data-login="<?php echo $loginDate ? htmlspecialchars($loginDate) : ''; ?>"
                         data-logout="<?php echo $logoutDate ? htmlspecialchars($logoutDate) : ''; ?>"
                         data-status="<?php echo $user['status']; ?>"
                         data-image="<?php echo htmlspecialchars($imgPath); ?>">
                        <div class="um-user-card__header">
                            <?php if ($imgPath): ?>
                                <div class="um-avatar um-avatar--card"><img src="<?php echo htmlspecialchars($imgPath); ?>" alt="<?php echo htmlspecialchars($user['full_name']); ?>"></div>
                            <?php else: ?>
                                <div class="um-avatar um-avatar--card um-avatar--<?php echo $color; ?>"><?php echo $initials; ?></div>
                            <?php endif; ?>
                            <div class="um-user-card__identity">
                                <h3 class="um-user-card__name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                            </div>
                            <span class="um-badge <?php echo $user['status'] === 'active' ? 'um-badge--active' : 'um-badge--inactive'; ?>"><?php echo ucfirst($user['status']); ?></span>
                        </div>
                        <div class="um-user-card__info">
                            <div class="um-user-card__row">
                                <span class="um-user-card__label">Email</span>
                                <span class="um-user-card__value um-user-card__value--email"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="um-user-card__row">
                                <span class="um-user-card__label">Phone</span>
                                <span class="um-user-card__value um-user-card__value--phone"><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                            <div class="um-user-card__row">
                                <span class="um-user-card__label">Address</span>
                                <span class="um-user-card__value um-user-card__value--address"><?php echo htmlspecialchars($user['address'] ?: '—'); ?></span>
                            </div>
                            <div class="um-user-card__divider"></div>
                            <div class="um-user-card__meta">
                                <div class="um-user-card__meta-item">
                                    <span class="um-user-card__label">User ID</span>
                                    <span class="um-user-card__value"><?php echo htmlspecialchars($user['user_id']); ?></span>
                                </div>
                                <div class="um-user-card__meta-item">
                                    <span class="um-user-card__label">Registered</span>
                                    <span class="um-user-card__value"><?php echo $regDate ? htmlspecialchars($regDate) : '—'; ?></span>
                                </div>
                                <div class="um-user-card__meta-item">
                                    <span class="um-user-card__label">Last Login</span>
                                    <span class="um-user-card__value"><?php echo $loginDate ? htmlspecialchars($loginDate) : 'Never'; ?></span>
                                </div>
                                <div class="um-user-card__meta-item">
                                    <span class="um-user-card__label">Last Logout</span>
                                    <span class="um-user-card__value"><?php echo $logoutDate ? htmlspecialchars($logoutDate) : '—'; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="um-user-card__actions">
                            <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </button>
                            <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </button>
                            <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Empty State -->
        <div class="um-empty" id="umEmptyState" <?php echo !empty($users) ? 'hidden' : ''; ?>>
            <div class="um-empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="36" height="36">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h3 class="um-empty__title">No users found</h3>
            <p class="um-empty__desc">No registered users yet. Users will appear here after they sign up on the website.</p>
        </div>

        <div class="um-grid-section__footer">
            <span class="um-grid-section__count" id="umCount">Showing <?php echo count($users); ?> of <?php echo count($users); ?> users</span>
            <div class="um-pagination">
                <button type="button" class="um-pagination__btn" id="umPrevPage" aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <div class="um-pagination__pages" id="umPaginationPages"></div>
                <button type="button" class="um-pagination__btn" id="umNextPage" aria-label="Next page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- View User Modal -->
    <div class="sg-modal" id="umViewModal">
        <div class="sg-modal__overlay" data-um-close="view"></div>
        <div class="sg-modal__dialog um-modal">
            <button type="button" class="sg-modal__close" data-um-close="view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="um-view-header">
                    <div class="um-avatar um-avatar--large um-avatar--violet" id="um-view-avatar">—</div>
                    <div class="um-view-info">
                        <h2 class="um-view-info__title" id="um-view-name">—</h2>
                        <span class="um-view-info__id">User ID: <strong id="um-view-id">—</strong></span>
                    </div>
                </div>
                <div class="um-view-status">
                    <span class="um-badge" id="um-view-status-badge">—</span>
                </div>
                <div class="um-view-details">
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Email</span>
                        <span class="um-view-detail__value" id="um-view-email">—</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Phone Number</span>
                        <span class="um-view-detail__value" id="um-view-phone">—</span>
                    </div>
                    <div class="um-view-detail um-view-detail--full">
                        <span class="um-view-detail__label">Address</span>
                        <span class="um-view-detail__value" id="um-view-address">—</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Registered</span>
                        <span class="um-view-detail__value" id="um-view-registered">—</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Last Login</span>
                        <span class="um-view-detail__value" id="um-view-login">—</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Last Logout</span>
                        <span class="um-view-detail__value" id="um-view-logout">—</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Account Status</span>
                        <span class="um-view-detail__value" id="um-view-status-text">—</span>
                    </div>
                </div>
                <div class="um-view-actions">
                    <button type="button" class="sg-btn um-btn-cancel" data-um-close="view">Close</button>
                    <button type="button" class="sg-btn sg-btn--primary" id="umViewEditBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="sg-modal" id="umEditModal">
        <div class="sg-modal__overlay" data-um-close="edit"></div>
        <div class="sg-modal__dialog um-modal um-modal--wide">
            <button type="button" class="sg-modal__close" data-um-close="edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title" id="umEditTitle">Edit User</h2>
                <p class="sg-modal__subtitle">Update <strong id="um-edit-user-name">—</strong>'s account information.</p>

                <form id="umEditForm" class="um-form" novalidate>
                    <input type="hidden" id="um-edit-db-id" value="">
                    <div class="um-form__profile">
                        <div class="um-avatar um-avatar--large um-avatar--violet" id="um-edit-avatar">—</div>
                        <div class="um-form__profile-info">
                            <button type="button" class="sg-btn um-btn-outline" id="umEditImageBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                Change Image
                            </button>
                            <span class="um-form__profile-hint">JPG, PNG or WebP (Max 2MB)</span>
                            <input type="file" class="um-form__file-input" id="umEditImageInput" name="profile_image" accept=".jpg,.jpeg,.png,.webp" hidden>
                        </div>
                    </div>

                    <div class="um-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-name">Full Name</label>
                            <input type="text" class="sg-form-input um-form-input" id="um-edit-name" name="full_name">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-email">Email</label>
                            <input type="email" class="sg-form-input um-form-input" id="um-edit-email" name="email">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-phone">Phone Number</label>
                            <input type="tel" class="sg-form-input um-form-input" id="um-edit-phone" name="phone">
                        </div>
                        <div class="sg-form-group um-form__group--full">
                            <label class="sg-form-label" for="um-edit-address">Address</label>
                            <input type="text" class="sg-form-input um-form-input" id="um-edit-address" name="address">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-status">Account Status</label>
                            <select class="sg-form-input um-form-input" id="um-edit-status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="um-form__actions">
                        <button type="button" class="sg-btn um-btn-cancel" data-um-close="edit">Cancel</button>
                        <button type="submit" class="sg-btn sg-btn--primary" id="umUpdateUserBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="sg-modal" id="umDeleteModal">
        <div class="sg-modal__overlay" data-um-close="delete"></div>
        <div class="sg-modal__dialog um-modal um-modal--delete">
            <button type="button" class="sg-modal__close" data-um-close="delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="um-delete-body">
                    <div class="um-delete-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </div>
                    <h2 class="sg-modal__title">Delete User</h2>
                    <p class="sg-modal__subtitle">Are you sure you want to delete <strong id="um-delete-name">—</strong>? This action cannot be undone.</p>
                </div>
                <div class="um-form__actions um-delete-actions">
                    <button type="button" class="sg-btn um-btn-cancel" data-um-close="delete">Cancel</button>
                    <button type="button" class="sg-btn sg-btn--danger" id="umConfirmDeleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/admin-layout-end.php'; ?>
