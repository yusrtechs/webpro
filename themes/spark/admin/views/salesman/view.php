<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $salesman->company && $salesman->company != '-' ? $salesman->company : $salesman->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped " style="margin-bottom:0;">
                    <tbody>
                    <?php
                if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
                 ?>

                    <tr>
                        <td><strong><?= lang('Main_group'); ?></strong></td>
                        <td><?= $this->site->getMainGroupsByID($salesman->grpId)->name; ?></strong></td>
                    </tr>

                    <tr>
                        <td><strong><?= lang('Main_company'); ?></strong></td>
                        <td><?= $this->site->getMainCompaniesByID($salesman->coId)->name; ?></strong></td>
                    </tr>

                    <tr>
                        <td><strong><?= lang('Main_branch'); ?></strong></td>

                        <td><?= $this->site->getMainBranchesByID($salesman->brId)->name; ?></strong></td>

                  </tr>

                  <?php } ?>





                    <tr>
                        <td><strong><?= lang('name'); ?></strong></td>
                        <td><?= $salesman->name; ?></strong></td>
                    </tr>

                    <?php
					$Commission_calculation="";
					if($salesman->cf5=="1")
						$Commission_calculation=lang('sales');
					if($salesman->cf5=="2")
						$Commission_calculation=lang('profit');
					?>
					<tr>
                        <td><strong><?= lang('Commission_calculation'); ?></strong></td>
                        <td><?= $Commission_calculation; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Commission_%'); ?></strong></td>
                        <td><?= $salesman->cf6; ?></strong></td>
                    </tr>

                    <tr>
                        <td><strong><?= lang('email'); ?></strong></td>
                        <td><?= $salesman->email; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('phone'); ?></strong></td>
                        <td><?= $salesman->phone; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Address_in_Arabic'); ?></strong></td>
                        <td><?= $salesman->address; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('English_address'); ?></strong></td>
                        <td><?= $salesman->addressEn; ?></strong></td>
                    </tr>

                    <tr>
                        <td><strong><?= lang('city'); ?></strong></td>
                        <td><?= $salesman->city; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('state'); ?></strong></td>
                        <td><?= $salesman->state; ?></strong></td>
                    </tr>

                    <tr>
                        <td><strong><?= lang('country'); ?></strong></td>
                        <td><?= $salesman->country; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('ID_Number'); ?></strong></td>
                        <td><?= $salesman->cf3; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Nationality'); ?></strong></td>
                        <td><?= $salesman->cf4; ?></strong></td>
                    </tr>

                    <tr>
                        <td><strong><?= lang('cf1'); ?></strong></td>
                        <td><?= $salesman->cf1; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('cf2'); ?></strong></td>
                        <td><?= $salesman->cf2; ?></strong></td>
                    </tr>


                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
                <?php if ($Owner || $Admin || $GP['reports-salesman']) {

    ?>

                    <a style="display:none;" href="<?=admin_url('sales/salesman_report/' . $salesman->id); ?>" target="_blank" class="btn btn-primary"><?= lang('salesman_report'); ?></a>

                <?php

} ?>
                <?php if ($Owner || $Admin || $GP['salesman-edit']) {
        ?>
                    <a href="<?=admin_url('salesman/edit/' . $salesman->id); ?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('edit_salesman'); ?></a>
                <?php
    } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
