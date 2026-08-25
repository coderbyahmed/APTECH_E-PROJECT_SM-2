<?php
/**
 * SOUND Group — Video Management
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireAuth();

$pageTitle = 'Video Management';
$activeItem = 'video-management';

$db = getDb();

$cmData = [];
foreach (['artists', 'albums', 'air', 'genres', 'languages'] as $table) {
    $stmt = $db->query("SELECT `id`, `name` FROM `" . $table . "` ORDER BY `name` ASC");
    $cmData[$table] = $stmt->fetchAll();
}

$stmt = $db->query("SELECT v.*,
    a.`name` AS `artist_name`,
    al.`name` AS `album_name`,
    y.`name` AS `year_name`,
    g.`name` AS `genre_name`,
    l.`name` AS `language_name`
FROM `videos` v
LEFT JOIN `artists` a ON a.`id` = v.`artist_id`
LEFT JOIN `albums` al ON al.`id` = v.`album_id`
LEFT JOIN `air` y ON y.`id` = v.`year_id`
LEFT JOIN `genres` g ON g.`id` = v.`genre_id`
LEFT JOIN `languages` l ON l.`id` = v.`language_id`
ORDER BY v.`id` DESC");
$videoRecords = $stmt->fetchAll();

$videoData = [];
foreach ($videoRecords as $row) {
    $videoData[] = [
        'id'             => (int) $row['id'],
        'video_title'    => $row['video_title'],
        'artist_id'      => $row['artist_id'] !== null ? (int) $row['artist_id'] : null,
        'artist_name'    => $row['artist_name'] ?? '',
        'album_id'       => $row['album_id'] !== null ? (int) $row['album_id'] : null,
        'album_name'     => $row['album_name'] ?? '',
        'year_id'        => $row['year_id'] !== null ? (int) $row['year_id'] : null,
        'year_name'      => $row['year_name'] ?? '',
        'genre_id'       => $row['genre_id'] !== null ? (int) $row['genre_id'] : null,
        'genre_name'     => $row['genre_name'] ?? '',
        'language_id'    => $row['language_id'] !== null ? (int) $row['language_id'] : null,
        'language_name'  => $row['language_name'] ?? '',
        'description'    => $row['description'] ?? '',
        'video_path'     => $row['video_path'] ?? '',
        'thumbnail_path' => $row['thumbnail_path'] ?? '',
        'status'         => $row['status'],
        'created_at'     => $row['created_at'] ?? '',
        'updated_at'     => $row['updated_at'] ?? '',
    ];
}

include __DIR__ . '/../layout/admin-layout.php';
?>

    <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
    <script type="application/json" id="vmCategoriesJson"><?php echo json_encode($cmData); ?></script>
    <script type="application/json" id="vmVideosJson"><?php echo json_encode($videoData); ?></script>

    <div class="vm-header">
        <div class="vm-header__left">
            <h1 class="vm-header__title">Video Management</h1>
            <p class="vm-header__subtitle">Manage your video catalog. Add, edit, and organize your music videos, performances, and visual content.</p>
        </div>
        <div class="vm-header__right">
            <button type="button" class="sg-btn sg-btn--primary vm-btn-add" id="vmAddVideoBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Video
            </button>
        </div>
    </div>

    <div class="vm-table-card">
        <div class="vm-table-card__header">
            <h2 class="vm-table-card__title">All Videos</h2>
            <div class="vm-table-card__search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="vm-search-input" placeholder="Search videos..." id="vmSearchInput">
            </div>
        </div>

        <div class="vm-table-wrapper">
            <table class="vm-table">
                <thead>
                    <tr>
                        <th class="vm-table__th">Video</th>
                        <th class="vm-table__th">Title</th>
                        <th class="vm-table__th">Artist</th>
                        <th class="vm-table__th">Album</th>
                        <th class="vm-table__th">Year</th>
                        <th class="vm-table__th">Genre</th>
                        <th class="vm-table__th">Language</th>
                        <th class="vm-table__th">Status</th>
                        <th class="vm-table__th vm-table__th--actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="vmTableBody">
                </tbody>
            </table>
        </div>

        <div class="vm-mobile-cards" id="vmMobileCards"></div>

        <div class="vm-table-card__footer">
            <span class="vm-table-card__count" id="vmCount">Showing 0 videos</span>
        </div>
    </div>

    <!-- Add Video Modal -->
    <div class="sg-modal" id="vmAddModal">
        <div class="sg-modal__overlay" data-vm-close="add"></div>
        <div class="sg-modal__dialog vm-modal--wide">
            <button type="button" class="sg-modal__close" data-vm-close="add">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <polygon points="23 7 16 12 23 17 23 7"/>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Add New Video</h2>
                <p class="sg-modal__subtitle">Fill in the details below to add a new video to your catalog.</p>

                <form id="vmAddForm" class="vm-form" method="POST" enctype="multipart/form-data">
                    <div class="vm-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-add-title">Video Title</label>
                            <input type="text" class="sg-form-input vm-form-input" id="vm-add-title" placeholder="Enter video title">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-add-artist">Artist</label>
                            <select class="sg-form-input vm-form-input" id="vm-add-artist">
                                <option value="">Select artist</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-add-album">Album</label>
                            <select class="sg-form-input vm-form-input" id="vm-add-album">
                                <option value="">Select album</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-add-year">Year</label>
                            <select class="sg-form-input vm-form-input" id="vm-add-year">
                                <option value="">Select year</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-add-genre">Genre</label>
                            <select class="sg-form-input vm-form-input" id="vm-add-genre">
                                <option value="">Select genre</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-add-language">Language</label>
                            <select class="sg-form-input vm-form-input" id="vm-add-language">
                                <option value="">Select language</option>
                            </select>
                        </div>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="vm-add-description">Description</label>
                        <textarea class="sg-form-input vm-form-input vm-form-textarea" id="vm-add-description" placeholder="Enter a brief description of the video" rows="3"></textarea>
                    </div>

                    <div class="vm-form__grid vm-form__grid--2col">
                        <div class="sg-form-group">
                            <label class="sg-form-label">Video File</label>
                            <div class="vm-upload-area" id="vm-video-upload">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <polygon points="23 7 16 12 23 17 23 7"/>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                </svg>
                                <span class="vm-upload-area__text">Drop video file here or <span class="vm-upload-area__browse">browse</span></span>
                                <span class="vm-upload-area__hint">MP4, WebM, MOV, AVI, MKV (Max 500MB)</span>
                                <input type="file" class="vm-upload-area__input" accept=".mp4,.webm,.mov,.avi,.mkv,.m4v" id="vm-add-video-file">
                            </div>
                            <div class="vm-upload-preview" id="vm-video-preview" style="display:none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                    <polygon points="23 7 16 12 23 17 23 7"/>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                </svg>
                                <span class="vm-upload-preview__name" id="vm-video-file-name"></span>
                                <button type="button" class="vm-upload-preview__remove" id="vm-video-file-remove">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="sg-form-group">
                            <label class="sg-form-label">Thumbnail / Cover Image</label>
                            <div class="vm-upload-area vm-upload-area--image" id="vm-thumb-upload">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span class="vm-upload-area__text">Drop thumbnail image or <span class="vm-upload-area__browse">browse</span></span>
                                <span class="vm-upload-area__hint">JPG, PNG, WebP (Max 5MB)</span>
                                <input type="file" class="vm-upload-area__input" accept=".jpg,.jpeg,.png,.webp" id="vm-add-thumb-image">
                            </div>
                            <div class="vm-upload-preview vm-upload-preview--image" id="vm-thumb-preview" style="display:none;">
                                <img src="" alt="Thumbnail preview" id="vm-thumb-preview-img" class="vm-thumb-preview-img">
                                <div class="vm-upload-preview__info">
                                    <span class="vm-upload-preview__name" id="vm-thumb-file-name"></span>
                                    <button type="button" class="vm-upload-preview__remove" id="vm-thumb-file-remove">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="vm-add-status">Status</label>
                        <select class="sg-form-input vm-form-input" id="vm-add-status">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="vm-form__actions">
                        <button type="button" class="sg-btn vm-btn-cancel" data-vm-close="add">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="vmSaveVideoBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Video Modal -->
    <div class="sg-modal" id="vmEditModal">
        <div class="sg-modal__overlay" data-vm-close="edit"></div>
        <div class="sg-modal__dialog vm-modal--wide">
            <button type="button" class="sg-modal__close" data-vm-close="edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Edit Video</h2>
                <p class="sg-modal__subtitle">Update the details for <strong id="vm-edit-video-name"></strong>.</p>

                <form id="vmEditForm" class="vm-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="vm-edit-id" value="">
                    <div class="vm-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-edit-title">Video Title</label>
                            <input type="text" class="sg-form-input vm-form-input" id="vm-edit-title">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-edit-artist">Artist</label>
                            <select class="sg-form-input vm-form-input" id="vm-edit-artist">
                                <option value="">Select artist</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-edit-album">Album</label>
                            <select class="sg-form-input vm-form-input" id="vm-edit-album">
                                <option value="">Select album</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-edit-year">Year</label>
                            <select class="sg-form-input vm-form-input" id="vm-edit-year">
                                <option value="">Select year</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-edit-genre">Genre</label>
                            <select class="sg-form-input vm-form-input" id="vm-edit-genre">
                                <option value="">Select genre</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="vm-edit-language">Language</label>
                            <select class="sg-form-input vm-form-input" id="vm-edit-language">
                                <option value="">Select language</option>
                            </select>
                        </div>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="vm-edit-description">Description</label>
                        <textarea class="sg-form-input vm-form-input vm-form-textarea" id="vm-edit-description" placeholder="Enter a brief description of the video" rows="3"></textarea>
                    </div>

                    <div class="vm-form__grid vm-form__grid--2col">
                        <div class="sg-form-group">
                            <label class="sg-form-label">Video File</label>
                            <div class="vm-upload-area" id="vm-edit-video-upload" style="display:none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <polygon points="23 7 16 12 23 17 23 7"/>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                </svg>
                                <span class="vm-upload-area__text">Drop video file here or <span class="vm-upload-area__browse">browse</span></span>
                                <span class="vm-upload-area__hint">MP4, WebM, MOV, AVI, MKV (Max 500MB)</span>
                                <input type="file" class="vm-upload-area__input" accept=".mp4,.webm,.mov,.avi,.mkv,.m4v" id="vm-edit-video-file">
                            </div>
                            <div class="vm-upload-preview" id="vm-edit-video-preview">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                    <polygon points="23 7 16 12 23 17 23 7"/>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                </svg>
                                <span class="vm-upload-preview__name" id="vm-edit-video-name-file"></span>
                                <button type="button" class="vm-upload-preview__remove vm-upload-preview__remove--replace" id="vm-edit-video-replace">Replace</button>
                            </div>
                        </div>

                        <div class="sg-form-group">
                            <label class="sg-form-label">Thumbnail / Cover Image</label>
                            <div class="vm-upload-area vm-upload-area--image" id="vm-edit-thumb-upload" style="display:none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span class="vm-upload-area__text">Drop thumbnail image or <span class="vm-upload-area__browse">browse</span></span>
                                <span class="vm-upload-area__hint">JPG, PNG, WebP (Max 5MB)</span>
                                <input type="file" class="vm-upload-area__input" accept=".jpg,.jpeg,.png,.webp" id="vm-edit-thumb-image">
                            </div>
                            <div class="vm-upload-preview vm-upload-preview--image" id="vm-edit-thumb-preview">
                                <img src="" alt="Thumbnail" id="vm-edit-thumb-img" class="vm-thumb-preview-img" style="display:none;">
                                <div class="vm-thumb-placeholder" id="vm-edit-thumb-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                </div>
                                <div class="vm-upload-preview__info">
                                    <span class="vm-upload-preview__name" id="vm-edit-thumb-name"></span>
                                    <button type="button" class="vm-upload-preview__remove vm-upload-preview__remove--replace" id="vm-edit-thumb-replace">Replace</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="vm-edit-status">Status</label>
                        <select class="sg-form-input vm-form-input" id="vm-edit-status">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="vm-form__actions">
                        <button type="button" class="sg-btn vm-btn-cancel" data-vm-close="edit">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="vmUpdateVideoBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Update Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Video Modal -->
    <div class="sg-modal" id="vmViewModal">
        <div class="sg-modal__overlay" data-vm-close="view"></div>
        <div class="sg-modal__dialog vm-modal--view">
            <button type="button" class="sg-modal__close" data-vm-close="view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="vm-view-header">
                    <div class="vm-view-thumb" id="vm-view-thumb-wrap">
                        <img src="" alt="Thumbnail" id="vm-view-thumb-img" class="vm-view-thumb-img" style="display:none; width:80px; height:60px; object-fit:cover; border-radius:10px;">
                        <svg id="vm-view-thumb-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="36" height="36">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                    </div>
                    <div class="vm-view-info">
                        <h2 class="vm-view-info__title" id="vm-view-title"></h2>
                        <p class="vm-view-info__artist" id="vm-view-artist"></p>
                        <span class="vm-badge vm-badge--active" id="vm-view-status"></span>
                    </div>
                </div>

                <div class="vm-view-details">
                    <div class="vm-view-detail">
                        <span class="vm-view-detail__label">Album</span>
                        <span class="vm-view-detail__value" id="vm-view-album"></span>
                    </div>
                    <div class="vm-view-detail">
                        <span class="vm-view-detail__label">Year</span>
                        <span class="vm-view-detail__value" id="vm-view-year"></span>
                    </div>
                    <div class="vm-view-detail">
                        <span class="vm-view-detail__label">Genre</span>
                        <span class="vm-view-detail__value" id="vm-view-genre"></span>
                    </div>
                    <div class="vm-view-detail">
                        <span class="vm-view-detail__label">Language</span>
                        <span class="vm-view-detail__value" id="vm-view-language"></span>
                    </div>
                    <div class="vm-view-detail vm-view-detail--full">
                        <span class="vm-view-detail__label">Description</span>
                        <span class="vm-view-detail__value" id="vm-view-description"></span>
                    </div>
                    <div class="vm-view-detail vm-view-detail--full">
                        <span class="vm-view-detail__label">Video File</span>
                        <span class="vm-view-detail__value vm-view-detail__value--file" id="vm-view-video-file"></span>
                    </div>
                </div>

                <div class="vm-view-player" id="vm-view-player" style="display:none;">
                    <video id="vm-view-video" controls class="vm-video-player" style="width:100%; max-height:300px; border-radius:10px;"></video>
                </div>

                <div class="vm-view-actions">
                    <button type="button" class="sg-btn vm-btn-cancel" data-vm-close="view">Close</button>
                    <button type="button" class="sg-btn sg-btn--primary" id="vmViewEditBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit Video
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Thumbnail Preview / Video Player Modal -->
    <div class="sg-modal" id="vmPreviewModal">
        <div class="sg-modal__overlay" data-vm-close="preview"></div>
        <div class="sg-modal__dialog vm-preview-dialog">
            <button type="button" class="sg-modal__close" data-vm-close="preview">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body vm-preview-body">
                <div class="vm-preview-container" id="vm-preview-container">
                    <img src="" alt="" id="vm-preview-thumb" class="vm-preview-thumb" style="display:none;">
                    <div class="vm-preview-fallback" id="vm-preview-fallback" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <span>No thumbnail available</span>
                    </div>
                    <button type="button" class="vm-preview-play-btn" id="vm-preview-play-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                            <polygon points="6 3 20 12 6 21 6 3"/>
                        </svg>
                    </button>
                    <video id="vm-preview-video" controls preload="none" class="vm-preview-video" style="display:none;"></video>
                </div>
                <div class="vm-preview-title" id="vm-preview-title"></div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <input type="hidden" id="vm-delete-id" value="">
    <div class="sg-modal" id="vmDeleteModal">
        <div class="sg-modal__overlay" data-vm-close="delete"></div>
        <div class="sg-modal__dialog vm-modal--delete">
            <button type="button" class="sg-modal__close" data-vm-close="delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body vm-delete-body">
                <div class="vm-delete-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Delete Video</h2>
                <p class="sg-modal__subtitle">Are you sure you want to delete <strong id="vm-delete-video-name"></strong>? This action cannot be undone.</p>
                <div class="vm-form__actions vm-delete-actions">
                    <button type="button" class="sg-btn vm-btn-cancel" data-vm-close="delete">Cancel</button>
                    <button type="button" class="sg-btn vm-btn-danger" id="vmConfirmDeleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete Video
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php
include __DIR__ . '/../layout/admin-layout-end.php';
?>