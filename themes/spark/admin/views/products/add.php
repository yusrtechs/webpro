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
        placeholder: "<?= lang('select_category_to_load') ?>", minimumResultsForSearch: 7, data: [
            {id: '', text: '<?= lang('select_category_to_load') ?>'}
        ]
    });

    $('#category').change(function () {
        var v = $(this).val();
        try{
            var brId = $('#brId').val() || "1";
        }catch(err){
            var brId = "1";
        }

        $('#modal-loading').show();
        if (v) {
            $.ajax({
                type: "get",
                async: false,
                url: "<?= admin_url('products/getSubCategories') ?>/" + v +"/"+ brId,
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

    $('#grpId').change(function () {
        var v = $(this).val();
        var uopt = $("#coId");

        if (v) {
            $.ajax({
                type: "get",
                async: false,
                url: "<?= admin_url('branches/getCompaniesBygrpId') ?>/" + v,
                dataType: "json",
                success: function (scdata) {
                    if (scdata != null) {
                        $('#coId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>').val('');
                        $('#brId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>').val('');
                        $('#coId').val('').trigger('change.select2');
                        $('#brId').val('').trigger('change.select2');
                        $('#pwarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#category').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#brand').select2("destroy").empty().select2({minimumResultsForSearch: 7});

                        $.each(scdata, function () {
                            $("<option />", {value: this.id, text: this.text}).appendTo(uopt);
                        });
                    } else {
                        $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                            placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                            scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                        });
                        $('#pwarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                    }
                },
                error: function () {
                    bootbox.alert('<?= lang('ajax_error') ?>');
                }
            });
        } else {
            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
            });
        }
    });

    $('#coId').change(function () {
        var v = $(this).val();
        var uopt = $("#brId");

        if (v) {
            $.ajax({
                type: "get",
                async: false,
                url: "<?= admin_url('branches/getBranchesBycoId') ?>/" + v,
                dataType: "json",
                success: function (scdata) {
                    if (scdata != null) {
                        $('#brId').find('option').remove().end().append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>').val('');
                        $('#brId').val('').trigger('change.select2');
                        $('#pwarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#category').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#brand').select2("destroy").empty().select2({minimumResultsForSearch: 7});

                        $.each(scdata, function () {
                            $("<option />", {value: this.id, text: this.text}).appendTo(uopt);
                        });
                    } else {
                        $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                            placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                            scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                        });
                        $('#pwarehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                    }
                },
                error: function () {
                    bootbox.alert('<?= lang('ajax_error') ?>');
                }
            });
        } else {
            $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
            });
        }
    });

    $('#brId').change(function () {
        var v = $(this).val();
        var uopt = $("#unit");
        var uopt1 = $("#pwarehouse");
        var uopt2 = $("#category");
        var uopt3 = $("#brand");

        var saved_warehouses = <?= json_encode(!empty($whselected) ? array_values(array_map('strval', $whselected)) : []) ?>;

        if (v) {
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
                            // Priority 1: Saved original product warehouse(s) that exist in this branch
                            $('#pwarehouse').val(validSavedWh).trigger('change.select2');
                        } else if (saved_warehouses && saved_warehouses.length > 0) {
                            // Saved warehouse exists on original product
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
                        if (scdata.length > 0) {
                            $('#unit').val(scdata[0].id).trigger('change.select2');
                        }
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
                                    $('#default_sale_unit').select2('val', vu);
                                    $('#default_purchase_unit').select2('val', vu);
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
                        if (scdata.length > 0) {
                            $('#category').val(scdata[0].id).trigger('change.select2');
                        }
                        var vc = $('#category').val();

                        if (vc) {
                            $.ajax({
                                type: "get",
                                async: false,
                                url: "<?= admin_url('products/getSubCategories') ?>/" + vc +"/"+ v,
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
                        if (scdata.length > 0) {
                            $('#brand').val(scdata[0].id).trigger('change');
                        }
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
        } else {
            $("#pwarehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
            });
            $("#unit").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('unit') ?>").select2({allowClear: false,
                placeholder: "<?= lang('select') . ' ' . lang('unit') ?>",
                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('unit') ?>'}]
            });
            $("#category").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('category') ?>").select2({allowClear: false,
                placeholder: "<?= lang('select') . ' ' . lang('category') ?>",
                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('category') ?>'}]
            });
            $("#brand").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('brand') ?>").select2({allowClear: false,
                placeholder: "<?= lang('select') . ' ' . lang('brand') ?>",
                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('brand') ?>'}]
            });
        }
    });
});
</script>

