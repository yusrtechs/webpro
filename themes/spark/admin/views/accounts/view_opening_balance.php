<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <div class="text-center" style="margin-bottom:20px;">
                <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>" alt="<?= $Settings->site_name; ?>">
            </div>
            <div class="well well-sm">
                <div class="row bold">
                    <div class="col-xs-5">
                        <?php
                        if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){

                        ?>
                    <p class="bold">
                        <?= lang('Main_branch'); ?>: <?= $this->site->getMainBranchesByID($inv->brId)->name; ?><br>
                    </p>
                    <?php
                        }
                        ?>

                    <p class="bold">
                        <?= lang('date'); ?>: <?= $this->sma->hrld($inv->openingDate); ?><br>
                    </p>
                    </div>
                    <div class="col-xs-7 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->id) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <?= $this->sma->qrcode('link', urlencode(admin_url('accounts/view_opening_balance/' . $inv->id)), 2); ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table">

                    <thead>

                    <tr>
                        <th><?= lang('Sr#'); ?></th>
                        <th><?= lang('description'); ?></th>
                        <th><?= lang('Debit')." (".$inv->currency_code.")"; ?></th>
                        <th><?= lang('Credit')." (".$inv->currency_code.")"; ?></th>
                        <th><?= lang('Cost_center'); ?></th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php $r = 1;
                    $totalDebit=0;
                    $totalCredit=0;
                    foreach ($rows as $row):
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <td style="vertical-align:middle;">
                                <?= $row->account_code . ' - ' . $row->accounts_name; ?>
                                <?= $row->accounts_nameEn ? '<br>' . $row->accounts_nameEn : ''; ?>
                            </td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->debit); ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->credit); ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= '' ?></td>

                        </tr>
                        <?php
                        $r++;
                        $totalDebit=$totalDebit+$row->debit;
                        $totalCredit=$totalCredit+$row->credit;
                    endforeach;
                    ?>
                    </tbody>
                    <tfoot class="thead-dark">
                        <tr>
                            <td colspan="2"><strong style="font-size:18px;"><?= lang("total") ?></strong></td>
                            <td style="text-decoration: underline;border-top: 2px solid #000000;min-width:120px;"><strong style="font-size:18px;"><?= $this->sma->formatMoney($totalDebit,$inv->currency_code); ?></strong></td>
                            <td style="text-decoration: underline;border-top: 2px solid #000000;min-width:120px;"><strong style="font-size:18px;"><?= $this->sma->formatMoney($totalCredit,$inv->currency_code); ?></strong></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= '' ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <?php if ($inv->note || $inv->note != '') {
                        ?>
                        <div class="well well-sm">
                            <p class="bold"><?= lang('note'); ?>:</p>
                            <div><?= $this->sma->decode_html($inv->note); ?></div>
                        </div>
                    <?php
                    } ?>
                </div>
				<div class="col-xs-6">
                    <?php if ($inv->approve_note || $inv->approve_note != '') {
                        ?>
                        <div class="well well-sm">
                            <p class="bold"><?= lang('approve_note'); ?>:</p>
                            <div><?= $this->sma->decode_html($inv->approve_note); ?></div>
                        </div>
                    <?php
                    } ?>
                </div>


                <div class="col-xs-5 pull-right">
                    <div class="well well-sm">
                        <p>
                            <?= lang('created_by'); ?>: <?= $created_by->first_name . ' ' . $created_by->last_name; ?> <br>
                            <?= lang('date'); ?>: <?= $this->sma->hrld($inv->openingDate); ?>
                        </p>
                        <?php if ($inv->updated_by) {
                        ?>
                        <p>
                            <?= lang('updated_by'); ?>: <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?><br>
                            <?= lang('update_at'); ?>: <?= $this->sma->hrld($inv->updated_at); ?>
                        </p>
                        <?php
                    } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
