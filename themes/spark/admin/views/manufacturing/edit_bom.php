<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Bill_Of_Material'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('manufacturing/edit_bom/' . $bom_details->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= lang('code', 'code'); ?>
                <?= form_input('code', $bom_details->code, 'class="form-control" id="code"'); ?>
            </div>
			
			
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $bom_details->name, 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('quantity', 'quantity'); ?>
                <?= form_input('quantity', $this->sma->formatQuantity($bom_details->quantity), 'class="form-control tip" id="quantity" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
		</div>
	    <div class="col-md-6">
            
			    <div class="form-group">
                    <?= lang('Raw_materials_warehouse', 'Raw_materials_warehouse'); ?>
                    <?php
                    
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_raw', $wh, $bom_details->wh_raw, 'id="wh_raw" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Raw_materials_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>
            
                <div class="form-group">
                    <?= lang('Manufacturing_warehouse', 'Manufacturing_warehouse'); ?>
                    <?php
                    
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_manufacturing', $wh, $bom_details->wh_manufacturing, 'id="wh_manufacturing" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Manufacturing_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>
                
				<div class="form-group">
                    <?= lang('Complete_materials_warehouse', 'Complete_materials_warehouse'); ?>
                    <?php
                    
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_complete', $wh, $bom_details->wh_complete, 'id="wh_complete" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Complete_materials_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>
				
				<div class="form-group">
                    <?= lang('Destruction_warehouse', 'Destruction_warehouse'); ?>
                    <?php
                    
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('wh_destruction', $wh, $bom_details->wh_destruction, 'id="wh_destruction" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Destruction_warehouse') . '" required="required" style="width:100%;" '); ?>
                </div>
				
            
            
		</div>
	</div>


			<div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_textarea('description', $bom_details->description, 'class="form-control" id="details"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_bom', lang('Edit_Bill_Of_Material'), 'class="btn btn-primary"'); ?>
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
