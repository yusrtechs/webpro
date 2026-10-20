<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>

<div class="modal-dialog modal-lg" style="width: 95%; max-width: 1100px;">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: 1px solid var(--spark-border-default, #e2e8f0); box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
        <!-- Emerald Modal Header -->
        <div class="modal-header" style="background: var(--spark-surface-ground, #f8fafc) !important; color: #ffffff; padding: 18px 24px; border: none; display: flex; align-items: center; justify-content: space-between;">
            <h4 class="modal-title" id="myModalLabel" style="font-weight: 800; font-size: 18px; display: flex; align-items: center; gap: 10px; margin: 0;">
                <i class="fa fa-edit"></i> <?= lang('edit_gift_card'); ?> (<?= $gift_card->card_no; ?>)
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #ffffff; opacity: 0.9; font-size: 24px; line-height: 1; margin: 0;">&times;</button>
        </div>

        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'edit-gift-card-form'];
        echo admin_form_open('sales/edit_gift_card/' . $gift_card->id, $attrib); ?>

        <div class="modal-body" style="padding: 24px; background: var(--spark-surface-ground, #f8fafc);">
            <div class="row">
                <!-- LEFT COLUMN: Live 3D Interactive Card Preview & Template Selector -->
                <div class="col-md-5">
                    <div style="background: var(--spark-surface-card, #ffffff); border: 1px solid var(--spark-border-default, #e2e8f0); border-radius: 16px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); position: sticky; top: 10px;">
                        
                        <!-- Preview Header with Flip Button -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <span style="font-size: 14px; font-weight: 800; color: var(--spark-text-primary, #0f172a); display: flex; align-items: center; gap: 6px;">
                                <i class="fa fa-eye text-success"></i> <?= lang('live_preview'); ?>
                            </span>
                            <button type="button" id="btn-flip-card" class="btn btn-xs" style="border-radius: 8px; font-weight: 700; padding: 5px 12px; background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); cursor: pointer;">
                                <i class="fa fa-refresh"></i> <span id="flip-label"><?= lang('view_back_face'); ?></span>
                            </button>
                        </div>

                        <!-- 3D Card Flipper Container -->
                        <div class="spark-card-preview-container">
                            <?php
                            $gc_type = !empty($gift_card->gift_card_type) && in_array($gift_card->gift_card_type, ['classic', 'golden', 'elegant', 'modern']) ? $gift_card->gift_card_type : 'classic';
                            $gc_theme = ($gc_type == 'golden') ? 'theme-gold' : (($gc_type == 'elegant') ? 'theme-emerald' : (($gc_type == 'modern') ? 'theme-modern' : 'theme-classic'));
                            ?>
                            <div class="spark-credit-card-flipper <?= $gc_theme; ?>" id="card-flipper" title="<?= lang('click_card_to_flip'); ?>">
                                <!-- FRONT FACE -->
                                <div class="spark-card-face spark-card-front">
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

                                    <!-- EMV Chip -->
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
                                        <p class="spark-card-number" id="preview_card_no"><?= wordwrap($gift_card->card_no, 4, '-', true); ?></p>
                                        <p id="preview_card_customer" style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9); margin: 0 0 6px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= lang('customer_name'); ?></p>
                                    </div>

                                    <!-- Bottom Row (Balance + Expiry) -->
                                    <div class="spark-card-bottom-row">
                                        <div>
                                            <p class="spark-card-balance-lbl"><?= lang('balance'); ?></p>
                                            <div style="display: flex; align-items: baseline;">
                                                <span class="spark-card-balance-val" id="preview_card_val"><?= $this->sma->formatMoney($gift_card->value); ?></span>
                                                <span class="spark-card-currency"></span>
                                            </div>
                                        </div>
                                        <div style="text-align: left;">
                                            <p class="spark-card-expiry-lbl"><?= lang('valid_thru'); ?></p>
                                            <p class="spark-card-expiry-val" id="preview_card_expiry"><?= $gift_card->expiry ? $this->sma->hrsd($gift_card->expiry) : '--/--/----'; ?></p>
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
                                                <span id="preview_card_cvv"><?= substr(md5($gift_card->card_no), 0, 3); ?></span>
                                            </div>
                                        </div>

                                        <!-- Barcode Container -->
                                        <div class="spark-card-barcode-box">
                                            <div class="spark-card-barcode-bars" id="preview_barcode_bars">
                                                <?php
                                                $bar_widths = [1, 2, 1, 3, 1, 2, 1, 2, 3, 1, 2, 1, 1, 3, 2, 1, 2, 1, 3, 1, 2];
                                                foreach ($bar_widths as $idx => $bw) {
                                                    $bh = 20 + ($idx % 3) * 4;
                                                    echo '<div class="spark-card-barcode-bar" style="width: ' . $bw . 'px; height: ' . $bh . 'px;"></div>';
                                                }
                                                ?>
                                            </div>
                                            <p class="spark-card-barcode-num" id="preview_barcode_num"><?= $gift_card->card_no; ?></p>
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

                        <p style="font-size: 11px; color: var(--spark-text-muted, #64748b); text-align: center; margin: 0 0 14px 0;">
                            * <?= lang('click_card_to_flip'); ?>
                        </p>

                        <!-- 4-Card Template Selector -->
                        <div>
                            <label style="font-size: 12px; font-weight: 700; color: var(--spark-text-secondary, #475569); margin-bottom: 6px; display: block;">
                                <i class="fa fa-paint-brush"></i> <?= lang('card_design_template'); ?>
                            </label>
                            <div class="spark-template-grid">
                                <div class="spark-template-option opt-classic <?= ($gc_type == 'classic') ? 'is-active' : ''; ?>" data-theme="theme-classic" data-type="classic">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #0f3460; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('classic'); ?></span>
                                    </div>
                                    <i class="fa <?= ($gc_type == 'classic') ? 'fa-check-circle' : 'fa-circle-o'; ?> theme-check"></i>
                                </div>
                                <div class="spark-template-option opt-gold <?= ($gc_type == 'golden') ? 'is-active' : ''; ?>" data-theme="theme-gold" data-type="golden">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #d4a853; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('gold'); ?></span>
                                    </div>
                                    <i class="fa <?= ($gc_type == 'golden') ? 'fa-check-circle' : 'fa-circle-o'; ?> theme-check"></i>
                                </div>
                                <div class="spark-template-option opt-emerald <?= ($gc_type == 'elegant') ? 'is-active' : ''; ?>" data-theme="theme-emerald" data-type="elegant">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #2d6a4f; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('elegant'); ?></span>
                                    </div>
                                    <i class="fa <?= ($gc_type == 'elegant') ? 'fa-check-circle' : 'fa-circle-o'; ?> theme-check"></i>
                                </div>
                                <div class="spark-template-option opt-modern <?= ($gc_type == 'modern') ? 'is-active' : ''; ?>" data-theme="theme-modern" data-type="modern">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #3d3d3d; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('modern'); ?></span>
                                    </div>
                                    <i class="fa <?= ($gc_type == 'modern') ? 'fa-check-circle' : 'fa-circle-o'; ?> theme-check"></i>
                                </div>
                            </div>
                            <input type="hidden" name="gift_card_type" id="gift_card_type" value="<?= $gc_type; ?>">
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN: Card Information Form Fields -->
                <div class="col-md-7">
                    <div style="background: var(--spark-surface-card, #ffffff); border: 1px solid var(--spark-border-default, #e2e8f0); border-radius: 16px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                        
                        <h4 style="font-size: 15px; font-weight: 800; color: var(--spark-text-primary, #0f172a); margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 13px;">
                                <i class="fa fa-edit"></i>
                            </div>
                            <?= lang('card_info'); ?>
                        </h4>

                        <!-- Customer Selection Input -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('customer', 'customer'); ?></label>
                            <?php echo form_input('customer', $gift_card->customer_id, 'class="form-control" id="customer" style="width:100%;"'); ?>
                        </div>

                        <!-- Amount Value Input -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('value', 'value'); ?> ()</label>
                            <div class="spark-preset-chips">
                                <button type="button" class="spark-preset-chip" data-amount="500">500</button>
                                <button type="button" class="spark-preset-chip" data-amount="1000">1,000</button>
                                <button type="button" class="spark-preset-chip" data-amount="2000">2,000</button>
                                <button type="button" class="spark-preset-chip" data-amount="5000">5,000</button>
                            </div>
                            <?php echo form_input('value', $this->sma->formatDecimal($gift_card->value), 'class="form-control" id="value" required="required" style="height: 42px; border-radius: 10px; font-weight: 700; font-size: 15px;"'); ?>
                        </div>

                        <!-- Card Number -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('card_no', 'card_no'); ?></label>
                            <div class="input-group">
                                <?php echo form_input('card_no', $gift_card->card_no, 'class="form-control" id="card_no" required="required" style="height: 42px; border-radius: 0 10px 10px 0; font-family: monospace; font-weight: 700; font-size: 15px;"'); ?>
                                <div class="input-group-addon" style="padding: 0 14px; background: #10b981; border-color: #10b981; border-radius: 10px 0 0 10px;">
                                    <a href="#" id="genNo" title="<?= lang('generate_random_no'); ?>" style="color: #ffffff; font-size: 15px;">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Expiry Date -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('expiry_date', 'expiry'); ?></label>
                            <?php echo form_input('expiry', $this->sma->hrsd($gift_card->expiry), 'class="form-control date" id="expiry" style="height: 42px; border-radius: 10px; font-weight: 600;"'); ?>
                        </div>

                        <!-- Status Option Dropdown -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('Status', 'Status'); ?></label>
                            <?php
                            $opt = [1 => lang('active'), 0 => lang('inactive')];
                            echo form_dropdown('status', $opt, $gift_card->status, 'id="status" required="required" class="form-control input-tip select" style="width:100%; height: 42px; border-radius: 10px;"');
                            ?>
                        </div>

                        <!-- Payment Selection (Free vs Paid) -->
                        <div class="form-group" style="padding-top: 10px; border-top: 1px solid var(--spark-border-default, #e2e8f0);">
                            <label style="font-size: 13px; font-weight: 700; margin-inline-end: 16px;">
                                <?php echo form_radio('paid', '0', $gift_card->paid == 0 ? '1' : '0', "id='paid_1'"); ?> <?= lang('Free_of_charge'); ?>
                            </label>
                            <label style="font-size: 13px; font-weight: 700;">
                                <?php echo form_radio('paid', '1', $gift_card->paid == 1 ? '1' : '0', "id='paid_2'"); ?> <?= lang('Paid'); ?>
                            </label>

                            <!-- Paid Amount & Gateway Sub-Form -->
                            <div class="well well-sm" id="div_paid_amount" style="<?= $gift_card->paid == 1 ? '' : 'display:none;'; ?> border-radius: 12px; margin-top: 10px; background: var(--spark-surface-subtle, #f8fafc); border: 1px solid var(--spark-border-default, #e2e8f0);">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('Amount', 'paid_amount'); ?>
                                            <?php echo form_input('paid_amount', $this->sma->formatDecimal($gift_card->paid_amount), 'class="form-control" id="paid_amount" style="height: 40px; border-radius: 8px;"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('paying_by', 'paid_by_1'); ?>
                                            <select name="paid_by" id="paid_by_1" class="form-control paid_by" style="height: 40px; border-radius: 8px;">
                                                <?= $Settings->accounting ? $this->sma->paid_opts_accounts(isset($payment->paid_by) ? $payment->paid_by : '', true) : $this->sma->paid_opts(isset($payment->paid_by) ? $payment->paid_by : '', true); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if ($Settings->accounting) { ?>
                                        <div class="col-md-6 pcash_1">
                                            <div class="form-group"><?= lang('Account', 'cash_account'); ?>
                                                <?php
                                                $acc = [];
                                                foreach ($payment_types as $account) {
                                                    if ($account->payment_type == 'cash')
                                                        $acc[$account->id] = $account->accountName;
                                                }
                                                echo form_dropdown('cash_account', $acc, '', 'class="form-control tip" id="cash_account" style="width:100%; height: 40px; border-radius: 8px;"');
                                                ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <!-- Tax Rate & Totals -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('VAT_rate', 'tax_rate'); ?>
                                            <?php
                                             $tr[''] = '';
                                            foreach ($tax_rates as $tax) {
                                                $tr[$tax->id] = $tax->name;
                                            }
                                            echo form_dropdown('tax_rate', $tr, $gift_card->tax_rate, 'id="tax_rate" class="form-control input-tip select" style="width:100%;"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('VAT_Amount', 'tax_amount'); ?>
                                            <?= form_input('tax_amount', $gift_card->tax_amount, 'class="form-control tip" id="tax_amount" readonly'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('grand_total', 'total_paid_amount'); ?>
                                            <?= form_input('total_paid_amount', $gift_card->total_paid_amount, 'class="form-control tip" id="total_paid_amount" readonly'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Field -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('note', 'note'); ?></label>
                            <?php echo form_textarea('note', $gift_card->note, 'class="form-control" id="note" rows="2" style="border-radius: 10px;" placeholder="' . lang('add_notes_optional') . '"'); ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Action Footer -->
        <div class="modal-footer" style="padding: 16px 24px; background: var(--spark-surface-card, #ffffff); border-top: 1px solid var(--spark-border-default, #e2e8f0); display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 12px; font-weight: 700; padding: 10px 20px;">
                <?= lang('close'); ?>
            </button>
            <?php echo form_submit('edit_gift_card', lang('edit_gift_card'), 'class="spark-btn-giftcard-primary" id="btn-submit-gift-card"'); ?>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<?= $modal_js ?>

<script type="text/javascript">
var tax_rates = <?php echo json_encode($tax_rates); ?>;

$(document).ready(function () {
    // ─── 1. Customer Select2 with Init Selection ───
    if ($.fn.select2) {
        $('#customer').val('<?= $gift_card->customer_id ?>').select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?= admin_url('customers/getCustomer') ?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        if (data != null) {
                            callback(data[0]);
                            if (data[0] && data[0].text) {
                                $('#preview_card_customer').text(data[0].text);
                            }
                        }
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    }

    $('#customer').on('change select2-selecting select2-close', function (e) {
        var selected_data = $(this).select2('data');
        var cust_name = (e.choice && e.choice.text) ? e.choice.text : (selected_data ? selected_data.text : '');
        if (cust_name && cust_name !== 'No Match Found') {
            $('#preview_card_customer').text(cust_name);
        }
    });

    // ─── 2. Formatting Helper for Card Number ───
    function formatCardNumber(num) {
        if (!num) return 'XXXX-XXXX-XXXX-XXXX';
        var clean = num.toString().replace(/[^0-9A-Za-z]/g, '');
        var parts = [];
        for (var i = 0; i < clean.length; i += 4) {
            parts.push(clean.substring(i, i + 4));
        }
        return parts.join('-') || clean;
    }

    // ─── 3. Real-Time Live Preview Synchronization ───
    function updateGiftCardPreview() {
        var cardVal = $('#value').val();
        var cardNo = $('#card_no').val();
        var expiryVal = $('#expiry').val();

        if (cardVal && !isNaN(cardVal)) {
            $('#preview_card_val').text(parseFloat(cardVal).toLocaleString());
        }

        if (cardNo) {
            var formatted = formatCardNumber(cardNo);
            $('#preview_card_no').text(formatted);
            $('#preview_barcode_num').text(cardNo);
        }

        if (expiryVal) {
            $('#preview_card_expiry').text(expiryVal);
        }
    }

    $('#value').on('input change keyup', updateGiftCardPreview);
    $('#card_no').on('input change keyup', updateGiftCardPreview);
    $('#expiry').on('change dp.change input', updateGiftCardPreview);

    // Preset Amount Chips
    $(document).on('click', '.spark-preset-chip', function(e) {
        e.preventDefault();
        $('.spark-preset-chip').removeClass('is-active');
        $(this).addClass('is-active');
        var amt = $(this).attr('data-amount');
        $('#value').val(amt).trigger('input').trigger('change');
    });

    // 4-Template Selection Switcher
    $(document).on('click', '.spark-template-option', function(e) {
        e.preventDefault();
        $('.spark-template-option').removeClass('is-active');
        $('.spark-template-option .theme-check').removeClass('fa-check-circle').addClass('fa-circle-o');
        
        $(this).addClass('is-active');
        $(this).find('.theme-check').removeClass('fa-circle-o').addClass('fa-check-circle');

        var themeClass = $(this).attr('data-theme');
        $('#card-flipper').removeClass('theme-classic theme-gold theme-emerald theme-modern').addClass(themeClass);

        var cardType = $(this).attr('data-type') || 'classic';
        $('#gift_card_type').val(cardType);
    });

    // 3D Card Flip Handler
    function toggleFlip() {
        var flipper = $('#card-flipper');
        flipper.toggleClass('is-flipped');
        if (flipper.hasClass('is-flipped')) {
            $('#flip-label').text('<?= lang('view_front_face'); ?>');
        } else {
            $('#flip-label').text('<?= lang('view_back_face'); ?>');
        }
    }

    $('#card-flipper').on('click', function(e) {
        toggleFlip();
    });
    $('#btn-flip-card').on('click', function(e) {
        e.stopPropagation();
        toggleFlip();
    });

    // Generator Button Handler
    $('#genNo').click(function (e) {
        e.preventDefault();
        var no = (typeof generateCardNo === 'function') ? generateCardNo() : (Math.floor(1000000000000000 + Math.random() * 9000000000000000) + '');
        $('#card_no').val(no).trigger('input').trigger('change');
        return false;
    });

    updateGiftCardPreview();

    // Payment method toggle
    $('input[name="paid"]').on('ifChanged change', function () {
        if ($('#paid_2').is(':checked')) {
            $('#div_paid_amount').slideDown();
        } else {
            $('#div_paid_amount').slideUp();
        }
    });

    $(document).on('change', '.paid_by', function () {
        var p_val = $(this).val();
        if (p_val == 'cash') {
            $('.pcheque_1, .ptransfer_1, .pcc_1, .gc').hide();
            $('.pcash_1').show();
        } else if (p_val == 'CC') {
            $('.pcheque_1, .ptransfer_1, .pcash_1, .gc').hide();
            $('.pcc_1').show();
        } else if (p_val == 'Cheque') {
            $('.pcc_1, .pcash_1, .ptransfer_1, .gc').hide();
            $('.pcheque_1').show();
        } else {
            $('.pcheque_1, .pcc_1, .pcash_1, .ptransfer_1, .gc').hide();
        }
    });
});
</script>