<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="spark-whatsapp-module">
    <!-- Green WhatsApp Header Banner (Matching React Reference) -->
    <div class="spark-whatsapp-banner">
        <div class="spark-whatsapp-banner-content">
            <div class="spark-whatsapp-banner-badge">
                <i class="fa fa-whatsapp"></i>
            </div>
            <div>
                <h1 class="spark-whatsapp-banner-title"><?= lang('whatsapp_service'); ?></h1>
                <p class="spark-whatsapp-banner-subtitle"><?= lang('whatsapp_service_sub'); ?></p>
            </div>
        </div>
    </div>

    <!-- 4-Tab Navigation Bar -->
    <div class="spark-whatsapp-nav">
        <a href="<?= admin_url('system_settings/whatsapp_settings'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-cogs"></i>
            <span><?= lang('Whatsapp_settings'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/send_whatsapp'); ?>" class="spark-whatsapp-tab is-active">
            <i class="fa fa-paper-plane"></i>
            <span><?= lang('send_whatsapp'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/whatsapp_log'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-history"></i>
            <span><?= lang('Whatsapp_log'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/whatsapp_templates'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-file-text-o"></i>
            <span><?= lang('Whatsapp_templates'); ?></span>
        </a>
    </div>

    <!-- Main Content Body -->
    <div class="spark-whatsapp-body">
        <p class="introtext" style="margin-bottom: 24px;"><?= lang('send_whatsapp_heading'); ?></p>

        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'send-whatsapp', 'autocomplete' => 'off'];
        echo admin_form_open_multipart('system_settings/send_whatsapp', $attrib);
        ?>

        <?php if ($whatsapp_settings->config->gateway == "chatberry") { ?>
            <div class="alert alert-warning" style="border-radius: 12px; margin-bottom: 20px;">
                <i class="fa fa-exclamation-triangle"></i> <?= lang('You_cannot_send_public_messages_because_you_have_chosen_the_Chatberry_gateway'); ?>
            </div>
        <?php } ?>

        <!-- Recipient Selection Cards (Matching React Screenshot 2) -->
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="spark-whatsapp-card">
                    <div class="spark-whatsapp-card-title">
                        <i class="fa fa-user"></i>
                        <span><?= lang('customer', 'customer'); ?></span>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <?= form_input('customer', '', 'id="ncustomer" placeholder="' . lang('search_phone_by_customer') . '" class="form-control" style="width:100%;z-index:0;" autocomplete="off"'); ?>
                            <div class="input-group-addon no-print" style="padding: 0 40px; background: var(--spark-surface-ground, #f8fafc); border-radius: 0 12px 12px 0;">
                                <a>
                                    <input type="checkbox" class="checkbox" name="all_customer" id="all_customer" />
                                    <label for="all_customer" class="padding05" style="margin-bottom:0; font-weight:700; cursor:pointer;"><?= lang('all'); ?></label>
                                </a>
                            </div>
                        </div>
                        <small class="text-muted" style="margin-top: 6px; display: block;"><?= lang('search_phone_by_customer_tip'); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="spark-whatsapp-card">
                    <div class="spark-whatsapp-card-title">
                        <i class="fa fa-truck"></i>
                        <span><?= lang('supplier', 'supplier'); ?></span>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <?= form_input('supplier', '', 'id="nsupplier" placeholder="' . lang('search_phone_by_supplier') . '" class="form-control" style="width:100%;z-index:0;" autocomplete="off"'); ?>
                            <div class="input-group-addon no-print" style="padding: 0 40px; background: var(--spark-surface-ground, #f8fafc); border-radius: 0 12px 12px 0;">
                                <a>
                                    <input type="checkbox" class="checkbox" name="all_supplier" id="all_supplier" />
                                    <label for="all_supplier" class="padding05" style="margin-bottom:0; font-weight:700; cursor:pointer;"><?= lang('all'); ?></label>
                                </a>
                            </div>
                        </div>
                        <small class="text-muted" style="margin-top: 6px; display: block;"><?= lang('search_phone_by_supplier_tip'); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Mobile Numbers Input -->
        <div class="spark-whatsapp-card">
            <div class="spark-whatsapp-card-title">
                <i class="fa fa-phone"></i>
                <span><?= lang('mobile'); ?> <span class="text-danger">*</span></span>
                <small class="text-muted" style="font-weight: normal; margin-inline-start: 8px;">(<?= lang('To_use_more_than_one_mobile_number_please_separate_them_with_a_comma'); ?>)</small>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <?= form_textarea('mobile', set_value('mobile'), 'class="form-control skip" rows="2" style="height:70px;direction:ltr; font-family:monospace;" id="mobile" placeholder="' . lang('multiple_mobile_numbers_placeholder') . '" required="required"'); ?>
            </div>
        </div>

        <!-- Message Composer & Attachment -->
        <div class="spark-whatsapp-card">
            <div class="spark-whatsapp-card-title">
                <i class="fa fa-comment-o"></i>
                <span><?= lang('message_content'); ?></span>
            </div>

            <?php if ($whatsapp_settings->config->gateway == "arkanprowhatsapp") { ?>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="margin-inline-end: 20px; font-weight:700; cursor:pointer;">
                        <input type="radio" class="checkbox message_type" name="message_type" value="text" checked>
                        <span style="margin-inline-start: 6px;"><?= lang('text_message'); ?></span>
                    </label>

                    <label style="font-weight:700; cursor:pointer;">
                        <input type="radio" class="checkbox message_type" name="message_type" value="pdf">
                        <span style="margin-inline-start: 6px;"><?= lang('pdf_attachment'); ?></span>
                    </label>
                </div>

                <div class="form-group" id="text_message">
                    <div class="input-group">
                        <div class="input-group-addon no-print" style="padding: 0 20px; background: var(--spark-surface-ground, #f8fafc); border-radius: 12px 0 0 12px;">
                            <a class="btn btn-outline-secondary" type="button" id="emoji-trigger" style="padding: 0px !important; font-size: 18px; border:none; background:transparent;">😃</a>
                        </div>
                        <?= form_input('message', set_value('message'), 'class="form-control" style="width:100%;" id="message" placeholder="' . lang('write_message_here') . '" required="required"'); ?>
                    </div>
                </div>

                <div class="form-group" id="pdf_message" style="display:none;">
                    <p class="text-primary" style="margin-bottom: 8px;"><i class="fa fa-info-circle"></i> <?= lang('pdf_tip'); ?></p>
                    <?= lang('PDF_File', 'pdf_file'); ?>
                    <input type="file"
                        data-browse-label="<?= lang('browse'); ?>"
                        name="pdf_file"
                        id="pdf_file"
                        data-show-upload="false"
                        data-show-preview="false"
                        class="form-control file"
                        accept="application/pdf">
                </div>
            <?php } else if ($whatsapp_settings->config->gateway == "4whatsapp") { ?>
                <div class="form-group" style="margin-bottom: 0;">
                    <?= form_textarea('message', set_value('message'), 'class="form-control skip" rows="3" style="height:100px;" id="message" placeholder="' . lang('write_message_here') . '" required="required"'); ?>
                </div>
            <?php } ?>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" name="send_whatsapp" value="1" class="spark-btn-whatsapp-primary">
                <i class="fa fa-whatsapp fa-lg"></i>
                <span><?= lang('send_via_whatsapp'); ?></span>
            </button>
        </div>

        <?= form_close(); ?>
    </div>
</div>

<script>
$(document).ready(function() {

    var mobile_cus = '<?= $mobile_cus ?>';
    var mobile_sup = '<?= $mobile_sup ?>';

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

    $('.message_type').on('ifChecked', function(e){
        var type = $(this).val();
        if (type === 'text') {

            $('#text_message').show();
            $('#pdf_message').hide();

            $('#pdf_file').val('');

        } else {

            $('#text_message').hide();
            $('#pdf_message').show();

            $('#message').val('');
        }
    });

});
</script>
<!-- Emoji Picker Library -->
<script type="module">
  import { EmojiButton } from '<?= $assets ?>emoji-button/index.js';

  document.addEventListener('DOMContentLoaded', () => {
    const picker = new EmojiButton({
      position: 'top-end'
    });

    const trigger = document.querySelector('#emoji-trigger');
    const input = document.querySelector('#message');

    if (trigger && input) {
      trigger.addEventListener('click', () => {
        picker.togglePicker(trigger);
      });

      picker.on('emoji', selection => {
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;

        input.value = text.substring(0, start) + selection.emoji + text.substring(end);

        input.focus();
        input.selectionStart = input.selectionEnd = start + selection.emoji.length;
      });
    }
  });
</script>