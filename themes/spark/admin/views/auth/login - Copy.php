<?php defined('BASEPATH') or exit('No direct script access allowed');
$bgs = glob(VIEWPATH . 'default/admin/assets/images/login-bgs/*.jpg');
foreach ($bgs as &$bg) {
    $af = explode('assets/', $bg);
    $bg = $assets . $af[1];
}
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?=admin_url()?>'; }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="<?= $assets ?>styles/theme.css?1" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>

    <link href="<?= $assets ?>styles/helpers/login.css?1" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <style>
        body {
            min-width: 350px;
        }
        .bblue {
            background: #fff !important;
        }
        .login-page .page-back {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            background-size: cover !important;
            background-position: center !important;
			background-image: url("<?= $assets ?>/images/login-bgs/bgoffline.png") !important;

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
    <div class="page-back">
        <div class="contents">
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


                            <?php
                            if ( isset( $_GET['code']  ) ) {

                                $redirectURI="https://domain1.com/admin/login";
                               $clientId="99e315d9-9edc-48cd-9359-d9466b917439";
                               $clientSecret="mP04tZCQodrPFnOiDLvdB2jT3s8FGaYUGvimncIF";
                               $code=$_GET['code'];






                               // Use cURL to get access token and refresh token
                               $data = array(
                                   'redirect_uri' => $redirectURI,
                                   'grant_type'   => 'authorization_code',
                                   'code'         => $code
                               );
                               $ch            = curl_init();
                               curl_setopt( $ch, CURLOPT_URL, 'https://api.foodics.com/oauth/token' );
                               curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                               curl_setopt( $ch, CURLOPT_POST, 1 );
                               curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
                               curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $clientId . ':' . $clientSecret ) ) );


                               $result = json_decode( curl_exec( $ch ) );

                               curl_close( $ch );

                               if ( isset( $result->access_token ) ) {
                                  ?>
                                <input type="hidden" value="<?= $result->access_token; ?>"  name="foodics_access_token"/>

                                  <?php
                               }
                           }
                            ?>


                            <div class="div-title col-sm-12">
                                <h3 class="text-primary"><?= lang('login_to_your_account') ?></h3>
                            </div>
                            <div class="col-sm-12">
								<div class="textbox-wrap form-group">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-user"></i></span>
										<input type="text" value="<?= DEMO ? 'demo' : ''; ?>" required="required" class="form-control" name="company"
										placeholder="<?= lang('company') ?>" <?= DEMO ? 'readonly' : ''; ?> />
									</div>
								</div>

                                <div class="textbox-wrap form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" value="<?= DEMO ? 'demo' : ''; ?>" required="required" class="form-control" name="identity"
                                        placeholder="<?= lang('username') ?>" <?= DEMO ? 'readonly' : ''; ?>/>
                                    </div>
                                </div>
                                <div class="textbox-wrap form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                        <input type="password" value="<?= DEMO ? 'Anyone123' : ''; ?>" required="required" class="form-control " name="password"
                                        placeholder="<?= lang('pw') ?>" <?= DEMO ? 'readonly' : ''; ?>/>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ($Settings->captcha) {
                                ?>
                                <div class="col-sm-12">
                                    <div class="textbox-wrap form-group">
                                        <div class="row">
                                            <div class="col-sm-6 div-captcha-left">
                                                <span class="captcha-image"><?php echo $image; ?></span>
                                            </div>
                                            <div class="col-sm-6 div-captcha-right">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <a href="<?= admin_url('auth/reload_captcha'); ?>" class="reload-captcha">
                                                            <i class="fa fa-refresh"></i>
                                                        </a>
                                                    </span>
                                                    <?php echo form_input($captcha); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } /* echo $recaptcha_html; */
                            ?>

                            <div class="form-action col-sm-12">
                                <div class="checkbox pull-left">
                                    <div class="custom-checkbox">
                                        <?php echo form_checkbox('remember', '1', false, 'id="remember"'); ?>
                                    </div>
                                    <span class="checkbox-text pull-left"><label for="remember"><?= lang('remember_me') ?></label></span>
                                </div>
                                <button type="submit" class="btn btn-success pull-right"><?= lang('login') ?> &nbsp; <i class="fa fa-sign-in"></i></button>
                            </div>
                            <?php echo form_close(); ?>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>



        </div>
    </div>
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

<?php
if ( isset( $_GET['code']  ) ) {
     //getToken();
    //getAccessToken("https://domain1.com/admin/login","99e315d9-9edc-48cd-9359-d9466b917439","mP04tZCQodrPFnOiDLvdB2jT3s8FGaYUGvimncIF",$_GET['code']);


}




  function getAccessToken($redirectURI, $clientId, $clientSecret, $code) {
    // Use cURL to get access token and refresh token
    $data = array(
        'redirect_uri' => $redirectURI,
        'grant_type'   => 'authorization_code',
        'code'         => $code
    );
    $ch            = curl_init();
    curl_setopt( $ch, CURLOPT_URL, 'https://api.foodics.com/oauth/token' );
    //curl_setopt( $ch, CURLOPT_URL, 'https://api-sandbox.foodics.com/oauth/token' );

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $clientId . ':' . $clientSecret ) ) );


    $result = json_decode( curl_exec( $ch ) );
//print_r($result->access_token);

    curl_close( $ch );

    if ( isset( $result->access_token ) ) {

    }
}


?>




</body>
</html>
