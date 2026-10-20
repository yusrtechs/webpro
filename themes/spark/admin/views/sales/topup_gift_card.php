<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal-dialog modal-md" style="max-width: 540px;">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: 1px solid var(--spark-border-default, #e2e8f0); box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
        
        <!-- Emerald Modal Header -->
        <div class="modal-header" style="background: var(--spark-surface-ground, #f8fafc) !important; color: #ffffff; padding: 18px 24px; border: none; display: flex; align-items: center; justify-content: space-between;">
            <h4 class="modal-title" id="myModalLabel" style="font-weight: 800; font-size: 17px; display: flex; align-items: center; gap: 8px; margin: 0;">
                <i class="fa fa-dollar"></i> <?= lang('topup_gift_card') . ' (' . $card->card_no . ')'; ?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #ffffff; opacity: 0.9; font-size: 22px; line-height: 1;">&times;</button>
        </div>

        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('sales/topup_gift_card/' . $card->id, $attrib); ?>

        <div class="modal-body" style="padding: 24px; background: var(--spark-surface-ground, #f8fafc);">
            <div style="background: var(--spark-surface-card, #ffffff); border: 1px solid var(--spark-border-default, #e2e8f0); border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                
                <!-- Current Balance Display Banner -->
                <div class="spark-giftcard-balance-banner" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="font-size: 11px; font-weight: 700; color: #059669; text-transform: uppercase; margin: 0 0 2px 0;"><?= lang('current_balance'); ?></p>
                        <h3 style="font-size: 20px; font-weight: 800; color: #065f46; margin: 0;"><?= $this->sma->formatMoney($card->balance); ?> <small style="font-size: 12px; font-weight: 700; color: #059669;"></small></h3>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa fa-credit-card"></i>
                    </div>
                </div>

                <!-- Recharge Amount with Preset Chips -->
                <div class="form-group">
                    <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('amount', 'amount'); ?> </label>
                    <div class="spark-preset-chips" style="margin-bottom: 8px;">
                        <button type="button" class="spark-preset-chip" data-amount="100">100</button>
                        <button type="button" class="spark-preset-chip" data-amount="250">250</button>
                        <button type="button" class="spark-preset-chip is-active" data-amount="500">500</button>
                        <button type="button" class="spark-preset-chip" data-amount="1000">1,000</button>
                    </div>
                    <?php echo form_input('amount', '500', 'class="form-control" id="amount" required="required" placeholder="' . lang('or_custom_amount') . '" style="height: 42px; border-radius: 10px; font-weight: 700; font-size: 15px;"'); ?>
                </div>

                <!-- Expiry Date -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('expiry_date', 'expiry'); ?></label>
                    <?php echo form_input('expiry', $this->sma->hrsd(date('Y-m-d', strtotime('+2 year'))), 'class="form-control date" id="expiry" style="height: 42px; border-radius: 10px; font-weight: 600;"'); ?>
                </div>

            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer" style="padding: 14px 24px; background: var(--spark-surface-card, #ffffff); border-top: 1px solid var(--spark-border-default, #e2e8f0); display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 12px; font-weight: 700; padding: 9px 18px;">
                <?= lang('close'); ?>
            </button>
            <?php echo form_submit('topup', lang('topup_gift_card'), 'class="spark-btn-giftcard-primary" style="padding: 9px 20px;"'); ?>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<?= $modal_js ?>

<script type="text/javascript">
$(document).ready(function() {
    $('.spark-preset-chip').on('click', function() {
        $('.spark-preset-chip').removeClass('is-active');
        $(this).addClass('is-active');
        var amt = $(this).attr('data-amount');
        $('#amount').val(amt).trigger('input');
    });
});
</script>
