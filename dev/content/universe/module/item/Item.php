<?php

namespace App\content\universe\module\item;

use App\class\Module;
use App\class\Site;
use PDO;

class Item extends Module {
	
	public function __construct(Site $site) {
		parent::__construct($site);
	}
	
	public function getItems(bool $itemSet = TRUE): array {
//		itemSetItem.ID IS '.(!$itemSet ? 'NOT ': '').'NULL
//		LEFT JOIN item_has_item AS itemSet ON itemSet.fiItemSet = item.ID
//		LEFT JOIN item_has_item AS itemSetItem ON itemSetItem.fiItem = item.ID
		// create SQL
		$sql = '
		SELECT item.*,
		       GROUP_CONCAT(DISTINCT CONCAT(tag.ID,\',\',tag.tag) ORDER BY tag.tag ASC SEPARATOR \'|\') AS \'tags\',
		       COUNT(DISTINCT itemSet.ID) AS \'members\', itemSetParent.item AS parent
		FROM item
		    INNER JOIN tagging ON tagging.entity = \'item\' AND tagging.fiEntity = item.ID
		    LEFT JOIN tag ON tagging.fiTag = tag.ID
		    LEFT JOIN item_has_item AS itemSet ON itemSet.fiItemSet = item.ID
		    LEFT JOIN item_has_item AS itemSetItem ON itemSetItem.fiItem = item.ID
		    LEFT JOIN item AS itemSetParent ON itemSetItem.fiItemSet = itemSetParent.ID
		WHERE (itemSet.ID IS NULL AND itemSetItem.ID IS NULL) OR (itemSet.ID '.($itemSet ? 'IS':'IS NOT').' NULL AND itemSetItem.ID '.(!$itemSet ? 'IS':'IS NOT').' NULL)
		GROUP BY item.ID
		ORDER BY item.item ASC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		//$query->bindValue('entity', 'item', PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
}