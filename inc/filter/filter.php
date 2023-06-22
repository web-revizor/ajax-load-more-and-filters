<?php
global $load_more_variables;
?>
<form id="all_posts_filter" role="search">
	<?php if ($load_more_variables['enable_search'] === 'true'): ?>
		<div class="all-post-search-holder">
			<input class=""
			       type="search" id="all-post-search" placeholder="<?= $load_more_variables['search_placeholder']; ?>">
			<button type="submit" class="all-post-submit">
				<?= $load_more_variables['label_search_button']; ?>
			</button>
		</div>
	<?php endif; ?>
	<div class="<?= $load_more_variables['filter_row_classes'] ?>">
		<button type="submit"
		        class="js-category-filter allCategories active <?= $load_more_variables['filter_item_classes']; ?>">
			<span class="text"><?= $load_more_variables['all_category_button']; ?></span>
			<span class="postCount"><?= wp_count_posts($load_more_variables['post_type'])->publish; ?></span>
		</button>
		<?php $categories = get_terms($load_more_variables['filter_taxonomy'], array(
			'parent' => 0
		)); ?>
		<?php foreach ($categories as $category) : ?>
			<?php
			$childrensCat = get_terms($category->taxonomy, array(
				'parent' => $category->term_id,
				'hide_empty' => false
			));
			?>
			<button type="submit"
			        class="js-category-filter <?= $load_more_variables['filter_item_classes']; ?><?= ($childrensCat) ? ' parentCategory' : '' ?>"
			        data-slug="<?php echo $category->slug; ?>">
				<span class="text"><?php echo $category->name; ?></span>
				<span class="postCount"><?= $category->count; ?></span>
			</button>
			<?php foreach ($childrensCat as $childrenCat) : ?>
				<button type="submit"
				        class="js-category-filter childCategory <?= $load_more_variables['filter_item_classes']; ?>"
				        data-slug="<?php echo $childrenCat->slug; ?>">
					<span class="text"><?php echo $childrenCat->name; ?></span>
					<span class="postCount"><?= $childrenCat->count; ?></span>
				</button>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</div>
</form>