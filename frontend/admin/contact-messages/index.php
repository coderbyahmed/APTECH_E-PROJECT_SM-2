<?php
/**
 * SOUND Group — Contact Messages Management
 *
 * Fetches real contact messages from the database.
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireAuth();

$pageTitle = 'Contact Messages';
$activeItem = 'contact-messages';

include __DIR__ . '/../layout/admin-layout.php';

$db = getDb();

/* ----------------------------------------------------------
   Fetch real stats from database
   ---------------------------------------------------------- */
$statsStmt = $db->query("SELECT COUNT(*) AS total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS new_count, SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) AS read_count FROM contact_messages");
$stats = $statsStmt->fetch();
$totalMessages = (int) ($stats['total'] ?? 0);
$newMessages = (int) ($stats['new_count'] ?? 0);
$readMessages = (int) ($stats['read_count'] ?? 0);

/* ----------------------------------------------------------
   Helper functions for rendering cards
   ---------------------------------------------------------- */
function cmBuildRecord($row) {
    $fullNameParts = explode(' ', trim($row['full_name']), 2);
    $firstName = $fullNameParts[0] ?? '';
    $lastName = $fullNameParts[1] ?? '';
    $initials = strtoupper(substr($firstName, 0, 1));
    if ($lastName !== '') {
        $initials .= strtoupper(substr($lastName, 0, 1));
    } else {
        $initials .= strtoupper(substr($firstName, 1, 1));
    }

    $avatarColors = ['violet','blue','pink','green','amber','rose','teal','indigo','cyan','orange'];
    $colorIndex = crc32($row['full_name']) % count($avatarColors);

    return [
        'id'            => (int) $row['id'],
        'message_id'    => 'CM-' . (int) $row['id'],
        'full_name'     => $row['full_name'],
        'first_name'    => $firstName,
        'last_name'     => $lastName,
        'initials'      => $initials,
        'avatar_color'  => $avatarColors[$colorIndex],
        'profile_image' => !empty($row['profile_image']) ? $row['profile_image'] : null,
        'email'         => $row['email'],
        'phone'         => $row['phone'] ?? '',
        'inquiry_type'  => $row['inquiry_type'],
        'subject'       => $row['subject'],
        'message'       => $row['message'],
        'is_read'       => (int) $row['is_read'],
        'status'        => (int) $row['is_read'] ? 'read' : 'new',
        'created_at'    => $row['created_at'],
    ];
}

function cmFormatDate($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return '';
    return date('M j, Y', strtotime($ts));
}

function cmFormatDateTime($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return '';
    return date('M j, Y, g:i A', strtotime($ts));
}

