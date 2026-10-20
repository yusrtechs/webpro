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
            <h4 class="modal-title" id="myModalLabel"><?= lang('view_coupon'); ?></h4>
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
            <div class="card">
                <div class="front">
                    <img src="<?=$assets;?>images/card.png" alt="" class="card_img">
                    <div class="card-content white-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="353px" height="206px" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <text x="215"  y="20" style="font-size:16;fill:#FFF;">
                                <?= lang('Coupon'); ?>
                            </text>
                            <text x="220"  y="47" style="font-size:16;fill:#FFF;">
                                <?= wordwrap($coupon_code, 4, ' ', true); ?>
                            </text>

                            <?php
                            if($discount_type=='percent'){
                                ?>
                                <text x="230"  y="90" style="font-size:36;fill:#FFF;">
                                <?php
                                echo '%'.$this->sma->formatDecimal($coupons->coupon_amount);
                            }else{
                                ?>
                                <text x="250"  y="90" style="font-size:36;fill:#FFF;">
                                <?php
                                echo $this->sma->formatMoney($coupons->coupon_amount);
                            }
                            ?>
                            </text>
                            <text x="5"  y="95" style="font-size:14;fill:#FFF;">
                                <?= $customer ? ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) : ''; ?>
                            </text>
                            <text x="350"  y="115" style="font-size:13;fill:#FFF;">
                                <?= $coupons->coupon_expiry_date ? lang('expiry') . ' : ' . $this->sma->hrsd($coupons->coupon_expiry_date) : ''; ?>
                            </text>
                            <div style="position:absolute;left:0px;bottom:12px;width:353px;background:#FFF;">
                                <div class="text-center">
                                <img src="<?= admin_url('misc/barcode/' . $coupon_code . '/code128/50'); ?>" alt="<?= $coupon_code; ?>" class="bcimg" />
                                </div>
                            </div>
                        </svg>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <div class="back">
                    <img src="<?=$assets;?>images/card2.png" alt="" class="card_img">
                    <div class="card-content">
                        <div class="middle">
                            <?= '<img src="' . base_url('assets/uploads/logos/' . $Settings->logo) . '" alt="' . $Settings->site_name . '" />'; ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php
                }
            ?>
            <div class="clearfix"></div>
            <?php if ($coupons->coupon_amount > 0 && (!$coupons->coupon_expiry_date || $coupons->coupon_expiry_date > date('Y-m-d'))) {
        ?>
            <button type="button" class="btn btn-primary btn-block no-print" onClick="window.print();"><?= lang('print'); ?></button>
            <?php
    } ?>

        </div>
    </div>
</div>
