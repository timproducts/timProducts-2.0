<?php
?>
	<h2>Item</h2>
<?php

use App\content\universe\module\item\Item;

if(isset($site)) {
	/** @var Item $module */
	$module = $site->module;
	
	$items = $module->getItems();
	?>
	<table border="1">
		<caption>Items</caption>
		<thead>
		<tr>
			<th>#</th>
			<th>Item</th>
			<!--<th>Members</th>-->
			<th>Parent</th>
			<th>Tag(s)</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($items as $item) {
			?>
			<tr>
				<td><?=$item['ID']?></td>
				<td><?=$item['item']?></td>
				<!--<td><?=$item['members']?></td>-->
				<td><?=$item['parent']?></td>
				<td>
					<?php
					foreach(explode('|', $item['tags']) as $tag) {
						$tag = explode(',', $tag);
						?>
						<span class="tag"><?=$tag[1]?></span>
						<?php
					}
					?>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
} else {
	?>
	<div class="error">Could not load Module</div>
	<?php
}