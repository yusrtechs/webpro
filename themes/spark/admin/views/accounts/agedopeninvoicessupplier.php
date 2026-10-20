<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>




    /*  table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }
*/

	 /*table    { page-break-inside:avoid; page-break-after:auto }*/
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }



</style>
<?php

if (empty($_GET)){
		$_GET['brId']=$this->session->userdata('brId');
		$_GET['start_date']=$this->sma->hrsd(date('Y-m-d'));
		$_GET['end_date']=$this->sma->hrsd(date('Y-m-d'));
}

$v = '';

if ($this->input->get('grpId')) {
    $v .= '&grpId=' . $this->input->get('grpId');
}
if ($this->input->get('coId')) {
    $v .= '&coId=' . $this->input->get('coId');
}
if ($this->input->get('brId')) {
    $v .= '&brId=' . $this->input->get('brId');
}
if ($this->input->get('compared_to')) {
    $v .= '&compared_to=' . $this->input->get('compared_to');
}
if ($this->input->get('compare_periods')) {
    $v .= '&compare_periods=' . $this->input->get('compare_periods');
}


if ($this->input->get('start_date')) {
    $v .= '&start_date=' . $this->input->get('start_date');
}
if ($this->input->get('end_date')) {
    $v .= '&end_date=' . $this->input->get('end_date');
}
if ($this->input->get('supplier')) {
    $v .= '&supplier=' . $this->input->get('supplier');
}
if ($this->input->get('due')) {
    $v .= '&due=' . $this->input->get('due');
}
if ($this->input->get('the_oldest')) {
    $v .= '&the_oldest=' . $this->input->get('the_oldest');
}

$due     = isset($_GET['due'])  ? $_GET['due'] : '1';
$the_oldest     = isset($_GET['the_oldest'])  ? $_GET['the_oldest'] : '1';

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
       // $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });

		$('#glbrId').change(function () {
			var v = $(this).val();

        var uopt1 = $("#acsupplier");


		if (v) {
            $.ajax({
                type: "get",
                async: false,
                url: site.base_url + 'branches/getSuppliersBybrId/' + v,
                dataType: "json",
                success: function (scdata) {

                    if (scdata != null) {


$('#acsupplier')
.find('option')
.remove()
.end()
.append('<option value="">'+lang.Select_supplier+'</option>')
.val('')
;


$('#acsupplier').val('').trigger('change.select2');



                        $.each(scdata, function () {
            $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

    });
    //$('#warehouse').val(scdata[0].id).trigger('change.select2');
                    } else {
                        $("#acsupplier").select2("destroy").empty().attr("placeholder", lang.Select_supplier).select2({allowClear: false,
                            placeholder: lang.Select_supplier,
                            scdata: [{id: '', text: lang.Select_supplier}]
                        });
                    }
                },
                error: function () {
                    //bootbox.alert('<?= lang('ajax_error') ?>');
                }
            });
        } else {
            $("#acsupplier").select2("destroy").empty().attr("placeholder", lang.Select_supplier).select2({allowClear: false,
                placeholder: lang.Select_supplier,
                scdata: [{id: '', text: lang.Select_supplier}]
            });
        }
		});
    });




</script>



