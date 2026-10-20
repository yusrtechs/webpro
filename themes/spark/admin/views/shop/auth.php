<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<?php
redirect($shop_settings->base_url.'/login/store/token?token='.$shop_settings->token.'&api_key='.$api_key);
exit;
?>

