<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/contact';
$jsBase = $websiteBase . '/js/contact';
$currentPage = 'contact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact - SOUND Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/contact.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/profile_modal/profile_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/loaders/button-spinner.css">
</head>
<body class="wg-page--contact">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- CONTACT HERO -->
<section class="wg-contact-hero">
    <div class="wg-contact-hero__inner">
        <span class="wg-contact-hero__label">GET IN TOUCH</span>
        <h1 class="wg-contact-hero__title">Let's Connect With <span class="wg-contact-hero__title-accent">Sound Group</span></h1>
        <p class="wg-contact-hero__desc">Have a question, suggestion, feedback, collaboration idea, or something you'd like to share? We'd love to hear from you.</p>
    </div>
</section>

<!-- MAIN CONTACT SECTION -->
<section class="wg-contact-main">
    <div class="wg-contact-main__inner">
        <div class="wg-contact-main__grid">

            <!-- LEFT: CONTACT FORM -->
            <div class="wg-contact-form-wrap" data-animate="fade-up">
                <div class="wg-contact-form-card">
                    <h2 class="wg-contact-form-card__title">Send Us a Message</h2>
                    <p class="wg-contact-form-card__desc">Fill out the form below and we'll get back to you as soon as possible.</p>

                    <form class="wg-contact-form" id="contactForm" novalidate>
                        <!-- Full Name -->
                        <div class="wg-contact-form__field">
                            <label class="wg-contact-form__label" for="contactName">Full Name <span class="wg-contact-form__required">*</span></label>
                            <div class="wg-contact-form__input-wrap">
                                <span class="wg-contact-form__input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </span>
                                <input type="text" class="wg-contact-form__input" id="contactName" name="name" placeholder="Enter your full name" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="wg-contact-form__field">
                            <label class="wg-contact-form__label" for="contactEmail">Email Address <span class="wg-contact-form__required">*</span></label>
                            <div class="wg-contact-form__input-wrap">
                                <span class="wg-contact-form__input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </span>
                                <input type="email" class="wg-contact-form__input" id="contactEmail" name="email" placeholder="Enter your email address" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="wg-contact-form__field">
                            <label class="wg-contact-form__label" for="contactPhone">Phone / WhatsApp Number <span class="wg-contact-form__optional">(Optional)</span></label>
                            <div class="wg-contact-form__input-wrap">
                                <span class="wg-contact-form__input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <input type="tel" class="wg-contact-form__input" id="contactPhone" name="phone" placeholder="Enter your phone number">
                            </div>
                        </div>

                        <!-- Inquiry Type -->
                        <div class="wg-contact-form__field">
                            <label class="wg-contact-form__label" for="contactInquiry">Inquiry Type <span class="wg-contact-form__required">*</span></label>
                            <div class="wg-contact-form__input-wrap">
                                <span class="wg-contact-form__input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                </span>
                                <select class="wg-contact-form__input wg-contact-form__select" id="contactInquiry" name="inquiry_type" required>
                                    <option value="" disabled selected>Select inquiry type</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="report">Report an Issue</option>
                                    <option value="request">Music / Video Request</option>
                                    <option value="business">Business / Collaboration</option>
                                    <option value="partnership">Investment / Partnership</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="wg-contact-form__field">
                            <label class="wg-contact-form__label" for="contactSubject">Subject <span class="wg-contact-form__required">*</span></label>
                            <div class="wg-contact-form__input-wrap">
                                <span class="wg-contact-form__input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                                </span>
                                <input type="text" class="wg-contact-form__input" id="contactSubject" name="subject" placeholder="What is this about?" required>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="wg-contact-form__field">
                            <label class="wg-contact-form__label" for="contactMessage">Message <span class="wg-contact-form__required">*</span></label>
                            <div class="wg-contact-form__input-wrap wg-contact-form__input-wrap--textarea">
                                <span class="wg-contact-form__input-icon wg-contact-form__input-icon--top">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                </span>
                                <textarea class="wg-contact-form__input wg-contact-form__textarea" id="contactMessage" name="message" placeholder="Write your message here..." rows="5" required></textarea>
                            </div>
                        </div>

                        <!-- Error / Success Messages -->
                        <div class="wg-contact-form__message wg-contact-form__message--error" id="formError" style="display:none;"></div>
                        <div class="wg-contact-form__message wg-contact-form__message--success" id="formSuccess" style="display:none;"></div>

                        <!-- Submit -->
                        <button type="submit" class="wg-btn wg-btn--primary wg-btn--lg wg-contact-form__submit" id="contactSubmit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            <span>Send Message</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT: GET IN TOUCH -->
            <div class="wg-contact-info" data-animate="fade-left">

                <!-- WhatsApp CTA -->
                <div class="wg-contact-info__card wg-contact-info__card--whatsapp">
                    <div class="wg-contact-info__card-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <div class="wg-contact-info__card-content">
                        <h3 class="wg-contact-info__card-title">WhatsApp</h3>
                        <p class="wg-contact-info__card-text">Chat with us directly for quick questions and inquiries.</p>
                        <a href="https://wa.me/923178497732?text=Hello%20Sound%20Group%2C%20I%20want%20to%20contact%20you." class="wg-btn wg-btn--primary wg-contact-info__card-btn" id="whatsappBtn" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Chat on WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Email -->
                <div class="wg-contact-info__card">
                    <div class="wg-contact-info__card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div class="wg-contact-info__card-content">
                        <h3 class="wg-contact-info__card-title">Email</h3>
                        <p class="wg-contact-info__card-text">Have something detailed to discuss? Send us an email.</p>
                        <a href="mailto:info@soundgroup.com" class="wg-contact-info__card-link">info@soundgroup.com</a>
                    </div>
                </div>

                <!-- Location -->
                <div class="wg-contact-info__card">
                    <div class="wg-contact-info__card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="wg-contact-info__card-content">
                        <h3 class="wg-contact-info__card-title">Location</h3>
                        <p class="wg-contact-info__card-text">Pakistan</p>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="wg-contact-info__card">
                    <div class="wg-contact-info__card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </div>
                    <div class="wg-contact-info__card-content">
                        <h3 class="wg-contact-info__card-title">Follow Us</h3>
                        <p class="wg-contact-info__card-text">Stay connected on social media.</p>
                        <div class="wg-contact-info__social">
                            <a href="#" class="wg-contact-info__social-link" aria-label="Facebook" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                            </a>
                            <a href="#" class="wg-contact-info__social-link" aria-label="TikTok" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3.15 15.2a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.75a8.18 8.18 0 0 0 4.76 1.52V6.8a4.83 4.83 0 0 1-1-.11z"/></svg>
                            </a>
                            <a href="#" class="wg-contact-info__social-link" aria-label="LinkedIn" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                            </a>
                            <a href="#" class="wg-contact-info__social-link" aria-label="GitHub" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Collaboration CTA -->
                <div class="wg-contact-info__card wg-contact-info__card--cta">
                    <h3 class="wg-contact-info__card-title">Have an idea or want to collaborate?</h3>
                    <p class="wg-contact-info__card-text">Whether you have feedback, a project idea, a business opportunity, or simply want to connect with Sound Group, feel free to reach out.</p>
                    <a href="#contactForm" class="wg-btn wg-btn--primary wg-contact-info__card-btn">Get In Touch</a>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- WHATSAPP PREFER CTA -->
<section class="wg-contact-whatsapp">
    <div class="wg-contact-whatsapp__inner">
        <div class="wg-contact-whatsapp__content" data-animate="fade-up">
            <div class="wg-contact-whatsapp__icon">
                <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <h2 class="wg-contact-whatsapp__title">Prefer WhatsApp?</h2>
            <p class="wg-contact-whatsapp__desc">Chat with us directly for a faster response.</p>
            <a href="https://wa.me/923178497732?text=Hello%20Sound%20Group%2C%20I%20want%20to%20contact%20you." class="wg-btn wg-btn--primary wg-btn--lg" id="whatsappBtnBottom" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat on WhatsApp
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>

<script src="<?php echo $jsBase; ?>/contact.js"></script>
</body>
</html>
