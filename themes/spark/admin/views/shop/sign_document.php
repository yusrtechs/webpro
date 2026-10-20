<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue">
            <i class="fa-fw fa fa-pencil-square-o fa-lg"></i>
            <?= lang('Sign_a_document'); ?>
        </h2>
    </div>

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('sign_document_heading'); ?></p>

                <?php
                $attrib = [
                    'data-toggle' => 'validator',
                    'role' => 'form',
                    'id' => 'sign-document-form',
                    'autocomplete' => 'off',
                    'enctype' => 'multipart/form-data'
                ];
                echo admin_form_open('system_settings/sign_document', $attrib);
                ?>

                <div class="row">
                    <div class="col-md-12">

                        <!-- اسم الموقّع -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang('Signer_name', 'signer_name'); ?>
                                <?= form_input(
                                    'signer_name',
                                    set_value('signer_name'),
                                    'class="form-control" id="signer_name" required'
                                ); ?>
                            </div>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang('email', 'signer_email'); ?>
                                <?= form_input(
                                    'signer_email',
                                    set_value('signer_email'),
                                    'type="email" class="form-control" id="signer_email" required'
                                ); ?>
                            </div>
                        </div>

                        <!-- نوع المصادقة -->
<div class="col-md-3">
    <div class="form-group">
        <?= lang('Authentication_Type', 'authenticationType'); ?>
        <select name="authenticationType" id="authenticationType" class="form-control" required>
            <option value="0"><?= lang('None'); ?></option>
            <option value="7"><?= lang('Nafath'); ?></option>
            <option value="9"><?= lang('Absher'); ?></option>
            <option value="2"><?= lang('SMS_OTP'); ?></option>
            <option value="10"><?= lang('Whatsapp_OTP'); ?></option>
            <option value="3"><?= lang('Email_OTP'); ?></option>
        </select>
    </div>
</div>

<!-- National ID -->
<div class="col-md-3 auth-field" id="national_id_group" style="display:none;">
    <div class="form-group">
        <?= lang('National_ID_or_iqama', 'nationalId'); ?>
        <input
            type="text"
            name="nationalId"
            id="nationalId"
            class="form-control"
            maxlength="10"
        >
    </div>
</div>

<!-- Phone Number -->
<div class="col-md-3 auth-field" id="phone_group" style="display:none;">
    <div class="form-group">
        <?= lang('Mobile_No', 'phoneNumber'); ?>
        <input
            type="text"
            name="phoneNumber"
            id="phoneNumber"
            class="form-control"
            placeholder="+9665xxxxxxxx"
            style="direction: ltr;"
        >
    </div>
</div>

                        <!-- ملف PDF -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('document', 'document'); ?>
                                <input
                                    type="file"
                                    name="document"
                                    id="document"
                                    class="form-control"
                                    accept="application/pdf"
                                    required
                                >
                            </div>
                        </div>

                        <!-- زر الإرسال -->
                        <div class="col-md-12">
                            <?= form_submit(
                                'sign_document',
                                lang('Sign_a_document'),
                                'class="btn btn-primary"'
                            ); ?>
                        </div>

                    </div>
                </div>

                <?= form_close(); ?>

            </div>
        </div>
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

    function toggleAuthenticationFields() {

        var type = $('#authenticationType').val();

        // إخفاء جميع الحقول
        $('.auth-field').hide();
        $('#nationalId').prop('required', false);
        $('#phoneNumber').prop('required', false);

        switch (type) {

            // Nafath + Absher
            case '7':
            case '9':
                $('#national_id_group').show();
                $('#nationalId').prop('required', true);
                break;

            // SMS + WhatsApp
            case '2':
            case '10':
                $('#phone_group').show();
                $('#phoneNumber').prop('required', true);
                break;

            // None و Email OTP
            case '0':
            case '3':
            default:
                break;
        }
    }

    $('#authenticationType').on('change', toggleAuthenticationFields);

    // عند تحميل الصفحة
    toggleAuthenticationFields();

});
</script>
