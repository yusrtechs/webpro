<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_payment_type'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('accounts/Edit_payment_type/' . $payment_type->id, $attrib); ?>
        <div class="modal-body col-md-12">
            <p><?= lang('enter_info'); ?></p>


			<div class="col-md-6">
            <div class="form-group">
                <?= lang('Payment_type_name', 'Payment_type_name'); ?>
                <?= form_input('payment_name', $payment_type->payment_name, 'class="form-control tip" id="payment_name" required="required"'); ?>
            </div>
            <div class="form-group">
               <?= lang('Payment_method', 'Payment_method'); ?>
                <select name="payment_type" id="payment_type" class="form-control paid_by">
                <?= $this->sma->paid_opts_accounts($payment_type->payment_type); ?>
                </select>
            </div>
			<div class="form-group" id="div_payment_cash">
               <?= lang('Account', 'Account'); ?>
                <?php
                $acc= [];
				$acc[$accounts_main_boxes_acct->id] = $accounts_main_boxes_acct->accountName;
                foreach ($payment_account_cash as $account) {
                     $acc[$account->id] = $account->accountName;
                 }
                echo form_dropdown('payment_account', $acc, $payment_type->payment_account, 'class="form-control tip" id="payment_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
            </div>

			<div class="form-group" id="div_payment_bank" style="display:none;">
               <?= lang('Account', 'Account'); ?>
                <?php
                $acc= [];
				$acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                foreach ($payment_account_bank as $account) {
                     $acc[$account->id] = $account->accountName;
                 }
                echo form_dropdown('payment_account1', $acc, $payment_type->payment_account, 'class="form-control tip" id="payment_account1" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
            </div>

			<div class="form-group">
               <?= lang('Commission_Tax', 'Commission_Tax'); ?>
                <?php
                $acc= [];
				$acc[''] = '';
                foreach ($tax_rates as $account) {
                     $acc[$account->id] = $account->name;
                 }
                echo form_dropdown('tax_rate', $acc, $payment_type->tax_rate, 'class="form-control tip" id="tax_rate" data-placeholder="' . lang('select') . ' ' . lang('Commission_Tax') . '"  style="width:100%;"');
                ?>
            </div>

			</div>


        	<div class="col-md-6">
            <div class="form-group">
                <?= lang('English_name', 'English_name'); ?>
                <?= form_input('payment_ename', $payment_type->payment_ename, 'class="form-control tip" id="payment_ename" '); ?>
            </div>

			<div class="form-group" id="div_payment_CC" style="display:none;">
               <?= lang('paying_by', 'paying_by'); ?>
                <?php
                $acc= [];
				$payment_cc_id="";
				foreach ($paymentType as $payment) {
					if($payment_type->payment_cc_id==$payment->id)
						$payment_cc_id= $payment->id ;

                     $acc[$payment->id] = $this->Settings->user_language == 'arabic' ? $payment->name :  $payment->second_name;
                 }
                echo form_dropdown('payment_cc_id', $acc, $payment_cc_id, 'class="form-control tip" id="payment_cc_id" data-placeholder="' . lang('select') . ' ' . lang('payment_cc_id') . '"  style="width:100%;"');
                ?>
            </div>

			<div class="form-group">
                <?= lang('Commission_%', 'Commission_%'); ?>
                <?= form_input('commisson_perc', $payment_type->commisson_perc, 'class="form-control tip" id="commisson_perc" '); ?>
            </div>
            <div class="form-group">
             <?= lang('Bank_commisson_account', 'Bank_commisson_account'); ?>
                <?php
                $acc= [];
				$acc[''] = '';
				if(sizeof($commisson_account)==1){
					$acc[$commisson_account->id] = $commisson_account->accountName;
				}else{
                foreach ($commisson_account as $account) {
                     $acc[$account->id] = $account->accountName;
                 }
				}
                echo form_dropdown('commisson_account', $acc, $payment_type->commisson_account, 'class="form-control tip" id="commisson_account" data-placeholder="' . lang('select') . ' ' . lang('Bank_commisson_account') . '"   style="width:100%;"');
                ?>
            </div>

			<div class="form-group" style="display:none;">
               <?= lang('biller', 'biller'); ?>
                <?php
                $acc= [];
				$acc[''] = '';
                foreach ($billers as $account) {
                     $acc[$account->id] = $account->company;
                 }
                echo form_dropdown('casher_id', $acc, $payment_type->casher_id, 'class="form-control tip" id="casher_id" data-placeholder="' . lang('select') . ' ' . lang('biller') . '"  style="width:100%;"');
                ?>
            </div>



			</div>

			<div class="col-md-12">
			<div class="form-group">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', $payment_type->description, 'class="form-control tip" id="description" '); ?>
                </div>
                </div>





		</div>
        <div class="modal-footer">
            <?php echo form_submit('edit_payment_type', lang('Edit_payment_type'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript">
    $(document).ready(function() {

		var payment_type =  $('#payment_type').val();

			if(payment_type=='cash'){
				$("#div_payment_cash").css("display", "block");
				$("#div_payment_bank").css("display", "none");
				$("#div_payment_CC").css("display", "none");

			}else if(payment_type=='CC'){
				$("#div_payment_cash").css("display", "none");
				$("#div_payment_bank").css("display", "block");
				$("#div_payment_CC").css("display", "block");

			}else{
				$("#div_payment_cash").css("display", "none");
				$("#div_payment_bank").css("display", "block");
				$("#div_payment_CC").css("display", "none");


			}


        $('#base_account_type').change(function(e) {
            var bu = $(this).val();
            if(bu > 0)
                $('#measuring').slideDown();
            else
                $('#measuring').slideUp();
        });
        var obu = <?= !empty($account_type->base_account_type) ? $account_type->base_account_type : 0; ?>;
        if(obu > 0)
            $('#measuring').slideDown();
        else
            $('#measuring').slideUp();



		$(document).on('change', '.paid_by', function () {



            var payment_type =  $('#payment_type').val();

			if(payment_type=='cash'){
				$("#div_payment_cash").css("display", "block");
				$("#div_payment_bank").css("display", "none");

			}else{
				$("#div_payment_cash").css("display", "none");
				$("#div_payment_bank").css("display", "block");

			}




			//var product_code = row.find('.product_code').val();

			//var opt = $("<select name=\"prunit[]\" id=\"prunit_" + product_id + "\"  class=\"form-control select rprunit\" />");
			//opt=row.find('.unit');


				/*

				 $.ajax({
                type: 'post',
                url: '<?= admin_url('sales/get_product_group_price/' . $price_group->id); ?>',
                dataType: "json",
                data: {
                    <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>',
                    product_id: product_id,  product_unit_id: product_unit_id
                },
                success: function (data) {

                    if (data.status == 0){
                    var price = row.find('.price').val('');
                    var product_code = row.find('.product_code').val('');
			}else{


							var price = row.find('.price').val(data.price);
                         var product_code = row.find('.product_code').val(data.product_code);



						//btn.removeClass('btn-primary').removeClass('btn-danger').addClass('btn-success').html('<i class="fa fa-check"></i>');
					}
                },
                error: function (data) {
                    bootbox.alert('<?= lang('ajax_error') ?>');//btn.removeClass('btn-primary').addClass('btn-danger').html('<i class="fa fa-times"></i>');
                }
            });




				//alert(opt.get(0).outerHTML)

			*/


        });
    });
</script>
