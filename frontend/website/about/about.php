<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/about';
$jsBase = $websiteBase . '/js/about';
$currentPage = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>About - SOUND Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/about.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
</head>
<body class="wg-page--about">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- ABOUT HERO -->
<section class="wg-about-hero">
    <div class="wg-about-hero__inner">
        <div class="wg-about-hero__content" data-animate="fade-up">
            <span class="wg-about-hero__label">ABOUT SOUND GROUP</span>
            <h1 class="wg-about-hero__title">Where Music Meets <span class="wg-about-hero__title-accent">Visuals.</span></h1>
            <p class="wg-about-hero__desc">Sound Group is a modern platform where music and music videos come together in one place. Explore artists, discover new sounds, and enjoy a unified listening and watching experience.</p>
        </div>
        <div class="wg-about-hero__visual" data-animate="fade-left">
            <div class="wg-about-visual">
                <div class="wg-about-visual__orb wg-about-visual__orb--1"></div>
                <div class="wg-about-visual__orb wg-about-visual__orb--2"></div>
                <div class="wg-about-visual__card wg-about-visual__card--1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    <span>Music</span>
                </div>
                <div class="wg-about-visual__card wg-about-visual__card--2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    <span>Videos</span>
                </div>
                <div class="wg-about-visual__card wg-about-visual__card--3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span>Discover</span>
                </div>
                <div class="wg-about-visual__disc">
                    <div class="wg-about-visual__disc-inner">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="100%" height="100%"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                </div>
                <div class="wg-about-visual__wave">
                    <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHO WE ARE -->
<section class="wg-about-section wg-about-who">
    <div class="wg-about-section__inner">
        <div class="wg-about-who__grid">
            <div class="wg-about-who__visual" data-animate="fade-right">
                <div class="wg-about-who__art">
                    <div class="wg-about-who__art-orb"></div>
                    <div class="wg-about-who__art-lines">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <div class="wg-about-who__art-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                </div>
            </div>
            <div class="wg-about-who__content" data-animate="fade-left">
                <span class="wg-about-section__label">WHO WE ARE</span>
                <h2 class="wg-about-section__title">A Platform Built for <span class="wg-about-section__title-accent">Music Lovers</span></h2>
                <p class="wg-about-who__text">Sound Group is a digital platform that brings music and visual content together in a unified experience. Users can discover music and videos, explore artists, albums, genres and languages, and engage with content through ratings and reviews.</p>
                <p class="wg-about-who__text">Whether you're looking for the latest tracks, classic videos, or hidden gems across different genres and languages, Sound Group provides an organized and enjoyable way to explore it all.</p>
            </div>
        </div>
    </div>
</section>

<!-- WHAT WE OFFER -->
<section class="wg-about-section wg-about-offer">
    <div class="wg-about-section__inner">
        <div class="wg-about-section__header" data-animate="fade-up">
            <span class="wg-about-section__label">WHAT WE OFFER</span>
            <h2 class="wg-about-section__title">What Sound Group Offers</h2>
        </div>
        <div class="wg-about-offer__grid">
            <div class="wg-about-offer__card" data-animate="fade-up">
                <div class="wg-about-offer__card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                </div>
                <h3 class="wg-about-offer__card-title">Music</h3>
                <p class="wg-about-offer__card-desc">Explore and listen to your favorite music across genres, artists, and languages. From trending hits to timeless classics.</p>
            </div>
            <div class="wg-about-offer__card" data-animate="fade-up">
                <div class="wg-about-offer__card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="2" y1="7" x2="7" y2="7"/><line x1="2" y1="17" x2="7" y2="17"/><line x1="17" y1="7" x2="22" y2="7"/><line x1="17" y1="17" x2="22" y2="17"/></svg>
                </div>
                <h3 class="wg-about-offer__card-title">Music Videos</h3>
                <p class="wg-about-offer__card-desc">Experience music visually with music videos. Watch official videos, live sessions, and exclusive visual content from your favorite artists.</p>
            </div>
            <div class="wg-about-offer__card" data-animate="fade-up">
                <div class="wg-about-offer__card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                </div>
                <h3 class="wg-about-offer__card-title">Discover</h3>
                <p class="wg-about-offer__card-desc">Discover new content based on artists, albums, genres, and languages. Find your next favorite track or video with ease.</p>
            </div>
            <div class="wg-about-offer__card" data-animate="fade-up">
                <div class="wg-about-offer__card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3 class="wg-about-offer__card-title">Reviews &amp; Ratings</h3>
                <p class="wg-about-offer__card-desc">Rate and review your favorite music and videos. Share your opinions and see what others think about the content you love.</p>
            </div>
        </div>
    </div>
</section>

