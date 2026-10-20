<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>

<script>
    $(document).ready(function () {
       (function ($) {

        $('#filter').keyup(function () {
			

            var rex = new RegExp($(this).val(), 'i');
            $('.searchable tr').hide();
            $('.searchable tr').filter(function () {
                return rex.test($(this).text());
            }).show();

        })

    }(jQuery));
    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-filter"></i><?= lang('List_of_ledgers'); ?> </h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('accounts/ledgers') ?>">
                                <i class="fa fa-list"></i> <?= lang('Show_All') ?>
                            </a>
                        </li>
                        <!--<li>
                            <a href="<?= admin_url('products/add_adjustment_by_csv') ?>">
                                <i class="fa fa-plus-circle"></i> <?= lang('add_adjustment_by_csv') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('products/add_adjustment_by_excel') ?>">
                                <i class="fa fa-plus-circle"></i> <?= lang('add_adjustment_by_excel') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>-->
                        
                    </ul>
                </li>
      
            </ul>
        </div>
    </div>
    <div class="box-content">
	
	<div class="row">
				<div class="col-lg-8">
		<p>
			<?= lang('list_results'); ?>
			
		</p>
		</div>
		
		
		<div class="col-md-4" >
                                    <div class="form-group">
                                        <?php echo form_input('filter', '', 'class="form-control input-tip" id="filter" placeholder="' . lang('Search') . '"  '); ?>
                                    </div>
        </div>
		
	</div>
        <div class="row">
            <div class="col-lg-12">
			
			
                

                
				
				
				
		<?php
		
						
		foreach ($accountList as $account){
			
			$account = (array) $account;
			$accountEntries = array();

			$id      = $account['id'];
			$accountBalance = $account['accountDebit'] - $account['accountCredit'];
			

			foreach($entryList as $entry){
				$entry = (array) $entry;
				if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
					array_push($accountEntries, $entry);
				}
			}
			//

			if (!empty($accountEntries)){
				echo '<h2 class="pull-left">';
				?>
				<a href="<?= admin_url('accounts/ledgers/'.$account['id']); ?> ">
				<?php
				if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				echo $account['accountName'].'</a></h2>';
				}else{
				echo $account['accountNameEn'].'</a></h2>';
					
				}

				echo '
				<table class="table table-striped table-bordered table-hover">
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("date").'</th>
				<th>'.lang("Debit").'</th>
				<th>'.lang("Credit").'</th>
				<th>'.lang("reference_no").'</th>
				
				</tr>
				</thead>
				<tbody class="searchable text-center">
				';
				
				
				
				
				
				$balanceDebit=0;
				$balanceCredit=0;
					
				foreach ($accountEntries as $entry){
					$entry = (array) $entry;

					$entryDebitAccounts = array();
					$entryDebitBalances = 0;

					$entryCreditAccounts = array();
					$entryCreditBalances = 0;

					foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
						array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
						$entryDebitBalances += 1;
					}

					foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
						array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
						$entryCreditBalances += 1;
					}
					
					
					/*echo '
					<tr>
					<td class="text-left align-middle">'.$this->sma->hrld($entry['entryCreateDate']).'</td>
					<td class="align-middle">';
					echo lang($entry['entryDescription']);
					
					echo 			'</td>
					<td class="align-middle">';*/
					
					

					echo '
					<tr>
					<td class="text-left align-middle">'.$this->sma->hrld($entry['entryCreateDate']).'</td>
					<td class="align-middle">';
					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);
		
						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance).'</strong><br />';
							$balanceDebit=$balanceDebit+$balance;
							
							
							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];
				
				            $accountDebitBalance = $accountInfo->accountDebit;

							
							$accountInfoDebitBalance = array('accountDebit' => ($balance + $accountDebitBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoDebitBalance);
				
							$x++;
						}
					}
					echo '			</td>
					<td class="align-middle">';
					foreach ($entryCreditAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance).'</strong><br />';
							$balanceCredit=$balanceCredit+$balance;
							
							$accountCreditBalance = $accountInfo->accountCredit;

							
							$accountInfoCreditBalance = array('accountCredit' => ($balance + $accountCreditBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoCreditBalance);
				
							$y++;
						}
					}
					echo '			</td>
					<td class="align-middle">';
					?>
				<a href="<?php echo admin_url('accounts/view_entries/'.$entry['entryID']); ?>" data-toggle="modal" data-target="#myModal">
                                <?= '#'.$entry['entryID'] ?>
                            </a>
				
				<?php
				echo '</td>
					</tr>
					';
					
					
					
				

					
					
				}
				$total=0;
				$diffDebit=0;
				$diffCredit=0;
				if($balanceDebit>$balanceCredit){
					$total=$balanceDebit;
					$diffDebit=($balanceDebit-$balanceCredit);
				}else{
					$total=$balanceCredit;
					if($balanceCredit>$balanceDebit)
					$diffCredit=($balanceCredit-$balanceDebit);
				}
					
					
				echo '
				</tbody>
				<tfoot class="thead-dark">';
				if($diffDebit!=0 || $diffCredit!=0){
					if($diffDebit!=0)
						$diffDebit=$this->sma->formatMoney($diffDebit);
					else
						$diffDebit='';
				
				if($diffCredit!=0)
						$diffCredit=$this->sma->formatMoney($diffCredit);
					else
						$diffCredit='';
					
					
				
				echo '
				<tr class="text-center">
				<td><strong>'.lang("balance").'</strong></td>';
				if($diffDebit!=''){
					
				echo '
				<td style="text-decoration: underline;"><strong>'.$diffDebit.'</strong></td>
				<td style="text-decoration: underline;"><strong><font color="green"><i class="fa fa-arrow-right"></i></font>'.$diffDebit.'</strong></td>';
				}else{
				echo '
				<td style="text-decoration: underline;"><strong>'.$diffCredit.'<font color="green"><i class="fa fa-arrow-left"></i></font></strong></td>
				<td style="text-decoration: underline;"><strong>'.$diffCredit.'</strong></td>';
					
				}
				
				echo '
				<td></td>
				
				</tr>';	
					
				}
				
				
				echo '
				<tr class="text-center">
				<td><strong>'.lang("total").'</strong></td>
				<td style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($total).'</strong></td>
				<td style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($total).'</strong></td>
				<td></td>
				
				</tr>
				</tfoot></table>
				';
			}
		}

		?>
	</div>
				
				
				
            
        </div>
    </div>
</div>


