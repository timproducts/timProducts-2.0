<?php

namespace App\class;

class Module {
	public ?int $ID;
	public ?string $module;
	public ?string $tag;
	
	public function __construct() {
		$this->ID = null;
		$this->module = null;
		$this->tag = null;
	}
	
	public function importFromDB(array $result): void {
		if(isset($result['ID'])) {
			$this->ID = $result['ID'];
		}
		
		if(isset($result['module'])) {
			$this->module = $result['module'];
		}
		
		if(isset($result['tag'])) {
			$this->tag = $result['tag'];
		}
	}
	
}