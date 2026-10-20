<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped " style="margin-bottom:0;">
                    <tbody>
                    <?php
					if($supplier->co_to_co=="1"){
						?>
                    <tr>
                        <td><strong><?= lang('company'); ?></strong></td>
                        <td><?= $supplier->company; ?></strong></td>
                    </tr>
					<?php
					}
					?>
                    <tr>
                        <td><strong><?= lang('name'); ?></strong></td>
                        <td><?= $supplier->name; ?></strong></td>
                    </tr>
					<?php
					$co_to_co="";
					if($supplier->co_to_co=="1")
						$co_to_co=lang('company');
					if($supplier->co_to_co=="0")
						$co_to_co=lang('person');
					?>
					<tr>
                        <td><strong><?= lang('type'); ?></strong></td>
                        <td><?= $co_to_co; ?></strong></td>
                    </tr>
                    <?php
					$co_type="";
					if($supplier->co_type=="1")
						$co_type=lang('local');
					if($supplier->co_type=="2")
						$co_type=lang('international');
					?>
					<tr>
                        <td><strong><?= lang('co_type'); ?></strong></td>
                        <td><?= $co_type; ?></strong></td>
                    </tr>
                    <?php
					if($supplier->co_to_co=="1"){
						?>
                    <tr>
                        <td><strong><?= lang('VAT_Regesteration_Number'); ?></strong></td>
                        <td><?= $supplier->vat_no; ?></strong></td>
                    </tr>
					<?php
					}
					?>
                    <tr>
                        <td><strong><?= lang('gst_no'); ?></strong></td>
                        <td><?= $supplier->gst_no; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('email'); ?></strong></td>
                        <td><?= $supplier->email; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('phone'); ?></strong></td>
                        <td><?= $supplier->phone; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('address'); ?></strong></td>
                        <td><?= $supplier->address; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('city'); ?></strong></td>
                        <td><?= $supplier->city; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('state'); ?></strong></td>
                        <td><?= $supplier->state; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('postal_code'); ?></strong></td>
                        <td><?= $supplier->postal_code; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('country'); ?></strong></td>
                        <td><?= $supplier->country; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('ID_Number'); ?></strong></td>
                        <td><?= $supplier->cf3; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Attorney_No'); ?></strong></td>
                        <td><?= $supplier->cf4; ?></strong></td>
                    </tr>
                    <tr>
					
                        <td><strong>
							<?php
                    if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					?>
						<?= lang('BANK_NAME'); ?>
                    <?php
                    }else{
					?>
						<?= lang('scf1'); ?>
                    <?php
                    }
					?>
					</strong></td>
                        <td><?= $supplier->cf1; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>
						<?php
                    if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					?>
						<?= lang('IBAN'); ?>
                    <?php
                    }else{
					?>
						<?= lang('scf2'); ?>
                    <?php
                    }
					?>
					</strong></td>
                        <td><?= $supplier->cf2; ?></strong></td>
                    </tr>
                   
                    <tr>
                        <td><strong><?= lang('scf5'); ?></strong></td>
                        <td><?= $supplier->cf5; ?></strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
                <?php if ($Owner || $Admin || $GP['reports-suppliers']) {
    ?>
                    <a href="<?=admin_url('carsreports/supplier_report/' . $supplier->id); ?>" target="_blank" class="btn btn-primary"><?= lang('Car_Owner_report'); ?></a>
                <?php
} ?>
                <?php if ($Owner || $Admin || $GP['suppliers-edit']) {
        ?>
                    <a href="<?=admin_url('carssuppliers/edit/' . $supplier->id); ?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('Edit_car_owner'); ?></a>
                <?php
    } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
