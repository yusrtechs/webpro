<?php defined('BASEPATH') or exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang('quote') . ' ' . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">
    <style>
        .qrimg {
            display: inline-block;
            padding-bottom: 5px;
        }

    </style>
</head>

<body>
<div id="wrap">
    <div class="row">
        <div class="col-lg-12">
            <div class="row bold" style="border-bottom:2px solid #000;padding:0in 0in 1.0pt 0in;margin-left:0in;margin-right:-.05in;">

            <div class="padding10">

            <div class="col-xs-4 pull-right">

				            <span valign="top"><h1  style="text-align:right; font-size: 20px;"> Quotation&nbsp;/&nbsp;عرض&nbsp;سعر</h1><span>



                    <p class="bold">
                        <?= lang('date'); ?> : <?= $this->sma->hrld($inv->date); ?><br>
                        <?= lang('quote_no'); ?> : <?= $inv->id; ?><br>
                        <?= lang('reference_no'); ?> : <?= $inv->reference_no; ?><br>
                        <?= lang('status'); ?> : <?= lang($inv->status); ?>
                    </p>

			</div>

            <div class="col-xs-6">
            <?php
            $logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;

    $path   = base_url() . 'assets/uploads/' . $logo;
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $data   = file_get_contents($path);
    //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $base64 = $path; ?>
    <div class="pull-left" style="margin-top:10px;">
                <div class="text-right order_barcodes">

                    <img style="margin-left:20px;margin-bottom:2px;" src="<?= $base64; ?>" alt="<?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?>">
                    <br/>

                        <?= $this->sma->qrcode('link', urlencode(admin_url('quotes/view/' . $inv->id)), 2); ?>
                         <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />

                </div>
                </div>

            </div>

            </div>

            </div>

            <div class="clearfix"></div>
            <div class="padding10" style="margin-bottom:15px;margin-top:15px;">

                <div class="col-xs-5">
                   <b> <?php echo $this->lang->line('to'); ?> : <?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></b><br/>
                    <!--<h2 style="margin-top:10px;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>-->
                    <?= $customer->company                              && $customer->company != '-' ? '' : 'Attn: ' . $customer->name ?>

                    <?php
                    echo $customer->address . '<br>' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . ' ' . $customer->country . '<br>';

                    /*echo '<p>';

                    if ($customer->vat_no != '-' && $customer->vat_no != '') {
                        echo '<br>' . lang('vat_no') . ': ' . $customer->vat_no;
                    }
                    if ($customer->gst_no != '-' && $customer->gst_no != '') {
                        echo '<br>' . lang('gst_no') . ': ' . $customer->gst_no;
                    }
                    if ($customer->cf1 != '-' && $customer->cf1 != '') {
                        echo '<br>' . lang('ccf1') . ': ' . $customer->cf1;
                    }
                    if ($customer->cf2 != '-' && $customer->cf2 != '') {
                        echo '<br>' . lang('ccf2') . ': ' . $customer->cf2;
                    }
                    if ($customer->cf3 != '-' && $customer->cf3 != '') {
                        echo '<br>' . lang('ccf3') . ': ' . $customer->cf3;
                    }
                    if ($customer->cf4 != '-' && $customer->cf4 != '') {
                        echo '<br>' . lang('ccf4') . ': ' . $customer->cf4;
                    }
                    if ($customer->cf5 != '-' && $customer->cf5 != '') {
                        echo '<br>' . lang('ccf5') . ': ' . $customer->cf5;
                    }
                    if ($customer->cf6 != '-' && $customer->cf6 != '') {
                        echo '<br>' . lang('ccf6') . ': ' . $customer->cf6;
                    }

                    echo '</p>';*/
                    echo ($customer->vat_no ? lang('VAT_Regesteration_Number') . ' : ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ? lang('CR_Number') . ' : ' . $customer->cr_no . '<br>' : '<br>') ;
					?>
					<?php if ($Settings->IsConstruction && !empty($customer->cf1)) { ?>
					<?php if ($Settings->user_language == 'arabic') {
						?>

                       <font dir="ltr"> <?= $customer->cf1; ?> : <?= lang('PO_Number'); ?></font><br>
						<?php }else{ ?>
                    	<font> <?= lang('PO_Number'); ?> : <?= $customer->cf1; ?></font><br>
						<?php } ?>
                    	<?php } ?>
						<?php
                    echo lang('tel') . ' : ' . $customer->phone . '<br>' . lang('email') . ' : ' . $customer->email;
                    ?>
                </div>

                <div class="col-xs-5  pull-right">
                   <b><?php echo $this->lang->line('from'); ?> : <?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></b><br/>
                    <!--
					<h2 style="margin-top:10px;"><?= $Settings->site_name; ?></h2>

					<?= $warehouse->name ?>-->

                    <?php
					$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ' : ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->cr_no ? lang('CR_Number') . ' : ' . $warehouse->cr_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ' : ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ' : ' . $warehouse->email : '');
                     ?>
                </div>

            </div>
            <div class="clearfix"></div>


            <div class="col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table" style="margin-top: 15px;">
                    <thead>
                    <tr style="-webkit-print-color-adjust:exact;">
                        <th  class="col-xs-1"><?= lang('No_sales'); ?></th>
                        <!--<th><?= lang('product_code_sales'); ?></th>-->
                        <th  class="col-xs-6"><?= lang('Product_name_sales'); ?></th>
                        <th><?= lang('Product_unit_sales'); ?></th>
                        <?php if ($Settings->indian_gst) {
                        ?>
                            <th><?= lang('hsn_code'); ?></th>
                        <?php
                    } ?>
                        <th class="col-xs-2"><?= lang('Quantity_sales'); ?></th>

                        <th><?= lang('Unit_price_sales'); ?></th>
						 <!--<th><?= lang('value'); ?></th>-->
                        <?php
						if ($Settings->product_discount && $inv->product_discount != 0) {

                            echo '<th>' . lang('Discount_sales') . '</th>';
                        }
						//echo '<th>' . lang('item_price_sales') . '</th>';
                        if ($Settings->tax1 && $inv->product_tax > 0) {
							 echo '<th>' . lang('Tax_percent_sales') . '</th>';
                            echo '<th class="col-xs-2">' . lang('VAT_amount_sales') . '</th>';
                        }

                        ?>
                        <th  class="col-xs-2"><?= lang('Total_price_sales'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
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
                                <?php if ($Settings->show_product_code) {
							?>
                             <?=  lang('code'). ' : ' .$row->product_code. '<br>' ; ?>
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
								$product_unit_code=$row->product_unit_code;
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$base_unit_code=$unit->name;
										$product_unit_code=$unit->name;
									}
								}
								?>

							<td class="col-xs-2"><?=$product_unit_code?></td>
                            <?php if ($Settings->indian_gst) {
							?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php
							} ?>


							<?php
								$base_unit_code=$row->base_unit_code;
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$base_unit_code=$unit->name;
									}
								}
								?>


                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity) . ' ' . ($inv->sale_status == 'returned' ? $row->base_unit_code : ''); ?></td>

							<td style="text-align:right; width:100px;">
                                <!--<?= $row->unit_price != $row->real_unit_price && $row->item_discount > 0 ? '<del>' . $this->sma->formatMoney($row->real_unit_price,' ') . '</del>' : ''; ?>

								<?= $this->sma->formatMoney($row->unit_price,' '); ?>
                                 -->
                                 <?= $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4),' '); ?>
                            </td>
							<!--<td><?=($this->sma->formatMoney($row->real_unit_price * $row->unit_quantity,' '))?></td>-->
                            <?php
							if ($Settings->product_discount && $inv->product_discount != 0) {
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney($row->item_discount,' ') . '</td>';
                            }
							// echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->net_unit_price * $row->quantity). '</td>';
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $row->tax . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->item_tax,' ') . '</td>';
                            }

                            ?>
                            <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal,' '); ?></td>
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
                                        <?php if ($Settings->show_product_code) {
							?>
                             <?=  lang('code'). ' : ' .$row->product_code. '<br>' ; ?>
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
                                    <?= $row->serial_no ? ' - ' . $row->serial_no : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) {
                            ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php
                        } ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity) . ' ' . $row->base_unit_code; ?></td>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatMoney($row->unit_price,' '); ?></td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax,' ') . '</td>';
                                }
                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $this->sma->formatMoney($row->discount,' ') . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount,' ') . '</td>';
                        } ?>
                                <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal,' '); ?></td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>
                    </tbody>

                </table>
            </div>
            <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, null, $inv->product_tax) : ''; ?>
            <?php
            $whole = floor(abs($tquantity));
            $fraction = abs($tquantity) - $whole;
            ?>
            <div class="row inv-details">
			   <div class="col-xs-6 pull-right pdftable">
                    <div class="well well-sm" >

                            <table class="table table-striped jambo_table bulk_action print-table order-table">
				        <tbody>
						<tr style="-webkit-print-color-adjust:exact;">
						<?php if ($Settings->user_language == 'arabic') {
						?>


						<?php if($inv->total < 0){ ?>

					    <td colspan="<?= $col-4; ?>" style=" text-align:right;">عدد الأصناف المرتجعة
                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:right;">عدد الأصناف المباعة

                        </td>
						<?php } ?>

                        <td style="text-align:left; "><?= ($fraction > 0 ? $this->sma->formatDecimal(abs($tquantity),2) : (int)$this->sma->formatDecimal(abs($tquantity),2)); ?></td>

						<?php if($inv->total < 0){ ?>
					    <td colspan="4"
                            style="text-align:left;">Item Return Total
                        </td>
						<?php }else{ ?>

                        <td colspan="4"
                            style=" text-align:left;">Item Sold Total

                        </td>
						<?php } ?>
					    <?php }else{ ?>
						<?php if($inv->total < 0){ ?>

					    <td colspan="<?= $col-4; ?>" style=" text-align:left;">Item Return Total
                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">Item Sold Total

                        </td>
						<?php } ?>

                        <td style="text-align:left; "><?= ($fraction > 0 ? $this->sma->formatDecimal(abs($tquantity),2) : (int)$this->sma->formatDecimal(abs($tquantity),2)); ?></td>

						<?php if($inv->total < 0){ ?>
					    <td colspan="4"
                            style="text-align:right;">عدد الأصناف المرتجعة
                        </td>
						<?php }else{ ?>

                        <td colspan="4"
                            style=" text-align:right;">عدد الأصناف المباعة

                        </td>
						<?php } ?>

					    <?php } ?>

                    </tr>
                    <tr>






					   <?php if ($Settings->user_language == 'arabic') {
						?>




                        <td colspan="<?= $col-4; ?>" style=" text-align:right;">إجمالي خصم العروض

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount)); ?></td>


                        <td colspan="4"
                            style=" text-align:left;">Total Discounts

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">Total Discounts

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount)); ?></td>


                        <td colspan="4"
                            style=" text-align:right;">إجمالي خصم العروض

                        </td>

					    <?php } ?>



				<!--
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('discount_total'); ?>

                        </td>

                        <td style="text-align:right; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount)); ?></td>
                    -->
					</tr>
                    <tr>


					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;">الإجمالي قبل الضريبة

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount)); ?></td>


                        <td colspan="4"
                            style=" text-align:left;">VAT Before Total

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">VAT Before Total

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount)); ?></td>


                        <td colspan="4"
                            style=" text-align:right;">الإجمالي قبل الضريبة

                        </td>

					    <?php } ?>




					   <!--
					   <td colspan="<?= $col; ?>"
                            style=" "><?= lang('VAT_before_total'); ?>

                        </td>


                        <td style="text-align:right; "><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount)); ?></td>

						-->
                    </tr>
                    <tr>



						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;">ضريبة القيمة المضافة

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax)); ?></td>


                        <td colspan="4"
                            style=" text-align:left;">VAT

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">VAT

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax)); ?></td>


                        <td colspan="4"
                            style=" text-align:right;">ضريبة القيمة المضافة

                        </td>

					    <?php } ?>
					   <!--

						<td colspan="<?= $col; ?>"
                            style=" "><?= lang('VAT'); ?>

                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax)); ?></td>
						-->
                    </tr>
						<tr>



						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;">المبلغ الكلي

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)); ?></td>


                        <td colspan="4"
                            style=" text-align:left;">Total Amount

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">Total Amount

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)); ?></td>


                        <td colspan="4"
                            style=" text-align:right;">المبلغ الكلي

                        </td>

					    <?php } ?>

						<!--
						<td colspan="<?= $col; ?>"
                            style=" "><?= lang('total_amount_Tax_Invoice'); ?>

                        </td>

                        <td style="text-align:right; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)); ?></td>
						-->
                    </tr>


                    </tbody>
                </table>

                    </div>
                </div>
                <div class="col-xs-5 pull-left">
                    <div class="well well-sm"  >

                        <table class="table table-striped jambo_table bulk_action print-table order-table">
				        <tbody style="-webkit-print-color-adjust:exact;">
					<tr style="-webkit-print-color-adjust:exact;">


						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;">مدفوع

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid)); ?></td>


                        <td colspan="4"
                            style=" text-align:left;">Paid

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">Paid

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid)); ?></td>


                        <td colspan="4"
                            style=" text-align:right;">مدفوع

                        </td>

					    <?php } ?>

						<!--
						<td colspan="<?= $col; ?>"
                            style=" "><?= lang('paid_tax_invoice'); ?>

                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid)); ?></td>
						-->
                    </tr>
					<tr>


					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;">الرصيد

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid))); ?></td>


                        <td colspan="4"
                            style=" text-align:left;">Balance

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">Balance

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid))); ?></td>


                        <td colspan="4"
                            style=" text-align:right;">الرصيد

                        </td>

					    <?php } ?>
					   <!--
                        <td colspan="<?= $col; ?>"
                            style=""><?= lang('balance_tax_invoice'); ?>

                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid))); ?></td>
						-->
                    </tr>
					<?php
					if($inv->shipping!=0){
					?>
					<tr>

					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;">الشحن

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping)); ?></td>


                        <td colspan="4"
                            style=" text-align:left;">Shipping

                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col-4; ?>" style=" text-align:left;">Shipping

                        </td>

                        <td style="text-align:left; padding-right:10px; "><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping)); ?></td>


                        <td colspan="4"
                            style=" text-align:right;">الشحن

                        </td>

					    <?php } ?>
						<!--
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('shipping_tax_invoice'); ?>

                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping)); ?></td>
						-->
                    </tr>
					<?php
					}
					?>



                    </tbody>
                </table>


                    </div>
                </div>
            </div>
            </div>
            <div class="clearfix"></div>


                <div class="col-xs-12">
                    <?php if ($inv->note || $inv->note != '') {
                        ?>
                        <div class="well well-sm">
                            <p class="bold"><?= lang('note'); ?>:</p>

                            <div><?= $this->sma->decode_html($inv->note); ?></div>
                        </div>
                    <?php
                    } ?>
                </div>
                <div class="clearfix"></div>


        </div>
    </div>
</div>
</body>
</html>
