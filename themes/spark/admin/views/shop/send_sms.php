<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="spark-sms-module">
    <!-- Blue SMS Header Banner -->
    <div class="spark-sms-banner">
        <div class="spark-sms-banner-content">
            <div class="spark-sms-banner-badge">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div>
                <h1 class="spark-sms-banner-title"><?= lang('send_sms'); ?></h1>
                <p class="spark-sms-banner-subtitle"><?= lang('send_sms_sub'); ?></p>
            </div>
        </div>

        <?php if ($check_balance) { ?>
            <div class="spark-sms-balance-badge">
                <i class="fa fa-credit-card"></i>
                <span><?= lang('balance'); ?>: <strong><?= $check_balance; ?></strong></span>
            </div>
        <?php } ?>
    </div>

    <!-- 3-Tab Navigation Bar -->
    <div class="spark-sms-nav">
        <a href="<?= admin_url('system_settings/sms_settings'); ?>" class="spark-sms-tab">
            <i class="fa fa-cogs"></i>
            <span><?= lang('sms_settings'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/send_sms'); ?>" class="spark-sms-tab is-active">
            <i class="fa fa-paper-plane"></i>
            <span><?= lang('send_sms'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/sms_log'); ?>" class="spark-sms-tab">
            <i class="fa fa-history"></i>
            <span><?= lang('sms_log'); ?></span>
        </a>
    </div>

    <!-- Main Content Body -->
    <div class="spark-sms-body">
        <p class="introtext" style="margin-bottom: 24px;"><?= lang('send_sms_heading'); ?></p>

        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'send-sms', 'autocomplete' => 'off'];
        echo admin_form_open('system_settings/send_sms', $attrib);
        ?>

        <!-- Recipient Selection Cards (Matching WhatsApp Style) -->
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="spark-sms-card">
                    <div class="spark-sms-card-title">
                        <i class="fa fa-user"></i>
                        <span><?= lang('customer', 'customer'); ?></span>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="input-group">
                            <?= form_input('customer', '', 'id="ncustomer" placeholder="' . lang('search_phone_by_customer') . '" class="form-control" style="width:100%;z-index:0;" autocomplete="off"'); ?>
                            <div class="input-group-addon no-print" style="padding: 0 40px; background: var(--spark-surface-ground, #f8fafc); border-radius: 0 12px 12px 0;">
                                <a>
                                    <input type="checkbox" class="checkbox" name="all_customer" id="all_customer" />
                                    <label for="all_customer" class="padding05" style="margin-bottom:0; font-weight:700; cursor:pointer;"><?= lang('all'); ?></label>
                                </a>
                            </div>
                        </div>
                        <small class="text-muted" style="margin-top: 6px; display: block;"><?= lang('search_phone_by_customer'); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="spark-sms-card">
                    <div class="spark-sms-card-title">
                        <i class="fa fa-truck"></i>
                        <span><?= lang('supplier', 'supplier'); ?></span>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="input-group">
                            <?= form_input('supplier', '', 'id="nsupplier" placeholder="' . lang('search_phone_by_supplier') . '" class="form-control" style="width:100%;z-index:0;" autocomplete="off"'); ?>
                            <div class="input-group-addon no-print" style="padding: 0 40px; background: var(--spark-surface-ground, #f8fafc); border-radius: 0 12px 12px 0;">
                                <a>
                                    <input type="checkbox" class="checkbox" name="all_supplier" id="all_supplier" />
                                    <label for="all_supplier" class="padding05" style="margin-bottom:0; font-weight:700; cursor:pointer;"><?= lang('all'); ?></label>
                                </a>
                            </div>
                        </div>
                        <small class="text-muted" style="margin-top: 6px; display: block;"><?= lang('search_phone_by_supplier'); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Mobile Numbers Input Card -->
        <div class="spark-sms-card">
            <div class="spark-sms-card-title">
                <i class="fa fa-phone"></i>
                <span><?= lang('mobile'); ?> <span class="text-danger">*</span></span>
                <small class="text-muted" style="font-weight: normal; margin-inline-start: 8px;">(<?= lang('separate_numbers_comma'); ?>)</small>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <?= form_input('mobile', set_value('mobile'), 'class="form-control tip" id="mobile" placeholder="' . lang('multiple_sms_numbers_placeholder') . '" style="direction:ltr; font-family:monospace;" required="required"'); ?>
            </div>
        </div>

        <!-- Message Composer Card -->
        <div class="spark-sms-card">
            <div class="spark-sms-card-title" style="justify-content: space-between;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="fa fa-comment-o"></i>
                    <span><?= lang('message_content'); ?> <span class="text-danger">*</span></span>
                </div>
                <div id="sms_char_count" class="badge" style="background: rgba(59, 130, 246, 0.15); color: #2563eb; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 8px;">
                    0 <?= lang('characters'); ?>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <?= form_textarea('message', set_value('message'), 'class="form-control skip" rows="3" style="height:100px; font-size:14px; line-height:1.5;" id="message" placeholder="' . lang('write_message_here') . '" required="required"'); ?>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" name="send_sms" value="1" class="spark-btn-sms-primary">
                <i class="fa fa-paper-plane"></i>
                <span><?= lang('send_sms'); ?></span>
            </button>
        </div>

        <?= form_close(); ?>
    </div>
</div>

<script>
$(document).ready(function() {

    var mobile_cus = '<?= $mobile_cus ?>';
    var mobile_sup = '<?= $mobile_sup ?>';
    var char_label = ' <?= lang('characters'); ?>';

    $('#ncustomer').autocomplete({
        minLength: 1,
        source: site.base_url+"customers/suggestions/0/0/1",
        response: function (event, ui) {
            if (ui.content != null) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }
            }
            $(this).removeClass('ui-autocomplete-loading');
            $(this).val('');
        },
        select: function (event, ui) {
            event.preventDefault();
            var mobile = $('#mobile').val();
            if (mobile.indexOf(ui.item.phone) == -1) {
                $('#mobile').val(mobile+ui.item.phone+', ')
            }
        }
    });

    $('#nsupplier').autocomplete({
        minLength: 1,
        source: site.base_url+"suppliers/suggestions/0/0/1",
        response: function (event, ui) {
            if (ui.content != null) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }
            }
            $(this).removeClass('ui-autocomplete-loading');
            $(this).val('');
        },
        select: function (event, ui) {
            event.preventDefault();
            var mobile = $('#mobile').val();
            if (mobile.indexOf(ui.item.phone) == -1) {
                $('#mobile').val(mobile+ui.item.phone+', ')
            }
        }
    });

    $('#all_customer').on('ifChecked', function (e) {
        $('#ncustomer').attr('disabled', true);
        var mobile = $('#mobile').val();
        if (mobile.indexOf(mobile_cus) == -1) {
            $('#mobile').val(mobile+mobile_cus);
        }
    });

    $('#all_customer').on('ifUnchecked', function (e) {
        $('#ncustomer').attr('disabled', false);
        $('#mobile').val('');
        if($("#all_supplier").is(":checked")){
            $('#mobile').val(mobile_sup);
        }
    });

    $('#all_supplier').on('ifChecked', function (e) {
        $('#nsupplier').attr('disabled', true);
        var mobile = $('#mobile').val();
        if (mobile.indexOf(mobile_sup) == -1) {
            $('#mobile').val(mobile+mobile_sup);
        }
    });

    $('#all_supplier').on('ifUnchecked', function (e) {
        $('#nsupplier').attr('disabled', false);
        $('#mobile').val('');
        if($("#all_customer").is(":checked")){
            $('#mobile').val(mobile_cus);
        }
    });

    $('#message').on('input', function() {
        var len = $(this).val().length;
        $('#sms_char_count').text(len + char_label);
    });

});
</script>
