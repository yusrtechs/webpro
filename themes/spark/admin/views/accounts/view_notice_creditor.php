<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

				<?php
			if(isset($receipt->currency_code)){
				$currencyCode= $receipt->currency_code;
				$currencyRate= $receipt->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

$currencyName=$this->site->getCurrencyByCode($currencyCode)->name;

			?>

<style>
.right-side{
    direction: rtl;
    text-align: -webkit-right;
}



</style>
<style>
.left-side{
    direction: ltr;
    text-align: -webkit-left;
}
.qrimg{
	padding-bottom:20px;
}

.back-color{

    -webkit-print-color-adjust:exact !important;
}
.table > thead:first-child > tr, .table thead th {

    border:none !important;
    }
.table > tfoot:first-child > tr, .table tfoot td {



    }

    .table-striped>tbody>tr:nth-child(odd)>td, .table-striped>tbody>tr:nth-child(odd)>th {
    background-color:#fff;

    }
.bord{
   /* border:solid windowtext 1.5pt !important;
   box-sizing: border-box;*/
    border:2px solid #000 !important;

}
body{
    -webkit-print-color-adjust: exact;
}

    table { page-break-inside:auto;margin-bottom:1px !important; }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt;border: 1px solid #ddd; }
    td {
    }

@media print {


	.modal-content th,td {
  font-size: 10px;
}
                /*h1 {padding:1px;}*/
				.well { margin-bottom:-2px;padding:1px;}
				.well1 {margin-top:-15px;padding:1px;}

				.table td, .table th {
				padding: 0.2rem !important;
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

    -webkit-print-color-adjust:exact !important;
}

  body {
  position: absolute;
     -webkit-print-color-adjust: exact;

  }


      table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt; }


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
</style>


			<?php

		}else{

			?>
			<style>

.fixRight {
    direction: rtl;
    text-align: -webkit-right;
}

.col-md-55, .col-xs-12, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-sm-12, .col-md-12, .col-lg-12 {
    float: right;
    }
</style>


			<?php

		}
		?>
