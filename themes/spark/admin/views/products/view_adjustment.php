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
                    <p class="bold">
                        <?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                        <?= lang('reference_no'); ?>: <?= $inv->reference_no; ?><br>
                    </p>
                    </div>
                    <div class="col-xs-7 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <?= $this->sma->qrcode('link', urlencode(admin_url('products/view_adjustment/' . $inv->id)), 2); ?>
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
                        <th><?= lang('variants'); ?></th>
                        <th><?= lang('type'); ?></th>
                        <th><?= lang('quantity'); ?></th>
                        <th><?= lang('cost'); ?></th>
                        <th><?= lang('value'); ?></th>
                        <th><?= lang('unit'); ?></th>
                        <th><?= lang('expiry_date'); ?></th>
                    </tr>

                    </thead>

                    <tbody>

                    <?php $r = 1;
                    $totalquantity=0;
                    $totalvalue=0;
                    foreach ($rows as $row):
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <td style="vertical-align:middle;">
                                <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                            </td>
                            <th><?= $row->variant; ?></th>
                            <th><?= lang($row->type); ?></th>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity); ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->cost); ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->value); ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->unit_name; ?></td>
							<?php
							if($row->expiry==null){
							?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"></td>
                        	<?php
							}else{
							?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->expiry; ?></td>
                        	<?php
							}
							?>
                        </tr>
                        <?php
                        $r++;
                        $totalquantity=$totalquantity+$row->quantity;
                        $totalvalue=$totalvalue+$row->value;
                    endforeach;
                    ?>
                    </tbody>
                    <tfoot class="thead-dark">
                        <tr>
                            <td colspan="4"><strong style="font-size:18px;"><?= lang("total") ?></strong></td>
                            <td style="text-decoration: underline;border-top: 2px solid #000000;text-align:center; vertical-align:middle;"><strong style="font-size:18px;"><?= $this->sma->formatQuantity($totalquantity); ?></strong></td>
                            <td></td>
                            <td style="text-decoration: underline;border-top: 2px solid #000000;text-align:center; vertical-align:middle;"><strong style="font-size:18px;"><?= $this->sma->formatQuantity($totalvalue); ?></strong></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= '' ?></td>
                            <td></td>
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
                            <?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?>
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
