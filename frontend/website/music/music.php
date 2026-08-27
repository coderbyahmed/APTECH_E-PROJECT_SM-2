<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/music';
$jsBase = $websiteBase . '/js/music';
$currentPage = 'music';

require_once dirname(__DIR__, 1) . '/includes/music-data.php';
$allMusic = wgGetAllMusic(0, 'published');
$musicCount = count($allMusic);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Music Library - SOUND Group</title>
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
                    <option value="Hammad Aziz">Hammad Aziz</option>
                    <option value="Arijit Singh">Arijit Singh</option>
                    <option value="Aria Collins">Aria Collins</option>
                    <option value="Kai Moreno">Kai Moreno</option>
                    <option value="Luna Park">Luna Park</option>
                    <option value="Hamza Tahir">Hamza Tahir</option>
                    <option value="Ahmed Raza">Ahmed Raza</option>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterAlbum">Album</label>
                <select class="wg-music-filters__select" id="filterAlbum">
                    <option value="">All Albums</option>
                    <option value="Night Sessions">Night Sessions</option>
                    <option value="Aashiqui 2">Aashiqui 2</option>
                    <option value="Electric Dreams">Electric Dreams</option>
                    <option value="Lo-fi Nights">Lo-fi Nights</option>
                    <option value="Chill Vibes">Chill Vibes</option>
                    <option value="Sade">Sade</option>
                    <option value="Acoustic Sessions">Acoustic Sessions</option>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterYear">Year</label>
                <select class="wg-music-filters__select" id="filterYear">
                    <option value="">All Years</option>
                    <option value="2013">2013</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterGenre">Genre</label>
                <select class="wg-music-filters__select" id="filterGenre">
                    <option value="">All Genres</option>
                    <option value="Pop">Pop</option>
                    <option value="Bollywood">Bollywood</option>
                    <option value="Electronic">Electronic</option>
                    <option value="Lo-fi">Lo-fi</option>
                    <option value="Chill">Chill</option>
                    <option value="Sufi">Sufi</option>
                    <option value="Acoustic">Acoustic</option>
                    <option value="R&B">R&B</option>
                </select>
            </div>
            <div class="wg-music-filters__group">
                <label class="wg-music-filters__label" for="filterLanguage">Language</label>
                <select class="wg-music-filters__select" id="filterLanguage">
                    <option value="">All Languages</option>
                    <option value="English">English</option>
                    <option value="Hindi">Hindi</option>
                    <option value="Urdu">Urdu</option>
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
