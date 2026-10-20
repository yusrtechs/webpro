<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }

</style>

<?php

$v = "";

if ($this->input->post('customer')) {
    $v .= "&customer=" . $this->input->post('customer');
}
if ($this->input->post('payment_status')) {
    $v .= "&payment_status=" . $this->input->post('payment_status');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
if ($this->input->post('show_style')) {
    $v .= "&show_style=" . $this->input->post('show_style');
}

?>

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
  PrPDF('pr_pdf','<?= lang('salesman_report'); ?>','L');
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
            'sAjaxSource': '<?= admin_url('reports/getSalesman/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, null, null,
            {
                "mRender": currencyFormat,
                "bSearchable": false
            },{"mRender": currencyFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {
                "mRender": currencyFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {"mRender": currencyFormat, "bSearchable": false}, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var purchases = 0, total = 0, paid = 0, discount_allowance = 0, balance = 0, before_tax = 0;
                for (var i = 0; i < aaData.length; i++) {
                    before_tax += parseFloat(aaData[aiDisplay[i]][3]);
                    purchases += parseFloat(aaData[aiDisplay[i]][4]);
                    total += parseFloat(aaData[aiDisplay[i]][5]);
                    paid += parseFloat(aaData[aiDisplay[i]][6]);
                    discount_allowance += parseFloat(aaData[aiDisplay[i]][7]);
                    balance += parseFloat(aaData[aiDisplay[i]][8]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[3].innerHTML = currencyFormat(parseFloat(before_tax));
                nCells[4].innerHTML = currencyFormat(parseFloat(purchases));
                nCells[5].innerHTML = currencyFormat(parseFloat(total));
                nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                nCells[7].innerHTML = currencyFormat(parseFloat(discount_allowance));
                nCells[8].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Salesman');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('Commission_calculation');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('Commission_%');?>]", filter_type: "text", data: []},
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
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('salesman'); ?></h2>

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


                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open("sales/salesman_report"); ?>
                    <div class="row">




                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang("customer"); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="slcustomer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="show_style"><?= lang('Show_style'); ?></label>
                                <?php
                                $ss['1'] = lang('Detailed');
                                $ss['0'] = lang('Aggregation');

                                echo form_dropdown('show_style', $ss, (isset($_POST['show_style']) ? $_POST['show_style'] : '0'), 'class="form-control" id="show_style" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_style') . '"');
                                ?>
                            </div>
                        </div>

                        <?php if ($Owner || $Admin || $GP['sales-payments']) {
                                    ?>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('payment_status', 'payment_status'); ?>
                                <?php $pst = ['0' => lang('all'), 'due' => lang('due'), 'paid' => lang('paid')];
                                    echo form_dropdown('payment_status', $pst, (isset($_POST['payment_status']) ? $_POST['payment_status'] : ""), 'class="form-control input-tip" required="required" id="slpayment_status"'); ?>

                            </div>
                        </div>
                        <?php
                                } else {
                                    echo form_hidden('payment_status', 'due');
                                }
                        ?>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>



                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>



                <div id="pr_pdf">
                    <table border="0" cellpadding="0" cellspacing="0" class="table tableData">



                <thead style="border: 1px solid #00ace6;">
                <tr>
                    <td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('salesman_report'); ?></font></b></td>
                </tr>
                <tr>
                    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

                </tr>
                <tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('salesman_report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>

                </thead>
               </table>

                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table  table-condensed table-hover table-striped reports-table tableData">
                        <thead>
                        <tr class="primary">
                            <th><?= lang('Salesman'); ?></th>
                            <th><?= lang('Commission_calculation'); ?></th>
                            <th><?= lang('Commission_%'); ?></th>
                            <th><?= lang('Total_before_VAT'); ?></th>
                            <th><?= lang('total_tax'); ?></th>
                            <th><?= lang('Net'); ?></th>
                            <th><?= lang('goods_cost'); ?></th>
                            <th><?= lang('Net_profit_loss'); ?></th>
                            <th><?= lang('Net_commission'); ?></th>
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
                            <th class="text-center"><?= lang('Total_before_VAT'); ?></th>
                            <th class="text-center"><?= lang('total_tax'); ?></th>
                            <th class="text-center"><?= lang('Net'); ?></th>
                            <th class="text-center"><?= lang('goods_cost'); ?></th>
                            <th class="text-center"><?= lang('Net_profit_loss'); ?></th>
                            <th class="text-center"><?= lang('Net_commission'); ?></th>
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
            window.location.href = "<?=admin_url('reports/getSalesman/0/xls/?v=1'.$v)?>";
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