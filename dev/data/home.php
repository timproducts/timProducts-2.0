<?php

use App\class\Site;
use App\content\universe\module\file\File;

require '../class/Site.php';
require '../class/User.php';
require '../class/Universe.php';
require '../class/Module.php';
//require 'content/universe/module/file/File.php';
require '../content/universe/module/file/File.php';

const DB_HOST = 'localhost';
const DB_NAME = 'timProducts';
const DB_USERNAME = 'timproducts';
const DB_PASSWORD = 'timproductsSQL123***';

$site = new Site(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

$site->loadDatabase();

$module = new File($site);
$result = $module->getFile($_GET['ID']);
//$site->dump($result);
//exit();
if(count($result) === 1) {
	$file = $result[0];
	if($file['file'] !== '') {
		$path = $file['fiModule']."/".$file['file'];
		//echo $path;
		//echo file_exists($path);
		if(file_exists($path)) {
			header ('Content-Type: '.$file['type']);
			echo file_get_contents($path);
		} else {
			echo 'no File';
		}
	} else {
		echo 'no File Path found';
	}
} else {
	echo 'no File found';
}