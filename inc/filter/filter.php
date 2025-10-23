<?php
global $load_more_variables;
?>
<form id="all_posts_filter" class="all_posts_form" role="search">
    <?php if ($load_more_variables['enable_search'] === 'true'): ?>
        <div class="all-post-search-holder">
            <input class="all-post-search"
                   type="search" id="all-post-search" placeholder="<?= $load_more_variables['search_placeholder']; ?>">
            <button type="submit" class="all-post-submit">
                <?= $load_more_variables['label_search_button']; ?>
            </button>
        </div>
    <?php endif; ?>
    <?php if ($load_more_variables['filter_by_category'] === 'true' || $load_more_variables['enable_clear_button'] === 'true'): ?>
        <?php
        $filter_item_limit = $load_more_variables['filter_item_limit'];
        $filter_expand_label = $load_more_variables['filter_expand_label'];
        $filter_expand_class = $load_more_variables['filter_expand_class'];
        $categoriesCount = 0;
        $index = 0;
        ?>
        <div class="<?= $load_more_variables['filter_row_classes'] ?>">
            <?php $categoriesArray = explode(',', $load_more_variables['filter_taxonomy']) ?>
            <?php if ($load_more_variables['filter_type'] === 'button'): ?>
                <button type="submit"
                        class="js-category-filter allCategories active multiply-<?= $load_more_variables['multiply_filter'] ?> <?= $load_more_variables['filter_item_classes']; ?>">
                    <span class="text"><?= $load_more_variables['all_category_button']; ?></span>
                    <span class="postCount"><?= wp_count_posts($load_more_variables['post_type'])->publish; ?></span>
                </button>
                <?php foreach ($categoriesArray as $categories) : ?>
                    <?php
                    $taxonomy = $categories;
                    $name = get_taxonomy($taxonomy);
                    $categories = get_terms($categories, array(
                            'parent' => 0
                    ));

                    if ($filter_item_limit > 0) {
                        $categoriesCount = $categoriesCount + count($categories);
                    }

                    ?>
                    <?php if ($categories && $load_more_variables['filter_titles'] === 'true'): ?>
                        <p class="filterHeading"><?= $name->label ?></p>
                    <?php endif; ?>
                    <?php foreach ($categories as $category) : ?>

                        <?php
                        $childrensCat = get_terms($category->taxonomy, array(
                                'parent' => $category->term_id,
                                'hide_empty' => false
                        ));
                        if ($filter_item_limit > 0) {
                            $categoriesCount = $categoriesCount + count($childrensCat);
                        }
                        ?>
                        <button type="submit"
                                class="js-category-filter multiply-<?= $load_more_variables['multiply_filter'] ?> <?= $load_more_variables['filter_item_classes']; ?><?= ($childrensCat) ? ' parentCategory' : '' ?><?= $filter_item_limit > 0 && $filter_item_limit >= $index ? '' : ' hidden' ?>"
                                data-taxonomy="<?= $taxonomy ?>" data-slug="<?= $category->slug; ?>">
                            <span class="text"><?= $category->name; ?></span>
                            <span class="postCount"><?= $category->count; ?></span>
                        </button>
                        <?php $index++; ?>
                        <?php foreach ($childrensCat as $childrenCat) : ?>
                            <button type="submit"
                                    class="js-category-filter multiply-<?= $load_more_variables['multiply_filter'] ?> childCategory <?= $load_more_variables['filter_item_classes']; ?>"
                                    data-slug="<?= $childrenCat->slug; ?>">
                                <span class="text"><?= $childrenCat->name; ?></span>
                                <span class="postCount"><?= $childrenCat->count; ?></span>
                            </button>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php elseif ($load_more_variables['filter_type'] === 'select'): ?>
                <?php foreach ($categoriesArray as $categories) : ?>
                    <?php
                    $taxonomy = $categories;
                    $name = get_taxonomy($taxonomy);
                    $label = $name->label;
                    $categories = get_terms($categories, array(
                            'parent' => 0
                    )); ?>

                    <div class="category-filter-select-holder">
                        <select <?= $load_more_variables['multiply_filter'] == 'true' ? 'multiple' : '' ?>
                                class="js-category-filter-select <?= $load_more_variables['filter_item_classes']; ?>"
                                data-taxonomy="<?= $taxonomy ?>">
                            <option <?= $load_more_variables['filter_item_classes'] == 'true' ? 'disabled' : 'selected' ?>
                                    value="">
                                <?= $label ?>
                            </option>

                            <?php foreach ($categories as $category) : ?>
                                <?php
                                $childrensCat = get_terms($category->taxonomy, array(
                                        'parent' => $category->term_id,
                                        'hide_empty' => false
                                ));
                                ?>
                                <option value="<?= $category->slug; ?>">
                                    <?= $category->name; ?>
                                </option>
                                <?php foreach ($childrensCat as $childrenCat) : ?>
                                    <option value="<?= $childrenCat->slug; ?>">
                                        <?= $childrenCat->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($load_more_variables['enable_clear_button'] === 'true'): ?>
                <button type="submit" class="js-clear-filter">
                    Clear Filters
                </button>
            <?php endif; ?>
        </div>
        <?php if ($filter_item_limit > 0 && $categoriesCount > $filter_item_limit) : ?>
            <div class="<?= $filter_expand_class ?>">
                <span><?= $filter_expand_label ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</form>
