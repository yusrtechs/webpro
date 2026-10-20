<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>

.invoice_link4 {
    cursor: pointer;
}
.tableData table    { page-break-inside:avoid; page-break-after:auto }
	 tr    { page-break-inside:avoid; page-break-after:auto }

</style>

<?php
if (empty($_POST)){
	$_POST['brId']=$this->session->userdata('brId');
}

$v = "";
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
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
}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= "&serial=" . $this->input->post('serial');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
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
  PrPDF('pr_pdf','<?= lang('Credit_Notes'); ?>','L');
  $('.dataTables_length').show();
  $('.dataTables_filter').show();
  //console.log(result);
  // expected output: "resolved"
}


		$('#pdf').click(function (event) {


asyncCall();


		    return false;

        });


		$('body').on('click', '.invoice_link4 td:not(:first-child, :last-child)', function() {
        $('#myModal').modal({
            remote:
                site.base_url +
                'sales/credit_note_view/' +
                $(this)
                    .closest('tr')
                    .attr('id'),
        });
        $('#myModal').modal('show');
    });
		function pqFormatFWS(x) {
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
        }
        return d;
    } else {
        return '';
    }
}

        oTable = $('#SlRData').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getSalesReportCreditNote/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[9];
                nRow.className = (aData[5] > 0) ? "invoice_link4" : "invoice_link4 warning";
                return nRow;
            },
            "aoColumns": [{"mRender": fld}, null, null, null, {
                "bSearchable": false,
                "mRender": pqFormatFWS
            }, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": row_status}],
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
            {column_number: 2, filter_default_label: "[<?=lang('biller');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 8, filter_default_label: "[<?=lang('payment_status');?>]", filter_type: "text", data: []},
        ], "footer");


    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
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

        $('#slcustomer').val(<?= $this->input->post('customer') ?>);

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
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('Credit_Notes'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
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
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open("sales/sales_cn_report"); ?>
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

                        <div class="col-sm-3">
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
                                <label class="control-label" for="user"><?= lang("created_by"); ?></label>
                                <?php
                                $us[""] = lang('select').' '.lang('user');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . " " . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ""), 'class="form-control" id="user" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("user") . '"');
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
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="slcustomer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
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
                      <table border="0" cellpadding="0" cellspacing="0" class="table">

					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('Credit_Notes'); ?></font></b></td>
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
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Credit_Notes') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>

                <div class="table-responsive">
                    <table id="SlRData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table tableData">
                        <thead>
                        <tr>
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("reference_no"); ?></th>
                            <th><?= lang("biller"); ?></th>
                            <th><?= lang("customer"); ?></th>
                            <th><?= lang("product").' / '.lang("quantity"); ?></th>
                            <th><?= lang("grand_total"); ?></th>
                            <th><?= lang("paid"); ?></th>
                            <th><?= lang("balance"); ?></th>
                            <th><?= lang("payment_status"); ?></th>
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
                            <th><?= lang("product_qty"); ?></th>
                            <th><?= lang("grand_total"); ?></th>
                            <th><?= lang("paid"); ?></th>
                            <th><?= lang("balance"); ?></th>
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
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('sales/getSalesReportCreditNote/0/xls/?v=1'.$v)?>";
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