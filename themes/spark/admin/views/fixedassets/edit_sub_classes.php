<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_sub_classes'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('fixedassets/edit_sub_classes/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>


            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line('code'); ?></label>
                <?php echo form_input('code', $sub_classes->code, 'readonly class="form-control" id="code" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>
                <?php echo form_input('name', $sub_classes->name, 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="nameEn"><?php echo $this->lang->line('English_name'); ?></label>
                <?php echo form_input('nameEn', $sub_classes->nameEn, 'class="form-control" id="nameEn"'); ?>
            </div>
            <div class="form-group">
                <?= lang('Parent_class', 'Parent_class') ?>
                <?php
                $cat[''] = lang('select') . ' ' . lang('Parent_class');
                foreach ($parentClasses as $parentClass) {
                    $cat[$parentClass->id] = $parentClass->name;
                }
                echo form_dropdown('parent_id', $cat,$sub_classes->parent_id , 'class="form-control select" id="parent_id"  required="required" style="width:100%"')
                ?>
            </div>






        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_sub_classes', lang('Edit_sub_classes'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>