<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

				<?php
			if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

$currencyName=$this->site->getCurrencyByCode($currencyCode)->name;

			?>

<style>
    .tfoot-footer td {
    height: 220px;
}

    .tbody-header th {
        height: 150px;

}

.report-footer {
    position: fixed;
    bottom: 0px;
    height: 210px;
    display: block;
    width: 100%;
    /*border-top: solid 1px #ccc;*/
    overflow: visible;
}


.report-header {
    position: fixed;
    top: 0px;
    height: 140px;
    display: block;
    width: 100%;

    /*border-bottom:2px solid #000 !important;*/
    overflow: visible;
}



@media screen {
  .report-footer {
    display: none;
    overflow: hidden;
  }

  .report-header {
    display: none;
    overflow: hidden;
  }
}

.right-side{
    direction: rtl;
    text-align: -webkit-right;
}
.col-style{
    background:none !important;text-align:right;font-weight:700;font-size: 16px;

}

.left-side{
    direction: ltr;
    text-align: -webkit-left;
}
.qrimg{
	padding:10px;
    max-width:170px;
    max-height:170px;
}

.back-color{
    background-color:#dbdbdb !important;
    -webkit-print-color-adjust:exact !important;
}


.tableInv > thead:first-child > tr, .tableInv thead th {
    background:#dbdbdb !important;
    border:none !important;
    }
.tableInv > tfoot:first-child > tr, .tableInv tfoot td {
    background:#dbdbdb !important;


    }

    .tableInv-striped>tbody>tr:nth-child(odd)>td, .tableInv-striped>tbody>tr:nth-child(odd)>th {
    background-color:#fff;

    }

    .bodyInv{
    font-size: 1rem;
    -webkit-print-color-adjust: exact;
    }


.bord{
   /* border:solid windowtext 1.5pt !important;
   box-sizing: border-box;*/
    border:2px solid #000 !important;

}
.bordl{

    border-left:1px solid #000 !important;

    }



    table { page-break-inside:auto;margin-bottom:1px !important; }
      tr    { page-break-inside:avoid; page-break-after:auto }
