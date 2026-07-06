<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('wralm_searchable_acf_fields');

// Note: the `all_posts_ajax_hide` post meta value is left in place, since
// it lives on user content (posts) rather than plugin configuration.
