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
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_promo_type'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("promo/add_promo_type", $attrib)
                ?>

                <div class="col-md-5">
                    <div class="form-group all">
                        <?= lang("promo_type", "promo type") ?>
                        <?= form_input('promo_type', (isset($_POST['promo_type']) ? $_POST['promo_type'] : ($promo_type ? $promo_type->promo_type : '')), 'class="form-control gen_slug" id="promo_type" required="required"'); ?>
                    </div>
          
          <div class="form-group" >
           <?= lang("status", "Status") ?>
             
                  <input type="checkbox" id="status"  name="status"  value="1"  checked  >
                  
                
              </div>
                    
                    <div class="form-group">
                        <?php echo form_submit('add_promo_type', $this->lang->line("add_promo_type"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