/*
   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }*/

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:8pt;border: 1px solid #ccc6c6;padding:0px !important;font-weight: bold; }
/*
    .inv-details1 table    { page-break-inside:avoid; page-break-after:auto }*/
	.inv-details1 tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details1 tr td    { font-size:8pt;border: 1px solid #ccc6c6;font-weight: bold; }

    td {
    }
    .table1 {
    width: 100%;
    max-width: 100%;
    margin-bottom: 20px;
}
.table1>thead:first-child>tr:first-child>th, .table1>thead:first-child>tr:first-child>td, .table-striped thead tr.primary:nth-child(odd) th {
    background-color: #dee2e6;
    color: #212529;
    border-top: 1px solid #00ace6;
    border-bottom: 1px solid #00ace6;
    text-align: center;
}
.table1>thead>tr>th, .table1>tbody>tr>th, .table1>tfoot>tr>th, .table1>thead>tr>td, .table1>tbody>tr>td, .table1>tfoot>tr>td {
    padding: 8px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.table1 th, .table1 td {
    vertical-align: top !important;
}
.table1 td, .table1 th {
    padding: .75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
@media print {

    @page {
				size: A4;
                margin-top: 0cm;
                margin-bottom: 0cm;
            }

            .table1 {
    width: 100%;
    max-width: 100%;
    margin-bottom: 20px;
}
.table1>thead:first-child>tr:first-child>th, .table1>thead:first-child>tr:first-child>td, .table-striped thead tr.primary:nth-child(odd) th {
    background-color: #dee2e6;
    color: #212529;
    border-top: 1px solid #00ace6;
    border-bottom: 1px solid #00ace6;
    text-align: center;
}
.table1>thead>tr>th, .table1>tbody>tr>th, .table1>tfoot>tr>th, .table1>thead>tr>td, .table1>tbody>tr>td, .table1>tfoot>tr>td {
    padding: 8px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}
.table1 th, .table1 td {
    vertical-align: top !important;
}
.table1 td, .table1 th {
    padding: .75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
.modal-content th,td {
/*font-size: 10px;*/
}
            /*h1 {padding:1px;}*/
            .well { margin-bottom:-2px;padding:1px;}
            .well1 {margin-top:-15px;padding:1px;}

            .tableInv td, .tableInv th {
        /*	padding: 0.2rem !important;*/
            }

            .col-xs-1 {
width: 4.33333333%;
}
            .col-xs-11 {
width: 95.66666667%;
}
/*		td {
border: 1px solid #ddd;

} */

.from,.to{margin-bottom:15px;margin-top:15px !important;}

.bord{
border:solid windowtext 1.5pt !important;

}
.back-color{
background:#dbdbdb !important;
-webkit-print-color-adjust:exact !important;
}

.bodyInv {
/*position: absolute; */
 -webkit-print-color-adjust: exact;

}



table { page-break-inside:auto;margin-bottom:2px !important; }
  tr    { page-break-inside:avoid; page-break-after:auto }

.inv-details    { display: block; position: relative;
bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }

.inv-details table    { page-break-inside:avoid; page-break-after:auto }
.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
.inv-details tr td    { font-size:6pt;border: 1px solid #ccc6c6;padding:0px !important;font-weight: bold; }
/*
.inv-details1 table    { page-break-inside:avoid; page-break-after:auto }*/
	.inv-details1 tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details1 tr td    { font-size:6pt;border: 1px solid #ccc6c6;padding:4px !important;font-weight: bold; }

td {
}
}



.divbg:after {
content: '';
width: 100%;
height: 99%;
position: absolute;
background-color: #2A3F54;
top: 0;
right: 0;
-webkit-print-color-adjust:exact;
}

.col-xs-1 {
width: 4.33333333%;
}

</style>

<?php
		if ($Settings->user_language == 'arabic') {
			?>
			<style>

.fixRight{
    direction: rtl;
    text-align: -webkit-right;
}
.fixRight2{
    direction: rtl;
}
</style>


			<?php

		}else{

			?>
			<style>

.fixRight {
    direction: rtl;
    text-align: -webkit-right;
}
.fixRight2{
    direction: ltr;
}

.col-md-55, .col-xs-12, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-sm-12, .col-md-12, .col-lg-12 {
    float: right;
    }
</style>


			<?php

		}
		?>


<div class="modal-dialog modal-lg no-modal-header bodyInv">
<table>

        <thead class="tbody-header">
			<tr >
				<th >&nbsp;</th>
			</tr>
		</thead>

         <tbody>

			<tr>
				<td>
    <div class="modal-content">
        <div class="modal-body">
		<div class="row bold">
            <div class="col-xs-12 no-print" >

                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-2x">&times;</i>
                </button>
                <button type="button" class="btn btn-xs btn-default no-print" style="margin-right:15px;" onclick="window.print();">
                    <i class="fa fa-print"></i> <?= lang('print'); ?>
                </button>
                <button type="button"  id="pdf" class="btn btn-xs btn-default no-print" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">

                                    <i class="fa fa-download"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>



                </button>
			</div>
         </div>


	<div id="pr_pdf">



    <div class="row fixRight  no-print" style="border-bottom:2px solid #000 !important;">
                     <div class="col-xs-7 right-side">
                     <b style="border-bottom:1px solid #000; font-size:18px;margin-top:1px !important;"> <?= $Settings->site_name; ?></b><br/>
                     <b style="color:#bb0e1f !important;font-size:18px;margin:1px !important;"> <?= $Settings->site_ename; ?></b><br/>

                    </div>


		           <div class="col-xs-5 text-left" dir="ltr">
                   <?php
			            //if ($logo)
			            {
							$logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;
                             ?>





                    <div class=" order_barcodes " >
					    <img style="padding-top:5px !important;max-width:270px;" src="<?= base_url() . 'assets/uploads/' . $logo; ?>"
                         alt="<?= $Settings->site_name; ?>"><br/>


                    </div>
                     <?php
			            //if ($logo)
			            }
                            ?>
                   </div>
    </div>

    <div class="row fixRight  no-print" style=" margin-top:1px;">
                     <div class="col-xs-3" style="margin-top: 20px;margin-bottom: 1px;">
                     <b style="color:#bb0e1f !important;font-size:16px;">رقم&nbsp;الفاتورة : </b>  <b style="font-size:16px;color:#bb0e1f !important;"><?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".($inv->sale_no ? $inv->sale_no : $inv->id) : ($inv->sale_no ? $inv->sale_no : $inv->id); ?></b>  </br>
                       </div>
                <div  class="col-xs-6 text-center bold">

                     <?php
                     if($customer->co_type==2){// || abs($inv->grand_total)>=1000){
                        ?>
                       <h1  style="border:solid windowtext 1.5pt !important;border-radius:10px;font-size:18px;color:blue !important;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;مبيعات&nbsp;/&nbsp;Sale&nbsp;Invoice</h1>
                    <?php
					}else if($customer->vat_no!=""){// || abs($inv->grand_total)>=1000){
					?>
				    <h1  style="border:solid windowtext 1.5pt !important;border-radius:10px;font-size:18px;color:blue !important;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;ضريبية&nbsp;/&nbsp;Tax&nbsp;Invoice</h1>
					<?php
					}else{
					?>
				      <h1  style="border:solid windowtext 1.5pt !important;border-radius:10px;font-size:16px;color:blue !important;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;ضريبية&nbsp;مبسطة&nbsp;/&nbsp;Simplified&nbsp;Tax&nbsp;Invoice</h1>
					<?php
					}
					?>
                 </div>

		       <div class="col-xs-3 text-left"style="margin-top: 20px;margin-bottom: 1px;">
                   <b style="color:#bb0e1f !important;font-size:16px;">Invoice&nbsp;No : </b>  <b style="font-size:16px; color:#bb0e1f !important;" ><?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".($inv->sale_no ? $inv->sale_no : $inv->id) : ($inv->sale_no ? $inv->sale_no : $inv->id); ?></b>  </br>
               </div>


            </div>




            <div class="row inv-details "  >

<div class="col-xs-12">

    <table style="border:solid windowtext 1.5pt !important;" class="table1 tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >


        <tfoot >
        <tr style=" background:none !important; font-size: 16px;color:#007bff !important;">
            <td class=" td right-side"colspan="4" style=" border:none !important; background:none !important;"> <span class="fa fa-cube"> </span>  تفاصيل شركتنا  </td>
            <td class=" td left-side"colspan="4"style=" border:none !important; background:none !important;"><i class="fa fa-cube"></i>Our Details</td>
        </tr>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td right-side" style=" ">الاسم : </td>
           <td class=" td right-side " colspan="3" style=" background:none !important;"><?= $Settings->site_name; ?></td>
           <td class=" td" colspan="3"style=" background:none !important;text-align:left;"><?= $Settings->site_ename; ?></td>
           <td class=" td" style=" text-align:left;"> : Name  </td>
        </tr>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td right-side" style=" ">اسم الشارع : </td>
           <td class=" td right-side" colspan="3" style=" background:none !important;"><?= $warehouse->street_name ? $warehouse->street_name. '</b><br>' : '</b>' ; ?></td>
           <td class=" td" colspan="3"style=" background:none !important;text-align:left;"><?= $warehouse->street_nameEn ? $warehouse->street_nameEn : $warehouse->street_name; ?></td>
           <td class=" td" style=" text-align:left;"> : Street name  </td>
        </tr>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td right-side" style=" ">رقم المبنى : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->building_no ? $warehouse->building_no. '</b><br>' : '</b>' ; ?></td>
           <td class=" td right-side" style=" ;">المدينة : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->city ? $warehouse->city. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->cityEn ? $warehouse->cityEn : $warehouse->city; ?></td>
           <td class=" td left-side" style=" ">City : </td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->building_no ? $warehouse->building_no. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" ">Building No :  </td>
        </tr>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td right-side" style=" ">الرقم الاضافي : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->additional_no ? $warehouse->additional_no. '</b><br>' : '</b>' ; ?></td>
           <td class=" td right-side" style=" ;">الحي : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->district_name ? $warehouse->district_name. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->district_nameEn ? $warehouse->district_nameEn : $warehouse->district_name; ?></td>
           <td class=" td left-side" style=" ">District : </td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->additional_no ? $warehouse->additional_no. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" ">Addl.No :  </td>
        </tr>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td right-side" style=" ">الرمز البريدي : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->postal_code ? $warehouse->postal_code. '</b><br>' : '</b>' ; ?></td>
           <td class=" td right-side" style=" ;">البلد : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->country ? $warehouse->country. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->countryEn ? $warehouse->countryEn : $warehouse->country; ?></td>
           <td class=" td left-side" style=" ">Country : </td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->postal_code ? $warehouse->postal_code. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" ">Postal code :  </td>
        </tr>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td right-side" style=" ">الرقم الضريبي : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?></td>
           <td class=" td right-side" style=" ;">السجل التجاري : </td>
           <td class=" td right-side" style=" background:none !important;"><?= $warehouse->cr_no ? $warehouse->cr_no. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->cr_no ? $warehouse->cr_no. '</b><br>' : '</b>' ;?></td>
           <td class=" td left-side" style=" ">CR No : </td>
           <td class=" td left-side" style=" background:none !important;"><?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?></td>
           <td class=" td left-side" style=" ">VAT No :  </td>
        </tr>
        </tfoot>

    </table>
</div>
</div>

            <div class="row inv-details "  >

                <div class="col-xs-12">

                    <table style="border:solid windowtext 1.5pt !important;" class="table1 tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >


                        <tfoot >
                        <tr style=" background:none !important; font-size: 16px;color:#007bff !important;">
                            <td class=" td right-side"colspan="4" style=" border:none !important; background:none !important;"> <span class="fa fa-cube"> </span>  تفاصيل شركة العميل  </td>
                            <td class=" td left-side"colspan="4"style=" border:none !important; background:none !important;"><i class="fa fa-cube"></i>Client Details</td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" td right-side" style=" ">الاسم : </td>
                           <td class=" td right-side " colspan="3" style=" background:none !important;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></td>
                           <td class=" td" colspan="3"style=" background:none !important;text-align:left;"><?= $customer->companyEn && $customer->companyEn != '-' ? $customer->companyEn : $customer->name;?></td>
                           <td class=" td" style=" text-align:left;"> : Name  </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" td right-side" style=" ">تلفون : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->phone ? $customer->phone : '' ; ?></td>
                           <td class=" td right-side" style=" ;">اسم الشارع : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->street_name && $customer->street_name != '-' ? $customer->street_name :'';  ?></td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->street_nameEn && $customer->street_nameEn != '-' ? $customer->street_nameEn :$customer->street_name; ?></td>
                           <td class=" td left-side" style=" ">Street name : </td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->phone ? $customer->phone : '' ; ?></td>
                           <td class=" td left-side" style=" ">Tel :  </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" td right-side" style=" ">رقم المبنى : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->building_no ? $customer->building_no : '' ; ?></td>
                           <td class=" td right-side" style=" ;">المدينة : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->city ? $customer->city: '' ; ?></td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->cityEn ? $customer->cityEn : $customer->city; ?></td>
                           <td class=" td left-side" style=" ">City : </td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->building_no ? $customer->building_no : '' ; ?></td>
                           <td class=" td left-side" style=" ">Building No :  </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" td right-side" style=" ">الرقم الاضافي : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->additional_no ? $customer->additional_no : '' ; ?></td>
                           <td class=" td right-side" style=" ;">الحي : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->district_name ? $customer->district_name : '' ; ?></td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->district_nameEn ? $customer->district_nameEn : $customer->district_name; ?></td>
                           <td class=" td left-side" style=" ">District : </td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->additional_no ? $customer->additional_no : '' ; ?></td>
                           <td class=" td left-side" style=" ">Addl.No :  </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" td right-side" style=" ">الرمز البريدي : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->postal_code ? $customer->postal_code : '' ; ?></td>
                           <td class=" td right-side" style=" ;">البلد : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->country ? $customer->country : '' ; ?></td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->countryEn ? $customer->countryEn : $customer->country; ?></td>
                           <td class=" td left-side" style=" ">Country : </td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->postal_code ? $customer->postal_code : '' ; ?></td>
                           <td class=" td left-side" style=" ">Postal code :  </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" td right-side" style=" ">الرقم الضريبي : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->vat_no ?  $customer->vat_no : '' ; ?></td>
                           <td class=" td right-side" style=" ;">السجل التجاري : </td>
                           <td class=" td right-side" style=" background:none !important;"><?= $customer->cr_no ? $customer->cr_no : '' ; ?></td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->cr_no ? $customer->cr_no : '' ;?></td>
                           <td class=" td left-side" style=" ">CR No : </td>
                           <td class=" td left-side" style=" background:none !important;"><?= $customer->vat_no ?  $customer->vat_no : '' ; ?></td>
                           <td class=" td left-side" style=" ">VAT No :  </td>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
        <div class="row inv-details "  >

            <div class="col-xs-12 ">

                <table style="border:solid windowtext 1.5pt !important;" class="table1 tableInv  table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >

                    <thead >

                  <tr style="-webkit-print-color-adjust:exact;">

                        <th><?= "رقم المرجع".'</br>'."Reference No"; ?></th>
                        <th><?= "فترة الفاتورة".'</br>'."Invoice Period"; ?></th>
                        <th><?= "تاريخ الاستحقاق".'</br>'."Due Date"; ?></th>
                        <th><?= "رقم امر الشراء".'</br>'."Contract / PO No"; ?></th>
                        <th><?= "تاريخ التسليم". '</br>'."Supply Date"; ?></th>
                        <th><?= "تاريخ الفاتورة" .'</br>'."Invoice Date"; ?></th>
                    </tr>

                    </thead>

                    <tfoot >
                    <tr style="background:none !important;vertical-align:middle;">
                    <td class=" td text-center" style=" background:none !important;"><?= $inv->reference_no; ?> </td>
                    <td class=" td  text-center" style=" background:none !important;direction:ltr;"><?= $inv->inv_period ? $inv->inv_period : " - "; ?></td>
                    <td class=" td text-center " style=" background:none !important;"><?= $inv->due_date ? $this->sma->hrsd($inv->due_date) : ""; ?> </td>
                    <td class=" td text-center " style=" background:none !important;"><?= $inv->po_no ? $inv->po_no : $customer->cf1; ?></td>
                    <td class=" td text-center " style=" background:none !important;"><?= $inv->exit_date ? str_replace(" ","&nbsp;&nbsp;&nbsp;",$this->sma->hrld($inv->exit_date)) : "" ;?></td>
                    <td class=" td text-center " style=" background:none !important;"><?= $inv->date ? str_replace(" ","&nbsp;&nbsp;&nbsp;",$this->sma->hrld($inv->date)) : "" ;?></td>

                    </tr>
                    </tfoot>

                </table>
            </div>
</div>

            <?php
                    $col = 8;
                    //if ($Settings->product_discount && $inv->product_discount != 0) {
                    //    $col++;
                    //}
                    if ($Settings->tax1 && $inv->product_tax > 0) {
                        $col++;
                    }
                    if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 2;
                    //} elseif ($Settings->product_discount && $inv->product_discount != 0) {
                    //    $tcol = $col - 1;
                    } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 1;
                    } else {
                        $tcol = $col;
                    }
                    ?>
<!--<div style="border:2px solid #000">
    <div class="table-responsive" >
                 <table class="table table-striped jambo_table bulk_action print-table order-table pdftable " >
                -->
        <div class="row inv-details1 "  >


            <div class=" col-xs-12 " >
                <table style="border:solid windowtext 1.5pt !important;" class="table1 table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >


                    <thead >

                  <tr style="-webkit-print-color-adjust:exact;">

                        <th><?= lang('No_sales'); ?></th>
                        <th><?= lang('Product_code_sales'); ?></th>
                        <th class="col-xs-6"><?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? lang('Service_name_sales') : lang('Product_name_sales'); ?></th>
                        <th><?= lang('Product_unit_sales'); ?></th>

                        <th><?= lang('Quantity_sales'); ?></th>

                        <th ><?= lang('Unit_price_sales'); ?></th>
						 <!--<th><?= lang('value'); ?></th>-->
                        <?php
						if($inv->order_type==1){
						//if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th >' . lang('Discount_sales') . '</th>';
                        //}
						//echo '<th class="col-xs-2">' . lang('Item_price_sales') . '</th>';
                        if ($Settings->tax1 && $inv->product_tax > 0) {
							 echo '<th >' . lang('Tax_percent_sales') . '</th>';
                            echo '<th >' . lang('VAT_amount_sales') . '</th>';
                        }
						}

                        ?>
                        <th class="col-xs-2"><?= lang('Total_price_sales'); ?></th>
                    </tr>

                    </thead>

                    <tbody >

                    <?php $r = 1;
					$tquantity=0;
					foreach ($rows as $row):
					if($row->quantity_received==0)
                    $tquantity=$tquantity+$row->quantity;
                    else
					$tquantity=$tquantity+$row->quantity_received;
                    ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <td style="vertical-align:middle;">
                                <?= $row->product_code ?>
                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">
							<?php
							if($row->promo_free==1){
								?>
							<img src="<?= base_url() . 'assets/images/free.jpg' ?>" width="10%">
							<?php
							}
							?>
                                <?php if ($Settings->show_product_code) {
							?>
                           <!--  <?=  lang('code'). ' : ' .$row->product_code. '<br>' ; ?> -->
                               <?php
							}
							if($row->details){
							$details= str_replace("<p>","",($row->details)."");
							$details= str_replace("</p>","",($details)."");
							}

							?>

								<?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                            <?= $row->second_name ? ' - ' . $row->second_name : ''; ?>
                                <?= $row->details ? ' - ' . $details : ''; ?>
                                <?= $row->cf1 ? ' - ' . $row->cf1. ' ' .$row->cf2. ' ' .$row->cf3 : ''; ?>
                            </td>
							<?php
								$base_unit_code=$row->base_unit_code;
								$product_unit_code=$row->product_unit_code;
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$base_unit_code=$unit->name;
										$product_unit_code=$unit->name;
									}
								}
								?>

							<td><?=$product_unit_code?></td>
                            <?php if ($Settings->indian_gst) {
							?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php
							} ?>

                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity) . ' ' . ($inv->sale_status == 'returned' ? $base_unit_code : ''); ?></td>

							<td style="text-align:right; width:100px;">
                                <!--<?= $row->unit_price != $row->real_unit_price && $row->item_discount > 0 ? '<del>' . $this->sma->formatMoney($row->real_unit_price) . '</del>' : ''; ?>
                                -->
								<?= $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4),' '); ?>
                            </td>
							<!--<td><?=($this->sma->formatMoney($row->real_unit_price * $row->unit_quantity))?></td>-->
                            <?php
							if($inv->order_type==1){
							//if ($Settings->product_discount && $inv->product_discount != 0) {
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney($row->item_discount,' ') . '</td>';
                            //}
							// echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->net_unit_price * $row->quantity). '</td>';


							if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $row->tax . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->item_tax,' ') . '</td>';
                            }
                            echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->subtotal,' ') . '</td>';

							}else{
							    echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' . $this->sma->formatDecimal(($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity))*$row->unit_quantity) . '</td>';
                            }
                            ?>
                           <!-- <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal,' '); ?></td> -->
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
                        foreach ($return_rows as $row):
						$tquantity=$tquantity+$row->unit_quantity;
                        ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code; ?>
                                </td>
                                <td style="vertical-align:middle;">
                                    <?php if ($Settings->show_product_code) {
							?>
                         <!--    <?=  lang('code'). ' : ' .$row->product_code. '<br>' ; ?> -->
                               <?php
							}
							if($row->details){
							$details= str_replace("<p>","",($row->details)."");
							$details= str_replace("</p>","",($details)."");
							}

							?>

								<?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                            <?= $row->second_name ? ' - ' . $row->second_name : ''; ?>
                                    <?= $row->details ? ' - ' . $details : ''; ?>
                                <?= $row->cf1 ? ' - ' . $row->cf1. ' ' .$row->cf2. ' ' .$row->cf3. ' ' .$row->cf4. ' ' .$row->cf5. ' ' .$row->cf6 : ''; ?>
                                </td>

                                <?php
								$base_unit_code=$row->base_unit_code;
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$base_unit_code=$unit->name;
									}
								}
								?>

								<td style="width: 80px; text-align:center; vertical-align:middle;"><?= $base_unit_code; ?></td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($row->quantity)); ?></td>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatMoney($row->unit_price,' '); ?></td>
                                <?php
                                if($inv->order_type==1){
								if ($Settings->tax1 && $inv->product_tax > 0)
								{
                                //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                    echo '<td style="width: 100px; text-align:center; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->item_discount)) . '</td>';
                                    echo '<td style="width: 100px; text-align:center; vertical-align:middle;">' . abs($row->tax) . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';

								}else{
                        //if ($Settings->product_discount && $inv->product_discount != 0) {
                          //  echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $this->sma->formatMoney($row->discount) . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                             echo '<td style="width: 100px; text-align:center; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';
                           }
						   	  echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->subtotal),' ') . '</td>';

								}else{

									  echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->unit_price) * $this->sma->formatQuantity(abs($row->quantity)),' ') . '</td>';

								}

						?>
                                <!--<td style="text-align:right; width:120px;"><?= $this->sma->formatMoney(abs($row->subtotal),' '); ?></td>-->
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>
                    </tbody>
                    <!--<tfoot>
                    <?php if ($inv->grand_total != $inv->total) {
                        ?>
                        <tr>
                            <td colspan="<?= $tcol-2; ?>"
                                style="text-align:right; padding-right:10px;"><?= lang('total'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>

                            <?php

                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_discount + $return_sale->product_discount) : $inv->product_discount) . '</td>';
                        } ?>
						<td></td>
						<td></td>
						<?php
						if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_tax + $return_sale->product_tax) : $inv->product_tax) . '</td>';
                            }
						?>
                            <td style="text-align:right; padding-right:10px;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax) + ($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php
                    } ?>
                    <?php
                    if ($return_sale) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('return_total') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($return_sale->grand_total) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('return_surcharge') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>



                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('order_discount') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . ($inv->order_discount_id ? '<small>(' . $inv->order_discount_id . ')</small> ' : '') . $this->sma->formatMoney($return_sale ? ($inv->order_discount + $return_sale->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;">' . lang('order_tax') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax + $return_sale->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang('shipping') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->shipping - ($return_sale && $return_sale->shipping ? $return_sale->shipping : 0)) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('total_amount_Tax_Invoice'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>

                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('paid'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('balance'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid)); ?></td>
                    </tr>

                    </tfoot>-->
                </table>
                <div class="clearfix"></div>
            </div>
			<?php
			if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

			?>

			<?php
            $note= str_replace("<p>","",$this->sma->decode_html($inv->note));
            $note= str_replace("</p>","",$this->sma->decode_html($note));
            $note= str_replace(" ","",$this->sma->decode_html($note));

            ?>

            <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax + $return_sale->product_tax : $inv->product_tax)) : ''; ?>
            </div> <!-- end  border -->
            <?php
            $whole = floor(abs($tquantity));
            $fraction = abs($tquantity) - $whole;
            ?>


            <div class="row inv-details "  >

