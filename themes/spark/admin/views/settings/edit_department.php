<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Department'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('system_settings/edit_department/' . $department->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

            <div class="form-group">
                <?= lang('Department_code', 'code'); ?>
                <?= form_input('code', set_value('code', $department->code), 'class="form-control" id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('Department_name', 'name'); ?>
                <?= form_input('name', set_value('name', $department->name), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>

            
            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $department->desc), 'class="form-control tip" id="description" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('Parent_Department', 'parent') ?>
                <?php
                $cat[''] = lang('select') . ' ' . lang('Parent_Department');
                foreach ($departments as $pcat) {
                    $cat[$pcat->id] = $pcat->name;
                }
                echo form_dropdown('parent', $cat, (isset($_POST['parent']) ? $_POST['parent'] : $department->parent), 'class="form-control select" id="parent" style="width:100%"')
                ?>
            </div>
			
			
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_department', lang('Edit_Department'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'category');
        });
    });
</script>
