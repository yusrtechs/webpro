<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>





    /*  table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }
*/

	 table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }



</style>
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
if ($this->input->post('compared_to')) {
    $v .= '&compared_to=' . $this->input->post('compared_to');
}
if ($this->input->post('compare_periods')) {
    $v .= '&compare_periods=' . $this->input->post('compare_periods');
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
}//else
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

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>


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

<div class="spark-incomestatement-page">
    <div class="spark-incomestatement-header">
        <div class="spark-incomestatement-header-start">
            <div class="spark-incomestatement-header-icon">
                <i class="fa fa-line-chart"></i>
            </div>
            <div class="spark-incomestatement-header-info">
                <h1 class="spark-incomestatement-title">
                    <?= lang('Income_Statement') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-incomestatement-subtitle">
                    <?= lang('manage_incomestatement_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-incomestatement-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <button type="button" id="xls" class="spark-btn-secondary-action tip" title="<?= lang('export_to_excel') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('export_to_excel'); ?></span>
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

                    <?php echo admin_form_open('accounts/incomestatement',' autocomplete="off" id="incomestatementForm"'); ?>
                    <div class="col-sm-12">


						<input type="hidden" name="id" value="<?= $filter_id ?>" id="filter_id"/>

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
						<div class="col-sm-3">
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


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" readonly id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" readonly id="end_date"'); ?>
                            </div>
                        </div>

						<div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="compared_to"><?= lang('Compare_To'); ?></label>
                                <?php
                                $compared_to_arr['0'] = lang('Compare_To');
                                $compared_to_arr['-1 years'] = lang('Previous_Year');
                                $compared_to_arr['-1 months'] = lang('Previous_Month');
                                $compared_to_arr['-3 months'] = lang('Previous_Quarter');
                                $compared_to_arr['-1 weeks'] = lang('Previous_Week');
                                $compared_to_arr['-1 days'] = lang('Previous_Day');

                                echo form_dropdown('compared_to', $compared_to_arr, (isset($_POST['compared_to']) ? $_POST['compared_to'] : ''), 'class="form-control" id="compared_to" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Compare_To') . '"');
                                ?>
                            </div>
                        </div>

						<div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="compare_periods"><?= lang('Compare_Periods'); ?></label>
                                <?php
                                $compare_periods_arr['1'] = lang('1_Period');
                                $compare_periods_arr['2'] = lang('2_Periods');
                                $compare_periods_arr['3'] = lang('3_Periods');
                                $compare_periods_arr['4'] = lang('4_Periods');
                                $compare_periods_arr['5'] = lang('5_Periods');
                                $compare_periods_arr['6'] = lang('6_Periods');
                                $compare_periods_arr['7'] = lang('7_Periods');
                                $compare_periods_arr['8'] = lang('8_Periods');
                                $compare_periods_arr['9'] = lang('9_Periods');
                                $compare_periods_arr['10'] = lang('10_Periods');
                                $compare_periods_arr['11'] = lang('11_Periods');
                                $compare_periods_arr['12'] = lang('12_Periods');
                                $compare_periods_arr['13'] = lang('13_Periods');

                                echo form_dropdown('compare_periods', $compare_periods_arr, (isset($_POST['compare_periods']) ? $_POST['compare_periods'] : ''), 'class="form-control" id="compare_periods" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Compare_Periods') . '"');
                                ?>
                            </div>
                        </div>

						<div class="col-sm-3">
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

                </div>






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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Income_Statement'); ?></font></b></td>
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
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Income_Statement') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>
				</div>
			</div>
		</div>



        <div class="row">
		<div class="col-sm-12 col-md-12">

		<div>

	<table class="table table-striped table-bordered table-hover">
		<thead class="thead-dark">
			<tr class="text-center">
				<th><?= lang('Account') ?></th>
				<?php

				//if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



					for($i=1;$i<=$_POST['compare_periods'];$i++){
						echo '<th>'.$effectiveEndDate.'</th>';

					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}
				}else{
					?>
				<th><?= lang('Amount') ?></th>
				<?php
				}
				?>

			</tr>
		</thead>
		<tbody>
