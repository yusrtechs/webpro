<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

	if (isset($_POST['length'])) {
		//include 'class.coupon.php';
        require APPPATH . '/libraries/simple-php-coupon-code-generator-master/class.coupon.php';
		$no_of_coupons = $_POST['no_of_coupons'];
		$length = $_POST['length'];
		$prefix = $_POST['prefix'];
		$suffix = $_POST['suffix'];
		$numbers = $_POST['numbers'];
		$letters = $_POST['letters'];
		$symbols = $_POST['symbols'];
		$random_register = $_POST['random_register'] == 'false' ? false : true;
		$mask = $_POST['mask'] == '' ? false : $_POST['mask'];
		$coupons = coupon::generate_coupons($no_of_coupons, $length, $prefix, $suffix, $numbers, $letters, $symbols, $random_register, $mask);
		foreach ($coupons as $key => $value) {
			echo $value."\n";
		}
		die();
	}

?>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('add_coupon'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('sales/add_coupon', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
        <div class="row">
            <div class="col-md-3">
            <div class="form-group">
                <?= lang('Number_of_coupons', 'coupon_gen_no'); ?>
                <div class="input-group">
                    <?php echo form_input('coupon_gen_no', '1', 'type="number"  maxlength="3" class="form-control" id="coupon_gen_no" required="required"'); ?>
                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                        <a href="#" id="genNo">
                            <i class="fa fa-cogs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="button" onclick="exporttocsv()" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> <span class="padding04"><?= lang('Export_coupon_codes_to_Excel'); ?></span></button>
            </div>


            </div>
            <div class="col-md-9">

            <div class="form-group">
                <?= lang('Coupon_codes', 'coupon_code'); ?>
                <textarea class="form-control coupon" placeholder="<?= lang('Coupon_codes'); ?>" name="coupon_code" id="result" rows="4" readonly=""  required="required"></textarea>
            </div>




            </div>
            </div>

            <div class="form-group">
                <?= lang('description', 'description'); ?>
                <?php echo form_input('description', '', 'class="form-control" id="description" required="required"'); ?>
            </div>


            <div class="well well-sm well_1">
                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                    <label class="control-label" for="discount_type"><?= lang('Discount_type'); ?></label>

                        <div class="controls">
                        <?php $opt_coupon =  ['percent' => lang('Percentage_discount'), 'fixed' => lang('Fixed_discount')]; ?>
                            <?php
                                echo form_dropdown('discount_type', $opt_coupon, 'percent', 'class="form-control tip" required="required" id="discount_type" style="width:100%;"');
                            ?>
                        </div>
                    </div>


                    <div class="form-group">
                    <?= lang('customer', 'customer'); ?>
                    <div class="input-group">
                                            <?php echo form_input('customer', (isset($_GET['customer']) ? $_GET['customer'] : ''), 'class="form-control" id="customer" required="required"'); ?>
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





                    <div class="form-group" style="display:none;">
                        <?= lang('Minimum_purchase_value', 'minimum_amount').' ( '.lang('Optional').' ) '; ?>
                        <?php echo form_input('minimum_amount', '', 'class="form-control" id="minimum_amount" '); ?>
                    </div>

                        <div class="form-group">
                            <?= lang('Coupon_activation_date', 'coupon_start_date'); ?>
                            <?php echo form_input('coupon_start_date', '', 'readonly class="form-control tip date" id="coupon_start_date" required="required"'); ?>
                        </div>
                        <div class="form-group" style="display:none;">
                            <?= lang('products', 'products').' ( '.lang('Optional').' ) ' ?>
                            <?php

                            foreach ($products as $product) {
                                $pr[$product->id] = ' ( '.$product->code.' ) '.$product->name;
                            }
                            echo form_multiselect('products[]', $pr, '', 'multiple class="form-control " id="products" placeholder="' . lang('select') . ' ' . lang('products') . '" style="width:100%;overflow:hidden;height:auto;"')
                            ?>
                        </div>


                    </div>

                    <div class="col-md-6">

                    <div class="form-group">
                        <?= lang('Coupon_value', 'coupon_amount'); ?>
                        <?php echo form_input('coupon_amount', '', 'class="form-control" id="coupon_amount" required="required"'); ?>
                    </div>


                    <div class="form-group">
                        <?= lang('Usage_limit_per_coupon', 'usage_limit').' ( '.lang('Optional').' ) '; ?>
                        <?php echo form_input('usage_limit', '', 'class="form-control" id="usage_limit"'); ?>
                    </div>



                    <div class="form-group" style="display:none;">
                        <?= lang('Maximum_purchase_value', 'maximum_amount').' ( '.lang('Optional').' ) '; ?>
                        <?php echo form_input('maximum_amount', '', 'class="form-control" id="maximum_amount" '); ?>
                    </div>


                    <div class="form-group">
                            <?= lang('Coupon_expiry_date', 'coupon_expiry_date'); ?>
                            <?php echo form_input('coupon_expiry_date', '', 'readonly class="form-control tip date" id="coupon_expiry_date" required="required"'); ?>
                        </div>

                        <div class="form-group" style="display:none;">
                            <?= lang('category', 'product_categories').' ( '.lang('Optional').' ) ' ?>
                            <?php

                            foreach ($categories as $category) {
                                $cat[$category->id] = $category->name;
                            }
                            echo form_multiselect('product_categories[]', $cat, '', 'multiple class="form-control select" id="product_categories" placeholder="' . lang('select') . ' ' . lang('category') . '" style="width:100%;overflow:hidden;height:auto;"')
                            ?>
                        </div>

                    </div>
                </div>

                <div class="form-group">
				<label for="active">
					<?php echo form_radio('status', '1', '1', "id='active'"); ?> <?= lang('active'); ?>
				</label>

				<label for="inactive">
					<?php echo form_radio('status', '0', '', "id='inactive'"); ?> <?= lang('inactive'); ?>
				</label>



			    </div>

            </div>










        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_coupon', lang('add_coupon'), 'class="btn btn-primary"'); ?>
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
                $("[name='add_coupon']").attr('disabled', false);
            } else {
                $("[name='add_coupon']").attr('disabled', true);
            }
        });
        $(document).on('ifChecked', '#use_points', function (event) {
            $('#ca-points-con').slideDown();
        });
        $(document).on('ifUnchecked', '#use_points', function (event) {
            $('#ca-points-con').slideUp();
        });
        $('#genNo').click(function () {
            var coupon_gen_no = $('#coupon_gen_no').val();
            $('#result').load('<?php echo admin_url('sales/add_coupon'); ?>', {
				no_of_coupons: coupon_gen_no,
				length: '8',
				prefix: '',
				suffix: '',
				numbers: true,
				letters: true,
				symbols: false,
				random_register: false,
				mask: false
			});
            //var no = generateCardNo();
            //$(this).parent().parent('.input-group').children('input').val(no);
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
                url: site.base_url + "sales/get_award_points/" + selected_user,
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

    function exporttocsv() {
		if ($('#result').val()) {
			var a = document.createElement('a');
			with (a) {
				href='data:text/csv;base64,' + btoa($('#result').val());
				download='Coupon_codes.csv';
			}
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
	        }
        };

</script>