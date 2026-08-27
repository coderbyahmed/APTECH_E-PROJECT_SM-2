<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/search';
$jsBase = $websiteBase . '/js/search';
$currentPage = 'search';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search - SOUND Group</title>
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
            <span id="resultsCount">24 results</span>
        </div>

        <!-- Results Grid -->
        <div class="wg-search__results" id="searchResults">
            <?php
            $musicCards = [
                ['mc_id' => 1, 'mc_title' => 'Midnight Dreams', 'mc_artist' => 'Hammad Aziz', 'mc_album' => 'Night Sessions', 'mc_year' => '2025', 'mc_genre' => 'Pop', 'mc_language' => 'English', 'mc_placeholder' => 1],
                ['mc_id' => 2, 'mc_title' => 'Tum Hi Ho', 'mc_artist' => 'Arijit Singh', 'mc_album' => 'Aashiqui 2', 'mc_year' => '2013', 'mc_genre' => 'Bollywood', 'mc_language' => 'Hindi', 'mc_placeholder' => 2],
                ['mc_id' => 3, 'mc_title' => 'Neon Lights', 'mc_artist' => 'Aria Collins', 'mc_album' => 'Electric Dreams', 'mc_year' => '2024', 'mc_genre' => 'Electronic', 'mc_language' => 'English', 'mc_placeholder' => 3],
                ['mc_id' => 4, 'mc_title' => 'City Rain', 'mc_artist' => 'Kai Moreno', 'mc_album' => 'Lo-fi Nights', 'mc_year' => '2025', 'mc_genre' => 'Lo-fi', 'mc_language' => 'English', 'mc_placeholder' => 4],
                ['mc_id' => 5, 'mc_title' => 'Summer Breeze', 'mc_artist' => 'Luna Park', 'mc_album' => 'Chill Vibes', 'mc_year' => '2024', 'mc_genre' => 'Chill', 'mc_language' => 'English', 'mc_placeholder' => 5],
                ['mc_id' => 6, 'mc_title' => 'Dil Ka Rishta', 'mc_artist' => 'Hamza Tahir', 'mc_album' => 'Sade', 'mc_year' => '2026', 'mc_genre' => 'Sufi', 'mc_language' => 'Urdu', 'mc_placeholder' => 1],
                ['mc_id' => 7, 'mc_title' => 'Electric Soul', 'mc_artist' => 'Aria Collins', 'mc_album' => 'Electric Dreams', 'mc_year' => '2024', 'mc_genre' => 'Electronic', 'mc_language' => 'English', 'mc_placeholder' => 3],
                ['mc_id' => 8, 'mc_title' => 'Raaste Bhool Gaye', 'mc_artist' => 'Ahmed Raza', 'mc_album' => 'Aashiqui 2', 'mc_year' => '2013', 'mc_genre' => 'Bollywood', 'mc_language' => 'Hindi', 'mc_placeholder' => 2],
                ['mc_id' => 9, 'mc_title' => 'Ocean Waves', 'mc_artist' => 'Kai Moreno', 'mc_album' => 'Lo-fi Nights', 'mc_year' => '2025', 'mc_genre' => 'Lo-fi', 'mc_language' => 'English', 'mc_placeholder' => 4],
                ['mc_id' => 10, 'mc_title' => 'Starlight', 'mc_artist' => 'Luna Park', 'mc_album' => 'Chill Vibes', 'mc_year' => '2024', 'mc_genre' => 'R&B', 'mc_language' => 'English', 'mc_placeholder' => 5],
                ['mc_id' => 11, 'mc_title' => 'Noor', 'mc_artist' => 'Hammad Aziz', 'mc_album' => 'Sade', 'mc_year' => '2026', 'mc_genre' => 'Sufi', 'mc_language' => 'Urdu', 'mc_placeholder' => 1],
                ['mc_id' => 12, 'mc_title' => 'Acoustic Glow', 'mc_artist' => 'Arijit Singh', 'mc_album' => 'Acoustic Sessions', 'mc_year' => '2025', 'mc_genre' => 'Acoustic', 'mc_language' => 'Hindi', 'mc_placeholder' => 3],
            ];
            $videoCards = [
                ['vc_id' => 1, 'vc_title' => 'Live Session Vol.1', 'vc_artist' => 'Hammad Aziz', 'vc_album' => 'Acoustic Sessions', 'vc_year' => '2025', 'vc_genre' => 'Acoustic', 'vc_language' => 'English', 'vc_duration' => '4:12', 'vc_placeholder' => 1],
                ['vc_id' => 2, 'vc_title' => 'Music Video Premiere', 'vc_artist' => 'Aria Collins', 'vc_album' => 'Electric Dreams', 'vc_year' => '2024', 'vc_genre' => 'Pop', 'vc_language' => 'English', 'vc_duration' => '3:45', 'vc_placeholder' => 2],
                ['vc_id' => 3, 'vc_title' => 'Behind The Scenes', 'vc_artist' => 'Kai Moreno', 'vc_album' => 'Lo-fi Nights', 'vc_year' => '2025', 'vc_genre' => 'Documentary', 'vc_language' => 'English', 'vc_duration' => '5:30', 'vc_placeholder' => 3],
                ['vc_id' => 4, 'vc_title' => 'Studio Session', 'vc_artist' => 'Luna Park', 'vc_album' => 'Chill Vibes', 'vc_year' => '2024', 'vc_genre' => 'Lo-fi', 'vc_language' => 'English', 'vc_duration' => '6:18', 'vc_placeholder' => 4],
                ['vc_id' => 5, 'vc_title' => 'Summer Tour Recap', 'vc_artist' => 'Hammad Aziz', 'vc_album' => 'Night Sessions', 'vc_year' => '2025', 'vc_genre' => 'Vlog', 'vc_language' => 'English', 'vc_duration' => '3:22', 'vc_placeholder' => 5],
                ['vc_id' => 6, 'vc_title' => 'Dil Ka Rishta - Official Video', 'vc_artist' => 'Hamza Tahir', 'vc_album' => 'Sade', 'vc_year' => '2026', 'vc_genre' => 'Sufi', 'vc_language' => 'Urdu', 'vc_duration' => '4:55', 'vc_placeholder' => 1],
                ['vc_id' => 7, 'vc_title' => 'Acoustic Cover - Tum Hi Ho', 'vc_artist' => 'Arijit Singh', 'vc_album' => 'Aashiqui 2', 'vc_year' => '2024', 'vc_genre' => 'Bollywood', 'vc_language' => 'Hindi', 'vc_duration' => '5:10', 'vc_placeholder' => 2],
                ['vc_id' => 8, 'vc_title' => 'Live at the Rooftop', 'vc_artist' => 'Aria Collins', 'vc_album' => 'Electric Dreams', 'vc_year' => '2025', 'vc_genre' => 'Pop', 'vc_language' => 'English', 'vc_duration' => '38:22', 'vc_placeholder' => 3],
                ['vc_id' => 9, 'vc_title' => 'Lo-fi Study Session', 'vc_artist' => 'Kai Moreno', 'vc_album' => 'Lo-fi Nights', 'vc_year' => '2025', 'vc_genre' => 'Lo-fi', 'vc_language' => 'English', 'vc_duration' => '1:02:15', 'vc_placeholder' => 4],
                ['vc_id' => 10, 'vc_title' => 'Chill Vibes Visualizer', 'vc_artist' => 'Luna Park', 'vc_album' => 'Chill Vibes', 'vc_year' => '2024', 'vc_genre' => 'Electronic', 'vc_language' => 'English', 'vc_duration' => '4:30', 'vc_placeholder' => 5],
                ['vc_id' => 11, 'vc_title' => 'Midnight Dreams - Lyric Video', 'vc_artist' => 'Hammad Aziz', 'vc_album' => 'Night Sessions', 'vc_year' => '2025', 'vc_genre' => 'Pop', 'vc_language' => 'English', 'vc_duration' => '3:58', 'vc_placeholder' => 1],
                ['vc_id' => 12, 'vc_title' => 'Noor - Visual Album', 'vc_artist' => 'Hammad Aziz', 'vc_album' => 'Sade', 'vc_year' => '2026', 'vc_genre' => 'Sufi', 'vc_language' => 'Urdu', 'vc_duration' => '42:10', 'vc_placeholder' => 3],
            ];

            foreach ($musicCards as $card) {
                extract($card);
                echo '<div class="wg-search-card-wrap" data-type="music" '
                    . 'data-title="' . htmlspecialchars($mc_title, ENT_QUOTES) . '" '
                    . 'data-artist="' . htmlspecialchars($mc_artist, ENT_QUOTES) . '">';
                include __DIR__ . '/../components/music_card/music_card.php';
                echo '</div>';
            }

            foreach ($videoCards as $card) {
                extract($card);
                echo '<div class="wg-search-card-wrap" data-type="video" '
                    . 'data-title="' . htmlspecialchars($vc_title, ENT_QUOTES) . '" '
                    . 'data-artist="' . htmlspecialchars($vc_artist, ENT_QUOTES) . '">';
                include __DIR__ . '/../components/video_card/video_card.php';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Empty State -->
        <div class="wg-search__empty" id="searchEmpty" style="display:none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
            <p class="wg-search__empty-title" id="emptyTitle">No results found</p>
            <p class="wg-search__empty-desc" id="emptyDesc">Try searching for another music or video.</p>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>

<script src="<?php echo $jsBase; ?>/search.js"></script>
</body>
</html>
