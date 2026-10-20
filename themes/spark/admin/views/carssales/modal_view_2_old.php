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
.right-side{
    direction: rtl;
    text-align: -webkit-right;
}



</style>
<?php
		if ($Settings->user_language == 'arabic') {
			?>
			<style>

.modal-body{
    direction: rtl;
    text-align: -webkit-right;
}
</style>


			<?php

		}else{

			?>
			<style>

.modal-body{
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

    table { page-break-inside:auto }
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
           <button type="button"
            <a href="#" id="pdf" class="btn btn-xs btn-default no-print" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">

                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>

                            </a>

            </button>
			</div>
           </div>
	<div id="pr_pdf">
		   <div class="row bold left-side"  style="border:solid windowtext 1.5pt !important;border-radius:15px;padding:0in 0in 1.0pt 0in;margin-left:0in;margin-right:0in;">

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







                 <div class="col-xs-4 left-side" >

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


			      </div>

              </div>
			</div>



			        <!--<div class="row">

		    	        <div class="col-xs-12">




                                    <div class="well well-sm">
                                        <div class="row bold"  >
                                            <div class="col-xs-4">
                                            <p class="bold">
                                                <?= lang('Inv_date'); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                                                <?= lang('Invoice_No'); ?>: <?= $inv->id; ?><br>
                                                <?= lang('reference_no'); ?>: <?= $inv->reference_no; ?><br>
						                        <?php if (!empty($inv->supply_date)) { ?>
                                                <?= lang('Supply_date'); ?>: <?= $this->sma->hrld($inv->supply_date); ?><br>
						                        <?php } ?>
                                                <?php if (!empty($inv->return_sale_ref)) {
                                echo lang('return_ref') . ': ' . $inv->return_sale_ref;
                                if ($inv->return_id) {
                                    echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
                                } else {
                                    echo '<br>';
                                }
                            } ?>
                                                <?= lang('sale_status'); ?>: <?= lang($inv->sale_status); ?><br>
                                                <?= lang('payment_status'); ?>: <?= lang($inv->payment_status); ?>

                                                <?php
						                        /*
						                        if ($inv->payment_status != 'paid') {
                                echo '<br>' . lang('due_date') . ': ' . $this->sma->hrsd($inv->due_date);

                            }
	                        */
	                        ?>
                                            </p>
                                            </div>
					                        <?php
					                        $company = $this->session->userdata('company');
					                        ?>
                                            <div class="col-xs-8 text-right order_barcodes">
                                                <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                                                <?= $this->sma->qrcode('link', urlencode(site_url('SalesDetails/view/' . $company . '/' . $inv->hash )), 2); ?>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>-->

            <div class="row" style="margin-bottom:15px;margin-top:20px;border-bottom:solid windowtext 1.5pt !important;">

                <?php
				    //if ($Settings->invoice_view == 1)
				    {
                           ?>
                    <div class="col-xs-3 right-side" >
                     <b> الرقم الضريبي : <?= $warehouse->vat_reg_no ; ?></b></br>

                    </div>
                    <div class="col-xs-6 text-center">
                     <!--   <h1 style="min-height: 20px;padding: 10px;margin-top: -5px;background-color: #f5f5f5;border: 1px solid #e3e3e3;border-radius: 20px;-webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%);    box-shadow: inset 0 1px 1px rgb(0 0 0 / 5%); font-size: 25px;">Tax Invoice <br>فاتوره ضريبية</h1>
					    -->
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
                    <div class="col-xs-3 left-side">
                        <b> VAT No : <?= $warehouse->vat_reg_no ; ?></b></br>

                      </div>
                <?php
                    } ?>

            </div>



            <div class="row" style="margin-bottom:15px;margin-top:15px;border-bottom:solid windowtext 1.5pt !important;">
                    <div class="col-xs-6 right-side" >
                     <!--<b>:رقم الفاتورة Tax Invoice: <?= $inv->id; ?> </b></br>-->
                      <b style="color:red !important;"><?= lang('رقم الفاتورة'); ?></b>  <b><?= $inv->id; ?></b>  <b style="color:red!important;"><?= lang('Tax Invoice'); ?></b></br></br>


                    </div>


                    <div class="col-xs-6  left-side dir="rtl"">
                          <b style="color:red !important;">Invoice Date </b> <b style="color:black !important;"> <?= $this->sma->hrld($inv->date) ?></b>  <b style="color:red !important;"><?= lang('تاريخ الفاتورة'); ?></b></br></br>

                    </div>

            </div>

            <div class="row" style="margin-bottom:15px;margin-top:15px;">


                         <div class="col-xs-8 " >
                                <div class="row" >
                                    <div class="col-xs-6 pull-right" >
                                     <p >تفاصيل العميل </p>
                                     </div>
                                   <div class="col-xs-6 text-left" >
                                     <p >Customer Details </p>
                                     </div>
                                </div>


                               <div class="col-xs-12 back-color bord" dir="rtl" style="border-radius:20px; margin-bottom:15px;">
                                   <div class="col-xs-6" dir="rtl" style="margin-bottom:15px;margin-top:15px;">
                                         <b> الاسم : <?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></b><br/>
                                         <?php echo " </br>"; ?>
                                         <?php echo " </br>"; ?>
                                       <b> الرقم الضريبي: <?= $customer->vat_no ; ?></b><br/>
                                        <?php

                                        ?>
                                   </div>


				                    <div class="col-xs-6  text-left" dir="ltr" style="margin-bottom:15px;margin-top:15px;">
                                       <b > <?= 'Name : '.$customer->company ?></b><br/>
                                         <?php echo " </br>"; ?>
                                         <?php echo " </br>"; ?>
                                       <b> VAT No:<?= $customer->vat_no ; ?></b><br/>
                                        <?php

                                        ?>
                                    </div>

                               </div>

                          </div>


                        <div class="col-xs-4 text-center">
                             <div class="text-left order_barcodes" style="padding:10px;">


                                     <?= $this->sma->qrcode('', $this->sma->barcodeZATCA($Settings->site_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2)), 2); ?>

						    <!--<img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg"/>
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
    <div class="table-responsive" >
                 <table class="table table-striped jambo_table bulk_action print-table order-table  " >


                    <thead >

                  <tr style="-webkit-print-color-adjust:exact;">

                        <th><?= lang('No_sales'); ?></th>
                        <!--<th><?= lang('Product_code_sales'); ?></th>-->
                        <th class="col-xs-8"><?=  $Settings->IsServices ? lang('Service_name_sales') : lang('Product_name_sales'); ?></th>
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
                            echo '<th >' . lang('vat_amount_sales') . '</th>';
                        }
						}

                        ?>
                        <th class="col-xs-2"><?= lang('Total_price_sales'); ?></th>
                    </tr>

                    </thead>

                    <tbody ">

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
                            <!--<td style="vertical-align:middle;">
                                <?= $row->product_code ?>
                            </td>-->
							<td style="vertical-align:middle;">
							<?php
							if($row->promo_free==1){
								?>
							<img src="<?= base_url() . 'assets/images/free.jpg' ?>" width="10%">
							<?php
							}
							?>
                                <?=  lang('code'). ':' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= $row->cf1 ? '<br>' . $row->cf1. ' ' .$row->cf2. ' ' .$row->cf3 : ''; ?>
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

                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity) . ' ' . ($inv->sale_status == 'returned' ? $base_unit_code : ''); ?></td>

							<td style="text-align:center; width:100px;">
                                <!--<?= $row->unit_price != $row->real_unit_price && $row->item_discount > 0 ? '<del>' . $this->sma->formatMoney($row->real_unit_price) . '</del>' : ''; ?>
                                -->
								<?= $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4),' '); ?>
                            </td>
							<!--<td><?=($this->sma->formatMoney($row->real_unit_price * $row->unit_quantity))?></td>-->
                            <?php
							if($inv->order_type==1){
							//if ($Settings->product_discount && $inv->product_discount != 0) {
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                echo '<td style="width: 100px; text-align:center; vertical-align:middle;">' .  $this->sma->formatMoney($row->item_discount,' ') . '</td>';
                            //}
							// echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->net_unit_price * $row->quantity). '</td>';


							if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $row->tax . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:center; vertical-align:middle;">' . $this->sma->formatMoney($row->item_tax,' ') . '</td>';
                            }
                            echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' . $this->sma->formatMoney($row->subtotal,' ') . '</td>';

							}else{
							    echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' . $this->sma->formatMoney((($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4)) * $this->sma->formatQuantity($row->unit_quantity)),' ') . '</td>';
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
                                <!--<td style="vertical-align:middle;">
                                    <?= $row->product_code; ?>
                                </td>-->
                                <td style="vertical-align:middle;">
                                    <?= lang('code'). ':' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
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

            <div class="row inv-details" >

					<div class="col-xs-12" >




                        <p>
                            <table class="table table-striped jambo_table bulk_action print-table order-table " >
				        <tfoot style="-webkit-print-color-adjust:exact !important;">

	               <tr >


                        <?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي المبلغ

                        </td>

                        <td class="col-xs-2 td"style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Sub Total

                        </td>
                        <td class="col-xs-6 td " rowspan="2"
                            style=" text-align:right;">

                               <!-- <?php echo $this->lang->line(' ملاحظات'); ?><?php echo $this->lang->line(' /Note'); ?><br> -->
                                <b style="font-weight:bold;">ملاحظات / Note  </b></br>
                                <?= $this->sma->decode_html($inv->note); ?>


                        </td>
						<?php }else{ ?>

                         <td class="col-xs-6 td " rowspan="2"
                            style=" text-align:left;">



                                <b style="font-weight:bold;"> ملاحظات/Note   </b></br>

                                <?= $note; ?>


                        </td>
                        <td class="col-xs-2 td"
                            style=" text-align:right;font-weight:bold;">Sub Total
                            </td>
                        <td class="col-xs-2" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">اجمالي المبلغ

                        </td>

					    <?php } ?>



                        </tr>

                        <tr>

                        	   <?php if ($Settings->user_language == 'arabic') {
						?>




                          <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;" >اجمالي الخصم

                        </td>

                        <td class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Total Discounts

                        </td>
                        <?php }else{ ?>

                        <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">Total Discounts

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">إجمالي الخصم

                        </td>

					    <?php } ?>



				<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('discount_total'); ?>

                        </td>

                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                    -->
					</tr>


                      <tr>



						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي الضريبة

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Total VAT

                        </td>
                        <td class="col-xs-6 td" rowspan="2"
                            style=" text-align:right;font-weight:bold;">
                                 <font style="float:right;"><?= $this->sma->AmountInWordsAr($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'هللة'); ?></font> </br>
							    <font style="float:left;"><?= $this->sma->AmountInWordsEn($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'Halala'); ?></font>


						</td>
						<?php }else{ ?>

                        <td class="col-xs-6 td" rowspan="2"
                            style=" text-align:left;font-weight:bold;">
                                <font style="float:right;"><?= $this->sma->AmountInWordsAr($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'هللة'); ?></font> </br>
							    <font style="float:left;"><?= $this->sma->AmountInWordsEn($this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)),$currencyCode,$currencyName,'Halala'); ?></font>

                        </td>
                        <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">Total VAT

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">اجمالي الضريبة

                        </td>

					    <?php } ?>
					   <!--

						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('VAT'); ?>

                        </td>

                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
						-->
                    </tr>
						<tr>



						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي الفاتورة

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">Total Invoice

                        </td>
                        <?php }else{ ?>
						<td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">Total Invoice

                        </td>

                        <td  class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>


                        <td class="col-xs-2 td"
                            style=" text-align:left;font-weight:bold;">اجمالي الفاتورة

                        </td>

					    <?php } ?>

						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('total_amount_Tax_Invoice'); ?>

                        </td>

                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>
						-->
                    </tr>


                    </tfoot>
                </table>
						</p>
             <div class="clearfix"></div>
            </div>

           </div>






</div>









                <div class="clearfix"></div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;text-align:right;font-weight:bold;">
                    <p>&nbsp;Signature /&nbsp;التوقيع&nbsp; <br>  </p>
                </div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;text-align:center;font-weight:bold;">
                        <p>Recived&nbsp;By /&nbsp;استلمت&nbsp;من&nbsp;قبل&nbsp; <br>  </p>
                </div>
				<div class="col-xs-4 " style="padding-bottom:10px;padding-top:10px;text-align:left;font-weight:bold;">
                    <p>Prepared&nbsp;By / &nbsp;اعدت&nbsp;بواسطة <br>  </p>
                </div>


				<div class="clearfix"></div>

            		<div class="col-xs-12 pull-left">
                           <?= $biller->invoice_footer ? '<p class="text-center notorder"><center>' . $this->sma->decode_html($biller->invoice_footer) . '</center></p>' : ''; ?>
					</div>







<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });

        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('sales'. $inv->id); ?>');
		    return false;
        });

</script>
