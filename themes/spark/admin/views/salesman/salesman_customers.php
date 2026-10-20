<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>



<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Salesman_customers') . ' (' . $company->company . ')'; ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('salesman/salesman_customers/' . $company->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>


            <div class="row">
            <div class="container">

	<div class="row">
				<div class="col-xs-5">
                <select name="from[]" id="lstview" class="form-control" size="8" multiple="multiple">

                <?php
                    foreach ($customerCompanies as $customerCompany) {



                    ?>
                    <option  value="<?= $customerCompany->id ?>"><?= ($Settings->user_language != 'arabic' && $customerCompany->companyEn!="") ? $customerCompany->companyEn : $customerCompany->company ?></option>
                    <?php




                    }
                    ?>

					</select>
				</div>

				<div class="col-xs-2">
					<button type="button" id="lstview_undo" class="btn btn-danger btn-block"><?= lang('Undo'); ?></button>
                    <?php
                    if ($Settings->user_language == 'arabic') {
                    ?>
					<button type="button" id="lstview_rightAll" class="btn btn-default btn-block"><i class="fa fa-backward"></i></button>
					<button type="button" id="lstview_rightSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-right"></i></button>
					<button type="button" id="lstview_leftSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-left"></i></button>
					<button type="button" id="lstview_leftAll" class="btn btn-default btn-block"><i class="fa fa-forward"></i></button>
					<?php
                    }else{
                    ?>
                    <button type="button" id="lstview_rightAll" class="btn btn-default btn-block"><i class="fa fa-forward"></i></button>
					<button type="button" id="lstview_rightSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-right"></i></button>
					<button type="button" id="lstview_leftSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-left"></i></button>
					<button type="button" id="lstview_leftAll" class="btn btn-default btn-block"><i class="fa fa-backward"></i></button>

					<?php
                    }
                    ?>

                    <button type="button" id="lstview_redo" class="btn btn-warning btn-block"><?= lang('Redo'); ?></button>
				</div>

				<div class="col-xs-5">
                    <select name="to[]" id="lstview_to" class="form-control" size="8" multiple="multiple">
                    <?php
                    foreach ($salesmanCustomers as $salesmanCustomer) {




                        ?>
                <option  value="<?= $salesmanCustomer->customer_id ?>"><?= ($Settings->user_language != 'arabic' && $salesmanCustomer->companyEn!="") ? $salesmanCustomer->companyEn : $salesmanCustomer->company ?></option>
                    <?php

                    }
                    ?>

                    </select>
				</div>
	</div>
</div>






        </div>
        <div class="modal-footer">
            <?php echo form_submit('salesman_customers', lang('submit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script src="<?= $assets ?>js/multiselect.js"></script>



<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#lstview').multiselect({
        search: {
            left: '<input type="text" name="q" class="form-control" placeholder="<?= lang('Search'); ?>" />',
            right: '<input type="text" name="q" class="form-control" placeholder="<?= lang('Search'); ?>" />',
        }
    });
});
</script>