<div class="col-xs-12" >
</p>
    <table  style="border:solid windowtext 1.5pt !important;"
     class="table1 tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >

        <tfoot >

        <tr style="background:none !important;vertical-align:middle;">

           <td class=" td  col-xs-5 right-side " colspan="4" rowspan="<?= ($inv->donation!=0 || $inv->retention !=0) ? '7' : '6'; ?>"  style=" background:none !important;">
           <font style="float:left;"><?= $this->sma->AmountInWordsEn($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'Halala'); ?></font></br>
           <font style="float:right;"><?= $this->sma->AmountInWordsAr($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'هللة'); ?></font> </td>
           <?php if($inv->total < 0){ ?>

            <td class="td col-xs-3 right-side" style=" background:none !important;"><?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? 'عدد الخدمات المرتجعة' : 'عدد الأصناف المرتجعة'; ?>
            </td>
            <?php }else{ ?>

            <td class="td col-xs-3 right-side" style=" background:none !important;"> <?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? 'عدد الخدمات المقدمة' : 'عدد الأصناف المباعة'; ?>

            </td>
            <?php } ?>



           <td class=" td col-xs-2 text-center" colspan="2" style="background:none !important; "><?= ($Settings->isDisplayItemsSold==1) ? (int)$this->sma->formatDecimal(abs($r-1),2) : ($fraction > 0 ? $this->sma->formatDecimal(abs($tquantity),2) : (int)$this->sma->formatDecimal(abs($tquantity),2)); ?></td>
           <?php if($inv->total < 0){ ?>
           <td class="td col-xs-2 text-left" style=" background:none !important;"><?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? 'Services Return Total' : 'Item Return Total'; ?>
            </td>
            <?php }else{ ?>

            <td class="td col-xs-2 text-left" style=" background:none !important;"> <?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? 'Services Provided Total' : 'Item Sold Total'; ?>

            </td>
            <?php } ?>

        </tr>

        <?php

                    $db_name=str_replace("cpesoft_","",$this->db->database);
                    $VAT="";
					if($inv->total_tax!=0)
					$VAT="(".$this->sma->formatDecimal($active_tax,0)."%)";

					?>
        <?php
            if ($Settings->IsConstruction && $inv->retention !=0 && ($db_name=='rama' || $db_name=='hamad')) {
        ?>

        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-3 right-side" style=" background:none !important;">المبلغ الإجمالي </td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > Total Amount   </td>
        </tr>

        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-3 right-side" style=" background:none !important;">مبلغ الاحتجاز  <?= (int)$inv->retention.'%' ?></td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > Retention Amount  <?= (int)$inv->retention.'%' ?>  </td>
        </tr>

        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-3 right-side" style=" background:none !important;">الإجمالي بعد الاحتجاز </td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > Total Amount After Retention   </td>
        </tr>
        <?php
            }else{
        ?>

        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-3 right-side" style=" background:none !important;">الإجمالي غير شامل ضريبة القيمة المضافة : </td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > Total excl. VAT   </td>
        </tr>

        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-3 right-side" style=" background:none !important;"><?= $db_name != 'rana' ? 'اجمالي الخصم' : 'مستقطع' ?></td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > <?= ($db_name != 'rana' ? 'Total Discounts' : 'Deducation') ?>  </td>
        </tr>
        <?php
            }
        ?>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-3 right-side" style=" background:none !important;">ضريبة القيمة المضافة <?= $VAT ?>  </td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > VAT  <?= $VAT ?> </td>
        </tr>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-2 right-side" style=" background:none !important;">المبلغ شاملا ضريبة القيمة المضافة  </td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=""><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" ><?= $db_name=="rama" ? 'Grand Total' : 'Amount Incl. VAT' ?>     </td>
        </tr>
        <?php
		if(($db_name=='rama' || $db_name=='hamad')){
		?>

        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-2 right-side" style=" background:none !important;">الرصيد المستحق </td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=""><?= $this->sma->formatMoney(($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance - $inv->donation  + $advance_payment_discount + $advance_payment_discount_tax) + ($return_sale->paid + $return_sale->discount_allowance - $return_sale->donation + $advance_payment_discount + $advance_payment_discount_tax) : ($inv->paid + $inv->discount_allowance - $inv->donation + $advance_payment_discount + $advance_payment_discount_tax))),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > Balance Due    </td>
        </tr>
        <?php
        }
		if($inv->donation!=0){
		?>
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td col-xs-2 right-side" style=" background:none !important;">التبرع </td>
           <td class=" td col-xs-2 text-center fixRight2" colspan="2" style=""><?= $this->sma->formatMoney(($return_sale ? ($inv->donation - $return_sale->donation) : $inv->donation),$currencyCode); ?></td>
           <td class=" td col-xs-2 text-left" style=" background:none !important;" > Donation    </td>
        </tr>
        <?php
		}
		?>
        <tr style="background:none !important;vertical-align:middle;">
            <td class=" td col-xs-12 right-side" colspan="12" style=" background:none !important;">
                               <font style="float:right;" dir="ltr"> <?="ملاحظة : ". $this->sma->decode_html($inv->note); ?> </font> </td>
           <!-- <td class=" td" colspan="6"style=" background:none !important;text-align:left;">
                               <font style="float:left;"> <?="Note ". $this->sma->decode_html($inv->note); ?> </font></td> -->

        </tr>


        </tfoot>

    </table>
