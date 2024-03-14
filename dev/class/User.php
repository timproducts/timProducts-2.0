<?php

namespace App\class;

class User {
	public ?int $ID;
	public ?string $mail;
	public ?string $password;
	
	public function __construct() {
		$this->ID = null;
		$this->mail = null;
		$this->password = null;
	}
	
	public function importFromDB(array $result): void {
		if(isset($result['ID'])) {
			$this->ID = $result['ID'];
		}
	}
	
}