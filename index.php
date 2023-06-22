<?php
/*
Plugin Name:  All posts ajax
Plugin URI:
Description:  All posts ajax load more, search and filter
Version:      0.9.8.5
Author: WebRevizor
Author URI:
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

function all_post_ajax_admin($hook_suffix)
{
	if ($hook_suffix != 'toplevel_page_all-posts-ajax') {
		return;
	}
	wp_enqueue_style('my_load_more_admin', plugin_dir_url(__FILE__) . 'dist/css/app.css', false, '1.0.0');
	wp_enqueue_script('my_load_more_admin', plugin_dir_url(__FILE__) . 'dist/js/js_admin.js', array('jquery'), false, true);
}

add_action('admin_enqueue_scripts', 'all_post_ajax_admin');

function all_posts_ajax()
{
	$args = array(
		'public' => true,
		'_builtin' => false
	);
	$output = 'names';
	$operator = 'and';
	$post_types = get_post_types($args, $output, $operator);


	$taxonomies = get_taxonomies($args, $output, $operator);
	?>
	<div class="flex flex-col gap-5">
		<h1 class="font-bold text-30 mt-5">
			All Post AJAX
		</h1>
		<div>
			<p class="text-base font-semibold inline-block p-5 bg-lightBlue rounded-md max-w-[80%]">
				Edit a post-card.php in "<?= get_stylesheet_directory() . '/all_posts_ajax/' ?>"
				<br/>
				or create new {post-name}-card.php
			</p>
		</div>
		<div class="flex items-start">
			<ul class="js-tabs-list mr-4 flex list-none flex-col flex-wrap pl-0" role="tablist">
				<li role="presentation" class="flex-grow text-center">
					<a href="#main"
					   class="active block px-7 py-3.5 text-xs font-medium transition duration-300 bg-white text-blue hover:text-blue uppercase [&.active]:bg-blue [&.active]:text-white focus:shadow-none focus:outline-none rounded-md">
						Main
					</a>
				</li>
				<li role="presentation" class="flex-grow text-center">
					<a href="#main-classes"
					   class="block px-7 py-3.5 text-xs font-medium transition duration-300 bg-white text-blue hover:text-blue uppercase [&.active]:bg-blue [&.active]:text-white focus:shadow-none focus:outline-none rounded-md">
						Main classes
					</a>
				</li>
				<li role="presentation" class="flex-grow text-center">
					<a href="#filters"
					   class="block px-7 py-3.5 text-xs font-medium transition duration-300 bg-white text-blue hover:text-blue uppercase [&.active]:bg-blue [&.active]:text-white focus:shadow-none focus:outline-none rounded-md">
						Filters
					</a>
				</li>
				<li role="presentation" class="flex-grow text-center">
					<a href="#search"
					   class="block px-7 py-3.5 text-xs font-medium transition duration-300 bg-white text-blue hover:text-blue uppercase [&.active]:bg-blue [&.active]:text-white focus:shadow-none focus:outline-none rounded-md">
						Search
					</a>
				</li>
				<li role="presentation" class="flex-grow text-center">
					<a href="#order"
					   class="block px-7 py-3.5 text-xs font-medium transition duration-300 bg-white text-blue hover:text-blue uppercase [&.active]:bg-blue [&.active]:text-white focus:shadow-none focus:outline-none rounded-md">
						Order
					</a>
				</li>
			</ul>
			<div class="bg-white rounded-md flex-1 p-5 max-w-[600px]">
				<div class="js-tabs-content"
				     id="main">
					<div class="flex flex-col gap-5 max-w-[300px]">
						<div class="flex flex-col gap-2.5">
							<label for="post_type_load_more" class="font-bold">Post Type</label>
							<select id="post_type_load_more">
								<option value="post">Post</option>
								<?php foreach ($post_types as $post_type) {
									echo '<option value="' . $post_type . '">' . ucfirst($post_type) . '</option>';
								} ?>
							</select>
						</div>
						<div class="flex flex-col gap-2.5">
							<label for="type_pagination" class="font-bold">Type Pagination</label>
							<select id="type_pagination">
								<option value="default">
									Only button
								</option>
								<option value="list">
									List
								</option>
								<option value="both">
									List + button
								</option>
								<option value="none">
									None
								</option>
							</select>
						</div>
						<div class="flex flex-col gap-2.5">
							<label for="count_post_per_page" class="font-bold">
								Count Post Per page
							</label>
							<input
									type="number"
									min="-1"
									value="10"
									class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
									id="count_post_per_page"
							/>
						</div>
						<div class="flex flex-col gap-2.5">
							<label for="load_more_label" class="font-bold">
								Load more label
							</label>
							<input type="text"
							       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
							       id="load_more_label"
							       placeholder="Load more"/>
						</div>
						<div class="flex flex-col gap-2.5">
							<label for="prev_text" class="font-bold">
								Previous label
							</label>
							<input type="text"
							       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
							       id="prev_text"
							       placeholder="Previous"/>
						</div>
						<div class="flex flex-col gap-2.5">
							<label for="next_text" class="font-bold">
								Next label
							</label>
							<input type="text"
							       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
							       id="next_text"
							       placeholder="Next"/>
						</div>
					</div>
				</div>
				<div class="js-tabs-content hidden"
				     id="main-classes">
					<div class="flex flex-col gap-5 max-w-[300px]">
						<div class="flex flex-col gap-2.5">
							<label for="row_classes" class="font-bold">
								Row classes
							</label>
							<input type="text"
							       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
							       id="row_classes"
							       placeholder="posts_row"/>
						</div>
						<div class="flex flex-col gap-2.5">
							<label for="load_more_classes" class="font-bold">
								Load more button classes
							</label>
							<input type="text"
							       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
							       id="load_more_classes"
							       placeholder="load_more_button"/>
						</div>
					</div>
				</div>
				<div class="js-tabs-content hidden"
				     id="filters">
					<div class="flex flex-col gap-5 max-w-[300px]">
						<div class="flex flex-col gap-2.5">
							<label for="filter_by_category" class="font-bold cursor-pointer">Filter by category</label>
							<input id="filter_by_category" class="!m-0" type="checkbox">
						</div>
						<div class="hiddenFilter hidden">
							<div class="flex flex-col gap-2.5">
								<label for="filter_taxonomy" class="font-bold">Filter by category</label>
								<select id="filter_taxonomy">
									<option value="category">Post category</option>
									<?php foreach ($taxonomies as $taxonomy) {
										echo '<option value="' . $taxonomy . '">' . ucfirst(str_replace(["_", "-"], " ", $taxonomy)) . '</option>';
									} ?>
								</select>
							</div>
						</div>
						<div class="hiddenFilter hidden">
							<div class="flex flex-col gap-2.5">
								<label for="filter_row_classes" class="font-bold">
									Filter row classes
								</label>
								<input type="text"
								       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
								       id="filter_row_classes"
								       placeholder="filter_row"/>
							</div>
						</div>
						<div class="hiddenFilter hidden">
							<div class="flex flex-col gap-2.5">
								<label for="filter_item_classes" class="font-bold">
									Filter item classes
								</label>
								<input type="text"
								       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
								       id="filter_item_classes"
								       placeholder="filter_item"/>

							</div>
						</div>
						<div class="hiddenFilter hidden">
							<div class="flex flex-col gap-2.5">
								<label for="all_category_button" class="font-bold">
									Label all category button
								</label>
								<input type="text"
								       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
								       id="all_category_button"
								       placeholder="All"/>
							</div>
						</div>
					</div>
				</div>
				<div class="js-tabs-content hidden"
				     id="search">
					<div class="flex flex-col gap-5 max-w-[300px]">
						<div class="flex flex-col gap-2.5">
							<label for="enable_search" class="font-bold cursor-pointer">Enable Search</label>
							<input id="enable_search" class="!m-0" type="checkbox">
						</div>
						<div class="hiddenSearch hidden">
							<div class="flex flex-col gap-2.5">
								<label for="label_search_button" class="font-bold">
									Label search button
								</label>
								<input type="text"
								       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
								       id="label_search_button"
								       placeholder="Search"/>
							</div>
						</div>
						<div class="hiddenSearch hidden">
							<div class="flex flex-col gap-2.5">
								<label for="search_placeholder" class="font-bold">
									Search Placeholder
								</label>
								<input type="text"
								       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
								       id="search_placeholder"
								       placeholder="Search"/>
							</div>
						</div>
					</div>
				</div>
				<div class="js-tabs-content hidden"
				     id="order">
					<div class="flex flex-col gap-5 max-w-[300px]">
						<div class="flex flex-col gap-2.5">
							<label for="enable_order" class="font-bold cursor-pointer">Enable Order</label>
							<input id="enable_order" class="!m-0" type="checkbox">
						</div>
						<div class="hiddenOrder hidden">
							<div class="flex flex-col gap-2.5">
								<label for="label_newest_order" class="font-bold">
									Label Newest
								</label>
								<input type="text"
								       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
								       id="label_newest_order"
								       placeholder="Newest First"/>
							</div>
						</div>
						<div class="hiddenOrder hidden">
							<div class="flex flex-col gap-2.5">
								<label for="label_old_order" class="font-bold">
									Label Old
								</label>
								<input type="text"
								       class="block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !text-md !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
								       id="label_old_order"
								       placeholder="Old First"/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="relative flex gap-5 items-center max-w-[500px]">
			<div class="relative flex-1" data-te-input-wrapper-init>
				<input class="js-click-to-copy-link cursor-pointer block min-h-[auto] w-full rounded !border border-[#aaa] !bg-white !px-3 !py-[0.64rem] !leading-[1.6] focus:!shadow-none outline-none transition-all duration-200 ease-linear "
				       id="results_shortcode"
				       type="text"
				       value="[all_posts_ajax]"
				       title="Copy"
				       readonly>
			</div>
			<button type="button" title="Copy"
			        class="js-click-to-copy-link flex items-center justify-center cursor-pointer !outline-none [&_svg]:fill-gray [&_svg]:transition [&_svg]:duration-300 hover:[&_svg]:fill-blue">
				<svg width="25" height="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
					<path d="M224 0c-35.3 0-64 28.7-64 64V288c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H224zM64 160c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H288c35.3 0 64-28.7 64-64V384H288v64H64V224h64V160H64z"/>
				</svg>
			</button>
		</div>
	</div>

<?php } ?>
<?php
function all_posts_ajax_att($atts)
{
	$default = array(
		'post_type' => 'post',
		'posts_per_page' => '10',
		'type_pagination' => 'default',
		'row_classes' => 'posts_row',
		'load_more_label' => 'Show more',
		'load_more_classes' => 'load_more_button',
		'filter_by_category' => false,
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
	$filter_by_category = $a['filter_by_category'];
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
	);
	$posts_result = '';
	$my_load_more_pagination = '';
	$results = '';

	$wp_query = new WP_Query($args);


	if ($filter_by_category === 'true') {
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

	$results .= '<div class="ajax_row_holder"><div class="ajax_row ' . $row_classes . '" data-pagination-type="' . $type_pagination . '" data-posts-per-page="' . $posts_per_page . '" data-posts-type="' . $posts_type . '" data-taxonomy="' . $filter_taxonomy . '" data-more-classes="' . $load_more_classes . '" data-more-label="' . $load_more_label . '" data-prev-text="' . $prev_text . '" data-next-text="' . $next_text . '">';
	$results .= $posts_result;
	$results .= '</div>';
	$results .= $my_load_more_pagination;
	$results .= '</div>';

	return $results;
}

add_shortcode('all_posts_ajax', 'all_posts_ajax_att');

/**
 * Custom pagination
 */
require_once dirname(__FILE__) . '/inc/custom_pagination.php';

/**
 * Load more
 */
require_once dirname(__FILE__) . '/inc/load_more.php';