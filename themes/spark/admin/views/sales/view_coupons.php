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
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_coupon'); ?></h4>
        </div>

        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
            <div class="col-md-3">
            <div class="form-group">
                <?= lang('Number_of_coupons', 'coupon_gen_no'); ?>

                    <?php echo form_input('coupon_gen_no', $coupons->coupon_gen_no, 'readonly type="number"  maxlength="3" class="form-control" id="coupon_gen_no" required="required"'); ?>

            </div>

            <div class="form-group">
                <button type="button" onclick="exporttocsv()" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> <span class="padding04"><?= lang('Export_coupon_codes_to_Excel'); ?></span></button>
            </div>


            </div>
            <div class="col-md-9">

            <div class="form-group">
                <?= lang('Coupon_codes', 'coupon_code'); ?>
                <textarea class="form-control coupon" placeholder="<?= lang('Coupon_codes'); ?>" name="coupon_code" id="result" rows="4" readonly=""  required="required"><?= $coupons->coupon_code; ?></textarea>
            </div>




            </div>
            </div>

            <div class="form-group">
                <?= lang('description', 'description'); ?>
                <?php echo form_input('description', $coupons->description, 'readonly class="form-control" id="description" required="required"'); ?>
            </div>

            <div class="well well-sm well_1">
                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                    <label class="control-label" for="discount_type"><?= lang('Discount_type'); ?></label>

                        <div class="controls">
                        <?php $opt_coupon =  ['percent' => lang('Percentage_discount'), 'fixed' => lang('Fixed_discount')]; ?>
                            <?php
                                echo form_dropdown('discount_type', $opt_coupon, $coupons->discount_type, 'class="form-control tip" required="required" id="discount_type" style="width:100%;"');
                            ?>
                        </div>
                    </div>


                <div class="form-group">
                    <?= lang('customer', 'customer'); ?>
                    <?php echo form_input('customer', $coupons->customer_id, 'readonly class="form-control" id="customer" required="required"'); ?>
                </div>





                    <div class="form-group" style="display:none;">
                        <?= lang('Minimum_purchase_value', 'minimum_amount').' ( '.lang('Optional').' ) '; ?>
                        <?php echo form_input('minimum_amount', $coupons->minimum_amount==0 ? '' : $this->sma->formatDecimal($coupons->minimum_amount) , 'readonly class="form-control" id="minimum_amount" '); ?>
                    </div>

                        <div class="form-group">
                            <?= lang('Coupon_activation_date', 'coupon_start_date'); ?>
                            <?php echo form_input('coupon_start_date', empty($coupons->coupon_start_date) ? '' : $this->sma->hrsd($coupons->coupon_start_date), 'readonly class="form-control tip " id="coupon_start_date"'); ?>
                        </div>
                        <div class="form-group" style="display:none;">
                            <?= lang('products', 'products').' ( '.lang('Optional').' ) ' ?>
                            <?php

                            foreach ($products as $product) {
                                $pr[$product->id] = ' ( '.$product->code.' ) '.$product->name;
                            }
                            echo form_multiselect('products[]', $pr, $coupons->products, 'multiple class="form-control " id="products" placeholder="' . lang('select') . ' ' . lang('products') . '" style="width:100%;overflow:hidden;height:auto;"')
                            ?>
                        </div>


                    </div>

                    <div class="col-md-6">

                    <div class="form-group">
                        <?= lang('Coupon_value', 'coupon_amount'); ?>
                        <?php echo form_input('coupon_amount', $this->sma->formatDecimal($coupons->coupon_amount), 'readonly class="form-control" id="coupon_amount" required="required"'); ?>
                    </div>


                    <div class="form-group">
                        <?= lang('Usage_limit_per_coupon', 'usage_limit').' ( '.lang('Optional').' ) '; ?>
                        <?php echo form_input('usage_limit', $coupons->usage_limit==0 ? '' : $coupons->usage_limit, 'readonly class="form-control" id="usage_limit"'); ?>
                    </div>



                    <div class="form-group" style="display:none;">
                        <?= lang('Maximum_purchase_value', 'maximum_amount').' ( '.lang('Optional').' ) '; ?>
                        <?php echo form_input('maximum_amount', $coupons->maximum_amount==0 ? '' : $this->sma->formatDecimal($coupons->maximum_amount), 'readonly class="form-control" id="maximum_amount" '); ?>
                    </div>


                    <div class="form-group">
                            <?= lang('Coupon_expiry_date', 'coupon_expiry_date'); ?>
                            <?php echo form_input('coupon_expiry_date', empty($coupons->coupon_expiry_date) ? '' : $this->sma->hrsd($coupons->coupon_expiry_date), 'readonly class="form-control tip" id="coupon_expiry_date"'); ?>
                        </div>

                        <div class="form-group" style="display:none;">
                            <?= lang('category', 'product_categories').' ( '.lang('Optional').' ) ' ?>
                            <?php

                            foreach ($categories as $category) {
                                $cat[$category->id] = $category->name;
                            }
                            echo form_multiselect('product_categories[]', $cat,  $coupons->product_categories, 'multiple class="form-control select" id="product_categories" placeholder="' . lang('select') . ' ' . lang('category') . '" style="width:100%;overflow:hidden;height:auto;"')
                            ?>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                <?php
                    if($coupons->status==1){
                        ?>
				<label for="active">

					<?php echo form_radio('status', '1', $coupons->status==1 ? '1' : '0', "id='active'"); ?> <?= lang('active'); ?>
				</label>
                <?php
                    }else{
                        ?>

				<label for="inactive">
					<?php echo form_radio('status', '0', $coupons->status==0 ? '1' : '0', "id='inactive'"); ?> <?= lang('inactive'); ?>
				</label>
                <?php
                    }
                        ?>


			    </div>

            </div>





        </div>

    </div>

</div>
<?= $modal_js ?>
<script type="text/javascript">

var tax_rates = <?php echo json_encode($tax_rates); ?>;

    $(document).ready(function () {
        $('#discount_type').select2('readonly', true);
        $('#products').select2('readonly', true);
        $('#product_categories').select2('readonly', true);
        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
		var paid='<?=$coupons->paid?>';
		if(paid==1)
		$('#div_paid_amount').slideDown();

        $('#customer').val('<?=$coupons->customer_id?>').select2({
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
        });//.select2("val", "<?=$coupons->customer_id?>");
        $('#genNo').click(function () {
            var coupon_gen_no = $('#coupon_gen_no').val();
            $('#result').load('<?php echo admin_url('sales/view_coupon'); ?>', {
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