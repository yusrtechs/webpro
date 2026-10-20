<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!--<link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet' type='text/css'> style="height:110px;font-size:36pt; font-family:Orbitron;" -->
<link rel="stylesheet" href="<?= $assets ?>richtext/src/richtext.min.css">
<script type="text/javascript" src="<?= $assets ?>richtext/src/jquery.richtext.js"></script>

<script type="text/javascript">
    $(document).ready(function () {

        $('#grpId').change(function () {
            var v = $(this).val();
            var uopt = $("#coId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#coId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>')
    .val('')
;

$('#glbrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;

$('#coId').val('').trigger('change.select2');
$('#glbrId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                            //$("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_coId') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select_category_to_load') ?>",
                                //data: scdata


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                });
            }
        });





        $('#coId').change(function () {
            var v = $(this).val();
            var uopt = $("#glbrId");


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('branches/getBranchesBycoId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {

$('#glbrId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;


$('#glbrId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                            //$("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_coId') ?>").select2({allowClear: true,
                                //placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                //data: scdata


                        } else {
                            $("#glbrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#glbrId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                });
            }
        });


        $('#glbrId').change(function () {
            var v = $(this).val();



            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('accounts/getMaxBondreceipt') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {


                        if (scdata != null) {

                        $('#bond_no2').val(scdata);


                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            }
        });

    });
</script>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
	<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_receipt'); ?></h4>
        </div>
       <div class="modal-body">


                <?php
                $currencyCode= $Settings->default_currency;
				$currencyRate= 1;
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('accounts/add_receipt' . ($count_id ? '/' . $count_id : ''), $attrib);
                ?>



                <div class="row">
                    <div class="col-lg-12">
                    <?php
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                            {

                            ?>
							<div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('Main_group', 'Main_group') ?>
                                <?php
                                $cat[''] = lang('select') . ' ' . lang('Main_group');
                                foreach ($mainGroups as $mainGroup) {
                                    $cat[$mainGroup->id] = $mainGroup->name;
                                }
                                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                ?>
                            </div>
							</div>
                            <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                            <div class="col-sm-3">
								<div class="form-group">
                                <?= lang('Main_company', 'Main_company') ?>
                                <?php
                                $co[''] = lang('select') . ' ' . lang('Main_company');
                                if(($this->Settings->enable_grp==1)){}else{
                                    foreach ($mainCompanies as $mainCompany) {
                                        $co[$mainCompany->id] = $mainCompany->name;
                                    }
                                }

                                echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                ?>
                            </div>
							</div>
                            <?php }

                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
							<div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('Main_branch', 'Main_branch') ?>
                                <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
												if ($Owner || $Admin){
                                            foreach ($mainBranches as $mainBranche) {
                                                $br[$mainBranche->id] = $mainBranche->name;
                                            }
											}else{
												$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
											}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="glbrId"   style="width:100%"')
                                            ?>
                            </div>
							</div>
                           <?php
                        }} ?>

                    <div class="col-sm-3" >
                    <div class="form-group">
										<?= lang('currency', 'currency'); ?>
										<?php
										 foreach ($currencies as $currency) {
										$curr[$currency->code] = $currency->name;
										}
											echo form_dropdown('currency', $curr, $Settings->default_currency, 'id="slcurrency" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('currency') . '" required="required" style="width:100%;" '); ?>
									</div>

                    </div>

                    <div class="col-sm-3" style="display:none;">
                        <div class="form-group"  >
                            <?= lang('amount', 'amount'); ?>
                            <?= form_input('amount', (isset($_POST['amount']) ? $_POST['amount'] : $payment_ref), 'class="form-control tip" id="amount"  required="required"'); ?>
                        </div>
                    </div>












					<div class="col-sm-3">
                    <div class="form-group">
                        <?= lang('Bond_No', 'Bond_No'); ?>
                        <?= form_input('bond_no', (isset($_POST['bond_no']) ? $_POST['bond_no'] : $bond_no), 'class="form-control tip"  dir="ltr" id="bond_no2"'); ?>
                    </div>
                </div>


                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?= lang('date', 'date'); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ''), ' readonly class="form-control input-tip datetime" id="date" '); ?>
                                </div>
                            </div>

                            <div class="col-sm-3">
                    <div class="form-group">
                        <?= lang('reference_no', 'reference_no'); ?>
                        <?= form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $payment_ref), 'class="form-control tip" id="reference_no"'); ?>
                    </div>
                </div>



				<div class="col-sm-2" style="display:none;">

                                    <div class="form-group"  >
                                        <?= lang('VAT_rate', 'VAT_rate'); ?>
										<?php
                                    $tr[''] = '';
                                                foreach ($tax_rates as $tax) {
                                                    $tr[$tax->id] = $tax->name;
                                                }
                                                echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : $Settings->default_tax_rate2), 'id="tax_rate" data-placeholder="' . lang('select') . ' ' . lang('VAT_rate') . '" class="form-control input-tip select" style="width:100%;"');
												?>

                                    </div>

                            </div>




					<div class="col-sm-2" style="display:none;">

                                    <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount'); ?>
                                                 <?= form_input('tax_amount', (isset($_POST['tax_amount']) ? $_POST['tax_amount'] : '0'), 'class="form-control tip" id="tax_amount"  readonly'); ?>

                                    </div>

                            </div>


					<div class="col-sm-3">

                                    <div class="form-group"  >
                                        <?= lang('Amount', 'grand_total'); ?>
                                                 <?= form_input('total_amount', (isset($_POST['total_amount']) ? $_POST['total_amount'] : '0'), 'class="form-control tip" id="total_amount"  readonly'); ?>

                                    </div>

                            </div>












                </div>
                </div>


                <div class="col-md-12"  id="creditAccounts">
                <div class="alert alert-info w-50"> <b><?= lang('Information_about_accounts_from_which_debits_are_made')?></b></div>
                        <div class="row">
						<div class="col-sm-3">




							<select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]" id="entryCreditAccount" data-toggle="tooltip" data-placement="left" placeholder="<?= lang('Spend_from') ?>" required >
								<?php
								echo "
								<option></option>
								";
								foreach ($payment_types as $account) {
									$account = (array) $account;
									echo "
									<option value='{$account['id']}'> {$account['payment_name']} - {$account['accountName']}</option>
									";
								}
								?>
							</select>
						</div>
						<div class="col-sm-2" dir="ltr">
							<input class="form-control entryCreditBalance" placeholder="<?= lang('Amount') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" type="number" min="0" step=".01" required />
						</div>

                        <div class="col-3">
							<input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" type="text" />
						</div>

						<div class="col-sm-2">
							<button id="addCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_account') ?></button>
						</div>
						<div  class="col-sm-2">
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
					<div class="row" id="credit0">

					</div>
                    </div>

                    <div class="row">
                    <div class="col-lg-12">










                        <div class="col-lg-12 div_payment">
                        <div class="alert alert-info w-50" id="alertVisibleIn"> <b><?= lang('Local_currency_equivalent')?></b></div>
                        <div class="col-sm-3">
                                <div class="payment">
                                    <div class="form-group">
                                        <?= lang('Exchange_rate', 'Exchange_rate'); ?>
                                        <input name="exchange_rate" id="exchange_rate" type="text"
                                               value="<?= $currencyRate ?>"
                                               class="pa form-control kb-pad amount" required="required" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                        <div class="payment">
                                    <div class="form-group">
                                        <?= lang('Amount', 'amount_1')." (".$Settings->default_currency.")"; ?>
                                        <input name="local_curr_amount" type="text" id="local_curr_amount"
                                               value="<?= $this->sma->formatDecimal(($this->sma->formatDecimal($inv->grand_total) * $this->sma->formatDecimal($currencyRate)) - $this->sma->formatDecimal($this->sma->formatDecimal($inv->paid + $inv->discount_allowance) * $this->sma->formatDecimal($currencyRate))) ?>"
                                               class="pa form-control kb-pad amount" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3" style="display:none;">
                            <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount')." (".$Settings->default_currency.")"; ?>
                                                 <?= form_input('equivalent_tax_amount', (isset($_POST['tax_amount']) ? $_POST['tax_amount'] : '0'), 'class="form-control tip" id="equivalent_tax_amount"  readonly'); ?>

                                    </div>
                            </div>
                            <div class="col-sm-3" style="display:none;">
                            <div class="form-group"  >
                                        <?= lang('grand_total', 'grand_total')." (".$Settings->default_currency.")"; ?>
                                                 <?= form_input('equivalent_total_amount', (isset($_POST['total_amount']) ? $_POST['total_amount'] : '0'), 'class="form-control tip" id="equivalent_total_amount"  readonly'); ?>

                                    </div>
                            </div>
                            </div>


                        <!-- <div class="col-lg-12">
                 <div class="form-group" id="CostCentersCredit">
					<div class="row">

						<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
							<button id="addCostCenterCredit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
                    </div>
				</div> -->




                        <div class="clearfix"></div>





                    <div class="col-md-12">
                        <div class="row">
                            <!--<div class="col-sm-4">

                                    <div class="form-group all">
				<?= lang('The_recipient_account', 'The_recipient_account'); ?>

                <select name="toAccountID" class="form-control" id="toAccountID"   required="required" >
                <?php  if (!empty($toAccountIDs)) {?>
                        <option ></option>
						<?php
                        foreach ($toAccountIDs as $toAccountID) { ?>
                         <option value="<?=$toAccountID->id?>"  ><?=$toAccountID->accountID.' - '.$toAccountID->accountName?></option>

                        <?php } } ?>
                </select>

            </div>

                            </div>-->



							 <div class="col-sm-6">

						<div class="form-group">
                <?= lang('attachment', 'attachment') ?>
                <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
			</div>


							<div class="col-sm-6">
                    <div class="form-group">
                        <?= lang('Receipt_name', 'Receipt_name'); ?>
                        <?= form_input('receipt_name', (isset($_POST['receipt_name']) ? $_POST['receipt_name'] : $receipt_name), 'class="form-control tip" id="receipt_name"'); ?>
                    </div>
                </div>















                        </div>
                    </div>












                    <div class="col-md-12"  id="debitAccounts">
                    <div class="alert alert-info w-50"> <b><?= lang('Accounts_receivable_information')?></b></div>
                        <div class="row">
						<div class="col-sm-3">




							<select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]" id="entryDebitAccount" data-toggle="tooltip" data-placement="left" placeholder="<?= lang('The_recipient_account') ?>" required >
								<?php
								echo "
								<option></option>
								";
								foreach ($toAccountIDs as $account) {
									$account = (array) $account;
									echo "
									<option value='{$account['id']}'> {$account['accountID']} - {$account['accountName']}</option>
									";
								}
								?>
							</select>
						</div>
						<div class="col-sm-2" dir="ltr">
							<input class="form-control entryDebitBalance" placeholder="<?= lang('Amount') ?>" name="entryDebitBalance[]" id="entryDebitBalance" onchange="getDebit()" type="number" min="0" step=".01" required />
						</div>

                        <div class="col-3">
							<input class="form-control entryDebitDescription" placeholder="<?= lang('description') ?>" name="entryDebitDescription[]" id="entryDebitDescription" type="text" />
						</div>

						<div class="col-sm-2">
							<button id="addDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_account') ?></button>
						</div>
						<div  class="col-sm-2">
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
					<div class="row" id="debit0">

					</div>
                    </div>










               <!--<div class="form-group" id="CostCentersDebit">
					<div class="row">

						<div  style="padding-left:30px;padding-bottom:10px;padding-right:25px;" >
							<button id="addCostCenterDebit" class="btn btn-primary spark-btn-primary-action" type="button"><?= lang('Add_cost_center') ?></button>
						</div>
					</div>
				</div>-->



				<div class="clearfix"></div>

            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control content" id="note"'); ?>
            </div>






                            <div class="clearfix"></div>

                <input type="hidden" name="jsonArr_entryDebitAccount" id="jsonArr_entryDebitAccount"  value=''/>
				<input type="hidden" name="jsonArr_entryDebitBalance" id="jsonArr_entryDebitBalance"  value=''/>
				<input type="hidden" name="jsonArr_entryDebitDescription" id="jsonArr_entryDebitDescription"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitId_0" id="jsonArr_costCenterDebitId_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitAmount_0" id="jsonArr_costCenterDebitAmount_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitId" id="jsonArr_costCenterDebitId"  value=''/>
				<input type="hidden" name="jsonArr_costCenterDebitAmount" id="jsonArr_costCenterDebitAmount"  value=''/>

                <input type="hidden" name="jsonArr_entryCreditAccount" id="jsonArr_entryCreditAccount"  value=''/>
				<input type="hidden" name="jsonArr_entryCreditBalance" id="jsonArr_entryCreditBalance"  value=''/>
				<input type="hidden" name="jsonArr_entryCreditDescription" id="jsonArr_entryCreditDescription"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditId_0" id="jsonArr_costCenterCreditId_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditAmount_0" id="jsonArr_costCenterCreditAmount_0"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditId" id="jsonArr_costCenterCreditId"  value=''/>
				<input type="hidden" name="jsonArr_costCenterCreditAmount" id="jsonArr_costCenterCreditAmount"  value=''/>



                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('add_receipt', lang('submit'), 'id="add_receipt" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
<!--
<?= $modal_js ?>
-->
<script type="text/javascript" charset="UTF-8">
    var tax_rates = <?php echo json_encode($tax_rates); ?>;
    var dt=0;
    var cr=0;

function search_select(){
	$('select').select2({
  matcher: function(term, text, option) {
    return text.toUpperCase().indexOf(term.toUpperCase())>=0 || option.val().toUpperCase().indexOf(term.toUpperCase())>=0;
  }
});

}


		function getDebit(){
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
		$("#add_receipt").prop('disabled',false);
	}else{
		$("#add_receipt").prop('disabled',true);
	}

}

