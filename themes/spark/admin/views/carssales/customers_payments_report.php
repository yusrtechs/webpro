<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$v = '';
if ($this->input->post('customer')) {
    $v .= '&customer=' . $this->input->post('customer');
}
if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}
?>

<script>
    $(document).ready(function () {
		function get_lang(x) {
			var y=x;
			if(x=="paid")
				y="<?= lang('paid') ?>";
			if(x=="Partial_payment")
				y="<?= lang('Partial_payment') ?>";
			if(x=="partial")
				y="<?= lang('Partial_payment') ?>";
			if(x=="Deferred")
				y="<?= lang('Deferred') ?>";
			
			
            return y; // onclick=\"this.select();\"
        }
		
        oTable = $('#CusData').dataTable({
            "aaSorting": [[0, "asc"], [1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('carsreports/getCustomersPyments/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, null, {
                "mRender": decimalFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {
                "mRender": currencyFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {"mRender": currencyFormat, "bSearchable": false}, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var purchases = 0, total = 0, paid = 0, other = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    purchases += parseFloat(aaData[aiDisplay[i]][2]);
                    total += parseFloat(aaData[aiDisplay[i]][3]);
                    other += parseFloat(aaData[aiDisplay[i]][4]);
                    paid += parseFloat(aaData[aiDisplay[i]][5]);
                    balance += parseFloat(aaData[aiDisplay[i]][6]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[2].innerHTML = currencyFormat(parseFloat(purchases));
                nCells[3].innerHTML = currencyFormat(parseFloat(total));
                nCells[4].innerHTML = currencyFormat(parseFloat(other));
                nCells[5].innerHTML = currencyFormat(parseFloat(paid));
                nCells[6].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('company');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
        ], "footer");
    
	
	  
	});
	
	
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
      
        <?php if ($this->input->post('customer')) {
        ?>
        $('#rcustomer').val(<?= $this->input->post('customer') ?>).select2({
            minimumInputLength: 1,
            allowClear: true,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?= admin_url('customers/getCustomer') ?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
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
        
		<?php
		
    } ?>
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
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('Customers_Payments'); ?> <?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            } ?>
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
                <li class="dropdown"><a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>"><i class="icon fa fa-file-excel-o"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('carssales/customers_payments_report'); ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="rcustomer"><?= lang('customer'); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'class="form-control" id="rcustomer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '"'); ?>
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

                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table  table-condensed table-hover table-striped reports-table">
                        <thead>
                        <tr class="primary">
                            <th class="col-xs-3"><?= lang('company'); ?></th>
                            <th class="col-xs-3"><?= lang('name'); ?></th>
                            <th><?= lang('cash'); ?></th>
                            <th><?= lang('CC'); ?></th>
                            <th><?= lang('Others'); ?></th>
                            <th><?= lang('Discount_Allowance'); ?></th>
                            <th><?= lang('Net'); ?></th>
                            <th style="width:40px !important;"><?= lang('actions'); ?></th>
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
                            <th></th>
                            <th></th>
                            <th style="max-width:40px !important;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('carsreports/getCustomersPyments/pdf')?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('carsreports/getCustomersPyments/0/xls/?v=1' . $v)?>";
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