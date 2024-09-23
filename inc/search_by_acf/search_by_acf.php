<?php

/**
 * Search by ACF
 */

function loadmore_search_acf_table()
{

    global $wpdb;

    $table_name = $wpdb->prefix . "ajax_load_more_and_filters";

    $charset_collate = $wpdb->get_charset_collate();
    $dataBaseName = DB_NAME;
    $sql = "DROP TABLE `$dataBaseName`.`$table_name`";

    $wpdb->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      ACF_FIELDS mediumtext NOT NULL,
      PRIMARY KEY id (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);


    $args = array(
        'public' => true,
        '_builtin' => false
    );
    $output = 'names';
    $operator = 'and';
    $post_types = get_post_types($args, $output, $operator);
    $array = array();
    foreach ($post_types as $post_type) {
        $groups = acf_get_field_groups(array('post_type' => $post_type));
        foreach ($groups as $item) {
            $groups = acf_get_fields($item['key']);
            print_r($item['name']);
            foreach ($groups as $item) {
                $array[] = $item['name'];
            }
        }
    }
    $array = array_unique($array);
    $array = implode(', ', $array);

    $table_name = $wpdb->prefix . 'ajax_load_more_and_filters';

    $wpdb_quantity = json_encode($array);

    $query = "INSERT INTO $table_name (id, ACF_FIELDS) VALUES (%s, %s)";

    $prepare_query = $wpdb->prepare($query, '1', $wpdb_quantity);

    $wpdb->query($prepare_query);
}

add_action('acf/save_post', 'loadmore_search_acf_table');


/**
 * [list_searcheable_acf list all the custom fields we want to include in our search query]
 * @return [array] [list of custom fields]
 */


/**
 * [advanced_custom_search search that encompasses ACF/advanced custom fields and taxonomies and split expression before request]
 * @param  [query-part/string]      $where    [the initial "where" part of the search query]
 * @param  [object]                 $wp_query []
 * @return [query-part/string]      $where    [the "where" part of the search query as we customized]
 * see https://vzurczak.wordpress.com/2013/06/15/extend-the-default-wordpress-search/
 * credits to Vincent Zurczak for the base query structure/spliting tags section
 */
function advanced_custom_search($where, $wp_query)
{
    global $wpdb;

    if (empty($where) || is_admin())
        return $where;

    // get search expression
    $terms = $wp_query->query_vars['s'];

    // explode search expression to get search terms
    $exploded = explode(' ', $terms);
    if ($exploded === FALSE || count($exploded) == 0)
        $exploded = array(0 => $terms);

    // reset search in order to rebuilt it as we whish
    $where = '';
    $result = $wpdb->get_results("SELECT * FROM `$wpdb->prefix`ajax_load_more_and_filters");
    $result = json_decode(json_encode($result[0]->ACF_FIELDS), true);
    $result = explode(', ', $result);
    // get searcheable_acf, a list of advanced custom fields you want to search content in
    $list_searcheable_acf = $result;
    foreach ($exploded as $tag) :
        $where .= " 
          AND (
            (wp_posts.post_title LIKE '%$tag%')
            OR (wp_posts.post_content LIKE '%$tag%')
            OR EXISTS (
              SELECT * FROM wp_postmeta
                  WHERE post_id = wp_posts.ID
                    AND (";
        foreach ($list_searcheable_acf as $searcheable_acf) :
            if ($searcheable_acf == $list_searcheable_acf[0]):
                $where .= " (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
            else :
                $where .= " OR (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
            endif;
        endforeach;
        $where .= ")
            )
            OR EXISTS (
              SELECT * FROM wp_comments
              WHERE comment_post_ID = wp_posts.ID
                AND comment_content LIKE '%$tag%'
            )
            OR EXISTS (
              SELECT * FROM wp_terms
              INNER JOIN wp_term_taxonomy
                ON wp_term_taxonomy.term_id = wp_terms.term_id
              INNER JOIN wp_term_relationships
                ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
              WHERE (
                taxonomy = 'post_tag'
                    OR taxonomy = 'category'                
                    OR taxonomy = 'myCustomTax'
                )
                AND object_id = wp_posts.ID
                AND wp_terms.name LIKE '%$tag%'
            )
        )";
    endforeach;
    return $where;
}

add_filter('posts_search', 'advanced_custom_search', 500, 2);