<!-- WHY SOUND GROUP -->
<section class="wg-about-section wg-about-why">
    <div class="wg-about-section__inner">
        <div class="wg-about-section__header wg-about-section__header--center" data-animate="fade-up">
            <span class="wg-about-section__label">WHY SOUND GROUP</span>
            <h2 class="wg-about-section__title">Why Choose Sound Group?</h2>
        </div>
        <div class="wg-about-why__grid">
            <div class="wg-about-why__item" data-animate="fade-up">
                <div class="wg-about-why__item-num">01</div>
                <h3 class="wg-about-why__item-title">Easy Discovery</h3>
                <p class="wg-about-why__item-desc">Find music and videos effortlessly. Our organized structure makes it simple to explore content by artists, albums, genres, and languages.</p>
            </div>
            <div class="wg-about-why__item" data-animate="fade-up">
                <div class="wg-about-why__item-num">02</div>
                <h3 class="wg-about-why__item-title">One Place</h3>
                <p class="wg-about-why__item-desc">No need to switch between different platforms. Music and music videos are available together in one unified experience.</p>
            </div>
            <div class="wg-about-why__item" data-animate="fade-up">
                <div class="wg-about-why__item-num">03</div>
                <h3 class="wg-about-why__item-title">Rich Content</h3>
                <p class="wg-about-why__item-desc">Explore content across multiple artists, albums, genres, and languages. A curated library designed for music enthusiasts.</p>
            </div>
            <div class="wg-about-why__item" data-animate="fade-up">
                <div class="wg-about-why__item-num">04</div>
                <h3 class="wg-about-why__item-title">Interactive Experience</h3>
                <p class="wg-about-why__item-desc">Engage with content through ratings and reviews. Share your thoughts and discover what others enjoy.</p>
            </div>
        </div>
    </div>
</section>

<!-- MUSIC + VIDEO EXPERIENCE -->
<section class="wg-about-section wg-about-experience">
    <div class="wg-about-section__inner">
        <div class="wg-about-section__header wg-about-section__header--center" data-animate="fade-up">
            <span class="wg-about-section__label">EXPERIENCE</span>
            <h2 class="wg-about-section__title">One Platform. <span class="wg-about-section__title-accent">Two Experiences.</span></h2>
        </div>
        <div class="wg-about-exp__grid">
            <div class="wg-about-exp__card" data-animate="fade-right">
                <div class="wg-about-exp__card-visual">
                    <div class="wg-about-exp__card-bg wg-about-exp__card-bg--music"></div>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                </div>
                <div class="wg-about-exp__card-content">
                    <h3 class="wg-about-exp__card-title">Music</h3>
                    <p class="wg-about-exp__card-desc">Listen to tracks across multiple genres, artists, and languages. Build your playlist and discover new sounds every day.</p>
                    <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-btn wg-btn--primary wg-btn--lg">Explore Music</a>
                </div>
            </div>
            <div class="wg-about-exp__card" data-animate="fade-left">
                <div class="wg-about-exp__card-visual">
                    <div class="wg-about-exp__card-bg wg-about-exp__card-bg--video"></div>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
                <div class="wg-about-exp__card-content">
                    <h3 class="wg-about-exp__card-title">Videos</h3>
                    <p class="wg-about-exp__card-desc">Watch music videos, live sessions, behind-the-scenes content, and exclusive visual experiences from your favorite artists.</p>
                    <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-btn wg-btn--primary wg-btn--lg">Explore Videos</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BUILT AROUND MUSIC -->
<section class="wg-about-section wg-about-values">
    <div class="wg-about-section__inner">
        <div class="wg-about-values__content" data-animate="fade-up">
            <span class="wg-about-section__label">OUR PASSION</span>
            <h2 class="wg-about-section__title">Built Around the Love of Music</h2>
            <div class="wg-about-values__steps">
                <div class="wg-about-values__step">
                    <div class="wg-about-values__step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <span class="wg-about-values__step-text">Discover</span>
                </div>
                <div class="wg-about-values__step-connector">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
                <div class="wg-about-values__step">
                    <div class="wg-about-values__step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                    <span class="wg-about-values__step-text">Listen</span>
                </div>
                <div class="wg-about-values__step-connector">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
                <div class="wg-about-values__step">
                    <div class="wg-about-values__step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                    <span class="wg-about-values__step-text">Watch</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CLOSING CTA -->
<section class="wg-about-section wg-about-cta">
    <div class="wg-about-section__inner">
        <div class="wg-about-cta__content" data-animate="fade-up">
            <h2 class="wg-about-cta__title">Ready to Discover Something New?</h2>
            <p class="wg-about-cta__desc">Start exploring music and videos on Sound Group. Your next favorite track is just a click away.</p>
            <div class="wg-about-cta__actions">
                <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-btn wg-btn--primary wg-btn--lg">Explore Music</a>
                <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-btn wg-btn--outline wg-btn--lg">Explore Videos</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>

<script src="<?php echo $jsBase; ?>/about.js"></script>
</body>
</html>
