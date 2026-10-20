<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_price_group'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('sales/edit_price_group/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('group_name'); ?></label>
                <?php echo form_input('name', $price_group->name, 'class="form-control" id="name" required="required"'); ?>
            </div>

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
                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $price_group->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
                ?>
            </div>
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
                                if(($this->Settings->enable_grp==1)){}else{
                                    foreach ($mainCompanies as $mainCompany) {
                                        $co[$mainCompany->id] = $mainCompany->name;
                                    }
                                }

                echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $price_group->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
                ?>
            </div>
            <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                            <div class="form-group">
                                <?= lang('Main_branch', 'Main_branch') ?>
                                <?php
                                $br[''] = lang('all');
                                if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
                                    foreach ($mainBranches as $mainBranche) {
                                        $br[$mainBranche->id] = $mainBranche->name;
                                    }
                                }
                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $price_group->brId), 'class="form-control select" id="brId"  required="required" style="width:100%"')
                    ?>
                </div>
                <?php } } ?>

                <div class="form-group">
                <?= lang('warehouse', 'warehouse'); ?>
                <?php
                $wh[''] = lang('all_warehouses');
                foreach ($warehouses as $warehouse) {
                    $wh[$warehouse->id] = $warehouse->name;
                }
                echo form_dropdown('warehouse', $wh, set_value('warehouse', $price_group->warehouse_id), 'id="warehouse" class="form-control input-tip select" style="width:100%;" ');
                ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_price_group', lang('edit_price_group'), 'class="btn btn-primary"'); ?>
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
       $('#warehouse').select2('readonly', true);

    }
    );

</script>