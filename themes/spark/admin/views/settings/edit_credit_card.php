<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('Edit_credit_card'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('system_settings/edit_credit_card/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('Credit_card_code', 'Credit_card_code'); ?>
                <?= form_input('code', set_value('code', $PaymentType->code), 'class="form-control tip" readonly id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('Credit_card_name', 'Credit_card_name'); ?>
                <?= form_input('name', set_value('name', $PaymentType->name), 'class="form-control tip" id="name" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('English_name', 'English_name'); ?>
                <?= form_input('second_name', $PaymentType->second_name, 'class="form-control tip" id="second_name" '); ?>
            </div>

            
            <div class="form-group">
                <input type="checkbox" value="1" name="isActive" id="isActive" <?= $PaymentType->isActive ? 'checked="checked"' : ''; ?>>
                <label class="padding-left-10" for="isActive"><?= lang('active'); ?></label>
            </div>
        </div>
        <div class="modal-footer">
            <?= form_submit('edit_credit_card', lang('Edit_credit_card'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<?= $modal_js ?>
