<?php
/**
 * SOUND Group — My Profile Modal Component
 * Two-view modal: view profile -> edit profile.
 * Include inside the admin layout body.
 */

$mpCsrf    = csrfToken();
$mpEndpoint = baseUrl() . '/backend/handlers/admin-profile-handler.php';
$baseUrl    = baseUrl();
?>
<div class="sg-modal" id="myProfileModal" aria-hidden="true">
    <div class="sg-modal__overlay" data-mp-close></div>
    <div class="sg-modal__dialog sg-modal__dialog--wide" role="dialog" aria-modal="true" aria-labelledby="mpTitle">
        <button type="button" class="sg-modal__close" data-mp-close aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="sg-modal__body">

            <!-- View Profile -->
            <div class="sg-modal__step" id="mpViewStep">
                <div class="mp-view">
                    <!-- Avatar -->
                    <div class="mp-view__avatar-wrap">
                        <div class="mp-view__avatar" id="mpViewAvatar">
                            <img id="mpViewAvatarImg" src="" alt="Profile" style="display:none;">
                            <span id="mpViewAvatarInitials"></span>
                        </div>
                    </div>

                    <!-- Info -->
                    <h3 class="sg-modal__title" id="mpTitle">My Profile</h3>

                    <div class="mp-view__details">
                        <div class="mp-view__row">
                            <span class="mp-view__label">Name</span>
                            <span class="mp-view__value" id="mpViewName">—</span>
                        </div>
                        <div class="mp-view__row">
                            <span class="mp-view__label">Email</span>
                            <span class="mp-view__value" id="mpViewEmail">—</span>
                        </div>
                        <div class="mp-view__row">
                            <span class="mp-view__label">Address</span>
                            <span class="mp-view__value" id="mpViewAddress">—</span>
                        </div>
                        <div class="mp-view__row">
                            <span class="mp-view__label">Created At</span>
                            <span class="mp-view__value" id="mpViewCreated">—</span>
                        </div>
                        <div class="mp-view__row">
                            <span class="mp-view__label">Updated At</span>
                            <span class="mp-view__value" id="mpViewUpdated">—</span>
                        </div>
                    </div>

                    <div class="mp-view__actions">
                        <button type="button" class="sg-btn mp-btn-cancel" data-mp-close>Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="mpEditProfileBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Profile -->
            <div class="sg-modal__step" id="mpEditStep" hidden>
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h3 class="sg-modal__title">Edit Profile</h3>
                <p class="sg-modal__subtitle">Update your name, address and profile picture.</p>

                <form id="mpEditForm" class="sg-form" data-endpoint="<?php echo $mpEndpoint; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $mpCsrf; ?>">
                    <input type="hidden" name="action" value="update_profile">

                    <!-- Profile Image Upload -->
                    <div class="sg-form-group">
                        <label class="sg-form-label">Profile Image</label>
                        <div class="mp-edit__image-section">
                            <div class="mp-edit__image-preview" id="mpEditImagePreview">
                                <img id="mpEditPreviewImg" src="" alt="Preview" style="display:none;">
                                <span id="mpEditPreviewInitials"></span>
                            </div>
                            <div class="mp-edit__image-controls">
                                <label for="mpProfileImageInput" class="sg-btn mp-btn-upload">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    Choose Image
                                </label>
                                <input type="file" id="mpProfileImageInput" name="profile_image" accept="image/jpeg,image/png,image/webp" style="display:none;">
                                <span class="mp-edit__image-hint">JPG, PNG or WebP. Max 2MB.</span>
                                <button type="button" class="sg-btn--link mp-remove-image-btn" id="mpRemoveImageBtn" style="display:none;">Remove image</button>
                            </div>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="mpEditName">Name</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input type="text" id="mpEditName" name="name" class="sg-form-input" placeholder="Enter your name" maxlength="255">
                        </div>
                    </div>

                    <!-- Email (Disabled) -->
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="mpEditEmail">Email</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </span>
                            <input type="email" id="mpEditEmail" class="sg-form-input" disabled readonly>
                        </div>
                        <span class="sg-form-hint">Email cannot be changed here. Use Change Email option.</span>
                    </div>

                    <!-- Address -->
                    <div class="sg-form-group">
                        <label class="sg-form-label" for="mpEditAddress">Address</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </span>
                            <input type="text" id="mpEditAddress" name="address" class="sg-form-input" placeholder="Enter your address" maxlength="500">
                        </div>
                    </div>

                    <button type="submit" class="sg-btn sg-btn--primary sg-btn--block" id="mpSaveBtn">
                        <span>Save Changes</span>
                    </button>
                </form>

                <button type="button" class="sg-modal__back" id="mpBackToView">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Back
                </button>
            </div>

        </div>
    </div>
</div>
