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
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('Branches_settings'); ?></h2>


    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('branches/branches_settings', $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('System_controls') ?></legend>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="auto_share_customers"><?= lang('Automatically_share_customers'); ?></label>

                                    <div class="controls">
									    <?php $options =  [0 => lang('no'), 1 => lang('yes')]; ?>
                                        <?php
                                        echo form_dropdown('auto_share_customers', $options, $group_settings->auto_share_customers, 'class="form-control tip" id="auto_share_customers" data-placeholder="' . lang('select') . ' ' . lang('Automatically_share_customers') . '"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="auto_share_suppliers"><?= lang('Automatically_share_suppliers'); ?></label>

                                    <div class="controls">
									    <?php $options =  [0 => lang('no'), 1 => lang('yes')]; ?>
                                        <?php
                                        echo form_dropdown('auto_share_suppliers', $options, $group_settings->auto_share_suppliers, 'class="form-control tip" id="auto_share_suppliers" data-placeholder="' . lang('select') . ' ' . lang('Automatically_share_suppliers') . '"  required="required" style="width:100%;"');
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

