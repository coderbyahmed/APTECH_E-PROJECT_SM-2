<?php
/**
 * SOUND Group — User Management
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireAuth();

$pageTitle = 'User Management';
$activeItem = 'user-management';

include __DIR__ . '/../layout/admin-layout.php';
?>

    <div class="um-header">
        <div class="um-header__left">
            <h1 class="um-header__title">User Management</h1>
            <p class="um-header__subtitle">Manage registered users and their account information.</p>
        </div>
    </div>

    <div class="um-toolbar">
        <div class="um-toolbar__search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="um-search-input" placeholder="Search by name or user ID..." id="umSearchInput">
        </div>
        <div class="um-toolbar__filter">
            <label class="um-toolbar__filter-label" for="umStatusFilter">Status</label>
            <select class="um-toolbar__filter-select" id="umStatusFilter">
                <option value="all">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <section class="um-grid-section">
        <div class="um-grid-section__header">
            <h2 class="um-grid-section__title">All Users</h2>
        </div>

        <div class="um-user-grid" id="umCardGrid">

            <div class="um-user-card"
                 data-user-id="U1001"
                 data-first="Ava"
                 data-last="Thompson"
                 data-email="ava.thompson@example.com"
                 data-phone="+1 555-0101"
                 data-address="123 Waves Ave, Los Angeles, CA"
                 data-registered="Jan 15, 2025"
                 data-login="Feb 12, 2025, 09:41 AM"
                 data-logout="Feb 12, 2025, 06:02 PM"
                 data-status="active">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--violet">AT</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Ava Thompson</h3>
                    </div>
                    <span class="um-badge um-badge--active">Active</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">ava.thompson@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0101</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">123 Waves Ave, Los Angeles, CA</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1001</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Jan 15, 2025</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Feb 12, 2025, 09:41 AM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Feb 12, 2025, 06:02 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="um-user-card"
                 data-user-id="U1002"
                 data-first="Liam"
                 data-last="Carter"
                 data-email="liam.carter@example.com"
                 data-phone="+1 555-0102"
                 data-address="45 Melody St, New York, NY"
                 data-registered="Feb 03, 2025"
                 data-login="Feb 11, 2025, 08:15 AM"
                 data-logout="Feb 11, 2025, 05:30 PM"
                 data-status="active">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--blue">LC</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Liam Carter</h3>
                    </div>
                    <span class="um-badge um-badge--active">Active</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">liam.carter@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0102</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">45 Melody St, New York, NY</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1002</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Feb 03, 2025</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Feb 11, 2025, 08:15 AM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Feb 11, 2025, 05:30 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="um-user-card"
                 data-user-id="U1003"
                 data-first="Sofia"
                 data-last="Reyes"
                 data-email="sofia.reyes@example.com"
                 data-phone="+1 555-0103"
                 data-address="88 Rhythm Blvd, Miami, FL"
                 data-registered="Nov 20, 2024"
                 data-login="Nov 19, 2024, 10:20 AM"
                 data-logout="Nov 19, 2024, 04:45 PM"
                 data-status="inactive">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--pink">SR</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Sofia Reyes</h3>
                    </div>
                    <span class="um-badge um-badge--inactive">Inactive</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">sofia.reyes@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0103</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">88 Rhythm Blvd, Miami, FL</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1003</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Nov 20, 2024</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Nov 19, 2024, 10:20 AM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Nov 19, 2024, 04:45 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="um-user-card"
                 data-user-id="U1004"
                 data-first="Noah"
                 data-last="Williams"
                 data-email="noah.williams@example.com"
                 data-phone="+1 555-0104"
                 data-address="210 Harmony Rd, Austin, TX"
                 data-registered="Dec 11, 2024"
                 data-login="Feb 10, 2025, 11:05 AM"
                 data-logout="Feb 10, 2025, 07:20 PM"
                 data-status="active">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--green">NW</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Noah Williams</h3>
                    </div>
                    <span class="um-badge um-badge--active">Active</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">noah.williams@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0104</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">210 Harmony Rd, Austin, TX</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1004</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Dec 11, 2024</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Feb 10, 2025, 11:05 AM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Feb 10, 2025, 07:20 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="um-user-card"
                 data-user-id="U1005"
                 data-first="Mia"
                 data-last="Chen"
                 data-email="mia.chen@example.com"
                 data-phone="+1 555-0105"
                 data-address="7 Bass Court, Seattle, WA"
                 data-registered="Feb 08, 2025"
                 data-login="Feb 12, 2025, 07:30 AM"
                 data-logout="Feb 12, 2025, 05:55 PM"
                 data-status="active">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--amber">MC</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Mia Chen</h3>
                    </div>
                    <span class="um-badge um-badge--active">Active</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">mia.chen@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0105</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">7 Bass Court, Seattle, WA</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1005</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Feb 08, 2025</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Feb 12, 2025, 07:30 AM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Feb 12, 2025, 05:55 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="um-user-card"
                 data-user-id="U1006"
                 data-first="Ethan"
                 data-last="Brooks"
                 data-email="ethan.brooks@example.com"
                 data-phone="+1 555-0106"
                 data-address="152 Vinyl Ln, Chicago, IL"
                 data-registered="Oct 30, 2024"
                 data-login="Oct 29, 2024, 02:40 PM"
                 data-logout="Oct 29, 2024, 06:10 PM"
                 data-status="inactive">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--rose">EB</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Ethan Brooks</h3>
                    </div>
                    <span class="um-badge um-badge--inactive">Inactive</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">ethan.brooks@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0106</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">152 Vinyl Ln, Chicago, IL</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1006</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Oct 30, 2024</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Oct 29, 2024, 02:40 PM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Oct 29, 2024, 06:10 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="um-user-card"
                 data-user-id="U1007"
                 data-first="Zoe"
                 data-last="Martin"
                 data-email="zoe.martin@example.com"
                 data-phone="+1 555-0107"
                 data-address="340 Treble Ave, Denver, CO"
                 data-registered="Jan 28, 2025"
                 data-login="Feb 09, 2025, 09:55 AM"
                 data-logout="Feb 09, 2025, 04:35 PM"
                 data-status="active">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--teal">ZM</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Zoe Martin</h3>
                    </div>
                    <span class="um-badge um-badge--active">Active</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">zoe.martin@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0107</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">340 Treble Ave, Denver, CO</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1007</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Jan 28, 2025</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Feb 09, 2025, 09:55 AM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Feb 09, 2025, 04:35 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="um-user-card"
                 data-user-id="U1008"
                 data-first="Lucas"
                 data-last="Bell"
                 data-email="lucas.bell@example.com"
                 data-phone="+1 555-0108"
                 data-address="91 Tempo St, San Francisco, CA"
                 data-registered="Sep 17, 2024"
                 data-login="Sep 16, 2024, 01:25 PM"
                 data-logout="Sep 16, 2024, 05:40 PM"
                 data-status="inactive">
                <div class="um-user-card__header">
                    <div class="um-avatar um-avatar--card um-avatar--indigo">LB</div>
                    <div class="um-user-card__identity">
                        <h3 class="um-user-card__name">Lucas Bell</h3>
                    </div>
                    <span class="um-badge um-badge--inactive">Inactive</span>
                </div>
                <div class="um-user-card__info">
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Email</span>
                        <span class="um-user-card__value um-user-card__value--email">lucas.bell@example.com</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Phone</span>
                        <span class="um-user-card__value um-user-card__value--phone">+1 555-0108</span>
                    </div>
                    <div class="um-user-card__row">
                        <span class="um-user-card__label">Address</span>
                        <span class="um-user-card__value um-user-card__value--address">91 Tempo St, San Francisco, CA</span>
                    </div>
                    <div class="um-user-card__divider"></div>
                    <div class="um-user-card__meta">
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">User ID</span>
                            <span class="um-user-card__value">U1008</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Registered</span>
                            <span class="um-user-card__value">Sep 17, 2024</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Login</span>
                            <span class="um-user-card__value">Sep 16, 2024, 01:25 PM</span>
                        </div>
                        <div class="um-user-card__meta-item">
                            <span class="um-user-card__label">Last Logout</span>
                            <span class="um-user-card__value">Sep 16, 2024, 05:40 PM</span>
                        </div>
                    </div>
                </div>
                <div class="um-user-card__actions">
                    <button type="button" class="um-card-action um-card-action--view" title="View" data-um-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        View
                    </button>
                    <button type="button" class="um-card-action um-card-action--edit" title="Edit" data-um-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </button>
                    <button type="button" class="um-card-action um-card-action--delete" title="Delete" data-um-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

        </div>

        <!-- Empty State -->
        <div class="um-empty" id="umEmptyState" hidden>
            <div class="um-empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="36" height="36">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h3 class="um-empty__title">No users found</h3>
            <p class="um-empty__desc">Try adjusting your search or filter to find what you're looking for.</p>
        </div>

        <div class="um-grid-section__footer">
            <span class="um-grid-section__count" id="umCount">Showing 6 of 8 users</span>
            <div class="um-pagination">
                <button type="button" class="um-pagination__btn" id="umPrevPage" aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <div class="um-pagination__pages" id="umPaginationPages">
                    <!-- Page number buttons are built by user-management.js -->
                </div>
                <button type="button" class="um-pagination__btn" id="umNextPage" aria-label="Next page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- View User Modal -->
    <div class="sg-modal" id="umViewModal">
        <div class="sg-modal__overlay" data-um-close="view"></div>
        <div class="sg-modal__dialog um-modal">
            <button type="button" class="sg-modal__close" data-um-close="view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="um-view-header">
                    <div class="um-avatar um-avatar--large um-avatar--violet" id="um-view-avatar">AT</div>
                    <div class="um-view-info">
                        <h2 class="um-view-info__title" id="um-view-name">Ava Thompson</h2>
                        <span class="um-view-info__id">User ID: <strong id="um-view-id">U1001</strong></span>
                    </div>
                </div>
                <div class="um-view-status">
                    <span class="um-badge" id="um-view-status-badge">Active</span>
                </div>
                <div class="um-view-details">
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Email</span>
                        <span class="um-view-detail__value" id="um-view-email">ava.thompson@example.com</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Phone Number</span>
                        <span class="um-view-detail__value" id="um-view-phone">+1 555-0101</span>
                    </div>
                    <div class="um-view-detail um-view-detail--full">
                        <span class="um-view-detail__label">Address</span>
                        <span class="um-view-detail__value" id="um-view-address">123 Waves Ave, Los Angeles, CA</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Registered</span>
                        <span class="um-view-detail__value" id="um-view-registered">Jan 15, 2025</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Last Login</span>
                        <span class="um-view-detail__value" id="um-view-login">Feb 12, 2025, 09:41 AM</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Last Logout</span>
                        <span class="um-view-detail__value" id="um-view-logout">Feb 12, 2025, 06:02 PM</span>
                    </div>
                    <div class="um-view-detail">
                        <span class="um-view-detail__label">Account Status</span>
                        <span class="um-view-detail__value" id="um-view-status-text">Active</span>
                    </div>
                </div>
                <div class="um-view-actions">
                    <button type="button" class="sg-btn um-btn-cancel" data-um-close="view">Close</button>
                    <button type="button" class="sg-btn sg-btn--primary" id="umViewEditBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="sg-modal" id="umEditModal">
        <div class="sg-modal__overlay" data-um-close="edit"></div>
        <div class="sg-modal__dialog um-modal um-modal--wide">
            <button type="button" class="sg-modal__close" data-um-close="edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title" id="umEditTitle">Edit User</h2>
                <p class="sg-modal__subtitle">Update <strong id="um-edit-user-name">Ava Thompson</strong>'s account information.</p>

                <form id="umEditForm" class="um-form">
                    <div class="um-form__profile">
                        <div class="um-avatar um-avatar--large um-avatar--violet" id="um-edit-avatar">AT</div>
                        <div class="um-form__profile-info">
                            <button type="button" class="sg-btn um-btn-outline" id="umEditImageBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                Change Image
                            </button>
                            <span class="um-form__profile-hint">JPG, PNG or WebP (Max 2MB)</span>
                            <input type="file" class="um-form__file-input" id="umEditImageInput" accept=".jpg,.jpeg,.png,.webp" hidden>
                        </div>
                    </div>

                    <div class="um-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-first">First Name</label>
                            <input type="text" class="sg-form-input um-form-input" id="um-edit-first" value="Ava">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-last">Last Name</label>
                            <input type="text" class="sg-form-input um-form-input" id="um-edit-last" value="Thompson">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-email">Email</label>
                            <input type="email" class="sg-form-input um-form-input" id="um-edit-email" value="ava.thompson@example.com">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-phone">Phone Number</label>
                            <input type="tel" class="sg-form-input um-form-input" id="um-edit-phone" value="+1 555-0101">
                        </div>
                        <div class="sg-form-group um-form__group--full">
                            <label class="sg-form-label" for="um-edit-address">Address</label>
                            <input type="text" class="sg-form-input um-form-input" id="um-edit-address" value="123 Waves Ave, Los Angeles, CA">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="um-edit-status">Account Status</label>
                            <select class="sg-form-input um-form-input" id="um-edit-status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="um-form__actions">
                        <button type="button" class="sg-btn um-btn-cancel" data-um-close="edit">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="umUpdateUserBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="sg-modal" id="umDeleteModal">
        <div class="sg-modal__overlay" data-um-close="delete"></div>
        <div class="sg-modal__dialog um-modal um-modal--delete">
            <button type="button" class="sg-modal__close" data-um-close="delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="um-delete-body">
                    <div class="um-delete-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </div>
                    <h2 class="sg-modal__title">Delete User</h2>
                    <p class="sg-modal__subtitle">Are you sure you want to delete <strong id="um-delete-name">Ava Thompson</strong>? This action cannot be undone.</p>
                </div>
                <div class="um-form__actions um-delete-actions">
                    <button type="button" class="sg-btn um-btn-cancel" data-um-close="delete">Cancel</button>
                    <button type="button" class="sg-btn sg-btn--danger" id="umConfirmDeleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/admin-layout-end.php'; ?>