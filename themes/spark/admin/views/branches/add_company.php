<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_Company'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('branches/add_company', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

        <div class="row">
          <div class="col-md-6">
          <?php
          if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
          <div class="form-group">
                <?= lang('Main_group', 'Main_group') ?>
                <?php
                $cat[''] = lang('select') . ' ' . lang('Main_group');
                foreach ($mainGroups as $mainGroup) {
                    $cat[$mainGroup->id] = $mainGroup->name;
                }
                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
                ?>
            </div>
            <?php }    ?>
            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line('Code_Company'); ?></label>
                <?php echo form_input('code', $company_code, 'class="form-control" id="code" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>
                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="nameEn"><?php echo $this->lang->line('English_name'); ?></label>
                <?php echo form_input('nameEn', '', 'class="form-control" id="nameEn" '); ?>
            </div>


            <div class="form-group">
                <label class="control-label" for="phone"><?php echo $this->lang->line('phone'); ?></label>
                <?php echo form_input('phone', '', 'class="form-control" id="phone"'); ?>
            </div>





            <div class="form-group">
                <label class="control-label" for="email"><?php echo $this->lang->line('email'); ?></label>
                <?php echo form_input('email', '', 'class="form-control" id="email"'); ?>
            </div>
            </div>


		  <div class="col-md-6">



          <div class="form-group">
                <label class="control-label" for="address"><?php echo $this->lang->line('address'); ?></label>
                <?php echo form_textarea('address', '', 'class="form-control" id="address" required="required"'); ?>
            </div>

			<div class="form-group">
                <label class="control-label" for="addressEn"><?php echo $this->lang->line('English_address'); ?></label>
                <?php echo form_textarea('addressEn', '', 'class="form-control" id="addressEn"'); ?>
            </div>


            <p class="text-primary"><?= lang('Recommended')." : ".lang('logo_image_tip'); ?></p>

<div class="form-group">
    <?= lang('Logo', 'Logo') ?>
    <input id="company_logo" type="file" data-browse-label="<?= lang('browse'); ?>" name="company_logo" data-show-upload="false" data-show-preview="false" class="form-control file">
</div>

          </div>
		</div>




        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_company', lang('Add_Company'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
