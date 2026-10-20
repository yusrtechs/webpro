<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_category'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('products/edit_category/' . $category->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>
            <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
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
                                        echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $category->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
                                        ?>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $category->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
                                        ?>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $category->brId), ' id="brId" class="form-control select"  required="required" style="width:100%"')
                                            ?>
                                        </div>
                                        <?php }
                                        } ?>

            <div class="form-group">
                <?= lang('category_code', 'code'); ?>
                <?= form_input('code', set_value('code', $category->code), 'class="form-control" id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('category_name', 'name'); ?>
                <?= form_input('name', set_value('name', $category->name), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('English_name', 'English_name'); ?>
                <?= form_input('nameEn', set_value('nameEn', $category->nameEn), 'class="form-control " id="nameEn" '); ?>
            </div>

            <div class="form-group all">
                <?= lang('slug', 'slug'); ?>
                <?= form_input('slug', set_value('slug', $category->slug), 'class="form-control tip" id="slug" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $category->description), 'class="form-control tip" id="description" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('category_image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
            <div class="form-group">
                <?= lang('parent_category', 'parent') ?>
                <?php
                $cat[''] = lang('select') . ' ' . lang('parent_category');
                foreach ($categories as $pcat) {
                    $cat[$pcat->id] = $pcat->name;
                }
                echo form_dropdown('parent', $cat, (isset($_POST['parent']) ? $_POST['parent'] : $category->parent_id), 'class="form-control select" id="parent" style="width:100%"')
                ?>
            </div>
			<div class="form-group all">
                <?php
			$hide_pos=false;
			if($category->hide_pos==1)
			     $hide_pos=true;
			 ?>

				<?= form_checkbox('hide_pos', '1', $hide_pos,'class="form-control tip" id="hide_pos" '); ?>
				<?= lang('hide_in_pos', 'hide_pos'); ?>
			</div>

            <?php
			if($Settings->isTobacco==1){
                $isTobacco=false;
			if($category->isTobacco==1)
                $isTobacco=true;
                ?>
            <div class="form-group all">
            	<?= form_checkbox('isTobacco', '1', $isTobacco,'class="form-control tip" id="isTobacco" '); ?>
				<?= lang('A_store_that_provides_tobacco_products', 'A_store_that_provides_tobacco_products'); ?>
			</div>
            <?php
            }
            ?>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_category', lang('edit_category'), 'class="btn btn-primary"'); ?>
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
<script>
    $(document).ready(function () {

       $('#grpId').select2('readonly', true);
       $('#coId').select2('readonly', true);
       $('#brId').select2('readonly', true);
    }
    );

</script>
