<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/home';
$jsBase = $websiteBase . '/js/home';
$currentPage = 'home';

require_once __DIR__ . '/includes/music-data.php';
$latestMusic = wgGetAllMusic(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SOUND Group Music &amp; Video Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/music_card/music_card.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/video_card/video_card.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/profile_modal/profile_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/loaders/button-spinner.css">
</head>
<body>

<?php include __DIR__ . '/components/layout/navbar/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="wg-hero" id="hero">
    <div class="wg-hero__inner">
        <div class="wg-hero__content">
            <span class="wg-hero__badge">
                <span class="wg-hero__badge-dot"></span>
                Your Music. Your Vibe.
            </span>
            <h1 class="wg-hero__title">Discover the <span class="wg-hero__title-accent">Sound of Tomorrow</span></h1>
            <p class="wg-hero__subtitle">Stream unlimited music and videos. Explore artists, albums, and curated playlists crafted just for you.</p>
            <div class="wg-hero__actions">
                <a href="#music" class="wg-btn wg-btn--primary wg-btn--lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    Browse Music
                </a>
                <a href="#videos" class="wg-btn wg-btn--outline wg-btn--lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                    Browse Videos
                </a>
            </div>
        </div>
        <div class="wg-hero__visual">
            <div class="wg-cd">
                <div class="wg-cd__disc">
                    <div class="wg-cd__ring wg-cd__ring--outer"></div>
                    <div class="wg-cd__ring wg-cd__ring--middle"></div>
                    <div class="wg-cd__ring wg-cd__ring--inner"></div>
                </div>
                <div class="wg-cd__label">
                    <span class="wg-cd__label-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 18V5l12-2v13"/>
                            <circle cx="6" cy="18" r="3"/>
                            <circle cx="18" cy="16" r="3"/>
                        </svg>
                    </span>
                    <span class="wg-cd__brand">Sound Group</span>
                </div>
                <div class="wg-cd__hole"></div>
                <div class="wg-cd__glow"></div>
                <div class="wg-cd__particles">
                    <span class="wg-cd__particle"></span>
                    <span class="wg-cd__particle"></span>
                    <span class="wg-cd__particle"></span>
                    <span class="wg-cd__particle"></span>
                    <span class="wg-cd__particle"></span>
                    <span class="wg-cd__particle"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="wg-hero__bg-orb wg-hero__bg-orb--1"></div>
    <div class="wg-hero__bg-orb wg-hero__bg-orb--2"></div>
</section>
<!-- LATEST MUSIC SECTION -->
<section class="wg-section" id="music">
    <div class="wg-section__inner">
        <div class="wg-section__header">
            <h2 class="wg-section__title">Latest Music</h2>
            <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-section__link">View All <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg></a>
        </div>
        <div class="wg-cards wg-cards--music">
            <?php
            $placeholderCounter = 1;
            foreach ($latestMusic as $m):
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
                include __DIR__ . '/components/music_card/music_card.php';
            endforeach;
            if (empty($latestMusic)):
            ?>
                <p style="color:var(--wg-text-secondary);grid-column:1/-1;text-align:center;padding:2rem 0;">No music available yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- LATEST VIDEOS SECTION -->
<section class="wg-section wg-section--videos" id="videos">
    <div class="wg-section__inner">
        <div class="wg-section__header">
            <h2 class="wg-section__title">Latest Videos</h2>
            <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-section__link">View All <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg></a>
        </div>
        <div class="wg-cards wg-cards--video">
            <?php
            $videoCards = [
                ['vc_id' => 1, 'vc_title' => 'Live Session Vol.1', 'vc_artist' => 'Hammad Aziz', 'vc_album' => 'Acoustic Sessions', 'vc_year' => '2025', 'vc_genre' => 'Acoustic', 'vc_language' => 'English', 'vc_duration' => '4:12', 'vc_placeholder' => 1],
                ['vc_id' => 2, 'vc_title' => 'Music Video Premiere', 'vc_artist' => 'Aria Collins', 'vc_album' => 'Electric Dreams', 'vc_year' => '2024', 'vc_genre' => 'Pop', 'vc_language' => 'English', 'vc_duration' => '3:45', 'vc_placeholder' => 2],
                ['vc_id' => 3, 'vc_title' => 'Behind The Scenes', 'vc_artist' => 'Kai Moreno', 'vc_album' => 'Lo-fi Nights', 'vc_year' => '2025', 'vc_genre' => 'Documentary', 'vc_language' => 'English', 'vc_duration' => '5:30', 'vc_placeholder' => 3],
                ['vc_id' => 4, 'vc_title' => 'Studio Session', 'vc_artist' => 'Luna Park', 'vc_album' => 'Chill Vibes', 'vc_year' => '2024', 'vc_genre' => 'Lo-fi', 'vc_language' => 'English', 'vc_duration' => '6:18', 'vc_placeholder' => 4],
                ['vc_id' => 5, 'vc_title' => 'Summer Tour Recap', 'vc_artist' => 'Hammad Aziz', 'vc_album' => 'Night Sessions', 'vc_year' => '2025', 'vc_genre' => 'Vlog', 'vc_language' => 'English', 'vc_duration' => '3:22', 'vc_placeholder' => 5],
            ];
            foreach ($videoCards as $card) {
                extract($card);
                include __DIR__ . '/components/video_card/video_card.php';
            }
            ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/components/layout/footer/footer.php'; ?>

<script src="<?php echo $jsBase; ?>/website.js"></script>
</body>
</html>