<?php

use App\class\Site;

require 'class/Site.php';
require 'class/User.php';
require 'class/Universe.php';
require 'class/Module.php';

const DB_HOST = 'localhost';
const DB_NAME = 'timProducts';
const DB_USERNAME = 'timproducts';
const DB_PASSWORD = 'timproductsSQL123***';

const SITE_AUTH_TOKEN_NAME = 'auth_token';
const SITE_UNIVERSE_NAME = 'universe';
const SITE_MODULE_NAME = 'module';
const SITE_VIEW_NAME = 'view';

try {
	$site = new Site(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);

	$site->loadDatabase();

	$site->checkAuthenticationBypassOverToken(SITE_AUTH_TOKEN_NAME);

	$site->navigate(SITE_UNIVERSE_NAME, SITE_MODULE_NAME, SITE_VIEW_NAME);
} catch(Exception $exception) {
	echo $exception->getFile().' Line: '.$exception->getLine();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>[DEV] timProducts</title>
</head>
<body>
<div>
	<?php
	$breadcrumbs = [];
	foreach($site->getBreadcrumb() as $breadcrumb) {
		$breadcrumbs[] = '<a href="'.$breadcrumb['url'].'">'.$breadcrumb['title'].'</a>';
	}
	echo implode('>', $breadcrumbs);
	?>
</div>
<?php
$file = 'content/'.$site->content;
//echo $file;
if(file_exists($file)) {
	try {
		include $file;
	} catch(Exception $exception) {
		//echo($exception->getMessage());
		echo $exception->getFile().' Line: '.$exception->getLine();
	}
}
?>
</body>
</html>
<?php
function formatPrice($price) {
	if(is_null($price)) {
		$return = '';
	} else {
		$return = number_format($price, 2, ',', ' ').' €';
	}
	return $return;
}