function getCredit(){
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
		$("#add_receipt").prop('disabled',false);
	}else{
		$("#add_receipt").prop('disabled',true);
	}

}



	$(document).ready(function () {
        $(".div_payment").css("display", "none");
		search_select();


        var debitCount  = 0;
		var debitCountTemp  = 0;
		var costdebitCount  = 0;

        var creditCount  = 0;
		var creditCountTemp  = 0;
		var costcreditCount  = 0;

		var currencyCode ='<?=$currencyCode ?>';
		var currencyRate ='<?=$currencyRate ?>';


        var amount =$('#amount').val();
        var clicked = false;

        $(document).on('click', '#add_receipt', function(e) {


        if (clicked) {
        	e.preventDefault();
            return;
      	}

		if($('#jsonArr_entryDebitAccount').val()!='' && $('#jsonArr_entryDebitBalance').val()!='' && $('#jsonArr_entryCreditAccount').val()!='' && $('#jsonArr_entryCreditBalance').val()!=''){
		  	clicked = true;
            var $this = $(this);
            $this.addClass('disabled');
            setTimeout(function() {
                $this.removeClass('disabled');
            }, 10000);
		}else{
            var $this = $(this);
            $this.addClass('disabled');
            setTimeout(function() {
                $this.removeClass('disabled');
            }, 3000);
        }

        clicked = false;



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





});


            $('button#addCredit').click(function(){
				costcreditCount=creditCount;
			var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
			var selectOption = '<div class="col-3"><select class="form-control entryCreditAccount" style="padding:1px;" name="entryCreditAccount[]"  id="entryCreditAccount" data-toggle="tooltip" data-placement="left" placeholder="<?= lang('Spend_from') ?>" required >';
			<?php
			echo "
			selectOption += '<option></option>';
			";
			foreach ($payment_types as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['payment_name']} - {$account['accountName']}</option>';
				";
			}

			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-2" dir="ltr"><input class="form-control entryCreditBalance" placeholder="<?= lang('Amount') ?>" name="entryCreditBalance[]" id="entryCreditBalance" onchange="getCredit()" type="number" step=".01" required /></div>');
			var inputOption1   = $('<div class="col-3"><input class="form-control entryCreditDescription" placeholder="<?= lang('description') ?>" name="entryCreditDescription[]" id="entryCreditDescription" type="text" /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');
			var buttonOption1  = $('<div class="col-2" data-item-id="' + (creditCount+1) + '"><button id="addCostCenterCredit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');





			buttonOption.click(function() {
				creditCountTemp--;

				$(this).parent().remove();
				getCredit();
			});
			buttonOption1.click(function() {
				var row = $(this).closest('div');
        //console.log(row)
		var creditCC=(row.attr('data-item-id'));
		//alert(creditCC);
		//console.log($(this))
			//var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
			var divOption    = '<div class="row" id="credit'+creditCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId"  name="costCenterCreditId_'+creditCC+'[]"  id="costCenterCreditId" data-toggle="tooltip" data-placement="left" title="Credit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_'+creditCC+'[]" id="costCenterCreditAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
				//$(this).remove();


			});

			$('#creditAccounts').append(divOption);
			$('#credit'+creditCC).append(selectOption);
			$('#credit'+creditCC).append(inputOption);
			$('#credit'+creditCC).append(buttonOption);
			//creditCount += 1;



			});

			$('#creditAccounts').append(divOption);
			$('#creditRow'+creditCount).append(selectOption);
			$('#creditRow'+creditCount).append(inputOption);
			$('#creditRow'+creditCount).append(inputOption1);
			$('#creditRow'+creditCount).append(buttonOption);
			$('#creditRow'+creditCount).append(buttonOption1);
			$('#creditAccounts').append('<div class="row" id="credit'+(creditCount+1)+'"></div>');
			creditCount += 1;
			creditCountTemp += 1;

			search_select()

		});


        $('button#addCostCenterCredit').click(function() {


//alert(creditCount)
    //var divOption    = '<div class="row" id="creditRow'+creditCount+'">';
    var divOption    = '<div class="row" id="credit0">';
    var selectOption = '<div class="col-5" ><select class="form-control costCenterCreditId_0"  name="costCenterCreditId_0[]" id="costCenterCreditId_0" data-toggle="tooltip" data-placement="left" title="Credit Account">';
    <?php
    foreach ($cost_centers as $account) {
        $account = (array) $account;
        echo "
        selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
        ";
    }
    ?>
    selectOption += '</select></div>';
    var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterCreditAmount_0" placeholder="<?= lang('Amount') ?>" name="costCenterCreditAmount_0[]" id="costCenterCreditAmount_0" type="number" step=".01" required /></div>');
    var buttonOption  = $('<div class="col-2"><button id="removeCredit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

    buttonOption.click(function() {
        $(this).parent().remove();
    });

    $('#creditAccounts').append(divOption);
    $('#credit0').append(selectOption);
    $('#credit0').append(inputOption);
    $('#credit0').append(buttonOption);
    //creditCount += 1;

search_select()

    });









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



        $('button#addDebit').click(function(){
				costdebitCount=debitCount;
			var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var selectOption = '<div class="col-3"><select class="form-control entryDebitAccount" style="padding:1px;" name="entryDebitAccount[]"  id="entryDebitAccount" data-toggle="tooltip" data-placement="left" placeholder="<?= lang('The_recipient_account') ?>" required >';
			<?php
			echo "
			selectOption += '<option></option>';
			";
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
			var buttonOption1  = $('<div class="col-2" data-item-id="' + (debitCount+1) + '"><button id="addCostCenterDebit[]" class="btn btn-primary spark-btn-primary-action"><?= lang('Add_cost_center') ?></button></div></div>');





			buttonOption.click(function() {
				debitCountTemp--;

				$(this).parent().remove();
				getDebit();
			});
			buttonOption1.click(function() {
				var row = $(this).closest('div');
        //console.log(row)
		var debitCC=(row.attr('data-item-id'));
		//alert(debitCC);
		//console.log($(this))
			//var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
			var divOption    = '<div class="row" id="debit'+debitCount+'">';
			var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId"  name="costCenterDebitId_'+debitCC+'[]"  id="costCenterDebitId" data-toggle="tooltip" data-placement="left" title="Debit Account">';
			<?php
			foreach ($cost_centers as $account) {
				$account = (array) $account;
				echo "
				selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
				";
			}
			?>
			selectOption += '</select></div>';
			var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_'+debitCC+'[]" id="costCenterDebitAmount" type="number" step=".01" required /></div>');
			var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

			buttonOption.click(function() {
				$(this).parent().remove();
				//$(this).remove();


			});

			$('#debitAccounts').append(divOption);
			$('#debit'+debitCC).append(selectOption);
			$('#debit'+debitCC).append(inputOption);
			$('#debit'+debitCC).append(buttonOption);
			//debitCount += 1;



			});

			$('#debitAccounts').append(divOption);
			$('#debitRow'+debitCount).append(selectOption);
			$('#debitRow'+debitCount).append(inputOption);
			$('#debitRow'+debitCount).append(inputOption1);
            $('#debitRow'+debitCount).append(buttonOption);
			$('#debitRow'+debitCount).append(buttonOption1);
			$('#debitAccounts').append('<div class="row" id="debit'+(debitCount+1)+'"></div>');
			debitCount += 1;
			debitCountTemp += 1;

			search_select()

		});

        $('button#addCostCenterDebit').click(function() {


//alert(debitCount)
    //var divOption    = '<div class="row" id="debitRow'+debitCount+'">';
    var divOption    = '<div class="row" id="debit0">';
    var selectOption = '<div class="col-5" ><select class="form-control costCenterDebitId_0"  name="costCenterDebitId_0[]" id="costCenterDebitId_0" data-toggle="tooltip" data-placement="left" title="Debit Account">';
    <?php
    foreach ($cost_centers as $account) {
        $account = (array) $account;
        echo "
        selectOption += '<option value=\"{$account['id']}\"> {$account['costName']}</option>';
        ";
    }
    ?>
    selectOption += '</select></div>';
    var inputOption   = $('<div class="col-3" dir="ltr"><input class="form-control costCenterDebitAmount_0" placeholder="<?= lang('Amount') ?>" name="costCenterDebitAmount_0[]" id="costCenterDebitAmount_0" type="number" step=".01" required /></div>');
    var buttonOption  = $('<div class="col-2"><button id="removeDebit[]" class="btn btn-danger spark-btn-secondary-action spark-btn-danger-outline"><?= lang('Remove') ?></button></div></div>');

    buttonOption.click(function() {
        $(this).parent().remove();
    });

    $('#debitAccounts').append(divOption);
    $('#debit0').append(selectOption);
    $('#debit0').append(inputOption);
    $('#debit0').append(buttonOption);
    //debitCount += 1;

search_select()

    });
    $('.content').richText();

    });

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





</script>
