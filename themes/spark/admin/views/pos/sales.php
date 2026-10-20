<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

$v = "";
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */
  if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}else{
    $_POST['start_date']=$this->sma->hrsd(date('Y-m-d'));
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}else{
    $_POST['end_date']=$this->sma->hrsd(date('Y-m-d'));
    $v .= "&end_date=" . $this->input->post('end_date');
}

if ($this->input->post('product')) {
    $v .= "&product=" . $this->input->post('product');
}
if ($this->input->post('reference_no')) {
    $v .= "&reference_no=" . $this->input->post('reference_no');
}
if ($this->input->post('customer')) {
    $v .= "&customer=" . $this->input->post('customer');
}
if ($this->input->post('biller')) {
    $v .= "&biller=" . $this->input->post('biller');
}
if ($this->input->post('salesman_id')) {
    $v .= "&salesman_id=" . $this->input->post('salesman_id');
}

if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
		$warehouse_id= $this->input->post('warehouse');

}else if($this->session->userdata('warehouse_id') &&  !$Owner && !$Admin){
$v .= "&warehouse=" . $this->session->userdata('warehouse_id');


}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}else if($this->session->userdata('user_id') &&  !$Owner && !$Admin){
    $v .= "&created_by=" . $this->session->userdata('user_id');
}
if ($this->input->post('serial')) {
    $v .= "&serial=" . $this->input->post('serial');
}
if ($this->input->post('sale_status')) {
    $v .= "&sale_status=" . $this->input->post('sale_status');
}
if ($this->input->post('payment_status')) {
    $v .= "&payment_status=" . $this->input->post('payment_status');
}
   //'sAjaxSource': '<?=admin_url('sales/getSales' . ($warehouse_id ? '/' . $warehouse_id : '') . '?v=1' . $v' . ($this->input->get('shop') ? '&shop=' . $this->input->get('shop') : '') . ($this->input->get('attachment') ? '&attachment=' . $this->input->get('attachment') : '') . ($this->input->get('delivery') ? '&delivery=' . $this->input->get('delivery') : '')); ',
    //echo    $v;
?>

