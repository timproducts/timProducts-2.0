<?php

namespace App\content\universe\module\financial;

use App\class\Module;
use App\class\Site;
use PDO;

class Financial extends Module {
	
	private Site $site;
	
	public function __construct(Site $site) {
		parent::__construct();
		
		$this->site = $site;
	}
	
	// SQL
	// between last 25. and this Month
	// (YEAR(date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND DAY(date) > 25) OR (YEAR(date) = YEAR(CURRENT_DATE) AND MONTH(date) = MONTH(CURRENT_DATE))
	
	public function getAccountsOverview():array {
		// create SQL
		$sql = '
		SELECT financial_account.ID, financial_account.account, SUM(financial_account_transaction.value) AS \'value\', SUM(financial_account_transaction.IN) AS \'IN\', SUM(financial_account_transaction.OUT) AS \'OUT\'
		FROM financial_account
			LEFT JOIN financial_account_transaction ON financial_account_transaction.fiAccount = financial_account.ID AND
			YEAR(financial_account_transaction.date) = YEAR(CURRENT_DATE) AND MONTH(financial_account_transaction.date) = MONTH(CURRENT_DATE)
		WHERE financial_account.fiModule = :module GROUP BY financial_account.ID;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('module', $this->site->module->ID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getAccounts(): array {
		// create SQL
		$sql = '
		SELECT *
		FROM financial_account AS account
		WHERE account.fiModule = :module;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('module', $this->site->module->ID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getAccountCurrentMonthStatement(int $accountID): array {
		// create SQL
		$sql = '
		SELECT transaction.*, category.category
		FROM financial_account_transaction AS transaction
			LEFT JOIN financial_account_transaction_category AS category ON transaction.fiCategory = category.ID
		WHERE transaction.fiAccount = :account AND YEAR(transaction.date) = YEAR(CURRENT_DATE) AND MONTH(transaction.date) = MONTH(CURRENT_DATE)
		ORDER BY transaction.date DESC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('account', $accountID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getAccountCurrentMonthStatisticSubCategory(int $accountID): array {
		// create SQL
		$sql = '
		SELECT category.ID, category.category, SUM(transaction.value) AS value, SUM(transaction.IN) AS \'IN\', SUM(transaction.OUT) AS \'OUT\'
		FROM financial_account_transaction AS transaction
			LEFT JOIN financial_account_transaction_category AS category ON transaction.fiCategory = category.ID
		WHERE transaction.fiAccount = :account AND YEAR(transaction.date) = YEAR(CURRENT_DATE) AND MONTH(transaction.date) = MONTH(CURRENT_DATE)
		GROUP BY category.ID
		ORDER BY value DESC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('account', $accountID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getAccountCurrentMonthStatisticCategory(int $accountID): array {
		// create SQL
		$sql = '
		SELECT category.ID, category.category, SUM(transaction.value) AS value, SUM(transaction.IN) AS \'IN\', SUM(transaction.OUT) AS \'OUT\'
		FROM financial_account_transaction AS transaction
			LEFT JOIN financial_account_transaction_category AS sub_category ON transaction.fiCategory = sub_category.ID
			LEFT JOIN financial_account_transaction_category AS category ON sub_category.fiParent = category.ID
		WHERE transaction.fiAccount = :account AND YEAR(transaction.date) = YEAR(CURRENT_DATE) AND MONTH(transaction.date) = MONTH(CURRENT_DATE)
		GROUP BY category.ID
		ORDER BY value DESC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('account', $accountID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getAccountMonthlyHistory(int $accountID): array {
		// create SQL
		$sql = '
		SELECT YEAR(transaction.date) AS year,
		    MONTH(transaction.date) AS month,
		    SUM(transaction.value) AS value,
		    SUM(transaction.IN) AS \'IN\',
		    SUM(transaction.OUT) AS \'OUT\'
		FROM financial_account_transaction AS transaction
		WHERE transaction.fiAccount = :account
		GROUP BY MONTH(transaction.date)
		ORDER BY year DESC, month DESC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('account', $accountID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getAccountTransactionTemplates(int $accountID): array {
		// create SQL
		$sql = '
		SELECT template.*, category.ID AS category_ID, category.category AS category
		FROM financial_account_transaction_template AS template
			LEFT JOIN financial_account_transaction_category AS category ON category.ID = template.fiCategory
		WHERE template.fiAccount = :account
		ORDER BY template.template ASC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('account', $accountID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getAccountTransactionTemplate(int $templateID): array {
		// create SQL
		$sql = '
		SELECT template.*
		FROM financial_account_transaction_template AS template
		WHERE template.ID = :ID
		ORDER BY template.template ASC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('ID', $templateID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// return Result
		return $query->fetchAll();
	}
	
	public function getModuleCategories(): array {
		// create SQL
		$sql = '
		SELECT category.ID AS category_ID, category.category AS category, sub_category.ID AS sub_category_ID, sub_category.category AS sub_category
		FROM financial_account_transaction_category AS category
			LEFT JOIN financial_account_transaction_category AS sub_category ON category.ID = sub_category.fiParent
		WHERE category.fiParent IS NULL AND category.fiModule = :module
		ORDER BY category.category ASC, sub_category.category ASC;';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Value
		$query->bindValue('module', $this->site->module->ID, PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// get Result
		$categories = $query->fetchAll();
		
		// format
		$tmp = [];
		foreach($categories as $category) {
			$tmp[$category['category_ID']]['ID'] = $category['category_ID'];
			$tmp[$category['category_ID']]['category'] = $category['category'];
			$tmp[$category['category_ID']]['subCategory'][$category['sub_category_ID']]['ID'] = $category['sub_category_ID'];
			$tmp[$category['category_ID']]['subCategory'][$category['sub_category_ID']]['category'] = $category['sub_category'];
		}
		
		return $tmp;
	}
	
	public function addTransaction(int $accountID, int $categoryID, string $date, string $value, string $fromto, string $description, string $comment): void {
		// create SQL
		$sql = '
			INSERT INTO financial_account_transaction (fiAccount, fiCategory, date, value, fromto, description, comment)
			VALUES (:account, :category, :date, :value, :fromto, :description, :comment);';
		// create Query
		$query = $this->site->db->prepare($sql);
		// bind Values
		$query->bindValue('account', $accountID, PDO::PARAM_INT);
		$query->bindValue('category', $categoryID, PDO::PARAM_INT);
		$query->bindValue('date', $date, PDO::PARAM_STR);
		$query->bindValue('value', $value);
		$query->bindValue('fromto', $fromto, PDO::PARAM_STR);
		$query->bindValue('description', ($description !== ''?$description:null));
		$query->bindValue('comment', ($comment !== ''?$comment:null));
		// execute Query
		$query->execute();
	}
}