<?php
$attrib = ['data-toggle' => 'validator', 'role' => 'form', 'class' => 'spark-product-form'];
echo admin_form_open_multipart('products/add', $attrib);
?>

<div class="spark-product-module">
    
    <!-- Modern Header Banner -->
    <div class="spark-product-banner">
        <div class="spark-product-banner-content">
            <div class="spark-product-banner-badge">
                <i class="fa fa-cube"></i>
            </div>
            <div>
                <h1 class="spark-product-banner-title"><?= lang('add_product'); ?></h1>
                <p class="spark-product-banner-subtitle"><?php echo lang('enter_info'); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= admin_url('products'); ?>" class="btn btn-default" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); color: #fff; border-radius: 12px; padding: 8px 16px; font-weight: 700;">
                <i class="fa fa-arrow-right" style="margin-inline-end: 4px;"></i> <?= lang('back_to_products'); ?>
            </a>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════
         CORE CARD: Simple by Default (Essential Fields + Fallback Image Dropzone)
         ══════════════════════════════════════════════════════════════════════════ -->
    <div class="spark-product-core-card">
        <div class="spark-product-core-layout">
            
            <!-- Left Column: Core Fields Grid -->
            <div class="spark-core-fields-grid">
                
                <!-- Product Name (Highlighted Field) -->
                <div class="form-group all spark-field-full spark-field-primary-name">
                    <label for="name" style="font-weight: 800; font-size: 14px; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                        <i class="fa fa-tag text-success"></i> <?= lang('product_name') ?> 
                    </label>
                    <?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control gen_slug" id="name" required="required" placeholder="' . lang('enter_product_name') . '"'); ?>
                </div>

                <!-- Product Types -->
                <div class="form-group">
                    <label for="type" style="font-weight: 700;"><?= lang('product') ?> </label>
                    <?php
                    $opts = ['standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service'), 'insurance' => lang('insurance')];
                    echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
                    ?>
                </div>

                <div class="form-group">
                    <label for="type2" style="font-weight: 700;"><?= lang('product_type') ?> </label>
                    <?php
                    $opts = ['primary' => lang('primary'), 'complete' => lang('complete'), 'service' => lang('service_product')];
                    echo form_dropdown('type2', $opts, (isset($_POST['type2']) ? $_POST['type2'] : ($product ? $product->type2 : '')), 'class="form-control" id="type2" required="required"');
                    ?>
                </div>

                <!-- Product Code & Item No -->
                <div class="form-group all">
                    <label for="code" style="font-weight: 700;"><?= lang('product_code') ?> </label>
                    <div class="input-group">
                        <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code" required="required" placeholder="' . lang('product_code') . '"') ?>
                        <span class="input-group-addon pointer tip" id="random_num" title="<?= lang('random_code'); ?>" style="padding: 1px 12px; background: #f1f5f9; cursor: pointer;">
                            <i class="fa fa-random text-primary"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group all">
                    <label for="item_no" style="font-weight: 700;"><?= lang('Item_no') ?></label>
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
                    $max_p_no = '';
                    if (!empty($max_product_number)) {
                        foreach ($max_product_number as $max_product_no) {
                            $max_p_no = $max_product_no->max_product_number;
                        }
                    }
                    echo form_input('product_number', $max_p_no, 'class="form-control tip" id="product_number" ');
                    ?>
                </div>

                <!-- Group / Company / Branch (if grouping enabled) -->
                <?php if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) { ?>
                    <div class="spark-field-full spark-grouping-container">
                        <div class="spark-grouping-grid">
                            <?php if (isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) { ?>
                                <div class="form-group">
                                    <label for="grpId" style="font-weight: 700;"><?= lang('Main_group', 'Main_group') ?></label>
                                    <?php
                                    $grp[''] = lang('select') . ' ' . lang('Main_group');
                                    if ($Owner || $Admin || !$this->session->userdata('biller_id')){
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                    } else {
                                        $grp[$this->session->userdata('grpId')] = $this->site->getMainGroupsByID($this->session->userdata('grpId'))->name;
                                    }
                                    echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : $this->session->userdata('grpId')), 'class="form-control select" id="grpId" style="width:100%"');
                                    ?>
                                </div>
                            <?php } ?>

                            <?php if ((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1)) { ?>
                                <div class="form-group">
                                    <label for="coId" style="font-weight: 700;"><?= lang('Main_company', 'Main_company') ?></label>
                                    <?php
                                    $co[''] = lang('select') . ' ' . lang('Main_company');
                                    if ($Owner || $Admin || !$this->session->userdata('biller_id')){
                                        if(!($this->Settings->enable_grp==1)){
                                            foreach ($mainCompanies as $mainCompany) {
                                                $co[$mainCompany->id] = $mainCompany->name;
                                            }
                                        }
                                    } else {
                                        $co[$this->session->userdata('coId')] = $this->site->getMainCompaniesByID($this->session->userdata('coId'))->name;
                                    }
                                    echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $this->session->userdata('coId')), 'class="form-control select" id="coId" style="width:100%"');
                                    ?>
                                </div>
                            <?php } ?>

                            <?php if ((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1)) { ?>
                                <div class="form-group">
                                    <label for="brId" style="font-weight: 700;"><?= lang('Main_branch', 'Main_branch') ?></label>
                                    <?php
                                    $br[''] = lang('select') . ' ' . lang('Main_branch');
                                    if ($Owner || $Admin || !$this->session->userdata('biller_id')){
                                        if(!($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                        }
                                    } else {
                                        $br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
                                    }
                                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : ($product ? $product->brId : $this->session->userdata('brId'))), 'class="form-control select" id="brId" style="width:100%"');
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
                            <i class="fa fa-building-o text-primary"></i> <?= lang('warehouses'); ?> 
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
                    <label for="category" style="font-weight: 700;"><?= lang('category') ?> </label>
                    <div class="input-group">
                        <?php
                        $cat[''] = '';
                        if (!(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)) {
                            foreach ($categories as $category) {
                                $cat[$category->id] = $category->name;
                            }
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
                    <label for="subcategory" style="font-weight: 700;"><?= lang('subcategory') ?></label>
                    <div class="controls" id="subcat_data">
                        <?php echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory" placeholder="' . lang('select_category_to_load') . '"'); ?>
                    </div>
                </div>

                <!-- Product Unit -->
                <div class="form-group standard_combo">
                    <label for="unit" style="font-weight: 700;"><?= lang('product_unit'); ?> </label>
                    <div class="input-group">
                        <?php
                        $pu[''] = lang('select') . ' ' . lang('unit');
                        if (!(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)) {
                            foreach ($base_units as $bu) {
                                $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                            }
                        }
                        echo form_dropdown('unit', $pu, set_value('unit', ($product ? $product->unit : '')), 'class="form-control tip" id="unit" required="required" style="width:100%;"');
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
                    <?= form_input('second_name', $product ? $product->second_name : set_value('second_name'), 'class="form-control tip" id="second_name" placeholder="' . lang('second_name') . '"'); ?>
                </div>

                <!-- Core Pricing Box (Selling Price, Min Price — Cost hidden from Add UI safely) -->
                <div class="spark-field-full">
                    <div class="spark-pricing-box">
                        <div class="spark-pricing-header">
                            <i class="fa fa-money text-success"></i> <?= lang('selling_prices'); ?>
                        </div>
                        <div class="spark-pricing-grid spark-pricing-grid-2">
                            
                            <!-- Selling Price -->
                            <div class="form-group" style="margin: 0;">
                                <label for="price" style="font-size: 12.5px; font-weight: 700;"><?= lang('product_price') ?> </label>
                                <?= form_input('price', (isset($_POST['price']) ? $_POST['price'] : ($product ? $this->sma->formatDecimal($product->price, 4) : '')), 'class="form-control tip" id="price" required="required" placeholder="0.00"') ?>
                            </div>

                            <!-- Minimum Selling Price -->
                            <div class="form-group" style="margin: 0;">
                                <label for="minimum_selling_price" style="font-size: 12.5px; font-weight: 700;"><?= lang('Minimum_selling_price', 'Minimum_selling_price') ?></label>
                                <?= form_input('minimum_selling_price', (isset($_POST['minimum_selling_price']) ? $_POST['minimum_selling_price'] : ($product ? $this->sma->formatDecimal($product->minimum_selling_price, 4) : 0)), 'class="form-control" id="minimum_selling_price" placeholder="0.00"') ?>
                            </div>

                        </div>
                    </div>
                    <!-- Hidden cost input to maintain backend POST & combo calculations safely -->
                    <input type="hidden" name="cost" id="cost" value="<?= (isset($_POST['cost']) ? $_POST['cost'] : ($product ? $this->sma->formatDecimal($product->cost, 4) : '0')) ?>">
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
                        <!-- Live Image Preview Box (Always showing image or no_image.png) -->
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
                            <th class="col-md-1 col-sm-1 col-xs-1 text-center"><i class="fa fa-trash-o" style="opacity:0.5;"></i></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div id="ex-combo"></div>
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
                        $tr_default = !empty($tax_rates) ? $tax_rates[0]->id : '';
                        foreach ($tax_rates as $tax) {
                            if ($tax->tax_default == 1) {
                                $tr_default = $tax->id;
                            }
                            $tr[$tax->id] = $tax->name;
                        }
                        echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : ($product ? $product->tax_rate : $tr_default)), 'class="form-control select" id="tax_rate" placeholder="' . lang('select') . ' ' . lang('product_tax') . '" style="width:100%"');
                        ?>
                    </div>

                    <div class="form-group all" id="div_tax_method" style="display:none">
                        <label for="tax_method" style="font-weight: 700;"><?= lang('tax_method', 'tax_method') ?></label>
                        <?php
                        $tm = ['1' => lang('exclusive'), '0' => lang('inclusive')];
                        echo form_dropdown('tax_method', $tm, (isset($_POST['tax_method']) ? $_POST['tax_method'] : ($product ? $product->tax_method : $Settings->default_tax_method)), 'class="form-control select" id="tax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"');
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
                <div class="spark-advanced-grid-3">
                    
                    <!-- Alert Quantity & Track Quantity -->
                    <div class="form-group standard">
                        <label for="alert_quantity" style="font-weight: 700;"><?= lang('alert_quantity', 'alert_quantity') ?></label>
                        <div class="input-group">
                            <?= form_input('alert_quantity', (isset($_POST['alert_quantity']) ? $_POST['alert_quantity'] : ($product ? $this->sma->formatQuantityDecimal($product->alert_quantity) : '')), 'class="form-control tip" id="alert_quantity" placeholder="0"') ?>
                            <span class="input-group-addon tip" title="<?= lang('track_quantity'); ?>">
                                <input type="checkbox" name="track_quantity" id="track_quantity" value="1" <?= ($product ? (isset($product->track_quantity) ? 'checked="checked"' : '') : 'checked="checked"') ?>>
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
                        if (!(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)) {
                            foreach ($brands as $brand) {
                                $brnd[$brand->id] = $brand->name;
                            }
                        }
                        echo form_dropdown('brand', $brnd, (isset($_POST['brand']) ? $_POST['brand'] : ($product ? $product->brand : '')), 'class="form-control select" id="brand" placeholder="' . lang('select') . ' ' . lang('brand') . '" style="width:100%"');
                        ?>
                    </div>

                    <!-- Default Sale Unit -->
                    <div class="form-group standard_combo">
                        <label for="default_sale_unit" style="font-weight: 700;"><?= lang('default_sale_unit', 'default_sale_unit'); ?></label>
                        <?php $uopts[''] = lang('select_unit_first'); ?>
                        <?= form_dropdown('default_sale_unit', $uopts, ($product ? $product->sale_unit : ''), 'class="form-control" id="default_sale_unit" style="width:100%;"'); ?>
                    </div>

                    <!-- Default Purchase Unit -->
                    <div class="form-group standard" style="display:none">
                        <label for="default_purchase_unit" style="font-weight: 700;"><?= lang('default_purchase_unit', 'default_purchase_unit'); ?></label>
                        <?= form_dropdown('default_purchase_unit', $uopts, ($product ? $product->purchase_unit : ''), 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
                    </div>

                </div>

                <!-- Product Attributes / Variants -->
                <div class="standard">
                    <div id="attrs"></div>
                    <div class="form-group">
                        <input type="checkbox" class="checkbox" name="attributes" id="attributes" <?= $this->input->post('attributes') || $product_options ? 'checked="checked"' : ''; ?>>
                        <label for="attributes" class="padding05" style="font-weight: 700;"><?= lang('product_has_attributes'); ?></label>
                        <span class="text-muted">(<?= lang('eg_sizes_colors'); ?>)</span>
                    </div>

                    <div class="well well-sm" id="attr-con" style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?>">
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
                            <table id="attrTable" class="table table-condensed table-striped" style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?> margin-bottom: 0; margin-top: 10px;">
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
                                            echo '<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' . $_POST['attr_name'][$r] . '"><span>' . $_POST['attr_name'][$r] . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $_POST['attr_warehouse'][$r] . '"><input type="hidden" name="attr_wh_name[]" value="' . $_POST['attr_wh_name'][$r] . '"><span>' . $_POST['attr_wh_name'][$r] . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $_POST['attr_price'][$r] . '"><span>' . $_POST['attr_price'][$r] . '</span></td><td class="text-center"><i class="fa fa-times delAttr text-danger pointer"></i></td></tr>';
                                        }
                                    }
                                } elseif ($product_options) {
                                    foreach ($product_options as $option) {
                                        echo '<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' . $option->name . '"><span>' . $option->name . '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $option->warehouse_id . '"><input type="hidden" name="attr_wh_name[]" value="' . $option->wh_name . '"><span>' . $option->wh_name . '</span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="' . $this->sma->formatQuantityDecimal($option->wh_qty) . '"><span>' . $this->sma->formatQuantity($option->wh_qty) . '</span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $this->sma->formatMoney($option->price) . '"><span>' . $this->sma->formatMoney($option->price) . '</span></td><td class="text-center"><i class="fa fa-times delAttr text-danger pointer"></i></td></tr>';
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
                        <div class="form-group digital">
                            <label for="digital_file" style="font-weight: 700;"><?= lang('digital_file', 'digital_file') ?></label>
                            <input id="digital_file" type="file" data-browse-label="<?= lang('browse'); ?>" name="digital_file" data-show-upload="false" data-show-preview="false" class="form-control file">
                        </div>
                        <div class="form-group">
                            <label for="file_link" style="font-weight: 700;"><?= lang('file_link', 'file_link'); ?></label>
                            <?= form_input('file_link', set_value('file_link'), 'class="form-control" id="file_link"'); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Section 6: Price Groups (مجموعات الأسعار) -->
        <div class="spark-advanced-card">
            <div class="spark-advanced-card-header">
                <div class="spark-advanced-card-header-left">
                    <div class="spark-section-icon icon-pricing">
                        <i class="fa fa-tags"></i>
                    </div>
                    <div>
                        <h3 class="spark-section-title"><?= lang('price_groups'); ?></h3>
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
                        <label for="slug" style="font-weight: 700;"><?= lang('slug'); ?></label>
                        <?= form_input('slug', set_value('slug'), 'class="form-control tip" id="slug" required="required" placeholder="' . lang('slug') . '"'); ?>
                    </div>

                    <div class="form-group standard_combo">
                        <label for="weight" style="font-weight: 700;"><?= lang('weight', 'weight'); ?></label>
                        <?= form_input('weight', set_value('weight'), 'class="form-control tip" id="weight" placeholder="0.00"'); ?>
                    </div>

                    <input type="hidden" name="barcode_symbology" id="barcode_symbology" value="<?= isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : 'code128' ?>">

                    <?php if ($Settings->product_expiry) { ?>
                        <div class="form-group">
                            <label for="expiry" style="font-weight: 700;"><?= lang('expiry', 'expiry'); ?></label>
                            <?= form_input('expiry', set_value('expiry'), 'class="form-control tip date" id="expiry"'); ?>
                        </div>
                    <?php } ?>

                </div>

                <!-- Visibility & Feature Checkboxes -->
                <div class="row" style="margin-top: 10px; margin-bottom: 16px;">
                    <?php if (isset($this->Settings->isStore) && $this->Settings->isStore==1) { ?>
                        <div class="col-sm-4 form-group">
                            <input name="featured" type="checkbox" class="checkbox" id="featured" value="1" <?= isset($_POST['featured']) ? 'checked="checked"' : '' ?>/>
                            <label for="featured" class="padding05"><?= lang('featured') ?></label>
                        </div>
                    <?php } ?>
                    <div class="col-sm-4 form-group">
                        <input name="hide_pos" type="checkbox" class="checkbox" id="hide_pos" value="1" <?= isset($_POST['hide_pos']) ? 'checked="checked"' : '' ?>/>
                        <label for="hide_pos" class="padding05"><?= lang('hide_in_pos') ?></label>
                    </div>
                    <div class="col-sm-4 form-group">
                        <input name="hide" type="checkbox" class="checkbox" id="hide" value="1" <?= isset($_POST['hide']) ? 'checked="checked"' : '' ?>/>
                        <label for="hide" class="padding05"><?= lang('product_is_inactive') ?></label>
                    </div>
                </div>

                <!-- Custom Fields (CF1 - CF6) -->
                <div class="form-group">
                    <input name="cf" type="checkbox" class="checkbox" id="extras" value="" <?= isset($_POST['cf']) ? 'checked="checked"' : '' ?>/>
                    <label for="extras" class="padding05" style="font-weight: 700;"><?= lang('custom_fields') ?></label>
                </div>
                <div class="row" id="extras-con" style="display: none;">
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
        <button type="submit" name="add_product" id="add_product" class="spark-btn-product-submit">
            <i class="fa fa-check-circle"></i> <?= $this->lang->line('add_product'); ?>
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

    // Auto-open Advanced Details if Error or Existing Data
    <?php if ($error || (isset($_POST['tax_rate']) && $_POST['tax_rate'] != '') || isset($_POST['attributes']) || isset($_POST['supplier']) || isset($_POST['cf']) || isset($_POST['price_groups2'])) { ?>
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
                    if (data > 0) {
                        $('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
                        $('#tax_method').select2('readonly', false);
                    } else {
                        $('#tax_method').val('<?= $Settings->default_tax_method; ?>').trigger('change');
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
        foreach ($combo_items as $item) {
            if ($item->code) {
                echo 'add_product_item(' . json_encode($item) . ');';
            }
        }
    }
    ?>
    <?= isset($_POST['cf']) ? '$("#extras").iCheck("check");' : '' ?>
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
        <?= isset($_POST['promotion']) ? '$("#promotion").iCheck("check");' : '' ?>
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
            if ($('#cost').length) { $('#cost').attr('disabled', true); }
            $('#track_quantity').iCheck('uncheck');
        } else {
            $('.standard').slideDown();
            $('#track_quantity').iCheck('check');
            $('#unit').attr('disabled', false);
            if ($('#cost').length) { $('#cost').attr('disabled', false); }
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
            if ($('#cost').length) { $('#cost').attr('disabled', false); }
            $('#unit').removeAttr('disabled');
        } else {
            $('.standard_combo').slideUp();
        }
    });

    var t = $('#type').val();
    if (t !== 'standard') {
        $('.standard').slideUp();
        $('#unit').attr('disabled', true);
        if ($('#cost').length) { $('#cost').attr('disabled', true); }
        $('#track_quantity').iCheck('uncheck');
    } else {
        $('.standard').slideDown();
        $('#track_quantity').iCheck('check');
        $('#unit').attr('disabled', false);
        if ($('#cost').length) { $('#cost').attr('disabled', false); }
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
        $("#pwarehouse > option").each(function(){
            $("#prTable"+ this.value).hide();
            $("#title"+ this.value).hide();
        });

        try{
            var wh = [];
            var i=0;
            $('#pwarehouse :selected').each(function() {
                wh[i] = $(this).val();
                var html = '<div class="control-group table-group" id="title' + wh[i] + '"><label class="table-label" for="combo"><?= lang('combo_products'); ?> (' + $(this).text() + ')</label><div class="controls table-controls"><table id="prTable' + wh[i] + '" class="table items table-striped table-condensed table-hover"><thead><tr><th class="col-md-4 col-sm-5 col-xs-5"><?= lang('product') . ' (' . lang('code') . ' - ' . lang('name') . ')'; ?></th><th class="col-md-2 col-sm-2 col-xs-2"><?= lang('unit'); ?></th><th class="col-md-3 col-sm-3 col-xs-3"><?= lang('cost'); ?></th><th class="col-md-1 col-sm-1 col-xs-1 text-center"><i class="fa fa-trash-o" style="opacity:0.5;"></i></th></tr></thead><tbody></tbody><tfoot></tfoot></table></div></div>';
                $('#ex-combo').append(html);
                multiTable(wh[i],wh);
                i++;
            });
        } catch(err) { alert(err); }
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
                $('#pwarehouse').select2('readonly', true);
                $("#allck").css("display", "none");
                var counter=0;
                var checkwp=1;

                $('#pwarehouse :selected').each(function() {
                    for(var j=0;j<ui.item.wpr.length;j++){
                        if(ui.item.id==ui.item.wpr[j]['id'] && ui.item.wpr[j]['warehouse_id']=='no')
                            checkwp=0;
                    }
                    counter++;
                });
                event.preventDefault();
                try{
                    if (ui.item.id !== 0 && checkwp==1) {
                        var row = add_product_item(ui.item,i,wh);
                        if (row) {
                            $(this).val('');
                        }
                    } else {
                        bootbox.alert('<?= lang('no_match_found') ?>');
                    }
                } catch(err) { alert(err); }
            }
        });
    }

    function add_product_item(item,tabRow,wh) {
        var wpArr = wh;
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
            var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
            tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
            tr_html += '<td><span id="unit_' + row_no + '">' + this.unit.name + '</span></td>';
            tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatDecimal(this.qty,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<input class="form-control text-center rprice" name="combo_item_price[]" type="hidden" value="' + formatDecimal(this.price,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();">';
            tr_html += '<td class="text-center"><i class="fa fa-times tip del text-danger pointer" id="' + row_no + '" title="<?= lang('remove'); ?>"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#prTable" );
            pp += formatDecimal(parseFloat(this.price)*parseFloat(this.qty),4);
        });
        $('.item_' + item_id).addClass('warning');
        if ($('#cost').length) { $('#cost').val(pp); }

        if (wpArr) {
            for(var i=0; i<wpArr.length; i++){
                if (item == null) {
                    return false;
                }
                item_id = item.id;
                var pp = 0;
                var fp = 0;
                $("#prTable"+ wh[i] + " tbody").empty();
                $.each(items, function () {
                    var wpcost;
                    for(var j=0;j<this.wpr.length;j++){
                        if(wh[i]==this.wpr[j]['warehouse_id'] && this.code== this.wpr[j]['code']){
                            wpcost=this.wpr[j]['avg_cost'];
                            break;
                        }
                    }

                    var row_no = this.id;
                    var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                    tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                    tr_html += '<td><span id="unit_' + row_no + '">' + this.unit.name + '</span></td>';
                    tr_html += '<td><input disabled class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(wpcost,4) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                    tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="<?= lang('remove'); ?>" style="cursor:pointer;display:none"></i></td>';
                    newTr.html(tr_html);
                    newTr.prependTo("#prTable"+ wh[i] );

                    pp += formatDecimal(parseFloat(this.price)*parseFloat(this.qty),4);
                    fp += parseFloat(wpcost)*this.qty;
                });
                $('.item_' + item_id).addClass('warning');
                var tfoot ='<tr id="tfoot" class="tfoot active"><th colspan="2"><?= lang('total') ?></th><th class="text-center total"><input disabled class="form-control text-center" name="total' + wh[i] + '" id="total' + wh[i] + '" type="text" value="' + formatDecimal(fp,4) + '"></th><tr>';
                $("#prTable"+ wh[i] + " tfoot").html(tfoot);
                if ($('#cost').length) { $('#cost').val(pp); }
            }
        }
        return true;
    }

    function calculate_price(new_qty,id) {
        var rows = $('#prTable').children('tbody').children('tr');
        var pp = 0;
        var qty = [];
        var j=0;
        $.each(rows, function () {
            pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()),4);
            qty[j]=parseFloat($(this).find('.rquantity').val());
            j++;
        });
        if ($('#cost').length) { $('#cost').val(pp); }

        var wh = [];
        var i=0;
        $('#pwarehouse :selected').each(function() {
            var ppwp = 0;
            wh[i] = $(this).val();
            var rowswp = $('#prTable'+ wh[i]).children('tbody').children('tr');
            var j=0;
            $.each(rowswp, function () {
                ppwp += formatDecimal(parseFloat($(this).find('.rprice').val())*qty[j],4);
                j++;
            });
            $('#total'+ wh[i]).val(formatDecimal(ppwp,4));
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
                img.src = _URL.createObjectURL(fle);
            }
        }
    });

    $('#unit').change(function () {
        var v = $(this).val();
        try {
            var brId = $('#brId').val() || "1";
        } catch(err) {
            var brId = "1";
        }
        if (v) {
            $.ajax({
                type: "get",
                async: false,
                url: "<?= admin_url('products/getSubUnits') ?>/" + v +"/"+ brId,
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

    $('#random_num').click(function () {
        var code = generateCardNo(8);
        $(this).parent('.input-group').children('input').val(code);
        $('#code').val(code).trigger('input').trigger('change');
    });

    $('#random_item_num').click(function() {
        $(this).parent('.input-group').children('input').val(generateCardNo(5));
    });

    var gp = 2;
    $('#addPriceGroups').click(function () {
        if (gp <= 5) {
            var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-lg-12"><div class="col-sm-3"><div class="form-group"><input type="hidden" name="product_unit_id2_' + gp + '", class="form-control" id="product_unit_id2_' + gp + '" placeholder="<?= lang('select') . ' ' . lang('Pricing_unit') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-sm-3"><div class="form-group"><input type="number" min="1" step="1" name="factor2_' + gp + '" class="form-control tip" id="factor2_' + gp + '" placeholder="<?= lang('Conversion_factor') ?>" /></div></div><div class="col-sm-3"><div class="form-group"><div class="input-group"><input type="text" name="pricing_code2_' + gp + '" class="form-control tip" id="pricing_code2_' + gp + '" placeholder="<?= lang('Pricing_code') ?>" /><div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genPurcCode' + gp + '" ><i class="fa fa-cogs"></i></a></div></div></div></div><div class="col-sm-3"><div class="form-group"><input type="number" min=".01" step=".01" name="selling_price2_' + gp + '" class="form-control tip" id="selling_price2_' + gp + '" placeholder="<?= lang('selling_price') ?>" /></div></div></div></div>';
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
    if ($('#attributes').length && ($('#attributes').is(':checked') || $('#attributes').prop('checked') || <?= ($this->input->post('attributes') || $product_options) ? 'true' : 'false' ?>)) {
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
    
    var isGroup= '<?= $this->Settings->isGrouping ?>';
    if (isGroup!=1){
        var initial_wh = <?= json_encode(!empty($whselected) ? array_values(array_map('strval', $whselected)) : (!empty($Settings->default_warehouse) ? [(string)$Settings->default_warehouse] : (!empty($warehouses) ? [(string)$warehouses[0]->id] : []))) ?>;
        if (initial_wh && initial_wh.length > 0) {
            $('#pwarehouse').val(initial_wh).trigger('change.select2');
        } else {
            $('#pwarehouse').val('<?= $warehouses[0]->id ?>').trigger('change.select2');
        }
        $('#category').val('<?= $categories[0]->id ?>').trigger('change.select2');
	    $('#unit').val('<?= $base_units[0]->id ?>').trigger('change.select2');
        var v = $('#unit').val();

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





			$("#div_tax_method").css("display", "block");
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

    }else{
        $('#pwarehouse').trigger('change');
    }

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

<div class="modal" id="aModal" tabindex="-1" role="dialog" aria-labelledby="aModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                    <i class="fa fa-2x">&times;</i></span><span class="sr-only"><?= lang('close'); ?></span>
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
