<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
if (empty($_POST)){
	//$_POST['brId']=$this->session->userdata('brId');
$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
	if ($check_period || $check_period->period_status==1){
		$_POST['start_date']=$this->sma->hrsd($check_period->period_from_date);
		$_POST['end_date']=$this->sma->hrsd($check_period->period_to_date);
	}else{

		$_POST['start_date']=$this->sma->hrsd($max_min_period_year->period_from_date);
		$_POST['end_date']=$this->sma->hrsd($max_min_period_year->period_to_date);
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
if ($this->input->post('levels')) {
    $v .= '&levels=' . $this->input->post('levels');
}


if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}else{
	$_POST['start_date']=$this->sma->hrsd($max_min_period_year->period_from_date);
	$v .= '&start_date=' . $_POST['start_date'];
}
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}else{
	$_POST['end_date']=$this->sma->hrsd($max_min_period_year->period_to_date);
	$v .= '&end_date=' . $_POST['end_date'];
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;

$levels=$this->input->post('levels')>1 ? $this->input->post('levels') : NULL;

?>

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
        <?php if (!$this->input->post('submit_report') && !$this->input->post('levels') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
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

<div class="spark-balancesheet-page">
    <div class="spark-balancesheet-header">
        <div class="spark-balancesheet-header-start">
            <div class="spark-balancesheet-header-icon">
                <i class="fa fa-university"></i>
            </div>
            <div class="spark-balancesheet-header-info">
                <h1 class="spark-balancesheet-title">
                    <?= lang('Balance_Sheet') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-balancesheet-subtitle">
                    <?= lang('manage_balancesheet_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-balancesheet-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <button type="button" id="xls" class="spark-btn-secondary-action tip" title="<?= lang('download_xls') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('download_xls'); ?></span>
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

                    <?php echo admin_form_open('accounts/balancesheet',' autocomplete="off" id="balancesheetForm"'); ?>
                    <div class="col-sm-12">


						<input type="hidden" name="id" value="<?= $filter_id ?>" id="filter_id"/>



						<?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-2">
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

                                    <div class="col-sm-2">
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
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
												if ($Owner || $Admin){
													$br['']=lang('all');
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ''), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('Year', 'Year'); ?>
                                <?php
								 foreach ($period_year as $account) {
									$account = (array) $account;


				$wh[$account['period_year']] = $account['period_year'];


                                }
								 echo form_dropdown('period_year', $wh, (isset($_POST['period_year']) ? $_POST['period_year'] : ''), 'class="form-control" id="period_year" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Year') . '"');
								 ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control" readonly id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control" readonly id="end_date"'); ?>
                            </div>
                        </div>
						<div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="levels"><?= lang('Levels'); ?></label>
                                <?php
                                $levels_arr['0'] = lang('all');
                                $levels_arr['2'] = lang('Level_2');
                                $levels_arr['3'] = lang('Level_3');
                                $levels_arr['4'] = lang('Level_4');
                                $levels_arr['5'] = lang('Level_5');
                                $levels_arr['6'] = lang('Level_6');
                                $levels_arr['7'] = lang('Level_7');

                                echo form_dropdown('levels', $levels_arr, (isset($_POST['levels']) ? $_POST['levels'] : ''), 'class="form-control" id="levels" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Levels') . '"');
                                ?>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div><!-- end form -->


		</div>
	</div>
	 <div id="pr_pdf">
		<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl" id="pr_pdf1">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('Balance_Sheet'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

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
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Balance_Sheet') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>
				</div>
			</div>
		</div>

<div class="row">
	<div class="col-lg-12">

	<table class="table table-striped table-bordered table-hover">
		<thead class="thead-dark">
			<tr class="text-center">
				<th><?= lang('Account') ?></th>
				<th><?= lang('Amount') ?></th>
			</tr>
		</thead>
				<tbody >





		<?php
	$revenueTotal  = 0;
	$orevenueTotal  = 0;
	$expensesTotal = 0;
	$opexpensesTotal = 0;
	$maexpensesTotal = 0;
	$oexpensesTotal = 0;



	$accounts = array();

	foreach ($accountList as $account){
		$account = (array) $account;

		if (!isset($accounts[$account['accountCategory']])){
			$accounts[$account['accountCategory']] = array($account);
		}
		else {
			array_push($accounts[$account['accountCategory']], $account);
		}
	}

	$accountCategories = array_keys($accounts);
	$accountOrder = 0;




	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_acct);
	$acc = (array) $acc[0];

	$sales_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);



	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_return_acct);
	$acc = (array) $acc[0];

	$sales_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);


			$net_sales=$this->sma->formatDecimal(($sales_acctBalance))+$this->sma->formatDecimal(($sales_return_acctBalance));



	if($Settings->accounting_type=='periodic' && substr($this->input->post('start_date'),6,10)!=2022){







	$first_period_stock = $this->site->getOpeningBalanceForWarehouses((isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),null,$_POST['grpId'],$_POST['coId'],$_POST['brId'])->first_period_stock;
	//$this->sma->print_arrays($first_period_stock);
	if($first_period_stock==0){

		$first_period_stock = $this->site->getLastendPeriodStock((isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),($_POST['brId'] ? $_POST['brId'] : null));
		}


				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_acct);
	$acc = (array) $acc[0];

	$purchases_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);


	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_return_acct);
	$acc = (array) $acc[0];



	$purchases_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);



					$net_purchases=$this->sma->formatDecimal(($purchases_acctBalance))+$this->sma->formatDecimal(($purchases_return_acctBalance));








					$goods_available_for_sale=$this->sma->formatDecimal(($first_period_stock))+$this->sma->formatDecimal(($net_purchases));




					$total_end_of_period_stock=0;
					$warehouses=$this->site->getAllWarehouses(($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));
					foreach($warehouses as $warehouse){
				$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2((isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$warehouse->id,($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));

	foreach($end_of_period_stock_arr as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
}
	$end_of_period_stock = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));
	//$end_of_period_stock = $this->sma->formatDecimal(($first_period_stock)+ ($end_of_period_stock));
	//$this->sma->print_arrays($end_of_period_stock);



					$sales_cost_of_good_sold_acctBalance=$this->sma->formatDecimal(($goods_available_for_sale))-$this->sma->formatDecimal(($end_of_period_stock));











	}else{

	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);
	$acc = (array) $acc[0];

	$sales_cost_of_good_sold_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

	}



			$Gross_income=$this->sma->formatDecimal(($net_sales))+$this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance));




			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID']);
			foreach ($accountCategory as $account){

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}



			if ($accountBalance == 0){
				continue;
			}

				$opexpensesTotal += $accountBalance;


			}







	$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID']);
			foreach ($accountCategory as $account){

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}



			if ($accountBalance == 0){
				continue;
			}

				$maexpensesTotal += $accountBalance;


			}



		$Net_operating_income=$this->sma->formatDecimal(($Gross_income) + (($opexpensesTotal) + ($maexpensesTotal)));



		$otherRevenuesAccounts = $this->accounts_model->getOtherRevenuesAccounts();
			//print_r($this->site->getAccountsSettings()->sales_acct);

			foreach ($otherRevenuesAccounts as $account){

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}



			if ($accountBalance == 0){
				continue;
			}

				$orevenueTotal += $accountBalance;



			}



		$accounts_operating_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
		$accounts_operating_expenses_acct = (array) $accounts_operating_expenses_acct[0];


		$accounts_marketing_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
		$accounts_marketing_expenses_acct = (array) $accounts_marketing_expenses_acct[0];


		$otherExpensesAccounts = $this->accounts_model->getOtherExpensesAccounts($accounts_operating_expenses_acct['accountID'],$accounts_marketing_expenses_acct['accountID']);

			foreach ($otherExpensesAccounts as $account){

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}



			if ($accountBalance == 0){
				continue;
			}

				$oexpensesTotal += $accountBalance;



			}

















