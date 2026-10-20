<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('deactivate'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('fixedassets/deactivate/' . $fixedasset->id, $attrib); ?>
        <div class="modal-body">
            <p><?php echo lang('Are_you_sure_you_disable_fixed_asset')." ( ". $fixedasset->code." - ".$fixedasset->name." ) "; ?></p>

            <div class="form-group">
                <label class="checkbox" for="confirm"><input type="checkbox" name="confirm" value="yes"
                                                             checked="checked" id="confirm"/> <?= lang('yes') ?></label>
            </div>

            <?php echo form_hidden($csrf); ?>
            <?php echo form_hidden(['id' => $fixedasset->id]); ?>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('deactivate', lang('deactivate'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
