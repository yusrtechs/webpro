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
            <h4 class="modal-title" id="myModalLabel"><?= $payment_details->id. ' - ' .$payment_details->payment_name. ' / ' .$payment_details->payment_ename; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped " style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td><strong><?= lang('Payment_type_name'); ?></strong></td>
                        <td><?= $payment_details->payment_name; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('English_name'); ?></strong></td>
                        <td><?= $payment_details->payment_ename; ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Payment_method'); ?></strong></td>
                        <td><?= lang($payment_details->payment_type); ?>
						<?php
						if($payment_details->payment_type == 'CC') echo lang($paymentType[0]->code);
						?>
						</strong></td>
                    </tr>



                    <tr>
                        <td><strong><?= lang('Commission_%'); ?></strong></td>
                        <td><?= ($payment_details->commisson_perc); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('Account'); ?></strong></td>

                        <td><?= ($payment_details->accounts_name); ?></strong></td>
                    </tr>

					<tr>
                        <td><strong><?= lang('Bank_commisson_account'); ?></strong></td>

                        <td><?= ($Commisson_payment_details->accounts_name); ?></strong></td>
                    </tr>

					<tr>
                        <td><strong><?= lang('Commission_Tax'); ?></strong></td>
						<?php
						$tax_rate='';
						foreach ($tax_rates as $account) {
							if($payment_details->tax_rate==$account->id)
								$tax_rate=$account->name;
						}
						?>

                        <td><?= $tax_rate; ?></strong></td>
                    </tr>

					<tr>
                        <td><strong><?= lang('biller'); ?></strong></td>
						<?php
						$casher_id='';
						foreach ($billers as $account) {
							if($payment_details->casher_id==$account->id)
								$casher_id=$account->company;
						}
						?>

                        <td><?= $casher_id; ?></strong></td>
                    </tr>

					<tr>
                        <td><strong><?= lang('description'); ?></strong></td>

                        <td><?= ($payment_details->description); ?></strong></td>
                    </tr>


                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>

                <?php if ($Owner || $Admin) {
                ?>
                    <a href="<?=admin_url('accounts/edit_payment_type/' . $payment_details->id); ?>" data-toggle="modal" data-target="#myModal2" class="btn btn-primary"><?= lang('Edit_payment_type'); ?></a>
					<?php
					} ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
