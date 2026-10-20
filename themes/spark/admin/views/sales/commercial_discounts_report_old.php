<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>

.tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }
</style>

<?php
$v = '';
/* if($this->input->post('name')){
  $v .= "&name=".$this->input->post('name');
} */
if ($this->input->post('payment_ref')) {
    $v .= '&payment_ref=' . $this->input->post('payment_ref');
}
if ($this->input->post('paid_by')) {
    $v .= '&paid_by=' . $this->input->post('paid_by');
}
if ($this->input->post('sale_ref')) {
    $v .= '&sale_ref=' . $this->input->post('sale_ref');
}
if ($this->input->post('purchase_ref')) {
    $v .= '&purchase_ref=' . $this->input->post('purchase_ref');
}
if ($this->input->post('supplier')) {
    $v .= '&supplier=' . $this->input->post('supplier');
}
if ($this->input->post('biller')) {
    $v .= '&biller=' . $this->input->post('biller');
}
if ($this->input->post('customer')) {
    $v .= '&customer=' . $this->input->post('customer');
}
if ($this->input->post('user')) {
    $v .= '&user=' . $this->input->post('user');
}
if ($this->input->post('cheque')) {
    $v .= '&cheque=' . $this->input->post('cheque');
}
if ($this->input->post('tid')) {
    $v .= '&tid=' . $this->input->post('tid');
}
if ($this->input->post('card')) {
    $v .= '&card=' . $this->input->post('card');
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
  PrPDF('pr_pdf','<?= lang('Discount_Allowance'); ?>','L');
  $('.dataTables_length').show();
  $('.dataTables_filter').show();
  //console.log(result);
  // expected output: "resolved"
}


		$('#pdf').click(function (event) {


asyncCall();


		    return false;

        });


		 var pb = <?= json_encode($pb); ?>;
        function paid_by(x) {
            return (x != null) ? (pb[x] ? pb[x] : x) : x;
        }

        function ref(x) {

			var mv='<?= admin_url('sales/modal_view_ByRef?ref=') ?>'+x
            return (x != null) ? '<a href="'+mv+'" data-toggle="modal" data-target="#myModal">'+x+'</a>' : ' ';
        }
		function clear_tag(x) {

			try{
				x= x.replace("&lt;p&gt;", '');
				x= x.replace("&lt;&sol;p&gt;", '');
			}catch(err){}
			return x;
        }



        oTable = $('#PayRData').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getCommercialDiscountsReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"mRender": fld}, null, {"mRender": ref},   {"mRender": currencyFormat}, {"mRender": clear_tag}, {"mRender": row_status}, {"bVisible": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[6];
				nRow.className = "payment_link";
                if (aData[5] == 'returned') {
                    nRow.className = "payment_link danger";
                }

                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0;
                for (var i = 0; i < aaData.length; i++) {
                    // if (aaData[aiDisplay[i]][6] == 'sent' || aaData[aiDisplay[i]][6] == 'returned')
                    //if (aaData[aiDisplay[i]][6] == 'sent')
                    //    total -= parseFloat(aaData[aiDisplay[i]][5]);
                    //else
                        total += parseFloat(aaData[aiDisplay[i]][3]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[3].innerHTML = currencyFormat(parseFloat(total));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('payment_ref');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('sale_ref');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('note');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('type');?>]", filter_type: "text", data: []},
        ], "footer");

    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        //$('#form').hide();
        <?php if ($this->input->post('biller')) {
    ?>
        $('#rbiller').select2({ allowClear: true });
        <?php
} ?>
        <?php if ($this->input->post('supplier')) {
        ?>
        $('#rsupplier').val(<?= $this->input->post('supplier') ?>).select2({
            minimumInputLength: 1,
            allowClear: true,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?= admin_url('suppliers/getSupplier') ?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "suppliers/suggestions",
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
        $('#rsupplier').val(<?= $this->input->post('supplier') ?>);
        <?php
    } ?>
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
        <h2 class="blue"><i class="fa-fw fa fa-money"></i><?= lang('Discount_Allowance'); ?> <?php
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
                <li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
                <li class="dropdown">
						<a href="#" id="pdf" class="tip"  title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>
             <!--   -->

            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row" >
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('sales/commercial_discounts_report'); ?>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('payment_ref', 'payment_ref'); ?>
                                <?php echo form_input('payment_ref', (isset($_POST['payment_ref']) ? $_POST['payment_ref'] : ''), 'class="form-control tip" id="payment_ref"'); ?>

                            </div>
                        </div>

                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                            <?=lang('paid_by', 'paid_by');?>
                                <select name="paid_by" id="paid_by" class="form-control paid_by">
                                    <?= $this->sma->paid_opts($this->input->post('paid_by'), false, true); ?>
                                    <?=$pos_settings && $pos_settings->paypal_pro ? '<option value="ppp">' . lang('paypal_pro') . '</option>' : '';?>
                                    <?=$pos_settings && $pos_settings->stripe ? '<option value="stripe">' . lang('stripe') . '</option>' : '';?>
                                    <?=$pos_settings && $pos_settings->authorize ? '<option value="authorize">' . lang('authorize') . '</option>' : '';?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('sale_ref', 'sale_ref'); ?>
                                <?php echo form_input('sale_ref', (isset($_POST['sale_ref']) ? $_POST['sale_ref'] : ''), 'class="form-control tip" id="sale_ref"'); ?>

                            </div>
                        </div>

                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('purchase_ref', 'purchase_ref'); ?>
                                <?php echo form_input('purchase_ref', (isset($_POST['purchase_ref']) ? $_POST['purchase_ref'] : ''), 'class="form-control tip" id="purchase_ref"'); ?>

                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="rcustomer"><?= lang('customer'); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'class="form-control" id="rcustomer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="rbiller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = '';
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="rbiller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('supplier', 'rsupplier'); ?>
                                <?php echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control" id="rsupplier" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('supplier') . '"'); ?> </div>
                        </div>

                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('transaction_id', 'tid'); ?>
                                <?php echo form_input('tid', (isset($_POST['tid']) ? $_POST['tid'] : ''), 'class="form-control" id="tid"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('card_no', 'card'); ?>
                                <?php echo form_input('card', (isset($_POST['card']) ? $_POST['card'] : ''), 'class="form-control" id="card"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3" style="display:none;">
                            <div class="form-group">
                                <?= lang('cheque_no', 'cheque'); ?>
                                <?php echo form_input('cheque', (isset($_POST['cheque']) ? $_POST['cheque'] : ''), 'class="form-control" id="cheque"'); ?>
                            </div>
                        </div>

                        <div class="col-sm-3">
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
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

				<div class="tbl" id="pr_pdf">
					<table border="0" cellpadding="0" cellspacing="0" class="table">



					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('Discount_Allowance'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Discount_Allowance') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
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
                            <th><?= lang('Discount_Allowance'); ?></th>
                            <th><?= lang('note'); ?></th>
                            <th><?= lang('type'); ?></th>
                            <th><?= lang('id'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
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
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /* $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('sales/getCommercialDiscountsReport/pdf/?v=1' . $v)?>";
            return false;
        });*/
         $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('sales/getCommercialDiscountsReport/0/xls/?v=1' . $v)?>";
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
