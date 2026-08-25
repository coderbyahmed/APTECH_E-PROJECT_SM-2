<?php
/**
 * SOUND Group — Contact Messages Management
 *
 * UI ONLY — mock data. Backend/database to be connected later.
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireAuth();

$pageTitle = 'Contact Messages';
$activeItem = 'contact-messages';

include __DIR__ . '/../layout/admin-layout.php';

/* ----------------------------------------------------------
   Mock message data (UI only). Keys describe the future
   backend structure: message id, sender name, email,
   subject, message text, date, status.
   ---------------------------------------------------------- */
$now = time();
$dateValue = function ($daysAgo) use ($now) {
    return date('Y-m-d', $now - $daysAgo * 86400);
};
$dateLabel = function ($daysAgo) use ($now) {
    return date('M j, Y', $now - $daysAgo * 86400);
};

$messages = [
    // id, first, last, avatar, email, subject, daysAgo, status, text
    ['CM-101', 'Sarah',  'Mitchell',  'violet', 'sarah.mitchell@gmail.com',   'Licensing inquiry for our cafe playlist',    0,  'new',  'Hi there, I run a small cafe in Portland and I would love to use a few tracks from your catalog as background music. Could you share the licensing terms and pricing for commercial use? We also host a live music night every Friday and would love to feature some local SOUND artists.' ],
    ['CM-102', 'James',  "O'Connor",  'blue',   'james.oconnor@outlook.com',  'Artist submission - demo track',             0,  'new',  'Hello SOUND team, I am an independent producer from Dublin and I recently finished a demo I think would fit your label perfectly. It is an alternative rock track with a lot of energy. Where can I send the files? Happy to share a streaming link first if that is easier.' ],
    ['CM-103', 'Priya',  'Sharma',    'pink',   'priya.sharma@yahoo.com',     'Issue with premium subscription',            1,  'read', 'Hi, I upgraded to the premium plan yesterday but the high-quality streaming option is still locked in my account. I have already tried logging out and back in. Could you take a look at my account? The subscription email is the one used here.' ],
    ['CM-104', 'Marcus', 'Webb',      'green',  'marcus.webb@protonmail.com','Partnership opportunity with our record label', 2, 'new', 'Dear SOUND Group, our label represents twelve emerging electronic artists and we would love to discuss a distribution partnership with your platform. We are especially interested in your playlist placement program and the analytics tools you offer to signed artists. Please let me know if you are open to a call this week.' ],
    ['CM-105', 'Elena',  'Petrova',   'amber',  'elena.petrova@gmail.com',    'Billing question about the yearly plan',     3,  'read', 'Hello, I was charged the full yearly amount today but I thought I was still on the monthly plan. Can you confirm when my billing cycle renewed and whether I can switch back to monthly without a penalty? Thank you.' ],
    ['CM-106', 'David',  'Kim',       'teal',   'david.kim@icloud.com',       'Feedback on the mobile app',                 4,  'new',  'Overall I love the app, it has been my daily driver for months. One suggestion though: the queue page could really use a drag-to-reorder handle. Right now reordering tracks on mobile is painful because you have to tap and hold for a long time. Thanks for considering it.' ],
    ['CM-107', 'Amara',  'Diallo',    'rose',   'amara.diallo@gmail.com',     'Request to feature our band on the homepage', 6, 'read', 'Hi! My band recently released our debut EP on your platform and we would love to be considered for the featured artist slot on the homepage. We have a growing audience and strong engagement numbers. Happy to provide press photos, a short bio and any other materials you might need.' ],
    ['CM-108', 'Tomislav','Horvat',   'indigo', 'tom.horvat@outlook.com',     'Report an issue with video playback',        8,  'read', 'I am having trouble streaming one of your official videos. It loads for a few seconds and then starts buffering endlessly even on a fast connection. I tried on both Wi-Fi and mobile data. The rest of the site works fine, so it seems specific to that video.' ],
    ['CM-109', 'Grace',  'Liu',       'cyan',   'grace.liu@gmail.com',        'Media press kit request',                    9,  'new',  'Hello, I am a journalist writing a feature about the rise of independent streaming platforms. Could you point me to your media press kit or a downloadable logo pack? I would also love to set up a short interview with someone from your team if possible.' ],
    ['CM-110', 'Omar',   'Farouk',    'orange', 'omar.farouk@zoho.com',       'Advertising campaign collaboration',        12, 'read', 'We are a digital marketing agency running campaigns for several lifestyle brands and we believe a collaboration with SOUND Group could be mutually beneficial. We would like to propose a sponsored playlist campaign. Are you open to exploring branded playlist slots or sponsored placements in your discover feed?' ],
    ['CM-111', 'Nina',   'Kowalski',  'rose',   'nina.kowalski@gmail.com',    'Account recovery help',                     15, 'read', 'I think I deleted my account by mistake a few months ago. I would really like to get my playlists back. Is it possible to restore a deleted account or recover the playlists? I remember the email I registered with but not the exact password.' ],
    ['CM-112', 'Leo',    'Mendes',    'teal',   'leo.mendes@gmail.com',       'Compliment on the new album',               16, 'new',  'Just wanted to say the new Neon Tides album is fantastic. The production quality is on another level and I have had it on repeat all week. You guys keep outdoing yourselves. That is all, keep up the great work!' ],
];