?>





















			<?php

			/*function getRetainedEarnings($accountList){
				$revenueTotal  = 0;
				$expensesTotal = 0;


				$accounts = array();

				foreach ($accountList as $account){
					$account = (array) $account;

					if (!isset($accounts[$account['accountCategory']])){
						$accounts[$account['accountCategory']] = array($account);
					}
					else {
						array_push($accounts[$account['accountCategory']], $account);
					}
				}

				$accountCategories = array_keys($accounts);
				$accountOrder = 0;
				foreach ($accounts as $accountCategory){
					foreach ($accountCategory as $account){
						$accountBalance = $account['accountDebit'] - $account['accountCredit'];
						if ($account['accountCategory'] == 'Revenues'){
							$revenueTotal += $accountBalance;
						}
						else {
							$expensesTotal += $accountBalance;
						}
					}
				}
				$netIncome        = abs($revenueTotal) - $expensesTotal;
				$dividends        = 0;
				$retainedEarnings = $netIncome - $dividends;

				return $retainedEarnings;
			}

			$retainedEarnings = getRetainedEarnings($retainedEarningsList);

			$retainedEarningArray = array(
				"accountID" => "30009990",
				"accountName" => "Retained Earnings",
				"accountCategory" => "Owners Equity",
				"accountCategorySub" => "Stockholders' Equity",
				"accountDebit" => 0,
				"accountCredit" => $retainedEarnings,
			);*/





			$assetsTotal      = 0;
			$fassetsTotal      = 0;
			$cassetsTotal      = 0;
			$oassetsTotal      = 0;
			$provisionsTotal      = 0;
			$liabilitiesTotal = 0;
			$equityTotal      = 0;

			$accounts = array();


			foreach ($accountList as $account){
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])){
					$accounts[$account['accountCategory']] = array($account);
				}
				else {
					array_push($accounts[$account['accountCategory']], $account);
				}
			}


			/*if($retainedEarningArray == null)  {
				array_push($accounts["Owners Equity"], $retainedEarningArray);
				return 0;
			}*/


			$accountCategories = array_keys($accounts);



			echo '
						<tr>
							<td><strong>'.lang('Assets').':</strong></td>
							<td></td>
							</tr>
			';





	echo '
						<tr>
							<td><strong>'.lang('Fixed_Assets').'</strong></td>
							<td></td>
							</tr>
			';



