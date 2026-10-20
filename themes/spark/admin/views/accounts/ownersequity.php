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
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
$levels=$this->input->post('levels')>1 ? $this->input->post('levels') : NULL;
?>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('submit_report') && !$this->input->post('start_date') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
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

<div class="spark-ownersequity-page">
    <div class="spark-ownersequity-header">
        <div class="spark-ownersequity-header-start">
            <div class="spark-ownersequity-header-icon">
                <i class="fa fa-pie-chart"></i>
            </div>
            <div class="spark-ownersequity-header-info">
                <h1 class="spark-ownersequity-title">
                    <?= lang('Statement_of_owners_equity') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-ownersequity-subtitle">
                    <?= lang('manage_ownersequity_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-ownersequity-header-actions">
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

                    <?php echo admin_form_open('accounts/ownersequity',' autocomplete="off"'); ?>
                    <div class="col-sm-12">


						<input type="hidden" name="id" value="<?= $filter_id ?>" id="filter_id"/>





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

						<!--<div class="col-sm-2">
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

						<div class="col-sm-2">
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
                        </div> -->

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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Statement_of_owners_equity'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Statement_of_owners_equity') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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
				<th><?= lang('Declaration') ?></th>
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
				<th><?= lang('Credit') ?></th>
				<th><?= lang('Debit') ?></th>
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


echo '
						<tr>
							<td><strong>'.lang('Capital_account').' '.lang('Start_Period').'</strong></td>
							<td></td>
							<td></td>
							</tr>
			';


                    $accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_ready_funds_acct);
                    $accountChilds_levels=$accountChilds;

                    $accountChilds = (array) $accountChilds[0];
                    /*if($levels && $levels==2)
                        $accountCategory=$accountChilds_levels;
                    else*/
                    $accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID']);

                    foreach ($accountCategory as $account){




                        //print_r($account);
                    if (!empty($_POST)){
                        $accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''));
                        $accountBalanceAdd = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:01' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''));

                    }
                    else {
                        $accountBalance = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''));
                        $accountBalanceAdd = $this->accounts_model->getAccountTotal($account->id,(isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']).' 00:00:01' : ''),(isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''));

                    }





                    if ($accountBalance != 0){
                        //continue;


                    echo '
						<tr>
							<td>'.($this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn).'</td>';
                            if($accountBalance<0){
                                echo '
                                <td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
                                <td>0</td>';

                            }else{
                                echo '
                                <td>0</td>
                                <td class="text-right">'.$this->sma->formatMoney(abs($accountBalance)).'</td>
                                ';

                            }

                    }

                    $capita_acctBalance += $accountBalance;
                    if($accountBalanceAdd>0)
                    $accountBalanceAddDt += $accountBalanceAdd;
                    else
                    $add_capita_acctBalance += $accountBalanceAdd;



                    }





	echo '
						<tr>
							<td><strong>'.lang('Total').' '.lang('Capital_account').' '.lang('Start_Period').'</strong></td>';
                            if($capita_acctBalance<0){
                                echo '
                                <td class="text-right">'.$this->sma->formatMoney(abs($capita_acctBalance)).'</td>
                                <td>0</td>';

                            }else{
                                echo '
                                <td>0</td>
                                <td class="text-right">'.$this->sma->formatMoney(abs($capita_acctBalance)).'</td>
                                ';

                            }

                            echo '
							</tr>
			';







	echo '
						<tr>
							<td><strong>'.lang('Additional_Capital_account').'</strong></td>';
                            if($add_capita_acctBalance<0){
                                echo '
                                <td class="text-right">'.$this->sma->formatMoney(abs($add_capita_acctBalance)).'</td>
                                <td>0</td>';

                            }else{
                                echo '
                                <td>0</td>
                                <td class="text-right">'.$this->sma->formatMoney(abs($add_capita_acctBalance)).'</td>
                                ';

                            }

                            echo '
							</tr>
			';



$total_capita=$capita_acctBalance+$add_capita_acctBalance;


            echo '
						<tr>
							<td><strong>'.lang('Total').'</strong></td>';
                            if($total_capita<0){
                                echo '
                                <td class="text-right">'.$this->sma->formatMoney(abs($total_capita)).'</td>
                                <td>0</td>';

                            }else{
                                echo '
                                <td>0</td>
                                <td class="text-right">'.$this->sma->formatMoney(abs($total_capita)).'</td>
                                ';

                            }

                            echo '
							</tr>
			';


            echo '
            <tr>
                <td><strong>'.lang('Losses_Stage').'</strong></td>';
                if($accountBalanceAddDt<0){
                    echo '
                    <td class="text-right">'.$this->sma->formatMoney(abs($accountBalanceAddDt)).'</td>
                    <td>0</td>';

                }else{
                    echo '
                    <td>0</td>
                    <td class="text-right">'.$this->sma->formatMoney(abs($accountBalanceAddDt)).'</td>
                    ';

                }

                echo '
                </tr>
';
$final_owners_equity=$total_capita+$accountBalanceAddDt;


echo '
<tr>
    <td><strong>'.lang('Total').' '.lang('Statement_of_owners_equity').'</strong></td>';
    if($final_owners_equity<0){
        echo '
        <td class="text-right">'.$this->sma->formatMoney(abs($final_owners_equity)).'</td>
        <td>0</td>';

    }else{
        echo '
        <td>0</td>
        <td class="text-right">'.$this->sma->formatMoney(abs($final_owners_equity)).'</td>
        ';

    }

    echo '
    </tr>
';


        echo '
<tr>
    <td><strong>'.lang('Net_Income').'</strong></td>';
    if($incomestatement<0){
        echo '
        <td class="text-right">'.$this->sma->formatMoney(abs($incomestatement)).'</td>
        <td>0</td>';

    }else{
        echo '
        <td>0</td>
        <td class="text-right">'.$this->sma->formatMoney(abs($incomestatement)).'</td>
        ';

    }

    echo '
    </tr>
';

$final_total_capita=$total_capita+$incomestatement+$accountBalanceAddDt;


echo '
            <tr>
            <td><strong>'.lang('Capital_account').' '.lang('End_Period').'</strong></td>';
            if($final_total_capita<0){
                    echo '
                    <td class="text-right">'.$this->sma->formatMoney(abs($final_total_capita)).'</td>
                    <td>0</td>';

                }else{
                    echo '
                    <td>0</td>
                    <td class="text-right">'.$this->sma->formatMoney(abs($final_total_capita)).'</td>
                    ';

                }

                echo '
                </tr>
';


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
        PrPDF('pr_pdf','<?= lang('Statement_of_owners_equity'); ?>','L');
		    return false;
        });
		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/ownersequity/0/xls/?v=' . $v)?>";
            return false;
        });


    });
</script>



