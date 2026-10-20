<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.pr_link{
    cursor: pointer;
}
</style>

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

			$(document).on('click', '.pr_link', function(e) {
        var row = $(this).closest('tr');
        var itemid = row.find('.rid').val();
        $('#myModal').modal({ remote: site.base_url + 'products/modal_view/' + itemid });
        $('#myModal').modal('show');
    });


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
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file-text-o nb"></i><?= lang('Edit_Bill_Of_Material').' ( ' .$bom_details->name.' ) ' ; ?></h2>
    </div>
    <div class="box-content">
								<div class="alert alert-danger" id="mError-conBOM" style="display: none;">
                    			<span id="mErrorBOM"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-conBOM" style="display: none;">
                    			<span id="mSuccessBOM"></span>
								</div>

        <div class="row">
            <div class="col-lg-12">


				<form id="bomForm" method="post"  enctype="multipart/form-data">


				<div class="row">
                            <div class="col-sm-6">
                            <div class="form-group">
                <?= lang('code','code'); ?>
                <?= form_input('code', $bom_details->code, 'class="form-control" id="code_bom" required="required"'); ?>
            </div>


            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $bom_details->name, 'class="form-control gen_slug" id="name_bom" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('quantity', 'quantity'); ?>
                <?= form_input('quantity', $this->sma->formatQuantity($bom_details->quantity), 'class="form-control tip" id="quantity" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>


                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                    <?= lang('Raw_materials_warehouse', 'Raw_materials_warehouse'); ?>
                    <?php

                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_raw', $wh, $bom_details->wh_raw, 'id="wh_raw" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Raw_materials_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>

                <div class="form-group">
                    <?= lang('Manufacturing_warehouse', 'Manufacturing_warehouse'); ?>
                    <?php

                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_manufacturing', $wh, $bom_details->wh_manufacturing, 'id="wh_manufacturing" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Manufacturing_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>

				<div class="form-group">
                    <?= lang('Complete_materials_warehouse', 'Complete_materials_warehouse'); ?>
                    <?php

                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_complete', $wh, $bom_details->wh_complete, 'id="wh_complete" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Complete_materials_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>

				<div class="form-group">
                    <?= lang('Destruction_warehouse', 'Destruction_warehouse'); ?>
                    <?php

                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_destruction', $wh, $bom_details->wh_destruction, 'id="wh_destruction" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Destruction_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>
                            </div>


							<div class="col-sm-12">

                            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_textarea('description', $bom_details->description, 'class="form-control" id="description"'); ?>
					</div>

                            </div>

							<div class="col-sm-12 " style="padding-bottom:30px;">
								<button type="button" class="btn btn-primary" id="edit_bom"><?= lang('Edit_Bill_Of_Material') ?></button>

							</div>


							</div>
							</form>


							<div class="clearfix"></div>






							<ul id="myTab" class="nav nav-tabs no-print">
							<li class=""><a href="#complete-con" class="tab-grey"><?= lang('The_complete_product') ?></a></li>
    						<li class=""><a href="#materials-con" class="tab-grey"><?= lang('Raw_materials') ?></a></li>
    						<li class=""><a href="#labor-con" class="tab-grey"><?= lang('Labor_cost') ?></a></li>
    						<li class=""><a href="#expenses-con" class="tab-grey"><?= lang('Industrial_expenses') ?></a></li>
    						</ul>



							<div class="tab-content">


                                <div id="labor-con" class="tab-pane fade in">
                                    <div class="alert alert-danger" id="mError-conLC" style="display: none;">
                    			        <span id="mErrorLC"></span>
							        </div>
							        <div class="alert alert-success" id="mSuccess-conLC" style="display: none;">
                    			        <span id="mSuccessLC"></span>
							        </div>

                                    <div class="row">
                                    <input type="hidden" name="labor_no" id="labor_no" value="0" />

                                        <div class="col-sm-12">
                                            <div class="combo1" id="addLabor" >
                                                <div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button type="button" class="btn btn-primary btn-xs" id="addLaborButton"><i class="fa fa-plus"></i>
                                                    </button>
						                        </div>






                                            </div>

                                            <div id="ex-combo"></div>
            							<div class="col-sm-12">
                                            <h3 class="bold"></h3>
                                            <div class="table-responsive">
                                                <table
                                                class="table  table-striped table-condensed dfTable one-columns">
                                                    <thead>

                                                    </thead>
                                                    <tbody>
                                                    <?php
					                        			        echo '<tr><td class="col-xs-9"><strong>' . lang('total')  . '</strong></td><td><input disabled class="form-control text-center" name="tot_hours_wage" id="tot_hours_wage" type="text" ></td></tr>';
										            ?>
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>
						                <div class="col-sm-12 " id="div_addnewItemManually">
                    						<button type="button" class="btn btn-primary" id="addnewItemManuallyLC"><?= lang('save') ?></button>
					                		<button type="button" class="btn btn-danger" id="backLC" ><?= lang('Back') ?></button>
					                    </div>

                                        </div>
                                    </div>

                                </div>



                                <div id="expenses-con" class="tab-pane fade in">
                                    <div class="alert alert-danger" id="mError-conIE" style="display: none;">
                    			        <span id="mErrorIE"></span>
							        </div>
							        <div class="alert alert-success" id="mSuccess-conIE" style="display: none;">
                    			        <span id="mSuccessIE"></span>
							        </div>

                                    <div class="row">
                                    <input type="hidden" name="expenses_no" id="expenses_no" value="0" />

                                        <div class="col-sm-12">
                                            <div class="combo1" id="addExpenses" >
                                                <div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                                                    <button type="button" class="btn btn-primary btn-xs" id="addExpensesButton"><i class="fa fa-plus"></i>
                                                    </button>
						                        </div>






                                            </div>

                                            <div id="ex-combo"></div>
            							<div class="col-sm-12">
                                            <h3 class="bold"></h3>
                                            <div class="table-responsive">
                                                <table
                                                class="table  table-striped table-condensed dfTable one-columns">
                                                    <thead>

                                                    </thead>
                                                    <tbody>
                                                    <?php
					                        			        echo '<tr><td class="col-xs-9"><strong>' . lang('total')  . '</strong></td><td><input disabled class="form-control text-center" name="tot_cost" id="tot_cost" type="text" ></td></tr>';
										            ?>
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>
						                <div class="col-sm-12 " id="div_addnewItemManually">
                    						<button type="button" class="btn btn-primary" id="addnewItemManuallyIE"><?= lang('save') ?></button>
					                		<button type="button" class="btn btn-danger" id="backIE" ><?= lang('Back') ?></button>
					                    </div>

                                        </div>
                                    </div>

                                </div>

								<div id="complete-con" class="tab-pane fade in">

								<div class="alert alert-danger" id="mError-con" style="display: none;">
                    			<span id="mError"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-con" style="display: none;">
                    			<span id="mSuccess"></span>
								</div>





							<div class="row">

							<div class="col-sm-6">


							<div class="form-group all">
								<?= lang('product_name', 'name') ?>
								<?= form_input('name', $product ? $product->name : $bom_details->name, 'class="form-control gen_slug" id="name" required="required"'); ?>
							</div>
							<div class="form-group all">
                        <?= lang('second_name', 'second_name'); ?>
                        <?= form_input('second_name', $product ? $product->second_name : '', 'class="form-control tip" id="second_name"'); ?>
							</div>




							<div class="form-group all">
							<?= lang('product_price', 'price') ?>
							<?= form_input('price', ($product ? $this->sma->formatDecimal($product->price,4) : ''), 'class="form-control tip" id="price" required="required"') ?>
							</div>

							<div class="form-group all">
                            <?= lang('product_tax', 'tax_rate') ?>
                            <?php
                            $tr[''] = '';
							$tr_default=$tax_rates[0]->id;
                            foreach ($tax_rates as $tax) {
								if($tax->tax_default==1)
									$tr_default=$tax->id;

                                $tr[$tax->id] = $tax->name;
                            }
                            echo form_dropdown('tax_rate', $tr, ($product ? $product->tax_rate : $tr_default), 'class="form-control select" id="tax_rate" placeholder="' . lang('select') . ' ' . lang('product_tax') . '" style="width:100%"')
                            ?>
                        </div>
                        <div class="form-group all" id="div_tax_method" style="display:none">
                            <?= lang('tax_method', 'tax_method') ?>
                            <?php
                            $tm = ['1' => lang('exclusive'), '0' => lang('inclusive')];
                            echo form_dropdown('tax_method', $tm, ($product ? $product->tax_method : $Settings->default_tax_method), 'class="form-control select" id="tax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"'); ?>
                        </div>








							</div>
							<div class="col-sm-6">

							<div class="form-group all">
                        <?= lang('product_code', 'code') ?>
                        <div class="input-group">
                            <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                        	</div>

					<div class="form-group all" style="display:none;">
                        <?= lang('barcode_symbology', 'barcode_symbology') ?>
                        <?php
                        $bs = ['code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca' => 'UPC-A', 'upce' => 'UPC-E'];
                        echo form_dropdown('barcode_symbology', $bs, (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product ? $product->barcode_symbology : 'code128')), 'class="form-control select" id="barcode_symbology" required="required" style="width:100%;"');
                        ?>

						</div>

						<div class="form-group all">
					    <?= lang('category', 'category') ?>

                        <?php
                        $cat[''] = '';
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('category', $cat, $product ? $product->category_id : '', 'class="form-control select" id="category" placeholder="' . lang('select') . ' ' . lang('category') . '" required="required" style="width:100%"')
                        ?>
						</div>


							<div class="form-group standard_combo">
                        <?= lang('product_unit', 'unit'); ?>
							<?php
                        $pu[''] = lang('select') . ' ' . lang('unit');
                        foreach ($base_units as $bu) {
                            $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                        }
                        ?>
                        <?= form_dropdown('unit', $pu, $product ? $product->unit : '', 'class="form-control tip" id="unit" required="required" style="width:100%;"'); ?>
						</div>

						<div class="form-group">
                        <?= lang('alert_quantity', 'alert_quantity') ?>
                        <div
                            class="input-group"> <?= form_input('alert_quantity', $product ? $product->alert_quantity : '', 'class="form-control tip" id="alert_quantity"') ?>
                            <span class="input-group-addon">
                            <input type="checkbox" name="track_quantity" id="track_quantity"
                                   value="1" <?= 'checked="checked"' ?>>
                        </span>
                        </div>
						</div>

						<div class="form-group">
                        &nbsp;
                        <div class="input-group">
							<input name="hide_pos" type="checkbox" class="checkbox" id="hide_pos" value="1" <?= ($product->hide_pos) ? 'checked="checked"' : '' ?>/>
							<label for="hide_pos" class="padding05"><?= lang('hide_in_pos') ?></label>
							<input name="hide" type="checkbox" class="checkbox" id="hide" value="1" <?= ($product->hide) ? 'checked="checked"' : '' ?>/>
							<label for="hide" class="padding05"><?= lang('product_is_inactive') ?></label>
						</div>
						</div>






						</div>
						<div class="col-sm-12 " id="div_addnewItemManually">
							<button type="button" class="btn btn-primary" id="addnewItemManually"><?= lang('save') ?></button>
							<button type="button" class="btn btn-danger" id="back" ><?= lang('Back') ?></button>

						</div>
						<div class="clearfix"></div>


                        </div>

					</div>












						<div id="materials-con" class="tab-pane fade in">
							<div class="alert alert-danger" id="mError-conMR" style="display: none;">
                    			<span id="mErrorMR"></span>
							</div>
							<div class="alert alert-success" id="mSuccess-conMR" style="display: none;">
                    			<span id="mSuccessMR"></span>
							</div>
                            <div class="row">
                                <div class="col-sm-12">
						            <div class="combo1" >
						                <div class="col-md-4" >
							                <div class="form-group">
                                                <?= lang('Add_Raw_materials', 'add_item') ; ?>
                                                <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line('add_item') . '"'); ?>
							                </div>
                                        </div>
                                        <div class="col-sm-12">
						                    <div class="control-group table-group">
                                                <label class="table-label" for="combo"><?= lang('Raw_materials'); ?></label>
                                                <div class="controls table-controls">
                                                    <table id="prTable"
                                                    class="table items table-striped  table-condensed table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                                                <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th>
                                                                <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('cost'); ?></th>
                                                                <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('quantity'); ?></th>
                                                                <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('Total'); ?></th>
                                                                <!--<th class="col-md-3 col-sm-3 col-xs-3"><?= lang('price'); ?></th>-->
                                                                <th class="col-md-1 col-sm-1 col-xs-1 text-center">
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
						                <div id="ex-combo"></div>

						                <div class="col-sm-12 " id="div_addnewItemManually">
                    						<button type="button" class="btn btn-primary" id="addnewItemManuallyMR"><?= lang('save') ?></button>
					                		<button type="button" class="btn btn-danger" id="backMR" ><?= lang('Back') ?></button>
					                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="ex-combo"></div>
            							<div class="col-sm-12">
                                            <h3 class="bold"><?= lang('warehouse_quantity') ?></h3>
                                            <div class="table-responsive">
                                                <table
                                                class="table  table-striped table-condensed dfTable one-columns">
                                                    <thead>
                                                        <tr>
                                                            <th class="col-xs-6"><?= lang('warehouse_name') ?></th>
                                                            <th><?= lang('quantity'); ?></th>
                                                            <th><?= lang('Raw_materials')  ?></th>
                    										<th><?= lang('Labor_cost')  ?></th>
                    										<th><?= lang('Industrial_expenses')  ?></th>
                    										<th><?= lang('avg_cost')  ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
					                        			        $avg_cost=$this->sma->formatDecimal($warehouse->avg_cost,4) ;
            													echo '<tr><td>' . $bom_details->wh_complete_name  . '</td><td><strong>' . $this->sma->formatQuantity($bom_details->quantity) . '</strong>' . '</td><td><input disabled class="form-control text-center" name="wavg_cost" id="wavg_cost" type="text" ></td> <td><input disabled class="form-control text-center" name="main_tot_hours_wage" id="main_tot_hours_wage" type="text" ></td>  <td><input disabled class="form-control text-center" name="main_tot_cost" id="main_tot_cost" type="text" ></td>  <td><input disabled class="form-control text-center" name="main_avg_cost" id="main_avg_cost" type="text" ></td></tr>';
										            ?>
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>




                    </div>





























                <div class="col-md-5" style="display:none;">
                    <div class="form-group">
                        <?= lang('product', 'type') ?>
                        <?php
                        $opts = ['standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service')];
                        echo form_dropdown('type', $opts, 'combo', 'class="form-control" id="type" required="required"');
                        ?>
                    </div>


                </div>





            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

		$(document).on('click', '#back,#backMR', function (e) {

				    window.location.href = site.base_url + 'manufacturing/bom';

		});

		var hide_pos=0,hide=0;
		$('#hide_pos').on('ifChecked', function () {
            hide_pos=1;
        });
        $('#hide_pos').on('ifUnchecked', function () {
            hide_pos=0;
        });

		$('#hide').on('ifChecked', function () {
            hide=1;
        });
        $('#hide').on('ifUnchecked', function () {
            hide=0;
        });



		$(document).on('click', '#addnewItemManuallyMR', function (e) {

			$.each(items, function () {

			//var row_no = this.id;
			//var qty = $('#quantity_'+row_no).val();
			//items[row_no].qty=qty;
			//alert(items[item_id].qty);
			//alert(qty);
             });

			/*$.each(items, function () {

			var row_no = this.id;
			//var qty = $('#quantity_dest_'+row_no).val();
			//items_dest[row_no].qty=qty;
			//alert(items[row_no].qty);
			//alert(qty);
             });*/

			 var bom_id='<?= $bom_details->id; ?>';
			 //alert(bom_id);
			 //return false;

			 //var product_code=items[item_id].code;
			//var product_quantity=items[item_id].qty;
			//var product_unit=items[item_id].unit_id;
			//var product_avg_cost=items[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();


					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addBomRequestDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, items : items},
                success: function(data, page) {
					if(data==true){

						$('#mSuccessMR').text('<?= lang('Bill_Of_Material_updated_successfully') ?>');

						$('#mSuccess-conMR').show();

					    //window.location.href = site.base_url + 'manufacturing/bom';
				}else{
					$('#mErrorMR').text('<?= lang('failed_to_save') ?>');
					$('#mError-conMR').show();
					}

                },
            });




            return false;

		});


        $(document).on('click', '#addnewItemManuallyLC', function (e) {
            var bom_labors_arr = {};
            var labor_no=$('#labor_no').val();
		for(var i=1;i<=labor_no;i++){
            var bom_labors = {};


        var labor_desc=$('#labor_desc_' + i).val();
        var hours_no=$('#hours_no_' + i).val();
        var hours_wage=$('#hours_wage_' + i).val();
        if(hours_no==""){
            hours_no=0;
        }else{
            hours_no=Math.abs(hours_no);
        }

        if(hours_wage==""){
            hours_wage=0;
        }else{
            hours_wage=Math.abs(hours_wage);
        }

        bom_labors['labor_desc']=labor_desc;
        bom_labors['hours_no']=hours_no;
        bom_labors['hours_wage']=hours_wage;
        bom_labors_arr[(i-1)] = bom_labors;
        }
        //console.log(bom_labors_arr)



			 var bom_id='<?= $bom_details->id; ?>';


					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addBomRequestDetailLC',
                dataType : "JSON",
                data: { bom_id : bom_id, bom_labors_arr : bom_labors_arr},
                success: function(data, page) {
					if(data==true){

						$('#mSuccessLC').text('<?= lang('Bill_Of_Material_updated_successfully') ?>');

						$('#mSuccess-conLC').show();

				}else{
					$('#mErrorLC').text('<?= lang('failed_to_save') ?>');
					$('#mError-conLC').show();
					}

                },
            });




            return false;

		});





        $(document).on('click', '#addnewItemManuallyIE', function (e) {
            var bom_expensess_arr = {};
            var expenses_no=$('#expenses_no').val();
		for(var i=1;i<=expenses_no;i++){
            var bom_expensess = {};


        var expenses_acct=$('#expenses_acct_' + i).val();
        var cost=$('#cost_' + i).val();

        if(cost==""){
            cost=0;
        }else{
            cost=Math.abs(cost);
        }

        bom_expensess['expenses_acct']=expenses_acct;
        bom_expensess['cost']=cost;
        bom_expensess_arr[(i-1)] = bom_expensess;
        }
        //console.log(bom_expensess_arr)



			 var bom_id='<?= $bom_details->id; ?>';


					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addBomRequestDetailIE',
                dataType : "JSON",
                data: { bom_id : bom_id, bom_expensess_arr : bom_expensess_arr},
                success: function(data, page) {
					if(data==true){

						$('#mSuccessIE').text('<?= lang('Bill_Of_Material_updated_successfully') ?>');

						$('#mSuccess-conIE').show();

				}else{
					$('#mErrorIE').text('<?= lang('failed_to_save') ?>');
					$('#mError-conIE').show();
					}

                },
            });




            return false;

		});


		$(document).on('click', '#addnewItemManually', function (e) {


			if (!$('#name').val()) {
                $('#mError').text('<?= lang('product_name_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#code').val()) {
                $('#mError').text('<?= lang('product_code_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#category').val()) {
                $('#mError').text('<?= lang('product_category_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#unit').val()) {
                $('#mError').text('<?= lang('product_unit_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#price').val()) {
                $('#mError').text('<?= lang('product_price_is_required') ?>');
                $('#mError-con').show();
                return false;
            }


            var wh_complete='<?= $bom_details->wh_complete ?>';
            var id='<?= $bom_details->id ?>';
            var product_id='<?= $bom_details->product_id ?>';
			var url;
			var success_msg;
			if(!product_id){
				url="manufacturing/addByAjaxFrom_Manufacturing";
				success_msg='<?= lang('product_added') ?>';
			}else{
				url="manufacturing/updateByAjaxFrom_Manufacturing";
				success_msg='<?= lang('product_updated') ?>';
			}
			//alert(success_msg)

			var msg, row = null, product = {
                type: 'manufacturing',
                type2: 'complete',
                warehouse: wh_complete,
                quantity: '',
                code: $('#code').val(),
                name: $('#name').val(),
                tax_rate: $('#tax_rate').val(),
                tax_method: $('#tax_method').val(),
                category_id: $('#category').val(),
                unit: $('#unit').val(),
                sale_unit: $('#unit').val(),
                cost: '',
                price: $('#price').val(),
                alert_quantity: $('#alert_quantity').val(),
                hide_pos: hide_pos,
                hide: hide,
				second_name: $('#second_name').val()


            };



            $.ajax({
                type: "get", async: false,
                url: site.base_url +""+url,
                data: {token: "<?= $csrf; ?>", product: product, id: id, product_id:product_id},
                dataType: "json",
                success: function (data) {

                    if (data.msg == 'success') {
						//$("#add_item").val(data.result.code);
						//$('input#add_item').autocomplete("search");

						$('#mSuccess').text(success_msg);

						$('#mSuccess-con').show();
						//$("#div_addnewItemManually").css("display", "none");


                    } else {
                        msg = data.msg;
						$('#mError').text(msg);
						$('#mError-con').show();

                    }
                }
            });
            return false;

        });

		$(document).on('click', '#edit_bom', function (e) {

			if (!$('#name_bom').val()) {
                $('#mErrorBOM').text('<?= lang('Bill_Of_Material_name_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#code_bom').val()) {
                $('#mErrorBOM').text('<?= lang('Bill_Of_Material_code_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#quantity').val()) {
                $('#mErrorBOM').text('<?= lang('Bill_Of_Material_quantity_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }


            var name=$('#name_bom').val();
            var code=$('#code_bom').val();
            var quantity=$('#quantity').val();
            var image=$('#image').val();
            var wh_raw=$('#wh_raw').val();
            var wh_manufacturing=$('#wh_manufacturing').val();
            var wh_complete=$('#wh_complete').val();
            var wh_destruction=$('#wh_destruction').val();
            var description=$('#description').val();
            var id='<?= $bom_details->id ?>';


			var mts = $('#image').get(0).files[0];

			var formData = new FormData();

				var data = $('#bomForm').serializeArray();
				var obj = {};
				for (var i = 0; i < data.length; i++) {
    					//obj[data[i].name] = data[i].value;
    					formData.append([data[i].name], data[i].value);
				}



            var url;
			var success_msg;
				url="manufacturing/updateBomByAjax";
				success_msg='<?= lang('Bill_Of_Material_updated_successfully') ?>';
			//alert(success_msg)

			var msg, row = null, bom = {
                name: name,
                code: code,
                quantity: quantity,
                wh_raw: wh_raw,
                wh_manufacturing: wh_manufacturing,
                wh_complete: wh_complete,
                wh_destruction: wh_destruction,
                description: description


            };

				formData.append('userfile', mts);
				formData.append('id', id);
				formData.append('bom', bom);
				formData.append('token', "<?= $csrf; ?>");


            $.ajax({
                type: 'POST',
					data: formData,
					contentType: false,
					cache: false,
					processData:false,
					url: site.base_url +""+url,
                success: function (data) {

                    if (data.msg == 'success') {
						//console.log(data)
						//$("#add_item").val(data.result.code);
						//$('input#add_item').autocomplete("search");

						$('#mSuccessBOM').text(success_msg);

						$('#mSuccess-conBOM').show();
						//$("#div_addnewItemManually").css("display", "none");


                    } else {
                        msg = data.msg;
						$('#mErrorBOM').text(msg);
						$('#mError-conBOM').show();

                    }
                }
            });
            return false;

        });


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
                        $('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
				        $('#tax_method').select2('readonly', false);



                    } else {
                    	$('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
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
        if ($bom_items) {
            foreach ($bom_items as $item) {
                //echo 'ietms['.$item->id.'] = '.$item.';';
                if ($item->code) {
                    echo 'add_product_item(' . json_encode($item) . ',0);';
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
                $('#cost').attr('disabled', true);
                //$('#track_quantity').iCheck('uncheck');
            } else {
                $('.standard').slideDown();
                //$('#track_quantity').iCheck('check');
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
			} else {
                $('.standard_combo').slideUp();
            }
        });

        var t = $('#type').val();
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('disabled', true);
            //$('#track_quantity').iCheck('uncheck');
        } else {
            $('.standard').slideDown();
           // $('#track_quantity').iCheck('check');
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

            source: '<?= admin_url('manufacturing/suggestions?warehouse_id='.$bom_details->wh_raw); ?>' ,
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
                    var row = add_product_item(ui.item,1);
					if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });



		function add_product_item(item,flage) {
			if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
				var bom_id='<?= $bom_details->id; ?>';
				//updateBomDetail(bom_id,item_id,items[item_id].qty);
                //items[item_id].avg_cost = (parseFloat(items[item_id].avg_cost) * parseFloat(items[item_id].qty)).toFixed(2);
            } else {
                items[item_id] = item;
				if(flage==1){
			var bom_id='<?= $bom_details->id; ?>';
			var product_id=items[item_id].id;
			var product_code=items[item_id].code;
			var product_quantity=items[item_id].qty;
			var product_unit=items[item_id].unit_id;
			var product_avg_cost=items[item_id].avg_cost;
			//alert(product_avg_cost)
            //var id_product=$('#id_product1').val();
            //var quantity=$('#quantity1').val();



			/*		$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/addBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id, product_code : product_code, product_quantity : product_quantity, product_unit : product_unit, product_avg_cost : product_avg_cost},
                success: function(data, page) {


                },
            });
			*/

				}

			}
            var pp = 0;
            var total = 0;
            $("#prTable tbody").empty();
            $.each(items, function () {
				var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" class="rid" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span class="pr_link" id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                tr_html += '<td><input name="combo_item_unit[]" type="hidden" value="' + this.unit_id + '"><span id="unit_' + row_no + '">' + this.unit_name + '</span></td>';
                tr_html += '<td><input disabled class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatDecimal(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input disabled class="form-control text-center trprice" name="total_combo_item_price[]" type="text" value="' + formatDecimal(this.qty * this.avg_cost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
				tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable");
                pp += formatDecimal(parseFloat(this.avg_cost)*parseFloat(this.qty),4);
            });
            $('.item_' + item_id).addClass('warning');
			var tfoot ='<tr id="tfoot" class="tfoot active"><th colspan="4"><?= lang('total') ?></th><th class="text-center total"><input disabled class="form-control text-center" name="total" id="total" type="text" value="' + formatDecimal(pp,4) + '"></th><tr>';
				$("#prTable tfoot").html(tfoot);


                $('#total').val(formatDecimal(pp,4));
                $('#wavg_cost').val(formatDecimal(pp,4));


                var wavg_cost=$('#wavg_cost').val();
        var main_tot_hours_wage=$('#main_tot_hours_wage').val();
        var main_tot_cost=$('#main_tot_cost').val();
        if(wavg_cost==""){
            wavg_cost=0;
        }
        if(main_tot_hours_wage==""){
            main_tot_hours_wage=0;
        }
        if(main_tot_cost==""){
            main_tot_cost=0;
        }

        $('#main_avg_cost').val(formatDecimal((parseFloat(wavg_cost) + parseFloat(main_tot_hours_wage)+ parseFloat(main_tot_cost))/<?= $bom_details->quantity ?>,4));


            //$('#wavg_cost').val(formatDecimal(pp/<?= $bom_details->quantity ?>,4));
            return true;
        }






        function updateBomDetail(bom_id,product_id,product_quantity){
			$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/updateBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id, product_quantity : product_quantity},
                success: function(data, page) {


                },
            });
		}
        function deleteBomDetail(bom_id,product_id){
			$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'manufacturing/deleteBomDetail',
                dataType : "JSON",
                data: { bom_id : bom_id, product_id : product_id},
                success: function(data, page) {


                },
            });
		}
        function calculate_price() {
            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()),4);
            });
                        $('#total').val(formatDecimal(pp,4));
                        $('#wavg_cost').val(formatDecimal(pp,4));

                        var wavg_cost=$('#wavg_cost').val();
        var main_tot_hours_wage=$('#main_tot_hours_wage').val();
        var main_tot_cost=$('#main_tot_cost').val();
        if(wavg_cost==""){
            wavg_cost=0;
        }
        if(main_tot_hours_wage==""){
            main_tot_hours_wage=0;
        }
        if(main_tot_cost==""){
            main_tot_cost=0;
        }

        $('#main_avg_cost').val(formatDecimal((parseFloat(wavg_cost) + parseFloat(main_tot_hours_wage)+ parseFloat(main_tot_cost))/<?= $bom_details->quantity ?>,4));

                        //$('#wavg_cost').val(formatDecimal(pp/<?= $bom_details->quantity ?>,4));

            return true;
        }

        $(document).on('change', '.rquantity, .rprice', function () {
			var row = $(this).closest('tr');
			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');

			row.find('.trprice').val(formatDecimal(parseFloat(row.find('.rprice').val())*parseFloat(row.find('.rquantity').val()),4));
			var bom_id='<?= $bom_details->id; ?>';
			items[item_id].qty=formatDecimal(row.find('.rquantity').val(),4);
				//updateBomDetail(bom_id,item_id,row.find('.rquantity').val());

            calculate_price();
        });

        $(document).on('click', '.del', function () {


			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');
			var bom_id='<?= $bom_details->id; ?>';
				//deleteBomDetail(bom_id,item_id);

            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();




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

		$('#pwarehouse').val('<?= $warehouses[0]->id ?>').trigger('change.select2');

			$("#pwarehouse > option").each(function(){

				$("#prTable"+ this.value).hide();
                $("#title"+ this.value).hide();


			});


    });

    <?php if ($product) {
            ?>
    $(document).ready(function () {

        var t = "<?=$product->type?>";
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('required', 'required');
            //$('#track_quantity').iCheck('uncheck');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
        } else {
            $('.standard').slideDown();
            //$('#track_quantity').iCheck('check');
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


			$("#div_tax_method").css("display", "block");


        var addLabor  = 1;
		$('button#addLaborButton').click(function(){

            //if (addLabor <= 5) {

			var divOption    = '<div class="row" id="laborRow'+addLabor+'">';
			var selectOption = '<div class="col-4" style="padding-left:30px;padding-right:25px;"><input type="text" name="labor_desc_' + addLabor + '", class="form-control" id="labor_desc_' + addLabor + '" placeholder="<?= lang('Declaration') ?>" style="width:100%;display: block !important;" /></div>';
			var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control" placeholder="<?= lang('NO_of_working_hours') ?>" name="hours_no_' + addLabor + '" id="hours_no_' + addLabor + '" type="number" step="1" onchange="labor_calc(' + addLabor + ')" required /></div>');
			var inputOption1   = $('<div class="col-2" dir="ltr"><input class="form-control" placeholder="<?= lang('Hourly_wage') ?>" name="hours_wage_' + addLabor + '" id="hours_wage_' + addLabor + '" type="number" step=".01" onchange="labor_calc(' + addLabor + ')" required /></div>');
			var inputOption2   = $('<div class="col-2" dir="ltr"><input class="form-control" placeholder="<?= lang('Total') ?>" name="thours_wage_' + addLabor + '"  id="thours_wage_' + addLabor + '" type="number" readonly    required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removelabor[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
                addLabor -= 1;
			});

			$('#addLabor').append(divOption);
			$('#laborRow'+addLabor).append(selectOption);
			$('#laborRow'+addLabor).append(inputOption);
			$('#laborRow'+addLabor).append(inputOption1);
			$('#laborRow'+addLabor).append(inputOption2);
			$('#laborRow'+addLabor).append(buttonOption);
            var sup = $('#labor_desc_' + addLabor);
            //labor(sup);
            $('#labor_no').val(addLabor);
			addLabor += 1;
        //} else {
        //        bootbox.alert('<?= lang('max_reached') ?>');
        //        return false;
        //    }

		});



        var addExpenses  = 1;
		$('button#addExpensesButton').click(function(){

            //if (addExpenses <= 5) {

			var divOption    = '<div class="row" id="expensesRow'+addExpenses+'">';
			var selectOption = '<div class="col-6" style="padding-left:30px;padding-right:25px;"><select class="form-control"  name="expenses_acct_' + addExpenses + '" id="expenses_acct_' + addExpenses + '" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($expenses_accounts as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['accountName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
            var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control" placeholder="<?= lang('cost') ?>" name="cost_' + addExpenses + '" id="cost_' + addExpenses + '" type="number" step=".01" onchange="expenses_calc(' + addExpenses + ')" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeexpenses[]" class="btn btn-danger btn-block"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
                addExpenses -= 1;
			});

			$('#addExpenses').append(divOption);
			$('#expensesRow'+addExpenses).append(selectOption);
			$('#expensesRow'+addExpenses).append(inputOption);
			$('#expensesRow'+addExpenses).append(buttonOption);
            var sup = $('#expenses_acct_' + addExpenses);
            //expenses(sup);
            $('#expenses_no').val(addExpenses);
			addExpenses += 1;
        //} else {
        //        bootbox.alert('<?= lang('max_reached') ?>');
        //        return false;
        //    }

		});



        <?php
    if ($bom_labors) {

        ?>
        <?php
        $i=1;
        $tot_hours_wage=0;
        foreach($bom_labors as $product){


                ?>
                var i='<?= $i; ?>';
        $('#addLaborButton').click();
        $('#labor_desc_'+i+'').val("<?= $product->labor_desc; ?>");
        $('#hours_no_'+i+'').val("<?= $this->sma->formatDecimal($product->hours_no); ?>");
        $('#hours_wage_'+i+'').val("<?= $this->sma->formatDecimal($product->hours_wage); ?>");
        $('#thours_wage_'+i+'').val("<?= $this->sma->formatDecimal($product->hours_no*$product->hours_wage); ?>");
        <?php
        $tot_hours_wage=$tot_hours_wage + ($product->hours_no*$product->hours_wage);

        $i++;


            }
            //labor_calc(1);
            ?>
            $('#tot_hours_wage').val("<?= $this->sma->formatDecimal($tot_hours_wage,4); ?>");
            $('#main_tot_hours_wage').val("<?= $this->sma->formatDecimal($tot_hours_wage,4); ?>");

            var wavg_cost=$('#wavg_cost').val();
        var main_tot_hours_wage=$('#main_tot_hours_wage').val();
        var main_tot_cost=$('#main_tot_cost').val();
        if(wavg_cost==""){
            wavg_cost=0;
        }
        if(main_tot_hours_wage==""){
            main_tot_hours_wage=0;
        }
        if(main_tot_cost==""){
            main_tot_cost=0;
        }

        $('#main_avg_cost').val(formatDecimal((parseFloat(wavg_cost) + parseFloat(main_tot_hours_wage)+ parseFloat(main_tot_cost))/<?= $bom_details->quantity ?>,4));

            <?php
            }
        ?>



    <?php
    if ($bom_expenses) {

        ?>
        <?php
        $i=1;
        $tot_cost=0;
        foreach($bom_expenses as $product){


                ?>
                var i='<?= $i; ?>';
        $('#addExpensesButton').click();
        $('#expenses_acct_'+i+'').val("<?= $product->expenses_acct; ?>");
        $('#cost_'+i+'').val("<?= $this->sma->formatDecimal($product->cost); ?>");
    <?php
        $tot_cost=$tot_cost + ($product->cost);

        $i++;


            }
            //expenses_calc(1);
            ?>
            $('#tot_cost').val("<?= $this->sma->formatDecimal($tot_cost,4); ?>");
            $('#main_tot_cost').val("<?= $this->sma->formatDecimal($tot_cost,4); ?>");
            <?php
            }
        ?>
        //
        //alert($('#main_tot_cost').val())
        var wavg_cost=$('#wavg_cost').val();
        var main_tot_hours_wage=$('#main_tot_hours_wage').val();
        var main_tot_cost=$('#main_tot_cost').val();
        if(wavg_cost==""){
            wavg_cost=0;
        }
        if(main_tot_hours_wage==""){
            main_tot_hours_wage=0;
        }
        if(main_tot_cost==""){
            main_tot_cost=0;
        }

        $('#main_avg_cost').val(formatDecimal((parseFloat(wavg_cost) + parseFloat(main_tot_hours_wage)+ parseFloat(main_tot_cost))/<?= $bom_details->quantity ?>,4));



    });

    function labor_calc(labor_pointer){
        var hours_no=$('#hours_no_' + labor_pointer).val();
        var hours_wage=$('#hours_wage_' + labor_pointer).val();

        if(hours_no==""){
            hours_no=0;
        }else{
            hours_no=Math.abs(hours_no);
        }

        if(hours_wage==""){
            hours_wage=0;
        }else{
            hours_wage=Math.abs(hours_wage);
        }

        $('#thours_wage_' + labor_pointer).val(formatDecimal(hours_no * hours_wage,4));

        var labor_no=$('#labor_no').val();
        var tot_hours_wage=0;
        for(var i=1;i<=labor_no;i++){


        var hours_no2=$('#hours_no_' + i).val();
        var hours_wage2=$('#hours_wage_' + i).val();

        if(hours_no2==""){
            hours_no2=0;
        }else{
            hours_no2=Math.abs(hours_no2);
        }

        if(hours_wage2==""){
            hours_wage2=0;
        }else{
            hours_wage2=Math.abs(hours_wage2);
        }


        tot_hours_wage=tot_hours_wage + (hours_no2 * hours_wage2);

        }

        $('#tot_hours_wage').val(formatDecimal(tot_hours_wage,4));
        $('#main_tot_hours_wage').val(formatDecimal(tot_hours_wage,4));

        var wavg_cost=$('#wavg_cost').val();
        var main_tot_hours_wage=$('#main_tot_hours_wage').val();
        var main_tot_cost=$('#main_tot_cost').val();
        if(wavg_cost==""){
            wavg_cost=0;
        }
        if(main_tot_hours_wage==""){
            main_tot_hours_wage=0;
        }
        if(main_tot_cost==""){
            main_tot_cost=0;
        }

        $('#main_avg_cost').val(formatDecimal((parseFloat(wavg_cost) + parseFloat(main_tot_hours_wage)+ parseFloat(main_tot_cost))/<?= $bom_details->quantity ?>,4));


        }


        function expenses_calc(expenses_pointer){
        var cost=$('#cost_' + expenses_pointer).val();


        if(cost==""){
            cost=0;
        }else{
            cost=Math.abs(cost);
        }


        var expenses_no=$('#expenses_no').val();
        var tot_cost=0;
        for(var i=1;i<=expenses_no;i++){


        var cost2=$('#cost_' + i).val();



        if(cost2==""){
            cost2=0;
        }else{
            cost2=Math.abs(cost2);
        }


        tot_cost=tot_cost + (cost2);

        }

        $('#tot_cost').val(formatDecimal(tot_cost,4));
        $('#main_tot_cost').val(formatDecimal(tot_cost,4));

        var wavg_cost=$('#wavg_cost').val();
        var main_tot_hours_wage=$('#main_tot_hours_wage').val();
        var main_tot_cost=$('#main_tot_cost').val();
        if(wavg_cost==""){
            wavg_cost=0;
        }
        if(main_tot_hours_wage==""){
            main_tot_hours_wage=0;
        }
        if(main_tot_cost==""){
            main_tot_cost=0;
        }

        $('#main_avg_cost').val(formatDecimal((parseFloat(wavg_cost) + parseFloat(main_tot_hours_wage)+ parseFloat(main_tot_cost))/<?= $bom_details->quantity ?>,4));


        }
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
