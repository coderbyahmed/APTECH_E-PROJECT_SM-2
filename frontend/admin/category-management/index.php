<?php
/**
 * SOUND Group — Category Management
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireAuth();

$pageTitle = 'Category Management';
$activeItem = 'category-management';
$cmBaseUrl = baseUrl() . '/frontend/admin/category-management';
$cmCsrf    = csrfToken();

include __DIR__ . '/../layout/admin-layout.php';
?>

    <div class="cm-header">
        <div class="cm-header__left">
            <h1 class="cm-header__title">Category Management</h1>
            <p class="cm-header__subtitle">Manage categories used for organizing Music and Video content.</p>
        </div>
    </div>

    <div class="cm-categories">
        <!-- Year -->
        <div class="cm-category-card" id="cmYearCard">
            <div class="cm-category-card__icon cm-category-card__icon--year">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <h3 class="cm-category-card__title">Year</h3>
            <p class="cm-category-card__desc">Manage Years</p>
            <div class="cm-category-card__actions">
                <button type="button" class="sg-btn sg-btn--primary cm-category-card__btn" data-cm-open="year">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Year
                </button>
                <a href="<?php echo $cmBaseUrl; ?>/year-list.php" class="sg-btn cm-category-card__btn cm-category-card__btn--outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    View List
                </a>
            </div>
        </div>

        <!-- Artist -->
        <div class="cm-category-card" id="cmArtistCard">
            <div class="cm-category-card__icon cm-category-card__icon--artist">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h3 class="cm-category-card__title">Artist</h3>
            <p class="cm-category-card__desc">Manage Artists</p>
            <div class="cm-category-card__actions">
                <button type="button" class="sg-btn sg-btn--primary cm-category-card__btn" data-cm-open="artist">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Artist
                </button>
                <a href="<?php echo $cmBaseUrl; ?>/artist-list.php" class="sg-btn cm-category-card__btn cm-category-card__btn--outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    View List
                </a>
            </div>
        </div>

        <!-- Album -->
        <div class="cm-category-card" id="cmAlbumCard">
            <div class="cm-category-card__icon cm-category-card__icon--album">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <circle cx="12" cy="12" r="10"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
            <h3 class="cm-category-card__title">Album</h3>
            <p class="cm-category-card__desc">Manage Albums</p>
            <div class="cm-category-card__actions">
                <button type="button" class="sg-btn sg-btn--primary cm-category-card__btn" data-cm-open="album">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Album
                </button>
                <a href="<?php echo $cmBaseUrl; ?>/album-list.php" class="sg-btn cm-category-card__btn cm-category-card__btn--outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    View List
                </a>
            </div>
        </div>

        <!-- Genre -->
        <div class="cm-category-card" id="cmGenreCard">
            <div class="cm-category-card__icon cm-category-card__icon--genre">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <path d="M9 18V5l12-2v13"/>
                    <circle cx="6" cy="18" r="3"/>
                    <circle cx="18" cy="16" r="3"/>
                </svg>
            </div>
            <h3 class="cm-category-card__title">Genre</h3>
            <p class="cm-category-card__desc">Manage Genres</p>
            <div class="cm-category-card__actions">
                <button type="button" class="sg-btn sg-btn--primary cm-category-card__btn" data-cm-open="genre">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Genre
                </button>
                <a href="<?php echo $cmBaseUrl; ?>/genre-list.php" class="sg-btn cm-category-card__btn cm-category-card__btn--outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    View List
                </a>
            </div>
        </div>

        <!-- Language -->
        <div class="cm-category-card" id="cmLanguageCard">
            <div class="cm-category-card__icon cm-category-card__icon--language">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
            </div>
            <h3 class="cm-category-card__title">Language</h3>
            <p class="cm-category-card__desc">Manage Languages</p>
            <div class="cm-category-card__actions">
                <button type="button" class="sg-btn sg-btn--primary cm-category-card__btn" data-cm-open="language">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Language
                </button>
                <a href="<?php echo $cmBaseUrl; ?>/language-list.php" class="sg-btn cm-category-card__btn cm-category-card__btn--outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    View List
                </a>
            </div>
        </div>
    </div>

    <!-- Add Year Modal -->
    <div class="sg-modal" id="cmYearModal">
        <div class="sg-modal__overlay" data-cm-close="year"></div>
        <div class="sg-modal__dialog cm-modal">
            <button type="button" class="sg-modal__close" data-cm-close="year">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Add Year</h2>
                <p class="sg-modal__subtitle">Add a new year to organize your content.</p>
                <form id="cmYearForm" class="cm-form" data-cm-category="year">
                    <input type="hidden" name="csrf_token" value="<?php echo $cmCsrf; ?>">
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cm-year-input">Enter Year</label>
                        <input type="number" class="sg-form-input cm-form-input" id="cm-year-input" name="name" placeholder="e.g. 2024" min="1900" max="2099">
                    </div>
                    <div class="cm-form__actions">
                        <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="year">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="cmAddYearBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Year
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Artist Modal -->
    <div class="sg-modal" id="cmArtistModal">
        <div class="sg-modal__overlay" data-cm-close="artist"></div>
        <div class="sg-modal__dialog cm-modal">
            <button type="button" class="sg-modal__close" data-cm-close="artist">
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
                <h2 class="sg-modal__title">Add Artist</h2>
                <p class="sg-modal__subtitle">Add a new artist name to your catalog.</p>
                <form id="cmArtistForm" class="cm-form" data-cm-category="artist">
                    <input type="hidden" name="csrf_token" value="<?php echo $cmCsrf; ?>">
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cm-artist-input">Artist Name</label>
                        <input type="text" class="sg-form-input cm-form-input" id="cm-artist-input" name="name" placeholder="e.g. Aurora Waves">
                    </div>
                    <div class="cm-form__actions">
                        <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="artist">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="cmAddArtistBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Artist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Album Modal -->
    <div class="sg-modal" id="cmAlbumModal">
        <div class="sg-modal__overlay" data-cm-close="album"></div>
        <div class="sg-modal__dialog cm-modal">
            <button type="button" class="sg-modal__close" data-cm-close="album">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Add Album</h2>
                <p class="sg-modal__subtitle">Add a new album name to your catalog.</p>
                <form id="cmAlbumForm" class="cm-form" data-cm-category="album">
                    <input type="hidden" name="csrf_token" value="<?php echo $cmCsrf; ?>">
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cm-album-input">Album Name</label>
                        <input type="text" class="sg-form-input cm-form-input" id="cm-album-input" name="name" placeholder="e.g. Neon Horizons">
                    </div>
                    <div class="cm-form__actions">
                        <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="album">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="cmAddAlbumBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Album
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Genre Modal -->
    <div class="sg-modal" id="cmGenreModal">
        <div class="sg-modal__overlay" data-cm-close="genre"></div>
        <div class="sg-modal__dialog cm-modal">
            <button type="button" class="sg-modal__close" data-cm-close="genre">
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
                <h2 class="sg-modal__title">Add Genre</h2>
                <p class="sg-modal__subtitle">Add a new genre to classify your content.</p>
                <form id="cmGenreForm" class="cm-form" data-cm-category="genre">
                    <input type="hidden" name="csrf_token" value="<?php echo $cmCsrf; ?>">
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cm-genre-input">Genre Name</label>
                        <input type="text" class="sg-form-input cm-form-input" id="cm-genre-input" name="name" placeholder="e.g. Pop, Rock, Jazz">
                    </div>
                    <div class="cm-form__actions">
                        <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="genre">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="cmAddGenreBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Genre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Language Modal -->
    <div class="sg-modal" id="cmLanguageModal">
        <div class="sg-modal__overlay" data-cm-close="language"></div>
        <div class="sg-modal__dialog cm-modal">
            <button type="button" class="sg-modal__close" data-cm-close="language">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Add Language</h2>
                <p class="sg-modal__subtitle">Add a new language for content classification.</p>
                <form id="cmLanguageForm" class="cm-form" data-cm-category="language">
                    <input type="hidden" name="csrf_token" value="<?php echo $cmCsrf; ?>">
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cm-language-input">Language Name</label>
                        <input type="text" class="sg-form-input cm-form-input" id="cm-language-input" name="name" placeholder="e.g. English, Spanish, Hindi">
                    </div>
                    <div class="cm-form__actions">
                        <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="language">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="cmAddLanguageBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Language
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/admin-layout-end.php'; ?>
