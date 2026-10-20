<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_unit'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('products/edit_unit/' . $unit->id, $attrib); ?>
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
                                        echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $unit->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
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
                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $unit->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
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
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $unit->brId), ' id="brId" class="form-control select"  required="required" style="width:100%"')
                                            ?>
                                        </div>
                                        <?php }
                                        } ?>

            <div class="form-group">
                <?= lang('unit_code', 'code'); ?>
                <?= form_input('code', set_value('code', $unit->code), 'class="form-control tip" id="code" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('unit_name', 'name'); ?>
                <?= form_input('name', set_value('name', $unit->name), 'class="form-control tip" id="name" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('base_unit', 'base_unit'); ?>
                <?php
                $opts[0] = lang('select') . ' ' . lang('unit');
                foreach ($base_units as $bu) {
                    $opts[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                }
                ?>
                <?= form_dropdown('base_unit', $opts, set_value('base_unit', $unit->base_unit), 'class="form-control tip" id="base_unit" style="width:100%;"'); ?>
            </div>
            <div id="measuring" style="display:none;">
                <div class="form-group">
                    <?= lang('operator', 'operator'); ?>
                    <?php
                    //$oopts = ['*' => lang('*'), '/' => lang('/'), '+' => lang('+'), '-' => lang('-')];
                    $oopts = ['*' => lang('*'), '/' => lang('/')];
                    ?>
                    <?= form_dropdown('operator', $oopts, set_value('operator', $unit->operator), 'class="form-control tip" id="operator" style="width:100%;"'); ?>
                </div>
                <div class="form-group">
                    <?= lang('operation_value', 'operation_value'); ?>
                    <?= form_input('operation_value', set_value('operation_value', $unit->operation_value), 'class="form-control tip" id="operation_value"'); ?>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_unit', lang('edit_unit'), 'class="btn btn-primary"'); ?>
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
        var obu = <?= !empty($unit->base_unit) ? $unit->base_unit : 0; ?>;
        if(obu > 0)
            $('#measuring').slideDown();
        else
            $('#measuring').slideUp();
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
