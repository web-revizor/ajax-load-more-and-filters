<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adds a "Hide from list" checkbox meta box to every public post type,
 * used to exclude a post from the [all_posts_ajax] query.
 */
class WRALM_Hide_Meta_Box
{
    const META_KEY = 'all_posts_ajax_hide';
    const NONCE_ACTION = 'apa_hide_meta_box';
    const NONCE_NAME = 'apa_hide_meta_box_nonce';

    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box']);
    }

    public function add_meta_box()
    {
        $args = array(
            'public' => true,
            '_builtin' => false,
        );
        $screens = get_post_types($args, 'names', 'and');
        $screens[] = 'post';

        add_meta_box(
            'myplugin_sectionid',
            __('All Posts Ajax', 'wr-ajax-load-more-and-filters'),
            [$this, 'render_meta_box'],
            $screens,
            'side',
            'high'
        );
    }

    public function render_meta_box($post)
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $value = get_post_meta($post->ID, self::META_KEY, true);
        ?>
        <label for="all_posts_ajax_hide">
            <input type="checkbox" id="all_posts_ajax_hide"
                   name="all_posts_ajax_hide" <?= $value ? 'checked' : '' ?>/>
            <?php esc_html_e('Hide from list', 'wr-ajax-load-more-and-filters'); ?>
        </label>
        <?php
    }

    public function save_meta_box($post_id)
    {
        if (!isset($_POST[self::NONCE_NAME])) {
            return;
        }

        if (!wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $value = isset($_POST['all_posts_ajax_hide']) ? '1' : '0';
        update_post_meta($post_id, self::META_KEY, $value);
    }
}
