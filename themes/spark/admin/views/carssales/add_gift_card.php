<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('add_gift_card'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('carssales/add_gift_card', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('card_no', 'card_no'); ?>
                <div class="input-group">
                    <?php echo form_input('card_no', '', 'class="form-control" id="card_no" required="required"'); ?>
                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                        <a href="#" id="genNo">
                            <i class="fa fa-cogs"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?= lang('value', 'value'); ?>
                <?php echo form_input('value', '', 'class="form-control" id="value" required="required"'); ?>
            </div>
            <div class="form-group">
                <input type="checkbox" class="checkbox" name="staff_points" id="staff_points">
                <label for="staff_points" class="padding05"><?= lang('use_staff_award_points'); ?></label>
            </div>
            <div id="staff-con" style="display:none;">
                <div class="form-group">
                    <label class="control-label" for="user"><?= lang('user'); ?></label>
                    <?php
                    $us[''] = '';
                    foreach ($users as $user) {
                        $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                    }
                    echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                    ?>
                </div>
                <div class="form-group well well-sm" id="sa-points-con" style="display:none;">
                    <p class="bold"><?= lang('award_points'); ?>: <span id="staff_award_points"></span></p>
                    <?= lang('use_points', 'sa_points'); ?>
                    <?php echo form_input('sa_points', '', 'class="form-control" id="sa_points"'); ?>
                </div>
            </div>
            <div id="customer-con">
                <div class="form-group">
                    <?= lang('customer', 'customer'); ?>
                    <div class="input-group">
                                            <?php echo form_input('customer', (isset($_GET['customer']) ? $_GET['customer'] : ''), 'class="form-control" id="customer"'); ?>
					                                                <?php if ($Owner || $Admin || $GP['customers-add']) {
                                                    ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                    <a href="<?= admin_url('customers/add'); ?>" id="addManually">
                                                        <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php
                                                } 
												?>
					</div>
                </div>
                <div class="well well-sm" id="award-points-con" style="display:none;">
                    <div class="form-group">
                        <p class="bold"><?= lang('award_points'); ?>: <span id="award_points"></span></p>
                        <input type="checkbox" class="checkbox" name="use_points" id="use_points"><label
                            for="use_points" class="padding05"><?= lang('use_award_points'); ?></label>
                    </div>
                    <div class="form-group" id="ca-points-con" style="display:none;">
                        <?= lang('use_points', 'ca_points'); ?>
                        <?php echo form_input('ca_points', '', 'class="form-control" id="ca_points"'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?= lang('expiry_date', 'expiry'); ?>
                <?php echo form_input('expiry', $this->sma->hrsd(date('Y-m-d', strtotime('+2 year'))), 'class="form-control date" id="expiry"'); ?>
            </div>
              
			  
			<div class="form-group">
				<label for="paied_1">
					<?php echo form_radio('paid', '0', '1', "id='paid_1'"); ?> <?= lang('Free_of_charge'); ?>
				</label>

				<label for="paied_2">
					<?php echo form_radio('paid', '1', '', "id='paid_2'"); ?> <?= lang('Paid'); ?>
				</label>
				
				<div class="well well-sm" id="div_paid_amount" style="display:none;">
				
				<div class="row">
                    <div class="col-lg-12">
					
					<div class="col-sm-6">
                
                    <div class="form-group">
                <?= lang('Amount', 'Amount'); ?>
                <?php echo form_input('paid_amount', '', 'class="form-control" id="paid_amount"'); ?>
				
				
            
            </div>
            </div>
			
			<div class="col-sm-6">
                                <div class="form-group">
                                    <?= lang('paying_by', 'paid_by_1'); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by" required="required">
									
                                        <?= $Settings->accounting ? $this->sma->paid_opts_accounts(false, true) : $this->sma->paid_opts(false, true); ?>
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
                                                echo form_dropdown('tax_rate', $tr, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : $Settings->default_tax_rate2), 'id="tax_rate" data-placeholder="' . lang('select') . ' ' . lang('VAT_rate') . '" class="form-control input-tip select" style="width:100%;"'); 
												?>
                                
                                    </div>
                                
                            </div>
                            
					
					<div class="col-sm-6">
                                
                                    <div class="form-group"  >
                                        <?= lang('VAT_Amount', 'VAT_Amount'); ?>
                                                 <?= form_input('tax_amount', (isset($_POST['tax_amount']) ? $_POST['tax_amount'] : '0'), 'class="form-control tip" id="tax_amount"  readonly'); ?>
                    
                                    </div>
                                
                            </div>
                            
					
					<div class="col-sm-6">
                                
                                    <div class="form-group"  >
                                        <?= lang('grand_total', 'grand_total'); ?>
                                                 <?= form_input('total_paid_amount', (isset($_POST['total_paid_amount']) ? $_POST['total_paid_amount'] : '0'), 'class="form-control tip" id="total_paid_amount"  readonly'); ?>
                    
                                    </div>
                                
                            </div>
                            
			
			
			</div>
            
                </div>
				<div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'class="form-control" id="note"'); ?>
            </div>

			</div>
			
			
			

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_gift_card', lang('add_gift_card'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_customers') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="mError"></span>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group company">
							<?= lang('Company_name_in_Arabic', 'Company_name_in_Arabic'); ?>*
							<?php echo form_input('company', '', 'class="form-control tip" id="company" data-bv-notempty="true"'); ?>
						</div>
						<div class="form-group">
                            <?= lang('email_address', 'email_address'); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"/>
                    </div>
                    <div class="form-group">
                        <?= lang('phone', 'phone'); ?>
                        <input type="tel" name="phone" class="form-control"  id="phone"/>
                    </div>
                   
                    </div>
                    <div class="col-md-6 col-sm-6" >
                        <div class="form-group companyEn">
                        <?= lang('English_company_name', 'English_company_name'); ?>
                        <?php echo form_input('companyEn', '', 'class="form-control tip" id="companyEn" '); ?>
                    </div>
                       <div class="form-group">
                        <?= lang('VAT_Regesteration_Number', 'VAT_Regesteration_Number'); ?>
                        <?php echo form_input('vat_no', '', 'class="form-control" id="vat_no"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('CR_Number', 'CR_Number'); ?>
                        <?php echo form_input('cr_no', '', 'class="form-control" id="cr_no"'); ?>
                    </div>
                    
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addCustomerManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<?= $modal_js ?>
<script type="text/javascript">
var tax_rates = <?php echo json_encode($tax_rates); ?>;
	
    $(document).ready(function () {
        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
        $('#customer').select2({
            minimumInputLength: 1,
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
        });
        var customer_points = 0;
        $('#customer').on('select2-close', function () {
            var selected_customer = $(this).val();
            $.ajax({
                type: "get", async: false,
                url: site.base_url + "customers/get_award_points/" + selected_customer,
                dataType: 'json',
                success: function (data) {
                    if (data != null) {
                        $('#award_points').html(data.ca_points);
                        $('#ca_points').val(data.ca_points);
                        customer_points = parseInt(data.ca_points);
                        if (data.ca_points > 0) {
                            $('#award-points-con').slideDown();
                        } else {
                            $('#award-points-con').slideUp();
                        }
                    } else {
                        $('#award-points-con').slideUp();
                    }
                }
            });
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
        
        $(document).on('change', '#ca_points', function () {
            if (parseInt($(this).val()) <= customer_points) {
                $("[name='add_gift_card']").attr('disabled', false);
            } else {
                $("[name='add_gift_card']").attr('disabled', true);
            }
        });
        $(document).on('ifChecked', '#use_points', function (event) {
            $('#ca-points-con').slideDown();
        });
        $(document).on('ifUnchecked', '#use_points', function (event) {
            $('#ca-points-con').slideUp();
        });
        $('#genNo').click(function () {
            var no = generateCardNo();
            $(this).parent().parent('.input-group').children('input').val(no);
            return false;
        });
        $('#staff_points').on('ifChecked', function (event) {
            $('#customer-con').slideUp('fast');
            $('#staff-con').slideDown();
        });
        $('#staff_points').on('ifUnchecked', function (event) {
            $('#staff-con').slideUp('fast');
            $('#customer-con').slideDown();
        });
        $('#user').change(function () {
            var selected_user = $(this).val();
            $.ajax({
                type: "get", async: false,
                url: site.base_url + "carssales/get_award_points/" + selected_user,
                dataType: 'json',
                success: function (data) {
                    if (data != null) {
                        $('#staff_award_points').html(data.sa_points);
                        $('#sa_points').val(data.sa_points);
                        if (data.sa_points > 0) {
                            $('#sa-points-con').slideDown();
                        } else {
                            $('#sa-points-con').slideUp();
                        }
                    } else {
                        $('#sa-points-con').slideUp();
                    }
                }
            });
        });
		
		
		$(document).on('click', '#addManually', function (e) {
        $('#mModal').appendTo("body").modal('show');
        return false;
    });
	
	$(document).on('click', '#addCustomerManually', function (e) {
			if (!$('#company').val()) {
                $('#mError').text('<?= lang('company_name_is_required') ?>');
                $('#mError-con').show();
                return false;
            }
            var customer_group_id ='<?=$Settings->customer_group ?>';
            var price_group_id ='<?=$Settings->price_group ?>';
		
            var msg, row = null, customerArr = {
                name: '',
                nameEn: '',
				address: '',
                addressEn: '',
				customer_group_id: customer_group_id,
				price_group_id: price_group_id,
				customer_group_name: '',
				price_group_name: '',
				group_id: '3',
				group_name: 'customer',
                company: $('#company').val(),
                companyEn: $('#companyEn').val(),
                email: $('#email').val(),
                vat_no: $('#vat_no').val(),
                cr_no: $('#cr_no').val(),
                phone: $('#phone').val(),
                city: '',
                state: '',
                postal_code: '',
                country: '',
                cf1: '',
                cf2: '',
                cf3: '',
                cf4: '',
                cf5: '',
                cf6: '',
                gst_no: '',
                co_type: '1',
                hide_in_pos: '0'
                

            };

            $.ajax({
                type: "get", async: false,
                url: site.base_url + "customers/addByAjax",
                data: {token: "<?= $csrf; ?>", customer: customerArr},
                dataType: "json",
                success: function (data) {
				
                    if (data.msg == 'success') {
						msg = data.msg;
                        //row = add_purchase_item(data.result);
						//$("#customer").val(data.result);
						//$('input#add_item').autocomplete("search");
                    } else {
                        msg = data.msg;
                    }
                }
            });

            if (msg == 'success') {
                $('#mModal').modal('hide');
                //audio_success.play();
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();
            }
            return false;

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