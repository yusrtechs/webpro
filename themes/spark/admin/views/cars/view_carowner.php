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
            <h4 class="modal-title" id="myModalLabel"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped " style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td><strong><?= lang('Company_name_in_Arabic'); ?></strong></td>
                        <td><?= $customer->company; ?></strong></td>
                    </tr>
					<tr>
                        <td><strong><?= lang('English_company_name'); ?></strong></td>
                        <td><?= $customer->companyEn; ?></strong></td>
                    </tr>
                    
                    <tr>
                        <td><strong><?= lang('name'); ?></strong></td>
                        <td><?= $customer->name; ?></strong></td>
                    </tr>
                    <?php
					$co_type="";
					if($customer->co_type=="1")
						$co_type=lang('local');
					if($customer->co_type=="2")
						$co_type=lang('international');
					?>
				<!--	<tr>
                        <td><strong><?= lang('co_type'); ?></strong></td>
                        <td><?= $co_type; ?></strong></td>
                    </tr> 
                    <tr>
                        <td><strong><?= lang('customer_group'); ?></strong></td>
                        <td><?= $customer->customer_group_name; ?></strong></td>
                    </tr>-->
                    <tr>
                        <td><strong><?= lang('VAT_Regesteration_Number'); ?></strong></td>
                        <td><?= $customer->vat_no; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('CR_Number'); ?></strong></td>
                        <td><?= $customer->cr_no; ?></strong></td>
                    </tr>
            <!--        <tr>
                        <td><strong><?= lang('deposit'); ?></strong></td>
                        <td><?= $this->sma->formatMoney($customer->deposit_amount); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('award_points'); ?></strong></td>
                        <td><?= $customer->award_points; ?></strong></td>
                    </tr> -->
                    <tr>
                        <td><strong><?= lang('email'); ?></strong></td>
                        <td><?= $customer->email; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('phone'); ?></strong></td>
                        <td><?= $customer->phone; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Address_in_Arabic'); ?></strong></td>
                        <td><?= $customer->address; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('English_address'); ?></strong></td>
                        <td><?= $customer->addressEn; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('city'); ?></strong></td>
                        <td><?= $customer->city; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('state'); ?></strong></td>
                        <td><?= $customer->state; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('postal_code'); ?></strong></td>
                        <td><?= $customer->postal_code; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('country'); ?></strong></td>
                        <td><?= $customer->country; ?></strong></td>
                    </tr>
              <!--      <tr>
                        <td><strong><?= lang('ccf1'); ?></strong></td>
                        <td><?= $customer->cf1; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('ccf2'); ?></strong></td>
                        <td><?= $customer->cf2; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('ccf3'); ?></strong></td>
                        <td><?= $customer->cf3; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('ccf4'); ?></strong></td>
                        <td><?= $customer->cf4; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('ccf5'); ?></strong></td>
                        <td><?= $customer->cf5; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('hide_in_pos'); ?></strong></td>
                        <td><?= $customer->hide_in_pos==1 ? lang('yes') : lang('no') ; ?></strong></td>
                    </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
                <!--<?php if ($Owner || $Admin || $GP['reports-customers']) {
    ?>
                    <a href="<?=admin_url('sales/customer_report/' . $customer->id); ?>" target="_blank" class="btn btn-primary"><?= lang('customers_report'); ?></a>
                <?php
} ?>
                <?php if ($Owner || $Admin || $GP['customers-edit']) {
        ?>
                    <a href="<?=admin_url('customers/edit/' . $customer->id); ?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('edit_customer'); ?></a>
                <?php
    } ?> -->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
