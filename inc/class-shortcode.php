<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers [all_posts_ajax] and [all_posts_ajax_filters].
 */
class WRALM_Shortcode
{
    public function __construct()
    {
        add_shortcode('all_posts_ajax', [$this, 'render_posts']);
        add_shortcode('all_posts_ajax_filters', [$this, 'render_filters']);
    }

    public function render_posts($atts)
    {
        $default = array(
            'post_type' => 'post',
            'posts_per_page' => '10',
            'type_pagination' => 'default',
            'row_classes' => 'posts_row',
            'load_more_label' => __('Show more', 'wr-ajax-load-more-and-filters'),
            'load_more_classes' => 'load_more_button',
            'prev_text' => __('Previous', 'wr-ajax-load-more-and-filters'),
            'next_text' => __('Next', 'wr-ajax-load-more-and-filters'),
            'filter_id' => '',
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
                    'compare' => '!=',
                ),
                array(
                    'key' => 'all_posts_ajax_hide',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        );

        $posts_result = '';
        $my_load_more_pagination = '';

        $wp_query = new WP_Query($args);
        $pagenum_link = html_entity_decode(get_pagenum_link());
        $url_parts = explode('?', $pagenum_link);

        $pagenum_link = trailingslashit($url_parts[0]) . '%_%';

        global $wp_rewrite;
        $format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
        $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

        if ($wp_query->have_posts()) :
            ob_start();
            while ($wp_query->have_posts()) :
                $wp_query->the_post();
                get_template_part('all_posts_ajax/' . $posts_type . '-card');
            endwhile;
            $posts_result = ob_get_clean();

            if ($wp_query->max_num_pages > 1) :
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
                $my_load_more_pagination = WRALM_Pagination::links($args_pagination);
            endif;
            wp_reset_postdata();
        endif;

        $filter_data_attr = $filter_id ? ' data-filter-id="' . esc_attr($filter_id) . '"' : '';

        $results = '<div class="ajax_row_holder" data-init-page="' . esc_attr(max(1, get_query_var('paged'))) . '">';
        $results .= '<div class="ajax_row ' . esc_attr($row_classes) . '"' . $filter_data_attr
            . ' data-pagination-type="' . esc_attr($type_pagination) . '"'
            . ' data-posts-per-page="' . esc_attr($posts_per_page) . '"'
            . ' data-posts-type="' . esc_attr($posts_type) . '"'
            . ' data-more-classes="' . esc_attr($load_more_classes) . '"'
            . ' data-more-label="' . esc_attr($load_more_label) . '"'
            . ' data-prev-text="' . esc_attr($prev_text) . '"'
            . ' data-next-text="' . esc_attr($next_text) . '">';
        $results .= $posts_result;
        $results .= '</div>';
        $results .= $my_load_more_pagination;
        $results .= '</div>';

        return $results;
    }

    public function render_filters($atts)
    {
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
            'filter_expand_label' => __('See all', 'wr-ajax-load-more-and-filters'),
            'filter_expand_class' => 'filter_expand_class',
            'filter_taxonomy' => 'category',
            'all_category_button' => __('All', 'wr-ajax-load-more-and-filters'),
            'enable_search' => false,
            'label_search_button' => __('Search', 'wr-ajax-load-more-and-filters'),
            'search_placeholder' => __('Search', 'wr-ajax-load-more-and-filters'),
            'enable_order' => false,
            'label_newest_order' => __('Newest First', 'wr-ajax-load-more-and-filters'),
            'label_old_order' => __('Old First', 'wr-ajax-load-more-and-filters'),
            'filter_id' => '',
        );

        $a = shortcode_atts($default, $atts);

        $load_more_variables = array(
            'enable_clear_button' => $a['enable_clear_button'],
            'filter_by_category' => $a['filter_by_category'],
            'filter_type' => $a['filter_type'],
            'filter_titles' => $a['filter_titles'],
            'multiply_filter' => $a['multiply_filter'],
            'filter_row_classes' => $a['filter_row_classes'],
            'filter_item_classes' => $a['filter_item_classes'],
            'filter_item_limit' => $a['filter_item_limit'],
            'filter_expand_label' => $a['filter_expand_label'],
            'filter_expand_class' => $a['filter_expand_class'],
            'filter_taxonomy' => $a['filter_taxonomy'],
            'enable_search' => $a['enable_search'],
            'all_category_button' => $a['all_category_button'],
            'label_search_button' => $a['label_search_button'],
            'search_placeholder' => $a['search_placeholder'],
            'label_newest_order' => $a['label_newest_order'],
            'label_old_order' => $a['label_old_order'],
            'post_type' => $a['post_type'],
        );

        $filter_id = $a['filter_id'];
        $filter_data_attr = $filter_id ? ' data-filter-id="' . esc_attr($filter_id) . '"' : '';

        $results = '<div class="ajax_filters_wrapper"' . $filter_data_attr . '>';

        if ($a['filter_by_category'] === 'true' || $a['enable_search'] === 'true') {
            ob_start();
            require WRALM_PATH . 'inc/views/filter.php';
            $results .= ob_get_clean();
        }

        if ($a['enable_order'] === 'true') {
            ob_start();
            require WRALM_PATH . 'inc/views/order.php';
            $results .= ob_get_clean();
        }

        $results .= '</div>';

        return $results;
    }
}
