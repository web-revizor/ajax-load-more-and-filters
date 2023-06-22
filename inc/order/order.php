<?php
global $load_more_variables;
?>
<div>
	<select id="js-post-order">
		<option value="DESC">
			<?= $load_more_variables['label_newest_order']; ?>
		</option>
		<option value="ASC">
			<?= $load_more_variables['label_old_order'] ?>
		</option>
	</select>
</div>