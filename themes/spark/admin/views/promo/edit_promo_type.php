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
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Edit_promo_type'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("promo/update_promo_type", $attrib)
                ?>

                <div class="col-md-5">
                    <div class="form-group all">
                        <?= lang("Promo_type", "Promo_type") ?>
                        <?= form_input('promo_type_ar', (($promo_type ? $promo_type->promo_type_ar : '')), 'class="form-control gen_slug" id="promo_type_ar" required="required"'); ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("English_name", "English_name") ?>
                        <?= form_input('promo_type', (isset($_POST['promo_type']) ? $_POST['promo_type'] : ($promo_type ? $promo_type->promo_type : '')), 'class="form-control gen_slug" id="promo_type" required="required"'); ?>
                    </div>

          <div class="form-group" >
           <?= lang("Status", "Status")  ?>

                  <input type="checkbox" id="status"  name="status"  value="1"  <?php if($promo_type->status=="1"){ echo "checked"; }else{ echo "";}?>  >
                  <input type="hidden" name="id" value="<?=$promo_type->id?>" />

              </div>

                    <div class="form-group">
                        <?php echo form_submit('update_promo_type', $this->lang->line("Edit_promo_type"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

