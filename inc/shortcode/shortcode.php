<?php

/**
 * Shortcode
 */

function all_posts_ajax_att($atts)
{
	$default = array(
		'post_type' => 'post',
		'posts_per_page' => '10',
		'type_pagination' => 'default',
		'row_classes' => 'posts_row',
		'load_more_label' => 'Show more',
		'load_more_classes' => 'load_more_button',
		'enable_clear_button' => false,
		'filter_by_category' => false,
		'multiply_filter_button' => 'false',
		'filter_type' => 'button',
		'filter_row_classes' => 'filter_row',
		'filter_item_classes' => 'filter_item',
		'filter_taxonomy' => 'category',
		'all_category_button' => 'All',
		'enable_search' => false,
		'label_search_button' => 'Search',
		'search_placeholder' => 'Search',
		'enable_order' => false,
		'label_newest_order' => 'Newest First',
		'label_old_order' => 'Old First',
		'prev_text' => 'Previous',
		'next_text' => 'Next',
	);


	global $load_more_variables;

	$a = shortcode_atts($default, $atts);
	$posts_type = $a['post_type'];
	$posts_per_page = $a['posts_per_page'];
	$type_pagination = $a['type_pagination'];
	$row_classes = $a['row_classes'];
	$load_more_label = $a['load_more_label'];
	$load_more_classes = $a['load_more_classes'];
	$enable_clear_button = $a['enable_clear_button'];
	$filter_by_category = $a['filter_by_category'];
	$filter_type = $a['filter_type'];
	$multiply_filter_button = $a['multiply_filter_button'];
	$filter_row_classes = $a['filter_row_classes'];
	$filter_item_classes = $a['filter_item_classes'];
	$filter_taxonomy = $a['filter_taxonomy'];
	$all_category_button = $a['all_category_button'];
	$enable_search = $a['enable_search'];
	$label_search_button = $a['label_search_button'];
	$search_placeholder = $a['search_placeholder'];
	$enable_order = $a['enable_order'];
	$label_newest_order = $a['label_newest_order'];
	$label_old_order = $a['label_old_order'];
	$prev_text = $a['prev_text'];
	$next_text = $a['next_text'];

	$load_more_variables = array(
		'load_more_label' => $load_more_label,
		'enable_clear_button' => $enable_clear_button,
		'filter_by_category' => $filter_by_category,
		'filter_type' => $filter_type,
		'multiply_filter_button' => $multiply_filter_button,
		'filter_row_classes' => $filter_row_classes,
		'filter_item_classes' => $filter_item_classes,
		'filter_taxonomy' => $filter_taxonomy,
		'enable_search' => $enable_search,
		'all_category_button' => $all_category_button,
		'label_search_button' => $label_search_button,
		'search_placeholder' => $search_placeholder,
		'label_newest_order' => $label_newest_order,
		'label_old_order' => $label_old_order,
		'post_type' => $posts_type
	);


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


	if ($filter_by_category === 'true' || $enable_search === 'true') {
		require_once dirname(__FILE__) . '/inc/filter/filter.php';
	}

	if ($enable_order === 'true') {
		require_once dirname(__FILE__) . '/inc/order/order.php';
	}

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
				'format' => 'page/%#%',
				'base' => get_pagenum_link(1) . '%_%',
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

	$results .= '<div class="ajax_row_holder"><div class="ajax_row ' . $row_classes . '" data-pagination-type="' . $type_pagination . '" data-posts-per-page="' . $posts_per_page . '" data-posts-type="' . $posts_type . '" data-more-classes="' . $load_more_classes . '" data-more-label="' . $load_more_label . '" data-prev-text="' . $prev_text . '" data-next-text="' . $next_text . '">';
	$results .= $posts_result;
	$results .= '</div>';
	$results .= $my_load_more_pagination;
	$results .= '</div>';

	return $results;
}

add_shortcode('all_posts_ajax', 'all_posts_ajax_att');