<?php
?>
	<h2>File</h2>
<?php

use App\content\universe\module\file\File;

if(isset($site)) {
	/** @var File $module */
	$module = $site->module;
	
} else {
	?>
	<div class="error">Could not load Module</div>
	<?php
}