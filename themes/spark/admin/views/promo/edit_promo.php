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
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_promo'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("promo/update_promo", $attrib)
                ?>

                 <input type="hidden" name="id" value="<?=$promo->id?>" />
                <div class="col-md-5">


            <div class="form-group all">
				<?= lang("Promo_type", "Promo_type ") ?>*
             <?php
			// $ds = $this->promo_model->getCategoryList();
			// print_r($ds);


			 // $ds = $this->promo_model->getCategoryList(0,'parent_id','TOP',$promo->id);
			// print_r($ds);
			 ?>
			<select name="promo_type_id" class="form-control" id="promo_type" onChange="promo_select()"  required="required" title="<?= lang("please_select_promo_type", "Please Select promo type") ?>">

          <?php  if (!empty($promo_type)) {
                        foreach ($promo_type as $pr) {
                            if($promo->promo_type_id==$pr->id) { ?>
                         <option value="<?=$pr->id?>" <?php if($promo->promo_type_id==$pr->id){ echo "selected"; }else{ echo "";}?>  ><?= $this->Settings->user_language == 'arabic' ? $pr->promo_type_ar : $pr->promo_type ?></option>

                        <?php } } } ?>

                </select>


            </div>

                    <div class="form-group all">
                        <?= lang("Promo_name", "Promo_name") ?>*
                        <?= form_input('promo_name', (isset($_POST['promo_name']) ? $_POST['promo_name'] : ($promo ? $promo->promo_name : '')), 'class="form-control gen_slug" id="promo_name" required="required"'); ?>
                    </div>



          			<div class="form-group" >
           <?= lang("Status", "Status") ?>

                  <input id="status"  name="status" value="1" type="checkbox" <?php if($promo->status=="1"){ echo "checked"; }else{ echo "";}?>  >


              </div>

                    <div class="form-group">
                        <?php echo form_submit('update_promo', $this->lang->line("edit_promo"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5" id="promo_condition">




 <span id="promo_type1"  style="display:none;">
                <div class="form-group all">
                        <?= lang("cart_min_value", "Cart Min Value") ?>
                        <?= form_input('cart_min_value', (isset($_POST['cart_min_value']) ? $_POST['cart_min_value'] : ($promo ? $promo->cart_min_value : '')), 'class="form-control gen_slug" id="cart_min_value" required=""'); ?>
                    </div>
            <div class="form-group all">
            <select name="sel_option" class="form-control" id="sel_option" onChange="sel_op()"  required="required" title="<?= lang("please_select_option", "Please Select Option") ?>">
            <option value="cashback" <?php if($promo->sel_option=="cashback"){ echo "selected"; }else{ echo "";}?>  >Cashback</option>

            <option value="gift" <?php if($promo->sel_option=="cashback"){ echo "selected"; }else{ echo "";}?>  >Gift Product</option>


            </select>

            </div>


                    <div class="form-group all" id="op1"  style="display:none;">
                    <?= lang("cashback", "Cashback") ?>*
                        <?= form_input('cashback', (isset($_POST['cashback']) ? $_POST['cashback'] : ($promo ? $promo->cashback : '')), 'class="form-control gen_slug" id="cashback" required="required"'); ?>
                    </div>


              <div class="form-group all" id="op2" style="display:none;">
				<?= lang("gift_product", "Gift Product") ?>*

                <select name="gift_product" class="form-control" id="gift_product1"  required="required" title="<?= lang("please_select_gift_product", "Please Select Gift product") ?>">
                         <?php  if (!empty($gift_product1)) {
                        foreach ($gift_product1 as $gp) { ?>
                         <option value="<?=$gp->id?>" <?php if($promo->gift_product==$gp->id){ echo "selected"; }else{ echo "";}?>  ><?=$gp->name?></option>

                        <?php } } ?>
                </select>

            </div>
            </span>

             <span id="promo_type2"  style="display:none;">
                  <div class="form-group all">
				<?= lang("offer_category", "Offer Category") ?>*
                <?=$this->promo_model->showCategoryList(NULL,'offer_category','TOP',$promo->offer_category)?>


            </div>
             <div class="form-group all">
				<?= lang("gift_product", "Gift Product") ?>*

                <select name="gift_product2" class="form-control" id="gift_product2"  required="required" title="<?= lang("please_select_gift_product", "Please Select Gift product") ?>">

                 <?php  if (!empty($gift_product1)) {
                        foreach ($gift_product1 as $gp) { ?>
                         <option value="<?=$gp->id?>"  <?php if($promo->gift_product==$gp->id){ echo "selected"; }else{ echo "";}?> ><?=$gp->name?></option>

                        <?php } } ?>

                </select>

            </div>


            </span>

                 <span id="promo_type3"  style="display:none;">


                 <div class="form-group all" style="display:none;">
				<?= lang("offer_category", "Offer Category") ?>*
                <?=$this->promo_model->showCategoryList(NULL,'offer_category3','TOP',$promo->offer_category)?>
           		 </div>

                       <div class="form-group all">

                       <?= lang('product2buy', 'offer_product3'); ?>


                    <?php
                    foreach ($gift_product as $gp) {
                        $opr3[$gp->id] = $gp->name;
                    }

                    echo form_dropdown('offer_product3', $opr3, $promo->offer_product, 'id="offer_product3" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('product2buy') . '" required="required" style="width:100%;" ');

                    ?>


            </div>

                     <div class="form-group all">
                       <?= lang("buy_quantity", "Buy Quantity") ?>
                        <?= form_input('buy_quantity3', (isset($_POST['buy_quantity']) ? $_POST['buy_quantity'] : ($promo ? $promo->buy_quantity : '')), 'class="form-control gen_slug" id="buy_quantity3" required=""'); ?>
                    </div>



					       <div class="form-group all">

                           <?= lang('product2get', 'free_product3'); ?>

                <?php

                    foreach ($gift_product as $gp) {
                        $fpr3[$gp->id] = $gp->name;
                    }

                    echo form_dropdown('free_product3', $fpr3, $promo->free_product, 'id="free_product3" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('product2get') . '" required="required" style="width:100%;" ');

                    ?>


            </div>


                     <div class="form-group all">
                        <?= lang("get_quantity", "Get Quantity") ?>
                        <?= form_input('get_quantity3', (isset($_POST['get_quantity']) ? $_POST['get_quantity'] : ($promo ? $promo->get_quantity : '')), 'class="form-control gen_slug" id="get_quantity3" required=""'); ?>
                    </div>

					<div class="form-group">
                            <?= lang('start_date', 'start_date'); ?>
                            <?php echo form_input('start_date3', $promo->start_date ? $this->sma->hrsd($promo->start_date) : '', 'class="form-control tip date" id="start_date3"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('end_date', 'end_date'); ?>
                            <?php echo form_input('end_date3', $promo->end_date ? $this->sma->hrsd($promo->end_date) : '', 'class="form-control tip date" id="end_date3"'); ?>
                        </div>



            </span>


             <span id="promo_type4"  style="display:none;">




            <div class="form-group all"  id="div_offer_category4">
				<?= lang("category", "category") ?>
                <select name="offer_category4" class="form-control" id="offer_category4" >
                <option value=""  ></option>
                        <?php  if (!empty($promo_categories) && $promo->hide_opt==2) {
                        foreach ($promo_categories as $gp) { ?>
                         <option value="<?=$gp->id?>"   <?php if($promo->offer_category==$gp->id){ echo "selected"; }else{ echo "";}?>  ><?=$gp->name?></option>

                        <?php } } ?>
                </select>


            </div>


               <div class="form-group all"  id="div_offer_product4">

               <?= lang("product", "offer_product4") ?>

                <?php
                    foreach ($gift_product as $gp) {
                        $opr4[$gp->id] = $gp->name;
                    }

                    echo form_dropdown('offer_product4', $opr4, $promo->offer_product, 'id="offer_product4" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('product') . '" required="required" style="width:100%;" ');

                    ?>




            </div>






            <div class="form-group all">
					<?= lang('buy_quantity', 'Buy Quantity') ?>
					<input class="form-control gen_slug"  name="buy_quantity4" id="buy_quantity4"  value="<?= $promo->buy_quantity ? $promo->buy_quantity : '1' ?>" type="number" min="1" step="1" required />
			</div>
            <div class="form-group all">
                        <?= lang("product_discount", "Percentage Off") ?>
                        <?= form_input('percentage_off4', (isset($_POST['percentage_off4']) ? $_POST['percentage_off4'] : ($promo ? $promo->percentage_off : '')), 'class="form-control gen_slug" id="percentage_off4" required=""'); ?>
                    </div>
			<div class="form-group">
                            <?= lang('start_date', 'start_date'); ?>
                            <?php echo form_input('start_date4', $promo->start_date ? $this->sma->hrsd($promo->start_date) : '', 'class="form-control tip date" id="start_date4"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('end_date', 'end_date'); ?>
                            <?php echo form_input('end_date4', $promo->end_date ? $this->sma->hrsd($promo->end_date) : '', 'class="form-control tip date" id="end_date4"'); ?>
                        </div>

            </span>



            <span id="promo_type5"  style="display:none;">


             <div class="form-group all">
                        <?= lang("percentage_off", "Percentage Off") ?>
                        <?= form_input('percentage_off5', (isset($_POST['percentage_off5']) ? $_POST['percentage_off5'] : ($promo ? $promo->percentage_off : '')), 'class="form-control gen_slug" id="percentage_off5" required=""'); ?>
                    </div>
            </span>



            <span id="promo_type6"  style="display:none;">



             <div class="form-group all">
                        <?= lang("percentage_off", "Percentage Off") ?>
                        <?= form_input('percentage_off6', (isset($_POST['percentage_off6']) ? $_POST['percentage_off6'] : ($promo ? $promo->percentage_off : '')), 'class="form-control gen_slug" id="percentage_off6" required=""'); ?>
                    </div>

                     <div class="form-group all">
                            Start Time   <select name="daily_start_time" class="form-control" id="start"  required=""  >

                       <?php

					   for($hours=0; $hours<24; $hours++){ // the interval for hours is '1'
    for($mins=0; $mins<60; $mins+=30){ // the interval for mins is '30'
	$str1=str_pad($hours,2,'0',STR_PAD_LEFT).':'.str_pad($mins,2,'0',STR_PAD_LEFT);?>

       <option value="<?=$str1?>" <?php if($promo->daily_start_time==$str1){ echo "selected"; }else{ echo "";}?> ><?=$str1?></option>
       <?php } } ?>
                        </select>
                    </div>


                     <div class="form-group all">
                  End Time   <select name="daily_end_time" class="form-control" id="end"  required="" >
                <?php

					   for($hours=0; $hours<24; $hours++){ // the interval for hours is '1'
    for($mins=0; $mins<60; $mins+=30){ // the interval for mins is '30'
	$str2=str_pad($hours,2,'0',STR_PAD_LEFT).':'.str_pad($mins,2,'0',STR_PAD_LEFT);?>

       <option value="<?=$str2?>" <?php if($promo->daily_end_time==$str2){ echo "selected"; }else{ echo "";}?> ><?=$str2?></option>
       <?php } } ?>
                        </select>
                    </div>

            </span>


<span id="promo_type7"  style="display:none;">

<div class="form-group all" style="display:none;">
				<?= lang("offer_category", "Offer Category") ?>*
                <?=$this->promo_model->showCategoryList(NULL,'offer_category7','TOP',$promo->offer_category)?>
           		 </div>

                    <div class="form-group all" style="padding-bottom: 100px;">
				<?= lang('product2buy', 'suggest_product'); ?>

<?php
foreach ($gift_product as $gp) {
    $wh[$gp->id] = $gp->name;
}
foreach ($promo_details as $promo_detail) {
    $whselected[] = $promo_detail->offer_product;

}
echo form_multiselect('offer_product7[]', $wh, $whselected, 'id="offer_product7" class="form-control input-tip multiselect" data-placeholder="' . lang('select') . ' ' . lang('product2buy') . '" required="required" style="width:100%;" ');

?>


</div>

<div class="form-group all">
                       <?= lang("buy_quantity", "Buy Quantity") ?>
                        <?= form_input('buy_quantity7', (isset($_POST['buy_quantity']) ? $_POST['buy_quantity'] : ($promo ? $promo->buy_quantity : '')), 'class="form-control gen_slug" id="buy_quantity7" required=""'); ?>
                    </div>



					       <div class="form-group all">

                <?= lang('product2get', 'free_product7'); ?>

                <?php
                    foreach ($gift_product as $gp) {
                        $fpr7[$gp->id] = $gp->name;
                    }

                    echo form_dropdown('free_product7', $fpr7, $promo->free_product, 'id="free_product7" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('product2get') . '" required="required" style="width:100%;" ');

                ?>



            </div>


                     <div class="form-group all">
                        <?= lang("get_quantity", "Get Quantity") ?>
                        <?= form_input('get_quantity7', (isset($_POST['get_quantity']) ? $_POST['get_quantity'] : ($promo ? $promo->get_quantity : '')), 'class="form-control gen_slug" id="get_quantity7" required=""'); ?>
                    </div>

					<div class="form-group">
                            <?= lang('start_date', 'start_date'); ?>
                            <?php echo form_input('start_date7', $promo->start_date ? $this->sma->hrsd($promo->start_date) : '', 'class="form-control tip date" id="start_date7"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('end_date', 'end_date'); ?>
                            <?php echo form_input('end_date7', $promo->end_date ? $this->sma->hrsd($promo->end_date) : '', 'class="form-control tip date" id="end_date7"'); ?>
                        </div>


</span>

<span id="promo_type8"  style="display:none;">


                                    <div class="form-group all" style="display:none;">
				<?= lang("offer_category", "Offer Category") ?>*
                <?=$this->promo_model->showCategoryList(NULL,'offer_category8','TOP',$promo->offer_category)?>


            </div>

               <div class="form-group all">
				<?= lang("products", "Offer Product") ?>




<?php
foreach ($gift_product as $gp) {
    $wh[$gp->id] = $gp->name;
}
foreach ($promo_details as $promo_detail) {
    $whselected[] = $promo_detail->offer_product;

}
echo form_multiselect('offer_product8[]', $wh, $whselected, 'id="offer_product8" class="form-control input-tip multiselect" data-placeholder="' . lang('select') . ' ' . lang('products') . '" required="required" style="width:100%;" ');

?>


            </div>





             <div class="form-group all">
                        <?= lang("product_discount", "Percentage Off") ?>
                        <?= form_input('percentage_off8', (isset($_POST['percentage_off8']) ? $_POST['percentage_off8'] : ($promo ? $promo->percentage_off : '')), 'class="form-control gen_slug" id="percentage_off8" required=""'); ?>
                    </div>
			<div class="form-group">
                            <?= lang('start_date', 'start_date'); ?>
                            <?php echo form_input('start_date8', $promo->start_date ? $this->sma->hrsd($promo->start_date) : '', 'class="form-control tip date" id="start_date8"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('end_date', 'end_date'); ?>
                            <?php echo form_input('end_date8', $promo->end_date ? $this->sma->hrsd($promo->end_date) : '', 'class="form-control tip date" id="end_date8"'); ?>
                        </div>

            </span>


            <span id="promo_type9"  style="display:none;">

<div class="form-group all" style="display:none;">
				<?= lang("offer_category", "Offer Category") ?>*
                <?=$this->promo_model->showCategoryList(NULL,'offer_category9','TOP',$promo->offer_category)?>
           		 </div>

                    <div class="form-group all" style="padding-bottom: 100px;">
				<?= lang('product2buy', 'suggest_product'); ?>

<?php
foreach ($gift_product as $gp) {
    $wh[$gp->id] = $gp->name;
}
foreach ($promo_details as $promo_detail) {
    $whselected[] = $promo_detail->offer_product;

}
echo form_multiselect('offer_product9[]', $wh, $whselected, 'id="offer_product9" class="form-control input-tip multiselect" data-placeholder="' . lang('select') . ' ' . lang('product2buy') . '" required="required" style="width:100%;" ');

?>


</div>

<div class="form-group all"  style="display:none;">
                       <?= lang("buy_quantity", "Buy Quantity") ?>
                        <?= form_input('buy_quantity9', 1, 'class="form-control gen_slug" id="buy_quantity9" required=""'); ?>
                    </div>



					       <div class="form-group all"  style="display:none;">
				<?= lang('product2get', 'suggest_product'); ?>

                <select name="free_product9" class="form-control" id="free_product9"  required="" title="<?= lang("please_select_offer_product", "Please Select offer product") ?>">
                <?php  if (!empty($gift_product1)) {
                        foreach ($gift_product1 as $gp) { ?>
                         <option value="<?=$gp->id?>" <?php if($promo->free_product==$gp->id){ echo "selected"; }else{ echo "";}?>  ><?=$gp->name?></option>

                        <?php } } ?>
                </select>

            </div>


                     <div class="form-group all"  style="display:none;">
                        <?= lang("get_quantity", "Get Quantity") ?>
                        <?= form_input('get_quantity9', 1, 'class="form-control gen_slug" id="get_quantity9" required=""'); ?>
                    </div>

					<div class="form-group">
                            <?= lang('start_date', 'start_date'); ?>
                            <?php echo form_input('start_date9', $promo->start_date ? $this->sma->hrsd($promo->start_date) : '', 'class="form-control tip date" id="start_date9"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('end_date', 'end_date'); ?>
                            <?php echo form_input('end_date9', $promo->end_date ? $this->sma->hrsd($promo->end_date) : '', 'class="form-control tip date" id="end_date9"'); ?>
                        </div>


        </span>



        <span id="promo_type10"  style="display:none;">



            <input type="hidden" name="su10" id="su10" value="<?= $su10; ?>" />

            <div class="form-group all"  id="div_offer_category10">
				<?= lang("category", "category") ?>
                <select name="offer_category10" class="form-control" id="offer_category10" >
                <option value=""  ></option>
                        <?php  if (!empty($promo_categories) && $promo->hide_opt==2) {
                        foreach ($promo_categories as $gp) { ?>
                         <option value="<?=$gp->id?>"   <?php if($promo->offer_category==$gp->id){ echo "selected"; }else{ echo "";}?>  ><?=$gp->name?></option>

                        <?php } } ?>
                </select>


            </div>


            <div class="form-group standard">
                    <div class="form-group">
                        <?= lang('product', 'product') ?>
                        <button type="button" class="btn btn-primary btn-xs" id="addProduct"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="row" id="product-con">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <?php
                                echo form_input('offer_product10', $promo->offer_product, 'class="form-control products" id="offer_product10"   required="required" placeholder="' . lang('select') . ' ' . lang('product') . '" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <?= form_input('buy_quantity10', ($promo->buy_quantity ? $promo->buy_quantity : '1'), ' type="text" class="form-control tip" id="buy_quantity10"  placeholder="' . lang('buy_quantity') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                            <?= form_input('fixed_off10', ($promo ?$this->sma->formatDecimal($promo->fixed_off) : ''), 'type="number" min=".01" step=".01" class="form-control tip" id="fixed_off10" placeholder="' . lang('product_discount') . '"   required="required"'); ?>
                            </div>
                        </div>

                    </div>
                    <div id="ex-products"></div>
                </div>



			<div class="form-group">
                            <?= lang('start_date', 'start_date'); ?>
                            <?php echo form_input('start_date10', $promo->start_date ? $this->sma->hrsd($promo->start_date) : '', 'class="form-control tip date" id="start_date10"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('end_date', 'end_date'); ?>
                            <?php echo form_input('end_date10', $promo->end_date ? $this->sma->hrsd($promo->end_date) : '', 'class="form-control tip date" id="end_date10"'); ?>
                        </div>

            </span>



            <span id="promo_type11"  style="display:none;">


                                    <div class="form-group all" style="display:none;">
				<?= lang("offer_category", "Offer Category") ?>*
                <?=$this->promo_model->showCategoryList(NULL,'offer_category11','TOP',$promo->offer_category)?>


            </div>

               <div class="form-group all">
				<?= lang("products", "Offer Product") ?>




<?php
foreach ($gift_product as $gp) {
    $wh[$gp->id] = $gp->name;
}
foreach ($promo_details as $promo_detail) {
    $whselected[] = $promo_detail->offer_product;

}
echo form_multiselect('offer_product11[]', $wh, $whselected, 'id="offer_product11" class="form-control input-tip multiselect" data-placeholder="' . lang('select') . ' ' . lang('products') . '" required="required" style="width:100%;" ');

?>


            </div>





             <div class="form-group all">
                        <?= lang("product_discount", "fixed_off11") ?>
                        <input class="form-control gen_slug"  name="fixed_off11" id="fixed_off11"  value="<?= $promo ?$this->sma->formatDecimal($promo->fixed_off) : '' ?>" type="number" min=".01" step=".01" required />
					</div>
			<div class="form-group">
                            <?= lang('start_date', 'start_date'); ?>
                            <?php echo form_input('start_date11', $promo->start_date ? $this->sma->hrsd($promo->start_date) : '', 'class="form-control tip date" id="start_date11"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang('end_date', 'end_date'); ?>
                            <?php echo form_input('end_date11', $promo->end_date ? $this->sma->hrsd($promo->end_date) : '', 'class="form-control tip date" id="end_date11"'); ?>
                        </div>

            </span>






                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>







<script language="javascript">


/*var htmlEntities = {
    nbsp: ' ',
    cent: '¢',
    pound: '£',
    yen: '¥',
    euro: '€',
    copy: '©',
    reg: '®',
    lt: '<',
    gt: '>',
    quot: '"',
    amp: '&',
    apos: '\''
};

function unescapeHTML(str) {
    return str.replace(/\&([^;]+);/g, function (entity, entityCode) {
        var match;

        if (entityCode in htmlEntities) {
            return htmlEntities[entityCode];

        } else if (match = entityCode.match(/^#x([\da-fA-F]+)$/)) {
            return String.fromCharCode(parseInt(match[1], 16));

        } else if (match = entityCode.match(/^#(\d+)$/)) {
            return String.fromCharCode(~~match[1]);
        } else {
            return entity;
        }
    });
};*/
function offer_category4_select(){
    var promo_type_id='<?= $promo->promo_type_id ?>';
    if(promo_type_id==4){
    var hide_opt='<?= $promo->hide_opt ?>';

var cat=$("#offer_category4").val();
if(cat && hide_opt==2){
    $("#div_offer_product4").css("display","none");
    $("#div_offer_category4").css("display","block");


}else{
$("#div_offer_product4").css("display","block");
$("#div_offer_category4").css("display","none");

}
    }
//var a=$("#promo_condition").html(v);
//alert($("#promo_condition").html());
}


function offer_category10_select(){
    var promo_type_id='<?= $promo->promo_type_id ?>';
    if(promo_type_id==10){
    var hide_opt='<?= $promo->hide_opt ?>';

var cat=$("#offer_category10").val();
if(cat && hide_opt==2){
    $(".standard").css("display","none");
    $("#div_offer_category10").css("display","block");


}else{
$(".standard").css("display","block");
$("#div_offer_category10").css("display","none");

}
    }
//var a=$("#promo_condition").html(v);
//alert($("#promo_condition").html());
}
function promo_select(){

	var sel=$("#promo_type").val();
	$("#promo_type1").css("display","none");
	$("#promo_type2").css("display","none");
	$("#promo_type3").css("display","none");
	$("#promo_type4").css("display","none");
	$("#promo_type10").css("display","none");
	$("#promo_type5").css("display","none");
	$("#promo_type6").css("display","none");
    $("#promo_type7").css("display","none");
    $("#promo_type8").css("display","none");
    $("#promo_type11").css("display","none");
    $("#promo_type9").css("display","none");

    $("#promo_type"+sel).css("display","block");
	//var a=$("#promo_condition").html(v);
	//alert($("#promo_condition").html());
	}

	function sel_op() {
		var op=$("#sel_option").val();


		if(op=="gift"){
	$("#op1").css("display","none");
    $("#op2").css("display","block");
			}
				if(op=="cashback"){
	$("#op1").css("display","block");
    $("#op2").css("display","none");
			}

	}
	promo_select();
	sel_op();
    offer_category4_select();
    offer_category10_select();


    function products(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + 'products/suggestionsV3',
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    limit: 10,
                };
            },
            results: function(data, page) {

                if (data != null) {
                    return { results: data };
                } else {
                    return { results: [{ id: '', text: 'No Match Found' }] };
                }
            },
        },
    });
}


    $(document).ready(function() {

var su = 2;
    $('#addProduct').click(function () {
        //if (su <= 5) {
            var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="offer_product10_' + su + '", class="form-control" id="offer_product10_' + su + '" placeholder="<?= lang('select') . ' ' . lang('product') ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input  type="number" min="1" step="1" name="buy_' + su + '_quantity10" class="form-control tip" id="buy_' + su + '_quantity10"  placeholder="<?= lang('buy_quantity') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="number" min=".01" step=".01" name="fixed_' + su + '_off10" class="form-control tip" id="fixed_' + su + '_off10" placeholder="<?= lang('product_discount') ?>" /></div></div></div>';
            $('#ex-products').append(html);
            var sup = $('#offer_product10_' + su);
            products(sup);
            $('#su10').val(su);
            $('#fixed_' + su + '_off10').focus();
            su++;
        //} else {
        //    bootbox.alert('<?= lang('max_reached') ?>');
        //    return false;
        //}
    });





$('#offer_product10').select2({
minimumInputLength: 1,
    ajax: {
        url: site.base_url + 'products/suggestionsV3',
        dataType: 'json',
        quietMillis: 15,
        data: function(term, page) {
            return {
                term: term,
                limit: 10,
            };
        },
        results: function(data, page) {

            if (data != null) {
                return { results: data };
            } else {
                return { results: [{ id: '', text: 'No Match Found' }] };
            }
        },
    },
});


    <?php
    if ($promo->promo_type_id==10) {

    if ($promo->offer_product) {
                ?>
        select_product('offer_product10', "<?= $promo->offer_product; ?>");
        $('#fixed_off10').val("<?= $this->sma->formatDecimal($promo->fixed_off); ?>");
        $('#buy_quantity10').val("<?= $promo->buy_quantity; ?>");
        <?php
            }
        ?>
        <?php
        $i=2;
        foreach($promo_product_details as $product){

        if ($product->promo_sr==$i) {

                ?>
                var i='<?= $i; ?>';
        $('#addProduct').click();
        select_product('offer_product10_'+i+'', "<?= $product->offer_product; ?>");
        $('#fixed_'+i+'_off10').val("<?= $this->sma->formatDecimal($product->fixed_off); ?>");
        $('#buy_'+i+'_quantity10').val("<?= $product->buy_quantity; ?>");
        <?php

            } ?>
        <?php
        $i++;


            }
            }
        ?>

        function select_product(id, v) {
            $('#' + id).val(v).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: "<?= admin_url('products/getProductV3') ?>/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + 'products/suggestionsV3',
                    dataType: 'json',
                    quietMillis: 15,
                data: function(term, page) {
                    return {
                        term: term,
                        limit: 10,
                    };
                },
                results: function(data, page) {

                    if (data != null) {
                        return { results: data };
                    } else {
                        return { results: [{ id: '', text: 'No Match Found' }] };
                    }
                },
                }
            });
        }

    });
</script>
