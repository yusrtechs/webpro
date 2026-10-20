<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('Whatsapp_settings'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'whatsapp-settings', 'autocomplete="off"'];
                echo admin_form_open('system_settings/whatsapp_settings', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('gateway', 'gateway'); ?>
                            <?php
                            if(str_replace("cpesoft_","",$this->db->database)=="arkanpro" || str_replace("cpesoft_","",$this->db->database)=="spark"){
                            $gopts = [
                                'arkanprowhatsapp'     => 'ArkanPro Whatsapp',
                                '4whatsapp'     => '4whatsapp',
                            ];
                            }else{
                                $gopts = [
                                '4whatsapp'     => '4whatsapp',
                            ];
                            }

                            ?>
                            <?= form_dropdown('gateway', $gopts, set_value('gateway', $whatsapp_settings->config->gateway), 'class="form-control tip" id="gateway" required="required" style="width:100%;"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('auto_send', 'auto_send'); ?>
                            <?php $opts = [ 1 => lang('yes')]; ?>
                            <?= form_dropdown('auto_send', $opts, set_value('auto_send', $whatsapp_settings->auto_send), 'class="form-control tip" id="auto_send" required="required" style="width:100%;display:none;"'); ?>
                        </div>

                        <div class="form-group" id="div_auto_send" style="display:none;">
                            <input name="sales" type="checkbox" id="sales" style="display:inline-block;" />
                            <label for="sales" class="padding05"><?= lang('sales'); ?></label>
                            <input name="pos" type="checkbox" id="pos" style="display:inline-block;" />
                            <label for="pos" class="padding05"><?= lang('pos_sales'); ?></label>
                            <input name="quotes" type="checkbox" id="quotes"  style="display:inline-block;" />
                            <label for="quotes" class="padding05"><?= lang('quotes'); ?></label>
                            <input name="award_points" type="checkbox" id="award_points" style="display:inline-block;" />
                            <label for="award_points" class="padding05"><?= lang('award_points'); ?></label>
                        </div>
                            <?php
                            if($whatsapp_settings->config->gateway=="4whatsapp"){
                                ?>
                        <div class="form-group" style="margin-top:50px;">
                            <div class="actions">
                                <div class="col-xs-4"><a href="#" class="btn btn-primary btn-block" id="create_a_new_QR"><strong><?= lang('Create_a_new_QR') ?></strong></a>
                                </div>
                                <div class="performing col-xs-8" style="display:none;"><i
                                    class="fa fa-refresh fa-spin"></i>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                                <?php
                            }
                            ?>

                            <?php
                            if($whatsapp_settings->config->gateway=="arkanprowhatsapp"){
                                ?>
                        <div class="form-group" style="margin-top:50px;">
                            <div class="actions">
                                <div class="col-xs-4"><a href="#" class="btn btn-primary btn-block" id="create_a_new_QR1"><strong><?= lang('Create_a_new_QR') ?></strong></a>
                                </div>
                                <div class="performing col-xs-8" style="display:none;"><i
                                    class="fa fa-refresh fa-spin"></i>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                                <?php
                            }
                            ?>


                    </div>



                    <div class="clearfix"></div>

                    <div class="col-md-6">

                    <div class="4whatsapp dn">
                        <div class="form-group">
                            <?= lang('user', '4whatsapp_user'); ?>
                            <?= form_input('4whatsapp_user', set_value('4whatsapp_user', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->user ?? ''), 'class="form-control tip" id="4whatsapp_user" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('password', '4whatsapp_password'); ?>
                            <?= form_password('4whatsapp_password', set_value('4whatsapp_password', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->password ?? ''), 'class="form-control tip" id="4whatsapp_password" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('Company_code', '4whatsapp_code'); ?>
                            <?= form_input('4whatsapp_code', set_value('4whatsapp_code', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->code ?? ''), 'class="form-control tip" id="4whatsapp_code" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('Mobile_No', '4whatsapp_phone'); ?> Ex: (9665XXXXXXXX)
                            <?= form_input('4whatsapp_phone', set_value('4whatsapp_phone', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->phone ?? ''), 'class="form-control tip" id="4whatsapp_phone" required="required"'); ?>
                        </div>
                    </div>

                    <div class="arkanprowhatsapp dn">
                        <div class="form-group">
                            <?= lang('sessionId', 'arkanprowhatsapp_sessionId'); ?>
                            <?= form_input('sessionId', set_value('arkanprowhatsapp_sessionId', $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->sessionId ?? str_replace("cpesoft_","",$this->db->database)), 'class="form-control tip" id="arkanprowhatsapp_sessionId" required="required" readonly'); ?>
                        </div>
                    </div>



                    <?= form_submit('whatsapp_settings', lang('submit'), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>
            </div>
        </div>
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



        $('#create_a_new_QR').click(function (e) {
			var res='Not Action';

            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();


            $.ajax({
                url: '<?=admin_url('system_settings/generate_qr')?>',
                type: 'get',

                success: function (data) {
                    //console.log(data);
                    if(data.msg){
                        perf.html('<img src="'+data.msg+'" class="qrimg" style="max-width:250px;">');
                    }else{
                        if(data.err!='Session already exists')
                        perf.html(data.err).show();
                    }
                        btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });

            return false;
        });



        $('#create_a_new_QR1').click(function (e) {
			var res='Not Action';


            var btn = $(this);
            e.preventDefault();
            btn.attr('disabled', true);
            var perf = btn.parent().parent('.actions').children('.performing');
            perf.show();


            var url  = 'http://iparkanwebsitforwhats:3000/sessions/add';






var xhr = new XMLHttpRequest();
xhr.open("POST", url);


xhr.setRequestHeader("Content-Type", "application/json");



xhr.onreadystatechange = function () {
   if (xhr.readyState === 4) {
      //console.log(xhr.status);
      var data=JSON.parse(xhr.responseText);
        //console.log(qr);
        //console.log(qr.qr);

     //otp->otp_token //access_token=

						if(xhr.status=="200"){
                    if(data.qr){
                        perf.html('<img src="'+data.qr+'" class="qrimg" style="max-width:250px;">');
                    }else{
                        if(data.err!='Session already exists')
                        perf.html(data.error).show();
                    }
                        btn.attr('disabled', false);



						}





		//perf.html(xhr);
		//btn.attr('disabled', false);


   }
   };

//var data = "grant_type=client_credentials&scope=customer-authorization otp-validation redemption-transaction-reversal redemption-transactions&client_id=483020386674213ca6539348c95a02f0&client_secret=cd3a599408d5b4b9a96af37660289081";
var sessionId='<?= $whatsapp_settings->config->{$whatsapp_settings->config->gateway}->sessionId ?>';
var data = '{"sessionId":"'+sessionId+'"}';

xhr.send(data);


           /* $.ajax({
                url: '<?=admin_url('system_settings/generate_qr')?>',
                type: 'get',

                success: function (data) {
                    //console.log(data);
                    if(data.msg){
                        perf.html('<img src="'+data.msg+'" class="qrimg" style="max-width:250px;">');
                    }else{
                        if(data.err!='Session already exists')
                        perf.html(data.err).show();
                    }
                        btn.attr('disabled', false);
                },
                error: function () {
                    perf.html('Request Failed!').show();
                    btn.attr('disabled', false);
                }
            });*/

            return false;
        });

    });
</script>
