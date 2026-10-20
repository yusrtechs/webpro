<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .two-side{
        padding-right:5px;
        padding-left:5px;
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

$wh = [];
if (!empty($warehouses)) {
    foreach ($warehouses as $warehouse) {
        $wh[$warehouse->id] = $warehouse->name;
    }
}
if (!isset($whselected) || !is_array($whselected)) {
    $whselected = [];
    if (!empty($warehouses_products)) {
        foreach ($warehouses_products as $warehouse) {
            $whselected[] = (string)$warehouse->id;
        }
    } elseif (!empty($Settings->default_warehouse)) {
        $whselected[] = (string)$Settings->default_warehouse;
    } elseif (!empty($warehouses)) {
        $whselected[] = (string)$warehouses[0]->id;
    }
}
?>
<script type="text/javascript">
    function baseunits(ele) {
        $(ele).select2({
            minimumInputLength: 1,
            ajax: {
                url: site.base_url + 'products/suggestionsV4',
                dataType: 'json',
                quietMillis: 15,
                data: function(term, page) {
                    return {
                        term: term,
                        limit: 10,
                    };
                },
                results: function(data, page) {
                    if (data != null) {
                        return { results: data };
                    } else {
                        return { results: [{ id: '', text: '<?= lang('no_match_found') ?>' }] };
                    }
                },
            },
        });
    }
    $(document).ready(function () {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'products');
        });
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
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
                            <?php if ($product->subcategory_id) { ?>
                                $("#subcategory").select2('val', '<?= $product->subcategory_id ?>');
                            <?php } ?>
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

        // ─── Branch change handler for Edit Product (Preserving saved values) ───
        $('#brId').change(function () {
            var v = $(this).val();
            var uopt = $("#unit");
            var uopt1 = $("#pwarehouse");
            var uopt2 = $("#category");
            var uopt3 = $("#brand");

            var saved_category = '<?= $product->category_id ?>';
            var saved_subcategory = '<?= $product->subcategory_id ?>';
            var saved_unit = '<?= $product->unit ?>';
            var saved_sale_unit = '<?= $product->sale_unit ?>';
            var saved_purchase_unit = '<?= $product->purchase_unit ?>';
            var saved_brand = '<?= $product->brand ?>';
            var saved_warehouses = <?= json_encode(!empty($whselected) ? array_values(array_map('strval', $whselected)) : []) ?>;

            if (v) {
                // 1. Warehouses
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getWarehousesBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#pwarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                            var availableIds = [];
                            $.each(scdata, function () {
                                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);
                                availableIds.push(String(this.id));
                            });

                            // Determine selection based on strict priority hierarchy
                            var validSavedWh = [];
                            if (saved_warehouses && saved_warehouses.length > 0) {
                                validSavedWh = $.grep(saved_warehouses, function(id) {
                                    return $.inArray(String(id), availableIds) !== -1;
                                });
                            }

                            if (validSavedWh.length > 0) {
                                // Priority 1: Saved product warehouse(s) that exist in this branch
                                $('#pwarehouse').val(validSavedWh).trigger('change.select2');
                            } else if (saved_warehouses && saved_warehouses.length > 0) {
                                // Saved warehouse exists on product (e.g. cross-branch / legacy)
                                $('#pwarehouse').val(saved_warehouses).trigger('change.select2');
                            } else {
                                // Fallback when product has NO saved warehouse
                                var defaultWh = '<?= !empty($Settings->default_warehouse) ? (string)$Settings->default_warehouse : '' ?>';
                                if (defaultWh && $.inArray(String(defaultWh), availableIds) !== -1) {
                                    // Priority 2: System default warehouse
                                    $('#pwarehouse').val([String(defaultWh)]).trigger('change.select2');
                                } else if (scdata.length > 0) {
                                    // Priority 3: First available warehouse in branch
                                    $('#pwarehouse').val([String(scdata[0].id)]).trigger('change.select2');
                                } else {
                                    // Priority 4: Empty
                                    $('#pwarehouse').val([]).trigger('change.select2');
                                }
                            }
                        } else {
                            $("#pwarehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });

                // 2. Units
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getUnitBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                            $.each(scdata, function () {
                                $("<option />", {value: this.id, text: this.text + ' (' + this.code + ')'}).appendTo(uopt);
                            });
                            var selectedUnit = saved_unit || (scdata.length > 0 ? scdata[0].id : '');
                            $('#unit').val(selectedUnit).trigger('change.select2');
                            var vu = $('#unit').val();

                            if (vu) {
                                $.ajax({
                                    type: "get",
                                    async: false,
                                    url: "<?= admin_url('products/getSubUnits') ?>/" + vu +"/"+ v,
                                    dataType: "json",
                                    success: function (data) {
                                        $('#default_sale_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                                        $('#default_purchase_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                                        $.each(data, function () {
                                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_sale_unit'));
                                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo($('#default_purchase_unit'));
                                        });
                                        $('#default_sale_unit').select2('val', saved_sale_unit || vu);
                                        $('#default_purchase_unit').select2('val', saved_purchase_unit || vu);
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
                        } else {
                            $("#unit").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('unit') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('unit') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('unit') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });

                // 3. Categories
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getCategoryBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#category').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                            $.each(scdata, function () {
                                $("<option />", {value: this.id, text: this.text}).appendTo(uopt2);
                            });
                            var selectedCat = saved_category || (scdata.length > 0 ? scdata[0].id : '');
                            $('#category').val(selectedCat).trigger('change.select2');
                            var vc = $('#category').val();

                            if (vc) {
                                $.ajax({
                                    type: "get",
                                    async: false,
                                    url: "<?= admin_url('products/getSubCategories') ?>/" + vc +"/"+ v,
                                    dataType: "json",
                                    success: function (subdata) {
                                        if (subdata != null) {
                                            subdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                                placeholder: "<?= lang('select_category_to_load') ?>",
                                                minimumResultsForSearch: 7,
                                                data: subdata
                                            });
                                            if (saved_subcategory) {
                                                $("#subcategory").select2('val', saved_subcategory);
                                            }
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
                        } else {
                            $("#category").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('category') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('category') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('category') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });

                // 4. Brands
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getBrandBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#brand').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                            $.each(scdata, function () {
                                $("<option />", {value: this.id, text: this.text}).appendTo(uopt3);
                            });
                            $('#brand').val(saved_brand || (scdata.length > 0 ? scdata[0].id : '')).trigger('change');
                        } else {
                            $("#brand").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('brand') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('brand') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('brand') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            }
        });
    });
</script>

<?php
$attrib = ['data-toggle' => 'validator', 'role' => 'form', 'class' => 'spark-product-form'];
echo admin_form_open_multipart('products/edit/' . $product->id, $attrib);
?>

