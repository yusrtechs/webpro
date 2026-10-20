<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_account_type'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('accounts/add_account_type', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group">
                <?= lang('Account_type', 'Account_type'); ?>
                <?= form_input('categoryName', set_value('categoryName'), 'class="form-control tip" id="categoryName" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('English_name', 'English_name'); ?>
                <?= form_input('categoryNameEn', set_value('categoryNameEn'), 'class="form-control tip" id="categoryNameEn" '); ?>
            </div>
            <div class="form-group">
                <?= lang('description', 'description'); ?>
                <?= form_input('categoryDescription', set_value('categoryDescription'), 'class="form-control tip" id="categoryDescription"'); ?>
            </div>
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_account_type', lang('Add_account_type'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#base_unit').change(function(e) {
            var bu = $(this).val();
            if(bu > 0)
                $('#measuring').slideDown();
            else
                $('#measuring').slideUp();
        });
    });
</script>