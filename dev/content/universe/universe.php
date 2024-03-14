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
				<td><a href="<?=$site->getURL2(TRUE, TRUE, TRUE, ['universe' => $universe['ID']])?>"><?=$universe['universe']?></a></td>
				<td><?=$site->getURL2(TRUE, TRUE, TRUE, ['universe' => $universe['ID']])?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
}