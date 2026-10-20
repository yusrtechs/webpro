<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = [];
}
?>
<script type="text/javascript">
    $(document).ready(function () {
		
		
		
		$('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'products');
        });
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", minimumResultsForSearch: 7, data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    minimumResultsForSearch: 7,
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
        $('#code').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_product'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('products/add_product', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

          

            <div class="row">
                    <div class="col-md-5">
                    <div class="form-group">
                        <?= lang('product', 'type') ?>
                        <?php
                        $opts = ['standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service')];
                        echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
                        ?>
                    </div>
					<div class="form-group">
                        <?= lang('product_type', 'type2') ?>
                        <?php
                        $opts = ['primary' => lang('primary'), 'complete' => lang('complete'), 'service' => lang('service_product')];
                        echo form_dropdown('type2', $opts, (isset($_POST['type2']) ? $_POST['type2'] : ($product ? $product->type2 : '')), 'class="form-control" id="type2" required="required"');
                        ?>
                    </div>
					
                    <div class="form-group all">
                        <?= lang('product_name', 'name') ?>
                        <?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control gen_slug" id="name" required="required"'); ?>
                    </div>
					
                    <div class="form-group all">
                        <?= lang('product_code', 'code') ?>
                        <div class="input-group">
                            <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                        <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>
                    </div>
					
					<div class="form-group all"   style="display:none">
                        <?= lang('product_number', 'product_number'); ?>
						<?php 
						$max_p_no = '';
                        foreach ($max_product_number as $max_product_no) {
                            $max_p_no = $max_product_no->max_product_number;
                        }
                        
                        echo form_input('product_number', $max_p_no, 'class="form-control tip" id="product_number" '); 
						?>
                    </div>
					
                    <div class="form-group all">
                        <?= lang('slug', 'slug'); ?>
                        <?= form_input('slug', set_value('slug'), 'class="form-control tip" id="slug" required="required"'); ?>
                    </div>
	
					
                    <div class="form-group all">
                        <?= lang('second_name', 'second_name'); ?>
                        <?= form_input('second_name', set_value('second_name'), 'class="form-control tip" id="second_name"'); ?>
                    </div>

                    <div class="form-group standard_combo">
                        <?= lang('weight', 'weight'); ?>
                        <?= form_input('weight', set_value('weight'), 'class="form-control tip" id="weight"'); ?>
                    </div>
                    <div class="form-group all">
                        <?= lang('barcode_symbology', 'barcode_symbology') ?>
                        <?php
                        $bs = ['code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca' => 'UPC-A', 'upce' => 'UPC-E'];
                        echo form_dropdown('barcode_symbology', $bs, (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product ? $product->barcode_symbology : 'code128')), 'class="form-control select" id="barcode_symbology" required="required" style="width:100%;"');
                        ?>

                    </div>
                    <div class="form-group all">
                        <?= lang('brand', 'brand') ?>
                        <?php
                        $br[''] = '';
                        foreach ($brands as $brand) {
                            $br[$brand->id] = $brand->name;
                        }
                        echo form_dropdown('brand', $br, (isset($_POST['brand']) ? $_POST['brand'] : ($product ? $product->brand : '')), 'class="form-control select" id="brand" placeholder="' . lang('select') . ' ' . lang('brand') . '" style="width:100%"')
                        ?>
                    </div>
                    <div class="form-group all">
					    <?= lang('category', 'category') ?>
                    <div class="input-group">
                        <?php
                        $cat[''] = '';
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang('select') . ' ' . lang('category') . '" required="required" style="width:100%"')
                        ?>
						<?php if ($Owner || $Admin || $GP['category-add']) {
                         ?>
                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
									<a href="<?= admin_url('products/add_category'); ?>" id="add-category"class="external" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                    </a>
                                </div>
                        <?php
                        } ?>
                                           
                    </div>
                    </div>
                    <div class="form-group all">
                        <?= lang('subcategory', 'subcategory') ?>
                        <div class="controls" id="subcat_data"> <?php
                            echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang('select_category_to_load') . '"');
                            ?>
                        </div>
                    </div>
                    <div class="form-group standard_combo">
                        <?= lang('product_unit', 'unit'); ?>
                    <div class="input-group">
					<?php
                        $pu[''] = lang('select') . ' ' . lang('unit');
                        foreach ($base_units as $bu) {
                            $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                        }
                        ?>
                        <?= form_dropdown('unit', $pu, set_value('unit', ($product ? $product->unit : '')), 'class="form-control tip" id="unit" required="required" style="width:100%;"'); ?>
						
							<?php if ($Owner || $Admin || $GP['category-add']) {
                         ?>
                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
									<a href="<?= admin_url('products/add_unit'); ?>" id="add-category"class="external" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                    </a>
                                </div>
                        <?php
                        } ?>
                                           
                    </div>
                    
                    </div>
                    <div class="form-group standard_combo">
                        <?= lang('default_sale_unit', 'default_sale_unit'); ?>
                        <?php $uopts[''] = lang('select_unit_first'); ?>
                        <?= form_dropdown('default_sale_unit', $uopts, ($product ? $product->sale_unit : ''), 'class="form-control" id="default_sale_unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group standard"  style="display:none">
                        <?= lang('default_purchase_unit', 'default_purchase_unit'); ?>
                        <?= form_dropdown('default_purchase_unit', $uopts, ($product ? $product->purchase_unit : ''), 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group" style="display:none">
                        <?= lang('product_cost', 'cost') ?>
                        <?= form_input('cost', (isset($_POST['cost']) ? $_POST['cost'] : ($product ? $this->sma->formatDecimal($product->cost) : '')), 'class="form-control tip" id="cost" ') ?>
                    </div>
                    <div class="form-group all">
                        <?= lang('product_price', 'price') ?>
                        <?= form_input('price', (isset($_POST['price']) ? $_POST['price'] : ($product ? $this->sma->formatDecimal($product->price) : '')), 'class="form-control tip" id="price" required="required"') ?>
                    </div>
					<?php if ($Settings->product_expiry) { ?>
					<div class="form-group">
						<?= lang('expiry', 'expiry'); ?>
						<?= form_input('expiry', set_value('expiry'), 'class="form-control tip date" id="expiry"'); ?>
					</div>
					 <?php } ?>
                    <div class="form-group">
                        <input type="checkbox" class="checkbox" value="1" name="promotion" id="promotion" <?= $this->input->post('promotion') ? 'checked="checked"' : ''; ?>>
                        <label for="promotion" class="padding05">
                            <?= lang('promotion'); ?>
                        </label>
                    </div>

                    <div id="promo" style="display:none;">
                        <div class="well well-sm">
                            <div class="form-group">
                                <?= lang('promo_price', 'promo_price'); ?>
                                <?= form_input('promo_price', set_value('promo_price'), 'class="form-control tip" id="promo_price"'); ?>
                            </div>
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?= form_input('start_date', set_value('start_date'), 'class="form-control tip date" id="start_date"'); ?>
                            </div>
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?= form_input('end_date', set_value('end_date'), 'class="form-control tip date" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($Settings->invoice_view == 200) {
                            ?>
                        <div class="form-group">
                            <?= lang('hsn_code', 'hsn_code'); ?>
                            <?= form_input('hsn_code', set_value('hsn_code', ($product ? $product->hsn_code : '')), 'class="form-control" id="hsn_code"'); ?>
                        </div>
                    <?php
                        } ?>

                    <?php if ($Settings->tax1) {
                            ?>
                        <div class="form-group all">
                            <?= lang('product_tax', 'tax_rate') ?>
                            <?php
                            $tr[''] = '';
                            foreach ($tax_rates as $tax) {
                                $tr[$tax->id] = $tax->name;
                            }
                            echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : ($product ? $product->tax_rate : $Settings->default_tax_rate)), 'class="form-control select" id="tax_rate" placeholder="' . lang('select') . ' ' . lang('product_tax') . '" style="width:100%"')
                            ?>
                        </div>
                        <div class="form-group all" id="div_tax_method" style="display:none">
                            <?= lang('tax_method', 'tax_method') ?>
                            <?php
                            $tm = ['1' => lang('exclusive'), '0' => lang('inclusive')];
                            echo form_dropdown('tax_method', $tm, (isset($_POST['tax_method']) ? $_POST['tax_method'] : ($product ? $product->tax_method : '')), 'class="form-control select" id="tax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"'); ?>
                        </div>
                    <?php
                        } ?>
					<div class="form-group standard">
                        <?= lang('alert_quantity', 'alert_quantity') ?>
                        <div
                            class="input-group"> <?= form_input('alert_quantity', (isset($_POST['alert_quantity']) ? $_POST['alert_quantity'] : ($product ? $this->sma->formatQuantityDecimal($product->alert_quantity) : '')), 'class="form-control tip" id="alert_quantity"') ?>
                            <span class="input-group-addon">
                            <input type="checkbox" name="track_quantity" id="track_quantity"
                                   value="1" <?= ($product ? (isset($product->track_quantity) ? 'checked="checked"' : '') : 'checked="checked"') ?>>
                        </span>
                        </div>
                    </div>

                    <div class="form-group all">
                        <?= lang('product_image', 'product_image') ?>
                        <input id="product_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="product_image" data-show-upload="false"
                               data-show-preview="false" accept="image/*" class="form-control file">
                    </div>

                    <div class="form-group all">
                        <?= lang('product_gallery_images', 'images') ?>
                        <input id="images" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile[]" multiple="true" data-show-upload="false"
                               data-show-preview="false" class="form-control file" accept="image/*">
                    </div>
                    <div id="img-details"></div>
                </div>
                <div class="col-md-6 col-md-offset-1">
					<div class="form-group row">
					<h3><?= lang('warehouses'); ?></h3>
					<div  class="col-md-9">
						<?php
						//$wh['all'] =  lang('all');
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_multiselect('wh_option[]', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : null), 'id="pwarehouse" class="form-control input-tip multiselect" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;" '); 
					?>
			
                                </div>
						<div class="col-md-3" id="allck">
							<input type="checkbox" class="checkbox" name="attributes"
								   id="all" ><label
								for="attributes"
								class="padding05"><?= lang('all') ?></label>
						</div>
						
					<!--
					<?php foreach($warehouses as $row){?>
						<div class="col-md-4">
							<input type="checkbox" class="checkbox" name="attributes"
								   id="wh" <?= $this->input->post('wh') || $row->id ? 'checked="checked"' : ''; ?>><label
								for="attributes"
								class="padding05"><?= $row->name ?></label>
						</div>
						
					<?php } ?>
					-->
					</div>
					<div class="form-group row">
						<hr>
					</div>
                    <div class="standard">

                        <div id="attrs"></div>

                        <div class="form-group">
                            <input type="checkbox" class="checkbox" name="attributes"
                                   id="attributes" <?= $this->input->post('attributes') || $product_options ? 'checked="checked"' : ''; ?>><label
                                for="attributes"
                                class="padding05"><?= lang('product_has_attributes'); ?></label> <?= lang('eg_sizes_colors'); ?>
                        </div>
                        <div class="well well-sm" id="attr-con"
                             style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?>">
                            <div class="form-group" id="ui" style="margin-bottom: 0;">
                                <div class="input-group">
                                    <?php echo form_input('attributesInput', '', 'class="form-control select-tags" id="attributesInput" placeholder="' . $this->lang->line('enter_attributes') . '"'); ?>
                                    <div class="input-group-addon" style="padding: 2px 5px;">
                                        <a href="#" id="addAttributes">
                                            <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
                                        </a>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="table-responsive">
                                <table id="attrTable" class="table  table-condensed table-striped"
                                       style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?>margin-bottom: 0; margin-top: 10px;">
                                    <thead>
                                    <tr class="active">
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('warehouse') ?></th>
                                        <!-- <th><?= lang('quantity') ?></th> -->
                                        <th><?= lang('price_addition') ?></th>
                                        <th><i class="fa fa-times attr-remove-all"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody><?php
                                    if ($this->input->post('attributes')) {
                                        $a = sizeof($_POST['attr_name']);
                                        for ($r = 0; $r <= $a; $r++) {
                                            if (isset($_POST['attr_name'][$r]) && (isset($_POST['attr_warehouse'][$r]) || isset($_POST['attr_quantity'][$r]))) {
                                                echo '<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' . $_POST['attr_name'][$r] . '"><span>' . $_POST['attr_name'][$r] . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $_POST['attr_warehouse'][$r] . '"><input type="hidden" name="attr_wh_name[]" value="' . $_POST['attr_wh_name'][$r] . '"><span>' . $_POST['attr_wh_name'][$r] . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $_POST['attr_price'][$r] . '"><span>' . $_POST['attr_price'][$r] . '</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>';
                                                // echo '<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' . $_POST['attr_name'][$r] . '"><span>' . $_POST['attr_name'][$r] . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $_POST['attr_warehouse'][$r] . '"><input type="hidden" name="attr_wh_name[]" value="' . $_POST['attr_wh_name'][$r] . '"><span>' . $_POST['attr_wh_name'][$r] . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="' . $this->sma->formatQuantityDecimal($_POST['attr_quantity'][$r]) . '"><span>' . $this->sma->formatQuantity($_POST['attr_quantity'][$r]) . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $_POST['attr_price'][$r] . '"><span>' . $_POST['attr_price'][$r] . '</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>';
                                            }
                                        }
                                    } elseif ($product_options) {
                                        foreach ($product_options as $option) {
                                            echo '<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' . $option->name . '"><span>' . $option->name . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $option->warehouse_id . '"><input type="hidden" name="attr_wh_name[]" value="' . $option->wh_name . '"><span>' . $option->wh_name . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="' . $this->sma->formatQuantityDecimal($option->wh_qty) . '"><span>' . $this->sma->formatQuantity($option->wh_qty) . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $this->sma->formatMoney($option->price) . '"><span>' . $this->sma->formatMoney($option->price) . '</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>';
                                        }
                                    }
                                    ?></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        
                        <div class="clearfix"></div> 

                    </div>
                    <div class="combo" style="display:none;">

                        <div class="form-group">
                            <?= lang('add_product', 'add_item') . ' (' . lang('not_with_variants') . ')'; ?>
                            <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line('add_item') . '"'); ?>
                        </div>
                        <div class="control-group table-group">
                            <label class="table-label" for="combo"><?= lang('combo_products'); ?></label>

                            <div class="controls table-controls">
                                <table id="prTable"
                                       class="table items table-striped  table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('quantity'); ?></th>
                                        <!--<th class="col-md-3 col-sm-3 col-xs-3"><?= lang('price'); ?></th>-->
                                        <th class="col-md-1 col-sm-1 col-xs-1 text-center">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
						    <div id="ex-combo"></div>

                    </div>

                    <div class="digital" style="display:none;">
                        <div class="form-group digital">
                            <?= lang('digital_file', 'digital_file') ?>
                            <input id="digital_file" type="file" data-browse-label="<?= lang('browse'); ?>" name="digital_file" data-show-upload="false"
                                   data-show-preview="false" class="form-control file">
                        </div>
                        <div class="form-group">
                            <?= lang('file_link', 'file_link'); ?>
                            <?= form_input('file_link', set_value('file_link'), 'class="form-control" id="file_link"'); ?>
                        </div>
                    </div>

                <div class="form-group standard">
                    <div class="form-group">
                        <?= lang('supplier', 'supplier') ?>
                        <button type="button" class="btn btn-primary btn-xs" id="addSupplier"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="row" id="supplier-con">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <?php
                                echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control ' . ($product ? '' : 'suppliers') . '" id="' . ($product && !empty($product->supplier1) ? 'supplier1' : 'supplier') . '" placeholder="' . lang('select') . ' ' . lang('supplier') . '" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <?= form_input('supplier_part_no', (isset($_POST['supplier_part_no']) ? $_POST['supplier_part_no'] : ''), 'class="form-control tip" id="supplier_part_no" placeholder="' . lang('supplier_part_no') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                            <?= form_input('supplier_price', (isset($_POST['supplier_price']) ? $_POST['supplier_price'] : ''), 'class="form-control tip" id="supplier_price" placeholder="' . lang('supplier_price') . '"'); ?>
                            </div>
                        </div>
                    </div>
                    <div id="ex-suppliers"></div>
                </div>

                </div>
				
				
				
				
				
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_product', lang('add_product'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>

