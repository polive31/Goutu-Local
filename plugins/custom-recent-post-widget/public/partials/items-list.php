<?php

    // Thumbnails
    $thumb_id = get_post_thumbnail_id(); // Get the featured image id.
    $img_url  = wp_get_attachment_url($thumb_id); // Get img URL.
    $entry_url = $link ? esc_url(get_permalink()) : '#';

    $thumb_width = $first ? 'first_thumb_width' : 'thumb_width';
    $thumb_height = $first ? 'first_thumb_height' : 'thumb_height';
    // $html .= '<br>$width : ' . $thumb_width;
    // $html .= '<br>$args[$width] : ' . $args[$thumb_width];
    // $html .= '<br>$height : ' . $thumb_height;
    // $html .= '<br>$args[$height] : ' . $args[$thumb_height];

    // Display the image url and crop using the resizer.
    $image    = rpwe_resize($img_url, $args[$thumb_width], $args[$thumb_height], true);

    // Start recent posts markup.
    $html .= '<li class="rpwe-li rpwe-clearfix ' . (($first) ? 'rpwe-first' : '') . '">';
    $first = false;

    if ($args['thumb']) :

        // Check if post has post thumbnail.
        if (has_post_thumbnail()) :
            $html .= '<div class="entry-header-overlay ' . $entry_class . '">';
            $html .= '<a class="rpwe-img ' . $entry_class . '" href="' . $entry_url . '"  rel="bookmark">';
            if ($image) :
                $html .= '<img class="' . esc_attr($args['thumb_align']) . ' rpwe-thumb" src="' . esc_url($image) . '" alt="' . esc_attr(get_the_title()) . '">';
            else :
                $html .= get_the_post_thumbnail(
                    get_the_ID(),
                    array($args[$thumb_width], $args[$thumb_height]),
                    array(
                        'class' => $args['thumb_align'] . ' rpwe-thumb the-post-thumbnail',
                        'alt'   => esc_attr(get_the_title())
                    )
                );
            endif;
            /* Added P.O. */
            $html = apply_filters('rpwe_in_thumbnail', $html, $args);
            // $html .= 'In the post thumbnail';
            /* End P.O. */
            $html .= '</a>';
            $html .= '</div>';

        // Display default image.
        elseif (!empty($args['thumb_default'])) :
            $html .= sprintf(
                '<a class="rpwe-img" href="%1$s" rel="bookmark"><img class="%2$s rpwe-thumb rpwe-default-thumb" src="%3$s" alt="%4$s" width="%5$s" height="%6$s"></a>',
                $entry_url,
                esc_attr($args['thumb_align']),
                esc_url($args['thumb_default']),
                esc_attr(get_the_title()),
                (int) $args['thumb_width'],
                (int) $args[$thumb_height]
            );
        // $html .= 'In the default thumb';

        endif;

    endif;


    /* Added P.O. */
    $html = apply_filters('rpwe_after_thumbnail', $html, $args);
    /* End P.O. */

    $html .= '<div class="entry-header-meta">';
    /* Added P.O. */
    $title_meta = '';
    $title_meta = apply_filters('rpwe_post_title_meta', $title_meta, $args);
    $title_html = '<h3 class="rpwe-title"><a href="' . $entry_url . '" class="' . $entry_class . '" title="' . sprintf(esc_attr__('Permalink to %s', 'recent-posts-widget-extended'), the_title_attribute('echo=0')) . '" rel="bookmark">' . esc_attr(get_the_title()) . '</a>' . $title_meta . '</h3>';
    $title_html = apply_filters('rpwe_post_title', $title_html, $args);
    $html .= $title_html;
    $html .= '</div>';
    /* End P.O. */

    /* Added P.O. */
    do_action('rpwe_loop', get_post());
    /* End P.O. */


    if ($args['date']) :
        $date = get_the_date();
        if ($args['date_relative']) :
            $date = sprintf(__('%s ago', 'recent-posts-widget-extended'), human_time_diff(get_the_date('U'), current_time('timestamp')));
        endif;
        $html .= '<time class="rpwe-time published" datetime="' . esc_html(get_the_date('c')) . '">' . esc_html($date) . '</time>';
    elseif ($args['date_modified']) : // if both date functions are provided, we use date to be backwards compatible
        $date = get_the_modified_date();
        if ($args['date_relative']) :
            $date = sprintf(__('%s ago', 'recent-posts-widget-extended'), human_time_diff(get_the_modified_date('U'), current_time('timestamp')));
        endif;
        $html .= '<time class="rpwe-time modfied" datetime="' . esc_html(get_the_modified_date('c')) . '">' . esc_html($date) . '</time>';
    endif;

    if ($args['comment_count']) :
        if (get_comments_number() == 0) {
            $comments = __('No Comments', 'recent-posts-widget-extended');
        } elseif (get_comments_number() > 1) {
            $comments = sprintf(__('%s Comments', 'recent-posts-widget-extended'), get_comments_number());
        } else {
            $comments = __('1 Comment', 'recent-posts-widget-extended');
        }
        $html .= '<a class="rpwe-comment comment-count" href="' . get_comments_link() . '">' . $comments . '</a>';
    endif;

    if ($args['excerpt']) :
        $html .= '<div class="rpwe-summary">';
        $html .= wp_trim_words(apply_filters('rpwe_excerpt', get_the_excerpt()), $args['length'], ' &hellip;');
        if ($args['readmore']) :
            $html .= '<a href="' . esc_url(get_permalink()) . '" class="more-link">' . $args['readmore_text'] . '</a>';
        endif;
        $html .= '</div>';
    endif;

    $html .= '</li>';
