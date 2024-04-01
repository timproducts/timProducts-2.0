<?php

namespace App\content\universe\module\file;

use App\class\Module;
use App\class\Site;

class File extends Module {
	
	public function __construct(Site $site) {
		parent::__construct($site);
	}
}