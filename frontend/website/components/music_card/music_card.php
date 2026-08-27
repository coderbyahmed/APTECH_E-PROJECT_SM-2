<?php
if (!isset($mc_title))
    $mc_title = 'Untitled';
if (!isset($mc_artist))
    $mc_artist = 'Unknown Artist';
if (!isset($mc_album))
    $mc_album = '';
if (!isset($mc_year))
    $mc_year = '';
if (!isset($mc_genre))
    $mc_genre = '';
if (!isset($mc_language))
    $mc_language = '';
if (!isset($mc_placeholder))
    $mc_placeholder = 1;
if (!isset($mc_id))
    $mc_id = 1;
if (!isset($mc_cover_image))
    $mc_cover_image = '';
if (!isset($websiteBase))
    $websiteBase = '/Aptech_E_Project_02/sound_management/frontend/website';
$detailHref = $websiteBase . '/music_details/music_details.php?id=' . (int) $mc_id;
$coverUrl = '';
if ($mc_cover_image) {
    $coverUrl = '/Aptech_E_Project_02/sound_management/' . ltrim($mc_cover_image, '/');
}
?>
<a href="<?php echo $detailHref; ?>" class="wg-card wg-card--music wg-card--link">
    <div class="wg-card__cover">
        <?php if ($coverUrl): ?>
            <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="<?php echo htmlspecialchars($mc_title); ?>"
                class="wg-card__cover-img" loading="lazy">
        <?php else: ?>
            <div class="wg-card__cover-placeholder wg-card__cover-placeholder--<?php echo (int) $mc_placeholder; ?>"><svg
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" width="36" height="36">
                    <path d="M9 18V5l12-2v13" />
                    <circle cx="6" cy="18" r="3" />
                    <circle cx="18" cy="16" r="3" />
                </svg></div>
        <?php endif; ?>
    </div>
    <div class="wg-card__info">
        <h3 class="wg-card__title"><?php echo htmlspecialchars($mc_title); ?></h3>
        <p class="wg-card__artist"><?php echo htmlspecialchars($mc_artist); ?></p>
        <div class="wg-card__meta-grid">
            <?php if ($mc_album): ?><span class="wg-card__meta-item"><span class="wg-card__meta-label">Album</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($mc_album); ?></span></span><?php endif; ?>
            <?php if ($mc_year): ?><span class="wg-card__meta-item"><span class="wg-card__meta-label">Year</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($mc_year); ?></span></span><?php endif; ?>
            <?php if ($mc_genre): ?><span class="wg-card__meta-item"><span class="wg-card__meta-label">Genre</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($mc_genre); ?></span></span><?php endif; ?>
            <?php if ($mc_language): ?><span class="wg-card__meta-item"><span
                        class="wg-card__meta-label">Language</span><span
                        class="wg-card__meta-value"><?php echo htmlspecialchars($mc_language); ?></span></span><?php endif; ?>
        </div>
    </div>
</a>