<?php
/**
 * SOUND Group — Category Management: Artist List
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireAuth();

$pageTitle = 'Artist List — Category Management';
$activeItem = 'category-management';

$db = getDb();
$stmt = $db->prepare("SELECT `id`, `name`, `created_at`, `updated_at` FROM `artists` ORDER BY `id` DESC");
$stmt->execute();
$records = $stmt->fetchAll();
$recordsJson = json_encode($records);
$cmCsrf = csrfToken();

include __DIR__ . '/../layout/admin-layout.php';
?>

<script>document.body.classList.add('cm-page','cm-page--artist');</script>

    <div class="cm-header">
        <div class="cm-header__left">
            <a href="/Aptech_E_Project_02/sound_management/frontend/admin/category-management/index.php" class="cm-back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Back to Categories
            </a>
            <h1 class="cm-header__title">Artist List</h1>
            <p class="cm-header__subtitle">All artist entries used for organizing content.</p>
        </div>
    </div>

    <div class="cm-table-card">
        <div class="cm-table-card__header">
            <h2 class="cm-table-card__title">All Artists</h2>
            <div class="cm-table-card__search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="cm-search-input" placeholder="Search artists..." id="cmSearchInput">
            </div>
        </div>

        <div class="cm-table-wrapper">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th class="cm-table__th">#</th>
                        <th class="cm-table__th">Artist Name</th>
                        <th class="cm-table__th">Created At</th>
                        <th class="cm-table__th cm-table__th--actions">Actions</th>
                    </tr>
                </thead>
                <tbody class="cm-table__tbody">
                </tbody>
            </table>
        </div>

        <div class="cm-table-card__footer">
            <span class="cm-table-card__count">Showing <?php echo count($records); ?> artist<?php echo count($records) !== 1 ? 's' : ''; ?></span>
        </div>
    </div>

    <script id="cmRecordsJson" type="application/json"><?php echo $recordsJson; ?></script>

    <!-- Edit Artist Modal -->
    <div class="sg-modal" id="cmEditModal">
        <div class="sg-modal__overlay" data-cm-close="edit"></div>
        <div class="sg-modal__dialog cm-modal">
            <button type="button" class="sg-modal__close" data-cm-close="edit">
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
                <h2 class="sg-modal__title">Edit Artist</h2>
                <p class="sg-modal__subtitle">Update the artist name.</p>
                <form id="cmEditForm" class="cm-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $cmCsrf; ?>">
                    <input type="hidden" id="cm-edit-id">
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cm-edit-input">Artist Name</label>
                        <input type="text" class="sg-form-input cm-form-input" id="cm-edit-input">
                    </div>
                    <div class="cm-form__actions">
                        <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="edit">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="cmUpdateBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Update Artist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="sg-modal" id="cmDeleteModal">
        <div class="sg-modal__overlay" data-cm-close="delete"></div>
        <div class="sg-modal__dialog cm-modal cm-modal--delete">
            <button type="button" class="sg-modal__close" data-cm-close="delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="cm-delete-body">
                    <div class="cm-delete-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </div>
                    <h2 class="sg-modal__title">Delete Artist</h2>
                    <p class="sg-modal__subtitle">Are you sure you want to delete <strong id="cm-delete-name"></strong>? This action cannot be undone.</p>
                </div>
                <input type="hidden" id="cm-delete-id">
                <div class="cm-form__actions cm-delete-actions">
                    <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="delete">Cancel</button>
                    <button type="button" class="sg-btn sg-btn--danger" id="cmConfirmDeleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/admin-layout-end.php'; ?>
