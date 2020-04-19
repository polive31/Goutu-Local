<table class="ratings-table" id="rating">
    <tbody>
        <tr>
            <td class="rating" title="<?= $rating_title; ?>">
                <a class="<?= $rating_class; ?>" data-tooltip-id="<?= $tooltip_id; ?>" <?= $rating_href; ?> id="<?= $rating_id; ?>">
                    <?= CSR_Rating::output_stars($stars, $half); ?>
                </a>
            </td>
            <?php if ($details) { ?>
                <td class="rating-details">
                    <a class="<?= $details_class; ?>" data-tooltip-id="<?= $tooltip_id; ?>" href="<?= $details_url; ?>">
                        <?= $details_label; ?>
                    </a>
                </td>
            <?php } ?>
        </tr>
    </tbody>
</table>
