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
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('Account_system_settings'); ?></h2>


    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('accounts/settings', $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('Accounts') ?></legend>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_main_boxes_acct"><?= lang('Main_Funds_Account'); ?></label>

                                    <div class="controls"> <?php
                                     $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_main_boxes_acct', $acc, $accounts_settings->accounts_main_boxes_acct, 'class="form-control tip" id="accounts_main_boxes_acct" data-placeholder="' . lang('select') . ' ' . lang('Main_Funds_Account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_main_banks_acct"><?= lang('Main_bank_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_main_banks_acct', $acc, $accounts_settings->accounts_main_banks_acct, 'class="form-control tip" id="accounts_main_banks_acct" data-placeholder="' . lang('select') . ' ' . lang('Main_bank_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4"  style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_loans_acct"><?= lang('Loans_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_loans_acct', $acc, $accounts_settings->accounts_loans_acct, 'class="form-control tip" id="accounts_loans_acct" data-placeholder="' . lang('select') . ' ' . lang('Loans_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4"  style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_customers_acct"><?= lang('Customers_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_customers_acct', $acc, $accounts_settings->accounts_customers_acct, 'class="form-control tip" id="accounts_customers_acct" data-placeholder="' . lang('select') . ' ' . lang('Customers_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_suppliers_acct"><?= lang('Suppliers_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_suppliers_acct', $acc, $accounts_settings->accounts_suppliers_acct, 'class="form-control tip" id="accounts_suppliers_acct" data-placeholder="' . lang('select') . ' ' . lang('Suppliers_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_local_suppliers_acct"><?= lang('Local_Suppliers_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_local_suppliers_acct', $acc, $accounts_settings->accounts_local_suppliers_acct, 'class="form-control tip" id="accounts_local_suppliers_acct" data-placeholder="' . lang('select') . ' ' . lang('Local_Suppliers_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_international_suppliers_acct"><?= lang('International_Suppliers_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_international_suppliers_acct', $acc, $accounts_settings->accounts_international_suppliers_acct, 'class="form-control tip" id="accounts_international_suppliers_acct" data-placeholder="' . lang('select') . ' ' . lang('International_Suppliers_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_bank_commisson_acct"><?= lang('Bank_commissons_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_bank_commisson_acct', $acc, $accounts_settings->accounts_bank_commisson_acct, 'class="form-control tip" id="accounts_bank_commisson_acct" data-placeholder="' . lang('select') . ' ' . lang('Bank_commissons_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_shipping_return_losses"><?= lang('Shipping_return_losses_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_shipping_return_losses', $acc, $accounts_settings->accounts_shipping_return_losses, 'class="form-control tip" id="accounts_shipping_return_losses" data-placeholder="' . lang('select') . ' ' . lang('Shipping_return_losses_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_local_customers_acct"><?= lang('Local_Customers_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_local_customers_acct', $acc, $accounts_settings->accounts_local_customers_acct, 'class="form-control tip" id="accounts_local_customers_acct" data-placeholder="' . lang('select') . ' ' . lang('Local_Customers_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_international_customers_acct"><?= lang('International_Customers_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_international_customers_acct', $acc, $accounts_settings->accounts_international_customers_acct, 'class="form-control tip" id="accounts_international_customers_acct" data-placeholder="' . lang('select') . ' ' . lang('International_Customers_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_shipping_other_revenues"><?= lang('Other_revenues_shipping_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_shipping_other_revenues', $acc, $accounts_settings->accounts_shipping_other_revenues, 'class="form-control tip" id="accounts_shipping_other_revenues" data-placeholder="' . lang('select') . ' ' . lang('Other_revenues_shipping_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4"  style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_bank_acct"><?= lang('Bank_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_bank_acct', $acc, $accounts_settings->accounts_bank_acct, 'class="form-control tip" id="accounts_bank_acct" data-placeholder="' . lang('select') . ' ' . lang('Bank_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>



							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_operating_expenses_acct"><?= lang('Operating_expenses_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_operating_expenses_acct', $acc, $accounts_settings->accounts_operating_expenses_acct, 'class="form-control tip" id="accounts_operating_expenses_acct" data-placeholder="' . lang('select') . ' ' . lang('Operating_expenses_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_marketing_expenses_acct"><?= lang('Marketing_expenses_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_marketing_expenses_acct', $acc, $accounts_settings->accounts_marketing_expenses_acct, 'class="form-control tip" id="accounts_marketing_expenses_acct" data-placeholder="' . lang('select') . ' ' . lang('Marketing_expenses_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_fixed_assets_acct"><?= lang('Fixed_Assets_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_fixed_assets_acct', $acc, $accounts_settings->accounts_fixed_assets_acct, 'class="form-control tip" id="accounts_fixed_assets_acct" data-placeholder="' . lang('select') . ' ' . lang('Fixed_Assets_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_current_assets_acct"><?= lang('Current_Assets_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_current_assets_acct', $acc, $accounts_settings->accounts_current_assets_acct, 'class="form-control tip" id="accounts_current_assets_acct" data-placeholder="' . lang('select') . ' ' . lang('Current_Assets_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<!--<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_provision_for_depreciation_of_fixed_assets_acct"><?= lang('Provision_for_depreciation_of_fixed_assets'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_provision_for_depreciation_of_fixed_assets_acct', $acc, $accounts_settings->accounts_provision_for_depreciation_of_fixed_assets_acct, 'class="form-control tip" id="accounts_provision_for_depreciation_of_fixed_assets_acct" data-placeholder="' . lang('select') . ' ' . lang('Provision_for_depreciation_of_fixed_assets') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_provision_for_doubtful_debts_acct"><?= lang('Provision_for_doubtful_debts_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_provision_for_doubtful_debts_acct', $acc, $accounts_settings->accounts_provision_for_doubtful_debts_acct, 'class="form-control tip" id="accounts_provision_for_doubtful_debts_acct" data-placeholder="' . lang('select') . ' ' . lang('Provision_for_doubtful_debts_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>-->

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_provisions_acct"><?= lang('The_Provisions_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_provisions_acct', $acc, $accounts_settings->accounts_provisions_acct, 'class="form-control tip" id="accounts_provisions_acct" data-placeholder="' . lang('select') . ' ' . lang('The_Provisions_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_profits_stage_acct"><?= lang('Profits_Stage'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_profits_stage_acct', $acc, $accounts_settings->accounts_profits_stage_acct, 'class="form-control tip" id="accounts_profits_stage_acct" data-placeholder="' . lang('select') . ' ' . lang('Profits_Stage') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_losses_stage_acct"><?= lang('Losses_Stage'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_losses_stage_acct', $acc, $accounts_settings->accounts_losses_stage_acct, 'class="form-control tip" id="accounts_losses_stage_acct" data-placeholder="' . lang('select') . ' ' . lang('Losses_Stage') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_currency_differences_profit_acct"><?= lang('Currency_differences_profit_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_currency_differences_profit_acct', $acc, $accounts_settings->accounts_currency_differences_profit_acct, 'class="form-control tip" id="accounts_currency_differences_profit_acct" data-placeholder="' . lang('select') . ' ' . lang('Currency_differences_profit_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_currency_spread_losses_acct"><?= lang('Currency_spread_losses_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_currency_spread_losses_acct', $acc, $accounts_settings->accounts_currency_spread_losses_acct, 'class="form-control tip" id="accounts_currency_spread_losses_acct" data-placeholder="' . lang('select') . ' ' . lang('Currency_spread_losses_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_gift_card_payments"><?= lang('Gift_card_payments'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_gift_card_payments', $acc, $accounts_settings->accounts_gift_card_payments, 'class="form-control tip" id="accounts_gift_card_payments" data-placeholder="' . lang('select') . ' ' . lang('Gift_card_payments') . '"    required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_discount_allowance"><?= lang('Discount_Allowance'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_discount_allowance', $acc, $accounts_settings->accounts_discount_allowance, 'class="form-control tip" id="accounts_discount_allowance" data-placeholder="' . lang('select') . ' ' . lang('Discount_Allowance') . '"    required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_discount_earned"><?= lang('Discount_Earned'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_discount_earned', $acc, $accounts_settings->accounts_discount_earned, 'class="form-control tip" id="accounts_discount_earned" data-placeholder="' . lang('select') . ' ' . lang('Discount_Earned') . '"    required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="expenses_shipping_acct"><?= lang('Accrued_Expenses'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                        //foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('expenses_shipping_acct', $acc, $accounts_settings->expenses_shipping_acct, 'class="form-control tip" id="expenses_shipping_acct" data-placeholder="' . lang('select') . ' ' . lang('Accrued_Expenses') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4"  >
                                <div class="form-group">
                                    <label class="control-label" for="accounts_ready_funds_acct"><?= lang('Owners_equity_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_ready_funds_acct', $acc, $accounts_settings->accounts_ready_funds_acct, 'class="form-control tip" id="accounts_ready_funds_acct" data-placeholder="' . lang('select') . ' ' . lang('Owners_equity_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?php
						if($Settings->isDonation)
                        {
							?>
                        <div class="col-md-4" >
                                <div class="form-group">
                                    <label class="control-label" for="donations_acct"><?= lang('Donations_due_to_charities_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('donations_acct', $acc, $accounts_settings->donations_acct, 'class="form-control tip" id="donations_acct" data-placeholder="' . lang('select') . ' ' . lang('Donations_due_to_charities_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


                        <?php
						}
						?>






                    </fieldset>

                    <fieldset class="scheduler-border" >
                        <legend class="scheduler-border"><?= lang('sales') ?></legend>
                        <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="sales_acct"><?= lang('Sales_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('sales_acct', $acc, $accounts_settings->sales_acct, 'class="form-control tip" id="sales_acct" data-placeholder="' . lang('select') . ' ' . lang('Sales_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="sales_boxes_acct"><?= lang('Boxes_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('sales_boxes_acct', $acc, $accounts_settings->sales_boxes_acct, 'class="form-control tip" id="sales_boxes_acct" data-placeholder="' . lang('select') . ' ' . lang('Boxes_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="sales_return_acct"><?= lang('Sales_return_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('sales_return_acct', $acc, $accounts_settings->sales_return_acct, 'class="form-control tip" id="sales_return_acct" data-placeholder="' . lang('select') . ' ' . lang('Sales_return_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="sales_vat_acct"><?= lang('Sales_Tax_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('sales_vat_acct', $acc, $accounts_settings->sales_vat_acct, 'class="form-control tip" id="sales_vat_acct" data-placeholder="' . lang('select') . ' ' . lang('Sales_Tax_account') . '"   required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="sales_store_goods_receiving_acct"><?= lang('Store_Goods_Receiving_Account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('sales_store_goods_receiving_acct', $acc, $accounts_settings->sales_store_goods_receiving_acct, 'class="form-control tip" id="sales_store_goods_receiving_acct" data-placeholder="' . lang('select') . ' ' . lang('Store_Goods_Receiving_Account') . '"   required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="sales_cost_of_good_sold_acct"><?= lang('Cost_of_good_sold_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('sales_cost_of_good_sold_acct', $acc, $accounts_settings->sales_cost_of_good_sold_acct, 'class="form-control tip" id="sales_cost_of_good_sold_acct" data-placeholder="' . lang('select') . ' ' . lang('Cost_of_good_sold_account') . '"   required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="accounts_gift_card_sales"><?= lang('Gift_card_sales'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('accounts_gift_card_sales', $acc, $accounts_settings->accounts_gift_card_sales, 'class="form-control tip" id="accounts_gift_card_sales" data-placeholder="' . lang('select') . ' ' . lang('Gift_card_sales') . '"    required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


							<div class="col-md-4" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="sales_extra_charge_acct"><?= lang('Extra_charge'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('sales_extra_charge_acct', $acc, $accounts_settings->sales_extra_charge_acct, 'class="form-control tip" id="sales_extra_charge_acct" data-placeholder="' . lang('select') . ' ' . lang('Extra_charge') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?php
						if($Settings->IsInsuranceBusiness)
                        {
							?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="business_guarantee_insurance_acct"><?= lang('Business_guarantee_insurance_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('business_guarantee_insurance_acct', $acc, $accounts_settings->business_guarantee_insurance_acct, 'class="form-control tip" id="business_guarantee_insurance_acct" data-placeholder="' . lang('select') . ' ' . lang('Business_guarantee_insurance_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="advance_payments_acct"><?= lang('Advance_payments_from_customers_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('advance_payments_acct', $acc, $accounts_settings->advance_payments_acct, 'class="form-control tip" id="advance_payments_acct" data-placeholder="' . lang('select') . ' ' . lang('Advance_payments_from_customers_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?php
						    }
							?>






                    </fieldset>

                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('purchases') ?></legend>
						<?php
						if($Settings->accounting_type=='periodic'){
							?>
                        <div class="col-md-4" >
                                <div class="form-group">
                                    <label class="control-label" for="purchases_acct"><?= lang('Purchases_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('purchases_acct', $acc, $accounts_settings->purchases_acct, 'class="form-control tip" id="purchases_acct" data-placeholder="' . lang('select') . ' ' . lang('Purchases_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4" >
                                <div class="form-group">
                                    <label class="control-label" for="purchases_return_acct"><?= lang('Purchases_return_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('purchases_return_acct', $acc, $accounts_settings->purchases_return_acct, 'class="form-control tip" id="purchases_return_acct" data-placeholder="' . lang('select') . ' ' . lang('Purchases_return_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php
						}
						?>
							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="purchases_vat_acct"><?= lang('Purchases_Tax_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('purchases_vat_acct', $acc, $accounts_settings->purchases_vat_acct, 'class="form-control tip" id="purchases_vat_acct" data-placeholder="' . lang('select') . ' ' . lang('Purchases_Tax_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="purchases_store_goods_by_road_acct"><?= lang('Store_Goods_By_Road_Account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('purchases_store_goods_by_road_acct', $acc, $accounts_settings->purchases_store_goods_by_road_acct, 'class="form-control tip" id="purchases_store_goods_by_road_acct" data-placeholder="' . lang('select') . ' ' . lang('Store_Goods_By_Road_Account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="purchases_foreign_orders_acct"><?= lang('Foreign_orders'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('purchases_foreign_orders_acct', $acc, $accounts_settings->purchases_foreign_orders_acct, 'class="form-control tip" id="purchases_foreign_orders_acct" data-placeholder="' . lang('select') . ' ' . lang('Foreign_orders') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


                    </fieldset>



					<fieldset class="scheduler-border"  style="display:none;">
                        <legend class="scheduler-border"><?= lang('expenses') ?></legend>



                    </fieldset>

                    <fieldset class="scheduler-border">
                    <?php
                            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                                ?>
                           <legend class="scheduler-border"><?= lang('Showrooms') ?></legend>


                                   <?php
                            }else{
                                ?>
                            <legend class="scheduler-border"><?= lang('warehouses') ?></legend>

                           <?php
                            }
                                ?>
                        <div class="col-md-4">
                                <div class="form-group">
                                <?php
                            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                                ?>
                           <label class="control-label" for="warehouses_acct"><?= lang('Showrooms_account'); ?></label>


                                   <?php
                            }else{
                                ?>
                            <label class="control-label" for="warehouses_acct"><?= lang('Warehouses_account'); ?></label>

                           <?php
                            }
                                ?>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_acct', $acc, $accounts_settings->warehouses_acct, 'class="form-control tip" id="warehouses_acct" data-placeholder="' . lang('select') . ' ' . lang('Warehouses_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>

						<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="warehouses_destruction"><?= lang('Warehouses_destruction_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_destruction', $acc, $accounts_settings->warehouses_destruction, 'class="form-control tip" id="warehouses_destruction" data-placeholder="' . lang('select') . ' ' . lang('Warehouses_destruction_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>

						<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="warehouses_adjustment_inability"><?= lang('Warehouses_adjustment_deficit_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_adjustment_inability', $acc, $accounts_settings->warehouses_adjustment_inability, 'class="form-control tip" id="warehouses_adjustment_inability" data-placeholder="' . lang('select') . ' ' . lang('Warehouses_adjustment_deficit_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>

						<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="warehouses_adjustment_surplus"><?= lang('Warehouses_adjustment_surplus_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_adjustment_surplus', $acc, $accounts_settings->warehouses_adjustment_surplus, 'class="form-control tip" id="warehouses_adjustment_surplus" data-placeholder="' . lang('select') . ' ' . lang('Warehouses_adjustment_surplus_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>

						<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="warehouses_revaluation_profite"><?= lang('Warehouses_revaluation_profite_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_revaluation_profite', $acc, $accounts_settings->warehouses_revaluation_profite, 'class="form-control tip" id="warehouses_revaluation_profite" data-placeholder="' . lang('select') . ' ' . lang('Warehouses_revaluation_profite_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>

						<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="warehouses_revaluation_losses"><?= lang('Warehouses_revaluation_losses_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_revaluation_losses', $acc, $accounts_settings->warehouses_revaluation_losses, 'class="form-control tip" id="warehouses_revaluation_losses" data-placeholder="' . lang('select') . ' ' . lang('Warehouses_revaluation_losses_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>

						<div class="col-md-4"  style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="warehouses_products_beginning_period_acct"><?= lang('Products_beginning_period_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_products_beginning_period_acct', $acc, $accounts_settings->warehouses_products_beginning_period_acct, 'class="form-control tip" id="warehouses_products_beginning_period_acct" data-placeholder="' . lang('select') . ' ' . lang('Products_beginning_period_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>


						<div class="col-md-4"  style="display:none;">
                                <div class="form-group">
                                    <label class="control-label" for="warehouses_products_ending_period_acct"><?= lang('Products_ending_period_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('warehouses_products_ending_period_acct', $acc, $accounts_settings->warehouses_products_ending_period_acct, 'class="form-control tip" id="warehouses_products_ending_period_acct" data-placeholder="' . lang('select') . ' ' . lang('Products_ending_period_account') . '"  style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>




                    </fieldset>


					<fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?= lang('General_Settings') ?></legend>
                        <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="cost_centers"><?= lang('Cost_centers'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc['0'] = lang('Optional');
                                     $acc['1'] = lang('Mandatory');

                                        echo form_dropdown('cost_centers', $acc, $accounts_settings->cost_centers, 'class="form-control tip" id="cost_centers" data-placeholder="' . lang('select') . ' ' . lang('Cost_centers') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
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

