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
if (empty($_POST))
	{
		$_POST['brId']=$this->session->userdata('brId');
		//$_POST['start_date']=$this->sma->hrsd(date('Y-m-d'));
		$_POST['end_date']=$this->sma->hrsd(date('Y-m-d'));


$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
	if ($check_period || $check_period->period_status==1){
		$_POST['start_date']=$this->sma->hrsd($check_period->period_from_date);

	}else{

		$_POST['start_date']=$this->sma->hrsd($max_min_period_year->period_from_date);

	}
}

$v = '';

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


if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}
if ($this->input->post('customer')) {
    $v .= '&customer=' . $this->input->post('customer');
}
if ($this->input->post('salesman_id')) {
    $v .= '&salesman_id=' . $this->input->post('salesman_id');
}

if ($this->input->post('only_difference')) {
    $v .= '&only_difference=' . $this->input->post('only_difference');
}
if ($this->input->post('the_oldest')) {
    $v .= '&the_oldest=' . $this->input->post('the_oldest');
}

$only_difference     = isset($_POST['only_difference'])  ? $_POST['only_difference'] : '0';
$the_oldest     = isset($_POST['the_oldest'])  ? $_POST['the_oldest'] : '1';



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


		$('#glbrId').change(function () {
			var v = $(this).val();

        var uopt1 = $("#accustomer");


		if (v) {
            $.ajax({
                type: "get",
                async: false,
                url: site.base_url + 'branches/getCustomersBybrId/' + v,
                dataType: "json",
                success: function (scdata) {

                    if (scdata != null) {


$('#accustomer')
.find('option')
.remove()
.end()
.append('<option value="">'+lang.Select_customer+'</option>')
.val('')
;


$('#accustomer').val('').trigger('change.select2');



                        $.each(scdata, function () {
            $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

    });
    //$('#warehouse').val(scdata[0].id).trigger('change.select2');
                    } else {
                        $("#accustomer").select2("destroy").empty().attr("placeholder", lang.Select_customer).select2({allowClear: false,
                            placeholder: lang.Select_customer,
                            scdata: [{id: '', text: lang.Select_customer}]
                        });
                    }
                },
                error: function () {
                    //bootbox.alert('<?= lang('ajax_error') ?>');
                }
            });
        } else {
            $("#accustomer").select2("destroy").empty().attr("placeholder", lang.Select_customer).select2({allowClear: false,
                placeholder: lang.Select_customer,
                scdata: [{id: '', text: lang.Select_customer}]
            });
        }
		});




    });




</script>



