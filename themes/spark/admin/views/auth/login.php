<?php defined('BASEPATH') or exit('No direct script access allowed');

// Consume Centrally Resolved Brand (MY_Controller)
$brand = $brand ?? (isset($this->brand) ? $this->brand : ($this->session->userdata('spark_brand') ?? 'cpe'));
if (!in_array($brand, ['cpe', 'quroosh'], true)) {
    $brand = 'cpe';
}

$bgs = glob(VIEWPATH . 'default/admin/assets/images/login-bgs/*.jpg');
foreach ($bgs as &$bg) {
    $af = explode('assets/', $bg);
    $bg = $assets . $af[1];
}

if (isset($_GET['code'])) {
    $redirectURI = "https://domain1.com/admin/login";
    $clientId = "99e315d9-9edc-48cd-9359-d9466b917439";
    $clientSecret = "mP04tZCQodrPFnOiDLvdB2jT3s8FGaYUGvimncIF";
    $code = $_GET['code'];

    $data = array(
        'redirect_uri' => $redirectURI,
        'grant_type'   => 'authorization_code',
        'code'         => $code
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.foodics.com/oauth/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret)));

    $result = json_decode(curl_exec($ch));
    curl_close($ch);
}
?><!DOCTYPE html>
<html lang="<?= (isset($Settings->user_language) && $Settings->user_language == 'english') ? 'en' : 'ar' ?>" dir="<?= (isset($Settings->user_language) && $Settings->user_language == 'english') ? 'ltr' : 'rtl' ?>" data-theme="light" data-brand="<?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="utf-8">
    <script type="text/javascript">
        (function() {
            var savedTheme = localStorage.getItem('spark-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <title><?= $title ?></title>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?=admin_url()?>'; }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/<?= ($brand === 'quroosh' && file_exists(FCPATH . 'themes/default/admin/assets/images/iconq.png')) ? 'iconq.png' : 'icon.png' ?>"/>

    <!-- Stylesheets -->
    <link href="<?= $assets ?>styles/theme.css?1" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/spark/spark.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/spark/components/_login.css" rel="stylesheet"/>

    <style>
        /* Floating WhatsApp Styles */
        .floating-wpp {
            position: fixed;
            bottom: 24px;
            left: 24px;
            font-size: 14px;
            transition: bottom .2s;
            z-index: 9999;
        }
        .floating-wpp .floating-wpp-button {
            position: relative;
            border-radius: 50%;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            overflow: hidden;
            width: 60px;
            height: 60px;
            background-color: #25d366;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .floating-wpp .floating-wpp-button:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.45);
        }
        .floating-wpp .floating-wpp-button img, 
        .floating-wpp .floating-wpp-button svg {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            top: 50%;
            left: 50%;
            transform: translate3d(-50%, -50%, 0);
            cursor: pointer;
            border-radius: 50%;
        }
        .floating-wpp .floating-wpp-popup {
            border-radius: 12px;
            background-color: #e5ddd5;
            position: absolute;
            overflow: hidden;
            padding: 0;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            bottom: 75px;
            left: 0;
            width: 0;
            height: 0;
            opacity: 0;
            transition: opacity .3s, width .3s, height .3s;
            z-index: 10000;
        }
        .floating-wpp .floating-wpp-popup.active {
            padding: 0 0 12px 0;
            width: 300px;
            height: auto;
            opacity: 1;
        }
        .floating-wpp .floating-wpp-head {
            text-align: right;
            color: #fff;
            background-color: #075e54;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }
        .floating-wpp .floating-wpp-message {
            background-color: #fff;
            padding: 10px 14px;
            margin: 14px 14px 10px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            font-size: 13px;
            color: #333;
            line-height: 1.5;
            direction: rtl;
            text-align: right;
        }
        [dir="ltr"] .floating-wpp .floating-wpp-message,
        [dir="ltr"] .floating-wpp .floating-wpp-head {
            direction: ltr;
            text-align: left;
        }
        .floating-wpp .floating-wpp-input-message {
            background-color: #fff;
            margin: 10px 14px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            padding: 8px 12px;
            display: flex;
            align-items: center;
        }
        .floating-wpp .floating-wpp-input-message textarea {
            border: none;
            outline: none;
            width: 100%;
            resize: none;
            font-family: inherit;
            font-size: 13px;
            background: transparent;
        }
        .floating-wpp .floating-wpp-btn-send {
            margin-inline-start: 8px;
            cursor: pointer;
            color: #075e54;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/spark-alerts.js?v=<?= time() ?>"></script>
    <!-- Floating WhatsApp javascript -->
    <script type="text/javascript" src="https://rawcdn.githack.com/rafaelbotazini/floating-whatsapp/3d18b26d5c7d430a1ab0b664f8ca6b69014aed68/floating-wpp.min.js"></script>
</head>

<body class="spark-login-page">
    <noscript>
        <div class="global-site-notice noscript">
            <div class="notice-inner">
                <p>
                    <strong><?= lang('javascript_disabled_heading'); ?></strong><br><?= lang('javascript_disabled_message'); ?>
                </p>
            </div>
        </div>
    </noscript>

    <?php 
    $is_arabic = (!isset($Settings->user_language) || $Settings->user_language == 'arabic');
    ?>
    <div class="spark-login-wrapper">
        <!-- Top Utilities (Theme & Language) -->
        <div class="spark-login-top-bar">
            <?php if ($is_arabic) { ?>
                <a href="<?= admin_url('auth/language/english'); ?>" class="spark-login-util-btn" id="langToggleBtn" title="<?= lang('switch_to_english'); ?>">
                    <img src="<?= base_url('assets/images/english.png'); ?>" alt="English" style="width: 18px; height: 12px; object-fit: cover; border-radius: 2px;">
                    <span><?= lang('switch_to_english'); ?></span>
                </a>
            <?php } else { ?>
                <a href="<?= admin_url('auth/language/arabic'); ?>" class="spark-login-util-btn" id="langToggleBtn" title="<?= lang('switch_to_arabic'); ?>">
                    <img src="<?= base_url('assets/images/arabic.png'); ?>" alt="Arabic" style="width: 18px; height: 12px; object-fit: cover; border-radius: 2px;">
                    <span><?= lang('switch_to_arabic'); ?></span>
                </a>
            <?php } ?>
            <button type="button" class="spark-login-util-btn" id="themeToggleBtn" title="<?= lang('toggle_theme'); ?>">
                <i class="fa fa-moon-o" id="themeIcon"></i>
                <span id="themeText"><?= lang('dark_mode'); ?></span>
            </button>
        </div>

        <!-- Form Pane -->
        <div class="spark-login-form-pane">
            <div class="spark-login-card">
                
                <!-- Logo & Heading -->
                <div class="spark-login-header">
                    <div class="spark-login-logo-container">
                        <?php 
                        $is_quroosh = (isset($brand) && $brand === 'quroosh');
                        $is_arabic  = (!isset($Settings->user_language) || $Settings->user_language == 'arabic');

                        if ($is_quroosh) {
                            $logo_light = $is_arabic
                                ? base_url('assets/uploads/logos/quroosh_ar.png')
                                : base_url('assets/uploads/logos/quroosh_en.png');

                            $logo_dark = $is_arabic
                                ? base_url('assets/uploads/logos/quroosh_ar_white.png')
                                : base_url('assets/uploads/logos/quroosh_en_white.png');
                        } else {
                            $logo_light = $is_arabic
                                ? base_url('assets/uploads/logos/cpe_ar.png')
                                : base_url('assets/uploads/logos/cpe_en.png');

                            $logo_dark = $is_arabic
                                ? base_url('assets/uploads/logos/cpe_ar_white.png')
                                : base_url('assets/uploads/logos/cpe_en_white.png');
                        }
                        ?>
                        <img src="<?= $logo_light ?>" alt="<?= lang('Arkan_Pro_Company') ?>" class="spark-login-logo-img spark-login-logo-light" onerror="this.src='<?= base_url('themes/default/admin/assets/images/LOGO.png') ?>'" />
                        <img src="<?= $logo_dark ?>" alt="<?= lang('Arkan_Pro_Company') ?>" class="spark-login-logo-img spark-login-logo-dark" onerror="this.src='<?= base_url('themes/default/admin/assets/images/LOGO.png') ?>'" />
                    </div>
                    <h1 class="spark-login-title"><?= lang('welcome_back'); ?></h1>
                    <p class="spark-login-subtitle"><?= lang('login_subtitle'); ?></p>
                </div>

                <!-- Flash Messages & Alerts -->
                <?php if ($Settings->mmode) { ?>
                    <div class="spark-login-alert alert-warning">
                        <i class="fa fa-exclamation-triangle" style="font-size:16px; margin-top:2px;"></i>
                        <div><?= lang('site_offline'); ?></div>
                    </div>
                <?php } ?>
                <?php if ($error) { ?>
                    <div class="spark-login-alert alert-danger">
                        <i class="fa fa-times-circle" style="font-size:16px; margin-top:2px;"></i>
                        <div><?= $error ?></div>
                    </div>
                <?php } ?>
                <?php if ($message) { ?>
                    <div class="spark-login-alert alert-success">
                        <i class="fa fa-check-circle" style="font-size:16px; margin-top:2px;"></i>
                        <div><?= $message ?></div>
                    </div>
                <?php } ?>

                <!-- Login Form -->
                <?php echo admin_form_open('auth/login', 'class="spark-login-form" id="loginForm" data-toggle="validator"'); ?>
                    
                    <?php if (isset($_GET['code']) && isset($result->access_token)) { ?>
                        <input type="hidden" value="<?= $result->access_token; ?>" name="foodics_access_token"/>
                    <?php } ?>

                    <!-- Company Code -->
                    <div class="spark-login-group">
                        <label for="company" class="spark-login-label">
                            <?= lang('company'); ?>
                        </label>
                        <div class="spark-login-input-wrapper">
                            <i class="fa fa-building-o spark-login-input-icon"></i>
                            <input type="text" name="company" id="company" required="required" class="spark-login-input" 
                                   placeholder="<?= lang('company_placeholder'); ?>" 
                                   value="<?= DEMO ? 'demo' : ''; ?>" <?= DEMO ? 'readonly' : ''; ?> />
                        </div>
                    </div>

                    <!-- Username / Email -->
                    <div class="spark-login-group">
                        <label for="identity" class="spark-login-label">
                            <?= lang('username'); ?> / <?= lang('email'); ?>
                        </label>
                        <div class="spark-login-input-wrapper">
                            <i class="fa fa-user spark-login-input-icon"></i>
                            <input type="text" name="identity" id="identity" required="required" class="spark-login-input" 
                                   placeholder="example@company.com" 
                                   value="<?= DEMO ? 'demo' : ''; ?>" <?= DEMO ? 'readonly' : ''; ?> />
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="spark-login-group">
                        <label for="password" class="spark-login-label">
                            <?= lang('pw'); ?>
                        </label>
                        <div class="spark-login-input-wrapper">
                            <i class="fa fa-lock spark-login-input-icon"></i>
                            <input type="password" name="password" id="password" required="required" class="spark-login-input" 
                                   placeholder="••••••••" 
                                   value="<?= DEMO ? 'Anyone123' : ''; ?>" <?= DEMO ? 'readonly' : ''; ?> />
                            <button type="button" class="spark-password-toggle" id="togglePasswordBtn" title="<?= lang('toggle_password'); ?>">
                                <i class="fa fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha if enabled -->
                    <?php if ($Settings->captcha) { ?>
                        <div class="spark-login-group">
                            <label for="captcha" class="spark-login-label"><?= lang('type_captcha'); ?></label>
                            <div class="spark-login-captcha-row">
                                <div class="spark-login-captcha-img"><?= $image; ?></div>
                                <a href="<?= admin_url('auth/reload_captcha'); ?>" class="spark-login-captcha-reload reload-captcha" title="<?= lang('reload_captcha'); ?>">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <div class="spark-login-input-wrapper" style="flex:1;">
                                    <input type="text" name="captcha" id="captcha" class="spark-login-input" required="required" placeholder="<?= lang('type_captcha'); ?>" style="padding-inline-start:16px;" />
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Options: Remember Me -->
                    <div class="spark-login-options">
                        <label class="spark-login-checkbox-label">
                            <?php echo form_checkbox('remember', '1', false, 'id="remember" class="spark-login-checkbox"'); ?>
                            <span><?= lang('remember_me'); ?></span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="spark-login-submit-btn" id="submitBtn">
                        <i class="fa fa-sign-in"></i>
                        <span><?= lang('login'); ?></span>
                    </button>

                <?php echo form_close(); ?>

            </div>
        </div>

        <!-- Hero / Feature Showcase Pane (Desktop) -->
        <div class="spark-login-hero-pane">
            <div class="spark-hero-pattern"></div>
            <div class="spark-hero-orb-1"></div>
            <div class="spark-hero-orb-2"></div>

            <div class="spark-hero-content">
                <div class="spark-hero-icon-badge">
                    <i class="fa fa-cubes"></i>
                </div>
                <h2 class="spark-hero-title"><?= $brand=='quroosh' ? lang('hero_title_quroosh') : lang('hero_title'); ?></h2>
                <p class="spark-hero-desc">
                    <?= $brand=='quroosh' ? lang('hero_desc_quroosh') : lang('hero_desc'); ?>
                </p>

                <!-- Feature List Cards -->
                <div class="spark-hero-features">
                    
                    <div class="spark-hero-feature-card">
                        <div class="spark-hero-feature-icon emerald">
                            <i class="fa fa-shield"></i>
                        </div>
                        <div class="spark-hero-feature-text">
                            <h3 class="spark-hero-feature-title"><?= lang('feature_security_title'); ?></h3>
                            <p class="spark-hero-feature-sub"><?= lang('feature_security_desc'); ?></p>
                        </div>
                    </div>

                    <div class="spark-hero-feature-card">
                        <div class="spark-hero-feature-icon blue">
                            <i class="fa fa-bolt"></i>
                        </div>
                        <div class="spark-hero-feature-text">
                            <h3 class="spark-hero-feature-title"><?= $brand=='quroosh' ? lang('feature_performance_title_quroosh') : lang('feature_performance_title'); ?></h3>
                            <p class="spark-hero-feature-sub"><?= $brand=='quroosh' ? lang('feature_performance_desc_quroosh') : lang('feature_performance_desc'); ?></p>
                        </div>
                    </div>

                    <div class="spark-hero-feature-card">
                        <div class="spark-hero-feature-icon purple">
                            <i class="fa fa-cloud"></i>
                        </div>
                        <div class="spark-hero-feature-text">
                            <h3 class="spark-hero-feature-title"><?= lang('feature_cloud_title'); ?></h3>
                            <p class="spark-hero-feature-sub"><?= lang('feature_cloud_desc'); ?></p>
                        </div>
                    </div>

                    <div class="spark-hero-feature-card">
                        <div class="spark-hero-feature-icon amber">
                            <i class="fa fa-bar-chart"></i>
                        </div>
                        <div class="spark-hero-feature-text">
                            <h3 class="spark-hero-feature-title"><?= lang('feature_analytics_title'); ?></h3>
                            <p class="spark-hero-feature-sub"><?= lang('feature_analytics_desc'); ?></p>
                        </div>
                    </div>

                </div>

                <!-- Stats Row -->
                <div class="spark-hero-stats">
                    <div class="spark-hero-stat-item">
                        <div class="spark-hero-stat-number">3500+</div>
                        <div class="spark-hero-stat-label"><?= lang('stat_enterprises'); ?></div>
                    </div>
                    <div class="spark-hero-stat-item">
                        <div class="spark-hero-stat-number">10K+</div>
                        <div class="spark-hero-stat-label"><?= lang('stat_users'); ?></div>
                    </div>
                    <div class="spark-hero-stat-item">
                        <div class="spark-hero-stat-number">99.9%</div>
                        <div class="spark-hero-stat-label"><?= lang('stat_uptime'); ?></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- WhatsApp Floating Button -->
    <div id="WAButton" style="z-index:9999;"></div>

    <script src="<?= $assets ?>js/bootstrap.min.js"></script>
    <script src="<?= $assets ?>js/jquery.cookie.js"></script>
    
    <!-- Login Scripts -->
    <script type="text/javascript">
        $(document).ready(function () {
            // Theme initialization
            var currentTheme = localStorage.getItem('spark-theme') || 'light';
            document.documentElement.setAttribute('data-theme', currentTheme);
            updateThemeUI(currentTheme);

            $('#themeToggleBtn').on('click', function() {
                var activeTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', activeTheme);
                localStorage.setItem('spark-theme', activeTheme);
                updateThemeUI(activeTheme);
            });

            function updateThemeUI(theme) {
                if (theme === 'dark') {
                    $('#themeIcon').removeClass('fa-moon-o').addClass('fa-sun-o');
                    $('#themeText').text('<?= lang('light_mode'); ?>');
                } else {
                    $('#themeIcon').removeClass('fa-sun-o').addClass('fa-moon-o');
                    $('#themeText').text('<?= lang('dark_mode'); ?>');
                }
            }

            // Password Visibility Toggle
            $('#togglePasswordBtn').on('click', function () {
                var $pwd = $('#password');
                var $icon = $('#togglePasswordIcon');
                if ($pwd.attr('type') === 'password') {
                    $pwd.attr('type', 'text');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $pwd.attr('type', 'password');
                    $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Loading state on submit
            $('#loginForm').on('submit', function () {
                var $btn = $('#submitBtn');
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <span><?= lang('logging_in'); ?></span>');
            });
        });
    </script>

    <!-- Floating WhatsApp Plugin Fallback & Initialization -->
    <script type="text/javascript">
        (function ($) {
            if (typeof $.fn.floatingWhatsApp !== 'function') {
                $.fn.floatingWhatsApp = function (options) {
                    var settings = $.extend({
                        phone: '966534277747',
                        headerTitle: '<?= lang('whatsapp_header_title'); ?>',
                        popupMessage: '<?= lang('whatsapp_popup_message'); ?>',
                        showPopup: false,
                        buttonImage: '<img src="<?= $assets ?>images/whatsapp.svg" />',
                        size: '60px',
                        position: 'left'
                    }, options);

                    var $wpp = $(this);
                    var location = settings.position === 'right' ? 'right: 24px;' : 'left: 24px;';
                    $wpp.empty().addClass('floating-wpp').attr('style', 'position: fixed; bottom: 24px; ' + location + ' z-index: 9999;');

                    var $btn = $('<div class="floating-wpp-button" style="width:' + settings.size + '; height:' + settings.size + ';">' + settings.buttonImage + '</div>');
                    var $popup = $('<div class="floating-wpp-popup"><div class="floating-wpp-head"><span>' + settings.headerTitle + '</span></div><div class="floating-wpp-message">' + settings.popupMessage + '</div><div class="floating-wpp-input-message"><textarea rows="1" placeholder="..."></textarea><div class="floating-wpp-btn-send"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#075e54"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg></div></div></div>');

                    $wpp.append($btn).append($popup);

                    $btn.on('click', function (e) {
                        e.stopPropagation();
                        $popup.toggleClass('active');
                    });

                    $popup.find('.floating-wpp-head').on('click', function () {
                        $popup.removeClass('active');
                    });

                    $popup.find('.floating-wpp-btn-send').on('click', function () {
                        var msg = $popup.find('textarea').val() || '';
                        var url = 'https://api.whatsapp.com/send?phone=' + settings.phone + '&text=' + encodeURIComponent(msg);
                        window.open(url, '_blank');
                        $popup.removeClass('active');
                    });

                    $popup.find('textarea').on('keydown', function (e) {
                        if (e.which === 13 && !e.shiftKey) {
                            e.preventDefault();
                            $popup.find('.floating-wpp-btn-send').trigger('click');
                        }
                    });

                    $(document).on('click', function (e) {
                        if (!$(e.target).closest('#WAButton').length) {
                            $popup.removeClass('active');
                        }
                    });

                    return this;
                };
            }

            $(function() {
                $('#WAButton').floatingWhatsApp({
                    phone: '966534277747',
                    headerTitle: '<?= lang('whatsapp_header_title'); ?>',
                    popupMessage: '<?= lang('whatsapp_popup_message'); ?>',
                    showPopup: false,
                    buttonImage: '<img src="<?= $assets ?>images/whatsapp.svg" />',
                    size: '60px',
                    position: "left"
                });
            });
        }(jQuery));
    </script>

    <!-- Start of Tawk.to Script -->
    <script type="text/javascript">
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function(){
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/5fac328a0a68960861be1fe5/default';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
    </script>
    <!-- End of Tawk.to Script -->
</body>
</html>
