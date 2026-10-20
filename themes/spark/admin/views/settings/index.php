<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$wm = ['0' => lang('no'), '1' => lang('yes')];
$ps = ['0' => lang('disable'), '1' => lang('enable')];
?>
<style>
    /* Preview box */
    .image-preview {
      height: 222px;
      border: 2px dashed #ccc;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #EDEDED;
      margin-top: 10px;
    }

    .image-preview img {
      max-width: 100%;
      max-height: 100%;
      border-radius: 8px;
    }
</style>

<div class="spark-settings-hub spark-settings-page">
    <!-- Settings Header -->
    <div class="spark-settings-header">
        <div class="spark-settings-title-area">
            <div class="spark-settings-header-badge">
                <i class="fa fa-cog"></i>
            </div>
            <div>
                <h1 class="spark-settings-page-title"><?= lang('system_settings'); ?></h1>
                <p class="spark-settings-page-subtitle"><?= lang('System_settings_and_configurations_management'); ?></p>
            </div>
        </div>

        <!-- Top Actions -->
        <div class="spark-settings-actions">
            <a href="<?= admin_url('system_settings/whatsapp_settings'); ?>" class="spark-btn-whatsapp">
                <i class="fa fa-whatsapp"></i>
                <span><?= lang('Whatsapp_settings'); ?></span>
            </a>
            <a href="<?= admin_url('system_settings/user_groups'); ?>" class="spark-btn-settings-outline">
                <i class="fa fa-users"></i>
                <span><?= lang('group_permissions'); ?></span>
            </a>
            <a href="<?= admin_url('system_settings/change_logo'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-settings-outline">
                <i class="fa fa-picture-o"></i>
                <span><?= lang('change_logo'); ?></span>
            </a>
        </div>
    </div>

    <!-- 9 Spark Settings Cards Grid -->
    <div class="spark-settings-grid">
        <!-- Card 1: إعدادات الموقع -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon blue">
                        <i class="fa fa-sliders"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('Company_config'); ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('site_name'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->site_name; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('language'); ?></span>
                        <span class="spark-settings-row-value"><?= ucfirst($Settings->language); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('default_currency'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->default_currency; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('timezone'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->timezone; ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsSiteModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 2: المنتجات -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon emerald">
                        <i class="fa fa-barcode"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('products') ?: 'المنتجات'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('product_tax'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->default_tax_rate ? lang('enable') : lang('disable'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('product_serial'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->product_serial ? lang('enable') : lang('disable'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('product_discount'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->product_discount ? lang('enable') : lang('disable'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('auto_detect_barcode'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->auto_detect_barcode ? lang('enable') : lang('disable'); ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsProductsModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 3: مبيعات -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon purple">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('sales') ?: 'مبيعات'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('over_selling'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->overselling ? lang('yes') : lang('no'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('reference_format'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->reference_format == 1 ? lang('prefix_year_no') : ($Settings->reference_format == 2 ? lang('prefix_month_year_no') : ($Settings->reference_format == 3 ? lang('sequence_number') : lang('random_number'))); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('Open_invoice'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->isOpen_invoice ? lang('yes') : lang('no'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('Insurance_business'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->IsInsuranceBusiness ? lang('yes') : lang('no'); ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsSalesModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 4: بادئة -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon amber">
                        <i class="fa fa-tag"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('prefix') ?: 'بادئة'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('sales_prefix'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->sales_prefix ?: '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('quote_prefix'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->quote_prefix ?: '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('purchase_prefix'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->purchase_prefix ?: '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('payment_prefix'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->payment_prefix ?: '-'; ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsPrefixesModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 5: تنسيق المال وعدد -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon cyan">
                        <i class="fa fa-money"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('money_number_format') ?: 'تنسيق المال وعدد'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('decimals'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->decimals; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('qty_decimals'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->qty_decimals; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('decimals_sep'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->decimals_sep == '.' ? lang('dot') : lang('comma'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('thousands_sep'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->thousands_sep == '.' ? lang('dot') : ($Settings->thousands_sep == ',' ? lang('comma') : lang('space')); ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsMoneyModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 6: تفاصيل البنك -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon rose">
                        <i class="fa fa-university"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('bank_details') ?: 'تفاصيل البنك'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('BANK_NAME'); ?></span>
                        <span class="spark-settings-row-value"><?= !empty($Settings->bank_name) ? $Settings->bank_name : '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('Account_name'); ?></span>
                        <span class="spark-settings-row-value"><?= !empty($Settings->account_name) ? $Settings->account_name : '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('IBAN'); ?></span>
                        <span class="spark-settings-row-value"><?= !empty($Settings->IBAN) ? $Settings->IBAN : '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('Branch_name'); ?></span>
                        <span class="spark-settings-row-value"><?= !empty($Settings->branch_name) ? $Settings->branch_name : '-'; ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsBankModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 7: البريد الإلكتروني -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon indigo">
                        <i class="fa fa-envelope-o"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('email') ?: 'البريد الإلكتروني'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('email_protocol'); ?></span>
                        <span class="spark-settings-row-value"><?= strtoupper($Settings->protocol); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('smtp_host'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->protocol == 'smtp' ? ($Settings->smtp_host ?: '-') : '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('smtp_port'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->protocol == 'smtp' ? ($Settings->smtp_port ?: '-') : '-'; ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('smtp_crypto'); ?></span>
                        <span class="spark-settings-row-value"><?= $Settings->protocol == 'smtp' ? ($Settings->smtp_crypto ? strtoupper($Settings->smtp_crypto) : lang('none')) : '-'; ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsEmailModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 8: النقاط المستحقة -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon teal">
                        <i class="fa fa-star"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('award_points') ?: 'النقاط المستحقة'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('customer_award_points'); ?></span>
                        <span class="spark-settings-row-value"><?= ($Settings->each_spent && $Settings->ca_point) ? ($Settings->each_spent . ' ➔ ' . $Settings->ca_point) : lang('disabled'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('staff_award_points'); ?></span>
                        <span class="spark-settings-row-value"><?= ($Settings->each_sale && $Settings->sa_point) ? ($Settings->each_sale . ' ➔ ' . $Settings->sa_point) : lang('disabled'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('award_points'); ?></span>
                        <span class="spark-settings-row-value"><?= lang('active'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('status'); ?></span>
                        <span class="spark-settings-row-value"><?= ($Settings->ca_point || $Settings->sa_point) ? (lang('active')) : (lang('disabled')); ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsPointsModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>

        <!-- Card 9: عكس النقاط المستحقة -->
        <div class="spark-settings-card">
            <div>
                <div class="spark-settings-card-header">
                    <div class="spark-settings-card-icon orange">
                        <i class="fa fa-undo"></i>
                    </div>
                    <h3 class="spark-settings-card-title"><?= lang('Reverse_award_points') ?: 'عكس النقاط المستحقة'; ?></h3>
                </div>
                <div class="spark-settings-card-items">
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('Customer_award_amounts'); ?></span>
                        <span class="spark-settings-row-value"><?= ($Settings->reverse_each_spent && $Settings->reverse_ca_point) ? ($Settings->reverse_each_spent . ' ➔ ' . $Settings->reverse_ca_point) : lang('disabled'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('Staff_award_amounts'); ?></span>
                        <span class="spark-settings-row-value"><?= ($Settings->reverse_each_sale && $Settings->reverse_sa_point) ? ($Settings->reverse_each_sale . ' ➔ ' . $Settings->reverse_sa_point) : lang('disabled'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('Reverse_points'); ?></span>
                        <span class="spark-settings-row-value"><?= lang('active'); ?></span>
                    </div>
                    <div class="spark-settings-card-row">
                        <span class="spark-settings-row-label"><?= lang('status'); ?></span>
                        <span class="spark-settings-row-value"><?= ($Settings->reverse_ca_point || $Settings->reverse_sa_point) ? (lang('active')) : (lang('disabled')); ?></span>
                    </div>
                </div>
            </div>
            <button type="button" class="spark-settings-card-btn" data-toggle="modal" data-target="#settingsReversePointsModal">
                <i class="fa fa-pencil-square-o"></i>
                <span><?= lang('Edit_settings'); ?></span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 9 INDEPENDENT SETTINGS MODALS                                             -->
<!-- ========================================================================= -->

<!-- Modal 1: إعدادات الموقع -->
<div class="modal fade spark-settings-modal" id="settingsSiteModal" tabindex="-1" role="dialog" aria-labelledby="settingsSiteModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsSiteModalLabel"><i class="fa fa-sliders"></i> <?= lang('Company_config') ?: 'إعدادات الموقع'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_site_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('site_name', 'site_name'); ?>
                            <?= form_input('site_name', $Settings->site_name, 'class="form-control tip" id="site_name" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('English_site_name', 'site_ename'); ?>
                            <?= form_input('site_ename', $Settings->site_ename, 'class="form-control tip" id="site_ename"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('language', 'language'); ?>
                            <?php
                            $lang_opts = [
                                'arabic'               => 'Arabic',
                                'english'              => 'English',
                            ];
                            echo form_dropdown('language', $lang_opts, $Settings->language, 'class="form-control tip" id="language" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="currency"><?= lang('default_currency'); ?></label>
                            <div class="controls">
                                <?php
                                $cu = [];
                                foreach ($currencies as $currency) {
                                    $cu[$currency->code] = $currency->name;
                                }
                                echo form_dropdown('currency', $cu, $Settings->default_currency, 'class="form-control tip" id="currency" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('accounting_method', 'accounting_method'); ?>
                            <?php
                            $am = [ 2 => 'AVCO (Average Cost Method)'];
                            echo form_dropdown('accounting_method', $am, $Settings->accounting_method, 'class="form-control tip" id="accounting_method" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <?php if (isset($Settings->preAccounting) && $Settings->preAccounting == 1) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="accounting_type"><?= lang('Accounting_type'); ?></label>
                                <div class="controls">
                                    <?php
                                    $at = ['' => '', 'continuous' => lang('Continuous_inventory'), 'periodic' => lang('Periodic_inventory')];
                                    echo form_dropdown('accounting_type', $at, $Settings->accounting_type, 'class="form-control tip" id="accounting_type" data-placeholder="' . lang('select') . ' ' . lang('Accounting_type') . '" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="email"><?= lang('default_email'); ?></label>
                            <?= form_input('email', $Settings->default_email, 'class="form-control tip" required="required" id="email"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="customer_group"><?= lang('default_customer_group'); ?></label>
                            <?php
                            $pgs = [];
                            foreach ($customer_groups as $customer_group) {
                                $pgs[$customer_group->id] = $customer_group->name;
                            }
                            echo form_dropdown('customer_group', $pgs, $Settings->customer_group, 'class="form-control tip" id="customer_group" style="width:100%;" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="price_group"><?= lang('default_price_group'); ?></label>
                            <?php
                            $cgs = [];
                            foreach ($price_groups as $price_group) {
                                $cgs[$price_group->id] = $price_group->name;
                            }
                            echo form_dropdown('price_group', $cgs, $Settings->price_group, 'class="form-control tip" id="price_group" style="width:100%;" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4" style="display:none">
                        <div class="form-group">
                            <?= lang('maintenance_mode', 'mmode'); ?>
                            <div class="controls">
                                <?= form_dropdown('mmode', $wm, (isset($_POST['mmode']) ? $_POST['mmode'] : $Settings->mmode), 'class="tip form-control" required="required" id="mmode" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="theme"><?= lang('theme'); ?></label>
                            <div class="controls">
                                <?php
                                $themes = ['default' => 'Default'];
                                echo form_dropdown('theme', $themes, $Settings->theme, 'id="theme" class="form-control tip" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="display:none">
                        <div class="form-group">
                            <label class="control-label" for="rtl"><?= lang('rtl_support'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('rtl', $ps, $Settings->rtl, 'id="rtl" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="captcha"><?= lang('login_captcha'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('captcha', $ps, $Settings->captcha, 'id="captcha" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="disable_editing"><?= lang('disable_editing'); ?></label>
                            <?= form_input('disable_editing', $Settings->disable_editing, 'class="form-control tip" id="disable_editing" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="rows_per_page"><?= lang('rows_per_page'); ?></label>
                            <?php
                            $rppopts = ['10' => '10', '25' => '25', '50' => '50', '100' => '100'];
                            echo form_dropdown('rows_per_page', $rppopts, $Settings->rows_per_page, 'id="rows_per_page" class="form-control tip" style="width:100%;" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="dateformat"><?= lang('dateformat'); ?></label>
                            <div class="controls">
                                <?php
                                $dt = [];
                                foreach ($date_formats as $date_format) {
                                    $dt[$date_format->id] = $date_format->js;
                                }
                                echo form_dropdown('dateformat', $dt, $Settings->dateformat, 'id="dateformat" class="form-control tip" style="width:100%;" required="required"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="timezone"><?= lang('timezone'); ?></label>
                            <?php
                            $timezone_identifiers = DateTimeZone::listIdentifiers();
                            $tz = [];
                            foreach ($timezone_identifiers as $tzi) {
                                $tz[$tzi] = $tzi;
                            }
                            echo form_dropdown('timezone', $tz, TIMEZONE, 'class="form-control tip" id="timezone" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="restrict_calendar"><?= lang('calendar'); ?></label>
                            <div class="controls">
                                <?php
                                $opt_cal = [1 => lang('private'), 0 => lang('shared')];
                                echo form_dropdown('restrict_calendar', $opt_cal, $Settings->restrict_calendar, 'class="form-control tip" required="required" id="restrict_calendar" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) { ?>
                                <label class="control-label" for="warehouse"><?= lang('Default_showroom'); ?></label>
                            <?php } else { ?>
                                <label class="control-label" for="warehouse"><?= lang('default_warehouse'); ?></label>
                            <?php } ?>
                            <div class="controls">
                                <?php
                                $wh = [];
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name . ' (' . $warehouse->code . ')';
                                }
                                echo form_dropdown('warehouse', $wh, $Settings->default_warehouse, 'class="form-control tip" id="warehouse" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('default_biller', 'biller'); ?>
                            <?php
                            $bl = ['' => ''];
                            foreach ($billers as $biller) {
                                $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                            }
                            echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $Settings->default_biller), 'id="biller" data-placeholder="' . lang('select') . ' ' . lang('biller') . '" required="required" class="form-control input-tip select" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('pdf_lib', 'pdf_lib'); ?>
                            <?php $pdflibs = ['mpdf' => 'mPDF', 'dompdf' => 'Dompdf']; ?>
                            <?= form_dropdown('pdf_lib', $pdflibs, $Settings->pdf_lib, 'class="form-control tip" id="pdf_lib" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Restaurant_options', 'IsResturant'); ?>
                            <?php $restaurant_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('IsResturant', $restaurant_options, $Settings->IsResturant, 'class="form-control tip" id="IsResturant" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 menunotifications">
                        <div class="form-group">
                            <?= lang('Activate_menu_notifications', 'MenuNotificationsactive'); ?>
                            <?php $menunotificationsactive_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('MenuNotificationsactive', $menunotificationsactive_options, $Settings->MenuNotificationsactive, 'class="form-control tip" id="MenuNotificationsactive"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 menunotifications">
                        <div class="form-group">
                            <?= lang('Repeat_the_notifications_in_minutes', 'MenuNotificationsRepeatTime'); ?>
                            <?= form_input('MenuNotificationsRepeatTime', $Settings->MenuNotificationsRepeatTime, 'class="form-control tip" id="MenuNotificationsRepeatTime" placeholder="' . lang('Repeat_the_notifications_in_minutes') . '" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Services_sector', 'IsServices'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('IsServices', $services_options, $Settings->IsServices, 'class="form-control tip" id="IsServices" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Automotive_services_sector', 'IsAutomotiveServices'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('IsAutomotiveServices', $services_options, $Settings->IsAutomotiveServices, 'class="form-control tip" id="IsAutomotiveServices" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Lundry_sector', 'Lundry_sector'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('IsLundry', $services_options, $Settings->IsLundry, 'class="form-control tip" id="Lundry_sector"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Station_sector', 'Station_sector'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('isStation', $services_options, $Settings->isStation, 'class="form-control tip" id="Station_sector"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Logistic_sector', 'isLogistic'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('isLogistic', $services_options, $Settings->isLogistic, 'class="form-control tip" id="isLogistic"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Shipping_sector', 'isShipping'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('isShipping', $services_options, $Settings->isShipping, 'class="form-control tip" id="isShipping"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Provides_tobacco_products', 'isTobacco'); ?>
                            <?php $tobacco_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('isTobacco', $tobacco_options, $Settings->isTobacco, 'class="form-control tip" id="isTobacco"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 tobacco">
                        <div class="form-group">
                            <?= lang('Tobacco_fees', 'tobacco_tax'); ?>
                            <?= form_input('tobacco_tax', $Settings->tobacco_tax, 'class="form-control tip" id="tobacco_tax" placeholder="' . lang('Tobacco_fees') . '" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Rental_sector', 'IsRental'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('IsRental', $services_options, $Settings->IsRental, 'class="form-control tip" id="IsRental" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Equipment_Rental_Sector', 'IsEquipmentRental'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('IsEquipmentRental', $services_options, $Settings->IsEquipmentRental, 'class="form-control tip" id="IsEquipmentRental" required="required"'); ?>
                        </div>
                    </div>
                    <?php if (isset($Settings->preAccounting) && $Settings->preAccounting == 1) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Create_an_account_automatically_for_offline_customers', 'isAutoAccountOfflineCustomers'); ?>
                                <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                                <?= form_dropdown('isAutoAccountOfflineCustomers', $services_options, $Settings->isAutoAccountOfflineCustomers, 'class="form-control tip" id="isAutoAccountOfflineCustomers"'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Donation', 'isDonation'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('isDonation', $services_options, $Settings->isDonation, 'class="form-control tip" id="isDonation"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Curtains_sector', 'IsCurtains'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('IsCurtains', $services_options, $Settings->IsCurtains, 'class="form-control tip" id="IsCurtains"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Register_options', 'IsOneRegisterByBiller'); ?>
                            <?php $registerByBiller_options = [0 => lang('Each_biller_has_a_register'), 1 => lang('One_register_for_the_branch')]; ?>
                            <?= form_dropdown('IsOneRegisterByBiller', $registerByBiller_options, $Settings->IsOneRegisterByBiller, 'class="form-control tip" id="IsOneRegisterByBiller"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Activate_subscriptions', 'isSubscriptions'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('isSubscriptions', $services_options, $Settings->isSubscriptions, 'class="form-control tip" id="isSubscriptions"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 2: المنتجات -->
<div class="modal fade spark-settings-modal" id="settingsProductsModal" tabindex="-1" role="dialog" aria-labelledby="settingsProductsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsProductsModalLabel"><i class="fa fa-barcode"></i> <?= lang('products') ?: 'المنتجات'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_product_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('product_tax', 'tax_rate'); ?>
                            <?= form_dropdown('tax_rate', $ps, $Settings->default_tax_rate, 'class="form-control tip" id="tax_rate" required="required" style="width:100%;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Default_tax_method', 'default_tax_method'); ?>
                            <?php
                            $tm = ['1' => lang('exclusive'), '0' => lang('inclusive')];
                            echo form_dropdown('default_tax_method', $tm, $Settings->default_tax_method, 'class="form-control tip" id="default_tax_method" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="racks"><?= lang('racks'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('racks', $ps, $Settings->racks, 'id="racks" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="attributes"><?= lang('attributes'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('attributes', $ps, $Settings->attributes, 'id="attributes" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="product_expiry"><?= lang('product_expiry'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('product_expiry', $ps, $Settings->product_expiry, 'id="product_expiry" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="remove_expired"><?= lang('remove_expired'); ?></label>
                            <div class="controls">
                                <?php
                                $re_opts = [0 => lang('no') . ', ' . lang('i_ll_remove'), 1 => lang('yes') . ', ' . lang('remove_automatically')];
                                echo form_dropdown('remove_expired', $re_opts, $Settings->remove_expired, 'id="remove_expired" class="form-control tip" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="iwidth"><?= lang('image_size'); ?> (Width : Height) *</label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <?= form_input('iwidth', $Settings->iwidth, 'class="form-control tip" id="iwidth" placeholder="image width" required="required"'); ?>
                                </div>
                                <div class="col-xs-6">
                                    <?= form_input('iheight', $Settings->iheight, 'class="form-control tip" id="iheight" placeholder="image height" required="required"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="twidth"><?= lang('thumbnail_size'); ?> (Width : Height) *</label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <?= form_input('twidth', $Settings->twidth, 'class="form-control tip" id="twidth" placeholder="thumbnail width" required="required"'); ?>
                                </div>
                                <div class="col-xs-6">
                                    <?= form_input('theight', $Settings->theight, 'class="form-control tip" id="theight" placeholder="thumbnail height" required="required"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('watermark', 'watermark'); ?>
                            <div class="controls">
                                <?= form_dropdown('watermark', $wm, $Settings->watermark, 'class="tip form-control" required="required" id="watermark" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('display_all_products', 'display_all_products'); ?>
                            <div class="controls">
                                <?php
                                $dopts = [0 => lang('hide'), 1 => lang('show')];
                                echo form_dropdown('display_all_products', $dopts, $Settings->display_all_products, 'class="tip form-control" required="required" id="display_all_products" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="item_addition"><?= lang('item_addition'); ?></label>
                            <div class="controls">
                                <?php
                                $ia = [0 => lang('add_new_item'), 1 => lang('increase_quantity_if_item_exist')];
                                echo form_dropdown('item_addition', $ia, $Settings->item_addition, 'id="item_addition" class="form-control tip" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="product_serial"><?= lang('product_serial'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('product_serial', $ps, $Settings->product_serial, 'id="product_serial" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="product_discount"><?= lang('product_level_discount'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('product_discount', $ps, $Settings->product_discount, 'id="product_discount" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="bc_fix"><?= lang('bc_fix'); ?></label>
                            <?= form_input('bc_fix', $Settings->bc_fix, 'class="form-control tip" required="required" id="bc_fix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="detect_barcode"><?= lang('auto_detect_barcode'); ?></label>
                            <div class="controls">
                                <?= form_dropdown('detect_barcode', $ps, $Settings->auto_detect_barcode, 'id="detect_barcode" class="form-control tip" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="barcode_separator"><?= lang('barcode_separator'); ?></label>
                            <div class="controls">
                                <?php
                                $bcopts = ['-' => lang('dash'), '.' => lang('dot'), '~' => lang('tilde'), '_' => lang('underscore')];
                                echo form_dropdown('barcode_separator', $bcopts, $Settings->barcode_separator, 'class="form-control tip" required="required" id="barcode_separator" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="barcode_renderer"><?= lang('barcode_renderer'); ?></label>
                            <div class="controls">
                                <?php
                                $bcimg = ['svg' => lang('svg'), 'image' => lang('image')];
                                echo form_dropdown('barcode_renderer', $bcimg, $Settings->barcode_img, 'class="form-control tip" required="required" id="barcode_renderer" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('show_product_code', 'show_product_code'); ?>
                            <?php $services_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('show_product_code', $services_options, $Settings->show_product_code, 'class="form-control tip" id="show_product_code"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 3: مبيعات -->
<div class="modal fade spark-settings-modal" id="settingsSalesModal" tabindex="-1" role="dialog" aria-labelledby="settingsSalesModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsSalesModalLabel"><i class="fa fa-shopping-cart"></i> <?= lang('sales') ?: 'مبيعات'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open_multipart('system_settings/save_sales_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="overselling"><?= lang('over_selling'); ?></label>
                            <div class="controls">
                                <?php
                                $opt = [1 => lang('yes'), 0 => lang('no')];
                                echo form_dropdown('restrict_sale', $opt, $Settings->overselling, 'class="form-control tip" id="overselling" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="reference_format"><?= lang('reference_format'); ?></label>
                            <div class="controls">
                                <?php
                                $ref = [1 => lang('prefix_year_no'), 2 => lang('prefix_month_year_no'), 3 => lang('sequence_number'), 4 => lang('random_number')];
                                echo form_dropdown('reference_format', $ref, $Settings->reference_format, 'class="form-control tip" required="required" id="reference_format" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="display:none">
                        <div class="form-group">
                            <?= lang('invoice_tax', 'tax_rate2'); ?>
                            <?php
                            $tr['0'] = lang('disable');
                            foreach ($tax_rates as $rate) {
                                $tr[$rate->id] = $rate->name;
                            }
                            echo form_dropdown('tax_rate2', $tr, $Settings->default_tax_rate2, 'id="tax_rate2" class="form-control tip" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('set_focus', 'set_focus'); ?>
                            <?php
                            $sfopts = [0 => lang('add_item_input'), 1 => lang('last_order_item')];
                            echo form_dropdown('set_focus', $sfopts, (isset($_POST['set_focus']) ? $_POST['set_focus'] : $Settings->set_focus), 'id="set_focus" data-placeholder="' . lang('select') . ' ' . lang('set_focus') . '" required="required" class="form-control input-tip select" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="invoices_sequence"><?= lang('Getting_invoices_number_sequence'); ?></label>
                            <div class="controls">
                                <?php
                                $opt_inv = [0 => lang('From_Online'), 1 => lang('From_Offline')];
                                echo form_dropdown('invoices_sequence', $opt_inv, $Settings->invoices_sequence, 'class="form-control tip" required="required" id="invoices_sequence" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="invoice_view"><?= lang('invoice_view'); ?></label>
                            <div class="controls">
                                <?php
                                $db_name = str_replace("cpesoft_", "", $this->db->database);
                                if ($db_name == 'awtan' || $db_name == 'spark') {
                                    $opt_inv_view = [0 => lang('Template_1'), 2 => lang('Template_2'), 3 => lang('Template_3'), 4 => lang('Template_4'), 7 => lang('Template_5'), 9 => lang('Template_6'), 16 => lang('Template_7'), 22 => lang('Template_8'), 21 => lang('Payment_certificate_template'), 8 => lang('Stamped_invoice_template'), 18 => lang('Stamped_invoice_template') . ' 2', 20 => lang('Stamped_invoice_template') . ' 3', 5 => lang('Logistic_Template'), 13 => lang('Logistic_Template') . ' Ar-En', 10 => lang('Delivery_invoice_template'), 11 => lang('Cash_or_credit_invoice_template'), 12 => lang('Template_without_header'), 19 => lang('Template_without_header') . ' 2', 6 => lang('Pending_Template'), 14 => lang('Watermarked_invoice'), 15 => lang('Watermarked_invoice') . ' 2', 17 => lang('Watermarked_invoice') . ' 3', 1 => lang('tax_invoice') . ' ' . lang('In_Offline')];
                                } else {
                                    $opt_inv_view = [0 => lang('Template_1'), 2 => lang('Template_2'), 3 => lang('Template_3'), 4 => lang('Template_4'), 7 => lang('Template_5'), 9 => lang('Template_6'), 16 => lang('Template_7'), 22 => lang('Template_8'), 8 => lang('Stamped_invoice_template'), 18 => lang('Stamped_invoice_template') . ' 2', 20 => lang('Stamped_invoice_template') . ' 3', 5 => lang('Logistic_Template'), 13 => lang('Logistic_Template') . ' Ar-En', 10 => lang('Delivery_invoice_template'), 11 => lang('Cash_or_credit_invoice_template'), 12 => lang('Template_without_header'), 19 => lang('Template_without_header') . ' 2', 6 => lang('Pending_Template'), 14 => lang('Watermarked_invoice'), 15 => lang('Watermarked_invoice') . ' 2', 17 => lang('Watermarked_invoice') . ' 3', 1 => lang('tax_invoice') . ' ' . lang('In_Offline')];
                                }
                                echo form_dropdown('invoice_view', $opt_inv_view, $Settings->invoice_view, 'class="form-control tip" required="required" id="invoice_view" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <!-- External image preview box -->
                        <div class="image-preview" id="previewBox">
                            <span>No preview selected</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="invoiceNoRef"><?= lang('Sales_presentation'); ?></label>
                            <div class="controls">
                                <?php
                                $opt_ref = [0 => lang('reference_no'), 1 => lang('Invoice_No')];
                                echo form_dropdown('invoiceNoRef', $opt_ref, $Settings->invoiceNoRef, 'class="form-control tip" required="required" id="invoiceNoRef" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="isOpen_invoice"><?= lang('Open_invoice'); ?></label>
                            <div class="controls">
                                <?php $opt_open = [0 => lang('no'), 1 => lang('yes')]; ?>
                                <?= form_dropdown('isOpen_invoice', $opt_open, $Settings->isOpen_invoice, 'class="form-control tip" required="required" id="isOpen_invoice" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="IsInsuranceBusiness"><?= lang('Insurance_business'); ?></label>
                            <div class="controls">
                                <?php $opt_ins = [0 => lang('no'), 1 => lang('yes')]; ?>
                                <?= form_dropdown('IsInsuranceBusiness', $opt_ins, $Settings->IsInsuranceBusiness, 'class="form-control tip" required="required" id="IsInsuranceBusiness" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 insuranceType">
                        <div class="form-group">
                            <label class="control-label" for="insuranceType"><?= lang('Business_insurance_of_the_amount'); ?></label>
                            <div class="controls">
                                <?php $opt_ins_type = [1 => lang('Before_tax'), 2 => lang('After_tax')]; ?>
                                <?= form_dropdown('insuranceType', $opt_ins_type, $Settings->insuranceType, 'class="form-control tip" required="required" id="insuranceType" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="states" style="display: none;">
                        <div class="form-group">
                            <label class="control-label" for="state"><?= lang('biz_state'); ?></label>
                            <div class="controls">
                                <?php
                                $states = $this->gst->getIndianStates();
                                echo form_dropdown('state', $states, $Settings->state, 'class="form-control tip" required="required" id="state" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="isDeliveryFromSales"><?= lang('Delivery_from_sales'); ?></label>
                            <div class="controls">
                                <?php $opt_del = [0 => lang('no'), 1 => lang('yes')]; ?>
                                <?= form_dropdown('isDeliveryFromSales', $opt_del, $Settings->isDeliveryFromSales, 'class="form-control tip" required="required" id="isDeliveryFromSales" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($Settings->preAccounting) && $Settings->preAccounting == 1) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Display_the_customers_account_number_on_the_invoice', 'isDisplayCustomersAccountOnInvoice'); ?>
                                <?php $cust_acc_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                                <?= form_dropdown('isDisplayCustomersAccountOnInvoice', $cust_acc_options, $Settings->isDisplayCustomersAccountOnInvoice, 'class="form-control tip" id="isDisplayCustomersAccountOnInvoice"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Display_the_warehouse_name_on_the_invoice', 'isDisplayWarehouseOnInvoice'); ?>
                                <?php $wh_options = [0 => lang('no'), 1 => lang('yes')]; ?>
                                <?= form_dropdown('isDisplayWarehouseOnInvoice', $wh_options, $Settings->isDisplayWarehouseOnInvoice, 'class="form-control tip" id="isDisplayWarehouseOnInvoice"'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Display_the_number_of_items_sold_on_the_invoice', 'isDisplayItemsSold'); ?>
                            <?php $items_sold_options = [1 => lang('According_to_the_number_of_items'), 2 => lang('According_to_the_total_quantity'), 0 => lang('no')]; ?>
                            <?= form_dropdown('isDisplayItemsSold', $items_sold_options, $Settings->isDisplayItemsSold, 'class="form-control tip" id="isDisplayItemsSold"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Terms_and_conditions_renderer', 'terms_renderer'); ?>
                            <?php
                            $tropts = [0 => lang('Text'), 1 => lang('QR_Code')];
                            echo form_dropdown('terms_renderer', $tropts, (isset($_POST['terms_renderer']) ? $_POST['terms_renderer'] : $Settings->terms_renderer), 'class="tip form-control" required="required" id="terms_renderer" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4 terms">
                        <div class="form-group">
                            <?= lang('Terms_and_conditions_document', 'terms_attachment'); ?>
                            <input id="terms_attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="terms_attachment" data-show-upload="false" data-show-preview="false" class="form-control file">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 4: بادئة -->
<div class="modal fade spark-settings-modal" id="settingsPrefixesModal" tabindex="-1" role="dialog" aria-labelledby="settingsPrefixesModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsPrefixesModalLabel"><i class="fa fa-tag"></i> <?= lang('prefix') ?: 'بادئة'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_prefix_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="sales_prefix"><?= lang('sales_prefix'); ?></label>
                            <?= form_input('sales_prefix', $Settings->sales_prefix, 'class="form-control tip" id="sales_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="quote_prefix"><?= lang('quote_prefix'); ?></label>
                            <?= form_input('quote_prefix', $Settings->quote_prefix, 'class="form-control tip" id="quote_prefix"'); ?>
                        </div>
                    </div>
                    <?php if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="offer_prefix"><?= lang('Offer_prefix'); ?></label>
                                <?= form_input('offer_prefix', $Settings->offer_prefix, 'class="form-control tip" id="offer_prefix"'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="purchase_prefix"><?= lang('purchase_prefix'); ?></label>
                            <?= form_input('purchase_prefix', $Settings->purchase_prefix, 'class="form-control tip" id="purchase_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="transfer_prefix"><?= lang('transfer_prefix'); ?></label>
                            <?= form_input('transfer_prefix', $Settings->transfer_prefix, 'class="form-control tip" id="transfer_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="delivery_prefix"><?= lang('delivery_prefix'); ?></label>
                            <?= form_input('delivery_prefix', $Settings->delivery_prefix, 'class="form-control tip" id="delivery_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="payment_prefix"><?= lang('payment_prefix'); ?></label>
                            <?= form_input('payment_prefix', $Settings->payment_prefix, 'class="form-control tip" id="payment_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="ppayment_prefix"><?= lang('ppayment_prefix'); ?></label>
                            <?= form_input('ppayment_prefix', $Settings->ppayment_prefix, 'class="form-control tip" id="ppayment_prefix"'); ?>
                        </div>
                    </div>
                    <?php if (isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset == 1) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="aipayment_prefix"><?= lang('aipayment_prefix'); ?></label>
                                <?= form_input('aipayment_prefix', $Settings->aipayment_prefix, 'class="form-control tip" id="aipayment_prefix"'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="qa_prefix"><?= lang('qa_prefix'); ?></label>
                            <?= form_input('qa_prefix', $Settings->qa_prefix, 'class="form-control tip" id="qa_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="return_prefix"><?= lang('return_prefix'); ?></label>
                            <?= form_input('return_prefix', $Settings->return_prefix, 'class="form-control tip" id="return_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="returnp_prefix"><?= lang('returnp_prefix'); ?></label>
                            <?= form_input('returnp_prefix', $Settings->returnp_prefix, 'class="form-control tip" id="returnp_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="expense_prefix"><?= lang('expense_prefix'); ?></label>
                            <?= form_input('expense_prefix', $Settings->expense_prefix, 'class="form-control tip" id="expense_prefix"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="revaluation_prefix"><?= lang('Revaluation_prefix'); ?></label>
                            <?= form_input('revaluation_prefix', $Settings->revaluation_prefix, 'class="form-control tip" id="revaluation_prefix"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 5: تنسيق المال وعدد -->
<div class="modal fade spark-settings-modal" id="settingsMoneyModal" tabindex="-1" role="dialog" aria-labelledby="settingsMoneyModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsMoneyModalLabel"><i class="fa fa-money"></i> <?= lang('money_number_format') ?: 'تنسيق المال وعدد'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_money_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="decimals"><?= lang('decimals'); ?></label>
                            <div class="controls">
                                <?php
                                $decimals = [0 => lang('disable'), 1 => '1', 2 => '2', 3 => '3', 4 => '4'];
                                echo form_dropdown('decimals', $decimals, $Settings->decimals, 'class="form-control tip" id="decimals" style="width:100%;" required="required"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="qty_decimals"><?= lang('qty_decimals'); ?></label>
                            <div class="controls">
                                <?php
                                $qty_decimals = [0 => lang('disable'), 1 => '1', 2 => '2', 3 => '3', 4 => '4'];
                                echo form_dropdown('qty_decimals', $qty_decimals, $Settings->qty_decimals, 'class="form-control tip" id="qty_decimals" style="width:100%;" required="required"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="display: none;">
                        <div class="form-group">
                            <?= lang('sac', 'sac'); ?>
                            <?php $sacps = ['0' => lang('disable')]; ?>
                            <?= form_dropdown('sac', $sacps, 0, 'class="form-control tip" id="sac" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="decimals_sep"><?= lang('decimals_sep'); ?></label>
                            <div class="controls">
                                <?php
                                $dec_point = ['.' => lang('dot'), ',' => lang('comma')];
                                echo form_dropdown('decimals_sep', $dec_point, $Settings->decimals_sep, 'class="form-control tip" id="decimals_sep" style="width:100%;" required="required"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="thousands_sep"><?= lang('thousands_sep'); ?></label>
                            <div class="controls">
                                <?php
                                $thousands_sep = ['.' => lang('dot'), ',' => lang('comma'), '0' => lang('space')];
                                echo form_dropdown('thousands_sep', $thousands_sep, $Settings->thousands_sep, 'class="form-control tip" id="thousands_sep" style="width:100%;" required="required"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('display_currency_symbol', 'display_symbol'); ?>
                            <?php $opts = [0 => lang('disable'), 1 => lang('before'), 2 => lang('after')]; ?>
                            <?= form_dropdown('display_symbol', $opts, $Settings->display_symbol, 'class="form-control" id="display_symbol" style="width:100%;" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('currency_symbol', 'symbol'); ?>
                            <?= form_input('symbol', $Settings->symbol, 'class="form-control" id="symbol" style="width:100%;"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 6: تفاصيل البنك -->
<div class="modal fade spark-settings-modal" id="settingsBankModal" tabindex="-1" role="dialog" aria-labelledby="settingsBankModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsBankModalLabel"><i class="fa fa-university"></i> <?= lang('bank_details') ?: 'تفاصيل البنك'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_bank_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Account_name', 'account_name'); ?>
                            <?= form_input('account_name', $Settings->account_name, 'class="form-control tip" id="account_name"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('BANK_NAME', 'bank_name'); ?>
                            <?= form_input('bank_name', $Settings->bank_name, 'class="form-control tip" id="bank_name"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('IBAN', 'IBAN'); ?>
                            <?= form_input('IBAN', $Settings->IBAN, 'class="form-control tip" id="IBAN"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Branch_name', 'branch_name'); ?>
                            <?= form_input('branch_name', $Settings->branch_name, 'class="form-control tip" id="branch_name"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Swift_Code', 'swift_code'); ?>
                            <?= form_input('swift_code', $Settings->swift_code, 'class="form-control tip" id="swift_code"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 7: البريد الإلكتروني -->
<div class="modal fade spark-settings-modal" id="settingsEmailModal" tabindex="-1" role="dialog" aria-labelledby="settingsEmailModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsEmailModalLabel"><i class="fa fa-envelope-o"></i> <?= lang('email') ?: 'البريد الإلكتروني'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_email_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="protocol"><?= lang('email_protocol'); ?></label>
                            <div class="controls">
                                <?php
                                $popt = ['mail' => 'PHP Mail Function', 'sendmail' => 'Send Mail', 'smtp' => 'SMTP'];
                                echo form_dropdown('protocol', $popt, $Settings->protocol, 'class="form-control tip skip" id="protocol" style="width:100%;" required="required"');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="sendmail_config" style="display: none;">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="mailpath"><?= lang('mailpath'); ?></label>
                                <?= form_input('mailpath', $Settings->mailpath, 'class="form-control tip" id="mailpath"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="smtp_config" style="display: none;">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="smtp_host"><?= lang('smtp_host'); ?></label>
                                <?= form_input('smtp_host', $Settings->smtp_host, 'class="form-control tip" id="smtp_host"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="smtp_user"><?= lang('smtp_user'); ?></label>
                                <?= form_input('smtp_user', $Settings->smtp_user, 'class="form-control tip" id="smtp_user"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="smtp_pass"><?= lang('smtp_pass'); ?></label>
                                <?= form_password('smtp_pass', $Settings->smtp_pass, 'class="form-control tip" id="smtp_pass"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="smtp_port"><?= lang('smtp_port'); ?></label>
                                <?= form_input('smtp_port', $Settings->smtp_port, 'class="form-control tip" id="smtp_port"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="smtp_crypto"><?= lang('smtp_crypto'); ?></label>
                                <div class="controls">
                                    <?php
                                    $crypto_opt = ['' => lang('none'), 'tls' => 'TLS', 'ssl' => 'SSL'];
                                    echo form_dropdown('smtp_crypto', $crypto_opt, $Settings->smtp_crypto, 'class="form-control tip" id="smtp_crypto"');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 8: النقاط المستحقة -->
<div class="modal fade spark-settings-modal" id="settingsPointsModal" tabindex="-1" role="dialog" aria-labelledby="settingsPointsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsPointsModalLabel"><i class="fa fa-star"></i> <?= lang('award_points') ?: 'النقاط المستحقة'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_points_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?= lang('customer_award_points'); ?></label>
                            <div class="row">
                                <div class="col-sm-4 col-xs-6">
                                    <?= lang('each_spent'); ?><br>
                                    <?= form_input('each_spent', $this->sma->formatDecimal($Settings->each_spent), 'class="form-control"'); ?>
                                </div>
                                <div class="col-sm-1 col-xs-1 text-center" style="padding-top: 25px;"><i class="fa fa-arrow-right"></i></div>
                                <div class="col-sm-4 col-xs-5">
                                    <?= lang('award_points'); ?><br>
                                    <?= form_input('ca_point', $Settings->ca_point, 'class="form-control"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?= lang('staff_award_points'); ?></label>
                            <div class="row">
                                <div class="col-sm-4 col-xs-6">
                                    <?= lang('each_in_sale'); ?><br>
                                    <?= form_input('each_sale', $this->sma->formatDecimal($Settings->each_sale), 'class="form-control"'); ?>
                                </div>
                                <div class="col-sm-1 col-xs-1 text-center" style="padding-top: 25px;"><i class="fa fa-arrow-right"></i></div>
                                <div class="col-sm-4 col-xs-5">
                                    <?= lang('award_points'); ?><br>
                                    <?= form_input('sa_point', $Settings->sa_point, 'class="form-control"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal 9: عكس النقاط المستحقة -->
<div class="modal fade spark-settings-modal" id="settingsReversePointsModal" tabindex="-1" role="dialog" aria-labelledby="settingsReversePointsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsReversePointsModalLabel"><i class="fa fa-undo"></i> <?= lang('Reverse_award_points') ?: 'عكس النقاط المستحقة'; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?= admin_form_open('system_settings/save_reverse_points_settings', ['data-toggle' => 'validator', 'role' => 'form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?= lang('Customer_award_amounts'); ?></label>
                            <div class="row">
                                <div class="col-sm-4 col-xs-6">
                                    <?= lang('Reverse_points'); ?><br>
                                    <?= form_input('reverse_ca_point', $Settings->reverse_ca_point, 'class="form-control"'); ?>
                                </div>
                                <div class="col-sm-1 col-xs-1 text-center" style="padding-top: 25px;"><i class="fa fa-arrow-left"></i></div>
                                <div class="col-sm-4 col-xs-5">
                                    <?= lang('Award_amounts'); ?><br>
                                    <?= form_input('reverse_each_spent', $this->sma->formatDecimal($Settings->reverse_each_spent), 'class="form-control"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?= lang('Staff_award_amounts'); ?></label>
                            <div class="row">
                                <div class="col-sm-4 col-xs-6">
                                    <?= lang('Reverse_points'); ?><br>
                                    <?= form_input('reverse_sa_point', $Settings->reverse_sa_point, 'class="form-control"'); ?>
                                </div>
                                <div class="col-sm-1 col-xs-1 text-center" style="padding-top: 25px;"><i class="fa fa-arrow-left"></i></div>
                                <div class="col-sm-4 col-xs-5">
                                    <?= lang('Award_amounts'); ?><br>
                                    <?= form_input('reverse_each_sale', $this->sma->formatDecimal($Settings->reverse_each_sale), 'class="form-control"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update_settings'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        <?php if (isset($message)) {
            echo 'localStorage.clear();';
        } ?>

        var timezones = <?= json_encode(DateTimeZone::listIdentifiers(DateTimeZone::ALL)); ?>;
        $('#timezone').autocomplete({
            source: timezones
        });

        // Email protocol toggles
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

        // Overselling and accounting alerts
        $('#overselling').change(function () {
            if ($(this).val() == 1) {
                if ($('#accounting_method').select2 && $('#accounting_method').select2("val") != 2) {
                    bootbox.alert('<?=lang('overselling_will_only_work_with_AVCO_accounting_method_only')?>');
                    $('#accounting_method').select2("val", '2');
                }
            }
        });
        $('#accounting_method').change(function () {
            var oam = <?=$Settings->accounting_method?>, nam = $(this).val();
            if (oam != nam) {
                bootbox.alert('<?=lang('accounting_method_change_alert')?>');
            }
        });
        $('#accounting_method').change(function () {
            if ($(this).val() != 2) {
                if ($('#overselling').select2 && $('#overselling').select2("val") == 1) {
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

        // PreAccounting type readonly check
        var accounting_type = '<?= $Settings->accounting_type; ?>';
        if (accounting_type) {
            $('#accounting_type').prop('readonly', true);
        }

        // Invoice view preview box
        $('#invoice_view').change(function(e) {
            var previewBox = document.getElementById("previewBox");
            if (previewBox) {
                var imgSrc = "<?= base_url() . 'assets/image_preview/' ?>";
                previewBox.innerHTML = '<img src="' + imgSrc + $(this).val() + '.png">';
            }
        });
        var initialPreviewBox = document.getElementById("previewBox");
        if (initialPreviewBox) {
            var initialImgSrc = "<?= base_url() . 'assets/image_preview/' ?>";
            initialPreviewBox.innerHTML = '<img src="' + initialImgSrc + $('#invoice_view').val() + '.png">';
        }

        // Tobacco toggle
        $('#isTobacco').change(function(e) {
            if ($(this).val() == 1) {
                $('.tobacco').show();
            } else {
                $('.tobacco').hide();
            }
        });
        if ($('#isTobacco').val() == 1) {
            $('.tobacco').show();
        } else {
            $('.tobacco').hide();
        }

        // Terms renderer toggle
        $('#terms_renderer').change(function(e) {
            if ($(this).val() == 1) {
                $('.terms').show();
            } else {
                $('.terms').hide();
            }
        });
        if ($('#terms_renderer').val() == 1) {
            $('.terms').show();
        } else {
            $('.terms').hide();
        }

        // Insurance toggle
        $('#IsInsuranceBusiness').change(function(e) {
            if ($(this).val() == 1) {
                $('.insuranceType').show();
            } else {
                $('.insuranceType').hide();
            }
        });
        if ($('#IsInsuranceBusiness').val() == 1) {
            $('.insuranceType').show();
        } else {
            $('.insuranceType').hide();
        }

        // Restaurant notifications toggle
        $('#IsResturant').change(function(e) {
            if ($(this).val() == 1) {
                $('.menunotifications').show();
            } else {
                $('.menunotifications').hide();
            }
        });
        if ($('#IsResturant').val() == 1) {
            $('.menunotifications').show();
        } else {
            $('.menunotifications').hide();
        }

        // State toggle for template 200
        $('#invoice_view').change(function(e) {
            if ($(this).val() == 200) {
                $('#states').show();
            } else {
                $('#states').hide();
            }
        });
        if ($('#invoice_view').val() == 200) {
            $('#states').show();
        } else {
            $('#states').hide();
        }

        // Reinitialize select2 inside modals upon shown
        $('.spark-settings-modal').on('shown.bs.modal', function () {
            $(this).find('select.select').each(function () {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
                $(this).select2({
                    dropdownParent: $(this).closest('.modal')
                });
            });
        });
    });
</script>
