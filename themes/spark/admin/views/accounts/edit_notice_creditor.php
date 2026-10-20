<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!--<link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet' type='text/css'> style="height:110px;font-size:36pt; font-family:Orbitron;" -->
<link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
<script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>
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

        var addProducts  = 1;
		$('button#addProductsButton').click(function(){

            //if (addProducts <= 5) {

			var divOption    = '<div class="row" id="productsRow'+addProducts+'">';
			var selectOption = '<div class="col-6" style="padding-left:30px;padding-right:25px;"><input type="text" name="product_' + addProducts + '", class="form-control" id="product_' + addProducts + '" placeholder="<?= lang('select') . ' ' . lang('product') ?>" style="width:100%;display: block !important;" /></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control" placeholder="<?= lang('quantity') ?>" name="quantity_' + addProducts + '" id="quantity_' + addProducts + '" type="number" step="1" required /></div>');
			var buttonOption  = $('<div class="col-3"><button id="removeProduct[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
                addProducts -= 1;
			});

			$('#addProducts').append(divOption);
			$('#productsRow'+addProducts).append(selectOption);
			$('#productsRow'+addProducts).append(inputOption);
			$('#productsRow'+addProducts).append(buttonOption);
            var sup = $('#product_' + addProducts);
            products(sup);
            $('#products_no').val(addProducts);
			addProducts += 1;
        //} else {
        //        bootbox.alert('<?= lang('max_reached') ?>');
        //        return false;
        //    }

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


		$('#exchange_rate').change(function(e) {

try{
amount =$('#local_curr_amount').val();
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

$('#fromAccountID').change(function(e) {
var sales_return_acct ='<?=$accounts_settings->sales_return_acct ?>';
if($(this).val()==sales_return_acct){
    $("#addProducts").css("display", "block");
    $("#addWarehouse").css("display", "block");
}else{
    $("#addProducts").css("display", "none");
    $("#addWarehouse").css("display", "none");
}

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
            $('#exchange_rate').val(data.rate);
            $('#local_curr_amount').val(formatDecimal(amount*data.rate,2));
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

function products(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + 'products/suggestionsV5',
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    warehouse_id: $("#warehouse_id").val(),
                    limit: 10,
                };
            },
            results: function(data, page) {
                if (data != null) {
                    return { results: data };
                } else {
                    return { results: [{ id: '', text: 'No Match Found' }] };
                }
            },
        },
    });
}

    });
</script>




