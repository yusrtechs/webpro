<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Add admin_url
if (!function_exists('admin_url')) {
    function admin_url($uri = '', $protocol = null)
    {
        return get_instance()->config->site_url('admin/' . $uri, $protocol);
    }
}

// Add admin_redirect
if (!function_exists('admin_redirect')) {
    function admin_redirect($uri = '', $method = 'auto', $code = null)
    {
        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = site_url('admin/' . $uri);
        }
        return redirect($uri, $method, $code);
    }
}

// Add shop_url
if (!function_exists('shop_url')) {
    function shop_url($uri = '', $protocol = null)
    {
        return get_instance()->config->site_url('shop/' . $uri, $protocol);
    }
}

// Add shop_redirect
if (!function_exists('shop_redirect')) {
    function shop_redirect($uri = '', $method = 'auto', $code = null)
    {
        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = site_url('shop/' . $uri);
        }
        return redirect($uri, $method, $code);
    }
}

/**
 * Increases or decreases the brightness of a color by a percentage of the current brightness.
 *
 * @param   string  $hexCode        Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
 * @param   float   $adjustPercent  A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
 *
 * @return  string
 *
 * @author  maliayas
 */
if (!function_exists('adjustBrightness')) {

function adjustBrightness($hexCode, $adjustPercent) {
    $hexCode = ltrim($hexCode, '#');

    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    $hexCode = array_map('hexdec', str_split($hexCode, 2));

    foreach ($hexCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }

    return '#' . implode($hexCode);
}
}

if (!function_exists('get_brightness')) {

function get_brightness($hex) {
    // returns brightness value from 0 to 255
    // strip off any leading #
    $hex = str_replace('#', '', $hex);
   
    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));
   
    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}
}