/* ----------------------------------------------------------
   Summary statistics (computed from mock data)
   ---------------------------------------------------------- */
$totalMessages = count($messages);
$newMessages = 0;
foreach ($messages as $m) {
    if ($m[7] === 'new') {
        $newMessages++;
    }
}
$readMessages = $totalMessages - $newMessages;
?>

    <div class="cm-header">
        <div class="cm-header__left">
            <h1 class="cm-header__title">Contact Messages</h1>
            <p class="cm-header__subtitle">Review and respond to messages submitted through the public contact form.</p>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="cm-stats">
        <div class="cm-stat-card">
            <div class="cm-stat-card__icon cm-stat-card__icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="cm-stat-card__info">
                <span class="cm-stat-card__label">Total Messages</span>
                <span class="cm-stat-card__value" id="cmTotalMessages"><?php echo $totalMessages; ?></span>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-card__icon cm-stat-card__icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <path d="M22 12h-6l-2 3h-4l-2-3H2"/>
                    <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>
                </svg>
            </div>
            <div class="cm-stat-card__info">
                <span class="cm-stat-card__label">New Messages</span>
                <span class="cm-stat-card__value" id="cmNewMessages"><?php echo $newMessages; ?></span>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-card__icon cm-stat-card__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="cm-stat-card__info">
                <span class="cm-stat-card__label">Read Messages</span>
                <span class="cm-stat-card__value" id="cmReadMessages"><?php echo $readMessages; ?></span>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="cm-toolbar">
        <div class="cm-toolbar__search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="cm-search-input" placeholder="Search by sender name, email or subject..." id="cmSearchInput">
        </div>
        <div class="cm-toolbar__filter">
            <label class="cm-toolbar__filter-label" for="cmStatusFilter">Status</label>
            <select class="cm-toolbar__filter-select" id="cmStatusFilter">
                <option value="all">All</option>
                <option value="new">New</option>
                <option value="read">Read</option>
            </select>
        </div>
    </div>

    <!-- Messages Grid -->
    <section class="cm-grid-section">
        <div class="cm-grid-section__header">
            <h2 class="cm-grid-section__title">All Messages</h2>
        </div>

        <div class="cm-message-grid" id="cmMessageGrid">

            <?php foreach ($messages as $m) {
                list($mid, $first, $last, $avatarColor, $email, $subject, $daysAgo, $status, $text) = $m;
                $dValue = $dateValue($daysAgo);
                $dLabel = $dateLabel($daysAgo);
                $fullName = $first . ' ' . $last;
                $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
                $statusClass = $status === 'new' ? 'cm-badge--new' : 'cm-badge--read';
                $statusLabel = $status === 'new' ? 'New' : 'Read';
            ?>
            <article class="cm-message-card"
                     data-cmid="<?php echo $mid; ?>"
                     data-first="<?php echo htmlspecialchars($first); ?>"
                     data-last="<?php echo htmlspecialchars($last); ?>"
                     data-avatar="<?php echo $avatarColor; ?>"
                     data-initials="<?php echo $initials; ?>"
                     data-email="<?php echo htmlspecialchars($email); ?>"
                     data-subject="<?php echo htmlspecialchars($subject); ?>"
                     data-date="<?php echo $dValue; ?>"
                     data-label="<?php echo $dLabel; ?>"
                     data-status="<?php echo $status; ?>"
                     data-text="<?php echo htmlspecialchars($text); ?>">
                <div class="cm-message-card__header">
                    <div class="cm-avatar cm-avatar--card cm-avatar--<?php echo $avatarColor; ?>"><?php echo $initials; ?></div>
                    <div class="cm-message-card__user">
                        <h3 class="cm-message-card__user-name"><?php echo htmlspecialchars($fullName); ?></h3>
                        <span class="cm-message-card__user-email"><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <span class="cm-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                </div>
                <div class="cm-message-card__content">
                    <h4 class="cm-message-card__subject"><?php echo htmlspecialchars($subject); ?></h4>
                </div>
                <p class="cm-message-card__preview"><?php echo htmlspecialchars($text); ?></p>
                <div class="cm-message-card__meta">
                    <span class="cm-message-card__meta-item">Received: <strong><?php echo $dLabel; ?></strong></span>
                </div>
                <div class="cm-message-card__actions">
                    <button type="button" class="cm-action-btn cm-action-btn--view" title="View" data-cm-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <button type="button" class="cm-action-btn cm-action-btn--delete" title="Delete" data-cm-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                    </button>
                </div>
            </article>
            <?php } ?>

        </div>

        <!-- Empty State -->
        <div class="cm-empty" id="cmEmptyState" hidden>
            <div class="cm-empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <h3 class="cm-empty__title">No messages found</h3>
            <p class="cm-empty__desc">Try adjusting your search or filters.</p>
        </div>

        <div class="cm-grid-section__footer">
            <span class="cm-grid-section__count" id="cmCount">Showing 8 of <?php echo $totalMessages; ?> messages</span>
            <div class="cm-pagination">
                <button type="button" class="cm-pagination__btn" id="cmPrevPage" aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <div class="cm-pagination__pages" id="cmPaginationPages">
                    <!-- Page number buttons are built by contact-messages.js -->
                </div>
                <button type="button" class="cm-pagination__btn" id="cmNextPage" aria-label="Next page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- View Message Modal -->
    <div class="sg-modal" id="cmViewModal">
        <div class="sg-modal__overlay" data-cm-close="view"></div>
        <div class="sg-modal__dialog cm-modal cm-modal--wide">
            <button type="button" class="sg-modal__close" data-cm-close="view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="cm-view-header">
                    <div class="cm-avatar cm-avatar--large cm-avatar--violet" id="cm-view-avatar">SM</div>
                    <div class="cm-view-info">
                        <h2 class="cm-view-info__name" id="cm-view-name">Sarah Mitchell</h2>
                        <span class="cm-view-info__email" id="cm-view-email">sarah.mitchell@gmail.com</span>
                    </div>
                </div>
                <div class="cm-view-meta">
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Message ID</span>
                        <span class="cm-view-detail__value" id="cm-view-id">CM-101</span>
                    </div>
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Received</span>
                        <span class="cm-view-detail__value" id="cm-view-date">Aug 19, 2026</span>
                    </div>
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Status</span>
                        <span class="cm-view-detail__value">
                            <span class="cm-badge cm-badge--new" id="cm-view-status-badge">New</span>
                        </span>
                    </div>
                </div>
                <div class="cm-view-subject">
                    <span class="cm-view-subject__label">Subject</span>
                    <h3 class="cm-view-subject__value" id="cm-view-subject">Licensing inquiry for our cafe playlist</h3>
                </div>
                <div class="cm-view-message">
                    <span class="cm-view-message__label">Message</span>
                    <p class="cm-view-message__text" id="cm-view-text">Hi there...</p>
                </div>
                <div class="cm-view-actions">
                    <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="view">Close</button>
                    <button type="button" class="sg-btn sg-btn--primary" id="cmMarkReadBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            <path d="M12 3v6"/>
                        </svg>
                        Mark as Read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Message Modal -->
    <div class="sg-modal" id="cmDeleteModal">
        <div class="sg-modal__overlay" data-cm-close="delete"></div>
        <div class="sg-modal__dialog cm-modal cm-modal--delete">
            <button type="button" class="sg-modal__close" data-cm-close="delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="cm-delete-body">
                    <div class="cm-delete-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </div>
                    <h2 class="sg-modal__title">Delete Message</h2>
                    <p class="sg-modal__subtitle">Are you sure you want to delete the message from <strong id="cm-delete-name">Sarah Mitchell</strong> about <strong id="cm-delete-subject">Licensing inquiry</strong>? This action cannot be undone.</p>
                </div>
                <div class="cm-form__actions cm-delete-actions">
                    <button type="button" class="sg-btn cm-btn-cancel" data-cm-close="delete">Cancel</button>
                    <button type="button" class="sg-btn cm-btn-danger" id="cmConfirmDeleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete Message
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/admin-layout-end.php'; ?>