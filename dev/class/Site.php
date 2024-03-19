<?php

namespace App\class;

use PDO;
use PDOException;

class Site {
	/** @var PDO|null */
	public ?PDO    $db;
	private string $dbHost;
	private string $dbDatabase;
	private string $dbUsername;
	private string $dbPassword;
	
	public string $universeParameterName;
	public string $moduleParameterName;
	public string $viewParameterName;
	
	public bool      $login;
	public ?User     $user;
	public ?Universe $universe;
	public ?Module   $module;
	public ?string   $view;
	public ?string   $action;
	public ?string   $token;
	public ?string   $content;
	
	public function __construct(string $dbHost, string $dbDatabase, string $dbUsername, string $dbPassword) {
		$this->db = null;
		$this->dbHost = $dbHost;
		$this->dbDatabase = $dbDatabase;
		$this->dbUsername = $dbUsername;
		$this->dbPassword = $dbPassword;
		
		$this->authenticationBypassTokenParameterName = 'auth_token';
		$this->universeParameterName = 'universe';
		$this->moduleParameterName = 'module';
		$this->viewParameterName = 'view';
		
		$this->login = FALSE;
		$this->universe = null;
		$this->module = null;
		$this->view = null;
		$this->action = null;
		$this->token = null;
		$this->content = 'out.php';
	}
	
