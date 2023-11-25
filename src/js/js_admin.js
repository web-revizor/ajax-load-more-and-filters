//= ../../node_modules/select2/dist/js/select2.min.js

jQuery(function ($) {
  $('.js-tabs-list a').on('click', function () {
    if (!$(this).hasClass('active')) {
      let target = $(this).attr('href');

      $('.js-tabs-list a').removeClass('active');
      $(this).addClass('active');

      $('.js-tabs-content').hide();
      $(target).fadeIn();
    }
  });

  $('select').select2({
    minimumResultsForSearch: -1,
    width: '100%',
  });

  function generateShortCode() {
    let post_type_val = $('#post_type_load_more').find(':selected').val(),
      countPosts_val = $('#count_post_per_page').val(),
      type_pagination_val = $('#type_pagination').find(':selected').val(),
      row_classes_val = $('#row_classes').val(),
      load_more_label_val = $('#load_more_label').val(),
      load_more_classes_val = $('#load_more_classes').val(),
      filter_row_classes_val = $('#filter_row_classes').val(),
      filter_item_classes_val = $('#filter_item_classes').val(),
      filter_taxonomy_val = $('#filter_taxonomy').val(),
      filter_type_val = $('#filter_type').val(),
      multiply_filter_button = '',
      all_category_button_val = $('#all_category_button').val(),
      filter_by_category = '',
      enable_clear_button = '',
      enable_search = '',
      label_search_button_val = $('#label_search_button').val(),
      search_placeholder_val = $('#search_placeholder').val(),
      enable_order = '',
      label_newest_order_val = $('#label_newest_order').val(),
      label_old_order_val = $('#label_old_order').val(),
      prev_label_val = $('#prev_text').val(),
      next_label_val = $('#next_text').val(),
      shortCodeArray = [];

    shortCodeArray.push(
      ' post_type="' +
        post_type_val +
        '"' +
        ' posts_per_page="' +
        countPosts_val +
        '"' +
        ' type_pagination="' +
        type_pagination_val +
        '"'
    );

    if (row_classes_val !== '') {
      shortCodeArray.push(' row_classes="' + row_classes_val + '"');
    }
    if (load_more_label_val !== '') {
      shortCodeArray.push(' load_more_label="' + load_more_label_val + '"');
    }
    if (load_more_classes_val !== '') {
      shortCodeArray.push(' load_more_classes="' + load_more_classes_val + '"');
    }
    if (prev_label_val !== '') {
      shortCodeArray.push(' prev_text="' + prev_label_val + '"');
    }
    if (next_label_val !== '') {
      shortCodeArray.push(' next_text="' + next_label_val + '"');
    }

    if ($('#filter_by_category').is(':checked')) {
      filter_by_category = ' filter_by_category="true"';
      if ($('#enable_clear_button').is(':checked')) {
        enable_clear_button = ' enable_clear_button="true"';
      }
      $('.hiddenFilter').show();
      if (filter_row_classes_val !== '') {
        shortCodeArray.push(
          ' filter_row_classes="' + filter_row_classes_val + '"'
        );
      }
      if (filter_item_classes_val !== '') {
        shortCodeArray.push(
          ' filter_item_classes="' + filter_item_classes_val + '"'
        );
      }
      if (filter_taxonomy_val !== '') {
        shortCodeArray.push(' filter_taxonomy="' + filter_taxonomy_val + '"');
      }
      if (filter_type_val !== '') {
        shortCodeArray.push(' filter_type="' + filter_type_val + '"');
      }
      if (filter_type_val === 'button') {
        $('.hiddenFilterType').show();
        if ($('#multiply_filter_button').is(':checked')) {
          multiply_filter_button = ' multiply_filter_button="true"';
        }
        shortCodeArray.push(' filter_type="' + filter_type_val + '"');
      } else {
        $('.hiddenFilterType').hide();
      }
      if (all_category_button_val !== '') {
        shortCodeArray.push(
          ' all_category_button="' + all_category_button_val + '"'
        );
      }
    } else {
      filter_by_category = '';
      $('.hiddenFilter').hide();
    }

    if ($('#enable_search').is(':checked')) {
      enable_search = ' enable_search="true"';
      $('.hiddenSearch').show();
      if (label_search_button_val !== '') {
        shortCodeArray.push(
          ' label_search_button="' + label_search_button_val + '"'
        );
      }
      if (search_placeholder_val !== '') {
        shortCodeArray.push(
          ' search_placeholder="' + search_placeholder_val + '"'
        );
      }
    } else {
      $('.hiddenSearch').hide();
    }

    if ($('#enable_order').is(':checked')) {
      enable_order = ' enable_order="true"';
      $('.hiddenOrder').show();
      if (label_newest_order_val !== '') {
        shortCodeArray.push(
          ' label_newest_order="' + label_newest_order_val + '"'
        );
      }
      if (label_old_order_val !== '') {
        shortCodeArray.push(' label_old_order="' + label_old_order_val + '"');
      }
    } else {
      $('.hiddenOrder').hide();
    }
    shortCodeArray = shortCodeArray.join('');
    shortCodeArray = shortCodeArray.toString();
    $('#results_shortcode').val(
      '[all_posts_ajax' +
        shortCodeArray +
        filter_by_category +
        multiply_filter_button +
        enable_clear_button +
        enable_search +
        enable_order +
        ']'
    );
  }

  generateShortCode();

  $('input, select').on('change', function () {
    generateShortCode();
  });

  function copyToClipboard(text) {
    if (window.clipboardData && window.clipboardData.setData) {
      // IE specific code path to prevent textarea being shown while dialog is visible.
      return clipboardData.setData('Text', text);
    } else if (
      document.queryCommandSupported &&
      document.queryCommandSupported('copy')
    ) {
      let textarea = document.createElement('textarea');
      textarea.textContent = text;
      textarea.style.position = 'absolute'; // Prevent scrolling to bottom of page in MS Edge.
      textarea.style.height = '1px';
      textarea.style.width = '1px';
      document.body.appendChild(textarea);
      textarea.select();
      try {
        return document.execCommand('copy'); // Security exception may be thrown by some browsers.
      } catch (ex) {
        console.warn('Copy to clipboard failed.', ex);
        return false;
      } finally {
        document.body.removeChild(textarea);
      }
    }
  }

  $('.js-click-to-copy-link').on('click', function () {
    copyToClipboard($('#results_shortcode').val());
  });
});
