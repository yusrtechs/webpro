<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_car_owner'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('carssuppliers/add', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <!--<div class="form-group">
                    <?= lang('type', 'type'); ?>
                    <?php $types = ['company' => lang('company'), 'person' => lang('person')];
            echo form_dropdown('type', $types, '', 'class="form-control select" id="type" required="required"'); ?>
                </div> -->

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                      <?= lang('type', 'Supplier_Type'); ?>
                    <?php $types = ['1' => lang('company'), '0' => lang('person')];
                    echo form_dropdown('co_to_co', $types, $customer->co_to_co, 'class="form-control select" id="co_to_co" required="required"'); ?>
                
                    </div>
					<div class="form-group company">
                        <?= lang('company', 'company'); ?>
                        <?php echo form_input('company', '', 'class="form-control tip" id="company" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang('name', 'name'); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group VAT_Regesteration_Number">
                        <?= lang('VAT_Regesteration_Number', 'VAT_Regesteration_Number'); ?>
                        <?php echo form_input('vat_no', '', 'class="form-control" id="vat_no"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('gst_no', 'gst_no'); ?>
                        <?php echo form_input('gst_no', '', 'class="form-control" id="gst_no"'); ?>
                    </div>
                    <!--<div class="form-group company">
                    <?= lang('contact_person', 'contact_person'); ?>
                    <?php echo form_input('contact_person', '', 'class="form-control" id="contact_person" data-bv-notempty="true"'); ?>
                </div>-->
                    <div class="form-group">
                        <?= lang('email_address', 'email_address'); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"/>
                    </div>
                    <div class="form-group">
                        <?= lang('phone', 'phone'); ?>
                        <input type="tel" name="phone" class="form-control" required="required" id="phone"/>
                    </div>
                    <div class="form-group">
                        <?= lang('address', 'address'); ?>
                        <?php echo form_input('address', '', 'class="form-control" id="address" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('city', 'city'); ?>
                        <?php echo form_input('city', '', 'class="form-control" id="city" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('state', 'state'); ?>
                        <?php
                        if ($Settings->indian_gst) {
                            $states = $this->gst->getIndianStates(true);
                            echo form_dropdown('state', $states, '', 'class="form-control select" id="state" required="required"');
                        } else {
                            echo form_input('state', '', 'class="form-control" id="state"');
                        }
                        ?>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <?= lang('co_type', 'co_type'); ?>
                    <?php $types = ['1' => lang('local'), '2' => lang('international')];
                    echo form_dropdown('co_type', $types, '', 'class="form-control select" id="co_type" required="required"'); ?>
                    </div>
				    <div class="form-group">
                        <?= lang('postal_code', 'postal_code'); ?>
                        <?php echo form_input('postal_code', '', 'class="form-control" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('country', 'country'); ?>
                        <?php echo form_input('country', '', 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('ID_Number', 'cf3'); ?>
                        <?php echo form_input('cf3', '', 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('Attorney_No', 'cf4'); ?>
                        <?php echo form_input('cf4', '', 'class="form-control" id="cf4"'); ?>

                    </div>
                    <div class="form-group">
					<?php
                    if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					?>
						<?= lang('BANK_NAME', 'BANK_NAME'); ?>
                    <?php
                    }else{
					?>
						<?= lang('scf1', 'cf1'); ?>
                    <?php
                    }
					?>
					    <?php echo form_input('cf1', '', 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                    <?php
                    if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					?>
						<?= lang('IBAN', 'IBAN'); ?>
                    <?php
                    }else{
					?>
					    <?= lang('scf2', 'cf2'); ?>
                    <?php
                    }
					?>
					    <?php echo form_input('cf2', '', 'class="form-control" id="cf2"'); ?>

                    </div>
                   
                    <div class="form-group">
                        <?= lang('scf5', 'cf5'); ?>
                        <?php echo form_input('cf5', '', 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang('scf6', 'cf6'); ?>
                        <?php echo form_input('cf6', '', 'class="form-control" id="cf6"'); ?>
                    </div>
                </div>
            </div>
            <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
				  ?>
                    <div class="form-group">
						<?= form_checkbox('is_auto_account', '1','', 'class="icheckbox_square" id="is_auto_account" '); ?>
						<?= lang('Create_an_account_automatically', 'Create_an_account_automatically'); ?>
					</div>

                    <?php
                        }
                        ?>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_supplier', lang('Add_car_owner'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>



<script type="text/javascript">
    $(document).ready(function (e) {
     
$('#co_to_co').change(function(e) {
        if($(this).val()==0){
		$(".company").css("display", "none");
		$(".VAT_Regesteration_Number").css("display", "none");
		$('#company').val($('#name').val());
		$('#vat_no').val('');
		
		}else{
		$(".company").css("display", "block");
		$(".VAT_Regesteration_Number").css("display", "block");
			
			
		}
		
    });
		
		$('#name').change(function(e) {
        if($('#co_to_co').val()==0){
			
			$('#company').val($(this).val());
		}
		
    });
	
	
	
		
    });
</script>


