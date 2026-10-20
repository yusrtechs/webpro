<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>

	#pr_pdf table td,#pr_pdf table th{
		padding: 0.2rem;
		font-size: 13px;
	}



    /*  table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }
*/

	 .tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }



</style>
<?php


if (empty($_POST)){
	$_POST['brId']=$this->session->userdata('brId');
	//
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
if ($this->input->post('show_Opening_balance')) {
    $v .= '&show_Opening_balance=' . $this->input->post('show_Opening_balance');
}
if ($this->input->post('show_Closing_balance')) {
    $v .= '&show_Closing_balance=' . $this->input->post('show_Closing_balance');
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

$levels=$this->input->post('levels')>=1 ? $this->input->post('levels') : NULL;

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


        //$('#form').hide();
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
        <h2 class="blue"><i class="fa-fw fa fa-filter"></i><?= lang('Trial_balance'); ?> <?php
            if ($this->input->post('start_date')) {
                echo lang('From').' '. substr($this->input->post('start_date'),0,10) .' '.lang('to').' ' . substr($this->input->post('end_date'),0,10);
            }?></h2>
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
                <li class="dropdown"><a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>"><i class="icon fa fa-file-excel-o"></i></a></li>

				<li class="dropdown">
						<a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>
			</ul>
        </div>


    </div>
    <div class="box-content">

	<div class="row">
	<div class="col-sm-12">

	<div id="form">

                    <?php echo admin_form_open('accounts/trialbalance',' autocomplete="off" id="trialbalanceForm"'); ?>
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
                                <label class="control-label" for="show_Opening_balance"><?= lang('Opening_balance'); ?></label>
                                <?php
                                $ss['0'] = lang('no');
                                $ss['1'] = lang('yes');

                                echo form_dropdown('show_Opening_balance', $ss, (isset($_POST['show_Opening_balance']) ? $_POST['show_Opening_balance'] : ''), 'class="form-control" id="show_Opening_balance" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Opening_balance') . '"');
                                ?>
                            </div>
                        </div>


					    <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="show_Closing_balance"><?= lang('Closing_balance'); ?></label>
                                <?php
                                $ss['0'] = lang('no');
                                $ss['1'] = lang('yes');

                                echo form_dropdown('show_Closing_balance', $ss, (isset($_POST['show_Closing_balance']) ? $_POST['show_Closing_balance'] : ''), 'class="form-control" id="show_Closing_balance" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Closing_balance') . '"');
                                ?>
                            </div>
                        </div>

						<div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="levels"><?= lang('Levels'); ?></label>
                                <?php
                                $levels_arr['0'] = lang('all');
                                $levels_arr['1'] = lang('Level_1');
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
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Trial_balance'); ?></font></b></td>
					</tr>
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Trial_balance') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>
				</div>
			</div>
		</div>



	<table class="table table-striped table-bordered table-hover tableData" >
		<thead class="thead-dark">
			<tr class="text-center">
				<th  class="col-xs-12" rowspan="2"><?= lang('Account') ?></th>
				<?php
				if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{
				?>
				<th  class="col-xs-2" colspan="2"><?= lang('Opening_balance') ?></th>
				<?php
					}
				?>
				<th  class="col-xs-2" colspan="2"><?= lang('Movement') ?></th>
				<th  class="col-xs-2" colspan="2"><?= lang('Net_Movement') ?></th>
				<?php
				if($_POST['show_Closing_balance'] && $_POST['show_Closing_balance']!='0')
					{
				?>
				<th  class="col-xs-2" colspan="2"><?= lang('Closing_balance') ?></th>
				<?php
					}
				?>

			</tr>
			<tr class="text-center">
				<?php
				if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{
				?>
				<th><?= lang('Debit').' ('.$this->Settings->symbol.')' ?></th>
				<th><?= lang('Credit').' ('.$this->Settings->symbol.')' ?></th>
				<?php
					}
				?>
				<th><?= lang('Debit').' ('.$this->Settings->symbol.')' ?></th>
				<th><?= lang('Credit').' ('.$this->Settings->symbol.')' ?></th>
				<th><?= lang('Debit').' ('.$this->Settings->symbol.')' ?></th>
				<th><?= lang('Credit').' ('.$this->Settings->symbol.')' ?></th>
				<?php
				if($_POST['show_Closing_balance'] && $_POST['show_Closing_balance']!='0')
					{
				?>
				<th><?= lang('Debit').' ('.$this->Settings->symbol.')' ?></th>
				<th><?= lang('Credit').' ('.$this->Settings->symbol.')' ?></th>
				<?php
					}
				?>

			</tr>
		</thead>
		<tbody>
			<?php

			if($_POST['start_date']){
		$ts1 = strtotime($this->sma->fld($_POST['start_date']));
		$year1 = date('Y', $ts1);
		$prev_start_date=$this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
		$prev_start_date=$prev_start_date.' 00:00:00';
		$prev_end_date=date('Y-m-d', strtotime('-1 days', strtotime($this->sma->fld($_POST['start_date'])))).' 23:59:58';
			}

			$totalDebitAccount  = 0;
			$totalCreditAccount = 0;
			$totalDebitAccountOB  = 0;
			$totalCreditAccountOB = 0;
			$totalDebitAccountM  = 0;
			$totalCreditAccountM = 0;
			$totalDebitAccountNM  = 0;
			$totalCreditAccountNM = 0;
			$totalDebitAccountCB  = 0;
			$totalCreditAccountCB = 0;


			$accounts = array();

			//asort($accountList);

			foreach ($accountList as $account){
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])){
					$accounts[$account['accountCategory']] = array($account);
				}
				else {
					array_push($accounts[$account['accountCategory']], $account);
				}
			}

			//$this->sma->print_arrays($accounts);


			$accountCategories = array_keys($accounts);
			$accountOrder = 0;
			foreach ($accounts as $accountCategory){
				if ($this->Settings->user_language == 'arabic'){
				echo '
				<tr>
				<td><strong>'.$accountCategories[$accountOrder].':</strong></td>';
				}else{
					$categoryNameEn=$accountCategories[$accountOrder];
					if($accountCategories[$accountOrder]=='اصول'){
						$categoryNameEn='Assets';
					}
					if($accountCategories[$accountOrder]=='الإلتزامات وحقوق الملكية'){
						$categoryNameEn='Liabilities &Equity Owners';
					}
					if($accountCategories[$accountOrder]=='المصروفات'){
						$categoryNameEn='Expenses';
					}
					if($accountCategories[$accountOrder]=='الإيرادات'){
						$categoryNameEn='Revenues';
					}

					echo '
				<tr>
				<td><strong>'.$categoryNameEn.':</strong></td>';
				}
				if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{
				echo '
				<td></td>
				<td></td>';
					}
				echo '
				<td></td>
				<td></td>';
				echo '
				<td></td>
				<td></td>';
				if($_POST['show_Closing_balance'] && $_POST['show_Closing_balance']!='0')
					{
				echo '
				<td></td>
				<td></td>';
					}


				echo '
				</tr>
				';
				$accountOrder += 1;

				$categoryCount = 0;
				$textDecor = 'none';

				$moneySign = 'TK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

				foreach ($accountCategory as $account){
					//echo $account['accountID'];
					$categoryCount++;
					if ($categoryCount == count($accountCategory)){
						//$textDecor = 'underline';
					}




					if (!empty($_POST)){
						$accountTotal = $this->accounts_model->getAccountTotalDebitCredit($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}
					else {
						$accountTotal = $this->accounts_model->getAccountTotalDebitCredit($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}

					//$this->sma->print_arrays($_POST['brId']);


					if($levels){
						if($levels==$account['level'])
						{
							$accountCategory2=$this->accounts_model->getAccountItemDebitCreditByLevel($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotal['debitTotal']=$accountTotal['debitTotal']+$accountCategory2['debitTotal'];
							$accountTotal['creditTotal']=$accountTotal['creditTotal']+$accountCategory2['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}

					if (!empty($_POST)){
						$accountTotalNet = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}
					else {
						$accountTotalNet = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}

					//


					if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryNet=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalNet['debitTotal']=$accountTotalNet['debitTotal']+$accountCategoryNet['debitTotal'];
							$accountTotalNet['creditTotal']=$accountTotalNet['creditTotal']+$accountCategoryNet['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}

					//$this->sma->print_arrays($accountTotalNet);

					if($prev_start_date){
					if (!empty($_POST)){
						$accountTotalCB = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],$prev_start_date,(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}
					else {
						$accountTotalCB = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],$prev_start_date,(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);
					}


						if($levels){
						if($levels==$account['level'])
						{
							$accountCategory22=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],$prev_start_date,(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalCB['debitTotal']=$accountTotalCB['debitTotal']+$accountCategory22['debitTotal'];
							$accountTotalCB['creditTotal']=$accountTotalCB['creditTotal']+$accountCategory22['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


					}else{
							//$this->sma->print_arrays($accountTotal);

						$accountTotalCB=$accountTotal;
					}

							//$this->sma->print_arrays($accountTotalCB);

					if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{

					if (!empty($_POST)){
						$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryOB2=$this->accounts_model->getAccountItemDebitCreditByLevelOB($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$accountCategoryOB2['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$accountCategoryOB2['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


						if($prev_start_date){
						$prev_accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],$prev_start_date,$prev_end_date,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

									if($levels){
						if($levels==$account['level'])
						{
							$accountCategory3=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],$prev_start_date,$prev_end_date,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$prev_accountTotalOB['debitTotal']=$prev_accountTotalOB['debitTotal']+$accountCategory3['debitTotal'];
							$prev_accountTotalOB['creditTotal']=$prev_accountTotalOB['creditTotal']+$accountCategory3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$prev_accountTotalOB['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$prev_accountTotalOB['creditTotal'];



						}


						$accountTotalM = $this->accounts_model->getAccountTotalDebitCreditM($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

						if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryM3=$this->accounts_model->getAccountItemDebitCreditByLevelM($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalM['debitTotal']=$accountTotalM['debitTotal']+$accountCategoryM3['debitTotal'];
							$accountTotalM['creditTotal']=$accountTotalM['creditTotal']+$accountCategoryM3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


					}
					else {
						$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);


							if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryOB2=$this->accounts_model->getAccountItemDebitCreditByLevelOB($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$accountCategoryOB2['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$accountCategoryOB2['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}





						if($prev_start_date){
						$prev_accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],$prev_start_date,$prev_end_date,$_POST['grpId'],$_POST['coId'],$_POST['brId']);



									if($levels){
						if($levels==$account['level'])
						{
							$accountCategory3=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],$prev_start_date,$prev_end_date,$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$prev_accountTotalOB['debitTotal']=$prev_accountTotalOB['debitTotal']+$accountCategory3['debitTotal'];
							$prev_accountTotalOB['creditTotal']=$prev_accountTotalOB['creditTotal']+$accountCategory3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}

							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$prev_accountTotalOB['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$prev_accountTotalOB['creditTotal'];
						}


						$accountTotalM = $this->accounts_model->getAccountTotalDebitCreditM($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

						if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryM3=$this->accounts_model->getAccountItemDebitCreditByLevelM($account['id'],(isset($_POST['start_date']) && $_POST['start_date']!=NULL ? $this->sma->fld($_POST['start_date']).' 00:00:00' : ''),(isset($_POST['end_date']) && $_POST['end_date']!=NULL ? $this->sma->fld($_POST['end_date']).' 23:59:58' : ''),$_POST['grpId'],$_POST['coId'],$_POST['brId']);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalM['debitTotal']=$accountTotalM['debitTotal']+$accountCategoryM3['debitTotal'];
							$accountTotalM['creditTotal']=$accountTotalM['creditTotal']+$accountCategoryM3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}
					}
					//echo $prev_accountTotalOB;





					}else{
						$accountTotalM=$accountTotal;
					}

						$accountTotalNM=$accountTotalM;

					//$this->sma->print_arrays($accountTotalM);
					//print_r($accountTotal);

					// if ($accountTotal == 0){
					// 	continue;
					// }

					/*if($accountTotalCB['debitTotal']==0 && $accountTotalCB['creditTotal']==0){
					}else*/{

					if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{
						/*if($accountTotalOB['debitTotal']>$accountTotalOB['creditTotal']){
							$accountTotalOB['debitTotal']=($accountTotalOB['debitTotal']-$accountTotalOB['creditTotal']);
							$accountTotalOB['creditTotal']=0;
						}else if($accountTotalOB['debitTotal']<$accountTotalOB['creditTotal']){
							$accountTotalOB['creditTotal']=($accountTotalOB['creditTotal']-$accountTotalOB['debitTotal']);
							$accountTotalOB['debitTotal']=0;
						}else{
							$accountTotalOB['debitTotal']=0;
							$accountTotalOB['creditTotal']=0;
						}*/



					}
					//$this->sma->print_arrays($accountTotalOB);

					$totalDebitAccountOB += $accountTotalOB['debitTotal'];
					$totalCreditAccountOB += $accountTotalOB['creditTotal'];





					$totalDebitAccountNM += $accountTotalNM['debitTotal'];
					$totalCreditAccountNM += $accountTotalNM['creditTotal'];


						/*if($accountTotalM['debitTotal']>$accountTotalM['creditTotal']){
							$accountTotalM['debitTotal']=($accountTotalM['debitTotal']-$accountTotalM['creditTotal']);
							$accountTotalM['creditTotal']=0;
						}else if($accountTotalM['debitTotal']<$accountTotalM['creditTotal']){
							$accountTotalM['creditTotal']=($accountTotalM['creditTotal']-$accountTotalM['debitTotal']);
							$accountTotalM['debitTotal']=0;
						}else{
							$accountTotalM['debitTotal']=0;
							$accountTotalM['creditTotal']=0;
						}*/


					$totalDebitAccountM += $accountTotalNet['debitTotal'];
					$totalCreditAccountM += $accountTotalNet['creditTotal'];



						/*if($accountTotalCB['debitTotal']>$accountTotalCB['creditTotal']){
							$accountTotalCB['debitTotal']=($accountTotalCB['debitTotal']-$accountTotalCB['creditTotal']);
							$accountTotalCB['creditTotal']=0;
						}else if($accountTotalCB['debitTotal']<$accountTotalCB['creditTotal']){
							$accountTotalCB['creditTotal']=($accountTotalCB['creditTotal']-$accountTotalCB['debitTotal']);
							$accountTotalCB['debitTotal']=0;
						}else{
							$accountTotalCB['debitTotal']=0;
							$accountTotalCB['creditTotal']=0;
						}
						*/


					$totalDebitAccountCB += $accountTotalCB['debitTotal'];
					$totalCreditAccountCB += $accountTotalCB['creditTotal'];

					$zeroFlage=0;
					if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{
						if($accountTotalOB['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalOB['creditTotal']!=0)
							$zeroFlage=1;

					}
						if($accountTotalNM['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalNM['creditTotal']!=0)
							$zeroFlage=1;

						if($accountTotalNet['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalNet['creditTotal']!=0)
							$zeroFlage=1;


					if($_POST['show_Closing_balance'] && $_POST['show_Closing_balance']!='0')
					{
						if($accountTotalCB['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalCB['creditTotal']!=0)
							$zeroFlage=1;

					}
					if($zeroFlage!=0)
					{
						if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
					echo '
					<tr>
					<td>('.$account['accountID'].') '.$account['accountName'].'</td>';
						}else{
							echo '
					<tr>
					<td>('.$account['accountID'].') '.$account['accountNameEn'].'</td>';
						}
					if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{

					echo '
					<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalOB['debitTotal'] != abs($accountTotalOB['debitTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalOB['debitTotal'])).')';
					}
					else {
						echo $this->sma->formatDecimal($accountTotalOB['debitTotal']);
					}
					echo '<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalOB['creditTotal'] != abs($accountTotalOB['creditTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalOB['creditTotal'])).')';

					}
					else {
						echo $this->sma->formatDecimal($accountTotalOB['creditTotal']);
					}
					echo '</td>';

					}



					echo '
					<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalNM['debitTotal'] != abs($accountTotalNM['debitTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalNM['debitTotal'])).')';
					}
					else {
						echo $this->sma->formatDecimal($accountTotalNM['debitTotal']);
					}
					echo '<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalNM['creditTotal'] != abs($accountTotalNM['creditTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalNM['creditTotal'])).')';

					}
					else {
						echo $this->sma->formatDecimal($accountTotalNM['creditTotal']);
					}
					echo '</td>';




					echo '
					<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalNet['debitTotal'] != abs($accountTotalNet['debitTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalNet['debitTotal'])).')';
					}
					else {
						echo $this->sma->formatDecimal($accountTotalNet['debitTotal']);
					}
					echo '<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalNet['creditTotal'] != abs($accountTotalNet['creditTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalNet['creditTotal'])).')';

					}
					else {
						echo $this->sma->formatDecimal($accountTotalNet['creditTotal']);
					}
					echo '</td>';

					if($_POST['show_Closing_balance'] && $_POST['show_Closing_balance']!='0')
					{

					echo '
					<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalCB['debitTotal'] != abs($accountTotalCB['debitTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalCB['debitTotal'])).')';
					}
					else {
						echo $this->sma->formatDecimal($accountTotalCB['debitTotal']);
					}
					echo '<td style="text-decoration: '.$textDecor.';" class="text-right">';
					if ($accountTotalCB['creditTotal'] != abs($accountTotalCB['creditTotal'])){
						echo '('.$this->sma->formatDecimal(abs($accountTotalCB['creditTotal'])).')';

					}
					else {
						echo $this->sma->formatDecimal($accountTotalCB['creditTotal']);
					}
					echo '</td>';

					}
					echo '
					</tr>
					';
					}
					}

					// if ($account['accountSide'] == 'Left (Debit)'){
					// 	$totalDebitAccount += $accountTotal;
					// 	echo '
					// 	<tr>
					// 	<td>'.$account['accountName'].'</td>
					// 	<td style="text-decoration: '.$textDecor.';" class="text-right">'.$moneySign;
					// 	if ($accountTotal != abs($accountTotal)){
					// 		echo '('.number_format(abs($accountTotal), 0).')';
					// 	}
					// 	else {
					// 		echo number_format($accountTotal, 0);
					// 	}
					// 	echo '</td>
					// 	<td></td>
					// 	</tr>
					// 	';
					// }
					// else {
					// 	$totalCreditAccount += $accountTotal;
					// 	echo '
					// 	<tr>
					// 	<td>'.$account['accountName'].'</td>
					// 	<td></td>
					// 	<td style="text-decoration: '.$textDecor.';" class="text-right">'.$moneySign;
					// 	if ($accountTotal != abs($accountTotal)){
					// 		echo '('.number_format($accountTotal, 0).')';
					// 	}
					// 	else {
					// 		echo number_format($accountTotal, 0);
					// 	}
					// 	echo '</td>
					// 	</tr>
					// 	';
					// }

					$moneySign = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

				}
			}









			echo '
			<tr>
			<td class="text-center"><strong>'.lang("total").'</strong></td>';
			if($_POST['show_Opening_balance'] && $_POST['show_Opening_balance']!='0')
					{
			echo '
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalDebitAccountOB).'</strong></td>
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalCreditAccountOB).'</strong></td>
			';
					}
			echo '
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalDebitAccountNM).'</strong></td>
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalCreditAccountNM).'</strong></td>
			';
			echo '
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalDebitAccountM).'</strong></td>
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalCreditAccountM).'</strong></td>
			';
			if($_POST['show_Closing_balance'] && $_POST['show_Closing_balance']!='0')
					{
			echo '
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalDebitAccountCB).'</strong></td>
			<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;'.$this->sma->formatDecimal($totalCreditAccountCB).'</strong></td>
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

<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Trial_balance'); ?>','L');
		    return false;
        });

		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/trialbalance/0/xls/?v=' . $v)?>";
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
$("#trialbalanceForm").submit();

});

    });
</script>