</div>

<div class="col-xs-12 no-print" >
                    <table   style="border:solid windowtext 1.5pt !important;"
                    class="table1 tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side" dir="rtl">

				        <tfoot style="-webkit-print-color-adjust:exact !important; ">
                        <tr style="background:none !important;vertical-align:middle;">
                            <td class=" td col-xs-2 text-center" rowspan="6" style=" background:none !important;">
    						 <?php
							  $wh_name=$warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name;
							 ?>

                                        <?php
						        $QR=$this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2));
						    ?>
						    <?= $invQR ? $this->sma->qrcode('', $invQR, 2) : ($warehouse->vat_reg_no ? $this->sma->qrcode('', $QR, 2) : '') ?>

                             </td>
                            <td class=" td col-xs-2 text-center"  style="">Received&nbsp;By&nbsp;/&nbsp;مستلم&nbsp;الفاتورة</td>
                            <td class=" td col-xs-2 text-center" > Authorized&nbsp;Signatory </td>
                            <td class=" td col-xs-6 text-center"colspan="3" > Bank&nbsp;Details&nbsp;/&nbsp;التفاصيل&nbsp;المصرفية</td>

                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                            <td class=" td col-xs-2 text-center" rowspan="5" style="background:none !important;"></td>
                            <td class=" td col-xs-2 text-left" rowspan="5" style="background:none !important;"> </td>

                            <td class=" td col-xs-4 text-left"style="background:none !important;" ><?= $Settings->site_name; ?>
                            </br><?=  $Settings->account_name; ?></td>
                            <td class=" td col-xs-2 text-left" style="background:none !important;" >ACCOUNT NAME </br> اسم الحساب</td>

                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">

                            <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->IBAN; ?></td>
                            <td class=" td col-xs-2 text-left" style="background:none !important;" >IBAN </br> آيب</td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">

                            <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->bank_name; ?></td>
                            <td class=" td col-xs-2 text-left" style="background:none !important;" >BANK</br>بنك</td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">

                            <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->branch_name; ?></br>
                            <?= $warehouse->branch_nameEn ?></td>
                            <td class=" td col-xs-2 text-left" style="background:none !important;" >BRANCH </br> فرع</td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">

                            <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->swift_code; ?></td>
                            <td class=" td col-xs-2 text-left" style="background:none !important;" >Swift Code </br> سويفت</td>
                        </tr>



                    </tfoot>

    </table>

