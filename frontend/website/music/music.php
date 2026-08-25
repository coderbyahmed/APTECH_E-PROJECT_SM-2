<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/music';
$jsBase = $websiteBase . '/js/music';
$currentPage = 'music';
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
            <span class="wg-music-filters__count" id="resultCount">12 songs</span>
        </div>
    </div>
</section>

<!-- MUSIC CARDS SECTION -->
<section class="wg-music-grid-section">
    <div class="wg-music-grid-section__inner">
        <div class="wg-cards wg-cards--music wg-music-page__cards" id="musicGrid">
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
            foreach ($musicCards as $card) {
                extract($card);
                echo '<div class="wg-music-card-wrap" '
                    . 'data-title="' . htmlspecialchars($mc_title, ENT_QUOTES) . '" '
                    . 'data-artist="' . htmlspecialchars($mc_artist, ENT_QUOTES) . '" '
                    . 'data-album="' . htmlspecialchars($mc_album, ENT_QUOTES) . '" '
                    . 'data-year="' . htmlspecialchars($mc_year, ENT_QUOTES) . '" '
                    . 'data-genre="' . htmlspecialchars($mc_genre, ENT_QUOTES) . '" '
                    . 'data-language="' . htmlspecialchars($mc_language, ENT_QUOTES) . '">';
                include __DIR__ . '/../components/music_card/music_card.php';
                echo '</div>';
            }
            ?>
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