<div class="spark-debtliability-page">
    <div class="spark-debtliability-header">
        <div class="spark-debtliability-header-start">
            <div class="spark-debtliability-header-icon">
                <i class="fa fa-credit-card"></i>
            </div>
            <div class="spark-debtliability-header-info">
                <h1 class="spark-debtliability-title">
                    <?= lang('Debt_and_Liability') . ($this->input->post('start_date') ? ' (' . lang('From') . ' ' . substr($this->input->post('start_date'),0,10) . ' ' . lang('to') . ' ' . substr($this->input->post('end_date'),0,10) . ')' : ''); ?>
                </h1>
                <p class="spark-debtliability-subtitle">
                    <?= lang('manage_debtliability_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-debtliability-header-actions">
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

                    <?php echo admin_form_open('accounts/debtliability',' autocomplete="off"'); ?>
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
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="glbrId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>

						<div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="salesman_id"><?= lang("Salesman"); ?></label>
                                <?php
                                $salM[""] = lang('select').' '.lang('Salesman');
                                foreach ($allSalesMan as $salesman) {
                                    $salM[$salesman->id] = $salesman->company;
                                }
                                echo form_dropdown('salesman_id', $salM, (isset($_POST['salesman_id']) ? $_POST['salesman_id'] : ""), 'class="form-control" id="salesman_id" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Salesman") . '"');
                                ?>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang('customer').'/'.lang('supplier'); ?></label>
                                <?php
                                $cs[''] = lang('select') . ' ' . lang('customer').'/'.lang('supplier');
                                foreach ($customers as $customer) {
                                    $cs[$customer->id] = $customer->accountID.' - '.(($this->Settings->user_language == "arabic" || $customer->accountNameEn=="") ? $customer->accountName : $customer->accountNameEn);
                                }
                                 echo form_dropdown('customer',$cs, (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="accustomer" class="form-control"  data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
                            </div>
                        </div>



                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('date', 'date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>

						<div class="col-sm-3" style="display:none;">
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

						<div class="col-sm-3" style="display:none;">
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
                                <label class="control-label" for="only_difference"><?= lang('Only_difference'); ?></label>
                                <?php
                                $ss['0'] = lang('no');
                                $ss['1'] = lang('yes');

                                echo form_dropdown('only_difference', $ss, (isset($_POST['only_difference']) ? $_POST['only_difference'] : ''), 'class="form-control" id="only_difference" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Only_difference') . '"');
                                ?>
                            </div>
                        </div>


						<div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="the_oldest"><?= lang('The_Oldest'); ?></label>
                                <?php
                                $ss['1'] = lang('yes');
                                $ss['0'] = lang('no');

                                echo form_dropdown('the_oldest', $ss, (isset($_POST['the_oldest']) ? $_POST['the_oldest'] : ''), 'class="form-control" id="the_oldest" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('The_Oldest') . '"');
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Debt_and_Liability'); ?></font></b></td>
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
					<?php
                    if($this->input->post('salesman_id')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Salesman').' : '.($this->site->getSalesmanByID($this->input->post('salesman_id'))->company);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('Debt_and_Liability') .' '. lang('On_date') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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
				<th><?= lang('customer')?>&nbsp;/&nbsp;<?= lang('supplier') ?></th>
				<?php

				//if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				if($_POST['compare_periods'] && $_POST['compared_to']!='0')
				{
					$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



					for($i=1;$i<=$_POST['compare_periods'];$i++){
						//if($i!=1)
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
					if($the_oldest){
						echo '<th>'.lang('The_Oldest').'</th>';

					}
				}



				?>
                <?php
                if($only_difference=='0'){
                    ?>

				<th><?= lang('Debit') ?></th>
                <th><?= lang('Credit') ?></th>
				<?php
                }
                ?>
                <th><?= lang('The_difference') ?></th>


			</tr>
		</thead>
		<tbody class="searchable text-center">
<?php
	$amountTotal  = 0;
	$pamountTotal  = 0;
	$dueTotal  = 0;
	$noDueTotal  = 0;
	$the_oldestTotal=0;
    $the_oldestTotalsp=0;
    $amountTotalsp  = 0;
	$pamountTotalsp  = 0;
	$fbalance=0;

   /*$date =date("Y-m-d");
   $day = 16;
   $newdate=date('Y-m-d', strtotime("+$day days"));
   echo "today is:".$date;
   echo "<br> and after 5 days is :".$newdate;
	echo $effectiveDate = date('Y-m-d', strtotime("-16 days", strtotime(date('Y-m-d'))));

	*/
	//echo $_POST['compare_periods'];
	//echo $effectiveDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime((isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']).' 23:59:59' : ''))));

	$accountOrder = 0;

	$moneySign = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';




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



			//<td><a target="_blank"href='.admin_url("sales/modal_view/".$sale->id).' data-toggle="modal" data-target="#myModal"> '.$sale->reference_no.'</a></td>

            if($only_difference=='0'){
                echo '
					<tr>
						<td colspan="4"><strong>'.lang('Debts').'</strong></td></tr>
						';

            }else{
                echo '
					<tr>
						<td colspan="2"><strong>'.lang('Debts').'</strong></td></tr>
						';
            }



			foreach ($saleList as $sale){

				if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
					echo '
					<tr>
						<td>'.$sale->customer.'</td>
						';
				}else{

					echo '
						<tr>
							<td>'.$sale->customer.'</td>
							';
				}





				$m_ver='';
				$m_ver .= '&start_date='.$_POST['start_date'];
				$m_ver .= '&end_date=' . $_POST['end_date'];
				$m_ver .= '&account=' . $sale->id;
				$m_ver .= '&due=1' ;
				$m_ver .= '&the_oldest=' . $the_oldest;
                $m_ver .= '&show_style=1';
                $m_ver .= '&show_Opening_balance=1';
                $m_ver .= '&Carry_over_balance=1';

				if($this->input->post('salesman_id')){
					$m_ver .= '&salesman_id=' . $this->input->post('salesman_id');

				}
				if($this->input->post('grpId')){
					$m_ver .= '&grpId=' . $this->input->post('grpId');

				}
				if($this->input->post('coId')){
					$m_ver .= '&coId=' . $this->input->post('coId');

				}
				if($this->input->post('brId')){
					$m_ver .= '&brId=' . $this->input->post('brId');

				}
                if($only_difference=='0'){

					echo '<td>'.$this->sma->formatMoney($sale->amountDT).'</td>
							';
                }
					$amountTotal += $sale->amountDT;


                    $amountTotalsp += $sale->amountCR;
                    $fbalance = $sale->amount ;
                    if($only_difference=='0'){

                        echo '<td>'.$this->sma->formatMoney($sale->amountCR).'</td>
							';
                    }

					echo '<td><a target="_blank"href='.admin_url("accounts/accountstatementprint/?" . $m_ver).' > '.$this->sma->formatMoney($sale->amount).'</a></td>
							';
                    echo '						</tr>
					';

			}





				//<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($dueTotal).'</strong></td>


			echo '</tbody><tfoot class="thead-dark">
						<tr class="text-center">
							<td><strong>'.lang('Total_Debts').'</strong></td>
							';




            if($only_difference=='0'){
				echo '<td style="border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($amountTotal).'</strong></td>';
				echo '<td style="border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($amountTotalsp).'</strong></td>';
            }
                echo '<td style="border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney(($amountTotal - $amountTotalsp)).'</strong></td>
							</tr>';








            if($only_difference=='0'){
                echo '
					<tr>
						<td colspan="4"><strong>'.lang('Liabilies').'</strong></td></tr>
						';

            }else{
                echo '
					<tr>
						<td colspan="2"><strong>'.lang('Liabilies').'</strong></td></tr>
						';
            }



			foreach ($parchList as $sale){

				if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
					echo '
					<tr>
						<td>'.$sale->customer.'</td>
						';
				}else{

					echo '
						<tr>
							<td>'.$sale->customer.'</td>
							';
				}





				$m_ver='';
				$m_ver .= '&start_date='.$_POST['start_date'];
				$m_ver .= '&end_date=' . $_POST['end_date'];
				$m_ver .= '&account=' . $sale->id;
				$m_ver .= '&due=1' ;
				$m_ver .= '&the_oldest=' . $the_oldest;
                $m_ver .= '&show_style=1';
                $m_ver .= '&show_Opening_balance=1';
                $m_ver .= '&Carry_over_balance=1';

				if($this->input->post('salesman_id')){
					$m_ver .= '&salesman_id=' . $this->input->post('salesman_id');

				}
				if($this->input->post('grpId')){
					$m_ver .= '&grpId=' . $this->input->post('grpId');

				}
				if($this->input->post('coId')){
					$m_ver .= '&coId=' . $this->input->post('coId');

				}
				if($this->input->post('brId')){
					$m_ver .= '&brId=' . $this->input->post('brId');

				}
                if($only_difference=='0'){

					echo '<td>'.$this->sma->formatMoney($sale->amountDT).'</td>
							';
                }
					$pamountTotal += $sale->amountDT;


                    $pamountTotalsp += $sale->amountCR;
                    $fbalance = $sale->amount ;
                    if($only_difference=='0'){

                        echo '<td>'.$this->sma->formatMoney($sale->amountCR).'</td>
							';
                    }

					echo '<td><a target="_blank"href='.admin_url("accounts/accountstatementprint/?" . $m_ver).' > '.$this->sma->formatMoney($sale->amount).'</a></td>
							';
                    echo '						</tr>
					';

			}

            echo '
						<tr class="text-center">
							<td><strong>'.lang('Total_Liabilies').'</strong></td>
							';




            if($only_difference=='0'){
				echo '<td style="border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($pamountTotal).'</strong></td>';
				echo '<td style="border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney($pamountTotalsp).'</strong></td>';
            }
                echo '<td style="border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney(($pamountTotal - $pamountTotalsp)).'</strong></td>
							</tr>';




            echo '
						<tr class="text-center">
							<td><strong>'.lang('The_difference_between_debts_and_liabilities').'</strong></td>
							';




            if($only_difference=='0'){
				echo '<td></td>';
				echo '<td></td>';

            }
                echo '<td style="border-top: 2px solid #000000;"><strong>'.$this->sma->formatMoney(($amountTotal - $amountTotalsp) + ($pamountTotal - $pamountTotalsp)).'</strong></td>
							</tr>';





				//<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($dueTotal).'</strong></td>







?>
		</tfoot>
	</table>
</div>
</div>
</div>
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
        PrPDF('pr_pdf','<?= lang('Debt_and_Liability'); ?>','L');
		    return false;
        });
		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/debtliability/0/xls/?v=' . $v)?>";
            return false;
        });


    });
</script>



