<?php
/**
 * SOUND Group — Music Management
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireAuth();

$pageTitle = 'Music Management';
$activeItem = 'music-management';

$db = getDb();

$cmData = [];
foreach (['artists', 'albums', 'air', 'genres', 'languages'] as $table) {
    $stmt = $db->query("SELECT `id`, `name` FROM `" . $table . "` ORDER BY `name` ASC");
    $cmData[$table] = $stmt->fetchAll();
}

$stmt = $db->query("SELECT m.*,
    a.`name` AS `artist_name`,
    al.`name` AS `album_name`,
    y.`name` AS `year_name`,
    g.`name` AS `genre_name`,
    l.`name` AS `language_name`
FROM `music` m
LEFT JOIN `artists` a ON a.`id` = m.`artist_id`
LEFT JOIN `albums` al ON al.`id` = m.`album_id`
LEFT JOIN `air` y ON y.`id` = m.`year_id`
LEFT JOIN `genres` g ON g.`id` = m.`genre_id`
LEFT JOIN `languages` l ON l.`id` = m.`language_id`
ORDER BY m.`id` DESC");
$musicRecords = $stmt->fetchAll();

$musicData = [];
foreach ($musicRecords as $row) {
    $musicData[] = [
        'id'            => (int) $row['id'],
        'song_title'    => $row['song_title'],
        'artist_id'     => $row['artist_id'] !== null ? (int) $row['artist_id'] : null,
        'artist_name'   => $row['artist_name'] ?? '',
        'album_id'      => $row['album_id'] !== null ? (int) $row['album_id'] : null,
        'album_name'    => $row['album_name'] ?? '',
        'year_id'       => $row['year_id'] !== null ? (int) $row['year_id'] : null,
        'year_name'     => $row['year_name'] ?? '',
        'genre_id'      => $row['genre_id'] !== null ? (int) $row['genre_id'] : null,
        'genre_name'    => $row['genre_name'] ?? '',
        'language_id'   => $row['language_id'] !== null ? (int) $row['language_id'] : null,
        'language_name' => $row['language_name'] ?? '',
        'description'   => $row['description'] ?? '',
        'music_file'    => $row['music_file'] ?? '',
        'cover_image'   => $row['cover_image'] ?? '',
        'status'        => $row['status'],
        'created_at'    => $row['created_at'] ?? '',
        'updated_at'    => $row['updated_at'] ?? '',
    ];
}

include __DIR__ . '/../layout/admin-layout.php';
?>

    <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
    <script type="application/json" id="mmCategoriesJson"><?php echo json_encode($cmData); ?></script>
    <script type="application/json" id="mmMusicJson"><?php echo json_encode($musicData); ?></script>

    <div class="mm-header">
        <div class="mm-header__left">
            <h1 class="mm-header__title">Music Management</h1>
            <p class="mm-header__subtitle">Manage your music catalog. Add, edit, and organize your songs, albums, and artist information.</p>
        </div>
        <div class="mm-header__right">
            <button type="button" class="sg-btn sg-btn--primary mm-btn-add" id="mmAddMusicBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Music
            </button>
        </div>
    </div>

    <div class="mm-table-card">
        <div class="mm-table-card__header">
            <h2 class="mm-table-card__title">All Music</h2>
            <div class="mm-table-card__search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="mm-search-input" placeholder="Search music..." id="mmSearchInput">
            </div>
        </div>

        <div class="mm-table-wrapper">
            <table class="mm-table">
                <thead>
                    <tr>
                        <th class="mm-table__th">Music</th>
                        <th class="mm-table__th">Artist</th>
                        <th class="mm-table__th">Album</th>
                        <th class="mm-table__th">Year</th>
                        <th class="mm-table__th">Genre</th>
                        <th class="mm-table__th">Language</th>
                        <th class="mm-table__th">Status</th>
                        <th class="mm-table__th mm-table__th--actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="mmTableBody">
                </tbody>
            </table>
        </div>

        <div class="mm-mobile-cards" id="mmMobileCards"></div>

        <div class="mm-table-card__footer">
            <span class="mm-table-card__count" id="mmCount">Showing 0 music</span>
        </div>
    </div>

    <!-- Add Music Modal -->
    <div class="sg-modal" id="mmAddModal">
        <div class="sg-modal__overlay" data-mm-close="add"></div>
        <div class="sg-modal__dialog mm-modal--wide">
            <button type="button" class="sg-modal__close" data-mm-close="add">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M9 18V5l12-2v13"/>
                        <circle cx="6" cy="18" r="3"/>
                        <circle cx="18" cy="16" r="3"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Add New Music</h2>
                <p class="sg-modal__subtitle">Fill in the details below to add a new music track to your catalog.</p>

                <form id="mmAddForm" class="mm-form" enctype="multipart/form-data">
                    <div class="mm-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-add-title">Song Title</label>
                            <input type="text" class="sg-form-input mm-form-input" id="mm-add-title" placeholder="Enter song title">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-add-artist">Artist</label>
                            <select class="sg-form-input mm-form-input" id="mm-add-artist">
                                <option value="">Select artist</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-add-album">Album</label>
                            <select class="sg-form-input mm-form-input" id="mm-add-album">
                                <option value="">Select album</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-add-year">Year</label>
                            <select class="sg-form-input mm-form-input" id="mm-add-year">
                                <option value="">Select year</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-add-genre">Genre</label>
                            <select class="sg-form-input mm-form-input" id="mm-add-genre">
                                <option value="">Select genre</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-add-language">Language</label>
                            <select class="sg-form-input mm-form-input" id="mm-add-language">
                                <option value="">Select language</option>
                            </select>
                        </div>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="mm-add-description">Description</label>
                        <textarea class="sg-form-input mm-form-input mm-form-textarea" id="mm-add-description" placeholder="Enter a brief description of the music" rows="3"></textarea>
                    </div>

                    <div class="mm-form__grid mm-form__grid--2col">
                        <div class="sg-form-group">
                            <label class="sg-form-label">Music File</label>
                            <div class="mm-upload-area" id="mm-music-upload">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <path d="M9 18V5l12-2v13"/>
                                    <circle cx="6" cy="18" r="3"/>
                                    <circle cx="18" cy="16" r="3"/>
                                </svg>
                                <span class="mm-upload-area__text">Drop music file here or <span class="mm-upload-area__browse">browse</span></span>
                                <span class="mm-upload-area__hint">MP3, WAV, FLAC, AAC (Max 50MB)</span>
                                <input type="file" class="mm-upload-area__input" accept=".mp3,.wav,.flac,.aac" id="mm-add-music-file">
                            </div>
                            <div class="mm-upload-preview" id="mm-music-preview" style="display:none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                    <path d="M9 18V5l12-2v13"/>
                                    <circle cx="6" cy="18" r="3"/>
                                    <circle cx="18" cy="16" r="3"/>
                                </svg>
                                <span class="mm-upload-preview__name" id="mm-music-file-name"></span>
                                <button type="button" class="mm-upload-preview__remove" id="mm-music-file-remove">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="sg-form-group">
                            <label class="sg-form-label">Cover Image</label>
                            <div class="mm-upload-area mm-upload-area--image" id="mm-cover-upload">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span class="mm-upload-area__text">Drop cover image or <span class="mm-upload-area__browse">browse</span></span>
                                <span class="mm-upload-area__hint">JPG, PNG, WebP (Max 5MB)</span>
                                <input type="file" class="mm-upload-area__input" accept=".jpg,.jpeg,.png,.webp" id="mm-add-cover-image">
                            </div>
                            <div class="mm-upload-preview mm-upload-preview--image" id="mm-cover-preview" style="display:none;">
                                <img src="" alt="Cover preview" id="mm-cover-preview-img" class="mm-cover-preview-img">
                                <div class="mm-upload-preview__info">
                                    <span class="mm-upload-preview__name" id="mm-cover-file-name"></span>
                                    <button type="button" class="mm-upload-preview__remove" id="mm-cover-file-remove">
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
                        <label class="sg-form-label" for="mm-add-status">Status</label>
                        <select class="sg-form-input mm-form-input" id="mm-add-status">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mm-form__actions">
                        <button type="button" class="sg-btn mm-btn-cancel" data-mm-close="add">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="mmSaveMusicBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save Music
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Music Modal -->
    <div class="sg-modal" id="mmEditModal">
        <div class="sg-modal__overlay" data-mm-close="edit"></div>
        <div class="sg-modal__dialog mm-modal--wide">
            <button type="button" class="sg-modal__close" data-mm-close="edit">
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
                <h2 class="sg-modal__title">Edit Music</h2>
                <p class="sg-modal__subtitle">Update the details for <strong id="mm-edit-song-name">Midnight Dreams</strong>.</p>

                <form id="mmEditForm" class="mm-form" enctype="multipart/form-data">
                    <input type="hidden" id="mm-edit-id" value="">
                    <div class="mm-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-edit-title">Song Title</label>
                            <input type="text" class="sg-form-input mm-form-input" id="mm-edit-title" placeholder="Enter song title">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-edit-artist">Artist</label>
                            <select class="sg-form-input mm-form-input" id="mm-edit-artist">
                                <option value="">Select artist</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-edit-album">Album</label>
                            <select class="sg-form-input mm-form-input" id="mm-edit-album">
                                <option value="">Select album</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-edit-year">Year</label>
                            <select class="sg-form-input mm-form-input" id="mm-edit-year">
                                <option value="">Select year</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-edit-genre">Genre</label>
                            <select class="sg-form-input mm-form-input" id="mm-edit-genre">
                                <option value="">Select genre</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="mm-edit-language">Language</label>
                            <select class="sg-form-input mm-form-input" id="mm-edit-language">
                                <option value="">Select language</option>
                            </select>
                        </div>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="mm-edit-description">Description</label>
                            <textarea class="sg-form-input mm-form-input mm-form-textarea" id="mm-edit-description" placeholder="Enter a brief description of the music" rows="3"></textarea>
                    </div>

                    <div class="mm-form__grid mm-form__grid--2col">
                        <div class="sg-form-group">
                            <label class="sg-form-label">Music File</label>
                            <div class="mm-upload-area" id="mm-edit-music-upload" style="display:none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <path d="M9 18V5l12-2v13"/>
                                    <circle cx="6" cy="18" r="3"/>
                                    <circle cx="18" cy="16" r="3"/>
                                </svg>
                                <span class="mm-upload-area__text">Drop music file here or <span class="mm-upload-area__browse">browse</span></span>
                                <span class="mm-upload-area__hint">MP3, WAV, FLAC, AAC (Max 50MB)</span>
                                <input type="file" class="mm-upload-area__input" accept=".mp3,.wav,.flac,.aac" id="mm-edit-music-file">
                            </div>
                            <div class="mm-upload-preview" id="mm-edit-music-preview">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                    <path d="M9 18V5l12-2v13"/>
                                    <circle cx="6" cy="18" r="3"/>
                                    <circle cx="18" cy="16" r="3"/>
                                </svg>
                                <span class="mm-upload-preview__name" id="mm-edit-music-name"></span>
                                <button type="button" class="mm-upload-preview__remove mm-upload-preview__remove--replace" id="mm-edit-music-replace">Replace</button>
                            </div>
                        </div>

                        <div class="sg-form-group">
                            <label class="sg-form-label">Cover Image</label>
                            <div class="mm-upload-area mm-upload-area--image" id="mm-edit-cover-upload" style="display:none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span class="mm-upload-area__text">Drop cover image or <span class="mm-upload-area__browse">browse</span></span>
                                <span class="mm-upload-area__hint">JPG, PNG, WebP (Max 5MB)</span>
                                <input type="file" class="mm-upload-area__input" accept=".jpg,.jpeg,.png,.webp" id="mm-edit-cover-image">
                            </div>
                            <div class="mm-upload-preview mm-upload-preview--image" id="mm-edit-cover-preview">
                                <img src="" alt="Cover" id="mm-edit-cover-img" class="mm-cover-preview-img" style="display:none;">
                                <div class="mm-cover-placeholder" id="mm-edit-cover-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                </div>
                                <div class="mm-upload-preview__info">
                                    <span class="mm-upload-preview__name" id="mm-edit-cover-name"></span>
                                    <button type="button" class="mm-upload-preview__remove mm-upload-preview__remove--replace" id="mm-edit-cover-replace">Replace</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="mm-edit-status">Status</label>
                        <select class="sg-form-input mm-form-input" id="mm-edit-status">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mm-form__actions">
                        <button type="button" class="sg-btn mm-btn-cancel" data-mm-close="edit">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="mmUpdateMusicBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Update Music
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Music Modal -->
    <div class="sg-modal" id="mmViewModal">
        <div class="sg-modal__overlay" data-mm-close="view"></div>
        <div class="sg-modal__dialog mm-modal--view">
            <button type="button" class="sg-modal__close" data-mm-close="view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="mm-view-header">
                    <div class="mm-view-cover" id="mm-view-cover-wrap">
                        <img src="" alt="Cover" id="mm-view-cover-img" class="mm-view-cover-img" style="display:none;">
                        <svg id="mm-view-cover-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="40" height="40">
                            <path d="M9 18V5l12-2v13"/>
                            <circle cx="6" cy="18" r="3"/>
                            <circle cx="18" cy="16" r="3"/>
                        </svg>
                    </div>
                    <div class="mm-view-info">
                        <h2 class="mm-view-info__title" id="mm-view-title"></h2>
                        <p class="mm-view-info__artist" id="mm-view-artist"></p>
                        <span class="mm-badge mm-badge--active" id="mm-view-status"></span>
                    </div>
                </div>

                <div class="mm-view-details">
                    <div class="mm-view-detail">
                        <span class="mm-view-detail__label">Album</span>
                        <span class="mm-view-detail__value" id="mm-view-album"></span>
                    </div>
                    <div class="mm-view-detail">
                        <span class="mm-view-detail__label">Year</span>
                        <span class="mm-view-detail__value" id="mm-view-year"></span>
                    </div>
                    <div class="mm-view-detail">
                        <span class="mm-view-detail__label">Genre</span>
                        <span class="mm-view-detail__value" id="mm-view-genre"></span>
                    </div>
                    <div class="mm-view-detail">
                        <span class="mm-view-detail__label">Language</span>
                        <span class="mm-view-detail__value" id="mm-view-language"></span>
                    </div>
                    <div class="mm-view-detail mm-view-detail--full">
                        <span class="mm-view-detail__label">Description</span>
                        <span class="mm-view-detail__value" id="mm-view-description"></span>
                    </div>
                    <div class="mm-view-detail mm-view-detail--full">
                        <span class="mm-view-detail__label">Music File</span>
                        <span class="mm-view-detail__value mm-view-detail__value--file" id="mm-view-music-file"></span>
                    </div>
                </div>

                <div class="mm-view-player" id="mm-view-player" style="display:none;">
                    <audio id="mm-view-audio" controls class="mm-audio-player"></audio>
                </div>

                <div class="mm-view-actions">
                    <button type="button" class="sg-btn mm-btn-cancel" data-mm-close="view">Close</button>
                    <button type="button" class="sg-btn sg-btn--primary" id="mmViewEditBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit Music
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <input type="hidden" id="mm-delete-id" value="">
    <div class="sg-modal" id="mmDeleteModal">
        <div class="sg-modal__overlay" data-mm-close="delete"></div>
        <div class="sg-modal__dialog mm-modal--delete">
            <button type="button" class="sg-modal__close" data-mm-close="delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body mm-delete-body">
                <div class="mm-delete-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Delete Music</h2>
                <p class="sg-modal__subtitle">Are you sure you want to delete <strong id="mm-delete-song-name">Midnight Dreams</strong>? This action cannot be undone.</p>
                <div class="mm-form__actions mm-delete-actions">
                    <button type="button" class="sg-btn mm-btn-cancel" data-mm-close="delete">Cancel</button>
                    <button type="button" class="sg-btn mm-btn-danger" id="mmConfirmDeleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete Music
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php
include __DIR__ . '/../layout/admin-layout-end.php';
?>