<div class="modal-dialog modal-lg no-modal-header ">
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
               <button type="button" <a href="#" id="pdf" class="btn btn-xs btn-default no-print" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">

                                    <i class="fa fa-download"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>

                                </a>

                </button>
			</div>
         </div>
	<div id="pr_pdf">
            <div class="row fixRight" style="border-bottom:2px solid #000 !important;">
                     <div class="col-xs-3">
                       </div>
                <div  class="col-xs-6 text-center bold"style="margin-top:1px;padding-right:1px;padding-left:1px;">

                     <b valign="top"><h1  style="font-size:20px; color:#007bff !important;margin-top:1px;margin-bottom: 1px;">إشعار&nbsp;دائن&nbsp;/&nbsp;Credit&nbsp;Note</h1></b>

                    </div>

		           <div class="col-xs-3">
                   </div>

                    <div   class="col-xs-5 right-side" style="margin-top:1px;">
                      <b style="color:red !important;margin-top:2px;">رقم&nbsp;السند</b>  <b><?= $receipt->bond_no; ?></b>  <font dir="ltr"><b style="color:red!important;">Bond&nbsp;No</b></font></br>
                      <b style="color:red !important;">الرقم&nbsp;المرجعي</b>&nbsp;<b><?= $receipt->reference_no ? $receipt->reference_no : $receipt->bond_no; ?></b>&nbsp;<b dir="rtl" style="color:red!important;">Reference</b></br>


                    </div>
                    <div class="col-xs-2">
                    </div>


                    <div class="col-xs-5  text-left" dir="ltr"  style="margin-top:1px;padding-right:1px;">
                          <b style="color:red !important;">Bond&nbsp;Date</b> <b style="color:black !important;"> <?= $this->sma->hrld($receipt->date) ?></b>  <b style="color:red !important;">تاريخ&nbsp;السند</b></br>

                    </div>

            </div>

            <div class="well-sm " style="padding:5px;" >
            </div>

		   <div class="row bold left-side fixRight"  style="border:solid windowtext 1.5pt !important;border-radius:15px;padding:0in 0in 1.0pt 0in;margin-left:0in;margin-right:0in;">

		   <div class="col-xs-12">

               <div class="col-xs-4 right-side">


                    <b> <?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></b><br/>
                    <!--
					<h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>

					<?= $warehouse->name ?>-->

                    <?php

                     echo ($warehouse->phone ? lang('تلفون') . ' : ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('ايميل') . ' : ' . $warehouse->email . '<br>'  : '');
                    $address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address ;
                      /*      echo $warehouse->city . ' ' . $warehouse->postal_code . '<br>'. ' ' . $warehouse->state . ' ' . $warehouse->country . '<br>'; */

                    ?>
                    <b> الرقم الضريبي : <?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?>
                    <b> السجل التجاري : <?= $warehouse->cr_no ?  $warehouse->cr_no. '</b><br>' : '</b>' ; ?>




			    </div>

                <div class="col-xs-4 text-center">


                        <?php
			            //if ($logo)
			            {
                             ?>





                    <div class=" order_barcodes " >
					    <img style="padding-top:20px !important;" src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>"><br/>


                    </div>
                     <?php
			            //if ($logo)
			            }
                            ?>
            </div>







                 <div class="col-xs-4 text-left" dir="ltr" >

                    <b> <?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_ename; ?></b><br/>
                    <!--
					<h2 style="margin-top:10px;"><?= $Settings->site_ename; ?></h2>

					<?= $warehouse->name ?>-->

                    <?php

                    echo ($warehouse->phone ? lang('Tel') . ' : ' . $warehouse->phone . '<br>'  :  '') . ($warehouse->email ? lang('Email') . ' : ' . $warehouse->email . '<br>'  : '');
                    $address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address ;
                  /*  echo $warehouse->city . ' ' . $warehouse->postal_code . '<br>'. ' ' . $warehouse->state . ' ' . $warehouse->country . '<br>';*/


                    ?>
                    <b> VAT No : <?= $warehouse->vat_reg_no ?  $warehouse->vat_reg_no. '</b><br>' : '</b>' ; ?>
                    <b> CR No : <?= $warehouse->cr_no ?  $warehouse->cr_no. '</b><br>' : '</b>' ; ?>

			      </div>

              </div>
			</div>



			        <!--<div class="row">

		    	        <div class="col-xs-12">




                                    <div class="well well-sm">
                                        <div class="row bold"  >
                                            <div class="col-xs-4">
                                            <p class="bold">
                                                <?= lang('Inv_date'); ?>: <?= $this->sma->hrld($receipt->date); ?><br>
                                                <?= lang('Invoice_No'); ?>: <?= ($receipt->sale_no ? $receipt->sale_no : $receipt->id); ?><br>
                                                <?= lang('reference_no'); ?>: <?= $receipt->reference_no; ?><br>
						                        <?php if (!empty($receipt->supply_date)) { ?>
                                                <?= lang('Supply_date'); ?>: <?= $this->sma->hrld($receipt->supply_date); ?><br>
						                        <?php } ?>
                                                <?php if (!empty($receipt->return_sale_ref)) {
                                echo lang('return_ref') . ': ' . $receipt->return_sale_ref;
                                if ($receipt->return_id) {
                                    echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $receipt->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
                                } else {
                                    echo '<br>';
                                }
                            } ?>
                                                <?= lang('sale_status'); ?>: <?= lang($receipt->sale_status); ?><br>
                                                <?= lang('payment_status'); ?>: <?= lang($receipt->payment_status); ?>

                                                <?php
						                        /*
						                        if ($receipt->payment_status != 'paid') {
                                echo '<br>' . lang('due_date') . ': ' . $this->sma->hrsd($receipt->due_date);

                            }
	                        */
	                        ?>
                                            </p>
                                            </div>
					                        <?php
					                        $company = $this->session->userdata('company');
					                        ?>
                                            <div class="col-xs-8 text-right order_barcodes">
                                                <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($receipt->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $receipt->reference_no; ?>" class="bcimg" />
                                                <?= $this->sma->qrcode('link', urlencode(site_url('SalesDetails/view/' . $company . '/' . $receipt->hash )), 2); ?>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>-->

         <!--   <div class="row" style="margin-bottom:15px;margin-top:10px;border-bottom:solid windowtext 1.5pt !important;">

                <?php

				    {
                           ?>
                    <div class="col-xs-2 right-side" >
                     <b> الرقم الضريبي : <?= $warehouse->vat_reg_no ; ?></b></br>

                    </div>

                    <div class="col-xs-8 text-center">

                     <?php
					if($customer->vat_no!=""){
					?>
				    <b valign="top"><h1  style=" font-size: 20px;color:#007bff !important;">فاتورة&nbsp;ضريبية&nbsp;/&nbsp;TAX&nbsp;INVOICE </h1></b></br>
					<?php
					}else{
					?>
				    <b valign="top"><h1  style=" font-size: 20px;color:#007bff !important;"> فاتورة&nbsp;ضريبية&nbsp;مبسطة&nbsp;/&nbsp;SIMPLIFIDE&nbsp;TAX&nbsp;INVOICE</h1></b></br>
					<?php
					}
					?>
                    </div>
                    <div class="col-xs-2 left-side">
                       <b> VAT No : <?= $warehouse->vat_reg_no ; ?></b></br>

                      </div>

                <?php
                    } ?>

            </div>

            -->



            <div class="row fixRight" style="margin-bottom:1px;margin-top:5px;">


                         <div class="col-xs-9 " >
                                <div class="row" >
                                    <div class="col-xs-5 pull-right" >
                                     <p >تفاصيل العميل </p>
                                     </div>
                                     <div class="col-xs-2" >

                                     </div>

                                   <div class="col-xs-5 text-left" >
                                     <p >Customer Details </p>
                                     </div>
                                </div>


                                <div class="col-xs-12 back-color bord" dir="rtl" style="border-radius:10px; margin-bottom:1px;padding:5px;">
                                   <div class="col-xs-6" dir="rtl" style="padding:1px;">

                                         <b> الاسم : <?= $toAccountID->accountName ?></b></br>
                                        <!-- <?php echo " </br>"; ?>
                                         <?php echo " </br>"; ?>
                                       <b> الرقم الضريبي: <?= $customer->vat_no ; ?></b><br/> -->
                                        <?php

                                        if($customer->vat_no!="")


                                               echo ($customer->vat_no ? lang('الرقم الضريبي') . ' : ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ? lang('السجل التجاري') . ' : ' . $customer->cr_no . '<br>' : '<br>') ;
					                        ?>
					                        <?php if ($Settings->IsConstruction && (!empty($customer->cf1) || !empty($receipt->po_no) )) { ?>
					                        <?php if ($Settings->user_language == 'arabic') {
						                        ?>

                                               <font dir="ltr"> <?= $receipt->po_no ? $receipt->po_no : $customer->cf1; ?> : <?= lang('PO_Number'); ?></font><br>
						                        <?php }else{ ?>
                    	                        <font > <?= lang('PO&nbsp;رقم'); ?>:<?= $receipt->po_no ? $receipt->po_no : $customer->cf1; ?></font><br>
						                        <?php } ?>
                    	                        <?php } ?>
                                                <?php
                                                if($customer->phone!=""){
                                                     echo lang('تلفون ') . ' : ' . $customer->phone . '<br>';
                                                    }
                                                 if($customer->email!=""){
                                                    echo lang('ايميل ') . ' : ' . $customer->email . '<br>';
                                                   }
                                                   if($customer->address!=""){
                                                    echo  $customer->address . '<br>';
                                                   }

                                              echo $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . ' ' . $customer->country;
                                            ?>
                                     </div>

                                   <!--  <div class="col-xs-2" >
                                     </div> -->

				                    <div class="col-xs-6  text-left" dir="ltr" style="padding:1px;">
                                       <b > <?= 'Name : ' ?> <?= (($toAccountID->accountNameEn=="") ? $toAccountID->accountName : $toAccountID->accountNameEn) ?></b><br/>
                                           <!-- <?php echo " </br>"; ?>
                                         <?php echo " </br>"; ?>
                                       <b> VAT No:<?= $customer->vat_no ; ?></b><br/>-->
                                         <?php

                                            if($customer->vat_no!="")


                                               echo ($customer->vat_no ? lang('VAT No') . ' : ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ? lang('CR Number') . ' : ' . $customer->cr_no . '<br>' : '<br>') ;
					                        ?>
					                        <?php if ($Settings->IsConstruction && (!empty($customer->cf1) || !empty($receipt->po_no) )) { ?>
					                        <?php if ($Settings->user_language == 'arabic') {
						                        ?>

                                               <font dir="ltr"><?= lang('PO Number'); ?>:<?= $receipt->po_no ? $receipt->po_no : $customer->cf1; ?> </font><br>
						                        <?php }else{ ?>
                    	                        <font> <?= lang('PO Number'); ?>:<?= $receipt->po_no ? $receipt->po_no : $customer->cf1; ?></font><br>
						                        <?php } ?>
                    	                        <?php } ?>
                                                <?php
                                                if($customer->phone!=""){
                                                     echo lang('tel ') . ' : ' . $customer->phone . '<br>';
                                                    }
                                                 if($customer->email!=""){
                                                    echo lang('email ') . ' : ' . $customer->email . '<br>';
                                                   }
                                                   if($customer->addressEn!="" || $customer->address!=""){
                                                    $address= $customer->addressEn ? $customer->addressEn : $customer->address ;
                                                    echo $address . '<br>';
                                                   }

                                                   echo $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . ' ' . $customer->country;
                                            ?>
                                    </div>

                               </div>
                                <?php if ($Settings->IsServices) {
                                    ?>
                                        <div class="col-xs-12  " dir="rtl" style=" margin-bottom:1px;padding:5px;">
                                        <table class="table bord  table-striped"   >
                                        <tr class="  " >
                                            <th class=" bordl "  style="width:25%;text-align:center; padding:2px;"><?= $this->sma->hrld($receipt->exit_date); ?></th>
                                            <th class="  bordl back-color"  style="width:25%;text-align:center;padding:2px;"><?= lang('Exit_date'); ?> </th>
                                            <th class="bordl   " style="width:25%;text-align:center;padding:2px;"><?= $receipt->plate_no; ?></th>
                                            <th class="  back-color"  style="width:25%;text-align:center;padding:2px;"><?= lang('Plate_Number'); ?></th>
                                        </tr>
                                        </table>

                                        </div>
                                    <?php
                                    } ?>


                          </div>

                     <!--   <div class="col-xs-3 text-center order_barcodes" style="padding:20px 1px 1px 1px;"> -->
                        <div class="col-xs-3 order_barcodes" style="padding-right:1px;">
                             <div class=" order_barcodes" style="padding:10px;margin-top:10px;">

							 <?php
							  $wh_name=$warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name;
							 ?>


                            <?= $this->sma->qrcode('', $this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($receipt->date),$this->sma->formatDecimal($receipt->equivalent_total_amount,2),$this->sma->formatDecimal($receipt->equivalent_tax_amount,2)), 2); ?>

						    <!--<img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($receipt->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $receipt->reference_no; ?>" class="bcimg"/>
                             -->

                        </div>
                        </div>

            </div>




<!--
                <div class="row" style="margin-bottom:15px;margin-top:15px;">

                <div class="col-xs-6 from">
                    <b><?php echo $this->lang->line('from'); ?>: <?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></b><br/>
                    <!--
					<h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>

					<?= $warehouse->name ?>-->
<!--
                    <?php
					$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ': ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->cr_no ? lang('CR_Number') . ': ' . $warehouse->cr_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ': ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?>
                </div>


				<div class="col-xs-6 to">
                   <b> <?php echo $this->lang->line('to'); ?> :<?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></b><br/>
                    <!--<h2 style="margin-top:10px;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>-->
    <!--                <?= $customer->company                              && $customer->company != '-' ? '' : 'Attn: ' . $customer->name ?>

                    <?php
                    echo $customer->address . '<br>' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . ' ' . $customer->country . '<br>';


                    echo ($customer->vat_no ? lang('VAT_Regesteration_Number') . ': ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ? lang('CR_Number') . ': ' . $customer->cr_no . '<br>' : '<br>') . lang('tel') . ': ' . $customer->phone . '<br>' . lang('email') . ': ' . $customer->email;
                    ?>
                </div>

            </div>
-->


            <?php
                    $col = 8;
                    //if ($Settings->product_discount && $receipt->product_discount != 0) {
                    //    $col++;
                    //}
                    if ($Settings->tax1 && $receipt->product_tax > 0) {
                        $col++;
                    }
                    if ($Settings->product_discount && $receipt->product_discount != 0 && $Settings->tax1 && $receipt->product_tax > 0) {
                        $tcol = $col - 2;
                    //} elseif ($Settings->product_discount && $receipt->product_discount != 0) {
                    //    $tcol = $col - 1;
                    } elseif ($Settings->tax1 && $receipt->product_tax > 0) {
                        $tcol = $col - 1;
                    } else {
                        $tcol = $col;
                    }
                    ?>

<div style="border:2px solid #000">
    <div class="table-responsive" >
                 <table class="table table-striped jambo_table bulk_action print-table order-table pdftable " >


                    <thead >

                  <tr style="-webkit-print-color-adjust:exact;">

                  <th class="col-xs-2">كود الحساب <br/>Account Code</th>
                  <th class="col-xs-4">اسم الحساب <br/>Account Name</th>
                  <th>المبلغ <br/>Amount</th>


                        <?php


							 echo '<th >' . lang('Tax_percent_sales') . '</th>';
                            echo '<th  class="col-xs-2">' . lang('vat_amount_sales') . '</th>';



                        ?>
                        <th>الاجمالي <br/>Grand Total</th>

                    </tr>

                    </thead>

                    <tbody >


                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $fromAccountID->accountID; ?></td>

							<td style="vertical-align:middle;">



								<?= (($this->Settings->user_language == "arabic" || $fromAccountID->accountNameEn=="") ? $fromAccountID->accountName : $fromAccountID->accountNameEn) ?>

                            </td>



							<td style="text-align:right; width:100px;">
                                <?= $this->sma->formatMoney($receipt->amount,$currencyCode); ?>

							</td>
							<?php




                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($this->site->getTaxRateByID($receipt->tax_rate)->rate,' ') . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($receipt->tax_amount,$currencyCode) . '</td>';

                               echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($receipt->total_amount,$currencyCode) . '</td>';


                            ?>
                           <!-- <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal,' '); ?></td> -->
                        </tr>
                        <?php


                    if ($AdjustItems) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
                        ?>

                        <tr style="-webkit-print-color-adjust:exact;">

                        <th><?= lang('No_sales'); ?></th>
                        <th><?= lang('Product_code_sales'); ?></th>
                        <th colspan="3" class="<?= $Settings->IsCurtains ? 'col-xs-5' : 'col-xs-6' ?>"><?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? lang('Service_name_sales') : lang('Product_name_sales'); ?></th>



                        <th><?= lang('Quantity_sales'); ?></th>



                    </tr>
                        <?php
                        $r=1;
                        foreach ($AdjustItems as $row):
						$tquantity=$tquantity+$row->unit_quantity;
                        ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code; ?>
                                </td>
                                <td colspan="3" style="vertical-align:middle;font-weight:bold;">
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


                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($row->quantity)); ?></td>

                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>

                    </tbody>

                </table>
                <div class="clearfix"></div>
            </div>
			<?php
			if(isset($receipt->currency_code)){
				$currencyCode= $receipt->currency_code;
				$currencyRate= $receipt->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

			?>

			<?php
            $note= str_replace("<p>","",$this->sma->decode_html($receipt->note));
            $note= str_replace("</p>","",$this->sma->decode_html($note));
            $note= str_replace(" ","",$this->sma->decode_html($note));

            ?>


            <div class="row inv-details" >

					<div class="col-xs-12" >






                      <?php if ($Settings->user_language == 'arabic') {?>
                        <table class="table table-striped jambo_table bulk_action print-table order-table pdftable right-side" dir="rtl">
					    <?php }else{ ?>


                       <table class="table table-striped jambo_table bulk_action print-table order-table pdftable left-side" dir="rtl">
				        <?php } ?>

                        <p>
				        <tfoot style="-webkit-print-color-adjust:exact !important; ">


                        <tr style="-webkit-print-color-adjust:exact;">
						<?php if ($Settings->user_language == 'arabic') {
						?>




                        <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;"> <?=  'عدد الحسابات '; ?>

                        </td>


                        <td class="col-xs-2 td"style="text-align:center; padding-right:10px; font-weight:bold;">1</td>



                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;"><?=   'Accounts Total'; ?>

                        </td>


                           <td class="col-xs-6 td " rowspan="2"
                            style=" text-align:right;">

                                <b style="font-weight:bold;">ملاحظات&nbsp;/&nbsp;Note  </b></br>
                                <font style="float:right;"> <?= $this->sma->decode_html($receipt->note); ?> </font>


                        </td>


					    <?php }else{ ?>

                        <td class="col-xs-6 td " rowspan="2"
                            style=" text-align:left;">



                                <b style="font-weight:bold;"> ملاحظات&nbsp;/&nbsp;Note   </b></br>

                                <font style="float:left;">  <?= $this->sma->decode_html($receipt->note); ?> </font>


                        </td>






                        <td class="col-xs-2 td" style=" text-align:left;font-weight:bold;"><?=  'Accounts Total'; ?>

                        </td>








                        <td class="col-xs-2 td"style="text-align:center; padding-right:10px; font-weight:bold;">1</td>



                        <td class="col-xs-2 td"
                            style=" text-align:right;font-weight:bold;"><?=  'عدد الحسابات '; ?>

                        </td>


					    <?php } ?>

                    </tr>



	               <tr >


                        <?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي المبلغ

                        </td>

                        <td class="col-xs-2 td"style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($receipt->amount,$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Sub Total

                        </td>

						<?php }else{ ?>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Sub Total
                            </td>
                        <td class="col-xs-2" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($receipt->amount,$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:right;font-weight:bold;">اجمالي المبلغ

                        </td>

					    <?php } ?>



                        </tr>




                      <tr>

							<?php
						$VAT="";
					if($receipt->tax_amount!=0)
					$VAT="(".$this->sma->formatDecimal($active_tax,0)."%)";


					?>


						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي الضريبة <?= $VAT ?>

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($receipt->tax_amount,$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Total VAT <?= $VAT ?>

                        </td>
                        <td class="col-xs-6 td" rowspan="2"
                            style=" text-align:right;font-weight:bold;">

                               <font style="float:right;"><?= $this->sma->AmountInWordsAr($this->sma->formatDecimal(abs($receipt->total_amount)),$currencyCode,$currencyName,'هللة'); ?></font> </br>
							    <font style="float:left;"><?= $this->sma->AmountInWordsEn($this->sma->formatDecimal(abs($receipt->total_amount)),$currencyCode,$currencyName,'Halala'); ?></font>


						</td>
						<?php }else{ ?>

                        <td class="col-xs-6 td" rowspan="2"
                            style=" text-align:left;font-weight:bold;">
                                    <font style="float:right;"><?= $this->sma->AmountInWordsAr($this->sma->formatDecimal(abs($receipt->total_amount)),$currencyCode,$currencyName,'هللة'); ?></font> </br>
							    <font style="float:left;"><?= $this->sma->AmountInWordsEn($this->sma->formatDecimal(abs($receipt->total_amount)),$currencyCode,$currencyName,'Halala'); ?></font>

                        </td>
                        <td class="col-xs-2 td" style=" text-align:left;font-weight:bold;">Total VAT <?= $VAT ?>

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($receipt->tax_amount,$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:right;font-weight:bold;">اجمالي الضريبة <?= $VAT ?>

                        </td>

					    <?php } ?>
					   <!--

						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('VAT'); ?>

                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($receipt->total_tax + $return_sale->total_tax) : abs($receipt->total_tax),$currencyCode); ?></td>
						-->
                    </tr>
						<tr>



						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي السند

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;color:red;"><?= $this->sma->formatMoney( abs($receipt->total_amount),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Total Bond

                        </td>
                        <?php }else{ ?>
						<td class="col-xs-2 td" style=" text-align:left;font-weight:bold;">Total Bond

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;color:red;"><?= $this->sma->formatMoney(abs($receipt->total_amount),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:right;font-weight:bold;">اجمالي السند

                        </td>

					    <?php } ?>

						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('total_amount_Tax_Invoice'); ?>

                        </td>

                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($receipt->grand_total + $return_sale->grand_total) : abs($receipt->grand_total),$currencyCode); ?></td>
						-->
                    </tr>


                    </tfoot>
                </table>

						</p>
                </div>
                </div>

             <div class="clearfix"></div>




                </div> <!-- end  border -->











            <div class="row">
               <div class="col-xs-12 ">
                <?php if ($Settings->user_language == 'arabic') {
						?>
                <div class="col-xs-4 right-side" style="padding-bottom:10px;padding-top:10px;font-weight:bold;">
                    <p>التــوقيـــع&nbsp;</br>&nbsp;Signature  </p>
                </div>
				<div class="col-xs-4 text-center" style="padding-bottom:10px;padding-top:10px;text-align:center;font-weight:bold;">
                        <p><!--استلمت&nbsp;من&nbsp;قبل&nbsp;</br>&nbsp;Recived&nbsp;By-->   </p>
                </div>
				<div class="col-xs-4 text-left" style="padding-bottom:10px;padding-top:10px;font-weight:bold;">
                    <p>اعدت&nbsp;بواسطة&nbsp;</br>&nbsp;Prepared&nbsp;By  </p>
                </div>

        		<?php }else{ ?>
					<div class="col-xs-4 " style="padding-bottom:10px;padding-top:10px;text-align:right;font-weight:bold;display:inline-block;">
                     <p>Prepared&nbsp;By&nbsp;</br>&nbsp;اعدت&nbsp;بواسطة  </p>
                </div>

				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;text-align:center;font-weight:bold;display:inline-block;">
                        <p> <!--Recived&nbsp;By&nbsp;</br>&nbsp;استلمت&nbsp;من&nbsp;قبل-->  </p>
                </div>

                <div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;text-align:left;font-weight:bold;display:inline-block;">
                    <p>Signature&nbsp;</br>&nbsp;التوقيع  </p>
                </div>
            	<?php } ?>

                </div>
                </div>





			    <!--<div class="row">
            		<div class="col-xs-12 pull-left">
                           <?= $biller->invoice_footer ? '<p class="text-center notorder"><center>' . $this->sma->decode_html($biller->invoice_footer) . '</center></p>' : ''; ?>
					</div>
				</div>-->



     </div><!-- end pr_pdf -->
    </div><!-- end modal-body -->
   </div><!-- end modal-countent -->


  </div><!-- end modal-dialog  -->







<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });

        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('Notice_creditor'. ($receipt->id)); ?>');
		    return false;
        });

</script>
