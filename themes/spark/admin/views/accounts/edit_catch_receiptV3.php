<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
<script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>
<!--<link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet' type='text/css'> style="height:110px;font-size:36pt; font-family:Orbitron;" -->
<script type="text/javascript" charset="UTF-8">
var tax_rates = <?php echo json_encode($tax_rates); ?>;

        var tax_rates = <?php echo json_encode($tax_rates); ?>;
		function search_select(){
	$('select').select2({
  matcher: function(term, text, option) {
    return text.toUpperCase().indexOf(term.toUpperCase())>=0 || option.val().toUpperCase().indexOf(term.toUpperCase())>=0;
  }
});

}

	$(document).ready(function () {
		search_select();

        var default_currency ='<?=$Settings->default_currency ?>';
        if($('#slcurrency').val()==default_currency)
        $(".div_payment").css("display", "none");


		var currencyCode ='<?=$currencyCode ?>';
		var currencyRate ='<?=$currencyRate ?>';


        var amount =$('#amount').val();


		var costCountDebit  = 0;
		$('button#addCostCenterDebit').click(function(){

			var divOption    = '<div class="row" id="costDebitRow'+costCountDebit+'">';
			var selectOption = '<div class="col-6" style="padding-left:30px;padding-right:25px;"><select class="form-control"  id="costCenterDebitId'+costCountDebit+'" name="costCenterDebitId[]" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control" placeholder="<?= lang('Amount') ?>" id="costCenterDebitAmount'+costCountDebit+'" name="costCenterDebitAmount[]" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#CostCentersDebit').append(divOption);
			$('#costDebitRow'+costCountDebit).append(selectOption);
			$('#costDebitRow'+costCountDebit).append(inputOption);
			$('#costDebitRow'+costCountDebit).append(buttonOption);
			costCountDebit += 1;

		});


				var costCountCredit  = 0;
		$('button#addCostCenterCredit').click(function(){
			var divOption    = '<div class="row" id="costCreditRow'+costCountCredit+'">';
			var selectOption = '<div class="col-6" style="padding-left:30px;padding-right:25px;"><select class="form-control"  id="costCenterCreditId'+costCountCredit+'" name="costCenterCreditId[]" data-toggle="tooltip" data-placement="left" title="Credit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control" placeholder="<?= lang('Amount') ?>" id="costCenterCreditAmount'+costCountCredit+'" name="costCenterCreditAmount[]" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#CostCentersCredit').append(divOption);
			$('#costCreditRow'+costCountCredit).append(selectOption);
			$('#costCreditRow'+costCountCredit).append(inputOption);
			$('#costCreditRow'+costCountCredit).append(buttonOption);
			costCountCredit += 1;

		});



      var p_val = $('.paid_by').val();
	  if (p_val == 'cash') {
                $('.pcheque_1').hide();
                $('.ptransfer_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').show();
                $('#amount_1').focus();
            } else if (p_val == 'CC') {
                $('.pcheque_1').hide();
                $('.ptransfer_1').hide();
                $('.pcash_1').hide();
                $('.pcc_1').show();
                $('#pcc_no_1').focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.ptransfer_1').hide();
                $('.pcheque_1').show();
                $('#cheque_no_1').focus();
            } else if (p_val == 'bank_transfer') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
				$('.pcheque_1').hide();
                $('.ptransfer_1').show();

            } else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
            }
            if (p_val == 'gift_card') {
                $('.gc').show();
                $('#gift_card_no').focus();
            } else {
                $('.gc').hide();
            }
           //alert(p_val);
        $(document).on('change', '.paid_by', function () {

            var p_val = $(this).val();
            $('#rpaidby').val(p_val);
            if (p_val == 'cash') {
                $('.pcheque_1').hide();
                $('.ptransfer_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').show();
                $('#amount_1').focus();
            } else if (p_val == 'CC') {
                $('.pcheque_1').hide();
                $('.ptransfer_1').hide();
                $('.pcash_1').hide();
                $('.pcc_1').show();
                $('#pcc_no_1').focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.ptransfer_1').hide();
                $('.pcheque_1').show();
                $('#cheque_no_1').focus();
            } else if (p_val == 'bank_transfer') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
				$('.pcheque_1').hide();
                $('.ptransfer_1').show();

            } else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
            }
            if (p_val == 'gift_card') {
                $('.gc').show();
                $('#gift_card_no').focus();
            } else {
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
                CardType = 'Visa';

            $('#pcc_type_1').select2("val", CardType);
        });
        $("#date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());

		$('#the_oldest').change(function(e) {
            if ($(this).val() != 2) {
                $('#inv_no').val('');
                $('.inv_no').hide();
			} else {
                $('.inv_no').show();
			}
        });

			var the_oldest ='<?= $receipt->the_oldest ?>';
		    if (the_oldest != 2) {
                $('#inv_no').val('');
                $('.inv_no').hide();
				if(the_oldest == 1){
					$('#the_oldest').select2('readonly', true);
					$("#edit_catch_receipt").prop('disabled',true);
				}else
					$('#the_oldest').select2('readonly', false);
            } else {
                $('.inv_no').show();
				$('#the_oldest').select2('readonly', true);
            }



		$('#exchange_rate').change(function(e) {

try{
	var amount =$('#amount').val();
	var exchange_rate = $('#exchange_rate').val();

$('#local_curr_amount').val(formatDecimal(amount*exchange_rate,2));
}catch(err){}

var amount=$('#amount').val();
var local_curr_amount=$('#local_curr_amount').val();
var tax=0;
var equivalent_tax_amount=0;
var tax_rate = $('#tax_rate').val();


$.each(tax_rates, function() {

            if (this.id == tax_rate) {

                if (this.type == 2) {
                    tax = formatDecimal(this.rate,12);
                } else if (this.type == 1) {
                    tax = formatDecimal((parseFloat((amount)) * this.rate) / 100, 12);
                    equivalent_tax_amount = formatDecimal((parseFloat((local_curr_amount)) * this.rate) / 100, 12);
                }
            }
        });
        $('#tax_amount').val(tax);
        $('#total_amount').val(formatDecimal(parseFloat(amount) + parseFloat(tax), 2));
        $('#equivalent_tax_amount').val(equivalent_tax_amount);
        $('#equivalent_total_amount').val(formatDecimal(parseFloat(local_curr_amount) + parseFloat(equivalent_tax_amount), 2));

//calcTax();
});


		$('#amount').change(function(e) {
		calcTax();

    });

	$('#tax_rate').change(function(e) {
		calcTax();

    });
    $('#slcurrency').change(function(e) {
        var default_currency ='<?=$Settings->default_currency ?>';
        if($(this).val()==default_currency)
            $(".div_payment").css("display", "none");
        else
            $(".div_payment").css("display", "block");


        calcTax();

        //alert();


});


function calcTax(){


var slcurrency = $('#slcurrency').val();


try{
$.ajax({
        type: 'get',
        async: false,
        url: site.base_url + 'sales/getCurrencyByCode/' + slcurrency,
        dataType: 'json',
        data: { code : slcurrency},
        success: function(data, page) {
            //data.co_type
            //alert(data.rate);
            amount =$('#amount').val();

            //if(data.code!=default_currency){
            //$('#exchange_rate').val(data.rate);
            //$('#local_curr_amount').val(formatDecimal(amount*data.rate,2));
            //}else{
            //$('#exchange_rate').val(data.rate);
            //}



            /*if(data.code==default_currency)
            $('#exchange_rate').val(data.rate);
        else
        */


        },
    });

}catch(err){alert(err);}

var amount=$('#amount').val();
var local_curr_amount=$('#local_curr_amount').val();
var tax=0;
var equivalent_tax_amount=0;
var tax_rate = $('#tax_rate').val();


$.each(tax_rates, function() {

            if (this.id == tax_rate) {

                if (this.type == 2) {
                    tax = formatDecimal(this.rate,12);
                } else if (this.type == 1) {
                    tax = formatDecimal((parseFloat((amount)) * this.rate) / 100, 12);
                    equivalent_tax_amount = formatDecimal((parseFloat((local_curr_amount)) * this.rate) / 100, 12);
                }
            }
        });
        $('#tax_amount').val(tax);
        $('#total_amount').val(formatDecimal(parseFloat(amount) + parseFloat(tax), 2));
        $('#equivalent_tax_amount').val(equivalent_tax_amount);
        $('#equivalent_total_amount').val(formatDecimal(parseFloat(local_curr_amount) + parseFloat(equivalent_tax_amount), 2));


}


    });
