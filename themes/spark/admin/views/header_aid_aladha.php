<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>

<?php
						if($this->session->userdata('username') =='demo'){

                        admin_redirect('logout');


						}
						?>

<?php

if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
}

if (strlen(strstr($agent, 'Firefox')) > 0) {

}else{

	?>
<style>
body {
    overflow:hidden;

}

div.clsoflow {
  overflow: auto;
}

@media print {
                .ncf-container{ display: none; }


            }

@media (min-width:992px){
div.clsoflow {
  overflow: hidden;

}
}


</style>
<?php
}
?>

<?php
$site_name=$Settings->site_name;

if ($Settings->user_language == 'arabic' || $Settings->site_ename=='')
 $site_name= $Settings->site_name;
else
 $site_name= $Settings->site_ename;


?>


    <meta charset="utf-8">
    <base href="<?= site_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable = no">

	<meta name="viewport" content = "width = device-width, initial-scale = 1.0, user-scalable = no" />
	<meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">




    <title><?= $page_title ?> - <?= $site_name ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>


	<link rel="stylesheet" href="<?= $assets ?>template/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?= $assets ?>template/vendors/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?= $assets ?>template/css/style1.css">


	<script src="<?= $assets ?>template/vendors/base/vendor.bundle.base.js"></script>

	<script src="<?= $assets ?>template/js/template.js"></script>


<!-- bootstrap-colorpicker.css
 <link href="<?= $assets ?>styles/fine-uploader-new.css" rel="stylesheet">
-->
<!-- Fine Uploader jQuery JS file
======================================================================
<script src="<?= $assets ?>js/jquery.fine-uploader.js"></script>


-->



    <link href="<?= $assets ?>template1/build/css/custom.min.css" rel="stylesheet">
	<link href="<?= $assets ?>template1/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">



	 <!-- -->
















    <link href="<?= $assets ?>styles/theme.css?1" rel="stylesheet"/>

    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>

	<link rel="stylesheet" href="<?= $assets ?>styles/notifications.css">
	<script type="text/javascript" src="<?= $assets ?>js/notifications.js"></script>

    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-migrate-1.2.1.min.js"></script>

    <noscript><style type="text/css">#loading { display: none; }</style></noscript>

	<?php if ($Settings->user_language == 'arabic') {
    ?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style_rtl.css?1" rel="stylesheet"/>

		<script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>
    <?php
}


?>










    <script type="text/javascript">
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });
    </script>
    <!--
	<link href="<?=$assets ?>/styles/spectrum.css?ark=<?=rand()?>" rel="stylesheet" >
    -->
</head>

<body >
<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div id="loading"></div>
<div  id="app_wrapper">
    <?php
	if(!stripos(current_url(),'SalesDetails')){
	?>
    <header id="header" class="navbar no-print">
        <?php
	if(stripos(current_url(),'welcome')){
	?>
        <div class="container_ch">

    <div class="eid-scene">

        <div class="ground"></div>

        <!-- الخروف -->
        <svg class="sheep" viewBox="0 0 300 200">

            <ellipse cx="150" cy="90" rx="90" ry="60" fill="#ffffff"/>

            <circle cx="60" cy="90" r="35" fill="#444"/>

            <circle cx="50" cy="82" r="4" fill="#fff"/>
            <circle cx="50" cy="82" r="2" fill="#000"/>

            <ellipse cx="40" cy="60" rx="10" ry="20" fill="#444"/>
            <ellipse cx="80" cy="60" rx="10" ry="20" fill="#444"/>

            <rect class="leg1" x="110" y="135" width="12" height="50" rx="5" fill="#333"/>
            <rect class="leg2" x="140" y="135" width="12" height="50" rx="5" fill="#333"/>
            <rect class="leg3" x="180" y="135" width="12" height="50" rx="5" fill="#333"/>
            <rect class="leg4" x="210" y="135" width="12" height="50" rx="5" fill="#333"/>

        </svg>

        <!-- البانر -->
        <div class="banner">
            <h1>🐑 عيد أضحى مبارك 🐑</h1>
            <p>كل عام وأنتم بخير</p>
        </div>

    </div>

</div>

<style>

.container_ch{
    width:100%;

}

/* المشهد بالكامل */
.eid-scene{
    position:relative;
    width:100%;
    height:55px; /* الارتفاع الكلي */
    overflow:hidden;
    background:transparent;
    border-radius:12px;

    animation:hideScene .8s forwards 9s;

}

/* الأرض */
.ground{
    position:absolute;
    bottom:0;
    width:100%;
    height:18px;
    background:#6fbe63;
    display:none;
}

/* الخروف */
.sheep{
    position:absolute;
    bottom:8px;
    right:-120px;
    width:70px;
    z-index:5;

    animation:sheepRun 6s linear forwards;
}

/* الأرجل */
.leg1,.leg3{
    animation:legMove1 .25s infinite alternate;
    transform-origin:top center;
}

.leg2,.leg4{
    animation:legMove2 .25s infinite alternate;
    transform-origin:top center;
}

@keyframes legMove1{
    from{ transform:rotate(15deg); }
    to{ transform:rotate(-15deg); }
}

@keyframes legMove2{
    from{ transform:rotate(-15deg); }
    to{ transform:rotate(15deg); }
}

/* حركة الخروف */
@keyframes sheepRun{
    from{
        right:-250px;
    }
    to{
        right:110%;
    }
}

/* البانر الاحترافي */
.banner{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%) scale(.5);

    min-width:320px;
    max-width:90%;

    padding:10px 24px;

    border-radius:18px;

    background:
        linear-gradient(
            135deg,
            rgba(255,255,255,.96),
            rgba(255,248,220,.95)
        );

    border:1px solid rgba(255,255,255,.7);

    backdrop-filter:blur(8px);

    box-shadow:
        0 8px 25px rgba(0,0,0,.12),
        inset 0 1px 0 rgba(255,255,255,.7);

    text-align:center;

    opacity:0;

    animation:
        showBanner 1s cubic-bezier(.22,1,.36,1) forwards 5.5s,
        bannerGlow 2s ease-in-out infinite alternate;

    z-index:10;
}

/* خط ذهبي علوي */
.banner::before{
    content:"";
    position:absolute;
    top:0;
    left:12%;
    width:76%;
    height:3px;

    border-radius:50px;

    background:linear-gradient(
        90deg,
        #d4af37,
        #ffe08a,
        #d4af37
    );
}

/* العنوان */
.banner h1{
    margin:0;
    font-size:20px;
    font-weight:700;
    color:#0f5132;
    letter-spacing:.3px;
    line-height:1.4;

    text-shadow:0 1px 2px rgba(255,255,255,.6);
}

/* النص */
.banner p{
    margin-top:4px;
    font-size:13px;
    color:#444;
    font-weight:600;
    letter-spacing:.2px;
}

/* ظهور ناعم */
@keyframes showBanner{
    0%{
        opacity:0;
        transform:translate(-50%,-50%) scale(.7);
    }

    100%{
        opacity:1;
        transform:translate(-50%,-50%) scale(1);
    }
}

/* توهج خفيف */
@keyframes bannerGlow{
    from{
        box-shadow:
            0 8px 25px rgba(0,0,0,.12),
            0 0 8px rgba(212,175,55,.15);
    }

    to{
        box-shadow:
            0 10px 30px rgba(0,0,0,.18),
            0 0 18px rgba(212,175,55,.35);
    }
}
/* إخفاء المشهد بالكامل */
@keyframes hideScene{
    to{
        opacity:0;
        visibility:hidden;
        height:0;
    }
}

</style>
<?php
    }
	?>
        <div   class="container">

            <a class="navbar-brand" href="<?= admin_url() ?>"><span class="logo"><?= $site_name ?></span></a>

           <!-- <div class="btn-group visible-xs pull-right btn-visible-sm">
                <button class="navbar-toggle btn" type="button" data-toggle="collapse" data-target="#sidebar_menu">
                    <span class="fa fa-bars"></span>
                </button>
                <?php if (SHOP) {
        ?>
                <a href="<?= site_url('/') ?>" class="btn">
                    <span class="fa fa-shopping-cart"></span>
                </a>
                <?php
    } ?>
                <?php if (POS && $Settings->pos==1) {
        ?>
                <a href="<?= admin_url('pos') ?>" class="btn">
                    <span class="fa fa-th-large"></span>
                </a>
                <?php
    } ?>
                <a href="<?= admin_url('calendar') ?>" class="btn">
                    <span class="fa fa-calendar"></span>
                </a>
                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn">
                    <span class="fa fa-user"></span>
                </a>
                <a href="<?= admin_url('logout'); ?>" class="btn">
                    <span class="fa fa-sign-out"></span>
                </a>
            </div>-->
            <div class="header-nav">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown">
                        <a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
                            <img alt="" src="<?= $this->session->userdata('avatar') ? base_url() . 'assets/uploads/avatars/thumbs/' . $this->session->userdata('avatar') : base_url('assets/images/' . $this->session->userdata('gender') . '.png'); ?>" class="mini_avatar img-rounded">

                            <div class="user">
                                <span><?= lang('welcome') ?> <?= $this->session->userdata('username'); ?></span>
                            </div>
                        </a>
						<?php
						if($this->session->userdata('username') !='demo'){
						?>
                        <ul class="dropdown-menu pull-right">
                            <li  >
                                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>">
                                    <i class="fa fa-user"></i> <?= lang('profile'); ?>
                                </a>
                            </li>
                            <li  style="z-index:1000;">
                                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>"><i class="fa fa-key"></i> <?= lang('change_password'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?= admin_url('logout'); ?>">
                                    <i class="fa fa-sign-out"></i> <?= lang('logout'); ?>
                                </a>
                            </li>
                        </ul>
						<?php
						}
						?>
                    </li>
                </ul>
                <!--<ul class="nav navbar-nav pull-right">
                    <li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('dashboard') ?>" data-placement="bottom" href="<?= admin_url('welcome') ?>"><i class="fa fa-dashboard"></i></a></li>
                    <?php if (SHOP) {
        ?>
                    <li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('shop') ?>" data-placement="bottom" href="<?= base_url() ?>"><i class="fa fa-shopping-cart"></i></a></li>
                    <?php
    } ?>
                    <?php if (POS && $Settings->pos==1) {
        ?>
                    <a href="<?= admin_url('pos') ?>" class="btn">
                        <span class="fa fa-th-large"></span>
                    </a>
                    <?php
    } ?>
                    <?php if ($Owner) {
        ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn tip" title="<?= lang('settings') ?>" data-placement="bottom" href="<?= admin_url('system_settings') ?>">
                            <i class="fa fa-cogs"></i>
                        </a>
                    </li>
                    <?php
    } ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn tip" title="<?= lang('calculator') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                            <i class="fa fa-calculator"></i>
                        </a>
                        <ul class="dropdown-menu pull-right calc">
                            <li class="dropdown-content">
                                <span id="inlineCalc"></span>
                            </li>
                        </ul>
                    </li>
                    <?php if ($info) {
        ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn tip" title="<?= lang('notifications') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-info-circle"></i>
                                <span class="number blightOrange black"><?= sizeof($info) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header"><i class="fa fa-info-circle"></i> <?= lang('notifications'); ?></li>
                                <li class="dropdown-content">
                                    <div class="scroll-div">
                                        <div class="top-menu-scroll">
                                            <ol class="oe">
                                                <?php foreach ($info as $n) {
            echo '<li>' . $n->comment . '</li>';
        } ?>
                                            </ol>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    <?php
    } ?>
                    <?php if ($events) {
        ?>
                        <li class="dropdown hidden-xs">
                            <a class="btn tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-calendar"></i>
                                <span class="number blightOrange black"><?= sizeof($events) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header">
                                <i class="fa fa-calendar"></i> <?= lang('upcoming_events'); ?>
                                </li>
                                <li class="dropdown-content">
                                    <div class="top-menu-scroll">
                                        <ol class="oe">
                                            <?php foreach ($events as $event) {
            echo '<li>' . date($dateFormats['php_ldate'], strtotime($event->start)) . ' <strong>' . $event->title . '</strong><br>' . $event->description . '</li>';
        } ?>
                                        </ol>
                                    </div>
                                </li>
                                <li class="dropdown-footer">
                                    <a href="<?= admin_url('calendar') ?>" class="btn-block link">
                                        <i class="fa fa-calendar"></i> <?= lang('calendar') ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php
    } else {
        ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="<?= admin_url('calendar') ?>">
                            <i class="fa fa-calendar"></i>
                        </a>
                    </li>
                    <?php
    } ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn tip" title="<?= lang('styles') ?>" data-placement="bottom" data-toggle="dropdown"
                           href="#">
                            <i class="fa fa-css3"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li class="bwhite noPadding">
                                <a href="#" id="fixed" class="">
                                    <i class="fa fa-angle-double-left"></i>
                                    <span id="fixedText">Fixed</span>
                                </a>
                                <a href="#" id="cssLight" class="grey">
                                    <i class="fa fa-stop"></i> Grey
                                </a>
                                <a href="#" id="cssBlue" class="blue">
                                    <i class="fa fa-stop"></i> Blue
                                </a>
                                <a href="#" id="cssBlack" class="black">
                                   <i class="fa fa-stop"></i> Black
                               </a>
                           </li>
                        </ul>
                    </li>
                    <li class="dropdown hidden-xs">
                        <a class="btn tip" title="<?= lang('language') ?>" data-placement="bottom" data-toggle="dropdown"
                           href="#">
                            <img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt="">
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <?php $scanned_lang_dir = array_map(function ($path) {
        return basename($path);
    }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) {
                                ?>
                                <li>
                                    <a href="<?= admin_url('welcome/language/' . $entry); ?>">
                                        <img src="<?= base_url('assets/images/' . $entry . '.png'); ?>" class="language-img">
                                        &nbsp;&nbsp;<?= ucwords($entry); ?>
                                    </a>
                                </li>
                            <?php
                            } ?>
                            <li class="divider"></li>
                            <li>
                                <a href="<?= admin_url('welcome/toggle_rtl') ?>">
                                    <i class="fa fa-align-<?=$Settings->user_language == 'arabic' ? 'right' : 'left';?>"></i>
                                    <?= lang('toggle_alignment') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php /* if ($Owner && $Settings->update) { ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn blightOrange tip" title="<?= lang('update_available') ?>"
                            data-placement="bottom" data-container="body" href="<?= admin_url('system_settings/updates') ?>">
                            <i class="fa fa-download"></i>
                        </a>
                    </li>
                        <?php } */ ?>
                    <?php if (($Owner || $Admin || $GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts']) && ($qty_alert_num > 0 || $exp_alert_num > 0 || $shop_sale_alerts)) {
                                ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn blightOrange tip" title="<?= lang('alerts') ?>"
                                data-placement="left" data-toggle="dropdown" href="#">
                                <i class="fa fa-exclamation-triangle"></i>
                                <span class="number bred black"><?= $qty_alert_num + (($Settings->product_expiry) ? $exp_alert_num : 0) + $shop_sale_alerts + $shop_payment_alerts; ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <?php if ($qty_alert_num > 0) {
                                    ?>
                                <li>
                                    <a href="<?= admin_url('reports/quantity_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
                                    </a>
                                </li>
                                <?php
                                } ?>
                                <?php if ($Settings->product_expiry) {
                                    ?>
                                <li>
                                    <a href="<?= admin_url('reports/expiry_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $exp_alert_num; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('expiry_alerts') ?></span>
                                    </a>
                                </li>
                                <?php
                                } ?>
                                <?php if ($shop_sale_alerts) {
                                    ?>
                                <li>
                                    <a href="<?= admin_url('sales?shop=yes&delivery=no') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_sale_alerts; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('sales_x_delivered') ?></span>
                                    </a>
                                </li>
                                <?php
                                } ?>
                                <?php if ($shop_payment_alerts) {
                                    ?>
                                <li>
                                    <a href="<?= admin_url('sales?shop=yes&attachment=yes') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_payment_alerts; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('manual_payments') ?></span>
                                    </a>
                                </li>
                                <?php
                                } ?>
                            </ul>
                        </li>
                    <?php
                            } ?>
                    <?php if (POS && $Settings->pos==1) {
                                ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn bdarkGreen tip" title="<?= lang('pos') ?>" data-placement="bottom" href="<?= admin_url('pos') ?>">
                            <i class="fa fa-th-large"></i> <span class="padding05"><?= lang('pos') ?></span>
                        </a>
                    </li>
                    <?php
                            } ?>
                    <?php if ($Owner) {
                                ?>
                        <li class="dropdown">
                            <a class="btn bdarkGreen tip" id="today_profit" title="<span><?= lang('today_profit') ?></span>"
                                data-placement="bottom" data-html="true" href="<?= admin_url('reports/profit') ?>"
                                data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-hourglass-2"></i>
                            </a>
                        </li>
                    <?php
                            } ?>
                    <?php if ($Owner || $Admin) {
                                ?>
                    <?php if (POS) {
                                    ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn bblue tip" title="<?= lang('list_open_registers') ?>" data-placement="bottom" href="<?= admin_url('pos/registers') ?>">
                            <i class="fa fa-list"></i>
                        </a>
                    </li>
                    <?php
                                } ?>
                    <li class="dropdown hidden-xs">
                        <a class="btn bred tip" title="<?= lang('clear_ls') ?>" data-placement="bottom" id="clearLS" href="#">
                            <i class="fa fa-eraser"></i>
                        </a>
                    </li>
                    <?php
                            } ?>
                </ul>-->
            </div>

        </div>




    </header>






    <div class="container1-scroller no-print" id="mainnav2">

		<!-- partial:partials/_horizontal-navbar.html -->
    <div class="horizontal-menu">

      <nav class="bottom-navbar1">
        <div class="container1">
            <ul class="nav1 page-navigation">
              <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

			  <li class="nav-item2  mm_welcome">
                <a class="nav-link2" href="<?= admin_url() ?>">
                  <i class="mdi mdi-file-document-box menu-icon" style="font-size:25px;"></i>
                  <span><?= lang('home'); ?></span>
                </a>
              </li>
			  <?php
			  }
			  ?>

              <?php
			  if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
              {
				  ?>
              <li class="nav-item2 mm_branches">
                  <a href="<?= admin_url('branches/dashboard') ?>" class="nav-link2">
                    <i class="fa fa-home menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('Branches'); ?></span>

                  </a>
              </li>
			  <?php
			  }
			  ?>

            <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
				  ?>
              <li class="nav-item2 mm_accounts">
                  <a href="<?= admin_url('accounts/dashboard') ?>" class="nav-link2">
                    <i class="fa fa-line-chart menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('Accounting'); ?></span>

                  </a>
              </li>
			  <?php
			  }
			  ?>








                <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>


