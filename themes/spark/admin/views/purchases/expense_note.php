<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body print">
            <div class="row bold">
                    <div class="col-xs-12 no-print" >
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <button type="button "
            <a href="#" id="pdf" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">

                                <i  class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>

                 </a>

            </button>
            </div>
            </div>
            <div id="pr_pdf">
                <div class="col-xs-6 pull-left" style=" margin-bottom:50px;text-align:left;">
                <?php
                //if ($logo) {
            ?>
                <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                     alt="<?= $Settings->site_name; ?>">
            <?php
            //    }
            ?>

					</div>
					<div style="margin-top:10px;float:right;">

            <span valign="top"><h2  style=" font-size: 20px;" dir="ltr">Expense&nbsp;Note/ملاحظات&nbsp;المصاريف </h2><span>
            </div>
            <div class="clearfix"></div>
            <div class="row padding10">
                <div class="col-xs-5">
                    <h2 class=""><?= $Settings->site_name; ?></h2>
                    <?php
                    if ($warehouse) {
                    echo '<p>';
                    echo $warehouse->name . ' (' . $warehouse->code . ')<br>' . $warehouse->address;
                    echo '</p>';
                    echo lang('tel') . ': ' . $warehouse->phone . '<br />' . lang('email') . ': ' . $warehouse->email;
                    }
                    ?>

                    <div class="clearfix"></div>
                </div>
            </div>
            <div style="clear: both;"></div>
            <p>&nbsp;</p>

            <div class="well">
                <table class="table table-borderless" style="margin-bottom:0;">
                    <tbody>
                    <tr>
                        <td><strong><?= lang('date'); ?></strong></td>
                        <td><strong class="text-right"><?php echo $this->sma->hrld($expense->date); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong><?= lang('reference'); ?></strong></td>
                        <td><strong class="text-right"><?php echo $expense->reference; ?></strong></td>
                    </tr>
                    <?php if ($category) {
        ?>
                    <tr>
                        <td><strong><?= lang('category'); ?></strong></td>
                        <td><strong class="text-right"><?php echo $category->name; ?></strong></td>
                    </tr>
                    <?php
    } ?>
                    <?php if ($warehouse) {
        ?>
                    <tr>
                        <td><strong><?= lang('warehouse'); ?></strong></td>
                        <td><strong class="text-right"><?php echo $warehouse->name; ?></strong></td>
                    </tr>
                    <?php
    } ?>
                    <tr>
                        <td><strong><?= lang('amount'); ?></strong></td>
                        <td><strong class="text-right"><?php echo $this->sma->formatMoney($expense->amount); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><?php echo $expense->note; ?></td>
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
</div>

<script type="text/javascript">
    $('#pdf').click(function (event) {
                event.preventDefault();
        PrPDF('pr_pdf','<?= lang('expense_note').' '. $expense->id; ?>');
		        return false;
            });
</script>