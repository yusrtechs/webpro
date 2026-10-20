<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

$v = '';

if($salesman_id)
$_POST['salesman_id']=$salesman_id;

if ($this->input->post('salesman_id')) {
    $v .= "&salesman_id=" . $this->input->post('salesman_id');
}


?>
<style>
.tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }

</style>
<script>
    $(document).ready(function () {
		function resolveAfter2Seconds() {
			$('.dataTables_length').hide();
			$('.dataTables_filter').hide();
			var oSettings = oTable.fnSettings();
        oSettings._iDisplayLength = -1;


   oTable.fnDraw();
  return new Promise(resolve => {
    setTimeout(() => {

      resolve('resolved');
    }, 2000);
  });
}

async function asyncCall() {
  //console.log('calling');
  const result = await resolveAfter2Seconds();
  PrPDF('pr_pdf','<?= lang('customers_report'); ?>','L');
  $('.dataTables_length').show();
  $('.dataTables_filter').show();
  //console.log(result);
  // expected output: "resolved"
}


		$('#pdf').click(function (event) {


asyncCall();


		    return false;

        });


        oTable = $('#CusData').dataTable({
            "aaSorting": [[0, "asc"], [1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getCustomers/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, null, null, null, {
                "mRender": decimalFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {
                "mRender": currencyFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {"mRender": currencyFormat, "bSearchable": false}, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var purchases = 0, total = 0, paid = 0, discount_allowance = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    purchases += parseFloat(aaData[aiDisplay[i]][4]);
                    total += parseFloat(aaData[aiDisplay[i]][5]);
                    paid += parseFloat(aaData[aiDisplay[i]][6]);
                    discount_allowance += parseFloat(aaData[aiDisplay[i]][7]);
                    balance += parseFloat(aaData[aiDisplay[i]][8]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = decimalFormat(parseFloat(purchases));
                nCells[5].innerHTML = currencyFormat(parseFloat(total));
                nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                nCells[7].innerHTML = currencyFormat(parseFloat(discount_allowance));
                nCells[8].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('company');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('phone');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('email_address');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
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
<?=$content_report?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('customers'); ?></h2>

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
						<a href="#" id="pdf" class="tip"  title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>

            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('view_report_customer'); ?></p>
                <div id="form">

                    <?php echo admin_form_open("sales/customers_report"); ?>
                    <div class="row">

                        <div class="col-sm-4">
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

                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div id="pr_pdf">
                  <table border="0" cellpadding="0" cellspacing="0" class="table tableData">



                <thead style="border: 1px solid #00ace6;">
                <tr>
                    <td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('customers_report'); ?></font></b></td>
                </tr>
                <tr>
                    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

                </tr>

                <?php
                    if($this->input->post('salesman_id')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Salesman').' : '.($this->site->getSalesmanByID($this->input->post('salesman_id'))->company);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                </thead>
               </table>

                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table  table-condensed table-hover table-striped reports-table tableData">
                        <thead>
                        <tr class="primary">
                            <th><?= lang('company'); ?></th>
                            <th><?= lang('name'); ?></th>
                            <th><?= lang('phone'); ?></th>
                            <th><?= lang('email_address'); ?></th>
                            <th><?= lang('total_sales'); ?></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Allowance'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th style="width:85px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-center"><?= lang('total_sales'); ?></th>
                            <th class="text-center"><?= lang('grand_total'); ?></th>
                            <th class="text-center"><?= lang('paid'); ?></th>
                            <th class="text-center"><?= lang('Discount_Allowance'); ?></th>
                            <th class="text-center"><?= lang('balance'); ?></th>
                            <th style="width:85px;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getCustomers/0/xls/?v=1' . $v)?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>