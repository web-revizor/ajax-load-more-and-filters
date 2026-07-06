<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Front-end script enqueue + the loadmore/filter AJAX endpoint.
 */
class WRALM_Load_More
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_loadmore', [$this, 'handle_ajax']);
        add_action('wp_ajax_nopriv_loadmore', [$this, 'handle_ajax']);
    }

    public function enqueue_scripts()
    {
        global $wp_query;

        wp_enqueue_script('jquery');

        wp_register_script(
            'my_loadmore',
            WRALM_URL . 'dist/js/load_more_and_filter.js',
            ['jquery'],
            WRALM_VERSION,
            true
        );

        wp_localize_script('my_loadmore', 'loadmore_params', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'posts' => $wp_query->query_vars,
            'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
            'max_page' => $wp_query->max_num_pages,
        ));

        wp_enqueue_script('my_loadmore');
    }

    public function handle_ajax()
    {
        $wp_query_post_type = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : 'post';
        $wp_query_posts_per_page = isset($_POST['posts_per_page']) ? (int)$_POST['posts_per_page'] : 10;
        $wp_query_pagination_type = isset($_POST['pagination_type']) ? sanitize_text_field($_POST['pagination_type']) : 'default';
        $categories = isset($_POST['category']) ? (array)$_POST['category'] : array();
        $load_more_classes = isset($_POST['more_classes']) ? sanitize_text_field($_POST['more_classes']) : '';
        $load_more_label = isset($_POST['more_label']) ? sanitize_text_field($_POST['more_label']) : '';
        $prev_text = isset($_POST['prev_text']) ? sanitize_text_field($_POST['prev_text']) : '';
        $next_text = isset($_POST['next_text']) ? sanitize_text_field($_POST['next_text']) : '';
        $base_url = isset($_POST['base_url']) ? esc_url_raw($_POST['base_url']) : home_url('/');
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $category_taxonomy = isset($_POST['category_taxonomy']) ? sanitize_text_field($_POST['category_taxonomy']) : '';
        $tax_query = array('relation' => 'AND');
        $search = '';
        $order = 'DESC';

        if (!empty($_POST['search'])) {
            $search = sanitize_text_field($_POST['search']);
        }

        if (!empty($_POST['order'])) {
            $order = sanitize_text_field($_POST['order']);
        }

        if ($category_id && !empty($category_taxonomy)) {
            $tax_query[] = array(
                'taxonomy' => sanitize_key($category_taxonomy),
                'field' => 'term_id',
                'terms' => array((int)$category_id),
            );
        }

        if ($categories) {
            foreach ($categories as $category => $value) :
                $tax_query[] = array(
                    'taxonomy' => sanitize_key($category),
                    'field' => 'slug',
                    'terms' => array_map('sanitize_text_field', (array)$value),
                );
            endforeach;
        }

        $args = array(
            'post_type' => $wp_query_post_type,
            'posts_per_page' => $wp_query_posts_per_page,
            'post_status' => 'publish',
            'tax_query' => $tax_query,
            'order' => $order,
            'paged' => isset($_POST['page']) ? absint($_POST['page']) : 1,
            's' => $search,
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

        $query = new WP_Query($args);

        ob_start();
        if ($query->have_posts()) :
            while ($query->have_posts()) :
                $query->the_post();
                get_template_part('all_posts_ajax/' . $wp_query_post_type . '-card');
            endwhile;
            wp_reset_postdata();
        endif;
        $html = ob_get_clean();

        global $wp_rewrite;

        if ($wp_rewrite->using_permalinks()) {
            $pagination_base = $wp_rewrite->pagination_base; // usually 'page'
            $base_url = preg_replace('#/' . $pagination_base . '/\d+/?$#', '/', $base_url);
        }

        $pagenum_link = trailingslashit($base_url) . '%_%';

        $format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
        $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

        $args_pagination = array(
            'base' => $pagenum_link,
            'format' => $format,
            'total' => $query->max_num_pages,
            'current' => $args['paged'],
            'type' => $wp_query_pagination_type,
            'load_more_classes' => $load_more_classes,
            'load_more_label' => $load_more_label,
            'prev_text' => $prev_text,
            'next_text' => $next_text,
        );

        $pagination = WRALM_Pagination::links($args_pagination);

        wp_send_json(array(
            'html' => $html,
            'pagination' => $pagination,
            'max_page' => $query->max_num_pages,
            'base_url' => $base_url,
        ));
    }
}
