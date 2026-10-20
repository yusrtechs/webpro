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

use mikehaertl\wkhtmlto\Pdf;

class Cpe_pdf
{
    public function __construct()
    {
    }

    public function __get($var)
    {
        //return get_instance()->$var;
    }

    public function generate()
    {

		$pdf = new Pdf('http://localhost/spark_erp_1_1_2020/spark_erp/admin/sales/pdf/1064');

// On some systems you may have to set the path to the wkhtmltopdf executable
// $pdf->binary = 'C:\...';

if (!$pdf->saveAs('/path/to/page.pdf')) {
    $error = $pdf->getError();
    // ... handle error here
}

/*
$pdf = new Pdf(array(
    'no-outline',         // Make Chrome not complain
    'margin-top'    => 0,
    'margin-right'  => 0,
    'margin-bottom' => 0,
    'margin-left'   => 0,

    // Default page options
    'disable-smart-shrinking',

));

// Add a page. To override above page defaults, you could add
// another $options array as second argument.
$pdf->addPage('http://localhost/spark_erp_1_1_2020/spark_erp/admin/sales/pdf/1064');

if (!$pdf->send()) {
    $error = $pdf->getError();
    // ... handle error here
}
*/

    }
}