function cmInquiryLabel($type) {
    $labels = [
        'general'     => 'General Inquiry',
        'feedback'    => 'Feedback',
        'report'      => 'Report an Issue',
        'request'     => 'Music / Video Request',
        'business'    => 'Business / Collaboration',
        'partnership' => 'Investment / Partnership',
        'other'       => 'Other',
    ];
    return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

/* ----------------------------------------------------------
   Fetch first page of messages for initial render
   ---------------------------------------------------------- */
$perPage = 8;
$stmt = $db->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', 0, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

$messages = [];
foreach ($rows as $row) {
    $messages[] = cmBuildRecord($row);
}

$initialTotal = $totalMessages;
$initialPages = max(1, ceil($totalMessages / $perPage));
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
                $statusClass = $m['status'] === 'new' ? 'cm-badge--new' : 'cm-badge--read';
                $statusLabel = $m['status'] === 'new' ? 'New' : 'Read';
            ?>
            <article class="cm-message-card"
                     data-cmid="<?php echo $m['id']; ?>"
                     data-message-id="<?php echo htmlspecialchars($m['message_id']); ?>"
                     data-first="<?php echo htmlspecialchars($m['first_name']); ?>"
                     data-last="<?php echo htmlspecialchars($m['last_name']); ?>"
                     data-avatar="<?php echo $m['avatar_color']; ?>"
                     data-initials="<?php echo $m['initials']; ?>"
                     data-profile-image="<?php echo htmlspecialchars($m['profile_image'] ?? ''); ?>"
                     data-email="<?php echo htmlspecialchars($m['email']); ?>"
                     data-phone="<?php echo htmlspecialchars($m['phone']); ?>"
                     data-inquiry="<?php echo htmlspecialchars($m['inquiry_type']); ?>"
                     data-subject="<?php echo htmlspecialchars($m['subject']); ?>"
                     data-date="<?php echo htmlspecialchars(cmFormatDate($m['created_at'])); ?>"
                     data-datetime="<?php echo htmlspecialchars(cmFormatDateTime($m['created_at'])); ?>"
                     data-status="<?php echo $m['status']; ?>"
                     data-text="<?php echo htmlspecialchars($m['message']); ?>">
                <div class="cm-message-card__header">
                    <div class="cm-avatar cm-avatar--card cm-avatar--<?php echo $m['avatar_color']; ?>"><?php if (!empty($m['profile_image'])): ?><img src="/Aptech_E_Project_02/sound_management/<?php echo htmlspecialchars($m['profile_image']); ?>" alt="" class="cm-avatar__img" width="48" height="48"><?php else: ?><?php echo $m['initials']; ?><?php endif; ?></div>
                    <div class="cm-message-card__user">
                        <h3 class="cm-message-card__user-name"><?php echo htmlspecialchars($m['full_name']); ?></h3>
                        <span class="cm-message-card__user-email"><?php echo htmlspecialchars($m['email']); ?></span>
                    </div>
                    <span class="cm-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                </div>
                <div class="cm-message-card__content">
                    <h4 class="cm-message-card__subject"><?php echo htmlspecialchars($m['subject']); ?></h4>
                </div>
                <p class="cm-message-card__preview"><?php echo htmlspecialchars($m['message']); ?></p>
                <div class="cm-message-card__meta">
                    <span class="cm-message-card__meta-item">Received: <strong><?php echo htmlspecialchars(cmFormatDate($m['created_at'])); ?></strong></span>
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
        <div class="cm-empty" id="cmEmptyState" <?php if ($totalMessages > 0) echo 'hidden'; ?>>
            <div class="cm-empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <h3 class="cm-empty__title">No messages found</h3>
            <p class="cm-empty__desc">Try adjusting your search or filters.</p>
        </div>

        <div class="cm-grid-section__footer">
            <span class="cm-grid-section__count" id="cmCount">Showing <?php echo $totalMessages > 0 ? '1' : '0'; ?><?php echo $totalMessages > 8 ? '&ndash;8' : ''; ?> of <?php echo $totalMessages; ?> messages</span>
            <div class="cm-pagination">
                <button type="button" class="cm-pagination__btn" id="cmPrevPage" aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <div class="cm-pagination__pages" id="cmPaginationPages">
                    <!-- Page number buttons are built by contact-messages.js -->
                </div>
                <button type="button" class="cm-pagination__btn" id="cmNextPage" aria-label="Next page" <?php if ($initialPages <= 1) echo 'disabled'; ?>>
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
                    <div class="cm-avatar cm-avatar--large cm-avatar--violet" id="cm-view-avatar"><span id="cm-view-avatar-text">SM</span><img src="" alt="" class="cm-avatar__img" id="cm-view-avatar-img" style="display:none;"></div>
                    <div class="cm-view-info">
                        <h2 class="cm-view-info__name" id="cm-view-name">—</h2>
                        <span class="cm-view-info__email" id="cm-view-email">—</span>
                    </div>
                </div>
                <div class="cm-view-meta">
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Message ID</span>
                        <span class="cm-view-detail__value" id="cm-view-id">—</span>
                    </div>
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Received</span>
                        <span class="cm-view-detail__value" id="cm-view-date">—</span>
                    </div>
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Status</span>
                        <span class="cm-view-detail__value">
                            <span class="cm-badge cm-badge--new" id="cm-view-status-badge">New</span>
                        </span>
                    </div>
                </div>
                <div class="cm-view-meta" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Phone / WhatsApp</span>
                        <span class="cm-view-detail__value" id="cm-view-phone">—</span>
                    </div>
                    <div class="cm-view-detail">
                        <span class="cm-view-detail__label">Inquiry Type</span>
                        <span class="cm-view-detail__value" id="cm-view-inquiry">—</span>
                    </div>
                </div>
                <div class="cm-view-subject" style="margin-top: 1rem;">
                    <span class="cm-view-subject__label">Subject</span>
                    <h3 class="cm-view-subject__value" id="cm-view-subject">—</h3>
                </div>
                <div class="cm-view-message">
                    <span class="cm-view-message__label">Message</span>
                    <p class="cm-view-message__text" id="cm-view-text">—</p>
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
                    <p class="sg-modal__subtitle">Are you sure you want to delete the message from <strong id="cm-delete-name">—</strong> about <strong id="cm-delete-subject">—</strong>? This action cannot be undone.</p>
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
