<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_module'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('modules/edit_module/' . $module->id, $attrib); ?>

        <div class="modal-body">

            <p><?= lang('update_info'); ?></p>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $module->name, 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('English_name', 'name_en'); ?>
                <?= form_input('name_en', $module->name_en, 'class="form-control gen_slug" id="name_en" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $module->description), 'class="form-control tip" id="description"'); ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?= lang('Directory', 'directory'); ?>
                <?= form_input('directory', $module->directory, 'class="form-control" id="directory"'); ?>
            </div>


            <div class="form-group">
                <?= lang('Status', 'Status'); ?>
                <?php
                $opt = [1 => lang('active'), 0 => lang('inactive')];
                echo form_dropdown('status', $opt, $module->status, 'id="status" " class="form-control input-tip select" style="width:100%;"'); ?>
                </div>

            <div class="form-group all">
                <?= lang('icon', 'icon'); ?>
                <?= form_input('icon', $module->icon, 'class="form-control tip" id="icon" '); ?>
            </div>


        </div>
        </div>
            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>

            <?php echo form_hidden('id', $module->id); ?>
        </div>

        <div class="modal-footer">
            <?php echo form_submit('edit_module', lang('Edit_module'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'module');
        });
    });
</script>
