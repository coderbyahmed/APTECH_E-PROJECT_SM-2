<!-- PROFILE MODAL -->
<div class="wg-profile-overlay" id="wgProfileOverlay">
    <div class="wg-profile-modal" id="wgProfileModal" role="dialog" aria-modal="true" aria-labelledby="wgProfileTitle">

        <button class="wg-profile-modal__close" id="wgProfileClose" type="button" aria-label="Close profile modal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <div class="wg-profile-modal__scroll">

            <!-- Header -->
            <div class="wg-profile-modal__header">
                <a href="<?php echo $websiteBase; ?>/index.php" class="wg-profile-modal__logo">
                    <span class="wg-profile-modal__logo-icon">
                        <?php if (!empty($wsLogoPath)): ?>
                            <img src="<?php echo htmlspecialchars($wsLogoPath); ?>" alt="<?php echo $wsWebsiteName; ?>" style="width:28px;height:28px;object-fit:contain;">
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        <?php endif; ?>
                    </span>
                    <span class="wg-profile-modal__logo-text"><?php echo $wsWebsiteName; ?></span>
                </a>
            </div>

            <!-- ===== VIEW STATE ===== -->
            <div class="wg-profile-view" id="wgProfileView">

                <!-- Avatar -->
                <div class="wg-profile-avatar" id="wgProfileViewAvatar">
                    <?php if ($siteUserImage): ?>
                        <img src="<?php echo (strpos($siteUserImage, 'http') === 0) ? htmlspecialchars($siteUserImage) : $baseUrl . '/' . htmlspecialchars(ltrim($siteUserImage, '/')); ?>" alt="<?php echo htmlspecialchars($siteUserName); ?>" class="wg-profile-avatar__img">
                    <?php else: ?>
                        <span class="wg-profile-avatar__initial" style="background-color:<?php echo $siteUserAvatarColor; ?>;"><?php echo $siteUserInitial; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Info rows -->
                <div class="wg-profile-info">
                    <div class="wg-profile-info__row">
                        <span class="wg-profile-info__label">Full Name</span>
                        <span class="wg-profile-info__value" id="wgProfileViewName"><?php echo htmlspecialchars($siteUserName); ?></span>
                    </div>
                    <div class="wg-profile-info__row">
                        <span class="wg-profile-info__label">Email Address</span>
                        <span class="wg-profile-info__value" id="wgProfileViewEmail"><?php echo htmlspecialchars($siteUserEmail); ?></span>
                    </div>
                    <div class="wg-profile-info__row">
                        <span class="wg-profile-info__label">Phone Number</span>
                        <span class="wg-profile-info__value" id="wgProfileViewPhone"><?php echo htmlspecialchars($siteUserPhone ?: '—'); ?></span>
                    </div>
                    <div class="wg-profile-info__row">
                        <span class="wg-profile-info__label">Address</span>
                        <span class="wg-profile-info__value" id="wgProfileViewAddress"><?php echo htmlspecialchars($siteUserAddress ?: '—'); ?></span>
                    </div>
                </div>

                <!-- Messages -->
                <div class="wg-profile-form__message wg-profile-form__message--error" id="wgProfileViewError" style="display:none;"></div>
                <div class="wg-profile-form__message wg-profile-form__message--success" id="wgProfileViewSuccess" style="display:none;"></div>

                <!-- Actions -->
                <div class="wg-profile-actions">
                    <button type="button" class="wg-btn wg-btn--primary wg-btn--lg" id="wgProfileEditBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        <span>Edit Profile</span>
                    </button>
                    <button type="button" class="wg-btn wg-btn--ghost wg-btn--lg" id="wgProfileCancelViewBtn">Cancel</button>
                </div>
            </div>

            <!-- ===== EDIT STATE ===== -->
            <div class="wg-profile-edit" id="wgProfileEdit" style="display:none;">

                <!-- Avatar with change button -->
                <div class="wg-profile-avatar wg-profile-avatar--edit">
                    <div class="wg-profile-avatar__wrap" id="wgProfileEditAvatarWrap">
                        <?php if ($siteUserImage): ?>
                            <img src="<?php echo (strpos($siteUserImage, 'http') === 0) ? htmlspecialchars($siteUserImage) : $baseUrl . '/' . htmlspecialchars(ltrim($siteUserImage, '/')); ?>" alt="Profile" class="wg-profile-avatar__img" id="wgProfileEditAvatarImg">
                        <?php else: ?>
                            <span class="wg-profile-avatar__initial" style="background-color:<?php echo $siteUserAvatarColor; ?>;" id="wgProfileEditAvatarInitial"><?php echo $siteUserInitial; ?></span>
                        <?php endif; ?>
                        <div class="wg-profile-avatar__overlay" id="wgProfileChangeImageBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    </div>
                    <input type="file" class="wg-profile-avatar__input" id="wgProfileImageInput" accept=".jpg,.jpeg,.png,.webp" hidden>
                    <span class="wg-profile-avatar__hint">JPG, PNG or WebP (Max 2MB)</span>
                </div>

                <!-- Form -->
                <form class="wg-profile-form" id="wgProfileForm" novalidate>
                    <div class="wg-profile-form__field">
                        <label class="wg-profile-form__label" for="wgProfileName">Full Name <span class="wg-profile-form__required">*</span></label>
                        <input type="text" class="wg-profile-form__input" id="wgProfileName" name="full_name" placeholder="Enter your full name" required>
                    </div>
                    <div class="wg-profile-form__field">
                        <label class="wg-profile-form__label">Email Address</label>
                        <input type="email" class="wg-profile-form__input wg-profile-form__input--readonly" id="wgProfileEmail" readonly tabindex="-1">
                        <span class="wg-profile-form__readonly-hint">Email cannot be changed</span>
                    </div>
                    <div class="wg-profile-form__field">
                        <label class="wg-profile-form__label" for="wgProfilePhone">Phone Number <span class="wg-profile-form__required">*</span></label>
                        <input type="tel" class="wg-profile-form__input" id="wgProfilePhone" name="phone" placeholder="Enter 11-digit phone number" required>
                    </div>
                    <div class="wg-profile-form__field">
                        <label class="wg-profile-form__label" for="wgProfileAddress">Address</label>
                        <input type="text" class="wg-profile-form__input" id="wgProfileAddress" name="address" placeholder="Enter your address (optional)">
                    </div>

                    <!-- Messages -->
                    <div class="wg-profile-form__message wg-profile-form__message--error" id="wgProfileEditError" style="display:none;"></div>
                    <div class="wg-profile-form__message wg-profile-form__message--success" id="wgProfileEditSuccess" style="display:none;"></div>

                    <!-- Actions -->
                    <div class="wg-profile-actions">
                        <button type="submit" class="wg-btn wg-btn--primary wg-btn--lg" id="wgProfileSaveBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            <span>Save Changes</span>
                        </button>
                        <button type="button" class="wg-btn wg-btn--ghost wg-btn--lg" id="wgProfileCancelEditBtn">Cancel</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
