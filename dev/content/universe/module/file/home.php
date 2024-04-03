<?php
?>
	<h2>File</h2>
<?php

use App\content\universe\module\file\File;

if(isset($site)) {
	/** @var File $module */
	$module = $site->module;
	
	if(isset($_POST['file-upload']) && isset($_FILES['file-file'])) {
		// check Upload Error
		if($_FILES['file-file']['error'] === 0) {
			$moduleID = $module->ID;
			
			// check Folder
			if(!file_exists('data/'.$moduleID)) {
				// create Folder
				mkdir('data/'.$moduleID, 0755);
			}
			
			$date = date('Y-m-d_H-i-s');
			$extension = '';
			$tmp = explode('.', $_FILES['file-file']['name']);
			if(count($tmp) > 1) {
				$extension = '.'.$tmp[count($tmp) - 1];
			}
			$newFileName = $date.$extension;
			if(!file_exists('data/'.$moduleID.'/'.$newFileName)) {
				// move File
				move_uploaded_file($_FILES['file-file']['tmp_name'], 'data/'.$moduleID.'/'.$newFileName);
				// insert File Details
				$module->insertFile($_FILES['file-file']['name'], $newFileName, $_FILES['file-file']['type']);
			}
		}
	}
	
	$search = null;
	if(isset($_POST['file-search'])) {
		if(isset($_POST['file-search']['search'])) {
			$search = $_POST['file-search']['search'];
		}
	}
	
	$files = $module->getFiles($search);
	?>
	<form method="post">
		<label for="data-file-search">Search</label>
		<input type="text"
			   id="data-file-search"
			   name="file-search[search]"
			   value="<?=$search?>">
		<input type="submit"
			   name="file-search[submit]"
			   value="Search">
	</form>
	<table border="1">
		<caption>Files</caption>
		<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Link</th>
			<th>Link</th>
			<th>Tag</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($files as $file) {
			?>
			<tr>
				<td><?=$file['ID']?></td>
				<td><?=$file['name']?></td>
				<td><a href="data/home.php?ID=<?=$file['ID']?>"
					   target="_blank">Link</a></td>
				<td><a href="data/104/<?=$file['file']?>"
					   target="_blank">direct Link</a></td>
				<td>
					<?php
					// get Tags
					$tags = explode(',', $file['tags']);
					// get Tag ID's
					$tagsID = explode(',', $file['tagsID']);
					// check Tag and Tag ID's
					if(count($tags) === count($tagsID)) {
						foreach($tags as $index => $tag) {
							?>
							<!-- TODO: Filter-By-Tag-URL -->
							<span class="tag"><a href="<?=$tagsID[$index]?>"><?=$tag?></a></span>
							<?php
						}
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
	
	$tags = $module->getTags();
	?>
	<table border="1">
		<caption>Tags</caption>
		<thead>
		<tr>
			<th>#</th>
			<th>Tag</th>
			<th>Count</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($tags as $tag) {
			if(is_null($tag['ID']) && is_null($tag['tag'])) {
				$tag['ID'] = 0;
				$tag['tag'] = 'no Tag';
			}
			?>
			<tr>
				<td><?=$tag['ID']?></td>
				<td><?=$tag['tag']?></td>
				<td><?=$tag['count']?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<hr>
	<form method="post"
		  action=""
		  enctype="multipart/form-data">
		<label for="file">File name:</label>
		<input type="file"
			   name="file-file"/>
		<input type="submit"
			   name="file-upload"
			   value="upload"/>
	</form>
	<?php
} else {
	?>
	<div class="error">Could not load Module</div>
	<?php
}