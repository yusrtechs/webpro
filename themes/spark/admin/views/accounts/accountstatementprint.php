<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
th {
  font-size: 12px;
}
td {
  font-size: 12px;
}

	 .greenColor  {color:green !important;}
		tr td    {-webkit-print-color-adjust:exact !important;}


@media print {

/*
	.greenColor:before,
    .greenColor:after {
        background: transparent !important;
        color: red !important; // Black prints faster: h5bp.com/s
        box-shadow: none !important;
        text-shadow: none !important;
    }
	.
*/

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

				    <span valign="top"><h1  style=" font-size: 25px;">Account statement / كشف حساب</h1><span>

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
			$start_date=$_GET['start_date'];
			$end_date=$_GET['end_date'];
			if($start_date&& $end_date){
				//$date     = explode(' ', $start_date);
                //$start_date = $date[0];
            	//$date     = explode(' ', $end_date);
                //$end_date = $date[0];

			?>

			<div class="well well-sm">
                <div class="row bold"  >
                    <div class="col-xs-12">
                    <p class="bold">

						<?= lang('from'); ?>: <?= ($start_date); ?><br>
						<?= lang('to'); ?>: <?= ($end_date); ?>




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



		//if($this->input->get('show_style')==1)
		{










			foreach ($accountList as $account1){


			$account1 = (array) $account1;
			if($this->input->get('show_style')==2 || $this->input->get('show_style')==3){
				if($_GET['account']){
					if($_GET['account']!=$account1['id']){
				if ($this->accounts_model->IsAccountIsChild($_GET['account'],$account1['parent_id']) == '0'){
					continue;
				}
			}
			}
			}
			$accountSide="";

			//if($accountSide==""){
				$accountInfo = $this->accounts_model->getAccountByID($account1['id']);
				$accountSide=$accountInfo->accountSide;
			//}
			if($account1['id']==1)
				$accountSide="Debit";
			if($account1['id']==2)
				$accountSide="Credit";
			if($account1['id']==3)
				$accountSide="Debit";
			if($account1['id']==4)
				$accountSide="Credit";

			//$accountList1=$this->accounts_model->getAccountChilds($account1['accountID']);
			if($this->input->get('show_style')==2 || $this->input->get('show_style')==3){
				$accountList1=$this->accounts_model->getAccountByAccountIDV2($account1['accountID']);
			}else{
			$accountList1=$this->accounts_model->getAccountChildsWithLevels($account1['accountID'],$levels);
			}



			echo '<h2 class="pull-left">';
				if ($this->Settings->user_language == 'arabic' || $account1['accountNameEn']==''){
				echo $account1['accountName'].'</h2>';
				}else{
				echo $account1['accountNameEn'].'</h2>';

				}

				echo '
				<table class="table table-striped table-bordered table-hover">
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("Sr#").'</th>
				<th>'.lang("Entry_number").'</th>
				<th>'.lang("date").'</th>
				<th>'.lang("reference_no").'</th>
				<th>'.lang("Detailed_account").'</th>
				<th>'.lang("Debit").' ('.((isset($_GET['currency']) && $_GET['currency']!='') ? $_GET['currency'] : $this->Settings->symbol).')</th>
				<th>'.lang("Credit").' ('.((isset($_GET['currency']) && $_GET['currency']!='') ? $_GET['currency'] : $this->Settings->symbol).')</th>
				<th>'.lang("balance").' ('.((isset($_GET['currency']) && $_GET['currency']!='') ? $_GET['currency'] : $this->Settings->symbol).')</th>

				</tr>
				</thead>
				<tbody class="searchable text-center">
				';





				$balanceDebit=0;
				$balanceCredit=0;
				$fbalance=0;
				$sr=0;
				$prev_fbalance=0;

					$root_parent_key=$account1['id'];

				foreach ($accountList1 as $account){

			$account = (array) $account;
			if($this->input->get('show_style')==1 || $this->input->get('show_style')==4){
				if($account1['id']!=$account['id']){
			if ($this->accounts_model->IsAccountIsChild($account1['id'],$account['parent_id']) == '0'){
				continue;
			}
		}
		}
			//if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
			{

			$prev_accountEntries = array();

			$id      = $account['id'];
			$accountBalance = $account['accountDebit'] - $account['accountCredit'];


		foreach($prev_entryList as $prev_entry){
				$prev_entry = (array) $prev_entry;
//
			if (in_array($account['id'], json_decode($prev_entry['entryDebitAccount'])) || in_array($account['id'], json_decode($prev_entry['entryCreditAccount']))){
					array_push($prev_accountEntries, $prev_entry);
				}
			}








			//if (!empty($prev_accountEntries))
			{





				foreach ($prev_accountEntries as $entry){
					$entry = (array) $entry;

					$entryDebitAccounts = array();
					$entryDebitBalances = 0;

					$entryCreditAccounts = array();
					$entryCreditBalances = 0;

					foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
						array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]/((isset($_GET['currency']) && $_GET['currency']!='') ? $entry['currency_rate'] : 1)));
						$entryDebitBalances += 1;
					}

					foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
						array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]/((isset($_GET['currency']) && $_GET['currency']!='') ? $entry['currency_rate'] : 1)));
						$entryCreditBalances += 1;
					}





					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);

						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							//$prev_fbalance=$prev_fbalance+$balance;
							if($accountSide=="Debit")
								$prev_fbalance=$prev_fbalance+$balance;
							else
								$prev_fbalance=$prev_fbalance-$balance;



							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



							$x++;
						}
					}
					foreach ($entryCreditAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							//$prev_fbalance=$prev_fbalance-$balance;
							if($accountSide=="Credit")
								$prev_fbalance=$prev_fbalance+$balance;
							else
								$prev_fbalance=$prev_fbalance-$balance;





							$y++;
						}
					}







				}


				}
				}
		}
		//$this->sma->print_arrays($prev_fbalance);
	//$this->data['prev_fbalance']  = $prev_fbalance;



						if($prev_fbalance){

						$sr++;
					if($this->input->get('show_style')==0 || $this->input->get('show_style')==2){
						echo '
						<tr style="display:none;">';
					}else{
						echo '
						<tr>';
					}
					echo '
					<td class="text-left align-middle">'.$sr.'</td>

					<td class="align-middle">';

				echo '</td>

					<td class="text-left align-middle">'.lang('Before').' '.(isset($start_date) ? $this->sma->hrsd($start_date) : '').'</td><td></td>
					<td class="text-left align-middle">'.lang('Carry-over_balance').'</td>
					<td class="align-middle">';


						//if ($prev_fbalance >= 0){
						if($accountSide=="Debit"){
							echo '<strong>'.$this->sma->formatMoney($prev_fbalance,' ').'</strong><br />';
							$balanceDebit=$balanceDebit+$prev_fbalance;
							$fbalance=$fbalance+$prev_fbalance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];

				            /*$accountDebitBalance = $accountInfo->accountDebit;


							$accountInfoDebitBalance = array('accountDebit' => ($balance + $accountDebitBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoDebitBalance);*/

						}
					echo '			</td>
					<td class="align-middle">';
						//if ($prev_fbalance < 0){
						if($accountSide=="Credit"){
							echo '<strong>'.$this->sma->formatMoney(abs($prev_fbalance),' ').'</strong><br />';
							$balanceCredit=$balanceCredit+$prev_fbalance;
							$fbalance=$fbalance+$prev_fbalance;


							/*$accountCreditBalance = $accountInfo->accountCredit;


							$accountInfoCreditBalance = array('accountCredit' => ($balance + $accountCreditBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoCreditBalance);*/


						}




					if($accountSide=="Debit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle" ><div class="greenColor"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></div></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red !important;-webkit-print-color-adjust:exact;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
					}

					if($accountSide=="Credit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle greenColor" style="color:green !important;-webkit-print-color-adjust:exact;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red !important;-webkit-print-color-adjust:exact;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
					}


					}


			foreach ($accountList1 as $account){

			$account = (array) $account;
			if($this->input->get('show_style')==1 || $this->input->get('show_style')==4){
				if($account1['id']!=$account['id']){
			if ($this->accounts_model->IsAccountIsChild($account1['id'],$account['parent_id']) == '0'){
				continue;
			}
		}
		}
			$accountEntries = array();

			//if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
			{




			$id      = $account['id'];
			$accountBalance = $account['accountDebit'] - $account['accountCredit'];


			foreach($entryList as $entry){
				$entry = (array) $entry;
				if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
					array_push($accountEntries, $entry);
				}
			}
			//

			//if (!empty($accountEntries))
			{
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
				$accountName= $account['accountName'];
				}else{
				$accountName=$account['accountNameEn'];

				}



				foreach ($accountEntries as $entry){
					$entry = (array) $entry;

					$entryDebitAccounts = array();
					$entryDebitBalances = 0;

					$entryCreditAccounts = array();
					$entryCreditBalances = 0;

					$entryDescriptionText = "";


					foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
						array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]/((isset($_GET['currency']) && $_GET['currency']!='') ? $entry['currency_rate'] : 1)));
						if(json_decode($entry['entryDebitAccount'])[$entryDebitBalances]== $id){
							if($entryDescriptionText=="")
								$entryDescriptionText = json_decode($entry['entryDebitDescription'])[$entryDebitBalances];
							else
							$entryDescriptionText = $entryDescriptionText. " <br> " .json_decode($entry['entryDebitDescription'])[$entryDebitBalances];

						}
						$entryDebitBalances += 1;
					}

					foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
						array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]/((isset($_GET['currency']) && $_GET['currency']!='') ? $entry['currency_rate'] : 1)));
						if(json_decode($entry['entryCreditAccount'])[$entryCreditBalances]== $id){
							if($entryDescriptionText=="")
								$entryDescriptionText = json_decode($entry['entryCreditDescription'])[$entryCreditBalances];
							else
							$entryDescriptionText = $entryDescriptionText. " <br> " .json_decode($entry['entryCreditDescription'])[$entryCreditBalances];

						}
						$entryCreditBalances += 1;
					}


					$entryDescriptionTextSearch=$entryDescriptionText;

					if($entryDescriptionTextSearch==""){
						$entryDescriptionTextSearch=$entry['entryDescription'];
						$entryDescription= str_replace("<p>","",($entryDescriptionTextSearch)."");
						$entryDescription= str_replace("</p>","",($entryDescription)."");
						$iof=strpos(trim($entryDescription), "#");
						if($iof>-1)
							$entryDescriptionTextSearch=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);
					}

						if($entryDescriptionTextSearch=="")
							$entryDescriptionTextSearch=$entry['entryType'];