<?php
	$revenueTotal  = 0;
	$orevenueTotal  = 0;
	$expensesTotal = 0;
	$opexpensesTotal = 0;
	$maexpensesTotal = 0;
	$oexpensesTotal = 0;

   /*$date =date("Y-m-d");
   $day = 16;
   $newdate=date('Y-m-d', strtotime("+$day days"));
   echo "today is:".$date;
   echo "<br> and after 5 days is :".$newdate;
	echo $effectiveDate = date('Y-m-d', strtotime("-16 days", strtotime(date('Y-m-d'))));

	*/
	//echo $_POST['compare_periods'];
	//echo $effectiveDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime((isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''))));

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
	//print_r($accountList);
	$accountOrder = 0;

	$moneySign = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';


	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Revenues').':</strong></td>
							';


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					for($i=1;$i<=$_POST['compare_periods'];$i++){
						//echo $effectiveStartDate.'</br>';
						echo '
							<td></td>
							';

					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}
					echo '						</tr>
					';

				}else{

	echo '
						<tr>
							<td><strong>'.lang('Revenues').':</strong></td>
							<td></td>
							</tr>
			';

				}
	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_acct);
	$acc = (array) $acc[0];

	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$sales_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'],$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);
						echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($sales_acctBalance[$icount])).'</td>
							';

					//$effectiveEndDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveEndDate)));
					//$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					//$effectiveEndDate = date('Y-m-d', strtotime('-1 days', strtotime($effectiveStartDate)));
					//if()
					//$effectiveEndDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveEndDate)));

					//$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveEndDate)));



					$icount++;
					}
					echo '						</tr>
					';

				}else{

//echo (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : '');
//echo (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : '');
	$sales_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

	echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($sales_acctBalance)).'</td>
							</tr>
			';
				}


	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_return_acct);
	$acc = (array) $acc[0];


	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$sales_return_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'],$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($sales_return_acctBalance[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

	$sales_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

	echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($sales_return_acctBalance)).'</td>
							</tr>
			';
				}



	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('net_sales').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$net_sales[$icount]=$this->sma->formatDecimal(($sales_acctBalance[$icount]))+$this->sma->formatDecimal(($sales_return_acctBalance[$icount]));



							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($net_sales[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{
					$net_sales=$this->sma->formatDecimal(($sales_acctBalance))+$this->sma->formatDecimal(($sales_return_acctBalance));


	echo '
					<tr>
						<td><strong>'.lang('net_sales').'</strong></td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($net_sales)).'</strong></td>

					</tr>
		';
				}
				//echo   substr($this->input->post('start_date'),6,10);
	if($Settings->accounting_type=='periodic' && substr($this->input->post('start_date'),6,10)!=2022){






				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td>'.lang("First_period_stock").'</td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$first_period_stock[$icount] = $this->site->getOpeningBalanceForWarehouses($effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',null,$_POST['grpId'],$_POST['coId'],$_POST['brId'])->first_period_stock;
						if($first_period_stock[$icount]==0){

							$first_period_stock[$icount] = $this->site->getLastendPeriodStock($effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',($_POST['brId'] ? $_POST['brId'] : null));
						}
							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($first_period_stock[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

	$first_period_stock = $this->site->getOpeningBalanceForWarehouses((isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),null,$_POST['grpId'],$_POST['coId'],$_POST['brId'])->first_period_stock;
	if($first_period_stock==0){

	$first_period_stock = $this->site->getLastendPeriodStock((isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),($_POST['brId'] ? $_POST['brId'] : null));
	}
	//$this->sma->print_arrays($first_period_stock);

	echo '
					<tr>
						<td>'.lang("First_period_stock").'</td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($first_period_stock)).'</strong></td>

					</tr>
		';

				}



				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_acct);
	$acc = (array) $acc[0];

	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$purchases_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'],$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);
						echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($purchases_acctBalance[$icount])).'</td>
							';

					//$effectiveEndDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveEndDate)));
					//$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					//$effectiveEndDate = date('Y-m-d', strtotime('-1 days', strtotime($effectiveStartDate)));
					//if()
					//$effectiveEndDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveEndDate)));

					//$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveEndDate)));



					$icount++;
					}
					echo '						</tr>
					';

				}else{

//echo (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : '');
//echo (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : '');
	$purchases_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

	echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($purchases_acctBalance)).'</td>
							</tr>
			';
				}


	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_return_acct);
	$acc = (array) $acc[0];


	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$purchases_return_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'],$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($purchases_return_acctBalance[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

	$purchases_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

	echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($purchases_return_acctBalance)).'</td>
							</tr>
			';
				}



	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('net_purchases').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$net_purchases[$icount]=$this->sma->formatDecimal(($purchases_acctBalance[$icount]))+$this->sma->formatDecimal(($purchases_return_acctBalance[$icount]));



							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($net_purchases[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{
					$net_purchases=$this->sma->formatDecimal(($purchases_acctBalance))+$this->sma->formatDecimal(($purchases_return_acctBalance));


	echo '
					<tr>
						<td><strong>'.lang('net_purchases').'</strong></td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($net_purchases)).'</strong></td>

					</tr>
		';
				}





				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Goods_available_for_sale').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$goods_available_for_sale[$icount]=$this->sma->formatDecimal(($first_period_stock[$icount]))+$this->sma->formatDecimal(($net_purchases[$icount]));



							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($goods_available_for_sale[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{
					$goods_available_for_sale=$this->sma->formatDecimal(($first_period_stock))+$this->sma->formatDecimal(($net_purchases));


	echo '
					<tr>
						<td><strong>'.lang('Goods_available_for_sale').'</strong></td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($goods_available_for_sale)).'</strong></td>

					</tr>
		';
				}


				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td>'.lang("End-of-period_stock").'</td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$total_end_of_period_stock=0;
	$warehouses=$this->site->getAllWarehouses(($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));
	foreach($warehouses as $warehouse){
						$end_of_period_stock_arr[$icount] = $this->site->getEndPeriodForWarehousesV2($effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',$warehouse->id,($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));

	foreach($end_of_period_stock_arr[$icount] as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
	}
	$end_of_period_stock[$icount] = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));


						//$end_of_period_stock[$icount] = $this->sma->formatDecimal(($first_period_stock[$icount])+ ($end_of_period_stock[$icount]));
							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($end_of_period_stock[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{
	$total_end_of_period_stock=0;
	$warehouses=$this->site->getAllWarehouses(($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));
	foreach($warehouses as $warehouse){
		//$this->sma->print_arrays($warehouse->id);
	$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2((isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$warehouse->id,($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));

	foreach($end_of_period_stock_arr as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
	}
	$end_of_period_stock = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));
	//$this->sma->print_arrays($end_of_period_stock);

	echo '
					<tr>
						<td>'.lang("End-of-period_stock").'</td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($end_of_period_stock)).'</strong></td>

					</tr>
		';

				}


				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Cost_of_Goods_Sold').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$sales_cost_of_good_sold_acctBalance[$icount]=$this->sma->formatDecimal(($goods_available_for_sale[$icount]))-$this->sma->formatDecimal(($end_of_period_stock[$icount]));



							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($sales_cost_of_good_sold_acctBalance[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{
					$sales_cost_of_good_sold_acctBalance=$this->sma->formatDecimal(($goods_available_for_sale))-$this->sma->formatDecimal(($end_of_period_stock));


	echo '
					<tr>
						<td><strong>'.lang('Cost_of_Goods_Sold').'</strong></td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($sales_cost_of_good_sold_acctBalance)).'</strong></td>

					</tr>
		';
				}









	}else{


	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);
	$acc = (array) $acc[0];

	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$sales_cost_of_good_sold_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'],$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($sales_cost_of_good_sold_acctBalance[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

	$sales_cost_of_good_sold_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

	echo '
					<tr>
						<td><strong>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</strong></td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($sales_cost_of_good_sold_acctBalance)).'</strong></td>

					</tr>
		';

				}

				$dbName=str_replace("cpesoft_","",$this->db->database);
        if($dbName=="bader"){
				///////////


	$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);

			$accountChilds_levels=$accountChilds;


			$accountChilds = (array) $accountChilds[0];

			/*if($levels)
				$accountCategory=$accountChilds_levels;
			else*/
				//$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID'],$levels);
				$accountCategory=$this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'],$levels);

			foreach ($accountCategory as $account){






			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}

					if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}


			if ($accountBalance == 0){
				continue;
			}

				//$maexpensesTotal += $accountBalance;
				$sales_cost_of_good_sold_acctBalance += $accountBalance;
			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';



			}
			echo '
					<tr>
						<td><strong>'.lang('Total').' '.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</strong></td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($sales_cost_of_good_sold_acctBalance)).'</strong></td>

					</tr>
		';
		//////////
		}


	}


			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Gross_income').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$Gross_income[$icount]=$this->sma->formatDecimal(($net_sales[$icount]))+$this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance[$icount]));


							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($Gross_income[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{
			$gross_income=$this->sma->formatDecimal(($net_sales))+$this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance));


	echo '
					<tr>
						<td><strong>'.lang('Gross_income').'</strong></td>
						<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($gross_income)).'</strong></td>

					</tr>
		';
				}



		if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Expenses').':</strong></td>
							';


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					for($i=1;$i<=$_POST['compare_periods'];$i++){
						echo '
							<td></td>
							';

					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}
					echo '						</tr>
					';

				}else{
		echo '
						<tr>
							<td><strong>'.lang('Expenses').':</strong></td>
							<td></td>
							</tr>
			';
				}




		if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Administrative_Expenses').':</strong></td>
							';


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					for($i=1;$i<=$_POST['compare_periods'];$i++){
						echo '
							<td></td>
							';

					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}
					echo '						</tr>
					';

				}else{
					echo '
						<tr>
							<td><strong>'.lang('Administrative_Expenses').'</strong></td>
							<td></td>
							</tr>
			';
				}


