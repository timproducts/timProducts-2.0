<?php
if(isset($site)) {
	?>
	<h2>Module</h2>
	<table border="1">
		<caption>Select Module</caption>
		<thead>
		<tr>
			<th>Module</th>
		</tr>
		</thead>
		<tbody>
		<?php
		// create SQL
		$sql = 'SELECT module.ID AS \'ID\', module.module AS \'module\', module.tag AS \'tag\', universe_has_module.ID AS \'universe_has_module_ID\' FROM universe_has_module LEFT JOIN module ON universe_has_module.fiModule = module.ID WHERE universe_has_module.fiUniverse = :universe;';
		// create Query
		$query = $site->db->prepare($sql);
		// bind Value
		$query->bindValue(':universe', $site->universe->ID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// get Result
		$result = $query->fetchAll();
		
		foreach($result as $module) {
			
			?>
			<tr>
				<td><a href="<?=$site->getURL(TRUE, TRUE, TRUE, [$site->moduleParameterName => $module['ID']])?>"><?=$module['module']?></a></td>
				<td><?=$site->getURL(TRUE, TRUE, TRUE, [$site->moduleParameterName => $module['ID']])?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
}