<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
	<style>
		@media print {
                .no-print { display: none !important; }
				.tbl table  {
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        padding: 0.1in !important;


    }
	.tbl table td{
		-webkit-print-color-adjust:exact !important;
	}


    }
	</style>
<?php

$v = '';
if ($this->input->post('biller')) {
    $v .= '&biller=' . $this->input->post('biller');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}

?>
<ul id="myTab" class="nav nav-tabs no-print">
    <li class=""><a href="#sales-con" class="tab-grey"><?= lang('sales') ?></a></li>
    <li class=""><a href="#purchases-con" class="tab-grey"><?= lang('purchases') ?></a></li>
    <li class=""><a href="#tax-con" class="tab-grey"><?= lang('Tax_Declaration') ?></a></li>
</ul>

<div class="tab-content">
    <div id="sales-con" class="tab-pane fade in">
<script>
    $(document).ready(function () {
        oTable = $('#TAXData').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/get_sale_taxes/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {

			   nRow.id = aData[0];
                nRow.className = (aData[5] == 'completed') ? "invoice_link2" : "invoice_link2 warning";
                return nRow;
            },
            //"aoColumns": [{"mRender": fld}, null, {"mRender": row_status}, null, null,{"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
			"aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
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


<div class="box box1">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= lang('VAT_on_sales'); ?>
        </h2>

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
				<li class="dropdown">
						<a href="#" id="pdf1" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
				</li>

                <li class="dropdown" >
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="box-content">



		<div class="row">
			<div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('reports/tax'); ?>
                    <div class="row">

                        <!--<div class="col-sm-6">
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
                        </div>-->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
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
                <div class="clearfix"></div>






		<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl" id="pr_pdf1">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('VAT_on_sales'); ?></font></b></td>
					</tr>
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"   class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="10"  class="pltable" ><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('VAT_on_sales') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></td>

					</tr>
					</thead>
					<tbody width="100%" class="salesdiv" id="salesdiv">

		<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="10" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('VAT_on_sales'); ?></font></b></td>

	</tr>

	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="4"  class="pltable"  ><b><font  size="3" color="#212529"><?= lang('Declaration'); ?></font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amendment_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('VAT_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>

	</tr>
	<?php
	$total_sales_amount=0;
	$total_sales_amount_returned=0;
	$total_sales_tax=0;




	foreach ($sale_tax_local_basic_details as $sale_tax_local_basic_detail) {

		if($start_date && $end_date){
		$sale_tax_local_basic_details_returned     = $this->reports_model->getSalesTaxLocalBasicDetailsReturned($start_date, $end_date ,$warehouse_id,$sale_tax_local_basic_detail->rate);
        $ret_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($sale_tax_local_basic_details_returned as $sale_tax_local_basic_details_return) {
            $sdate=$this->site->getSaleByid($sale_tax_local_basic_details_return->sale_id)->date;
			if ($sdate > $start_date && $sdate < $end_date)
				{
					$qTotal=$qTotal+$sale_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$sale_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$sale_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$sale_tax_local_basic_details_return->total_tax;
				}
            //$this->sma->print_arrays($t);
		}
					$ret_data['total']= $qTotal;
					$ret_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		//$this->data['sale_tax_local_basic_details_returned']=$ret_data_amendment;
		}
		//echo $ret_data_amendment['total']."  ".$ret_data_amendment['total_tax']."<br>";
		//echo $sale_tax_local_basic_detail->total."  ".$sale_tax_local_basic_detail->total_tax."<br>";
		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Sales_subject_to_the_standard')." ".(int)$this->sma->formatNumber($sale_tax_local_basic_detail->rate)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($sale_tax_local_basic_detail->total) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($ret_data_amendment['total'])) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(($sale_tax_local_basic_detail->total_tax +($ret_data_amendment['total_tax']))) ?></font></td>

	</tr>
	<?php
	$total_sales_amount=$total_sales_amount+($sale_tax_local_basic_detail->total);
	$total_sales_amount_returned=$total_sales_amount_returned+abs($ret_data_amendment['total']);
	$total_sales_tax=$total_sales_tax+(($sale_tax_local_basic_detail->total_tax + ($ret_data_amendment['total_tax'])));
	//$total_sales_tax_completed=$total_sales_tax_completed+$sale_tax_local_basic_details_completed[$index]->total_tax;
	//$total_sales_tax_returned=$total_sales_tax_returned+abs($sale_tax_local_basic_details_returned[$index]->total_tax);
	//$index++;
	}



	$qTotal_amendmentZero=0;
	$qTotal_amendmentinternational=0;
	$qTotal_amendmentnt=0;

	if($start_date && $end_date){
		foreach ($sale_tax_local_zero_returned as $sale_tax_local_zero_return) {

		$sdate=$this->site->getSaleByid($sale_tax_local_zero_return->sale_id)->date;
		//print_r($sale_tax_local_zero_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				//	$qTotal_Zero=$qTotal_Zero+abs($sale_tax_local_zero_return->total);
				}
			else
				{
					$qTotal_amendmentZero=$qTotal_amendmentZero+abs($sale_tax_local_zero_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

	if($start_date && $end_date){
		foreach ($sale_tax_international_returned as $sale_tax_international_return) {
        $sdate=$this->site->getSaleByid($sale_tax_international_return->sale_id)->date;
		//print_r($sale_tax_international_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				}
			else
				{
					$qTotal_amendmentinternational=$qTotal_amendmentinternational+abs($sale_tax_international_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

	if($start_date && $end_date){
		foreach ($sale_tax_local_nt_returned as $sale_tax_local_nt_return) {
       $sdate=$this->site->getSaleByid($sale_tax_local_nt_return->sale_id)->date;
		//print_r($sale_tax_local_nt_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				}
			else
				{
					$qTotal_amendmentnt=$qTotal_amendmentnt+abs($sale_tax_local_nt_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}


		?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Zero-rated_domestic_sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($sale_tax_local_zero->total) ? $this->sma->formatDecimal(($sale_tax_local_zero->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentZero) ? abs($this->sma->formatDecimal($qTotal_amendmentZero)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Exports') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($sale_tax_international->total) ? $this->sma->formatDecimal(($sale_tax_international->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentinternational) ? abs($this->sma->formatDecimal($qTotal_amendmentinternational)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Tax_free_sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($sale_tax_local_nt->total) ? $this->sma->formatDecimal(($sale_tax_local_nt->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentnt) ? abs($this->sma->formatDecimal($qTotal_amendmentnt)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<?php
	$total_sales_amount=$total_sales_amount+(($sale_tax_local_zero->total) )+(($sale_tax_international->total))+(($sale_tax_local_nt->total));
	$total_sales_amount_returned=$total_sales_amount_returned+abs($qTotal_amendmentZero)+abs($qTotal_amendmentinternational)+abs($qTotal_amendmentnt);
	?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="2"><?= lang('Total_sales') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_amount) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_amount_returned) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_sales_tax) ?></font></b></td>

	</tr>







					</tbody>
					</table>





				</div>


			</div>




		</div>


		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="TAXData"class="table table-striped jambo_table bulk_action table-condensed reports-table">
                        <thead>
                        <tr>

                            <th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('customer'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
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

<div id="purchases-con" class="tab-pane fade in">

    <script type="text/javascript">
        $(document).ready(function () {
            //$('#purchaseform').hide();
            $('.purchasetoggle_down').click(function () {
                $("#purchaseform").slideDown();
                return false;
            });
            $('.purchasetoggle_up').click(function () {
                $("#purchaseform").slideUp();
                return false;
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            oTable = $('#PTAXData').dataTable({
                "aaSorting": [[0, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= admin_url('reports/get_purchase_taxes/?v=1' . $v) ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    nRow.id = aData[0];
                    nRow.className = (aData[5] == 'received') ? "purchase_link" : "purchase_link warning";
                    return nRow;
                },
                "aoColumns": [null,null, null, null, null, {"mRender": row_status},{"mRender": fld}, {"mRender": fldo}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],

			//"aoColumns": [ null,null,null,null, {"mRender": row_status},{"mRender": fld},{"mRender": fsd},{"mRender": currencyFormat}, {"mRender": currencyFormat}],

            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

                var gtotal = paid = tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += aaData[aiDisplay[i]][8] != null ? parseFloat(aaData[aiDisplay[i]][8]) : 0;
                    tax += aaData[aiDisplay[i]][9] != null ? parseFloat(aaData[aiDisplay[i]][9]) : 0;

                    // paid += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[9].innerHTML = currencyFormat(parseFloat(tax));

                // nCells[9].innerHTML = currencyFormat(parseFloat(paid));
            }
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 0, filter_default_label: "[<?=lang('Invoice_No');?>]", filter_type: "text", data: []},
				{column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('description');?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?=lang('supplier');?>]", filter_type: "text", data: []},
                {column_number: 4, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
                {column_number: 5, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
            ], "footer");



			function attachment(x) {
            if (x != null) {
                return '<a href="' + site.url + 'assets/uploads/' + x + '" target="_blank"><i class="fa fa-chain"></i></a>';
            }
            return x;
        }

        oTable = $('#EXPData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getExpensesTax/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, {"mRender": fld}, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "expense_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0, paid = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total += parseFloat(aaData[aiDisplay[i]][4]);
					paid += parseFloat(aaData[aiDisplay[i]][5]);

                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = currencyFormat(total);
                nCells[5].innerHTML = currencyFormat(paid);
               }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Expense_No');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('category');?>]", filter_type: "text", data: []},
        ], "footer");




        });
    </script>
    <div class="box box2">
        <div class="box-header">
            <h2 class="blue"><i class="fa-fw fa fa-star nb"></i><?= lang('VAT_on_purchases'); ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="purchasetoggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="purchasetoggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
						<a href="#" id="pdf2" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
				</li>
                <li class="dropdown">
                    <a href="#" id="xls1" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="box-content">



        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="purchaseform">

                    <?php echo admin_form_open('reports/tax'); ?>
                    <div class="row">

                        <!--<div class="col-sm-6">
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
                        </div>-->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
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
                <div class="clearfix"></div>






				<div class="row">
			<div class="col-sm-12 col-md-12">

				<div  id="pr_pdf2">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('VAT_on_purchases'); ?></font></b></td>
					</tr>
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;"  colspan="10"   class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="10"  class="pltable" ><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('VAT_on_purchases') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></td>

					</tr>
					</thead>
					<tbody width="100%" class="purchasesdiv" id="purchasesdiv">

		<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="10" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('VAT_on_purchases'); ?></font></b></td>

	</tr>

	<tr>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="4"  class="pltable"  ><b><font  size="3" color="#212529"><?= lang('Declaration'); ?></font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('Amendment_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>
		<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="3" color="#212529"><?= lang('VAT_Amount'); ?> (<?= $default_currency->code; ?>) </font></b></td>

	</tr>
	<?php
	$total_purchases_amount=0;
	$total_purchases_amount_returned=0;
	$total_purchases_tax=0;
	foreach ($purchase_tax_local_basic_details as $purchase_tax_local_basic_detail) {

		if($start_date && $end_date){
		$purchase_tax_local_basic_details_returned     = $this->reports_model->getPurchasesTaxLocalBasicDetailsReturned($start_date, $end_date ,$warehouse_id,$purchase_tax_local_basic_detail->rate);
        $ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($purchase_tax_local_basic_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->site->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $start_date && $sdate < $end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
            //$this->sma->print_arrays($t);
		}
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}
		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Purchases_subject_to_the_standard')." ".(int)$this->sma->formatNumber($purchase_tax_local_basic_detail->rate)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($purchase_tax_local_basic_detail->total ) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($ret_data_amendment['total'])) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(($purchase_tax_local_basic_detail->total_tax  + ($ret_data_amendment['total_tax']))) ?></font></td>

	</tr>
	<?php
	//$total_purchases_amount=$total_purchases_amount+$purchase_tax_local_basic_detail->total;
	//$total_purchases_tax=$total_purchases_tax+$purchase_tax_local_basic_detail->total_tax;

	$total_purchases_amount=$total_purchases_amount+($purchase_tax_local_basic_detail->total );
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($ret_data_amendment['total']);
	$total_purchases_tax=$total_purchases_tax+(($purchase_tax_local_basic_detail->total_tax  + ($ret_data_amendment['total_tax'])));

	}
		?>


	<?php
	foreach ($purchase_tax_international_basic_details as $purchase_tax_international_basic_detail) {
		if($start_date && $end_date){
		$purchase_tax_international_basic_details_returned     = $this->reports_model->getPurchasesTaxInternationalBasicDetailsReturned($start_date, $end_date ,$warehouse_id,$purchase_tax_local_basic_details->rate);
        //print_r($purchase_tax_international_basic_details_returned);
		$ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

		foreach ($purchase_tax_international_basic_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->site->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $start_date && $sdate < $end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
            //$this->sma->print_arrays($t);
		}
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}
		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Imports_subject_to_value_added_tax_and_paid_to_the_customs_authority')." ".(int)$this->sma->formatNumber($purchase_tax_international_basic_detail->rate)."%" ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($purchase_tax_international_basic_detail->total+$purchase_tax_international_basic_detail->customs_fees +$purchase_tax_international_basic_detail->Other_fees) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal(abs($ret_data_amendment['total'])) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney($purchase_tax_international_basic_detail->total_tax+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100)+$ret_data_amendment['total_tax']) ?></font></td>

	</tr>
	<?php
	//$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_basic_detail->total+$purchase_tax_international_basic_detail->customs_fees +$purchase_tax_international_basic_detail->Other_fees - abs($ret_data_amendment['total']);
	//$total_purchases_tax=$total_purchases_tax+$purchase_tax_international_basic_detail->total_tax+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100);


	$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_basic_detail->total+$purchase_tax_international_basic_detail->customs_fees +$purchase_tax_international_basic_detail->Other_fees ;
	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($ret_data_amendment['total']);
	$total_purchases_tax=$total_purchases_tax+($purchase_tax_international_basic_detail->total_tax+($purchase_tax_international_basic_detail->customs_fees * $purchase_tax_international_basic_detail->rate /100)+($purchase_tax_international_basic_detail->Other_fees * $purchase_tax_international_basic_detail->rate /100)+$ret_data_amendment['total_tax']);

	}





		?>




<?php
$purchase_tax_international_zero_ret_var=0;
if($start_date && $end_date)
$purchase_tax_international_zero_ret_var=$purchase_tax_international_zero_returned->total;


$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_zero->total;
$numItems = count($purchase_tax_international_zero_details);
$i = 0;

	$ret_p_data = [];
		$qTotal=0;
		$qTotalTax=0;
		$ret_data_amendment = [];
		$qTotal_amendment=0;
		$qTotalTax_amendment=0;

	foreach ($purchase_tax_international_zero_details as $purchase_tax_international_zero_detail) {

		if($start_date && $end_date){
		$purchase_tax_international_zero_details_returned     = $this->reports_model->getPurchasesTaxInternationalZeroDetailsReturned($start_date, $end_date ,$warehouse_id,$purchase_tax_international_zero_detail->tax_active_rate);
        //print_r($purchase_tax_international_zero_details_returned);

		foreach ($purchase_tax_international_zero_details_returned as $purchase_tax_local_basic_details_return) {
            $sdate=$this->site->getPurchaseByID($purchase_tax_local_basic_details_return->purchase_id)->date;
			if ($sdate > $start_date && $sdate < $end_date)
				{
					$qTotal=$qTotal+$purchase_tax_local_basic_details_return->total;
					$qTotalTax=$qTotalTax+$purchase_tax_local_basic_details_return->total_tax;
				}
			else
				{
					$qTotal_amendment=$qTotal_amendment+$purchase_tax_local_basic_details_return->total;
					$qTotalTax_amendment=$qTotalTax_amendment+$purchase_tax_local_basic_details_return->total_tax;
				}
        }
					$ret_p_data['total']= $qTotal;
					$ret_p_data['total_tax']= $qTotalTax;
					$ret_data_amendment['total']= $qTotal_amendment;
					$ret_data_amendment['total_tax']= $qTotalTax_amendment;


		}

		//echo $ret_data_amendment['total']."p";

		?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Imports_subject_to_value_added_tax_to_which_the_reverse_charge_mechanism_is_applied')." ".(int)$this->sma->formatNumber($purchase_tax_international_zero_detail->tax_active_rate)."%" ?></font></td>
		<?php
		if($i==0){
			?>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" rowspan="<?= $numItems ?>" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatDecimal($purchase_tax_international_zero->total) ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" rowspan="<?= $numItems ?>" align="left"  class="pltable1"><font  size="2"><?= isset($purchase_tax_international_zero_ret_var) ? $this->sma->formatDecimal(abs($purchase_tax_international_zero_ret_var)) : '0.00' ?></font></td>
		<?php
		}
		?>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney((($purchase_tax_international_zero_detail->total+($purchase_tax_international_zero_ret_var)) * ($purchase_tax_international_zero_detail->tax_active_rate) / 100)) ?></font></td>

	</tr>
	<?php
	$i++;
	//$total_purchases_amount=$total_purchases_amount+$purchase_tax_international_zero_detail->total;

	$total_purchases_amount_returned=$total_purchases_amount_returned+abs($purchase_tax_international_zero_ret_var);
	$total_purchases_tax=$total_purchases_tax+(($purchase_tax_international_zero_detail->total+($purchase_tax_international_zero_ret_var)) * ($purchase_tax_international_zero_detail->tax_active_rate) / 100);

	}
		?>

		<?php
		$qTotal_amendmentZero=0;
	$qTotal_amendmentinternational=0;
	$qTotal_amendmentnt=0;

	if($start_date && $end_date){
		foreach ($purchase_tax_local_zero_returned as $sale_tax_local_zero_return) {

		$sdate=$this->site->getPurchaseByID($sale_tax_local_zero_return->sale_id)->date;
		//print_r($sale_tax_local_zero_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				//	$qTotal_Zero=$qTotal_Zero+abs($sale_tax_local_zero_return->total);
				}
			else
				{
					$qTotal_amendmentZero=$qTotal_amendmentZero+abs($sale_tax_local_zero_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}


	if($start_date && $end_date){
		foreach ($purchase_tax_local_nt_returned as $sale_tax_local_nt_return) {
       $sdate=$this->site->getPurchaseByID($sale_tax_local_nt_return->sale_id)->date;
		//print_r($sale_tax_local_nt_returned[0]->sale_id);
			if ($sdate > $start_date && $sdate < $end_date)
				{
				}
			else
				{
					$qTotal_amendmentnt=$qTotal_amendmentnt+abs($sale_tax_local_nt_return->total);
				}
            //$this->sma->print_arrays($t);
		}
	}

?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Zero-rated_domestic_purchases') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($purchase_tax_local_zero->total) ? $this->sma->formatDecimal(($purchase_tax_local_zero->total) ) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentZero) ? abs($this->sma->formatDecimal($qTotal_amendmentZero)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>



	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1"><font  size="2"><?= lang('Tax_free_purchases') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($purchase_tax_local_nt->total) ? $this->sma->formatDecimal(($purchase_tax_local_nt->total)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= isset($qTotal_amendmentnt) ? abs($this->sma->formatDecimal($qTotal_amendmentnt)) : '0.00' ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"></font></td>

	</tr>
	<?php
	$total_purchases_amount_returned=$total_purchases_amount_returned+$qTotal_amendmentZero+$qTotal_amendmentnt;
	$total_purchases_amount=$total_purchases_amount+$purchase_tax_local_zero->total+$purchase_tax_local_nt->total;
	?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="2"><?= lang('Total_purchases') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_amount) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_amount_returned) ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatDecimal($total_purchases_tax) ?></font></b></td>

	</tr>







					</tbody>
					</table>





				</div>


			</div>




		</div>




		<div class="row">
			<div class="col-sm-12 col-md-12">

		<div class="table-responsive">
                    <table id="PTAXData"
                                   class="table table-striped jambo_table bulk_action table-condensed reports-table">
                                <thead>
                                <tr>
							<th style="width:100px !important;"><?= lang('Invoice_No'); ?></th>
                            <th style="width:100px !important;"><?= lang('reference_no'); ?></th>
                            <th style="width:200px !important;"><?= lang('description'); ?></th>
                            <th style="width:150px !important;"><?= lang('supplier'); ?></th>
                            <th style="width:100px !important;"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th style="width:80px !important;"><?= lang('Status'); ?></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Total_before_VAT'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </thead>




						<tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="width:100px !important;"></th>
							<th style="width:100px !important;"></th>
                            <th style="width:200px !important;"></th>
                            <th style="width:150px !important;"></th>
                            <th style="width:100px !important;"></th>
                            <th style="width:80px !important;"></th>
                            <th style="width:150px !important;"><?= lang('Inv_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('Due_date'); ?></th>
                            <th style="width:100px !important;"><?= lang('grand_total'); ?></th>
                            <th style="width:100px !important;"><?= lang('total_tax'); ?></th>
                        </tr>
                        </tfoot>
                            </table>
                </div>

				</div>
				</div>




				 <div class="row">
            <div class="col-lg-12">


                <div class="table-responsive">
                    <table id="EXPData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-striped jambo_table bulk_action">
                        <thead>
                        <tr class="active">
                            <th style="width:100px !important;"><?= lang('Expense_No'); ?></th>
                            <th><?= lang('date'); ?></th>
                            <th class="col-xs-2"><?= lang('reference'); ?></th>
                            <th><?= lang('category'); ?></th>
                            <th><?= lang('Total_before_VAT'); ?></th>
                            <th><?= lang('total_tax'); ?></th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="10" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>

                        </tr>
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



<!-----tax-------->
<div id="tax-con" class="tab-pane fade in">
<script type="text/javascript">
	$(document).ready(function () {
		$('#purchaseform_tax').hide();
		$('.purchasetoggle_down_tax').click(function () {
			$("#purchaseform_tax").slideDown();
			return false;
		});
		$('.purchasetoggle_up_tax').click(function () {
			$("#purchaseform_tax").slideUp();
			return false;
		});
	});
</script>
	<div class="box box2">
        <div class="box-header">
            <h2 class="blue"><i class="fa-fw fa fa-star nb"></i><?= lang('Tax_Declaration'); ?>
			</h2>

			<div class="box-icon">
				<ul class="btn-tasks">
					<li class="dropdown">
						<a href="#" class="purchasetoggle_up_tax tip" title="<?= lang('hide_form') ?>">
							<i class="icon fa fa-toggle-up"></i>
						</a>
					</li>
					<li class="dropdown">
						<a href="#" class="purchasetoggle_down_tax tip" title="<?= lang('show_form') ?>">
							<i class="icon fa fa-toggle-down"></i>
						</a>
					</li>
				</ul>
			</div>
			<div class="box-icon">
				<ul class="btn-tasks">
					<li class="dropdown">
						<a href="#" id="pdf3" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
					</li>

					<li class="dropdown">
						<a href="#" id="print" class="tip" title="<?= lang('print') ?>">
							<i class="icon fa  fa-print"></i>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<div class="box-content">
			<p class="introtext"><?= lang('Value_added_tax_for_the_current_period_if_it_is_positive_on_you_and_if_it_is_negative_for_you'); ?></p>
			<div id="pr_pdf3">

			<div class="no-print" id="purchaseform_tax">


				<?php echo admin_form_open('reports/tax'); ?>
				<div class="row">

					<!--<div class="col-sm-6">
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
					</div>-->
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
							<?php
							$wh[''] = lang('select') . ' ' . lang('warehouse');
							foreach ($warehouses as $warehouse) {
								$wh[$warehouse->id] = $warehouse->name;
							}
							echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
							?>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<?= lang('start_date', 'start_date'); ?>
							<?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<?= lang('end_date', 'end_date'); ?>
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

			<div class="clearfix"></div>





			<div class="row">
			<div class="col-sm-12 col-md-12">

				<div class="tbl">
					<table border="0" cellpadding="0" cellspacing="0" class="table">


					<thead  style="width:100%;border: 1px solid #00ace6;">
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;-webkit-print-color-adjust:exact !important;"  colspan="10"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('Tax_Declaration'); ?></font></b></td>
					</tr>
					<tr>
					<td style="background-color:#dee2e6 !important;color:#ECF0F1;border:0;-webkit-print-color-adjust:exact !important;"  colspan="10"   class="pltable" ><b><font size="4" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="background-color:#dee2e6 !important;color:#ECF0F1;border-bottom: 1px solid #00ace6;-webkit-print-color-adjust:exact !important;"  colspan="10"  class="pltable" ><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Tax_Declaration') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></td>

					</tr>
					</thead>
					<tbody  width="100%" class="taxsalesdiv">



					</tbody>
					<tr>
					<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="4"  class="pltable1" ><b><font  size="3"><?= lang('Total_VAT_due_for_the_current_period') ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal($total_sales_amount-$total_purchases_amount) ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal(abs($total_sales_amount_returned)-abs($total_purchases_amount_returned)) ?></font></b></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="3"><?= $this->sma->formatDecimal($total_sales_tax-$total_purchases_tax) ?></font></b></td>

					</tr>

					</table>





				</div>


			</div>




		</div>









			<div class="clearfix"></div>
			<!-----------end --->
		</div>
	</div>
	</div>


</div>
<!---------------->




</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {


		$(function(){

			var htmlsales = document.getElementById("salesdiv").innerHTML;
            var htmlpurchases = document.getElementById("purchasesdiv").innerHTML;
            $('.taxsalesdiv').html(htmlsales+' '+htmlpurchases);
			//htmlString = document.getElementById("purchasesdiv").innerHTML;
            //$('.taxsalesdiv').appendTo(htmlString);


		});

		$('#pdf1').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf1','<?= lang('sales'); ?>');
		    return false;
        });

		$('#pdf2').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf2','<?= lang('purchases'); ?>');
		    return false;
        });

        $('#pdf3').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf3','<?= lang('Tax_Declaration'); ?>');
		    return false;
        });


        $('#print').click(function (event) {
            window.print();
			return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/get_sale_taxes/0/xls/?v=1' . $v)?>";
            return false;
        });
        $('#xls1').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/get_purchase_taxes/0/xls/?v=1' . $v)?>";
            return false;
        });

        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box1'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
        $('#image1').click(function (event) {
            event.preventDefault();
            html2canvas($('.box2'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
