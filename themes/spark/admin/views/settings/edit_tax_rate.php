<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_tax_rate'); ?></h4>
        </div>
        <?php echo admin_form_open('system_settings/edit_tax_rate/' . $id); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
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
                                        echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $tax_rate->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
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
                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $tax_rate->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
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
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $tax_rate->brId), ' id="brId" class="form-control select"  required="required" style="width:100%"')
                                            ?>
                                        </div>
                                        <?php }
                                        } ?>

            <div class="form-group">
                <label for="category_id"><?php echo $this->lang->line('tax_type'); ?></label>

                <div class="controls"> <?php


                                    $tc[''] = '';
                    foreach ($tax_categories as $tax_category) {
                        $tc[$tax_category->id] = $tax_category->name;
                    }

				//$category_id = ['1' => lang('add_value_tax')];
                    echo form_dropdown('category_id', $tc, $tax_rate->category_id, 'class="form-control" id="category_id" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>

                <div
                    class="controls"> <?php echo form_input('name', $tax_rate->name, 'class="form-control" id="name" required="required" readonly'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line('code'); ?></label>

                <div
                    class="controls"> <?php echo form_input('code', $tax_rate->code, 'class="form-control" id="code"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="rate"><?php echo $this->lang->line('tax_rate'); ?></label>

                <div
                    class="controls"> <?php echo form_input('rate', $tax_rate->rate, 'class="form-control" id="rate" required="required" readonly'); ?> </div>
            </div>
            <div class="form-group">
			<?php
			$tax_active=false;
			if($tax_rate->tax_active==1)
			     $tax_active=true;
			 ?>

                <label for="tax_active"><?php echo $this->lang->line('tax_status_activate'); ?><br/><?= lang('You_will_activate_this_tax') ?></label>

                <div
                    class="controls"> <?php echo form_checkbox('tax_active','1',$tax_active,'id="tax_active"'); ?> </div>

            </div>
			<div class="form-group">
                <label for="type"><?php echo $this->lang->line('type'); ?></label>

                <div class="controls"> <?php $type = ['1' => lang('percentage'), '2' => lang('fixed')];
                    echo form_dropdown('type', $type, $tax_rate->type, 'class="form-control" id="type" required="required"'); ?> </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_tax_rate', lang('edit_tax_rate'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>

<script>
    $(document).ready(function () {

       $('#grpId').select2('readonly', true);
       $('#coId').select2('readonly', true);
       $('#brId').select2('readonly', true);
    }
    );

</script>
