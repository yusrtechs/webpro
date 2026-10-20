<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

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
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
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

        var isDonation='<?=$Settings->isDonation ?>';
        var isSynchronizewithZATCA='<?=$Settings->isSynchronizewithZATCA ?>';
        var idSearchFlage=true;
        var isGrouping='<?=$Settings->isGrouping ?>';
        if(isGrouping==1){
            idSearchFlage=false;
        }

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
        oTable = $('#SLData').dataTable({
            "aaSorting": [[1, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?=$Settings->rows_per_page?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getSales/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?=$this->security->get_csrf_token_name()?>",
                    "value": "<?=$this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                //$("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);
                nRow.id = aData[0];
                nRow.setAttribute('data-return-id', aData[13]);
                nRow.className = "invoice_link re"+aData[13];
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [{"bSearchable": idSearchFlage,"bSortable": false,"mRender": checkbox}, {"mRender": fld}, {
					"mRender": function(value, type, row, meta) {
                        if (isNaN(row[2])){
						    return row[2];
                        }else{
                            return row[0];
                        }

					}
				}, null, null, {"mRender": row_status}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": pay_status}, {"bSortable": false,"mRender": attachment}, {"bVisible": false}, {"mRender": synch_status}, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, discount_allowance = 0,donation=0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += parseFloat(aaData[aiDisplay[i]][6]);
                    paid += parseFloat(aaData[aiDisplay[i]][7]);
                    discount_allowance += parseFloat(aaData[aiDisplay[i]][8]);
                    donation += parseFloat(aaData[aiDisplay[i]][9]);
                    balance += parseFloat(aaData[aiDisplay[i]][10]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[6].innerHTML = decimalFormat(parseFloat(gtotal));
                nCells[7].innerHTML = decimalFormat(parseFloat(paid));
                nCells[8].innerHTML = decimalFormat(parseFloat(discount_allowance));
                nCells[9].innerHTML = decimalFormat(parseFloat(donation));
                nCells[10].innerHTML = decimalFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?= $this->Settings->invoiceNoRef==1 ? lang('Invoice_No') : lang('reference_no'); ?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('biller');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('sale_status');?>]", filter_type: "text", data: []},
            {column_number: 11, filter_default_label: "[<?=lang('payment_status');?>]", filter_type: "text", data: []},
        ], "footer");
        if(isDonation==0){
            oTable.fnSetColumnVis( 9, false );
        }
        if(isSynchronizewithZATCA==0){
            oTable.fnSetColumnVis( 14, false );
        }


        if (localStorage.getItem('remove_slls')) {
            if (localStorage.getItem('sldebit_id')) {
                localStorage.removeItem('sldebit_id');
            }
            if (localStorage.getItem('slcredit_id')) {
                localStorage.removeItem('slcredit_id');
            }
            if (localStorage.getItem('slitems')) {
                localStorage.removeItem('slitems');
            }
			if (localStorage.getItem('pcash_account_1')) {
                localStorage.removeItem('pcash_account_1');
            }
			if (localStorage.getItem('pcc_account_1')) {
                localStorage.removeItem('pcc_account_1');
            }

			if (localStorage.getItem('pcheque_account_1')) {
                localStorage.removeItem('pcheque_account_1');
            }

			if (localStorage.getItem('ptransfer_account_1')) {
                localStorage.removeItem('ptransfer_account_1');
            }
            if (localStorage.getItem('sldiscount')) {
                localStorage.removeItem('sldiscount');
            }
            if (localStorage.getItem('sltax2')) {
                localStorage.removeItem('sltax2');
            }
            if (localStorage.getItem('slref')) {
                localStorage.removeItem('slref');
            }
            if (localStorage.getItem('slshipping')) {
                localStorage.removeItem('slshipping');
            }
            if (localStorage.getItem('slwarehouse')) {
                localStorage.removeItem('slwarehouse');
            }
            if (localStorage.getItem('sgrpId')) {
                localStorage.removeItem('sgrpId');
            }
            if (localStorage.getItem('scoId')) {
                localStorage.removeItem('scoId');
            }
            if (localStorage.getItem('sbrId')) {
                localStorage.removeItem('sbrId');
            }
            if (localStorage.getItem('slnote')) {
                localStorage.removeItem('slnote');
            }
            if (localStorage.getItem('slinnote')) {
                localStorage.removeItem('slinnote');
            }
            if (localStorage.getItem('slcustomer')) {
                localStorage.removeItem('slcustomer');
            }
            if (localStorage.getItem('slbiller')) {
                localStorage.removeItem('slbiller');
            }
            if (localStorage.getItem('slcurrency')) {
                localStorage.removeItem('slcurrency');
            }
            if (localStorage.getItem('slsrc_reference_no')) {
                localStorage.removeItem('slsrc_reference_no');
            }
            if (localStorage.getItem('slinv_src')) {
                localStorage.removeItem('slinv_src');
            }
            if (localStorage.getItem('sldate')) {
                localStorage.removeItem('sldate');
            }
            if (localStorage.getItem('slsale_status')) {
                localStorage.removeItem('slsale_status');
            }
            if (localStorage.getItem('slpayment_status')) {
                localStorage.removeItem('slpayment_status');
            }
            if (localStorage.getItem('paid_by')) {
                localStorage.removeItem('paid_by');
            }
            if (localStorage.getItem('amount_1')) {
                localStorage.removeItem('amount_1');
            }
            if (localStorage.getItem('paid_by_1')) {
                localStorage.removeItem('paid_by_1');
            }
            if (localStorage.getItem('pcc_holder_1')) {
                localStorage.removeItem('pcc_holder_1');
            }
            if (localStorage.getItem('pcc_type_1')) {
                localStorage.removeItem('pcc_type_1');
            }
            if (localStorage.getItem('pcc_month_1')) {
                localStorage.removeItem('pcc_month_1');
            }
            if (localStorage.getItem('pcc_year_1')) {
                localStorage.removeItem('pcc_year_1');
            }
            if (localStorage.getItem('pcc_no_1')) {
                localStorage.removeItem('pcc_no_1');
            }
            if (localStorage.getItem('cheque_no_1')) {
                localStorage.removeItem('cheque_no_1');
            }
            if (localStorage.getItem('slpayment_term')) {
                localStorage.removeItem('slpayment_term');
            }
            localStorage.removeItem('remove_slls');
        }

        <?php if ($this->session->userdata('remove_slls')) {
    ?>
        if (localStorage.getItem('sldebit_id')) {
                localStorage.removeItem('sldebit_id');
        }
        if (localStorage.getItem('slcredit_id')) {
                localStorage.removeItem('slcredit_id');
        }
        if (localStorage.getItem('slitems')) {
            localStorage.removeItem('slitems');
        }
        if (localStorage.getItem('sldiscount')) {
            localStorage.removeItem('sldiscount');
        }
        if (localStorage.getItem('sltax2')) {
            localStorage.removeItem('sltax2');
        }
        if (localStorage.getItem('slref')) {
            localStorage.removeItem('slref');
        }
        if (localStorage.getItem('slshipping')) {
            localStorage.removeItem('slshipping');
        }
        if (localStorage.getItem('slwarehouse')) {
            localStorage.removeItem('slwarehouse');
        }
        if (localStorage.getItem('sgrpId')) {
            localStorage.removeItem('sgrpId');
        }
        if (localStorage.getItem('scoId')) {
            localStorage.removeItem('scoId');
        }
        if (localStorage.getItem('sbrId')) {
            localStorage.removeItem('sbrId');
        }
        if (localStorage.getItem('slnote')) {
            localStorage.removeItem('slnote');
        }
        if (localStorage.getItem('slinnote')) {
            localStorage.removeItem('slinnote');
        }
        if (localStorage.getItem('slcustomer')) {
            localStorage.removeItem('slcustomer');
        }
        if (localStorage.getItem('slbiller')) {
            localStorage.removeItem('slbiller');
        }
        if (localStorage.getItem('slcurrency')) {
            localStorage.removeItem('slcurrency');
        }
        if (localStorage.getItem('slsrc_reference_no')) {
            localStorage.removeItem('slsrc_reference_no');
        }
        if (localStorage.getItem('slinv_src')) {
            localStorage.removeItem('slinv_src');
        }
        if (localStorage.getItem('sldate')) {
            localStorage.removeItem('sldate');
        }
        if (localStorage.getItem('slsale_status')) {
            localStorage.removeItem('slsale_status');
        }
        if (localStorage.getItem('slpayment_status')) {
            localStorage.removeItem('slpayment_status');
        }
        if (localStorage.getItem('paid_by')) {
            localStorage.removeItem('paid_by');
        }
        if (localStorage.getItem('amount_1')) {
            localStorage.removeItem('amount_1');
        }
        if (localStorage.getItem('paid_by_1')) {
            localStorage.removeItem('paid_by_1');
        }
        if (localStorage.getItem('pcc_holder_1')) {
            localStorage.removeItem('pcc_holder_1');
        }
        if (localStorage.getItem('pcc_type_1')) {
            localStorage.removeItem('pcc_type_1');
        }
        if (localStorage.getItem('pcc_month_1')) {
            localStorage.removeItem('pcc_month_1');
        }
        if (localStorage.getItem('pcc_year_1')) {
            localStorage.removeItem('pcc_year_1');
        }
        if (localStorage.getItem('pcc_no_1')) {
            localStorage.removeItem('pcc_no_1');
        }
        if (localStorage.getItem('cheque_no_1')) {
            localStorage.removeItem('cheque_no_1');
        }
        if (localStorage.getItem('slpayment_term')) {
            localStorage.removeItem('slpayment_term');
        }
        <?php $this->sma->unset_data('remove_slls');
}
        ?>

        $(document).on('click', '.sledit', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?=lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });
        $(document).on('click', '.slduplicate', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?=lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });

    });