// 	case $this->startsWith($accountID, '100'):

			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{
				//$FStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
				$LEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
				$FStartDate = date('Y-m-d', strtotime('+1 days', strtotime($LEndDate)));
				$FStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($FStartDate)));
				for($i=1;$i<$_POST['compare_periods'];$i++){

						$FStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($FStartDate)));
					}
					//echo $FStartDate;

				}



			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
			$accountChilds_levels=$accountChilds;

			$accountChilds = (array) $accountChilds[0];
			/*if($levels && $levels==2)
				$accountCategory=$accountChilds_levels;
			else*/
				//$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID'],$levels);
				$accountCategory=$this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'],$levels);

			foreach ($accountCategory as $account){


				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{
						$accountAllBalance = $this->accounts_model->getAccountTotal($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58',NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

					if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58',$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance=$accountAllBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}

					if ($accountAllBalance == 0){
							continue;
						}


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));




					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							';





					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

						if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,$_POST['grpId'],$_POST['coId'],$_POST['brId'],$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance[$icount]=$accountBalance[$icount]+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}

						$opexpensesTotalWD[$icount] += $accountBalance[$icount];
					//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';

							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

				//print_r($account);
			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}

			if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}



			if ($accountBalance == 0){
				continue;
			}

				$opexpensesTotal += $accountBalance;
			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';

				}
			}





			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Total').' '.lang('Administrative_Expenses').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){


							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($opexpensesTotalWD[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

			echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('Administrative_Expenses').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($opexpensesTotal)).'</strong></td>
						</tr>
		';
		}




			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Selling_And_Markiting_Expenses').'</strong></td>
							';


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					for($i=1;$i<=$_POST['compare_periods'];$i++){
						echo '
							<td></td>
							';

					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}
					echo '						</tr>
					';

				}else{
				echo '
						<tr>
							<td><strong>'.lang('Selling_And_Markiting_Expenses').'</strong></td>
							<td></td>
							</tr>
			';
				}


	$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);

			$accountChilds_levels=$accountChilds;


			$accountChilds = (array) $accountChilds[0];

			/*if($levels)
				$accountCategory=$accountChilds_levels;
			else*/
				//$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID'],$levels);
				$accountCategory=$this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'],$levels);

			foreach ($accountCategory as $account){

				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{

						$accountAllBalance = $this->accounts_model->getAccountTotal($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58' ,NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58',$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance=$accountAllBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}

						if ($accountAllBalance == 0){
							continue;
						}


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));




					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							';





					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance[$icount]=$accountBalance[$icount]+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}


						$maexpensesTotalWD[$icount] += $accountBalance[$icount];
					//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';

							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{




			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}

					if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}


			if ($accountBalance == 0){
				continue;
			}

				$maexpensesTotal += $accountBalance;
			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';


			}
			}



			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Total').' '.lang('Selling_And_Markiting_Expenses').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){


							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($maexpensesTotalWD[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{
			        echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('Selling_And_Markiting_Expenses').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($maexpensesTotal)).'</strong></td>
						</tr>
					';
				}




		if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Total').' '.lang('Expenses').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){


							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($opexpensesTotalWD[$icount]) + abs($maexpensesTotalWD[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

		echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('Expenses').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($opexpensesTotal) + abs($maexpensesTotal)).'</strong></td>
						</tr>
		';
				}





		if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Net_operating_income').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$net_operating_income[$icount]=$this->sma->formatDecimal(($Gross_income[$icount]) + (($opexpensesTotalWD[$icount]) + ($maexpensesTotalWD[$icount])));


							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($net_operating_income[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

		$Net_operating_income=$this->sma->formatDecimal(($gross_income) + (($opexpensesTotal) + ($maexpensesTotal)));

		echo '
							<tr>
								<td><strong>'.lang('Net_operating_income').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($Net_operating_income)).'</strong></td>
						</tr>
		';

				}

		if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Other_Revenues').':</strong></td>
							';


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					for($i=1;$i<=$_POST['compare_periods'];$i++){
						echo '
							<td></td>
							';

					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}
					echo '						</tr>
					';

				}else{

		echo '
						<tr>
							<td><strong>'.lang('Other_Revenues').':</strong></td>
							<td></td>
							</tr>
			';
				}


		$otherRevenuesAccounts = $this->accounts_model->getOtherRevenuesAccounts($levels);
			//print_r($otherRevenuesAccounts);

			foreach ($otherRevenuesAccounts as $account){
				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{

						$accountAllBalance = $this->accounts_model->getAccountTotal($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58' ,NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58' ,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance=$accountAllBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}

						if ($accountAllBalance == 0){
							continue;
						}


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));




					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							';





					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);


									if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance[$icount]=$accountBalance[$icount]+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}

						$orevenueTotalWD[$icount] += $accountBalance[$icount];
					//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';

							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}

							if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}


			if ($accountBalance == 0){
				continue;
			}

				$orevenueTotal += $accountBalance;
			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';


			}


			}



			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Total').' '.lang('Other_Revenues').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){


							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($orevenueTotalWD[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{


			echo '
							<tr>
								<td><strong>'.lang('Total').' '.lang('Other_Revenues').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($orevenueTotal)).'</strong></td>
						</tr>
		';

				}





		if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Other_Expenses').':</strong></td>
							';


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					for($i=1;$i<=$_POST['compare_periods'];$i++){
						echo '
							<td></td>
							';

					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}
					echo '						</tr>
					';

				}else{

		echo '
						<tr>
							<td><strong>'.lang('Other_Expenses').':</strong></td>
							<td></td>
							</tr>
			';

				}

		$accounts_operating_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
		$accounts_operating_expenses_acct = (array) $accounts_operating_expenses_acct[0];

			//$this->sma->print_arrays($accounts_operating_expenses_acct);

		$accounts_marketing_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
		$accounts_marketing_expenses_acct = (array) $accounts_marketing_expenses_acct[0];

		if($dbName=="bader"){
			$sales_cost_of_good_sold_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);
			$sales_cost_of_good_sold_acct = (array) $sales_cost_of_good_sold_acct[0];
			$sales_cost_of_good_sold_acct = $this->accounts_model->getAccountByID($sales_cost_of_good_sold_acct['parent_id']);
			//$this->sma->print_arrays($sales_cost_of_good_sold_acct->accountID);
			$otherExpensesAccounts = $this->accounts_model->getOtherExpensesAccountsV2($accounts_operating_expenses_acct['accountID'],$accounts_marketing_expenses_acct['accountID'],$sales_cost_of_good_sold_acct->accountID,$levels);
		}else{
			$otherExpensesAccounts = $this->accounts_model->getOtherExpensesAccounts($accounts_operating_expenses_acct['accountID'],$accounts_marketing_expenses_acct['accountID'],$levels);
		}
		//$sales_cost_of_good_sold_acct=$this->site->getAccountsSettings()->sales_cost_of_good_sold_acct;
		//$sales_cost_of_good_sold_acct_parent_id=$this->accounts_model->getAccounts($sales_cost_of_good_sold_acct)[0]->parent_id;
		//$this->sma->print_arrays($otherExpensesAccounts);

			foreach ($otherExpensesAccounts as $account){
			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{

						$accountAllBalance = $this->accounts_model->getAccountTotal($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58',NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);
									if($levels){
						if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$FStartDate.' 00:00:00',$LEndDate.' 23:59:58',$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance=$accountAllBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}


						if ($accountAllBalance == 0){
							continue;
						}


					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));




					echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							';





					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							if($levels){
								if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58' ,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance[$icount]=$accountBalance[$icount]+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}

						$oexpensesTotalWD[$icount] += $accountBalance[$icount];
					//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';

							echo '
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance[$icount])).'</td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

			if (!empty($_POST)){
				$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}
			else {
			$accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),NULL,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

			}

			//$this->sma->print_arrays($accountBalance);


					if($levels){
								if($levels==$account->level)
						{
							$accountCategory2=$this->accounts_model->getAccountItemByLevel($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							$accountBalance=$accountBalance+$accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
						}
					}


			if ($accountBalance == 0){
				continue;
			}

				$oexpensesTotal += $accountBalance;
			echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>
							<td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
							</tr>
			';


			}
			}



			if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Total').' '.lang('Other_Expenses').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){


							echo '
							<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($oexpensesTotalWD[$icount])).'</strong></td>
							';


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

			echo '
							<tr>
								<td ><strong>'.lang('Total').' '.lang('Other_Expenses').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs($oexpensesTotal)).'</strong></td>
						</tr>
		';

				}













	echo '
							<tr>
								<td></td>
								<td></td>
							</tr>';

	if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{


					echo '
						<tr>
							<td><strong>'.lang('Net_Income').'</strong></td>
							';



					//$effectiveStartDate = (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) : '');
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_POST['compare_periods'];$i++){

					if((($net_operating_income[$icount]) + ($orevenueTotalWD[$icount]) + ($oexpensesTotalWD[$icount]))<0){
							echo '
							<td class="text-right" style="color:green;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs(($net_operating_income[$icount]) + ($orevenueTotalWD[$icount]) + ($oexpensesTotalWD[$icount]))).'</strong></td>';
					}else{
							echo '
							<td class="text-right" style="color:red;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs(($net_operating_income[$icount]) + ($orevenueTotalWD[$icount]) + ($oexpensesTotalWD[$icount]))).'</strong></td>';
					}


					//$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_POST['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}else{

	/*echo '
							<tr>
								<td><strong>'.lang('Net_Income').'</strong></td>
								<td class="text-right" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs(($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal))).'</strong></td>
							</tr>
	';*/


	echo '

							<tr>
								<td><strong>'.lang('Net_Income').'</strong></td>';
			if((($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal))<0){
			echo '
								<td class="text-right" style="color:green;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs(($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal))).'</strong></td>
								';
			}else{
				echo '
								<td class="text-right" style="color:red;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney(abs(($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal))).'</strong></td>
								';
			}
			echo '
							</tr>
	';


				}
?>
		</tbody>
	</table>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Income_Statement'); ?>','L');
		    return false;
        });
		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/incomestatement/0/xls/?v=' . $v)?>&period_year="+$('#period_year').val();
            return false;
        });

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
$("#incomestatementForm").submit();

});



    });
</script>



