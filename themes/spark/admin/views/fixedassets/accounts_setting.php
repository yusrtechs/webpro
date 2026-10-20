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
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('Accounts_settings'); ?></h2>


    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('fixedassets/accounts_setting', $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('Fixed_assets') ?></legend>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="depreciation_allowance_acct"><?= lang('Depreciation_allowance_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('depreciation_allowance_acct', $acc, $accounts_settings->depreciation_allowance_acct, 'class="form-control tip" id="depreciation_allowance_acct" data-placeholder="' . lang('select') . ' ' . lang('Depreciation_allowance_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="depreciation_expenses_acct"><?= lang('Depreciation_expenses_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('depreciation_expenses_acct', $acc, $accounts_settings->depreciation_expenses_acct, 'class="form-control tip" id="depreciation_expenses_acct" data-placeholder="' . lang('select') . ' ' . lang('Depreciation_expenses_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>



							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="profits_sale_fixed_assets_acct"><?= lang('Profits_sale_fixed_assets_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('profits_sale_fixed_assets_acct', $acc, $accounts_settings->profits_sale_fixed_assets_acct, 'class="form-control tip" id="profits_sale_fixed_assets_acct" data-placeholder="' . lang('select') . ' ' . lang('Profits_sale_fixed_assets_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="losses_on_fixed_assets_acct"><?= lang('Losses_on_fixed_assets_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($masterAccounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('losses_on_fixed_assets_acct', $acc, $accounts_settings->losses_on_fixed_assets_acct, 'class="form-control tip" id="losses_on_fixed_assets_acct" data-placeholder="' . lang('select') . ' ' . lang('Losses_on_fixed_assets_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


						<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="revaluation_profite_fixed_assets_acct"><?= lang('Revaluation_profite_fixed_assets_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('revaluation_profite_fixed_assets_acct', $acc, $accounts_settings->revaluation_profite_fixed_assets_acct, 'class="form-control tip" id="revaluation_profite_fixed_assets_acct" data-placeholder="' . lang('select') . ' ' . lang('Revaluation_profite_fixed_assets_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>

						<div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="revaluation_losses_fixed_assets_acct"><?= lang('Revaluation_losses_fixed_assets_account'); ?></label>

                                    <div class="controls"> <?php
									 $acc= [];
									 $acc[''] = '';
                                        foreach ($accounts as $account) {
                                            $acc[$account->id] = $account->accountName;
                                        }
                                        echo form_dropdown('revaluation_losses_fixed_assets_acct', $acc, $accounts_settings->revaluation_losses_fixed_assets_acct, 'class="form-control tip" id="revaluation_losses_fixed_assets_acct" data-placeholder="' . lang('select') . ' ' . lang('Revaluation_losses_fixed_assets_account') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                        </div>


                        <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Automatic_depreciation_of_fixed_assets', 'Automatic_depreciation_of_fixed_assets'); ?>
                            <?php $autodep_options =  [0 => lang('no'), 1 => lang('yes')]; ?>
                            <?= form_dropdown('isAutoDepreciation', $autodep_options, $accounts_settings->isAutoDepreciation, 'class="form-control tip" id="isAutoDepreciation" '); ?>
                        </div>
                    </div>
                    <div class="col-md-4 autodep">
                        <div class="form-group">
                            <?= lang('Depreciation_period', 'Depreciation_period'); ?>
                            <?php $depper_options =  ['monthly' => lang('Monthly'), 'annual' => lang('Annual')]; ?>
                            <?= form_dropdown('depreciation_period', $depper_options, $accounts_settings->depreciation_period, 'class="form-control tip" id="depreciation_period" '); ?>
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
<script>
    $(document).ready(function() {
        $('#isAutoDepreciation').change(function(e) {
            if ($(this).val() == 1) {
                $('.autodep').show();
            } else {
                $('.autodep').hide();
            }
        });
        if ($('#isAutoDepreciation').val() == 1) {
                $('.autodep').show();
            } else {
                $('.autodep').hide();
        }
    });
</script>
