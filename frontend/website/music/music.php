<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/music';
$jsBase = $websiteBase . '/js/music';
$currentPage = 'music';

require_once dirname(__DIR__, 1) . '/includes/music-data.php';
require_once __DIR__ . '/../../../backend/helpers/media-duration.php';
$allMusic = wgGetAllMusic(0, 'published');
$musicCount = count($allMusic);

$filterArtists = [];
$filterAlbums = [];
$filterYears = [];
$filterGenres = [];
$filterLanguages = [];
foreach ($allMusic as $m) {
    if (!empty($m['artist_name'])) $filterArtists[$m['artist_name']] = true;
    if (!empty($m['album_name'])) $filterAlbums[$m['album_name']] = true;
    if (!empty($m['year_name'])) $filterYears[$m['year_name']] = true;
    if (!empty($m['genre_name'])) $filterGenres[$m['genre_name']] = true;
    if (!empty($m['language_name'])) $filterLanguages[$m['language_name']] = true;
}
$filterArtists = array_keys($filterArtists);
$filterAlbums = array_keys($filterAlbums);
$filterYears = array_keys($filterYears);
$filterGenres = array_keys($filterGenres);
$filterLanguages = array_keys($filterLanguages);

require_once __DIR__ . '/../../../backend/includes/website-settings.php';
$ws = getWebsiteSettings();
$wsWebsiteName = htmlspecialchars($ws['website_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Music Library - <?php echo $wsWebsiteName; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/music_card/music_card.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/music.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/profile_modal/profile_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/loaders/button-spinner.css">
</head>
<body class="wg-page--music">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="wg-music-hero">
    <div class="wg-music-hero__inner">
        <h1 class="wg-music-hero__title">Music Library</h1>
        <p class="wg-music-hero__subtitle">Browse the complete Sound Group collection. Discover artists, albums, and tracks across every genre and language.</p>
    </div>
</section>

<!-- FILTERS SECTION -->
<section class="wg-music-filters" id="filters">
    <div class="wg-music-filters__inner">
        <div class="wg-music-filters__search">
            <span class="wg-music-filters__search-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" class="wg-music-filters__search-input" id="musicSearch" placeholder="Search music by title, artist, album...">
        </div>
        <div class="wg-music-filters__row">
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterArtist">Artist</label>
                <select class="wg-music-filters__select" id="filterArtist">
                    <option value="">All Artists</option>
                    <?php foreach ($filterArtists as $a): ?>
                    <option value="<?php echo htmlspecialchars($a, ENT_QUOTES); ?>"><?php echo htmlspecialchars($a); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterAlbum">Album</label>
                <select class="wg-music-filters__select" id="filterAlbum">
                    <option value="">All Albums</option>
                    <?php foreach ($filterAlbums as $al): ?>
                    <option value="<?php echo htmlspecialchars($al, ENT_QUOTES); ?>"><?php echo htmlspecialchars($al); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterYear">Year</label>
                <select class="wg-music-filters__select" id="filterYear">
                    <option value="">All Years</option>
                    <?php foreach ($filterYears as $y): ?>
                    <option value="<?php echo htmlspecialchars($y, ENT_QUOTES); ?>"><?php echo htmlspecialchars($y); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterGenre">Genre</label>
                <select class="wg-music-filters__select" id="filterGenre">
                    <option value="">All Genres</option>
                    <?php foreach ($filterGenres as $g): ?>
                    <option value="<?php echo htmlspecialchars($g, ENT_QUOTES); ?>"><?php echo htmlspecialchars($g); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterLanguage">Language</label>
                <select class="wg-music-filters__select" id="filterLanguage">
                    <option value="">All Languages</option>
                    <?php foreach ($filterLanguages as $l): ?>
                    <option value="<?php echo htmlspecialchars($l, ENT_QUOTES); ?>"><?php echo htmlspecialchars($l); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="wg-music-filters__actions">
            <button class="wg-music-filters__clear" id="clearFilters">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Clear Filters
            </button>
            <span class="wg-music-filters__count" id="resultCount"><?php echo $musicCount; ?> songs</span>
        </div>
    </div>
</section>

<!-- MUSIC CARDS SECTION -->
<section class="wg-music-grid-section">
    <div class="wg-music-grid-section__inner">
        <div class="wg-cards wg-cards--music wg-music-page__cards" id="musicGrid">
            <?php
            $placeholderCounter = 1;
            foreach ($allMusic as $m):
                $mc_id = (int)$m['id'];
                $mc_title = $m['song_title'];
                $mc_artist = $m['artist_name'] ?: 'Unknown Artist';
                $mc_album = $m['album_name'] ?: '';
                $mc_year = $m['year_name'] ?: '';
                $mc_genre = $m['genre_name'] ?: '';
                $mc_language = $m['language_name'] ?: '';
                $mc_placeholder = $placeholderCounter;
                $mc_cover_image = $m['cover_image'] ?: '';
                $mc_duration = $m['duration'] ?? '';
                $placeholderCounter = ($placeholderCounter % 5) + 1;
                echo '<div class="wg-music-card-wrap" '
                    . 'data-title="' . htmlspecialchars($mc_title, ENT_QUOTES) . '" '
                    . 'data-artist="' . htmlspecialchars($mc_artist, ENT_QUOTES) . '" '
                    . 'data-album="' . htmlspecialchars($mc_album, ENT_QUOTES) . '" '
                    . 'data-year="' . htmlspecialchars($mc_year, ENT_QUOTES) . '" '
                    . 'data-genre="' . htmlspecialchars($mc_genre, ENT_QUOTES) . '" '
                    . 'data-language="' . htmlspecialchars($mc_language, ENT_QUOTES) . '">';
                include __DIR__ . '/../components/music_card/music_card.php';
                echo '</div>';
            endforeach;
            if (empty($allMusic)):
            ?>
                <div class="wg-music-empty" style="display:block;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    <p class="wg-music-empty__title">No music available yet</p>
                    <p class="wg-music-empty__desc">Check back later for new releases.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- EMPTY STATE (hidden by default) -->
        <div class="wg-music-empty" id="musicEmpty" style="display:none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
            <p class="wg-music-empty__title">No songs found</p>
            <p class="wg-music-empty__desc">Try adjusting your filters or search terms.</p>
        </div>

        <!-- LOAD MORE -->
        <div class="wg-music-loadmore" id="loadMoreWrap">
            <button class="wg-btn wg-btn--outline wg-btn--lg" id="loadMoreBtn">
                Load More
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>

<script src="<?php echo $jsBase; ?>/music.js"></script>
</body>
</html>
