<?php

/**
 * Hide from list
 */

add_action('add_meta_boxes', 'all_posts_ajax_add_custom_box');
function all_posts_ajax_add_custom_box()
{
	$args = array(
		'public' => true,
		'_builtin' => false
	);
	$output = 'names';
	$operator = 'and';
	$screens = get_post_types($args, $output, $operator);
	$screens[] = 'post';
	add_meta_box('myplugin_sectionid', 'All Posts Ajax', 'all_posts_ajax_meta_box_callback', $screens, 'side', 'high');
}

function all_posts_ajax_meta_box_callback($post, $meta)
{
	$screens = $meta['args'];

	wp_nonce_field(plugin_basename(__FILE__), 'myplugin_noncename');

	$value = get_post_meta($post->ID, 'all_posts_ajax_hide', true);
	?>


	<label for="all_posts_ajax_hide">
		<input type="checkbox" id="all_posts_ajax_hide"
		       name="all_posts_ajax_hide" <?= $value ? 'checked' : '' ?>/>
		Hide from list
	</label>
	<?php
}

add_action('save_post', 'all_posts_ajax_save_postdata');
function all_posts_ajax_save_postdata($post_id)
{
    // Перевірка на існування ключа 'myplugin_noncename'
    if (!isset($_POST['myplugin_noncename']) || !wp_verify_nonce($_POST['myplugin_noncename'], plugin_basename(__FILE__)))
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    $my_data = isset($_POST['all_posts_ajax_hide']) && $_POST['all_posts_ajax_hide'] ? '1' : '0';

    update_post_meta($post_id, 'all_posts_ajax_hide', $my_data);
}