<?php defined('BASEPATH') or exit('No direct script access allowed');
$bgs = glob(VIEWPATH . 'default/admin/assets/images/login-bgs/*.jpg');
foreach ($bgs as &$bg) {
    $af = explode('assets/', $bg);
    $bg = $assets . $af[1];
}
// $this->sma->print_arrays($bgs);
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?=admin_url()?>'; }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>

    <link href="<?= $assets ?>styles/helpers/login.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            min-width: 350px;
        }
        .bblue {
            background: #fff !important;
        }
        .ncolor{
            margin: 16px;
            border-radius: 6px;
            padding: 32px 16px;
            border: 1px solid rgba(0, 0, 0, 0.2);
            background:#03178C;
        }


        .section_login{
            width:30%;
            height:500px;
        }
        @media (min-width: 767px) {
            .desktop-hiddin{
                display:none !important;
            }
        }
        @media (max-width: 767px)  {
            .mobile-hidden{
                display:none !important;
            }
            .section_login{
            width:30%;
            height:400px;
        }

        .ncolor{
            width:90% !important;
            height:500px !important;
        }

        }
        .text-white{
            color:#fff;
        }
        .text-white> a:hover{
            color:red;
        }
        .padd30{
        padding: 30px;
        }
        .padd70{
            padding-top: 70px;
        }
        .login-page .page-back {
            display: flex;
            align-items: center;
          // flex-direction: column;
            justify-content: center;
            background-size: cover !important;
            background-position: center !important;
			background-image: url("<?= $assets ?>/images/login-bgs/bgoffline.png") !important;
            /*background-image: url("<?= $bgs[mt_rand(0, count($bgs) - 1)] ?>") !important;*/


        }
        .contents {
            margin: 16px;
            border-radius: 6px;
            padding: 32px 16px;
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.2);
        }
        .login-content, .login-page .login-form-links {
            margin-top: 20px;
            border-radius: 6px;
        }


		.floating-wpp {
    position: fixed;
    bottom: 20px;
    left: 10px;
    font-size: 14px;
    transition: bottom .2s;
}

.floating-wpp .floating-wpp-button img, .floating-wpp .floating-wpp-button svg {
    position: absolute;
    width: 100%;
    height: auto;
    object-fit: cover;
    top: 50%;
    left: 50%;
    transform: translate3d(-50%,-50%,0);
	cursor: pointer;
	    border-radius: 50%;
}

.floating-wpp .floating-wpp-button {
    position: relative;
    border-radius: 50%;
    box-shadow: 1px 1px 4px rgb(60 60 60 / 40%);
    transition: box-shadow .2s;
    cursor: pointer;
    overflow: hidden;
}

.login-wrapper{
    display:flex;
    min-height:100vh;
}

#login1{
    width:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px;
    justify-content: space-evenly;
    align-content: flex-start;
    flex-wrap: nowrap;
    flex-direction: column;
}

.section_login{
    width:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px;
}

/* الفورم */
.login-form-div{
    width:100%;
    max-width:450px;
}

/* الحقول */
.login-form-div .form-control{
    height:46px;
}

.form-action{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:15px;
}

/* ===========================
   Responsive
=========================== */

@media (max-width:991px){

    .login-wrapper{
        flex-direction:column;
    }

    #login1,
    .section_login{
        width:100%;
        min-height:auto;
        padding:30px 20px;
    }

    .login-form-div{
        max-width:500px;
        margin:auto;
    }

    .form-action{
        flex-direction:column;
        gap:15px;
    }

    .form-action .checkbox,
    .form-action button{
        width:100%;
    }

    .form-action button{
        margin:0;
    }

    .checkbox{
        text-align:center;
    }

    .section_login h1{
        font-size:30px;
    }

    .section_login h3{
        font-size:18px;
    }

}

/* موبايل */

@media (max-width:576px){

    .login-content{
        padding:20px;
    }

    .div-title h3{
        font-size:18px;
    }

    .section_login{
        padding:20px 15px;
    }

    .section_login h1{
        font-size:24px;
        padding:10px 0;
    }

    .section_login h3{
        font-size:16px;
        line-height:1.7;
        padding:10px 0;
    }

    .input-group-addon{
        width:45px;
    }

    .form-control{
        font-size:14px;
    }

    button.btn{
        width:100%;
    }

}

.register-btn{
    display:inline-block;
    margin-top:12px;
    padding:12px 28px;
    background:#fff;
    color:#03178C;
    border:2px solid #fff;
    border-radius:50px;
    font-size:16px;
    font-weight:600;
    text-decoration:none;
    transition:all .3s ease;
    box-shadow:0 5px 15px rgba(0,0,0,.15);
}

.register-btn i{
    margin-right:8px;
}

.register-btn:hover{
    background:#03178C;
    border-color:#03178C;
    color:#fff;
    text-decoration:none;
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(0,0,0,.25);
}
    </style>

<!--Floating WhatsApp css-->
<!--Floating WhatsApp javascript-->
<script type="text/javascript" src="https://rawcdn.githack.com/rafaelbotazini/floating-whatsapp/3d18b26d5c7d430a1ab0b664f8ca6b69014aed68/floating-wpp.min.js"></script>


</head>

