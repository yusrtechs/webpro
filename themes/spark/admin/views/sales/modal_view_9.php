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
    border-top: solid 1px #ccc;
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
}


.back-color{
    background-color:#dbdbdb !important;
    -webkit-print-color-adjust:exact !important;
}
 .tableInv>tbody>tr>td {
    border:solid windowtext 1pt !important;
    }
.tableInv > thead:first-child > tr, .tableInv thead th {
    background:#dbdbdb !important;
    border:solid windowtext 1pt !important;

    }
.tableInv > tfoot:first-child > tr, .tableInv tfoot td {
    background:none !important;


    }

    .tableInv-striped>tbody>tr:nth-child(odd)>td, .tableInv-striped>tbody>tr:nth-child(odd)>th {
    background-color:#fff !important;

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
	.inv-details tr td    { font-size:8pt;border:solid windowtext 1.5pt !important;padding:0px !important;font-weight: bold; }
    td {
    }

@media print {

    @page {
				size: A4;
                margin-top: 0cm;
                margin-bottom: 0cm;
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


.tableInv>tbody>tr>td {
    border:solid windowtext 1pt !important;
    }
.tableInv > thead:first-child > tr, .tableInv thead th {
    background:#dbdbdb !important;
    border:solid windowtext 1pt !important;

    }
    table { page-break-inside:auto;margin-bottom:1px !important; }
      tr    { page-break-inside:avoid; page-break-after:auto }
      /*
   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }*/

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:8pt;border:solid windowtext 1.5pt !important;font-weight: bold; }
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



        <div class="row fixRight" style="">

        <div class="col-xs-12 text-center">
                        <?php
                            //if ($logo)
                            {
                                $logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;
                                ?>

                        <div class=" order_barcodes " >
                            <img style="padding-top:5px !important;padding-bottom: 10px;" src="<?= base_url() . 'assets/uploads/' . $logo; ?>"
                            alt="<?= $Settings->site_name; ?>"><br/>


                        </div>

                        <?php
                            //if ($logo)
                            }
                                ?>
        </div>
        </div>

    <div class="row inv-details "  >

<div class="col-xs-12">

    <table  class="table tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >


        <tfoot >
        <tr style="background:none !important;vertical-align:middle;">
           <td class=" td right-side" colspan="4" style="background:#eee !important;font-size:18px;font-weight:bold;border:none !important; ">رقم السجل التجاري :  <?= $warehouse->cr_no ? $warehouse->cr_no. '</b>' : '</b>' ; ?></td>
           <td class=" td left-side" colspan="4"style=" background:#eee !important; font-size:18px;font-weight:bold;text-align:left;border:none !important;"> C.R : <?= $warehouse->cr_no ? $warehouse->cr_no. '</b>' : '</b>' ; ?></td>
        </tr>

        <tr style="background:none !important;">
        <td class="td right-side" colspan="4" style="background:#eee !important;font-size:18px;font-weight:bold;border:none !important;"> <?php echo $warehouse->country . ' - ' . $warehouse->city .  ' - ' . $warehouse->district_name . '<br>'; ?></td>
        <td class="td left-side" colspan="4" style="background:#eee !important;font-size:18px;font-weight:bold;border:none !important;">
            <?= ($warehouse->countryEn ? $warehouse->countryEn : $warehouse->country) . ' - ' . ($warehouse->cityEn ? $warehouse->cityEn : $warehouse->city) .  ' - ' . ($warehouse->district_nameEn ? $warehouse->district_nameEn : $warehouse->district_name) . '<br>'; ?>
        </td>

        </tr>

        <tr style="background:none !important;">
           <td class=" td right-side" colspan="4" style="background:#eee !important;font-size:18px;font-weight:bold;color:red;border:none !important; "><?= $warehouse->vat_reg_no ?  '<b> الرقم الضريبي : '.$warehouse->vat_reg_no. '</b><br>' : '' ; ?></td>
           <td class=" td left-side" colspan="4"style=" background:#eee !important;font-size:18px;font-weight:bold;color:red;text-align:left;border:none !important;"><?= $warehouse->vat_reg_no ? '<b> VAT : '. $warehouse->vat_reg_no. '</b><br>' : '' ; ?></td>
        </tr>
       <tr style="background:none !important;">
           <td class=" td text-center" colspan="12" style="background:#eee !important;font-weight:bold;border:none !important; ">
           <?php
					if($customer->vat_no!=""){// || abs($inv->grand_total)>=1000){
					?>
				    <h1  style="font-size:22px;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;ضريبية&nbsp;/&nbsp;Tax&nbsp;Invoice</h1>
					<?php
					}else{
					?>
				      <h1  style="font-size:22px;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;ضريبية&nbsp;مبسطة&nbsp;/&nbsp;Simplified&nbsp;Tax&nbsp;Invoice</h1>
					<?php
					}
					?>
        </td>
        </tr>
        </tfoot>

    </table>
</div>
</div>


            <div class="row inv-details " style="margin-bottom:10px !important;" >

<div class="col-xs-12">
            <?php
            $note= str_replace("<p>","",$this->sma->decode_html($inv->note));
            $note= str_replace("</p>","",$this->sma->decode_html($note));
            $note= str_replace(" ","",$this->sma->decode_html($note));

            ?>
    <table style="border:solid windowtext 1.5pt !important;" class="table tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >


        <tfoot >
        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">تاريخ اصدار الفاتورة</td>
                           <td class=" col-xs-2 td text-center" style=" background:none !important;"><?= $inv->date ? $this->sma->hrld($inv->date) : "" ;?></td>
                           <td class=" col-xs-2 td text-center" style=" ;">Invoice Issue Date</td>
                           <td class="col-xs-2  td text-center" style=" ">رقم الفاتورة </td>
                           <td class=" col-xs-2 td text-center" style=" background:none !important;"><?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".($inv->sale_no ? $inv->sale_no : $inv->id) : ($inv->sale_no ? $inv->sale_no : $inv->id); ?></td>
                           <td class="col-xs-2  td left-side" style=" ">Invoice Number  </td>
                        </tr>

                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">اسم العميل</td>
                          <td class=" col-xs-4 td text-center" colspan="2"style="border-left:none !important; background:none !important;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></td>

                            <!--<td class=" col-xs-4 td text-center" colspan="2"style=" background:none !important;"><?= $customer->companyEn && $customer->companyEn != '-' ? $customer->companyEn : $customer->name;?></td>
                            -->

                           <td class=" col-xs-4 td text-center" colspan="2"style="border-right:none !important; background:none !important;"><?= $customer->companyEn && $customer->companyEn != '-' ? $customer->companyEn : $customer->nameEn;?></td>
                           <td class="col-xs-2  td left-side" style=" ">Customer Name </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">الرقم الضريبي للعميل</td>
                           <td class=" col-xs-8 td text-center" colspan="4"style=" background:none !important;"><?= $customer->vat_no ?  $customer->vat_no : '' ; ?></td>
                           <td class="col-xs-2  td left-side" style=" ">Customer VAT No </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">نوع الفاتورة</td>
                           <td class=" col-xs-8 td text-center" colspan="4"style=" background:none !important;"><?= $inv->order_type ?  $inv->order_type : '' ; ?></td>
                           <td class="col-xs-2  td left-side" style=" ">Invoice Type </td>
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">عنوان العميل</td>
                          <td class=" col-xs-4 td text-center" colspan="2"style=" border-left:none !important;background:none !important;"><?= ($customer->country ? $customer->country : '') . '    ' . ($customer->city ? $customer->city : '') .  '    ' . ($customer->district_name ? $customer->district_name : '')
                           . ($customer->street_name ? $customer->street_name : ' ') . ($customer->building_no ? $customer->building_no :'') . '<br>'; ?>

                           <td class=" col-xs-4 td text-center " colspan="2"style="border-right:none !important; background:none !important;"><?= ($customer->countryEn ? $customer->countryEn : '') . '    ' . ($customer->cityEn ? $customer->cityEn : '') .  '    ' . ($customer->district_nameEn ? $customer->district_nameEn : ' ')
                                            . ($customer->street_nameEn ? $customer->street_nameEn : ' ') . '<br>'; ?>
                           <td class="col-xs-2  td left-side" style=" ">Customer Name </td>
                        </tr>
                       <!-- <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">عنوان العميل</td>
                           <?php if ($Settings->user_language == 'arabic') { ?>
                            <td class=" col-xs-8 td text-center" colspan="4"style=" background:none !important;">
                           <?= ($customer->country ? $customer->country : '') . '  ' . ($customer->city ? $customer->city : '') .  '     ' . ($customer->district_name ? $customer->district_name : '   - ')
                           . ($customer->street_name ? $customer->street_name : ' ') . ($customer->building_no ? $customer->building_no :' ') . '<br>'; ?>
                            <?php } else{?>
                                <td class=" col-xs-8 td text-center" colspan="4"style=" background:none !important;">
                                            <?= ($customer->countryEn ? $customer->countryEn : ' ') . '  ' . ($customer->cityEn ? $customer->cityEn : ' ') .  ' - ' . ($customer->district_nameEn ? $customer->district_nameEn : '')
                                            . ($customer->street_nameEn ? $customer->street_nameEn : ' ') . ($customer->building_no ? $customer->building_no :'') . '<br>'; ?>

                            <?php  } ?>

                           <td class="col-xs-2  td left-side" style=" ">Customer Address</td>-->
                        </tr>
                        <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">ملاحظات</td>
                           <td class=" col-xs-8 td text-center" colspan="4"style=" background:none !important;"><font style="float:right;"> <?= $this->sma->decode_html($inv->note); ?> </font></td>
                           <td class="col-xs-2  td left-side" style=" ">Notes</td>


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
<div style="border:2px solid #000">
    <div class="table-responsive " >
                 <table class="table tableInv  jambo_table bulk_action print-table order-table pdftable " >


                    <thead >

                  <tr style="-webkit-print-color-adjust:exact;">

                        <th><?= lang('No'); ?></th>
                        <th><?= lang('Code '); ?></th>
                        <th class="col-xs-6"><?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? lang('Item Description') : lang('Item Description'); ?></th>
                        <th><?= lang('Unit'); ?></th>

                        <th><?= lang('QTY'); ?></th>

                        <th ><?= lang('Rate'); ?></th>
						 <!--<th><?= lang('value'); ?></th>-->
                        <?php
						if($inv->order_type==1){
						//if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th >' . lang('Discount ') . '</th>';
                        //}
						//echo '<th class="col-xs-2">' . lang('Item_price_sales') . '</th>';
                        if ($Settings->tax1 && $inv->product_tax > 0) {
							 echo '<th >' . lang('%VAT') . '</th>';
                            echo '<th >' . lang('VAT Amount') . '</th>';
                        }
						}

                        ?>
                        <th class="col-xs-2"><?= lang('Due Amount'); ?></th>
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
							<td style="vertical-align:middle;font-weight:bold;">
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

            <div class="row inv-details "  >



                    <div class="col-xs-12">

<table style="border-bottom:solid windowtext 1.5pt !important;border-buttom:solid windowtext 1.5pt !important;border-left:solid windowtext 1.5pt !important;" class="table tableInv table-striped jambo_table bulk_action print-table order-table pdftable right-side " dir="rtl" >


        <tfoot >
                 <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">الاجمالي غير شامل الضريبة</td>
                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
                           <td class=" col-xs-4 text-center" rowspan="4" style=" background:none !important;">
    						 <?php
							  $wh_name=$warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name;
							 ?>
                            <?= $warehouse->vat_reg_no ? $this->sma->qrcode('', $this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2)), 2) : ''; ?>
                             </td>

                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
                           <td class="col-xs-2  td left-side" style=" ">Total Excluding VAT </td>
                </tr>
                <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">مجموع الخصومات</td>
                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                           <td class="col-xs-2  td left-side" style=" ">Discount  </td>
                </tr>
                <!--<tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">الاجمالي الخاضع للضريبة</td>
                           <td class=" col-xs-2 td text-center" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>

                           <td class=" col-xs-2 td text-center" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
                           <td class="col-xs-2  td left-side" style=" ">Total Taxable Amount  </td>
                </tr> -->
                <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">مجموع ضريبة القيمة المضافة 15% </td>
                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
                           <td class="col-xs-2  td left-side" style=" ">Total VAT 15% </td>
                </tr>
                <tr style="background:none !important;vertical-align:middle;">
                           <td class=" col-xs-2 td right-side" style=" ">المبلغ الكلي</td>
                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>

                           <td class=" col-xs-2 td text-center fixRight2" style=" background:none !important;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>

                           <td class="col-xs-2  td left-side" style=" ">Total Amount  </td>
                </tr>
                <tr style="background:none !important;vertical-align:middle;">

           <td class=" td  col-xs-5 text-center " colspan="12" style=" background:none !important;">
           <?php if ($Settings->user_language == 'arabic') { ?>
            <font style=""><?= $this->sma->AmountInWordsAr($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'هللة'); ?></font> </td>
           <?php } else{?>
            <font style=""><?= $this->sma->AmountInWordsEn($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'Halala'); ?></font></br>

          <?php  } ?>

                </tr>



        </tfoot>

        </table>

            </div>


