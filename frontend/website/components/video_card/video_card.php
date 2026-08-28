<?php
if (!isset($vc_title))
    $vc_title = 'Untitled';
if (!isset($vc_artist))
    $vc_artist = 'Unknown Artist';
if (!isset($vc_album))
    $vc_album = '';
if (!isset($vc_year))
    $vc_year = '';
if (!isset($vc_genre))
    $vc_genre = '';
if (!isset($vc_language))
    $vc_language = '';
if (!isset($vc_duration))
    $vc_duration = '0:00';
if (!isset($vc_placeholder))
    $vc_placeholder = 1;
if (!isset($vc_id))
    $vc_id = 1;
if (!isset($vc_thumbnail))
    $vc_thumbnail = '';
if (!isset($websiteBase))
    $websiteBase = '/Aptech_E_Project_02/sound_management/frontend/website';
$detailHref = $websiteBase . '/video_detail/video_detail.php?id=' . (int)$vc_id;
$thumbUrl = '';
if ($vc_thumbnail) {
    $thumbUrl = '/Aptech_E_Project_02/sound_management/' . ltrim($vc_thumbnail, '/');
}
?>
<a href="<?php echo $detailHref; ?>" class="wg-card wg-card--video wg-card--link">
    <div class="wg-card__thumb">
        <?php if ($thumbUrl): ?>
            <img src="<?php echo htmlspecialchars($thumbUrl); ?>" alt="<?php echo htmlspecialchars($vc_title); ?>"
                class="wg-card__cover-img" loading="lazy">
        <?php else: ?>
            <div class="wg-card__thumb-placeholder wg-card__thumb-placeholder--<?php echo (int) $vc_placeholder; ?>"><svg
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" width="40" height="40">
                    <polygon points="23 7 16 12 23 17 23 7" />
                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
                </svg></div>
        <?php endif; ?>
        <div class="wg-card__play wg-card__play--video"><svg viewBox="0 0 24 24" fill="currentColor" width="28"
                height="28">
                <polygon points="5 3 19 12 5 21 5 3" />
            </svg></div>
        <span class="wg-card__duration"><?php echo htmlspecialchars($vc_duration); ?></span>
    </div>
    <div class="wg-card__info">
        <h3 class="wg-card__title"><?php echo htmlspecialchars($vc_title); ?></h3>
        <p class="wg-card__artist"><?php echo htmlspecialchars($vc_artist); ?></p>
        <div class="wg-card__meta-grid">
            <?php if ($vc_album): ?><span class="wg-card__meta-item"><span class="wg-card__meta-label">Album</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($vc_album); ?></span></span><?php endif; ?>
            <?php if ($vc_year): ?><span class="wg-card__meta-item"><span class="wg-card__meta-label">Year</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($vc_year); ?></span></span><?php endif; ?>
            <?php if ($vc_genre): ?><span class="wg-card__meta-item"><span class="wg-card__meta-label">Genre</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($vc_genre); ?></span></span><?php endif; ?>
            <?php if ($vc_language): ?><span class="wg-card__meta-item"><span
                        class="wg-card__meta-label">Language</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($vc_language); ?></span></span><?php endif; ?>
        </div>
    </div>
</a>