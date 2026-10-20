<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
 table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }
</style>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<?php


if (empty($_POST)){
	$_POST['brId']=$this->session->userdata('brId');
	//
$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
	if ($check_period || $check_period->period_status==1){
		$_POST['start_date']=$this->sma->hrsd($check_period->period_from_date);
		$_POST['end_date']=$this->sma->hrsd($check_period->period_to_date);
	}
}

$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

  if ($this->input->post('grpId')) {
    $v .= '&grpId=' . $this->input->post('grpId');
}
if ($this->input->post('coId')) {
    $v .= '&coId=' . $this->input->post('coId');
}
if ($this->input->post('brId')) {
    $v .= '&brId=' . $this->input->post('brId');
}


  if ($this->input->post('Carry_over_balance')) {
    $v .= '&Carry_over_balance=' . $this->input->post('Carry_over_balance');
}
if ($this->input->post('show_style')) {
    $v .= '&show_style=' . $this->input->post('show_style');
}
if ($this->input->post('show_Opening_balance')) {
    $v .= '&show_Opening_balance=' . $this->input->post('show_Opening_balance');
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
if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
if ($this->input->post('salesman')) {
	//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
		$v .= '&salesman=' . $this->input->post('salesman');
	}//else

	if ($this->input->post('search_description')) {
		//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
			$v .= '&search_description=' . $this->input->post('search_description');
		}
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
		<?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		localStorage.setItem('glgrpId', '<?=  $this->session->userdata('grpId') ?>');
				<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

		localStorage.setItem('glcoId', '<?=  $this->session->userdata('coId') ?>');
				<?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

localStorage.setItem('glgrpId', '<?=  $this->session->userdata('grpId') ?>');
localStorage.setItem('glcoId', '<?=  $this->session->userdata('coId') ?>');
localStorage.setItem('glbrId', '<?=  $this->session->userdata('brId') ?>');
				<?php
						}
                    }
						?>
        <?php if (!$this->input->post('submit_report') && !$this->input->post('account') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
            $('#form').hide();
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideToggle(200);
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp(200);
            return false;
        });
    });
</script>

