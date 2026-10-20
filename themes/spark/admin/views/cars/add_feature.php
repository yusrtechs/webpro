<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_Feature'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('cars/add_feature', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            
            <div class="form-group">
                <?= lang('co_type', 'type'); ?>
                <!--'important_features' => lang('Important_Features'),  -->
                <?php $car_type_options =  [ 'external_equipment' => lang('External_Equipment'), 'comfort' => lang('Comfort'), 'techniques' => lang('Techniques'), 'safty' => lang('Safty')]; ?>
                   <?= form_dropdown('type', $car_type_options, $cars_features->type, 'class="form-control tip" id="type" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('Feature_Name', 'feature_name'); ?>
                <?= form_input('feature_name', '', 'class="form-control gen_slug" id="feature_name" required="required"'); ?>
            </div>

           
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_feature', lang('Add_Feature'), 'class="btn btn-primary"'); ?>
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
