<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Group'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('branches/edit_main_group/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line('Code_Group'); ?></label>
                <?php echo form_input('code', $group->code, 'readonly class="form-control" id="code" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>
                <?php echo form_input('name', $group->name, 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="nameEn"><?php echo $this->lang->line('English_name'); ?></label>
                <?php echo form_input('nameEn', $group->nameEn, 'class="form-control" id="nameEn"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="phone"><?php echo $this->lang->line('phone'); ?></label>
                <?php echo form_input('phone', $group->phone, 'class="form-control" id="phone"'); ?>
            </div>

            <p class="text-primary"><?= lang('Recommended')." : ".lang('logo_image_tip'); ?></p>

            <div class="form-group">
                <?= lang('Logo', 'Logo') ?>
                <input id="group_logo" type="file" data-browse-label="<?= lang('browse'); ?>" name="group_logo" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>


		  </div>


		  <div class="col-md-6">

          <div class="form-group">
                <label class="control-label" for="address"><?php echo $this->lang->line('address'); ?></label>
                <?php echo form_textarea('address', $group->address, 'class="form-control" id="address" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="addressEn"><?php echo $this->lang->line('English_address'); ?></label>
                <?php echo form_textarea('addressEn', $group->addressEn, 'class="form-control" id="addressEn"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="email"><?php echo $this->lang->line('email'); ?></label>
                <?php echo form_input('email', $group->email, 'class="form-control" id="email"'); ?>
            </div>



          </div>
		</div>





        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_group', lang('Edit_Group'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>