	public function loadDatabase(): void {
		try {
			// TODO: add Port
			$this->db = new PDO('mysql:host='.$this->dbHost.';dbname='.$this->dbDatabase, $this->dbUsername, $this->dbPassword);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch(PDOException $exception) {
			switch($exception->getCode()) {
				case 2002:
					// "SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it"
					die('DB Server Connection refused');
					break;
				case 1045:
					// "SQLSTATE[HY000] [1045] Access denied for user 'admin'@'localhost' (using password: YES)"
					die('DB Server User Authentication');
					break;
				case 1044:
					// "SQLSTATE[HY000] [1044] Access denied for user 'admin'@'localhost' to database 'test'"
					die('DB Server User Authorization');
					break;
				default:
					die('Exception '.$exception->getCode().': '.$exception->getMessage());
			}
		}
	}
	
	public function checkAuthenticationBypassOverToken(): void {
		if(isset($_GET[$this->authenticationBypassTokenParameterName])) {
			$token = $_GET[$this->authenticationBypassTokenParameterName];
			// create SQL
			$sql = 'SELECT * FROM user WHERE user.auth_token = :token;';
			// create Query
			$query = $this->db->prepare($sql);
			// bind Value
			$query->bindValue(':token', $token, PDO::PARAM_STR);
			// execute Query
			$query->execute();
			// get Result
			$result = $query->fetchAll();
			
			// check Result
			if(count($result) === 1) {
				$user = $result[0];
				//echo $result['ID'].sha1($result['mail'];
				if($user['auth_token'] === $user['ID'].sha1($user['mail'])) {
					$this->token = $token;
					$this->user = new User();
					$this->user->importFromDB($user);
					$this->login = TRUE;
					//echo 'Authentication bypassed';
				}
			}
		}
	}
	
	public function navigate(): void {
		if($this->login) {
			if(isset($_GET[$this->universeParameterName])) {
				// get Parameter Universe
				// TODO: check for special Chars or Hacks
				$universeID = $_GET[$this->universeParameterName];
				// set Universe
				$this->getUniverse($universeID);
			}
			
			if(!is_null($this->universe)) {
				$this->content = 'universe/module/module.php';
				
				if(isset($_GET[$this->viewParameterName])) {
					// get Parameter View
					// TODO: check for special Chars or Hacks
					$view = $_GET[$this->viewParameterName];
					// set View
					$this->view = $view;
				}
				
				if(isset($_GET[$this->moduleParameterName])) {
					// get Parameter Module
					// TODO: check for special Chars or Hacks
					$moduleID = $_GET[$this->moduleParameterName];
					// set Module
					$this->getModule($moduleID);
				}
				
				if(!is_null($this->module)) {
					switch($this->module->tag) {
						case 'financial':
							$this->content = 'universe/module/financial/home.php';
							break;
					}
				}
			} else {
				$this->content = 'universe/universe.php';
			}
		}
		
		// TODO: DEV
		//$this->dump($this->universe, 'Universe');
		// TODO: DEV
		//$this->dump($this->module, 'Module');
		// TODO: DEV
		//$this->dump($this->view, 'View');
	}
	
	private function getUniverse(int $universeID): void {
		// create SQL
		$sql = '
		SELECT universe.ID AS \'ID\', universe.universe AS \'universe\', user_has_universe.ID AS \'user_has_universe_ID\'
		FROM user_has_universe
			LEFT JOIN universe ON user_has_universe.fiUniverse = universe.ID
		WHERE user_has_universe.fiUser = :user AND user_has_universe.fiUniverse = :universe;
		';
		// create Query
		$query = $this->db->prepare($sql);
		// bind Value
		$query->bindValue('user', $this->user->ID, PDO::PARAM_INT);
		$query->bindValue('universe', $universeID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// get Result
		$result = $query->fetchAll();
		// check Result
		if(count($result) === 1) {
			$this->universe = new Universe();
			$this->universe->importFromDB($result[0]);
		}
	}
	
	private function getModule(int $moduleID): void {
		// get Module
		// create SQL
		$sql = '
		SELECT module.ID AS \'ID\', module.module AS \'module\', module.tag AS \'tag\', universe_has_module.ID AS \'universe_has_module\'
		FROM universe_has_module
			LEFT JOIN module ON universe_has_module.fiModule = module.ID
		WHERE universe_has_module.fiUniverse = :universe AND universe_has_module.fiModule = :module;
		';
		// create Query
		$query = $this->db->prepare($sql);
		// bind Value
		$query->bindValue('universe', $this->universe->ID, PDO::PARAM_INT);
		$query->bindValue('module', $moduleID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// get Result
		$result = $query->fetchAll();
		// check Result
		if(count($result) === 1) {
			$this->module = new Module();
			$this->module->importFromDB($result[0]);
		}
	}
	
	public function getURL2(bool $universe, bool $module, bool $view, array $param = []) {
		$return = $_SERVER['SCRIPT_NAME'];
		$parameters = [];
		
		if($module && !is_null($this->module)) {
			$parameters['module'] = 'module='.$this->module->ID;
		}
		
		if($universe && !is_null($this->universe)) {
			$parameters['universe'] = 'universe='.$this->universe->ID;
		}
		
		if($view && !is_null($this->view)) {
			$parameters['view'] = 'view='.$this->view;
		}
		
		if(!is_null($this->token)) {
			$parameters['auth_token'] = 'auth_token='.$this->token;
		}
		
		foreach($param as $key => $value) {
			$parameters[$key] = $key.'='.$value;
		}
		
		$tmp = [];
		$sequence = ['view', 'module', 'universe'];
		foreach($sequence as $check) {
			if(isset($parameters[$check])) {
				$tmp[] = $parameters[$check];
				unset($parameters[$check]);
			}
		}
		
		foreach($parameters as $parameter) {
			$tmp[] = $parameter;
		}
		
		if(count($tmp) > 0) {
			$return = $return.'?'.implode('&', $tmp);
		}
		
		return $return;
	}
	
	public function dump($all, ?string $tile = null): void {
		if(!is_null($tile)) {
			$all = [$tile => $all];
		}
		
		if(is_null($all)) {
			echo 'NULL';
		} else {
			echo '<pre>'.print_r($all, TRUE).'</pre>';
		}
	}
	
	public function getBreadcrumb(): array {
		$return = [
			'home' => [
				'title' => 'Home',
				'url' => $_SERVER['SCRIPT_NAME'].$this->getURL2(FALSE, FALSE, FALSE)
			]
		];
		
		// Universe Parameter
		if(!is_null($this->universe)) {
			$return['universe'] = [
				'title' => 'Universe '.$this->universe->universe,
				'url' => $_SERVER['SCRIPT_NAME'].$this->getURL2(TRUE, FALSE, FALSE)
			];
		}
		
		// Module Parameter
		if(!is_null($this->module)) {
			$return['module'] = [
				'title' => 'Module '.$this->module->module,
				'url' => $_SERVER['SCRIPT_NAME'].$this->getURL2(TRUE, TRUE, FALSE)
			];
		}
		
		// View Parameter
		if(!is_null($this->view)) {
			$return['view'] = [
				'title' => 'View '.$this->view,
				'url' => $_SERVER['SCRIPT_NAME'].$this->getURL2(TRUE, TRUE, TRUE)
			];
		}
		
		return $return;
	}
}