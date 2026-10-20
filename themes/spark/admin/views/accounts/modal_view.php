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
            <h4 class="modal-title" id="myModalLabel"><?= $account_details->accountID. ' - ' .$account_details->accountName. ' / ' .$account_details->accountNameEn; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped " style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td><strong><?= lang('Account_No'); ?></strong></td>
                        <td><?= $account_details->accountID; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Account_name'); ?></strong></td>
                        <td><?= $account_details->accountName; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('English_name'); ?></strong></td>
                        <td><?= $account_details->accountNameEn; ?></strong></td>
                    </tr>
                    <?php
					$account_type="";
					if($account_details->master_account=="0")
						$account_type=lang('Detailed_account');
					if($account_details->master_account=="1")
						$account_type=lang('Master_account');
					?>
					<tr>
                        <td rowspan="3"><strong><?= lang('Account_type'); ?></strong></td>
                        <td><?= $account_type; ?></strong></td>
                    </tr>
					<?php
					$account_type="";
					if($account_details->accountSide=="Debit")
						$account_type=lang('Debit');
					if($account_details->accountSide=="Credit")
						$account_type=lang('Credit');
					?>
					<tr>
                        <td><?= $account_type; ?></strong></td>
                    </tr>
                    <?php
					$account_type="";
					if($account_details->accountStatement=="Income Statement")
						$account_type=lang('Income_Statement');
					if($account_details->accountStatement=="Balance Sheet")
						$account_type=lang('Balance_Sheet');
					?>
					<tr>
                        <td><?= $account_type; ?></strong></td>
                    </tr>

                    <tr>
                        <td><strong><?= lang('Opening_balance'); ?></strong></td>
                        <td><?= $this->sma->formatMoney($account_details->openingBalance); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('balance'); ?></strong></td>
                        <td><?= $this->sma->formatMoney($account_details->accountBalance); ?></strong></td>
                    </tr>

					<?php if ($account_details->account_ref_id && $account_details->account_type=='warehouses') {
                    ?>

					<tr>
                    <?php
                            if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                                ?>
                           <td><strong><?= lang('Showroom'); ?></strong></td>


                                   <?php
                            }else{
                                ?>
                            <td><strong><?= lang('warehouse'); ?></strong></td>

                           <?php
                            }
                                ?>

                        <td><?= $warehouse->name; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id && $account_details->account_type=='sales') {
                    ?>

					<tr>
                        <td><strong><?= lang('warehouse'); ?></strong></td>
                        <td><?= $sales->name; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id &&  $account_details->account_type=='return_sales') {
                    ?>

					<tr>
                        <td><strong><?= lang('warehouse'); ?></strong></td>
                        <td><?= $return_sales->name; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id && $account_details->account_type=='local_suppliers') {
                    ?>

					<tr>
                        <td><strong><?= lang('supplier'); ?></strong></td>
                        <td><?= $local_suppliers->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id && $account_details->account_type=='international_suppliers') {
                    ?>

					<tr>
                        <td><strong><?= lang('supplier'); ?></strong></td>
                        <td><?= $international_suppliers->company; ?></strong></td>
                    </tr>
					<?php
					} ?>
                     <?php if ($account_details->account_ref_id && $account_details->account_type=='local_customers') {
                    ?>

					<tr>
                        <td><strong><?= lang('customer'); ?></strong></td>
                        <td><?= $local_customers->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id && $account_details->account_type=='international_customers') {
                    ?>

					<tr>
                        <td><strong><?= lang('customer'); ?></strong></td>
                        <td><?= $international_customers->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id && $account_details->account_type=='insurance') {
                    ?>

					<tr>
                        <td><strong><?= lang('customer'); ?></strong></td>
                        <td><?= $insurance->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id && $account_details->account_type=='advance') {
                    ?>

					<tr>
                        <td><strong><?= lang('customer'); ?></strong></td>
                        <td><?= $advance->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

					<?php if ($account_details->account_ref_id && $account_details->account_type=='billers') {
                    ?>

					<tr>
                        <td><strong><?= lang('biller'); ?></strong></td>
                        <td><?= $billers->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id2 && $account_details->account_type=='billers') {
                    ?>

					<tr>
                        <td><strong><?= lang('biller'); ?></strong></td>
                        <td><?= $this->site->getCompanyByID($account_details->account_ref_id2)->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id3 && $account_details->account_type=='billers') {
                    ?>

					<tr>
                        <td><strong><?= lang('biller'); ?></strong></td>
                        <td><?= $this->site->getCompanyByID($account_details->account_ref_id3)->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id4 && $account_details->account_type=='billers') {
                    ?>

					<tr>
                        <td><strong><?= lang('biller'); ?></strong></td>
                        <td><?= $this->site->getCompanyByID($account_details->account_ref_id4)->company; ?></strong></td>
                    </tr>
					<?php
					} ?>

                    <?php if ($account_details->account_ref_id5 && $account_details->account_type=='billers') {
                    ?>

					<tr>
                        <td><strong><?= lang('biller'); ?></strong></td>
                        <td><?= $this->site->getCompanyByID($account_details->account_ref_id5)->company; ?></strong></td>
                    </tr>
					<?php
					} ?>


					<?php if ($account_details->costCenterId) {
                    ?>

					<tr>
                        <td><center><strong><?= lang('Cost_center'); ?></strong></center></td>
                        <td><center><strong><?= lang('%') ?></strong></center></td>
                    </tr>

					<?php
						$entry = (array) $entry;
						$entry['costCenterId'] = json_decode($account_details->costCenterId);
						$entry['costCenterPercentage'] = json_decode($account_details->costCenterPercentage);

						$cost_index=0;
							foreach ($entry['costCenterId'] as $cost_center){
								echo '
							<tr>
							<td>';
								echo $this->accounts_model->getCostCentersByID($cost_center)->costName.'</td><td>';
								echo $entry['costCenterPercentage'][$cost_index];
								echo '</td></tr>';
								$cost_index++;


							}

					} ?>



                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>

                <?php if ($Owner || $Admin) {
                ?>
                    <a href="<?=admin_url('accounts/edit_account/' . $account_details->accountID); ?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('Edit_account'); ?></a>
					<?php
					} ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
