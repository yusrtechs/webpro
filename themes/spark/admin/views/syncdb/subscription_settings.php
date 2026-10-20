<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Subscription_settings'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form','id'=>'tax_form'];
        echo admin_form_open('sync/subscription_settings', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>




            <div class="form-group">
                <label for="name">Name</label>

                <div
                    class="controls"> <?php echo form_input('name', $settings->name, 'class="form-control" id="name" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>

                <div
                    class="controls"> <?php echo form_input('description', $settings->description, 'class="form-control" id="description" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label for="client_id">client_id</label>

                <div
                    class="controls"> <?php echo form_input('client_id', $settings->access_token ? substr($settings->client_id,0,6)."XXXXXXXXXXXXXXXX" : $settings->client_id, 'class="form-control" id="client_id" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label for="secret">secret</label>

                <div
                    class="controls">
                    <?php echo form_input('secret', $settings->access_token ? substr($settings->secret,0,6)."XXXXXXXXXXXXXXXX" : $settings->secret, 'class="form-control" id="secret" required="required"'); ?> </div>
            </div>

            <div class="form-group">
                <label for="scopes">scopes</label>

                <div
                    class="controls"> <?php echo form_input('scopes', $settings->scopes, 'class="form-control" id="scopes" required="required"'); ?> </div>
            </div>

            <div class="form-group">
                <?= lang('access_token', 'access_token'); ?>
                <?php echo form_textarea('access_token', $settings->access_token, 'class="form-control" id="access_token"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('subscription_settings', lang('Subscription_settings'), 'class="btn btn-primary" id="subscription_settings"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>



<script>
$('#access_token').prop("disabled", true);;
</script>