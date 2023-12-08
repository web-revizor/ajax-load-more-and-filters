# AJAX load more and filters

## WordPress plugin

Easy load more, filter and searching

[Download](https://github.com/web-revizor/ajax-load-more-and-filters/releases)

### Pagination:

- List
- Button
- Both
- None

### Default Shortcode

````
[all_posts_ajax post_type="post" posts_per_page="10" type_pagination="default"]
````

### Shortcode Parameters (generate in admin panel)

- post_type: post type name
- posts_per_page: number, can be "-1" for infinity posts on page
- type_pagination: list/both/default/none
- row_classes: string
- load_more_label: string
- load_more_classes: string
- filter_by_category: boolean
- filter_row_classes: string
- filter_item_classes: string
- filter_taxonomy: comma separated string with taxonomy name
- multiply_filter_button: boolean
- enable_clear_button: boolean
- filter_type: button/select
- filter_titles: boolean
- all_category_button: string
- enable_search: boolean
- label_search_button: string
- search_placeholder: string
- enable_order: boolean
- label_newest_order: string
- label_old_order: string
- prev_text: string
- next_text: string

## JQuery events

- AjaxPaginationDone
- AjaxFilterDone

### Example

````
$(document).on('AjaxFilterDone', function() {
    //do something
});

$(document).on('AjaxFilterDone', function() {
    //do something
});
````