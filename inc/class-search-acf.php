<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Extends the default WordPress search to also match ACF field values,
 * by caching the list of registered ACF field names in a WordPress
 * option whenever a field group is saved.
 *
 * Earlier versions of this class kept that cache in a dedicated DB
 * table that got DROPped and recreated from scratch on every single
 * `acf/save_post` — expensive, momentarily breaks search for anyone
 * hitting it mid-rebuild, and total overkill for one row of data. A
 * plain option does the same job with a single autoloaded read.
 */
class WRALM_Search_ACF
{
    const OPTION_NAME = 'wralm_searchable_acf_fields';

    public function __construct()
    {
        add_action('acf/save_post', [$this, 'refresh_searchable_fields'], 20);
        add_filter('posts_search', [$this, 'extend_search_query'], 500, 2);
    }

    /**
     * Rebuilds the cached list of searchable ACF field names whenever an
     * ACF field group is saved. Requires the ACF plugin to be active.
     */
    public function refresh_searchable_fields()
    {
        if (!function_exists('acf_get_field_groups') || !function_exists('acf_get_fields')) {
            return;
        }

        $args = array(
            'public' => true,
            '_builtin' => false,
        );
        $post_types = get_post_types($args, 'names', 'and');

        $field_names = array();
        foreach ($post_types as $post_type) {
            $groups = acf_get_field_groups(array('post_type' => $post_type));
            foreach ($groups as $group) {
                $fields = acf_get_fields($group['key']);
                foreach ($fields as $field) {
                    $field_names[] = $field['name'];
                }
            }
        }

        update_option(self::OPTION_NAME, array_values(array_unique($field_names)), false);
    }

    /**
     * @return string[]
     */
    private function get_searchable_fields()
    {
        $fields = get_option(self::OPTION_NAME, array());

        return is_array($fields) ? $fields : array();
    }

    /**
     * Rebuilds the `WHERE` clause of a front-end search to also match
     * ACF meta values, comment content, and taxonomy term names.
     *
     * Credit for the base query structure: Vincent Zurczak,
     * https://vzurczak.wordpress.com/2013/06/15/extend-the-default-wordpress-search/
     */
    public function extend_search_query($where, $wp_query)
    {
        global $wpdb;

        if (empty($where)) {
            return $where;
        }

        $terms = $wp_query->query_vars['s'];
        $exploded = explode(' ', $terms);
        if ($exploded === false || count($exploded) === 0) {
            $exploded = array($terms);
        }

        $list_searcheable_acf = $this->get_searchable_fields();

        if (empty($list_searcheable_acf)) {
            return $where;
        }

        $where = '';

        foreach ($exploded as $tag) {
            $like = '%' . $wpdb->esc_like($tag) . '%';

            $meta_conditions = array();
            $meta_params = array();
            foreach ($list_searcheable_acf as $searcheable_acf) {
                $meta_conditions[] = '(meta_key LIKE %s AND meta_value LIKE %s)';
                $meta_params[] = '%' . $wpdb->esc_like($searcheable_acf) . '%';
                $meta_params[] = $like;
            }
            $meta_where = implode(' OR ', $meta_conditions);

            $where .= $wpdb->prepare(
                " AND (
                    ({$wpdb->posts}.post_title LIKE %s)
                    OR ({$wpdb->posts}.post_content LIKE %s)
                    OR EXISTS (
                        SELECT * FROM {$wpdb->postmeta}
                        WHERE post_id = {$wpdb->posts}.ID
                        AND ({$meta_where})
                    )
                    OR EXISTS (
                        SELECT * FROM {$wpdb->comments}
                        WHERE comment_post_ID = {$wpdb->posts}.ID
                        AND comment_content LIKE %s
                    )
                    OR EXISTS (
                        SELECT * FROM {$wpdb->terms}
                        INNER JOIN {$wpdb->term_taxonomy}
                            ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
                        INNER JOIN {$wpdb->term_relationships}
                            ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
                        WHERE (
                            taxonomy = 'post_tag'
                            OR taxonomy = 'category'
                            OR taxonomy = 'myCustomTax'
                        )
                        AND object_id = {$wpdb->posts}.ID
                        AND {$wpdb->terms}.name LIKE %s
                    )
                )",
                array_merge([$like, $like], $meta_params, [$like, $like])
            );
        }

        return $where;
    }
}
