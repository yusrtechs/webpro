<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$wm = ['0' => lang('no'), '1' => lang('yes')];
$ps = ['0' => lang('disable'), '1' => lang('enable')];
?>
<script>
    $(document).ready(function () {
        <?php if (isset($message)) {
    echo 'localStorage.clear();';
} ?>
        var timezones = <?= json_encode(DateTimeZone::listIdentifiers(DateTimeZone::ALL)); ?>;
        $('#timezone').autocomplete({
            source: timezones
        });
        if ($('#protocol').val() == 'smtp') {
            $('#smtp_config').slideDown();
        } else if ($('#protocol').val() == 'sendmail') {
            $('#sendmail_config').slideDown();
        }
        $('#protocol').change(function () {
            if ($(this).val() == 'smtp') {
                $('#sendmail_config').slideUp();
                $('#smtp_config').slideDown();
            } else if ($(this).val() == 'sendmail') {
                $('#smtp_config').slideUp();
                $('#sendmail_config').slideDown();
            } else {
                $('#smtp_config').slideUp();
                $('#sendmail_config').slideUp();
            }
        });
        $('#overselling').change(function () {
            if ($(this).val() == 1) {
                if ($('#accounting_method').select2("val") != 2) {
                    bootbox.alert('<?=lang('overselling_will_only_work_with_AVCO_accounting_method_only')?>');
                    $('#accounting_method').select2("val", '2');
                }
            }

        });
        $('#suggest_product').change(function () {
            var product = $('#report_product_id').val();
            var href="<?= admin_url('products/products_details'); ?>"+"/"+product;
            $("#add_product").attr("href", href);

        });
        $('#unit').change(function () {
            var unit = $(this).val();
            var href="<?= admin_url('products/units_details'); ?>"+"/"+unit;
            $("#add_unit").attr("href", href);

        });

        $('#category').change(function () {
            var category = $(this).val();
            var href="<?= admin_url('products/categories_details'); ?>"+"/"+category;
            $("#add_category").attr("href", href);

        });
        $('#brand').change(function () {
            var brand = $(this).val();
            var href="<?= admin_url('products/brands_details'); ?>"+"/"+brand;
            $("#add_brand").attr("href", href);

        });
        $('#variant').change(function () {
            var variant = $(this).val();
            var href="<?= admin_url('products/variants_details'); ?>"+"/"+variant;
            $("#add_variant").attr("href", href);

        });
        $('#tax_rate').change(function () {
            var tax_rate = $(this).val();

            var href="<?= admin_url('system_settings/taxs_details'); ?>"+"/"+tax_rate;
            $("#add_tax").attr("href", href);

        });
        $('#currency').change(function () {
            var currency = $(this).val();
            //alert(currency)

            var href="<?= admin_url('system_settings/currencies_details'); ?>"+"/"+currency;
            $("#add_currency").attr("href", href);

        });
 $('#customer').change(function () {
            var customer = $(this).val();
            var href="<?= admin_url('customers/customers_details'); ?>"+"/"+customer;
            $("#add_customer").attr("href", href);

        });

        $('#supplier').change(function () {
            var supplier = $(this).val();
            var href="<?= admin_url('suppliers/suppliers_details'); ?>"+"/"+supplier;
            $("#add_supplier").attr("href", href);

        });

        $('#accounting_method').change(function () {
            var oam = <?=$Settings->accounting_method?>, nam = $(this).val();
            if (oam != nam) {
                bootbox.alert('<?=lang('accounting_method_change_alert')?>');
            }
        });


        $('#accounting_method').change(function () {
            if ($(this).val() != 2) {
                if ($('#overselling').select2("val") == 1) {
                    bootbox.alert('<?=lang('overselling_will_only_work_with_AVCO_accounting_method_only')?>');
                    $('#overselling').select2("val", 0);
                }
            }
        });
        $('#item_addition').change(function () {
            if ($(this).val() == 1) {
                bootbox.alert('<?=lang('product_variants_feature_x')?>');
            }
        });
        var sac = $('#sac').val()
        if(sac == 1) {
            $('.nsac').slideUp();
        } else {
            $('.nsac').slideDown();
        }
        $('#sac').change(function () {
            if ($(this).val() == 1) {
                $('.nsac').slideUp();
            } else {
                $('.nsac').slideDown();
            }
        });
    });