// 	case $this->startsWith($accountID, '100'):

			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_fixed_assets_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory=$this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'],$levels);
			//$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID']);
			//$this->sma->print_arrays($accountCategory);
							foreach ($accountCategory as $account){

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}




			if ($accountBalance == 0){
				continue;
			}

				$fassetsTotal += $accountBalance;

			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';


			}

			echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('Fixed_Assets').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($fassetsTotal)).'</strong></td>
						</tr>
		';








		echo '
						<tr>
							<td><strong>'.lang('Current_Assets').'</strong></td>
							<td></td>
							</tr>
			';



// 	case $this->startsWith($accountID, '100'):

			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_current_assets_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory=$this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'],$levels);
			foreach ($accountCategory as $account){

				if($Settings->accounting_type=='periodic' && $account->account_type=="warehouses" && $account->account_ref_id>0){
					//$this->sma->print_arrays($levels);
					$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2((isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$account->account_ref_id,($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));
					//$this->sma->print_arrays($end_of_period_stock_arr);
					$total_end_of_period_stock=0;
	foreach($end_of_period_stock_arr as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
	$accountBalance = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));

				}else{

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
		}

			if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}


			if ($accountBalance == 0){
				continue;
			}

				$cassetsTotal += $accountBalance;

			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';


			}

			echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('Current_Assets').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($cassetsTotal)).'</strong></td>
						</tr>
		';



