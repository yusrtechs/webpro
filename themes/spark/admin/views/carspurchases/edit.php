<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var old_owner_id;
    var currRate = 1,count = 1, an = 1, po_edit = true, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?=$default_currency->code?>', shipping = 0,customs_fees = 0,Other_fees = 0,shipping_received = 0,shipping_amount = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, poitems = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(window).bind("load", function() {
        <?= ($inv->status == 'received' || $inv->status == 'partial') ? '$(".rec_con").show();' : '$(".rec_con").hide();'; ?>
    });
    $(document).ready(function () {
        <?= ($inv->status == 'received' || $inv->status == 'partial') ? '$(".rec_con").show();' : '$(".rec_con").hide();'; ?>
        $('#postatus').change(function(){
            var st = $(this).val();
            if (st == 'received' || st == 'received1' || st == 'partial') {
                $(".rec_con").show();
            } else {
                $(".rec_con").hide();
            }
        });
        <?php if ($inv) {
    ?>
        <?php if($inv->product_tax>0 || $inv->product_discount>0){ ?>
		localStorage.setItem('ptax_method_type', '2');
		
	    <?php }else{ ?>
		localStorage.setItem('ptax_method_type', '1');
		
	    <?php } ?>
        <?php if($inv->product_tax>0){ ?>
		localStorage.setItem('potax2', '1');
        
	    <?php }else{ ?>
		localStorage.setItem('potax2', '<?=$inv->order_tax_id?>');
        
	    <?php } ?>
        localStorage.setItem('podate', '<?= date($dateFormats['php_ldate'], strtotime($inv->date))?>');
        localStorage.setItem('posupplier', '<?=$inv->supplier_id?>');
        localStorage.setItem('poref', '<?=$inv->reference_no?>');
        localStorage.setItem('powarehouse', '<?=$inv->warehouse_id?>');
        localStorage.setItem('pocurrency', '<?=$inv->currency_code?>');
        localStorage.setItem('postatus', '<?=$inv->status?>');
        localStorage.setItem('ponote', '<?= str_replace(["\r", "\n"], '', $this->sma->decode_html($inv->note)); ?>');
        localStorage.setItem('podiscount', '<?=$inv->order_discount_id?>');
        localStorage.setItem('poshipping', '<?=$inv->shipping?>');
        localStorage.setItem('pocustoms_fees', '<?=$inv->customs_fees?>');
        localStorage.setItem('poOther_fees', '<?=$inv->other_fees?>');
        localStorage.setItem('poshipping_received', '<?=$inv->shipping_received?>');
        localStorage.setItem('popayment_term', '<?=$inv->payment_term?>');
        if (parseFloat(localStorage.getItem('potax2')) >= 1 || localStorage.getItem('podiscount').length >= 1 || parseFloat(localStorage.getItem('poshipping')) >= 1) {
            localStorage.setItem('poextras', '1');
        }
        localStorage.setItem('poitems', JSON.stringify(<?=$inv_items; ?>));
        <?php
} ?>

        <?php if ($Owner || $Admin) {
        ?>
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('podate', $(this).val());
        });
        if (podate = localStorage.getItem('podate')) {
            $('#podate').val(podate);
        }
        <?php
    } ?>
        ItemnTotals();
        $("#add_item").autocomplete({
            source: '<?= admin_url('carspurchases/suggestions'); ?>',
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
                    var row = add_purchase_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });

        $(document).on('click', '#addItemManually', function (e) {
            if (!$('#mcode').val()) {
                $('#mError').text('<?=lang('product_code_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mname').val()) {
                $('#mError').text('<?=lang('product_name_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcategory').val()) {
                $('#mError').text('<?=lang('product_category_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#munit').val()) {
                $('#mError').text('<?=lang('product_unit_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mcost').val()) {
                $('#mError').text('<?=lang('product_cost_is_required')?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#mprice').val()) {
                $('#mError').text('<?=lang('product_price_is_required')?>');
                $('#mError-con').show();
                return false;
            }

            var msg, row = null, product = {
                type: 'standard',
                code: $('#mcode').val(),
                name: $('#mname').val(),
                tax_rate: $('#mtax').val(),
                tax_method: $('#mtax_method').val(),
                category_id: $('#mcategory').val(),
                unit: $('#munit').val(),
                cost: $('#mcost').val(),
                price: $('#mprice').val()
            };

            $.ajax({
                type: "get", async: false,
                url: site.base_url + "products/addByAjax",
                data: {token: "<?= $csrf; ?>", product: product},
                dataType: "json",
                success: function (data) {
                    if (data.msg == 'success') {
                        row = add_purchase_item(data.result);
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#mModal').modal('hide');
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();
            }
            return false;

        });
        $(window).bind('beforeunload', function (e) {
            $.get('<?=admin_url('welcome/set_data/remove_pols/1');?>');
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
        $('#reset').click(function (e) {
            $(window).unbind('beforeunload');
        });
        $('#edit_pruchase').click(function () {
            $(window).unbind('beforeunload');
            $('form.edit-po-form').submit();
        });

    });
	
	
	$(document).on('click', '#edit_pruchase', function(e) {
		
		var formData_product_id = JSON.stringify($(".rid").serializeArray());
		$('#jsonArr_product_id').val(formData_product_id);
		
		var formData_product = JSON.stringify($(".rcode").serializeArray());
		$('#jsonArr_product').val(formData_product);
		
		var formData_product_name = JSON.stringify($(".rname").serializeArray());
		$('#jsonArr_product_name').val(formData_product_name);
		
		var formData_product_option = JSON.stringify($(".roption").serializeArray());
		$('#jsonArr_product_option').val(formData_product_option);
		
		var formData_part_no = JSON.stringify($(".rpart_no").serializeArray());
		$('#jsonArr_part_no').val(formData_part_no);
		
		
		var formData_expiry = JSON.stringify($(".rexpiry").serializeArray());
		$('#jsonArr_expiry').val(formData_expiry);
		
		var formData_net_cost = JSON.stringify($(".rcost").serializeArray());
		$('#jsonArr_net_cost').val(formData_net_cost);
		
		
		var formData_unit_cost = JSON.stringify($(".rucost").serializeArray());
		$('#jsonArr_unit_cost').val(formData_unit_cost);
		
		var formData_real_unit_cost = JSON.stringify($(".realucost").serializeArray());
		$('#jsonArr_real_unit_cost').val(formData_real_unit_cost);
		
		var formData_quantity_balance = JSON.stringify($(".rbqty").serializeArray());
		$('#jsonArr_quantity_balance').val(formData_quantity_balance);
		
		var formData_quantity = JSON.stringify($(".rquantity").serializeArray());
		$('#jsonArr_quantity').val(formData_quantity);
		
		var formData_product_unit = JSON.stringify($(".runit").serializeArray());
		$('#jsonArr_product_unit').val(formData_product_unit);
		
		var formData_product_base_quantity = JSON.stringify($(".rbase_quantity").serializeArray());
		$('#jsonArr_product_base_quantity').val(formData_product_base_quantity);
		
		var formData_product_discount = JSON.stringify($(".rdiscount").serializeArray());
		$('#jsonArr_product_discount').val(formData_product_discount);
		
		var formData_product_tax = JSON.stringify($(".rproduct_tax").serializeArray());
		$('#jsonArr_product_tax').val(formData_product_tax);
		
		var formData_ordered_quantity = JSON.stringify($(".oqty").serializeArray());
		$('#jsonArr_ordered_quantity').val(formData_ordered_quantity);
		
		var formData_received_base_quantity = JSON.stringify($(".rrbase_quantity").serializeArray());
		$('#jsonArr_received_base_quantity').val(formData_received_base_quantity);
		
		
		
		
		
		
		
		
		
		
        });


</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_purchase'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-po-form'];
                echo admin_form_open_multipart('carspurchases/edit/' . $inv->id, $attrib)
                ?>


                <div class="row">
				<input type="hidden" name="jsonArr_product_id" id="jsonArr_product_id"  value='1'/>
				<input type="hidden" name="jsonArr_product" id="jsonArr_product"  value='1'/>
				<input type="hidden" name="jsonArr_product_name" id="jsonArr_product_name"  value='1'/>
				<input type="hidden" name="jsonArr_product_option" id="jsonArr_product_option"  value='1'/>
				<input type="hidden" name="jsonArr_part_no" id="jsonArr_part_no"  value='1'/>
				<input type="hidden" name="jsonArr_expiry" id="jsonArr_expiry"  value='1'/>
				<input type="hidden" name="jsonArr_net_cost" id="jsonArr_net_cost"  value='1'/>
				<input type="hidden" name="jsonArr_unit_cost" id="jsonArr_unit_cost"  value='1'/>
				<input type="hidden" name="jsonArr_real_unit_cost" id="jsonArr_real_unit_cost"  value='1'/>
				<input type="hidden" name="jsonArr_quantity_balance" id="jsonArr_quantity_balance"  value='1'/>
				<input type="hidden" name="jsonArr_quantity" id="jsonArr_quantity"  value='1'/>
				<input type="hidden" name="jsonArr_product_unit" id="jsonArr_product_unit"  value='1'/>
				<input type="hidden" name="jsonArr_product_base_quantity" id="jsonArr_product_base_quantity"  value='1'/>
				<input type="hidden" name="jsonArr_product_discount" id="jsonArr_product_discount"  value='1'/>
				<input type="hidden" name="jsonArr_product_tax" id="jsonArr_product_tax"  value='1'/>
				<input type="hidden" name="jsonArr_ordered_quantity" id="jsonArr_ordered_quantity"  value='1'/>
				<input type="hidden" name="jsonArr_received_base_quantity" id="jsonArr_received_base_quantity"  value='1'/>
				
				
                    <div class="col-lg-12">

                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'podate'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($purchase->date)), ' class="form-control input-tip datetime" id="podate" required="required"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference_no', 'poref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $purchase->reference_no), 'class="form-control input-tip" id="poref" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Showroom', 'powarehouse'); ?>
                                <?php
                                $wh[''] = '';
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $purchase->warehouse_id), 'id="powarehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Showroom') . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('status', 'postatus'); ?>
                                <?php
								
								if($purchase->status=='received' || $purchase->status=='partial'){
								if($co_type==2)
                                $post = ['received' => lang('Purchase_invoice_received'),'received1' => lang('Purchase_invoice_pendding')];
								else
								$post = ['received' => lang('Purchase_invoice_received'),'received1' => lang('Purchase_invoice_pendding'), 'partial' => lang('partial')];
								
                                }else{
								if($co_type==2)
                                $post = ['received' => lang('Purchase_invoice_received'),'received1' => lang('Purchase_invoice_pendding'), 'pending' => lang('purchase_pending'), 'ordered' => lang('ordered')];
                                else
								$post = ['received' => lang('Purchase_invoice_received'),'received1' => lang('Purchase_invoice_pendding'), 'partial' => lang('partial'), 'pending' => lang('purchase_pending'), 'ordered' => lang('ordered')];
                                }
								echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : $purchase->status), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('status') . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>

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
                                    <div class="col-md-6" style="display:none;">
                                        <div class="form-group">
                                            <?= lang('Car_Owner', 'posupplier'); ?>
                                            <div class="input-group">
                                                <input type="hidden" name="supplier" value="" id="posupplier"
                                                       class="form-control" style="width:100%;"
                                                       placeholder="<?= lang('select') . ' ' . lang('Car_Owner') ?>">

                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <a href="#" id="removeReadonly">
                                                        <i class="fa fa-unlock" id="unLock"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <input type="hidden" name="supplier_id" value="" id="supplier_id" class="form-control">
                                        </div>
                                    </div>
									<div class="col-md-3">
									<div class="form-group">
										<?= lang('tax_method_type', 'tax_method_type'); ?>
										<?php
										//print_r( $purchase->product_discount);//->product_tax."pppp";
										$tmt=1;
										if($purchase->product_tax>0 || $purchase->product_discount>0)
										$tmt=2;
										$type = ['1' => lang('tax_invoices_type'), '2' => lang('tax_products_type')];
										echo form_dropdown('tax_method_type', $type, $tmt, 'id="tax_method_type" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Showroom') . '" required="required" style="width:100%;" '); ?>
									</div>
								</div>
								
								
								<div class="col-md-3">
									<div class="form-group">
										<?= lang('currency', 'currency'); ?>
										<?php
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
											echo form_dropdown('currency', $curr, ($inv->currency_code ? $inv->currency_code : $Settings->default_currency), 'id="pocurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>
									</div>
								
								

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
						

                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line('add_product_to_order') . '"'); ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;display:none;">
                                            <a href="#" id="addManually"><i class="fa fa-2x fa-plus-circle addIcon"
                                                                            id="addIcon"></i></a></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang('order_items'); ?></label>

                                <div class="controls table-controls">
                                    <table id="poTable"
                                           class="table items table-striped  table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                            <!--<?php
                                            if ($Settings->product_expiry) {
                                                echo '<th class="col-md-1">' . $this->lang->line('expiry_date') . '</th>';
                                            }
                                            ?>-->
                                            <th class="col-md-2"><?= lang('net_unit_cost'); ?></th>
                                            <th class="col-md-1"><?= lang('quantity'); ?></th>
                                            <th class="col-md-1 rec_con"><?= lang('received'); ?></th>
                                            <th class="col-md-1 rec_con"><?= lang('receive'); ?></th>
                                            <?php
                                            if ($Settings->product_discount) 
											{
                                                echo '<th class="col-md-1" id="div_th_discount">' . $this->lang->line('discount') . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) 
											{
                                                echo '<th class="col-md-1" id="div_th_tax">' . $this->lang->line('product_tax') . '</th>';
                                            }
                                            ?>
                                            <th><?= lang('subtotal'); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                            <th style="width: 30px !important; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>
						
						
						<div id="CostCenters" style="display: none;">
						<?php
						if($Settings->accounting){
						?>
					
                            <div class="col-md-12">
                            	<div class="row">
									<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
										<button id="addCostCenter" class="btn btn-primary btn-block" type="button"><?= lang('Add_cost_center') ?></button>
									</div>
								</div>
							</div>
						<?php
						}
						?>
						</div>
                       

                        <div class="col-md-12" id="more_options">
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" value=""/>
                                <label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>
                            <div class="row" id="extras-con" style="display: none;">
                                <?php if ($Settings->tax1) {
                                                ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('order_tax', 'potax2') ?>
                                            <?php
                                            $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('order_tax', $tr, '', 'id="potax2" class="form-control input-tip select" style="width:100%;"'); ?>
                                        </div>
                                    </div>
                                <?php
                                            } ?>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('discount_label', 'podiscount'); ?>
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="podiscount"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('payment_term', 'popayment_term'); ?>
                                        <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="popayment_term"'); ?>
                                    </div>
                                </div>
								
								
								<div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('shipping', 'poshipping'); ?>
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
                                    </div>
                                </div>
								<div class="col-md-4" style="display:none">
                                    <div class="form-group">
                                        <?= lang('shipping_received', 'poshipping_received'); ?>
                                        <?php echo form_input('shipping_received', '', 'class="form-control input-tip" id="poshipping_received"'); ?>
                                    </div>
                                </div>
								<div class="col-md-4" style="display:none">
                                    <div class="form-group">
                                        <?= lang('shipping_amount', 'poshipping_amount'); ?>
                                        <?php echo form_input('shipping_amount', '', 'class="form-control input-tip" id="poshipping_amount"'); ?>
                                    </div>
                                </div>
								
								
								<div class="col-md-4" id="div_customs_fees">
                                    <div class="form-group">
                                        <?= lang('customs_fees', 'customs_fees'); ?>
                                        <?php echo form_input('customs_fees', '', 'class="form-control input-tip" id="customs_fees"'); ?>
                                    </div>
                                </div>
								
								<div class="col-md-4" id="div_Other_fees">
                                    <div class="form-group">
                                        <?= lang('Other_fees', 'Other_fees'); ?>
                                        <?php echo form_input('Other_fees', '', 'class="form-control input-tip" id="Other_fees"'); ?>
                                    </div>
                                </div>
								
								<?php
								if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
		
								?>

								<div class="col-md-4" id="receiving_date_div">
                                    <div class="form-group">
                                       <?= lang('Receiving_date','Receiving_date'); ?>
                                     <?php echo form_input('receiving_date', '', 'class="form-control input-tip datetime" id="receiving_date"'); ?>
                                    </div>
                                </div>
								
								<?php
								}
								?>

								
								

                                
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <?= lang('note', 'ponote'); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('edit_pruchase', $this->lang->line('submit'), 'id="edit_pruchase" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
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
                        <div class="form-group">
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
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <?php if ($Settings->product_expiry) {
                                                ?>
                        <div class="form-group">
                            <label for="pexpiry" class="col-sm-4 control-label"><?= lang('product_expiry') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control date" id="pexpiry">
                            </div>
                        </div>
                    <?php
                                            } ?>
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
                                <input type="text" class="form-control" id="pdiscount">
                            </div>
                        </div>
                    <?php
                                            } ?>
                    <div class="form-group">
                        <label for="pcost" class="col-sm-4 control-label"><?= lang('unit_cost') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pcost">
                        </div>
                    </div>
                    <table class="table  table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_cost'); ?></th>
                            <th style="width:25%;"><span id="net_cost"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <div class="panel panel-default">
                        <div class="panel-heading"><?= lang('calculate_unit_cost'); ?></div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label for="pcost" class="col-sm-4 control-label"><?= lang('subtotal') ?></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="psubtotal">
                                        <div class="input-group-addon" style="padding: 2px 8px;">
                                            <a href="#" id="calculate_unit_price" class="tip" title="<?= lang('calculate_unit_cost'); ?>">
                                                <i class="fa fa-calculator"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="punit_cost" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_cost" value=""/>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_standard_product') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="mError"></span>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('product_code', 'mcode') ?> *
                            <input type="text" class="form-control" id="mcode">
                        </div>
                        <div class="form-group">
                            <?= lang('product_name', 'mname') ?> *
                            <input type="text" class="form-control" id="mname">
                        </div>
                        <div class="form-group">
                            <?= lang('category', 'mcategory') ?> *
                            <?php
                            $cat[''] = '';
                            foreach ($categories as $category) {
                                $cat[$category->id] = $category->name;
                            }
                            echo form_dropdown('category', $cat, '', 'class="form-control select" id="mcategory" placeholder="' . lang('select') . ' ' . lang('category') . '" style="width:100%"')
                            ?>
                        </div>
                        <div class="form-group">
                            <?= lang('unit', 'munit') ?> *
                            <input type="text" class="form-control" id="munit">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('cost', 'mcost') ?> *
                            <input type="text" class="form-control" id="mcost">
                        </div>
                        <div class="form-group">
                            <?= lang('price', 'mprice') ?> *
                            <input type="text" class="form-control" id="mprice">
                        </div>

                        <?php if ($Settings->tax1) {
                                ?>
                            <div class="form-group">
                                <?= lang('product_tax', 'mtax') ?>
                                <?php
                                $tr[''] = '';
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, '', 'id="mtax" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>
                            <div class="form-group all">
                                <?= lang('tax_method', 'mtax_method') ?>
                                <?php
                                $tm = ['0' => lang('inclusive'), '1' => lang('exclusive')];
                                echo form_dropdown('tax_method', $tm, '', 'class="form-control select" id="mtax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"')
                                ?>
                            </div>
                        <?php
                            } ?>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
		
		
		var costCount  = 0;
			        //$('#CostCenters').slideDown();
    
		$('button#addCostCenter').click(function(){
			var divOption    = '<div class="row" id="costRow'+costCount+'">';
			var selectOption = '<div class="col-5" style="padding-left:30px;padding-right:25px;"><select class="form-control"  name="costCenterId[]" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-4" dir="ltr"><input class="form-control" placeholder="<?= lang('Amount') ?>" name="costCenterAmount[]" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#CostCenters').append(divOption);
			$('#costRow'+costCount).append(selectOption);
			$('#costRow'+costCount).append(inputOption);
			$('#costRow'+costCount).append(buttonOption);
			costCount += 1;

		});
	});
</script>