</div>



</div>


             <div class="clearfix"></div>


             <div class="row inv-details" style="display:none;">
                <div class="col-xs-12" >


                        <p>
                     <table style="border:solid windowtext 1.5pt !important;" class="table1 tableInv table-striped jambo_table bulk_action print-table order-table pdftable  right-side" >
				        <tfoot style="-webkit-print-color-adjust:exact !important;">

	                    <tr >


						<?php if ($Settings->user_language == 'arabic') {
						?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع
                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid),$currencyCode); ?>


                        </td>



                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid),$currencyCode); ?>

                        </td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">مدفوع

                        </td>

					    <?php } ?>

						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('paid_tax_invoice'); ?>

                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid)); ?></td>
						-->
                    </tr>
					<?php
					if(abs($inv->paid)!=0){
						foreach ($payments as $payment) {

						?>
                    <tr>




					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع بواسطة

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?php
							 if ($payment->paid_by == 'cash' || $payment->paid_by == 'gift_card'  || $payment->paid_by == 'deposit'){
								 ?>

								  <?= lang($payment->paid_by) ?>
							<?php
							 }
                            ?>
                        	<?php
							 if ($payment->paid_by == 'CC' || $payment->paid_by == 'span'){
								 ?>

								  <?= lang($payment->cc_type) ?>
							<?php
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'Cheque'){
								 ?>

								  <?= lang('cheque') ?><?= ' '.lang('cheque_no').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)


							<?php
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>

								  <?= lang('bank_transfer') ?><?= ' '. lang('bank_details').' : ' ?>
								  <?= lang($payment->cheque_no) ?>


							<?php
							 }
                            ?>
							<?= $this->sma->formatMoney(abs($payment->amount),$payment->currency); ?>
						<?= $this->sma->hrld($payment->date); ?>
						</td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid by

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid by

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(abs($payment->amount),$payment->currency); ?>
						<?php
							 if ($payment->paid_by == 'cash' || $payment->paid_by == 'gift_card'  || $payment->paid_by == 'deposit'){
								 ?>

								  (<?= lang($payment->paid_by) ?>)
							<?php
							 }
                            ?>
                        	<?php
							 if ($payment->paid_by == 'CC' || $payment->paid_by == 'span'){
								 ?>

								  (<?= lang($payment->cc_type) ?>)
							<?php
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'Cheque'){
								 ?>

								  (<?= lang('cheque') ?>)<?= '  '.lang('cheque_no').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)


							<?php
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>

								  (<?= lang('bank_transfer') ?>)<?= '  '.lang('bank_details').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)


							<?php
							 }
                            ?>
                            <?= $this->sma->hrld($payment->date); ?>
							</td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">مدفوع بواسطة

                        </td>

					    <?php } ?>



                    </tr>











	<!--

					<tr>




					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">تاريخ الدفع

                        </td>

                        <td colspan="4"style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->hrld($payment->date); ?></td>



                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid Date

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid Date

                        </td>

                        <td colspan="4"style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->hrld($payment->date); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">تاريخ الدفع

                        </td>

					    <?php } ?>



                    </tr>

