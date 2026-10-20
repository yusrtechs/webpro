<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Pre-calculate summary values for the 4 cards
$default_biller_name = '-';
if (!empty($billers)) {
    foreach ($billers as $b) {
        if ($b->id == $pos->default_biller) {
            $default_biller_name = $b->company && $b->company != '-' ? $b->company : $b->name;
            break;
        }
    }
}

$printing_name = lang('local_install');
if ($pos->remote_printing == 1) {
    $printing_name = lang('web_browser_print');
} elseif ($pos->remote_printing == 3) {
    $printing_name = lang('php_pos_print_app');
}

$receipt_printer_title = '-';
if (!empty($printers)) {
    foreach ($printers as $pr) {
        if ($pr->id == $pos->printer || $pr->id == $pos->receipt_printer) {
            $receipt_printer_title = $pr->title;
            break;
        }
    }
}

$yn = ['1' => lang('yes'), '0' => lang('no')];
?>

<div class="spark-pos-settings">
    <!-- POS Settings Header -->
    <div class="spark-settings-header">
        <div class="spark-settings-title-area">
            <div class="spark-settings-header-badge">
                <i class="fa fa-cogs"></i>
            </div>
            <div>
                <h1 class="spark-settings-page-title"><?= lang('pos_settings'); ?></h1>
                <p class="spark-settings-page-subtitle"><?= lang('pos_config_desc') ; ?></p>
            </div>
        </div>

        <!-- Top Actions -->
        <div class="spark-settings-actions">
            <a href="<?= admin_url('pos/printers'); ?>" class="spark-btn-settings-outline">
                <i class="fa fa-print"></i>
                <span><?= lang('printers'); ?></span>
            </a>
        </div>
    </div>

    <!-- 4 Spark Settings Cards Grid -->
    <div class="spark-settings-grid">
        <!-- Card 1: POS Configuration -->
        <div class="spark-settings-card">
            <div class="spark-settings-card-header">
                <div class="spark-settings-card-icon indigo">
                    <i class="fa fa-desktop"></i>
                </div>
                <h3 class="spark-settings-card-title"><?= lang('pos_config'); ?></h3>
            </div>
            <div class="spark-settings-card-items">
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('pro_limit'); ?></span>
                    <span class="spark-settings-card-val"><?= $pos->pro_limit; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('default_biller'); ?></span>
                    <span class="spark-settings-card-val"><?= $default_biller_name; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('display_time'); ?></span>
                    <span class="spark-settings-card-val"><?= $pos->display_time ? lang('yes') : lang('no'); ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('order_type'); ?></span>
                    <span class="spark-settings-card-val"><?= $pos->order_type ? lang('yes') : lang('no'); ?></span>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#posConfigurationModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 2: POS Printing -->
        <div class="spark-settings-card">
            <div class="spark-settings-card-header">
                <div class="spark-settings-card-icon emerald">
                    <i class="fa fa-print"></i>
                </div>
                <h3 class="spark-settings-card-title"><?= lang('pos_printing'); ?></h3>
            </div>
            <div class="spark-settings-card-items">
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('printing'); ?></span>
                    <span class="spark-settings-card-val"><?= $printing_name; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('auto_print'); ?></span>
                    <span class="spark-settings-card-val"><?= $pos->auto_print ? lang('yes') : lang('no'); ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('use_local_printers'); ?></span>
                    <span class="spark-settings-card-val"><?= $pos->local_printers ? lang('yes') : lang('no'); ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('receipt_printer'); ?></span>
                    <span class="spark-settings-card-val"><?= $receipt_printer_title; ?></span>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#posPrintingModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 3: Receiving Custom Fields -->
        <div class="spark-settings-card">
            <div class="spark-settings-card-header">
                <div class="spark-settings-card-icon purple">
                    <i class="fa fa-list-alt"></i>
                </div>
                <h3 class="spark-settings-card-title"><?= lang('custom_fileds'); ?></h3>
            </div>
            <div class="spark-settings-card-items">
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('cf_title1'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->cf_title1) ? $pos->cf_title1 : '-'; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('cf_value1'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->cf_value1) ? $pos->cf_value1 : '-'; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('cf_title2'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->cf_title2) ? $pos->cf_title2 : '-'; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('cf_value2'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->cf_value2) ? $pos->cf_value2 : '-'; ?></span>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#receivingFieldsModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 4: Shortcuts -->
        <div class="spark-settings-card">
            <div class="spark-settings-card-header">
                <div class="spark-settings-card-icon amber">
                    <i class="fa fa-keyboard-o"></i>
                </div>
                <h3 class="spark-settings-card-title"><?= lang('shortcuts'); ?></h3>
            </div>
            <div class="spark-settings-card-items">
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('focus_add_item'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->focus_add_item) ? $pos->focus_add_item : '-'; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('cancel_sale'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->cancel_sale) ? $pos->cancel_sale : '-'; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('finalize_sale'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->finalize_sale) ? $pos->finalize_sale : '-'; ?></span>
                </div>
                <div class="spark-settings-card-row">
                    <span class="spark-settings-card-label"><?= lang('today_sale'); ?></span>
                    <span class="spark-settings-card-val"><?= !empty($pos->today_sale) ? $pos->today_sale : '-'; ?></span>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#posShortcutsModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: POS Configuration                                               -->
    <!-- ========================================================================= -->
    <div class="modal fade spark-settings-modal" id="posConfigurationModal" tabindex="-1" role="dialog" aria-labelledby="posConfigurationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="posConfigurationModalLabel"><i class="fa fa-desktop"></i> <?= lang('pos_config') ?: 'تكوين نقاط البيع'; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <?= admin_form_open('pos/save_pos_configuration', ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos_config_form']); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('pro_limit', 'limit'); ?>
                                <?= form_input('pro_limit', $pos->pro_limit, 'class="form-control" id="limit" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('delete_code', 'pin_code'); ?>
                                <?= form_password('pin_code', $pos->pin_code, 'class="form-control" pattern="[0-9]{4,8}" id="pin_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Report_pin_code', 'report_pin_code'); ?>
                                <?= form_password('report_pin_code', $pos->report_pin_code, 'class="form-control" pattern="[0-9]{4,8}" id="report_pin_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Slip_pin_code', 'slip_pin_code'); ?>
                                <?= form_password('slip_pin_code', $pos->slip_pin_code, 'class="form-control" pattern="[0-9]{4,8}" id="slip_pin_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Expenses_pin_code', 'expenses_pin_code'); ?>
                                <?= form_password('expenses_pin_code', $pos->expenses_pin_code, 'class="form-control" pattern="[0-9]{4,8}" id="expenses_pin_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Collections_pin_code', 'collections_pin_code'); ?>
                                <?= form_password('collections_pin_code', $pos->collections_pin_code, 'class="form-control" pattern="[0-9]{4,8}" id="collections_pin_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Add_product_pin_code', 'add_product_pin_code'); ?>
                                <?= form_password('add_product_pin_code', $pos->add_product_pin_code, 'class="form-control" pattern="[0-9]{4,8}" id="add_product_pin_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Customers_pin_code', 'customers_pin_code'); ?>
                                <?= form_password('customers_pin_code', $pos->customers_pin_code, 'class="form-control" pattern="[0-9]{4,8}" id="customers_pin_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('default_category', 'default_category'); ?>
                                <?php
                                $ct[''] = lang('select') . ' ' . lang('default_category');
                                foreach ($categories as $catrgory) {
                                    $ct[$catrgory->id] = $catrgory->name;
                                }
                                echo form_dropdown('category', $ct, $pos->default_category, 'class="form-control" id="default_category" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('default_biller', 'default_biller'); ?>
                                <?php
                                $bl[0] = '';
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                if (isset($_POST['biller'])) {
                                    $biller = $_POST['biller'];
                                } else {
                                    $biller = '';
                                }
                                echo form_dropdown('biller', $bl, $pos->default_biller, 'class="form-control" id="default_biller" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('default_customer', 'customer1'); ?>
                                <?= form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : $pos->default_customer), 'id="customer1" data-placeholder="' . lang('select') . ' ' . lang('customer') . '" required="required" class="form-control" style="width:100%;"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('display_time', 'display_time'); ?>
                                <?= form_dropdown('display_time', $yn, $pos->display_time, 'class="form-control" id="display_time" required="required"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('onscreen_keyboard', 'keyboard'); ?>
                                <?= form_dropdown('keyboard', $yn, $pos->keyboard, 'class="form-control" id="keyboard" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('product_button_color', 'product_button_color'); ?>
                                <?php $col = ['default' => lang('default'), 'primary' => lang('primary'), 'info' => lang('info'), 'warning' => lang('warning'), 'danger' => lang('danger')];
                                echo form_dropdown('product_button_color', $col, $pos->product_button_color, 'class="form-control" id="product_button_color" required="required"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('tooltips', 'tooltips'); ?>
                                <?= form_dropdown('tooltips', $yn, $pos->tooltips, 'class="form-control" id="tooltips" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('rounding', 'rounding'); ?>
                                <?php
                                $rnd = ['0' => lang('disable'), '1' => lang('to_nearest_005'), '2' => lang('to_nearest_050'), '3' => lang('to_nearest_number'), '4' => lang('to_next_number')];
                                echo form_dropdown('rounding', $rnd, $pos->rounding, 'class="form-control" id="rounding" required="required"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('item_order', 'item_order'); ?>
                                <?php $oopts = [0 => lang('default'), 1 => lang('category')]; ?>
                                <?= form_dropdown('item_order', $oopts, $pos->item_order, 'class="form-control" id="item_order" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('after_sale_page', 'after_sale_page'); ?>
                                <?php $popts = [0 => lang('receipt'), 1 => lang('pos')]; ?>
                                <?= form_dropdown('after_sale_page', $popts, $pos->after_sale_page, 'class="form-control" id="after_sale_page" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('display_customer_details', 'customer_details'); ?>
                                <?php $popts = [0 => lang('no'), 1 => lang('yes')]; ?>
                                <?= form_dropdown('customer_details', $popts, $pos->customer_details, 'class="form-control" id="customer_details" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('tax_method_type', 'tax_method_type'); ?>
                                <?php $type = ['2' => lang('tax_products_type'), '1' => lang('tax_invoices_type')]; ?>
                                <?= form_dropdown('tax_method_type', $type, $pos->tax_method_type, 'id="tax_method_type" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('tax_method_type') . '" required="required" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('order_type', 'order_type'); ?>
                                <?php $otype = ['0' => lang('no'), '1' => lang('yes')]; ?>
                                <?= form_dropdown('order_type', $otype, $pos->order_type, 'id="order_type" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('order_type') . '" required="required" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('categories_tab', 'categories_tab'); ?>
                                <?php $ctype = ['0' => lang('no'), '1' => lang('yes')]; ?>
                                <?= form_dropdown('categories_tab', $ctype, $pos->categories_tab, 'id="categories_tab" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('order_type') . '" required="required" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Reciept_A4_page_size', 'pos_reciept_a4_page_size'); ?>
                                <?php $ctype = ['0' => lang('no'), '1' => lang('yes')]; ?>
                                <?= form_dropdown('pos_reciept_a4_page_size', $ctype, $pos->pos_reciept_a4_page_size, 'id="pos_reciept_a4_page_size" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Reciept_A4_page_size') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Screen_style', 'pos_screen_style'); ?>
                                <?php $ctype = ['0' => lang('Small'), '1' => lang('Medium'), '2' => lang('Large')]; ?>
                                <?= form_dropdown('pos_screen_style', $ctype, $pos->pos_screen_style, 'id="pos_screen_style" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Screen_style') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Sale_screen', 'screen_style'); ?>
                                <?php $ttype = ['0' => lang('Main_sale_screen'), '1' => lang('Touch_sale_screen')]; ?>
                                <?= form_dropdown('screen_style', $ttype, $pos->screen_style, 'id="screen_style" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Sale_screen') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Insurance_use', 'RestInsurance'); ?>
                                <?= form_dropdown('RestInsurance', $yn, $pos->RestInsurance, 'id="RestInsurance" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Insurance_use') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Delete_from_basket', 'AnyUserCanDeleteFromBasket'); ?>
                                <?= form_dropdown('AnyUserCanDeleteFromBasket', $yn, $pos->AnyUserCanDeleteFromBasket, 'id="AnyUserCanDeleteFromBasket" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Delete_from_basket') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('pin_code_to_delete_from_basket', 'DeleteFromBaskerPinCode'); ?>
                                <?= form_password('DeleteFromBaskerPinCode', $pos->DeleteFromBaskerPinCode, 'class="form-control" pattern="[0-9]{4,8}" id="DeleteFromBaskerPinCode"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Increase_or_decrease_the_quantity_in_the_basket', 'AnyUserCanPlusMinusFromBasket'); ?>
                                <?= form_dropdown('AnyUserCanPlusMinusFromBasket', $yn, $pos->AnyUserCanPlusMinusFromBasket, 'id="AnyUserCanPlusMinusFromBasket" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Increase_or_decrease_the_quantity_in_the_basket') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_shipping', 'ShowShippingInSales'); ?>
                                <?= form_dropdown('ShowShippingInSales', $yn, $pos->ShowShippingInSales, 'id="ShowShippingInSales" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_shipping') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_insurance', 'ShowInsuranceInSales'); ?>
                                <?= form_dropdown('ShowInsuranceInSales', $yn, $pos->ShowInsuranceInSales, 'id="ShowInsuranceInSales" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_insurance') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Search_product_by_name', 'searchbyitemname'); ?>
                                <?= form_dropdown('searchbyitemname', $yn, $pos->searchbyitemname, 'id="searchbyitemname" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Search_product_by_name') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_screen_settings', 'Opensetting'); ?>
                                <?= form_dropdown('Opensetting', $yn, $pos->Opensetting, 'id="Opensetting" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_screen_settings') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_today_sale', 'Opentodaysales'); ?>
                                <?= form_dropdown('Opentodaysales', $yn, $pos->Opentodaysales, 'id="Opentodaysales" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_today_sale') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_expenses', 'OpenExpensiveView'); ?>
                                <?= form_dropdown('OpenExpensiveView', $yn, $pos->OpenExpensiveView, 'id="OpenExpensiveView" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_expenses') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_quotes', 'OpentQuotesView'); ?>
                                <?= form_dropdown('OpentQuotesView', $yn, $pos->OpentQuotesView, 'id="OpentQuotesView" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_quotes') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_coupons', 'OpentCouponsInSales'); ?>
                                <?= form_dropdown('OpentCouponsInSales', $yn, $pos->OpentCouponsInSales, 'id="OpentCouponsInSales" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_coupons') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Show_product_code_on_invoice', 'show_product_code_on_invoice'); ?>
                                <?= form_dropdown('show_product_code_on_invoice', $yn, $pos->show_product_code_on_invoice, 'id="show_product_code_on_invoice" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Show_product_code_on_invoice') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Send_receipt_to_warehouse', 'send_receipt_to_warehouse'); ?>
                                <?= form_dropdown('send_receipt_to_warehouse', $yn, $pos->send_receipt_to_warehouse, 'id="send_receipt_to_warehouse" class="form-control pos-input-tip" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Send_receipt_to_warehouse') . '" style="width:100%;" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 warehouse_receipt">
                            <div class="form-group">
                                <?= lang('Number_of_copies_of_the_warehouse_receipt', 'warehouse_receipt_print_copy_count'); ?>
                                <?= form_input('warehouse_receipt_print_copy_count', $pos->warehouse_receipt_print_copy_count, 'class="form-control" id="warehouse_receipt_print_copy_count" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Activating_NFC_electronic_payment', 'nfc_enable'); ?>
                                <?php $nfc = ['1' => lang('yes'), '0' => lang('no')]; ?>
                                <?= form_dropdown('nfc_enable', $nfc, $pos->nfc_enable, 'class="form-control" id="nfc_enable" '); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('Product_name_display', 'product_name_display'); ?>
                                <?php $pop = ['not_selected' => lang('Not_selected'), 'arabic' => lang('Arabic'), 'arabic_english' => lang('Arabic_English'), 'english' => lang('English')]; ?>
                                <?= form_dropdown('product_name_display', $pop, $pos->product_name_display, 'class="form-control" id="product_name_display" '); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                    <?= form_submit('update_settings', lang('update_settings'), 'class="btn btn-primary"'); ?>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: POS Printing                                                     -->
    <!-- ========================================================================= -->
    <div class="modal fade spark-settings-modal" id="posPrintingModal" tabindex="-1" role="dialog" aria-labelledby="posPrintingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="posPrintingModalLabel"><i class="fa fa-print"></i> <?= lang('pos_printing') ?: 'POS Printing'; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <?= admin_form_open('pos/save_pos_printing', ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos_printing_form']); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('printing', 'remote_printing'); ?>
                                <?php
                                $opts = [0 => lang('local_install'), 1 => lang('web_browser_print'), 3 => lang('php_pos_print_app')];
                                ?>
                                <?= form_dropdown('remote_printing', $opts, $pos->remote_printing, 'class="form-control select2" id="remote_printing" style="width:100%;" required="required"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="printers col-md-12" style="padding: 0;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('auto_print', 'auto_print'); ?> <strong>*</strong>
                                    <?= form_dropdown('auto_print', $yn, $pos->auto_print, 'class="form-control select2" id="auto_print" style="width:100%;"'); ?>
                                </div>
                            </div>

                            <div class="ppp col-md-6" style="padding-inline-start: 0; padding-inline-end: 0;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('use_local_printers', 'local_printers'); ?>
                                        <?= form_dropdown('local_printers', $yn, set_value('local_printers', $pos->local_printers), 'class="form-control tip" id="local_printers" required="required"'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="lp col-md-12" style="padding: 0;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('receipt_printer', 'receipt_printer'); ?> <strong>*</strong>
                                        <?php
                                        $printer_opts = [];
                                        if (!empty($printers)) {
                                            foreach ($printers as $printer) {
                                                $printer_opts[$printer->id] = $printer->title;
                                            }
                                        }
                                        ?>
                                        <?= form_dropdown('receipt_printer', $printer_opts, $pos->printer, 'class="form-control select2" id="receipt_printer" style="width:100%;"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('order_printers', 'order_printers'); ?> <strong>*</strong>
                                        <?= form_dropdown('order_printers[]', $printer_opts, '', 'multiple class="form-control select2" id="order_printers" style="width:100%;"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('cash_drawer_codes', 'cash_drawer_codes'); ?>
                                        <?= form_input('cash_drawer_codes', $pos->cash_drawer_codes, 'class="form-control" id="cash_drawer_codes" placeholder="\\x1C"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                    <?= form_submit('update_settings', lang('update_settings'), 'class="btn btn-primary"'); ?>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: Receiving Custom Fields                                          -->
    <!-- ========================================================================= -->
    <div class="modal fade spark-settings-modal" id="receivingFieldsModal" tabindex="-1" role="dialog" aria-labelledby="receivingFieldsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="receivingFieldsModalLabel"><i class="fa fa-list-alt"></i> <?= lang('custom_fileds') ?: 'الحقول المخصصة لاستلام'; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <?= admin_form_open('pos/save_receiving_fields', ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'receiving_fields_form']); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('cf_title1', 'tcf1'); ?>
                                <?= form_input('cf_title1', $pos->cf_title1, 'class="form-control tip" id="tcf1"'); ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('cf_value1', 'vcf1'); ?>
                                <?= form_input('cf_value1', $pos->cf_value1, 'class="form-control tip" id="vcf1"'); ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('cf_title2', 'tcf2'); ?>
                                <?= form_input('cf_title2', $pos->cf_title2, 'class="form-control tip" id="tcf2"'); ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <?= lang('cf_value2', 'vcf2'); ?>
                                <?= form_input('cf_value2', $pos->cf_value2, 'class="form-control tip" id="vcf2"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                    <?= form_submit('update_settings', lang('update_settings'), 'class="btn btn-primary"'); ?>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: Shortcuts                                                        -->
    <!-- ========================================================================= -->
    <div class="modal fade spark-settings-modal" id="posShortcutsModal" tabindex="-1" role="dialog" aria-labelledby="posShortcutsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="posShortcutsModalLabel"><i class="fa fa-keyboard-o"></i> <?= lang('shortcuts') ?: 'الاختصارات'; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <?= admin_form_open('pos/save_pos_shortcuts', ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos_shortcuts_form']); ?>
                <div class="modal-body">
                    <p style="color: var(--spark-text-secondary, #64748b); margin-bottom: 20px;"><?= lang('shortcut_heading'); ?></p>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('focus_add_item', 'focus_add_item'); ?>
                                <?= form_input('focus_add_item', $pos->focus_add_item, 'class="form-control tip" id="focus_add_item"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('add_manual_product', 'add_manual_product'); ?>
                                <?= form_input('add_manual_product', $pos->add_manual_product, 'class="form-control tip" id="add_manual_product"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('customer_selection', 'customer_selection'); ?>
                                <?= form_input('customer_selection', $pos->customer_selection, 'class="form-control tip" id="customer_selection"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('add_customer', 'add_customer'); ?>
                                <?= form_input('add_customer', $pos->add_customer, 'class="form-control tip" id="add_customer"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('toggle_category_slider', 'toggle_category_slider'); ?>
                                <?= form_input('toggle_category_slider', $pos->toggle_category_slider, 'class="form-control tip" id="toggle_category_slider"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('toggle_subcategory_slider', 'toggle_subcategory_slider'); ?>
                                <?= form_input('toggle_subcategory_slider', $pos->toggle_subcategory_slider, 'class="form-control tip" id="toggle_subcategory_slider"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('toggle_brands_slider', 'toggle_brands_slider'); ?>
                                <?= form_input('toggle_brands_slider', $pos->toggle_brands_slider, 'class="form-control tip" id="toggle_brands_slider"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('cancel_sale', 'cancel_sale'); ?>
                                <?= form_input('cancel_sale', $pos->cancel_sale, 'class="form-control tip" id="cancel_sale"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('suspend_sale', 'suspend_sale'); ?>
                                <?= form_input('suspend_sale', $pos->suspend_sale, 'class="form-control tip" id="suspend_sale"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('print_items_list', 'print_items_list'); ?>
                                <?= form_input('print_items_list', $pos->print_items_list, 'class="form-control tip" id="print_items_list"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('finalize_sale', 'finalize_sale'); ?>
                                <?= form_input('finalize_sale', $pos->finalize_sale, 'class="form-control tip" id="finalize_sale"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('today_sale', 'today_sale'); ?>
                                <?= form_input('today_sale', $pos->today_sale, 'class="form-control tip" id="today_sale"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('open_hold_bills', 'open_hold_bills'); ?>
                                <?= form_input('open_hold_bills', $pos->open_hold_bills, 'class="form-control tip" id="open_hold_bills"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('close_register', 'close_register'); ?>
                                <?= form_input('close_register', $pos->close_register, 'class="form-control tip" id="close_register"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                    <?= form_submit('update_settings', lang('update_settings'), 'class="btn btn-primary"'); ?>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Modal Select2 dropdownParent binding on show
        $('.spark-settings-modal').on('shown.bs.modal', function () {
            var $modal = $(this);
            $modal.find('select.select2, select.select').each(function () {
                if (!$(this).data('select2')) {
                    $(this).select2({
                        dropdownParent: $modal,
                        minimumResultsForSearch: 7
                    });
                }
            });
        });

        // Initialize order_printers
        $("#order_printers").select2().select2('val', <?= !empty($pos->order_printers) ? $pos->order_printers : '[]'; ?>);

        // Remote printing toggles
        function handleRemotePrinting() {
            var val = $('#remote_printing').val();
            if (val == 1) {
                $('.printers').slideUp();
            } else if (val == 0) {
                $('.printers').slideDown();
                $('.ppp').slideUp();
                $('.lp').slideDown();
            } else {
                $('.printers').slideDown();
                $('.ppp').slideDown();
                if ($('#local_printers').val() == 1) {
                    $('.lp').slideUp();
                } else {
                    $('.lp').slideDown();
                }
            }
        }

        handleRemotePrinting();

        $('#remote_printing').off('change.posPrinting').on('change.posPrinting', function () {
            handleRemotePrinting();
        });

        $('#local_printers').off('change.posPrinting').on('change.posPrinting', function () {
            if ($(this).val() == 1) {
                $('.lp').slideUp();
            } else {
                $('.lp').slideDown();
            }
        });

        $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });

        // Customer Select2 AJAX autocomplete
        $('#customer1').val('<?= $pos->default_customer; ?>').select2({
            minimumInputLength: 1,
            dropdownParent: $('#posConfigurationModal'),
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: site.base_url + "customers/getCustomer/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
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
        });

        // Warehouse receipt copy count toggle
        function handleWarehouseReceipt() {
            if ($('#send_receipt_to_warehouse').val() == 1) {
                $('.warehouse_receipt').show();
            } else {
                $('.warehouse_receipt').hide();
            }
        }

        handleWarehouseReceipt();

        $('#send_receipt_to_warehouse').off('change.posConfig').on('change.posConfig', function () {
            handleWarehouseReceipt();
        });
    });
</script>
