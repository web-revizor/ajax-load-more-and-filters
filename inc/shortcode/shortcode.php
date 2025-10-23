<?php

/**
 * Shortcode для відображення постів з AJAX
 */
function all_posts_ajax_att($atts)
{
    global $plugin_all_ajax_dir;
    $default = array(
        'post_type' => 'post',
        'posts_per_page' => '10',
        'type_pagination' => 'default',
        'row_classes' => 'posts_row',
        'load_more_label' => 'Show more',
        'load_more_classes' => 'load_more_button',
        'prev_text' => 'Previous',
        'next_text' => 'Next',
        'filter_id' => '', // ID фільтра для зв'язку
    );

    $a = shortcode_atts($default, $atts);
    $posts_type = $a['post_type'];
    $posts_per_page = $a['posts_per_page'];
    $type_pagination = $a['type_pagination'];
    $row_classes = $a['row_classes'];
    $load_more_label = $a['load_more_label'];
    $load_more_classes = $a['load_more_classes'];
    $prev_text = $a['prev_text'];
    $next_text = $a['next_text'];
    $filter_id = $a['filter_id'];

    $args = array(
        'post_type' => $posts_type,
        'posts_per_page' => $posts_per_page,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'all_posts_ajax_hide',
                'value' => '1',
                'compare' => '!='
            ),
            array(
                'key' => 'all_posts_ajax_hide',
                'compare' => 'NOT EXISTS'
            ),
        ),
    );

    $posts_result = '';
    $my_load_more_pagination = '';
    $results = '';

    $wp_query = new WP_Query($args);
    $pagenum_link = html_entity_decode(get_pagenum_link());
    $url_parts = explode('?', $pagenum_link);

    // Append the format placeholder to the base URL.
    $pagenum_link = trailingslashit($url_parts[0]) . '%_%';

    global $wp_rewrite;
    // URL base depends on permalink settings.
    $format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
    $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

    if ($wp_query->have_posts()):
        ob_start();
        while ($wp_query->have_posts()):
            $wp_query->the_post();
            get_template_part('all_posts_ajax/' . $posts_type . '-card');
        endwhile;
        $posts_result = ob_get_contents();
        ob_end_clean();

        if ($wp_query->max_num_pages > 1):
            $args_pagination = array(
                'base' => $pagenum_link,
                'format' => $format,
                'total' => $wp_query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'type' => $type_pagination,
                'load_more_classes' => $load_more_classes,
                'load_more_label' => $load_more_label,
                'prev_text' => $prev_text,
                'next_text' => $next_text,
            );
            $my_load_more_pagination = paginate_links_custom($args_pagination);
        endif;
        wp_reset_postdata();
    endif;

    $filter_data_attr = $filter_id ? ' data-filter-id="' . esc_attr($filter_id) . '"' : '';

    $results .= '<div class="ajax_row_holder" data-init-page="' . max(1, get_query_var('paged')) . '"><div class="ajax_row ' . $row_classes . '"' . $filter_data_attr . ' data-pagination-type="' . $type_pagination . '" data-posts-per-page="' . $posts_per_page . '" data-posts-type="' . $posts_type . '" data-more-classes="' . $load_more_classes . '" data-more-label="' . $load_more_label . '" data-prev-text="' . $prev_text . '" data-next-text="' . $next_text . '">';
    $results .= $posts_result;
    $results .= '</div>';
    $results .= $my_load_more_pagination;
    $results .= '</div>';

    return $results;
}

add_shortcode('all_posts_ajax', 'all_posts_ajax_att');


/**
 * Shortcode для фільтрів
 */
function all_posts_ajax_filters_att($atts)
{
    global $plugin_all_ajax_dir;
    global $load_more_variables;

    $default = array(
        'post_type' => 'post',
        'enable_clear_button' => false,
        'filter_by_category' => false,
        'multiply_filter' => 'false',
        'filter_type' => 'button',
        'filter_titles' => false,
        'filter_row_classes' => 'filter_row',
        'filter_item_classes' => 'filter_item',
        'filter_item_limit' => 0,
        'filter_expand_label' => 'See all',
        'filter_expand_class' => 'filter_expand_class',
        'filter_taxonomy' => 'category',
        'all_category_button' => 'All',
        'enable_search' => false,
        'label_search_button' => 'Search',
        'search_placeholder' => 'Search',
        'enable_order' => false,
        'label_newest_order' => 'Newest First',
        'label_old_order' => 'Old First',
        'filter_id' => '', // ID для зв'язку з постами
    );

    $a = shortcode_atts($default, $atts);

    $posts_type = $a['post_type'];
    $enable_clear_button = $a['enable_clear_button'];
    $filter_by_category = $a['filter_by_category'];
    $filter_type = $a['filter_type'];
    $filter_titles = $a['filter_titles'];
    $multiply_filter = $a['multiply_filter'];
    $filter_row_classes = $a['filter_row_classes'];
    $filter_item_classes = $a['filter_item_classes'];
    $filter_item_limit = $a['filter_item_limit'];
    $filter_expand_label = $a['filter_expand_label'];
    $filter_expand_class = $a['filter_expand_class'];
    $filter_taxonomy = $a['filter_taxonomy'];
    $all_category_button = $a['all_category_button'];
    $enable_search = $a['enable_search'];
    $label_search_button = $a['label_search_button'];
    $search_placeholder = $a['search_placeholder'];
    $enable_order = $a['enable_order'];
    $label_newest_order = $a['label_newest_order'];
    $label_old_order = $a['label_old_order'];
    $filter_id = $a['filter_id'];

    $load_more_variables = array(
        'enable_clear_button' => $enable_clear_button,
        'filter_by_category' => $filter_by_category,
        'filter_type' => $filter_type,
        'filter_titles' => $filter_titles,
        'multiply_filter' => $multiply_filter,
        'filter_row_classes' => $filter_row_classes,
        'filter_item_classes' => $filter_item_classes,
        'filter_item_limit' => $filter_item_limit,
        'filter_expand_label' => $filter_expand_label,
        'filter_expand_class' => $filter_expand_class,
        'filter_taxonomy' => $filter_taxonomy,
        'enable_search' => $enable_search,
        'all_category_button' => $all_category_button,
        'label_search_button' => $label_search_button,
        'search_placeholder' => $search_placeholder,
        'label_newest_order' => $label_newest_order,
        'label_old_order' => $label_old_order,
        'post_type' => $posts_type
    );

    $results = '';
    $filter_data_attr = $filter_id ? ' data-filter-id="' . esc_attr($filter_id) . '"' : '';

    $results .= '<div class="ajax_filters_wrapper"' . $filter_data_attr . '>';

    if ($filter_by_category === 'true' || $enable_search === 'true') {
        ob_start();
        require $plugin_all_ajax_dir . '/inc/filter/filter.php';
        $results .= ob_get_clean();
    }

    if ($enable_order === 'true') {
        ob_start();
        require $plugin_all_ajax_dir . '/inc/order/order.php';
        $results .= ob_get_clean();
    }

    $results .= '</div>';

    return $results;
}

add_shortcode('all_posts_ajax_filters', 'all_posts_ajax_filters_att');
