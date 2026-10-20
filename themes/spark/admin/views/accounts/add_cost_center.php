<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_cost_center'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('accounts/add_cost_center', $attrib); ?>
        <div class="modal-body col-md-12">
            <p><?= lang('enter_info'); ?></p>
			
			
			<div class="col-md-4">
            <div class="form-group">
                <?= lang('Cost_center_No', 'Cost_center_No'); ?>
                <?= form_input('costID', $costID, 'class="form-control tip" id="costID" required="required" readonly'); ?>
            </div>
            <div class="form-group">
               <?= lang('Cost_center_type', 'Cost_center_type'); ?>
                <?php
                $opts[0] = lang('Detailed_cost_center');
				$opts[1] = lang('Master_cost_center');
				
                
                ?>
                <?= form_dropdown('master_cost', $opts, set_value('master_cost'), 'class="form-control tip" id="master_cost" style="width:100%;"'); ?>
            </div>
			
			
			</div>
            

        	<div class="col-md-8">
            <div class="form-group">
                <?= lang('Cost_center_name', 'Cost_center_name'); ?>
                <?= form_input('costName', set_value('costName'), 'class="form-control tip" id="costName" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('English_name', 'English_name'); ?>
                <?= form_input('costNameEn', set_value('costNameEn'), 'class="form-control tip" id="costNameEn" '); ?>
            </div>
            
			
			
			
			</div>
            

        
		
		</div>
        <div class="modal-footer">
		<input type="hidden" name="id" value="<?= $id; ?>" />
            <?php echo form_submit('add_cost_center', lang('Add_cost_center'), 'class="btn btn-primary"'); ?>
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