<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_service'); ?></h4>
        </div>
       
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('cars/edit_service/' . $service->id, $attrib); ?>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="mError"></span>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                    <div class="form-group all">
                        <?= lang('Service_code', 'code') ?>
                        <div class="input-group">
                            <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($service ? $service->code : '')), 'class="form-control" id="code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                        	</div>
                            <div class="form-group">
                            <?= lang('name', 'name') ?> 
                           <!-- <input type="text" class="form-control" name="name" id="name"> -->
                        
                            <?= form_input('name', set_value('name', ($service ? $service->name : '')), 'class="form-control tip" id="name" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('English_name','second_name') ?> 
                            <?= form_input('second_name', set_value('second_name', ($service ? $service->second_name : '')), 'class="form-control tip" id="second_name" '); ?>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('cf1', 'cf1') ?>
                                <?= form_input('cf1', (isset($_POST['cf1']) ? $_POST['cf1'] : ($service ? $service->cf1 : '')), 'class="form-control tip" id="cf1"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('cf2', 'cf2') ?>
                                <?= form_input('cf2', (isset($_POST['cf2']) ? $_POST['cf2'] : ($service ? $service->cf2 : '')), 'class="form-control tip" id="cf2"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('cf3', 'cf3') ?>
                                <?= form_input('cf3', (isset($_POST['cf3']) ? $_POST['cf3'] : ($service ? $service->cf3 : '')), 'class="form-control tip" id="cf3"') ?>
                            </div>
                        </div>
                        <div class="form-group all">
                        <?= lang('Service_details', 'service_details') ?>
                        <?= form_textarea('service_details', (isset($_POST['service_details']) ? $_POST['service_details'] : ($service ? $service->service_details : '')), 'class="form-control" id="service_details"'); ?>
                    </div>
               

                    </div>
                    <div class="col-md-6 col-sm-6" >
                        <div class="form-group">
                             <?= lang('price', 'price'); ?>
                             <?= form_input('price', set_value('price', ($service ? $service->price : '')), 'class="form-control tip" id="price" '); ?>
                        </div>
                        
                            <div class="form-group">
                                <?= lang('Tax', 'tax_rate') ?>
                                <?php
                                $tr[''] = '';
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('tax_rate', $tr, $service->tax_rate, 'id="tax_rate" class="form-control input-tip select" style="width:100%;"'); ?>
                            </div>
                            <div class="form-group all">
                                <?= lang('tax_method', 'mtax_method') ?>
                                <?php
                                $tm = ['0' => lang('inclusive'), '1' => lang('exclusive')];
                                echo form_dropdown('tax_method', $tm, $service->tax_method, 'class="form-control select" id="mtax_method" placeholder="' . lang('select') . ' ' . lang('tax_method') . '" style="width:100%"')
                                ?>
                            </div>

                            
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('cf4', 'cf4') ?>
                                <?= form_input('cf4', (isset($_POST['cf4']) ? $_POST['cf4'] : ($service ? $service->cf4 : '')), 'class="form-control tip" id="cf4"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('cf5', 'cf5') ?>
                                <?= form_input('cf5', (isset($_POST['cf5']) ? $_POST['cf5'] : ($service ? $service->cf5 : '')), 'class="form-control tip" id="cf5"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('cf6', 'cf6') ?>
                                <?= form_input('cf6', (isset($_POST['cf6']) ? $_POST['cf6'] : ($service ? $service->cf6 : '')), 'class="form-control tip" id="cf6"') ?>
                            </div>
                        </div>

                        <div class="form-group all">
                        <?= lang('Service_details_for_invoice', 'details') ?>
                        <?= form_textarea('details', (isset($_POST['details']) ? $_POST['details'] : ($service ? $service->details : '')), 'class="form-control" id="details"'); ?>
                    </div>


                        
                    </div>
                </div>
                   
            </div>
            <div class="modal-footer">
            <?php echo form_submit('edit_service', lang('Edit_service'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {

$('#random_num').click(function() {
        $(this)
            .parent('.input-group')
            .children('input')
            .val(generateCardNo(8));
    });
    function generateCardNo(x) {
    if (!x) {
        x = 16;
    }
    chars = '1234567890';
    no = '';
    for (var i = 0; i < x; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        no += chars.substring(rnum, rnum + 1);
    }
    return no;
}
});
</script>
