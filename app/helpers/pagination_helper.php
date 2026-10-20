<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('pagination')) {
    function pagination($uri, $total, $per_page)
    {
        $ci = &get_instance();
        $ci->load->library('pagination');
        $config                         = [];
        $config['base_url']             = site_url($uri);
        $config['total_rows']           = $total;
        $config['per_page']             = $per_page;
        $config['full_tag_open']        = '<ul class="pagination mb-0 pagination-shop justify-content-center justify-content-md-start">';
        $config['full_tag_close']       = '</ul>';
        $config['first_tag_open']       = '<li class="page-item first">';
        $config['first_tag_close']      = '</li>';
        $config['last_tag_open']        = '<li class="page-item last">';
        $config['last_tag_close']       = '</li>';
        $config['next_tag_open']        = '<li class="page-item next">';
        $config['next_tag_close']       = '</li>';
        $config['prev_tag_open']        = '<li class="page-item prev">';
        $config['prev_tag_close']       = '</li>';
        $config['cur_tag_open']         = '<li class="page-item active"><a class="page-link current">';
        $config['cur_tag_close']        = '</a></li>';
        $config['num_tag_open']         = '<li class="page-item">';
        $config['num_tag_close']        = '</li>';
        $config['page_query_string']    = true;
        $config['use_page_numbers']     = true;
        $config['query_string_segment'] = 'page';
        // the href value of the anchor tag
        $config['attributes'] = ['class' => 'page-link'];
        $config['first_link']           = '<i class="fa fa-angle-double-left"></i>';
        $config['last_link']            = '<i class="fa fa-angle-double-right"></i>';
        $config['prev_link']            = '<i class="fa fa-angle-left"></i>';
        $config['next_link']            = '<i class="fa fa-angle-right"></i>';
        $ci->pagination->initialize($config);
        return $ci->pagination->create_links();
    }
}
