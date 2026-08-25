<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/music_details';
$jsBase = $websiteBase . '/js/music_details';
$currentPage = 'music';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$allMusic = [
    1  => ['title' => 'Midnight Dreams', 'artist' => 'Hammad Aziz', 'album' => 'Night Sessions', 'year' => '2025', 'genre' => 'Pop', 'language' => 'English', 'placeholder' => 1, 'status' => 'Active', 'description' => 'A dreamy pop anthem that captures the essence of late-night creativity. Built with layered synths and atmospheric production, Midnight Dreams takes listeners on a sonic journey through urban landscapes and quiet introspection.'],
    2  => ['title' => 'Tum Hi Ho', 'artist' => 'Arijit Singh', 'album' => 'Aashiqui 2', 'year' => '2013', 'genre' => 'Bollywood', 'language' => 'Hindi', 'placeholder' => 2, 'status' => 'Active', 'description' => 'An iconic Bollywood romantic ballad that became a cultural phenomenon. With its soulful melody and deeply emotional lyrics, Tum Hi Ho defined a generation of Indian love songs and remains one of the most streamed Hindi tracks of all time.'],
    3  => ['title' => 'Neon Lights', 'artist' => 'Aria Collins', 'album' => 'Electric Dreams', 'year' => '2024', 'genre' => 'Electronic', 'language' => 'English', 'placeholder' => 3, 'status' => 'Active', 'description' => 'An electrifying electronic track pulsing with energy. Neon Lights combines driving basslines with shimmering synth leads, creating a vibrant soundscape perfect for late-night drives and festival stages alike.'],
    4  => ['title' => 'City Rain', 'artist' => 'Kai Moreno', 'album' => 'Lo-fi Nights', 'year' => '2025', 'genre' => 'Lo-fi', 'language' => 'English', 'placeholder' => 4, 'status' => 'Active', 'description' => 'A mellow lo-fi track inspired by rainy evenings in the city. Soft piano chords, gentle vinyl crackle, and ambient rain textures create the perfect backdrop for studying, relaxing, or simply watching the world go by.'],
    5  => ['title' => 'Summer Breeze', 'artist' => 'Luna Park', 'album' => 'Chill Vibes', 'year' => '2024', 'genre' => 'Chill', 'language' => 'English', 'placeholder' => 5, 'status' => 'Active', 'description' => 'A laid-back chill track that embodies the warmth of a summer afternoon. Gentle guitar strums, soft pads, and a breezy rhythm make this the ideal companion for lazy weekends and outdoor gatherings.'],
    6  => ['title' => 'Dil Ka Rishta', 'artist' => 'Hamza Tahir', 'album' => 'Sade', 'year' => '2026', 'genre' => 'Sufi', 'language' => 'Urdu', 'placeholder' => 1, 'status' => 'Active', 'description' => 'A soulful Sufi-inspired composition that bridges traditional Eastern spirituality with modern production. Dil Ka Rishta explores themes of divine love and inner peace through haunting vocals and organic instrumentation.'],
    7  => ['title' => 'Electric Soul', 'artist' => 'Aria Collins', 'album' => 'Electric Dreams', 'year' => '2024', 'genre' => 'Electronic', 'language' => 'English', 'placeholder' => 3, 'status' => 'Active', 'description' => 'A powerful electronic anthem about finding humanity in a digital world. Electric Soul merges pulsating beats with emotive melodies, delivering a track that resonates on both the dancefloor and in quiet reflection.'],
    8  => ['title' => 'Raaste Bhool Gaye', 'artist' => 'Ahmed Raza', 'album' => 'Aashiqui 2', 'year' => '2013', 'genre' => 'Bollywood', 'language' => 'Hindi', 'placeholder' => 2, 'status' => 'Active', 'description' => 'A poignant Bollywood track about lost love and the paths we forget. With its melancholic melody and heartfelt vocals, this song captures the bittersweet feeling of looking back at moments that shaped us.'],
    9  => ['title' => 'Ocean Waves', 'artist' => 'Kai Moreno', 'album' => 'Lo-fi Nights', 'year' => '2025', 'genre' => 'Lo-fi', 'language' => 'English', 'placeholder' => 4, 'status' => 'Active', 'description' => 'Ambient lo-fi textures meet oceanic field recordings in this serene composition. Ocean Waves is designed to calm the mind and create a peaceful atmosphere, blending natural sounds with gentle electronic elements.'],
    10 => ['title' => 'Starlight', 'artist' => 'Luna Park', 'album' => 'Chill Vibes', 'year' => '2024', 'genre' => 'R&B', 'language' => 'English', 'placeholder' => 5, 'status' => 'Active', 'description' => 'A smooth R&B-infused chill track that glows with warmth. Starlight features velvety vocals, lush harmonies, and a groovy bassline that together create an intimate and enchanting listening experience.'],
    11 => ['title' => 'Noor', 'artist' => 'Hammad Aziz', 'album' => 'Sade', 'year' => '2026', 'genre' => 'Sufi', 'language' => 'Urdu', 'placeholder' => 1, 'status' => 'Draft', 'description' => 'An ethereal Sufi composition whose name means light. Noor weaves together classical instrumentation with contemporary arrangements, creating a transcendent musical experience that speaks to the soul.'],
    12 => ['title' => 'Acoustic Glow', 'artist' => 'Arijit Singh', 'album' => 'Acoustic Sessions', 'year' => '2025', 'genre' => 'Acoustic', 'language' => 'Hindi', 'placeholder' => 3, 'status' => 'Active', 'description' => 'A stripped-down acoustic reimagining that reveals the raw beauty of the original composition. Acoustic Glow showcases the power of simplicity, with nothing but an acoustic guitar and an emotive voice carrying the melody.'],
];

