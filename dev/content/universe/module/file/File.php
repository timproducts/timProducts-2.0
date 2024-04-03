<?php

namespace App\content\universe\module\file;

use App\class\Module;
use App\class\Site;
use PDO;

class File extends Module {
	
	public function __construct(Site $site) {
		parent::__construct($site);
	}
	
	public function getFiles(string $search = null): array {
		// create SQL
		$sql = '
		SELECT file.*, GROUP_CONCAT(tag.tag) AS tags, GROUP_CONCAT(tag.ID) as tagsID
		FROM file AS file
		    LEFT JOIN tagging ON tagging.entity = "file" AND tagging.fiEntity = file.ID
		    LEFT JOIN tag on tagging.fiTag = tag.ID
		WHERE file.fiModule = :module ';
		if(!is_null($search)) {
			$sql = $sql.'AND (file.name LIKE :search OR tag.tag LIKE :search) ';
		}
		$sql = $sql.'GROUP BY file.ID;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('module', $this->site->module->ID, PDO::PARAM_INT);
		if(!is_null($search)) {
			$query->bindValue('search', '%'.$search.'%', PDO::PARAM_STR);
		}
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getTags(): array {
		// create SQL
		$sql = '
		SELECT tag.*, COUNT(file.ID) as \'count\'
		FROM file AS file
		    LEFT JOIN tagging ON tagging.entity = "file" AND tagging.fiEntity = file.ID
		    LEFT JOIN tag on tagging.fiTag = tag.ID
		WHERE file.fiModule = :module
		GROUP BY tag.ID
		ORDER BY tag.tag ASC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('module', $this->site->module->ID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function insertFile(string $name, string $path, string $type): void {
		// create SQL
		$sql = '
			INSERT INTO file (fiModule, type, name, file)
			VALUES (:module, :type, :name, :file);';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Values
		$query->bindValue('module', $this->ID, PDO::PARAM_INT);
		$query->bindValue('type', $type, PDO::PARAM_STR);
		$query->bindValue('name', $name, PDO::PARAM_STR);
		$query->bindValue('file', $path);
		// execute Query
		$query->execute();
	}
	
	public function getFile(int $ID): array {
		// create SQL
		$sql = '
		SELECT file.*
		FROM file AS file
		WHERE file.ID = :ID;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('ID', $ID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
}