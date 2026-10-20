<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_Stuff'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('system_settings/add_stuff', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('Stuff_code', 'code'); ?>
                <?= form_input('code', set_value('code'), 'class="form-control" id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('Stuff_name', 'name'); ?>
                <?= form_input('name', set_value('name'), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>



            <div class="form-group">
                <?= lang('Department', 'department') ?>
                <?php
                $cat[''] = lang('select') . ' ' . lang('Department');
                foreach ($departments as $pcat) {
                    $cat[$pcat->id] = $pcat->name;
                }
                echo form_dropdown('dept', $cat, (isset($_POST['dept']) ? $_POST['dept'] : ''), 'class="form-control select" id="dept"  style="width:100%"')
                ?>
            </div>


			 <div class="form-group">
                <?= lang('Stuff_type', 'Stuff_type') ?>
                <?php
                $Stff[''] = lang('select') . ' ' . lang('Stuff_type');
                    $Stff[1] = lang('salesman');
                    $Stff[2] = lang('Waiters');
                    $Stff[3] = lang('Drivers');
                echo form_dropdown('emp_type', $Stff, (isset($_POST['emp_type']) ? $_POST['emp_type'] : ''), 'class="form-control select" id="emp_type"  required="required" style="width:100%"')
                ?>
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_stuff', lang('Add_Stuff'), 'class="btn btn-primary"'); ?>
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