<div class="spark-product-module">
    
    <!-- Modern Header Banner -->
    <div class="spark-product-banner">
        <div class="spark-product-banner-content">
            <div class="spark-product-banner-badge">
                <i class="fa fa-pencil-square-o"></i>
            </div>
            <div>
                <h1 class="spark-product-banner-title"><?= lang('edit_product'); ?>: <?= $product->name; ?></h1>
                <p class="spark-product-banner-subtitle"><?php echo lang('update_info'); ?> &bull; <?= lang('code'); ?>: <?= $product->code; ?></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= admin_url('products'); ?>" class="btn btn-default" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); color: #fff; border-radius: 12px; padding: 8px 16px; font-weight: 700;">
                <i class="fa fa-arrow-right" style="margin-inline-end: 4px;"></i> <?= lang('back_to_products'); ?>
            </a>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         CORE CARD: Simple by Default (Essential Fields + Live Image Dropzone)
         ══════════════════════════════════════════════════════════════════════════ -->
    <div class="spark-product-core-card">
        <div class="spark-product-core-layout">
            
            <!-- Left Column: Core Fields Grid -->
            <div class="spark-core-fields-grid">
                
                <!-- Product Name (Highlighted Field) -->
                <div class="form-group all spark-field-full spark-field-primary-name">
                    <label for="name" style="font-weight: 800; font-size: 14px; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                        <i class="fa fa-tag text-success"></i> <?= lang('product_name', 'name') ?> *
                    </label>
                    <?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control gen_slug" id="name" required="required" placeholder="' . lang('enter_product_name') . '"'); ?>
                </div>

                <!-- Product Types -->
                <div class="form-group">
                    <label for="type" style="font-weight: 700;"><?= lang('product', 'type') ?> *</label>
                    <?php
                    $opts = ['standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service'), 'insurance' => lang('insurance')];
                    echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
                    ?>
                </div>

                <div class="form-group">
                    <label for="type2" style="font-weight: 700;"><?= lang('product_type', 'type2') ?> *</label>
                    <?php
                    $opts = ['primary' => lang('primary'), 'complete' => lang('complete'), 'service' => lang('service_product')];
                    echo form_dropdown('type2', $opts, (isset($_POST['type2']) ? $_POST['type2'] : ($product ? $product->type2 : '')), 'class="form-control" id="type2" required="required"');
                    ?>
                </div>

                <!-- Product Code & Item No -->
                <div class="form-group all">
                    <label for="code" style="font-weight: 700;"><?= lang('product_code', 'code') ?> *</label>
                    <div class="input-group">
                        <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code" required="required" placeholder="' . lang('product_code') . '"') ?>
                        <span class="input-group-addon pointer tip" id="random_num" title="<?= lang('random_code'); ?>" style="padding: 1px 12px; background: #f1f5f9; cursor: pointer;">
                            <i class="fa fa-random text-primary"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group all">
                    <label for="item_no" style="font-weight: 700;"><?= lang('Item_no', 'item_no') ?></label>
                    <div class="input-group">
                        <?= form_input('item_no', (isset($_POST['item_no']) ? $_POST['item_no'] : ($product ? $product->item_no : '')), 'class="form-control" id="item_no" placeholder="' . lang('Item_no') . '"') ?>
                        <span class="input-group-addon pointer tip" id="random_item_num" title="<?= lang('random_item_code'); ?>" style="padding: 1px 12px; background: #f1f5f9; cursor: pointer;">
                            <i class="fa fa-random text-primary"></i>
                        </span>
                    </div>
                </div>

                <!-- Hidden Product Number -->
                <div class="form-group all" style="display:none">
                    <?= lang('product_number', 'product_number'); ?>
                    <?php
                    $max_p_no = $product->product_number;
                    if (!isset($max_p_no) || $max_p_no == '' || $max_p_no == null) {
                        if (!empty($max_product_number)) {
                            foreach ($max_product_number as $max_product_no) {
                                $max_p_no = $max_product_no->max_product_number;
                            }
                        }
                    }
                    echo form_input('product_number', set_value('product_number', ($product ? $max_p_no : '')), 'class="form-control tip" id="product_number" ');
                    ?>
                </div>

                <!-- Group / Company / Branch (if grouping enabled) -->
                <?php if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) { ?>
                    <div class="spark-field-full spark-grouping-container">
                        <div class="spark-grouping-grid">
                            <?php if (isset($this->Settings->enable_grp) && $this->Settings->enable_grp == 1) { ?>
                                <div class="form-group">
                                    <label for="grpId" style="font-weight: 700;"><?= lang('Main_group', 'Main_group') ?></label>
                                    <?php
                                    $grp = [];
                                    foreach ($mainGroups as $mainGroup) {
                                        if ($product->grpId == $mainGroup->id) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                    }
                                    echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : $product->grpId), 'class="form-control select" id="grpId" required="required" style="width:100%"');
                                    ?>
                                </div>
                            <?php } ?>

                            <?php if ((isset($this->Settings->enable_grp) && $this->Settings->enable_grp == 1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co == 1)) { ?>
                                <div class="form-group">
                                    <label for="coId" style="font-weight: 700;"><?= lang('Main_company', 'Main_company') ?></label>
                                    <?php
                                    $co = [];
                                    foreach ($mainCompanies as $mainCompany) {
                                        if ($product->coId == $mainCompany->id) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }
                                    echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $product->coId), 'class="form-control select" id="coId" required="required" style="width:100%"');
                                    ?>
                                </div>
                            <?php } ?>

                            <?php if ((isset($this->Settings->enable_grp) && $this->Settings->enable_grp == 1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co == 1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br == 1)) { ?>
                                <div class="form-group">
                                    <label for="brId" style="font-weight: 700;"><?= lang('Main_branch', 'Main_branch') ?></label>
                                    <?php
                                    $br = [];
                                    foreach ($mainBranches as $mainBranche) {
                                        if ($product->brId == $mainBranche->id) {
                                            $br[$mainBranche->id] = $mainBranche->name;
                                        }
                                    }
                                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $product->brId), 'id="brId" class="form-control select" required="required" style="width:100%"');
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Warehouses Multiselect & Select All -->
                <div class="form-group all spark-field-full spark-wh-container" id="wh_div1">
                    <div class="spark-wh-header">
                        <label for="pwarehouse" style="font-weight: 700; margin: 0; display: flex; align-items: center; gap: 6px;">
                            <i class="fa fa-building-o text-primary"></i> <?= lang('warehouses', 'warehouses'); ?> *
                        </label>
                        <div id="allck" class="spark-wh-select-all">
                            <input type="checkbox" class="checkbox" name="attributes" id="all">
                            <label for="all" class="padding05">
                                <?= lang('all') ?> (<?= lang('select_all_warehouses'); ?>)
                            </label>
                        </div>
                    </div>
                    <?php
                    if (empty($wh) && !empty($warehouses)) {
                        foreach ($warehouses as $warehouse) {
                            $wh[$warehouse->id] = $warehouse->name;
                        }
                    }
                    echo form_multiselect('wh_option[]', $wh, $whselected, 'id="pwarehouse" class="form-control input-tip multiselect" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;overflow:hidden;height:auto;" ');
                    ?>
                </div>

                <!-- Category & Subcategory -->
                <div class="form-group all">
                    <label for="category" style="font-weight: 700;"><?= lang('category', 'category') ?> *</label>
                    <div class="input-group">
                        <?php
                        $cat[''] = '';
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang('select') . ' ' . lang('category') . '" required="required" style="width:100%"');
                        ?>
                        <?php if ($Owner || $Admin || $GP['category-add']) { ?>
                            <div class="input-group-addon no-print" style="padding: 2px 10px; background: #f1f5f9;">
                                <a href="<?= admin_url('products/add_category'); ?>" id="add-category" class="external" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-plus-circle text-success" style="font-size: 1.2em;"></i>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group all">
                    <label for="subcategory" style="font-weight: 700;"><?= lang('subcategory', 'subcategory') ?></label>
                    <div class="controls" id="subcat_data">
                        <?php echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory" placeholder="' . lang('select_category_to_load') . '"'); ?>
                    </div>
                </div>

                <!-- Product Unit -->
                <div class="form-group standard_combo">
                    <label for="unit" style="font-weight: 700;"><?= lang('product_unit', 'unit'); ?> *</label>
                    <div class="input-group">
                        <?php
                        $pu[''] = lang('select') . ' ' . lang('unit');
                        foreach ($base_units as $bu) {
                            $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                        }
                        echo form_dropdown('unit', $pu, set_value('unit', $product->unit), 'class="form-control tip" required="required" id="unit" style="width:100%;"');
                        ?>
                        <?php if ($Owner || $Admin || $GP['category-add']) { ?>
                            <div class="input-group-addon no-print" style="padding: 2px 10px; background: #f1f5f9;">
                                <a href="<?= admin_url('products/add_unit'); ?>" id="add-unit" class="external" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-plus-circle text-success" style="font-size: 1.2em;"></i>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group all">
                    <label for="second_name" style="font-weight: 700;"><?= lang('second_name', 'second_name'); ?></label>
                    <?= form_input('second_name', set_value('second_name', ($product ? $product->second_name : '')), 'class="form-control tip" id="second_name"'); ?>
                </div>


                <!-- Core Pricing Box (Cost, Price, Min Price) -->
                <div class="spark-field-full">
                    <div class="spark-pricing-box">
                        <div class="spark-pricing-header">
                            <i class="fa fa-money text-success"></i> <?= lang('product_prices_and_cost'); ?>
                        </div>
                        <div class="spark-pricing-grid">

                            <!-- Selling Price -->
                            <div class="form-group" style="margin: 0;">
                                <label for="price" style="font-size: 12.5px; font-weight: 700;"><?= lang('product_price', 'price') ?> *</label>
                                <?= form_input('price', (isset($_POST['price']) ? $_POST['price'] : ($product ? $this->sma->formatDecimal($product->price, 4) : '')), 'class="form-control tip" id="price" required="required" placeholder="0.00"') ?>
                            </div>

                            <!-- Minimum Selling Price -->
                            <div class="form-group" style="margin: 0;">
                                <label for="minimum_selling_price" style="font-size: 12.5px; font-weight: 700;"><?= lang('Minimum_selling_price', 'Minimum_selling_price') ?></label>
                                <?= form_input('minimum_selling_price', (isset($_POST['minimum_selling_price']) ? $_POST['minimum_selling_price'] : ($product ? $this->sma->formatDecimal($product->minimum_selling_price, 4) : 0)), 'class="form-control" id="minimum_selling_price" placeholder="0.00"') ?>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Professional Image Upload Card & Preview with no_image.png fallback -->
            <?php
            $default_no_image = base_url('assets/uploads/no_image.png');
            $current_image_url = ($product && !empty($product->image)) ? base_url('assets/uploads/' . $product->image) : $default_no_image;
            $has_custom_image = ($product && !empty($product->image));
            ?>
            <div>
                <div class="spark-image-upload-card" id="spark-dropzone-container">
                    <!-- Native file input kept intact for form upload (hidden visually) -->
                    <input id="product_image" type="file" name="product_image" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" class="spark-hidden-file-input">
                    
                    <div class="spark-preview-container">
                        <!-- Live Image Preview Box (Always showing current image or no_image.png) -->
                        <div class="spark-image-preview-box">
                            <img id="spark-img-preview" src="<?= $current_image_url ?>" data-default-src="<?= $default_no_image ?>" alt="<?= lang('product_image'); ?>" />
                        </div>
                        
                        <div class="spark-preview-meta" id="spark-img-meta" style="<?= $has_custom_image ? 'display:flex;' : 'display:none;' ?>">
                            <span id="spark-img-filename" class="spark-filename-badge">
                                <?= $has_custom_image ? $product->image : '' ?>
                            </span>
                        </div>

                        <div class="spark-dropzone-controls">
                            <label for="product_image" class="spark-btn-browse">
                                <i class="fa fa-folder-open-o"></i> <?= lang('browse_files'); ?>
                            </label>
                            <button type="button" id="spark-img-remove-btn" class="btn btn-sm btn-danger spark-btn-remove-img" style="<?= $has_custom_image ? 'display:inline-flex;' : 'display:none;' ?>">
                                <i class="fa fa-trash"></i> <?= lang('remove'); ?>
                            </button>
                        </div>

                        <div class="spark-dropzone-hint">
                            <?= lang('drag_or_browse_image'); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Combo Products Table (if combo type) -->
        <div class="combo spark-combo-products" style="display:none;">
            <div class="spark-combo-header">
                <div class="spark-combo-icon">
                    <i class="fa fa-cubes"></i>
                </div>
                <div>
                    <h4 class="spark-combo-title"><?= lang('combo_products'); ?></h4>
                    <p class="spark-combo-desc"><?= lang('add_product') . ' (' . lang('not_with_variants') . ')'; ?></p>
                </div>
            </div>
            <div class="form-group">
                <label for="add_item" style="font-weight: 700;"><?= lang('add_product', 'add_item') . ' (' . lang('not_with_variants') . ')'; ?></label>
                <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line('add_item') . '"'); ?>
            </div>
            <div class="control-group table-group">
                <label class="table-label" for="combo"><?= lang('combo_products'); ?></label>
                <div class="controls table-controls table-responsive spark-combo-products-table-wrap">
                    <table id="prTable" class="table items table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th>
                            <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th>
                            <th class="col-md-2 col-sm-2 col-xs-2"><?= lang('quantity'); ?></th>
                            <th class="col-md-3 col-sm-3 col-xs-3"><?= lang('cost'); ?></th>
                            <th class="col-md-1 col-sm-1 col-xs-1 text-center"><i class="fa fa-trash-o" style="opacity:0.5;"></i></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         ADVANCED TOGGLE BUTTON: "تفاصيل إضافية" (Expand / Collapse Animation)
         ══════════════════════════════════════════════════════════════════════════ -->
    <div class="spark-advanced-toggle-wrapper">
        <button type="button" id="btn-toggle-advanced" class="spark-advanced-toggle-btn">
            <i class="fa fa-sliders text-success"></i>
            <span class="toggle-text"><?= lang('additional_details'); ?></span>
            <span class="spark-advanced-badge"><?= lang('advanced_settings'); ?></span>
            <i class="fa fa-chevron-down toggle-icon"></i>
        </button>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         ADVANCED SECTIONS CONTAINER (Collapsible Cards)
         ══════════════════════════════════════════════════════════════════════════ -->
    <div id="spark-advanced-sections" class="spark-advanced-sections-container">
        
        <!-- Section 1: Taxes & Invoicing (الضرائب والفوترة) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-tax">
                        <i class="fa fa-percent"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('product_tax_and_methods'); ?></h3>
                        <p class="spark-section-desc"><?= lang('tax_settings_desc'); ?></p>
                    </div>
                </div>
                <i class="fa fa-chevron-down spark-section-chevron"></i>
            </div>
            <div class="spark-advanced-card-body">
                <div class="spark-advanced-grid-3">
                    
                    <div class="form-group all">
                        <label for="tax_rate" style="font-weight: 700;"><?= lang('product_tax', 'tax_rate') ?></label>
                        <?php
                        $tr[''] = '';
                        foreach ($tax_rates as $tax) {
                            $tr[$tax->id] = $tax->name;
                        }
                        echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : ($product ? $product->tax_rate : $Settings->default_tax_rate)), 'class="form-control select" id="tax_rate" placeholder="' . lang('select') . ' ' . lang('product_tax') . '" style="width:100%"');
                        ?>
                    </div>

                    <div class="form-group all" id="div_tax_method">
                        <label for="tax_method" style="font-weight: 700;"><?= lang('tax_method', 'tax_method') ?></label>
                        <?php
                        $tm = ['0' => lang('inclusive'), '1' => lang('exclusive')];
                        echo form_dropdown('tax_method', $tm, (isset($_POST['tax_method']) ? $_POST['tax_method'] : ($product ? $product->tax_method : '')), 'class="form-control select" id="tax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"');
                        ?>
                    </div>

                    <?php if ($Settings->invoice_view == 200) { ?>
                        <div class="form-group">
                            <label for="hsn_code" style="font-weight: 700;"><?= lang('hsn_code', 'hsn_code'); ?></label>
                            <?= form_input('hsn_code', set_value('hsn_code', ($product ? $product->hsn_code : '')), 'class="form-control" id="hsn_code"'); ?>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>

        <!-- Section 2: Stock & Warehouses (المخزون والمستودعات) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-stock">
                        <i class="fa fa-building-o"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('inventory_and_warehouses'); ?></h3>
                        <p class="spark-section-desc"><?= lang('inventory_settings_desc'); ?></p>
                    </div>
                </div>
                <i class="fa fa-chevron-down spark-section-chevron"></i>
            </div>
            <div class="spark-advanced-card-body">
                
                <!-- Existing Warehouse Quantities Display -->
                <?php
                if (!empty($warehouses) || !empty($warehouses_products)) {
                    echo '<div class="row" style="margin-bottom: 16px;"><div class="col-md-12"><div class="well well-sm" style="background: #f8fafc; border-radius: 12px; padding: 14px;">';
                    echo '<p style="font-weight:800; margin-bottom: 8px; color: #0f172a;"><i class="fa fa-database text-primary"></i> ' . lang('warehouse_quantity') . '</p>';
                    if (!empty($warehouses_products)) {
                        if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) {
                            echo '<table class="table table-striped table-condensed" style="margin-top: 10px;"><thead><tr><th>' . lang('Main_branch') . '</th><th>' . lang('warehouse') . '</th><th>' . lang('quantity') . '</th></tr></thead><tbody>';
                        }
                        foreach ($warehouses_products as $wh_pr) {
                            if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) {
                                echo '<tr><td class="text-center"><span>' . $this->site->getMainBranchesByID($wh_pr->brId)->name . '</span></td><td class="text-center"><span>' . $wh_pr->name . '</span></td><td class="text-right">' . $this->sma->formatQuantity($wh_pr->quantity) . '</td></tr>';
                            } else {
                                echo '<span class="bold text-info" style="display:inline-block; margin-inline-end: 18px; margin-bottom: 6px;"><i class="fa fa-cube"></i> ' . $wh_pr->name . ': <input type="hidden" value="' . $this->sma->formatDecimal($wh_pr->quantity, 4) . '" id="vwh_qty_' . $wh_pr->id . '"><span class="label label-info" id="rwh_qty_' . $wh_pr->id . '" style="border-radius: 6px;">' . $this->sma->formatQuantity($wh_pr->quantity) . '</span>' . ($wh_pr->rack ? ' (<span class="padding05" id="rrack_' . $wh_pr->id . '">' . $wh_pr->rack . '</span>)' : '') . '</span>';
                            }
                        }
                        if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) {
                            echo '</tbody></table>';
                        }
                    }
                    echo '<div class="clearfix"></div></div></div></div>';
                }
                ?>

                <div class="spark-advanced-grid-3">
                    
                    <!-- Alert Quantity & Track Quantity -->
                    <div class="form-group standard">
                        <label for="alert_quantity" style="font-weight: 700;"><?= lang('alert_quantity', 'alert_quantity') ?></label>
                        <div class="input-group">
                            <?= form_input('alert_quantity', (isset($_POST['alert_quantity']) ? $_POST['alert_quantity'] : ($product ? $this->sma->formatDecimal($product->alert_quantity, 4) : '')), 'class="form-control tip" id="alert_quantity"') ?>
                            <span class="input-group-addon tip" title="<?= lang('track_quantity'); ?>">
                                <input type="checkbox" name="track_quantity" id="inlineCheckbox1" value="1" <?= ($product ? (!empty($product->track_quantity) ? 'checked="checked"' : '') : 'checked="checked"') ?>>
                            </span>
                        </div>
                    </div>

                    <?php if ($Settings->isMinimum) { ?>
                        <div class="form-group standard">
                            <label for="minimum" style="font-weight: 700;"><?= lang('Minimum_stock_for_purchase_order', 'Minimum_stock_for_purchase_order') ?></label>
                            <?= form_input('minimum', (isset($_POST['minimum']) ? $_POST['minimum'] : ($product ? $this->sma->formatQuantityDecimal($product->minimum, 4) : '0')), 'type="number" pattern="[0-9]" maxlength="10" class="form-control tip" id="minimum"') ?>
                        </div>
                        <div class="form-group standard">
                            <label for="po_quantity" style="font-weight: 700;"><?= lang('Order_quantity', 'Order_quantity') ?></label>
                            <?= form_input('po_quantity', (isset($_POST['po_quantity']) ? $_POST['po_quantity'] : ($product ? $this->sma->formatQuantityDecimal($product->po_quantity, 4) : '0')), 'type="number" pattern="[0-9]" maxlength="10" class="form-control tip" id="po_quantity"') ?>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>

        <!-- Section 3: Units, Brand & Variants (الوحدات، العلامة التجارية والمتغيرات) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-variants">
                        <i class="fa fa-sitemap"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('units_brand_and_variants'); ?></h3>
                        <p class="spark-section-desc"><?= lang('variants_settings_desc'); ?></p>
                    </div>
                </div>
                <i class="fa fa-chevron-down spark-section-chevron"></i>
            </div>
            <div class="spark-advanced-card-body">
                
                <div class="spark-advanced-grid-3" style="margin-bottom: 16px;">
                    
                    <!-- Brand -->
                    <div class="form-group all">
                        <label for="brand" style="font-weight: 700;"><?= lang('brand', 'brand') ?></label>
                        <?php
                        $brnd[''] = '';
                        foreach ($brands as $brand) {
                            $brnd[$brand->id] = $brand->name;
                        }
                        echo form_dropdown('brand', $brnd, (isset($_POST['brand']) ? $_POST['brand'] : ($product ? $product->brand : '')), 'class="form-control select" id="brand" placeholder="' . lang('select') . ' ' . lang('brand') . '" style="width:100%"');
                        ?>
                    </div>

                    <!-- Default Sale Unit -->
                    <div class="form-group standard_combo">
                        <label for="default_sale_unit" style="font-weight: 700;"><?= lang('default_sale_unit', 'default_sale_unit'); ?></label>
                        <?php
                        $uopts[''] = lang('select') . ' ' . lang('unit');
                        if (!empty($subunits)) {
                            foreach ($subunits as $sunit) {
                                $uopts[$sunit->id] = $sunit->name . ' (' . $sunit->code . ')';
                            }
                        }
                        echo form_dropdown('default_sale_unit', $uopts, $product->sale_unit, 'class="form-control" id="default_sale_unit" style="width:100%;"');
                        ?>
                    </div>

                    <!-- Default Purchase Unit -->
                    <div class="form-group standard_combo" style="display:none">
                        <label for="default_purchase_unit" style="font-weight: 700;"><?= lang('default_purchase_unit', 'default_purchase_unit'); ?></label>
                        <?= form_dropdown('default_purchase_unit', $uopts, $product->purchase_unit, 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
                    </div>

                </div>

                <!-- Product Attributes & Variants -->
                <div class="standard">
                    <div id="attrs"></div>
                    <div class="well well-sm" style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 16px;">
                        
                        <?php if ($product_options) { ?>
                            <h4 style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 10px;"><?= lang('product_options'); ?></h4>
                            <table class="table table-condensed table-striped" style="margin-top: 10px;">
                                <thead>
                                <tr class="active">
                                    <th><?= lang('name') ?></th>
                                    <th><?= lang('warehouse') ?></th>
                                    <th><?= lang('price_addition') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($product_options as $option) {
                                    echo '<tr><td class="col-xs-4"><input type="hidden" name="attr_id[]" value="' . $option->id . '"><span>' . $option->name . '</span></td><td class="code text-center col-xs-4"><span>' . $option->wh_name . '</span></td><td class="price text-right col-xs-4">' . $this->sma->formatMoney($option->price) . '</td></tr>';
                                } ?>
                                </tbody>
                            </table>
                        <?php } ?>

                        <?php if ($product_variants) { ?>
                            <h4 style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin: 16px 0 10px 0;"><?= lang('update_variants'); ?></h4>
                            <table class="table table-condensed table-striped" style="margin-top: 10px;">
                                <thead>
                                <tr class="active">
                                    <th class="col-xs-8"><?= lang('name') ?></th>
                                    <th class="col-xs-4"><?= lang('price_addition') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($product_variants as $pv) {
                                    echo '<tr><td class="col-xs-8"><input type="hidden" name="variant_id_' . $pv->id . '" value="' . $pv->id . '"><input type="text" name="variant_name_' . $pv->id . '" value="' . $pv->name . '" class="form-control"></td><td class="price text-right col-xs-4"><input type="text" name="variant_price_' . $pv->id . '" value="' . $pv->price . '" class="form-control"></td></tr>';
                                } ?>
                                </tbody>
                            </table>
                        <?php } ?>

                        <div class="form-group" style="margin-top: 14px;">
                            <input type="checkbox" class="checkbox" name="attributes" id="attributes" <?= $this->input->post('attributes') ? 'checked="checked"' : ''; ?>>
                            <label for="attributes" class="padding05" style="font-weight: 700;"><?= lang('add_more_variants'); ?></label>
                            <span class="text-muted">(<?= lang('eg_sizes_colors'); ?>)</span>
                        </div>

                        <div id="attr-con" <?= $this->input->post('attributes') ? '' : 'style="display:none;"'; ?>>
                            <div class="form-group" id="ui" style="margin-bottom: 0;">
                                <div class="input-group">
                                    <?php echo form_input('attributesInput', '', 'class="form-control select-tags" id="attributesInput" placeholder="' . $this->lang->line('enter_attributes') . '"'); ?>
                                    <div class="input-group-addon" style="padding: 2px 8px; background: #f1f5f9;">
                                        <a href="#" id="addAttributes">
                                            <i class="fa fa-2x fa-plus-circle text-success" id="addIcon"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="attrTable" class="table table-condensed table-striped" style="margin-bottom: 0; margin-top: 10px;">
                                    <thead>
                                    <tr class="active">
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('warehouse') ?></th>
                                        <th><?= lang('price_addition') ?></th>
                                        <th style="width: 30px; text-align: center;"><i class="fa fa-times attr-remove-all text-danger pointer"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($this->input->post('attributes')) {
                                        $a = sizeof($_POST['attr_name']);
                                        for ($r = 0; $r <= $a; $r++) {
                                            if (isset($_POST['attr_name'][$r]) && (isset($_POST['attr_warehouse'][$r]) || isset($_POST['attr_quantity'][$r]))) {
                                                echo '<tr class="attr">
                                                <td><input type="hidden" name="attr_name[]" value="' . $_POST['attr_name'][$r] . '"><span>' . $_POST['attr_name'][$r] . '</span></td>
                                                <td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . (isset($_POST['attr_warehouse'][$r]) ? $_POST['attr_warehouse'][$r] : '') . '"><input type="hidden" name="attr_wh_name[]" value="' . (isset($_POST['attr_wh_name'][$r]) ? $_POST['attr_wh_name'][$r] : '') . '"><span>' . (isset($_POST['attr_wh_name'][$r]) ? $_POST['attr_wh_name'][$r] : '') . '</span></td>
                                                <td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $_POST['attr_price'][$r] . '"><span>' . $_POST['attr_price'][$r] . '</span></td><td class="text-center"><i class="fa fa-times delAttr text-danger pointer"></i></td>
                                            </tr>';
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Section 4: Suppliers & Purchasing (الموردون والمشتريات) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-supplier">
                        <i class="fa fa-truck"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('suppliers_and_purchasing'); ?></h3>
                        <p class="spark-section-desc"><?= lang('suppliers_settings_desc'); ?></p>
                    </div>
                </div>
                <i class="fa fa-chevron-down spark-section-chevron"></i>
            </div>
            <div class="spark-advanced-card-body">
                <div class="form-group standard">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <label for="supplier" style="font-weight: 700; margin: 0;"><?= lang('supplier', 'supplier') ?></label>
                        <button type="button" class="btn btn-primary btn-xs" id="addSupplier" style="border-radius: 8px;">
                            <i class="fa fa-plus"></i> <?= lang('add_more_suppliers'); ?>
                        </button>
                    </div>
                    <div class="row" id="supplier-con">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control ' . ($product ? '' : 'suppliers') . '" id="' . ($product && !empty($product->supplier1) ? 'supplier1' : 'supplier') . '" placeholder="' . lang('select') . ' ' . lang('supplier') . '" style="width:100%;"'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= form_input('supplier_part_no', (isset($_POST['supplier_part_no']) ? $_POST['supplier_part_no'] : ''), 'class="form-control tip" id="supplier_part_no" placeholder="' . lang('supplier_part_no') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= form_input('supplier_price', (isset($_POST['supplier_price']) ? $_POST['supplier_price'] : ''), 'class="form-control tip" id="supplier_price" placeholder="' . lang('supplier_price') . '"'); ?>
                            </div>
                        </div>
                    </div>
                    <div id="ex-suppliers"></div>
                </div>
            </div>
        </div>

        <!-- Section 5: Gallery & Media (معرض الصور والملفات الرقمية) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-media">
                        <i class="fa fa-picture-o"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('product_gallery_and_media'); ?></h3>
                        <p class="spark-section-desc"><?= lang('media_settings_desc'); ?></p>
                    </div>
                </div>
                <i class="fa fa-chevron-down spark-section-chevron"></i>
            </div>
            <div class="spark-advanced-card-body">
                <div class="spark-advanced-grid-2">
                    
                    <!-- Gallery Images -->
                    <div class="form-group all">
                        <label for="images" style="font-weight: 700;"><?= lang('product_gallery_images', 'images') ?></label>
                        <input id="images" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile[]" multiple="true" data-show-upload="false" data-show-preview="false" class="form-control file" accept="image/*">
                        <div id="img-details" style="margin-top: 10px;"></div>
                    </div>

                    <!-- Digital File / Link -->
                    <div class="digital" style="display:none;">
                        <?php
                        if (filter_var($product->file, FILTER_VALIDATE_URL) === false) {
                            $file      = $product->file;
                            $file_link = '';
                        } else {
                            $file_link = $product->file;
                            $file      = '';
                        }
                        ?>
                        <div class="form-group digital">
                            <label for="digital_file" style="font-weight: 700;"><?= lang('digital_file', 'digital_file') ?></label>
                            <input id="digital_file" type="file" data-browse-label="<?= lang('browse'); ?>" name="digital_file" data-show-upload="false" data-show-preview="false" class="form-control file">
                        </div>
                        <div class="form-group digital">
                            <label for="file_link" style="font-weight: 700;"><?= lang('file_link', 'file_link'); ?></label>
                            <?= form_input('file_link', $file_link, 'class="form-control" id="file_link"'); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Section 6: Price Groups & Promotions (مجموعات الأسعار والعروض الترويجية) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-pricing">
                        <i class="fa fa-tags"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('price_groups_and_promotions'); ?></h3>
                        <p class="spark-section-desc"><?= lang('price_groups_desc'); ?></p>
                    </div>
                </div>
                <i class="fa fa-chevron-down spark-section-chevron"></i>
            </div>
            <div class="spark-advanced-card-body">
                
                <!-- Price Groups Checkbox & Box -->
                <div class="form-group standard">
                    <div style="margin-bottom: 10px;">
                        <?= form_checkbox('price_groups2', '1', '', 'class="form-control tip" id="price_groups2"'); ?>
                        <label for="price_groups2" class="padding05" style="font-weight: 700;"><?= lang('price_groups', 'price_groups'); ?></label>
                    </div>
                    
                    <div class="well well-sm" id="div_price_groups2" style="display:none;">
                        <input type="hidden" name="gp_count" id="gp_count" value="1" />
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label style="font-size: 12px;"><?= lang('Pricing_unit','product_unit_id2') ?></label>
                                    <?php
                                    $pun[''] = '';
                                    foreach ($base_units as $unit) {
                                        $pun[$unit->id] = $unit->name;
                                    }
                                    echo form_dropdown('product_unit_id2', $pun, '', 'id="product_unit_id2" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label style="font-size: 12px;"><?= lang('Conversion_factor', 'factor2') ?></label>
                                    <input type="text" class="form-control" name="factor2" id="factor2">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label style="font-size: 12px;"><?= lang('Pricing_code', 'pricing_code2'); ?></label>
                                    <div class="input-group">
                                        <?php echo form_input('pricing_code2', '', 'class="form-control" id="pricing_code2"'); ?>
                                        <div class="input-group-addon pointer" style="padding: 2px 10px;">
                                            <a href="#" id="genPurcCode"><i class="fa fa-cogs"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label style="font-size: 12px;"><?= lang('selling_price', 'selling_price2') ?></label>
                                    <input type="text" class="form-control" name="selling_price2" id="selling_price2">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label style="font-size: 12px;"><?= lang('price_group','price_group_id2') ?></label>
                                    <?php
                                    $cgs = [];
                                    foreach ($price_groups as $price_group) {
                                        $cgs[$price_group->id] = $price_group->name;
                                    }
                                    echo form_dropdown('price_group_id2', $cgs, $Settings->price_group, 'id="price_group_id2" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-3" style="margin-top:22px;">
                                <button type="button" class="btn btn-primary btn-xs" id="addPriceGroups" style="border-radius: 8px;">
                                    <i class="fa fa-plus"></i> <?= lang('add_price_group'); ?>
                                </button>
                            </div>
                        </div>
                        <div id="ex-products"></div>
                    </div>
                </div>

                <!-- Promotions Checkbox & Box -->
                <div class="form-group">
                    <div style="margin-bottom: 10px;">
                        <input type="checkbox" class="checkbox" value="1" name="promotion" id="promotion" <?= $this->input->post('promotion') || $product->promotion ? 'checked="checked"' : ''; ?>>
                        <label for="promotion" class="padding05" style="font-weight: 700;"><?= lang('promotion'); ?></label>
                    </div>
                    <div id="promo" <?= $product->promotion ? '' : 'style="display:none;"'; ?>>
                        <div class="well well-sm">
                            <div class="spark-advanced-grid-3">
                                <div class="form-group">
                                    <?= lang('promo_price', 'promo_price'); ?>
                                    <?= form_input('promo_price', set_value('promo_price', $product->promo_price ? $this->sma->formatDecimal($product->promo_price, 4) : ''), 'class="form-control tip" id="promo_price"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('start_date', 'start_date'); ?>
                                    <?= form_input('start_date', set_value('start_date', $product->start_date ? $this->sma->hrsd($product->start_date) : ''), 'class="form-control tip date" id="start_date"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('end_date', 'end_date'); ?>
                                    <?= form_input('end_date', set_value('end_date', $product->end_date ? $this->sma->hrsd($product->end_date) : ''), 'class="form-control tip date" id="end_date"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Section 7: Additional Specs & Custom Fields (المواصفات والحقول الإضافية) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-specs">
                        <i class="fa fa-sliders"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('additional_specifications_and_custom_fields'); ?></h3>
                        <p class="spark-section-desc"><?= lang('specs_desc'); ?></p>
                    </div>
                </div>
                <i class="fa fa-chevron-down spark-section-chevron"></i>
            </div>
            <div class="spark-advanced-card-body">
                <div class="spark-advanced-grid-3">

                    <div class="form-group all">
                        <label for="slug" style="font-weight: 700;"><?= lang('slug', 'slug'); ?></label>
                        <?= form_input('slug', set_value('slug', ($product ? $product->slug : '')), 'class="form-control tip" id="slug" required="required"'); ?>
                    </div>

                    <div class="form-group standard_combo">
                        <label for="weight" style="font-weight: 700;"><?= lang('weight', 'weight'); ?></label>
                        <?= form_input('weight', set_value('weight', ($product ? $product->weight : '')), 'class="form-control tip" id="weight"'); ?>
                    </div>

                    <input type="hidden" name="barcode_symbology" id="barcode_symbology" value="<?= (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product && $product->barcode_symbology ? $product->barcode_symbology : 'code128')) ?>">

                    <?php if ($Settings->product_expiry) { ?>
                        <div class="form-group">
                            <label for="expiry" style="font-weight: 700;"><?= lang('expiry', 'expiry'); ?></label>
                            <?= form_input('expiry', set_value('expiry', $product->expiry ? $this->sma->hrsd($product->expiry) : ''), 'class="form-control tip date" id="expiry"'); ?>
                        </div>
                    <?php } ?>

                </div>

                <!-- Visibility & Feature Checkboxes -->
                <div class="row" style="margin-top: 10px; margin-bottom: 16px;">
                    <?php if (isset($this->Settings->isStore) && $this->Settings->isStore == 1) { ?>
                        <div class="col-sm-4 form-group">
                            <input name="featured" type="checkbox" class="checkbox" id="featured" value="1" <?= empty($product->featured) ? '' : 'checked="checked"' ?>/>
                            <label for="featured" class="padding05"><?= lang('featured') ?></label>
                        </div>
                    <?php } ?>
                    <div class="col-sm-4 form-group">
                        <input name="hide_pos" type="checkbox" class="checkbox" id="hide_pos" value="1" <?= empty($product->hide_pos) ? '' : 'checked="checked"' ?>/>
                        <label for="hide_pos" class="padding05"><?= lang('hide_in_pos') ?></label>
                    </div>
                    <div class="col-sm-4 form-group">
                        <input name="hide" type="checkbox" class="checkbox" id="hide" value="1" <?= empty($product->hide) ? '' : 'checked="checked"' ?>/>
                        <label for="hide" class="padding05"><?= lang('product_is_inactive') ?></label>
                    </div>
                </div>

                <!-- Custom Fields (CF1 - CF6) -->
                <div class="form-group">
                    <input name="cf" type="checkbox" class="checkbox" id="extras" value="" <?= isset($_POST['cf']) || ($product && ($product->cf1 || $product->cf2 || $product->cf3 || $product->cf4 || $product->cf5 || $product->cf6)) ? 'checked="checked"' : '' ?>/>
                    <label for="extras" class="padding05" style="font-weight: 700;"><?= lang('custom_fields') ?></label>
                </div>
                <div class="row" id="extras-con" style="<?= isset($_POST['cf']) || ($product && ($product->cf1 || $product->cf2 || $product->cf3 || $product->cf4 || $product->cf5 || $product->cf6)) ? '' : 'display:none;' ?>">
                    <div class="col-md-4">
                        <div class="form-group all">
                            <?= lang('pcf1', 'cf1') ?>
                            <?= form_input('cf1', (isset($_POST['cf1']) ? $_POST['cf1'] : ($product ? $product->cf1 : '')), 'class="form-control tip" id="cf1"') ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group all">
                            <?= lang('pcf2', 'cf2') ?>
                            <?= form_input('cf2', (isset($_POST['cf2']) ? $_POST['cf2'] : ($product ? $product->cf2 : '')), 'class="form-control tip" id="cf2"') ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group all">
                            <?= lang('pcf3', 'cf3') ?>
                            <?= form_input('cf3', (isset($_POST['cf3']) ? $_POST['cf3'] : ($product ? $product->cf3 : '')), 'class="form-control tip" id="cf3"') ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group all">
                            <?= lang('pcf4', 'cf4') ?>
                            <?= form_input('cf4', (isset($_POST['cf4']) ? $_POST['cf4'] : ($product ? $product->cf4 : '')), 'class="form-control tip" id="cf4"') ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group all">
                            <?= lang('pcf5', 'cf5') ?>
                            <?= form_input('cf5', (isset($_POST['cf5']) ? $_POST['cf5'] : ($product ? $product->cf5 : '')), 'class="form-control tip" id="cf5"') ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group all">
                            <?= lang('pcf6', 'cf6') ?>
                            <?= form_input('cf6', (isset($_POST['cf6']) ? $_POST['cf6'] : ($product ? $product->cf6 : '')), 'class="form-control tip" id="cf6"') ?>
                        </div>
                    </div>
                </div>

                <!-- Detailed Textareas -->
                <div class="spark-advanced-grid-2" style="margin-top: 16px;">
                    <div class="form-group all">
                        <label for="product_details" style="font-weight: 700;"><?= lang('product_details', 'product_details') ?></label>
                        <?= form_textarea('product_details', (isset($_POST['product_details']) ? $_POST['product_details'] : ($product ? $product->product_details : '')), 'class="form-control" id="product_details" rows="3"'); ?>
                    </div>
                    <div class="form-group all">
                        <label for="details" style="font-weight: 700;"><?= lang('product_details_for_invoice', 'details') ?></label>
                        <?= form_textarea('details', (isset($_POST['details']) ? $_POST['details'] : ($product ? $product->details : '')), 'class="form-control" id="details" rows="3"'); ?>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         SUBMIT BAR: Actions & Primary Button
         ══════════════════════════════════════════════════════════════════════════ -->
    <div class="spark-product-submit-bar">
        <a href="<?= admin_url('products'); ?>" class="btn btn-default" style="border-radius: 12px; font-weight: 700; padding: 10px 20px;">
            <?= lang('cancel'); ?>
        </a>
        <button type="submit" name="edit_product" id="edit_product" class="spark-btn-product-submit">
            <i class="fa fa-check-circle"></i> <?= $this->lang->line('edit_product'); ?>
        </button>
    </div>

</div>

<?= form_close(); ?>

<!-- ══════════════════════════════════════════════════════════════════════════
     DYNAMIC JS HANDLERS: Advanced Toggle, Image Dropzone, Select2 & Logic
     ══════════════════════════════════════════════════════════════════════════ -->
<script type="text/javascript">
$(document).ready(function () {
    
    // ─── 1. Toggle "تفاصيل إضافية" (Advanced Details) ───
    $('#btn-toggle-advanced').on('click', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $sections = $('#spark-advanced-sections');
        $sections.slideToggle(300, function () {
            if ($sections.is(':visible')) {
                $btn.addClass('is-active');
                $btn.find('.toggle-text').text('<?= lang('hide_additional_details'); ?>');
            } else {
                $btn.removeClass('is-active');
                $btn.find('.toggle-text').text('<?= lang('additional_details'); ?>');
            }
        });
    });

    // Individual Collapsible Card Accordion Header
    $(document).on('click', '.spark-advanced-card-header', function () {
        $(this).closest('.spark-advanced-card').toggleClass('is-collapsed');
    });

    // ─── 2. Modern Product Image Dropzone & Preview (Fallback to no_image.png) ───
    var defaultNoImg = $('#spark-img-preview').data('default-src') || '<?= base_url('assets/uploads/no_image.png') ?>';
    var $dropzone = $('#spark-dropzone-container');
    var $fileInput = $('#product_image');

    if ($dropzone.length) {
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
            $dropzone[0].addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        // Highlight dropzone on dragover
        ['dragenter', 'dragover'].forEach(function (eventName) {
            $dropzone[0].addEventListener(eventName, function () {
                $dropzone.addClass('is-dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            $dropzone[0].addEventListener(eventName, function () {
                $dropzone.removeClass('is-dragover');
            }, false);
        });

        // Handle dropped files
        $dropzone[0].addEventListener('drop', function (e) {
            var dt = e.dataTransfer;
            var files = dt.files;
            if (files && files.length > 0) {
                $fileInput[0].files = files;
                $fileInput.trigger('change');
            }
        }, false);
    }

    // Handle file input change (Live FileReader preview)
    $fileInput.on('change', function () {
        if (this.files && this.files[0]) {
            var file = this.files[0];
            
            // Validate max size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                bootbox.alert('<?= lang('file_too_large'); ?>');
                $(this).val('');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#spark-img-preview').attr('src', e.target.result);
                $('#spark-img-filename').text(file.name);
                $('#spark-img-meta').css('display', 'flex');
                $('#spark-img-remove-btn').css('display', 'inline-flex');
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle remove image (Reset to default fallback no_image.png)
    $('#spark-img-remove-btn').on('click', function (e) {
        e.preventDefault();
        $fileInput.val('');
        $('#spark-img-preview').attr('src', defaultNoImg);
        $('#spark-img-filename').text('');
        $('#spark-img-meta').hide();
        $(this).hide();
    });

    // Auto-open Advanced Details if Error or Existing Advanced Data in Product
    <?php if ($error || (isset($_POST['tax_rate']) && $_POST['tax_rate'] != '') || isset($_POST['attributes']) || isset($_POST['supplier']) || isset($_POST['cf']) || isset($_POST['price_groups2']) || ($product && ($product->brand || $product->supplier1 || $product->cf1 || $product->promotion || !empty($product_variants) || !empty($product_options)))) { ?>
        $('#spark-advanced-sections').show();
        $('#btn-toggle-advanced').addClass('is-active');
        $('#btn-toggle-advanced .toggle-text').text('<?= lang('hide_additional_details'); ?>');
    <?php } ?>

    // ─── 3. Tax Rate Change Handler & Trigger ───
    $('#tax_rate').change(function () {
        $("#div_tax_method").css("display", "block");
        try {
            $.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'products/getTax_Rate/' + $('#tax_rate').val(),
                dataType: 'json',
                data: { id : $('#tax_rate').val()},
                success: function(data, page) {
                    var currentMethod = '<?= $product ? $product->tax_method : '' ?>';
                    if (data > 0) {
                        if (currentMethod === '' || currentMethod === null) {
                            $('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
                        }
                        $('#tax_method').select2('readonly', false);
                    } else {
                        $('#tax_method').select2('readonly', true);
                    }
                }
            });
        } catch(err) { alert(err); }
    });

    // ─── 4. Branch & Tax Initialization Trigger ───
    if ($('#brId').length && $('#brId').val()) {
        $('#brId').trigger('change');
    }

    if ($('#tax_rate').length && $('#tax_rate').val()) {
        $('#tax_rate').trigger('change');
    }

    $('form[data-toggle="validator"]').bootstrapValidator({ excluded: [':disabled'] });
    var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
    var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    var items = {};
    <?php
    if ($combo_items) {
        echo '
            var ci = ' . json_encode($combo_items) . ';
            $.each(ci, function() { add_product_item(this); });
            ';
    }
    ?>
    <?= isset($_POST['cf']) ? '$("#extras").iCheck("check");' : '' ?>

    // ─── Warehouses Select All & Auto-Sync ───
    var syncingWarehouses = false;

    function syncWarehouseSelectAll() {
        if (syncingWarehouses) return;
        syncingWarehouses = true;

        var $warehouse = $('#pwarehouse');
        var $options = $warehouse.find('option').filter(function() {
            return $(this).val() && $(this).val() !== '';
        });
        var $selected = $warehouse.find('option:selected').filter(function() {
            return $(this).val() && $(this).val() !== '';
        });

        if ($options.length > 0 && $selected.length === $options.length) {
            if ($('#all').data('iCheck')) {
                $('#all').iCheck('check');
            } else {
                $('#all').prop('checked', true);
            }
        } else {
            if ($('#all').data('iCheck')) {
                $('#all').iCheck('uncheck');
            } else {
                $('#all').prop('checked', false);
            }
        }

        syncingWarehouses = false;
    }

    $(document).on('ifChecked', '#all', function (e) {
        if (syncingWarehouses) return;
        syncingWarehouses = true;
        var allVals = [];
        $('#pwarehouse option').each(function() {
            if ($(this).val() && $(this).val() !== '') {
                allVals.push($(this).val());
            }
        });
        $('#pwarehouse option').prop('selected', true);
        if ($('#pwarehouse').hasClass('select2-offscreen') || $('#pwarehouse').data('select2')) {
            $('#pwarehouse').select2('val', allVals).trigger('change');
        } else {
            $('#pwarehouse').val(allVals).trigger('change');
        }
        syncingWarehouses = false;
    });

    $(document).on('ifUnchecked', '#all', function (e) {
        if (syncingWarehouses) return;
        syncingWarehouses = true;
        $('#pwarehouse option').prop('selected', false);
        if ($('#pwarehouse').hasClass('select2-offscreen') || $('#pwarehouse').data('select2')) {
            $('#pwarehouse').select2('val', []).trigger('change');
        } else {
            $('#pwarehouse').val([]).trigger('change');
        }
        syncingWarehouses = false;
    });

    $(document).on('change', '#all', function (e) {
        if ($('#all').data('iCheck')) return;
        if (syncingWarehouses) return;
        if ($(this).is(':checked')) {
            syncingWarehouses = true;
            var allVals = [];
            $('#pwarehouse option').each(function() {
                if ($(this).val() && $(this).val() !== '') {
                    allVals.push($(this).val());
                }
            });
            $('#pwarehouse option').prop('selected', true);
            if ($('#pwarehouse').hasClass('select2-offscreen') || $('#pwarehouse').data('select2')) {
                $('#pwarehouse').select2('val', allVals).trigger('change');
            } else {
                $('#pwarehouse').val(allVals).trigger('change');
            }
            syncingWarehouses = false;
        } else {
            syncingWarehouses = true;
            $('#pwarehouse option').prop('selected', false);
            if ($('#pwarehouse').hasClass('select2-offscreen') || $('#pwarehouse').data('select2')) {
                $('#pwarehouse').select2('val', []).trigger('change');
            } else {
                $('#pwarehouse').val([]).trigger('change');
            }
            syncingWarehouses = false;
        }
    });

    $('#pwarehouse').on('change change.select2', function(e) {
        syncWarehouseSelectAll();
    });

    // Check initial warehouses state on load
    setTimeout(function() {
        syncWarehouseSelectAll();
    }, 100);

    <?= isset($_POST['cf']) || ($product && ($product->cf1 || $product->cf2 || $product->cf3 || $product->cf4 || $product->cf5 || $product->cf6)) ? '$("#extras").iCheck("check");' : '' ?>
    <?= isset($_POST['price_groups2']) ? '$("#price_groups2").iCheck("check");' : '' ?>

    $(document).on('ifChecked change', '#extras', function () {
        if ($(this).is(':checked') || $(this).prop('checked')) {
            $('#extras-con').slideDown();
        }
    });
    $(document).on('ifUnchecked change', '#extras', function () {
        if (!$(this).is(':checked') && !$(this).prop('checked')) {
            $('#extras-con').slideUp();
        }
    });

    $(document).on('ifChecked change', '#price_groups2', function () {
        if ($(this).is(':checked') || $(this).prop('checked')) {
            $('#div_price_groups2').slideDown();
            if ($('#product_unit_id2').length && !$('#product_unit_id2').hasClass('select2-offscreen')) {
                $('#product_unit_id2').select2({minimumResultsForSearch: 7});
            }
            if ($('#price_group_id2').length && !$('#price_group_id2').hasClass('select2-offscreen')) {
                $('#price_group_id2').select2({minimumResultsForSearch: 7});
            }
        }
    });
    $(document).on('ifUnchecked change', '#price_groups2', function () {
        if (!$(this).is(':checked') && !$(this).prop('checked')) {
            $('#div_price_groups2').slideUp();
        }
    });

    // Check on page load
    if ($('#extras').length && ($('#extras').is(':checked') || $('#extras').prop('checked'))) {
        $('#extras-con').show();
        $('#spark-advanced-sections').show();
        $('#btn-toggle-advanced').addClass('is-active');
        $('#btn-toggle-advanced .toggle-text').text('<?= lang('hide_additional_details'); ?>');
    }
    if ($('#price_groups2').length && ($('#price_groups2').is(':checked') || $('#price_groups2').prop('checked'))) {
        $('#div_price_groups2').show();
        if ($('#product_unit_id2').length) {
            $('#product_unit_id2').select2({minimumResultsForSearch: 7});
        }
        if ($('#price_group_id2').length) {
            $('#price_group_id2').select2({minimumResultsForSearch: 7});
        }
        $('#spark-advanced-sections').show();
        $('#btn-toggle-advanced').addClass('is-active');
        $('#btn-toggle-advanced .toggle-text').text('<?= lang('hide_additional_details'); ?>');
    }

    if ($('#promotion').length) {
        <?= isset($_POST['promotion']) || $product->promotion ? '$("#promotion").iCheck("check");' : '' ?>
        $('#promotion').on('ifChecked', function (e) {
            $('#promo').slideDown();
        });
        $('#promotion').on('ifUnchecked', function (e) {
            $('#promo').slideUp();
        });
    }

    $('.attributes').on('ifChecked', function (event) {
        $('#options_' + $(this).attr('id')).slideDown();
    });
    $('.attributes').on('ifUnchecked', function (event) {
        $('#options_' + $(this).attr('id')).slideUp();
    });

    $('#type').change(function () {
        var t = $(this).val();
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#unit').attr('disabled', true);
            if ($('#cost').length) { $('#cost').attr('disabled', true); }
        } else {
            $('.standard').slideDown();
            $('#unit').attr('disabled', false);
            if ($('#cost').length) { $('#cost').attr('disabled', false); }
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
        } else {
            $('.digital').slideDown();
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
        } else {
            $('.combo').slideDown();
        }
        if (t == 'standard' || t == 'combo') {
            $('.standard_combo').slideDown();
            $('#unit').removeAttr('disabled');
        } else {
            $('.standard_combo').slideUp();
        }
    });

    $("#add_item").autocomplete({
        source: '<?= admin_url('products/suggestions3'); ?>',
        minLength: 1,
        autoFocus: false,
        delay: 5,
        response: function (event, ui) {
            if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                bootbox.alert('<?= lang('no_product_found') ?>', function () {
                    $('#add_item').focus();
                });
                $(this).val('');
            }
            else if (ui.content.length == 1 && ui.content[0].id != 0) {
                ui.item = ui.content[0];
                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                $(this).autocomplete('close');
            }
            else if (ui.content.length == 1 && ui.content[0].id == 0) {
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
                    $('#add_item').removeAttr('required');
                    $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
                }
            } else {
                bootbox.alert('<?= lang('no_product_found') ?>');
            }
        }
    });
    $('#add_item').removeAttr('required');
    $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');

    function add_product_item(item) {
        if (item == null) {
            return false;
        }
        item_id = item.id;
        if (items[item_id]) {
            items[item_id].qty = (parseFloat(items[item_id].qty) + 1);
        } else {
            items[item_id] = item;
        }
        var pp = 0;
        $("#prTable tbody").empty();
        $.each(items, function () {
            var row_no = this.id;
            var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '"></tr>');
            tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
            tr_html += '<td><span id="unit_' + row_no + '">' + this.unit + '</span></td>';
            tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatQuantity2(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td><input disabled class="form-control text-center rprice2" name="combo_item_price[]" type="text" value="' + formatDecimal(this.price,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<input class="form-control text-center rprice" name="combo_item_price[]" type="hidden" value="' + formatDecimal(this.price,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();">';
            tr_html += '<td class="text-center"><i class="fa fa-times tip del text-danger pointer" id="' + row_no + '" title="<?= lang('remove'); ?>"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#prTable");
            pp += formatDecimal(parseFloat(this.price)*parseFloat(this.qty),4);
        });
        $('.item_' + item_id).addClass('warning');
        if ($('#cost').length) { $('#cost').val(pp); }
        return true;
    }

    function calculate_price() {
        var rows = $('#prTable').children('tbody').children('tr');
        var pp = 0;
        $.each(rows, function () {
            pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()),4);
        });
        if ($('#cost').length) { $('#cost').val(pp); }
        return true;
    }

    $(document).on('change', '.rquantity, .rprice', function () {
        calculate_price();
    });

    $(document).on('click', '.del', function () {
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

    var variants = <?= json_encode($vars); ?>;
    if ($(".select-tags").length) {
        $(".select-tags").select2({
            tags: variants,
            tokenSeparators: [","],
            multiple: true
        });
    }

    $(document).on('ifChecked change', '#attributes', function (e) {
        if ($(this).is(':checked') || $(this).prop('checked')) {
            $('#attr-con').slideDown();
            if ($(".select-tags").length && !$(".select-tags").hasClass("select2-offscreen")) {
                $(".select-tags").select2({
                    tags: variants,
                    tokenSeparators: [","],
                    multiple: true
                });
            }
        }
    });

    $(document).on('ifUnchecked change', '#attributes', function (e) {
        if (!$(this).is(':checked') && !$(this).prop('checked')) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-con').slideUp();
        }
    });

    // Check on page load
    if ($('#attributes').length && ($('#attributes').is(':checked') || $('#attributes').prop('checked') || <?= ($product_options || $product_variants || $this->input->post('attributes')) ? 'true' : 'false' ?>)) {
        $('#attributes').prop('checked', true);
        try { $('#attributes').iCheck('check'); } catch(e){}
        $('#attr-con').show();
        $('#spark-advanced-sections').show();
        $('#btn-toggle-advanced').addClass('is-active');
        $('#btn-toggle-advanced .toggle-text').text('<?= lang('hide_additional_details'); ?>');
    }

    $('#addAttributes').click(function (e) {
        e.preventDefault();
        var attrs_val = $('#attributesInput').val(), attrs;
        attrs = attrs_val.split(',');
        for (var i in attrs) {
            if (attrs[i] !== '') {
                try {
                    var w_id = [];
                    var w_name = [];
                    var j = 0;
                    $('#pwarehouse :selected').each(function() {
                        w_id[j] = $(this).val();
                        w_name[j] = $(this).text();
                        $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="'+ w_id[j] +'"><span>'+ w_name[j] +'</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></td><td class="text-center"><i class="fa fa-times delAttr text-danger pointer"></i></td></tr>');
                        j++;
                    });
                } catch(err) { alert(err); }
            }
        }
    });

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
        $('#aquantity').val(row.children().eq(2).find('span').text());
        $('#aprice').val(row.children().eq(3).find('span').text());
        $('#aModal').appendTo('body').modal('show');
    });

    $(document).on('click', '#updateAttr', function () {
        var wh = $('#awarehouse').val(), wh_name;
        $.each(warehouses, function () {
            if (this.id == wh) {
                wh_name = this.name;
            }
        });
        row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" value="' + wh_name + '"><span>' + wh_name + '</span>');
        row.children().eq(2).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
        $('#aModal').modal('hide');
    });
});

<?php if ($product) { ?>
$(document).ready(function () {
    $('#enable_wh').click(function () {
        var whs = $('.wh');
        $.each(whs, function () {
            $(this).val($('#v' + $(this).attr('id')).val());
        });
        $('#warehouse_quantity').val(1);
        $('.wh').attr('disabled', false);
        $('#show_wh_edit').slideDown();
    });
    $('#disable_wh').click(function () {
        $('#warehouse_quantity').val(0);
        $('#show_wh_edit').slideUp();
    });
    $('#show_wh_edit').hide();
    $('.wh').attr('disabled', true);
    var t = "<?=$product->type?>";
    if (t !== 'standard') {
        $('.standard').slideUp();
        $('#unit').attr('disabled', true);
        if ($('#cost').length) { $('#cost').attr('disabled', true); }
    } else {
        $('.standard').slideDown();
        $('#unit').attr('disabled', false);
        if ($('#cost').length) { $('#cost').attr('disabled', false); }
    }
    if (t !== 'digital') {
        $('.digital').slideUp();
    } else {
        $('.digital').slideDown();
    }
    if (t !== 'combo') {
        $('.combo').slideUp();
    } else {
        $('.combo').slideDown();
    }
    if (t == 'standard' || t == 'combo') {
        $('.standard_combo').slideDown();
        $('#unit').removeAttr('disabled');
    } else {
        $('.standard_combo').slideUp();
    }
    $('#add_item').removeAttr('required');
    $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
    $("#product_image").parent('.form-group').addClass("text-warning");
    $("#images").parent('.form-group').addClass("text-warning");

    <?php if ($product->supplier1) { ?>
        select_supplier('supplier1', "<?= $product->supplier1; ?>");
        $('#supplier_price').val("<?= $this->sma->formatDecimal($product->supplier1price); ?>");
        $('#supplier_part_no').val("<?= $product->supplier1_part_no; ?>");
    <?php } else { ?>
        $('#supplier1').addClass('rsupplier');
    <?php } ?>

    <?php if ($product->supplier2) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_2', "<?= $product->supplier2; ?>");
        $('#supplier_2_price').val("<?= $this->sma->formatDecimal($product->supplier2price); ?>");
        $('#supplier_2_part_no').val("<?= $product->supplier2_part_no; ?>");
    <?php } ?>

    <?php if ($product->supplier3) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_3', "<?= $product->supplier3; ?>");
        $('#supplier_3_price').val("<?= $this->sma->formatDecimal($product->supplier3price); ?>");
        $('#supplier_3_part_no').val("<?= $product->supplier3_part_no; ?>");
    <?php } ?>

    <?php if ($product->supplier4) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_4', "<?= $product->supplier4; ?>");
        $('#supplier_4_price').val("<?= $this->sma->formatDecimal($product->supplier4price); ?>");
        $('#supplier_4_part_no').val("<?= $product->supplier4_part_no; ?>");
    <?php } ?>

    <?php if ($product->supplier5) { ?>
        $('#addSupplier').click();
        select_supplier('supplier_5', "<?= $product->supplier5; ?>");
        $('#supplier_5_price').val("<?= $this->sma->formatDecimal($product->supplier5price); ?>");
        $('#supplier_5_part_no').val("<?= $product->supplier5_part_no; ?>");
    <?php } ?>

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
                        return {results: [{id: '', text: '<?= lang('no_match_found') ?>'}]};
                    }
                }
            }
        });
    }
});
<?php } ?>

$(document).ready(function () {
    $('#enable_wh').trigger('click');
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
                    $('#default_sale_unit').select2('val', '<?= $product->sale_unit ?>' || v);
                    $('#default_purchase_unit').select2('val', '<?= $product->purchase_unit ?>' || v);
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
    $('#digital_file').removeAttr('required');
    $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'digital_file');
});
</script>

<div class="modal" id="aModal" tabindex="-1" role="dialog" aria-labelledby="aModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i class="fa fa-2x">&times;</i></span><span class="sr-only"><?= lang('close'); ?></span></button>
                <h4 class="modal-title" id="aModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="awarehouse" class="col-sm-4 control-label"><?= lang('warehouse') ?></label>
                        <div class="col-sm-8">
                            <?php
                            $wh = [];
                            foreach ($warehouses as $warehouse) {
                                $wh[$warehouse->id] = $warehouse->name;
                            }
                            echo form_dropdown('warehouse', $wh, '', 'id="awarehouse" class="form-control"');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="aprice" class="col-sm-4 control-label"><?= lang('price') ?></label>
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

<script>
$(document).ready(function () {
    $("#wh_div").css("display", "none");

    var gp = 2;
    $('#addPriceGroups').click(function () {
        if (gp <= 5) {
            var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-lg-12"><div class="col-sm-3"><div class="form-group"><input type="hidden" name="product_unit_id2_' + gp + '", class="form-control" id="product_unit_id2_' + gp + '" placeholder="<?= lang('select') . ' ' . lang('Pricing_unit') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-sm-3"><div class="form-group"><input type="number" min="1" step="1" name="factor2_' + gp + '" class="form-control tip" id="factor2_' + gp + '" placeholder="<?= lang('Conversion_factor') ?>" /></div></div><div class="col-sm-3"><div class="form-group"><div class="input-group"><input type="text" name="pricing_code2_' + gp + '" class="form-control tip" id="pricing_code2_' + gp + '" placeholder="<?= lang('Pricing_code') ?>" /><div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genPurcCode' + gp + '"><i class="fa fa-cogs"></i></a></div></div></div></div><div class="col-sm-3"><div class="form-group"><input type="number" min=".01" step=".01" name="selling_price2_' + gp + '" class="form-control tip" id="selling_price2_' + gp + '" placeholder="<?= lang('selling_price') ?>" /></div></div></div></div>';
            $('#ex-products').append(html);
            var gpp = $('#product_unit_id2_' + gp);
            baseunits(gpp);
            $('#gp_count').val(gp);
            $('#add_checkbox2_' + gp).not('.skip').iCheck({
                checkboxClass: 'icheckbox_square-blue ',
                increaseArea: '20%',
            });
            $('#add_checkbox2_' + gp).iCheck('check');
            $('#factor2_' + gp + '').focus();
            gp++;
        } else {
            alert('<?= lang('max_reached') ?>');
            return false;
        }
    });

    $('#genPurcCode').click(function() {
        var no = generateCardNo(8);
        $(this).parent().parent('.input-group').children('input').val(no);
        return false;
    });
});

$(document).on('click', '#genPurcCode2', function (e) {
    var no = generateCardNo(8);
    $(this).parent().parent('.input-group').children('input').val(no);
    return false;
});
$(document).on('click', '#genPurcCode3', function (e) {
    var no = generateCardNo(8);
    $(this).parent().parent('.input-group').children('input').val(no);
    return false;
});
$(document).on('click', '#genPurcCode4', function (e) {
    var no = generateCardNo(8);
    $(this).parent().parent('.input-group').children('input').val(no);
    return false;
});
$(document).on('click', '#genPurcCode5', function (e) {
    var no = generateCardNo(8);
    $(this).parent().parent('.input-group').children('input').val(no);
    return false;
});
</script>
