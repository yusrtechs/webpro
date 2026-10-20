<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    @media print {
        #myModal .modal-content {
            display: none !important;
        }
    }
</style>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body print">
            <div class="row bold">
                    <div class="col-xs-12 no-print" >

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
			</div>
           </div>

			<div class="col-xs-6 pull-left" style=" margin-bottom:50px;text-align:left;">

				    <span valign="top"><h2  style=" font-size: 20px;">Payment Note / ملاحظات عمليه الدفع </h2><span>
					</div>
					<div style="margin-top:10px;float:right;">
            <?php
			//if ($logo) {
    ?>
                <!--<div class="text-center" style="margin-bottom:20px;">-->
                <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                     alt="<?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?>">
                <!--</div>-->
            <?php
//} ?>
			</div>
			<div class="clearfix"></div>
            <div class="row" style="padding-top:30px;">
                <div class="col-sm-12">
                    <p style="font-weight:bold;"><?= lang('date'); ?>: <?= $this->sma->hrld($payment->date); ?></p>
                    <p style="font-weight:bold;"><?= lang('reference_no') . ' ' . lang('Increasing_the_fixed_asset'); ?>: <?= $increasing->reference_no; ?></p>
                    <p style="font-weight:bold;"><?= lang('reference_no'); ?>: <?= $payment->reference_no; ?></p>
                </div>
                <p>&nbsp;</p>

                <div style="clear: both;"></div>
            </div>
            <div class="well">
                <table class="table table-borderless" style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td>
                            <strong><?= $payment->type == 'returned' ? lang('payment_returned') : lang('payment_received'); ?></strong>
                        </td>
                        <td class="text-right"><strong
                                class="text-right"><?php echo $this->sma->formatMoney($payment->amount); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('paid_by'); ?></strong></td>
                        <td class="text-right"><strong class="text-right"><?php echo lang($payment->paid_by);
                                if ($payment->paid_by == 'gift_card' || $payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe' || $payment->paid_by == 'authorize') {
                                 if ($payment->cc_no)
									   echo ' (' . substr($payment->cc_no, -4) . ')';
                                } elseif ($payment->paid_by == 'Cheque') {
                                    echo ' (' . $payment->cheque_no . ')';
                                }
                                ?></strong></td>
                    </tr>
                    <?php if ($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe' || $payment->paid_by == 'authorize') {
                                    ?>
                    <tr>
                        <td>
                            <strong><?= lang('name'); ?></strong>
                        </td>
                        <td class="text-right">
                            <strong class="text-right"><?= $payment->cc_type; ?></strong>
                        </td>
                    </tr>
                    <?php
                                } ?>
                    <?php if ($payment->paid_by == 'ppp' || $payment->paid_by == 'stripe' || $payment->paid_by == 'authorize') {
                                    ?>
                    <tr>
                        <td>
                            <strong><?= lang('transaction_id'); ?></strong>
                        </td>
                        <td class="text-right">
                            <strong class="text-right"><?= $payment->transaction_id; ?></strong>
                        </td>
                    </tr>
                    <?php
                                } ?>
                    <tr>
                        <td colspan="2"><?= html_entity_decode($payment->note); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div style="clear: both;"></div>
            <div class="row">
                <div class="col-sm-4 pull-left">
                    <p>&nbsp;</p>

                    <p>&nbsp;</p>

                    <p>&nbsp;</p>

                    <p style="border-bottom: 1px solid #666;">&nbsp;</p>

                    <p><?= lang('stamp_sign'); ?></p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>