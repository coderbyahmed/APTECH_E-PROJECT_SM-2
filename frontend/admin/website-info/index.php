<?php
/**
 * SOUND Group — Website Info Management
 *
 * UI ONLY — static/mock data. Backend/database to be connected later.
 * Each section shows current values and provides an Edit modal form
 * that updates the displayed values (frontend experience only).
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireAuth();

$pageTitle = 'Website Info';
$activeItem = 'website-info';

include __DIR__ . '/../layout/admin-layout.php';

/* ----------------------------------------------------------
   Mock website info data (UI only). Keys describe the future
   backend structure for each section.
   ---------------------------------------------------------- */
$siteInfo = [
    'websiteName' => 'SOUND Group',
    'logoFile'    => 'sound-group-logo.svg',
    'shortDesc'   => 'The official entertainment platform for SOUND Group — discover music, music videos and the latest releases all in one place.',
    'about'       => 'SOUND Group is an entertainment company dedicated to bringing artists and audiences closer together. From chart-topping tracks to cinematic videos, we showcase the best of global music and visual content on a single platform.',
];

$homeContent = [
    'heading'   => 'Discover Music That Moves You',
    'intro'     => 'Stream the hottest tracks, watch stunning music videos and explore artists from every corner of the world — all in one place.',
    'featured'  => 'Featured This Week',
    'latestMusic' => 'Latest Music',
    'latestVideo' => 'Latest Videos',
];

$contactInfo = [
    'email'   => 'contact@soundgroup.com',
    'phone'   => '+1 (555) 010-2030',
    'address' => '124 Harmony Avenue, Suite 500, Los Angeles, CA 90028, USA',
];

$socialLinks = [
    'facebook'  => 'https://www.facebook.com/soundgroup',
    'github'    => 'https://github.com/soundgroup',
    'linkedin'  => 'https://www.linkedin.com/company/soundgroup',
    'tiktok'    => 'https://www.tiktok.com/@soundgroup',
];

