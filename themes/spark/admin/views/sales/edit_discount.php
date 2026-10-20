<?php defined('BASEPATH') or exit('No direct script access allowed');
$allow_discount = ($Owner || $Admin || $this->session->userdata('allow_discount') || $inv->order_discount_id);
?>
<script type="text/javascript">
    var isTobacco =0,currRate = 1,count = 1, sl_edit = true,an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, allow_discount = <?= $allow_discount ? 1 : 0; ?>,
        tax_rates = <?php echo json_encode($tax_rates); ?>,base_units = <?php echo json_encode($base_units); ?>;
    //var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
    //var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if ($inv) {
    ?>
        <?php if($inv->order_type==1){ ?>
		localStorage.setItem('stax_method_type', '2');
		localStorage.setItem('sldiscount', '<?= $inv->discount_allowance ?>');
        <?php }else{ ?>
		localStorage.setItem('stax_method_type', '1');
	    localStorage.setItem('sldiscount', '<?= $inv->product_discount ?>');
        <?php } ?>
        //localStorage.setItem('stax_method_type', $inv->order_type);
	    //localStorage.setItem('stax_method_type', '1');

		localStorage.setItem('sldate', '<?= $this->sma->hrld($inv->date) ?>');
        localStorage.setItem('slcustomer', '<?= $inv->customer_id ?>');
        localStorage.setItem('slcurrency', '<?= $inv->currency_code ?>');
        localStorage.setItem('slbiller', '<?= $inv->biller_id ?>');
        localStorage.setItem('slref', '<?= $inv->reference_no ?>');
        localStorage.setItem('slwarehouse', '<?= $inv->warehouse_id ?>');
        localStorage.setItem('slsale_status', '<?= $inv->sale_status ?>');
        localStorage.setItem('slpayment_status', '<?= $inv->payment_status ?>');
        localStorage.setItem('slpayment_term', '<?= $inv->payment_term ?>');
        localStorage.setItem('slnote', '<?= str_replace(['\r', '\n'], '', $this->sma->decode_html($inv->note)); ?>');
        localStorage.setItem('slinnote', '<?= str_replace(["\r", "\n"], '', $this->sma->decode_html($inv->staff_note)); ?>');
        localStorage.setItem('sltax2', '<?= $inv->order_tax_id ?>');
        localStorage.setItem('slshipping', '<?= $inv->shipping ?>');
        localStorage.setItem('slitems', JSON.stringify(<?= $inv_items; ?>));
        <?php
} ?>
        <?php
		//if ($Owner || $Admin) {
        ?>
        $(document).on('change', '#sldate', function (e) {
            localStorage.setItem('sldate', $(this).val());
        });
        if (sldate = localStorage.getItem('sldate')) {
            $('#sldate').val(sldate);
        }
        <?php
    //}
	?>
        $(document).on('change', '#slbiller', function (e) {
            localStorage.setItem('slbiller', $(this).val());
        });
        if (slbiller = localStorage.getItem('slbiller')) {
            $('#slbiller').val(slbiller);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            source: function (request, response) {

                if (!$('#slwarehouse').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Select_warehouse');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }

                if (!$('#slcustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Select_customer');?>');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('sales/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#slwarehouse").val(),
                        customer_id: $("#slcustomer").val()
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });

        $(window).bind('beforeunload', function (e) {
            localStorage.setItem('remove_slls', true);
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
        $('#reset').click(function (e) {
            $(window).unbind('beforeunload');
        });
        $('#edit_sale').click(function () {
            $(window).unbind('beforeunload');
            $('form.edit-so-form').submit();
        });


        $(document).on('click', '#addnOpenItemManually', function (e) {

try{
if (count == 1) {
slitems = {};
if ($('#slwarehouse').val() && $('#slcustomer').val() && $('#tax_method_type').val()) {
    $('#slcustomer').select2('readonly', true);
    $('#slwarehouse').select2('readonly', true);
    $('#tax_method_type').select2('readonly', true);
    $('#grpId').select2('readonly', true);
    $('#coId').select2('readonly', true);
    $('#brId').select2('readonly', true);
} else {
    if($('#slwarehouse').val()){
                bootbox.alert(lang.Select_warehouse);
            }else if($('#slcustomer').val()){
                bootbox.alert(lang.Select_customer);
            }else{
                bootbox.alert(lang.Select_tax_method_type);
            }
    item = null;
    return false;
}
}

var mid = new Date().getTime(),
gccode = '',
gcname = '',
gcvalue = 0,
gccustomer = 'gccustomer',
gcexpiry = '',
gcprice = parseFloat(0);
if (gccode == '' || gcvalue == '' || gcprice == '' || gcvalue == 0 || gcprice == 0) {
//$('#gcerror').text('Please fill the required fields');
//$('.gcerror-con').show();
//return false;
}

var gc_data = new Array();
gc_data[0] = gccode;
gc_data[1] = gcvalue;
gc_data[2] = gccustomer;
gc_data[3] = gcexpiry;
//if (typeof slitems === "undefined") {
//    var slitems = {};
//}
slitems[mid] = {
            id: mid,
            item_id: mid,
            label: gcname + ' (' + gccode + ')',
            row: {
                id: mid,
                code: gccode,
                name: gcname,
                base_unit: 1,
                unit: 1,
                tax_method: 0,
                quantity: 1,
                new_entry: 1,
                base_quantity: 1,
                price: gcprice,
                real_unit_price: gcprice,
                tax_rate: 0,
                qty: 1,
                type: 'manual',
                discount: '0',
                expiry: '',
                quantity_balance: '',
                serial: '',

            },
            tax_rate: false,
            code: gccode,
            qty: 1,
            options: false,
            units: false,
        };


        localStorage.setItem('slitems', JSON.stringify(slitems));
        loadItems(1);
//add_invoice_item(data)
//$('#nmModal').appendTo("body").modal('show');
//return false;
}catch(err){alert(err)}
});

    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_sale'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-so-form'];
                echo admin_form_open_multipart('sales/edit_discount/' . $inv->id, $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">

                    <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                         <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $cat[''] = lang('select') . ' ' . lang('Main_group');
                                        if ($Owner || $Admin){
                                        foreach ($mainGroups as $mainGroup) {
                                            $cat[$mainGroup->id] = $mainGroup->name;
                                        }

                                    }else{
                                        $cat[$this->session->userdata('grpId')] = $this->site->getMainGroupsByID($this->session->userdata('grpId'))->name;
                                    }
                                        echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $inv->grpId), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                        </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if ($Owner || $Admin){

                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }

                                }else{
                                    $co[$this->session->userdata('coId')] = $this->site->getMainCompaniesByID($this->session->userdata('coId'))->name;
                                }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $inv->coId), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                        </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
                                            if ($Owner || $Admin){

                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }

                                    }else{
                                        $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                    }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $inv->brId), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>
                                        </div>
                                    </div>
                                        <?php }
                                        } ?>
                        <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'sldate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($inv->date)), 'readonly class="form-control input-tip datetime" id="sldate" required="required"'); ?>
                                </div>
                            </div>
                		<div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference_no', 'slref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control input-tip" id="slref" required="required"'); ?>
                            </div>
                        </div>
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('biller', 'slbiller'); ?>
                                    <?php
                                    $bl[''] = '';
                    foreach ($billers as $biller) {
                        $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                    }
                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $inv->biller_id), 'id="slbiller" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                                </div>
                            </div>
                        <?php
                } else {
                    $biller_input = [
                        'type'  => 'hidden',
                        'name'  => 'biller',
                        'id'    => 'slbiller',
                        'value' => $this->session->userdata('biller_id'),
                    ];
                    echo form_input($biller_input);
                } ?>


                <?php if ($Settings->IsAutomotiveServices) {
                    ?>
					    <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Exit_date', 'Exit_date'); ?>
                                <?php echo form_input('exit_date', (isset($_POST['exit_date']) ? $_POST['exit_date'] : $this->sma->hrld($inv->exit_date)), 'class="form-control input-tip datetime" id="exit_date"'); ?>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="form-group">
                            <?= lang('Plate_Number', 'Plate_Number'); ?>
                                <?php echo form_input('plate_no', (isset($_POST['plate_no']) ? $_POST['plate_no'] : $inv->plate_no), 'class="form-control input-tip" id="plate_no"'); ?>
                            </div>
                        </div>
						<?php
						} ?>

                <?php if ($Settings->IsRental) {
                    ?>
					    <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Rent_start_date', 'Rent_start_date'); ?>
                                <?php echo form_input('rent_start_date', (isset($inv->rent_start_date) ? $this->sma->hrsd($inv->rent_start_date) : ''), 'readonly class="form-control input-tip date" id="rent_start_date"'); ?>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Rent_end_date', 'Rent_end_date'); ?>
                                <?php echo form_input('rent_end_date', (isset($inv->rent_end_date) ? $this->sma->hrsd($inv->rent_end_date) : ''), 'readonly class="form-control input-tip date" id="rent_end_date"'); ?>
                            </div>
                        </div>
						<?php
						} ?>



                        	<?php if ($Settings->IsConstruction) {
                    ?>
					    <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Contract_Number', 'Contract_Number'); ?>
                                <?php echo form_input('manual_payment', (isset($_POST['manual_payment']) ? $_POST['manual_payment'] : $inv->manual_payment), 'class="form-control input-tip" id="manual_payment"'); ?>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="form-group">
                                <?= lang('PO_Number', 'PO_Number'); ?>
                                <?php echo form_input('po_no', (isset($_POST['po_no']) ? $_POST['po_no'] : $inv->po_no), 'class="form-control input-tip" id="po_no"'); ?>
                            </div>
                        </div>
						<?php
						} ?>

                <?php if ($Settings->isLogistic) {
                        ?>
					    <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Final_bayan_date', 'Final_bayan_date'); ?>
                                    <?php echo form_input('final_bayan_date', (isset($_POST['final_bayan_date']) ? $_POST['final_bayan_date'] : $this->sma->hrld($inv->final_bayan_date)), 'class="form-control input-tip datetime" id="final_bayan_date" '); ?>
                                </div>
                            </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Bill_lading_no', 'Bill_lading_no'); ?>
                                <?php echo form_input('bill_lading_no', (isset($_POST['bill_lading_no']) ? $_POST['bill_lading_no'] : $inv->bill_lading_no), 'class="form-control input-tip" id="bill_lading_no"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Bayan_no', 'Bayan_no'); ?>
                                <?php echo form_input('bayan_no', (isset($_POST['bayan_no']) ? $_POST['bayan_no'] : $inv->bayan_no), 'class="form-control input-tip" id="bayan_no"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Declaration_no', 'Declaration_no'); ?>
                                <?php echo form_input('declaration_no', (isset($_POST['declaration_no']) ? $_POST['declaration_no'] : $inv->declaration_no), 'class="form-control input-tip" id="declaration_no"'); ?>
                            </div>
                        </div>
						<?php
						} ?>

                        <?php if ($Settings->isShipping) {
                        ?>
					        <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Delivery_note_number', 'Delivery_note_number'); ?>
                                    <?php echo form_input('delivery_note_number', (isset($_POST['delivery_note_number']) ? $_POST['delivery_note_number'] : $inv->delivery_note_number), 'class="form-control input-tip" id="delivery_note_number" '); ?>
                                </div>
                            </div>

						<?php
						} ?>

                        <?php if ($Settings->invoice_view==11) {
                        ?>
					        <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('Invoice_type', 'inv_type'); ?>
                                    <?php
                                    $opt_inv = [ 1 => lang('credit'), 2 => lang('Cash')];
                                    echo form_dropdown('inv_type', $opt_inv, $inv->inv_type, 'class="form-control tip" required="required" id="inv_type" style="width:100%;"');
                                    ?>
                                </div>
                            </div>

						<?php
						} ?>


                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">

                                    <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {
                    ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= lang('warehouse', 'slwarehouse'); ?>
                                                <?php
                                                $wh[''] = '';
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $inv->warehouse_id), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;" '); ?>
                                            </div>
                                        </div>
                                    <?php
                } else {
                    $warehouse_input = [
                        'type'  => 'hidden',
                        'name'  => 'warehouse',
                        'id'    => 'slwarehouse',
                        'value' => $this->session->userdata('warehouse_id'),
                    ];
                    echo form_input($warehouse_input);
                } ?>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <?= lang('customer', 'slcustomer'); ?>
                                            <div class="input-group">
                                                <?php
                                                    echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'id="slcustomer" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                ?>
                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <a href="#" id="removeReadonly">
                                                        <i class="fa fa-unlock" id="unLock"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<div class="col-md-4">
									<div class="form-group">
										<?= lang('tax_method_type', 'tax_method_type'); ?>
										<?php
										$tmt=1;
										if($inv->product_tax>0)
										$tmt=2;




										$type = ['1' => lang('tax_invoices_type'), '2' => lang('tax_products_type')];
										echo form_dropdown('tax_method_type', $type, $tmt, 'id="tax_method_type" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;" '); ?>
									</div>
									</div>

									<div class="col-md-4">
									<div class="form-group">
										<?= lang('currency', 'currency'); ?>
										<?php
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
											echo form_dropdown('currency', $curr, ($inv->currency_code ? $inv->currency_code : $Settings->default_currency), 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>
									</div>


                                    <div class="col-md-4">
									<div class="form-group">
										<?= lang('Salesman', 'Salesman'); ?>
										<?php
                                         $salM[''] = lang('select') . ' ' . lang('Salesman');

										 foreach ($allSalesMan as $salesman) {
										$salM[$salesman->id] = $salesman->company;
										}

											echo form_dropdown('salesman', $salM, $inv->salesman_id ? $inv->salesman_id : '', 'id="slsalesman" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Salesman') . '"  style="width:100%;" '); ?>
									</div>
									</div>

                                </div>
                            </div>

                        </div>


                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang('add_product_to_order') . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) {
                                                    ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;display:none;">
                                            <a href="#" id="addManually">
                                                <i class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i>
                                            </a>
                                        </div>
                                        <?php
                                                } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang('order_items'); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="slTable"
                                           class="table items table-striped  table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                            <?php
                                            //if ($Settings->product_serial) {
                                                echo '<th class="col-md-1">' . lang('unit_price') . '</th>';
                                            //}
                                            ?>
                                            <th class="col-md-1"><?= lang('net_unit_price'); ?></th>
                                            <th class="col-md-1"><?= lang('quantity'); ?></th>
                                            <?php
                                            if ($Settings->product_discount && $allow_discount) {
                                                echo '<th class="col-md-1" id="div_th_discount">' . lang('discount') . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-1" id="div_th_tax">' . lang('product_tax') . '</th>';
                                            }
                                            ?>
                                            <th><?= lang('subtotal'); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                            <th style="width: 30px !important; text-align: center;"><i
                                                    class="fa fa-trash-o"
                                                    style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
										<?php
										if ($Settings->isOpen_invoice==1) {
											?>
										<tr id="tfoot" class="tfoot active">
										<th> <button id="addnOpenItemManually" class="tip" style="border-width:0px;"><i class="fa fa-plus"></i></button> </th>
										</tr>
										<?php
										}
										?>

										</tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php if ($Settings->tax2) {
                                                ?>
                            <div class="col-md-4"  id="div_invoice_tax">
                                <div class="form-group">
                                    <?= lang('order_tax', 'sltax2'); ?>
                                    <?php
                                    $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('order_tax', $tr, (isset($_POST['order_tax']) ? $_POST['order_tax'] : $Settings->default_tax_rate2), 'id="sltax2" data-placeholder="' . lang('select') . ' ' . lang('order_tax') . '" class="form-control input-tip select" style="width:100%;"'); ?>
                                </div>
                            </div>
                        <?php
                                            } ?>

                        <?php if ($allow_discount) {
                                                ?>
                        <div class="col-md-4" id="div_invoice_discount">
                            <div class="form-group">
                                <?= lang('order_discount', 'sldiscount'); ?>
                                <?php echo form_input('order_discount', '', 'class="form-control input-tip" id="sldiscount" ' . ($allow_discount ? '' : 'readonly="true"')); ?>
                            </div>
                        </div>
                        <?php
                                            } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('shipping', 'slshipping'); ?>
                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="slshipping"'); ?>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('document', 'document') ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('sale_status', 'slsale_status'); ?>
                                <?php $sst = ['pending' => lang('pending'), 'completed' => lang('completed')];
                                echo form_dropdown('sale_status', $sst, '', 'class="form-control input-tip" required="required" id="slsale_status"');
                                ?>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('payment_term', 'slpayment_term'); ?>
                                <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="slpayment_term"'); ?>

                            </div>
                        </div>

                        <?php if ($Settings->IsInsuranceBusiness) {
                    ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Advance_payment_discount', 'Advance_payment_discount').' %'; ?>
                                <?php echo form_input('advance_payment_discount', $inv->advance_payment_discount, 'class="form-control input-tip" id="advance_payment_discount"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Insurance_business', 'Insurance_business').' ( '.lang('An_amount_or_percentage').' ) '; ?>
                                <?php echo form_input('insurance_business',  $inv->insurance_percentage ? $inv->insurance_business.'%' : $inv->insurance_business, 'class="form-control input-tip" id="insurance_business"'); ?>
                            </div>
                        </div>

                        <?php
                        }
                    ?>
                        <?= form_hidden('payment_status', $inv->payment_status); ?>
                        <div class="clearfix"></div>

                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('sale_note', 'slnote'); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="slnote" style="margin-top: 10px; height: 100px;"'); ?>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('staff_note', 'slinnote'); ?>
                                        <?php echo form_textarea('staff_note', (isset($_POST['staff_note']) ? $_POST['staff_note'] : ''), 'class="form-control" id="slinnote" style="margin-top: 10px; height: 100px;"'); ?>

                                    </div>
                                </div>


                            </div>

                        </div>
                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_sale', lang('submit'), 'id="edit_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table  table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <?php if ($allow_discount) {
                                    ?>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
                            <?php
                                } ?>
                            <?php if ($Settings->tax2) {
                                    ?>
                                <td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                            <?php
                                } ?>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
                        </tr>
                    </table>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <?php if ($Settings->tax1) {
                                    ?>
                        <div class="form-group" id="div_product_tax">
                            <label class="col-sm-4 control-label"><?= lang('product_tax') ?></label>
                            <div class="col-sm-8">
                                <?php
                                $tr[''] = '';
                                    foreach ($tax_rates as $tax) {
                                        $tr[$tax->id] = $tax->name;
                                    }
                                    echo form_dropdown('ptax', $tr, '', 'id="ptax" class="form-control pos-input-tip" style="width:100%;"'); ?>
                            </div>
                        </div>
                    <?php
                                } ?>
                                <?php
										if ($Settings->isOpen_invoice==1) {
											?>

                        <div class="form-group">
                        <label for="ptax_method" class="col-sm-4 control-label"><?= lang('tax_method') ?></label>
                        <div class="col-sm-8">
                            <div id="ptax_method-div"></div>
                            </div>
                            </div>
                        <?php
										}
											?>
                    <?php if ($Settings->product_serial) {
                                    ?>
                        <div class="form-group">
                            <label for="pserial" class="col-sm-4 control-label"><?= lang('serial_no') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pserial">
                            </div>
                        </div>
                    <?php
                                } ?>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="punit" class="col-sm-4 control-label"><?= lang('product_unit') ?></label>
                        <div class="col-sm-8">
                            <div id="punits-div"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?= lang('product_option') ?></label>
                        <div class="col-sm-8">
                            <div id="poptions-div"></div>
                        </div>
                    </div>
                    <?php if ($Settings->product_discount) {
                                    ?>
                        <div class="form-group">
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount" <?= $allow_discount ? '' : 'readonly="true"'; ?>>
                            </div>
                        </div>
                    <?php
                                } ?>
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?= lang('unit_price') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pprice" <?= ($Owner || $Admin || $this->session->userdata('edit_price')) ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    <table class="table  table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="net_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?= lang('product_code') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?= lang('product_name') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mname">
                        </div>
                    </div>
                    <?php if ($Settings->tax1) {
                                    ?>
                        <div class="form-group">
                            <label for="mtax" class="col-sm-4 control-label"><?= lang('product_tax') ?> *</label>

                            <div class="col-sm-8">
                                <?php
                                $tr[''] = '';
                                    foreach ($tax_rates as $tax) {
                                        $tr[$tax->id] = $tax->name;
                                    }
                                    echo form_dropdown('mtax', $tr, '', 'id="mtax" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>
                        </div>
                    <?php
                                } ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?= lang('quantity') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="munit" class="col-sm-4 control-label"><?= lang('unit') ?> *</label>

                        <div class="col-sm-8">
                            <?php
                            $uts[''] = '';
                            foreach ($units as $unit) {
                                $uts[$unit->id] = $unit->name;
                            }
                            echo form_dropdown('munit', $uts, '', 'id="munit" class="form-control input-tip select" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <?php if ($Settings->product_serial) {
                                ?>
                        <div class="form-group">
                            <label for="mserial" class="col-sm-4 control-label"><?= lang('product_serial') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mserial">
                            </div>
                        </div>
                    <?php
                            } ?>
                    <?php if ($Settings->product_discount) {
                                ?>
                        <div class="form-group">
                            <label for="mdiscount" class="col-sm-4 control-label">
                                <?= lang('product_discount') ?>
                            </label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mdiscount" <?= $allow_discount ? '' : 'readonly="true"'; ?>>
                            </div>
                        </div>
                    <?php
                            } ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?= lang('unit_price') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mprice">
                        </div>
                    </div>
                    <table class="table  table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //localStorage.setItem('sldiscount', '<?= $inv->discount_allowance ?>');
        if ((sldiscount = localStorage.getItem('sldiscount'))) {
        //$('#sldiscount').val(sldiscount).trigger('change');
    }
        //$('#sldiscount').val("").trigger('change');
    });
</script>