<script type="text/javascript">
    $(document).ready(function () {
		
		
		$('#tax_rate').change(function () {
			
			$("#div_tax_method").css("display", "block");
				
			
			try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'products/getTax_Rate/' + $('#tax_rate').val(),
                dataType: 'json',
                data: { id : $('#tax_rate').val()},
                success: function(data, page) {
					
					if (data>0) {
                        $('#tax_method').val("1").trigger('change');
				        $('#tax_method').select2('readonly', false);
                        
				
                    	
                    } else {
                    	$('#tax_method').val("1").trigger('change');
				        $('#tax_method').select2('readonly', true);
                        
				
                    	
                    }
					
                },
            });
			
		}catch(err){alert(err);}
        
        });
		
        $('form[data-toggle="validator"]').bootstrapValidator({ excluded: [':disabled'] });
        var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
        var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
        var items = {};
        <?php
        if ($combo_items) {
            foreach ($combo_items as $item) {
                //echo 'ietms['.$item->id.'] = '.$item.';';
                if ($item->code) {
                    echo 'add_product_item(' . json_encode($item) . ');';
                }
            }
        }
        ?>
        <?=isset($_POST['cf']) ? '$("#extras").iCheck("check");' : '' ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });

        <?= isset($_POST['promotion']) ? '$("#promotion").iCheck("check");' : '' ?>
        $('#promotion').on('ifChecked', function (e) {
            $('#promo').slideDown();
        });
        $('#promotion').on('ifUnchecked', function (e) {
            $('#promo').slideUp();
        });

        $('.attributes').on('ifChecked', function (event) {
            $('#options_' + $(this).attr('id')).slideDown();
        });
        $('.attributes').on('ifUnchecked', function (event) {
            $('#options_' + $(this).attr('id')).slideUp();
        });
        //$('#cost').removeAttr('required');
        $('#digital_file').change(function () {
            if ($(this).val()) {
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
        });
        $('#type').change(function () {
            var t = $(this).val();
            if (t !== 'standard') {
                $('.standard').slideUp();
                $('#unit').attr('disabled', true);
                $('#cost').attr('disabled', true);
                $('#track_quantity').iCheck('uncheck');
            } else {
                $('.standard').slideDown();
                $('#track_quantity').iCheck('check');
                $('#unit').attr('disabled', false);
                $('#cost').attr('disabled', false);
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
                $('#file_link').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
            } else {
                $('.digital').slideDown();
                $('#file_link').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
            } else {
                $('.combo').slideDown();
            }
            if (t == 'standard' || t == 'combo') {
                $('.standard_combo').slideDown();
				$('#cost').attr('disabled', false);
				$('#unit').removeAttr('disabled');
            } else {
                $('.standard_combo').slideUp();
            }
        });

        var t = $('#type').val();
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#unit').attr('disabled', true);
            $('#cost').attr('disabled', true);
            $('#track_quantity').iCheck('uncheck');
        } else {
            $('.standard').slideDown();
            $('#track_quantity').iCheck('check');
            $('#unit').attr('disabled', false);
            $('#cost').attr('disabled', false);
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
        } else {
            $('.combo').slideDown();
        }
        if (t == 'standard' || t == 'combo') {
            $('.standard_combo').slideDown();
        } else {
            $('.standard_combo').slideUp();
        }
		
        

        
        $("#add_item").autocomplete({
		
            source: '<?= admin_url('products/suggestions2'); ?>' ,
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
				
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
					$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
				
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item);
					if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });

        <?php
        if ($this->input->post('type') == 'combo') {
            $c = isset($_POST['combo_item_code']) ? sizeof($_POST['combo_item_code']) : 0;
            for ($r = 0; $r <= $c; $r++) {
                if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                    $items[] = ['id' => $_POST['combo_item_id'][$r], 'name' => $_POST['combo_item_name'][$r], 'code' => $_POST['combo_item_code'][$r], 'unit' => $_POST['combo_item_unit'][$r], 'qty' => $_POST['combo_item_quantity'][$r], 'price' => $_POST['combo_item_price'][$r]];
                }
            }
            echo '
            var ci = ' . (isset($items) ? json_encode($items) : "''") . ';
            $.each(ci, function() { add_product_item(this); });
            ';
        }
        ?>
		
	$('#all').on('ifChecked', function (e) {
		
		$("#pwarehouse > option").prop("selected","selected");
          $("#pwarehouse").trigger("change");  
        });
        $('#all').on('ifUnchecked', function (e) {
         $("#pwarehouse > option").removeAttr("selected");
          $("#pwarehouse").trigger("change");  
           
        });
	
		
		$('#pwarehouse').change(function(e) {
			
			
			
			$("#pwarehouse > option").each(function(){
				
				$("#prTable"+ this.value).hide();
                $("#title"+ this.value).hide();
            
			
			});
			
			var items = {};
			try{
			var wh = [];
        var i=0;
			$('#pwarehouse :selected').each(function() {
            wh[i] = $(this).val();
			
			    //$('#supplier_1').select2('destroy');
                //var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + wh[i] + '", class="form-control" id="supplier_' + wh[i] + '" placeholder="<?= lang('select') . ' ' . lang('supplier') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + wh[i] + '_part_no" class="form-control tip" id="supplier_' + wh[i] + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + wh[i] + '_price" class="form-control tip" id="supplier_' + i + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
                var html = '<div class="control-group table-group" id="title' + wh[i] + '"><label class="table-label" for="combo"><?= lang('combo_products'); ?> (' + $(this).text() + ')</label><div class="controls table-controls"><table id="prTable' + wh[i] + '" class="table items table-striped  table-condensed table-hover"><thead><tr><th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th><th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th><th class="col-md-3 col-sm-3 col-xs-3"><?= lang('cost'); ?></th><th class="col-md-1 col-sm-1 col-xs-1 text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr></thead><tbody></tbody><tfoot></tfoot></table></div></div>';
				$('#ex-combo').append(html);
				
				
				
				multiTable(wh[i],wh);
				
                //var sup = $('#supplier_' + i);
                //suppliers(sup);
				
				
				
			
				
				
                //su++;
            
			i++;
			
        });
			//var wh = $('#pwarehouse').val();
			
          }catch(err){alert(err);}  
        });
		
		
		
		function multiTable(i,wh){
			$('#add_item').autocomplete({
		
            source: function (request, response) {
                
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('products/suggestions2'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: wh
                        
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
				//alert(ui.content[0].id);
                    
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
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
				
				
				$('#pwarehouse').select2('readonly', true);
				$("#allck").css("display", "none");
				
				
		
               //alert(ui.item.id);
			   var counter=0;
			   var checkwp=1;
			
			$('#pwarehouse :selected').each(function() {
            //wh[counter] = $(this).val();
			for(var j=0;j<ui.item.wpr.length;j++){
					//if(wh[i]==this.wpr[j]['warehouse_id']   && this.code== this.wpr[j]['code']){
					if(ui.item.id==ui.item.wpr[j]['id'] && ui.item.wpr[j]['warehouse_id']=='no')
						checkwp=0;
					
					
				
					}
				
			
			   
			counter++;
			});
			   event.preventDefault();
			   try{
             
			 if (ui.item.id !== 0 && checkwp==1) {
					//var item =ui.item
					
                    var row = add_product_item(ui.item,i,wh);
					
					
					
					if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
			}catch(err){alert(err);}
            }
        });

			
		}
        function add_product_item(item,tabRow,wh) {
			var wpArr=wh;
			if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
            } else {
                items[item_id] = item;
            }
            var pp = 0;
            $("#prTable tbody").empty();
            $.each(items, function () {
                var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                //tr_html += '<td><input class="form-control text-center runit" name="combo_item_unit[]" type="text" value="' + formatDecimal(this.unit) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><span id="unit_' + row_no + '">' + this.unit.name + '</span></td>';
                tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatDecimal(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';                
				//tr_html += '<td><input disabled class="form-control text-center rprice2" name="combo_item_price[]" type="text" value="' + formatDecimal(this.price) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				tr_html += '<input  class="form-control text-center rprice" name="combo_item_price[]" type="hidden" value="' + formatDecimal(this.price) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();">';
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable" );
                pp += formatDecimal(parseFloat(this.price)*parseFloat(this.qty));
            });
            $('.item_' + item_id).addClass('warning');
            $('#cost').val(pp);
			
			for(var i=0;i<wpArr.length; i++){
            
			
			
			
			
			if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
             //   items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
            } else {
                items[item_id] = item;
            }
            var pp = 0;
            var fp = 0;
            $("#prTable"+ wh[i] + " tbody").empty();
            $.each(items, function () {
				var wpcost;
				for(var j=0;j<this.wpr.length;j++){
					if(wh[i]==this.wpr[j]['warehouse_id']   && this.code== this.wpr[j]['code']){
					wpcost=this.wpr[j]['avg_cost'];
				break;
					}
				}
			
                var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                //tr_html += '<td><input class="form-control text-center runit" name="combo_item_unit[]" type="text" value="' + formatDecimal(this.unit) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><span id="unit_' + row_no + '">' + this.unit.name + '</span></td>';
                //tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatDecimal(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';                
				tr_html += '<td><input disabled class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(wpcost) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				//tr_html += '<input  class="form-control text-center rprice" name="combo_item_price[]" type="hidden" value="' + formatDecimal(this.price) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();">';
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;display:none"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable"+ wh[i] );
				
        
                pp += formatDecimal(parseFloat(this.price)*parseFloat(this.qty));
				fp +=parseFloat(wpcost)*this.qty;
            });
            $('.item_' + item_id).addClass('warning');
			var tfoot ='<tr id="tfoot" class="tfoot active"><th colspan="2"><?= lang('total') ?></th><th class="text-center total"><input disabled class="form-control text-center" name="total' + wh[i] + '" id="total' + wh[i] + '" type="text" value="' + formatDecimal(fp) + '"></th><tr>';
				$("#prTable"+ wh[i] + " tfoot").html(tfoot);
		
            $('#cost').val(pp);
			}
            return true;
        }

        function calculate_price(new_qty,id) {
			var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            var qty = [];
			var j=0;
			$.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()));
				qty[j]=parseFloat($(this).find('.rquantity').val());
				j++;
            });
            $('#cost').val(pp);
            
			
			
			var wh = [];
        var i=0;
			$('#pwarehouse :selected').each(function() {
            var ppwp = 0;
			wh[i] = $(this).val();
			
			var rowswp = $('#prTable'+ wh[i]).children('tbody').children('tr');
            
            var j=0;
			$.each(rowswp, function () {
			//if($(this).attr('id').substring(4)==id.substring(9))
                ppwp += formatDecimal(parseFloat($(this).find('.rprice').val())*qty[j]);
			//else
			//    ppwp += formatDecimal(parseFloat($(this).find('.rprice').val()));
				
				j++;
            });
            
			
			$('#total'+ wh[i]).val(formatDecimal(ppwp));
			
			i++;
			});
			
			
            return true;
        }

        $(document).on('change', '.rquantity, .rprice', function () {
			var row = $(this).closest('tr');
            if (!is_numeric($(this).val())) {
                $(this).val(old_row_qty);
                bootbox.alert(lang.unexpected_value);
                return;
            }
            var new_qty = parseFloat($(this).val());
            var id = $(this).attr('id');
             
            
			calculate_price(new_qty,id);
        });

        $(document).on('click', '.del', function () {
			
			
            var id = $(this).attr('id');
			
            delete items[id];
            $(this).closest('#row_' + id).remove();
			
			var wh = [];
            var i=0;
			$('#pwarehouse :selected').each(function() {
            wh[i] = $(this).val();
			
			var rowswp = $('#prTable'+ wh[i]).children('tbody').children('tr');
            $.each(rowswp, function () {
			   // ppwp += formatDecimal(parseFloat($(this).find('.rprice').val())*qty[j]);
				
			//var id = $(this).find('.del').attr('id');
            delete items[id];
            $(this).find('.del').closest('#row_' + id).remove();
			
				
            });
            
			
			
			
			});
			
			
            calculate_price();
        });
        var su = 2;
        $('#addSupplier').click(function () {
            if (su <= 5) {
                $('#supplier_1').select2('destroy');
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang('select') . ' ' . lang('supplier') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
                $('#ex-suppliers').append(html);
                var sup = $('#supplier_' + su);
                suppliers(sup);
                su++;
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });

        var _URL = window.URL || window.webkitURL;
        $("input#images").on('change.bs.fileinput', function () {
            var ele = document.getElementById($(this).attr('id'));
            var result = ele.files;
            $('#img-details').empty();
            for (var x = 0; x < result.length; x++) {
                var fle = result[x];
                for (var i = 0; i <= result.length; i++) {
                    var img = new Image();
                    img.onload = (function (value) {
                        return function () {
                            ctx[value].drawImage(result[value], 0, 0);
                        }
                    })(i);
                    img.src = 'images/' + result[i];
                }
            }
        });
        var variants = <?=json_encode($vars);?>;
        $(".select-tags").select2({
            tags: variants,
            tokenSeparators: [","],
            multiple: true
        });
        $(document).on('ifChecked', '#attributes', function (e) {
            $('#attr-con').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-con').slideUp();
        });
        $('#addAttributes').click(function (e) {
            e.preventDefault();
            var attrs_val = $('#attributesInput').val(), attrs;
            attrs = attrs_val.split(',');
            for (var i in attrs) {
                if (attrs[i] !== '') {
                    <?php if (!empty($warehouses)) {
            foreach ($warehouses as $warehouse) {
                echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $warehouse->id . '"><span>' . $warehouse->name . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');';
                // echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $warehouse->id . '"><span>' . $warehouse->name . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span>0</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');';
            }
        } else {
            ?>
                        $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                        // $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                    <?php
        } ?>
                }
            }
        });
//$('#attributesInput').on('select2-blur', function(){
//    $('#addAttributes').click();
//});
        $(document).on('click', '.delAttr', function () {
            $(this).closest("tr").remove();
        });
        $(document).on('click', '.attr-remove-all', function () {
            $('#attrTable tbody').empty();
            $('#attrTable').hide();
        });
        var row, warehouses = <?= json_encode($warehouses); ?>;
        $(document).on('click', '.attr td:not(:last-child)', function () {
            row = $(this).closest("tr");
            $('#aModalLabel').text(row.children().eq(0).find('span').text());
            $('#awarehouse').select2("val", (row.children().eq(1).find('input').val()));
            // $('#aquantity').val(row.children().eq(2).find('input').val());
            $('#aprice').val(row.children().eq(3).find('span').text());
            $('#aModal').appendTo('body').modal('show');
        });

        // $('#aModal').on('shown.bs.modal', function () {
        //     $('#aquantity').focus();
        //     $(this).keypress(function( e ) {
        //         if ( e.which == 13 ) {
        //             $('#updateAttr').click();
        //         }
        //     });
        // });
        $(document).on('click', '#updateAttr', function () {
            var wh = $('#awarehouse').val(), wh_name;
            $.each(warehouses, function () {
                if (this.id == wh) {
                    wh_name = this.name;
                }
            });
            row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" value="' + wh_name + '"><span>' + wh_name + '</span>');
            // row.children().eq(2).html('<input type="hidden" name="attr_quantity[]" value="' + ($('#aquantity').val() ? $('#aquantity').val() : 0) + '"><span>' + decimalFormat($('#aquantity').val()) + '</span>');
            // row.children().eq(3).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            row.children().eq(2).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            $('#aModal').modal('hide');
        });
    });

    <?php if ($product) {
            ?>
    $(document).ready(function () {
		
        var t = "<?=$product->type?>";
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('required', 'required');
            $('#track_quantity').iCheck('uncheck');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
        } else {
            $('.standard').slideDown();
            $('#track_quantity').iCheck('check');
            $('#cost').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'cost');
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#file_link').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'file_link');
        } else {
            $('.digital').slideDown();
            $('#file_link').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'file_link');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
            //$('#add_item').removeAttr('required');
            //$('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
        } else {
            $('.combo').slideDown();
            //$('#add_item').attr('required', 'required');
            //$('form[data-toggle="validator"]').bootstrapValidator('addField', 'add_item');
        }
        $("#code").parent('.form-group').addClass("has-error");
        $("#code").focus();
        $("#product_image").parent('.form-group').addClass("text-warning");
        $("#images").parent('.form-group').addClass("text-warning");
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $product->category_id ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                        placeholder: "<?= lang('select_category_to_load') ?>",
                        data: scdata
                    });
                } else {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                    });
                }
            }
        });
        <?php if ($product->supplier1) {
                ?>
        select_supplier('supplier1', "<?= $product->supplier1; ?>");
        $('#supplier_price').val("<?= $product->supplier1price == 0 ? '' : $this->sma->formatDecimal($product->supplier1price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier2) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_2', "<?= $product->supplier2; ?>");
        $('#supplier_2_price').val("<?= $product->supplier2price == 0 ? '' : $this->sma->formatDecimal($product->supplier2price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier3) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_3', "<?= $product->supplier3; ?>");
        $('#supplier_3_price').val("<?= $product->supplier3price == 0 ? '' : $this->sma->formatDecimal($product->supplier3price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier4) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_4', "<?= $product->supplier4; ?>");
        $('#supplier_4_price').val("<?= $product->supplier4price == 0 ? '' : $this->sma->formatDecimal($product->supplier4price); ?>");
        <?php
            } ?>
        <?php if ($product->supplier5) {
                ?>
        $('#addSupplier').click();
        select_supplier('supplier_5', "<?= $product->supplier5; ?>");
        $('#supplier_5_price').val("<?= $product->supplier5price == 0 ? '' : $this->sma->formatDecimal($product->supplier5price); ?>");
        <?php
            } ?>
        function select_supplier(id, v) {
            $('#' + id).val(v).select2({
                minimumInputLength: 1,
                data: [],
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
            });//.select2("val", "<?= $product->supplier1; ?>");
        }

        var whs = $('.wh');
        $.each(whs, function () {
            $(this).val($('#r' + $(this).attr('id')).text());
        });
    });
    <?php
        } ?>
    $(document).ready(function() {
		
        $('#unit').change(function(e) {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubUnits') ?>/" + v,
                    dataType: "json",
                    success: function (data) {
                        $('#default_sale_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#default_purchase_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $.each(data, function () {
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_sale_unit'));
                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_purchase_unit'));
                        });
                        $('#default_sale_unit').select2('val', v);
                        $('#default_purchase_unit').select2('val', v);
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#default_sale_unit').select2("destroy").empty();
                $('#default_purchase_unit').select2("destroy").empty();
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_sale_unit'));
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_purchase_unit'));
                $('#default_sale_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
                $('#default_purchase_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
            }
        });
		
		
		
		
		
    });
</script>

<div class="modal" id="aModal" tabindex="-1" role="dialog" aria-labelledby="aModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <iclass="fa fa-2x">&times;</i></span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="aModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="awarehouse" class="col-sm-4 control-label"><?= lang('warehouse') ?></label>
                        <div class="col-sm-8">
                            <?php
                            $wh[''] = '';
                            foreach ($warehouses as $warehouse) {
                                $wh[$warehouse->id] = $warehouse->name;
                            }
                            echo form_dropdown('warehouse', $wh, '', 'id="awarehouse" class="form-control"');
                            ?>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="aquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aquantity">
                        </div>
                    </div> -->
                    <input type="hidden" id="aquantity" value="0">
                    <div class="form-group">
                        <label for="aprice" class="col-sm-4 control-label"><?= lang('price_addition') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aprice">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="updateAttr"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