<script>
    $(document).ready(function () {
        var isSynchronizewithZATCA='<?=$Settings->isSynchronizewithZATCA ?>';

        function synch_status(x) {
            if (x == null) {
                return '';
            } else if (x == '0') {
                return '<div class="text-center"><span class="payment_status label label-warning"><i class="fa fa-remove" aria-hidden="true"></i></span></div>';
            } else if (x == '1') {
                return '<div class="text-center"><span class="payment_status label label-success"><i class="fa fa-check" aria-hidden="true"></i></span></div>';
            } else {
                return '<div class="text-center"><span class="payment_status label label-default">' + x + '</span></div>';
            }
        }

        function balance(x, number) {
            if (!x) {
                return '0.00';
            }
            var b = x.split('__');
            var total = parseFloat(b[0]);
            var rounding = parseFloat(b[1]);
            var paid = parseFloat(b[2]);
            if (number == 'number') {
                return (total+rounding-paid);
            }
            return currencyFormat(total+rounding-paid);
        }
        oTable = $('#POSData').dataTable({
            "aaSorting": [[1, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('pos/getSales/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "receipt_link";
                return nRow;
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            }, {"mRender": fld}, null, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": row_status}, {"mRender": pay_status}, {"mRender": synch_status}, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, bal = 0, discount_allowance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += parseFloat(aaData[aiDisplay[i]][5]);
                    paid += parseFloat(aaData[aiDisplay[i]][6]);
                    discount_allowance += parseFloat(aaData[aiDisplay[i]][7]);
                    bal += parseFloat(aaData[aiDisplay[i]][8]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[5].innerHTML = currencyFormat(parseFloat(gtotal));
                nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                nCells[7].innerHTML = currencyFormat(parseFloat(discount_allowance));
                nCells[8].innerHTML = currencyFormat(parseFloat(bal));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('biller');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text"},
            {column_number: 9, filter_default_label: "[<?=lang('sale_status');?>]", filter_type: "text", data: []},
            {column_number: 10, filter_default_label: "[<?=lang('payment_status');?>]", filter_type: "text", data: []},
            {column_number: 11, filter_default_label: "[<?=lang('Synchronize_with_ZATCA');?>]", filter_type: "text", data: []},
        ], "footer");

        if(isSynchronizewithZATCA==0){
            oTable.fnSetColumnVis( 11, false );
        }

        $(document).on('click', '.duplicate_pos', function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            if (localStorage.getItem('positems')) {
                bootbox.confirm("<?= $this->lang->line('leave_alert') ?>", function (gotit) {
                    if (gotit == false) {
                        return true;
                    } else {
                        window.location.href = link;
                    }
                });
            } else {
                window.location.href = link;
            }
        });
        $(document).on('click', '.email_receipt', function () {
            var sid = $(this).attr('data-id');
            var ea = $(this).attr('data-email-address');
            var email = prompt("<?= lang('email_address'); ?>", ea);
            if (email != null) {
                $.ajax({
                    type: "post",
                    url: "<?= admin_url('pos/email_receipt') ?>/" + sid,
                    data: { <?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: sid },
                    dataType: "json",
                        success: function (data) {
                        bootbox.alert(data.msg);
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_request_failed'); ?>');
                        return false;
                    }
                });
            }
        });
    });

</script>
<script type="text/javascript">
    $(document).ready(function () {
        //$('#form').hide();
        <?php if ($this->input->post('customer')) { ?>
        $('#customer').val(<?= $this->input->post('customer') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "customers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });

        $('#customer').val(<?= $this->input->post('customer') ?>);
        <?php } ?>

        <?php if (!$this->input->post('product') && !$this->input->post('reference_no') && !$this->input->post('customer') && !$this->input->post('biller') && !$this->input->post('salesman_id') && !$this->input->post('warehouse') && !$this->input->post('user') && !$this->input->post('serial') && !$this->input->post('sale_status') && !$this->input->post('payment_status')) { ?>
            $('#form').hide();
        <?php } ?>

        $('.toggle_down').click(function () {
            $("#form").slideToggle();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>
<!--
<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('sales/sale_actions', 'id="action-form"');
} ?>
-->
<?php echo admin_form_open('sales/sale_actions', 'id="action-form"'); ?>

<div class="spark-pos-sales-page">
    <div class="spark-pos-sales-header">
        <div class="spark-pos-sales-header-start">
            <div class="spark-pos-sales-header-icon">
                <i class="fa fa-desktop"></i>
            </div>
            <div class="spark-pos-sales-header-info">
                <h1 class="spark-pos-sales-title">
                    <?= lang('pos_sales'); ?><?= $warehouse_id ? ' (' . $warehouse->name . ')' : ''; ?>
                </h1>
                <p class="spark-pos-sales-subtitle">
                    <?= lang('manage_pos_sales_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-pos-sales-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <?php if ($Settings->pos == 1) { ?>
                <a href="<?= admin_url('pos'); ?>" class="spark-btn-primary-action">
                    <i class="fa fa-plus"></i>
                    <span><?= lang('add_sale'); ?></span>
                </a>
            <?php } ?>

            <a href="#" id="excel" class="spark-btn-secondary-action tip" data-action="export_excel" title="<?= lang('export_to_excel'); ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('export_to_excel'); ?></span>
            </a>
        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
				<div id="form">

                    <div class="row">
                         <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("Start_date", "Start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("End_date", "End_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                    <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang("product", "suggest_product"); ?>
                                <?php echo form_input('sproduct', (isset($_POST['sproduct']) ? $_POST['sproduct'] : ""), 'class="form-control" id="suggest_product"'); ?>
                                <input type="hidden" name="product" value="<?= isset($_POST['product']) ? $_POST['product'] : "" ?>" id="report_product_id"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang("reference_no"); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ""), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>



                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("biller"); ?></label>
                                <?php
                                $bl[""] = lang('select').' '.lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ""), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("biller") . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
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
                                <label class="control-label" for="customer"><?= lang("customer"); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
                                <?php
                                $wh[""] = lang('select').' '.lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                ?>
                            </div>
                        </div>
                        <?php if($Settings->product_serial) { ?>
                            <div class="col-sm-3" style="display:none;">
                                <div class="form-group">
                                    <?= lang('serial_no', 'serial'); ?>
                                    <?= form_input('serial', '', 'class="form-control tip" id="serial"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                           <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('sale_status', 'slsale_status'); ?>
                                <?php $sst = ['0' => lang('all'),'completed' => lang('completed'), 'pending' => lang('pending'), 'returned' => lang('returned')];
                                echo form_dropdown('sale_status', $sst, (isset($_POST['sale_status']) ? $_POST['sale_status'] : ""), 'class="form-control input-tip" required="required" id="slsale_status"'); ?>

                            </div>
                            </div>
                              <?php if ($Owner || $Admin || $GP['sales-payments']) {
                                    ?>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('payment_status', 'slpayment_status'); ?>
                                <?php $pst = ['0' => lang('all'),'pending' => lang('pending'), 'due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid')];
                                    echo form_dropdown('payment_status', $pst, (isset($_POST['payment_status']) ? $_POST['payment_status'] : ""), 'class="form-control input-tip" required="required" id="slpayment_status"'); ?>

                            </div>
                        </div>
                        <?php
                                } else {
                                    echo form_hidden('payment_status', 'pending');
                                }
                        ?>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                    </div>

                </div>
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="POSData" class="table table-striped jambo_table bulk_action">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('date'); ?></th>
                            <th><?= lang('reference_no'); ?></th>
                            <th><?= lang('biller'); ?></th>
                            <th><?= lang('customer'); ?></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Allowance'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th><?= lang('sale_status'); ?></th>
                            <th><?= lang('payment_status'); ?></th>
                            <th style="min-width:80px; text-align:center;"><?= lang('Synchronize_with_ZATCA'); ?></th>
                            <th style="width:80px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="10" class="dataTables_empty"><?= lang('loading_data'); ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Allowance'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th class="defaul-color"></th>
                            <th class="defaul-color"></th>
                            <th class="defaul-color"></th>
                            <th style="width:80px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php if ($Owner || $Admin || $GP['bulk_actions']) {
                                ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php
                            } ?>

<script>
$(document).ready(function () {

$('#submit_report').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('pos/sales') ?>');
			$('#action-form-submit').trigger('click');

        });
$('#excel').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('sales/sale_actions') ?>');

        });
$('#combine').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('sales/sale_actions') ?>');

        });
	});

</script>
