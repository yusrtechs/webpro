<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_model'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('cars/add_model', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('code', 'code'); ?>
                <?= form_input('code', '', 'class="form-control" id="code"'); ?>
            </div>
            <div class="form-group">
                <?= lang('Brand_name', 'brand_id') ?>
                <?php
                $br[''] = lang('select') . ' ' . lang('Brand_name');
                foreach ($brands as $brand) {
                    $br[$brand->id] = $brand->brand_name;
                }
                echo form_dropdown('brand_id', $br, '', 'class="form-control select" id="brand_id" style="width:100%"')
                ?>
            </div>
			
            <div class="form-group">
                <?= lang('Car_Type', 'model_name'); ?>
                <?= form_input('model_name', '', 'class="form-control gen_slug" id="model_name" required="required"'); ?>
            </div>

          
            <div class="form-group all">
                <?= lang('description', 'model_description'); ?>
                <?= form_input('model_description', set_value('model_description'), 'class="form-control tip" id="model_description" '); ?>
            </div>
            <!--
            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
            -->
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_model', lang('Add_model'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'brand');
        });
    });
</script>
