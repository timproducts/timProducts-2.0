<?php
?>
	<h2>Financial</h2>
<?php

use App\content\universe\module\financial\Financial;

if(isset($site)) {
	/** @var Financial $module */
	$module = $site->module;
	
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
			
			// add Transaction
			$module->addTransaction($accountID, $categoryID, $date, $value, $fromto, $description, $comment);
		}
	}
	
	include 'transaction.form.php';
	
	// get Overview
	$accounts = $module->getAccountsOverview();
	?>
	<table border="1">
		<caption>Overview</caption>
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
		foreach($accounts as $account) {
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
	// get Accounts
	$accounts = $module->getAccounts();
	
	foreach($accounts as $account) {
		// get Statement
		$transactions = $module->getAccountCurrentMonthStatement($account['ID']);
		
		?>
		<h3>Account: <?=$account['account']?></h3>
		<table border="1"
			   class="transaction">
			<caption>Statement</caption>
			<thead>
			<tr>
				<th>Date</th>
				<th>IN</th>
				<th>OUT</th>
				<th>Category</th>
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
					<td><?=$transaction['category']?></td>
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
		// get Statistic by Category
		$statistics = $module->getAccountCurrentMonthStatisticCategory($account['ID']);
		?>
		<table border="1">
			<caption>Statistic (by Category)</caption>
			<thead>
			<tr>
				<th>Category</th>
				<th>Value</th>
				<th>IN</th>
				<th>OUT</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($statistics as $statistic) {
				?>
				<tr>
					<td><?=$statistic['category']?></td>
					<td><?=formatPrice($statistic['value'])?></td>
					<td><?=formatPrice($statistic['IN'])?></td>
					<td><?=formatPrice($statistic['OUT'])?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
		// get Statistic by Subcategory
		$statistics = $module->getAccountCurrentMonthStatisticSubCategory($account['ID']);
		?>
		<table border="1">
			<caption>Statistic (by Subcategory)</caption>
			<thead>
			<tr>
				<th>Category</th>
				<th>Value</th>
				<th>IN</th>
				<th>OUT</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($statistics as $statistic) {
				?>
				<tr>
					<td><?=$statistic['category']?></td>
					<td><?=formatPrice($statistic['value'])?></td>
					<td><?=formatPrice($statistic['IN'])?></td>
					<td><?=formatPrice($statistic['OUT'])?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
		// get Monthly History
		$transactions = $module->getAccountMonthlyHistory($account['ID']);
		?>
		<table border="1">
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
		// get Templates
		$templates = $module->getAccountTransactionTemplates($account['ID']);
		?>
		<table border="1">
			<caption>Template</caption>
			<thead>
			<tr>
				<th>#</th>
				<th>Template</th>
				<th>Category</th>
				<th>From/To</th>
				<th>Value</th>
				<th>IN</th>
				<th>OUT</th>
				<th>Description</th>
				<th>Comment</th>
				<th>Action</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($templates as $template) {
				?>
				<tr>
					<td><?=$template['ID']?></td>
					<td><?=$template['template']?></td>
					<td><?=$template['category']?></td>
					<td><?=$template['fromto']?></td>
					<td><?=formatPrice($template['value'])?></td>
					<td><?=formatPrice($template['IN'])?></td>
					<td><?=formatPrice($template['OUT'])?></td>
					<td><?=$template['description']?></td>
					<td><?=$template['comment']?></td>
					<td>
						<a href="<?=$site->getURL(TRUE, TRUE, TRUE, [$site->viewParameterName => 'addTransaction', 'template' => $template['ID']])?>">add Transaction</a>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
	}
	// get Categories
	$categories = $module->getModuleCategories();
	?>
	<table border="1">
		<caption>Category</caption>
		<thead>
		<tr>
			<th>Category</th>
			<th>Subcategory</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$name = '';
		foreach($categories as $category) {
			foreach($category['subCategory'] as $subCategory) {
				?>
				<tr>
					<?php
					if($name <> $category['category']) {
						$name = $category['category'];
						?>
						<td rowspan="<?=count($category['subCategory'])?>"><?=$category['category']?></td>
						<?php
					}
					?>
					<td><?=$subCategory['category']?></td>
				</tr>
				<?php
			}
		}
		?>
		</tbody>
	</table>
	<?php
} else {
	?>
	<div class="error">Could not load Module</div>
	<?php
}