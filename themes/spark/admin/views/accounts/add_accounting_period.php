<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_accounting_period'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open('accounts/add_accounting_period', 'autocomplete="off"'); ?>
        <div class="modal-body col-md-12">
            <p><?= lang('enter_info'); ?></p>
			
			
			<div class="col-md-12">
            <div class="form-group">
                <?= lang('Year', 'Year'); ?>
                <?= form_input('period_year', '', 'class="form-control tip" style="max-width:100px" id="period_year" required="required"'); ?>
            </div>
            <div class="col-md-6 form-group">
               <?= lang('Start_Period', 'Start_Period'); ?>
                 <?php echo form_input('period_from_date', '', 'class="form-control date" id="period_from_date" required="required"'); ?>
             </div>
			<div class="col-md-6 form-group">
               <?= lang('End_Period', 'End_Period'); ?>
                 <?php echo form_input('period_to_date', '', 'class="form-control date" id="period_to_date" required="required"'); ?>
             </div>
			
			
			
			</div>
            

        	
            
            

        
		
		</div>
        <div class="modal-footer">
            <?php echo form_submit('add_accounting_period', lang('Add_accounting_period'), 'class="btn btn-primary"'); ?>
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