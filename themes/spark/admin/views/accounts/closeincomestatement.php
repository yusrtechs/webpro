<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Fiscal_year_closing'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('accounts/closeincomestatement', 'autocomplete="off"'); ?>
        <div class="modal-body col-md-12">
            <p><?= lang('enter_info'); ?></p>


			<div class="col-md-12">
            <div class="col-md-6 form-group">
                <?= lang('Year', 'Year'); ?>
                <?php
								 foreach ($period_year as $account) {
									$account = (array) $account;


				$wh[$account['period_year']] = $account['period_year'];


                                }
								 echo form_dropdown('period_old_year', $wh,  '', 'class="form-control" id="period_old_year" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Year') . '"  required="required"');
				?>

            </div>




			</div>








		</div>
        <div class="modal-footer">
            <?php echo form_submit('closeincomestatement', lang('Fiscal_year_closing'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#base_unit').change(function(e) {
            var bu = $(this).val();
            if(bu > 0)
                $('#measuring').slideDown();
            else
                $('#measuring').slideUp();

        });

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