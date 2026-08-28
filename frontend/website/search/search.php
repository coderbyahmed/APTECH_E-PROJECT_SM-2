<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/search';
$jsBase = $websiteBase . '/js/search';
$currentPage = 'search';

require_once dirname(__DIR__, 1) . '/includes/music-data.php';
require_once dirname(__DIR__, 1) . '/includes/video-data.php';
require_once __DIR__ . '/../../../backend/helpers/media-duration.php';
$allMusic = wgGetAllMusic(0, 'published');
$allVideos = wgGetAllVideos(0, 'published');
$totalCount = count($allMusic) + count($allVideos);

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
    <title>Search - <?php echo $wsWebsiteName; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/music_card/music_card.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/video_card/video_card.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/search.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/profile_modal/profile_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/loaders/button-spinner.css">
</head>
<body class="wg-page--search">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- SEARCH CONTENT -->
<main class="wg-search">
    <div class="wg-search__inner">

        <!-- Search Header -->
        <div class="wg-search__header">
            <h1 class="wg-search__title">Search</h1>
            <p class="wg-search__subtitle">Find your favorite music and videos</p>
        </div>

        <!-- Search Input -->
        <div class="wg-search__input-wrap">
            <span class="wg-search__input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" class="wg-search__input" id="searchInput" placeholder="Search for music or videos..." autofocus>
            <button class="wg-search__input-clear" id="searchClear" type="button" aria-label="Clear search" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <button class="wg-search__input-search-btn" id="searchBtn" type="button" aria-label="Search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </div>

        <!-- Filter Toggles -->
        <div class="wg-search__filters">
            <button class="wg-search__filter-btn wg-search__filter-btn--active" data-filter="all" type="button">All</button>
            <button class="wg-search__filter-btn" data-filter="music" type="button">Music</button>
            <button class="wg-search__filter-btn" data-filter="videos" type="button">Videos</button>
        </div>

        <!-- Results Count -->
        <div class="wg-search__results-info" id="resultsInfo">
            <span id="resultsCount"><?php echo $totalCount; ?> results</span>
        </div>

        <!-- Results Grid -->
        <div class="wg-search__results" id="searchResults">
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
                $mc_duration = formatDuration($m['duration'] ?? null);
                $placeholderCounter = ($placeholderCounter % 5) + 1;
                echo '<div class="wg-search-card-wrap" data-type="music" '
                    . 'data-title="' . htmlspecialchars($mc_title, ENT_QUOTES) . '" '
                    . 'data-artist="' . htmlspecialchars($mc_artist, ENT_QUOTES) . '">';
                include __DIR__ . '/../components/music_card/music_card.php';
                echo '</div>';
            endforeach;

            $placeholderCounter = 1;
            foreach ($allVideos as $v):
                $vc_id = (int)$v['id'];
                $vc_title = $v['video_title'];
                $vc_artist = $v['artist_name'] ?: 'Unknown Artist';
                $vc_album = $v['album_name'] ?: '';
                $vc_year = $v['year_name'] ?: '';
                $vc_genre = $v['genre_name'] ?: '';
                $vc_language = $v['language_name'] ?: '';
                $vc_duration = formatDuration($v['duration'] ?? null);
                $vc_placeholder = $placeholderCounter;
                $vc_thumbnail = $v['thumbnail_path'] ?: '';
                $placeholderCounter = ($placeholderCounter % 5) + 1;
                echo '<div class="wg-search-card-wrap" data-type="video" '
                    . 'data-title="' . htmlspecialchars($vc_title, ENT_QUOTES) . '" '
                    . 'data-artist="' . htmlspecialchars($vc_artist, ENT_QUOTES) . '">';
                include __DIR__ . '/../components/video_card/video_card.php';
                echo '</div>';
            endforeach;
            ?>
        </div>

        <!-- Empty State (no search results) -->
        <div class="wg-search__empty" id="searchEmpty" style="display:none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
            <p class="wg-search__empty-title" id="emptyTitle">No results found</p>
            <p class="wg-search__empty-desc" id="emptyDesc">Try searching for another music or video.</p>
        </div>

        <!-- Empty State (no content in database) -->
        <?php if ($totalCount === 0): ?>
        <div class="wg-search__empty" style="display:block;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
            <p class="wg-search__empty-title">No content available</p>
            <p class="wg-search__empty-desc">Check back later for new music and videos.</p>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>

<script src="<?php echo $jsBase; ?>/search.js"></script>
</body>
</html>