<body class="login-page">
<div id="WAButton" style="z-index:100;"></div>
    <noscript>
        <div class="global-site-notice noscript">
            <div class="notice-inner">
                <p>
                    <strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                    your browser to utilize the functionality of this website.
                </p>
            </div>
        </div>
    </noscript>
    <div class="page-back login-wrapper">
        <div class="contents"  id="login11">
            <div class="text-center">
                <?php if ($Settings->logo2) {
                    echo '<img src="' . base_url('assets/uploads/logos/' . $Settings->logo2) . '" alt="' . $Settings->site_name . '" style="margin-bottom:10px;max-width:100%;height:auto;" />';
                } ?>
            </div>

            <div id="login">
                <div class="container">

                    <div class="login-form-div">
                        <div class="login-content">
                            <?php if ($Settings->mmode) {
                                ?>
                                <div class="alert alert-warning">
                                    <button data-dismiss="alert" class="close" type="button">×</button>
                                    <?= lang('site_offline') ?>
                                </div>
                                <?php
                            }
                            if ($error) {
                                ?>
                                <div class="alert alert-danger">
                                    <button data-dismiss="alert" class="close" type="button">×</button>
                                    <ul class="list-group"><?= $error; ?></ul>
                                </div>
                                <?php
                            }
                            if ($message) {
                                ?>
                                <div class="alert alert-success">
                                    <button data-dismiss="alert" class="close" type="button">×</button>
                                    <ul class="list-group"><?= $message; ?></ul>
                                </div>
                                <?php
                            }
                            ?>
                            <?php echo admin_form_open('auth/login', 'class="login" data-toggle="validator"'); ?>



                            <div class="div-title col-sm-12">
                                <h3 class="text-primary text-center"><?= lang('login_to_your_account') ?></h3>
                            </div>
                            <div class="col-sm-12">
								<div class="textbox-wrap form-group">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-user"></i></span>
										<input type="text" value="" required="required" class="form-control" name="company"
										placeholder="<?= lang('company') ?>"  />
									</div>
								</div>

                                <div class="textbox-wrap form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" value="<?= DEMO ? 'admin' : ''; ?>" required="required" class="form-control" name="identity"
                                        placeholder="<?= lang('username') ?>" <?= DEMO ? 'readonly' : ''; ?>/>
                                    </div>
                                </div>
                                <div class="textbox-wrap form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                        <input type="password" value="" required="required" class="form-control " name="password"
                                        placeholder="<?= lang('pw') ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-action col-sm-12">
    <div class="checkbox pull-left">
        <div class="custom-checkbox">
            <?php echo form_checkbox('remember', '1', false, 'id="remember"'); ?>
        </div>
        <span class="checkbox-text pull-left">
            <label for="remember"><?= lang('remember_me') ?></label>
        </span>
    </div>

    <button type="submit" class="btn btn-success pull-right">
        <?= lang('login') ?> &nbsp;
        <i class="fa fa-sign-in"></i>
    </button>
</div>

<div class="text-center visible-xs visible-sm" style="margin-top:20px;">
    <a href="#register-section" class="mobile-register-link">
        <i class="fa fa-user-plus"></i>
        <?= lang('ليس لديك حساب؟ سجل الآن') ?>
    </a>
</div>
                            <?php echo form_close(); ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section_login ncolor " id="register-section">

            <div class=" link1">
                <h1 class="text-white text-center ">Login</h1>
                <h1 class="text-white text-center padd30">تسجيل الدخول</h1>
            <div class="text-center ">
    <h3 class="text-white">
        <?= lang('No Account?') ?>
    </h3>

    <a href="https://domain2.net/demo/register" class="register-btn">
        <i class="fa fa-user-plus"></i>
        <?= lang('Signup Now') ?>
    </a>
</div>

<div class="text-center padd30">
    <h3 class="text-white">
        <?= lang('ليس لديك حساب؟') ?>
    </h3>

    <a href="https://domain2.net/demo/register" class="register-btn">
        <i class="fa fa-user-plus"></i>
        <?= lang('سجل الآن') ?>
    </a>
</div>
</div>

        </div>

    </div>
	<!--
	<script src="<?= $assets ?>js/jquery.js"></script>
    -->
    <script src="<?= $assets ?>js/bootstrap.min.js"></script>
    <script src="<?= $assets ?>js/jquery.cookie.js"></script>
    <script src="<?= $assets ?>js/login.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            localStorage.clear();
            var hash = window.location.hash;
            if (hash && hash != '') {
                $("#login").hide();
                $(hash).show();
            }
        });
    </script>

	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5fac328a0a68960861be1fe5/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->


	<script type="text/javascript">
$(function() {

  $('#WAButton').floatingWhatsApp({
    phone: '966534277747', //WhatsApp Business phone number International format-
    //Get it with Toky at https://toky.co/en/features/whatsapp.
    headerTitle: 'Chat with us on WhatsApp!', //Popup Title
    popupMessage: 'Hello, how can we help you?', //Popup Message
    showPopup: false, //Enables popup display
    buttonImage: '<img src="<?= $assets ?>images/whatsapp.svg"  />', //Button Image
    size: '60px',
	//headerColor: 'crimson', //Custom header color
    //backgroundColor: 'crimson', //Custom background button color
    position: "left"
  });
});

</script>




</body>
</html>