</div>

             <div class="clearfix"></div>


             <div class="row inv-details" style="display:none;">
                <div class="col-xs-12" >


                        <p>
                     <table style="border:solid windowtext 1.5pt !important;" class="table tableInv table-striped jambo_table bulk_action print-table order-table pdftable  right-side" >
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










                    <div class="row inv-details">

                    <div class="col-xs-12" >

                        <table style="border:none !important;" class="table tableInv table-striped jambo_table bulk_action print-table order-table pdftable  right-side" >
                        <tfoot>
                            <tr style="background:none !important;vertical-align:middle;">
                                    <td class=" col-xs-3 td right-side bold" style="border:none !important;background:none !important; ">منظم الفاتورة : </td>
                                    <td class=" col-xs-6 td text-center bold" style=" border:none !important;background:none !important;">....................................................................................................................................................................................................................................</td>
                                    <td class="col-xs-3  td left-side bold" style="border:none !important;background:none !important; ">Issued By :  </td>
                            </tr>
                            <tr style="background:none !important;vertical-align:middle;">
                                    <td class=" col-xs-3 td right-side bold" style="border:none !important;background:none !important; "> المستلم :  </td>
                                    <td class=" col-xs-6 td text-center bold" style=" border:none !important;background:none !important;">.....................................................................................................................................................................................................................................</td>
                                    <td class="col-xs-3  td left-side bold" style="border:none !important;background:none !important; ">Received By :  </td>
                            </tr>

                            </tfoot>
                        </table>

                    </div>
                    </div>











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
            PrPDF('pr_pdf','<?= lang("sales"). ($inv->sale_no ? $inv->sale_no : $inv->id); ?>');
		    return false;
        });

</script>
