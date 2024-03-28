<?php
if(isset($site)) {
	?>
	<h2>Universe</h2>
	<?php
	// create SQL
	$sql = 'SELECT universe.ID AS \'ID\', universe.universe AS \'universe\', user_has_universe.ID AS \'user_has_universe_ID\' FROM user_has_universe LEFT JOIN universe ON user_has_universe.fiUniverse = universe.ID WHERE user_has_universe.fiUser = :user;';
	// create Query
	$query = $site->db->prepare($sql);
	// bind Value
	$query->bindValue(':user', $site->user->ID, PDO::PARAM_INT);
	// execute Query
	$query->execute();
	// get Result
	$result = $query->fetchAll();
	?>
	<table border="1">
		<caption>Select Universe</caption>
		<thead>
		<tr>
			<th>#</th>
			<th>Universe</th>
			<th>URL</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($result as $universe) {
			?>
			<tr>
				<td><?=$universe['ID']?></td>
				<td><a href="<?=$site->getURL(TRUE, TRUE, TRUE, [$site->universeParameterName => $universe['ID']])?>"><?=$universe['universe']?></a></td>
				<td><?=$site->getURL(TRUE, TRUE, TRUE, [$site->universeParameterName => $universe['ID']])?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	
	<h2>Module</h2>
	<?php
	// create SQL
	$sql = '
	SELECT universe.ID AS \'universeID\', universe.universe AS \'universe\', module.ID AS \'moduleID\', module.module AS \'module\', uhu.ID AS \'user_has_universe_ID\', uhm.ID AS \'universe_has_module_ID\'
	FROM user_has_universe AS uhu
	    LEFT JOIN universe ON uhu.fiUniverse = universe.ID
	    LEFT JOIN universe_has_module AS uhm ON uhm.fiUniverse = universe.ID
	    LEFT JOIN module ON uhm.fiModule = module.ID
	WHERE uhu.fiUser = :user
	ORDER BY module.module;';
	
	// create Query
	$query = $site->db->prepare($sql);
	// bind Value
	$query->bindValue(':user', $site->user->ID, PDO::PARAM_INT);
	// execute Query
	$query->execute();
	// get Result
	$modules = $query->fetchAll();
	
	?>
	<table border="1">
		<caption>Select Module</caption>
		<thead>
		<tr>
			<th>#</th>
			<th>Module</th>
			<th>#</th>
			<th>Universe</th>
			<th>URL</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($modules as $module) {
			$link = $site->getURL(TRUE, TRUE, TRUE, [$site->universeParameterName => $module['universeID'], $site->moduleParameterName => $module['moduleID']]);
			?>
			<tr>
				<td><?=$module['moduleID']?></td>
				<td><a href="<?=$link?>"><?=$module['module']?></a></td>
				<td><?=$module['universeID']?></td>
				<td><?=$module['universe']?></td>
				<td><?=$link?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
}