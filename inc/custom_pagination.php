<?php

/**
 * Custom pagination
 */

function paginate_links_custom($args = '')
{
    global $wp_query, $wp_rewrite;

    // Setting up default values based on the current URL.
    $pagenum_link = html_entity_decode(get_pagenum_link());
    $url_parts = explode('?', $pagenum_link);

    // Get max pages and current page out of the current query, if available.
    $total = isset($wp_query->max_num_pages) ? $wp_query->max_num_pages : 1;
    $current = get_query_var('paged') ? (int)get_query_var('paged') : 1;

    // Append the format placeholder to the base URL.
    $pagenum_link = trailingslashit($url_parts[0]) . '%_%';

    // URL base depends on permalink settings.
    $format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
    $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

    $defaults = array(
        'base' => $pagenum_link,
        'format' => $format,
        'total' => $total,
        'current' => $current,
        'aria_current' => 'page',
        'show_all' => false,
        'prev_next' => true,
        'prev_text' => __('Previous'),
        'next_text' => __('Next'),
        'end_size' => 1,
        'mid_size' => 2,
        'type' => 'plain',
        'add_args' => array(),
        'add_fragment' => '',
        'before_page_number' => '',
        'after_page_number' => '',
    );

    $args = wp_parse_args($args, $defaults);

    if (!is_array($args['add_args'])) {
        $args['add_args'] = array();
    }

    if (isset($url_parts[1])) {
        $format = explode('?', str_replace('%_%', $args['format'], $args['base']));
        $format_query = isset($format[1]) ? $format[1] : '';
        wp_parse_str($format_query, $format_args);
        wp_parse_str($url_parts[1], $url_query_args);
        foreach ($format_args as $format_arg => $format_arg_value) {
            unset($url_query_args[$format_arg]);
        }
        $args['add_args'] = array_merge($args['add_args'], urlencode_deep($url_query_args));
    }

    $total = (int)$args['total'];
    if ($total < 2) {
        return;
    }
    $current = (int)$args['current'];
    $end_size = (int)$args['end_size'];
    if ($end_size < 1) {
        $end_size = 1;
    }
    $mid_size = (int)$args['mid_size'];
    if ($mid_size < 0) {
        $mid_size = 2;
    }

    $add_args = $args['add_args'];
    $r = '';
    $page_links = array();
    $dots = false;

    if ($args['prev_next'] && $current && 1 < $current) :
        $link = str_replace('%_%', 2 == $current ? '' : $args['format'], $args['base']);
        $link = str_replace('%#%', $current - 1, $link);
        if ($add_args) {
            $link = add_query_arg($add_args, $link);
        }
        $link .= $args['add_fragment'];
        $page_links[] = sprintf(
            '<a class="prev load_page" href="%s">%s</a>',
            esc_url(apply_filters('paginate_links', $link)),
            $args['prev_text']
        );
    else:
        $page_links[] = sprintf('<span class="prev disabled">%s</span>', $args['prev_text']);
    endif;

    for ($n = 1; $n <= $total; $n++) :
        if ($n == $current) :
            $page_links[] = sprintf(
                '<span aria-current="%s" class="page-numbers current">%s</span>',
                esc_attr($args['aria_current']),
                $args['before_page_number'] . number_format_i18n($n) . $args['after_page_number']
            );
            $dots = true;
        else :
            if ($args['show_all'] || ($n <= $end_size || ($current && $n >= $current - $mid_size && $n <= $current + $mid_size) || $n > $total - $end_size)) :
                $link = str_replace('%_%', 1 == $n ? '' : $args['format'], $args['base']);
                $link = str_replace('%#%', $n, $link);
                if ($add_args) {
                    $link = add_query_arg($add_args, $link);
                }
                $link .= $args['add_fragment'];
                $page_links[] = sprintf(
                    '<a class="page-numbers load_page" href="%s" data-page="%s">%s</a>',
                    esc_url(apply_filters('paginate_links', $link)),
                    number_format_i18n($n),
                    $args['before_page_number'] . number_format_i18n($n) . $args['after_page_number']
                );
                $dots = true;
            elseif ($dots && !$args['show_all']) :
                $page_links[] = '<span class="page-numbers dots">' . __('&hellip;') . '</span>';
                $dots = false;
            endif;
        endif;
    endfor;

    if ($args['prev_next'] && $current && $current < $total) :
        $link = str_replace('%_%', $args['format'], $args['base']);
        $link = str_replace('%#%', $current + 1, $link);
        if ($add_args) {
            $link = add_query_arg($add_args, $link);
        }
        $link .= $args['add_fragment'];
        $page_links[] = sprintf(
            '<a class="next load_page" href="%s">%s</a>',
            esc_url(apply_filters('paginate_links', $link)),
            $args['next_text']
        );
        $page_links_more[] = sprintf(
            '<a class="load_page load_more ' . $args['load_more_classes'] . '" href="%s" data-page="%s">' . $args['load_more_label'] . '</a>',
            esc_url(apply_filters('paginate_links', $link)),
            number_format_i18n($current + 1),
        );
    else:
        $page_links_more[] = '';
        $page_links[] = sprintf('<span class="next disabled">%s</span>', $args['next_text']);
    endif;

    switch ($args['type']) {
        case 'list':
            $r .= "<div id='pagination_holder' class='load_more_holder'>";
            $r .= implode("\n", $page_links);
            $r .= "</div>";
            break;
        case 'both':
            $r .= "<div id='pagination_holder' class='load_more_holder'>";
            $r .= implode("\n", $page_links);
            $r .= '<div>' . implode("\n", $page_links_more) . '</div>';
            $r .= "</div>";
            break;
        case 'none':
            break;
        default:
            $r .= "<div id='pagination_holder' class='load_more_holder'>";
            $r .= implode("\n", $page_links_more);
            $r .= "</div>";
            break;
    }

    $r = apply_filters('paginate_links_output', $r, $args);
    return $r;
}
