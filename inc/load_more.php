<?php

/**
 * Load more posts
 *
 */

function my_load_more_scripts()
{

	global $wp_query;

	wp_enqueue_script('jquery');

	wp_register_script('my_loadmore', plugin_dir_url(__FILE__) . '../dist/js/load_more_and_filter.js', array('jquery'));

	wp_localize_script('my_loadmore', 'loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
		'posts' => $wp_query->query_vars,
		'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
		'max_page' => $wp_query->max_num_pages
	));

	wp_enqueue_script('my_loadmore');
}

add_action('wp_enqueue_scripts', 'my_load_more_scripts');


function loadmore_ajax_handler()
{
	$wp_query_post_type = $_POST['post_type'];
	$wp_query_posts_per_page = $_POST['posts_per_page'];
	$wp_query_pagination_type = $_POST['pagination_type'];
	$categories = $_POST['category'];
	$load_more_classes = $_POST['more_classes'];
	$load_more_label = $_POST['more_label'];
	$prev_text = $_POST['prev_text'];
	$next_text = $_POST['next_text'];
	$tax_query = array('relation' => 'AND');
	$search = '';
	$order = 'DESC';

	if ($_POST['search']) {
		$search = $_POST['search'];
	}

	if ($_POST['order']) {
		$order = $_POST['order'];
	}

	if ($categories != '') {
		foreach ($categories as $category => $value) :
			$array = array(
				'taxonomy' => $category,
				'field' => 'slug',
				'terms' => $value,
			);
			$tax_query[] = $array;
		endforeach;
	}

	$args = array(
		'post_type' => $wp_query_post_type,
		'posts_per_page' => $wp_query_posts_per_page,
		'post_status' => 'publish',
		'tax_query' => $tax_query,
		'order' => $order,
		'paged' => absint($_POST['page']),
		's' => $search
	);

	$wp_query = new WP_Query($args);

	ob_start();
	if ($wp_query->have_posts()):
		while ($wp_query->have_posts()):$wp_query->the_post();
			get_template_part('all_posts_ajax/' . $wp_query_post_type . '-card');
		endwhile;
	endif;

	$html = ob_get_contents();

	ob_end_clean();

	ob_start();

	$args_pagination = array(
		'format' => 'page/%#%',
		'base' => get_post_type_archive_link($wp_query_post_type) . '%_%',
		'total' => $wp_query->max_num_pages,
		'current' => $args['paged'],
		'type' => $wp_query_pagination_type,
		'load_more_classes' => $load_more_classes,
		'load_more_label' => $load_more_label,
		'prev_text' => $prev_text,
		'next_text' => $next_text,
	);

	echo paginate_links_custom($args_pagination);

	$pagination = ob_get_contents();

	ob_end_clean();

	$response = [
		'html' => $html,
		'pagination' => $pagination,
		'max_page' => $wp_query->max_num_pages,
	];

	wp_send_json($response);
}


add_action('wp_ajax_loadmore', 'loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmore', 'loadmore_ajax_handler');