<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_advance_payment'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('carsquotes/edit_payment/' . $payment->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <?php if ($Owner || $Admin) {
            ?>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= lang('date', 'date'); ?>
                            <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($payment->date)), 'class="form-control datetime" id="date" required="required"'); ?>
                        </div>
                    </div>
                <?php
        } ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('reference_no', 'reference_no'); ?>
                        <?= form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $payment->reference_no), 'class="form-control tip" id="reference_no" required="required"'); ?>
                    </div>
                </div>

                <input type="hidden" value="<?php echo $payment->quote_id; ?>" name="quote_id"/>
            </div>
			
			
			<?php
			if(isset($payment->currency)){
				$currencyCode= $payment->currency;
				//$currencyRate= $inv->currency_rate;
				$currencyRate=$payment->currency_rate;
			
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}
		
			?>
			
            <div class="clearfix"></div>
            <div id="payments">

                <div class="well well-sm well_1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="payment">
                                    <div class="form-group">
                                        <?= lang('amount', 'amount_1')." (".$currencyCode.")"; ?>
                                        <input name="amount-paid"
                                               value="<?= $this->sma->formatDecimal($payment->amount); ?>" type="text"
                                               id="amount_1" class="pa form-control kb-pad amount"/>
                                    </div>
                                </div>
                            </div>
                            <?php 
							if($currencyCode!=$Settings->default_currency){
							?>
                            <div class="col-sm-4">
                            <?php 
							}else{
							?>
                            <div class="col-sm-4" style="display:none">
                            <?php 
							}
							?>
                                <div class="payment">
                                    <div class="form-group">
                                        <?= lang('amount', 'amount_1')." (".$Settings->default_currency.")"; ?>
                                        <input name="local_curr_amount" type="text" id="local_curr_amount"
                                               value="<?= $this->sma->formatDecimal(($this->sma->formatDecimal($payment->amount) * $this->sma->formatDecimal($currencyRate))) ?>"
                                               class="pa form-control kb-pad amount" readonly />
                                    </div>
                                </div>
                            </div>
                            <?php 
							if($currencyCode!=$Settings->default_currency){
							?>
                            <div class="col-sm-4">
                            <?php 
							}else{
							?>
                            <div class="col-sm-4" style="display:none">
                            <?php 
							}
							?>
                            		<div class="form-group">
										<?= lang('Payment_currency', 'Payment_currency'); ?>
										<?php
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
											echo form_dropdown('currency', $curr, $currencyCode, 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>
									</div>
									
								<?php 
							if($currencyCode!=$Settings->default_currency){
							?>
                            <div class="col-sm-4">
                            <?php 
							}else{
							?>
                            <div class="col-sm-4" style="display:none">
                            <?php 
							}
							?>
                            <div class="payment">
                                    <div class="form-group">
                                        <?= lang('Exchange_rate', 'Exchange_rate'); ?>
                                        <input name="exchange_rate" id="exchange_rate" type="text" 
                                               value="<?= $currencyRate ?>"
                                               class="pa form-control kb-pad amount" required="required" />
                                    </div>
                                </div>
                            </div>
						<div class="col-sm-4">
                                <div class="form-group">
                                    <?= lang('paying_by', 'paid_by_1'); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by">
                                       <?= $Settings->accounting ? $this->sma->paid_opts_accounts($payment->paid_by,true) : $this->sma->paid_opts($payment->paid_by,true); ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="clearfix"></div>
						<div class="form-group gc" style="display: none;">
                            <?= lang('gift_card_no', 'gift_card_no'); ?>
                            <input name="gift_card_no" type="text" id="gift_card_no" class="pa form-control kb-pad"/>

                            <div id="gc_details"></div>
								<?php
							if($Settings->accounting){
						?>
						<div class="col-md-6" >
                            <div class="form-group"><?= lang('Account', 'boxes_account_1'); ?>
							<?php
                $acc= [];
				
				$acc[$accounts_gift_card_payments->id] = $accounts_gift_card_payments->accountName;
                
				echo form_dropdown('cgift_card_account', $acc, '', 'class="form-control tip" id="gift_card_account" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                
				?>
                            </div>
                        </div>
						<?php
						}
						?>
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
				 $acc[$accounts_main_boxes_acct->id] = $accounts_main_boxes_acct->accountName;
                
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
                                        <input name="pcc_no" value="<?= $payment->cc_no; ?>" type="text" id="pcc_no_1"
                                               class="form-control" placeholder="<?= lang('cc_no') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input name="pcc_holder" value="<?= $payment->cc_holder; ?>" type="text"
                                               id="pcc_holder_1" class="form-control"
                                               placeholder="<?= lang('cc_holder') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                                placeholder="<?= lang('card_type') ?>">
                                            <option
                                                value="Visa"<?= $payment->cc_type == 'Mada' ? ' checked="checcked"' : '' ?>><?= lang('Mada'); ?></option>
                                            <option
                                                value="Visa"<?= $payment->cc_type == 'Visa' ? ' checked="checcked"' : '' ?>><?= lang('Visa'); ?></option>
                                            <option
                                                value="MasterCard"<?= $payment->cc_type == 'MasterCard' ? ' checked="checcked"' : '' ?>><?= lang('MasterCard'); ?></option>
                                            <option
                                                value="Amex"<?= $payment->cc_type == 'Amex' ? ' checked="checcked"' : '' ?>><?= lang('Amex'); ?></option>
                                            <option
                                                value="Discover"<?= $payment->cc_type == 'Discover' ? ' checked="checcked"' : '' ?>><?= lang('Discover'); ?></option>
                                        </select>
                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?= lang('card_type') ?>" />-->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="pcc_month" value="<?= $payment->cc_month; ?>" type="text"
                                               id="pcc_month_1" class="form-control"
                                               placeholder="<?= lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <input name="pcc_year" value="<?= $payment->cc_year; ?>" type="text"
                                               id="pcc_year_1" class="form-control" placeholder="<?= lang('year') ?>"/>
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
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
                
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
                                <input name="cheque_no" value="<?= $payment->cheque_no; ?>" type="text" id="cheque_no_1"
                                       class="form-control cheque_no"/>
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
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
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
                $acc[$accounts_main_banks_acct->id] = $accounts_main_banks_acct->accountName;
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
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>

            <div class="form-group">
				<label for="paied_1">
					<?php echo form_radio('retrieved', '0', $payment->retrieved==0 ? '1' : '0', "id='paid_1'"); ?> <?= lang('Non_retrieved'); ?>
				</label>

				<label for="paied_2">
					<?php echo form_radio('retrieved', '1', $payment->retrieved==1 ? '1' : '0', "id='paid_2'"); ?> <?= lang('Retrieved'); ?>
				</label>
			</div>


            <div class="form-group">
                <?= lang('attachment', 'attachment') ?>
                <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>

            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $payment->note), 'class="form-control" id="note"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_payment', lang('edit_payment'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
</script>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
		
		var default_currency ='<?=$Settings->default_currency ?>';
		var currencyCode ='<?=$currencyCode ?>';
		var currencyRate ='<?=$currencyRate ?>';
		var amount =$('#amount_1').val();
		
		$('#slcurrency').change(function(e) {
        var slcurrency = $(this).val();
		
		
		try{
		$.ajax({
                type: 'get',
                async: false,
                url: site.base_url + 'carsquotes/getCurrencyByCode/' + slcurrency,
                dataType: 'json',
                data: { code : slcurrency},
                success: function(data, page) {
					//data.co_type
					//alert(data.rate);
					amount =$('#amount_1').val();
		
					if(data.code!=default_currency){
					$('#exchange_rate').val(data.rate);
					$('#local_curr_amount').val(formatDecimal(amount*data.rate,2));
					}else{
					//$('#exchange_rate').val(data.rate);
					}
				
					
					
					/*if(data.code==default_currency)
					$('#exchange_rate').val(data.rate);
				else
				*/
					
					
                },
            });
			
		}catch(err){alert(err);}
					
					//alert();
		
		
    });
	$('#exchange_rate').change(function(e) {
		try{
        amount =$('#amount_1').val();
		var exchange_rate = $(this).val();
		
		$('#local_curr_amount').val(formatDecimal(amount*exchange_rate,2));
		}catch(err){}		
	});	
    $('#amount_1').change(function(e) {
		try{
        exchange_rate =$('#exchange_rate').val();
		amount = $(this).val();
		$('#local_curr_amount').val(formatDecimal(amount*exchange_rate,2));
		}catch(err){}		
	});	
        
        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
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
        var p_val = '<?=$payment->paid_by?>';
        localStorage.setItem('paid_by', p_val);
        if (p_val == 'cash') {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').show();
            $('#amount_1').focus();
        } else if (p_val == 'CC') {
            $('.pcheque_1').hide();
            $('.pcash_1').hide();
            $('.pcc_1').show();
            $('#pcc_no_1').focus();
        } else if (p_val == 'Cheque') {
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.pcheque_1').show();
            $('#cheque_no_1').focus();
        } else {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').hide();
        }
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
        $('#paid_by_1').select2("val", '<?=$payment->paid_by?>');
    });
</script>
