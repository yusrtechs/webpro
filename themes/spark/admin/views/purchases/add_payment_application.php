<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_payment_application'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('purchases/add_payment_application/' . $inv->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                
				
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('code', 'code'); ?>
                        <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ''), 'class="form-control tip" id="code" required="required"'); ?>
                    </div>
                </div>

				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Payment_schedule', 'Payment_schedule'); ?>
                        <?= form_input('payment_schedule', (isset($_POST['payment_schedule']) ? $_POST['payment_schedule'] : ''), 'class="form-control tip" id="payment_schedule"'); ?>
                    </div>
                </div>
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Applied_amount', 'applied_amount'); ?>
                        <?= form_input('applied_amount', (isset($_POST['applied_amount']) ? $_POST['applied_amount'] :  $this->sma->formatDecimal($inv->grand_total - $inv->paid - $payment_application->applied_amount) ), 'class="form-control tip" id="applied_amount" required="required"'); ?>
                    </div>
                </div>
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Applied_schedule', 'Applied_schedule'); ?>
                        <?= form_input('applied_schedule', (isset($_POST['applied_schedule']) ? $_POST['applied_schedule'] : ''), 'class="form-control tip" id="applied_schedule"'); ?>
                    </div>
                </div>
				
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Remarks', 'Remarks'); ?>
                        <?= form_input('remarks', (isset($_POST['remarks']) ? $_POST['remarks'] : ''), 'class="form-control tip" id="remarks"'); ?>
                    </div>
                </div>
				

				    <div class="col-sm-6">
                        <div class="form-group">
                            <?= lang('Expected_payment_date', 'Expected_payment_date'); ?>
                            <?= form_input('expected_payment_date', (isset($_POST['expected_payment_date']) ? $_POST['expected_payment_date'] : ''), 'class="form-control date" id="expected_payment_date"'); ?>
                        </div>
                    </div>
                
               

                <input type="hidden" value="<?php echo $inv->id; ?>" name="purchase_id"/>
            </div>
            <div class="clearfix"></div>
            

            <div class="form-group">
                <?= lang('Purpose', 'Purpose'); ?>
                <?php echo form_textarea('purpose', (isset($_POST['purpose']) ? $_POST['purpose'] : ''), 'class="form-control" id="purpose"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_payment_application', lang('Add_payment_application'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
</script>
<?= $modal_js ?>
