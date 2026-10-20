<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_a_manufacturing_product'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('manufacturing/edit_manufacturing/' . $man_details->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

                <div class="form-group">
                    <?= lang('Bill_Of_Material', 'Bill_Of_Material'); ?>
                    <?php
                    $bomArr['']='';
                    foreach ($boms as $bom) {
                        $bomArr[$bom->id] = $bom->name;
                    }
                    echo form_dropdown('bom_id', $bomArr, $man_details->bom_id, 'id="bom_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('Bill_Of_Material') . '" required="required" style="width:100%;" '); ?>
                </div>
            
                <div class="form-group all">
                <?= lang('Bill_quantity', 'quantity'); ?>
                <?= form_input('quantity', $this->sma->formatQuantity($man_details->quantity), 'class="form-control tip" id="quantity" required="required"'); ?>
				</div>
				
				<div class="form-group">
                    <?= lang('Deadline_Start', 'Deadline_Start'); ?>
                    <?php echo form_input('deadline_start', $this->sma->hrsd($man_details->deadline_start), 'readonly class="form-control input-tip date" id="deadline_start"  required="required"'); ?>
                </div>
                        
			
            
            
	

			<div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_textarea('description', $man_details->description, 'class="form-control" id="details"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_manufacturing', lang('Edit_a_manufacturing_product'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>

function search_select(){
	$('select').select2({
  matcher: function(term, text, option) {
    return text.toUpperCase().indexOf(term.toUpperCase())>=0 || option.val().toUpperCase().indexOf(term.toUpperCase())>=0;
  }
});
		
}

    $(document).ready(function() {
		search_select();
		
		
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'brand');
        });
    });
</script>