</script>

<?php if ($Owner || $Admin) {
    ?>
    <ul id="myTab" class="nav nav-tabs">
        <!--<li class=""><a href="#system_controls" class="tab-grey"><?= lang('System_controls') ?></a></li>-->
        <li class=""><a href="#settings" class="tab-grey"><?= lang('settings') ?></a></li>
       <li class=""><a href="#warehouse" class="tab-grey"><?= lang('warehouses') ?></a></li>
        <li class=""><a href="#sales" class="tab-grey"><?= lang('sales') ?></a></li>
        <li class=""><a href="#purchases" class="tab-grey"><?= lang('purchases') ?></a></li>
         <?php if ($product->type == 'standard') {
        ?>
        <li class=""><a href="#purchases" class="tab-grey"><?= lang('purchases') ?></a></li>
        <li class=""><a href="#transfers" class="tab-grey"><?= lang('transfers') ?></a></li>
        <li class=""><a href="#quantity_adjustments" class="tab-grey"><?= lang('quantity_adjustments') ?></a></li>
        <?php
    } ?>
    </ul>

<div class="tab-content">
    <div id="system_controls" class="tab-pane fade in">
        <?php
} ?>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('branches/main_settings', $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('System_controls') ?></legend>



                                <div class="form-group all">
                                    <?= form_checkbox('enable_grp', '1',$Settings->enable_grp ? 'checked="checked"' : '',    'class="form-control tip" id="enable_grp" '); ?>
				                    <?= lang('Enable_groups', 'Enable_groups'); ?>
                                </div>

                                <div class="form-group all">
                                    <?= form_checkbox('enable_co', '1',$Settings->enable_co ? 'checked="checked"' : '',    'class="form-control tip" id="enable_co" '); ?>
				                    <?= lang('Enable_companies', 'Enable_companies'); ?>
                                </div>




                    </fieldset>



                </div>
            </div>
            <div class="cleafix"></div>
            <div class="form-group">
                <div class="controls">
                    <?= form_submit('update_settings', lang('update_settings'), 'class="btn btn-primary btn-lg"'); ?>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>

</div>
</div>
</div> <!-- end first tab -->





    <div id="warehouse" class="tab-pane fade in">


        <div class="box">

            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">

                        <p class="introtext"><?= lang('update_info'); ?></p>


                        <div class="row">




                        <div class="col-lg-12">

                        <div class="row">
                                    <div class="col-lg-6">
                                    <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('products/products_details', $attrib);

                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_products') ?></legend>


                                        <div class="form-group" id="all_product">
                                                    <input type="radio" class="radio" name="all_product" value="all" <?= isset($_POST['all_product']) && $_POST['all_product'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_product" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="attributes">
                                                    <input type="radio" class="radio" name="all_product" value="attributes" <?= isset($_POST['all_product']) && $_POST['all_product'] == 'attributes' ? 'checked' : '' ?>>
                                                    <label for="attributes" class="text-info padding05"><?= lang('One_product') ?></label>
                                                </div>

                                                <!--<div id="attr-con" class="form-group <?= ($this->input->post('attributes') || $product_options) ? '' : 'hide'; ?>">
                                                -->
                                                <div class="form-group"id="attr-product" style="display:none;">
                                                <?= lang('Select_product', 'suggest_product'); ?>
                                                    <div class="input-group">
                                                        <?php echo form_input('sproduct', (isset($_POST['sproduct']) ? $_POST['sproduct'] : ""), 'class="form-control" id="suggest_product" placeholder="' . lang('select') . ' ' . lang('product') . '" required="" style="width:100%"'); ?>
                                                        <input type="hidden" name="product" value="<?= isset($_POST['product']) ? $_POST['product'] : "" ?>" id="report_product_id"/>

                                                        <?php if ($Owner || $Admin || $GP['customers-add']) {
                                                            ?>
                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">

                                                            <a href="<?= admin_url('products/products_details'); ?>" id="add_product" class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>
                                                        <?php
                                                        } ?>
                                                    </div>
                                                </div>



                                        <div class="cleafix"></div>
                                        <div class="form-group" id="update_product">
                                        <div class="controls">
                                        <a href="<?= admin_url('products/products_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                        </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>



                            <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('products/units_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_units') ?></legend>



                                                <div class="form-group" id="all_unit">
                                                    <input type="radio" class="radio" name="all_unit" value="all" <?= isset($_POST['all_unit']) && $_POST['all_unit'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_unit" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_unit">
                                                    <input type="radio" class="radio" name="all_unit" value="one_unit" <?= isset($_POST['all_unit']) && $_POST['all_unit'] == 'one_unit' ? 'checked' : '' ?>>
                                                    <label for="one_unit" class="text-info padding05"><?= lang('One_unit') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-unit" style="display:none;">
                                                <?= lang('select') . ' ' . lang('unit') ?>
                                                    <div class="input-group">

                                                    <?php
                                                        $pu[''] = lang('select') . ' ' . lang('unit');
                                                        foreach ($base_units as $bu) {
                                                            $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                                                        }

                                                        ?>
                                                        <?= form_dropdown('unit', $pu, set_value('unit', ($product ? $product->unit : '')), '  class="form-control tip" id="unit" required="required" style="width:100%;"'); ?>

                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('products/units_details'); ?>" id="add_unit" class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>




                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_unit">
                                        <div class="controls">

                                            <a href="<?= admin_url('products/units_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                                            </a>
                                        </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>

            </div>

            <div class="row">

            <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('products/categories_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_categories') ?></legend>



                                                <div class="form-group" id="all_category">
                                                    <input type="radio" class="radio" name="all_category" value="all" <?= isset($_POST['all_category']) && $_POST['all_category'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_category" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_category">
                                                    <input type="radio" class="radio" name="all_category" value="one_category" <?= isset($_POST['all_category']) && $_POST['all_category'] == 'one_category' ? 'checked' : '' ?>>
                                                    <label for="one_category" class="text-info padding05"><?= lang('One_category') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-category" style="display:none;">
                                                <?= lang('select') . ' ' . lang('category') ?>
                                                    <div class="input-group">


                                                            <?php
                                                            $cat[''] = '';


                                                            foreach ($categories as $category) {
                                                                $cat[$category->id] = $category->name;
                                                            }

                                                            echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang('select') . ' ' . lang('category') . '" required="" style="width:100%"')
                                                            ?>

                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('products/categories_details'); ?>" id="add_category"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>




                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_category">
                                        <div class="controls">
                                        <a href="<?= admin_url('products/categories_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                    </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>

                            <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('products/brands_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_brands') ?></legend>



                                                <div class="form-group" id="all_brand">
                                                    <input type="radio" class="radio" name="all_brand" value="all" <?= isset($_POST['all_brand']) && $_POST['all_brand'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_brand" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_brand">
                                                    <input type="radio" class="radio" name="all_brand" value="one_brand" <?= isset($_POST['all_brand']) && $_POST['all_brand'] == 'one_brand' ? 'checked' : '' ?>>
                                                    <label for="one_brand" class="text-info padding05"><?= lang('One_brand') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-brand" style="display:none;">
                                                <?= lang('select') . ' ' . lang('brand') ?>
                                                    <div class="input-group">

                                                    <?php
                                                        $brnd[''] = '';

                                                        foreach ($brands as $brand) {
                                                            $brnd[$brand->id] = $brand->name;
                                                        }

                                                        echo form_dropdown('brand', $brnd, (isset($_POST['brand']) ? $_POST['brand'] : ($product ? $product->brand : '')), 'class="form-control select" id="brand" placeholder="' . lang('select') . ' ' . lang('brand') . '" style="width:100%"')
                                                        ?>


                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('products/brands_details'); ?>" id="add_brand"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>




                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_brand">
                                        <div class="controls">
                                        <a href="<?= admin_url('products/brands_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                        </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>



                    </div>
                      <div class="row">

                      <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('products/variants_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_variants') ?></legend>



                                                <div class="form-group" id="all_variant">
                                                    <input type="radio" class="radio" name="all_variant" value="all" <?= isset($_POST['all_variant']) && $_POST['all_variant'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_variant" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_variant">
                                                    <input type="radio" class="radio" name="all_variant" value="one_variant" <?= isset($_POST['all_variant']) && $_POST['all_variant'] == 'one_variant' ? 'checked' : '' ?>>
                                                    <label for="one_variant" class="text-info padding05"><?= lang('One_variant') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-variant" style="display:none;">
                                                <?= lang('select') . ' ' . lang('variants') ?>
                                                    <div class="input-group">

                                                    <?php
                                                        $var[''] = '';
                                                        foreach ($variants as $variant) {
                                                            $var[$variant->id] = $variant->name ;
                                                        }

                                                        ?>
                                                        <?= form_dropdown('variant', $var, set_value('variant', ($product ? $product->variant : '')),   'class="form-control select" id="variant" placeholder="' . lang('select') . ' ' . lang('variants') . '" style="width:100%"'); ?>

                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('products/variants_details'); ?>" id="add_variant"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>




                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_variant">
                                        <div class="controls">
                                        <a href="<?= admin_url('products/variants_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                         </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>

                    </div>




                        </div>
                    </div><!--    -->

                </div>
            </div>

        </div>
        </div>
</div> <!-- end second tab -->












<div id="sales" class="tab-pane fade in">


       <div class="box">
           <div class="box-header">
               <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('settings'); ?></h2>


           </div>
           <div class="box-content">
               <div class="row">
                   <div class="col-lg-12">

                       <p class="introtext"><?= lang('update_info'); ?></p>

                       <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                       echo admin_form_open_multipart('branches/main_settings', $attrib);
                       ?>
                       <div class="row">
                           <div class="col-lg-12">
                               <fieldset class="scheduler-border">
                                   <legend class="scheduler-border"><?= lang('sales') ?></legend>

                                   <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('customers/customers_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_customers') ?></legend>



                                                <div class="form-group" id="all_customer">
                                                    <input type="radio" class="radio" name="all_customer" value="all" <?= isset($_POST['all_customer']) && $_POST['all_customer'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_customer" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_customer">
                                                    <input type="radio" class="radio" name="all_customer" value="one_customer" <?= isset($_POST['all_customer']) && $_POST['all_customer'] == 'one_customer' ? 'checked' : '' ?>>
                                                    <label for="one_customer" class="text-info padding05"><?= lang('One_customer') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-customer" style="display:none;">
                                                <?= lang('select') . ' ' . lang('customer') ?>
                                                    <div class="input-group">



                                                        <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>




                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('customers/customers_details'); ?>" id="add_customer"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>




                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_customer">
                                        <div class="controls">
                                        <a href="<?= admin_url('customers/customers_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                        </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>





                                   </fieldset>



</div>
</div><!--    -->

</div>
</div>

</div>
</div>
</div> <!-- end sales tab -->



<div id="purchases" class="tab-pane fade in">


       <div class="box">
           <div class="box-header">
               <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('settings'); ?></h2>


           </div>
           <div class="box-content">
               <div class="row">
                   <div class="col-lg-12">

                       <p class="introtext"><?= lang('update_info'); ?></p>

                       <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                       echo admin_form_open_multipart('branches/main_settings', $attrib);
                       ?>
                       <div class="row">
                           <div class="col-lg-12">
                               <fieldset class="scheduler-border">
                                   <legend class="scheduler-border"><?= lang('purchases') ?></legend>

                                   <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('suppliers/suppliers_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_suppliers') ?></legend>



                                                <div class="form-group" id="all_supplier">
                                                    <input type="radio" class="radio" name="all_supplier" value="all" <?= isset($_POST['all_supplier']) && $_POST['all_supplier'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_supplier" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_supplier">
                                                    <input type="radio" class="radio" name="all_supplier" value="one_supplier" <?= isset($_POST['all_supplier']) && $_POST['all_supplier'] == 'one_supplier' ? 'checked' : '' ?>>
                                                    <label for="one_supplier" class="text-info padding05"><?= lang('One_supplier') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-supplier" style="display:none;">
                                                <?= lang('select') . ' ' . lang('supplier') ?>
                                                    <div class="input-group">



                                                        <?php echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ""), 'class="form-control" id="supplier" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("supplier") . '"'); ?>




                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('suppliers/suppliers_details'); ?>" id="add_supplier"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>




                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_supplier">
                                        <div class="controls">
                                        <a href="<?= admin_url('suppliers/suppliers_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                        </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>





                                   </fieldset>



</div>
</div><!--    -->

</div>
</div>

</div>
</div>
</div> <!-- end purchases tab -->



<div id="settings" class="tab-pane fade in">


       <div class="box">

           <div class="box-content">
               <div class="row">
                   <div class="col-lg-12">

                       <p class="introtext"><?= lang('update_info'); ?></p>


                       <div class="row">
                           <div class="col-lg-12">


                           <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('system_settings/taxs_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_taxs') ?></legend>



                                                <div class="form-group" id="all_tax">
                                                    <input type="radio" class="radio" name="all_tax" value="all" <?= isset($_POST['all_tax']) && $_POST['all_tax'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_tax" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_tax">
                                                    <input type="radio" class="radio" name="all_tax" value="one_tax" <?= isset($_POST['all_tax']) && $_POST['all_tax'] == 'one_tax' ? 'checked' : '' ?>>
                                                    <label for="one_tax" class="text-info padding05"><?= lang('One_tax') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-tax" style="display:none;">
                                                <?= lang('Select_tax', 'tax_rate') ?>

                                                    <div class="input-group">

                                                    <?php
                                                        $tr[''] = lang('select') . ' ' . lang('tax');
                                                        $tr_default=$tax_rates[0]->id;
                                                        foreach ($tax_rates as $tax_rate) {
                                                            if($tax_rate->tax_default==1)
                                                                $tr_default=$tax_rate->id;

                                                            $tr[$tax_rate->id] = $tax_rate->name;
                                                        }
                                                        echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : ($product ? $product->tax_rate : $tr_default)), 'class="form-control select" id="tax_rate" placeholder="' . lang('select') . ' ' . lang('product_tax') . '" style="width:100%"')

                                                        ?>

                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('system_settings/taxs_details'); ?>" id="add_tax"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>




                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_tax">
                                        <div class="controls">
                                        <a href="<?= admin_url('system_settings/taxs_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                        </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>



                            <div class="col-lg-6">
                                        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                                        echo admin_form_open_multipart('system_settings/currencies_details', $attrib);
                                        ?>
                                        <fieldset class="scheduler-border">

                                        <legend class="scheduler-border"><?= lang('Share_currencies') ?></legend>



                                                <div class="form-group" id="all_currency">
                                                    <input type="radio" class="radio" name="all_currency" value="all" <?= isset($_POST['all_currency']) && $_POST['all_currency'] == 'all' ? 'checked' : 'checked' ?>>
                                                    <label for="all_currency" class="text-info padding05"><?= lang('all') ?></label>
                                                </div>
                                                <div class="form-group" id="one_currency">
                                                    <input type="radio" class="radio" name="all_currency" value="one_currency" <?= isset($_POST['all_currency']) && $_POST['all_currency'] == 'one_currency' ? 'checked' : '' ?>>
                                                    <label for="one_currency" class="text-info padding05"><?= lang('One_currency') ?></label>
                                                </div>
                                                <div class="form-group" id="attr-currency" style="display:none;">
                                                <?= lang('select') . ' ' . lang('currency') ?>
                                                    <div class="input-group">

                                                <?php
                                                              $curr[''] = lang('select') . ' ' . lang('currency');
                                                                foreach ($currencies as $currency) {
                                                                $curr[$currency->id] = $currency->name;
                                                                }
                                                                echo form_dropdown('currency', $curr, $Settings->default_currency, 'id="currency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" ');

                                                        ?>


                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= admin_url('system_settings/currencies_details'); ?>" id="add_currency"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-2x fa-share-alt" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>


                                        <div class="cleafix"></div>
                                    <div class="form-group" id="update_currency">
                                        <div class="controls">
                                        <a href="<?= admin_url('system_settings/currencies_details'); ?>" class="external" data-toggle="modal" data-target="#myModal">
                                            <?= form_submit('update_settings', lang('Share') . ' ' . lang('all'), 'class="btn btn-primary btn-lg"'); ?>
                                         </a>
                                        </div>
                                    </div>
                                    <?= form_close(); ?>


                                    </fieldset>
                            </div>






                       </div>
                   </div><!--    -->

               </div>
           </div>

       </div>
       </div>
</div> <!-- end second tab -->


</div> <!-- end tab-content -->



<script>
    $(document).ready(function() {


        $('#enable_grp').on('ifChecked', function () {
            $('#enable_co').iCheck('check');
        });
        $('#enable_grp').on('ifUnchecked', function () {

        });
    });
</script>
<script>
$(document).on('ifChecked', '#attributes', function (e) {
            $('#attr-con').slideDown();

        });

        $(document).on('ifUnchecked', '#attributes', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-con').slideUp();
        });
        $(document).on('ifChecked', '#attributes', function (e) {
            $('#attr-product').slideDown();
            $('#update_product').hide();
        });

        $(document).on('ifUnchecked', '#attributes', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-product').slideUp(function() {
            $('#update_product').show();
            });
        });

        $(document).on('ifChecked', '#one_unit', function (e) {
            $('#attr-unit').slideDown();
            $('#update_unit').hide();
        });

        $(document).on('ifUnchecked', '#one_unit', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-unit').slideUp(function() {
            $('#update_unit').show();
            });
        });
        $(document).on('ifChecked', '#one_category', function (e) {
            $('#attr-category').slideDown();
            $('#update_category').hide();
        });

        $(document).on('ifUnchecked', '#one_category', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-category').slideUp(function() {
            $('#update_category').show();
            });
        });
        $(document).on('ifChecked', '#one_tax', function (e) {
            $('#attr-tax').slideDown();
            $('#update_tax').hide();
        });

        $(document).on('ifUnchecked', '#one_tax', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-tax').slideUp(function() {
            $('#update_tax').show();
            });
        });
        $(document).on('ifChecked', '#one_currency', function (e) {
            $('#attr-currency').slideDown();
            $('#update_currency').hide();
        });

        $(document).on('ifUnchecked', '#one_currency', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-currency').slideUp(function() {
            $('#update_currency').show();
            });
        });
        $(document).on('ifChecked', '#one_brand', function (e) {
            $('#attr-brand').slideDown();
            $('#update_brand').hide();
        });

        $(document).on('ifUnchecked', '#one_brand', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-brand').slideUp(function() {
            $('#update_brand').show();
            });
        });
        $(document).on('ifChecked', '#one_customer', function (e) {
            $('#attr-customer').slideDown();
            $('#update_customer').hide();
        });

        $(document).on('ifUnchecked', '#one_customer', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-customer').slideUp(function() {
            $('#update_customer').show();
            });
        });
        $(document).on('ifChecked', '#one_supplier', function (e) {
            $('#attr-supplier').slideDown();
            $('#update_supplier').hide();
        });

        $(document).on('ifUnchecked', '#one_supplier', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-supplier').slideUp(function() {
            $('#update_supplier').show();
            });
        });
        $(document).on('ifChecked', '#one_variant', function (e) {
            $('#attr-variant').slideDown();
            $('#update_variant').hide();
        });

        $(document).on('ifUnchecked', '#one_variant', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-variant').slideUp(function() {
            $('#update_variant').show();
            });
        });

        $(document).on('ifChecked', '#attributes2', function (e) {
            $('#attr-category').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes2', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-category').slideUp();
        });
        $(document).on('ifChecked', '#attributes3', function (e) {
            $('#attr-brand').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes3', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-brand').slideUp();
        });
        $(document).on('ifChecked', '#attributes4', function (e) {
            $('#attr-variant').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes4', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-variant').slideUp();
        });
        $(document).on('ifChecked', '#attributes5', function (e) {
            $('#attr-brand').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes5', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-brand').slideUp();
        });
        $(document).on('ifChecked', '#attributes6', function (e) {
            $('#attr-tax').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes6', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-tax').slideUp();
        });
        $(document).on('ifChecked', '#attributes7', function (e) {
            $('#attr-currency').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes7', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-currency').slideUp();
        });
    </script>
<!--    <script>
$(document).ready(function() {
    $('#attributes').change(function() {
        if ($(this).is(':checked')) {
            $('#attr-con').removeClass('hide');
            $('#all_products').prop('checked', false);
        } else {
            $('#attr-con').addClass('hide');
        }
    });

    $('#all_products').change(function() {
        if ($(this).is(':checked')) {
            $('#attributes').prop('checked', false);
            $('#attr-con').addClass('hide');
        }
    });
});
</script> -->