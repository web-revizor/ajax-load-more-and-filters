<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Extends the default WordPress search to also match ACF field values,
 * by caching the list of registered ACF field names in a WordPress option.
 */
class WRALM_Search_ACF
{
    const OPTION_NAME = 'wralm_searchable_acf_fields';

    public function __construct()
    {
        // Hook into ACF save post
        add_action('acf/save_post', [$this, 'refresh_searchable_fields'], 20);
        add_filter('posts_search', [$this, 'extend_search_query'], 500, 2);
    }

    /**
     * Rebuilds the cached list of searchable ACF field names.
     * FIX: Only triggers when an ACF Field Group is saved, preventing
     * massive memory/CPU spikes on regular post saves.
     */
    public function refresh_searchable_fields($post_id)
    {
        // Prevent execution on regular posts/pages
        if (get_post_type($post_id) !== 'acf-field-group') {
            return;
        }

        if (!function_exists('acf_get_field_groups') || !function_exists('acf_get_fields')) {
            return;
        }

        $post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
        $field_names = [];

        foreach ($post_types as $post_type) {
            $groups = acf_get_field_groups(['post_type' => $post_type]);
            if (empty($groups)) continue;

            foreach ($groups as $group) {
                $fields = acf_get_fields($group['key']);
                if (empty($fields)) continue;

                foreach ($fields as $field) {
                    if (!empty($field['name'])) {
                        $field_names[] = $field['name'];
                    }
                }
            }
        }

        // FIX: Enable autoload (3rd param = true) to avoid extra DB queries on every search.
        update_option(self::OPTION_NAME, array_values(array_unique($field_names)), true);
    }

    /**
     * Rebuilds the `WHERE` clause of a front-end search dynamically.
     */
    public function extend_search_query($where, $wp_query)
    {
        global $wpdb;

        if (empty($where) || empty($wp_query->query_vars['s'])) {
            return $where;
        }

        $search_term = $wp_query->query_vars['s'];
        // Filter out empty strings caused by multiple spaces
        $terms = array_filter(explode(' ', $search_term));
        if (empty($terms)) {
            return $where;
        }

        // FIX: Dynamically fetch all public taxonomies instead of hardcoding them
        $taxonomies = get_taxonomies(['public' => true], 'names');
        if (empty($taxonomies)) {
            return $where;
        }
        $tax_placeholders = implode(',', array_fill(0, count($taxonomies), '%s'));

        $acf_fields = $this->get_searchable_fields();
        $has_acf = !empty($acf_fields);

        // FIX: Optimize Meta Query.
        // Instead of generating N "OR meta_key LIKE" conditions, use a single "IN" clause.
        $meta_key_sql = '';
        if ($has_acf) {
            $acf_placeholders = implode(',', array_fill(0, count($acf_fields), '%s'));
            $meta_key_sql = "meta_key IN ({$acf_placeholders}) AND ";
        }

        // Base SQL structure for a single search term
        $base_sql = "
            ({$wpdb->posts}.post_title LIKE %s)
            OR ({$wpdb->posts}.post_content LIKE %s)
            OR EXISTS (
                SELECT 1 FROM {$wpdb->postmeta}
                WHERE post_id = {$wpdb->posts}.ID
                AND {$meta_key_sql} meta_value LIKE %s
            )
            OR EXISTS (
                SELECT 1 FROM {$wpdb->comments}
                WHERE comment_post_ID = {$wpdb->posts}.ID
                AND comment_content LIKE %s
            )
            OR EXISTS (
                SELECT 1 FROM {$wpdb->term_relationships}
                INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
                INNER JOIN {$wpdb->terms} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                WHERE {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
                AND {$wpdb->term_taxonomy}.taxonomy IN ({$tax_placeholders})
                AND {$wpdb->terms}.name LIKE %s
            )
        ";

        $new_where = '';

        foreach ($terms as $term) {
            $like = '%' . $wpdb->esc_like($term) . '%';

            // Build parameters array sequentially to avoid memory overhead of array_merge in loops
            $params = [$like, $like]; // post_title, post_content

            if ($has_acf) {
                $params = array_merge($params, $acf_fields); // meta_key IN (...)
            }
            $params[] = $like; // meta_value
            $params[] = $like; // comment_content

            $params = array_merge($params, $taxonomies); // taxonomy IN (...)
            $params[] = $like; // term name

            $new_where .= $wpdb->prepare(" AND ({$base_sql})", $params);
        }

        return $new_where;
    }

    /**
     * @return string[]
     */
    private function get_searchable_fields()
    {
        $fields = get_option(self::OPTION_NAME, []);
        return is_array($fields) ? $fields : [];
    }
}
