<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_carowner'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-carowner-form'];
        echo admin_form_open_multipart('cars/addcarowner', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="display:none;">
                    <label class="control-label" for="customer_group"><?php echo $this->lang->line('customer_group'); ?></label>
                        <?php
                        foreach ($customer_groups as $customer_group) {
                            $cgs[$customer_group->id] = $customer_group->name;
                        }
                        echo form_dropdown('customer_group', $cgs, $Settings->customer_group, 'class="form-control select" id="customer_group" style="width:100%;" required="required"');
                        ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" style="display:none;">
                        <label class="control-label" for="price_group"><?php echo $this->lang->line('price_group'); ?></label>
                        <?php
                        $pgs[''] = lang('select') . ' ' . lang('price_group');
                        foreach ($price_groups as $price_group) {
                            $pgs[$price_group->id] = $price_group->name;
                        }
                        echo form_dropdown('price_group', $pgs, $Settings->price_group, 'class="form-control select" id="price_group" style="width:100%;"');
                        ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group company">
                        <?= lang('Company_name_in_Arabic', 'Company_name_in_Arabic'); ?>
                        <?php echo form_input('company', '', 'class="form-control tip" id="company" data-bv-notempty="true"'); ?>
                    </div>
                    
                    <div class="form-group person">
                        <?= lang('name', 'name'); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" '); ?>
                    </div>
                          <div class="form-group" style="display:none;">
                    <?= lang('co_type', 'co_type'); ?>
                    <?php $types = ['1' => lang('local'), '2' => lang('international')];
                    echo form_dropdown('co_type', $types, '', 'class="form-control select" id="co_type" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('VAT_Regesteration_Number', 'VAT_Regesteration_Number'); ?>
                        <?php echo form_input('vat_no', '', 'class="form-control" id="vat_no"'); ?>
                    </div>
                    <div class="form-group">
                            <?= lang('Address_in_Arabic', 'Address_in_Arabic'); ?>
                            <?php echo form_input('address', '', 'class="form-control" id="address" '); ?>
                        </div>   
                    <div class="form-group">
                        <?= lang('CR_Number', 'CR_Number'); ?>
                        <?php echo form_input('cr_no', '', 'class="form-control" id="cr_no"'); ?>
                    </div>
                    <div class="form-group company" style="display:none;">
                    <?= lang('contact_person', 'contact_person'); ?>
                    <?php echo form_input('contact_person', '', 'class="form-control" id="contact_person" '); ?>
                </div>
                  
                    
                    <div class="form-group">
                        <?= lang('city', 'city'); ?>
                        <?php echo form_input('city', '', 'class="form-control" id="city" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('state', 'state'); ?>
                        <?php
                        if ($Settings->indian_gst) {
                            $states = $this->gst->getIndianStates(true);
                            echo form_dropdown('state', $states, '', 'class="form-control select" id="state" ');
                        } else {
                            echo form_input('state', '', 'class="form-control" id="state"');
                        }
                        ?>
                    </div>
					
			

                </div>
                <div class="col-md-6">
                   <div class="form-group companyEn">
                        <?= lang('English_company_name', 'English_company_name'); ?>
                        <?php echo form_input('companyEn', '', 'class="form-control tip" id="companyEn" '); ?>
                    </div>
                    <div class="form-group person" style="display:none;">
                        <?= lang('nameEn', 'nameEn'); ?>
                        <?php echo form_input('nameEn', '', 'class="form-control tip" id="nameEn" '); ?>
                    </div>
               
				    <div class="form-group">
                        <?= lang('postal_code', 'postal_code'); ?>
                        <?php echo form_input('postal_code', '', 'class="form-control" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('country', 'country'); ?>
                        <?php echo form_input('country', '', 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group" dir="ltr" style="display:none;">
					
                        <?= $Settings->IsConstruction ? lang('PO_Number', 'PO_Number') : lang('cf1', 'cf1'); ?>
                        <?php echo form_input('cf1', '', 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('English_address', 'English_address'); ?>
                        <?php echo form_input('addressEn', '', 'class="form-control" id="addressEn" '); ?>
                    </div>
                    <div class="form-group">
                            <?= lang('email_address', 'email_address'); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"/>
                    </div>
                    <div class="form-group">
                        <?= lang('phone', 'phone'); ?>
                        <input type="tel" name="phone" class="form-control"  id="phone"/>
                    </div>
                   
               <!--     <div class="form-group">
                        <?= lang('ccf2', 'cf2'); ?>
                        <?php echo form_input('cf2', '', 'class="form-control" id="cf2"'); ?>

                    </div>
                    
                    <div class="form-group">
                        <?= lang('ccf3', 'cf3'); ?>
                        <?php echo form_input('cf3', '', 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('ccf4', 'cf4'); ?>
                        <?php echo form_input('cf4', '', 'class="form-control" id="cf4"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang('ccf5', 'cf5'); ?>
                        <?php echo form_input('cf5', '', 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang('ccf6', 'cf6'); ?>
                        <?php echo form_input('cf6', '', 'class="form-control" id="cf6"'); ?>
                    </div>
                    
                </div>
				    
            </div>
			
					<div class="form-group" style="display:none;">         
						<?= form_checkbox('hide_in_pos', '1','', 'class="icheckbox_square" id="hide_in_pos" '); ?>
						<?= lang('hide_in_pos', 'hide_in_pos'); ?>
					</div>
					-->


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_carowner', lang('Add_carowner'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $('#add-customer-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });
        $('select.select').select2({minimumResultsForSearch: 7});
        fields = $('.modal-content').find('.form-control');
        $.each(fields, function () {
            var id = $(this).attr('id');
            var iname = $(this).attr('name');
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
        });
    });
</script>