<?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>


                  <li class="nav-item2 mm_cars">
                  <a href="<?= admin_url('cars') ?>" class="nav-link2">
                    <i class="fa fa-car menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('Cars_Showroom'); ?></span>

                  </a>
                </li>

			  <li class="nav-item2  mm_carssales mm_pos1 mm_promos mm_promo mm_customers">
                  <a href="<?= admin_url('carssales'); ?>" class="nav-link2">
                    <i class="mdi mdi-finance menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('sales'); ?></span>

                  </a>
              </li>
              <li class="nav-item2  mm_carspurchases mm_carssuppliers">
                  <a href="<?= admin_url('carspurchases'); ?>" class="nav-link2">
                    <i class="mdi mdi-chart-areaspline menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('purchases'); ?></span>

                  </a>
              </li>


              <?php
			  }else{
			  ?>

              <li class="nav-item2 mm_products">
                  <a href="<?= admin_url('products/dashboard') ?>" class="nav-link2">
                    <i class="mdi mdi-cube-outline menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('warehouses'); ?></span>

                  </a>

              </li>
			  <li class="nav-item2  mm_sales mm_pos1 mm_promos mm_promo mm_customers mm_salesman">

                  <a href="<?php if ($Owner || $Admin){ echo admin_url('sales'); }else{echo admin_url('sales/dashboard'); } ?>" class="nav-link2">
                    <i class="mdi mdi-finance menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('sales'); ?></span>

                  </a>

              </li>
              <li class="nav-item2  mm_purchases mm_suppliers">
                  <a href="<?php if ($Owner || $Admin){ echo admin_url('purchases'); }else{echo admin_url('purchases/dashboard'); } ?>" class="nav-link2">
                    <i class="mdi mdi-chart-areaspline menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('purchases'); ?></span>

                  </a>
              </li>

			  <?php
			  }
			  ?>

            <?php
			  }
			  ?>

			  <?php
            if ($Owner || $Admin) {
			  if(isset($this->Settings->isHR) && $this->Settings->isHR==1)
              {
				  ?>
            <li class="nav-item2 mm_hr">
            <a href="<?= admin_url('auth/login_hr/') ?>" class="nav-link2" target="_blank">
                    <i class="fa fa-users menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('HR'); ?></span>

                  </a>
            </li>
			  <?php
			  }
            }
			  ?>

              <?php
				$dbname=str_replace("cpesoft_","",$this->db->database);
				if($dbname=="arkanpro" || $dbname=="spark")
					{
				?>
                    <li class="nav-item2 mm_crm">
                        <a href="<?= admin_url('auth/login_crm/') ?>" class="nav-link2" target="_blank">
                        <i class="fa fa-sitemap menu-icon" style="font-size:25px;"></i>
                        <span><?= lang('CRM'); ?></span></a>
                    </li>
			  <?php
			  }
			  ?>

            <?php
			  if(isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset==1)
              {
				  ?>
              <li class="nav-item2 mm_fixedassets">
                  <a href="<?= admin_url('fixedassets') ?>" class="nav-link2">
                    <i class="fa fa-building menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('Fixed_assets'); ?></span>

                  </a>
              </li>
			  <?php
			  }
			  ?>

<?php
			  if(isset($this->Settings->isTailer) && $this->Settings->isTailer==1)
              {
				  ?>
              <li class="nav-item2 mm_tailor">
                  <a href="<?= admin_url('tailor/tailor') ?>" class="nav-link2">
                    <i class="fa fa-hand-scissors-o menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('Tailor'); ?></span>

                  </a>
              </li>
			  <?php
			  }
			  ?>

<?php
			  if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
              {
				  ?>
              <li class="nav-item2 mm_manufacturing">
                  <a href="<?= admin_url('manufacturing') ?>" class="nav-link2">
                    <i class="fa fa-industry menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('Manufacturing'); ?></span>

                  </a>
              </li>
			  <?php
			  }
			  ?>

              <li class="nav-item2  mm_system_settings mm_pos mm_auth   mm_billers">
                  <a href="<?= admin_url('users'); ?>" class="nav-link2">
                    <i class="	fa fa-cogs menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('User_tools'); ?></span>

                  </a>
              </li>


              <?php
            if(isset($this->Settings->isStore) && $this->Settings->isStore==1 && $Owner)
              {
				  ?>
            <li class="nav-item2  mm_shop_settings">
                  <a href="<?= admin_url('shop_settings/shop_sync'); ?>" class="nav-link2">
                    <i class="	fa fa-shopping-cart menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('shop'); ?></span>

                  </a>
            </li>
            <?php
            }else{
                ?>
                <li class="nav-item2  mm_shop_settings" style="cursor:not-allowed;pointer-events:none">
                   <a href="#" class="nav-link2">
                    <i class="	fa fa-shopping-cart menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('shop'); ?></span>
                   </a>
            </li>
            <?php
            }
            ?>

			      <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

            <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>
              <li class="nav-item2 mm_carsreports">
                  <a href="<?= admin_url('carsreports/dashboard') ?>" class="nav-link2">
                    <i class="fa fa-bar-chart-o menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('reports'); ?></span>

                  </a>
              </li>
			  <?php
			  }else{
			  ?>


              <li class="nav-item2 mm_reports">
                  <a href="<?= admin_url('reports/dashboard') ?>" class="nav-link2">
                    <i class="fa fa-bar-chart-o menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('reports'); ?></span>

                  </a>
              </li>
			  <?php
              }
			  }
			  ?>


            <?php
			  if(isset($this->Settings->isElectronicLinking) && $this->Settings->isElectronicLinking==1)
              {
				  ?>
              <li class="nav-item2 mm_sync">
                  <a href="<?= admin_url('sync/dashboard') ?>" class="nav-link2">
                    <i class="fa fa-cloud-download menu-icon" style="font-size:25px;"></i>
                    <span><?= lang('Integrations'); ?></span>
                   </a>
              </li>
			  <?php
			  }
			  ?>

            </ul>
        </div>
      </nav>
    </div>
    <!-- partial -->

		<!-- page-body-wrapper ends -->
    </div>