</script>



<div class="modal-dialog modal-xl">
    <div class="modal-content">
	<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_catch_receipt'); ?></h4>
        </div>
       <div class="modal-body">







                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('accounts/edit_catch_receiptV3' . ($id ? '/' . $id : ''), $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">

					<div class="col-sm-2" >
                    <div class="form-group">
										<?= lang('currency', 'currency'); ?>
										<?php
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
											echo form_dropdown('currency', $curr, $receipt->currency_code, 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>

                    </div>

					<div class="col-sm-3" style="display:none;">

                                    <div class="form-group"  >
                                        <?= lang('amount', 'amount'); ?>
                                                 <?= form_input('amount', $this->sma->formatDecimal($receipt->amount), 'class="form-control tip" id="amount"  required="required"'); ?>

                                    </div>

                            </div>


					<div class="col-sm-2">
                    <div class="form-group">
                        <?= lang('Bond_No', 'Bond_No'); ?>
                        <?= form_input('bond_no', $receipt->bond_no, 'class="form-control tip" dir="ltr" id="bond_no"'); ?>
                    </div>
                </div>



                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?= lang('date', 'date'); ?>
                                    <?php echo form_input('date', $this->sma->hrld($receipt->date), 'readonly class="form-control input-tip datetime" id="catchReceiptDate" '); ?>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?= lang('reference_no', 'reference_no'); ?>
                                    <?= form_input('reference_no', $receipt->reference_no, 'class="form-control tip" id="reference_no"'); ?>
                                </div>
                            </div>


				<div class="col-sm-3" style="display:none;">

                                    <div class="form-group"  >
                                        <?= lang('VAT_rate', 'VAT_rate'); ?>
										<?php
                                    $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('tax_rate', $tr, $receipt->tax_rate, 'id="tax_rate" data-placeholder="' . lang('select') . ' ' . lang('VAT_rate') . '" class="form-control input-tip select" style="width:100%;"');
												?>

                                    </div>

                            </div>


					<div class="col-sm-3" style="display:none;">

                                    <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount'); ?>
                                                 <?= form_input('tax_amount', $this->sma->formatDecimal($receipt->tax_amount), 'class="form-control tip" id="tax_amount"  readonly'); ?>

                                    </div>

                            </div>


					<div class="col-sm-2">

                                    <div class="form-group"  >
                                        <?= lang('Amount', 'grand_total'); ?>
                                                 <?= form_input('total_amount', $this->sma->formatDecimal($receipt->total_amount), 'class="form-control tip" id="total_amount"  readonly'); ?>

                                    </div>

                            </div>









                <div class="form-group col-md-12" id="debitAccounts">
                <div class="alert alert-info w-50"> <b><?= lang('Account_information_received_from')?></b></div>
				<?php

				//if ($account_details->costCenterId) {


						$entry = (array) $entry;
						$entry['entryDebitAccount'] = json_decode($receipt->entryDebitAccount);
						$entry['entryDebitBalance'] = json_decode($receipt->entryDebitBalance);
						$entry['entryDebitDescription'] = json_decode($receipt->entryDebitDescription);
						$entry['costCenterDebitId'] = json_decode($receipt->costCenterDebitId);
						$entry['costCenterDebitAmount'] = json_decode($receipt->costCenterDebitAmount);


					//print_r($entry['costCenterDebitId']);
						$dt_index=0;
						$cr_row_index=1;
						$DebitCountTemp=0;
							foreach ($entry['entryDebitAccount'] as $Debit){

								if($dt_index==0){

								?>

						<div class="row" id="<?= 'DebitRow'.$cr_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]" id="entryDebitAccount" data-toggle="tooltip" data-placement="left" title="Debit Account">
								<?php

								foreach ($fromAccountIDs as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['accountID']} - {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['accountID']} - {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryDebitBalance" placeholder="<?= lang('Amount') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" value="<?= $entry['entryDebitBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>

						<div class="col-3">
							<input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" value="<?= $entry['entryDebitDescription'][$dt_index] ?>" type="text" />
						</div>

						<div class="col-2">
							<button id="addDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_account') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $cr_row_index ?>" >
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
																<div class="row" id="<?= 'debit'.$cr_row_index ?>">
			</div>


					<?php
					if($entry['costCenterDebitId'][$dt_index]){
						$dt_cost_index=0;

						foreach ($entry['costCenterDebitId'][$dt_index] as $costDebit){

						?>
						<div class="row" id="<?= 'debit'.$cr_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterDebitId"  name="<?= 'costCenterDebitId_0[]' ?>"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costDebit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterDebitAmount_0[]' ?>" id="costCenterDebitAmount" value="<?= $entry['costCenterDebitAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'debitRow'.$cr_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
						//$cr_row_index++;
						}


					}



								}else{
									?>
									<div class="row" id="<?= 'debitRow'.$cr_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]" id="entryDebitAccount" data-toggle="tooltip" data-placement="left" title="Debit Account">
								<?php
								foreach ($fromAccountIDs as $account) {
									$account = (array) $account;
									if($Debit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['accountID']} - {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['accountID']} - {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryDebitBalance" placeholder="<?= lang('Amount') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" value="<?= $entry['entryDebitBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>

						<div class="col-3">
							<input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" value="<?= $entry['entryDebitDescription'][$dt_index] ?>" type="text" />
						</div>

						<div class="col-2">
							<button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="dtRemove('<?= 'debitRow'.$cr_row_index ?>')"><?= lang('Remove') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $cr_row_index ?>" >
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>

																		<div class="row" id="<?= 'debit'.$cr_row_index ?>">
			</div>

					<?php
					if($entry['costCenterDebitId'][$dt_index]){
						//$cr_row_index++;
						$dt_cost_index=0;

						foreach ($entry['costCenterDebitId'][$dt_index] as $costDebit){

						?>
						<div class="row" id="<?= 'debit'.$cr_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterDebitId"  name="<?= 'costCenterDebitId_'.$debitCountTemp.'[]' ?>"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costDebit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterDebitAmount_'.$debitCountTemp.'[]' ?>" id="costCenterDebitAmount" value="<?= $entry['costCenterDebitAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'debitRow'.$cr_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
}


					}

								}

								$dt_index++;
								$debitCountTemp++;
						        $cr_row_index++;




							}


					?>


				</div>






                            <div class="col-lg-12 div_payment">
                        <div class="alert alert-info w-50" id="alertVisibleIn"> <b><?= lang('Local_currency_equivalent')?></b></div>
                        <div class="col-sm-3">
                                <div class="payment">
                                    <div class="form-group">
                                        <?= lang('Exchange_rate', 'Exchange_rate'); ?>
                                        <input name="exchange_rate" id="exchange_rate" type="text"
                                               value="<?= $receipt->currency_rate ?>"
                                               class="pa form-control kb-pad amount" required="required" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                        <div class="payment">
                                    <div class="form-group">
                                        <?= lang('Amount', 'amount_1')." (".$Settings->default_currency.")"; ?>
                                        <input name="local_curr_amount" type="text" id="local_curr_amount"
                                               value="<?= $this->sma->formatDecimal($receipt->equivalent_amount) ?>"
                                               class="pa form-control kb-pad amount" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3" style="display:none;">
                            <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount')." (".$Settings->default_currency.")"; ?>
                                                 <?= form_input('equivalent_tax_amount', $this->sma->formatDecimal($receipt->equivalent_tax_amount), 'class="form-control tip" id="equivalent_tax_amount"  readonly'); ?>

                                    </div>
                            </div>
                            <div class="col-sm-3" style="display:none;">
                            <div class="form-group"  >
                                        <?= lang('grand_total', 'grand_total')." (".$Settings->default_currency.")"; ?>
                                                 <?= form_input('equivalent_total_amount', $this->sma->formatDecimal($receipt->equivalent_total_amount), 'class="form-control tip" id="equivalent_total_amount"  readonly'); ?>

                                    </div>
                            </div>
                            </div>













                        <div class="clearfix"></div>





                    <div class="col-md-12">
                        <div class="row">
                            <!--<div class="col-sm-4">

                                    <div class="form-group all">
				<?= lang('The_recipient_account', 'The_recipient_account'); ?>

                <select name="toAccountID" class="form-control" id="toAccountID"   required="required" >
                <?php  if (!empty($toAccountIDs)) {?>

						<?php
                        foreach ($toAccountIDs as $toAccountID) {

							if($receipt->toAccountID==$toAccountID->id){
								?>
                         <option value="<?=$toAccountID->id?>" selected ><?=$toAccountID->accountID.' - '.$toAccountID->accountName?></option>

							<?php

							}else{

							?>
                         <option value="<?=$toAccountID->id?>"  ><?=$toAccountID->accountID.' - '.$toAccountID->accountName?></option>

							<?php } } } ?>
                </select>

            </div>

                            </div>-->





				<div class="col-sm-4">
					<div class="form-group">
                		<?= lang('attachment', 'attachment') ?>
                		<input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            		</div>
				</div>


				<div class="col-sm-4">
                    <div class="form-group">
                        <?= lang('Recipient_name', 'Recipient_name'); ?>
                        <?= form_input('receipt_name', $receipt->receipt_name, 'class="form-control tip" id="receipt_name" '); ?>
                    </div>
                </div>

				<div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="salesman_id"><?= lang("Salesman"); ?></label>
                        <?php
                        $salM[""] = lang('select').' '.lang('Salesman');
                        foreach ($allSalesMan as $salesman) {
                            $salM[$salesman->id] = $salesman->company;
                        }
                        echo form_dropdown('salesman_id', $salM, $receipt->salesman_id, 'class="form-control" id="salesman_id" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Salesman") . '"');
                        ?>
                    </div>
                </div>

				<div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Payment_of_sales_invoices', 'Payment_of_sales_invoices'); ?>
                        <?php $the_oldest_options =  [0 => lang('no'), 2 => '( '.lang('Custom_invoices').' ) e.g. 100,101,102', 1 => lang('yes').' ( '.lang('All_in_order_of_oldest').' )']; ?>
                        <?= form_dropdown('the_oldest', $the_oldest_options, $receipt->the_oldest, 'class="form-control tip" id="the_oldest" '); ?>
                    </div>
                </div>

				<div class="col-sm-4 inv_no">
                    <div class="form-group">
                        <?= lang('Sales_invoice_number', 'Sales_invoice_number'); ?>
						<?php
						if($receipt->inv_no){
							?>
                        	<?= form_input('inv_no', $receipt->inv_no, 'readonly class="form-control tip" id="inv_no" '); ?>
						<?php
						}else{
							?>
							<?= form_input('inv_no', $receipt->inv_no, 'class="form-control tip" id="inv_no" '); ?>
						<?php
						}
						?>

                    </div>
                </div>








                        </div>
                    </div>



                    <div class="form-group col-md-12" id="creditAccounts">
                    <div class="alert alert-info w-50"> <b><?= lang('Accounts_receivable_information')?></b></div>
				<?php

				//if ($account_details->costCenterId) {


						$entry = (array) $entry;
						$entry['entryCreditAccount'] = json_decode($receipt->entryCreditAccount);
						$entry['entryCreditBalance'] = json_decode($receipt->entryCreditBalance);
						$entry['entryCreditDescription'] = json_decode($receipt->entryCreditDescription);
						$entry['costCenterCreditId'] = json_decode($receipt->costCenterCreditId);
						$entry['costCenterCreditAmount'] = json_decode($receipt->costCenterCreditAmount);


					//print_r($entry['costCenterCreditId']);
						$dt_index=0;
						$dt_row_index=1;
						$creditCountTemp=0;
							foreach ($entry['entryCreditAccount'] as $Credit){

								if($dt_index==0){
								?>

						<div class="row" id="<?= 'creditRow'.$dt_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]" id="entryCreditAccount" data-toggle="tooltip" data-placement="left" title="Credit Account">
								<?php
								foreach ($toAccountIDs as $account) {
									$account = (array) $account;
									if($Credit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['accountID']} - {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['accountID']} - {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryCreditBalance" placeholder="<?= lang('Amount') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" value="<?= $entry['entryCreditBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>

						<div class="col-3">
							<input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" value="<?= $entry['entryCreditDescription'][$dt_index] ?>" type="text" />
						</div>

						<div class="col-2">
							<button id="addCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_account') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $dt_row_index ?>" >
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
																<div class="row" id="<?= 'credit'.$dt_row_index ?>">
			</div>


					<?php
					if($entry['costCenterCreditId'][$dt_index]){
						$dt_cost_index=0;

						foreach ($entry['costCenterCreditId'][$dt_index] as $costCredit){

						?>
						<div class="row" id="<?= 'credit'.$dt_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterCreditId"  name="<?= 'costCenterCreditId_0[]' ?>"  id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Credit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costCredit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterCreditAmount_0[]' ?>" id="costCenterCreditAmount" value="<?= $entry['costCenterCreditAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'creditRow'.$dt_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
						//$dt_row_index++;
						}


					}



								}else{
									?>
									<div class="row" id="<?= 'creditRow'.$dt_row_index ?>" >

						<div class="col-3">




							<select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]" id="entryCreditAccount" data-toggle="tooltip" data-placement="left" title="Credit Account">
								<?php
								foreach ($toAccountIDs as $account) {
									$account = (array) $account;
									if($Credit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['accountID']} - {$account['accountName']}</option>
									";
									}else{
										echo "
									<option value='{$account['id']}'> {$account['accountID']} - {$account['accountName']}</option>
									";
									}

								}
								?>
							</select>
						</div>
						<div class="col-2" dir="ltr">
							<input class="form-control entryCreditBalance" placeholder="<?= lang('Amount') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" value="<?= $entry['entryCreditBalance'][$dt_index] ?>" type="number" min="0" step=".01" required />
						</div>

						<div class="col-3">
							<input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" value="<?= $entry['entryCreditDescription'][$dt_index] ?>" type="text" />
						</div>

						<div class="col-2">
							<button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="dtRemove('<?= 'creditRow'.$dt_row_index ?>')"><?= lang('Remove') ?></button>
						</div>
						<div  class="col-2" data-item-id="<?= $dt_row_index ?>" >
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>

																		<div class="row" id="<?= 'credit'.$dt_row_index ?>">
			</div>

					<?php
					if($entry['costCenterCreditId'][$dt_index]){
						//$dt_row_index++;
						$dt_cost_index=0;

						foreach ($entry['costCenterCreditId'][$dt_index] as $costCredit){

						?>
						<div class="row" id="<?= 'credit'.$dt_row_index ?>">
			<div class="col-5" ><select class="form-control costCenterCreditId"  name="<?= 'costCenterCreditId_'.$creditCountTemp.'[]' ?>"  id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Credit Account">
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				if($costCredit==$account['id']){
									echo "
									<option value='{$account['id']}'  selected> {$account['costName']}</option>
									";
									}else{

				echo "
				<option value='{$account['id']}'  > {$account['costName']}</option>
									";
									}
			}
			?>
			</select></div>
			<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="<?= 'costCenterCreditAmount_'.$creditCountTemp.'[]' ?>" id="costCenterCreditAmount" value="<?= $entry['costCenterCreditAmount'][$dt_index][$dt_cost_index] ?>" type="number" step=".01" required /></div>
			<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline" onclick="costRemove('<?= 'creditRow'.$dt_row_index ?>')"><?= lang('Remove') ?></button></div></div>

			<?php
						$dt_cost_index++;
}


					}

								}

								$dt_index++;
								$creditCountTemp++;
						        $dt_row_index++;




							}


					?>


				</div>


                                <!--

                        <div class="form-group" id="CostCentersCredit">
					<div class="row">

						<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
				</div>



						<?php

				if ($receipt->costCenterCreditId) {


						$entry = (array) $entry;
						$entry['costCenterCreditId'] = json_decode($receipt->costCenterCreditId);
						$entry['costCenterCreditAmount'] = json_decode($receipt->costCenterCreditAmount);


						$cost_index=0;
							foreach ($entry['costCenterCreditId'] as $cost_center){



								 ?>
								 <script>
								 $("#addCostCenterCredit" ).click()
								 $('#costCenterCreditId'+'<?= $cost_index; ?>').val('<?= $cost_center; ?>')
								 $('#costCenterCreditAmount'+'<?= $cost_index; ?>').val('<?= $entry["costCenterCreditAmount"][$cost_index]; ?>')
								 </script>
								 <?php

								$cost_index++;


							}

					} ?>

                -->

				<div class="clearfix"></div>

            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', $receipt->note, 'class="form-control content" id="note"'); ?>
            </div>




                <input type="hidden" name="jsonArr_entryCreditAccount" id="jsonArr_entryCreditAccount"  value=''/>
				<input type="hidden" name="jsonArr_entryCreditBalance" id="jsonArr_entryCreditBalance"  value=''/>
				<input type="hidden" name="jsonArr_entryCreditDescription" id="jsonArr_entryCreditDescription"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditId_0" id="jsonArr_costCenterCreditId_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditAmount_0" id="jsonArr_costCenterCreditAmount_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditId" id="jsonArr_costCenterCreditId"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditAmount" id="jsonArr_costCenterCreditAmount"  value=''/>

                <input type="hidden" name="jsonArr_entryDebitAccount" id="jsonArr_entryDebitAccount"  value=''/>
				<input type="hidden" name="jsonArr_entryDebitBalance" id="jsonArr_entryDebitBalance"  value=''/>
				<input type="hidden" name="jsonArr_entryDebitDescription" id="jsonArr_entryDebitDescription"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitId_0" id="jsonArr_costCenterDebitId_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitAmount_0" id="jsonArr_costCenterDebitAmount_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitId" id="jsonArr_costCenterDebitId"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitAmount" id="jsonArr_costCenterDebitAmount"  value=''/>


                            <div class="clearfix"></div>


                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_catch_receipt', lang('submit'), 'id="edit_catch_receipt" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>


<script>
   // $('#rpaidby').val(p_val);
   var dt=0;
   var debitCount  = <?= $dt_row_index ?>;
		var debitCountTemp  = <?= $debitCountTemp ?>;
		var costdebitCount  = 0;

        var cr=0;
   var creditCount  = <?= $cr_row_index ?>;
		var creditCountTemp  = <?= $creditCountTemp ?>;
		var costcreditCount  = 0;
		var the_oldest ='<?= $receipt->the_oldest ?>';
   function getDebit(){
    if(cr==0)
    cr=$("#amount").val();
	var p = ($(".entryDebitBalance"));
		//alert(p.length);
		dt=0;
		try{
jQuery.each(p, function(i, val) {
	dt=parseFloat(dt)+parseFloat(val.value);
  //alert(val.value+" "+i);
});
		}catch(err){}

	$("#amount").val(formatDecimal(dt)).trigger('change');

	if(formatDecimal(dt) >0 && formatDecimal(dt)==formatDecimal(cr)){
		if(the_oldest!=1)
		$("#edit_catch_receipt").prop('disabled',false);
	}else{
		$("#edit_catch_receipt").prop('disabled',true);
	}


}

function getCredit(){
    if(dt==0)
    dt=$("#amount").val();
	var p = ($(".entryCreditBalance"));
		//alert(p.length);
		cr=0;
		try{
jQuery.each(p, function(i, val) {
	cr=parseFloat(cr)+parseFloat(val.value);
  //alert(val.value+" "+i);
});
		}catch(err){}

	//$("#amount").val(formatDecimal(dt)).trigger('change');

	if(formatDecimal(cr) >0 && formatDecimal(dt)==formatDecimal(cr)){
		if(the_oldest!=1)
		$("#edit_catch_receipt").prop('disabled',false);
	}else{
		$("#edit_catch_receipt").prop('disabled',true);
	}

}
function costRemove(id){


$('#'+id).remove();
}

function dtRemove(id){
debitCountTemp--;

				$('#'+id).remove();
				getDebit();
}

function crRemove(id){
creditCountTemp--;

				$('#'+id).remove();
				getCredit();
}

   $(document).ready(function () {
    $('.paid_by').val('<?= $receipt->paid_by; ?>').trigger('change');

    $(document).on('click', '#edit_catch_receipt', function(e) {

var formData_entryCreditAccount = JSON.stringify($(".entryCreditAccount").serializeArray());
$('#jsonArr_entryCreditAccount').val(formData_entryCreditAccount);
var formData_entryCreditBalance = JSON.stringify($(".entryCreditBalance").serializeArray());
$('#jsonArr_entryCreditBalance').val(formData_entryCreditBalance);
var formData_entryCreditDescription = JSON.stringify($(".entryCreditDescription").serializeArray());
	$('#jsonArr_entryCreditDescription').val(formData_entryCreditDescription);


var formData_costCenterCreditId_0 = JSON.stringify($(".costCenterCreditId_0").serializeArray());
$('#jsonArr_costCenterCreditId_0').val(formData_costCenterCreditId_0);
var formData_costCenterCreditAmount_0 = JSON.stringify($(".costCenterCreditAmount_0").serializeArray());
$('#jsonArr_costCenterCreditAmount_0').val(formData_costCenterCreditAmount_0);

var formData_costCenterCreditId = JSON.stringify($(".costCenterCreditId").serializeArray());
$('#jsonArr_costCenterCreditId').val(formData_costCenterCreditId);
var formData_costCenterCreditAmount = JSON.stringify($(".costCenterCreditAmount").serializeArray());
$('#jsonArr_costCenterCreditAmount').val(formData_costCenterCreditAmount);



var formData_entryDebitAccount = JSON.stringify($(".entryDebitAccount").serializeArray());
		$('#jsonArr_entryDebitAccount').val(formData_entryDebitAccount);
		var formData_entryDebitBalance = JSON.stringify($(".entryDebitBalance").serializeArray());
		$('#jsonArr_entryDebitBalance').val(formData_entryDebitBalance);
		var formData_entryDebitDescription = JSON.stringify($(".entryDebitDescription").serializeArray());
		$('#jsonArr_entryDebitDescription').val(formData_entryDebitDescription);


		var formData_costCenterDebitId_0 = JSON.stringify($(".costCenterDebitId_0").serializeArray());
		$('#jsonArr_costCenterDebitId_0').val(formData_costCenterDebitId_0);
		var formData_costCenterDebitAmount_0 = JSON.stringify($(".costCenterDebitAmount_0").serializeArray());
		$('#jsonArr_costCenterDebitAmount_0').val(formData_costCenterDebitAmount_0);

		var formData_costCenterDebitId = JSON.stringify($(".costCenterDebitId").serializeArray());
		$('#jsonArr_costCenterDebitId').val(formData_costCenterDebitId);
		var formData_costCenterDebitAmount = JSON.stringify($(".costCenterDebitAmount").serializeArray());
		$('#jsonArr_costCenterDebitAmount').val(formData_costCenterDebitAmount);



});


$('button#addDebit').click(function(){
				costdebitCount=debitCount;
			var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-3"><select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]"  id="entryDebitAccount" data-toggle="tooltip" data-placement="left"  placeholder="<?= lang('The_recipient_account') ?>">';
			<?php
			foreach ($toAccountIDs as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['accountID']} - {$account['accountName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control entryDebitBalance" placeholder="<?= lang('Amount') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" type="number" step=".01" required /></div>');
			var inputOption1   = $('<div class="col-3"><input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" type="text" /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');
			var buttonOption1  = $('<div class="col-2" data-item-id="' + (debitCount) + '"><button id="addCostCenterDebit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');


			buttonOption.click(function() {
				debitCountTemp--;

				$(this).parent().remove();
				getDebit();
			});
			buttonOption1.click(function() {


					var row = $(this).closest('div');
        //console.log(row)
		var creditDT=(row.attr('data-item-id'));
		//alert(creditCC)

			var divOption    = '<div class="row" id="debit'+creditDT+'">';

			//var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId"  name="costCenterDebitId_'+creditDT+'[]"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_'+creditDT+'[]" id="costCenterDebitAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+creditDT).append(selectOption);
			$('#debitRow'+creditDT).append(inputOption);
			$('#debitRow'+creditDT).append(buttonOption);
			//debitCount += 1;
			return false;



			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCount).append(selectOption);
			$('#debitRow'+debitCount).append(inputOption);
			$('#debitRow'+debitCount).append(inputOption1);
			$('#debitRow'+debitCount).append(buttonOption);
			$('#debitRow'+debitCount).append(buttonOption1);
			$('#debitAccounts').append('<div class="row" id="debit'+(debitCount)+'"></div>');
			debitCount += 1;
			debitCountTemp += 1;

			search_select()

		});


		$('button#addCostCenterDebit').click(function() {



						var row = $(this).closest('div');
        //console.log(row)
		var debitCC=(row.attr('data-item-id'));
		//alert(creditCC)
			var divOption    = '<div class="row" id="debit'+debitCC+'">';
		//var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId"  name="costCenterDebitId_'+(debitCC-1)+'[]" id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_'+(debitCC-1)+'[]" id="costCenterDebitAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCC).append(selectOption);
			$('#debitRow'+debitCC).append(inputOption);
			$('#debitRow'+debitCC).append(buttonOption);
			//debitCount += 1;

		search_select()

			});


$('button#addCredit').click(function(){
        costcreditCount=creditCount;
    var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
    var selectOption = '<div class="col-3"><select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]"  id="entryCreditAccount" data-toggle="tooltip" data-placement="left"  placeholder="<?= lang('The_recipient_account') ?>">';
    <?php
    foreach ($toAccountIDs as $account) {
        $account = (array) $account;
        echo "
        selectOption += '<option value=\"{$account['id']}\"> {$account['accountID']} - {$account['accountName']}</option>';
        ";
    }
    ?>
    selectOption += '</select></div>';
    var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control entryCreditBalance" placeholder="<?= lang('Amount') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" type="number" step=".01" required /></div>');
    var inputOption1   = $('<div class="col-3"><input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" type="text" /></div>');
	var buttonOption  = $('<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');
    var buttonOption1  = $('<div class="col-2" data-item-id="' + (creditCount) + '"><button id="addCostCenterCredit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');


    buttonOption.click(function() {
        creditCountTemp--;

        $(this).parent().remove();
        getCredit();
    });
    buttonOption1.click(function() {


            var row = $(this).closest('div');
//console.log(row)
var creditDT=(row.attr('data-item-id'));
//alert(creditCC)

    var divOption    = '<div class="row" id="credit'+creditDT+'">';

    //var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
    var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId"  name="costCenterCreditId_'+creditDT+'[]"  id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Credit Account">';
    <?php
    foreach ($cost_centers as $account) {
        $account = (array) $account;
        echo "
        selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
        ";
    }
    ?>
    selectOption += '</select></div>';
    var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_'+creditDT+'[]" id="costCenterCreditAmount" type="number" step=".01" required /></div>');
    var buttonOption  = $('<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

    buttonOption.click(function() {
        $(this).parent().remove();
    });

    $('#creditAccounts').append(divOption);
    $('#creditRow'+creditDT).append(selectOption);
    $('#creditRow'+creditDT).append(inputOption);
    $('#creditRow'+creditDT).append(buttonOption);
    //creditCount += 1;
    return false;



    });

    $('#creditAccounts').append(divOption);
    $('#creditRow'+creditCount).append(selectOption);
    $('#creditRow'+creditCount).append(inputOption);
	$('#creditRow'+creditCount).append(inputOption1);
    $('#creditRow'+creditCount).append(buttonOption);
    $('#creditRow'+creditCount).append(buttonOption1);
    $('#creditAccounts').append('<div class="row" id="credit'+(creditCount)+'"></div>');
    creditCount += 1;
    creditCountTemp += 1;

    search_select()

});


$('button#addCostCenterCredit').click(function() {



                var row = $(this).closest('div');
//console.log(row)
var creditCC=(row.attr('data-item-id'));
//alert(creditCC)
    var divOption    = '<div class="row" id="credit'+creditCC+'">';
//var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
    var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId"  name="costCenterCreditId_'+(creditCC-1)+'[]" id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Credit Account">';
    <?php
    foreach ($cost_centers as $account) {
        $account = (array) $account;
        echo "
        selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
        ";
    }
    ?>
    selectOption += '</select></div>';
    var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_'+(creditCC-1)+'[]" id="costCenterCreditAmount" type="number" step=".01" required /></div>');
    var buttonOption  = $('<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

    buttonOption.click(function() {
        $(this).parent().remove();
    });

    $('#creditAccounts').append(divOption);
    $('#creditRow'+creditCC).append(selectOption);
    $('#creditRow'+creditCC).append(inputOption);
    $('#creditRow'+creditCC).append(buttonOption);
    //creditCount += 1;

search_select()

    });
    $('.content').richText();

	$('#slcurrency').val($('#slcurrency').val()).trigger('change');
   });
    </script>

