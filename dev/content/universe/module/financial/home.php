<?php
?>
	<h2>Financial</h2>
<?php
if(isset($site)) {
	// TODO: DEV
	//$site->dump($_POST);
	
	if(isset($_POST['financial'])) {
		if(isset($_POST['financial']['add_transaction'])) {
			$accountID = $_POST['financial']['add_transaction']['account'];
			$categoryID = $_POST['financial']['add_transaction']['category'];
			$date = $_POST['financial']['add_transaction']['date'];
			$value = $_POST['financial']['add_transaction']['value'];
			$fromto = $_POST['financial']['add_transaction']['fromto'];
			$description = $_POST['financial']['add_transaction']['description'];
			$comment = $_POST['financial']['add_transaction']['comment'];
			
			// create SQL
			$sql = '
			INSERT INTO financial_account_transaction (fiAccount, fiCategory, date, value, fromto, description, comment)
			VALUES (:account, :category, :date, :value, :fromto, :description, :comment);';
			// create Query
			$query = $site->db->prepare($sql);
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
	
	switch($site->view) {
		case 'addTransaction':
			// create SQL
			$sql = 'SELECT * FROM financial_account WHERE financial_account.fiModule = :module;';
			// create Query
			$query = $site->db->prepare($sql);
			// bind Value
			$query->bindValue('module', $site->module->ID, PDO::PARAM_INT);
			// execute Query
			$query->execute();
			// get Result
			$accounts = $query->fetchAll();
			
			// create SQL
			$sql = '
			SELECT category.ID AS category_ID, category.category AS category, sub_category.ID AS sub_category_ID, sub_category.category AS sub_category
			FROM financial_account_transaction_category AS category
			LEFT JOIN financial_account_transaction_category AS sub_category ON category.ID = sub_category.fiParent
			WHERE category.fiParent IS NULL AND sub_category.fiModule = :module;';
			// create Query
			$query = $site->db->prepare($sql);
			// bind Value
			$query->bindValue('module', $site->module->ID, PDO::PARAM_INT);
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
			$categories = $tmp;
			?>
			<form method="post"
				  action="<?=$site->getURL(TRUE, TRUE, FALSE)?>">
				<table border="1">
					<caption>add Transaction</caption>
					<thead>
					</thead>
					<tbody>
					<tr>
						<td><label for="financial_transaction_account">Account</label></td>
						<td>
							<select id="financial_transaction_account"
									name="financial[add_transaction][account]">
								<?php
								foreach($accounts as $account) {
									?>
									<option value="<?=$account['ID']?>"><?=$account['account']?></option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="financial_transaction_category">Category</label></td>
						<!-- TODO: remove
						<td><input type="text"
								   id="financial_transaction_category"
								   name="financial[add_transaction][category]"
								   value="2"></td>-->
						<td>
							<select id="financial_transaction_category"
									name="financial[add_transaction][category]">
								<?php
								foreach($categories as $category) {
									?>
									<optgroup label="<?=$category['category']?>">
										<?php
										foreach($category['subCategory'] as $subCategory) {
											?>
											<option value="<?=$subCategory['ID']?>"><?=$subCategory['category']?></option>
											<?php
										}
										?>
									</optgroup>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="financial_transaction_date">Date</label></td>
						<td><input type="date"
								   id="financial_transaction_date"
								   name="financial[add_transaction][date]"
								   value="<?=date('Y-m-d')?>"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_value">Value</label></td>
						<td><input type="number"
								   step="0.01"
								   id="financial_transaction_value"
								   name="financial[add_transaction][value]"
								   value="0"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_fromto">From/To</label></td>
						<td><input type="text"
								   id="financial_transaction_fromto"
								   name="financial[add_transaction][fromto]"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_description">Description</label></td>
						<td><input type="text"
								   id="financial_transaction_description"
								   name="financial[add_transaction][description]"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_comment">Comment</label></td>
						<td><input type="text"
								   id="financial_transaction_comment"
								   name="financial[add_transaction][comment]"></td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit"
											   name="financial[add_transaction][submit]"
											   value="Save"></td>
					</tr>
					</tbody>
				</table>
			</form>
			<?php
			break;
	}
	
	// SQL
	// between last 25. and this Month
	// (YEAR(date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND DAY(date) > 25) OR (YEAR(date) = YEAR(CURRENT_DATE) AND MONTH(date) = MONTH(CURRENT_DATE))
	$sql = '
	SELECT financial_account.ID, financial_account.account, SUM(financial_account_transaction.value) AS \'value\', SUM(financial_account_transaction.IN) AS \'IN\', SUM(financial_account_transaction.OUT) AS \'OUT\'
	FROM financial_account
		LEFT JOIN financial_account_transaction ON financial_account_transaction.fiAccount = financial_account.ID AND
		YEAR(financial_account_transaction.date) = YEAR(CURRENT_DATE) AND MONTH(financial_account_transaction.date) = MONTH(CURRENT_DATE)
	WHERE financial_account.fiModule = :module GROUP BY financial_account.ID ';
	$query = $site->db->prepare($sql);
	$query->bindValue('module', $site->module->ID, PDO::PARAM_INT);
	$query->execute();
	?>
	<table border="1">
		<caption>Financial</caption>
		<thead>
		<tr>
			<th>Account</th>
			<th>Value</th>
			<th>IN</th>
			<th>OUT</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($query->fetchAll() as $account) {
			?>
			<tr>
				<td><?=$account['account']?></td>
				<td><?=formatPrice($account['value'])?></td>
				<td><?=formatPrice($account['IN'])?></td>
				<td><?=formatPrice($account['OUT'])?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<a href="<?=$site->getURL(TRUE, TRUE, TRUE, [$site->viewParameterName => 'addTransaction'])?>">add Transaction</a>
	
	<style type="text/css">
		.transaction td:nth-child(2) {
			color: green;
		}
		.transaction td:nth-child(3) {
			color: red;
		}
	</style>
	<?php
	// create SQL
	$sql = 'SELECT * FROM financial_account WHERE financial_account.fiModule = :module;';
	// create Query
	$query = $site->db->prepare($sql);
	// bind Value
	$query->bindValue('module', $site->module->ID, PDO::PARAM_INT);
	// execute Query
	$query->execute();
	// get Result
	$accounts = $query->fetchAll();
	
	foreach($accounts as $account) {
		// create SQL
		$sql = '
		SELECT * FROM financial_account_transaction
		WHERE financial_account_transaction.fiAccount = :account AND YEAR(financial_account_transaction.date) = YEAR(CURRENT_DATE) AND MONTH(financial_account_transaction.date) = MONTH(CURRENT_DATE)
		ORDER BY financial_account_transaction.date DESC;';
		// create Query
		$query = $site->db->prepare($sql);
		// bind Value
		$query->bindValue('account', $account['ID'], PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// get Result
		$transactions = $query->fetchAll();
		
		?>
			<h3>Account: <?=$account['account']?></h3>
		<table border="1"
			   class="transaction">
			<caption>Bank Statement</caption>
			<thead>
			<tr>
				<th>Date</th>
				<th>IN</th>
				<th>OUT</th>
				<th>From/To</th>
				<th>Description</th>
				<th>Comment</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($transactions as $transaction) {
				?>
				<tr>
					<td><?=$transaction['date']?></td>
					<td><?=formatPrice($transaction['IN'])?></td>
					<td><?=formatPrice($transaction['OUT'])?></td>
					<td><?=$transaction['fromto']?></td>
					<td><?=$transaction['description']?></td>
					<td><?=$transaction['comment']?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
		
		// create SQL
		$sql = '
		SELECT YEAR(financial_account_transaction.date) AS \'year\',
		    MONTH(financial_account_transaction.date) AS \'month\',
		    SUM(financial_account_transaction.value) AS \'value\',
		    SUM(financial_account_transaction.IN) AS \'IN\',
		    SUM(financial_account_transaction.OUT) AS \'OUT\'
		FROM financial_account_transaction
		WHERE financial_account_transaction.fiAccount = :account
		GROUP BY MONTH(financial_account_transaction.date)
		ORDER BY year DESC, month DESC;';
		// create Query
		$query = $site->db->prepare($sql);
		// bind Value
		$query->bindValue('account', $account['ID'], PDO::PARAM_INT);
		// execute Query
		$query->execute();
		// get Result
		$transactions = $query->fetchAll();
		?>
		<table border="1"
			   class="">
			<caption>History</caption>
			<thead>
			<tr>
				<th>Year</th>
				<th>Month</th>
				<th>Value</th>
				<th>IN</th>
				<th>OUT</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$totalValue = 0;
			$totalIN = 0;
			$totalOUT = 0;
			foreach($transactions as $transaction) {
				$totalValue = $totalValue + $transaction['value'];
				$totalIN = $totalIN + $transaction['IN'];
				$totalOUT = $totalOUT + $transaction['OUT'];
				?>
				<tr>
					<td><?=$transaction['year']?></td>
					<td><?=$transaction['month']?></td>
					<td><?=formatPrice($transaction['value'])?></td>
					<td><?=formatPrice($transaction['IN'])?></td>
					<td><?=formatPrice($transaction['OUT'])?></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td colspan="2">Total:</td>
				<td><?=formatPrice($totalValue)?></td>
				<td><?=formatPrice($totalIN)?></td>
				<td><?=formatPrice($totalOUT)?></td>
			</tr>
			</tbody>
		</table>
		<?php
	}
}