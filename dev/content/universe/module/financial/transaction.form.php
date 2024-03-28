<?php

use App\content\universe\module\financial\Financial;

if(isset($site)) {
	/** @var Financial $module */
	$module = $site->module;
	
	// Default
	$title = 'Transaction';
	$transaction = [];
	$transaction['ID'] = 0;
	$transaction['account'] = 0;
	$transaction['category'] = 0;
	$transaction['date'] = date('Y-m-d');
	$transaction['value'] = 0.0;
	$transaction['fromto'] = '';
	$transaction['description'] = '';
	$transaction['comment'] = '';
	
	switch($site->view) {
		case 'addTransaction':
			$title = 'add Transaction';
			if(isset($_GET['template'])) {
				// get Template
				$result = $module->getAccountTransactionTemplate($_GET['template']);
				// check Result
				if(count($result) === 1) {
					$template = $result[0];
					$title = $title.' (Template: '.$template['template'].')';
					$transaction['account'] = $template['fiAccount'];
					$transaction['category'] = $template['fiCategory'];
					$transaction['value'] = $template['value'];
					$transaction['fromto'] = $template['fromto'];
					$transaction['description'] = $template['description'];
					$transaction['comment'] = $template['comment'];
				}
			}
			// get Accounts
			$accounts = $module->getAccounts();
			// get Categories
			$categories = $module->getModuleCategories();
			?>
			<form method="post"
				  action="<?=$site->getURL(TRUE, TRUE, FALSE)?>">
				<table border="1">
					<caption><?=$title?></caption>
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
									<option value="<?=$account['ID']?>"<?=($transaction['account'] === $account['ID']?' selected':'')?>><?=$account['account']?></option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="financial_transaction_category">Category</label></td>
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
											<option value="<?=$subCategory['ID']?>"<?=($transaction['category'] === $subCategory['ID']?' selected':'')?>><?=$subCategory['category']?></option>
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
								   value="<?=$transaction['date']?>"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_value">Value</label></td>
						<td><input type="number"
								   step="0.01"
								   id="financial_transaction_value"
								   name="financial[add_transaction][value]"
								   value="<?=$transaction['value']?>"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_fromto">From/To</label></td>
						<td><input type="text"
								   id="financial_transaction_fromto"
								   name="financial[add_transaction][fromto]"
								   value="<?=$transaction['fromto']?>"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_description">Description</label></td>
						<td><input type="text"
								   id="financial_transaction_description"
								   name="financial[add_transaction][description]"
								   value="<?=$transaction['description']?>"></td>
					</tr>
					<tr>
						<td><label for="financial_transaction_comment">Comment</label></td>
						<td><input type="text"
								   id="financial_transaction_comment"
								   name="financial[add_transaction][comment]"
								   value="<?=$transaction['comment']?>"></td>
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
}