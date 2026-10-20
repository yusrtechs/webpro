<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('edit_gift_card'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('carssales/edit_gift_card/' . $gift_card->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('card_no', 'card_no'); ?>
                <div class="input-group">
                    <?php echo form_input('card_no', $gift_card->card_no, 'class="form-control" id="card_no" required="required"'); ?>
                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#"
                                                                                                       id="genNo"><i
                                class="fa fa-cogs"></i></a></div>
                </div>
            </div>
            <div class="form-group">
                <?= lang('value', 'value'); ?>
                <?php echo form_input('value', $this->sma->formatDecimal($gift_card->value), 'class="form-control" id="value" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('customer', 'customer'); ?>
                <?php echo form_input('customer', $gift_card->customer_id, 'class="form-control" id="customer"'); ?>
            </div>
            <div class="form-group">
                <?= lang('expiry_date', 'expiry'); ?>
                <?php echo form_input('expiry', $this->sma->hrsd($gift_card->expiry), 'class="form-control date" id="expiry"'); ?>
            </div>
			<div class="form-group">
                <?= lang('Status', 'Status'); ?>
                <?php
                $opt = [1 => lang('active'), 0 => lang('inactive')];
                echo form_dropdown('status', $opt, $gift_card->status, 'id="status" required="required" class="form-control input-tip select" style="width:100%;"'); ?>
                </div>
                                                                        
			
			<div class="form-group">
				<label for="paied_1">
					<?php echo form_radio('paid', '0', $gift_card->paid==0 ? '1' : '0', "id='paid_1'"); ?> <?= lang('Free_of_charge'); ?>
				</label>

				<label for="paied_2">
					<?php echo form_radio('paid', '1', $gift_card->paid==1 ? '1' : '0', "id='paid_2'"); ?> <?= lang('Paid'); ?>
				</label>
				
				<div class="well well-sm" id="div_paid_amount" style="display:none;">
				
				<div class="row">
                    <div class="col-lg-12">
					
					<div class="col-sm-6">
                
                    <div class="form-group">
                <?= lang('Amount', 'Amount'); ?>
                <?php echo form_input('paid_amount', $this->sma->formatDecimal($gift_card->paid_amount), 'class="form-control" id="paid_amount"'); ?>
				
				
            
            </div>
            </div>
			
			<div class="col-sm-6">
                                <div class="form-group">
                                    <?= lang('paying_by', 'paid_by_1'); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by" required="required">
									
                                       <?= $Settings->accounting ? $this->sma->paid_opts_accounts($payment->paid_by,true) : $this->sma->paid_opts($payment->paid_by,true); ?>
                                    </select>
                                </div>
								
                            </div>
							
							
							
							
							
							
							
							
							
							<?php
						if($Settings->accounting){
						?>
						<div class="col-md-6 pcash_1" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				
				foreach ($payment_types as $account) {
					if($account->payment_type=='cash')
                     $acc[$account->id] = $account->accountName;
                 }
				 //$acc[$accounts_main_boxes_acct->id] = $accounts_main_boxes_acct->accountName;
                
                echo form_dropdown('cash_account', $acc, '', 'class="form-control tip" id="cash_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}
						?>
						
                        <div class="pcc_1" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_no" type="text" id="pcc_no_1" class="form-control"
                                               placeholder="<?= lang('cc_no') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input name="pcc_holder" type="text" id="pcc_holder_1" class="form-control"
                                               placeholder="<?= lang('cc_holder') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                                placeholder="<?= lang('card_type') ?>">
											<option value="Mada"><?= lang('Mada'); ?></option>
                                            <option value="Visa"><?= lang('Visa'); ?></option>
                                            <option value="MasterCard"><?= lang('MasterCard'); ?></option>
                                            <option value="Amex"><?= lang('Amex'); ?></option>
                                            <option value="Discover"><?= lang('Discover'); ?></option>
                                        </select>
                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?= lang('card_type') ?>" />-->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="pcc_month" type="text" id="pcc_month_1" class="form-control"
                                               placeholder="<?= lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <input name="pcc_year" type="text" id="pcc_year_1" class="form-control"
                                               placeholder="<?= lang('year') ?>"/>
                                    </div>
                                </div>
                                <!--<div class="col-md-3">
                                    <div class="form-group">
                                        <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control" placeholder="<?= lang('cvv2') ?>" />
                                    </div>
                                </div>-->
                            </div>
							<?php
							if($Settings->accounting){
						?>
						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				
				foreach ($payment_types as $account) {
					if($account->payment_type=='CC')
                     $acc[$account->id] = $account->accountName;
                 }
                //$acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                
				echo form_dropdown('cc_account', $acc, '', 'class="form-control tip" id="cc_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                
				?>
                            </div>
                        </div>
						<?php
						}
						?>
                        </div>
                        <div class="pcheque_1" style="display:none;">
                            <div class="form-group"><?= lang('cheque_no', 'cheque_no_1'); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>
							<?php
						if($Settings->accounting){
						?>
						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				
				foreach ($payment_types as $account) {
					if($account->payment_type=='Cheque')
                     $acc[$account->id] = $account->accountName;
                 }
                //$acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('cheque_account', $acc, '', 'class="form-control tip" id="cheque_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}
						?>
                        </div>
						
						
						<div class="ptransfer_1" style="display:none;">
                            
							<?php
						if($Settings->accounting){
						?>
						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				
				foreach ($payment_types as $account) {
					if($account->payment_type=='bank_transfer')
                     $acc[$account->id] = $account->accountName;
                 }
                //$acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                echo form_dropdown('transfer_account', $acc, '', 'class="form-control tip" id="transfer_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>
                            </div>
                        </div>
						<?php
						}else{
							
						?>
						<div class="form-group"><?= lang('bank_details', 'bank_details'); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>
							
							<?php
						}
						?>
                        </div>

            <div class="col-sm-6">
                                
                                    <div class="form-group"  >
                                        <?= lang('VAT_rate', 'VAT_rate'); ?>
										<?php
                                    $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('tax_rate', $tr, $gift_card->tax_rate, 'id="tax_rate" data-placeholder="' . lang('select') . ' ' . lang('VAT_rate') . '" class="form-control input-tip select" style="width:100%;"'); 
												?>
                                
                                    </div>
                                
                            </div>
                            
					
					<div class="col-sm-6">
                                
                                    <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount'); ?>
                                                 <?= form_input('tax_amount', $gift_card->tax_amount, 'class="form-control tip" id="tax_amount"  readonly'); ?>
                    
                                    </div>
                                
                            </div>
                            
					
					<div class="col-sm-6">
                                
                                    <div class="form-group"  >
                                        <?= lang('grand_total', 'grand_total'); ?>
                                                 <?= form_input('total_paid_amount', $gift_card->total_paid_amount, 'class="form-control tip" id="total_paid_amount"  readonly'); ?>
                    
                                    </div>
                                
                            </div>
                            
			
			
			</div>
            
                </div>
				<div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', $gift_card->note, 'class="form-control" id="note"'); ?>
            </div>

			</div>
			
			
			

        </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_gift_card', lang('edit_gift_card'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript">

var tax_rates = <?php echo json_encode($tax_rates); ?>;

    $(document).ready(function () {
        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
		var paid='<?=$gift_card->paid?>';
		if(paid==1)
		$('#div_paid_amount').slideDown();
	
        $('#customer').val('<?=$gift_card->customer_id?>').select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: "<?= admin_url('customers/getCustomer') ?>/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        if (data != null) {
                            callback(data[0]);
                        }
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });//.select2("val", "<?=$gift_card->customer_id?>");
        $('#genNo').click(function () {
            var no = generateCardNo();
            $(this).parent().parent('.input-group').children('input').val(no);
            return false;
        });
		
		
		$('#paid_amount').change(function(e) {
			
		calcTax();
			
    });
	
	$('#tax_rate').change(function(e) {
		calcTax();
			
    });
	
	
	function calcTax(){
		var amount=$('#paid_amount').val();
        var tax=0;
        var tax_rate = $('#tax_rate').val();
		
		
		$.each(tax_rates, function() {
			
                    if (this.id == tax_rate) {
						
                        if (this.type == 2) {
                            tax = formatDecimal(this.rate,12);
                        } else if (this.type == 1) {
                            tax = formatDecimal((parseFloat((amount)) * this.rate) / 100, 12);
                        }
                    }
                });
				$('#tax_amount').val(tax);
				$('#total_paid_amount').val(formatDecimal(parseFloat(amount) + parseFloat(tax), 2));
				
		
	}
    
		
		$(document).on('ifChecked', '#paid_2', function (event) {
            $('#div_paid_amount').slideDown();
        });
        $(document).on('ifChecked', '#paid_1', function (event) {
            $('#div_paid_amount').slideUp();
        });
		
		$(document).on('change', '.paid_by', function () {
            var p_val = $(this).val();
            $('#rpaidby').val(p_val);
            if (p_val == 'cash') {
                $('.pcheque_1').hide();
                $('.ptransfer_1').hide();
                $('.pcc_1').hide();
                $('.gc').hide();
                $('.pcash_1').show();
                $('#amount_1').focus();
            } else if (p_val == 'CC') {
                $('.pcheque_1').hide();
                $('.ptransfer_1').hide();
                $('.pcash_1').hide();
                $('.gc').hide();
                $('.pcc_1').show();
                $('#pcc_no_1').focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.ptransfer_1').hide();
                $('.gc').hide();
                $('.pcheque_1').show();
                $('#cheque_no_1').focus();
            } else if (p_val == 'bank_transfer') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
				$('.pcheque_1').hide();
				$('.gc').hide();
                $('.ptransfer_1').show();
               
            } else if (p_val == 'gift_card') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
				$('.pcheque_1').hide();
                $('.ptransfer_1').hide();
                $('.gc').show();
				$('#gift_card_no').focus();
            
               
            } else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.ptransfer_1').hide();
                $('.gc').hide();
            }
            
        });
        $('#pcc_no_1').change(function (e) {
            var pcc_no = $(this).val();
            localStorage.setItem('pcc_no_1', pcc_no);
            var CardType = null;
            var ccn1 = pcc_no.charAt(0);
            if (ccn1 == 4)
                CardType = 'Visa';
            else if (ccn1 == 5)
                CardType = 'MasterCard';
            else if (ccn1 == 3)
                CardType = 'Amex';
            else if (ccn1 == 6)
                CardType = 'Discover';
            else
                CardType = 'Mada';

            $('#pcc_type_1').select2("val", CardType);
        });
        
        
    });

</script>    