</script>
<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('start_date') && !$this->input->post('end_date') && !$this->input->post('reference_no') && !$this->input->post('customer') && !$this->input->post('biller') && !$this->input->post('warehouse') && !$this->input->post('product') && !$this->input->post('user') && !$this->input->post('serial') && !$this->input->post('sale_status') && !$this->input->post('payment_status') && !$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
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
<?php

if ($Owner || $GP['bulk_actions']) {
            echo admin_form_open('sales/sale_actions', 'id="action-form"');
        }
?>
-->
<?php echo admin_form_open("sales/index", 'id="action-form"'); ?>

<div class="spark-sales-page">
    <div class="spark-sales-header">
        <div class="spark-sales-header-start">
            <div class="spark-sales-header-icon">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="spark-sales-header-info">
                <h1 class="spark-sales-title">
                    <?= lang('sales'); ?><?= $warehouse_id ? ' (' . $warehouse->name . ')' : ''; ?>
                </h1>
                <p class="spark-sales-subtitle">
                    <?= lang('manage_sales_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-sales-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <a href="<?= admin_url('sales/add'); ?>" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('add_sale'); ?></span>
            </a>

            <div class="dropdown">
                <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                    <span><?= lang('actions'); ?></span>
                    <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
                    <li>
                        <a href="<?= admin_url('sales/sale_by_csv'); ?>">
                            <i class="fa fa-file-text-o"></i> <?= lang('add_sale_by_csv'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="combine" data-action="combine">
                            <i class="fa fa-file-pdf-o"></i> <?= lang('combine_to_pdf'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="excel" data-action="export_excel">
                            <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>

				 <div id="form">

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

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
                                                if ($Owner || $Admin){
                                                    $br['']=lang('all');
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }else{
                                            $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                        }
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ''), 'class="form-control select" id="brId"   style="width:100%"')
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
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="slcustomer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
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
                                <?= lang('payment_status', 'payment_status'); ?>
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
                    <table id="SLData" class="table table-striped jambo_table bulk_action " cellpadding="0" cellspacing="0" border="0">
                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('date'); ?></th>

                            <th><?= $this->Settings->invoiceNoRef==1 ? lang('Invoice_No') : lang('reference_no'); ?></th>
                            <th><?= lang('biller'); ?></th>
                            <th><?= lang('customer'); ?></th>
                            <th><?= lang('sale_status'); ?></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Allowance'); ?></th>
                            <th><?= lang('Donations'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th><?= lang('payment_status'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th></th>
                            <th style="min-width:80px; text-align:center;"><?= lang('Synchronize_with_ZATCA'); ?></th>
                            <th style="width:80px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="1" class="dataTables_empty"><?= lang('loading_data'); ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Allowance'); ?></th>
                            <th><?= lang('Donations'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th></th>
                            <th></th>
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
<?php
 if ($Owner || $Admin || $GP['bulk_actions']) {
                    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?=form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
    </div>
    <?=form_close()?>
<?php
                }
?>

<script>
$(document).ready(function () {

$('#submit_report').click(function (e) {
            e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('sales/index') ?>');
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
