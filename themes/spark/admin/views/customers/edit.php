<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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
                    url: "<?= admin_url('system_settings/getCompaniesBygrpId') ?>/" + v,
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

$('#coId').val('').trigger('change.select2');

                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('no_model_id') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_model_id') ?>",
                                data: [{id: '', text: '<?= lang('no_model_id') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_customer'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'edit-customer-form'];
        echo admin_form_open_multipart('customers/edit/' . $customer->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                    <label class="control-label" for="customer_group"><?php echo $this->lang->line('customer_group'); ?></label>
                        <?php
                        foreach ($customer_groups as $customer_group) {
                            $cgs[$customer_group->id] = $customer_group->name;
                        }
                        echo form_dropdown('customer_group', $cgs, $customer->customer_group_id, 'class="form-control select" id="customer_group" style="width:100%;" required="required"');
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="price_group"><?php echo $this->lang->line('price_group'); ?></label>
                        <?php
                        $pgs[''] = lang('select') . ' ' . lang('price_group');
                        foreach ($price_groups as $price_group) {
                            $pgs[$price_group->id] = $price_group->name;
                        }
                        echo form_dropdown('price_group', $pgs, $customer->price_group_id, 'class="form-control select" id="price_group" style="width:100%;"');
                        ?>
                    </div>
                </div>
				<div class="col-md-4">
                     <?= lang('Customer_Type', 'Customer_Type'); ?>
                    <?php $types = ['1' => lang('company'), '0' => lang('person')];
                    echo form_dropdown('co_to_co', $types, $customer->co_to_co, 'class="form-control select" id="co_to_co" required="required"'); ?>

                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                <?php
                if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
                    if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                            {

                            ?>
                            <div class="form-group">
                                <?= lang('Main_group', 'Main_group') ?>
                                <?php
                                $cat[''] = lang('select') . ' ' . lang('Main_group');
                                foreach ($mainGroups as $mainGroup) {
                                    $cat[$mainGroup->id] = $mainGroup->name;
                                }
                echo form_dropdown('grpId', $cat, (isset($_POST['grpId']) ? $_POST['grpId'] : $customer->grpId), 'class="form-control select" id="grpId"  required="required" style="width:100%"')
                ?>
            </div>
            ?>
                            </div>
                            <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                            <div class="form-group">
                                <?= lang('Main_company', 'Main_company') ?>
                                <?php
                                $co[''] = lang('select') . ' ' . lang('Main_company');
                                if(($this->Settings->enable_grp==1)){}else{
                                    foreach ($mainCompanies as $mainCompany) {
                                        $co[$mainCompany->id] = $mainCompany->name;
                                    }
                                }

                echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : $customer->coId), 'class="form-control select" id="coId"  required="required" style="width:100%"')
                ?>
            </div>
            <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                            <div class="form-group">
                                <?= lang('Main_branch', 'Main_branch') ?>
                                <?php
                                $br[''] = lang('select') . ' ' . lang('Main_branch');
                                if(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)){}else{
                                    foreach ($mainBranches as $mainBranche) {
                                        $br[$mainBranche->id] = $mainBranche->name;
                                    }
                                }
                    echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $customer->brId), 'class="form-control select" id="brId"  required="required" style="width:100%"')
                    ?>
                </div>
                <?php } } ?>

                    <div class="form-group company">
                        <?= lang('Company_name_in_Arabic', 'Company_name_in_Arabic'); ?>
                        <?php echo form_input('company', $customer->company, 'class="form-control tip" id="company" required="required"'); ?>
                    </div>
                    <div class="form-group company">
                        <?= lang('English_company_name', 'English_company_name'); ?>
                        <?php echo form_input('companyEn', $customer->companyEn, 'class="form-control tip" id="companyEn"'); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang('name', 'name'); ?>
                        <?php echo form_input('name', $customer->name, 'class="form-control tip" id="name" '); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang('English_name', 'English_name'); ?>
                        <?php echo form_input('nameEn', $customer->nameEn, 'class="form-control tip" id="nameEn" '); ?>
                    </div>

                    <div class="form-group">
                        <?= lang('code','Code'); ?>
                        <div class="input-group">
                            <?php echo form_input('code', $customer->code, 'class="form-control" id="code"'); ?>
                            <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                <a href="#" id="genCustCode">
                                <i class="fa fa-random"></i>
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                    <?= lang('co_type', 'co_type'); ?>
                    <?php $types = ['1' => lang('local'), '2' => lang('international')];
                    echo form_dropdown('co_type', $types, $customer->co_type, 'class="form-control select" id="co_type" required="required"'); ?>
                    </div>

                    <div class="form-group co_type" style="display:none;">
				        <?= lang('order_tax', 'potax2') ?>
        				<?php
		        		$tr[''] = '';
				        foreach ($tax_rates as $tax) {
            				$tr[$tax->id] = $tax->name;
			        	}
        				echo form_dropdown('company_tax', $tr, $customer->company_tax, 'id="potax2" class="form-control input-tip select" style="width:100%;"'); ?>
                    </div>

				    <div class="form-group VAT_Regesteration_Number">
                        <?= lang('VAT_Regesteration_Number', 'VAT_Regesteration_Number').' ( '.$this->lang->line('Required_fifteen_digits').' ) '; ?>
                        <?php echo form_input('vat_no', $customer->vat_no, 'type="number" pattern="[0-9]{10}" maxlength="15" class="form-control" id="vat_no"'); ?>
                    </div>
                    <div class="form-group CR_Number">
                        <?= lang('CR_Number', 'CR_Number').' ( '.$this->lang->line('Required_ten_digits').' ) '; ?>
                        <?php
                        $db_name=str_replace("cpesoft_","",$this->db->database);
                        if($db_name!="anwary"){
                            echo form_input('cr_no', $customer->cr_no, 'type="number"  maxlength="10" class="form-control" id="cr_no"');
                        }else{
                            echo form_input('cr_no', $customer->cr_no, 'type="number" pattern="[0-9]{10}" maxlength="10" class="form-control" id="cr_no"');
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <?= lang('Address_in_Arabic', 'Address_in_Arabic'); ?>
                        <?php echo form_input('address', $customer->address, 'class="form-control" id="address" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('English_address', 'English_address'); ?>
                        <?php echo form_input('addressEn', $customer->addressEn, 'class="form-control" id="addressEn" '); ?>
                    </div>

                    <div class="form-group">
                        <?= lang('phone', 'phone'); ?>
                        <input type="tel" name="phone" class="form-control"  id="phone"
                               value="<?= $customer->phone ?>"/>
                    </div>

                    <div class="form-group">
                        <?= lang('email_address', 'email_address'); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"
                               value="<?= $customer->email ?>"/>
                    </div>



                           <div class="form-group">
                <label class="control-label" for="building_no"><?php echo $this->lang->line('Building_No').' ( '.$this->lang->line('Required_four_digits').' ) '; ?></label>
                <?php echo form_input('building_no', $customer->building_no, 'type="number" pattern="[0-9]{4}" maxlength="4" class="form-control" id="building_no"'); ?>
            </div>



					<div class="form-group">
                        <label class="control-label" for="additional_no"><?php echo $this->lang->line('Additional_No').' ( '.$this->lang->line('Required_four_digits').' ) '; ?></label>
                        <?php echo form_input('additional_no', $customer->additional_no, 'type="number" pattern="[0-9]{4}" maxlength="4" class="form-control" id="additional_no"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('postal_code', 'postal_code').' ( '.$this->lang->line('Required_five_digits').' ) '; ?>
                        <?php echo form_input('postal_code', $customer->postal_code, 'type="number" pattern="[0-9]{5}" maxlength="5" class="form-control" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('award_points', 'award_points'); ?>
                        <?= form_input('award_points', set_value('award_points', $customer->award_points), 'class="form-control tip" id="award_points"  required="required"'); ?>
                    </div>

                    <?php
            if(isset($this->Settings->isSubscriptions) && $this->Settings->isSubscriptions==1)
                    {
                    ?>
					<div class="form-group" >

                        <?= lang('Subscription_duration', 'subscription_duration'); ?>
                        <?php
		        		$sub[''] = '';
				        foreach ($subscriptions as $subscription) {

                            if ($this->Settings->user_language == 'arabic' || $subscription->name_en==''){
				                $sub[$subscription->id] = $subscription->name;
				            }else{
				                $sub[$subscription->id] = $subscription->name_en;
				            }
			        	}
        				echo form_dropdown('subscription_duration', $sub, $customer->subscription_duration, 'id="subscription_duration" class="form-control input-tip select" style="width:100%;"'); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo lang('Beginning_of_subscription', 'start_subscription'); ?>
                            <div class="controls">
                                <?php echo form_input('start_subscription', $customer->start_subscription ? set_value('start_subscription', $this->sma->hrsd($customer->start_subscription)) : '', 'class="form-control date" id="start_subscription"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo lang('End_of_subscription', 'end_subscription'); ?>
                            <div class="controls">
                                <?php echo form_input('end_subscription', $customer->end_subscription ? set_value('end_subscription', $this->sma->hrsd($customer->end_subscription)) : '', 'class="form-control date" id="end_subscription"'); ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    }
                    ?>


                </div>
                <div class="col-md-6">
                <div class="form-group">
                <label class="control-label" for="street_name"><?php echo $this->lang->line('Street_name'); ?></label>
                <?php echo form_input('street_name', $customer->street_name, 'class="form-control" id="street_name"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="street_nameEn"><?php echo $this->lang->line('English_street_name'); ?></label>
                <?php echo form_input('street_nameEn', $customer->street_nameEn, 'class="form-control" id="street_nameEn" '); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="district_name"><?php echo $this->lang->line('District_name'); ?></label>
                <?php echo form_input('district_name', $customer->district_name, 'class="form-control" id="district_name"'); ?>
            </div>
            <div class="form-group">
                <label class="control-label" for="district_nameEn"><?php echo $this->lang->line('English_district_name'); ?></label>
                <?php echo form_input('district_nameEn', $customer->district_nameEn, 'class="form-control" id="district_nameEn" '); ?>
            </div>

                    <div class="form-group">
                        <?= lang('city', 'city'); ?>
                        <?php echo form_input('city', $customer->city, 'class="form-control" id="city" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('English_city', 'English_city'); ?>
                        <?php echo form_input('cityEn', $customer->cityEn, 'class="form-control" id="cityEn" '); ?>
                    </div>

                    <div class="form-group">
                        <?= lang('state', 'state'); ?>
                        <?php
                        if ($Settings->indian_gst) {
                            $states = $this->gst->getIndianStates(true);
                            echo form_dropdown('state', $states, $customer->state, 'class="form-control select" id="state" ');
                        } else {
                            echo form_input('state', $customer->state, 'class="form-control" id="state"');
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <?= lang('English_state', 'English_state'); ?>
                        <?php
                        if ($Settings->indian_gst) {
                            $stateEn = $this->gst->getIndianStates(true);
                            echo form_dropdown('stateEn', $stateEn, $customer->stateEn, 'class="form-control select" id="stateEn" ');
                        } else {
                            echo form_input('stateEn', $customer->stateEn, 'class="form-control" id="stateEn"');
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <?= lang('country', 'country'); ?>
                        <?php echo form_input('country', $customer->country, 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('English_country', 'English_country'); ?>
                        <?php echo form_input('countryEn', $customer->countryEn, 'class="form-control" id="countryEn"'); ?>
                    </div>



                     <div class="form-group" dir="ltr">

                        <?= ($Settings->IsConstruction || $Settings->invoice_view==7) ? lang('PO_Number', 'PO_Number') : lang('cf1', 'cf1'); ?>
                       <?php echo form_input('cf1', $customer->cf1, 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('ID_Number', 'cf3'); ?>
                        <?php echo form_input('cf3', $customer->cf3, 'class="form-control" id="cf3"'); ?>
                    </div>

                      <?php
                    if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                    {
                    ?>
					 <div class="form-group" >
                        <?= lang('Attorney_No', 'cf4'); ?>
                        <?php echo form_input('cf4', $customer->cf4, 'class="form-control" id="cf4"'); ?>

                    </div>
                    <?php
                    }else{
                    ?>
                   <div class="form-group">
                        <?= lang('ccf4', 'cf4'); ?>
                        <?php echo form_input('cf4', $customer->cf4, 'class="form-control" id="cf4"'); ?>

                    </div>
                    <?php
                        }
                        ?>





                    <div class="form-group">
                        <?= lang('ccf2', 'cf2'); ?>
                        <?php echo form_input('cf2', $customer->cf2, 'class="form-control" id="cf2"'); ?>

                    </div>

                    <div class="form-group">
                        <?= lang('ccf5', 'cf5'); ?>
                        <?php echo form_input('cf5', $customer->cf5, 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang('ccf6', 'cf6'); ?>
                        <?php echo form_input('cf6', $customer->cf6, 'class="form-control" id="cf6"'); ?>
                    </div>

                </div>


			</div>


            <?php
                    if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                    {
                    ?>
					<div class="form-group" style="display:none;">
						<?php
                        $hide_in_pos=false;
                        if($customer->hide_in_pos==1)
                            $hide_in_pos=true;
                        ?>

						<?= form_checkbox('hide_in_pos', '1',$hide_in_pos, 'class="icheckbox_square" id="hide_in_pos" '); ?>
						<?= lang('hide_in_pos', 'hide_in_pos'); ?>
					</div>
                    <?php
                    }else{
                    ?>
                    <div class="form-group">
						<?php
                        $hide_in_pos=false;
                        if($customer->hide_in_pos==1)
                            $hide_in_pos=true;
                        ?>

						<?= form_checkbox('hide_in_pos', '1',$hide_in_pos, 'class="icheckbox_square" id="hide_in_pos" '); ?>
						<?= lang('hide_in_pos', 'hide_in_pos'); ?>
					</div>
                    <?php
                        }
                        ?>


                <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $customer->is_auto_account==0)
              {

				  ?>
                    <div class="form-group">
						<?= form_checkbox('is_auto_account', '1','', 'class="icheckbox_square" id="is_auto_account" '); ?>
						<?= lang('Create_an_account_automatically', 'Create_an_account_automatically'); ?>
					</div>

                    <div class="row" id="account_type-con" style="display: none;">

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Account_type', 'account_type'); ?>
                                <?php $types = ['0' => lang('Customer'), '1' => lang('Customer_and_supplier')];
                                echo form_dropdown('account_type', $types, '0', 'class="form-control select" id="account_type"'); ?>
                            </div>
                        </div>

                    </div>

                    <?php
                        }

                        ?>

                        <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1 && $customer->is_auto_advance_account==0 && $this->Settings->IsInsuranceBusiness && $this->Settings->invoice_view ==21)
              {

				  ?>
                    <div class="form-group">
						<?= form_checkbox('is_auto_advance_account', '1','', 'class="icheckbox_square" id="is_auto_advance_account" '); ?>
						<?= lang('Create_a_advance_payment_settlement_account', 'Create_a_advance_payment_settlement_account'); ?>
					</div>



                    <?php
                        }

                        ?>



        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_customer', lang('edit_customer'), 'class="btn btn-primary"'); ?>
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
		$(".companyEn").css("display", "none");
		$(".VAT_Regesteration_Number").css("display", "none");
		$(".CR_Number").css("display", "none");
		$('#company').val($('#name').val());
		$('#companyEn').val('');
		$('#vat_no').val('');
		$('#cr_no').val('');

		}else{
		$(".company").css("display", "block");
		$(".companyEn").css("display", "block");
		$(".VAT_Regesteration_Number").css("display", "block");
		$(".CR_Number").css("display", "block");


		}

    });

    $('#co_type').change(function(e) {
        if($(this).val()==1){
		$(".co_type").css("display", "none");

		}else if($(this).val()==2){
		$(".co_type").css("display", "block");
		}else{
		$(".co_type").css("display", "none");
		}

    });

		$('#name').change(function(e) {
        if($('#co_to_co').val()==0){

			$('#company').val($(this).val());
		}

    });


	if($('#co_to_co').val()==0){
		$(".company").css("display", "none");
		$(".companyEn").css("display", "none");
		$(".VAT_Regesteration_Number").css("display", "none");
		$(".CR_Number").css("display", "none");
		}else{
		$(".company").css("display", "block");
		$(".companyEn").css("display", "block");
		$(".VAT_Regesteration_Number").css("display", "block");
		$(".CR_Number").css("display", "block");


		}


        if($("#co_type").val()==1){
		$(".co_type").css("display", "none");

		}else if($("#co_type").val()==2){
		$(".co_type").css("display", "block");
		}else{
		$(".co_type").css("display", "none");
		}




    });

</script>

<script>
    $(document).ready(function () {

       $('#grpId').select2('readonly', true);
       $('#coId').select2('readonly', true);
       $('#brId').select2('readonly', true);

       $('#is_auto_account').on('ifChecked', function () {
            $('#account_type-con').slideDown();
        });
        $('#is_auto_account').on('ifUnchecked', function () {
            $('#account_type-con').slideUp();
        });

        $('#genCustCode').click(function() {
        var no = generateCardNo(8);
        $(this)
            .parent()
            .parent('.input-group')
            .children('input')
            .val(no);
        return false;
        });

    }
    );

</script>