-->

					<?php
						}
					}
						?>

							<?php
							if(abs($return_sale->paid)!=0){
					        if ($return_sale) {

						?>
                    <tr>






					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع بواسطة


                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;">
						<?php
							 if ($return_payments[0]->paid_by == 'cash' || $return_payments[0]->paid_by == 'gift_card'){
								 ?>

								  (<?= lang($return_payments[0]->paid_by) ?>)
							<?php
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'CC' || $return_payments[0]->paid_by == 'span'){
								 ?>

								  <?= lang($return_payments[0]->cc_type) ?>
							<?php
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'Cheque'){
								 ?>

								  <?= lang('cheque') ?><?= ' '.lang('cheque_no').' : ' ?>
								  (<?= lang($return_payments[0]->cheque_no) ?>)
							<?php
							 }
                            ?>
						<?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid by

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid by

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?>
						<?php
							 if ($return_payments[0]->paid_by == 'cash' || $return_payments[0]->paid_by == 'gift_card'){
								 ?>

								  (<?= lang($return_payments[0]->paid_by) ?>)
							<?php
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'CC' || $return_payments[0]->paid_by == 'span'){
								 ?>

								  (<?= lang($return_payments[0]->cc_type) ?>)
							<?php
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'Cheque'){
								 ?>

								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($return_payments[0]->cheque_no) ?>)
							<?php
							 }
                            ?>
						</td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">مدفوع بواسطة


                        </td>

					    <?php } ?>



					<!--
                        <td colspan="<?= $col; ?>"
                            style="font-weight:bold;"><?= lang('paid_by_tax_invoice'); ?>
							<?php
							 if ($return_payments[0]->paid_by == 'cash' || $return_payments[0]->paid_by == 'gift_card'){
								 ?>

								  (<?= lang($return_payments[0]->paid_by) ?>)
							<?php
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'CC' || $return_payments[0]->paid_by == 'span'){
								 ?>

								  (<?= lang($return_payments[0]->cc_type) ?>)
							<?php
							 }
                            ?>
                        	<?php
							 if ($return_payments[0]->paid_by == 'Cheque'){
								 ?>

								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($return_payments[0]->cheque_no) ?>)
							<?php
							 }
                            ?>
                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_payments[0]->amount)); ?></td>
						-->
                    </tr>

					<?php
						}
						}





                    ?>
					<tr>

					<?php
					if($inv->discount_allowance!=0){
					?>

					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">خصم مسموح به

                        </td>


                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->discount_allowance  - abs($return_sale->discount_allowance)) : $inv->discount_allowance),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Discount Allowance

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Discount Allowance

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->discount_allowance  - abs($return_sale->discount_allowance)) : $inv->discount_allowance),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">خصم مسموح به

                        </td>

					    <?php } ?>
					   <!--
                        <td colspan="<?= $col; ?>"
                            style="font-weight:bold;"><?= lang('balance_tax_invoice'); ?>

                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid))); ?></td>
						-->
                    </tr>
					<?php
					}
                    ?>


                <?php
                $advance_payment_discount=0;
                $advance_payment_discount_tax=0;
                if ($Settings->IsInsuranceBusiness && $inv->advance_payment_discount !=0) {
                    $advance_payment_discount=$return_sale ? (($inv->total + $return_sale->total) * $inv->advance_payment_discount / 100) : (abs($inv->total - $inv->total_discount) * $inv->advance_payment_discount / 100);
                    }
                ?>

                    <?php

					if ($Settings->IsInsuranceBusiness && $inv->advance_payment_discount !=0) {
					?>
                    <tr>
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">خصم الدفعة المقدمة <?= '%'.(int)$inv->advance_payment_discount ?>

                        </td>


                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($advance_payment_discount,$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Advance payment discount <?= (int)$inv->advance_payment_discount.'%' ?>

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Advance payment discount <?= (int)$inv->advance_payment_discount.'%' ?>

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($advance_payment_discount,$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">خصم الدفعة المقدمة <?= '%'.(int)$inv->advance_payment_discount ?>

                        </td>

					    <?php }
                    ?>
                    </tr>

                    <?php
                    $advance_payment_discount_tax = ($advance_payment_discount * $active_tax / 100);
                    ?>



                    <tr>
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">ضريبة خصم الدفعة المقدمة <?= $VAT ?>

                        </td>


                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($advance_payment_discount_tax ,$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Advance payment discount tax <?= $VAT ?>

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Advance payment discount tax <?= $VAT ?>

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($advance_payment_discount_tax,$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">ضريبة خصم الدفعة المقدمة <?= $VAT ?>

                        </td>

					    <?php }
                    ?>
                    </tr>

                    <?php }
                    ?>


                    <?php

                    $db_name=str_replace("cpesoft_","",$this->db->database);
					?>

					<tr>


					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الباقي

                        </td>


                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance  + $advance_payment_discount + $advance_payment_discount_tax) + ($return_sale->paid + $return_sale->discount_allowance + $advance_payment_discount + $advance_payment_discount_tax) : ($inv->paid + $inv->discount_allowance + $advance_payment_discount + $advance_payment_discount_tax))),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;"><?php echo $db_name=="golden" || $Settings->IsConstruction1 ?  "Remaining amount -Retention 5%" :  "Remaining amount"; ?>

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;"><?php echo $db_name=="golden" || $Settings->IsConstruction1 ?  "Remaining amount -Retention 5%" :  "Remaining amount"; ?>

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance  + $advance_payment_discount + $advance_payment_discount_tax) + ($return_sale->paid + $return_sale->discount_allowance + $advance_payment_discount + $advance_payment_discount_tax) : ($inv->paid + $inv->discount_allowance + $advance_payment_discount + $advance_payment_discount_tax))),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الباقي

                        </td>

					    <?php } ?>
					   <!--
                        <td colspan="<?= $col; ?>"
                            style="font-weight:bold;"><?= lang('balance_tax_invoice'); ?>

                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid))); ?></td>
						-->
                    </tr>
					<?php
					if($inv->shipping!=0){
					?>
					<tr>

					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الشحن

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Shipping

                        </td>
						<?php }else{ ?>

                        <td colspan="4" colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Shipping

                        </td>

                        <td style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الشحن

                        </td>

					    <?php } ?>
						<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('shipping_tax_invoice'); ?>

                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping)); ?></td>
						-->
                    </tr>
					<?php
					}
					?>

					 <?php
					if ($Settings->IsInsuranceBusiness && $inv->insurance_business !=0) {
					?>


                    <tr>
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">تأمين الأعمال <?= '%'.(int)$inv->insurance_business ?>

                        </td>


                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $return_sale->total) * $inv->insurance_business / 100) : (abs($inv->total - $inv->total_discount) * $inv->insurance_business / 100),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Insurance business <?= (int)$inv->insurance_business.'%' ?>

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Insurance business <?= (int)$inv->insurance_business.'%' ?>

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $return_sale->total) * $inv->insurance_business / 100) : (abs($inv->total - $inv->total_discount) * $inv->insurance_business / 100),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">تأمين الأعمال <?= '%'.(int)$inv->insurance_business ?>

                        </td>

					    <?php }
                    ?>
                    </tr>

                    <tr>
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">المستحق صرفه

                        </td>


                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney((($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance) + ($return_sale->paid + $return_sale->discount_allowance) : ($inv->paid + $inv->discount_allowance))) - ($return_sale ? (($inv->total + $return_sale->total) * $inv->insurance_business / 100) : (abs($inv->total - $inv->total_discount) * $inv->insurance_business / 100)) - ($advance_payment_discount) - ($advance_payment_discount_tax)),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Net Payable

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Net Payable

                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney((($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance) + ($return_sale->paid + $return_sale->discount_allowance) : ($inv->paid + $inv->discount_allowance))) - ($return_sale ? (($inv->total + $return_sale->total) * $inv->insurance_business / 100) : (abs($inv->total - $inv->total_discount) * $inv->insurance_business / 100)) - ($advance_payment_discount) - ($advance_payment_discount_tax)),$currencyCode); ?></td>


                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">المستحق صرفه

                        </td>

					    <?php }
                    ?>
                    </tr>
                        <?php
                    } ?>


                    </tfoot>
                </table>
            	</p>

                    </div>
                    </div>















                    <div class="row no-print">
            		<div class="col-xs-12 pull-left text-center" style="margin-top:5px;border-top:2px solid #bb0e1f;">
                          <!-- <?= $biller->invoice_footer ? '<p class="text-center notorder"><center>' . $this->sma->decode_html($biller->invoice_footer) . '</center></p>' : ''; ?>  -->
                          <?php if (!empty($warehouse->cr_no)) { ?>
                        <b> CR No : <?= $warehouse->cr_no ?  $warehouse->cr_no. ';'.'</b>' : '' ; ?><b> <?= $warehouse->branch_nameEn ? $warehouse->branch_nameEn : $Settings->site_ename.'</b></br>'; ?></b>
                    <?php } ?>
                           <?php
                           $address=$warehouse->addressEn ? $warehouse->addressEn : $warehouse->address;
                           $address=str_replace("<p>","",$address);
                           $address=str_replace("</p>","",$address);
                                              echo '<b>'.$address .'. '. ($warehouse->email ? lang('Email') . ' : ' . $warehouse->email . '<b>'  : '');


                            ?>
					</div>
				</div>









     </div><!-- end pr_pdf -->



    </div><!-- end modal-body -->

   </div><!-- end modal-countent -->

   </td>
			</tr>
		</tbody>
		<tfoot  class="tfoot-footer">
			<tr>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>

  </div><!-- end modal-dialog  -->



	<div class="report-footer" style="padding-right:15px !important;padding-left:15px !important;">
    <div class="row footer7 inv-details">

