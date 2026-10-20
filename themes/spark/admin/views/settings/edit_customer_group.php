<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_customer_group'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('sales/edit_customer_group/' . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line('group_name'); ?></label>

                <div
                    class="controls"> <?php echo form_input('name', $customer_group->name, 'class="form-control" id="name" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="discount_type"><?= lang('type'); ?></label>

                <div class="controls">
                    <?php $opt_coupon =  ['percent' => lang('percentage'), 'amount' => lang('amount')]; ?>
                    <?php
                        echo form_dropdown('discount_type', $opt_coupon, $customer_group->discount_type, 'class="form-control tip" required="required" id="discount_type" style="width:100%;"');
                    ?>
                </div>
            </div>
            <div class="form-group percentage">
                <label class="control-label" for="percent"><?php echo $this->lang->line('group_percentage'); ?></label>

                <div
                    class="controls"> <?php echo form_input('percent', $customer_group->percent, 'class="form-control" id="percent" '); ?> </div>
            </div>
            <div class="form-group amount">
                <label for="amount"><?php echo $this->lang->line('amount'); ?></label>
                <div class="controls"> <?php echo form_input('amount', $customer_group->amount, 'class="form-control" id="amount" '); ?> </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_customer_group', lang('edit_customer_group'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('#discount_type').change(function(e) {
            if ($(this).val() == 'percent') {
                $('.percentage').show();
                $('.amount').hide();
            } else {
                $('.percentage').hide();
                $('.amount').show();
            }
        });
        if ($('#discount_type').val() == 'percent') {
            $('.percentage').show();
            $('.amount').hide();
        } else {
            $('.percentage').hide();
            $('.amount').show();
        }
    });
</script>