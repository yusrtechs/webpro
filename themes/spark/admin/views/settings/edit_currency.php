<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('edit_currency'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('system_settings/edit_currency/' . $id, $attrib); ?>
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
                                        echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $currency->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
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
                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $currency->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
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
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $currency->brId), ' id="brId" class="form-control select"  required="required" style="width:100%"')
                                            ?>
                                        </div>
                                        <?php }
                                        } ?>

            <div class="form-group">
                <?= lang('currency_code', 'code'); ?>
                <?= form_input('code', set_value('code', $currency->code), 'readonly class="form-control tip" id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('currency_name', 'name'); ?>
                <?= form_input('name', set_value('name', $currency->name), 'class="form-control tip" id="name" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('Fraction_name', 'fraction_name'); ?>
                <?= form_input('fraction_name', set_value('fraction_name', $currency->fraction_name), 'class="form-control tip" id="fraction_name"'); ?>
            </div>

            <div class="form-group">
                <?= lang('currency_symbol', 'symbol'); ?>
                <?= form_input('symbol', $currency->symbol, 'class="form-control tip" id="symbol" '); ?>
            </div>

            <div class="form-group">
                <?= lang('Fraction_symbol', 'fraction_symbol'); ?>
                <?= form_input('fraction_symbol', $currency->fraction_symbol, 'class="form-control tip" id="fraction_symbol" '); ?>
            </div>

            <div class="form-group">
                <?= lang('exchange_rate', 'rate'); ?>
                <?= form_input('rate', set_value('rate', $currency->rate), 'class="form-control tip" id="rate"  required="required"'); ?>
            </div>

            <div class="form-group">
                <input type="checkbox" value="1" name="auto_update" id="auto_update" <?= $currency->auto_update ? 'checked="checked"' : ''; ?>>
                <label class="padding-left-10" for="auto_update"><?= lang('auto_update_rate'); ?></label>
            </div>
        </div>
        <div class="modal-footer">
            <?= form_submit('edit_currency', lang('edit_currency'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?= form_close(); ?>
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
