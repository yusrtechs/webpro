<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
th {
  font-size: 12px;
}
td {
  font-size: 12px;
}

@media print {

th {
  font-size: 8px;
}
td {
  font-size: 8px;
}


	
                /*h1 {padding:1px;}*/
				.well { margin-bottom:-2px;padding:1px;}
				
				
				.col-xs-1 {
    width: 4.33333333%;
}
				.col-xs-11 {
    width: 95.66666667%;
}
		

.from{margin-bottom:15px;}		
  
  
  div.divFooter {
    position: fixed;
    bottom: 0;
  } 


      table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }
	  tr td    { font-size:8pt; }

   
	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt; }
	
	div.container {
  min-width: 100%;
  overflow:hidden;
  border:no-border;
}
	div. content {
 
  overflow:hidden;
}



div{
	overflow:visible !important;
	
}
                
            }
			
	
		
   		
 .divbg:after {
  content: '';
  width: 100%;
  height: 99%;
  position: absolute;
  background-color: #2A3F54;
  top: 0;
  right: 0;
  -webkit-print-color-adjust:exact;
}

.col-xs-1 {
    width: 4.33333333%;
}


</style>
<div class="box">
    
    <div class="box-content">
	
	<div class="row bold">
        <div class="col-xs-12 no-print" >
                
           
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
			</div>
           </div>
		   
		   
		   <div class="row bold">
            <div class="col-xs-8 pull-left" style=" margin-bottom:10px;"> 
					
				    <span valign="top"><h1  style=" font-size: 25px;">Account balance / رصيد الحساب</h1><span>
						
			</div>
					
             <div class="col-xs-4" >
					
            <?php 
			//if ($logo) 
			{
    ?>
                <div class="pull-right" style="margin-top:10px;">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>">
                </div>
            <?php
} ?>
			</div>
			</div>
			
		   
		   
		   

		   
       
			
			
			<div class="row">
			<!--<div class="col-xs-1 divbg">
			</div>-->
			<div class="col-xs-12">
			


				
            <?php
			if($start_date&& $end_date){
				$date     = explode(' ', $start_date);
                $start_date = $date[0];
            	$date     = explode(' ', $end_date);
                $end_date = $date[0];
            
			?>
			
			<div class="well well-sm">
                <div class="row bold"  >
                    <div class="col-xs-12">
                    <p class="bold">
                        <?= lang('from'); ?>: <?= $start_date; ?><br>
						<?= lang('to'); ?>: <?= $end_date; ?>
                        
						
                                
                        
                    </p>
                    </div>
					
                    
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
                

            <?php
			}
			?>
			    
				
				
				
		<?php
		
		
		
		if($this->input->get('show_style')==1){
			
			
			
			
			
			
			
			
			
			
			
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
				</tbody>
				
				
				
				</table>
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
</div>