/*


		echo '
						<tr>
							<td><strong>'.lang('The_Provisions').'</strong></td>
							<td></td>
							</tr>
			';



// 	case $this->startsWith($accountID, '100'):

			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_provisions_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory=$this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'],$levels);
			foreach ($accountCategory as $account){

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''));

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''));

			}

			if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''));

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}



			if ($accountBalance == 0){
				continue;
			}

				$provisionsTotal += $accountBalance;

			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';


			}

			echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('The_Provisions').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($provisionsTotal)).'</strong></td>
						</tr>
		';

*/


		echo '
						<tr>
							<td><strong>'.lang('Other_Assets').':</strong></td>
							<td></td>
							</tr>
			';



		$otherAssetsAccounts = $this->accounts_model->getOtherAssetsAccounts();
			//print_r($this->site->getAccountsSettings()->sales_acct);

			foreach ($otherAssetsAccounts as $account){

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}



			if ($accountBalance == 0){
				continue;
			}

				$oassetsTotal += $accountBalance;
			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';


			}

			echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('Other_Assets').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($oassetsTotal)).'</strong></td>
						</tr>
		';


		echo '
							<tr>
								<td ><strong>'.lang('Total').' '.lang('Assets').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs(($fassetsTotal)+($cassetsTotal)+($oassetsTotal)) - abs($provisionsTotal)).'</strong></td>
						</tr>
		';




			$accountOrder = 0;
			foreach ($accounts as $accountCategory){
				echo '
				<tr>
				<td><strong>'.$accountCategories[$accountOrder].':</strong></td>
				<td></td>
				</tr>
				';
				$moneySign = '';
				$accountSubCategory = NULL;
				$subTotal = 0;


				//$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_provisions_acct);
				//$accountChilds = (array) $accountChilds[0];


				foreach ($accountCategory as $account){




			//$accountChilds = $this->accounts_model->getAccounts($account['id']);
			//$accountChilds_levels=$accountChilds;

			//$accountChilds = (array) $accountChilds[0];

			$accountBalance=0;

					if (!empty($_POST)){
						//$_POST['startDate'] = '2020-01-01';
						$accountBalance = $this->accounts_model->getAccountTotal($account['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}
					else {
						$accountBalance = $this->accounts_model->getAccountTotal($account['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}


					if($levels){
						if($levels==$account['level'])
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}
					/*$accountCategoryprovisions=$this->accounts_model->isAccountChildsWithLevels($accountChilds['accountID'],$account['accountID'],$levels);
					if ($accountCategoryprovisions){
						continue;
					}*/

					if ($accountBalance == 0){
						continue;
					}

					if ($account['accountCategory'] == 'الإلتزامات وحقوق الملكية'){
						$liabilitiesTotal += $accountBalance;
					}
					else {
						$equityTotal += $accountBalance;
					}

					$subTotal += $accountBalance;
					if($accountSubCategory != $account['accountCategorySub']){
						$accountSubCategory = $account['accountCategorySub'];
						echo '
						<tr style="background:aliceblue;">
						<td colspan=2><strong>'.$account['accountCategorySub'].'</strong></td>
						</tr>
						';
						$subTotal = 0;
					}
					echo '
					<tr>
					<td>'.($this->Settings->user_language == 'arabic' ? $account['accountName'] : $account['accountNameEn']).'</td>
					<td class="text-right">'.$moneySign;
					if ($accountBalance != abs($accountBalance)){
						echo $this->sma->formatMoney(abs($accountBalance));
					}
					else {
						echo $this->sma->formatMoney($accountBalance);
					}
					echo '</td>
					</tr>
					';
					$moneySign = '';
				}
				if ($account['accountCategory'] == 'الإلتزامات وحقوق الملكية'){
					if ($liabilitiesTotal != abs($liabilitiesTotal)){
						$accountAmount = $this->sma->formatMoney(abs($liabilitiesTotal));
					}
					else {
						$accountAmount = $this->sma->formatMoney($liabilitiesTotal);
					}

					echo '
					<tr>
					<td class="text-center"><strong>'.lang('Total').' '.$accountCategories[$accountOrder].'</strong></td>
					<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;'.$accountAmount.'</strong></td>
					</tr>
					';
				}
				/*else{
					if ($equityTotal != abs($equityTotal)){
						$accountAmount = '('.number_format(abs($equityTotal), 0).')';
					}
					else {
						$accountAmount = number_format($equityTotal, 0);
					}
					echo '
					<tr>
					<td class="text-center"><strong>Total '.$accountCategories[$accountOrder].'</strong></td>
					<td class="text-right" style="text-decoration: underline; border-top: 2px solid #000000;"><strong>TK&nbsp;&nbsp;&nbsp;'.$accountAmount.'</strong></td>
					</tr>
					';
				}*/
				$accountOrder += 1;
			}
			$Final_Net_Income=($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal);

			echo '

							<tr>
								<td><strong>'.lang('Net_Income').'</strong></td>';
			if($Final_Net_Income<0){
			echo '
								<td class="text-right" style="color:green;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;  '.$this->sma->formatMoney(abs($Final_Net_Income)).'  </strong></td>
								';
			}else{
				echo '
								<td class="text-right" style="color:red;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($Final_Net_Income)).'</strong></td>
								';
			}
			echo '
							</tr>
	';

		echo '

							<tr>
								<td><strong>'.lang('Total').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs(($liabilitiesTotal) + ($Final_Net_Income))).'</strong></td>
							</tr>
	';

			/*$liaequTotal = $liabilitiesTotal + $equityTotal;
			if ($liaequTotal != abs($liaequTotal)){
				$accountAmount = '('.number_format(abs($liaequTotal), 0).')';
			}
			else {
				$accountAmount = number_format($liaequTotal, 0);
			}

			echo '
			<tr>
			<td class="text-center"><strong>Total Liabilities & Owner\'s Equity</strong></td>
			<td class="text-right" ><strong>TK&nbsp;&nbsp;&nbsp;'.$accountAmount.'</strong></td>
			</tr>
			';*/
			?>
		</tbody>
	</table>
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
        PrPDF('pr_pdf','<?= lang('Balance_Sheet'); ?>');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/balancesheet/0/xls/?v=' . $v)?>&period_year="+$('#period_year').val();
            return false;
        });
		//console.log(site.dateFormats.js_sdate);
 //$("#start_date").datetimepicker({format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma', todayBtn: 1, autoclose: 1,  startView: 2, startDate: "<?= $this->sma->hrsd(date('Y-m-d')); ?>", endDate: "<?= $this->sma->hrsd(date('Y-m-d')); ?>"});
 //$("#start_date").datetimepicker({format: site.dateFormats.js_ldate, fontAwesome: true, language: 'sma', weekStart: 1, todayBtn: 1, autoclose: 1, todayHighlight: 1, startView: 2, forceParse: 0, endDate: "<?= $this->sma->hrld(date('Y-m-d H:i:s')); ?>"});
 $("#start_date").datetimepicker({

            format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma',  todayBtn: 1, autoclose: 1, minView: 2,
            startDate: "<?= $max_min_period_year->period_from_date; ?>",
            endDate: "<?= $max_min_period_year->period_to_date; ?>",

});
$("#end_date").datetimepicker({

format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma',  todayBtn: 1, autoclose: 1, minView: 2,
startDate: "<?= $max_min_period_year->period_from_date; ?>",
endDate: "<?= $max_min_period_year->period_to_date; ?>",

});


$('#period_year').change(function (e) {

	//alert($(this).val());
	$("#start_date").val('');
	$("#end_date").val('');
	$("#balancesheetForm").submit();

    });


    });
</script>

