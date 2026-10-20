<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal-dialog modal-md" style="max-width: 600px;">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: 1px solid var(--spark-border-default, #e2e8f0); box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
        
        <!-- Emerald Modal Header -->
        <div class="modal-header no-print" style="background: var(--spark-surface-ground, #f8fafc) !important; color: #ffffff; padding: 18px 24px; border: none; display: flex; align-items: center; justify-content: space-between;">
            <h4 class="modal-title" id="myModalLabel" style="font-weight: 800; font-size: 17px; display: flex; align-items: center; gap: 8px; margin: 0;">
                <i class="fa fa-gift"></i> <?= lang('view_gift_card'); ?> (<?= $gift_card->card_no; ?>)
            </h4>
            <div class="flex items-center gap-2" style="display: flex; gap: 8px; align-items: center;">
                <button type="button" class="btn btn-xs btn-default no-print" style="background: rgba(255,255,255,0.2); color: #ffffff; font-weight: 700; border: none; border-radius: 8px; padding: 5px 12px; cursor: pointer;" onclick="window.print();">
                    <i class="fa fa-print"></i> <?= lang('print'); ?>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #ffffff; opacity: 0.9; font-size: 22px; line-height: 1; margin: 0;">&times;</button>
            </div>
        </div>

        <div class="modal-body" style="padding: 24px; background: var(--spark-surface-ground, #f8fafc);">
            
            <!-- Status Badge Alerts -->
            <?php if ($gift_card->expiry && $gift_card->expiry < date('Y-m-d')) { ?>
                <div class="alert alert-danger no-print" style="border-radius: 12px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-times-circle"></i> <?= lang('card_expired'); ?>
                </div>
            <?php } elseif ($gift_card->balance > 0) { ?>
                <div class="alert alert-success no-print" style="border-radius: 12px; font-weight: 700; margin-bottom: 20px; background: rgba(16, 185, 129, 0.12); color: #065f46; border: 1px solid rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: space-between;">
                    <span><i class="fa fa-check-circle"></i> <?= lang('balance'); ?>: <?= $Settings->default_currency . ' ' . $this->sma->formatMoney($gift_card->balance); ?></span>
                    <span class="label label-success" style="border-radius: 6px; font-weight: 700;"><?= lang('active'); ?></span>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning no-print" style="border-radius: 12px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-exclamation-triangle"></i> <?= lang('card_is_used'); ?>
                </div>
            <?php } ?>

            <?php
            $formatted_card_no = wordwrap($gift_card->card_no, 4, '-', true);
            $cust_name = $customer ? ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) : lang('customer_name');
            ?>

            <!-- Interactive 3D Card Container (Matching Add & Edit Views) -->
            <div class="no-print" style="background: var(--spark-surface-card, #ffffff); border: 1px solid var(--spark-border-default, #e2e8f0); border-radius: 16px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); margin-bottom: 20px;">
                
                <!-- Flip Header with Button -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="font-size: 14px; font-weight: 800; color: var(--spark-text-primary, #0f172a); display: flex; align-items: center; gap: 6px;">
                        <i class="fa fa-credit-card text-success"></i> <?= lang('gift_card'); ?>
                    </span>
                    <button type="button" id="btn-view-flip" class="btn btn-xs" style="border-radius: 8px; font-weight: 700; padding: 5px 12px; background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); cursor: pointer;">
                        <i class="fa fa-refresh"></i> <span id="view-flip-label"><?= lang('view_back_face'); ?></span>
                    </button>
                </div>

                <!-- 3D Interactive Card Component -->
                <div class="spark-card-preview-container">
                    <?php
                    $gc_type = !empty($gift_card->gift_card_type) && in_array($gift_card->gift_card_type, ['classic', 'golden', 'elegant', 'modern']) ? $gift_card->gift_card_type : 'classic';
                    $gc_theme = ($gc_type == 'golden') ? 'theme-gold' : (($gc_type == 'elegant') ? 'theme-emerald' : (($gc_type == 'modern') ? 'theme-modern' : 'theme-classic'));
                    ?>
                    <div class="spark-credit-card-flipper <?= $gc_theme; ?>" id="view-card-flipper" title="<?= lang('click_card_to_flip'); ?>">
                        
                        <!-- FRONT FACE -->
                        <div class="spark-card-face spark-card-front">
                            <!-- Top Row -->
                            <div class="spark-card-top-row">
                                <div class="spark-card-store-brand">
                                    <div class="spark-card-icon-badge">
                                        <i class="fa fa-gift"></i>
                                    </div>
                                    <div>
                                        <p class="spark-card-title-tag"><?= lang('gift_card'); ?></p>
                                        <h4 class="spark-card-store-name"><?= $Settings->site_name ?: 'شموخ جرير'; ?></h4>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 3px;">
                                    <div style="width: 18px; height: 12px; background: var(--accent-color, #e2b04a); border-radius: 2px; opacity: 0.8;"></div>
                                    <div style="width: 18px; height: 12px; background: var(--accent-color, #e2b04a); border-radius: 2px; opacity: 0.4;"></div>
                                </div>
                            </div>

                            <!-- Golden EMV Chip -->
                            <div style="display: flex; align-items: center; gap: 12px; margin: 8px 0;">
                                <div class="spark-card-chip">
                                    <div class="spark-card-chip-lines"></div>
                                </div>
                                <div style="display: flex; gap: 2px;">
                                    <div style="width: 2px; height: 16px; background: var(--accent-color, #e2b04a); opacity: 0.3; border-radius: 1px;"></div>
                                    <div style="width: 2px; height: 16px; background: var(--accent-color, #e2b04a); opacity: 0.3; border-radius: 1px;"></div>
                                    <div style="width: 2px; height: 16px; background: var(--accent-color, #e2b04a); opacity: 0.3; border-radius: 1px;"></div>
                                </div>
                            </div>

                            <!-- Card Number & Customer Name -->
                            <div>
                                <p class="spark-card-number"><?= $formatted_card_no; ?></p>
                                <p style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9); margin: 0 0 6px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $cust_name; ?></p>
                            </div>

                            <!-- Bottom Row (Balance + Expiry) -->
                            <div class="spark-card-bottom-row">
                                <div>
                                    <p class="spark-card-balance-lbl"><?= lang('balance'); ?></p>
                                    <div style="display: flex; align-items: baseline;">
                                        <span class="spark-card-balance-val"><?= $this->sma->formatMoney($gift_card->value); ?></span>
                                        <span class="spark-card-currency"></span>
                                    </div>
                                </div>
                                <div style="text-align: left;">
                                    <p class="spark-card-expiry-lbl"><?= lang('valid_thru'); ?></p>
                                    <p class="spark-card-expiry-val"><?= $gift_card->expiry ? $this->sma->hrsd($gift_card->expiry) : '--/--/----'; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- BACK FACE -->
                        <div class="spark-card-face spark-card-back">
                            <div class="spark-card-magstripe"></div>
                            <div class="spark-card-back-content">
                                <!-- Signature & CVV -->
                                <div class="spark-card-sig-cvv-row">
                                    <div class="spark-card-signature"><?= lang('cardholder_signature'); ?></div>
                                    <div class="spark-card-cvv">
                                        <span>CVV</span>
                                        <span><?= substr(md5($gift_card->card_no), 0, 3); ?></span>
                                    </div>
                                </div>

                                <!-- Real Code128 Barcode Container -->
                                <div class="spark-card-barcode-box">
                                    <div class="text-center" style="display: flex; justify-content: center; align-items: center;">
                                        <img src="<?= admin_url('misc/barcode/' . $gift_card->card_no . '/code128/40'); ?>" alt="<?= $gift_card->card_no; ?>" style="max-width: 90%; height: 30px;" />
                                    </div>
                                    <p class="spark-card-barcode-num"><?= $gift_card->card_no; ?></p>
                                </div>

                                <!-- Footer Info -->
                                <div class="spark-card-back-footer">
                                    <div>
                                        <p style="margin:0; font-weight:700;"><?= $Settings->site_name ?: 'شموخ جرير'; ?></p>
                                        <p style="margin:0; opacity:0.8; font-size:7.5px;"><?= lang('gift_card_non_refundable'); ?></p>
                                    </div>
                                    <div style="text-align:left;">
                                        <p style="margin:0; opacity:0.8; font-size:7.5px;"><?= lang('customer_service'); ?></p>
                                        <p style="margin:0; font-family:monospace; font-weight:700;"><?= $Settings->phone ?: '920-000-000'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <p style="font-size: 11px; color: var(--spark-text-muted, #64748b); text-align: center; margin: 0;">
                    * <?= lang('click_card_to_flip'); ?>
                </p>
            </div>

            <!-- DEDICATED PRINT CONTAINER (Visible during print only) -->
            <div class="spark-giftcard-print-target">
                <!-- Front Card Print -->
                <div class="spark-giftcard-print-card theme-classic">
                    <div class="spark-card-face spark-card-front" style="position: relative; height: 100%; border-radius: 18px; padding: 22px 24px; display: flex; flex-direction: column; justify-content: space-between; transform: none;">
                        <div class="spark-card-top-row">
                            <div class="spark-card-store-brand">
                                <div class="spark-card-icon-badge">
                                    <i class="fa fa-gift"></i>
                                </div>
                                <div>
                                    <p class="spark-card-title-tag"><?= lang('gift_card'); ?></p>
                                    <h4 class="spark-card-store-name"><?= $Settings->site_name ?: 'شموخ جرير'; ?></h4>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; margin: 8px 0;">
                            <div class="spark-card-chip"><div class="spark-card-chip-lines"></div></div>
                        </div>
                        <div>
                            <p class="spark-card-number"><?= $formatted_card_no; ?></p>
                            <p style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9); margin: 0;"><?= $cust_name; ?></p>
                        </div>
                        <div class="spark-card-bottom-row">
                            <div>
                                <p class="spark-card-balance-lbl"><?= lang('balance'); ?></p>
                                <span class="spark-card-balance-val"><?= $this->sma->formatMoney($gift_card->value); ?></span>
                                <span class="spark-card-currency"></span>
                            </div>
                            <div style="text-align: left;">
                                <p class="spark-card-expiry-lbl"><?= lang('valid_thru'); ?></p>
                                <p class="spark-card-expiry-val"><?= $gift_card->expiry ? $this->sma->hrsd($gift_card->expiry) : '--/--/----'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Card Print -->
                <div class="spark-giftcard-print-card theme-classic" style="margin-top: 20px;">
                    <div class="spark-card-face spark-card-back" style="position: relative; height: 100%; border-radius: 18px; transform: none; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;">
                        <div class="spark-card-magstripe" style="margin-top: 16px;"></div>
                        <div class="spark-card-back-content" style="padding: 10px 18px 14px 18px;">
                            <div class="spark-card-sig-cvv-row">
                                <div class="spark-card-signature"><?= lang('cardholder_signature'); ?></div>
                                <div class="spark-card-cvv">
                                    <span>CVV</span>
                                    <span><?= substr(md5($gift_card->card_no), 0, 3); ?></span>
                                </div>
                            </div>
                            <div class="spark-card-barcode-box" style="padding: 6px 12px;">
                                <div class="text-center">
                                    <img src="<?= admin_url('misc/barcode/' . $gift_card->card_no . '/code128/40'); ?>" alt="<?= $gift_card->card_no; ?>" style="max-width: 90%; height: 30px;" />
                                </div>
                                <p class="spark-card-barcode-num" style="font-size: 9.5px;"><?= $gift_card->card_no; ?></p>
                            </div>
                            <div class="spark-card-back-footer" style="font-size: 8px;">
                                <div>
                                    <p style="margin:0; font-weight:700;"><?= $Settings->site_name ?: 'شموخ جرير'; ?></p>
                                    <p style="margin:0; opacity:0.8;"><?= lang('gift_card_non_refundable'); ?></p>
                                </div>
                                <div style="text-align:left;">
                                    <p style="margin:0; opacity:0.8;"><?= lang('customer_service'); ?></p>
                                    <p style="margin:0; font-family:monospace; font-weight:700;"><?= $Settings->phone ?: '920-000-000'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Topups History Table -->
            <?php if ($topups) { ?>
                <div class="no-print" style="margin-top: 16px; background: var(--spark-surface-card, #ffffff); border: 1px solid var(--spark-border-default, #e2e8f0); border-radius: 14px; padding: 16px;">
                    <h5 style="font-size: 14px; font-weight: 800; color: var(--spark-text-primary, #0f172a); margin: 0 0 12px 0; display: flex; align-items: center; gap: 6px;">
                        <i class="fa fa-history text-primary"></i> <?= lang('last_topups'); ?>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed" style="margin: 0; font-size: 13px;">
                            <thead>
                                <tr style="background: var(--spark-surface-subtle, #f1f5f9);">
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('amount'); ?></th>
                                    <th><?= lang('created_by'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topups as $topup) { ?>
                                    <tr>
                                        <td><?= $this->sma->hrld($topup->date); ?></td>
                                        <td style="font-weight: 700; color: #10b981;"><?= $this->sma->formatMoney($topup->amount); ?></td>
                                        <td><?= $topup->first_name . ' ' . $topup->last_name; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>

        </div>

        <!-- Modal Footer -->
        <div class="modal-footer no-print" style="padding: 14px 24px; background: var(--spark-surface-card, #ffffff); border-top: 1px solid var(--spark-border-default, #e2e8f0); display: flex; align-items: center; justify-content: space-between;">
            <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 12px; font-weight: 700; padding: 8px 18px;">
                <?= lang('close'); ?>
            </button>
            <?php if ($gift_card->balance > 0 && (!$gift_card->expiry || $gift_card->expiry > date('Y-m-d'))) { ?>
                <button type="button" class="spark-btn-giftcard-primary" onClick="window.print();" style="padding: 8px 20px;">
                    <i class="fa fa-print"></i> <?= lang('print'); ?>
                </button>
            <?php } ?>
        </div>

    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    function toggleViewCardFlip() {
        var flipper = $('#view-card-flipper');
        flipper.toggleClass('is-flipped');
        if (flipper.hasClass('is-flipped')) {
            $('#view-flip-label').text('<?= lang('view_front_face'); ?>');
        } else {
            $('#view-flip-label').text('<?= lang('view_back_face'); ?>');
        }
    }

    $('#view-card-flipper').on('click', toggleViewCardFlip);
    $('#btn-view-flip').on('click', function (e) {
        e.stopPropagation();
        toggleViewCardFlip();
    });
});
</script>
