<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
 *  ==============================================================================
 *  Author  : Mian Saleem
 *  Email   : saleem@tecdiary.com
 *  For     : mPDF
 *  Web     : https://github.com/mpdf/mpdf
 *  License : GNU General Public License v2.0
 *          : https://github.com/mpdf/mpdf/blob/development/LICENSE.txt
 *  ==============================================================================
 */

use Mpdf\Mpdf;

class Tec_mpdf
{
    public function __construct()
    {
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }

    public function generate($content, $name = 'download.pdf', $output_type = null, $footer = null, $margin_bottom = null, $header = null, $margin_top = null, $xml_file_path = null)
    {
        if (!$output_type) {
            $output_type = 'D';
        }
        if (!$margin_top) {
            $margin_top = 20;
        }
         $vendorDir = dirname(dirname(__FILE__));
		 $baseDir = dirname($vendorDir);


        // $mpdf = new Mpdf(['tempDir' => __DIR__ . '/custom/temp/dir/path']);
        $iccProfilePath = './vendor/mpdf/mpdf/data/iccprofiles/sRGB_IEC61966-2-1.icc';

    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'PDFA' => true,
        'PDFAauto' => '3-b',
        'ICCProfile' => $iccProfilePath,
    ]);

    $mpdf->simpleTables = true;
$mpdf->packTableData = true;
$mpdf->useSubstitutions = false;
$mpdf->autoScriptToLang = false;
$mpdf->autoLangToFont = false;
		//file_get_contents('./vendor\mpdf\mpdf\mpdf.php');
        //require_once '/vendor/mpdf/mpdf/mpdf.php';
		//$mpdf=new mPDF();
//        $mpdf = new Mpdf([
//	'default_font' => 'Din'
//]);
$mpdf->autoScriptToLang = true;
    //$mpdf->autoLangToFont = true;

        if ($this->Settings->user_language == 'arabic')
			$mpdf->SetDirectionality('rtl');
        $mpdf->debug            = (ENVIRONMENT == 'development');
        $mpdf->autoScriptToLang = true;
  //    $mpdf->autoLangToFont   = true;

        // if you need to add protection to pdf files, please uncomment the line below or modify as you need.
         //$mpdf->SetProtection(array('print')); // You pass 2nd arg for user password (open) and 3rd for owner password (edit)
         //$mpdf->SetProtection(array('print', 'copy')); // Comment above line and uncomment this to allow copying of content
        $mpdf->SetTopMargin($margin_top);
    //      $mpdf->Setfont('Din');
        $mpdf->SetTitle($this->Settings->site_name);
        $mpdf->SetAuthor($this->Settings->site_name);
        $mpdf->SetCreator($this->Settings->site_name);
        $mpdf->SetDisplayMode('fullpage');

        if (is_array($content)) {
            $mpdf->SetHeader($this->Settings->site_name . '||{PAGENO}/{nbpg}', '', true); // For simple text header
            $as = sizeof($content);
            $r  = 1;
            foreach ($content as $page) {
                $mpdf->WriteHTML($page['content']);
                if (!empty($page['footer'])) {
                    $mpdf->SetHTMLFooter('<p class="text-center">' . $page['footer'] . '</p>', '', true);
                }
                if ($as != $r) {
                    $mpdf->AddPage();
                }
                $r++;
            }
        } else {
      //   $stylesheet = file_get_contents('./theme/defaul/admin/assets/styles/pdf_rtl.css');
        //  $mpdf->WriteHTML($stylesheet, 1);
           // For simple text footer

            $mpdf->WriteHTML($content);
            $mpdf->SetHTMLFooter('
<table width="100%">
    <tr>
        <td width="50%" >{DATE j-m-Y}</td>
        <td width="50%" >{PAGENO}/{nbpg}</td>
    </tr>
</table>');
            // $mpdf->SetFooter('Center Text|{PAGENO}/{nbpg}', '', TRUE);
            if ($header != '') {
                $mpdf->SetHTMLHeader('<p class="text-center">' . $header . '</p>', '', true);
            }
            if ($footer != '') {
                $mpdf->SetHTMLFooter('<p class="text-center">' . $footer . '</p>', '', true);
            }
        }

        if ($output_type == 'S') {
            $file_content = $mpdf->Output('', 'S');
            write_file('assets/uploads/' . $name, $file_content);
            return 'assets/uploads/' . $name;
        }

        $xmlFile = './assets/xml/'.$xml_file_path;

    if (file_exists($xmlFile)) {
        $mpdf->SetAssociatedFiles([[
            'name' => $xml_file_path,      // الاسم الذي سيظهر داخل الـ PDF
            'mime' => 'text/xml',          // نوع الملف
            'description' => 'ZATCA e-invoice XML',
            'AFRelationship' => 'Alternative', // العلاقة (مهمة لمعيار ZUGFeRD)
            'path' => $xmlFile // المسار الكامل للملف على السيرفر
        ]]);
    }

        setcookie("fileDownload", "true", time()+60, "/");
        $mpdf->Output($name, $output_type);
        exit;

    }
}
