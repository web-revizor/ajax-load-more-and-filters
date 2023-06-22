jQuery(function ($) {

    var row_items = $('.ajax_row'),
        ajax_row_holder = $('.ajax_row_holder'),
        posts_per_page = '',
        posts_type = '',
        pagination_type = '',
        taxonomy_type = '',
        more_classes = '',
        more_label = '',
        prev_text = '',
        next_text = '',
        category = '',
        search = '',
        order = '',
        this_load_more = '',
        data = [];

    function posts_per_page_fn() {
        posts_per_page = row_items.attr('data-posts-per-page');
    }

    function posts_type_fn() {
        posts_type = row_items.attr('data-posts-type');
    }

    function pagination_type_fn() {
        pagination_type = row_items.attr('data-pagination-type');
    }

    function taxonomy_type_fn() {
        taxonomy_type = row_items.attr('data-taxonomy');
    }

    function more_classes_fn() {
        more_classes = row_items.attr('data-more-classes');
    }

    function more_label_fn() {
        more_label = row_items.attr('data-more-label');
    }

    function prev_text_fn() {
        prev_text = row_items.attr('data-prev-text');
    }

    function next_text_fn() {
        next_text = row_items.attr('data-next-text');
    }

    function all_param() {
        posts_per_page_fn();
        posts_type_fn();
        pagination_type_fn();
        taxonomy_type_fn();
        more_classes_fn();
        more_label_fn();
        prev_text_fn();
        next_text_fn();

        category = $('.js-category-filter.active').attr('data-slug');
        search = $('#all-post-search').val();
        order = $('#js-post-order').val();
        this_load_more = $('.load_more_holder');

        data = {
            'action': 'loadmore',
            'query': loadmore_params.posts,
            'page': loadmore_params.current_page,
            'posts_per_page': posts_per_page,
            'post_type': posts_type,
            'pagination_type': pagination_type,
            'category': category,
            'taxonomy_type': taxonomy_type,
            'search': search,
            'order': order,
            'more_classes': more_classes,
            'more_label': more_label,
            'prev_text': prev_text,
            'next_text': next_text,
        };
    }

    $(document).on('click', '#pagination_holder .load_page', function (e) {
        e.preventDefault();
        loadmore_params.current_page = $(this).data('slug');

        all_param();

        var clearRow = true;

        if ($(this).hasClass('load_more')) {
            clearRow = false;
        }

        $.ajax({
            url: loadmore_params.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend: function (xhr) {
                ajax_row_holder.css("opacity", "0.5");
            },
            success: function (data) {
                if (data) {
                    this_load_more.remove();
                    if (clearRow === true) {
                        row_items.empty();
                        $('html,body').stop().animate({
                            scrollTop: row_items.offset().top
                        }, 300,);
                    }
                    row_items.append(data.html);
                    ajax_row_holder.append(data.pagination);
                    ajax_row_holder.css("opacity", "1");
                }
            }
        }).done(function () {
            $(document).trigger('AjaxPaginationDone');
        });
    });

    $('.js-category-filter').on('click', function () {
        if (!$(this).hasClass('active')) {
            $('.js-category-filter').removeClass('active');
            $(this).addClass('active')
        } else {
            $('.js-category-filter').removeClass('active');
        }
    })

    $('#js-post-order').on('change', function () {
        $('#all_posts_filter').trigger('submit');
    })

    $('#all_posts_filter').on('submit', function (e) {
        e.preventDefault()
        loadmore_params.current_page = 1;

        all_param();

        $.ajax({
            url: loadmore_params.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend: function (xhr) {
                ajax_row_holder.css("opacity", "0.5");
            },
            success: function (data) {
                if (data) {
                    this_load_more.remove();
                    row_items.empty();
                    row_items.append(data.html);
                    ajax_row_holder.append(data.pagination);
                    ajax_row_holder.css("opacity", "1");
                }
            }
        }).done(function () {
            $(document).trigger('AjaxFilterDone');
        });
    });
});