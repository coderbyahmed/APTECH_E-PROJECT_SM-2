<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/video_detail';
$jsBase = $websiteBase . '/js/video_detail';
$currentPage = 'videos';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$allVideos = [
    1  => ['title' => 'Live Session Vol.1', 'artist' => 'Hammad Aziz', 'album' => 'Acoustic Sessions', 'year' => '2025', 'genre' => 'Acoustic', 'language' => 'English', 'duration' => '4:12', 'placeholder' => 1, 'status' => 'Active', 'description' => 'An intimate live session featuring stripped-back acoustic performances. Hammad Aziz performs his greatest hits in a cozy studio setting, delivering raw emotion and powerful vocals that showcase his artistry without any production filters.'],
    2  => ['title' => 'Music Video Premiere', 'artist' => 'Aria Collins', 'album' => 'Electric Dreams', 'year' => '2024', 'genre' => 'Pop', 'language' => 'English', 'duration' => '3:45', 'placeholder' => 2, 'status' => 'Active', 'description' => 'The official music video premiere for Aria Collins\' biggest hit. Featuring stunning visual effects, choreography, and a cinematic storyline that perfectly complements the electrifying energy of the track.'],
    3  => ['title' => 'Behind The Scenes', 'artist' => 'Kai Moreno', 'album' => 'Lo-fi Nights', 'year' => '2025', 'genre' => 'Documentary', 'language' => 'English', 'duration' => '5:30', 'placeholder' => 3, 'status' => 'Active', 'description' => 'Go behind the scenes with Kai Moreno as he creates his signature lo-fi sound. See the studio setup, recording process, and the creative inspiration behind every track on the Lo-fi Nights album.'],
    4  => ['title' => 'Studio Session', 'artist' => 'Luna Park', 'album' => 'Chill Vibes', 'year' => '2024', 'genre' => 'Lo-fi', 'language' => 'English', 'duration' => '6:18', 'placeholder' => 4, 'status' => 'Active', 'description' => 'A mesmerizing studio session where Luna Park lays down tracks for the Chill Vibes album. Watch the creative process unfold as she blends organic instruments with electronic elements.'],
    5  => ['title' => 'Summer Tour Recap', 'artist' => 'Hammad Aziz', 'album' => 'Night Sessions', 'year' => '2025', 'genre' => 'Vlog', 'language' => 'English', 'duration' => '3:22', 'placeholder' => 5, 'status' => 'Active', 'description' => 'Relive the highlights from Hammad Aziz\'s sold-out summer tour. From backstage moments to explosive live performances, this recap captures the energy of an unforgettable concert series.'],
    6  => ['title' => 'Dil Ka Rishta - Official Video', 'artist' => 'Hamza Tahir', 'album' => 'Sade', 'year' => '2026', 'genre' => 'Sufi', 'language' => 'Urdu', 'duration' => '4:55', 'placeholder' => 1, 'status' => 'Active', 'description' => 'The official music video for Dil Ka Rishta, a soulful Sufi-inspired composition. Shot in stunning locations that blend traditional architecture with modern aesthetics, the video captures the spiritual essence of the song.'],
    7  => ['title' => 'Acoustic Cover - Tum Hi Ho', 'artist' => 'Arijit Singh', 'album' => 'Aashiqui 2', 'year' => '2024', 'genre' => 'Bollywood', 'language' => 'Hindi', 'duration' => '5:10', 'placeholder' => 2, 'status' => 'Active', 'description' => 'Arijit Singh performs an intimate acoustic cover of the iconic Tum Hi Ho. Stripped of all production, this version reveals the raw beauty and emotional depth of the beloved Bollywood ballad.'],
    8  => ['title' => 'Live at the Rooftop', 'artist' => 'Aria Collins', 'album' => 'Electric Dreams', 'year' => '2025', 'genre' => 'Pop', 'language' => 'English', 'duration' => '38:22', 'placeholder' => 3, 'status' => 'Active', 'description' => 'Aria Collins takes over the rooftop for an unforgettable live performance. Featuring the full band and special guests, this extended session showcases her incredible stage presence and vocal range.'],
    9  => ['title' => 'Lo-fi Study Session', 'artist' => 'Kai Moreno', 'album' => 'Lo-fi Nights', 'year' => '2025', 'genre' => 'Lo-fi', 'language' => 'English', 'duration' => '1:02:15', 'placeholder' => 4, 'status' => 'Active', 'description' => 'The ultimate lo-fi study companion. An hour-long visual and audio experience featuring ambient city sounds, gentle beats, and soothing melodies designed to help you focus and relax.'],
    10 => ['title' => 'Chill Vibes Visualizer', 'artist' => 'Luna Park', 'album' => 'Chill Vibes', 'year' => '2024', 'genre' => 'Electronic', 'language' => 'English', 'duration' => '4:30', 'placeholder' => 5, 'status' => 'Active', 'description' => 'A mesmerizing visual experience paired with Luna Park\'s signature chill electronic sound. The visualizer features generative art that responds to the music in real-time.'],
    11 => ['title' => 'Midnight Dreams - Lyric Video', 'artist' => 'Hammad Aziz', 'album' => 'Night Sessions', 'year' => '2025', 'genre' => 'Pop', 'language' => 'English', 'duration' => '3:58', 'placeholder' => 1, 'status' => 'Active', 'description' => 'The official lyric video for Midnight Dreams. Watch the words come alive with stunning typography and atmospheric visuals that perfectly capture the mood of this dreamy pop anthem.'],
    12 => ['title' => 'Noor - Visual Album', 'artist' => 'Hammad Aziz', 'album' => 'Sade', 'year' => '2026', 'genre' => 'Sufi', 'language' => 'Urdu', 'duration' => '42:10', 'placeholder' => 3, 'status' => 'Draft', 'description' => 'A complete visual album experience for Noor. Each track is accompanied by a unique visual narrative that weaves together themes of light, spirituality, and human connection across breathtaking landscapes.'],
];

