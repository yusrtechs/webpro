<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
<script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>
<style>
.pr_link{
    cursor: pointer;
}
/*
.redactor_box{
    display:none;
}*/
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

        $('#posupplier').select2({
            minimumInputLength: 1,
        ajax: {
            url: site.base_url + 'suppliers/suggestions',
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {

                return {
                        term: term,
                        limit: 10,

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

			$(document).on('click', '.pr_link', function(e) {
        var row = $(this).closest('tr');
        var itemid = row.find('.rid').val();
        $('#myModal').modal({ remote: site.base_url + 'products/modal_view/' + itemid });
        $('#myModal').modal('show');
    });


		$('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'products');
        });
        $("#sub_class_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", minimumResultsForSearch: 7, data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#parent_class_id').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('fixedassets/getSubClassesByParentId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            //scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#sub_class_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata
                            });
                        } else {
                            $("#sub_class_id").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
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
                $("#sub_class_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
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
<div class="spark-page-header" style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div class="spark-branches-header-start">
        <div class="spark-branches-header-icon" style="background: var(--spark-gradient-primary, linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)); box-shadow: 0 4px 12px var(--spark-focus-ring, rgba(99, 102, 241, 0.25));">
            <i class="fa fa-edit"></i>
        </div>
        <div class="spark-branches-header-info">
            <h1 class="spark-branches-title"><?= lang('Edit_fixed_asset') . ' &mdash; ' . $fixed_asset_details->name; ?></h1>
            <p class="spark-branches-subtitle"><?= lang('code'); ?>: <span class="spark-branch-code-badge"><?= $fixed_asset_details->code; ?></span></p>
        </div>
    </div>
    <div class="spark-branches-header-actions">
        <a href="<?= admin_url('fixedassets'); ?>" class="spark-btn-secondary-action">
            <i class="fa fa-arrow-right"></i>
            <span><?= lang('Fixed_assets_list'); ?></span>
        </a>
    </div>
</div>

<div class="box spark-branches-table-card" style="padding: 24px; margin-bottom: 24px; border-radius: 16px;">
    <div class="box-content" style="padding: 0;">
        <div class="alert alert-danger" id="mError-conBOM" style="display: none;">
            <span id="mErrorBOM"></span>
        </div>

        <div class="alert alert-success" id="mSuccess-conBOM" style="display: none;">
            <span id="mSuccessBOM"></span>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <form id="fixedAssetForm" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('code', 'code'); ?>
                                <?= form_input('code', $fixed_asset_details->code, 'class="form-control" id="code_fixed_asset" required="required"'); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('name', 'name'); ?>
                                <?= form_input('name', $fixed_asset_details->name, 'class="form-control" id="name_fixed_asset" required="required"'); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('unit', 'unit_id'); ?>
                                <?php
                                $pu[''] = lang('select') . ' ' . lang('unit');
                                foreach ($base_units as $bu) {
                                    $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                                }
                                ?>
                                <?= form_dropdown('unit_id', $pu, $fixed_asset_details ? $fixed_asset_details->unit_id : '', 'class="form-control tip" id="unit_id" required="required" style="width:100%;"'); ?>
                            </div>

                            <div class="form-group all">
                                <?= lang('Measurement', 'Measurement'); ?>
                                <?= form_input('measurement', $fixed_asset_details->measurement, 'class="form-control tip" id="measurement"'); ?>
                            </div>

                            <div class="form-group all">
                                <?= lang('The_manufacture_company', 'The_manufacture_company'); ?>
                                <?= form_input('man_company', $fixed_asset_details->man_company, 'class="form-control tip" id="man_company"'); ?>
                            </div>

                            <div class="form-group all">
                                <?= lang('Country_of_origin', 'Country_of_origin'); ?>
                                <?= form_input('country_origin', $fixed_asset_details->country_origin, 'class="form-control tip" id="country_origin"'); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('image', 'image') ?>
                                <?php if (!empty($fixed_asset_details->image) && $fixed_asset_details->image != 'no_image.jpg') { ?>
                                    <div style="margin-bottom: 8px;">
                                        <img src="<?= base_url('assets/uploads/thumbs/' . $fixed_asset_details->image) ?>" alt="" style="max-height: 50px; border-radius: 8px; border: 1px solid var(--spark-border-default, #e2e8f0); padding: 2px;" />
                                    </div>
                                <?php } ?>
                                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= lang('Fixed_assets_group', 'Fixed_assets_group'); ?>
                                <?php
                                foreach ($groups as $group) {
                                    $wh[$group->id] = $group->name;
                                }
                                echo form_dropdown('group_id', $wh, (isset($_POST['group_id']) ? $_POST['group_id'] : $fixed_asset_details->group_id), 'id="group_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Fixed_assets_group') . '" required="required" style="width:100%;" '); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('Location', 'Location'); ?>
                                <?= form_input('location', $fixed_asset_details->location, 'class="form-control " id="location" '); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('Color', 'Color'); ?>
                                <?= form_input('color', $fixed_asset_details->color, 'class="form-control " id="color" '); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('Quality', 'Quality'); ?>
                                <?= form_input('quality', $fixed_asset_details->quality, 'class="form-control " id="quality" '); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('Style', 'Style'); ?>
                                <?= form_input('style', $fixed_asset_details->style, 'class="form-control " id="style" '); ?>
                            </div>

                            <div class="form-group">
                                <?= lang('Status', 'Status'); ?>
                                <?php
                                $status_options = [1 => lang('new'), 2 => lang('Old')];
                                echo form_dropdown('status', $status_options, (isset($_POST['status']) ? $_POST['status'] : $fixed_asset_details->status), 'id="status" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Status') . '" required="required" style="width:100%;" '); ?>
                            </div>

                            <div class="form-group all">
                                <?= lang('status', 'active') ?>
                                <?php
                                $tm = ['0' => lang('inactive'), '1' => lang('active')];
                                echo form_dropdown('active', $tm, $fixed_asset_details->active, 'class="form-control select" id="active" placeholder="' . lang('select') . ' ' . lang('status') . '" required="required" style="width:100%"'); ?>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group all">
                                <?= lang('Specifications', 'Specifications'); ?>
                                <?= form_textarea('specifications', $fixed_asset_details->specifications, 'class="form-control content" id="specifications"'); ?>
                            </div>
                        </div>

                        <div class="col-sm-12" style="padding-top: 15px; padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                            <button type="button" class="spark-btn-primary-action" id="edit_fixed_asset" style="padding: 10px 24px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fa fa-save"></i>
                                <span><?= lang('Edit_fixed_asset') ?></span>
                            </button>
                            <a href="<?= admin_url('fixedassets'); ?>" class="spark-btn-secondary-action" style="padding: 10px 20px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fa fa-arrow-right"></i>
                                <span><?= lang('Back') ?></span>
                            </a>
                        </div>
                    </div>
                </form>

                <div class="clearfix"></div>

                <ul id="myTab" class="nav nav-tabs no-print spark-nav-tabs" style="margin-top: 20px; margin-bottom: 20px; border-bottom: 2px solid var(--spark-border-default, #e2e8f0); display: flex; gap: 8px;">
                    <li class="active"><a href="#information-con" data-toggle="tab" class="tab-grey"><i class="fa fa-info-circle"></i> <?= lang('Fixed_asset_information') ?></a></li>
                    <li class=""><a href="#purchase-con" data-toggle="tab" class="tab-grey"><i class="fa fa-shopping-cart"></i> <?= lang('Fixed_asset_purchase') ?></a></li>
                    <li class=""><a href="#depreciation-con" data-toggle="tab" class="tab-grey"><i class="fa fa-hourglass-half"></i> <?= lang('Fixed_asset_depreciation') ?></a></li>
                </ul>

                <div class="tab-content">
                    <div id="information-con" class="tab-pane fade in active">

								<div class="alert alert-danger" id="mError-con" style="display: none;">
                    			<span id="mError"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-con" style="display: none;">
                    			<span id="mSuccess"></span>
								</div>





							<div class="row">

							<div class="col-sm-6">


							<div class="form-group all">
								<?= lang('Branch_name', 'Branch_name') ?>
								<?= form_input('branch_name', $fixed_asset_details->branch_name, 'class="form-control " id="branch_name"  '); ?>
							</div>


							<div class="form-group all">
                            <?= lang('Depreciation_allowance_account', 'accountID') ?>

                            <?php
                        $acc[''] = '';
                        foreach ($accounts as $account) {
                            if ($this->Settings->user_language == 'arabic' || $account->accountNameEn==''){
                            $acc[$account->id] = $account->accountID." - ".$account->accountName;
                            }else{
                                $acc[$account->id] = $account->accountID." - ".$account->accountNameEn;

                            }
                        }
                        echo form_dropdown('accountID', $acc,$fixed_asset_details->accountID, 'class="form-control select" id="accountID" placeholder="' . lang('select') . ' ' . lang('Depreciation_allowance_account') . '" required="required" style="width:100%"')
                        ?>


                        </div>


                        <div class="form-group all">
                            <?= lang('Depreciation_expenses_account', 'accountIDEX') ?>

                            <?php
                        $acc[''] = '';
                        foreach ($accounts as $account) {
                            if ($this->Settings->user_language == 'arabic' || $account->accountNameEn==''){
                            $acc[$account->id] = $account->accountID." - ".$account->accountName;
                            }else{
                                $acc[$account->id] = $account->accountID." - ".$account->accountNameEn;

                            }
                        }
                        echo form_dropdown('accountIDEX', $acc,$fixed_asset_details->accountIDEX, 'class="form-control select" id="accountIDEX" placeholder="' . lang('select') . ' ' . lang('Depreciation_allowance_account') . '" required="required" style="width:100%"')
                        ?>


                        </div>












							</div>
							<div class="col-sm-6">



						<div class="form-group all">
					    <?= lang('Parent_class', 'parent_class_id') ?>

                        <?php
                        $cat[''] = '';
                        foreach ($parentClasses as $parentClass) {
                            $cat[$parentClass->id] = $parentClass->name;
                        }
                        echo form_dropdown('parent_class_id', $cat,$fixed_asset_details->parent_class_id, 'class="form-control select" id="parent_class_id" placeholder="' . lang('select') . ' ' . lang('Parent_class') . '" required="required" style="width:100%"')
                        ?>
						</div>

                        <div class="form-group all">
                        <?= lang('Sub_class', 'sub_class_id') ?>
                        <div class="controls" id="subcat_data"> <?php
                            echo form_input('sub_class_id',$fixed_asset_details->sub_class_id , 'class="form-control" id="sub_class_id" required="required"');
                            ?>
                        </div>
                        </div>










						</div>
						<div class="col-sm-12 spark-form-actions" id="div_saveinformation" style="margin-top: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
							<button type="button" class="spark-btn-primary-action" id="saveinformation" style="padding: 9px 22px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
								<i class="fa fa-check"></i>
								<span><?= lang('save') ?></span>
							</button>
							<button type="button" class="spark-btn-secondary-action" id="back" style="padding: 9px 20px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
								<i class="fa fa-arrow-right"></i>
								<span><?= lang('Back') ?></span>
							</button>
						</div>
						<div class="clearfix"></div>


                        </div>

								</div>




                                <div id="depreciation-con" class="tab-pane fade in">

								<div class="alert alert-danger" id="mError-conDE" style="display: none;">
                    			<span id="mErrorDE"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-conDE" style="display: none;">
                    			<span id="mSuccessDE"></span>
								</div>





							<div class="row">

							<div class="col-sm-4">

                        <?php
                            if($fixed_asset_details->purchase_date)
                            {
                        ?>
							<div class="form-group all">

                        <?php
                            }else{
                        ?>
                        <div class="form-group all" style="display:none;">
                        <?php
                            }
                        ?>
                        <?= lang('The_date_of_purchase', 'purchase_date2'); ?>
                        <?= form_input('purchase_date2',$fixed_asset_details->purchase_date ? $this->sma->hrld($fixed_asset_details->purchase_date) : '', 'readonly class="form-control " id="purchase_date2" required="required"'); ?>
							</div>



                            <div class="form-group all">
                        <?= lang('cost', 'cost'); ?>
                        <?= form_input('cost',$fixed_asset_details->cost!=0 ? $this->sma->formatDecimal($fixed_asset_details->cost,4) : '', $fixed_asset_details->cost!=0 ? 'readonly class="form-control tip" id="cost" required="required"' : ' class="form-control tip" id="cost" required="required"'); ?>
							</div>

                            <div class="form-group all">
                        <?= lang('quantity', 'quantity'); ?>
                        <?= form_input('quantity',$fixed_asset_details->quantity!=0 ? $this->sma->formatQuantity($fixed_asset_details->quantity) : '', $fixed_asset_details->quantity!=0 ? 'readonly class="form-control tip" id="quantity" required="required"' : ' class="form-control tip" id="quantity" required="required"' ); ?>
							</div>




							<div class="form-group all">
							<?= lang('value', 'value') ?>
                            <?php
                            $value=$fixed_asset_details->value;
                            ?>
							<?= form_input('value', $fixed_asset_details->value!=0 ? $this->sma->formatDecimal($fixed_asset_details->value,4) : '', 'readonly class="form-control tip" id="value" required="required" ') ?>
							</div>











							</div>
							<div class="col-sm-8">




                        <div class="col-sm-6">
                        <div class="form-group all">
                        <?= lang('Start_date_of_operation', 'start_date'); ?>
                        <?= form_input('start_date',$fixed_asset_details->start_date ? $this->sma->hrsd($fixed_asset_details->start_date) : date('d/m/Y'), 'class="form-control date" id="start_date" required="required"'); ?>
						</div>
                        </div>



                            <div class="col-sm-6">
                            <div class="form-group all">
                            <?= lang('Useful_life', 'useful_life'); ?> ( <?= lang('Per_year'); ?> )
                            <input class="form-control" placeholder="<?= lang('Useful_life') ?>" name="useful_life" id="useful_life" type="number" min="1" step="1" value="<?= $fixed_asset_details->useful_life!=0 ? $fixed_asset_details->useful_life : ''; ?>" required />
                            </div>
                            </div>


                            <div class="col-sm-6">
                            <div class="form-group all">
                            <?= lang('Scrap_value', 'scrap_value'); ?>
                        <?= form_input('scrap_value',$fixed_asset_details->useful_life!=0 ? $this->sma->formatDecimal($fixed_asset_details->scrap_value) : '', 'class="form-control" id="scrap_value" required="required"'); ?>
							</div>
                            </div>

                            <div class="col-sm-6">
                            <div class="form-group all">
                            <?= lang('Depreciation_percentage', 'depreciation_ratio'); ?> ( % )
                        <?= form_input('depreciation_ratio',$fixed_asset_details->depreciation_ratio!=0 ? $this->sma->formatDecimal($fixed_asset_details->depreciation_ratio,2) : '', 'readonly class="form-control" id="depreciation_ratio" required="required"'); ?>
							</div>
                            </div>

                            <div class="col-sm-6">
                            <div class="form-group all">
                            <?= lang('Depreciation_of_fixed_assets_per_month', 'depreciation_amount'); ?>
                            <?= form_input('depreciation_amount',$fixed_asset_details->depreciation_amount!=0 ? $this->sma->formatDecimal($fixed_asset_details->depreciation_amount) : '', 'readonly class="form-control" id="depreciation_amount" required="required"'); ?>
							</div>
                            </div>

                            <div class="col-sm-6">
                            <div class="form-group all">
                            <?= lang('Depreciation_of_fixed_assets_per_year', 'depreciation_amount_per_year'); ?>
                            <?= form_input('depreciation_amount_per_year',$fixed_asset_details->depreciation_amount_per_year!=0 ? $this->sma->formatDecimal(($fixed_asset_details->depreciation_amount_per_year  )) : '', 'readonly class="form-control" id="depreciation_amount_per_year" required="required"'); ?>
							</div>
                            </div>








						</div>
						<div class="col-sm-12 spark-form-actions" id="div_addDepreciation" style="margin-top: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
							<button type="button" class="spark-btn-primary-action" id="addDepreciation" style="padding: 9px 22px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
								<i class="fa fa-check"></i>
								<span><?= lang('save') ?></span>
							</button>
							<button type="button" class="spark-btn-secondary-action" id="back" style="padding: 9px 20px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
								<i class="fa fa-arrow-right"></i>
								<span><?= lang('Back') ?></span>
							</button>
						</div>
						<div class="clearfix"></div>


                        </div>

								</div>












							<div id="purchase-con" class="tab-pane fade in">


							<div class="alert alert-danger" id="mError-conPO" style="display: none;">
                    			<span id="mErrorPO"></span>
								</div>

								<div class="alert alert-success" id="mSuccess-conPO" style="display: none;">
                    			<span id="mSuccessPO"></span>
								</div>


                                <div class="row">

                                    <div class="col-sm-12">


						<div class="combo1" >
                        <div class="col-sm-12">
						<div class="col-md-4" >

                        <div class="form-group all">
                        <?= lang('The_date_of_purchase', 'purchase_date'); ?>
                        <?= form_input('purchase_date',$fixed_asset_details->purchase_date ? $this->sma->hrld($fixed_asset_details->purchase_date) : '', 'readonly class="form-control datetime" id="purchase_date" required="required"'); ?>
							</div>


                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('supplier', 'supplier_id'); ?>
                                <?php echo form_input('supplier_id', $fixed_asset_details->supplier_id, 'class="form-control" id="posupplier" required="required"'); ?>
						    </div>
                        </div>
                        <div class="col-md-4"  id="div_invoice_tax">
                                        <div class="form-group">
                                            <?= lang('order_tax', 'potax2') ?>
                                            <?php
                                            $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('tax_id', $tr, $fixed_asset_details->tax_id ? $fixed_asset_details->tax_id : '1', 'id="potax2" class="form-control input-tip select" style="width:100%;"'); ?>
                                        </div>
                        </div>
                        </div>
                        <div class="col-sm-12">
						<div class="control-group table-group">
                            <label class="table-label" for="combo"><?= lang('Purchase_details'); ?></label>

                            <div class="controls table-controls">
                                <table id="prTable"
                                       class="table items table-striped  table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('cost'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('quantity'); ?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('value'); ?></th>
                                        <!--<th class="col-md-3 col-sm-3 col-xs-3"><?= lang('price'); ?></th>
                                        <th class="col-md-1 col-sm-1 col-xs-1 text-center">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>-->
                                    </tr>
                                    </thead>
                                    <tbody><tr id="<?= 'row_'.$fixed_asset_details->id ?>" class="<?= 'item_'.$fixed_asset_details->id ?> warning" data-item-id="<?= $fixed_asset_details->id ?>"><td><input name="combo_item_id[]" class="rid" type="hidden" value="<?= $fixed_asset_details->id ?>"><input name="combo_item_name[]" type="hidden" value="<?= $fixed_asset_details->name ?>"><input name="combo_item_code[]" type="hidden" value="<?= $fixed_asset_details->code ?>"><span  id="<?= 'name_'.$fixed_asset_details->id ?>"><?= $fixed_asset_details->code." - ".$fixed_asset_details->name ?> </span></td><td><input name="combo_item_unit[]" type="hidden" value="<?= $fixed_asset_details->unit_id ?>"><span id="<?= 'unit_'.$fixed_asset_details->unit_id ?>"><?= $units->name ?></span></td><td><input  class="form-control text-center rprice" name="cost[]" type="text" value="<?= $fixed_asset_details->purchase_date ? $this->sma->formatDecimal($fixed_asset_details->cost,4) : 0 ?>" data-id="<?= $fixed_asset_details->id ?>" data-item="<?= $fixed_asset_details->id ?>" id="<?= 'combo_item_price_'.$fixed_asset_details->id ?>" onclick="this.select();"></td><td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="<?= ($fixed_asset_details->purchase_date && $fixed_asset_details->quantity!=0) ? $this->sma->formatQuantity($fixed_asset_details->quantity) : '1' ?>" data-id="<?= $fixed_asset_details->id ?>" data-item="<?= $fixed_asset_details->id ?>" id="<?= 'quantity_'.$fixed_asset_details->id ?>" onclick="this.select();"></td><td><input disabled="" class="form-control text-center trprice" name="total_combo_item_price[]" type="text" value="<?= $fixed_asset_details->purchase_date ? $this->sma->formatDecimal($fixed_asset_details->value) : 0 ?>" data-id="<?= $fixed_asset_details->id ?>" data-item="<?= $fixed_asset_details->id ?>" id="<?= 'combo_item_value_'.$fixed_asset_details->id ?>" onclick="this.select();"></td><!--<td class="text-center"><i class="fa fa-times tip del" id="<?= $fixed_asset_details->id ?>" title="Remove" style="cursor:pointer;"></i></td>--></tr></tbody>
									<tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                        </div>
						    <div id="ex-combo"></div>

							<div class="col-sm-12">
                            <h3 class="bold"></h3>
                            <div class="table-responsive">
                                <table
                                class="table  table-striped table-condensed dfTable one-columns">

                                <tbody>
                                    <?php
									//foreach ($warehouses as $warehouse) {


                                                //if ($warehouse->quantity != 0) {


														echo '<tr><td><strong>' . lang('Total')  . '</strong></td><td><input disabled class="form-control text-center" name="total" id="total" type="text" value="'.(($fixed_asset_details->purchase_date && $fixed_asset_details->value) ? $this->sma->formatDecimal($fixed_asset_details->value) : "").'"></td><td><strong>' . lang('order_tax')  . '</strong></td><td><input disabled class="form-control text-center" name="total_tax" id="total_tax" type="text" value="'.($fixed_asset_details->total_tax ? $this->sma->formatDecimal($fixed_asset_details->total_tax) : "").'" ></td><td><strong>' . lang('grand_total')  . '</strong></td><td><input disabled class="form-control text-center" name="wavg_cost" id="wavg_cost" type="text"  value="'.($fixed_asset_details->grand_total ? $this->sma->formatDecimal($fixed_asset_details->grand_total) : "").'"></td></tr>';
													//}

                                            //}
											?>
                                                </tbody>
                            </table>
                        </div>
                        </div>

						<div class="col-sm-12 spark-form-actions" id="div_addnewItemManually" style="margin-top: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
							<button type="button" class="spark-btn-primary-action" id="addPurchaseItem" style="padding: 9px 22px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
								<i class="fa fa-check"></i>
								<span><?= lang('save') ?></span>
							</button>
							<button type="button" class="spark-btn-secondary-action" id="backMR" style="padding: 9px 20px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
								<i class="fa fa-arrow-right"></i>
								<span><?= lang('Back') ?></span>
							</button>
						</div>


                    </div>



                                    </div>
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

				    window.location.href = site.base_url + 'fixedassets';

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



		$(document).on('click', '#addPurchaseItem', function (e) {


            if (!$('#posupplier').val()) {
                $('#mErrorPO').text('<?= lang('Supplier_is_required') ?>');
                $('#mError-conPO').show();
                return false;
            }
            if (!$('#wavg_cost').val() || $('#wavg_cost').val()==0) {
                $('#mErrorPO').text('<?= lang('Total_purchase_is_zero') ?>');
                $('#mError-conPO').show();
                return false;
            }

			 var id='<?= $fixed_asset_details->id; ?>';
			 var cost= $('#combo_item_price_'+id).val();
			 var quantity= $('#quantity_'+id).val();
             var value= $('#combo_item_value_'+id).val();
             var tax_id= $('#potax2').val();
             var total_tax= $('#total_tax').val();
             var supplier_id= $('#posupplier').val();
             var grand_total= $('#wavg_cost').val();
             var purchase_date= $('#purchase_date').val();






					$.ajax({
                type: 'POST',
                async: false,
                url: site.base_url + 'fixedassets/addPurchaseItem',
                dataType : "JSON",
                data: {token: "<?= $csrf; ?>",  id : id, cost : cost, quantity : quantity, value : value, tax_id : tax_id, total_tax : total_tax, grand_total : grand_total, supplier_id : supplier_id, purchase_date : purchase_date},
                success: function (data) {
                    //console.log(data)

                    if (data.msg == 'success') {

						$('#mSuccessPO').text('<?= lang('purchase_added') ?>');

						$('#mSuccess-conPO').show();
                        $('#cost').val(cost);
					    $('#quantity').val(quantity);
					    $('#value').val(value);
					    $('#purchase_date2').val(purchase_date);
					    //window.location.href = site.base_url + 'manufacturing/bom';
				}else{
					$('#mErrorPO').text('<?= lang('failed_to_save') ?>');
					$('#mError-conPO').show();
					}

                },
            });




            return false;

		});


        $(document).on('click', '#addDepreciation', function (e) {
    if (!$('#value').val() || $('#value').val()==0) {
    $('#mErrorDE').text('<?= lang('Value_is_required') ?>');
    $('#mError-conDE').show();
    return false;
    }

if (!$('#start_date').val()) {
    $('#mErrorDE').text('<?= lang('Start_date_of_operation_is_required') ?>');
    $('#mError-conDE').show();
    return false;
}

if (!$('#useful_life').val() || $('#useful_life').val()==0) {
    $('#mErrorDE').text('<?= lang('Useful_life_is_required') ?>');
    $('#mError-conDE').show();
    return false;
    }


    if (!$('#scrap_value').val()) {
    $('#mErrorDE').text('<?= lang('Scrap_value_is_required') ?>');
    $('#mError-conDE').show();
    return false;
    }


 var id='<?= $fixed_asset_details->id; ?>';

 var start_date= $('#start_date').val();
 var useful_life= $('#useful_life').val();
 var scrap_value= $('#scrap_value').val();
 var cost= $('#cost').val();
 var quantity= $('#quantity').val();
 var value= $('#value').val();
 var depreciation_ratio= $('#depreciation_ratio').val();
 var depreciation_amount= $('#depreciation_amount').val();
 var depreciation_amount_per_year= $('#depreciation_amount_per_year').val();






        $.ajax({
    type: 'POST',
    async: false,
    url: site.base_url + 'fixedassets/addDepreciation',
    dataType : "JSON",
    data: {token: "<?= $csrf; ?>",  id : id, start_date : start_date, useful_life : useful_life, scrap_value : scrap_value, cost : cost, quantity : quantity, value : value, depreciation_ratio : depreciation_ratio, depreciation_amount : depreciation_amount, depreciation_amount_per_year : depreciation_amount_per_year},
    success: function (data) {
        //console.log(data)

        if (data.msg == 'success') {

            $('#mSuccessDE').text('<?= lang('Depreciation_added') ?>');

            $('#mSuccess-conDE').show();

            //window.location.href = site.base_url + 'manufacturing/bom';
    }else{
        $('#mErrorDE').text('<?= lang('failed_to_save') ?>');
        $('#mError-conDE').show();
        }

    },
});




return false;

});


		$(document).on('click', '#saveinformation', function (e) {


			if (!$('#parent_class_id').val()) {
                $('#mError').text('<?= lang('Parent_class_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#sub_class_id').val()) {
                $('#mError').text('<?= lang('Sub_class_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            if (!$('#accountID').val()) {
                $('#mError').text('<?= lang('Depreciation_allowance_account_is_required') ?>');
                $('#mError-con').show();
                return false;
            }

            if (!$('#accountIDEX').val()) {
                $('#mError').text('<?= lang('Depreciation_expenses_account_is_required') ?>');
                $('#mError-con').show();
                return false;
            }



            var id='<?= $fixed_asset_details->id ?>';

            var url;
			var success_msg;
				url="fixedassets/updateFixedAssetInfoByAjax";
				success_msg='<?= lang('Fixed_asset_updated_successfully') ?>';
			//alert(success_msg)

			var msg, row = null, fixedasset = {

                sub_class_id: $('#sub_class_id').val(),
                parent_class_id: $('#parent_class_id').val(),
                branch_name: $('#branch_name').val(),


                accountID: $('#accountID').val(),
                accountIDEX: $('#accountIDEX').val()




            };



            $.ajax({
                type: "POST", async: false,
                url: site.base_url +""+url,
                data: {token: "<?= $csrf; ?>", fixedasset: fixedasset, id: id, sub_class_id: $('#sub_class_id').val(), parent_class_id: $('#parent_class_id').val(), branch_name: $('#branch_name').val(), accountID: $('#accountID').val(), accountIDEX: $('#accountIDEX').val()},
                dataType: "json",
                success: function (data) {
                    //console.log(data)

                    if (data.msg == 'success') {
						//$("#add_item").val(data.result.code);
						//$('input#add_item').autocomplete("search");

						$('#mSuccess').text(success_msg);

						$('#mSuccess-con').show();
						//$("#div_saveinformation").css("display", "none");


                    } else {
                        msg = data.msg;
						$('#mError').text(msg);
						$('#mError-con').show();

                    }
                }
            });
            return false;

        });

		$(document).on('click', '#edit_fixed_asset', function (e) {

			if (!$('#name_fixed_asset').val()) {
                $('#mErrorBOM').text('<?= lang('Fixed_asset_name_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#code_fixed_asset').val()) {
                $('#mErrorBOM').text('<?= lang('Fixed_asset_code_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }
            if (!$('#unit_id').val()) {
                $('#mErrorBOM').text('<?= lang('Fixed_asset_unit_is_required') ?>');
                $('#mError-conBOM').show();
                return false;
            }



            var name=$('#name_fixed_asset').val();
            var code=$('#code_fixed_asset').val();
            var measurement=$('#measurement').val();
            var image=$('#image').val();
            var group_id=$('#group_id').val();
            var unit_id=$('#unit_id').val();
            var location=$('#location').val();
            var color=$('#color').val();
            var man_company=$('#man_company').val();
            var quality=$('#quality').val();
            var country_origin=$('#country_origin').val();
            var style=$('#style').val();
            var active=$('#active').val()
            var specifications=$('#specifications').val();
            var id='<?= $fixed_asset_details->id ?>';


			var mts = $('#image').get(0).files[0];

			var formData = new FormData();

				var data = $('#fixedAssetForm').serializeArray();
				var obj = {};
				for (var i = 0; i < data.length; i++) {
    					//obj[data[i].name] = data[i].value;
    					formData.append([data[i].name], data[i].value);
				}



            var url;
			var success_msg;
				url="fixedassets/updateFixedAssetByAjax";
				success_msg='<?= lang('Fixed_asset_updated_successfully') ?>';
			//alert(success_msg)

			var msg, row = null, fixedasset = {
                name: name,
                code: code,
                measurement: measurement,
                group_id: group_id,
                unit_id: unit_id,
                location: location,
                color: color,
                man_company: man_company,
                quality: quality,
                country_origin: country_origin,
                style: style,
                active: active,
                specifications: specifications


            };

				formData.append('userfile', mts);
				formData.append('id', id);
				formData.append('fixedasset', fixedasset);
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
						//$("#div_saveinformation").css("display", "none");


                    } else {
                        msg = data.msg;
						$('#mErrorBOM').text(msg);
						$('#mError-conBOM').show();

                    }
                }
            });
            return false;

        });

        if (!$("#purchase_date").val()) {
            $("#purchase_date").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'sma',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#purchase_date', function (e) {
            localStorage.setItem('purchase_date', $(this).val());
        });
        if (purchase_date = localStorage.getItem('purchase_date')) {
            //$('#purchase_date').val(purchase_date);
        }

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





        $("#add_item").autocomplete({

            source: '<?= admin_url('fixedassets/suggestions?warehouse_id='.$fixed_asset_details->id); ?>' ,
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
				var bom_id='<?= $fixed_asset_details->id; ?>';
				//updateBomDetail(bom_id,item_id,items[item_id].qty);
                //items[item_id].avg_cost = (parseFloat(items[item_id].avg_cost) * parseFloat(items[item_id].qty)).toFixed(2);
            } else {
                items[item_id] = item;
				if(flage==1){
			var bom_id='<?= $fixed_asset_details->id; ?>';
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
            $('#wavg_cost').val(formatDecimal(pp/'<?= $fixed_asset_details->quantity ?>',4));
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
            var tax_rates =<?php echo json_encode($tax_rates); ?>;
            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0,invoice_tax=0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()),4);
            });
                    $('#total').val(formatDecimal(pp,4));

                    $.each(tax_rates, function () {
                    if (this.id == $('#potax2').val()) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            invoice_tax = formatDecimal((((pp) * this.rate) / 100), 12);
                        }
                    }
                });
                    $('#total_tax').val(formatDecimal(invoice_tax,4));
					$('#wavg_cost').val(formatDecimal(pp+invoice_tax,4));

            return true;
        }

        $('#potax2').change(function (e) {
            calculate_price();
        });
        $(document).on('change', '.rquantity, .rprice', function () {
			var row = $(this).closest('tr');
			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');

			row.find('.trprice').val(formatDecimal(parseFloat(row.find('.rprice').val())*parseFloat(row.find('.rquantity').val()),4));
			var bom_id='<?= $fixed_asset_details->id; ?>';
			//items[item_id].qty=formatDecimal(row.find('.rquantity').val(),4);
				//updateBomDetail(bom_id,item_id,row.find('.rquantity').val());

            calculate_price();
        });

        $(document).on('click', '.del', function () {


			var item_id = $(this)
            .closest('tr')
            .attr('data-item-id');
			var bom_id='<?= $fixed_asset_details->id; ?>';
				//deleteBomDetail(bom_id,item_id);

            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();




            calculate_price();
        });

        function calculate_depreciation() {

            if($('#useful_life').val() && $('#scrap_value').val() && $('#value').val()){

                $('#depreciation_amount_per_year').val(formatDecimal(($('#value').val() - $('#scrap_value').val())/$('#useful_life').val(),4))
                $('#depreciation_amount').val(formatDecimal($('#depreciation_amount_per_year').val() / 12,4));
                $('#depreciation_ratio').val(formatDecimal(($('#depreciation_amount').val() * 100 )/$('#value').val(),2));



            }



            return true;
        }

        $('#useful_life').change(function (e) {
            calculate_depreciation();
        });

        $('#scrap_value').change(function (e) {
            calculate_depreciation();
        });




        function calculate_depreciation_cost() {

            if($('#cost').val() && $('#quantity').val()){

                $('#value').val(formatDecimal(($('#cost').val() * $('#quantity').val()),4));


            }



            return true;
        }

        $('#cost').change(function (e) {
            calculate_depreciation_cost();
            calculate_depreciation();
        });

        $('#quantity').change(function (e) {
            calculate_depreciation_cost();
            calculate_depreciation();
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
                <button type="button" class="spark-btn-primary-action" id="updateAttr" style="padding: 8px 20px; font-size: 13px;">
                    <i class="fa fa-check"></i>
                    <span><?= lang('submit') ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {



        $('.content').richText();


        <?php if ($fixed_asset_details->parent_class_id) {
            ?>

        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('fixedassets/getSubClassesByParentId') ?>/" + <?= $fixed_asset_details->parent_class_id ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#sub_class_id").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                        placeholder: "<?= lang('select_category_to_load') ?>",
                        data: scdata
                    });
                } else {
                    $("#sub_class_id").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                    });
                }
            }
        });

        <?php }
            ?>

        $("#start_date").datetimepicker({
            format: site.dateFormats.js_sdate, fontAwesome: true, language: 'sma', todayBtn: 1, autoclose: 1, minView: 2,
            startDate: moment($("#purchase_date2").val()).format('YYYY-MM-DD'),
        });

        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
