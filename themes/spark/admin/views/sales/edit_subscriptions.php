<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
        <script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_subscriptions'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('sales/edit_subscriptions/' . $subscriptions->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row" >
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Name', 'name'); ?>
                        <?php echo form_input('name', $subscriptions->name, 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('English_name', 'name_en'); ?>
                        <?php echo form_input('name_en', $subscriptions->name_en, 'class="form-control tip" id="name_en"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?php echo lang('Paid_value', 'amount_1'); ?>
                        <div class="controls">
                            <?php echo form_input('paid_amount', $this->sma->formatDecimal($subscriptions->paid_amount), 'class="form-control" id="amount_1" required="required"'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <?php echo lang('Purchasing_value', 'amount_2'); ?>
                        <div class="controls">
                            <?php echo form_input('amount', $this->sma->formatDecimal($subscriptions->amount), 'class="form-control" id="amount_2" required="required"'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                                <?= lang('Status', 'status'); ?>
                                <?php
                                $opt = [1 => lang('active'), 0 => lang('inactive')];
                                echo form_dropdown('status', $opt, $subscriptions->status, 'id="status" required="required" class="form-control select" style="width:100%;"');
                                ?>
                    </div>
                </div>
            </div>



        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_subscriptions', lang('Edit_subscriptions'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script>
$(document).ready(function () {
    $('#amount_1').on('input', function () {
        $('#amount_2').val($(this).val());
    });
});
</script>