<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-filter"></i><?= lang('Supplier_debt_aging_report'); ?> <?php
            if ($this->input->get('start_date')) {
                echo lang('From').' '. substr($this->input->get('start_date'),0,10) .' '.lang('to').' ' . substr($this->input->get('end_date'),0,10);
            }?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
						<a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>
				<!--<li class="dropdown">
						<a href="#" id="xls" class="tip" title="<?= lang('export_to_excel') ?>">
							<i class="icon fa fa-file-excel-o"></i>
						</a>
			    </li>-->
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

    </div>
    <div class="box-content">


	<div class="row">
	<div class="col-sm-12">

	<div id="form">

                    <?php echo admin_form_open('accounts/agedopeninvoicesForSupplier',' method="GET"',' autocomplete="off"'); ?>
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
                                        echo form_dropdown('grpId', $grp, (isset($_GET['grpId']) ? $_GET['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
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
                                        if(($this->Settings->enable_grp==1)&& !$this->input->get('grpId')){}else{

                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_GET['coId']) ? $_GET['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
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

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->get('coId')){}else{
												if ($Owner || $Admin){
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_GET['brId']) ? $_GET['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="glbrId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="supplier"><?= lang("supplier"); ?></label>
                                <?php
                                $cs[''] = lang('select') . ' ' . lang('supplier');
                                foreach ($suppliers as $supplier) {
                                    $cs[$supplier->id] = $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name;
                                }
                                 echo form_dropdown('supplier',$cs, (isset($_GET['supplier']) ? $_GET['supplier'] : ""), 'id="acsupplier" class="form-control"  data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("supplier") . '"'); ?>
                            </div>
                        </div>



                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_GET['start_date']) ? $_GET['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('date', 'date'); ?>
                                <?php echo form_input('end_date', (isset($_GET['end_date']) ? $_GET['end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
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

                                echo form_dropdown('compared_to', $compared_to_arr, (isset($_GET['compared_to']) ? $_GET['compared_to'] : ''), 'class="form-control" id="compared_to" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Compare_To') . '"');
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

                                echo form_dropdown('compare_periods', $compare_periods_arr, (isset($_GET['compare_periods']) ? $_GET['compare_periods'] : ''), 'class="form-control" id="compare_periods" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Compare_Periods') . '"');
                                ?>
                            </div>
                        </div>

						<div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="due"><?= lang('Not_due'); ?></label>
                                <?php
                                $ss['1'] = lang('yes');
                                $ss['0'] = lang('no');

                                echo form_dropdown('due', $ss, (isset($_GET['due']) ? $_GET['due'] : ''), 'class="form-control" id="due" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Not_due') . '"');
                                ?>
                            </div>
                        </div>


						<div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="the_oldest"><?= lang('The_Oldest'); ?></label>
                                <?php
                                $ss['1'] = lang('yes');
                                $ss['0'] = lang('no');

                                echo form_dropdown('the_oldest', $ss, (isset($_GET['the_oldest']) ? $_GET['the_oldest'] : ''), 'class="form-control" id="the_oldest" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('The_Oldest') . '"');
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="6" color="#212529"><?= lang('Supplier_debt_aging_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="5" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<?php
                    if($this->input->get('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->get('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->get('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->get('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->get('brId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="4" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->get('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->get('start_date') ?  lang('Supplier_debt_aging_report') .' '. lang('On_date') .' '. $this->input->get('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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
				<th><?= lang('reference_no') ?></th>
				<th><?= lang('supplier') ?></th>
				<th><?= lang('date') ?></th>
				<th><?= lang('Amount') ?></th>
				<th><?= lang('payment_term') ?></th>
				<th><?= lang('Due_date') ?></th>
				<th><?= lang('Outstanding_of_days') ?></th>
				<th><?= (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '') ?></th>
				<?php
				if($due==1){
					?>

				<th><?= lang('Not_due') ?></th>
				<?php
				}
				//if($_GET['compare_periods'] && $_GET['compared_to']!='0')
				if($_GET['compare_periods'] && $_GET['compared_to']!='0')
				{
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));



					for($i=1;$i<=$_GET['compare_periods'];$i++){
						if($i!=1)
						echo '<th>'.$effectiveEndDate.'</th>';

					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));

					}

				}

				?>

			</tr>
		</thead>
		<tbody>
<?php
	$amountTotal  = 0;
	$dueTotal  = 0;
	$noDueTotal  = 0;

   /*$date =date("Y-m-d");
   $day = 16;
   $newdate=date('Y-m-d', strtotime("+$day days"));
   echo "today is:".$date;
   echo "<br> and after 5 days is :".$newdate;
	echo $effectiveDate = date('Y-m-d', strtotime("-16 days", strtotime(date('Y-m-d'))));

	*/
	//echo $_GET['compare_periods'];
	//echo $effectiveDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime((isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']).' 23:59:59' : ''))));

	$accountOrder = 0;

	$moneySign = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';




// 	case $this->startsWith($accountID, '100'):
			if($_GET['compare_periods'] && $_GET['compared_to']!='0')
				{
				//$FStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$LEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$FStartDate = date('Y-m-d', strtotime('+1 days', strtotime($LEndDate)));
				$FStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($FStartDate)));
				for($i=1;$i<$_GET['compare_periods'];$i++){

						$FStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($FStartDate)));
					}
					//echo $FStartDate;

				}




			foreach ($saleList as $sale){
				if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
					echo '
					<tr>
					<td><a href='.admin_url("carspurchases/modal_view/".$sale->id).' data-toggle="modal" data-target="#myModal"> '.$sale->reference_no.'</a></td>
					';
				}else{

					echo '
						<tr>
						<td><a href='.admin_url("purchases/modal_view/".$sale->id).' data-toggle="modal" data-target="#myModal"> '.$sale->reference_no.'</a></td>

						';
				}

					echo '
							<td>'.$sale->supplier.'</td>
							<td>'.$this->sma->hrsd($sale->date).'</td>
							<td>'.$this->sma->formatMoney($sale->amount).'</td>
							<td>'.$sale->payment_term.'</td>
							<td>'.$this->sma->hrsd($sale->due_date).'</td>
							<td>'.$sale->outstanding.'</td>
							<td>'.($sale->outstanding ==0 ? $this->sma->formatMoney($sale->amount) : 0).'</td>';
							if($due==1){
							echo '<td>'.($sale->outstanding <0 ? $this->sma->formatMoney($sale->amount) : 0).'</td>
							';
							}

								$amountTotal += $sale->amount;
								$dueTotal += ($sale->outstanding ==0 ? $sale->amount : 0);
								$noDueTotal += ($sale->outstanding <0 ? $sale->amount : 0);



				if($_GET['compare_periods'] && $_GET['compared_to']!='0')
				{



					//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));










					$icount=0;
					for($i=1;$i<=$_GET['compare_periods'];$i++){

						$accountBalance[$icount] = $this->site->getAllPurchasesDue($sale->id,$effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:59',1)[0]->amount;

						if ($accountBalance[$icount] =='' || $sale->outstanding<=0){
							$accountBalance[$icount]=0;
							//continue;
						}

						$dueTotalWD[$icount] += $accountBalance[$icount];

						if($i!=$_GET['compare_periods'] )
						{
							echo '
							<td class="text-right">'.$this->sma->formatMoney($accountBalance[$icount]).'</td>
							';
						}



					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-1 years"){
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					}
					if($_GET['compared_to']=="-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if($_GET['compared_to']=="-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
					}
					echo '						</tr>
					';

				}
			}



			echo '
						<tr>
							<td colspan="3"><strong>'.lang('total').'</strong></td>
							<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($amountTotal).'</strong></td>
							<td colspan="3"><strong></td>
							<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($dueTotal).'</strong></td>';
							if($due==1){
								echo '<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($noDueTotal).'</strong></td>
							';
							}



	if($_GET['compare_periods'] && $_GET['compared_to']!='0')
				{





					//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount=0;
					for($i=1;$i<=$_GET['compare_periods'];$i++){


							if($i!=$_GET['compare_periods'] )
							{
								echo '
								<td class="text-right" style="text-decoration: underline; text-decoration-style: double; border-top: 2px solid #000000;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->sma->formatMoney($dueTotalWD[$icount]).'</strong></td>';
							}


					$icount++;
					}
					echo '						</tr>
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
</div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Supplier_debt_aging_report'); ?>','L');
		    return false;
        });
		$('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('accounts/agedopeninvoices/0/xls/?v=' . $v)?>";
            return false;
        });


    });
</script>



