<?php defined('BASEPATH') OR exit('No direct script access allowed');
 ?>
<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = array();
}

?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_promo_code'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("promo/add_promo_code", $attrib)
                ?>

                <div class="col-md-5">
                 
               <div class="form-group all">
				<?=lang("Promo", "Promo") ?>
                
                <select name="promo_id" class="form-control" id="promo_id"  required="" title="<?= lang("please_select_promo_id", "Please Select Promo") ?>">
                <?php  if (!empty($promos)) {
                        foreach ($promos as $p) { ?>
                         <option value="<?=$p->id?>" class="<?=$p->promo_type_id?>" ><?=$p->promo_name?></option>
                        <?php } } ?>
                </select> 
            
            </div>
                    
                    <div class="form-group all">
                        <?= lang("promo_code", "promo_code") ?>
                        <div class="input-group">
                            <?= form_input('promo_codee', (isset($_POST['promo_codee']) ? $_POST['promo_codee'] : ($promo_code ? $promo_code->promo_code : '')), 'class="form-control" id="promo_code"  required="required"') ?>
                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                <i class="fa fa-random"></i>
                            </span>
                        </div>
                        <span class="help-block"><?= lang('Enter Any unique code for promo') ?></span>
                    </div>
                    
              
                    
             
                    
                    <div class="form-group all">
                        <?= lang("total_uses_limit", "Total Uses Limit") ?>
                        <?= form_input('total_uses_limit', (isset($_POST['total_uses_limit']) ? $_POST['total_uses_limit'] : ($promo_code ? $promo_code->total_uses_limit : '')), 'class="form-control gen_slug" id="total_uses_limit" required="required"'); ?>
                    </div>
                    
                    <div class="form-group all">
                        <?= lang("per_user_uses_limit", "Per User Uses Limit") ?>
                        <?= form_input('per_user_uses_limit', (isset($_POST['per_user_uses_limit']) ? $_POST['per_user_uses_limit'] : ($promo_code ? $promo_code->per_user_uses_limit : '')), 'class="form-control gen_slug" id="per_user_uses_limit" required="required"'); ?>
                    </div>
                    
                    
                    
                     
                   <div class="form-group all">
           <?= lang("start_date", "Start Date") ?>
  <input type="text" name="start_date" id="available_date_from" placeholder="Start Date" class="form-control gen_slug" />

</div>
                  
                    
                         <div class="form-group all">
           <?= lang("end_date", "End Date") ?>
  <input type="text" name="end_date" id="available_date_to" placeholder="End Date" class="form-control gen_slug" />

</div>
                 
                    
          
          <div class="form-group" >
           <?= lang("status", "status") ?>
             
                  <input id="status"  name="status" value="1" type="checkbox" checked  >
                  
                
              </div>
                    
                    <div class="form-group">
                        <?php echo form_submit('add_promo_code', $this->lang->line("add_promo_code"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
   $(document).ready(function () {
					$("#available_date_to").datepicker({
						changeMonth: true,
						changeYear: true,
						dateFormat: "dd/mm/yy",
						onSelect: function (selected) {
							$("#available_date_from").datepicker("option", "maxDate", selected)
						}
					});
					$("#available_date_from").datepicker({
						changeMonth: true,
						changeYear: true,
						dateFormat: "dd/mm/yy",
						onSelect: function (selected) {
							$("#available_date_to").datepicker("option", "minDate", selected)
						}
					});
				});
				

  </script>