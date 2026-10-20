<?php defined('BASEPATH') or exit('No direct script access allowed');
$allow_discount = ($Owner || $Admin || $this->session->userdata('allow_discount') || $inv->order_discount_id);
?>
<script type="text/javascript">
    var count = 1, an = 1, DT = <?= $Settings->default_tax_rate ?>, allow_discount = <?= $allow_discount ? 1 : 0; ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, shipping = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>,base_units = <?php echo json_encode($base_units); ?>;
    var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
    var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if ($inv) {
    ?>
        localStorage.setItem('dodate', '<?= date($dateFormats['php_ldate'], strtotime($inv->date))?>');
        localStorage.setItem('docustomer', '<?=$inv->customer_id?>');
        localStorage.setItem('dobiller', '<?=$inv->biller_id?>');
        localStorage.setItem('doref', '<?=$inv->reference_no?>');
        localStorage.setItem('dowarehouse', '<?=$inv->warehouse_id?>');
        localStorage.setItem('dostatus', '<?=$inv->status?>');
        localStorage.setItem('donote', '<?= str_replace(["\r", "\n"], '', $this->sma->decode_html($inv->note)); ?>');
        localStorage.setItem('dodiscount', '<?=$inv->order_discount_id?>');
        localStorage.setItem('dotax2', '<?=$inv->order_tax_id?>');
        localStorage.setItem('doshipping', '<?=$inv->shipping?>');
        localStorage.setItem('doitems', JSON.stringify(<?=$inv_items; ?>));
        <?php
} ?>
        <?php if ($Owner || $Admin) {
        ?>
        $(document).on('change', '#dodate', function (e) {
            localStorage.setItem('dodate', $(this).val());
        });
        if (dodate = localStorage.getItem('dodate')) {
            $('#dodate').val(dodate);
        }
        <?php
    } ?>
        $(document).on('change', '#dobiller', function (e) {
            localStorage.setItem('dobiller', $(this).val());
        });
        if (dobiller = localStorage.getItem('dobiller')) {
            $('#dobiller').val(dobiller);
        }
        ItemnTotals();
        $("#add_item").autocomplete({
            source: function (request, response) {

                if (!$('#dowarehouse').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Select_warehouse');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }

                if (!$('#docustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('Select_customer');?>');
                    //response('');
                    $('#add_item').focus();
                    return false;
                }

                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('delivers/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#dowarehouse").val(),
                        customer_id: $("#docustomer").val()
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
            $.get('<?= admin_url('welcome/set_data/remove_dols/1'); ?>');
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
        $('#reset').click(function (e) {
            $(window).unbind('beforeunload');
        });
        $('#edit_deliver').click(function () {
            $(window).unbind('beforeunload');
            $('form.edit-do-form').submit();
        });
        $(document).on('click', '#addnOpenItemManually', function (e) {

try{
if (count == 1) {
doitems = {};
if ($('#dowarehouse').val() && $('#docustomer').val() && $('#tax_method_type').val()) {
    $('#docustomer').select2('readonly', true);
    $('#dowarehouse').select2('readonly', true);
$('#tax_method_type').select2('readonly', true);
} else {
    if($('#dowarehouse').val()){

bootbox.alert(lang.Select_warehouse);
}else if($('#docustomer').val()){
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
//if (typeof doitems === "undefined") {
//    var doitems = {};
//}
doitems[mid] = {
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


        localStorage.setItem('doitems', JSON.stringify(doitems));
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
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_deliver'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-do-form'];
                echo admin_form_open_multipart('sales/edit_deliver/' . $id, $attrib)
                ?>

                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'dodate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ''), 'class="form-control input-tip datetime" id="dodate" required="required"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference_no', 'doref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control input-tip" id="doref" required="required"'); ?>
                            </div>
                        </div>
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('biller', 'dobiller'); ?>
                                    <?php
                                    $bl[''] = '';
                    foreach ($billers as $biller) {
                        $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                    }
                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $inv->biller_id), 'id="dobiller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '" required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                                </div>
                            </div>
                        <?php
                } else {
                    $biller_input = [
                        'type'  => 'hidden',
                        'name'  => 'biller',
                        'id'    => 'dobiller',
                        'value' => $this->session->userdata('biller_id'),
                    ];
                    echo form_input($biller_input);
                } ?>

                        <?php if ($Settings->tax2) {
                    ?>
                            <div class="col-md-4" style="display:none">
                                <div class="form-group">
                                    <?= lang('order_tax', 'dotax2'); ?>
                                    <?php
                                    $tr[''] = '';
                    foreach ($tax_rates as $tax) {
                        $tr[$tax->id] = $tax->name;
                    }
                    echo form_dropdown('order_tax', $tr, (isset($_POST['tax2']) ? $_POST['tax2'] : $Settings->default_tax_rate2), 'id="dotax2" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('order_tax') . '" required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>

                        <?php if ($allow_discount) {
                    ?>
                        <div class="col-md-4">
                            <div class="form-group"  id="div_invoice_discount">
                                <?= lang('discount', 'dodiscount'); ?>
                                <?php echo form_input('discount', '', 'class="form-control input-tip" id="dodiscount" ' . (($Owner || $Admin || $this->session->userdata('allow_discount')) ? '' : 'readonly="true"')); ?>
                            </div>
                        </div>
                        <?php
                } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('shipping', 'doshipping'); ?>
                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="doshipping"'); ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('status', 'dostatus'); ?>
                                <?php $st = ['pending' => lang('pending'), 'sent' => lang('sent'), 'completed' => lang('completed')];
                                echo form_dropdown('status', $st, '', 'class="form-control input-tip" id="dostatus"'); ?>

                            </div>
                        </div>


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

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('document', 'document') ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div
                                    class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">

                                    <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {
                                    ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= lang('warehouse', 'dowarehouse'); ?>
                                                <?php
                                                $wh[''] = '';
                                    foreach ($warehouses as $warehouse) {
                                        $wh[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $inv->warehouse_id), 'id="dowarehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '" required="required" style="width:100%;" '); ?>
                                            </div>
                                        </div>
                                    <?php
                                } else {
                                    $warehouse_input = [
                                        'type'  => 'hidden',
                                        'name'  => 'warehouse',
                                        'id'    => 'dowarehouse',
                                        'value' => $this->session->userdata('warehouse_id'),
                                    ];
                                    echo form_input($warehouse_input);
                                } ?>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <?= lang('customer', 'docustomer'); ?>
                                            <?php
                                            echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'id="docustomer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '" required="required" class="form-control input-tip" style="width:100%;"');
                                            ?>
                                        </div>
                                    </div>
									<div class="col-md-3">
									<div class="form-group">
										<?= lang('tax_method_type', 'tax_method_type'); ?>
										<?php
										$tmt=1;
										if($inv->product_tax>0)
										$tmt=2;
										$type = ['' => '', '1' => lang('tax_invoices_type'), '2' => lang('tax_products_type')];
										echo form_dropdown('tax_method_type', $type, $tmt, 'id="tax_method_type" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('tax_method_type') . '" required="required" style="width:100%;" '); ?>
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
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line('add_product_to_order') . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) {
                                                ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;display:none;">
                                            <a href="#" id="addManually" class="tip" title="<?= lang('add_product_manually') ?>">
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
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang('order_items'); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="doTable"
                                           class="table items table-striped  table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                            <th class="col-md-1"><?= lang('net_unit_price'); ?></th>
                                            <th class="col-md-1"><?= lang('quantity'); ?></th>
                                            <?php
                                            if ($Settings->product_discount && $allow_discount) {
                                                echo '<th class="col-md-1">' . $this->lang->line('discount') . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-2">' . $this->lang->line('product_tax') . '</th>';
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

                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="row" id="bt">
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?= lang('note', 'donote'); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="donote" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_deliver', $this->lang->line('submit'), 'id="edit_deliver" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table  table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
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
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
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
                        <div class="form-group" style="display:none">
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
                            <label for="pdiscount" class="col-sm-4 control-label">
                                <?= lang('product_discount') ?>
                            </label>
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
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
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