// || strpos(lang($entry['entryDescription']), $_GET['search_description'])==-1
//echo strpos(lang($entryDescriptionTextSearch), $_GET['search_description'])."ppp";
					$sr++;
					if($this->input->get('show_style')==0 || $this->input->get('show_style')==2  || (!is_numeric(strpos(lang($entryDescriptionTextSearch), $_GET['search_description'])) && trim($_GET['search_description'])!='')){
						echo '
						<tr style="display:none;">';
					}else{
						echo '
						<tr>';
					}
					echo '
					<td class="text-left align-middle">'.$sr.'</td>

					<td class="align-middle">';
					?>
				<a href="<?php echo admin_url('accounts/view_entries/'.$entry['entryID']); ?>" data-toggle="modal" data-target="#myModal">
					<?= '#'.($entry['entry_no'] ? $entry['entry_no'] : $entry['entryID']) ?>
                </a>

				<?php
				echo '</td>

					<td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td><td class="text-left align-middle">'.$entry['reference_no'].'</td>';
					if($entry['entryType']!="Opening_balance"){
						if($entryDescriptionText=="")
						$entryDescriptionText=$entry['entryDescription'];


						$entryDescription= str_replace("<p>","",($entryDescriptionText)."");
						$entryDescription= str_replace("</p>","",($entryDescription)."");
							$iof=strpos(trim($entryDescription), "#");
						if($iof>-1)
							$entryDescription=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);

					echo '
					<td class="text-left align-middle" style="white-space: normal; word-break: break-word;">'.lang($entryDescription).'</td>
					<td class="align-middle">';
				}else{
					echo '
					<td class="text-left align-middle">'.lang($entry['entryType']).'</td>
					<td class="align-middle">';

				}


					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);

						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
							$balanceDebit=$balanceDebit+$balance;
							//$fbalance=$fbalance+$balance;
							if($accountSide=="Debit")
								$fbalance=$fbalance+$balance;
							else
								$fbalance=$fbalance-$balance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];

				            /*$accountDebitBalance = $accountInfo->accountDebit;


							$accountInfoDebitBalance = array('accountDebit' => ($balance + $accountDebitBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoDebitBalance);*/

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
							echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
							$balanceCredit=$balanceCredit+$balance;
							//$fbalance=$fbalance-$balance;
							if($accountSide=="Credit")
								$fbalance=$fbalance+$balance;
							else
								$fbalance=$fbalance-$balance;


							/*$accountCreditBalance = $accountInfo->accountCredit;


							$accountInfoCreditBalance = array('accountCredit' => ($balance + $accountCreditBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoCreditBalance);*/

							$y++;
						}
					}



					if($accountSide=="Debit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle greenColor" style="color:green !important;-webkit-print-color-adjust:exact !important;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red !important;-webkit-print-color-adjust:exact !important;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
					}

					if($accountSide=="Credit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle greenColor" style="color:green !important;-webkit-print-color-adjust:exact; !important"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red !important;-webkit-print-color-adjust:exact !important;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
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


				echo '
				</tbody>
				<tfoot class="thead-dark">';
				if($diffDebit!=0 || $diffCredit!=0){
					if($diffDebit!=0)
						$diffDebit=$this->sma->formatMoney($diffDebit,' ');
					else
						$diffDebit='';

				if($diffCredit!=0)
						$diffCredit=$this->sma->formatMoney($diffCredit,' ');
					else
						$diffCredit='';



				/*echo '
				<tr class="text-center">
				<td colspan="4"><strong>'.lang("balance").'</strong></td>';
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
					*/
				}


				echo '
				<tr class="text-center">
				<td colspan="5"><strong style="font-size:18px;">'.lang("total").'</strong></td>
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceDebit,' ').'</strong></td>
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceCredit,' ').'</strong></td>';


				if($accountSide=="Debit"){
						if($fbalance>0){

				echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:green !important;-webkit-print-color-adjust:exact !important;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
						}else{
				echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:red !important;-webkit-print-color-adjust:exact !important;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

						}
					}

					if($accountSide=="Credit"){
						if($fbalance>0){

				echo '
				<td class="green" style="text-decoration: underline;  border-top: 2px solid #000000;color:green !important;-webkit-print-color-adjust:exact !important;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
						}else{
					echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:red !important;-webkit-print-color-adjust:exact !important;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

						}
					}




				echo '
				</tr>
				</tfoot></table>
				';
		}












		}

		/*else{




			/////////////////


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

			//if (!empty($accountEntries))
			{
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
				<div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table" >
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("Sr#").'</th>
				<th>'.lang("Entry_number").'</th>
				<th>'.lang("date").'</th>
				<th>'.lang("description").'</th>
				<th>'.lang("Debit").'</th>
				<th>'.lang("Credit").'</th>
				<th>'.lang("balance").'</th>

				</tr>
				</thead>
				<tbody class="searchable text-center">
				';





				$balanceDebit=0;
				$balanceCredit=0;
				$fbalance=0;
				$sr=0;


				foreach ($accountList as $account){

			$account = (array) $account;
			$prev_accountEntries = array();

			$id      = $account['id'];
			$accountBalance = $account['accountDebit'] - $account['accountCredit'];


		foreach($prev_entryList as $prev_entry){
				$prev_entry = (array) $prev_entry;
//
			if (in_array($account['id'], json_decode($prev_entry['entryDebitAccount'])) || in_array($account['id'], json_decode($prev_entry['entryCreditAccount']))){
					array_push($prev_accountEntries, $prev_entry);
				}
			}








			//if (!empty($prev_accountEntries))
			{




				$prev_fbalance=0;

				foreach ($prev_accountEntries as $entry){
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





					foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);

						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							$prev_fbalance=$prev_fbalance+$balance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



							$x++;
						}
					}
					foreach ($entryCreditAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							$prev_fbalance=$prev_fbalance-$balance;




							$y++;
						}
					}







				}


				}
		}
		//$this->sma->print_arrays($prev_fbalance);
	//$this->data['prev_fbalance']  = $prev_fbalance;



						if($prev_fbalance){

						$sr++;
					echo '
					<tr>
					<td class="text-left align-middle">'.$sr.'</td>

					<td class="align-middle">';

				echo '</td>

					<td class="text-left align-middle">'.lang('Before').' '.(isset($start_date) ? $this->sma->hrsd($start_date) : '').'</td>
					<td class="text-left align-middle">'.lang('Carry-over_balance').'</td>
					<td class="align-middle">';


						if ($prev_fbalance >= 0){
							echo '<strong>'.$this->sma->formatMoney($prev_fbalance).'</strong><br />';
							$balanceDebit=$balanceDebit+$prev_fbalance;
							$fbalance=$fbalance+$prev_fbalance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



						}
					echo '			</td>
					<td class="align-middle">';
						if ($prev_fbalance < 0){
							echo '<strong>'.$this->sma->formatMoney(abs($prev_fbalance)).'</strong><br />';
							$balanceCredit=$balanceCredit-$prev_fbalance;
							$fbalance=$fbalance+$prev_fbalance;




						}




					echo '			</td>

					<td class="text-left align-middle"><strong>'.$this->sma->formatMoney($fbalance).'</strong></td>



					</tr>
					';

					}


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






					$sr++;
					echo '
					<tr>
					<td class="text-left align-middle">'.$sr.'</td>

					<td class="align-middle">';
					?>
				<a href="<?php echo admin_url('accounts/view_entries/'.$entry['entryID']); ?>" data-toggle="modal" data-target="#myModal">
                                <?= '#'.$entry['entryID'] ?>
                            </a>

				<?php
				if($entry['entryType']!="Opening_balance"){
				echo '</td>

					<td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td>

					<td class="text-left align-middle">'.lang($entry['entryDescription']).'</td>
					<td class="align-middle">';
				}else{
				echo '</td>

					<td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td>

					<td class="text-left align-middle">'.lang($entry['entryType']).'</td>
					<td class="align-middle">';

				}

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
							$fbalance=$fbalance+$balance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



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
							$fbalance=$fbalance-$balance;




							$y++;
						}
					}



					echo '			</td>

					<td class="text-left align-middle"><strong>'.$this->sma->formatMoney($fbalance).'</strong></td>



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
				<td colspan="4"><strong>'.lang("total").'</strong></td>
				<td style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($balanceDebit).'</strong></td>
				<td style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($balanceCredit).'</strong></td>
				<td></td>

				</tr>';



				}


				echo '</tfoot></table>
				';
			}
		}



		}*/

		?>
	</div>




        </div>
    </div>
</div>
</div>