$video = isset($allVideos[$id]) ? $allVideos[$id] : $allVideos[1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($video['title']); ?> - SOUND Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/video_card/video_card.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/music_card/music_card.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/video_detail.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
</head>
<body class="wg-page--details wg-page--video-details">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- BREADCRUMB -->
<div class="wg-details-breadcrumb">
    <div class="wg-details-breadcrumb__inner">
        <a href="<?php echo $websiteBase; ?>/index.php" class="wg-details-breadcrumb__link">Home</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-details-breadcrumb__link">Videos</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <span class="wg-details-breadcrumb__current"><?php echo htmlspecialchars($video['title']); ?></span>
    </div>
</div>

<!-- MAIN CONTENT -->
<main class="wg-details">
    <div class="wg-details__inner">

        <!-- TOP SECTION: Video Player + Info -->
        <div class="wg-details__top">
            <div class="wg-details__cover wg-details__cover--video">
                <div class="wg-details__cover-placeholder wg-card__cover-placeholder--<?php echo (int) $video['placeholder']; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="56" height="56">
                        <polygon points="23 7 16 12 23 17 23 7"/>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                    </svg>
                </div>
                <div class="wg-details__cover-play">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
                <span class="wg-details__duration-badge"><?php echo htmlspecialchars($video['duration']); ?></span>
            </div>

            <div class="wg-details__info">
                <div class="wg-details__status">
                    <span class="wg-details__status-badge wg-details__status-badge--<?php echo strtolower($video['status']); ?>"><?php echo htmlspecialchars($video['status']); ?></span>
                </div>
                <h1 class="wg-details__title"><?php echo htmlspecialchars($video['title']); ?></h1>
                <p class="wg-details__artist"><?php echo htmlspecialchars($video['artist']); ?></p>

                <!-- META PILLS -->
                <div class="wg-details__meta">
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Album</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['album']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Year</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['year']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Genre</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['genre']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Language</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['language']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Duration</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['duration']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill wg-details__meta-pill--rating">
                        <span class="wg-details__meta-pill-value">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    </span>
                </div>

                <!-- DESCRIPTION -->
                <div class="wg-details__description">
                    <h3 class="wg-details__desc-heading">About this video</h3>
                    <p class="wg-details__desc-text"><?php echo htmlspecialchars($video['description']); ?></p>
                </div>
            </div>
        </div>

        <!-- RATINGS & REVIEWS -->
        <section class="wg-details__reviews">

            <!-- ROW 1: Summary + Form -->
            <div class="wg-reviews__top-row">
                <div class="wg-reviews__summary">
                    <h3 class="wg-reviews__summary-title">Ratings &amp; Reviews</h3>
                    <div class="wg-reviews__score">
                        <span class="wg-reviews__score-number">4.6</span>
                        <span class="wg-reviews__score-max">/ 5</span>
                    </div>
                    <div class="wg-reviews__stars">
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--half">&#9733;</span>
                    </div>
                    <p class="wg-reviews__based">Based on 94 reviews</p>
                    <div class="wg-reviews__dist">
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">5 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:62%"></div></div><span class="wg-reviews__dist-pct">62%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">4 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:25%"></div></div><span class="wg-reviews__dist-pct">25%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">3 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:8%"></div></div><span class="wg-reviews__dist-pct">8%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">2 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:3%"></div></div><span class="wg-reviews__dist-pct">3%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">1 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:2%"></div></div><span class="wg-reviews__dist-pct">2%</span></div>
                    </div>
                </div>

                <div class="wg-reviews__form-wrap">
                    <h3 class="wg-reviews__form-title">Add Your Review</h3>
                    <span class="wg-reviews__form-rating-label">Your Rating</span>
                    <div class="wg-reviews__star-select" id="starSelect">
                        <span class="wg-reviews__star-pick" data-star="1">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="2">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="3">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="4">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="5">&#9733;</span>
                    </div>
                    <textarea class="wg-reviews__textarea" placeholder="Write your review..." rows="3"></textarea>
                    <button class="wg-reviews__submit-btn" id="submitReview">Submit Review</button>
                </div>
            </div>

            <!-- ROW 2: Reviews Grid -->
            <div class="wg-reviews__cards-section">
                <h3 class="wg-details__section-title" style="margin-bottom:1rem;">Reviews</h3>
                <div class="wg-reviews__cards-grid">
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">MJ</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Maya Johnson</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">2 days ago</span>
                        </div>
                        <p class="wg-review-card__text">"Incredible visuals! The cinematography really brings the music to life. Must watch."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">RK</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Ravi Kumar</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                            </div>
                            <span class="wg-review-card__date">4 days ago</span>
                        </div>
                        <p class="wg-review-card__text">"Great production quality and the storyline keeps you engaged throughout. Well done."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">EW</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Emma Wilson</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">1 week ago</span>
                        </div>
                        <p class="wg-review-card__text">"The editing and color grading are top-notch. This sets a new standard for music videos."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">TA</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Tariq Ahmed</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">1 week ago</span>
                        </div>
                        <p class="wg-review-card__text">"Absolutely stunning. The visual effects perfectly complement the music. Brilliant work."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">LP</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Laura Petrov</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                            </div>
                            <span class="wg-review-card__date">2 weeks ago</span>
                        </div>
                        <p class="wg-review-card__text">"Really well-made video. The director did an amazing job capturing the mood of the song."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">DM</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">David Martinez</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">2 weeks ago</span>
                        </div>
                        <p class="wg-review-card__text">"One of the best music videos I\'ve seen this year. The attention to detail is remarkable."</p>
                    </div>
                </div>
                <button class="wg-reviews__all-btn" id="openDrawer">All Reviews</button>
            </div>
        </section>

        <!-- ALL REVIEWS DRAWER -->
        <div class="wg-drawer-overlay" id="drawerOverlay"></div>
        <aside class="wg-drawer" id="reviewsDrawer">
            <div class="wg-drawer__header">
                <div>
                    <h3 class="wg-drawer__title">All Reviews</h3>
                    <span class="wg-drawer__count">94 Reviews</span>
                </div>
                <button class="wg-drawer__close" id="closeDrawer" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="wg-drawer__list">
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">MJ</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Maya Johnson</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">2 days ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Incredible visuals! The cinematography really brings the music to life. Must watch."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">RK</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Ravi Kumar</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                        </div>
                        <span class="wg-drawer__review-date">4 days ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Great production quality and the storyline keeps you engaged throughout. Well done."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">EW</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Emma Wilson</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">1 week ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"The editing and color grading are top-notch. This sets a new standard for music videos."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">TA</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Tariq Ahmed</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">1 week ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Absolutely stunning. The visual effects perfectly complement the music. Brilliant work."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">LP</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Laura Petrov</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                        </div>
                        <span class="wg-drawer__review-date">2 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Really well-made video. The director did an amazing job capturing the mood of the song."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">DM</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">David Martinez</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">2 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"One of the best music videos I\'ve seen this year. The attention to detail is remarkable."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">SK</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Sofia Kim</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">3 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"The artistic direction is on another level. Every frame could be a painting."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">JT</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">James Thompson</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                        </div>
                        <span class="wg-drawer__review-date">3 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Great video overall. The choreography scenes were particularly impressive."</p>
                </div>
            </div>
            <div class="wg-drawer__form">
                <h4 class="wg-drawer__form-title">Add Your Review</h4>
                <div class="wg-reviews__star-select wg-drawer__star-select">
                    <span class="wg-reviews__star-pick" data-star="1">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="2">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="3">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="4">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="5">&#9733;</span>
                </div>
                <textarea class="wg-reviews__textarea" placeholder="Write your review..." rows="3"></textarea>
                <button class="wg-reviews__submit-btn wg-drawer__submit-btn">Submit Review</button>
            </div>
        </aside>

        <!-- MORE FROM THIS ARTIST -->
        <?php
        $artistCards = [];
        foreach ($allVideos as $vid => $v) {
            if ($v['artist'] === $video['artist'] && $vid !== $id) {
                $artistCards[] = ['vc_id' => $vid, 'vc_title' => $v['title'], 'vc_artist' => $v['artist'], 'vc_album' => $v['album'], 'vc_year' => $v['year'], 'vc_genre' => $v['genre'], 'vc_language' => $v['language'], 'vc_duration' => $v['duration'], 'vc_placeholder' => $v['placeholder']];
            }
        }
        if (count($artistCards) < 6) {
            foreach ($allVideos as $vid => $v) {
                if ($vid !== $id && $v['artist'] !== $video['artist'] && !in_array($vid, array_column($artistCards, 'vc_id')) && count($artistCards) < 6) {
                    $artistCards[] = ['vc_id' => $vid, 'vc_title' => $v['title'], 'vc_artist' => $v['artist'], 'vc_album' => $v['album'], 'vc_year' => $v['year'], 'vc_genre' => $v['genre'], 'vc_language' => $v['language'], 'vc_duration' => $v['duration'], 'vc_placeholder' => $v['placeholder']];
                }
            }
        }
        ?>
        <?php if (!empty($artistCards)): ?>
        <section class="wg-details__artist-section">
            <div class="wg-details__artist-header">
                <h2 class="wg-details__section-title">More From This Artist</h2>
                <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-details__viewall">View All <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </div>
            <div class="wg-carousel">
                <div class="wg-carousel__track" id="artistCarousel">
                    <?php foreach ($artistCards as $card): ?>
                    <div class="wg-carousel__item">
                        <?php
                        extract($card);
                        include __DIR__ . '/../components/video_card/video_card.php';
                        ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="wg-carousel__nav wg-carousel__nav--prev" id="carouselPrev" aria-label="Previous">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button class="wg-carousel__nav wg-carousel__nav--next" id="carouselNext" aria-label="Next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </section>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>

<script src="<?php echo $jsBase; ?>/video_detail.js"></script>
</body>
</html>
