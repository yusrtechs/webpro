<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>

<div class="modal-dialog modal-lg" style="width: 95%; max-width: 1100px;">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: 1px solid var(--spark-border-default, #e2e8f0); box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
        <!-- Emerald Modal Header -->
        <div class="modal-header" style="background: var(--spark-surface-ground, #f8fafc) !important; color: #ffffff; padding: 18px 24px; border: none; display: flex; align-items: center; justify-content: space-between;">
            <h4 class="modal-title" id="myModalLabel" style="font-weight: 800; font-size: 18px; display: flex; align-items: center; gap: 10px; margin: 0;">
                <i class="fa fa-gift"></i> <?= lang('add_gift_card'); ?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #ffffff; opacity: 0.9; font-size: 24px; line-height: 1; margin: 0;">&times;</button>
        </div>

        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-gift-card-form'];
        echo admin_form_open('sales/add_gift_card', $attrib); ?>

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
                            <div class="spark-credit-card-flipper theme-classic" id="card-flipper" title="<?= lang('click_card_to_flip'); ?>">
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
                                        <p class="spark-card-number" id="preview_card_no">XXXX-XXXX-XXXX-XXXX</p>
                                        <p id="preview_card_customer" style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9); margin: 0 0 6px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= lang('customer_name'); ?></p>
                                    </div>

                                    <!-- Bottom Row (Balance + Expiry) -->
                                    <div class="spark-card-bottom-row">
                                        <div>
                                            <p class="spark-card-balance-lbl"><?= lang('balance'); ?></p>
                                            <div style="display: flex; align-items: baseline;">
                                                <span class="spark-card-balance-val" id="preview_card_val">2,000</span>
                                                <span class="spark-card-currency"><?= $Settings->default_currency; ?></span>
                                            </div>
                                        </div>
                                        <div style="text-align: left;">
                                            <p class="spark-card-expiry-lbl"><?= lang('valid_thru'); ?></p>
                                            <p class="spark-card-expiry-val" id="preview_card_expiry"><?= date('d/m/Y', strtotime('+2 year')); ?></p>
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
                                                <span id="preview_card_cvv">117</span>
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
                                            <p class="spark-card-barcode-num" id="preview_barcode_num">XXXX-XXXX-XXXX-XXXX</p>
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
                                <div class="spark-template-option opt-classic is-active" data-theme="theme-classic" data-type="classic">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #0f3460; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('classic'); ?></span>
                                    </div>
                                    <i class="fa fa-check-circle theme-check"></i>
                                </div>
                                <div class="spark-template-option opt-gold" data-theme="theme-gold" data-type="golden">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #d4a853; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('gold'); ?></span>
                                    </div>
                                    <i class="fa fa-circle-o theme-check"></i>
                                </div>
                                <div class="spark-template-option opt-emerald" data-theme="theme-emerald" data-type="elegant">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #2d6a4f; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('elegant'); ?></span>
                                    </div>
                                    <i class="fa fa-circle-o theme-check"></i>
                                </div>
                                <div class="spark-template-option opt-modern" data-theme="theme-modern" data-type="modern">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 16px; height: 16px; border-radius: 4px; background: #3d3d3d; border: 1px solid rgba(255,255,255,0.4);"></div>
                                        <span><?= lang('modern'); ?></span>
                                    </div>
                                    <i class="fa fa-circle-o theme-check"></i>
                                </div>
                            </div>
                            <input type="hidden" name="gift_card_type" id="gift_card_type" value="classic">
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN: Form Fields -->
                <div class="col-md-7">
                    <div style="background: var(--spark-surface-card, #ffffff); border: 1px solid var(--spark-border-default, #e2e8f0); border-radius: 16px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                        
                        <h4 style="font-size: 15px; font-weight: 800; color: var(--spark-text-primary, #0f172a); margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 13px;">
                                <i class="fa fa-edit"></i>
                            </div>
                            <?= lang('card_info'); ?>
                        </h4>

                        <!-- Customer Selection (Select2 AJAX Autocomplete) -->
                        <div id="customer-con" class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('customer', 'customer'); ?></label>
                            <div class="input-group">
                                <?php echo form_input('customer', (isset($_GET['customer']) ? $_GET['customer'] : ''), 'class="form-control" id="customer" style="width:100%;"'); ?>
                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 10px; background: var(--spark-surface-subtle, #f1f5f9);">
                                        <a href="<?= admin_url('customers/add'); ?>" id="addManually" title="<?= lang('add_customer') ?>">
                                            <i class="fa fa-plus-circle text-success" id="addIcon" style="font-size: 1.3em;"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Amount Preset Chips + Value Input -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('value', 'value'); ?> (<?= $Settings->default_currency; ?>)</label>
                            <div class="spark-preset-chips">
                                <button type="button" class="spark-preset-chip" data-amount="500">500</button>
                                <button type="button" class="spark-preset-chip" data-amount="1000">1,000</button>
                                <button type="button" class="spark-preset-chip is-active" data-amount="2000">2,000</button>
                                <button type="button" class="spark-preset-chip" data-amount="5000">5,000</button>
                            </div>
                            <?php echo form_input('value', '2000', 'class="form-control" id="value" required="required" placeholder="' . lang('or_custom_amount') . '" style="height: 42px; border-radius: 10px; font-weight: 700; font-size: 15px;"'); ?>
                        </div>

                        <!-- Card Number + Generator -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('card_no', 'card_no'); ?></label>
                            <div class="input-group">
                                <?php echo form_input('card_no', '', 'class="form-control" id="card_no" required="required" placeholder="XXXX-XXXX-XXXX-XXXX" style="height: 42px; border-radius: 0 10px 10px 0; font-family: monospace; font-weight: 700; font-size: 15px;"'); ?>
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
                            <?php echo form_input('expiry', $this->sma->hrsd(date('Y-m-d', strtotime('+2 year'))), 'class="form-control date" id="expiry" style="height: 42px; border-radius: 10px; font-weight: 600;"'); ?>
                        </div>

                        <!-- Staff Points & Award Points -->
                        <div class="form-group" style="margin-top: 10px;">
                            <input type="checkbox" class="checkbox" name="staff_points" id="staff_points">
                            <label for="staff_points" class="padding05" style="font-size: 12.5px; font-weight: 600;"><?= lang('use_staff_award_points'); ?></label>
                        </div>
                        <div id="staff-con" style="display:none; background: var(--spark-surface-subtle, #f8fafc); border-radius: 12px; padding: 14px; margin-bottom: 14px; border: 1px solid var(--spark-border-default, #e2e8f0);">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang('user'); ?></label>
                                <?php
                                $us[''] = '';
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '" style="height: 42px; border-radius: 10px;"');
                                ?>
                            </div>
                            <div class="form-group well well-sm" id="sa-points-con" style="display:none; border-radius: 10px;">
                                <p class="bold"><?= lang('award_points'); ?>: <span id="staff_award_points"></span></p>
                                <?= lang('use_points', 'sa_points'); ?>
                                <?php echo form_input('sa_points', '', 'class="form-control" id="sa_points"'); ?>
                            </div>
                        </div>

                        <div class="well well-sm" id="award-points-con" style="display:none; border-radius: 10px;">
                            <div class="form-group">
                                <p class="bold"><?= lang('award_points'); ?>: <span id="award_points"></span></p>
                                <input type="checkbox" class="checkbox" name="use_points" id="use_points">
                                <label for="use_points" class="padding05"><?= lang('use_award_points'); ?></label>
                            </div>
                            <div class="form-group" id="ca-points-con" style="display:none;">
                                <?= lang('use_points', 'ca_points'); ?>
                                <?php echo form_input('ca_points', '', 'class="form-control" id="ca_points"'); ?>
                            </div>
                        </div>

                        <!-- Payment Selection (Free vs Paid) -->
                        <div class="form-group" style="padding-top: 10px; border-top: 1px solid #e2e8f0;">
                            <label style="font-size: 13px; font-weight: 700; margin-inline-end: 16px;">
                                <?php echo form_radio('paid', '0', '1', "id='paid_1'"); ?> <?= lang('Free_of_charge'); ?>
                            </label>
                            <label style="font-size: 13px; font-weight: 700;">
                                <?php echo form_radio('paid', '1', '', "id='paid_2'"); ?> <?= lang('Paid'); ?>
                            </label>

                            <!-- Paid Amount & Gateway Sub-Form -->
                            <div class="well well-sm" id="div_paid_amount" style="display:none; border-radius: 12px; margin-top: 10px;">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('Amount', 'paid_amount'); ?>
                                            <?php echo form_input('paid_amount', '', 'class="form-control" id="paid_amount" style="height: 40px; border-radius: 8px;"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('paying_by', 'paid_by_1'); ?>
                                            <select name="paid_by" id="paid_by_1" class="form-control paid_by" style="height: 40px; border-radius: 8px;">
                                                <?= $Settings->accounting ? $this->sma->paid_opts_accounts(false, true) : $this->sma->paid_opts(false, true); ?>
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

                                    <!-- Credit Card Sub-Fields -->
                                    <div class="pcc_1" style="display:none; width: 100%; padding: 0 15px;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input name="pcc_no" type="text" id="pcc_no_1" class="form-control" placeholder="<?= lang('cc_no') ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input name="pcc_holder" type="text" id="pcc_holder_1" class="form-control" placeholder="<?= lang('cc_holder') ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type">
                                                        <option value="Mada"><?= lang('Mada'); ?></option>
                                                        <option value="Visa"><?= lang('Visa'); ?></option>
                                                        <option value="MasterCard"><?= lang('MasterCard'); ?></option>
                                                        <option value="Amex"><?= lang('Amex'); ?></option>
                                                        <option value="Discover"><?= lang('Discover'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input name="pcc_month" type="text" id="pcc_month_1" class="form-control" placeholder="<?= lang('month') ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input name="pcc_year" type="text" id="pcc_year_1" class="form-control" placeholder="<?= lang('year') ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cheque Sub-Fields -->
                                    <div class="pcheque_1" style="display:none; width: 100%; padding: 0 15px;">
                                        <div class="form-group">
                                            <?= lang('cheque_no', 'cheque_no_1'); ?>
                                            <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                                        </div>
                                    </div>

                                    <!-- Tax Rate & Totals -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('VAT_rate', 'tax_rate'); ?>
                                            <?php
                                            $tr[''] = '';
                                            foreach ($tax_rates as $tax) {
                                                $tr[$tax->id] = $tax->name;
                                            }
                                            echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : $Settings->default_tax_rate2), 'id="tax_rate" class="form-control input-tip select" style="width:100%;"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('VAT_Amount', 'tax_amount'); ?>
                                            <?= form_input('tax_amount', (isset($_POST['tax_amount']) ? $_POST['tax_amount'] : '0'), 'class="form-control tip" id="tax_amount" readonly'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?= lang('grand_total', 'total_paid_amount'); ?>
                                            <?= form_input('total_paid_amount', (isset($_POST['total_paid_amount']) ? $_POST['total_paid_amount'] : '0'), 'class="form-control tip" id="total_paid_amount" readonly'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Field -->
                        <div class="form-group">
                            <label class="control-label" style="font-size: 12.5px; font-weight: 700; color: var(--spark-text-secondary, #475569);"><?= lang('note', 'note'); ?></label>
                            <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="note" rows="2" style="border-radius: 10px;" placeholder="' . lang('add_notes_optional') . '"'); ?>
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
            <?php echo form_submit('add_gift_card', lang('add_gift_card'), 'class="spark-btn-giftcard-primary" id="btn-submit-gift-card"'); ?>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<!-- Modal for Adding Customer Manually -->
<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_customers') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <span id="mError"></span>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group company">
                            <?= lang('Company_name_in_Arabic', 'Company_name_in_Arabic'); ?>*
                            <?php echo form_input('company', '', 'class="form-control tip" id="company" data-bv-notempty="true"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('email_address', 'email_address'); ?>
                            <input type="email" name="email" class="form-control" id="email"/>
                        </div>
                        <div class="form-group">
                            <?= lang('phone', 'phone'); ?>
                            <input type="tel" name="phone" class="form-control" id="phone"/>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            <?= lang('vat_no', 'vat_no'); ?>
                            <?php echo form_input('vat_no', '', 'class="form-control" id="vat_no"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('cr_no', 'cr_no'); ?>
                            <?php echo form_input('cr_no', '', 'class="form-control" id="cr_no"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addCustomerManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<?= $modal_js ?>

<script type="text/javascript">
var tax_rates = <?php echo json_encode($tax_rates); ?>;

$(document).ready(function () {

    // ─── 1. Customer Select2 AJAX Autocomplete (Original Spark ERP Implementation) ───
    if ($.fn.select2) {
        $('#customer').select2({
            minimumInputLength: 1,
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

    var customer_points = 0;
    $('#customer').on('change select2-close select2-selecting', function (e) {
        var selected_customer = $(this).val();
        var selected_data = $(this).select2('data');
        var cust_name = (e.choice && e.choice.text) ? e.choice.text : (selected_data ? selected_data.text : '');

        if (cust_name && cust_name !== 'No Match Found') {
            $('#preview_card_customer').text(cust_name);
        } else if (!selected_customer) {
            $('#preview_card_customer').text('<?= lang('customer_name'); ?>');
        }

        if (selected_customer) {
            $.ajax({
                type: "get", async: false,
                url: site.base_url + "customers/get_award_points/" + selected_customer,
                dataType: 'json',
                success: function (data) {
                    if (data != null) {
                        $('#award_points').html(data.ca_points);
                        $('#ca_points').val(data.ca_points);
                        customer_points = parseInt(data.ca_points);
                        if (data.ca_points > 0) {
                            $('#award-points-con').slideDown();
                        } else {
                            $('#award-points-con').slideUp();
                        }
                    } else {
                        $('#award-points-con').slideUp();
                    }
                }
            });
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

        // Update Amount
        if (cardVal && !isNaN(cardVal) && parseFloat(cardVal) > 0) {
            $('#preview_card_val').text(parseFloat(cardVal).toLocaleString());
        } else {
            $('#preview_card_val').text('2,000');
        }

        // Update Card Number & Barcode
        if (cardNo && cardNo.trim() !== '') {
            var formatted = formatCardNumber(cardNo);
            $('#preview_card_no').text(formatted);
            $('#preview_barcode_num').text(cardNo);
        } else {
            $('#preview_card_no').text('XXXX-XXXX-XXXX-XXXX');
            $('#preview_barcode_num').text('XXXX-XXXX-XXXX-XXXX');
        }

        // Update Expiry Date
        if (expiryVal && expiryVal.trim() !== '') {
            $('#preview_card_expiry').text(expiryVal);
        }
    }

    // Bind real-time input events for live preview
    $('#value').on('input change keyup', updateGiftCardPreview);
    $('#card_no').on('input change keyup', updateGiftCardPreview);
    $('#expiry').on('change dp.change input', updateGiftCardPreview);

    // ─── 4. Preset Amount Chips Click Handler ───
    $(document).on('click', '.spark-preset-chip', function(e) {
        e.preventDefault();
        $('.spark-preset-chip').removeClass('is-active');
        $(this).addClass('is-active');
        var amt = $(this).attr('data-amount');
        $('#value').val(amt).trigger('input').trigger('change');
    });

    // ─── 5. 4-Template Selection Switcher ───
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

    // ─── 6. 3D Card Flip Handler ───
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

    // ─── 7. Generator Button Handler ───
    $('#genNo').click(function (e) {
        e.preventDefault();
        var no = (typeof generateCardNo === 'function') ? generateCardNo() : (Math.floor(1000000000000000 + Math.random() * 9000000000000000) + '');
        $('#card_no').val(no).trigger('input').trigger('change');
        return false;
    });

    // Initial Generator Trigger & Preview Render
    if (!$('#card_no').val()) {
        $('#genNo').trigger('click');
    }
    updateGiftCardPreview();

    // ─── 8. Tax Calculation Function ───
    function calcTax() {
        var amount = $('#paid_amount').val();
        var tax = 0;
        var tax_rate = $('#tax_rate').val();

        if (tax_rates && amount) {
            $.each(tax_rates, function() {
                if (this.id == tax_rate) {
                    if (this.type == 2) {
                        tax = (typeof formatDecimal === 'function') ? formatDecimal(this.rate, 12) : this.rate;
                    } else if (this.type == 1) {
                        tax = (typeof formatDecimal === 'function') ? formatDecimal((parseFloat(amount) * this.rate) / 100, 12) : ((parseFloat(amount) * this.rate) / 100);
                    }
                }
            });
        }
        $('#tax_amount').val(tax);
        var total = parseFloat(amount || 0) + parseFloat(tax || 0);
        $('#total_paid_amount').val((typeof formatDecimal === 'function') ? formatDecimal(total, 2) : total.toFixed(2));
    }

    $('#paid_amount, #tax_rate').on('change input keyup', calcTax);

    // ─── 9. Payment Radios and Staff Points ───
    $('input[name="paid"]').on('ifChanged change', function () {
        if ($('#paid_2').is(':checked')) {
            $('#div_paid_amount').slideDown();
        } else {
            $('#div_paid_amount').slideUp();
        }
    });

    $('#staff_points').on('ifChecked', function () {
        $('#customer-con').slideUp('fast');
        $('#staff-con').slideDown();
    });
    $('#staff_points').on('ifUnchecked', function () {
        $('#staff-con').slideUp('fast');
        $('#customer-con').slideDown();
    });

    $('#use_points').on('ifChecked', function () {
        $('#ca-points-con').slideDown();
    });
    $('#use_points').on('ifUnchecked', function () {
        $('#ca-points-con').slideUp();
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

    $('#user').change(function () {
        var selected_user = $(this).val();
        if (selected_user) {
            $.ajax({
                type: "get", async: false,
                url: site.base_url + "sales/get_award_points/" + selected_user,
                dataType: 'json',
                success: function (data) {
                    if (data != null) {
                        $('#staff_award_points').html(data.sa_points);
                        $('#sa_points').val(data.sa_points);
                        if (data.sa_points > 0) {
                            $('#sa-points-con').slideDown();
                        } else {
                            $('#sa-points-con').slideUp();
                        }
                    } else {
                        $('#sa-points-con').slideUp();
                    }
                }
            });
        } else {
            $('#sa-points-con').slideUp();
        }
    });

    // ─── 10. Customer Manual Add Modal Handlers ───
    $(document).on('click', '#addManually', function (e) {
        e.preventDefault();
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

    $(document).on('click', '#addCustomerManually', function (e) {
        if (!$('#company').val()) {
            $('#mError').text('<?= lang('company_name_is_required'); ?>');
            $('#mError-con').show();
            return false;
        }

        var customerArr = {
            name: '', nameEn: '', address: '', addressEn: '',
            customer_group_id: '<?= $Settings->customer_group ?>',
            price_group_id: '<?= $Settings->price_group ?>',
            customer_group_name: '', price_group_name: '',
            group_id: '3', group_name: 'customer',
            company: $('#company').val(),
            companyEn: $('#company').val(),
            email: $('#email').val(),
            vat_no: $('#vat_no').val(),
            cr_no: $('#cr_no').val(),
            phone: $('#phone').val(),
            co_type: '1', hide_in_pos: '0'
        };

        $.ajax({
            type: "get", async: false,
            url: site.base_url + "customers/addByAjax",
            data: {token: "<?= $this->security->get_csrf_hash(); ?>", customer: customerArr},
            dataType: "json",
            success: function (data) {
                if (data.msg == 'success') {
                    $('#mModal').modal('hide');
                    if ($.fn.select2) {
                        $('#customer').select2("data", {id: data.result.id, text: data.result.text});
                    } else {
                        $('#customer').val(data.result.id);
                    }
                    $('#preview_card_customer').text(data.result.text);
                } else {
                    $('#mError').text(data.msg);
                    $('#mError-con').show();
                }
            }
        });
        return false;
    });
});
</script>