$footerInfo = [
    'desc'      => 'SOUND Group is your home for music, videos and entertainment updates. Follow us on social media to stay in the loop with the newest releases.',
    'copyright' => '© 2026 SOUND Group. All rights reserved.',
];
?>

    <div class="wi-header">
        <div class="wi-header__left">
            <h1 class="wi-header__title">Website Info</h1>
            <p class="wi-header__subtitle">Manage general website information — company identity, home page headings, contact details, social links and footer text.</p>
        </div>
    </div>

    <div class="wi-sections">

        <!-- ==================================================
             1. SITE / COMPANY INFORMATION
             ================================================== -->
        <section class="wi-section" id="wiSectionSite">
            <div class="wi-section__header">
                <div class="wi-section__heading">
                    <div class="wi-section__icon wi-section__icon--violet">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    </div>
                    <div class="wi-section__title-block">
                        <h2 class="wi-section__title">Site / Company Information</h2>
                        <p class="wi-section__desc">Identity and company details displayed across the website.</p>
                    </div>
                </div>
                <button type="button" class="wi-edit-btn" data-wi-open="site">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>
            </div>
            <div class="wi-section__body">
                <div class="wi-grid">
                    <div class="wi-field">
                        <span class="wi-field__label">Website Name</span>
                        <span class="wi-field__value" id="wiSiteNameValue"><?php echo htmlspecialchars($siteInfo['websiteName']); ?></span>
                    </div>
                    <div class="wi-field">
                        <span class="wi-field__label">Site Logo</span>
                        <div class="wi-logo">
                            <span class="wi-logo__badge" id="wiLogoBadge">SG</span>
                            <span class="wi-logo__name" id="wiLogoName"><?php echo htmlspecialchars($siteInfo['logoFile']); ?></span>
                        </div>
                    </div>
                    <div class="wi-field wi-field--full">
                        <span class="wi-field__label">Short Description</span>
                        <p class="wi-field__text" id="wiShortDescValue"><?php echo htmlspecialchars($siteInfo['shortDesc']); ?></p>
                    </div>
                    <div class="wi-field wi-field--full">
                        <span class="wi-field__label">About Website / About SOUND Group</span>
                        <p class="wi-field__text" id="wiAboutValue"><?php echo htmlspecialchars($siteInfo['about']); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================
             2. HOME PAGE CONTENT
             ================================================== -->
        <section class="wi-section" id="wiSectionHome">
            <div class="wi-section__header">
                <div class="wi-section__heading">
                    <div class="wi-section__icon wi-section__icon--blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="wi-section__title-block">
                        <h2 class="wi-section__title">Home Page Content</h2>
                        <p class="wi-section__desc">Headings and introductory text displayed on the home page.</p>
                    </div>
                </div>
                <button type="button" class="wi-edit-btn" data-wi-open="home">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>
            </div>
            <div class="wi-section__body">
                <div class="wi-grid">
                    <div class="wi-field">
                        <span class="wi-field__label">Home Page Heading</span>
                        <span class="wi-field__value" id="wiHomeHeadingValue"><?php echo htmlspecialchars($homeContent['heading']); ?></span>
                    </div>
                    <div class="wi-field">
                        <span class="wi-field__label">Featured Section Heading</span>
                        <span class="wi-field__value" id="wiFeaturedHeadingValue"><?php echo htmlspecialchars($homeContent['featured']); ?></span>
                    </div>
                    <div class="wi-field wi-field--full">
                        <span class="wi-field__label">Home Page Description / Introduction</span>
                        <p class="wi-field__text" id="wiHomeIntroValue"><?php echo htmlspecialchars($homeContent['intro']); ?></p>
                    </div>
                    <div class="wi-field">
                        <span class="wi-field__label">Latest Music Section Heading</span>
                        <span class="wi-field__value" id="wiLatestMusicHeadingValue"><?php echo htmlspecialchars($homeContent['latestMusic']); ?></span>
                    </div>
                    <div class="wi-field">
                        <span class="wi-field__label">Latest Video Section Heading</span>
                        <span class="wi-field__value" id="wiLatestVideoHeadingValue"><?php echo htmlspecialchars($homeContent['latestVideo']); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================
             3. CONTACT INFORMATION
             ================================================== -->
        <section class="wi-section" id="wiSectionContact">
            <div class="wi-section__header">
                <div class="wi-section__heading">
                    <div class="wi-section__icon wi-section__icon--teal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="wi-section__title-block">
                        <h2 class="wi-section__title">Contact Information</h2>
                        <p class="wi-section__desc">Contact details shown in the contact section and footer.</p>
                    </div>
                </div>
                <button type="button" class="wi-edit-btn" data-wi-open="contact">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>
            </div>
            <div class="wi-section__body">
                <div class="wi-grid">
                    <div class="wi-field">
                        <span class="wi-field__label">Email</span>
                        <span class="wi-field__value" id="wiContactEmailValue"><?php echo htmlspecialchars($contactInfo['email']); ?></span>
                    </div>
                    <div class="wi-field">
                        <span class="wi-field__label">Phone Number</span>
                        <span class="wi-field__value" id="wiContactPhoneValue"><?php echo htmlspecialchars($contactInfo['phone']); ?></span>
                    </div>
                    <div class="wi-field wi-field--full">
                        <span class="wi-field__label">Address</span>
                        <p class="wi-field__text" id="wiContactAddressValue"><?php echo htmlspecialchars($contactInfo['address']); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================
             4. SOCIAL MEDIA
             ================================================== -->
        <section class="wi-section" id="wiSectionSocial">
            <div class="wi-section__header">
                <div class="wi-section__heading">
                    <div class="wi-section__icon wi-section__icon--pink">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                            <polyline points="16 6 12 2 8 6"/>
                            <line x1="12" y1="2" x2="12" y2="15"/>
                        </svg>
                    </div>
                    <div class="wi-section__title-block">
                        <h2 class="wi-section__title">Social Media</h2>
                        <p class="wi-section__desc">Links to SOUND Group profiles on social media platforms.</p>
                    </div>
                </div>
                <button type="button" class="wi-edit-btn" data-wi-open="social">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>
            </div>
            <div class="wi-section__body">
                <div class="wi-grid">
                    <div class="wi-field">
                        <div class="wi-social__row">
                            <span class="wi-social__brand wi-social__brand--facebook">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                </svg>
                            </span>
                            <div class="wi-social__meta">
                                <span class="wi-field__label">Facebook</span>
                                <span class="wi-social__url" id="wiFacebookValue"><?php echo htmlspecialchars($socialLinks['facebook']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="wi-field">
                        <div class="wi-social__row">
                            <span class="wi-social__brand wi-social__brand--github">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>
                                </svg>
                            </span>
                            <div class="wi-social__meta">
                                <span class="wi-field__label">GitHub</span>
                                <span class="wi-social__url" id="wiGithubValue"><?php echo htmlspecialchars($socialLinks['github']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="wi-field">
                        <div class="wi-social__row">
                            <span class="wi-social__brand wi-social__brand--linkedin">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4V9h4v1.5A6 6 0 0 1 16 8z"/>
                                    <rect x="2" y="9" width="4" height="12"/>
                                    <circle cx="4" cy="4" r="2"/>
                                </svg>
                            </span>
                            <div class="wi-social__meta">
                                <span class="wi-field__label">LinkedIn</span>
                                <span class="wi-social__url" id="wiLinkedInValue"><?php echo htmlspecialchars($socialLinks['linkedin']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="wi-field">
                        <div class="wi-social__row">
                            <span class="wi-social__brand wi-social__brand--tiktok">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>
                                </svg>
                            </span>
                            <div class="wi-social__meta">
                                <span class="wi-field__label">TikTok</span>
                                <span class="wi-social__url" id="wiTikTokValue"><?php echo htmlspecialchars($socialLinks['tiktok']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================
             5. FOOTER INFORMATION
             ================================================== -->
        <section class="wi-section" id="wiSectionFooter">
            <div class="wi-section__header">
                <div class="wi-section__heading">
                    <div class="wi-section__icon wi-section__icon--amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div class="wi-section__title-block">
                        <h2 class="wi-section__title">Footer Information</h2>
                        <p class="wi-section__desc">Description and copyright text shown in the website footer.</p>
                    </div>
                </div>
                <button type="button" class="wi-edit-btn" data-wi-open="footer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>
            </div>
            <div class="wi-section__body">
                <div class="wi-grid">
                    <div class="wi-field wi-field--full">
                        <span class="wi-field__label">Footer Description</span>
                        <p class="wi-field__text" id="wiFooterDescValue"><?php echo htmlspecialchars($footerInfo['desc']); ?></p>
                    </div>
                    <div class="wi-field wi-field--full">
                        <span class="wi-field__label">Copyright Text</span>
                        <span class="wi-field__value" id="wiCopyrightValue"><?php echo htmlspecialchars($footerInfo['copyright']); ?></span>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- ==================================================
         EDIT MODALS
         ================================================== -->

    <!-- Edit Site / Company Information -->
    <div class="sg-modal" id="wiSiteModal">
        <div class="sg-modal__overlay" data-wi-close="site"></div>
        <div class="sg-modal__dialog wi-modal wi-modal--wide">
            <button type="button" class="sg-modal__close" data-wi-close="site">
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
                <h2 class="sg-modal__title">Edit Site / Company Information</h2>
                <p class="sg-modal__subtitle">Update the general identity and company details displayed across the website.</p>

                <form id="wiSiteForm" class="wi-form">
                    <div class="wi-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiWebsiteName">Website Name</label>
                            <input type="text" class="sg-form-input wi-form-input" id="wiWebsiteName" value="<?php echo htmlspecialchars($siteInfo['websiteName']); ?>">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label">Site Logo</label>
                            <div class="wi-file-field">
                                <button type="button" class="wi-file-field__btn" id="wiLogoPickerBtn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <span id="wiLogoFileName"><?php echo htmlspecialchars($siteInfo['logoFile']); ?></span>
                                </button>
                                <input type="file" class="wi-file-field__input" id="wiLogoInput" accept="image/svg+xml,image/png,image/jpeg,image/webp" hidden>
                                <span class="wi-file-field__hint">Upload a new logo image (SVG, PNG, JPG or WebP).</span>
                            </div>
                        </div>
                        <div class="sg-form-group wi-form__group--full">
                            <label class="sg-form-label" for="wiShortDesc">Short Description</label>
                            <textarea class="sg-form-input wi-form-input wi-form-textarea" id="wiShortDesc" rows="3"><?php echo htmlspecialchars($siteInfo['shortDesc']); ?></textarea>
                        </div>
                        <div class="sg-form-group wi-form__group--full">
                            <label class="sg-form-label" for="wiAbout">About Website / About SOUND Group</label>
                            <textarea class="sg-form-input wi-form-input wi-form-textarea" id="wiAbout" rows="5"><?php echo htmlspecialchars($siteInfo['about']); ?></textarea>
                        </div>
                    </div>

                    <div class="wi-form__actions">
                        <button type="button" class="sg-btn wi-btn-cancel" data-wi-close="site">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="wiSiteSaveBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Home Page Content -->
    <div class="sg-modal" id="wiHomeModal">
        <div class="sg-modal__overlay" data-wi-close="home"></div>
        <div class="sg-modal__dialog wi-modal wi-modal--wide">
            <button type="button" class="sg-modal__close" data-wi-close="home">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Edit Home Page Content</h2>
                <p class="sg-modal__subtitle">Update the headings and introductory text displayed on the home page.</p>

                <form id="wiHomeForm" class="wi-form">
                    <div class="wi-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiHomeHeading">Home Page Heading</label>
                            <input type="text" class="sg-form-input wi-form-input" id="wiHomeHeading" value="<?php echo htmlspecialchars($homeContent['heading']); ?>">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiFeaturedHeading">Featured Section Heading</label>
                            <input type="text" class="sg-form-input wi-form-input" id="wiFeaturedHeading" value="<?php echo htmlspecialchars($homeContent['featured']); ?>">
                        </div>
                        <div class="sg-form-group wi-form__group--full">
                            <label class="sg-form-label" for="wiHomeIntro">Home Page Description / Introduction</label>
                            <textarea class="sg-form-input wi-form-input wi-form-textarea" id="wiHomeIntro" rows="3"><?php echo htmlspecialchars($homeContent['intro']); ?></textarea>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiLatestMusicHeading">Latest Music Section Heading</label>
                            <input type="text" class="sg-form-input wi-form-input" id="wiLatestMusicHeading" value="<?php echo htmlspecialchars($homeContent['latestMusic']); ?>">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiLatestVideoHeading">Latest Video Section Heading</label>
                            <input type="text" class="sg-form-input wi-form-input" id="wiLatestVideoHeading" value="<?php echo htmlspecialchars($homeContent['latestVideo']); ?>">
                        </div>
                    </div>

                    <div class="wi-form__actions">
                        <button type="button" class="sg-btn wi-btn-cancel" data-wi-close="home">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="wiHomeSaveBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Contact Information -->
    <div class="sg-modal" id="wiContactModal">
        <div class="sg-modal__overlay" data-wi-close="contact"></div>
        <div class="sg-modal__dialog wi-modal">
            <button type="button" class="sg-modal__close" data-wi-close="contact">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Edit Contact Information</h2>
                <p class="sg-modal__subtitle">Update the contact details shown in the contact section and footer.</p>

                <form id="wiContactForm" class="wi-form">
                    <div class="wi-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiContactEmail">Email</label>
                            <div class="sg-form-input-wrap">
                                <span class="sg-form-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                </span>
                                <input type="email" class="sg-form-input" id="wiContactEmail" value="<?php echo htmlspecialchars($contactInfo['email']); ?>" placeholder="name@example.com">
                            </div>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiContactPhone">Phone Number</label>
                            <div class="sg-form-input-wrap">
                                <span class="sg-form-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                </span>
                                <input type="tel" class="sg-form-input" id="wiContactPhone" value="<?php echo htmlspecialchars($contactInfo['phone']); ?>" placeholder="+1 (555) 000-0000">
                            </div>
                        </div>
                        <div class="sg-form-group wi-form__group--full">
                            <label class="sg-form-label" for="wiContactAddress">Address</label>
                            <textarea class="sg-form-input wi-form-input wi-form-textarea" id="wiContactAddress" rows="3"><?php echo htmlspecialchars($contactInfo['address']); ?></textarea>
                        </div>
                    </div>

                    <div class="wi-form__actions">
                        <button type="button" class="sg-btn wi-btn-cancel" data-wi-close="contact">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="wiContactSaveBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Social Media -->
    <div class="sg-modal" id="wiSocialModal">
        <div class="sg-modal__overlay" data-wi-close="social"></div>
        <div class="sg-modal__dialog wi-modal">
            <button type="button" class="sg-modal__close" data-wi-close="social">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                        <polyline points="16 6 12 2 8 6"/>
                        <line x1="12" y1="2" x2="12" y2="15"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Edit Social Media</h2>
                <p class="sg-modal__subtitle">Update the social profile links displayed in the footer and site pages.</p>

                <form id="wiSocialForm" class="wi-form">
                    <div class="wi-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiFacebook">Facebook URL</label>
                            <div class="sg-form-input-wrap">
                                <span class="sg-form-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                    </svg>
                                </span>
                                <input type="url" class="sg-form-input" id="wiFacebook" value="<?php echo htmlspecialchars($socialLinks['facebook']); ?>" placeholder="https://...">
                            </div>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiGithub">GitHub URL</label>
                            <div class="sg-form-input-wrap">
                                <span class="sg-form-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>
                                    </svg>
                                </span>
                                <input type="url" class="sg-form-input" id="wiGithub" value="<?php echo htmlspecialchars($socialLinks['github']); ?>" placeholder="https://...">
                            </div>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiLinkedIn">LinkedIn URL</label>
                            <div class="sg-form-input-wrap">
                                <span class="sg-form-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4V9h4v1.5A6 6 0 0 1 16 8z"/>
                                        <rect x="2" y="9" width="4" height="12"/>
                                        <circle cx="4" cy="4" r="2"/>
                                    </svg>
                                </span>
                                <input type="url" class="sg-form-input" id="wiLinkedIn" value="<?php echo htmlspecialchars($socialLinks['linkedin']); ?>" placeholder="https://...">
                            </div>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="wiTikTok">TikTok URL</label>
                            <div class="sg-form-input-wrap">
                                <span class="sg-form-input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>
                                    </svg>
                                </span>
                                <input type="url" class="sg-form-input" id="wiTikTok" value="<?php echo htmlspecialchars($socialLinks['tiktok']); ?>" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <div class="wi-form__actions">
                        <button type="button" class="sg-btn wi-btn-cancel" data-wi-close="social">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="wiSocialSaveBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Footer Information -->
    <div class="sg-modal" id="wiFooterModal">
        <div class="sg-modal__overlay" data-wi-close="footer"></div>
        <div class="sg-modal__dialog wi-modal">
            <button type="button" class="sg-modal__close" data-wi-close="footer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Edit Footer Information</h2>
                <p class="sg-modal__subtitle">Update the description and copyright text shown in the website footer.</p>

                <form id="wiFooterForm" class="wi-form">
                    <div class="wi-form__grid">
                        <div class="sg-form-group wi-form__group--full">
                            <label class="sg-form-label" for="wiFooterDesc">Footer Description</label>
                            <textarea class="sg-form-input wi-form-input wi-form-textarea" id="wiFooterDesc" rows="4"><?php echo htmlspecialchars($footerInfo['desc']); ?></textarea>
                        </div>
                        <div class="sg-form-group wi-form__group--full">
                            <label class="sg-form-label" for="wiCopyright">Copyright Text</label>
                            <input type="text" class="sg-form-input wi-form-input" id="wiCopyright" value="<?php echo htmlspecialchars($footerInfo['copyright']); ?>" placeholder="© 2026 SOUND Group. All rights reserved.">
                        </div>
                    </div>

                    <div class="wi-form__actions">
                        <button type="button" class="sg-btn wi-btn-cancel" data-wi-close="footer">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="wiFooterSaveBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/admin-layout-end.php'; ?>