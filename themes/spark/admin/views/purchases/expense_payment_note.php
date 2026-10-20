<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body print">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <?php if ($logo) {
    ?>
                <!--<div class="text-center" style="margin-bottom:20px;">-->
                <img src="<?= base_url('assets/uploads/logos/' . $Settings->logo) ?>"
                     alt="<?= $Settings->site_name ?>">
                <!--</div>-->
            <?php
} ?>

			<div class="clearfix"></div>
            <div class="row" style="padding-top:30px;">
                <div class="col-sm-12">
                    <p style="font-weight:bold;"><?= lang('warehouse'); ?>: <?= $Settings->site_name; ?></p>
                    <p style="font-weight:bold;"><?= lang('date'); ?>: <?= $this->sma->hrld($payment->date); ?></p>
                    <p style="font-weight:bold;"><?= lang('purchase') . ' ' . lang('ref'); ?>: <?= $inv->reference_no; ?></p>
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
                            <strong><?= $payment->type == 'returned' ? lang('payment_returned') : lang('payment_sent'); ?></strong>
                        </td>
                        <td class="text-right"><strong
                                class="text-right"><?php echo $this->sma->formatMoney($payment->amount); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('paid_by'); ?></strong></td>
                        <td class="text-right"><strong class="text-right"><?php echo lang($payment->paid_by);
                                if ($payment->paid_by == 'gift_card' || $payment->paid_by == 'CC') {
                                    echo ' : '.lang($payment->cc_type).' (' . $payment->cc_no . ')';
                                } elseif ($payment->paid_by == 'Cheque') {
                                    echo ' (' . $payment->cheque_no . ')';
                                }
                                ?></strong></td>
                    </tr>
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