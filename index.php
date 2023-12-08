<?php
/*
Plugin Name:  All posts ajax
Plugin URI:
Description:  All posts ajax load more, search and filter
Version:      1.2.0
Author: WebRevizor
Author URI: https://github.com/web-revizor/ajax-load-more-and-filters
License:      GPL2
License URI:
Text Domain:  all-post-ajax-load-more
*/

add_action('admin_menu', 'all_posts_ajax_setup_menu');

function all_posts_ajax_setup_menu()
{
	add_menu_page('All Posts AJAX', 'All Posts AJAX', 'manage_options', 'all-posts-ajax', 'all_posts_ajax');
}


if (!is_dir(get_stylesheet_directory() . '/all_posts_ajax')) {
	wp_mkdir_p(get_stylesheet_directory() . '/all_posts_ajax');
}

if (!is_file(get_stylesheet_directory() . '/all_posts_ajax/post-card.php')) {
	fopen(get_stylesheet_directory() . '/all_posts_ajax/post-card.php', 'w');
}
$plugin_all_ajax_dir = WP_PLUGIN_DIR . '/ajax-load-more-and-filters';
global $plugin_all_ajax_dir;
/**
 * Shortcode
 */
require_once dirname(__FILE__) . '/inc/shortcode/shortcode.php';

/**
 * Styles and scripts
 */
require_once dirname(__FILE__) . '/inc/styles_scripts/styles_scripts.php';

/**
 * Admin layout
 */
require_once dirname(__FILE__) . '/inc/admin/admin.php';

/**
 * Hide from list
 */
require_once dirname(__FILE__) . '/inc/hide/hide.php';

/**
 * Custom pagination
 */
require_once dirname(__FILE__) . '/inc/custom_pagination.php';

/**
 * Load more
 */
require_once dirname(__FILE__) . '/inc/load_more.php';

/**
 * Search by ACF
 */
require_once dirname(__FILE__) . '/inc/search_by_acf/search_by_acf.php';
