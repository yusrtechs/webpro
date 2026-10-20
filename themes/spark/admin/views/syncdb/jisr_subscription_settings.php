<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Subscription_settings'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form','id'=>'tax_form'];
        echo admin_form_open('sync/jisr_subscription_settings', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>




            <div class="form-group">
                <label for="site_url">site_url</label>

                <div
                    class="controls"> <?php echo form_input('site_url', $settings->site_url, 'class="form-control" id="site_url" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label for="slug">slug</label>

                <div
                    class="controls"> <?php echo form_input('slug', $settings->slug, 'class="form-control" id="slug" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label for="api-key">api_key</label>

                <div
                    class="controls"> <?php echo form_input('api_key', $settings->access_token ? substr($settings->api_key,0,6)."XXXXXXXXXXXXXXXX" : $settings->api_key, 'class="form-control" id="api_key" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label for="secret">secret</label>

                <div
                    class="controls">
                    <?php echo form_input('secret', $settings->access_token ? substr($settings->secret,0,6)."XXXXXXXXXXXXXXXX" : $settings->secret, 'class="form-control" id="secret" required="required"'); ?> </div>
            </div>

            <div class="form-group">
                <label for="api_version">api_version</label>

                <div
                    class="controls"> <?php echo form_input('api_version', $settings->api_version, 'class="form-control" id="api_version" required="required"'); ?> </div>
            </div>

            <div class="form-group">
                <?= lang('access_token', 'access_token'); ?>
                <?php echo form_textarea('access_token', $settings->access_token, 'class="form-control" id="access_token"  name="access_token"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('jisr_subscription_settings', lang('Subscription_settings'), 'class="btn btn-primary" id="jisr_subscription_settings"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>



<script>
$('#access_token').prop("disabled", true);;
</script>