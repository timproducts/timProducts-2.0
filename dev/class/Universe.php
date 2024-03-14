<?php

namespace App\class;

class Universe {
	public ?int $ID;
	public ?string $universe;
	
	public function __construct() {
		$this->ID = null;
		$this->universe = null;
	}
	
	public function importFromDB(array $result): void {
		if(isset($result['ID'])) {
			$this->ID = $result['ID'];
		}
		
		if(isset($result['universe'])) {
			$this->universe = $result['universe'];
		}
	}
	
}