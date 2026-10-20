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
if ($this->input->post('show_style')) {
    $v .= '&show_style=' . $this->input->post('show_style');
}
if ($this->input->post('show_Opening_balance')) {
    $v .= '&show_Opening_balance=' . $this->input->post('show_Opening_balance');
}

if ($this->input->post('costcenter')) {
    $v .= '&costcenter=' . $this->input->post('costcenter');
}else{
	$v .= '&costcenter=' .$filter_id;
}
if ($this->input->post('account')) {
    $v .= '&account=' . $this->input->post('account');
}else{
	$v .= '&account=' .$filter_id1;
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

<div class="spark-distributioncostcenters-page">
    <div class="spark-distributioncostcenters-header">
        <div class="spark-distributioncostcenters-header-start">
            <div class="spark-distributioncostcenters-header-icon">
                <i class="fa fa-sitemap"></i>
            </div>
            <div class="spark-distributioncostcenters-header-info">
                <h1 class="spark-distributioncostcenters-title">
                    <?= lang('Cost_centers_Report') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-distributioncostcenters-subtitle">
                    <?= lang('manage_distributioncostcenters_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-distributioncostcenters-header-actions">
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

                    <?php echo admin_form_open('accounts/distributioncostcenters',' autocomplete="off"'); ?>
                    <div class="col-sm-12">


						<input type="hidden" name="id" value="<?= $filter_id ?>" id="filter_id"/>
                        <input type="hidden" name="id1" value="<?= $filter_id1 ?>" id="filter_id1"/>

						<?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
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

                                    <div class="col-sm-3">
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
                                        <div class="col-sm-3">
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

                        <div class="col-sm-2" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang('reference_no'); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>


                        <div class="col-sm-2" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang('customer'); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2" style="display:none;">
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
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="costcenter"><?= lang('Cost_center'); ?></label>
                                <?php
                                //$wh[''] = $this->lang->line('select') . ' ' . $this->lang->line('Account');
                                $cc[''] = $this->lang->line('all');
                                foreach ($costcenters as $account) {
									$account = (array) $account;
                                    if ($this->Settings->user_language == 'arabic' || $account['costNameEn']==''){
				$cc[$account['id']] = $account['costName'];
				}else{
				$cc[$account['id']] = $account['costNameEn'];

				}
                                }
                                echo form_dropdown('costcenter', $cc, (isset($_POST['costcenter']) ? $_POST['costcenter'] : $filter_id), 'class="form-control" id="costcenter" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Account') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="account"><?= lang('Account'); ?></label>
                                <?php
                                //$wh[''] = $this->lang->line('select') . ' ' . $this->lang->line('Account');
                                $wh[''] = $this->lang->line('all');
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

						<div class="col-sm-2" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="show_style"><?= lang('Show_style'); ?></label>
                                <?php
                                $ss['1'] = lang('Detailed');
                                $ss['0'] = lang('Aggregation');

                                echo form_dropdown('show_style', $ss, (isset($_POST['show_style']) ? $_POST['show_style'] : ''), 'class="form-control" id="show_style" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_style') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-2" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="show_Opening_balance"><?= lang('Show_opening_balance'); ?></label>
                                <?php
                                $ss['0'] = lang('no');
                                $ss['1'] = lang('yes');

                                echo form_dropdown('show_Opening_balance', $ss, (isset($_POST['show_Opening_balance']) ? $_POST['show_Opening_balance'] : ''), 'class="form-control" id="show_Opening_balance" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_opening_balance') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Cost_centers_Report'); ?></font></b></td>
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
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Cost_centers_Report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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

		$accountSide="Credit";

		//$accountInfo = $this->accounts_model->getCostCentersByID($_POST['costcenter']);

		//$accountSide=$accountInfo->accountSide;

		/*if($accountSide==""){
			if($_POST['account']==1)
				$accountSide="Debit";
			if($_POST['account']==2)
				$accountSide="Credit";
			if($_POST['account']==3)
				$accountSide="Debit";
			if($_POST['account']==4)
				$accountSide="Credit";

		}*/
		if(!isset($_POST['costcenter']))
				unset($accountList);

		//if($this->input->post('show_style')==1)
		{








			foreach ($accountList as $account1){




			$account1 = (array) $account1;
			//$accountSide="";

			if($accountSide=="Credit"){
				$accountInfo = $this->accounts_model->getCostCentersByID($account1['id']);
				//$accountSide=$accountInfo->accountSide;
			}
			/*//if($accountSide==""){
			if($account1['id']==1)
				$accountSide="Debit";
			if($account1['id']==2)
				$accountSide="Credit";
			if($account1['id']==3)
				$accountSide="Debit";
			if($account1['id']==4)
				$accountSide="Credit";
			//}
			*/
			//echo $account1['id']." ".$accountSide."<br/>";


			$accountList1=$this->accounts_model->getCostCenterChilds($account1['costID'],$account1['parent_id']);
			//$accountList1 = $this->accounts_model->getCostCenterChilds($this->accounts_model->getCostCentersByID($account)->costID);//




			echo '<h2 class="pull-left">';
				if ($this->Settings->user_language == 'arabic' || $account1['costNameEn']==''){
				echo $account1['costName'].'</h2>';
				}else{
				echo $account1['costNameEn'].'</h2>';

				}

				echo '
				<table class="table table-striped table-bordered table-hover">
				<thead class="thead-dark">
				<tr class="text-center">
				<th>'.lang("Sr#").'</th>
				<th>'.lang("Entry_number").'</th>
				<th>'.lang("date").'</th>
				<th>'.lang("Account").' - '.lang("description").'</th>
				<th>'.lang("Debit").' ('.$this->Settings->symbol.')</th>
				<th>'.lang("Credit").' ('.$this->Settings->symbol.')</th>
				<th>'.lang("balance").' ('.$this->Settings->symbol.')</th>

				</tr>
				</thead>
				<tbody class="searchable text-center">
				';





				$balanceDebit=0;
				$balanceCredit=0;
				$fbalance=0;
				$sr=0;

				$prev_fbalance=0;


				/*
				foreach ($accountList1 as $account){
			//$prev_fbalance=0;

			$account = (array) $account;

			$prev_accountEntries = array();

			$id      = $account['id'];
			//$accountBalance = $account['accountDebit'] - $account['accountCredit'];


		foreach($prev_entryList as $prev_entry){
				$prev_entry = (array) $prev_entry;

			//$temp=$prev_entry['costCenterDebitId']
			$prev_jsonArr_costCenter=json_decode($prev_entry['costCenterDebitId']);
			$prev_jsonArr_costCenter=$prev_jsonArr_costCenter + json_decode($prev_entry['costCenterCreditId']);
			$prev_jsonArr_costCenter= (array) $prev_jsonArr_costCenter;

			//print_r($jsonArr_costCenter);
			foreach($prev_jsonArr_costCenter as $ent){


				//$prev_entry['costCenterDebitId']=json_encode((Array) $prev_entry['costCenterDebitId']);

				//print_r(json_decode($prev_entry['costCenterDebitId']));

//

			if (in_array($account['id'], ($ent)) || in_array($account['id'], ($ent))){
				//print_r($prev_entry);
				array_push($prev_accountEntries, $prev_entry);
				}
			}
		}

			//print_r($prev_accountEntries);





			//if (!empty($prev_accountEntries))
			{





				foreach ($prev_accountEntries as $entry){
					$entry = (array) $entry;

					$costCenterDebitIds = array();
					$costCenterDebitAmounts = 0;

					$costCenterCreditIds = array();
					$costCenterCreditAmounts = 0;



					foreach (json_decode($entry['costCenterDebitId']) as $debitAccount){
					$costCenterDebitAmountsInner = 0;
					foreach ($debitAccount as $ent){

						//print_r(json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts]);
					//print_r(json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts][$costCenterDebitAmountsInner]);

						array_push($costCenterDebitIds, array((string) $ent => json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts][$costCenterDebitAmountsInner]));
					$costCenterDebitAmountsInner += 1;
						}
					$costCenterDebitAmounts += 1;
					}

					foreach (json_decode($entry['costCenterCreditId']) as $creditAccount){
					$costCenterCreditAmountsInner = 0;
					foreach ($creditAccount as $ent){
						array_push($costCenterCreditIds, array((string) $ent => json_decode($entry['costCenterCreditAmount'])[$costCenterCreditAmounts][$costCenterCreditAmountsInner]));
					$costCenterCreditAmountsInner += 1;
					}
					$costCenterCreditAmounts += 1;
					}





					//print_r($costCenterDebitIds);

					foreach ($costCenterDebitIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);

						//print_r($accountInfo);
						//print_r($accountInfo->id);


						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							$prev_fbalance=$prev_fbalance+$balance;




							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



							$x++;
						}
					}
					foreach ($costCenterCreditIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);
						//echo $balance;



						$y = 0;
						if ($accountInfo->id == $id){
							//$prev_fbalance=$prev_fbalance-$balance;




							$y++;
						}
					}






				}


				}
		}
		//$this->sma->print_arrays($prev_fbalance);
	//$this->data['prev_fbalance']  = $prev_fbalance;



					//echo $prev_fbalance;
						if($prev_fbalance){

						$sr++;
					if($this->input->post('show_style')==0){
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


						}

					echo '			</td>
					<td class="align-middle">';
						if($accountSide=="Credit"){
							echo '<strong>'.$this->sma->formatMoney(abs($prev_fbalance),' ').'</strong><br />';
							$balanceCredit=$balanceCredit+$prev_fbalance;
							$fbalance=$fbalance+$prev_fbalance;





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

				*/
			//print_r($accountList1);

			foreach ($accountList1 as $account){

			$account = (array) $account;
			$accountEntries = array();





			$id      = $account['id'];
			//$accountBalance = $account['accountDebit'] - $account['accountCredit'];


			foreach($entryList as $entry){
				$entry = (array) $entry;

				$jsonArr_costCenterDebit=json_decode($entry['costCenterDebitId']);
				$jsonArr_costCenterCredit=json_decode($entry['costCenterCreditId']);
			//$jsonArr_costCenter=$jsonArr_costCenter + json_decode($entry['costCenterCreditId']);
			$jsonArr_costCenterDebit= (array) $jsonArr_costCenterDebit;
			$jsonArr_costCenterCredit= (array) $jsonArr_costCenterCredit;

			//print_r($jsonArr_costCenter);
			foreach($jsonArr_costCenterDebit as $ent){


			if (in_array($account['id'], ($ent))){
				//print_r($entry);
				array_push($accountEntries, $entry);
				}
			}

			foreach($jsonArr_costCenterCredit as $ent){


			if (in_array($account['id'], ($ent))){
				//print_r($entry);
				array_push($accountEntries, $entry);
				}
			}


			}

			//if (empty(array_intersect($_POST['entryDebitAccount'], $_POST['entryCreditAccount']))){

			//print_r($accountEntries);

			if (!empty($accountEntries))
			{
				/*echo '<h2 class="pull-left">';
				?>
				<a href="<?= admin_url('accounts/ledgers/'.$account['id']); ?> ">
				<?php
				if ($this->Settings->user_language == 'arabic' || $account['costNameEn']==''){
				echo $account['costName'].'</a></h2>';
				}else{
				echo $account['costNameEn'].'</a></h2>';

				}*/

				$accountEntriesGlogal = [];
				$accountEntriesGlogal_temp = [];

				//$accountEntriesGlogal=$accountEntries;

			$countDublicat=0;
			foreach ($accountEntries as $accountEntry){

				if(!in_array($accountEntry['entryID'], $accountEntriesGlogal))
				{

				//echo '_'.$countDublicat.'+ser='.array_search($accountEntry['entryID'], $accountEntriesGlogal);
				array_push($accountEntriesGlogal, $accountEntry['entryID']);
				array_push($accountEntriesGlogal_temp, $countDublicat);
				}

				$countDublicat++;

			}
			$accountEntries_Arr = array();

				foreach ($accountEntriesGlogal_temp as $accountEntriesGlogal_temp_index){
				array_push($accountEntries_Arr, $accountEntries[$accountEntriesGlogal_temp_index]);
				}

				if ($this->Settings->user_language == 'arabic' || $account['costNameEn']==''){
				$costName= $account['costName'];
				}else{
				$costName=$account['costNameEn'];

				}



				foreach ($accountEntries_Arr as $entry){
					$entry = (array) $entry;

					$costCenterDebitIds = array();
					$costCenterDebitAmounts = 0;

					$costCenterCreditIds = array();
					$costCenterCreditAmounts = 0;

					$costCenterEntry= array();
					//print_r($accountEntries[]);


					foreach (json_decode($entry['costCenterDebitId']) as $debitAccount){
					$costCenterDebitAmountsInner = 0;
					foreach ($debitAccount as $ent){

						array_push($costCenterDebitIds, array((string) $ent => json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts][$costCenterDebitAmountsInner]));
					$costCenterDebitAmountsInner += 1;
						}
					$costCenterDebitAmounts += 1;
					}

					foreach (json_decode($entry['costCenterCreditId']) as $creditAccount){
					$costCenterCreditAmountsInner = 0;
					foreach ($creditAccount as $ent){
						array_push($costCenterCreditIds, array((string) $ent => json_decode($entry['costCenterCreditAmount'])[$costCenterCreditAmounts][$costCenterCreditAmountsInner]));
					$costCenterCreditAmountsInner += 1;
					}
					$costCenterCreditAmounts += 1;
					}


					/*foreach (json_decode($entry['costCenterDebitId']) as $debitAccount){
						array_push($costCenterDebitIds, array((string) $debitAccount => json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts]));
						$costCenterDebitAmounts += 1;
					}

					foreach (json_decode($entry['costCenterCreditId']) as $creditAccount){
						array_push($costCenterCreditIds, array((string) $creditAccount => json_decode($entry['costCenterCreditAmount'])[$costCenterCreditAmounts]));
						$costCenterCreditAmounts += 1;
					}*/

					//print_r($costCenterCreditIds);


					$accountFlage=1;
					$accountName=$costName;
				if(isset($_POST['account']) && $_POST['account']!=""){
					$accountFlage=0;
					$accountInfo = $this->accounts_model->getAccountByID($_POST['account']);
				if ($this->Settings->user_language == 'arabic' || $accountInfo->accountNameEn==''){
				$accountName= $accountInfo->accountName;
				}else{
				$accountName=$accountInfo->accountNameEn;

				}
				if (in_array($_POST['account'], json_decode($entry['entryDebitAccount'])) || in_array($_POST['account'], json_decode($entry['entryCreditAccount']))){
				$accountFlage=1;
				}
				}


							$dt_index=0;
							foreach (json_decode($entry['entryDebitAccount']) as $debitAccount){
								$dtc_index=0;
									foreach ((Array)json_decode($entry['costCenterDebitId'])[$dt_index] as $costDebitId){

									if(isset($_POST['account']) && $_POST['account']!=""){
									if(json_decode($entry['costCenterDebitId'])[$dt_index][$dtc_index]==$id && $debitAccount==$_POST['account']){


						array_push($costCenterEntry, $entry['entryID']);

									}
									}else{
									if(json_decode($entry['costCenterDebitId'])[$dt_index][$dtc_index]==$id){


						array_push($costCenterEntry, $entry['entryID']);

									}

									}
								$dtc_index++;
									}
								$dt=$dt+$entry['entryDebitBalance'][$dt_index];
								$dt_index++;


							}

							$ct_index=0;
							foreach (json_decode($entry['entryCreditAccount']) as $CreditAccount){
								$ctc_index=0;
									foreach ((Array)json_decode($entry['costCenterCreditId'])[$ct_index] as $costCreditId){
									if(isset($_POST['account']) && $_POST['account']!=""){
									if(json_decode($entry['costCenterCreditId'])[$ct_index][$ctc_index]==$id && $CreditAccount==$_POST['account']){


						array_push($costCenterEntry, $entry['entryID']);

									}
									}else{
									if(json_decode($entry['costCenterCreditId'])[$ct_index][$ctc_index]==$id){


						array_push($costCenterEntry, $entry['entryID']);

									}

									}
								$ctc_index++;
									}
								$ct=$ct+$entry['entryCreditBalance'][$ct_index];
								$ct_index++;


							}


					if($accountFlage==1 && in_array($entry['entryID'], $costCenterEntry)){
						$x = 0;

						foreach ($costCenterDebitIds as $account){


						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);
						//if(json_decode($entry['entryDebitAccount'])[$x] == $_POST['account'])
						//	echo json_decode($entry['entryDebitAccount'])[$x].' '.$x.' '.$entry['entryID'];


						//$accountInfo = $this->entry_model->getAccount($account);

						if ($accountInfo->id == $id){
							//echo $x.' '.$balance.'<br>';

							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];

				            /*$accountDebitBalance = $accountInfo->accountDebit;


							$accountInfoDebitBalance = array('accountDebit' => ($balance + $accountDebitBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoDebitBalance);*/

						$x++;
						}

					}



					$sr++;
					if($this->input->post('show_style')==0){
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
						$entryDescription= str_replace("<p>","",($entry['entryDescription'])."");
						$entryDescription= str_replace("</p>","",($entryDescription)."");
					echo '
					<td class="text-left align-middle">'.$accountName.'-'.lang($entryDescription).'</td>
					<td class="align-middle">';
				}else{
					echo '
					<td class="text-left align-middle">'.$accountName.'-'.lang($entry['entryType']).'</td>
					<td class="align-middle">';

				}
					foreach ($costCenterDebitIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);
						//print_r($accountInfo->id);



						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
							$balanceDebit=$balanceDebit+$balance;
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
					foreach ($costCenterCreditIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);

						$y = 0;
						if ($accountInfo->id == $id){
							echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
							$balanceCredit=$balanceCredit+$balance;
								$fbalance=$fbalance+$balance;



							/*$accountCreditBalance = $accountInfo->accountCredit;


							$accountInfoCreditBalance = array('accountCredit' => ($balance + $accountCreditBalance));
				            $this->db->where('id', $account);
				            $this->db->update('gl_accounts', $accountInfoCreditBalance);*/

							$y++;
						}
					}
					/*echo '			</td>

					<td class="text-left align-middle"><strong>'.$this->sma->formatMoney($fbalance,' ').'</strong></td>



					</tr>
					';*/



					//if($accountSide=="Credit"){
						if($fbalance>0){

					echo '			</td>

					<td class="text-left align-middle" style="color:green;"><strong>'.$this->sma->formatMoney(abs($fbalance),' ').'</strong></td>



					</tr>
					';
						}else{
					echo '			</td>

					<td class="text-left align-middle" style="color:red;"><strong>'.$this->sma->formatMoney(abs($fbalance),' ').'</strong></td>



					</tr>
					';

						}
					//}




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
				<td colspan="4"><strong style="font-size:18px;">'.lang("total").'</strong></td>
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceDebit,' ').'</strong></td>
				<td style="text-decoration: underline;border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($balanceCredit,' ').'</strong></td>';
				/*echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;"><strong style="font-size:18px;">'.$this->sma->formatMoney($fbalance,' ').'</strong></td>';
				*/




						if($fbalance>0){

				echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:green;"><strong style="font-size:18px;">'.$this->sma->formatMoney(abs($fbalance),' ').'</strong></td>';
						}else{
					echo '
				<td style="text-decoration: underline;  border-top: 2px solid #000000;color:red;"><strong style="font-size:18px;">'.$this->sma->formatMoney(abs($fbalance),' ').'</strong></td>';

						}





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
				if (in_array($account['id'], json_decode($entry['costCenterDebitId'])) || in_array($account['id'], json_decode($entry['costCenterCreditId']))){
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
				if ($this->Settings->user_language == 'arabic' || $account['costNameEn']==''){
				echo $account['costName'].'</a></h2>';
				}else{
				echo $account['costNameEn'].'</a></h2>';

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
			if (in_array($account['id'], json_decode($prev_entry['costCenterDebitId'])) || in_array($account['id'], json_decode($prev_entry['costCenterCreditId']))){
					array_push($prev_accountEntries, $prev_entry);
				}
			}








			//if (!empty($prev_accountEntries))
			{




				$prev_fbalance=0;

				foreach ($prev_accountEntries as $entry){
					$entry = (array) $entry;

					$costCenterDebitIds = array();
					$costCenterDebitAmounts = 0;

					$costCenterCreditIds = array();
					$costCenterCreditAmounts = 0;

					foreach (json_decode($entry['costCenterDebitId']) as $debitAccount){
						array_push($costCenterDebitIds, array((string) $debitAccount => json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts]));
						$costCenterDebitAmounts += 1;
					}

					foreach (json_decode($entry['costCenterCreditId']) as $creditAccount){
						array_push($costCenterCreditIds, array((string) $creditAccount => json_decode($entry['costCenterCreditAmount'])[$costCenterCreditAmounts]));
						$costCenterCreditAmounts += 1;
					}





					foreach ($costCenterDebitIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);
						//print_r($accountInfo->id);

						//$accountInfo = $this->entry_model->getAccount($account);

						$x = 0;
						if ($accountInfo->id == $id){
							$prev_fbalance=$prev_fbalance+$balance;


							//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



							$x++;
						}
					}
					foreach ($costCenterCreditIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);

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

					$costCenterDebitIds = array();
					$costCenterDebitAmounts = 0;

					$costCenterCreditIds = array();
					$costCenterCreditAmounts = 0;

					foreach (json_decode($entry['costCenterDebitId']) as $debitAccount){
						array_push($costCenterDebitIds, array((string) $debitAccount => json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts]));
						$costCenterDebitAmounts += 1;
					}

					foreach (json_decode($entry['costCenterCreditId']) as $creditAccount){
						array_push($costCenterCreditIds, array((string) $creditAccount => json_decode($entry['costCenterCreditAmount'])[$costCenterCreditAmounts]));
						$costCenterCreditAmounts += 1;
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

					foreach ($costCenterDebitIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);
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
					foreach ($costCenterCreditIds as $account){
						$balance = $account[key($account)];
						$account = key($account);
						$accountInfo = $this->accounts_model->getCostCentersByID($account);

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
        PrPDF('pr_pdf','<?= lang('Cost_centers_Report'); ?>','L');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/distributioncostcenters/0/xls/?v=' . $v)?>";
            return false;
        });

		 $('#print').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('accounts/distributioncostcentersprint/?' . $v) ?>";
                return false;

				//PrintElem('accountstatment);
            });



        });
    </script>

