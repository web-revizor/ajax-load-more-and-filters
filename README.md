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

- post_type
- posts_per_page
- type_pagination
- row_classes
- load_more_label
- load_more_classes
- filter_by_category
- filter_row_classes
- filter_item_classes
- filter_taxonomy
- all_category_button
- enable_search
- label_search_button
- search_placeholder
- enable_order
- label_newest_order
- label_old_order
- prev_text
- next_text

posts_per_page - can be "-1" for infinity posts on page

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