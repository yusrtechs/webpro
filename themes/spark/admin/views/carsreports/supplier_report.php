<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
.tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }

      .tooltip-class {
 background-color: #c6e5f6 !important;
 font-size: 11px !important;
 text-align: center !important;

 padding: 5px !important;
 color: black !important;
 overflow: auto !important;
 min-width: 100px !important;
      }
</style>

<?php
    foreach ($total_purchases_canv as $month_sale) {
        $msal[]      = $this->sma->formatDecimal($month_sale->total);
        $mpaid[]      = $this->sma->formatDecimal($month_sale->paid);
        $mdue[]      = $this->sma->formatDecimal($month_sale->total - $month_sale->paid);
    }

	?>

<div class="spark-supplier-report">
  <div class="spark-stat-grid spark-supplier-report-stats">
    <!-- Card 1: Purchases Amount -->
    <div class="spark-stat-card spark-supplier-report-kpi-card">
      <div class="spark-stat-card-top spark-supplier-report-kpi-top">
        <div class="spark-stat-card-icon spark-supplier-report-kpi-icon icon-indigo">
          <i class="fa fa-shopping-cart"></i>
        </div>
        <span class="spark-stat-card-badge spark-supplier-report-kpi-badge badge-indigo">
          <?= lang('purchases_amount'); ?>
        </span>
      </div>
      <div class="spark-stat-card-content spark-supplier-report-kpi-content">
        <div class="spark-stat-card-val spark-supplier-report-kpi-val tabular-nums"><?= isset($purchases->total_amount) ? $this->sma->formatMoney($purchases->total_amount) : '0.00' ?></div>
        <div class="spark-stat-card-lbl spark-supplier-report-kpi-lbl"><?= lang('purchases_amount') ?></div>
      </div>
    </div>

    <!-- Card 2: Total Paid -->
    <div class="spark-stat-card spark-supplier-report-kpi-card">
      <div class="spark-stat-card-top spark-supplier-report-kpi-top">
        <div class="spark-stat-card-icon spark-supplier-report-kpi-icon icon-emerald">
          <i class="fa fa-check-circle"></i>
        </div>
        <span class="spark-stat-card-badge spark-supplier-report-kpi-badge badge-emerald">
          <?= lang('total_paid'); ?>
        </span>
      </div>
      <div class="spark-stat-card-content spark-supplier-report-kpi-content">
        <div class="spark-stat-card-val spark-supplier-report-kpi-val tabular-nums"><?= isset($purchases->paid) ? $this->sma->formatMoney($purchases->paid) : '0.00' ?></div>
        <div class="spark-stat-card-lbl spark-supplier-report-kpi-lbl"><?= lang('total_paid') ?></div>
      </div>
    </div>

    <!-- Card 3: Due Amount -->
    <div class="spark-stat-card spark-supplier-report-kpi-card">
      <div class="spark-stat-card-top spark-supplier-report-kpi-top">
        <div class="spark-stat-card-icon spark-supplier-report-kpi-icon icon-rose">
          <i class="fa fa-exclamation-circle"></i>
        </div>
        <span class="spark-stat-card-badge spark-supplier-report-kpi-badge badge-rose">
          <?= lang('due_amount'); ?>
        </span>
      </div>
      <div class="spark-stat-card-content spark-supplier-report-kpi-content">
        <div class="spark-stat-card-val spark-supplier-report-kpi-val tabular-nums"><?= (isset($purchases->total_amount) || isset($purchases->paid)) ? $this->sma->formatMoney($purchases->total_amount - $purchases->paid) : '0.00' ?></div>
        <div class="spark-stat-card-lbl spark-supplier-report-kpi-lbl"><?= lang('due_amount') ?></div>
      </div>
    </div>

    <!-- Card 4: Total Purchases -->
    <div class="spark-stat-card spark-supplier-report-kpi-card">
      <div class="spark-stat-card-top spark-supplier-report-kpi-top">
        <div class="spark-stat-card-icon spark-supplier-report-kpi-icon icon-blue">
          <i class="fa fa-file-text-o"></i>
        </div>
        <span class="spark-stat-card-badge spark-supplier-report-kpi-badge badge-blue">
          <?= lang('total_purchases'); ?>
        </span>
      </div>
      <div class="spark-stat-card-content spark-supplier-report-kpi-content">
        <div class="spark-stat-card-val spark-supplier-report-kpi-val tabular-nums"><?= $total_purchases ?></div>
        <div class="spark-stat-card-lbl spark-supplier-report-kpi-lbl"><?= lang('total_purchases') ?></div>
      </div>
    </div>
  </div>
