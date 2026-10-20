<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <button type="button" <a href="#" id="pdf" class="btn btn-xs btn-default no-print" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">
                                
                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                           
                            </a>
                       
            </button>
          
        </div>
        <div id="pr_pdf">
        <div class="modal-body">
        <!--<h4 class="modal-title" id="myModalLabel"><?= lang('view_expenses') . ' (' . lang('sale') . ' ' . lang('reference') . ': ' . $inv->reference_no . ')'; ?></h4> -->
            <h4 class="modal-title" id="myModalLabel"><?= lang('Expenses_on_invoice_with_chesis_no') . ' : ' . $inv->chesis_no ; ?></h4></br>
            <div class="table-responsive">
                <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                       class="table table-striped jambo_table bulk_action">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line('date'); ?></th>
                        <th style="width:20%;"><?= $this->lang->line('reference_no'); ?></th>
                        <?php  if ($this->Settings->accounting){ ?>
                            <th style="width:15%;"><?= $this->lang->line('Account_type'); ?></th>
                             <?php }else { ?>
                                <th style="width:15%;"><?= $this->lang->line('category'); ?></th>
                                <?php
                                } ?>
                        <th style="width:15%;"><?= $this->lang->line('amount'); ?>
                        <!--<th style="width:15%;"><?= $this->lang->line('paid_by'); ?></th>-->
                        <th style="width:20%;"><?= $this->lang->line('actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($expenses)) {
                        $totalAmount=0;
                        foreach ($expenses as $payment) {
                            ?>
                            <tr class="row<?= $payment->id ?>">
                                <td><?= $this->sma->hrld($payment->date); ?></td>
                                <td><?= $payment->reference; ?></td>
                                <?php 
                                if ($this->Settings->accounting){
                                    ?>
                                    <td><?= $this->accounts_model->getAccountByID($payment->account_id)->accountName; ?></td>
                                
                                    <?php
                                }else{

                                    ?>
                                    <td><?= $this->carspurchases_model->getExpenseCategoryByID($payment->category_id)->name; ?></td>
                                
                                    <?php
                            
                                }
		                        ?>
                                <td><?= $this->sma->formatMoney($payment->amount,$payment->currency) . ' ' . (($payment->attachment) ? '<a href="' . admin_url('welcome/download/' . $payment->attachment) . '"><i class="fa fa-chain"></i></a>' : ''); ?></td>
                               <!-- <td><?= lang($payment->paid_by); ?></td>-->
                                <td>
                                    <div class="text-center">
                                        <a href="<?= admin_url('carspurchases/expense_note/' . $payment->id) ?>" title="<?= lang('expense_note')?>"
                                           data-toggle="modal" data-target="#myModal2"><i class="fa fa-file-text-o"></i></a>
                                        <?php if ($payment->paid_by != 'gift_card') {
            ?>
                                            <a href="<?= admin_url('carspurchases/add_expense_payment/' . $payment->id) ?>"   title="<?= lang('add_payment')?>"  data-toggle="modal" data-target="#myModal2"><i class="fa fa-dollar"></i></a>
                                            <a href="<?= admin_url('carspurchases/expense_payments/' . $payment->id) ?>"   title="<?= lang('view_payments')?>"  data-toggle="modal" data-target="#myModal2"><i class="fa fa-money"></i></a>
                                            <a href="<?= admin_url('carspurchases/edit_expense/' . $payment->id) ?>" title="<?= lang('edit_expense')?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                    class="fa fa-edit"></i></a>
                                            <a href="#" class="po"
                                               title="<?= $this->lang->line('delete_payment') ?>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $payment->id ?>' href='<?= admin_url('carssales/delete_payment/' . $payment->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
                                               rel="popover"><i class="fa fa-trash-o"></i></a>
                                        <?php
        } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        $totalAmount=$totalAmount+$payment->amount;
                        $totalAmountCurrency=$payment->currency;
    }
} else {
    echo "<tr><td colspan='5'>" . lang('no_data_available') . '</td></tr>';
} ?>
                    </tbody>
                    <tfoot>
                        <tr>
                                <td colspan="3"><b><?= lang('Total') ?></b></td>
                                <td colspan="2"><b><?= $this->sma->formatMoney($totalAmount,$totalAmountCurrency); ?></b></td>
                                
                            </tr>
                        </tfoot>

                </table>
            </div>
        </div>
        </div><!-- end pr_pdf -->
    </div>
</div>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang("Expenses_vehicle"). $inv->chesis_no; ?>');
		    return false;
        });

        $(document).on('click', '.po-delete', function () {
            var id = $(this).attr('id');
            $(this).closest('tr').remove();
        });
        $(document).on('click', '.email_payment', function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            $.get(link, function(data) {
                bootbox.alert(data.msg);
            });
            return false;
        });
    });
</script>
