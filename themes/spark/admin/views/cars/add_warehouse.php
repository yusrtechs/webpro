<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_warehouse'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('products/add_warehouse', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line('code'); ?></label>
                <?php echo form_input('code', $warehouse_code, 'class="form-control" id="code" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>
                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="branch_name"><?php echo $this->lang->line('Branch_name'); ?></label>
                <?php echo form_input('branch_name', '', 'class="form-control" id="branch_name" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="vat_reg_no"><?php echo $this->lang->line('VAT_Regesteration_Number').' ( '.$this->lang->line('Required_fifteen_digits').' ) '; ?></label>
                <?php echo form_input('vat_reg_no', '', 'type="number" pattern="[0-9]{10}" maxlength="15" class="form-control" id="vat_reg_no" required="required" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="cr_no"><?php echo $this->lang->line('CR_Number').' ( '.$this->lang->line('Required_ten_digits').' ) '; ?></label>
                <?php echo form_input('cr_no', '', 'type="number" pattern="[0-9]{10}" maxlength=10" class="form-control" id="cr_no" required="required" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="price_group"><?php echo $this->lang->line('price_group'); ?></label>
                <?php
                $pgs[''] = lang('select') . ' ' . lang('price_group');
                foreach ($price_groups as $price_group) {
                    $pgs[$price_group->id] = $price_group->name;
                }
                echo form_dropdown('price_group', $pgs, $Settings->price_group, 'class="form-control tip select" id="price_group" style="width:100%;"');
                ?>
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
                <label class="control-label" for="building_no"><?php echo $this->lang->line('Building_No').' ( '.$this->lang->line('Required_four_digits').' ) '; ?></label>
                <?php echo form_input('building_no', '', 'type="number" pattern="[0-9]{4}" maxlength="4" class="form-control" id="building_no" required="required"'); ?>
            </div>
			<div class="form-group">
                <label class="control-label" for="street_name"><?php echo $this->lang->line('Street_name'); ?></label>
                <?php echo form_input('street_name', '', 'class="form-control" id="street_name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="district_name"><?php echo $this->lang->line('District_name'); ?></label>
                <?php echo form_input('district_name', '', 'class="form-control" id="district_name" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="city"><?php echo $this->lang->line('city'); ?></label>
                <?php echo form_input('city', '', 'class="form-control" id="city" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="postal_code"><?php echo $this->lang->line('postal_code').' ( '.$this->lang->line('Required_five_digits').' ) '; ?></label>
                <?php echo form_input('postal_code', '', 'type="number" pattern="[0-9]{5}" maxlength="5" class="form-control" id="postal_code" required="required"'); ?>
            </div>
			<div class="form-group">
                <label class="control-label" for="additional_no"><?php echo $this->lang->line('Additional_No').' ( '.$this->lang->line('Required_four_digits').' ) '; ?></label>
                <?php echo form_input('additional_no', '', 'type="number" pattern="[0-9]{4}" maxlength="4" class="form-control" id="additional_no" required="required"'); ?>
            </div>
			<div class="form-group">
                <label class="control-label" for="state"><?php echo $this->lang->line('state'); ?></label>
                <?php echo form_input('state', '', 'class="form-control" id="state" required="required"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="country"><?php echo $this->lang->line('country'); ?></label>
                <?php echo form_input('country', '', 'class="form-control" id="country" required="required"'); ?>
            </div>
            
            
		  
          </div>
		</div>  
		  
            <div class="form-group">
                <label class="control-label" for="address"><?php echo $this->lang->line('address'); ?></label>
                <?php echo form_textarea('address', '', 'class="form-control" id="address" required="required"'); ?>
            </div>
            
			<p class="text-primary"><?= lang('Recommended')." : ".lang('logo_image_tip'); ?></p>

            <div class="form-group">
                <?= lang('Warehouse_logo', 'Warehouse_logo') ?>
                <input id="warehouse_logo" type="file" data-browse-label="<?= lang('browse'); ?>" name="warehouse_logo" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
            <div class="form-group">
                <?= lang('warehouse_map', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_warehouse', lang('add_warehouse'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
