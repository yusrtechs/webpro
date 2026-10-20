<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.card {
    width: 353px;
    height: 450px;
    border-radius: 10px;
    margin: 15px auto 80px;
    color: #FFF;
}
</style>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header no-print">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('Number_of_coupon_used'); ?></h4>
        </div>
        <div class="modal-body">
            <?php
            $discount_type=$coupons->discount_type;
            if ($coupons->coupon_expiry_date && $coupons->coupon_expiry_date < date('Y-m-d')) {
    ?>
                <div class="alert alert-danger">
                    <?= lang('card_expired') ?>
                </div>
            <?php

} elseif ($coupons->coupon_amount > 0) {
        ?>
                <div class="alert alert-info">
                <?php
                if($discount_type=='percent'){
                                echo lang('Discount_type') . ' : ' . lang('Percentage_discount'). ' ( ' .$this->sma->formatDecimal($coupons->coupon_amount).' ) ';
                            }else{
                                echo lang('Discount_type') . ' : ' . lang('Fixed_discount'). ' ( ' .$this->sma->formatDecimal($coupons->coupon_amount).' ) ';
                            }
                ?>
                </div>
            <?php
    } /*else {
        ?>
                <div class="alert alert-danger">
                    <?= lang('card_is_used'); ?>
                </div>
            <?php
    }*/

    foreach($coupon_codes as $coupon_code){
    ?>
        <div class="row">
            <div class="col-md-6">
            <div class="form-group">
                <?= lang('Coupon_code', 'Coupon_code'); ?>

                    <?php echo form_input('coupon_gen_no', $coupon_code, 'readonly type="number"  maxlength="3" class="form-control"'); ?>

            </div>




            </div>
            <div class="col-md-6">

            <div class="form-group">
                <?= lang('Number_of_coupon_used', 'Number_of_coupon_used'); ?>
                <?php echo form_input('coupon_gen_no', $this->sales_model->getAllSalessWithCouponCode(trim($coupon_code))->count, 'readonly type="number"  maxlength="3" class="form-control" id="coupon_gen_no" required="required"'); ?>
            </div>




            </div>
            </div>

            <div class="clearfix"></div>
            <?php
                }
            ?>
            <div class="clearfix"></div>


        </div>
    </div>
</div>
