<div class="ratings-table" id="rating">
    <span class="rating" title="<?= $rating_title; ?>">
        <a class="<?= $rating_class; ?>" data-tooltip-id="<?= $tooltip_id; ?>" <?= $rating_href; ?> id="<?= $rating_id; ?>">
            <?= CSR_Rating::output_stars($stars, $half); ?>
        </a>
    </span>
    <?php if ($details) { ?>
        <span class="rating-details">
            <a class="<?= $details_class; ?>" data-tooltip-id="<?= $tooltip_id; ?>" href="<?= $details_url; ?>">
            <?= $details_label; ?>
        </a>
    </span>
    <?php } ?>
    <?php if ($details && wp_is_mobile()) { ?>
        <span class="rating-caption"><?= $rating_title; ?></span>
    <?php } ?>
</div>
