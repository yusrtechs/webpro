<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<?php
$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */
 

if ($this->input->post('show_style')) {
    $v .= '&show_style=' . $this->input->post('show_style');
}
if ($this->input->post('account')) {
    $v .= '&account=' . $this->input->post('account');
}else{
	$v .= '&account=' .$filter_id;
}

if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>


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
		<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
	
	
	
	
</script>
  

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-filter"></i><?= lang('Account_balances'); ?> </h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('accounts/reports') ?>">
                                <i class="fa fa-list"></i> <?= lang('reports') ?>
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
		<div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
                    <ul class="btn-tasks">
                        <!--<li class="dropdown">
                            <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>-->
                        <li class="dropdown">
                            <a href="#" id="print" class="tip" title="<?= lang('print') ?>">
                                <i class="icon fa fa-print"></i>
                            </a>
                        </li>
                    </ul>
        </div>
    </div>
    <div class="box-content">
	
	<div class="row">
	<div id="form">

                    <?php echo admin_form_open('accounts/accountbalance'); ?>
                    <div class="col-lg-12">
			
                        
						<input type="hidden" name="id" value="<?= $filter_id ?>" id="filter_id"/>
                        
                        <div class="col-sm-4" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang('reference_no'); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>

                        
                        <div class="col-sm-4" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang('customer'); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = lang('select') . ' ' . lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="account"><?= lang('Account'); ?></label>
                                <?php
                                $wh[''] = lang('all');
                                foreach ($accounts as $account) {
									$account = (array) $account;
                                    if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				$wh[$account['id']] = $account['accountName'];
				}else{
				$wh[$account['id']] = $account['accountNameEn'];
					
				}
                                }
                                echo form_dropdown('account', $wh, (isset($_POST['account']) ? $_POST['account'] : $filter_id), 'class="form-control" id="account" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Account') . '"');
                                ?>
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="show_style"><?= lang('Show_style'); ?></label>
                                <?php
                                $ss['0'] = lang('Detailed');
                                $ss['1'] = lang('Aggregation');
                                
                                echo form_dropdown('show_style', $ss, (isset($_POST['show_style']) ? $_POST['show_style'] : ''), 'class="form-control" id="show_style" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_style') . '"');
                                ?>
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
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
        <div class="row" id="accountstatment">
            <div class="col-lg-12">
			
			
                

                
				
				
				
		<?php
		if($this->input->post('show_style')==1){
			
			
			
			
			echo '
				<table class="table table-striped table-bordered table-hover">
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("Sr#").'</th>
				<th>'.lang("Account").'</th>
				<th>'.lang("Debit").'</th>
				<th>'.lang("Credit").'</th>
				<th>'.lang("balance").'</th>
				
				</tr>
				</thead>
				<tbody class="searchable text-center">
				';
				$sr=0;
			
			
			
			
			
			foreach ($accountList as $account1){
			
			
			$account1 = (array) $account1;
			
			$accountList1=$this->accounts_model->getAccountChilds($account1['accountID']);
			
			
			
			/*echo '<h2 class="pull-left">';
				if ($this->Settings->user_language == 'arabic' || $account1['accountNameEn']==''){
				echo $account1['accountName'].'</h2>';
				}else{
				echo $account1['accountNameEn'].'</h2>';
					
				}*/
				
				
				if ($this->Settings->user_language == 'arabic' || $account1['accountNameEn']==''){
				$accountName=$account1['accountName'];
				}else{
				$accountName=$account1['accountNameEn'];
					
				}
				
				
				
				
				
				
				$balanceDebit=0;
				$balanceCredit=0;
				$fbalance=0;
				
			
			foreach ($accountList1 as $account){
			
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
				/*echo '<h2 class="pull-left">';
				?>
				<a href="<?= admin_url('accounts/ledgers/'.$account['id']); ?> ">
				<?php
				if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				echo $account['accountName'].'</a></h2>';
				}else{
				echo $account['accountNameEn'].'</a></h2>';
					
				}*/
				
				
				
				
					
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
					
					

					
					
					
					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);
		
						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							$balanceDebit=$balanceDebit+$balance;
							$fbalance=$fbalance+$balance;
							
							
							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];
				
				            /*$accountDebitBalance = $accountInfo->accountDebit;

							
							$accountInfoDebitBalance = array('accountDebit' => ($balance + $accountDebitBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoDebitBalance);*/
				
							$x++;
						}
					}
					foreach ($entryCreditAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							$balanceCredit=$balanceCredit+$balance;
							$fbalance=$fbalance-$balance;
							
							
							/*$accountCreditBalance = $accountInfo->accountCredit;

							
							$accountInfoCreditBalance = array('accountCredit' => ($balance + $accountCreditBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoCreditBalance);*/
				
							$y++;
						}
					}
					
					
					
					
					
					
				

					
					
				}
				
			}
			
			
			
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
					
					
				if($diffDebit!=0 || $diffCredit!=0){
					$sr++;
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
				<td class=" align-middle">'.$sr.'</td>
				<td class=" align-middle">'.$accountName.'</td>';
				if($diffDebit!=''){
					
				echo '
				<td class=" align-middle"><strong>'.$diffDebit.'</strong></td>
				<td class=" align-middle"><strong></strong></td>
				<td class=" align-middle" style="text-decoration: underline;"><strong>'.$diffDebit.'</strong></td>';
				}else{
				echo '
				<td class=" align-middle"><strong></strong></td>
				<td class=" align-middle"><strong>'.$diffCredit.'</strong></td>
				<td class=" align-middle" style="text-decoration: underline;"><strong>'.$diffCredit.'</strong></td>';
					
				}
				
				echo '
				
				</tr>';	
					
				}
				
				
				
				
				
				
				
		}
		
		echo '
				</tbody></table>
				';
			
			
			
			
			
			
			
			
			
			
			
			
		}else{
			
			echo '
				<table class="table table-striped table-bordered table-hover">
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("Sr#").'</th>
				<th>'.lang("Account").'</th>
				<th>'.lang("Debit").'</th>
				<th>'.lang("Credit").'</th>
				<th>'.lang("balance").'</th>
				
				</tr>
				</thead>
				<tbody class="searchable text-center">
				';
				$sr=0;
				
				
						
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
				/*echo '<h2 class="pull-left">';
				?>
				<a href="<?= admin_url('accounts/ledgers/'.$account['id']); ?> ">
				<?php
				if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				echo $account['accountName'].'</a></h2>';
				}else{
				echo $account['accountNameEn'].'</a></h2>';
					
				}*/
				
				if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				$accountName=$account['accountName'];
				}else{
				$accountName=$account['accountNameEn'];
					
				}
				
				

				
				
				
				$balanceDebit=0;
				$balanceCredit=0;
				$fbalance=0;
				
					$sr++;
					
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
					
					

					
					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);
		
						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							$balanceDebit=$balanceDebit+$balance;
							$fbalance=$fbalance+$balance;
							
							
							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];
				
				            /*$accountDebitBalance = $accountInfo->accountDebit;

							
							$accountInfoDebitBalance = array('accountDebit' => ($balance + $accountDebitBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoDebitBalance);*/
				
							$x++;
						}
					}
					foreach ($entryCreditAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							$balanceCredit=$balanceCredit+$balance;
							$fbalance=$fbalance-$balance;
							
							
							/*$accountCreditBalance = $accountInfo->accountCredit;

							
							$accountInfoCreditBalance = array('accountCredit' => ($balance + $accountCreditBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoCreditBalance);*/
				
							$y++;
						}
					}
					
					
					
					
					
					
				

					
					
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
				<td class=" align-middle">'.$sr.'</td>
				<td class=" align-middle">'.$accountName.'</td>';
				if($diffDebit!=''){
					
				echo '
				<td class=" align-middle"><strong>'.$diffDebit.'</strong></td>
				<td class=" align-middle"><strong></strong></td>
				<td class=" align-middle" style="text-decoration: underline;"><strong>'.$diffDebit.'</strong></td>';
				}else{
				echo '
				<td class=" align-middle"><strong></strong></td>
				<td class=" align-middle"><strong>'.$diffCredit.'</strong></td>
				<td class=" align-middle" style="text-decoration: underline;"><strong>'.$diffCredit.'</strong></td>';
					
				}
				
				echo '
				
				</tr>';	
					
				}
				
				
				
			}
		}
		
		echo '
				
				</tbody></table>
				';
		
		}

		?>
	</div>
				
				
				
            
        </div>
    </div>
</div>
<script type="text/javascript">
        $(document).ready(function () {
            $('#print').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('accounts/accountbalanceprint/?' . $v) ?>";
                return false;
				
				//PrintElem('accountstatment);
            });
			

           
        });
    </script>

