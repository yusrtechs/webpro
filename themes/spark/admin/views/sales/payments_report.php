<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }

</style>
<?php
if (empty($_POST)){
	$_POST['brId']=$this->session->userdata('brId');
}

$v = '';
/* if($this->input->post('name')){
  $v .= "&name=".$this->input->post('name');
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
if ($this->input->post('salesman_id')) {
    $v .= "&salesman_id=" . $this->input->post('salesman_id');
}
if ($this->input->post('cc_type')) {
    $v .= "&cc_type=" . $this->input->post('cc_type');
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

        <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
				<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

		localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
				<?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
localStorage.setItem('sbrId', '<?=  $this->session->userdata('brId') ?>');
				<?php
						}
                    }
						?>


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
  PrPDF('pr_pdf','<?= lang('payments_report'); ?>','L');
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
            'sAjaxSource': '<?= admin_url('sales/getPaymentsReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"mRender": fld}, null, {"mRender": ref},  {"mRender": paid_by}, {"mRender": currencyFormat}, {"mRender": clear_tag}, {"mRender": row_status}, {"bVisible": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[7];
				nRow.className = "payment_link";
                if (aData[6] == 'returned') {
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
                        total += parseFloat(aaData[aiDisplay[i]][4]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = currencyFormat(parseFloat(total));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('payment_ref');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('sale_ref');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('paid_by');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('note');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('type');?>]", filter_type: "text", data: []},
        ], "footer");



    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
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

$('#slcustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + 'customers/suggestions',
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    limit: 10,
                    grpId: $('#grpId').val(),
                    coId: $('#coId').val(),
                    brId: $('#brId').val(),

                };
            },
            results: function(data, page) {

                if (data.results != null) {
                    return { results: data.results };
                } else {
                    return { results: [{ id: '', text: 'No Match Found' }] };
                }

            }
            }
        });

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
        <h2 class="blue"><i class="fa-fw fa fa-money"></i><?= lang('payments_report'); ?> <?php
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

                    <?php echo admin_form_open('sales/payments_report'); ?>
                    <div class="row">
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
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
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
                                <?= lang('payment_ref', 'payment_ref'); ?>
                                <?php echo form_input('payment_ref', (isset($_POST['payment_ref']) ? $_POST['payment_ref'] : ''), 'class="form-control tip" id="payment_ref"'); ?>

                            </div>
                        </div>

                        <div class="col-sm-3">
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
                                <label class="control-label" for="slcustomer"><?= lang('customer'); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'class="form-control" id="slcustomer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '"'); ?>
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

                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('transaction_id', 'tid'); ?>
                                <?php echo form_input('tid', (isset($_POST['tid']) ? $_POST['tid'] : ''), 'class="form-control" id="tid"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('card_no', 'card'); ?>
                                <?php echo form_input('card', (isset($_POST['card']) ? $_POST['card'] : ''), 'class="form-control" id="card"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('cheque_no', 'cheque'); ?>
                                <?php echo form_input('cheque', (isset($_POST['cheque']) ? $_POST['cheque'] : ''), 'class="form-control" id="cheque"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="cc_type"><?= lang("card_type"); ?></label>
                                <?php
                                $pts[""] = lang('select').' '.lang('card_type');
                                foreach ($paymentType as $pt) {
                                    $pts[$pt->code] = $pt->name;
                                }
                                echo form_dropdown('cc_type', $pts, (isset($_POST['cc_type']) ? $_POST['cc_type'] : ""), 'class="form-control" id="cc_type" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("card_type") . '"');
                                ?>
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
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
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
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('payments_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
                    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('warehouse')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('warehouse').' : '.($this->site->getWarehouseByID($this->input->post('warehouse'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

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
                            <th><?= lang('paid_by'); ?></th>
                            <th><?= lang('amount'); ?></th>
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
                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
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
            window.location.href = "<?=admin_url('sales/getPaymentsReport/pdf/?v=1' . $v)?>";
            return false;
        });*/
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('sales/getPaymentsReport/0/xls/?v=1' . $v)?>";
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