<div class="spark-collection-report-page">
    <div class="spark-collection-report-header">
        <div class="spark-collection-report-header-start">
            <div class="spark-collection-report-header-icon">
                <i class="fa fa-money"></i>
            </div>
            <div class="spark-collection-report-header-info">
                <h1 class="spark-collection-report-title">
                    <?= lang('Collection_report') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-collection-report-subtitle">
                    <?= lang('manage_collection_report_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-collection-report-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <button type="button" id="pdf" class="spark-btn-secondary-action tip" title="<?= lang('download_pdf') ?>">
                <i class="fa fa-file-pdf-o"></i>
                <span><?= lang('download_pdf'); ?></span>
            </button>

        </div>
    </div>

<div class="box">

    <div class="box-content">

	<div class="row">
	<div class="col-sm-12">

	<div id="form">

                    <?php echo admin_form_open('accounts/collection_report',' autocomplete="off"'); ?>
                    <div class="col-sm-12">


						<input type="hidden" name="id" value="<?= $filter_id ?>" id="filter_id"/>

						<?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{

                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
												if ($Owner || $Admin){
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>


                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="account"><?= lang('Account'); ?></label>
                                <?php
                                //$wh[''] = $this->lang->line('select') . ' ' . $this->lang->line('Account');
                                //$wh[''] = $this->lang->line('all');
                                foreach ($accounts as $account) {
									$account = (array) $account;
                                    if($account['id']==$parent_account_details){
                                        $wh[$parent_account_details] = $this->lang->line('all');
                                    }else{
                                    if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
				$wh[$account['id']] = $account['accountID']." - ".$account['accountName'];
				}else{
				$wh[$account['id']] = $account['accountID']." - ".$account['accountNameEn'];

				}
                                }
                            }
                                echo form_dropdown('account', $wh, (isset($_POST['account']) ? $_POST['account'] : $filter_id), 'class="form-control" id="account" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Account') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="salesman"><?= lang('Salesman'); ?></label>
                                <?php
                                $salM[''] = lang('select') . ' ' . lang('Salesman');

                                foreach ($allSalesMan as $salesman) {
                                $salM[$salesman->id] = $salesman->company;
                               }
                                echo form_dropdown('salesman', $salM, (isset($_POST['salesman']) ? $_POST['salesman'] : ''), 'class="form-control" id="salesman" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Salesman') . '"');
                                ?>
                            </div>
                        </div>


                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="show_style"><?= lang('Show_style'); ?></label>
                                <?php
                                $ss['1'] = lang('Detailed');
                                $ss['3'] = lang('Detailed')." - ".lang('Aggregation');
                                $ss['0'] = lang('Aggregation');
								$ss['2'] = lang('Aggregation')." - ".lang('Detailed');

                                echo form_dropdown('show_style', $ss, (isset($_POST['show_style']) ? $_POST['show_style'] : ''), 'class="form-control" id="show_style" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_style') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="show_Opening_balance"><?= lang('Show_opening_balance'); ?></label>
                                <?php
                                $ss1['0'] = lang('no');
                                $ss1['1'] = lang('yes');

                                echo form_dropdown('show_Opening_balance', $ss1, (isset($_POST['show_Opening_balance']) ? $_POST['show_Opening_balance'] : '0'), 'class="form-control" id="show_Opening_balance" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_opening_balance') . '"');
                                ?>
                            </div>
                        </div>

						<div class="col-sm-2" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="Carry_over_balance"><?= lang('Carry-over_balance'); ?></label>
                                <?php
                                $ss2['0'] = lang('no');
                                $ss2['1'] = lang('yes');

                                echo form_dropdown('Carry_over_balance', $ss2, (isset($_POST['Carry_over_balance']) ? $_POST['Carry_over_balance'] : ''), 'class="form-control" id="Carry_over_balance" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Carry-over_balance') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'readonly class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'readonly class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('Search_for_a_description', 'Search_for_a_description'); ?>
                                <?php echo form_input('search_description', (isset($_POST['search_description']) ? $_POST['search_description'] : ''), 'class="form-control " id="search_description"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
				<!--<div class="col-lg-8">
		<p>
			<?= lang('list_results'); ?>



		</p>
		</div>


		<div class="col-md-4" >
                                    <div class="form-group">
                                        <?php echo form_input('filter', '', 'class="form-control input-tip" id="filter" placeholder="' . lang('Search') . '"  '); ?>
                                    </div>
        </div>-->


	</div>
	</div>



	<div class="row"  id="pr_pdf">
		<div class="col-sm-12 col-md-12">

		<div class="table-responsive">


		<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl" id="pr_pdf1">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Collection_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Collection_report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>
				</div>
			</div>
		</div>


        <div class="row" id="accountstatment">
            <div class="col-lg-12">








		<?php

		$accountSide="";

		$accountInfo = $this->accounts_model->getAccountByID($_POST['account']);
		$accountSide=$accountInfo->accountSide;

		if($accountSide==""){
			if($_POST['account']==1)
				$accountSide="Debit";
			if($_POST['account']==2)
				$accountSide="Credit";
			if($_POST['account']==3)
				$accountSide="Debit";
			if($_POST['account']==4)
				$accountSide="Credit";

		}
		if(!isset($_POST['account']))
				unset($accountList);

		//if($this->input->post('show_style')==1)
		{








			//$this->sma->print_arrays($accountList);

			//print_r($accountList);
			foreach ($accountList as $account1){





			$account1 = (array) $account1;
			if($this->input->post('show_style')==2 || $this->input->post('show_style')==3){
				if($_POST['account']){
					if($_POST['account']!=$account1['id']){
				if ($this->accounts_model->IsAccountIsChild($_POST['account'],$account1['parent_id']) == '0'){
					continue;
				}
			}
		}
			}

			$accountSide="";

			if($accountSide==""){
				$accountInfo = $this->accounts_model->getAccountByID($account1['id']);
				$accountSide=$accountInfo->accountSide;
			}
			//if($accountSide==""){
			if($account1['id']==1)
				$accountSide="Debit";
			if($account1['id']==2)
				$accountSide="Credit";
			if($account1['id']==3)
				$accountSide="Debit";
			if($account1['id']==4)
				$accountSide="Credit";
			//}

			//echo $account1['id']." ".$accountSide."<br/>";


			//$accountList1=$this->accounts_model->getAccountChilds($account1['accountID']);
				if($this->input->post('show_style')==2 || $this->input->post('show_style')==3){
					$accountList1=$this->accounts_model->getAccountByAccountIDV2($account1['accountID']);

				}else{
			$accountList1=$this->accounts_model->getAccountChildsWithLevels($account1['accountID'],$levels);

			//$this->sma->print_arrays($accountList1);
				}



			//$accountList1 = $this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID);//
			//$this->sma->print_arrays($accountList1);



				echo '
				<table class="table table-striped table-bordered table-hover">
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("Sr#").'</th>
				<th>'.lang("Entry_number").'</th>
				<th>'.lang("date").'</th>
				<th class="col-xs-6">'.lang("Detailed_account").'</th>
				<th class="col-xs-2">'.lang("Salesman").'</th>
				<th>'.lang("reference_no").'</th>
				<th>'.lang("Amount").' ('.$this->Settings->symbol.')</th>

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
			//$prev_fbalance=0;

			$account = (array) $account;
			//if($account['accountID']=='3101001'){
				//$this->accounts_model->IsAccountIsChild(73,['parent_id']);
				//$this->sma->print_arrays($account['parent_id']);
				if($this->input->post('show_style')==1 || $this->input->post('show_style')==4){
					if($account1['id']!=$account['id']){
				 if ($this->accounts_model->IsAccountIsChild($account1['id'],$account['parent_id']) == '0'){
					 	continue;
					 }
					}
					}
			//$this->sma->print_arrays();

			//}
			//if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
			{
			$prev_accountEntries = array();

			$id      = $account['id'];
			$accountBalance = $account['accountDebit'] - $account['accountCredit'];


		foreach($prev_entryList as $prev_entry){
				$prev_entry = (array) $prev_entry;

			if (in_array($account['id'], json_decode($prev_entry['entryDebitAccount'])) || in_array($account['id'], json_decode($prev_entry['entryCreditAccount']))){
					array_push($prev_accountEntries, $prev_entry);
				}
			}







			//if (!empty($prev_accountEntries))
			{

			//print_r($prev_accountEntries);




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





					//print_r($entryDebitAccounts);
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
						//echo $balance;



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
					if($this->input->post('show_style')==0 || $this->input->post('show_style')==2){
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

					<td class="text-left align-middle">'.lang('Before').' '.(isset($_POST['start_date']) ? $_POST['start_date'] : '').'</td>
					<td class="text-left align-middle">'.lang('Carry-over_balance').'</td>
					<td class="align-middle">';

					//echo $prev_fbalance."pppp";
						if($accountSide=="Debit"){
							echo '<strong>'.$this->sma->formatMoney(abs($prev_fbalance),' ').'</strong><br />';
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

					<td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
					}

					if($accountSide=="Credit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
					}



					}

                    //$this->sma->print_arrays($accountList1);
			foreach ($accountList1 as $account){

			$account = (array) $account;
			if($this->input->post('show_style')==1 || $this->input->post('show_style')==4){
			if($account1['id']!=$account['id']){
			if ($this->accounts_model->IsAccountIsChild($account1['id'],$account['parent_id']) == '0'){
				continue;
			}
		}
		}
			$accountEntries_cust = array();


			//if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
			{



			$id      = $account['id'];
			//$accountBalance = $account['accountDebit'] - $account['accountCredit'];


			foreach($entryList as $entry){
				$entry = (array) $entry;
				if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
                            array_push($accountEntries_cust, $entry);

				}
			}


			/*$i=sizeof($payment_types_boxes);
			foreach ($payment_types_banks as $payment_type) {
				$payment_types_boxes[$i]=$payment_type;
				$i++;
			}*/
            //$this->sma->print_arrays($payment_types_boxes);

			$accountEntries = array();

            foreach($accountEntries_cust as $entry){
				$entry = (array) $entry;
				    foreach ($payment_types_boxes as $payment_type) {
                        $payment_type = (array) $payment_type;
                        if (in_array($payment_type['id'], json_decode($entry['entryDebitAccount'])) || in_array($payment_type['id'], json_decode($entry['entryCreditAccount']))){
                            if($salesman_id){

								if($entry['entryType']=='Catch_receipts'){
									$salesman=$this->accounts_model->getCatchReceiptByBondNo($entry['reference_no']);
									$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
								}else if($entry['entryType']=='Sales_Payments' || $entry['entryType']=='sale_completed' || $entry['entryType']=='sale_pending' || $entry['entryType']=='sale_returned'){
									$salesman=$this->accounts_model->getSaleByReference($entry['reference_no']);
									$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
								}else{
									$salesman_ref_id=null;
								}

								if($salesman_ref_id &&($salesman_ref_id==$salesman_id))
								array_push($accountEntries, $entry);
							}else{
								array_push($accountEntries, $entry);
							}

                        }
                    }
			}

            foreach($accountEntries_cust as $entry){
				$entry = (array) $entry;
				    foreach ($payment_types_banks as $payment_type) {
                        $payment_type = (array) $payment_type;
                        if (in_array($payment_type['id'], json_decode($entry['entryDebitAccount'])) || in_array($payment_type['id'], json_decode($entry['entryCreditAccount']))){

							if($salesman_id){

								if($entry['entryType']=='Catch_receipts'){
									$salesman=$this->accounts_model->getCatchReceiptByBondNo($entry['reference_no']);
									$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
								}else if($entry['entryType']=='Sales_Payments' || $entry['entryType']=='sale_completed' || $entry['entryType']=='sale_pending' || $entry['entryType']=='sale_returned'){
									$salesman=$this->accounts_model->getSaleByReference($entry['reference_no']);
									$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
								}else{
									$salesman_ref_id=null;
								}

								if($salesman_ref_id &&($salesman_ref_id==$salesman_id))
								array_push($accountEntries, $entry);
							}else{
								array_push($accountEntries, $entry);
							}
                        }
                    }
			}

            //$this->sma->print_arrays($accountEntries);

			//
			//print_r($accountEntries);

			if (!empty($accountEntries))
			{
             //   $this->sma->print_arrays($accountEntries);
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
						array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
						if(json_decode($entry['entryDebitAccount'])[$entryDebitBalances]== $id){
							if($entryDescriptionText=="")
								$entryDescriptionText = json_decode($entry['entryDebitDescription'])[$entryDebitBalances];
							else
							$entryDescriptionText = $entryDescriptionText. " - " .json_decode($entry['entryDebitDescription'])[$entryDebitBalances];

						}
						$entryDebitBalances += 1;
					}

					foreach (json_decode($entry['entryCreditAccount']) as $creditAccount){
						array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
						if(json_decode($entry['entryCreditAccount'])[$entryCreditBalances]== $id){
							if($entryDescriptionText=="")
								$entryDescriptionText = json_decode($entry['entryCreditDescription'])[$entryCreditBalances];
							else
							$entryDescriptionText = $entryDescriptionText. " - " .json_decode($entry['entryCreditDescription'])[$entryCreditBalances];

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




// || strpos(lang($entry['entryDescription']), $_POST['search_description'])==-1
//echo strpos(lang($entryDescriptionTextSearch), $_POST['search_description'])."ppp";
					$sr++;
					if($this->input->post('show_style')==0 || $this->input->post('show_style')==2  || (!is_numeric(strpos(lang($entryDescriptionTextSearch), $_POST['search_description'])) && trim($_POST['search_description'])!='')){
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

					<td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td>';
					if($entry['entryType']!="Opening_balance"){
						if($entryDescriptionText=="")
						$entryDescriptionText=$entry['entryDescription'];

						$entryDescription= str_replace("<p>","",($entryDescriptionText)."");
						$entryDescription= str_replace("</p>","",($entryDescription)."");
						$iof=strpos(trim($entryDescription), "#");
						if($iof>-1)
							$entryDescription=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);
					echo '
					<td class="text-left align-middle">'.$accountName.'-'.lang($entryDescription).'</td>
					<td class="align-middle">';
				}else{
					echo '
					<td class="text-left align-middle">'.$accountName.'-'.lang($entry['entryType']).'</td>
					<td class="align-middle">';

				}
				$salesman_name='';
				$reference_no='';
				if($entry['entryType']=='Catch_receipts'){
					$salesman=$this->accounts_model->getCatchReceiptByBondNo($entry['reference_no']);
					$salesman_name=$salesman ? $salesman->salesman_name : '';
					$reference_no=$salesman ? $salesman->reference_no : '';
				}else if($entry['entryType']=='Sales_Payments' || $entry['entryType']=='sale_completed' || $entry['entryType']=='sale_pending' || $entry['entryType']=='sale_returned'){
					$salesman=$this->accounts_model->getSaleByReference($entry['reference_no']);
					$salesman_name=$salesman ? $this->site->getCompanyByID($salesman->salesman_id)->company : '';
					$reference_no=$salesman ? $salesman->reference_no : '';
				}

				echo $salesman_name.'</td><td class="text-left align-middle">'.$reference_no.'</td><td class="align-middle">';

					/*foreach ($entryDebitAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);
						//print_r($accountInfo->id);



						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
							$balanceDebit=$balanceDebit+$balance;
							if($accountSide=="Debit")
								$fbalance=$fbalance+$balance;
							else
								$fbalance=$fbalance-$balance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



							$x++;
						}
					}
					echo '			</td>
					<td class="align-middle">';*/
					foreach ($entryCreditAccounts as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getAccountByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
							$balanceCredit=$balanceCredit+$balance;
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

					/*if($accountSide=="Debit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
					}

					if($accountSide=="Credit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';

						}
					}*/






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
				*/

				echo '
				<td></td>

				</tr>';

				}


				echo '
				<tr class="text-center">
				<td colspan="6"><strong style="font-size:18px;">'.lang("total").'</strong></td>
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceCredit,' ').'</strong></td>';


				/*if($accountSide=="Debit"){
						if($fbalance>0){

				echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:green;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
						}else{
				echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:red;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

						}
					}

					if($accountSide=="Credit"){
						if($fbalance>0){

				echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:green;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
						}else{
					echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:red;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';

						}
					}*/




				echo '
				</tr>
				</tfoot></table>
				';
		}












		}
		/*else{



			////////////////////////////////////////////////////
















		foreach ($accountList as $account){

			$account = (array) $account;
			$accountEntries = array();
			$prev_accountEntries = array();

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
				<table class="table table-striped table-bordered table-hover">
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
		//print_r($prev_fbalance);
	//$this->data['prev_fbalance']  = $prev_fbalance;



						if($prev_fbalance){

						$sr++;
					echo '
					<tr>
					<td class="text-left align-middle">'.$sr.'</td>

					<td class="align-middle">';

				echo '</td>

					<td class="text-left align-middle">'.lang('Before').' '.(isset($_POST['start_date']) ? $_POST['start_date'] : '').'</td>
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




		//print();



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
</div>
<script type="text/javascript">
        $(document).ready(function () {
            $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Collection_report'); ?>','L');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/collection_report/0/xls/?v=' . $v)?>";
            return false;
        });

		 $('#print').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('accounts/collection_reportprint/?' . $v) ?>";
                return false;

				//PrintElem('accountstatment);
            });



        });
    </script>