$track = isset($allMusic[$id]) ? $allMusic[$id] : $allMusic[1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($track['title']); ?> - SOUND Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/music_card/music_card.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/music_details.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
</head>
<body class="wg-page--details">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- BREADCRUMB -->
<div class="wg-details-breadcrumb">
    <div class="wg-details-breadcrumb__inner">
        <a href="<?php echo $websiteBase; ?>/index.php" class="wg-details-breadcrumb__link">Home</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-details-breadcrumb__link">Music</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <span class="wg-details-breadcrumb__current"><?php echo htmlspecialchars($track['title']); ?></span>
    </div>
</div>

<!-- MAIN CONTENT -->
<main class="wg-details">
    <div class="wg-details__inner">

        <!-- TOP SECTION: Cover + Info -->
        <div class="wg-details__top">
            <div class="wg-details__cover">
                <div class="wg-details__cover-placeholder wg-card__cover-placeholder--<?php echo (int) $track['placeholder']; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="56" height="56">
                        <path d="M9 18V5l12-2v13"/>
                        <circle cx="6" cy="18" r="3"/>
                        <circle cx="18" cy="16" r="3"/>
                    </svg>
                </div>
                <div class="wg-details__cover-play">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
            </div>

            <div class="wg-details__info">
                <div class="wg-details__status">
                    <span class="wg-details__status-badge wg-details__status-badge--<?php echo strtolower($track['status']); ?>"><?php echo htmlspecialchars($track['status']); ?></span>
                </div>
                <h1 class="wg-details__title"><?php echo htmlspecialchars($track['title']); ?></h1>
                <p class="wg-details__artist"><?php echo htmlspecialchars($track['artist']); ?></p>

                <!-- META PILLS -->
                <div class="wg-details__meta">
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Album</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['album']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Year</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['year']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Genre</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['genre']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Language</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['language']); ?></span>
                    </span>
                    <span class="wg-details__meta-pill wg-details__meta-pill--rating">
                        <span class="wg-details__meta-pill-value">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    </span>
                </div>

                <!-- DESCRIPTION -->
                <div class="wg-details__description">
                    <h3 class="wg-details__desc-heading">About this song</h3>
                    <p class="wg-details__desc-text"><?php echo htmlspecialchars($track['description']); ?></p>
                </div>

                <!-- CUSTOM AUDIO PLAYER -->
                <div class="wg-player" id="wgPlayer">
                    <audio class="wg-player__audio" id="wgAudioPlayer" preload="metadata">
                        <source src="" type="audio/mpeg">
                    </audio>
                    <button class="wg-player__play" id="wgPlayerPlay" type="button" aria-label="Play">
                        <svg class="svg--play" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                        <svg class="svg--pause" viewBox="0 0 24 24" fill="currentColor"><rect x="5" y="3" width="4" height="18"/><rect x="15" y="3" width="4" height="18"/></svg>
                    </button>
                    <div class="wg-player__progress-wrap">
                        <span class="wg-player__time" id="wgPlayerCurrent">0:00</span>
                        <div class="wg-player__progress-bar" id="wgPlayerProgressBar">
                            <div class="wg-player__progress-fill" id="wgPlayerProgressFill"></div>
                        </div>
                        <span class="wg-player__duration" id="wgPlayerDuration">0:00</span>
                    </div>
                    <div class="wg-player__volume">
                        <button class="wg-player__volume-btn" id="wgPlayerVolumeBtn" type="button" aria-label="Volume">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                        </button>
                        <input type="range" class="wg-player__volume-slider" id="wgPlayerVolume" min="0" max="100" value="80">
                    </div>
                    <a class="wg-player__download" id="wgPlayerDownload" href="#" download aria-label="Download">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </a>
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
                        <span class="wg-reviews__score-number">4.8</span>
                        <span class="wg-reviews__score-max">/ 5</span>
                    </div>
                    <div class="wg-reviews__stars">
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                        <span class="wg-reviews__star wg-reviews__star--half">&#9733;</span>
                    </div>
                    <p class="wg-reviews__based">Based on 128 reviews</p>
                    <div class="wg-reviews__dist">
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">5 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:68%"></div></div><span class="wg-reviews__dist-pct">68%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">4 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:22%"></div></div><span class="wg-reviews__dist-pct">22%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">3 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:6%"></div></div><span class="wg-reviews__dist-pct">6%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">2 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:3%"></div></div><span class="wg-reviews__dist-pct">3%</span></div>
                        <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label">1 &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:1%"></div></div><span class="wg-reviews__dist-pct">1%</span></div>
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
                            <div class="wg-review-card__avatar">AR</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Alex Rivera</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">3 days ago</span>
                        </div>
                        <p class="wg-review-card__text">"Amazing track! The vocals and guitars create such a powerful vibe."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">SF</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Sara Fatima</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                            </div>
                            <span class="wg-review-card__date">5 days ago</span>
                        </div>
                        <p class="wg-review-card__text">"Beautiful composition with great emotional depth. Sophisticated yet accessible."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">MR</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Mujahid Raza</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">1 week ago</span>
                        </div>
                        <p class="wg-review-card__text">"On repeat since it dropped. Hauntingly beautiful vocals and deeply resonant lyrics."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">JL</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Jordan Lee</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">1 week ago</span>
                        </div>
                        <p class="wg-review-card__text">"The production quality is stellar. Every layer serves the song perfectly."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">NK</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Nadia Khan</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                            </div>
                            <span class="wg-review-card__date">2 weeks ago</span>
                        </div>
                        <p class="wg-review-card__text">"Great vibes, perfect for late-night listening. The arrangement is top notch."</p>
                    </div>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <div class="wg-review-card__avatar">TC</div>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name">Tom Chen</span>
                                <span class="wg-review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <span class="wg-review-card__date">2 weeks ago</span>
                        </div>
                        <p class="wg-review-card__text">"A masterpiece in every sense. The melody stays with you long after listening."</p>
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
                    <span class="wg-drawer__count">128 Reviews</span>
                </div>
                <button class="wg-drawer__close" id="closeDrawer" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="wg-drawer__list">
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">AR</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Alex Rivera</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">3 days ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Absolutely incredible track. The vocals and guitars create such a powerful vibe. One of the best releases this year."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">SF</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Sara Fatima</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                        </div>
                        <span class="wg-drawer__review-date">5 days ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Beautiful composition with great emotional depth. The arrangement is sophisticated yet accessible."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">MR</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Mujahid Raza</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">1 week ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"This has been on repeat since it dropped. The vocals are hauntingly beautiful and the lyrics resonate deeply."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">JL</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Jordan Lee</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">1 week ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"The production quality is stellar. Every layer serves the song perfectly. A masterclass in modern music production."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">NK</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Nadia Khan</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                        </div>
                        <span class="wg-drawer__review-date">2 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Great vibes, perfect for late-night listening. The arrangement is top notch and the melody is unforgettable."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">TC</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Tom Chen</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">2 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"A masterpiece in every sense. The melody stays with you long after listening. Truly exceptional work."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">JS</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">John Smith</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                        </div>
                        <span class="wg-drawer__review-date">3 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Great song overall. The mixing is clean and the vocals sit perfectly in the mix."</p>
                </div>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <div class="wg-drawer__review-avatar">LP</div>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name">Lisa Park</span>
                            <span class="wg-drawer__review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <span class="wg-drawer__review-date">3 weeks ago</span>
                    </div>
                    <p class="wg-drawer__review-text">"Instant classic. The emotion in every note is palpable. Can't stop listening to this one."</p>
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
        foreach ($allMusic as $mid => $m) {
            if ($m['artist'] === $track['artist'] && $mid !== $id) {
                $artistCards[] = ['mc_id' => $mid, 'mc_title' => $m['title'], 'mc_artist' => $m['artist'], 'mc_album' => $m['album'], 'mc_year' => $m['year'], 'mc_genre' => $m['genre'], 'mc_language' => $m['language'], 'mc_placeholder' => $m['placeholder']];
            }
        }
        if (count($artistCards) < 6) {
            foreach ($allMusic as $mid => $m) {
                if ($mid !== $id && $m['artist'] !== $track['artist'] && !in_array($mid, array_column($artistCards, 'mc_id')) && count($artistCards) < 6) {
                    $artistCards[] = ['mc_id' => $mid, 'mc_title' => $m['title'], 'mc_artist' => $m['artist'], 'mc_album' => $m['album'], 'mc_year' => $m['year'], 'mc_genre' => $m['genre'], 'mc_language' => $m['language'], 'mc_placeholder' => $m['placeholder']];
                }
            }
        }
        ?>
        <?php if (!empty($artistCards)): ?>
        <section class="wg-details__artist-section">
            <div class="wg-details__artist-header">
                <h2 class="wg-details__section-title">More From This Artist</h2>
                <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-details__viewall">View All <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </div>
            <div class="wg-carousel">
                <div class="wg-carousel__track" id="artistCarousel">
                    <?php foreach ($artistCards as $card): ?>
                    <div class="wg-carousel__item">
                        <?php
                        extract($card);
                        include __DIR__ . '/../components/music_card/music_card.php';
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

<script src="<?php echo $jsBase; ?>/music_details.js"></script>
</body>
</html>
