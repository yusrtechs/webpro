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
            <h4 class="modal-title" id="myModalLabel"><?= lang('View_payment_application') . ' (' . lang('purchase') . ' ' . lang('reference') . ': ' . $inv->reference_no . ')'; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="CompTable" class="table table-striped jambo_table bulk_action">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line('date'); ?></th>
                        <th style="width:30%;"><?= $this->lang->line('reference_no'); ?></th>
                        <th style="width:15%;"><?= $this->lang->line('code'); ?></th>
                        <th style="width:15%;"><?= $this->lang->line('Applied_amount'); ?></th>
                        <th style="width:10%;"><?= $this->lang->line('actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($payment_application)) {
    foreach ($payment_application as $payment) {
        ?>
                            <tr class="row<?= $payment->id ?>">
                                <td><?= $this->sma->hrld($payment->created_date); ?></td>
                                <td><?= $inv->reference_no; ?></td>
                                <td><?= $payment->code; ?></td>
                                <td><?= $this->sma->formatMoney($payment->applied_amount);?></td>
                                <td>
                                    <div class="text-center">
                                        <a href="<?= admin_url('purchases/payment_application_note/' . $payment->id) ?>"
                                           title="<?= $this->lang->line('Payment_application') ?>" data-toggle="modal" data-target="#myModal2"><i class="fa fa-file-text-o"></i></a>
                                        <a href="<?= admin_url('purchases/edit_payment_application/' . $payment->id) ?>"
                                           title="<?= $this->lang->line('Edit_payment_application') ?>" data-toggle="modal" data-target="#myModal2"><i class="fa fa-edit"></i></a>
                                        <a href="#" class="po" title="<?= $this->lang->line('Delete_payment_application') ?>"
                                           data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $payment->id ?>' href='<?= admin_url('purchases/delete_payment_application/' . $payment->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
                                           rel="popover"><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php
    }
} else {
    echo "<tr><td colspan='4'>" . lang('no_data_available') . '</td></tr>';
} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
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