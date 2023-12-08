<?php

/**
 * Styles and scripts
 */

function all_post_ajax_admin($hook_suffix)
{
	if ($hook_suffix != 'toplevel_page_all-posts-ajax') {
		return;
	}
	wp_enqueue_style('my_load_more_admin', plugin_dir_url(__DIR__) . '../dist/css/app.css', false, '1.0.0');
	wp_enqueue_script('my_load_more_admin', plugin_dir_url(__DIR__) . '../dist/js/js_admin.js', array('jquery'), false, true);
}

add_action('admin_enqueue_scripts', 'all_post_ajax_admin');