<?php
	}
	?>





  <body class="nav-md " >
    <div class="container2 body">
	<?php
	if(strpos(current_url(),'print_barcodes') || stripos(current_url(),'SalesDetails') || stripos(current_url(),'getSalesTransReport') || stripos(current_url(),'getSalesDeliveryReport') || stripos(current_url(),'getSalesReport') || stripos(current_url(),'getPurchasesReport') || stripos(current_url(),'getQuotesReport') || stripos(current_url(),'getTransfersReport') || stripos(current_url(),'getAdjustmentReport') || stripos(current_url(),'accountstatementprint') || stripos(current_url(),'accountbalanceprint') || stripos(current_url(),'tax') || stripos(current_url(),'getDestructionReport')){
	?>
      <div class=" main_container2">
	  <?php
	}else{
		?>
     <div class="no-print main_container2">
	    <?php
	}

		?>



		<?php
	if(!stripos(current_url(),'SalesDetails')){
	?>

        <div class="col-md-3 left_col "   id="mainnav">
          <div class="left_col scroll-view">
            <div class="navbar2 nav_title" style="border:0;">
              <!--<a href="<?= admin_url() ?>" class="site_title"><i class="fa fa-paw"></i> <span class="logo"><?= $site_name ?></span></a>-->
            </div>



<!-- menu profile quick info
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="images/img.jpg" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2>John Doe</h2>
              </div>
            </div>
			 -->
            <!-- /menu profile quick info -->





<!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu" >
              <div class="menu_section "  >
                <!--<h3>General</h3>-->
				<?php if ($Settings->user_language == 'arabic') {
    ?>

                <ul class="nav2 side-menu_ar"  >
				    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

                  <li id="mm_welcome" ><a><i class="fa fa-home mm_welcome"></i> <?= lang('home'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url() ?>"><?= lang('dashboard'); ?></a></li>
                    </ul>
                  </li>
                  <?php
                    if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                    {
                    ?>

                    <li id="mm_cars"><a><i class="fa fa-car mm_cars mm_carstransfers"></i> <?= lang('Cars_Showroom'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('cars/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('cars/add_vehicle'); ?>"><?= lang('Add_Vehicles'); ?></a></li>
                      <li><a href="<?= admin_url('cars'); ?>"><?= lang('Vehicles'); ?></a></li>
                      <li><a href="<?= admin_url('cars/showrooms'); ?>"><?= lang('Showroom'); ?></a></li>
                      <li><a href="<?= admin_url('cars/services'); ?>"><?= lang('Services'); ?></a></li>
                      <li><a href="<?= admin_url('cars/transfers'); ?>"><?= lang('list_transfers'); ?></a></li>


                        <li><a><i class="mm_cars_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('cars/brands') ?>"><?= lang('brands'); ?></a></li>
                            <li><a href="<?= admin_url('cars/models'); ?>"><?= lang('Car_Type'); ?></a></li>
                            <li><a href="<?= admin_url('cars/categories'); ?>"><?= lang('categories'); ?></a></li>
                            <li><a href="<?= admin_url('cars/features'); ?>"><?= lang('Features'); ?></a></li>

                         </ul>
                      </li>


					</ul>
                  </li>
                  <?php
                }else{
                ?>
                  <li id="mm_products"><a><i class="fa fa-building mm_products"></i> <?= lang('warehouses'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('products/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('products/warehouses') ?>"><?= lang('warehouses'); ?></a></li>
                      <li><a href="<?= admin_url('products'); ?>"><?= lang('list_products'); ?></a></li>
                      <li><a href="<?= admin_url('products/quantity_adjustments'); ?>"><?= lang('inventory_opening_balance'); ?></a></li>
                      <li><a href="<?= admin_url('products/transfers'); ?>"><?= lang('list_transfers'); ?></a></li>
                      <li><a href="<?= admin_url('products/destruction_inventory') ?>"><?= lang('Inventory_operations'); ?></a></li>
                      <li><a href="<?= admin_url('products/manual_inventory'); ?>"><?= lang('manual_inventory'); ?></a></li>
                      <li><a href="<?= admin_url('products/stock_counts'); ?>"><?= lang('stock_counts'); ?></a></li>
                      <li><a href="<?= admin_url('products/quantity_adjust'); ?>"><?= lang('Quantity_adjustments'); ?></a></li>
                      <li><a href="<?= admin_url('products/revaluation'); ?>"><?= lang('Re-evaluation_of_cost_prices'); ?></a></li>

					  <li><a><i class="mm_products_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('products/add'); ?>"><?= lang('add_product'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/import_csv'); ?>"><?= lang('import_csv'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/import_excel'); ?>"><?= lang('import_excel'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/print_barcodes'); ?>"><?= lang('print_barcode_label'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/units'); ?>"><?= lang('units'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/categories'); ?>"><?= lang('categories'); ?></a>
                            </li>

							<?php if ($Owner) {
                                ?>
							<li><a href="<?= admin_url('products/brands'); ?>"  ><?= lang('brands'); ?></a>
                            </li>
							<?php
							//$dbname=str_replace("cpesoft_","",$this->db->database);
								//if($dbname=="test" || $dbname=="spark")
								{
									?>
							<li><a href="<?= admin_url('products/variants'); ?>"  ><?= lang('variants'); ?></a>
                            </li>

                            <li><a href="<?= admin_url('products/modifiers'); ?>"  ><?= lang('Modifiers'); ?></a>
                            </li>
								<?php
                            } ?>

								<?php
                            } ?>


                          </ul>
                        </li>


					   <li><a href="<?= admin_url('products/reports'); ?>"><?= lang('reports'); ?></a></li>





					</ul>
                  </li>
                  <?php
                }
                ?>

                  <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>



                <li id="mm_carssales"><a><i class="fa fa-line-chart mm_carssales mm_pos1 mm_promos mm_promo mm_customers"></i> <?= lang('sales'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('carssales/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/add') ?>"><?= lang('add_sale'); ?></a></li>
					  <li><a href="<?= admin_url('carssales') ?>"><?= lang('list_sales'); ?></a></li>

                      <!--    <?php if (POS) {
                                ?>

					  <li class="sub_menu"><a href="<?= admin_url('pos/carssales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>

					  <li><a><i class="mm_sales_return"></i><?= lang('pos_sales'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('sales/returns_index'); ?>"><?= lang('list_returns'); ?></a>
                            </li>
                          </ul>
                      </li>

                          <?php
                            } ?> -->
					  <li><a href="<?= admin_url('carssales/deliveries'); ?>"><?= lang('deliveries'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/gift_cards'); ?>"><?= lang('list_gift_cards'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/quotes_index'); ?>"><?= lang('List_reserved_vehicle'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/offers_index'); ?>"><?= lang('list_quotes'); ?></a></li>
                      <li><a href="<?= admin_url('customers'); ?>"><?= lang('list_customers'); ?></a></li>
                     <!--<li><a href="<?= admin_url('promos') ?>"><?= lang('promos'); ?></a></li>
					   <li><a><i class="mm_sales_promo"></i><?= lang('promos'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('promo/promo_type'); ?>"><?= lang('Promos_type'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/add_promo'); ?>"><?= lang('add_promo'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/promos'); ?>"><?= lang('Promos_list'); ?></a>
                            </li>

                          </ul>
                      </li>
					  -->
					  <li><a><i class="mm_carssales_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('carssales/customer_groups') ?>"><?= lang('customer_groups'); ?></a></li>
							<!--<li><a href="<?= admin_url('carssales/price_groups') ?>"><?= lang('price_groups'); ?></a></li>
                            -->

                         </ul>
                      </li>
					  <li><a href="<?= admin_url('carssales/reports'); ?>"><?= lang('reports'); ?></a></li>




					</ul>
                  </li>

                <?php
                }else{
                ?>



                <li id="mm_sales"><a><i class="fa fa-line-chart mm_sales mm_pos1 mm_promos mm_promo mm_customers mm_salesman"></i> <?= lang('sales'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('sales/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('sales/add') ?>"><?= lang('add_sale'); ?></a></li>
					  <li><a href="<?= admin_url('sales') ?>"><?= lang('list_sales'); ?></a></li>

                          <?php if (POS) {
                                ?>

					  <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>

					  <!--<li><a><i class="mm_sales_return"></i><?= lang('pos_sales'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('sales/returns_index'); ?>"><?= lang('list_returns'); ?></a>
                            </li>
                          </ul>
                      </li>-->

                          <?php
                            } ?>
                      <?php if ($this->Settings->isDeliveryFromSales==1) {
                                ?>
					  <li><a href="<?= admin_url('sales/deliveries'); ?>"><?= lang('deliveries'); ?></a></li>
                      <?php
                        }else{ ?>
                      <li><a href="<?= admin_url('sales/delivers'); ?>"><?= lang('deliveries'); ?></a></li>
                      <?php
                        } ?>
                      <li><a href="<?= admin_url('sales/coupons'); ?>"><?= lang('Coupons'); ?></a></li>
                      <li><a href="<?= admin_url('sales/gift_cards'); ?>"><?= lang('list_gift_cards'); ?></a></li>
                      <li><a href="<?= admin_url('sales/quotes_index'); ?>"><?= lang('list_quotes'); ?></a></li>
                      <li><a href="<?= admin_url('customers'); ?>"><?= lang('list_customers'); ?></a></li>
                      <li><a href="<?= admin_url('salesman'); ?>"><?= lang('list_salesman'); ?></a></li>
                     <!--<li><a href="<?= admin_url('promos') ?>"><?= lang('promos'); ?></a></li>-->
					   <li><a><i class="mm_sales_promo"></i><?= lang('promos'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('promo/promo_type'); ?>"><?= lang('Promos_type'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/add_promo'); ?>"><?= lang('add_promo'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/promos'); ?>"><?= lang('Promos_list'); ?></a>
                            </li>

                          </ul>
                      </li>

					  <li><a><i class="mm_sales_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('sales/customer_groups') ?>"><?= lang('customer_groups'); ?></a></li>
							<li><a href="<?= admin_url('sales/price_groups') ?>"><?= lang('price_groups'); ?></a></li>
                            <li><a href="<?= admin_url('sales/terms') ?>"><?= lang('Terms_and_conditions'); ?></a></li>
                            <?php
                            if(isset($this->Settings->isSubscriptions) && $this->Settings->isSubscriptions==1)
                            {
                            ?>
                                <li><a href="<?= admin_url('sales/subscriptions') ?>"><?= lang('Subscriptions'); ?></a></li>
                            <?php
                            }
                            ?>

                         </ul>
                      </li>
					  <li><a href="<?= admin_url('sales/reports'); ?>"><?= lang('reports'); ?></a></li>




					</ul>
                  </li>

                  <?php
                }
                ?>



                <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>


				  <li id="mm_carspurchases"><a><i class="fa fa-industry  mm_carspurchases mm_carssuppliers"></i> <?= lang('purchases'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('carspurchases/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('carspurchases/add'); ?>"><?= lang('add_purchase'); ?></a></li>
                      <li><a href="<?= admin_url('carspurchases') ?>"><?= lang('list_purchases'); ?></a></li>
					 <!-- <li><a href="<?= admin_url('carspurchases/returns') ?>"><?= lang('return_purchase'); ?></a></li> -->

                      <li><a href="<?= admin_url('carspurchases/expenses'); ?>"><?= lang('list_expenses'); ?></a></li>
                      <li><a href="<?= admin_url('carssuppliers'); ?>"><?= lang('list_cars_owners'); ?></a></li>

					  <li><a><i class="mm_purchases_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">

							<?php if ($Owner) {
                                ?>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/expense_categories'); ?>"  ><?= lang('expense_categories'); ?></a>
                            </li>
								<?php
                            } ?>

                         </ul>
                      </li>
					   <li><a href="<?= admin_url('carspurchases/reports') ?>"><?= lang('reports'); ?></a></li>






					</ul>
                  </li>



                 <?php
                }else{
                ?>





				  <li id="mm_purchases"><a><i class="fa fa-industry  mm_purchases mm_suppliers"></i> <?= lang('purchases'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('purchases/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('purchases/add'); ?>"><?= lang('add_purchase'); ?></a></li>
                      <li><a href="<?= admin_url('purchases') ?>"><?= lang('list_purchases'); ?></a></li>
					  <li><a href="<?= admin_url('purchases/returns') ?>"><?= lang('return_purchase'); ?></a></li>

                      <li><a href="<?= admin_url('purchases/expenses'); ?>"><?= lang('list_expenses'); ?></a></li>
                      <li><a href="<?= admin_url('suppliers'); ?>"><?= lang('list_suppliers'); ?></a></li>

					  <li><a><i class="mm_purchases_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">

							<?php if ($Owner) {
                                ?>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/expense_categories'); ?>"  ><?= lang('expense_categories'); ?></a>
                            </li>
								<?php
                            } ?>

                         </ul>
                      </li>
					   <li><a href="<?= admin_url('purchases/reports') ?>"><?= lang('reports'); ?></a></li>






					</ul>
                  </li>


				      <?php
						}
						?>

<?php
                }
                ?>

                    <?php
            if ($Owner || $Admin) {
			  if(isset($this->Settings->isHR) && $this->Settings->isHR==1)
              {
				  ?>
                    <li id="mm_hr"><a href="<?= admin_url('auth/login_hr/') ?>"  target="_blank"><i class="fa fa-users  mm_hr"></i> <?= lang('HR'); ?> <span class="fa fa-chevron-down"></span></a>

                  </li>
			  <?php
			  }
            }
			  ?>

              <?php
				$dbname=str_replace("cpesoft_","",$this->db->database);
				if($dbname=="arkanpro" || $dbname=="spark")
					{
				?>
                    <li id="mm_crm"><a href="<?= admin_url('auth/login_crm/') ?>"  target="_blank"><i class="fa fa-sitemap  mm_crm"></i> <?= lang('CRM'); ?> <span class="fa fa-chevron-down"></span></a>
                    </li>
			  <?php
			  }
			  ?>
                      <?php
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            ?>

                <li  id="mm_branches"><a><i class="fa fa-home mm_branches"></i> <?= lang('Branches'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                    <?php if ($Owner) {

                    ?>

                            <li><a href="<?= admin_url('branches/dashboard') ?>"><?= lang('dashboard'); ?></a></li>

                          <?php
                          if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                            ?>

                            <li class="sub_menu"><a href="<?= admin_url('branches/groups_settings'); ?>"><?= lang('Groups_settings'); ?></a>
                            </li>
                            <?php }
                            if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                            {
                                ?>
                            <li class="sub_menu"><a href="<?= admin_url('branches/companies_settings'); ?>"><?= lang('Companies_settings'); ?></a>
                            </li>
                            <?php }
                            if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                            {
                                ?>
                            <li class="sub_menu"><a href="<?= admin_url('branches/branches_list'); ?>"><?= lang('Branches_list'); ?></a>
                            </li>
                            <?php
                            }
                            ?>
                            <li><a><i class="mm_branches_settings"></i><?= lang('settings'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu_ar">
                                    <li class="sub_menu"><a href="<?= admin_url('branches/main_settings'); ?>"><?= lang('Data_sharing'); ?></a>
                                    </li>
                                    <li class="sub_menu"><a href="<?= admin_url('branches/branches_settings'); ?>"><?= lang('Branches_settings'); ?></a>
                                    </li>
								</ul>
							</li>




                    <?php
                    }
                            ?>




                        </ul>
                    </li>
                    <?php
                    }
                            ?>



				  <li  id="mm_auth"><a><i class="fa fa-cog mm_system_settings mm_pos mm_auth mm_billers"></i> <?= lang('User_tools'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">



					  <li><a><i class="mm_system_settings_system_settings"></i><?= lang('Settings'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">


						          <?php if ($Owner) {

                                    ?>
									    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>


                            <li class="sub_menu"><a href="<?= admin_url('system_settings'); ?>"><?= lang('system_settings'); ?></a>


                            </li>



							     <?php if (POS) {
                                    ?>
                                    <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
                }else{
				  ?>


					  <li><a href="<?= admin_url('pos/settings') ?>"><?= lang('pos_settings'); ?></a></li>
					  <li><a href="<?= admin_url('pos/printers') ?>"><?= lang('list_printers'); ?></a></li>
					<?php
                            }
                        }
                        ?>
                    <?php
                            } ?>


                      <li><a href="<?= admin_url('system_settings/change_logo') ?>"  data-toggle="modal" data-target="#myModal"><?= lang('change_logo'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/tax_rates') ?>"><?= lang('tax_rates'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/currencies') ?>"><?= lang('currencies'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/credit_cards') ?>"><?= lang('Credit_Cards'); ?></a></li>
					  <?php if ($Settings->IsResturant) {
                                    ?>

					  <li><a href="<?= admin_url('system_settings/departments') ?>"><?= lang('Departments'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/tabels') ?>"><?= lang('Tabels'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/stuff') ?>"><?= lang('Stuff'); ?></a></li>
					  <?php
                            } ?>

					  <li><a href="<?= admin_url('system_settings/email_templates') ?>"><?= lang('email_templates'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/user_groups') ?>"><?= lang('group_permissions'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/backups') ?>"><?= lang('backups'); ?></a></li>

							<?php
                            } ?>


                        </ul>
                      </li>


                      <li><a><i class="mm_system_settings_sms_settings"></i><?= lang('SMS'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/sms_settings'); ?>"><?= lang('sms_settings'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/send_sms'); ?>"><?= lang('send_sms'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/sms_log'); ?>"><?= lang('sms_log'); ?></a>
                            </li>
                        </ul>
                      </li>

                      <li><a><i class="mm_system_settings_whatsapp_settings"></i><?= lang('Whatsapp'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_settings'); ?>"><?= lang('Whatsapp_settings'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/send_whatsapp'); ?>"><?= lang('send_whatsapp'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_log'); ?>"><?= lang('Whatsapp_log'); ?></a>
                            </li>
                        	<li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_templates'); ?>"><?= lang('Whatsapp_templates'); ?></a>
                            </li>
                        </ul>
                      </li>
                    <?php
							$dbname=str_replace("cpesoft_","",$this->db->database);
								if($dbname=="arkanpro" || $dbname=="test2" || $dbname=="spark")
								{
									?>
                      <li><a><i class="mm_system_settings_lectronic_signature"></i><?= lang('Electronic_signature'); ?><span class="fa fa-chevron-down"></span><span class="pc-micon" style="background-color:rgb(4 169 245);border-radius:12px;float:left;padding:3px;font-size:10px;"><?= lang('new'); ?></span></a>
                          <ul class="nav2 child_menu_ar">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/sign_document'); ?>"><?= lang('Sign_a_document'); ?></a>
                            </li>
						  </ul>
                      </li>
                      <?php
							}
					    ?>









					  <li><a><i class="mm_system_settings_people"></i><?= lang('users'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">

						          <?php if ($Owner) {
                                    ?>

                            <li class="sub_menu"><a href="<?= admin_url('users'); ?>"><?= lang('list_users'); ?></a>
                            </li>
							    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

						    <li><a href="<?= admin_url('billers'); ?>"><?= lang('list_billers'); ?></a>
                            </li>


							<?php
                            }
							?>
                    <?php
                            }
							?>


                        </ul>
                      </li>

					  <li><a><i class="mm_system_settings_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">

						          <?php if ($Owner) {
                                    ?>

							    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

							<li><a href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal"><?= lang('add_biller'); ?></a>
                            </li>


							      <?php if (POS) {
                                    ?>

                            <li><a href="<?= admin_url('pos/add_printer'); ?>"><?= lang('add_printer'); ?></a>
                            </li>
							<?php
                            } ?>

							<?php
                            }
                            } ?>


                        </ul>
                      </li>






					</ul>
                  </li>

                      <?php
			  if($this->Settings->accountingOnly==0)
              {
                if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){
                    ?>

              <li id="mm_carsreports"><a><i class="fa fa-bar-chart-o mm_carsreports"></i> <?= lang('reports'); ?> <span class="fa fa-chevron-down"></span></a>
                <ul class="nav2 child_menu_ar">
                  <li><a href="<?= admin_url('carsreports/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                  <li class="sub_menu"><a href="<?= admin_url('carsreports/tax') ?>"><?= lang('tax_report'); ?></a></li>
                                    <?php
                            if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
                            {
                            }else{
                                ?>

                                    <li><a href="<?= admin_url('carsreports/profit_loss'); ?>"><?= lang('profit_and_loss'); ?></a></li>
                                <?php
                            }
                            ?>

                                    <?php if(!DEMO){
                            ?>
                            <li><a href="<?= admin_url('carsreports/users_report'); ?>"><?= lang('staff_report'); ?></a>
                                </li>

                        <?php
                        }
                        ?>





                    </ul>
                 </li>

        <?php
            }else{
                        ?>
				  <li id="mm_reports"><a><i class="fa fa-bar-chart-o mm_reports"></i> <?= lang('reports'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('reports/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('reports/tax') ?>"><?= lang('tax_report'); ?></a></li>
					  <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
			  }else{
				  ?>

                      <li><a href="<?= admin_url('reports/profit_loss'); ?>"><?= lang('profit_and_loss'); ?></a></li>
				<?php
			  }
			  ?>

					  <?php if(!DEMO){
						  ?>
                          <li><a href="<?= admin_url('reports/users_report'); ?>"><?= lang('staff_report'); ?></a>
                            </li>

					  <?php
					  }
					  ?>





					</ul>
                  </li>
                  <?php
            }
			  }
			  ?>
				  <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
				  ?>

				  <li id="mm_accounts"><a><i class="fa fa-line-chart mm_accounts"></i> <?= lang('Accounting'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('accounts/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('accounts/accounts_tree') ?>"><?= lang('Accounts_tree'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/opening_balance'); ?>"><?= lang('Opening_balance'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/entries'); ?>"><?= lang('Entries'); ?></a></li>
					  <?php
						//if($this->session->userdata('company') =='spark' || $this->session->userdata('company') =='test' || $this->session->userdata('company') =='eqrar')
						{
						?>
                      <li><a href="<?= admin_url('accounts/accounting_period'); ?>"><?= lang('Accounting_Period'); ?></a></li>




						<?php
						}
						?>
                        <li><a><i class="mm_accounts_bond"></i><?= lang('Bond'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar">

							<li><a href="<?= admin_url('accounts/receipts'); ?>"><?= lang('Receipts'); ?></a></li>
							<li><a href="<?= admin_url('accounts/catch_receipts'); ?>"><?= lang('Catch_receipts'); ?></a></li>
							<li><a href="<?= admin_url('accounts/refunding_bonds'); ?>"><?= lang('Refunding_bonds'); ?></a></li>
                            <li><a href="<?= admin_url('accounts/notice_creditor'); ?>"><?= lang('Notice_creditor'); ?></a></li>

                         </ul>
                      </li>
					  <li><a href="<?= admin_url('accounts/payment_types'); ?>"><?= lang('Payment_types'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/cost_centers'); ?>"><?= lang('Cost_centers'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/settings'); ?>"><?= lang('Account_system_settings'); ?></a></li>
					  <li><a><i class="mm_accounts_report"></i><?= lang('reports'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu_ar" style="padding-bottom:20px;">

							<li><a href="<?= admin_url('accounts/accountsguide'); ?>"><?= lang('Accounts_guide'); ?></a></li>
							<li><a href="<?= admin_url('accounts/ledgers'); ?>"><?= lang('Ledgers'); ?></a></li>
							<li><a href="<?= admin_url('accounts/trialbalance'); ?>"><?= lang('Trial_balance'); ?></a></li>
							<li><a href="<?= admin_url('accounts/entriesreport'); ?>"><?= lang('Entries_Report'); ?></a></li>
							<li><a href="<?= admin_url('accounts/incomestatement'); ?>"><?= lang('Income_Statement'); ?></a></li>
							<li><a href="<?= admin_url('accounts/ownersequity'); ?>"><?= lang('Statement_of_owners_equity'); ?></a></li>
							<li><a href="<?= admin_url('accounts/balancesheet'); ?>"><?= lang('Balance_Sheet'); ?></a></li>
							<li><a href="<?= admin_url('accounts/accountstatement'); ?>"><?= lang('Account_statement'); ?></a></li>
							<li><a href="<?= admin_url('accounts/tax') ?>"><?= lang('tax_report'); ?></a></li>
					        <li><a href="<?= admin_url('accounts/agedopeninvoices'); ?>"><?= lang('Aged_Open_Invoices'); ?></a></li>
							<li><a href="<?= admin_url('accounts/agedopeninvoicessupplier'); ?>"><?= lang('Supplier_debt_aging_report'); ?></a></li>
							<li><a href="<?= admin_url('accounts/debtliability'); ?>"><?= lang('Debt_and_Liability'); ?></a></li>
							<li><a href="<?= admin_url('accounts/distributioncostcenters'); ?>"><?= lang('Cost_centers_Report'); ?></a></li>
                            <li><a href="<?= admin_url('accounts/collection_report'); ?>"><?= lang('Collection_report'); ?></a></li>




                         </ul>
                      </li>





					</ul>
                  </li>

				  <?php
					  }
					  ?>











                <?php
			  if(isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset==1)
              {
				  ?>

				  <li id="mm_fixedassets"><a><i class="fa fa-building mm_fixedassets"></i> <?= lang('Fixed_assets'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('fixedassets/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  	<li><a href="<?= admin_url('fixedassets'); ?>"><?= lang('Fixed_assets_list'); ?></a></li>


							<li><a><i class="mm_fixedassets_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu_ar">
									<li><a href="<?= admin_url('fixedassets/group'); ?>"><?= lang('Fixed_assets_group'); ?></a></li>
									<li><a href="<?= admin_url('fixedassets/parent_classes') ?>"><?= lang('Parent_classes'); ?></a></li>
									<li><a href="<?= admin_url('fixedassets/sub_classes') ?>"><?= lang('Sub_classes'); ?></a></li>
									<!--<li><a href="<?= admin_url('fixedassets/add_fixed_asset') ?>"><?= lang('Definition_of_fixed_asset'); ?></a></li>-->
								</ul>
							</li>
                            <li><a href="<?= admin_url('fixedassets/accounts_setting'); ?>"><?= lang('Accounts_settings'); ?></a></li>
							<li><a href="<?= admin_url('fixedassets/revaluation'); ?>"><?= lang('Revaluation_of_fixed_asset'); ?></a></li>
							<li><a href="<?= admin_url('fixedassets/increasing'); ?>"><?= lang('Increasing_the_fixed_asset'); ?></a></li>
							<li><a><i class="mm_fixedassets_reports"></i><?= lang('reports'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu_ar">
                                <li><a href="<?= admin_url('fixedassets/valuation_report'); ?>"><?= lang('Valuation_report'); ?></a></li>
								<li><a href="<?= admin_url('fixedassets/depreciation_report'); ?>"><?= lang('Depreciation_report'); ?></a></li>
								<li><a href="<?= admin_url('fixedassets/increasing_report'); ?>"><?= lang('Increasing_report'); ?></a></li>
                                </ul>
							</li>










					</ul>
                  </li>

				  <?php
					  }
					  ?>


				  <?php
			  if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
              {
				  ?>

				  <li id="mm_manufacturing"><a><i class="fa fa-industry mm_manufacturing"></i> <?= lang('Manufacturing'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('manufacturing/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('manufacturing/bom') ?>"><?= lang('Bill_Of_Material'); ?></a></li>
					  	<li><a href="<?= admin_url('manufacturing'); ?>"><?= lang('Manufacturing_list'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/confirmed'); ?>"><?= lang('Confirmed_products'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/progress'); ?>"><?= lang('In_progress_products'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/done'); ?>"><?= lang('Done_products'); ?></a></li>
							<li><a><i class="mm_products_definitions"></i><?= lang('Archive'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu_ar">
									<li><a href="<?= admin_url('manufacturing/transfers'); ?>"><?= lang('transfers'); ?></a></li>
									<li><a href="<?= admin_url('manufacturing/destructions') ?>"><?= lang('destruction_inventory'); ?></a></li>
								</ul>
							</li>








					</ul>
                  </li>

				  <?php
					  }
					  ?>


                   <?php
			  if(isset($this->Settings->isStore) && $this->Settings->isStore==1 && $Owner)
              {
				  ?>

				  <li id="mm_shop_settings"><a><i class="fa fa-shopping-cart mm_shop_settings"></i> <?= lang('shop'); ?> <span class="fa fa-chevron-down"></span></a>
                    <!--<ul class="nav2 child_menu_ar">
                        <li><a href="<?= admin_url('shop_settings/shop_sync') ?>"><?= lang('Synchronize_Data'); ?></a></li>


					</ul>-->
                  </li>

				  <?php
					  }else{
                        ?>

				  <li id="mm_shop_settings"><a><i class="fa fa-shopping-cart mm_shop_settings"></i> <?= lang('shop'); ?> <span class="fa fa-chevron-down"></span></a>

                  </li>

				  <?php
                      }
					  ?>


<?php
			  if(isset($this->Settings->isTailer) && $this->Settings->isTailer==1)
              {
				  ?>

				  <li id="mm_tailor"><a><i class="fa fa-hand-scissors-o mm_tailor"></i> <?= lang('Tailor'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                      <li><a href="<?= admin_url('tailor') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/tailor') ?>"><?= lang('Tailors'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/collar') ?>"><?= lang('Collar'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/cuff') ?>"><?= lang('Cuff'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/sleeve') ?>"><?= lang('Sleeve'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/filling') ?>"><?= lang('Filling'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/model') ?>"><?= lang('Model'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/embroidery') ?>"><?= lang('Embroidery'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/pocket') ?>"><?= lang('Pocket'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/chest_pocket') ?>"><?= lang('Chest_Pocket'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/chest_pocket_padding') ?>"><?= lang('Chest_Pocket_Padding'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/zipper') ?>"><?= lang('Zipper'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/zipper_padding') ?>"><?= lang('Zipper_Padding'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/buttons') ?>"><?= lang('Buttons'); ?></a></li>








					</ul>
                  </li>

				  <?php
					  }
					  ?>




            <?php
                if(isset($this->Settings->isElectronicLinking) && $this->Settings->isElectronicLinking==1)
                {
                    ?>

                <li  id="mm_sync"><a><i class="fa fa-cloud-download mm_sync"></i> <?= lang('Integrations'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu_ar">
                    <?php if ($Owner) {


				  ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/dashboard'); ?>"><?= lang('dashboard'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('sync/electronic_linking'); ?>"><?= lang('Supported_linking_services'); ?></a>
                            </li>

                            <?php
                            if(isset($this->Settings->isFoodics) && $this->Settings->isFoodics==1)
                            {
                                ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/foodics_sync'); ?>"><?= lang('Synchronize_with_Foodics'); ?></a>
                            </li>





                      <?php

                            }
                            ?>

                        <?php
                            if(isset($this->Settings->isJisr) && $this->Settings->isJisr==1)
                            {
                                ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/jisr_sync'); ?>"><?= lang('Synchronize_with_Jisr'); ?></a>
                            </li>





                      <?php

                            }
                            ?>

                            <?php
                        }
							?>
                        </ul>
                      </li>

                      <?php
                    }
                            ?>






























                </ul>
				<?php }else {
    ?>
				<ul class="nav2 side-menu"  >
                    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

				<li id="mm_welcome" ><a><i class="fa fa-home mm_welcome"></i> <?= lang('home'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url() ?>"><?= lang('dashboard'); ?></a></li>
                    </ul>
                  </li>

                  <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>
                    <li id="mm_cars"><a><i class="fa fa-car mm_cars"></i> <?= lang('Cars_Showroom'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                     <li><a href="<?= admin_url('cars/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('cars/add_vehicle'); ?>"><?= lang('Add_Vehicles'); ?></a></li>
                      <li><a href="<?= admin_url('cars'); ?>"><?= lang('Vehicles'); ?></a></li>
                      <li><a href="<?= admin_url('cars/showrooms'); ?>"><?= lang('Showroom'); ?></a></li>
                      <li><a href="<?= admin_url('cars/services'); ?>"><?= lang('Services'); ?></a></li>
                      <li><a href="<?= admin_url('cars/transfers'); ?>"><?= lang('list_transfers'); ?></a></li>


                        <li><a><i class="mm_cars_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('cars/brands') ?>"><?= lang('brands'); ?></a></li>
                            <li><a href="<?= admin_url('cars/models'); ?>"><?= lang('Car_Type'); ?></a></li>
                            <li><a href="<?= admin_url('cars/categories'); ?>"><?= lang('categories'); ?></a></li>
                            <li><a href="<?= admin_url('cars/features'); ?>"><?= lang('Features'); ?></a></li>

                         </ul>
                      </li>




					</ul>
                  </li>

				  <?php
					  }else{
					  ?>

                  <li id="mm_products"><a><i class="fa fa-building mm_products"></i> <?= lang('warehouses'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('products/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('products/warehouses') ?>"><?= lang('warehouses'); ?></a></li>
                      <li><a href="<?= admin_url('products'); ?>"><?= lang('list_products'); ?></a></li>
                      <li><a href="<?= admin_url('products/quantity_adjustments'); ?>"><?= lang('inventory_opening_balance'); ?></a></li>
                      <li><a href="<?= admin_url('products/transfers'); ?>"><?= lang('list_transfers'); ?></a></li>
                      <li><a href="<?= admin_url('products/destruction_inventory') ?>"><?= lang('Inventory_operations'); ?></a></li>
                      <li><a href="<?= admin_url('products/manual_inventory'); ?>"><?= lang('manual_inventory'); ?></a></li>
                      <li><a href="<?= admin_url('products/stock_counts'); ?>"><?= lang('stock_counts'); ?></a></li>
                      <li><a href="<?= admin_url('products/quantity_adjust'); ?>"><?= lang('Quantity_adjustments'); ?></a></li>
                      <li><a href="<?= admin_url('products/revaluation'); ?>"><?= lang('Re-evaluation_of_cost_prices'); ?></a></li>

					  <li><a><i class="mm_products_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('products/add'); ?>"><?= lang('add_product'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/import_csv'); ?>"><?= lang('import_csv'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/import_excel'); ?>"><?= lang('import_excel'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/print_barcodes'); ?>"><?= lang('print_barcode_label'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/units'); ?>"><?= lang('units'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('products/categories'); ?>"><?= lang('categories'); ?></a>
                            </li>

							<?php if ($Owner) {
                                ?>
							<li><a href="<?= admin_url('products/brands'); ?>"  ><?= lang('brands'); ?></a>
                            </li>
							<?php
							//$dbname=str_replace("cpesoft_","",$this->db->database);
								//if($dbname=="test" || $dbname=="spark")
								{
									?>
							<li><a href="<?= admin_url('products/variants'); ?>"  ><?= lang('variants'); ?></a>
                            </li>

                            <li><a href="<?= admin_url('products/modifiers'); ?>"  ><?= lang('Modifiers'); ?></a>
                            </li>
								<?php
                            } ?>
                              	<?php
                            } ?>


                          </ul>
                        </li>


					   <li><a href="<?= admin_url('products/reports'); ?>"><?= lang('reports'); ?></a></li>




					</ul>
                  </li>

                  <?php
                      }
                      ?>

                <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>


                 <li id="mm_carssales"><a><i class="fa fa-line-chart mm_carssales mm_pos1 mm_promos mm_promo"></i> <?= lang('sales'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('carssales/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/add') ?>"><?= lang('add_sale'); ?></a></li>
					  <li><a href="<?= admin_url('carssales') ?>"><?= lang('list_sales'); ?></a></li>

                      <!--    <?php if (POS) {
                                ?>
                          <li><a href="<?= admin_url('pos/carssales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>


					  <li><a><i class="mm_sales_return"></i><?= lang('pos_sales'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('sales/returns_index'); ?>"><?= lang('list_returns'); ?></a>
                            </li>
                          </ul>
                      </li>
                          <?php
                            } ?> -->

					  <li><a href="<?= admin_url('carssales/deliveries'); ?>"><?= lang('deliveries'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/gift_cards'); ?>"><?= lang('list_gift_cards'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/quotes_index'); ?>"><?= lang('List_reserved_vehicle'); ?></a></li>
                      <li><a href="<?= admin_url('carssales/offers_index'); ?>"><?= lang('list_quotes'); ?></a></li>
                      <li><a href="<?= admin_url('customers'); ?>"><?= lang('list_customers'); ?></a></li>
                      <!--<li><a href="<?= admin_url('promos') ?>"><?= lang('promos'); ?></a></li>
					  <li><a><i class="mm_sales_promo"></i><?= lang('promos'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('promo/promo_type'); ?>"><?= lang('Promos_type'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('promo/add_promo'); ?>"><?= lang('add_promo'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/promos'); ?>"><?= lang('Promos_list'); ?></a>
                            </li>


                          </ul>
                      </li>
					  -->
					  <li><a><i class="mm_carssales_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                          <li class="sub_menu"><a href="<?= admin_url('carssales/customer_groups') ?>"><?= lang('customer_groups'); ?></a></li>
							<!--<li><a href="<?= admin_url('carssales/price_groups') ?>"><?= lang('price_groups'); ?></a></li>
                            -->


                         </ul>
                      </li>
					  <li><a href="<?= admin_url('carssales/reports') ?>"><?= lang('reports'); ?></a></li>





					</ul>
                  </li>


                  <?php
                    }else{
                ?>

                 <li id="mm_sales"><a><i class="fa fa-line-chart mm_sales mm_pos1 mm_promos mm_promo"></i> <?= lang('sales'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('sales/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('sales/add') ?>"><?= lang('add_sale'); ?></a></li>
					  <li><a href="<?= admin_url('sales') ?>"><?= lang('list_sales'); ?></a></li>

                          <?php if (POS) {
                                ?>
                          <li><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>


					  <!--<li><a><i class="mm_sales_return"></i><?= lang('pos_sales'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('pos/sales'); ?>"><?= lang('pos_sales'); ?></a>
                            </li>
                            <li><a href="<?= admin_url('sales/returns_index'); ?>"><?= lang('list_returns'); ?></a>
                            </li>
                          </ul>
                      </li>-->

                          <?php
                            } ?>
					  <?php if ($this->Settings->isDeliveryFromSales==1) {
                                ?>
					  <li><a href="<?= admin_url('sales/deliveries'); ?>"><?= lang('deliveries'); ?></a></li>
                      <?php
                        }else{ ?>
                      <li><a href="<?= admin_url('sales/delivers'); ?>"><?= lang('deliveries'); ?></a></li>
                      <?php
                        } ?>
                      <li><a href="<?= admin_url('sales/coupons'); ?>"><?= lang('Coupons'); ?></a></li>
                      <li><a href="<?= admin_url('sales/gift_cards'); ?>"><?= lang('list_gift_cards'); ?></a></li>
                      <li><a href="<?= admin_url('sales/quotes_index'); ?>"><?= lang('list_quotes'); ?></a></li>
                      <li><a href="<?= admin_url('customers'); ?>"><?= lang('list_customers'); ?></a></li>
                      <li><a href="<?= admin_url('salesman'); ?>"><?= lang('list_salesman'); ?></a></li>

                      <!--<li><a href="<?= admin_url('promos') ?>"><?= lang('promos'); ?></a></li>-->
					  <li><a><i class="mm_sales_promo"></i><?= lang('promos'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('promo/promo_type'); ?>"><?= lang('Promos_type'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('promo/add_promo'); ?>"><?= lang('add_promo'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('promo/promos'); ?>"><?= lang('Promos_list'); ?></a>
                            </li>


                          </ul>
                      </li>

					  <li><a><i class="mm_sales_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('sales/customer_groups') ?>"><?= lang('customer_groups'); ?></a></li>
							<li><a href="<?= admin_url('sales/price_groups') ?>"><?= lang('price_groups'); ?></a></li>
                            <li><a href="<?= admin_url('sales/terms') ?>"><?= lang('Terms_and_conditions'); ?></a></li>
                            <?php
                            if(isset($this->Settings->isSubscriptions) && $this->Settings->isSubscriptions==1)
                            {
                            ?>
                                <li><a href="<?= admin_url('sales/subscriptions') ?>"><?= lang('Subscriptions'); ?></a></li>
                            <?php
                            }
                            ?>

                         </ul>
                      </li>
					  <li><a href="<?= admin_url('sales/reports') ?>"><?= lang('reports'); ?></a></li>





					</ul>
                  </li>

                  <?php
                    }
                ?>
                <?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
				  ?>


				  <li id="mm_carspurchases"><a><i class="fa fa-industry  mm_carspurchases mm_carssuppliers"></i> <?= lang('purchases'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('carspurchases/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('carspurchases/add'); ?>"><?= lang('add_purchase'); ?></a></li>
                      <li><a href="<?= admin_url('carspurchases') ?>"><?= lang('list_purchases'); ?></a></li>
					<!--  <li><a href="<?= admin_url('carspurchases/returns') ?>"><?= lang('return_purchase'); ?></a></li> -->

                      <li><a href="<?= admin_url('carspurchases/expenses'); ?>"><?= lang('list_expenses'); ?></a></li>
                      <li><a href="<?= admin_url('carssuppliers'); ?>"><?= lang('list_cars_owners'); ?></a></li>

					  <li><a><i class="mm_purchases_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">

							<?php if ($Owner) {
                                ?>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/expense_categories'); ?>"  ><?= lang('expense_categories'); ?></a>
                            </li>
								<?php
                            } ?>

                         </ul>
                      </li>

					  <li><a href="<?= admin_url('carspurchases/reports') ?>"><?= lang('reports'); ?></a></li>

					</ul>
                  </li>



                  <?php
                    }else{
                ?>



				  <li id="mm_purchases"><a><i class="fa fa-industry  mm_purchases mm_suppliers"></i> <?= lang('purchases'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('purchases/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li><a href="<?= admin_url('purchases/add'); ?>"><?= lang('add_purchase'); ?></a></li>
                      <li><a href="<?= admin_url('purchases') ?>"><?= lang('list_purchases'); ?></a></li>
					  <li><a href="<?= admin_url('purchases/returns') ?>"><?= lang('return_purchase'); ?></a></li>

                      <li><a href="<?= admin_url('purchases/expenses'); ?>"><?= lang('list_expenses'); ?></a></li>
                      <li><a href="<?= admin_url('suppliers'); ?>"><?= lang('list_suppliers'); ?></a></li>

					  <li><a><i class="mm_purchases_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">

							<?php if ($Owner) {
                                ?>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/expense_categories'); ?>"  ><?= lang('expense_categories'); ?></a>
                            </li>
								<?php
                            } ?>

                         </ul>
                      </li>

					  <li><a href="<?= admin_url('purchases/reports') ?>"><?= lang('reports'); ?></a></li>








					</ul>
                  </li>

                  <?php
                }
                ?>

			  <?php
			  }
			  ?>


              <?php
            if ($Owner || $Admin) {
			  if(isset($this->Settings->isHR) && $this->Settings->isHR==1)
              {
				  ?>
                    <li id="mm_hr"><a href="<?= admin_url('auth/login_hr/') ?>"  target="_blank"><i class="fa fa-users  mm_hr"></i> <?= lang('HR'); ?> <span class="fa fa-chevron-down"></span></a>

                  </li>
			  <?php
			  }
            }
			  ?>

              <?php
				$dbname=str_replace("cpesoft_","",$this->db->database);
				if($dbname=="arkanpro" || $dbname=="spark")
					{
				?>
                    <li id="mm_crm"><a href="<?= admin_url('auth/login_crm/') ?>"  target="_blank"><i class="fa fa-sitemap  mm_crm"></i> <?= lang('CRM'); ?> <span class="fa fa-chevron-down"></span></a>
                    </li>
			  <?php
			  }
			  ?>


                     <?php
                    if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            ?>
                <li  id="mm_branches"><a><i class="fa fa-home mm_branches"></i> <?= lang('Branches'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                    <?php if ($Owner) {

                    ?>

                            <li><a href="<?= admin_url('branches/dashboard') ?>"><?= lang('dashboard'); ?></a></li>


                          <?php
                          if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                            ?>

                            <li class="sub_menu"><a href="<?= admin_url('branches/groups_settings'); ?>"><?= lang('Groups_settings'); ?></a>
                            </li>
                            <?php }
                            if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                            {
                                ?>
                            <li class="sub_menu"><a href="<?= admin_url('branches/companies_settings'); ?>"><?= lang('Companies_settings'); ?></a>
                            </li>
                            <?php }
                            if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                            {
                                ?>
                            <li class="sub_menu"><a href="<?= admin_url('branches/branches_list'); ?>"><?= lang('Branches_list'); ?></a>
                            </li>
                            <?php
                            }
                            ?>
                            <li><a><i class="mm_branches_settings"></i><?= lang('settings'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu">
                                    <li class="sub_menu"><a href="<?= admin_url('branches/main_settings'); ?>"><?= lang('Data_sharing'); ?></a>
                                    </li>
                                    <li class="sub_menu"><a href="<?= admin_url('branches/branches_settings'); ?>"><?= lang('Branches_settings'); ?></a>
                                    </li>
								</ul>
							</li>




                    <?php
                    }
                            ?>



                        </ul>
                    </li>
                    <?php
                    }
                            ?>



                <li  id="mm_auth"><a><i class="fa fa-cog mm_system_settings mm_pos mm_auth mm_billers"></i> <?= lang('User_tools'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">




					  <li><a><i class="mm_system_settings_system_settings"></i><?= lang('Settings'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">

						          <?php if ($Owner) {
                                    ?>
									    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>


                            <li class="sub_menu"><a href="<?= admin_url('system_settings'); ?>"><?= lang('system_settings'); ?></a>
                            </li>


							     <?php if (POS) {
                                    ?>

<?php
                if(isset($this->Settings->isCars) && $this->Settings->isCars==1)
                {
                }else{
				  ?>

					  <li><a href="<?= admin_url('pos/settings') ?>"><?= lang('pos_settings'); ?></a></li>
					  <li><a href="<?= admin_url('pos/printers') ?>"><?= lang('list_printers'); ?></a></li>
					<?php
                            }
                        }
							}
					?>
                      <li><a href="<?= admin_url('system_settings/change_logo') ?>"  data-toggle="modal" data-target="#myModal"><?= lang('change_logo'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/tax_rates') ?>"><?= lang('tax_rates'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/currencies') ?>"><?= lang('currencies'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/credit_cards') ?>"><?= lang('Credit_Cards'); ?></a></li>
					  <?php if ($Settings->IsResturant) {
                                    ?>

					  <li><a href="<?= admin_url('system_settings/departments') ?>"><?= lang('Departments'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/tabels') ?>"><?= lang('Tabels'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/stuff') ?>"><?= lang('Stuff'); ?></a></li>
					  <?php
                            } ?>

					  <li><a href="<?= admin_url('system_settings/email_templates') ?>"><?= lang('email_templates'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/user_groups') ?>"><?= lang('group_permissions'); ?></a></li>
					  <li><a href="<?= admin_url('system_settings/backups') ?>"><?= lang('backups'); ?></a></li>

							<?php
                            } ?>


                        </ul>
                      </li>




                      <li><a><i class="mm_system_settings_sms_settings"></i><?= lang('SMS'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/sms_settings'); ?>"><?= lang('sms_settings'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/send_sms'); ?>"><?= lang('send_sms'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/sms_log'); ?>"><?= lang('sms_log'); ?></a>
                            </li>
                        </ul>
                      </li>

                      <li><a><i class="mm_system_settings_whatsapp_settings"></i><?= lang('Whatsapp'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_settings'); ?>"><?= lang('Whatsapp_settings'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/send_whatsapp'); ?>"><?= lang('send_whatsapp'); ?></a>
                            </li>
							<li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_log'); ?>"><?= lang('Whatsapp_log'); ?></a>
                            </li>
                        	<li class="sub_menu"><a href="<?= admin_url('system_settings/whatsapp_templates'); ?>"><?= lang('Whatsapp_templates'); ?></a>
                            </li>
                        </ul>
                      </li>

                      <?php
							$dbname=str_replace("cpesoft_","",$this->db->database);
								if($dbname=="arkanpro" || $dbname=="test2" || $dbname=="spark")
								{
									?>
                      <li><a><i class="mm_system_settings_lectronic_signature"></i><?= lang('Electronic_signature'); ?><span class="fa fa-chevron-down"></span><span class="pc-micon" style="background-color:rgb(4 169 245);border-radius:12px;float:right;padding:3px;font-size:10px;"><?= lang('new'); ?></span></a>
                          <ul class="nav2 child_menu">
                            <li class="sub_menu"><a href="<?= admin_url('system_settings/sign_document'); ?>"><?= lang('Sign_a_document'); ?></a>
                            </li>
						  </ul>
                      </li>
                      <?php
							}
					    ?>


					  <li><a><i class="mm_system_settings_people"></i><?= lang('users'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">

						          <?php if ($Owner) {
                                    ?>

                            <li class="sub_menu"><a href="<?= admin_url('users'); ?>"><?= lang('list_users'); ?></a>
                            </li>
							    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

						    <li><a href="<?= admin_url('billers'); ?>"><?= lang('list_billers'); ?></a>
                            </li>


							<?php
                            }
                            } ?>


                        </ul>
                      </li>

					  <li><a><i class="mm_system_settings_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">

						          <?php if ($Owner) {
                                    ?>

							    <?php
			  if($this->Settings->accountingOnly==0)
              {
				  ?>

							<li><a href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal"><?= lang('add_biller'); ?></a>
                            </li>


							      <?php if (POS) {
                                    ?>

                            <li><a href="<?= admin_url('pos/add_printer'); ?>"><?= lang('add_printer'); ?></a>
                            </li>
							<?php
                            } ?>

							<?php
                            }
                            } ?>


                        </ul>
                      </li>






					</ul>
                  </li>

                      <?php
			  if($this->Settings->accountingOnly==0)
              {
                if(  isset($this->Settings->isCars) && $this->Settings->isCars==1){

                    ?>

                    <li id="mm_carsreports"><a><i class="fa fa-bar-chart-o mm_carsreports"></i> <?= lang('reports'); ?> <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav2 child_menu">
                        <li><a href="<?= admin_url('carsreports/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                        <li class="sub_menu"><a href="<?= admin_url('carsreports/tax') ?>"><?= lang('tax_report'); ?></a></li>

                               <?php
                if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
                {
                }else{
                    ?>

                        <li><a href="<?= admin_url('carsreports/profit_loss'); ?>"><?= lang('profit_and_loss'); ?></a></li>
                  <?php
                }
                ?>


                      <?php if(!DEMO){
                            ?>
                            <li><a href="<?= admin_url('carsreports/users_report'); ?>"><?= lang('staff_report'); ?></a>
                              </li>

                        <?php
                        }
                        ?>






                      </ul>
                    </li>

                        <?php
                  }else{
                      ?>
				  <li id="mm_reports"><a><i class="fa fa-bar-chart-o mm_reports"></i> <?= lang('reports'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('reports/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('reports/tax') ?>"><?= lang('tax_report'); ?></a></li>

             		  	  <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
			  }else{
				  ?>

                      <li><a href="<?= admin_url('reports/profit_loss'); ?>"><?= lang('profit_and_loss'); ?></a></li>
				<?php
			  }
			  ?>


					<?php if(!DEMO){
						  ?>
                          <li><a href="<?= admin_url('reports/users_report'); ?>"><?= lang('staff_report'); ?></a>
                            </li>

					  <?php
					  }
					  ?>






					</ul>
                  </li>
				      <?php
                  }
							}
						?>


				  <?php
			  if(isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1)
              {
				  ?>

				  <li id="mm_accounts"><a><i class="fa fa-line-chart mm_accounts"></i> <?= lang('Accounting'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('accounts/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/accounts_tree') ?>"><?= lang('Accounts_tree'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/opening_balance'); ?>"><?= lang('Opening_balance'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/entries'); ?>"><?= lang('Entries'); ?></a></li>
					  <?php
						//if($this->session->userdata('company') =='spark' || $this->session->userdata('company') =='test' || $this->session->userdata('company') =='eqrar')
						{
						?>
                      <li><a href="<?= admin_url('accounts/accounting_period'); ?>"><?= lang('Accounting_Period'); ?></a></li>




						<?php
						}
						?>
                      <li><a><i class="mm_accounts_bond"></i><?= lang('Bond'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu">

							<li><a href="<?= admin_url('accounts/receipts'); ?>"><?= lang('Receipts'); ?></a></li>
							<li><a href="<?= admin_url('accounts/catch_receipts'); ?>"><?= lang('Catch_receipts'); ?></a></li>
							<li><a href="<?= admin_url('accounts/refunding_bonds'); ?>"><?= lang('Refunding_bonds'); ?></a></li>
                            <li><a href="<?= admin_url('accounts/notice_creditor'); ?>"><?= lang('Notice_creditor'); ?></a></li>

                         </ul>
                      </li>
					  <li><a href="<?= admin_url('accounts/payment_types'); ?>"><?= lang('Payment_types'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/cost_centers'); ?>"><?= lang('Cost_centers'); ?></a></li>
					  <li><a href="<?= admin_url('accounts/settings'); ?>"><?= lang('Account_system_settings'); ?></a></li>
					  <li><a><i class="mm_accounts_report"></i><?= lang('reports'); ?><span class="fa fa-chevron-down"></span></a>
                          <ul class="nav2 child_menu" style="padding-bottom:20px;">

							<li><a href="<?= admin_url('accounts/accountsguide'); ?>"><?= lang('Accounts_guide'); ?></a></li>
							<li><a href="<?= admin_url('accounts/ledgers'); ?>"><?= lang('Ledgers'); ?></a></li>
							<li><a href="<?= admin_url('accounts/trialbalance'); ?>"><?= lang('Trial_balance'); ?></a></li>
							<li><a href="<?= admin_url('accounts/entriesreport'); ?>"><?= lang('Entries_Report'); ?></a></li>
							<li><a href="<?= admin_url('accounts/incomestatement'); ?>"><?= lang('Income_Statement'); ?></a></li>
							<li><a href="<?= admin_url('accounts/ownersequity'); ?>"><?= lang('Statement_of_owners_equity'); ?></a></li>
							<li><a href="<?= admin_url('accounts/balancesheet'); ?>"><?= lang('Balance_Sheet'); ?></a></li>
							<li><a href="<?= admin_url('accounts/accountstatement'); ?>"><?= lang('Account_statement'); ?></a></li>
							<li><a href="<?= admin_url('accounts/tax') ?>"><?= lang('tax_report'); ?></a></li>
					        <li><a href="<?= admin_url('accounts/agedopeninvoices'); ?>"><?= lang('Aged_Open_Invoices'); ?></a></li>
							<li><a href="<?= admin_url('accounts/agedopeninvoicessupplier'); ?>"><?= lang('Supplier_debt_aging_report'); ?></a></li>
							<li><a href="<?= admin_url('accounts/debtliability'); ?>"><?= lang('Debt_and_Liability'); ?></a></li>
							<li><a href="<?= admin_url('accounts/distributioncostcenters'); ?>"><?= lang('Cost_centers_Report'); ?></a></li>
                            <li><a href="<?= admin_url('accounts/collection_report'); ?>"><?= lang('Collection_report'); ?></a></li>




                         </ul>
                      </li>





					</ul>
                  </li>

				  <?php
					  }
					  ?>

            <?php
			  if(isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset==1)
              {
				  ?>

				  <li id="mm_fixedassets"><a><i class="fa fa-building mm_fixedassets"></i> <?= lang('Fixed_assets'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('fixedassets/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  	<li><a href="<?= admin_url('fixedassets'); ?>"><?= lang('Fixed_assets_list'); ?></a></li>


							<li><a><i class="mm_fixedassets_definitions"></i><?= lang('Definitions'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu">
									<li><a href="<?= admin_url('fixedassets/group'); ?>"><?= lang('Fixed_assets_group'); ?></a></li>
									<li><a href="<?= admin_url('fixedassets/parent_classes') ?>"><?= lang('Parent_classes'); ?></a></li>
									<li><a href="<?= admin_url('fixedassets/sub_classes') ?>"><?= lang('Sub_classes'); ?></a></li>
									<!--<li><a href="<?= admin_url('fixedassets/add_fixed_asset') ?>"><?= lang('Definition_of_fixed_asset'); ?></a></li>-->
								</ul>
							</li>

                            <li><a href="<?= admin_url('fixedassets/accounts_setting'); ?>"><?= lang('Accounts_settings'); ?></a></li>
							<li><a href="<?= admin_url('fixedassets/revaluation'); ?>"><?= lang('Revaluation_of_fixed_asset'); ?></a></li>
							<li><a href="<?= admin_url('fixedassets/increasing'); ?>"><?= lang('Increasing_the_fixed_asset'); ?></a></li>
							<li><a><i class="mm_fixedassets_reports"></i><?= lang('reports'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu">
                                <li><a href="<?= admin_url('fixedassets/valuation_report'); ?>"><?= lang('Valuation_report'); ?></a></li>
								<li><a href="<?= admin_url('fixedassets/depreciation_report'); ?>"><?= lang('Depreciation_report'); ?></a></li>
								<li><a href="<?= admin_url('fixedassets/increasing_report'); ?>"><?= lang('Increasing_report'); ?></a></li>
								</ul>
							</li>










					</ul>
                  </li>

				  <?php
					  }
					  ?>

                  	  <?php
			  if(isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing==1)
              {
				  ?>

				  <li id="mm_manufacturing"><a><i class="fa fa-industry mm_manufacturing"></i> <?= lang('Manufacturing'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('manufacturing/dashboard') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('manufacturing/bom') ?>"><?= lang('Bill_Of_Material'); ?></a></li>


							<li><a href="<?= admin_url('manufacturing'); ?>"><?= lang('Manufacturing_list'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/confirmed'); ?>"><?= lang('Confirmed_products'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/progress'); ?>"><?= lang('In_progress_products'); ?></a></li>
							<li><a href="<?= admin_url('manufacturing/done'); ?>"><?= lang('Done_products'); ?></a></li>
							<li><a><i class="mm_products_definitions"></i><?= lang('Archive'); ?><span class="fa fa-chevron-down"></span></a>
								<ul class="nav2 child_menu">
									<li><a href="<?= admin_url('manufacturing/transfers'); ?>"><?= lang('transfers'); ?></a></li>
									<li><a href="<?= admin_url('manufacturing/destructions') ?>"><?= lang('destruction_inventory'); ?></a></li>
								</ul>
							</li>








					</ul>
                  </li>

				  <?php
					  }
					  ?>

                      <?php
			  if(isset($this->Settings->isStore) && $this->Settings->isStore==1 && $Owner)
              {
				  ?>

				  <li id="mm_shop_settings"><a><i class="fa fa-shopping-cart mm_shop_settings"></i> <?= lang('shop'); ?> <span class="fa fa-chevron-down"></span></a>
                    <!--<ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('shop_settings/shop_sync') ?>"><?= lang('Synchronize_Data'); ?></a></li>


					</ul>-->
                  </li>

				  <?php
					  }else{
                        ?>

				  <li id="mm_shop_settings"><a><i class="fa fa-shopping-cart mm_shop_settings"></i> <?= lang('shop'); ?> <span class="fa fa-chevron-down"></span></a>

                  </li>

				  <?php
                      }
					  ?>




<?php
			  if(isset($this->Settings->isTailer) && $this->Settings->isTailer==1)
              {
				  ?>

				  <li id="mm_tailor"><a><i class="fa fa-hand-scissors-o mm_tailor"></i> <?= lang('Tailor'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                      <li><a href="<?= admin_url('tailor') ?>"><?= lang('dashboard'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/tailor') ?>"><?= lang('Tailors'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/collar') ?>"><?= lang('Collar'); ?></a></li>
					  <li class="sub_menu"><a href="<?= admin_url('tailor/cuff') ?>"><?= lang('Cuff'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/sleeve') ?>"><?= lang('Sleeve'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/filling') ?>"><?= lang('Filling'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/model') ?>"><?= lang('Model'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/embroidery') ?>"><?= lang('Embroidery'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/pocket') ?>"><?= lang('Pocket'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/chest_pocket') ?>"><?= lang('Chest_Pocket'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/chest_pocket_padding') ?>"><?= lang('Chest_Pocket_Padding'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/zipper') ?>"><?= lang('Zipper'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/zipper_padding') ?>"><?= lang('Zipper_Padding'); ?></a></li>
                      <li class="sub_menu"><a href="<?= admin_url('tailor/buttons') ?>"><?= lang('Buttons'); ?></a></li>







					</ul>
                  </li>

				  <?php
					  }
					  ?>


            <?php
              if(isset($this->Settings->isElectronicLinking) && $this->Settings->isElectronicLinking==1)
              {
                ?>

                    <li  id="mm_sync"><a><i class="fa fa-cloud-download mm_sync"></i> <?= lang('Integrations'); ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav2 child_menu">
                    <?php if ($Owner) {


				  ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/dashboard'); ?>"><?= lang('dashboard'); ?></a>
                            </li>
                            <li class="sub_menu"><a href="<?= admin_url('sync/electronic_linking'); ?>"><?= lang('Supported_linking_services'); ?></a>
                            </li>

                            <?php
                            if(isset($this->Settings->isFoodics) && $this->Settings->isFoodics==1)
                            {
                                ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/foodics_sync'); ?>"><?= lang('Synchronize_with_Foodics'); ?></a>
                            </li>





                      <?php

                            }
                            ?>
                            <?php
                            if(isset($this->Settings->isJisr) && $this->Settings->isJisr==1)
                            {
                                ?>


                            <li class="sub_menu"><a href="<?= admin_url('sync/jisr_sync'); ?>"><?= lang('Synchronize_with_Jisr'); ?></a>
                            </li>





                      <?php

                            }
                            ?>
                            <?php
                        }
							?>
                        </ul>
                      </li>

                      <?php


                        }
							?>



                </ul>

				<?php }
    ?>



              </div>
             </div>
            <!-- /sidebar menu -->


			<!-- /menu footer buttons -->
            <div class="sidebar-footer2 hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings" href="<?= admin_url('users'); ?>">
                <span class="fa fa-cogs fa-lg" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen" style="cursor:pointer;color: #5A738E;">
                <span class="fa fa-arrows-alt fa-lg" aria-hidden="true" onclick="fullscreen();"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock" style="cursor:pointer;color: #5A738E;">
                <span class="fa fa-lock fa-lg" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?= admin_url('logout'); ?>">
                <span class="fa fa-power-off fa-lg" aria-hidden="true"></span>
              </a>
            </div>

			</div>
        </div>



		<!-- top navigation -->

		<?php if ($Settings->user_language == 'arabic') {
    ?>
		  <div class="top_nav_ar no-print"   id="mainnav1">
		  <div class="nav_menu no-print">
              <div class="nav2 toggle2_ar">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <nav class="nav2 navbar-nav2">
              <ul class=" navbar-right_ar">

      <?php }else {
    ?>
  <div class="top_nav no-print"   id="mainnav1">
  <div class="nav_menu no-print">
              <div class="nav2 toggle2">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <nav class="nav2 navbar-nav2">
              <ul class=" navbar-right">

      <?php
}
    ?>


                    <li class="dropdown hidden-sm">
                        <a class="btn1 tip" title="<?= lang('calculator') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                            <i class="fa fa-calculator"></i>
                        </a>
                        <ul class="dropdown-menu pull-right calc">
                            <li style="display: inherit; float: revert;" class="dropdown-content">
                                <span id="inlineCalc"></span>
                            </li>
                        </ul>
                    </li>
                    <?php if ($info) {
        ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn1 tip" title="<?= lang('notifications') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-info-circle"></i>
                                <span class="number blightOrange black"><?= sizeof($info) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header"><i class="fa fa-info-circle"></i> <?= lang('notifications'); ?></li>
                                <li class="dropdown-content">
                                    <div class="scroll-div">
                                        <div class="top-menu-scroll">
                                            <ol class="oe">
                                                <?php foreach ($info as $n) {
            echo '<li>' . $n->comment . '</li>';
        } ?>
                                            </ol>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    <?php
    } ?>
                    <?php if ($events) {
        ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn1 tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                <i class="fa fa-calendar"></i>
                                <span class="number blightOrange black"><?= sizeof($events) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header">
                                <i class="fa fa-calendar"></i> <?= lang('upcoming_events'); ?>
                                </li>
                                <li class="dropdown-content">
                                    <div class="top-menu-scroll">
                                        <ol class="oe">
                                            <?php foreach ($events as $event) {
            echo '<li>' . date($dateFormats['php_ldate'], strtotime($event->start)) . ' <strong>' . $event->title . '</strong><br>' . $event->description . '</li>';
        } ?>
                                        </ol>
                                    </div>
                                </li>
                                <li class="dropdown-footer">
                                    <a href="<?= admin_url('calendar') ?>" class="btn-block link">
                                        <i class="fa fa-calendar"></i> <?= lang('calendar') ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php
    } else {
        ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn1 tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="<?= admin_url('calendar') ?>">
                            <i class="fa fa-calendar"></i>
                        </a>
                    </li>
                    <?php
    } ?>

                <li class="dropdown hidden-sm">
                        <a class="btn1 tip" title="<?= lang('language') ?>" data-placement="bottom" data-toggle="dropdown"
                           href="#">
                            <img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt="">
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <?php $scanned_lang_dir = array_map(function ($path) {
        return basename($path);
    }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) {
								if($entry=="arabic" || $entry=="english"){
                                ?>
                                <li>
                                    <a href="<?= admin_url('welcome/language/' . $entry); ?>">
                                        <img src="<?= base_url('assets/images/' . $entry . '.png'); ?>" class="language-img">
                                        &nbsp;&nbsp;<?= ucwords($entry); ?>
                                    </a>
                                </li>
                            <?php
                            } }?>

                        </ul>
                    </li>

					<!--

					<li class="dropdown hidden-sm">
                            <a class="btn blightOrange tip" title="<?= lang('alerts') ?>"
                                data-placement="left" data-toggle="dropdown" href="#">
                                <i class="fa fa-exclamation-triangle"></i>
                         -->
					 <?php if (($Owner || $Admin || $GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts']) && ($qty_alert_num > 0 || $exp_alert_num > 0 || $shop_sale_alerts)) {
                                ?>

					<li class="dropdown hidden-sm">

				  <a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" aria-expanded="false" >
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green"><?= $qty_alert_num + (($Settings->product_expiry) ? $exp_alert_num : 0) + $shop_sale_alerts + $shop_payment_alerts; ?></span>
                  </a>
                  <ul class="dropdown-menu pull-right">
                                <?php if ($qty_alert_num > 0) {
                                    ?>
                                <li>
                                    <a href="<?= admin_url('products/quantity_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                                        <?php if ($Settings->user_language == 'arabic') {
                                        ?>
		                                <span style="padding-left: 45px;"><?= lang('quantity_alerts') ?></span>
                                        <?php }else {
                                        ?>
	                                    <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
                                        <?php
                                        }
                                        ?>
                                    </a>
                                </li>
                                <?php
                                } ?>
                                <?php if ($Settings->product_expiry) {
                                    ?>
                                <li>
                                    <a href="<?= admin_url('products/expiry_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $exp_alert_num; ?></span>
                                        <?php if ($Settings->user_language == 'arabic') {
                                        ?>
		                                <span style="padding-left: 45px;"><?= lang('expiry_alerts') ?></span>
                                        <?php }else {
                                        ?>
	                                    <span style="padding-right: 35px;"><?= lang('expiry_alerts') ?></span>
                                        <?php
                                        }
                                        ?>

                                    </a>
                                </li>
                                <?php
                                } ?>
                                <?php if ($shop_sale_alerts) {
                                    ?>
                                <li>
                                    <a href="<?= admin_url('sales?shop=yes&delivery=no') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_sale_alerts; ?></span>
                                        <?php if ($Settings->user_language == 'arabic') {
                                        ?>
		                                <span style="padding-left: 45px;"><?= lang('sales_x_delivered') ?></span>
                                        <?php }else {
                                        ?>
	                                    <span style="padding-right: 35px;"><?= lang('sales_x_delivered') ?></span>
                                        <?php
                                        }
                                        ?>

                                    </a>
                                </li>
                                <?php
                                } ?>
                                <?php if ($shop_payment_alerts) {
                                    ?>
                                <li role="presentation" class="nav-item dropdown open">
                                    <a href="<?= admin_url('sales?shop=yes&attachment=yes') ?>" class="dropdown-toggle info-number"  id="navbarDropdown1" data-toggle="dropdown" aria-expanded="false">
                                        <span class="badge bg-green" style="margin-top:3px;"><?= $shop_payment_alerts; ?></span>
                                        <?php if ($Settings->user_language == 'arabic') {
                                        ?>
		                                <span style="padding-left: 45px;"><?= lang('manual_payments') ?></span>
                                        <?php }else {
                                        ?>
	                                    <span style="padding-right: 35px;"><?= lang('manual_payments') ?></span>
                                        <?php
                                        }
                                        ?>

                                    </a>
                                </li>
                                <?php
                                } ?>
                            </ul>
                </li>
                 <?php
                                } ?>


                    <?php if (POS && $Settings->pos==1) {
                                ?>
                    <li class="dropdown hidden-sm" >
                        <a class="btn1 tip" title="<?= lang('pos') ?>" data-placement="bottom" href="<?= admin_url('pos') ?>">
                            <i  class="fa fa-th-large"></i>
                        </a>
                    </li>
                    <?php
                            } ?>







              </ul>
            </nav>
          </div>
        </div>

		<?php
		}
		?>






		<?php if ($Settings->user_language == 'arabic') {
    ?>
		<div class=" ar_left_col" role="main" id="controls" style="min-height:677px;">
		<?php }else {
    ?>
	<div class=" right_col" role="main" id="controls">
<?php
}
    ?>
    <div class="container" id="container">

	<?php
	if (strlen(strstr($agent, 'Firefox')) > 0) {
		?>
        <div  id="main-con">
		<?php
	}else{
	?>
		<!--<div class="row clsoflow" id="main-con">-->
		<div id="main-con">

			<?php
	}
	?>

        <table class="lt">
		<tr>

		<td class="content-con">
            <div id="content">
                <?php  if($m!=="reports"){
					?>
                <div class="row">
                    <?php if ($message) {
                                ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $message; ?>
                            </div>
                        </div>
                        <?php
                            } ?>
                        <?php if ($error) {
                                ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="alert alert-danger">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $error; ?>
                            </div>
                        </div>
                        <?php
                            } ?>
                        <?php if ($warning) {
                                ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="alert alert-warning">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $warning; ?>
                            </div>
                        </div>
                        <?php
                            } ?>
                        <?php if ($is_new) {
                                ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="alert alert-warning" style="background:#fcf2bc;">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= lang('You_are_using_the_default_password').'.' ?>
                                <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>"> <?= lang('Click_here_to_change_your_password'); ?></a>
                            </div>
                        </div>
                        <?php
                            } ?>
                        <?php
                        if ($info && !stripos(current_url(),'SalesDetails')) {
                            foreach ($info as $n) {
                                if (!$this->session->userdata('hidden' . $n->id)) {
                                    ?>
                        <div class="col-sm-12 col-md-12">
                                    <div class="alert alert-info">
                                        <a href="#" id="<?= $n->id ?>" class="close hideComment external"
                                           data-dismiss="alert">&times;</a>
                                        <?= $n->comment; ?>
                                    </div>
                        </div>
                                <?php
                                }
                            }
                        } ?>
                        <div class="alerts-con"></div>
						</div>

			   <?php } ?>







			<!--


			<div id="sidebar-left">
                <div class="sidebar-nav nav-collapse collapse navbar-collapse" id="sidebar_menu">
                    <ul class="nav main-menu">
                        <li class="mm_welcome">
                            <a href="<?= admin_url() ?>">
                                <i class="fa fa-dashboard"></i>
                                <span class="text"> <?= lang('dashboard'); ?></span>
                            </a>
                        </li>

                        <?php
                        if ($Owner || $Admin) {
                            ?>

                            <li class="mm_products">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-barcode"></i>
                                    <span class="text"> <?= lang('warehouses'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
									<li id="products_warehouses">
										<a href="<?= admin_url('products/warehouses') ?>">
											<i class="fa fa-building-o"></i><span class="text"> <?= lang('warehouses'); ?></span>
										</a>
									</li>
									<li id="products_index">
												<a class="submenu" href="<?= admin_url('products'); ?>">
													<i class="fa fa-barcode"></i>
													<span class="text"> <?= lang('list_products'); ?></span>
												</a>
									</li>
									<li id="products_quantity_adjustments">
                                        <a class="submenu" href="<?= admin_url('products/quantity_adjustments'); ?>">
                                            <i class="fa fa-filter"></i>
                                            <span class="text"> <?= lang('inventory_opening_balance'); ?></span>
                                        </a>
                                    </li>
									<li id="products_transfers">
                                        <a class="submenu" href="<?= admin_url('products/transfers'); ?>">
                                            <i class="fa fa-star-o"></i><span class="text"> <?= lang('list_transfers'); ?></span>
                                        </a>
                                    </li>
                                    <li id="products_destruction_inventory">
                                        <a href="<?= admin_url('products/destruction_inventory') ?>">
                                            <i class="fa fa-building"></i><span class="text"> <?= lang('destruction_inventory'); ?></span>
                                        </a>
                                    </li>
									<li id="products_manual_inventory">
                                        <a class="submenu" href="<?= admin_url('products/manual_inventory'); ?>">
                                            <i class="fa fa-list-ol"></i>
                                            <span class="text"> <?= lang('manual_inventory'); ?></span>
                                        </a>
                                    </li>
                                    <li id="products_stock_counts">
                                        <a class="submenu" href="<?= admin_url('products/stock_counts'); ?>">
                                            <i class="fa fa-list-ol"></i>
                                            <span class="text"> <?= lang('stock_counts'); ?></span>
                                        </a>
                                    </li>
									<li class="mm_products_definitions">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-barcode"></i>
                                            <span class="text"> <?= lang('Definitions'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>


											<li id="products_add">
												<a class="submenu" href="<?= admin_url('products/add'); ?>">
													<i class="fa fa-plus-circle"></i>
													<span class="text"> <?= lang('add_product'); ?></span>
												</a>
											</li>
											<li id="products_import_csv">
												<a class="submenu" href="<?= admin_url('products/import_csv'); ?>">
													<i class="fa fa-file-text"></i>
													<span class="text"> <?= lang('import_products'); ?></span>
												</a>
											</li>
											<li id="products_print_barcodes">
												<a class="submenu" href="<?= admin_url('products/print_barcodes'); ?>">
													<i class="fa fa-tags"></i>
													<span class="text"> <?= lang('print_barcode_label'); ?></span>
												</a>
											</li>
											<li id="products_units">
												<a href="<?= admin_url('products/units') ?>">
													<i class="fa fa-wrench"></i><span class="text"> <?= lang('units'); ?></span>
												</a>
											</li>
											<li id="products_categories">
												<a href="<?= admin_url('products/categories') ?>">
													<i class="fa fa-folder-open"></i><span class="text"> <?= lang('categories'); ?></span>
												</a>
											</li>








										</ul>
									</li>
									<li id="products_reports">
                                        <a class="submenu" href="<?= admin_url('products/reports'); ?>">
                                            <i class="fa fa-filter"></i>
                                            <span class="text"> <?= lang('reports'); ?></span>
                                        </a>
                                    </li>










                                </ul>
                            </li>


                            <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'sales' ? 'mm_pos' : '' ?>">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-heart"></i>
                                    <span class="text"> <?= lang('sales'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="sales_index">
                                        <a class="submenu" href="<?= admin_url('sales'); ?>">
                                            <i class="fa fa-heart"></i>
                                            <span class="text"> <?= lang('list_sales'); ?></span>
                                        </a>
                                    </li>
                                    <?php if (POS) {
                                ?>
                                    <li id="pos_sales">
                                        <a class="submenu" href="<?= admin_url('pos/sales'); ?>">
                                            <i class="fa fa-heart"></i>
                                            <span class="text"> <?= lang('pos_sales'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <li id="sales_add">
                                        <a class="submenu" href="<?= admin_url('sales/add'); ?>">
                                            <i class="fa fa-plus-circle"></i>
                                            <span class="text"> <?= lang('add_sale'); ?></span>
                                        </a>
                                    </li>
                                    <li id="sales_sale_by_csv">
                                        <a class="submenu" href="<?= admin_url('sales/sale_by_csv'); ?>">
                                            <i class="fa fa-plus-circle"></i>
                                            <span class="text"> <?= lang('add_sale_by_csv'); ?></span>
                                        </a>
                                    </li>
                                    <li id="sales_deliveries">
                                        <a class="submenu" href="<?= admin_url('sales/deliveries'); ?>">
                                            <i class="fa fa-truck"></i>
                                            <span class="text"> <?= lang('deliveries'); ?></span>
                                        </a>
                                    </li>
                                    <li id="sales_gift_cards">
                                        <a class="submenu" href="<?= admin_url('sales/gift_cards'); ?>">
                                            <i class="fa fa-gift"></i>
                                            <span class="text"> <?= lang('list_gift_cards'); ?></span>
                                        </a>
                                    </li>



									<li class="mm_sales_quote">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-heart-o"></i>
                                            <span class="text"> <?= lang('quotes'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

							            <li id="sales_quotes_index">
                                        <a class="submenu" href="<?= admin_url('sales/quotes_index'); ?>">
                                            <i class="fa fa-heart-o"></i>
                                            <span class="text"> <?= lang('list_quotes'); ?></span>
                                        </a>
                                        </li>
                                        <li id="sales_add_quote">
                                        <a class="submenu" href="<?= admin_url('sales/add_quote'); ?>">
                                            <i class="fa fa-plus-circle"></i>
                                            <span class="text"> <?= lang('add_quote'); ?></span>
                                        </a>
                                        </li>


									</ul>
									</li>


									<li class="mm_sales_return">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-random"></i>
                                            <span class="text"> <?= lang('returns'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

							                <li id="returns_index">
                                            <a class="submenu" href="<?= admin_url('sales/returns_index'); ?>">
                                            <i class="fa fa-random"></i><span class="text"> <?= lang('list_returns'); ?></span>
                                            </a>
                                            </li>
											<li id="returns_add">
											<a class="submenu" href="<?= admin_url('sales/add_return'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_return'); ?></span>
											</a>
											</li>


									</ul>
									</li>



									<li class="mm_sales_report">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-bar-chart-o"></i>
										    <span class="text"> <?= lang('reports'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

									<li id="sales_best_sellers">
                                        <a href="<?= admin_url('sales/best_sellers') ?>">
                                            <i class="fa fa-line-chart"></i><span class="text"> <?= lang('best_sellers'); ?></span>
                                        </a>
                                    </li>
                                    <li id="sales_daily_sales">
                                        <a href="<?= admin_url('sales/daily_sales') ?>">
                                            <i class="fa fa-calendar"></i><span class="text"> <?= lang('daily_sales'); ?></span>
                                        </a>
                                    </li>
                                    <li id="sales_monthly_sales">
                                        <a href="<?= admin_url('sales/monthly_sales') ?>">
                                            <i class="fa fa-calendar"></i><span class="text"> <?= lang('monthly_sales'); ?></span>
                                        </a>
                                    </li>
                                    <li id="sales_sales">
                                        <a href="<?= admin_url('sales/sales') ?>">
                                            <i class="fa fa-heart"></i><span class="text"> <?= lang('sales_report'); ?></span>
                                        </a>
                                    </li>


									</ul>
									</li>






                                </ul>
                            </li>



                            <li class="mm_purchases">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('purchases'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="purchases_index">
                                        <a class="submenu" href="<?= admin_url('purchases'); ?>">
                                            <i class="fa fa-star"></i>
                                            <span class="text"> <?= lang('list_purchases'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_add">
                                        <a class="submenu" href="<?= admin_url('purchases/add'); ?>">
                                            <i class="fa fa-plus-circle"></i>
                                            <span class="text"> <?= lang('add_purchase'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_purchase_by_csv">
                                        <a class="submenu" href="<?= admin_url('purchases/purchase_by_csv'); ?>">
                                            <i class="fa fa-plus-circle"></i>
                                            <span class="text"> <?= lang('add_purchase_by_csv'); ?></span>
                                        </a>
                                    </li>

									<li class="mm_purchases_report">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-bar-chart-o"></i>
											<span class="text"> <?= lang('purchases_reports'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

									<li id="purchases_daily_purchases">
                                        <a href="<?= admin_url('purchases/daily_purchases') ?>">
                                            <i class="fa fa-calendar"></i><span class="text"> <?= lang('daily_purchases'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_monthly_purchases">
                                        <a href="<?= admin_url('purchases/monthly_purchases') ?>">
                                            <i class="fa fa-calendar"></i><span class="text"> <?= lang('monthly_purchases'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_purchases">
                                        <a href="<?= admin_url('purchases/purchases') ?>">
                                            <i class="fa fa-star"></i><span class="text"> <?= lang('purchases_report'); ?></span>
                                        </a>
                                    </li>


									</ul>
									</li>



									<li id="purchases_expenses">
                                        <a class="submenu" href="<?= admin_url('purchases/expenses'); ?>">
                                            <i class="fa fa-dollar"></i>
                                            <span class="text"> <?= lang('list_expenses'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_add_expense">
                                        <a class="submenu" href="<?= admin_url('purchases/add_expense'); ?>" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle"></i>
                                            <span class="text"> <?= lang('add_expense'); ?></span>
                                        </a>
                                    </li>

									<li class="mm_purchases_expenses_report">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-bar-chart-o"></i>
											<span class="text"> <?= lang('expenses_reports'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

									<li id="purchases_expenses_report">
                                        <a href="<?= admin_url('purchases/expenses_report') ?>">
                                            <i class="fa fa-star"></i><span class="text"> <?= lang('expenses_report'); ?></span>
                                        </a>
                                    </li>


									</ul>
									</li>





                                </ul>
                            </li>



                            <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                <a class="dropmenu" href="#">
                                <i class="fa fa-users"></i>
                                <span class="text"> <?= lang('people'); ?> </span>
                                <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($Owner) {
                                ?>
                                    <li id="auth_users">
                                        <a class="submenu" href="<?= admin_url('users'); ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('list_users'); ?></span>
                                        </a>
                                    </li>
                                    <li id="auth_create_user">
                                        <a class="submenu" href="<?= admin_url('users/create_user'); ?>">
                                            <i class="fa fa-user-plus"></i><span class="text"> <?= lang('new_user'); ?></span>
                                        </a>
                                    </li>



									<li class="mm_auth_report">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-bar-chart-o"></i>
											<span class="text"> <?= lang('auth_reports'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

									<li id="auth_users_report">
                                        <a href="<?= admin_url('auth/users_report') ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('staff_report'); ?></span>
                                        </a>
                                    </li>

									</ul>
									</li>





									<li id="billers_index">
                                        <a class="submenu" href="<?= admin_url('billers'); ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('list_billers'); ?></span>
                                        </a>
                                    </li>
                                    <li id="billers_index">
                                        <a class="submenu" href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_biller'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers'); ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('list_customers'); ?></span>
                                        </a>
                                    </li>
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_customer'); ?></span>
                                        </a>
                                    </li>

									<li class="mm_customers_report">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-bar-chart-o"></i>
											<span class="text"> <?= lang('customers_reports'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

									<li id="customers_customers_report">
                                        <a href="<?= admin_url('customers/customers_report') ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('customers_report'); ?></span>
                                        </a>
                                    </li>

									</ul>
									</li>


									<li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers'); ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('list_suppliers'); ?></span>
                                        </a>
                                    </li>
                                    <li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_supplier'); ?></span>
                                        </a>
                                    </li>


									<li class="mm_suppliers_report">
                                        <a class="dropmenu" href="#">
                                            <i class="fa fa-bar-chart-o"></i>
											<span class="text"> <?= lang('suppliers_reports'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                    <ul>

								    <li id="suppliers_supplier_report">
                                        <a href="<?= admin_url('suppliers/supplier_report') ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('suppliers_report'); ?></span>
                                        </a>
                                    </li>

									</ul>
									</li>



                                </ul>
                            </li>
                            <li class="mm_notifications">
                                <a class="submenu" href="<?= admin_url('notifications'); ?>">
                                    <i class="fa fa-info-circle"></i><span class="text"> <?= lang('notifications'); ?></span>
                                </a>
                            </li>
                            <li class="mm_calendar">
                                <a class="submenu" href="<?= admin_url('calendar'); ?>">
                                    <i class="fa fa-calendar"></i><span class="text"> <?= lang('calendar'); ?></span>
                                </a>
                            </li>
                            <?php if ($Owner) {
                                ?>
                                <li class="mm_system_settings <?= strtolower($this->router->fetch_method()) == 'sales' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#">
                                        <i class="fa fa-cog"></i><span class="text"> <?= lang('settings'); ?> </span>
                                        <span class="chevron closed"></span>
                                    </a>
                                    <ul>
                                        <li id="system_settings_index">
                                            <a href="<?= admin_url('system_settings') ?>">
                                                <i class="fa fa-cogs"></i><span class="text"> <?= lang('system_settings'); ?></span>
                                            </a>
                                        </li>
                                        <?php if (POS) {
                                    ?>
                                        <li id="pos_settings">
                                            <a href="<?= admin_url('pos/settings') ?>">
                                                <i class="fa fa-th-large"></i><span class="text"> <?= lang('pos_settings'); ?></span>
                                            </a>
                                        </li>
                                        <li id="promos_index">
                                            <a href="<?= admin_url('promos') ?>">
                                                <i class="fa fa-cogs"></i><span class="text"> <?= lang('promos'); ?></span>
                                            </a>
                                        </li>
                                        <li id="pos_printers">
                                            <a href="<?= admin_url('pos/printers') ?>">
                                                <i class="fa fa-print"></i><span class="text"> <?= lang('list_printers'); ?></span>
                                            </a>
                                        </li>
                                        <li id="pos_add_printer">
                                            <a href="<?= admin_url('pos/add_printer') ?>">
                                                <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_printer'); ?></span>
                                            </a>
                                        </li>
                                        <?php
                                } ?>
                                        <li id="system_settings_change_logo">
                                            <a href="<?= admin_url('system_settings/change_logo') ?>" data-toggle="modal" data-target="#myModal">
                                                <i class="fa fa-upload"></i><span class="text"> <?= lang('change_logo'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_currencies">
                                            <a href="<?= admin_url('system_settings/currencies') ?>">
                                                <i class="fa fa-money"></i><span class="text"> <?= lang('currencies'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_customer_groups">
                                            <a href="<?= admin_url('system_settings/customer_groups') ?>">
                                                <i class="fa fa-chain"></i><span class="text"> <?= lang('customer_groups'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_price_groups">
                                            <a href="<?= admin_url('system_settings/price_groups') ?>">
                                                <i class="fa fa-dollar"></i><span class="text"> <?= lang('price_groups'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_expense_categories">
                                            <a href="<?= admin_url('system_settings/expense_categories') ?>">
                                                <i class="fa fa-folder-open"></i><span class="text"> <?= lang('expense_categories'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_brands">
                                            <a href="<?= admin_url('system_settings/brands') ?>">
                                                <i class="fa fa-th-list"></i><span class="text"> <?= lang('brands'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_variants">
                                            <a href="<?= admin_url('system_settings/variants') ?>">
                                                <i class="fa fa-tags"></i><span class="text"> <?= lang('variants'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_tax_rates">
                                            <a href="<?= admin_url('system_settings/tax_rates') ?>">
                                                <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('tax_rates'); ?></span>
                                            </a>
                                        </li>

                                        <li id="system_settings_email_templates">
                                            <a href="<?= admin_url('system_settings/email_templates') ?>">
                                                <i class="fa fa-envelope"></i><span class="text"> <?= lang('email_templates'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_user_groups">
                                            <a href="<?= admin_url('system_settings/user_groups') ?>">
                                                <i class="fa fa-key"></i><span class="text"> <?= lang('group_permissions'); ?></span>
                                            </a>
                                        </li>
                                        <li id="system_settings_backups">
                                            <a href="<?= admin_url('system_settings/backups') ?>">
                                                <i class="fa fa-database"></i><span class="text"> <?= lang('backups'); ?></span>
                                            </a>
                                        </li>

                                    </ul>
                                </li>
                            <?php
                            } ?>
                            <li class="mm_reports">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="text"> <?= lang('reports'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="reports_index">
                                        <a href="<?= admin_url('reports') ?>">
                                            <i class="fa fa-bars"></i><span class="text"> <?= lang('overview_chart'); ?></span>
                                        </a>
                                    </li>

                                    <?php if (POS) {
                                ?>
                                    <li id="reports_register">
                                        <a href="<?= admin_url('reports/register') ?>">
                                            <i class="fa fa-th-large"></i><span class="text"> <?= lang('register_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <li id="reports_categories">
                                        <a href="<?= admin_url('reports/categories') ?>">
                                            <i class="fa fa-folder-open"></i><span class="text"> <?= lang('categories_report'); ?></span>
                                        </a>
                                    </li>
                                    <li id="reports_brands">
                                        <a href="<?= admin_url('reports/brands') ?>">
                                            <i class="fa fa-cubes"></i><span class="text"> <?= lang('brands_report'); ?></span>
                                        </a>
                                    </li>
                                    <li id="reports_payments">
                                        <a href="<?= admin_url('reports/payments') ?>">
                                            <i class="fa fa-money"></i><span class="text"> <?= lang('payments_report'); ?></span>
                                        </a>
                                    </li>
                                    <li id="reports_tax">
                                        <a href="<?= admin_url('reports/tax') ?>">
                                            <i class="fa fa-area-chart"></i><span class="text"> <?= lang('tax_report'); ?></span>
                                        </a>
                                    </li>
                                    <li id="reports_profit_loss">
                                        <a href="<?= admin_url('reports/profit_loss') ?>">
                                            <i class="fa fa-money"></i><span class="text"> <?= lang('profit_and_loss'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php if ($Owner && file_exists(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR . 'Shop.php')) {
                                ?>
                            <li class="mm_shop_settings mm_api_settings">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-shopping-cart"></i><span class="text"> <?= lang('front_end'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="shop_settings_index">
                                        <a href="<?= admin_url('shop_settings') ?>">
                                            <i class="fa fa-cog"></i><span class="text"> <?= lang('shop_settings'); ?></span>
                                        </a>
                                    </li>
                                    <li id="shop_settings_slider">
                                        <a href="<?= admin_url('shop_settings/slider') ?>">
                                            <i class="fa fa-file"></i><span class="text"> <?= lang('slider_settings'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($Settings->apis) {
                                    ?>
                                    <li id="api_settings_index">
                                        <a href="<?= admin_url('api_settings') ?>">
                                            <i class="fa fa-key"></i><span class="text"> <?= lang('api_keys'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                                } ?>
                                    <li id="shop_settings_pages">
                                        <a href="<?= admin_url('shop_settings/pages') ?>">
                                            <i class="fa fa-file"></i><span class="text"> <?= lang('list_pages'); ?></span>
                                        </a>
                                    </li>
                                    <li id="shop_settings_pages">
                                        <a href="<?= admin_url('shop_settings/add_page') ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_page'); ?></span>
                                        </a>
                                    </li>
                                    <li id="shop_settings_sms_settings">
                                        <a href="<?= admin_url('shop_settings/sms_settings') ?>">
                                            <i class="fa fa-cogs"></i><span class="text"> <?= lang('sms_settings'); ?></span>
                                        </a>
                                    </li>
                                    <li id="shop_settings_send_sms">
                                        <a href="<?= admin_url('shop_settings/send_sms') ?>">
                                            <i class="fa fa-send"></i><span class="text"> <?= lang('send_sms'); ?></span>
                                        </a>
                                    </li>
                                    <li id="shop_settings_sms_log">
                                        <a href="<?= admin_url('shop_settings/sms_log') ?>">
                                            <i class="fa fa-file-text-o"></i><span class="text"> <?= lang('sms_log'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php
                            } ?>

                        <?php
                        } else { // not owner and not admin ?>
                            <?php if ($GP['products-index'] || $GP['products-add'] || $GP['products-barcode'] || $GP['products-adjustments'] || $GP['products-stock_count']) {
                            ?>
                            <li class="mm_products">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-barcode"></i>
                                    <span class="text"> <?= lang('products'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="products_index">
                                        <a class="submenu" href="<?= admin_url('products'); ?>">
                                            <i class="fa fa-barcode"></i><span class="text"> <?= lang('list_products'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($GP['products-add']) {
                                ?>
                                    <li id="products_add">
                                        <a class="submenu" href="<?= admin_url('products/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_product'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <?php if ($GP['products-barcode']) {
                                ?>
                                    <li id="products_sheet">
                                        <a class="submenu" href="<?= admin_url('products/print_barcodes'); ?>">
                                            <i class="fa fa-tags"></i><span class="text"> <?= lang('print_barcode_label'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <?php if ($GP['products-adjustments']) {
                                ?>
                                    <li id="products_quantity_adjustments">
                                        <a class="submenu" href="<?= admin_url('products/quantity_adjustments'); ?>">
                                            <i class="fa fa-filter"></i><span class="text"> <?= lang('quantity_adjustments'); ?></span>
                                        </a>
                                    </li>
                                    <li id="products_add_adjustment">
                                        <a class="submenu" href="<?= admin_url('products/add_adjustment'); ?>">
                                            <i class="fa fa-filter"></i>
                                            <span class="text"> <?= lang('add_adjustment'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <?php if ($GP['products-stock_count']) {
                                ?>
                                    <li id="products_stock_counts">
                                        <a class="submenu" href="<?= admin_url('products/stock_counts'); ?>">
                                            <i class="fa fa-list-ol"></i>
                                            <span class="text"> <?= lang('stock_counts'); ?></span>
                                        </a>
                                    </li>
                                    <li id="products_count_stock">
                                        <a class="submenu" href="<?= admin_url('products/count_stock'); ?>">
                                            <i class="fa fa-plus-circle"></i>
                                            <span class="text"> <?= lang('count_stock'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                            <?php if ($GP['sales-index'] || $GP['sales-add'] || $GP['sales-deliveries'] || $GP['sales-gift_cards']) {
                            ?>
                            <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'sales' ? 'mm_pos' : '' ?>">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-heart"></i>
                                    <span class="text"> <?= lang('sales'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="sales_index">
                                        <a class="submenu" href="<?= admin_url('sales'); ?>">
                                            <i class="fa fa-heart"></i><span class="text"> <?= lang('list_sales'); ?></span>
                                        </a>
                                    </li>
                                    <?php if (POS && $GP['pos-index']) {
                                ?>
                                    <li id="pos_sales">
                                        <a class="submenu" href="<?= admin_url('pos/sales'); ?>">
                                            <i class="fa fa-heart"></i><span class="text"> <?= lang('pos_sales'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <?php if ($GP['sales-add']) {
                                ?>
                                    <li id="sales_add">
                                        <a class="submenu" href="<?= admin_url('sales/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_sale'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['sales-deliveries']) {
                                ?>
                                    <li id="sales_deliveries">
                                        <a class="submenu" href="<?= admin_url('sales/deliveries'); ?>">
                                            <i class="fa fa-truck"></i><span class="text"> <?= lang('deliveries'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['sales-gift_cards']) {
                                ?>
                                    <li id="sales_gift_cards">
                                        <a class="submenu" href="<?= admin_url('sales/gift_cards'); ?>">
                                            <i class="fa fa-gift"></i><span class="text"> <?= lang('gift_cards'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                            <?php if ($GP['quotes-index'] || $GP['quotes-add']) {
                            ?>
                            <li class="mm_quotes">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="text"> <?= lang('quotes'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="sales_index">
                                        <a class="submenu" href="<?= admin_url('sales/quotes_index'); ?>">
                                            <i class="fa fa-heart-o"></i><span class="text"> <?= lang('list_quotes'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($GP['quotes-add']) {
                                ?>
                                    <li id="sales_add">
                                        <a class="submenu" href="<?= admin_url('quotes/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_quote'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                            <?php if ($GP['purchases-index'] || $GP['purchases-add'] || $GP['purchases-expenses']) {
                            ?>
                            <li class="mm_purchases">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="text"> <?= lang('purchases'); ?>
                                    </span> <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="purchases_index">
                                        <a class="submenu" href="<?= admin_url('purchases'); ?>">
                                            <i class="fa fa-star"></i><span class="text"> <?= lang('list_purchases'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($GP['purchases-add']) {
                                ?>
                                    <li id="purchases_add">
                                        <a class="submenu" href="<?= admin_url('purchases/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_purchase'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                    <?php if ($GP['purchases-expenses']) {
                                ?>
                                    <li id="purchases_expenses">
                                        <a class="submenu" href="<?= admin_url('purchases/expenses'); ?>">
                                            <i class="fa fa-dollar"></i><span class="text"> <?= lang('list_expenses'); ?></span>
                                        </a>
                                    </li>
                                    <li id="purchases_add_expense">
                                        <a class="submenu" href="<?= admin_url('purchases/add_expense'); ?>"
                                            data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_expense'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                            <?php if ($GP['transfers-index'] || $GP['transfers-add']) {
                            ?>
                            <li class="mm_transfers">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-star-o"></i>
                                    <span class="text"> <?= lang('transfers'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="transfers_index">
                                        <a class="submenu" href="<?= admin_url('transfers'); ?>">
                                            <i class="fa fa-star-o"></i><span class="text"> <?= lang('list_transfers'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($GP['transfers-add']) {
                                ?>
                                    <li id="transfers_add">
                                        <a class="submenu" href="<?= admin_url('transfers/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_transfer'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                            <?php if ($GP['returns-index'] || $GP['returns-add']) {
                            ?>
                            <li class="mm_returns">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-random"></i>
                                    <span class="text"> <?= lang('returns'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <li id="returns_index">
                                        <a class="submenu" href="<?= admin_url('returns'); ?>">
                                            <i class="fa fa-random"></i><span class="text"> <?= lang('list_returns'); ?></span>
                                        </a>
                                    </li>
                                    <?php if ($GP['returns-add']) {
                                ?>
                                    <li id="returns_add">
                                        <a class="submenu" href="<?= admin_url('returns/add'); ?>">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_return'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                            <?php if ($GP['customers-index'] || $GP['customers-add'] || $GP['suppliers-index'] || $GP['suppliers-add']) {
                            ?>
                            <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-users"></i>
                                    <span class="text"> <?= lang('people'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($GP['customers-index']) {
                                ?>
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers'); ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('list_customers'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['customers-add']) {
                                ?>
                                    <li id="customers_index">
                                        <a class="submenu" href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_customer'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['suppliers-index']) {
                                ?>
                                    <li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers'); ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('list_suppliers'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['suppliers-add']) {
                                ?>
                                    <li id="suppliers_index">
                                        <a class="submenu" href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-plus-circle"></i><span class="text"> <?= lang('add_supplier'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                            <?php if ($GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts'] || $GP['reports-products'] || $GP['reports-monthly_sales'] || $GP['reports-sales'] || $GP['reports-payments'] || $GP['reports-purchases'] || $GP['reports-customers'] || $GP['reports-suppliers'] || $GP['reports-expenses']) {
                            ?>
                            <li class="mm_reports">
                                <a class="dropmenu" href="#">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <span class="text"> <?= lang('reports'); ?> </span>
                                    <span class="chevron closed"></span>
                                </a>
                                <ul>
                                    <?php if ($GP['reports-quantity_alerts']) {
                                ?>
                                    <li id="reports_quantity_alerts">
                                        <a href="<?= admin_url('reports/quantity_alerts') ?>">
                                            <i class="fa fa-bar-chart-o"></i><span class="text"> <?= lang('product_quantity_alerts'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-expiry_alerts']) {
                                ?>
                                    <?php if ($Settings->product_expiry) {
                                    ?>
                                    <li id="reports_expiry_alerts">
                                        <a href="<?= admin_url('reports/expiry_alerts') ?>">
                                            <i class="fa fa-bar-chart-o"></i><span class="text"> <?= lang('product_expiry_alerts'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                                } ?>
                                    <?php
                            }
                            if ($GP['reports-products']) {
                                ?>
                                    <li id="reports_products">
                                        <a href="<?= admin_url('reports/products') ?>">
                                            <i class="fa fa-filter"></i><span class="text"> <?= lang('products_report'); ?></span>
                                        </a>
                                    </li>
                                    <li id="reports_adjustments">
                                        <a href="<?= admin_url('reports/adjustments') ?>">
                                            <i class="fa fa-barcode"></i><span class="text"> <?= lang('adjustments_report'); ?></span>
                                        </a>
                                    </li>
                                    <li id="reports_categories">
                                        <a href="<?= admin_url('reports/categories') ?>">
                                            <i class="fa fa-folder-open"></i><span class="text"> <?= lang('categories_report'); ?></span>
                                        </a>
                                    </li>
                                    <li id="reports_brands">
                                        <a href="<?= admin_url('reports/brands') ?>">
                                            <i class="fa fa-cubes"></i><span class="text"> <?= lang('brands_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-daily_sales']) {
                                ?>
                                    <li id="reports_daily_sales">
                                        <a href="<?= admin_url('reports/daily_sales') ?>">
                                            <i class="fa fa-calendar-o"></i><span class="text"> <?= lang('daily_sales'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-monthly_sales']) {
                                ?>
                                    <li id="reports_monthly_sales">
                                        <a href="<?= admin_url('reports/monthly_sales') ?>">
                                            <i class="fa fa-calendar-o"></i><span class="text"> <?= lang('monthly_sales'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-sales']) {
                                ?>
                                    <li id="reports_sales">
                                        <a href="<?= admin_url('reports/sales') ?>">
                                            <i class="fa fa-heart"></i><span class="text"> <?= lang('sales_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-payments']) {
                                ?>
                                    <li id="reports_payments">
                                        <a href="<?= admin_url('reports/payments') ?>">
                                            <i class="fa fa-money"></i><span class="text"> <?= lang('payments_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-tax']) {
                                ?>
                                    <li id="reports_tax">
                                        <a href="<?= admin_url('reports/tax') ?>">
                                            <i class="fa fa-area-chart"></i><span class="text"> <?= lang('tax_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-daily_purchases']) {
                                ?>
                                    <li id="reports_daily_purchases">
                                        <a href="<?= admin_url('reports/daily_purchases') ?>">
                                            <i class="fa fa-calendar-o"></i><span class="text"> <?= lang('daily_purchases'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-monthly_purchases']) {
                                ?>
                                    <li id="reports_monthly_purchases">
                                        <a href="<?= admin_url('reports/monthly_purchases') ?>">
                                            <i class="fa fa-calendar-o"></i><span class="text"> <?= lang('monthly_purchases'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-purchases']) {
                                ?>
                                    <li id="reports_purchases">
                                        <a href="<?= admin_url('reports/purchases') ?>">
                                            <i class="fa fa-star"></i><span class="text"> <?= lang('purchases_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-expenses']) {
                                ?>
                                    <li id="reports_expenses">
                                        <a href="<?= admin_url('reports/expenses') ?>">
                                            <i class="fa fa-star"></i><span class="text"> <?= lang('expenses_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-customers']) {
                                ?>
                                    <li id="reports_customer_report">
                                        <a href="<?= admin_url('reports/customers') ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('customers_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            }
                            if ($GP['reports-suppliers']) {
                                ?>
                                    <li id="reports_supplier_report">
                                        <a href="<?= admin_url('reports/suppliers') ?>">
                                            <i class="fa fa-users"></i><span class="text"> <?= lang('suppliers_report'); ?></span>
                                        </a>
                                    </li>
                                    <?php
                            } ?>
                                </ul>
                            </li>
                            <?php
                        } ?>

                        <?php
                        } ?>
                    </ul>
                </div>
                <a href="#" id="main-menu-act" class="full visible-md visible-lg">
                    <i class="fa fa-angle-double-left"></i>
                </a>
            </div>
            </td><td class="content-con">
            <div id="content">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <ul class="breadcrumb">
                            <?php
                            foreach ($bc as $b) {
                                if ($b['link'] === '#') {
                                    echo '<li class="active">' . $b['page'] . '</li>';
                                } else {
                                    echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                                }
                            }
                            ?>
                            <li class="right_log hidden-xs">
                                <?= lang('your_ip') . ' ' . $ip_address . " <span class='hidden-sm'>( " . lang('last_login_at') . ': ' . date($dateFormats['php_ldate'], $this->session->userdata('old_last_login')) . ' ' . ($this->session->userdata('last_ip') != $ip_address ? lang('ip:') . ' ' . $this->session->userdata('last_ip') : '') . ' )</span>' ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($message) {
                                ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $message; ?>
                            </div>
                        <?php
                            } ?>
                        <?php if ($error) {
                                ?>
                            <div class="alert alert-danger">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $error; ?>
                            </div>
                        <?php
                            } ?>
                        <?php if ($warning) {
                                ?>
                            <div class="alert alert-warning">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $warning; ?>
                            </div>
                        <?php
                            } ?>
                        <?php
                        if ($info) {
                            foreach ($info as $n) {
                                if (!$this->session->userdata('hidden' . $n->id)) {
                                    ?>
                                    <div class="alert alert-info">
                                        <a href="#" id="<?= $n->id ?>" class="close hideComment external"
                                           data-dismiss="alert">&times;</a>
                                        <?= $n->comment; ?>
                                    </div>
                                <?php
                                }
                            }
                        } ?>
                        <div class="alerts-con"></div>-->






