<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_modifier'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('products/edit_modifier/' . $modifier->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>
                <div
                    class="controls"> <?php echo form_input('name', $modifier->name, 'class="form-control" id="name" required="required"'); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="price"><?php echo $this->lang->line('price_addition'); ?></label>
                <div class="controls">
                    <?php echo form_input('price', $modifier->price, 'type="number" pattern="[0-9]" class="form-control" id="price" required="required"'); ?>
                </div>
            </div>

            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_modifier', lang('Edit_modifier'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
