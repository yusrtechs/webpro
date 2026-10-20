<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(function () {
        $('.bcc').hide();
        $(".toggle_form").slideDown('hide');
        $('.toggle_form').click(function () {
            $("#bcc").slideToggle();
            return false;
        });
    });
</script>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Send_by_sms_whatsapp'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('sales/sms_whatsapp_quote/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('mobile', 'mobile'); ?>
                <input type="text" name="mobile" id="mobile" class="form-control" value="<?= $customer->phone ?>"
                       required="required"/>
            </div>
            <div class="form-group">
                <?= lang('message', 'message'); ?>
                <?= form_textarea('message', $text, 'class="form-control skip" rows="3" style="height:80px;" id="message" required="required"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('send_whatsapp', lang('send_whatsapp'), 'class="btn btn-primary"'); ?>
            <?php echo form_submit('send_sms', lang('Send_by_sms'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
