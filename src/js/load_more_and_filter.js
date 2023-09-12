jQuery(function ($) {

    var row_items = $('.ajax_row'),
        ajax_row_holder = $('.ajax_row_holder'),
        posts_per_page = '',
        posts_type = '',
        pagination_type = '',
        more_classes = '',
        more_label = '',
        prev_text = '',
        next_text = '',
        category = {},
        search = '',
        order = '',
        this_load_more = '',
        data = [],
        urlParams = new URLSearchParams(window.location.search),
        first = true,
        URLArray = '',
        $select = $('.js-category-filter-select'),
        $button = $('.js-category-filter'),
        $form = $('#all_posts_filter'),
        $search = $('#all-post-search'),
        $order = $('#js-post-order'),
        totalSelect = $select.length,
        totalButton = $button.length;

    function posts_per_page_fn() {
        posts_per_page = row_items.attr('data-posts-per-page');
    }

    function posts_type_fn() {
        posts_type = row_items.attr('data-posts-type');
    }

    function pagination_type_fn() {
        pagination_type = row_items.attr('data-pagination-type');
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


    function category_fn() {
        category = {};
        URLArray = '';
        first = true;
        if ($('button').hasClass('js-category-filter')) {
            $('.js-category-filter.active').each(function () {
                if (!$(this).hasClass('allCategories')) {
                    var taxonomy = $(this).attr('data-taxonomy');
                    var slug = $(this).attr('data-slug');
                    category[taxonomy] = category[taxonomy] && Array.isArray(category[taxonomy]) ? [...category[taxonomy], slug] : [slug]
                }
            })
        }
        if ($('select').hasClass('js-category-filter-select')) {
            $select.each(function () {
                if ($(this).val()) {
                    var taxonomy = $(this).attr('data-taxonomy');
                    var slug = $(this).val();
                    category[taxonomy] = category[taxonomy] && Array.isArray(category[taxonomy]) ? [...category[taxonomy], slug] : [slug]
                }
            })
        }
        $.each(category, function (i, v) {
            if (first) {
                URLArray += i + '=' + v;
                first = false;
            } else {
                URLArray += '&' + i + '=' + v;
            }
        })

    }

    function all_param() {
        posts_per_page_fn();
        posts_type_fn();
        pagination_type_fn();
        more_classes_fn();
        more_label_fn();
        prev_text_fn();
        next_text_fn();
        category_fn();

        search = $search.val();
        order = $order.val();
        this_load_more = $('.load_more_holder');

        if (search != '') {
            if (first) {
                URLArray += 'filter_search=' + search;
                first = false;
            } else {
                URLArray += '&filter_search=' + search;
            }
        }

        window.history.pushState(null, null, '?' + URLArray);

        data = {
            'action': 'loadmore',
            'query': loadmore_params.posts,
            'page': loadmore_params.current_page,
            'posts_per_page': posts_per_page,
            'post_type': posts_type,
            'pagination_type': pagination_type,
            'category': category,
            'search': search,
            'order': order,
            'more_classes': more_classes,
            'more_label': more_label,
            'prev_text': prev_text,
            'next_text': next_text,
        };
    }

    function URL_Param() {
        if (urlParams.has('filter_search')) {
            $search.val(urlParams.get('filter_search'));
            $form.trigger('submit');
        } else {
            $form.trigger('submit');
        }
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

    $button.on('click', function () {
        if ($(this).hasClass('multiply-false')) {
            $button.removeClass('active');
            $(this).addClass('active')
        } else {
            if (!$(this).hasClass('active')) {
                $(this).addClass('active')
            } else {
                $(this).removeClass('active');
            }
        }

    })

    $('.js-clear-filter').on('click', function () {
        $button.removeClass('active');
        $search.val('');
        $select.prop("selectedIndex", 0);
    })

    $order.on('change', function () {
        $form.trigger('submit');
    })

    $select.on('change', function () {
        $form.trigger('submit');
    })

    $form.on('submit', function (e) {
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
                    if (data.html != '') {
                        row_items.append(data.html);
                    } else {
                        row_items.append('<div class="no-results-found">no results found</div>')
                    }
                    ajax_row_holder.append(data.pagination);
                    ajax_row_holder.css("opacity", "1");
                }
            }
        }).done(function () {
            $(document).trigger('AjaxFilterDone');
        });
    });


    $select.each(function (index) {
        var taxonomy = $(this).attr('data-taxonomy');
        if (urlParams.has(taxonomy)) {
            $(this).val(urlParams.get(taxonomy));
        }
        if (index === totalSelect - 1) {
            URL_Param()
        }
    })


    $button.each(function (index) {
        if (!$(this).hasClass('allCategories')) {
            var taxonomy = $(this).attr('data-taxonomy');
            var slug = $(this).attr('data-slug');
            var $this = $(this);
            if (urlParams.has(taxonomy)) {
                var array = urlParams.get(taxonomy).split(',');

                $.each(array, function (i, v) {
                    if (v === slug) {
                        $this.addClass('active');
                    }
                })
            }
        }
        if (index === totalButton - 1) {
            URL_Param()
        }
    })
});