</div>




<ul id="myTab" class="nav nav-tabs no-print">
    <li class=""><a href="#purcahses-con" class="tab-grey"><?= lang('purchases') ?></a></li>
    <li class=""><a href="#payments-con" class="tab-grey"><?= lang('payments') ?></a></li>
</ul>

<div class="tab-content">
    <div id="purcahses-con" class="tab-pane fade in">
        <?php
        $v = '&supplier=' . $user_id;
        if ($this->input->post('submit_purchase_report')) {
            if ($this->input->post('biller')) {
                $v .= '&biller=' . $this->input->post('biller');
            }
            if ($this->input->post('warehouse')) {
                $v .= '&warehouse=' . $this->input->post('warehouse');
            }
            if ($this->input->post('user')) {
                $v .= '&user=' . $this->input->post('user');
            }
            if ($this->input->post('start_date')) {
                $v .= '&start_date=' . $this->input->post('start_date');
            }
            if ($this->input->post('end_date')) {
                $v .= '&end_date=' . $this->input->post('end_date');
            }
        }
        ?>
        <script>
        $(document).ready(function () {

                function resolveAfter2Seconds() {
                    $('.dataTables_length').hide();
                    $('.dataTables_filter').hide();
                    $('.dataTables_paginate').hide();

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
        PrPDF('pr_pdf','<?= lang('purchases_report'); ?>','L');
        $('.dataTables_length').show();
        $('.dataTables_filter').show();
        $('.dataTables_paginate').show();

        //console.log(result);
        // expected output: "resolved"
        }


                $('#pdf').click(function (event) {


        asyncCall();


                    return false;

                });

                oTable = $('#PoRData').dataTable({

                "aaSorting": [[0, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= admin_url('carsreports/getPurchasesReportForSupplier/?v=1' . $v) ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    nRow.id = aData[10];
                    nRow.className = "purchase_link2";
                    return nRow;
                },
                "aoColumns": [{"mRender": fld}, null, null, null, {
                    "bSearchable": false,
                    "mRender": pqFormat
                }, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": row_status}],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var gtotal = 0, paid = 0, balance = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        gtotal += parseFloat(aaData[aiDisplay[i]][5]);
                        paid += parseFloat(aaData[aiDisplay[i]][6]);
                        balance += parseFloat(aaData[aiDisplay[i]][7]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[5].innerHTML = currencyFormat(parseFloat(gtotal));
                    nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                    nCells[7].innerHTML = currencyFormat(parseFloat(balance));
                }
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 0, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
                {column_number: 1, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('Showroom');?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?=lang('Car_Owner');?>]", filter_type: "text", data: []},
                {column_number: 8, filter_default_label: "[<?=lang('status');?>]", filter_type: "text", data: []},
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

        <div class="box purchases-table">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-star nb"></i> <?= lang('purchases_report'); ?> <?php
                if ($this->input->post('start_date')) {
                   //echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
                   echo lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date');
                }
                ?></h2>

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
                            <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>
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
                    <div class="col-lg-12">
                        <p class="introtext"><?= lang('customize_report'); ?></p>

                        <div id="form">

                            <?php echo admin_form_open('carsreports/supplier_report/' . $user_id); ?>
                            <div class="row">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="user"><?= lang('created_by'); ?></label>
                                        <?php
                                        $us[''] = lang('select') . ' ' . lang('user');
                                        foreach ($users as $user) {
                                            $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                        }
                                        echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                                        ?>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="warehouse"><?= lang('Showroom'); ?></label>
                                        <?php
                                        $wh[''] = lang('select') . ' ' . lang('Showroom');
                                        foreach ($warehouses as $warehouse) {
                                            $wh[$warehouse->id] = $warehouse->name;
                                        }
                                        echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Showroom') . '"');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('start_date', 'start_date'); ?>
                                        <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang('end_date', 'end_date'); ?>
                                        <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div
                                class="controls"> <?php echo form_submit('submit_purchase_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                            </div>
                            <?php echo form_close(); ?>

                        </div>
                        <div class="clearfix"></div>
                <div id="pr_pdf">
                <table border="0" cellpadding="0" cellspacing="0" class="table">

					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('purchases_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('purchases_report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>

                        <div class="table-responsive">
                            <table id="PoRData"
                            class="table table-striped jambo_table bulk_action table-condensed reports-table tableData">
                            <thead>
                                <tr>
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('Showroom'); ?></th>
                                    <th><?= lang('Car_Owner'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('Total_before_VAT'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
                                    <th><?= lang('expenses'); ?></th>
                                    <th><?= lang('grand_total'); ?></th>
                                    <th><?= lang('status'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                            </tbody>
                            <tfoot class="dtFilter">
                                <tr class="active">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('grand_total'); ?></th>
                                    <th><?= lang('paid'); ?></th>
                                    <th><?= lang('balance'); ?></th>
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

<div id="payments-con" class="tab-pane fade in">
    <?php
    $p = '&supplier=' . $user_id;
    if ($this->input->post('submit_payment_report')) {
        if ($this->input->post('pay_user')) {
            $p .= '&user=' . $this->input->post('pay_user');
        }
        if ($this->input->post('pay_start_date')) {
            $p .= '&start_date=' . $this->input->post('pay_start_date');
        }
        if ($this->input->post('pay_end_date')) {
            $p .= '&end_date=' . $this->input->post('pay_end_date');
        }
    }
    ?>
    <script>
    $(document).ready(function () {
        function resolveAfter2Seconds() {
                    $('.dataTables_length').hide();
                    $('.dataTables_filter').hide();
                    $('.dataTables_paginate').hide();

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
        PrPDF('pr_pdf1','<?= lang('payments_report'); ?>','L');
        $('.dataTables_length').show();
        $('.dataTables_filter').show();
        $('.dataTables_paginate').show();

        //console.log(result);
        // expected output: "resolved"
        }


                $('#pdf1').click(function (event) {


        asyncCall();


                    return false;

       });

        var pb = <?= json_encode($pb); ?>;
        function paid_by(x) {
            return (x != null) ? (pb[x] ? pb[x] : x) : x;
        }

        oTable = $('#PayRData').dataTable({

            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('carsreports/getPaymentsReport/?v=1' . $p) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"mRender": fld}, null, {"bVisible": false}, null, {"mRender": paid_by}, {"mRender": currencyFormat}, {"mRender": row_status}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[7];
                nRow.className = "payment_link2 warning";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total += parseFloat(aaData[aiDisplay[i]][5]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = currencyFormat(parseFloat(total));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('payment_ref');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('purchase_ref');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('paid_by');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('type');?>]", filter_type: "text", data: []},
        ], "footer");
    });
    </script>
    <script type="text/javascript">
    $(document).ready(function () {
        $('#payform').hide();
        $('.paytoggle_down').click(function () {
            $("#payform").slideDown();
            return false;
        });
        $('.paytoggle_up').click(function () {
            $("#payform").slideUp();
            return false;
        });
    });
    </script>

    <div class="box payments-table">
        <div class="box-header">
            <h2 class="blue"><i class="fa-fw fa fa-money nb"></i><?= lang('payments_report'); ?> <?php
            if ($this->input->post('start_date')) {
                   //echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
                   echo lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date');
                }
            ?></h2>

            <div class="box-icon">
                <ul class="btn-tasks">
                    <li class="dropdown">
                        <a href="#" class="paytoggle_up tip" title="<?= lang('hide_form') ?>">
                            <i class="icon fa fa-toggle-up"></i>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="paytoggle_down tip" title="<?= lang('show_form') ?>">
                            <i class="icon fa fa-toggle-down"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-icon">
                <ul class="btn-tasks">
                    <li class="dropdown">
                        <a href="#" id="xls1" class="tip" title="<?= lang('download_xls') ?>">
                            <i class="icon fa fa-file-excel-o"></i>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#" id="pdf1" class="tip" title="<?= lang('download_pdf') ?>">
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

                    <div id="payform">

                        <?php echo admin_form_open('carsreports/supplier_report/' . $user_id . '/#payments-con'); ?>
                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="user"><?= lang('created_by'); ?></label>
                                    <?php
                                    $us[''] = lang('select') . ' ' . lang('user');
                                    foreach ($users as $user) {
                                        $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                    }
                                    echo form_dropdown('pay_user', $us, (isset($_POST['pay_user']) ? $_POST['pay_user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <?= lang('start_date', 'start_date'); ?>
                                    <?php echo form_input('pay_start_date', (isset($_POST['pay_start_date']) ? $_POST['pay_start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <?= lang('end_date', 'end_date'); ?>
                                    <?php echo form_input('pay_end_date', (isset($_POST['pay_end_date']) ? $_POST['pay_end_date'] : ''), 'class="form-control date" id="end_date"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div
                            class="controls"> <?php echo form_submit('submit_payment_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                        </div>
                        <?php echo form_close(); ?>

                    </div>
                    <div class="clearfix"></div>
                    <div id="pr_pdf1">
                <table border="0" cellpadding="0" cellspacing="0" class="table">

					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('payments_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('payments_report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>
                    <div class="table-responsive">
                        <table id="PayRData"
                        class="table table-striped jambo_table bulk_action table-condensed reports-table tableData">

                        <thead>
                            <tr>
                                <th><?= lang('date'); ?></th>
                                <th><?= lang('payment_ref'); ?></th>
                                <th><?= lang('sale_ref'); ?></th>
                                <th><?= lang('purchase_ref'); ?></th>
                                <th><?= lang('paid_by'); ?></th>
                                <th><?= lang('amount'); ?></th>
                                <th><?= lang('type'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                            </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                            <tr class="active">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><?= lang('amount'); ?></th>
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
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>



	<!-- jQuery -->
    <script src="<?= $assets ?>template1/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
   <script src="<?= $assets ?>template1/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="<?= $assets ?>template1/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="<?= $assets ?>template1/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="<?= $assets ?>template1/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- jQuery Sparklines -->
    <script src="<?= $assets ?>template1/vendors/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- easy-pie-chart -->
    <script src="<?= $assets ?>template1/vendors/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="<?= $assets ?>template1/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

    <!-- Custom Theme Scripts
    <script src="<?= $assets ?>template1/build/js/custom.min.js"></script>
	-->
	<script>
function init_sparklines() {

    if (typeof (jQuery.fn.sparkline) === 'undefined') { return; }
    console.log('init_sparklines');


    $(".sparkline_one").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 4, 5, 6, 3, 5, 4, 5, 4, 5, 4, 3, 4, 5, 6, 7, 5, 4, 3, 5, 6], {
        type: 'bar',
        height: '125',
        barWidth: 13,
        colorMap: {
            '7': '#a1a1a1'
        },
        barSpacing: 2,
        barColor: '#26B99A'
    });


    $(".sparkline_two").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 7, 5, 4, 3, 5, 6], {
        type: 'bar',
        height: '40',
        barWidth: 9,
        colorMap: {
            '7': '#a1a1a1'
        },
        barSpacing: 2,
        barColor: '#26B99A'
    });


    $(".sparkline_sales").sparkline([<?php
                    echo implode(', ', $msal); ?>], {
        type: 'line',
        width: '100%',
        height: '60',
        lineColor: '#26B99A',
        fillColor: 'rgba(223, 223, 223, 0.57)',
        lineWidth: 2,
        spotColor: '#26B99A',
        minSpotColor: '#26B99A',
		tooltipFormat: $.spformat('<span style="color: {{color}}">&#9679;</span> {{prefix}}{{y}}{{suffix}}', 'tooltip-class')

    });



$(".sparkline_paid").sparkline([<?php
                    echo implode(', ', $mpaid); ?>], {
        type: 'line',
        width: '100%',
        height: '60',
        lineColor: '#26B99A',
        fillColor: 'rgba(223, 223, 223, 0.57)',
        lineWidth: 2,
        spotColor: '#26B99A',
        minSpotColor: '#26B99A',
		tooltipFormat: $.spformat('<span style="color: {{color}}">&#9679;</span> {{prefix}}{{y}}{{suffix}}', 'tooltip-class')

    });

$(".sparkline_due").sparkline([<?php
                    echo implode(', ', $mdue); ?>], {
        type: 'line',
        width: '100%',
        height: '60',
        lineColor: '#26B99A',
        fillColor: 'rgba(223, 223, 223, 0.57)',
        lineWidth: 2,
        spotColor: '#26B99A',
        minSpotColor: '#26B99A',
		tooltipFormat: $.spformat('<span style="color: {{color}}">&#9679;</span> {{prefix}}{{y}}{{suffix}}', 'tooltip-class')

    });


    $(".sparkline11").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 6, 2, 4, 3, 4, 5, 4, 5, 4, 3], {
        type: 'bar',
        height: '40',
        barWidth: 8,
        colorMap: {
            '7': '#a1a1a1'
        },
        barSpacing: 2,
        barColor: '#26B99A'
    });


    $(".sparkline22").sparkline([2, 4, 3, 4, 7, 5, 4, 3, 5, 6, 2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 6], {
        type: 'line',
        height: '40',
        width: '200',
        lineColor: '#26B99A',
        fillColor: '#ffffff',
        lineWidth: 3,
        spotColor: '#34495E',
        minSpotColor: '#34495E'
    });


    $(".sparkline_bar").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 4, 5, 6, 3, 5], {
        type: 'bar',
        colorMap: {
            '7': '#a1a1a1'
        },
        barColor: '#26B99A'
    });


    $(".sparkline_area").sparkline([5, 6, 7, 9, 9, 5, 3, 2, 2, 4, 6, 7], {
        type: 'line',
        lineColor: '#26B99A',
        fillColor: '#26B99A',
        spotColor: '#4578a0',
        minSpotColor: '#728fb2',
        maxSpotColor: '#6d93c4',
        highlightSpotColor: '#ef5179',
        highlightLineColor: '#8ba8bf',
        spotRadius: 2.5,
        width: 85
    });


    $(".sparkline_line").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 4, 5, 6, 3, 5], {
        type: 'line',
        lineColor: '#26B99A',
        fillColor: '#ffffff',
        width: 85,
        spotColor: '#34495E',
        minSpotColor: '#34495E'
    });


    $(".sparkline_pie").sparkline([1, 1, 2, 1], {
        type: 'pie',
        sliceColors: ['#26B99A', '#ccc', '#75BCDD', '#D66DE2']
    });


    $(".sparkline_discreet").sparkline([4, 6, 7, 7, 4, 3, 2, 1, 4, 4, 2, 4, 3, 7, 8, 9, 7, 6, 4, 3], {
        type: 'discrete',
        barWidth: 3,
        lineColor: '#26B99A',
        width: '85',
    });


};


$(document).ready(function () {

    init_sparklines();
});
</script>



<script type="text/javascript">
$(document).ready(function () {

    $('#xls').click(function (event) {
        event.preventDefault();
        window.location.href = "<?=admin_url('carsreports/getPurchasesReportForSupplier/0/xls/?v=1' . $v)?>";
        return false;
    });
    $('#image').click(function (event) {
        event.preventDefault();
        html2canvas($('.purchases-table'), {
            onrendered: function (canvas) {
                openImg(canvas.toDataURL());
            }
        });
        return false;
    });

    $('#xls1').click(function (event) {
        event.preventDefault();
        window.location.href = "<?=admin_url('carsreports/getPaymentsReport/0/xls/?v=1' . $p)?>";
        return false;
    });
    $('#image1').click(function (event) {
        event.preventDefault();
        html2canvas($('.payments-table'), {
            onrendered: function (canvas) {
                openImg(canvas.toDataURL());
            }
        });
        return false;
    });
});
</script>
