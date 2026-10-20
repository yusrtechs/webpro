<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_model'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('cars/edit_model/' . $model->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

            <div class="form-group">
                <?= lang('code', 'code'); ?>
                <?= form_input('code', $model->code, 'class="form-control" id="code"'); ?>
            </div>
          
            <div class="form-group">
                <?= lang('Brand_name', 'brand_id') ?>
                <?php
                $br[''] = lang('select') . ' ' . lang('Brand_name');
                foreach ($brands as $brand) {
                    $br[$brand->id] = $brand->brand_name;
                }
                echo form_dropdown('brand_id', $br, (isset($_POST['brand_id']) ? $_POST['brand_id'] : $model->brand_id), 'class="form-control select" id="brand_id" style="width:100%"')
                ?>
            </div>
            <div class="form-group">
                <?= lang('Car_Type', 'model_name'); ?>
                <?= form_input('model_name', $model->model_name, 'class="form-control gen_slug" id="model_name" required="required"'); ?>
            </div>

         
            <div class="form-group all">
                <?= lang('description', 'model_description'); ?>
                <?= form_input('model_description', set_value('model_description', $model->model_description), 'class="form-control tip" id="model_description"'); ?>
            </div>

            <?php echo form_hidden('id', $model->id); ?>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_model', lang('edit_model'), 'class="btn btn-primary"'); ?>
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
