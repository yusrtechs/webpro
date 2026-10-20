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
        <a href="<?= admin_url('system_settings/whatsapp_settings'); ?>" class="spark-whatsapp-tab is-active">
            <i class="fa fa-cogs"></i>
            <span><?= lang('Whatsapp_settings'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/send_whatsapp'); ?>" class="spark-whatsapp-tab">
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
        <p class="introtext" style="margin-bottom: 24px;"><?= lang('update_info'); ?></p>

        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'whatsapp-settings', 'autocomplete' => 'off'];
        echo admin_form_open('system_settings/whatsapp_settings', $attrib);
        ?>

        <div class="row">
            <!-- Gateway & Authentication Card -->
            <div class="col-md-7 col-sm-12">
                <div class="spark-whatsapp-card">
                    <h3 class="spark-whatsapp-card-title">
                        <i class="fa fa-plug"></i>
                        <span><?= lang('gateway_config'); ?></span>
                    </h3>

                    <div class="form-group">
                        <label for="gateway"><i class="fa fa-server" style="color:#16a34a;margin-inline-end:4px;"></i> <?= lang('gateway'); ?></label>
                        <?php
                        if (isset($this->Settings->isArkanproWhatsapp) && $this->Settings->isArkanproWhatsapp == 1) {
                            $gopts = [
                                'arkanprowhatsapp' => 'ArkanPro Whatsapp (' . lang('Recommended') . ')',
                                '4whatsapp'        => '4whatsapp',
                                'chatberry'        => 'Chatberry',
                            ];
                        } else {
                            $gopts = [
                                '4whatsapp' => '4whatsapp',
                                'chatberry' => 'Chatberry',
                            ];
                        }
                        ?>
                        <?= form_dropdown('gateway', $gopts, set_value('gateway', $whatsapp_settings->config->gateway), 'class="form-control tip" id="gateway" required="required" style="width:100%;"'); ?>
                    </div>

                    <div class="form-group" style="display:none;">
                        <?= lang('auto_send', 'auto_send'); ?>
                        <?php $opts = [1 => lang('yes')]; ?>
                        <?= form_dropdown('auto_send', $opts, set_value('auto_send', $whatsapp_settings->auto_send), 'class="form-control tip" id="auto_send" required="required" style="width:100%;display:none;"'); ?>
                    </div>

                    <div class="arkanprowhatsapp dn">
                        <div class="form-group" style="display:block;">
                            <label for="arkanprowhatsapp_sessionId"><i class="fa fa-key" style="color:#16a34a;margin-inline-end:4px;"></i> <?= lang('session_id'); ?></label>
                            <?= form_input('sessionId', set_value('arkanprowhatsapp_sessionId', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->sessionId ?? str_replace("cpesoft_", "", $this->db->database)), 'class="form-control tip" id="arkanprowhatsapp_sessionId" required="required" ' . ($whatsapp_status == 'connect' ? 'readonly' : '')); ?>
                        </div>
                    </div>

                    <div class="4whatsapp dn">
                        <div class="form-group">
                            <label for="4whatsapp_user"><i class="fa fa-user" style="color:#16a34a;margin-inline-end:4px;"></i> <?= lang('user', '4whatsapp_user'); ?></label>
                            <?= form_input('4whatsapp_user', set_value('4whatsapp_user', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->user ?? ''), 'class="form-control tip" id="4whatsapp_user" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <label for="4whatsapp_password"><i class="fa fa-lock" style="color:#16a34a;margin-inline-end:4px;"></i> <?= lang('password', '4whatsapp_password'); ?></label>
                            <?= form_input('4whatsapp_password', set_value('4whatsapp_password', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->password ?? ''), 'class="form-control tip" id="4whatsapp_password" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <label for="4whatsapp_code"><i class="fa fa-building" style="color:#16a34a;margin-inline-end:4px;"></i> <?= lang('Company_code', '4whatsapp_code'); ?></label>
                            <?= form_input('4whatsapp_code', set_value('4whatsapp_code', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->code ?? ''), 'class="form-control tip" id="4whatsapp_code" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <label for="4whatsapp_phone"><i class="fa fa-phone" style="color:#16a34a;margin-inline-end:4px;"></i> <?= lang('Mobile_No', '4whatsapp_phone'); ?> <small class="text-muted"><?= lang('mobile_number_example'); ?></small></label>
                            <?= form_input('4whatsapp_phone', set_value('4whatsapp_phone', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->phone ?? ''), 'class="form-control tip" id="4whatsapp_phone" required="required"'); ?>
                        </div>
                    </div>

                    <div class="chatberry dn">
                        <div class="form-group" style="display:block;">
                            <label for="authorization_token"><i class="fa fa-shield" style="color:#16a34a;margin-inline-end:4px;"></i> <?= lang('YOUR_AUTHORIZATION_TOKEN', 'authorization_token'); ?></label>
                            <?= form_input('authorization_token', set_value('authorization_token', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->authorization_token ?? ''), 'class="form-control tip" id="authorization_token" required="required" '); ?>
                        </div>
                    </div>

                    <!-- Connection Status & QR Code Section -->
                    <?php if ($whatsapp_settings->config->gateway == "arkanprowhatsapp") { ?>
                        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--spark-border-default, #e2e8f0);">
                            <?php if ($whatsapp_status == 'connect') { ?>
                                <div class="alert alert-info" style="border-radius: 12px; display: flex; align-items: center; gap: 10px; margin-bottom: 0;">
                                    <i class="fa fa-check-circle fa-lg text-info"></i>
                                    <div>
                                        <strong><?= lang('Status'); ?>:</strong> <?= lang('connected'); ?>
                                        <img src="<?= base_url() . 'assets/images/connect_status.png'; ?>" style="margin-inline-start: 8px; max-width: 18px; height: auto;">
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <div class="actions">
                                        <a href="#" class="spark-btn-whatsapp-primary" id="create_a_new_QR2" style="width: auto;">
                                            <i class="fa fa-qrcode"></i>
                                            <span><?= lang('Scan_QR_Code'); ?></span>
                                        </a>
                                        <div class="performing" style="display:none; margin-top: 16px;">
                                            <i class="fa fa-refresh fa-spin fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else if ($whatsapp_settings->config->gateway == "4whatsapp") { ?>
                        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--spark-border-default, #e2e8f0);">
                            <div class="form-group" style="margin-bottom: 0;">
                                <div class="actions">
                                    <a href="#" class="spark-btn-whatsapp-primary" id="create_a_new_QR2" style="width: auto;">
                                        <i class="fa fa-qrcode"></i>
                                        <span><?= lang('Scan_QR_Code'); ?></span>
                                    </a>
                                    <div class="performing" style="display:none; margin-top: 16px;">
                                        <i class="fa fa-refresh fa-spin fa-2x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Automatic Notifications Box (Matching React Screenshot) -->
            <div class="col-md-5 col-sm-12">
                <div class="spark-whatsapp-auto-box">
                    <h3 class="spark-whatsapp-card-title">
                        <i class="fa fa-bell"></i>
                        <span><?= lang('auto_notifications'); ?></span>
                    </h3>

                    <div id="div_auto_send">
                        <div class="spark-whatsapp-check-item">
                            <input name="sales" type="checkbox" id="sales" />
                            <label for="sales"><?= lang('notify_sales_order'); ?></label>
                        </div>
                        <div class="spark-whatsapp-check-item">
                            <input name="pos" type="checkbox" id="pos" />
                            <label for="pos"><?= lang('notify_pos_sales'); ?></label>
                        </div>
                        <div class="spark-whatsapp-check-item">
                            <input name="quotes" type="checkbox" id="quotes" />
                            <label for="quotes"><?= lang('notify_quotes'); ?></label>
                        </div>
                        <div class="spark-whatsapp-check-item">
                            <input name="award_points" type="checkbox" id="award_points" />
                            <label for="award_points"><?= lang('notify_award_points'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 16px;">
            <?= form_submit('whatsapp_settings', lang('save_settings'), 'class="spark-btn-whatsapp-primary"'); ?>
        </div>

        <?= form_close(); ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.dn').hide();
        var gw = $('#gateway').val();
        if (gw) {
            $('.'+gw).slideDown();
        }

        $(document).on('change', '#gateway', function() {
            var gw = $(this).val();
            if (gw) {
                $('.dn').slideUp();
                $('.'+gw).slideDown();
            }
            return;
        });

        var as = $('#auto_send').val();
            if (as==1) {
                $('#div_auto_send').slideDown();
            }else{
                $('#div_auto_send').slideUp();
            }


        $(document).on('change', '#auto_send', function() {
            var as = $(this).val();
            if (as==1) {
                $('#div_auto_send').slideDown();
            }else{
                $('#div_auto_send').slideUp();
            }
            return;
        });

        var sales='<?= $whatsapp_settings->sales; ?>';
        if (sales == 1){
            $('#sales').iCheck('check');
        }else{
            $('#sales').iCheck('uncheck');
        }

        var quotes='<?= $whatsapp_settings->quotes; ?>';
        if (quotes == 1){
            $('#quotes').iCheck('check');
        }else{
            $('#quotes').iCheck('uncheck');
        }

        var pos='<?= $whatsapp_settings->pos; ?>';
        if (pos == 1){
            $('#pos').iCheck('check');
        }else{
            $('#pos').iCheck('uncheck');
        }

        var award_points='<?= $whatsapp_settings->award_points; ?>';
        if (award_points == 1){
            $('#award_points').iCheck('check');
        }else{
            $('#award_points').iCheck('uncheck');
        }

        $(document).on('click', '#create_a_new_QR', function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.prop('disabled', true);
            var sessionId = $('#arkanprowhatsapp_sessionId').val();
            var perf = btn.closest('.actions').find('.performing');
            perf.html('<i class="fa fa-refresh fa-spin fa-2x text-success"></i>').show();

            const eventSource = new EventSource('https://iparkanwebsitforwhats/sessions/'+sessionId +'/add-sse');
            eventSource.onerror = function() {
                eventSource.close();
                btn.prop('disabled', false);
            };
            eventSource.onmessage = (event) => {
                const data = JSON.parse(event.data);
                if(data.receivedPendingNotifications){
                    perf.html('<img src="'+site.url +'assets/images/connect.png'+'" class="qrimg" style="max-width:350px;">');
                }else if(data.qr){
                    perf.html('<img src="'+data.qr+'" class="qrimg" style="max-width:250px;">');
                }else{
                    perf.html(data.error || 'Error').show();
                }
            };

            return false;
        });

        $(document).on('click', '#create_a_new_QR2', function (e) {
            var btn = $(this);
            e.preventDefault();
            btn.prop('disabled', true);
            var perf = btn.closest('.actions').find('.performing');
            perf.html('<i class="fa fa-refresh fa-spin fa-2x text-success"></i>').show();

            $.ajax({
                url: '<?=admin_url('system_settings/generate_qr')?>',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if(data.msg){
                        perf.html('<img src="'+data.msg+'" class="qrimg" style="max-width:250px; display:inline-block; border-radius:8px; border:1px solid #e2e8f0; padding:8px; background:#fff;">').show();
                    }else{
                        if(data.err != 'Session already exists') {
                            perf.html('<div class="text-danger" style="margin-top:10px;">' + (data.err || 'Error') + '</div>').show();
                        }
                    }
                    btn.prop('disabled', false);
                },
                error: function () {
                    perf.html('<div class="text-danger" style="margin-top:10px;"><?= lang('request_failed'); ?></div>').show();
                    btn.prop('disabled', false);
                }
            });

            return false;
        });

    });
</script>
