<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Chest_Pocket_Padding'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('tailor/edit_chest_pocket_padding/' . $chest_pocket_paddings->Chest_Pocket_Padding_Type_ID, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>



                <div class="form-group all">
                <?= lang('Chest_Pocket_Padding_Name', 'Chest_Pocket_Padding_Name'); ?>
                <?= form_input('Chest_Pocket_Padding_Name', $chest_pocket_paddings->Chest_Pocket_Padding_Name, 'class="form-control tip" id="Chest_Pocket_Padding_Name" required="required"'); ?>
				</div>


                <div class="form-group all">
                    <?= lang('image', 'Chest_Pocket_Padding_Image') ?>
                    <input id="Chest_Pocket_Padding_Image" type="file" data-browse-label="<?= lang('browse'); ?>" name="Chest_Pocket_Padding_Image" data-show-upload="false"
                       data-show-preview="false" accept="image/*" class="form-control file">
                </div>




        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_chest_pocket_padding', lang('Edit_Chest_Pocket_Padding'), 'class="btn btn-primary"'); ?>
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
