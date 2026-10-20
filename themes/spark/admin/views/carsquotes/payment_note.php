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
					
				    <span valign="top"><h2  style=" font-size: 20px;">Advance Payment Note / ملاحظات دفع العربون</h2><span>
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
			
            <div class="row padding10">
                <div class="col-xs-6 from">
                    <b><?php echo $this->lang->line('from'); ?>: <?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></b><br/>
                    <!--
					<h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>
                    
					<?= $warehouse->name ?>-->

                    <?php
					$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ': ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->cr_no ? lang('CR_Number') . ': ' . $warehouse->cr_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ': ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?>
                </div>
				
				
				<div class="col-xs-6 to">
                   <b> <?php echo $this->lang->line('to'); ?>:<?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></b><br/>
                    <!--<h2 style="margin-top:10px;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>-->
                    <?= $customer->company                              && $customer->company != '-' ? '' : 'Attn: ' . $customer->name ?>

                    <?php
                    echo $customer->address . '<br>' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . ' ' . $customer->country . '<br>';

                    /*echo '<p>';

                    if ($customer->vat_no != '-' && $customer->vat_no != '') {
                        echo '<br>' . lang('vat_no') . ': ' . $customer->vat_no;
                    }
                    if ($customer->gst_no != '-' && $customer->gst_no != '') {
                        echo '<br>' . lang('gst_no') . ': ' . $customer->gst_no;
                    }
                    if ($customer->cf1 != '-' && $customer->cf1 != '') {
                        echo '<br>' . lang('ccf1') . ': ' . $customer->cf1;
                    }
                    if ($customer->cf2 != '-' && $customer->cf2 != '') {
                        echo '<br>' . lang('ccf2') . ': ' . $customer->cf2;
                    }
                    if ($customer->cf3 != '-' && $customer->cf3 != '') {
                        echo '<br>' . lang('ccf3') . ': ' . $customer->cf3;
                    }
                    if ($customer->cf4 != '-' && $customer->cf4 != '') {
                        echo '<br>' . lang('ccf4') . ': ' . $customer->cf4;
                    }
                    if ($customer->cf5 != '-' && $customer->cf5 != '') {
                        echo '<br>' . lang('ccf5') . ': ' . $customer->cf5;
                    }
                    if ($customer->cf6 != '-' && $customer->cf6 != '') {
                        echo '<br>' . lang('ccf6') . ': ' . $customer->cf6;
                    }

                    echo '</p>';*/
                    echo ($customer->vat_no ? lang('VAT_Regesteration_Number') . ': ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ? lang('CR_Number') . ': ' . $customer->cr_no . '<br>' : '<br>') . lang('tel') . ': ' . $customer->phone . '<br>' . lang('email') . ': ' . $customer->email;
                    ?>
                </div>


            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <p style="font-weight:bold;"><?= lang('date'); ?>: <?= $this->sma->hrsd($payment->date); ?></p>
                    <p style="font-weight:bold;"><?= lang('reference_no') . ' ' . lang('For_reserve'); ?>: <?= $inv->reference_no; ?></p>
                    <p style="font-weight:bold;"><?= lang('payment_reference_no'); ?>: <?= $payment->reference_no; ?></p>
                </div>
            </div>
            <div class="well">
                <table class="table table-borderless" style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td>
                            <strong><?= $payment->type == 'returned' ? lang('payment_returned') : lang('payment_received'); ?></strong>
                        </td>
                        <td class="text-right">
                            <strong class="text-right"><?php echo $this->sma->formatMoney($payment->amount,$payment->currency); ?></strong>
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
                        <td>
                            <strong><?= lang('Advance_payment_status'); ?></strong>
                        </td>
                        <td class="text-right">
                            <strong class="text-right"><?= $payment->retrieved ? lang('Retrieved') : lang('Non_retrieved') ?></strong>
                        </td>
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

                    <p style="border-bottom: 1px solid #666;">&nbsp;</p>

                    <p><?= lang('stamp_sign'); ?></p>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