<div class="modal-dialog modal-lg">
    <div class="modal-content">
	<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_notice_creditor'); ?></h4>
        </div>
       <div class="modal-body">


                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('accounts/edit_notice_creditor' . ($id ? '/' . $id : ''), $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">

                    <input type="hidden" name="products_no" id="products_no" value="0" />

                    <div class="col-sm-3" >
                    <div class="form-group">
										<?= lang('currency', 'currency'); ?>
										<?php
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
											echo form_dropdown('currency', $curr, $receipt->currency_code, 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>

                    </div>

					<div class="col-sm-3">

                                    <div class="form-group"  >
                                        <?= lang('amount', 'amount'); ?>
                                                 <?= form_input('amount', $this->sma->formatDecimal($receipt->amount), 'class="form-control tip" id="amount"  required="required"'); ?>

                                    </div>

                            </div>


					<div class="col-sm-3">
                    <div class="form-group">
                        <?= lang('Bond_No', 'Bond_No'); ?>
                        <?= form_input('bond_no', $receipt->bond_no, 'class="form-control tip" dir="ltr" id="bond_no"'); ?>
                    </div>
                </div>



                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('date', 'date'); ?>
                                    <?php echo form_input('date', $this->sma->hrld($receipt->date), 'readonly class="form-control input-tip datetime" id="receiptDate" '); ?>
                                </div>
                            </div>



				<div class="col-sm-3">

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


					<div class="col-sm-3">

                                    <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount'); ?>
                                                 <?= form_input('tax_amount', $this->sma->formatDecimal($receipt->tax_amount), 'class="form-control tip" id="tax_amount"  readonly'); ?>

                                    </div>

                            </div>


					<div class="col-sm-3">

                                    <div class="form-group"  >
                                        <?= lang('grand_total', 'grand_total'); ?>
                                                 <?= form_input('total_amount', $this->sma->formatDecimal($receipt->total_amount), 'class="form-control tip" id="total_amount"  readonly'); ?>

                                    </div>

                            </div>



				<div class="col-sm-3">
                    <div class="form-group">
                        <?= lang('reference_no', 'reference_no'); ?>
                        <?= form_input('reference_no', $receipt->reference_no, 'class="form-control tip" id="reference_no"'); ?>
                    </div>
                </div>




                        <div class="col-sm-3">

                                    <div class="form-group all">
                                    <?= lang('Account', 'fromAccountID'); ?>

                <?php
                $acc= [];


                $acc[$accounts_discount_allowance->id] = $accounts_discount_allowance->accountID.' - '.(($this->Settings->user_language == "arabic" || $accounts_discount_allowance->accountNameEn=="") ? $accounts_discount_allowance->accountName : $accounts_discount_allowance->accountNameEn);
                $acc[$sales_return_acct->id] = $sales_return_acct->accountID.' - '.(($this->Settings->user_language == "arabic" || $sales_return_acct->accountNameEn=="") ? $sales_return_acct->accountName : $sales_return_acct->accountNameEn);

                echo form_dropdown('fromAccountID', $acc, $receipt->fromAccountID, 'class="form-control tip" id="fromAccountID" data-placeholder="' . lang('select') . ' ' . lang('Account') . '"  style="width:100%;"');
                ?>



				</div>
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
                                        <?= lang('amount', 'amount_1')." (".$Settings->default_currency.")"; ?>
                                        <input name="local_curr_amount" type="text" id="local_curr_amount"
                                               value="<?= $this->sma->formatDecimal($receipt->equivalent_amount) ?>"
                                               class="pa form-control kb-pad amount" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                            <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount')." (".$Settings->default_currency.")"; ?>
                                                 <?= form_input('equivalent_tax_amount', $this->sma->formatDecimal($receipt->equivalent_tax_amount), 'class="form-control tip" id="equivalent_tax_amount"  readonly'); ?>

                                    </div>
                            </div>
                            <div class="col-sm-3">
                            <div class="form-group"  >
                                        <?= lang('grand_total', 'grand_total')." (".$Settings->default_currency.")"; ?>
                                                 <?= form_input('equivalent_total_amount', $this->sma->formatDecimal($receipt->equivalent_total_amount), 'class="form-control tip" id="equivalent_total_amount"  readonly'); ?>

                                    </div>
                            </div>
                            </div>


                        <div class="col-lg-12">

						<div class="form-group" id="CostCentersCredit">
					<div class="row">

						<div  style="padding-left:15px;padding-bottom:10px;padding-right:10px;padding-top:10px;" >
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
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



                        <div class="clearfix"></div>





                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-4">

                                    <div class="form-group all">
                                    <?= lang('customer', 'toAccountID'); ?>

                <select name="toAccountID" class="form-control" id="toAccountID"   required="required" >
                <?php  if (!empty($accountsLocal)) {?>

						<?php
                        foreach ($accountsLocal as $toAccountID) {

							if($receipt->toAccountID==$toAccountID->id){
								?>
                         <option value="<?=$toAccountID->id?>" selected ><?=$toAccountID->accountID.' - '.(($this->Settings->user_language == "arabic" || $toAccountID->accountNameEn=="") ? $toAccountID->accountName : $toAccountID->accountNameEn)?></option>

							<?php

							}else{

							?>
                         <option value="<?=$toAccountID->id?>"  ><?=$toAccountID->accountID.' - '.(($this->Settings->user_language == "arabic" || $toAccountID->accountNameEn=="") ? $toAccountID->accountName : $toAccountID->accountNameEn)?></option>

							<?php } } } ?>

                 <?php  if (!empty($accountsIner)) {?>

						<?php
                        foreach ($accountsIner as $toAccountID) {

							if($receipt->toAccountID==$toAccountID->id){
								?>
                         <option value="<?=$toAccountID->id?>" selected ><?=$toAccountID->accountID.' - '.(($this->Settings->user_language == "arabic" || $toAccountID->accountNameEn=="") ? $toAccountID->accountName : $toAccountID->accountNameEn)?></option>

							<?php

							}else{

							?>
                         <option value="<?=$toAccountID->id?>"  ><?=$toAccountID->accountID.' - '.(($this->Settings->user_language == "arabic" || $toAccountID->accountNameEn=="") ? $toAccountID->accountName : $toAccountID->accountNameEn)?></option>

							<?php } } } ?>
                </select>

            </div>

                            </div>



                            <div class="col-md-4" id="addWarehouse" style="display:none;">
                                <div class="form-group">
                                    <?= lang('warehouse', 'warehouse_id'); ?>
                                    <?php
                                    $wh[''] = '';
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('warehouse_id', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ($warehouse_id ? $warehouse_id : $Settings->default_warehouse)), 'id="warehouse_id" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" ' . ($warehouse_id ? 'readonly' : '') . ' style="width:100%;"'); ?>
                                </div>
                            </div>



							<div class="col-sm-4">

						<div class="form-group">
                <?= lang('attachment', 'attachment') ?>
                <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
			</div>





							<div class="col-sm-2" style="display:none;">
                    <div class="form-group">
                        <?= lang('Recipient_name', 'Recipient_name'); ?>
                        <?= form_input('receipt_name', $receipt->receipt_name, 'class="form-control tip" id="receipt_name" '); ?>
                    </div>
                </div>








                        </div>
                    </div>

                <div class="form-group" id="addProducts" style="display:none;">
					<div class="row">



						<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
                            <?= lang('products', 'products') ?>
                            <button type="button" class="btn btn-primary btn-xs" id="addProductsButton"><i class="fa fa-plus"></i>
                            </button>
						</div>
					</div>
				</div>

			   <div class="form-group" id="CostCentersDebit">
					<div class="row">

						<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>

				</div>

						<?php

				if ($receipt->costCenterDebitId) {


						$entry = (array) $entry;
						$entry['costCenterDebitId'] = json_decode($receipt->costCenterDebitId);
						$entry['costCenterDebitAmount'] = json_decode($receipt->costCenterDebitAmount);


						$cost_index=0;
							foreach ($entry['costCenterDebitId'] as $cost_center){



								 ?>
								 <script>
								 $("#addCostCenterDebit" ).click()
								 $('#costCenterDebitId'+'<?= $cost_index; ?>').val('<?= $cost_center; ?>')
								 $('#costCenterDebitAmount'+'<?= $cost_index; ?>').val('<?= $entry["costCenterDebitAmount"][$cost_index]; ?>')
								 </script>
								 <?php

								$cost_index++;


							}

					} ?>


				<div class="clearfix"></div>

            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', $receipt->note, 'class="form-control content" id="note"'); ?>
            </div>






                            <div class="clearfix"></div>


                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_notice_creditor', lang('submit'), 'id="edit_notice_creditor" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
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
   $(document).ready(function () {
    $('.content').richText();

    var sales_return_acct ='<?=$accounts_settings->sales_return_acct ?>';
if($('#fromAccountID').val()==sales_return_acct){
    $("#addProducts").css("display", "block");
    $("#addWarehouse").css("display", "block");
}else{
    $("#addProducts").css("display", "none");
    $("#addWarehouse").css("display", "none");
}



<?php
    if ($AdjustItems) {

        ?>
        <?php
        $i=1;
        foreach($AdjustItems as $product){


                ?>
                var i='<?= $i; ?>';
        $('#addProductsButton').click();
        select_product('product_'+i+'', "<?= $product->product_id; ?>");
        $('#quantity_'+i+'').val("<?= $this->sma->formatDecimal($product->quantity); ?>");
        <?php

        $i++;


            }
            }
        ?>

        function select_product(id, v) {
            $('#' + id).val(v).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: "<?= admin_url('products/getProductV3') ?>/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + 'products/suggestionsV5',
                    dataType: 'json',
                    quietMillis: 15,
                data: function(term, page) {
                    return {
                        term: term,
                        limit: 10,
                    };
                },
                results: function(data, page) {

                    if (data != null) {
                        return { results: data };
                    } else {
                        return { results: [{ id: '', text: 'No Match Found' }] };
                    }
                },
                }
            });
        }

   });
    </script>