<div class="col-xs-12" >
                  <table   style="border:solid windowtext 1.5pt !important;"
                  class="table1 tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side" dir="rtl">

                      <tfoot style="-webkit-print-color-adjust:exact !important; ">
                      <tr style="background:none !important;vertical-align:middle;">
                          <td class=" td col-xs-2 text-center" rowspan="6" style=" background:none !important;">
                           <?php
                            $wh_name=$warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name;
                           ?>

                                        <?php
						        $QR=$this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2));
						    ?>
						    <?= $invQR ? $this->sma->qrcode('', $invQR, 2) : ($warehouse->vat_reg_no ? $this->sma->qrcode('', $QR, 2) : '') ?>

                           </td>
                          <td class=" td col-xs-2 text-center"  style="">Received&nbsp;By&nbsp;/&nbsp;مستلم&nbsp;الفاتورة</td>
                          <td class=" td col-xs-2 text-center" > Authorized&nbsp;Signatory </td>
                          <td class=" td col-xs-6 text-center"colspan="3" > Bank&nbsp;Details&nbsp;/&nbsp;التفاصيل&nbsp;المصرفية</td>

                      </tr>
                      <tr style="background:none !important;vertical-align:middle;">
                          <td class=" td col-xs-2 text-center" rowspan="5" style="background:none !important;"></td>
                          <td class=" td col-xs-2 text-left" rowspan="5" style="background:none !important;"> </td>

                          <td class=" td col-xs-4 text-left"style="background:none !important;" ><?= $Settings->site_name; ?>
                          </br><?=  $Settings->account_name; ?></td>
                          <td class=" td col-xs-2 text-left" style="background:none !important;" >ACCOUNT NAME </br> اسم الحساب</td>

                      </tr>
                      <tr style="background:none !important;vertical-align:middle;">

                          <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->IBAN; ?></td>
                          <td class=" td col-xs-2 text-left" style="background:none !important;" >IBAN </br> آيب</td>
                      </tr>
                      <tr style="background:none !important;vertical-align:middle;">

                          <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->bank_name; ?></td>
                          <td class=" td col-xs-2 text-left" style="background:none !important;" >BANK</br>بنك</td>
                      </tr>
                      <tr style="background:none !important;vertical-align:middle;">

                          <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->branch_name; ?></br>
                          <?= $warehouse->branch_nameEn ?></td>
                          <td class=" td col-xs-2 text-left" style="background:none !important;" >BRANCH </br> فرع</td>
                      </tr>
                      <tr style="background:none !important;vertical-align:middle;">

                            <td class=" td col-xs-4 text-left"style="background:none !important;" ><?=  $Settings->swift_code; ?></td>
                            <td class=" td col-xs-2 text-left" style="background:none !important;" >Swift Code </br> سويفت</td>
                        </tr>


                  </tfoot>

  </table>

