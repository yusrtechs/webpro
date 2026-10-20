<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_payment_application'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('purchases/edit_payment_application/' . $payment_application->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

           <div class="row">
                
				
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('code', 'code'); ?>
                        <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] :  $payment_application->code), 'class="form-control tip" id="code" required="required"'); ?>
                    </div>
                </div>

				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Payment_schedule', 'Payment_schedule'); ?>
                        <?= form_input('payment_schedule', (isset($_POST['payment_schedule']) ? $_POST['payment_schedule'] : $payment_application->payment_schedule), 'class="form-control tip" id="payment_schedule"'); ?>
                    </div>
                </div>
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Applied_amount', 'applied_amount'); ?>
                        <?= form_input('applied_amount', (isset($_POST['applied_amount']) ? $_POST['applied_amount'] : $this->sma->formatDecimal($payment_application->applied_amount)), 'class="form-control tip" id="applied_amount" required="required"'); ?>
                    </div>
                </div>
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Applied_schedule', 'Applied_schedule'); ?>
                        <?= form_input('applied_schedule', (isset($_POST['applied_schedule']) ? $_POST['applied_schedule'] : $payment_application->applied_schedule), 'class="form-control tip" id="applied_schedule"'); ?>
                    </div>
                </div>
				
				<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Remarks', 'Remarks'); ?>
                        <?= form_input('remarks', (isset($_POST['remarks']) ? $_POST['remarks'] : $payment_application->remarks), 'class="form-control tip" id="remarks"'); ?>
                    </div>
                </div>
				

				    <div class="col-sm-6">
                        <div class="form-group">
                            <?= lang('Expected_payment_date', 'Expected_payment_date'); ?>
                            <?= form_input('expected_payment_date', (isset($_POST['expected_payment_date']) ? $_POST['expected_payment_date'] :  $this->sma->hrsd($payment_application->expected_payment_date)), 'class="form-control date" id="expected_payment_date"'); ?>
                        </div>
                    </div>
                
               

                <input type="hidden" value="<?php echo $payment_application->purchase_id; ?>" name="purchase_id"/>
            </div>
            <div class="clearfix"></div>
            

            <div class="form-group">
                <?= lang('Purpose', 'Purpose'); ?>
                <?php echo form_textarea('purpose', (isset($_POST['purpose']) ? $_POST['purpose'] : $this->sma->decode_html($payment_application->purpose)), 'class="form-control" id="purpose"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('Edit_payment_application', lang('Edit_payment_application'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