</div>

<div class="col-xs-12">
                  <div class="col-xs-12 pull-left text-center" style="border-top:2px solid #bb0e1f;">
                         <?php if (!empty($warehouse->cr_no)) { ?>
                      <b> CR No : <?= $warehouse->cr_no ?  $warehouse->cr_no. ';'.'</b>' : '' ; ?><b> <?= $warehouse->branch_nameEn ? $warehouse->branch_nameEn : $Settings->site_ename.'</br>'; ?></b>
                  <?php } ?>
                         <?php
                         $address=$warehouse->addressEn ? $warehouse->addressEn : $warehouse->address;
                         $address=str_replace("<p>","",$address);
                         $address=str_replace("</p>","",$address);
                                            echo '<b>'.$address .'. '. ($warehouse->email ? lang('Email') . ' : ' . $warehouse->email . '<b>'  : '');


                          ?>
                  </div>
              </div>

</div>
	</div>










    <div class="report-header">
    <div class="row ">





    <div class="col-xs-12 fixRight" style="border-bottom:2px solid #000 !important;">
                     <div class="col-xs-7 right-side">
                     <b style="border-bottom:1px solid #000; font-size:18px;margin-top:1px !important;"> <?= $Settings->site_name; ?></b><br/>
                     <b style="color:#bb0e1f !important;font-size:18px;margin:1px !important;"> <?= $Settings->site_ename; ?></b><br/>

                    </div>


		           <div class="col-xs-5 text-left" dir="ltr">
                   <?php
			            //if ($logo)
			            {
							$logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;
                             ?>





                    <div class=" order_barcodes " >
					    <img style="padding-top:5px !important;max-width:270px;" src="<?= base_url() . 'assets/uploads/' . $logo; ?>"
                         alt="<?= $Settings->site_name; ?>"><br/>


                    </div>
                     <?php
			            //if ($logo)
			            }
                            ?>
                   </div>
    </div>
    <div class="col-xs-12 fixRight" style=" margin-top:1px;">
                     <div class="col-xs-3" style="margin-top: 20px;margin-bottom: 1px;">
                     <b style="color:#bb0e1f !important;font-size:16px;">رقم&nbsp;الفاتورة : </b>  <b style="font-size:16px;color:#bb0e1f !important;"><?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".($inv->sale_no ? $inv->sale_no : $inv->id) : ($inv->sale_no ? $inv->sale_no : $inv->id); ?></b>  </br>
                       </div>
                <div  class="col-xs-6 text-center bold">


					<?php
                    if($customer->co_type==2){// || abs($inv->grand_total)>=1000){
                       ?>
                      <h1  style="border:solid windowtext 1.5pt !important;border-radius:10px;font-size:18px;color:blue !important;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;مبيعات&nbsp;/&nbsp;Sale&nbsp;Invoice</h1>
                   <?php
                   }else if($customer->vat_no!=""){// || abs($inv->grand_total)>=1000){
					?>
				    <h1  style="border:solid windowtext 1.5pt !important;border-radius:10px;font-size:18px;color:blue !important;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;ضريبية&nbsp;/&nbsp;Tax&nbsp;Invoice</h1>
					<?php
					}else{
					?>
				      <h1  style="border:solid windowtext 1.5pt !important;border-radius:10px;font-size:16px;color:blue !important;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;ضريبية&nbsp;مبسطة&nbsp;/&nbsp;Simplified&nbsp;Tax&nbsp;Invoice</h1>
					<?php
					}
					?>
                 </div>

		       <div class="col-xs-3 text-left"style="margin-top: 20px;margin-bottom: 1px;">
                   <b style="color:#bb0e1f !important;font-size:16px;">Invoice&nbsp;No : </b>  <b style="font-size:16px; color:#bb0e1f !important;" ><?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".($inv->sale_no ? $inv->sale_no : $inv->id) : ($inv->sale_no ? $inv->sale_no : $inv->id); ?></b>  </br>
               </div>

            </div>

</div>
	</div>



<script type="text/javascript">
    $(document).ready( function() {
        //$('.tip').tooltip();
        $( "html" ).scrollTop( 0 );
    });

        $('#pdf').click(function (event) {
            event.preventDefault();
            PrPDF('pr_pdf','<?= lang("sales"). ($inv->sale_no ? $inv->sale_no : $inv->id); ?>');
		    return false;
        });

</script>
