<?php defined('BASEPATH') or exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line('sale') . ' ' . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">


	<link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>

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

	<?php if ($Settings->user_language == 'arabic') {
    ?>
    <!--
    <link href="<?= $assets ?>styles/pdf/pdf_rtl.css" rel="stylesheet">
	-->
	    <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style_rtl.css" rel="stylesheet"/>


	<?php }else{
    ?>
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">

	<?php }
    ?>
	<script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>



		<style>

	#wrap {
    float:none;
    margin:0px;
    overflow:auto;
    padding:0;
    width:100%;
}



th {
  font-size: 13px;
}

@media print {
                .well { margin-bottom:-2px;padding:1px;}


				.col-xs-1 {
    width: 4.33333333%;
}
				.col-xs-11 {
    width: 95.66666667%;
}
		td {
    border: 1px solid #ddd;

}

.from{margin-bottom:15px;}


  div.divFooter {
    position: fixed;
    bottom: 0;
  }

/*
      table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }


	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	*/

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

</head>

<body>
<div id="wrap">
    <div class="row">
        <div class="col-xs-12">


		<div class="row bold">
            <div class="col-xs-5 text-left" style=" margin-bottom:10px;">

				    <h1  style=" font-size: 25px;"><?= (($inv->sale_status=="returned" || $inv->credit_id) ? 'Credit Note / إشعار دائن' : 'Tax Invoice / فاتوره ضريبية') ?></h1>

			</div>

             <div class="col-xs-5" >

            <?php
            $logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;
            //if ($logo)
            {
    $path   = base_url() . 'assets/uploads/' . $logo;
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    //$data   = file_get_contents($path);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    $data   =  $output;
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data); ?>
                <div  style="margin-top:10px;">
                    <img src="<?= $base64; ?>" alt="<?= $Settings->site_name; ?>">
                </div>
            <?php
}
            ?>
			</div>
			</div>






            <div class="clearfix"></div>


			<div class="row">
			<div class="col-xs-1 divbg">
			</div>
			<div class="col-xs-11">



			   <div class="well well-sm">
                <div class="row bold">
                    <div class="col-xs-6 text-left order_barcodes">
                         <?php
							  $wh_name=$warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name;
							 ?>

                        <?=  $this->sma->qrcode('', $invQR, 2) ?>

                    </div>
                    <div class="col-xs-4">
                    <p class="bold">
                        <?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                        <?= lang('Invoice_No'); ?>: <?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".($inv->sale_no ? $inv->sale_no : $inv->id) : ($inv->sale_no ? $inv->sale_no : $inv->id); ?><br>
                        <?= lang('reference_no'); ?>: <?= $inv->reference_no; ?><br>
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
                        <?php if ($inv->payment_status != 'paid') {

    } ?>
                    </p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="padding10">
                <?php if ($Settings->invoice_view == 100) {
                ?>
                    <div class="col-xs-12 text-center">
                        <h1><?= lang('tax_invoice'); ?></h1>
                    </div>
                <?php
            } ?>

                <div class="col-xs-5">
                    <?php echo $this->lang->line('to'); ?>:<br/>
                    <h3 style="margin-top:10px;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h3>
                    <?= $customer->company                              && $customer->company != '-' ? '' : 'Attn: ' . $customer->name ?>
                    <?php
                    echo $customer->address . '<br>';
            if($customer->city || $customer->postal_code || $customer->state || $customer->country)
                        echo $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . ' ' . $customer->country . '<br>';

                    echo ($accountID ?  '<strong>'.lang('Account_No') . ' :</strong> ' . $accountID . '<br>' : '') . ($customer->vat_no ?  '<strong>'.lang('VAT_Regesteration_Number') . ' :</strong> ' . $customer->vat_no . '<br>' : '<br>') . ($customer->cr_no ?  '<strong>'.lang('CR_Number') . ' :</strong> ' . $customer->cr_no . '<br>' : '<br>') ;
					?>
					<?php if ($Settings->IsConstruction && (!empty($customer->cf1) || !empty($inv->po_no) )) { ?>
					<strong><?= lang('PO_Number'); ?> :</strong> <?= $inv->po_no ? $inv->po_no : $customer->cf1; ?><br>
						<?php } ?>
                    	<?php
                    echo  '<strong>'.lang('tel') . ' :</strong> ' . $customer->phone . '<br>' .  '<strong>'.lang('email') . ' :</strong> ' . $customer->email;


                    ?>
                </div>

                    <div class="col-xs-5 from">
                    <?php echo $this->lang->line('from'); ?>:
                    <h3 style="margin-top:10px;"><?= $Settings->site_name; ?></h3>
                    <!--<?= $warehouse->name ?>-->

                    <?php
					$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ': ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ': ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?>
                </div>

            </div>
            <div class="clearfix"></div>


            <?php
                $col = $Settings->indian_gst ? 5 : 4;
                if ($Settings->product_discount && $inv->product_discount != 0) {
                    $col++;
                }
                if ($Settings->tax1 && $inv->product_tax > 0) {
                    $col++;
                }
                if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                    $tcol = $col - 2;
                } elseif ($Settings->product_discount && $inv->product_discount != 0) {
                    $tcol = $col - 1;
                } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                    $tcol = $col - 1;
                } else {
                    $tcol = $col;
                }
            ?>

            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table">
                    <thead>

                    <tr style="-webkit-print-color-adjust:exact;">
                        <th><?= lang('no_sales'); ?></th>
                        <!--<th><?= lang('product_code_sales'); ?></th>-->
                        <th class="col-xs-4"><?= lang('product_name_sales'); ?></th>
                        <th><?= lang('product_unit_sales'); ?></th>
                        <?php if ($Settings->indian_gst) {
                        ?>
                            <th><?= lang('hsn_code'); ?></th>
                        <?php
                    } ?>
                        <th><?= lang('quantity_sales'); ?></th>

                        <th class="col-xs-2"><?= lang('unit_price_sales'); ?></th>
						<?php
						    echo '<th class="col-xs-2">' . lang('discount_sales') . '</th>';
                        if ($Settings->tax1 && $inv->product_tax > 0) {
							 echo '<th class="col-xs-2">' . lang('tax_percent_sales') . '</th>';
                            echo '<th class="col-xs-2">' . lang('VAT_amount_sales') . '</th>';
                        }

                        ?>
                        <th><?= lang('total_price_sales'); ?></th>
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
                                <?=  lang('code'). ':' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                            </td>
							<td><?=$row->product_unit_code?></td>
                            <?php if ($Settings->indian_gst) {
							?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php
							} ?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity) . ' ' . ($inv->sale_status == 'returned' ? $row->base_unit_code : ''); ?></td>

							<td style="text-align:right; width:100px;">
                                <!--<?= $row->unit_price != $row->real_unit_price && $row->item_discount > 0 ? '<del>' . $this->sma->formatMoney($row->real_unit_price,' ') . '</del>' : ''; ?>
                                -->
								<?= $this->sma->formatMoney($row->real_unit_price,' '); ?>
                            </td>
							<!--<td><?=($this->sma->formatMoney($row->real_unit_price * $row->unit_quantity))?></td>-->
                            <?php
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

                            ?>
                            <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal,' '); ?></td>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong style=" ">' . lang('returned_items') . '</strong></td></tr>';
                        foreach ($return_rows as $row):
						$tquantity=$tquantity+$row->unit_quantity;
                        ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code; ?>
                                </td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) {
                            ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php
                        } ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->base_unit_code; ?></td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($row->quantity)); ?></td>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatMoney($row->unit_price,' '); ?></td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0)
								{
                                //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . abs($row->tax) . '</td>';
                            //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';

								}
                        //if ($Settings->product_discount && $inv->product_discount != 0) {
                          //  echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $this->sma->formatMoney($row->discount) . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                             echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';
                           //}
						?>
                                <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney(abs($row->subtotal),' '); ?></td>
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
                            <td colspan="<?= $tcol; ?>" style="text-align:right;"><?= lang('total'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_tax + $return_sale->product_tax) : $inv->product_tax) . '</td>';
                                }
                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_discount + $return_sale->product_discount) : $inv->product_discount) . '</td>';
                        } ?>
                            <td style="text-align:right;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax) + ($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if ($return_sale) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('return_total') . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . $this->sma->formatMoney($return_sale->grand_total) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('return_surcharge') . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>

                    <?php if ($Settings->indian_gst) {
                        if ($inv->cgst > 0) {
                            $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>

                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('order_discount') . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . ($inv->order_discount_id ? '<small>(' . $inv->order_discount_id . ')</small> ' : '') . $this->sma->formatMoney($return_sale ? ($inv->order_discount + $return_sale->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('order_tax') . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax + $return_sale->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('shipping') . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . $this->sma->formatMoney($inv->shipping - ($return_sale && $return_sale->shipping ? $return_sale->shipping : 0)) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right;"><?= lang('total_amount'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:right;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total); ?></td>
                    </tr>

                    <tr>
                        <td colspan="<?= $col; ?>" style="text-align:right;"><?= lang('paid'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:right;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>" style="text-align:right;"><?= lang('balance'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:right;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid)); ?></td>
                    </tr>

                    </tfoot>-->
                </table>
            </div>
            <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax + $return_sale->product_tax : $inv->product_tax)) : ''; ?>
             <div class="row  inv-details" >
			   <div class="col-xs-5">
                    <div >
                        <p>
                            <table class="table table-striped jambo_table bulk_action print-table order-table">
				        <tfoot style="-webkit-print-color-adjust:exact;">
						<tr style="-webkit-print-color-adjust:exact;">
						<?php if($inv->total < 0){ ?>
					    <td colspan="<?= $col; ?>"
                            style=" "><?= lang('items_sold_purchase_total_return'); ?>
                        </td>
						<?php }else{ ?>

                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('items_sold_total'); ?>

                        </td>
						<?php } ?>

                        <td style="text-align:right; "><?= (int)$this->sma->formatDecimal(abs($tquantity),2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('discount_total'); ?>
                            <!--(<?= $default_currency->code; ?>)-->
                        </td>

                        <td style="text-align:right; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('VAT_before_total'); ?>
                            <!--(<?= $default_currency->code; ?>)-->
                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total - $inv->total_discount) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('VAT'); ?>
                            <!--(<?= $default_currency->code; ?>)-->
                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
                    </tr>
						<tr>
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('total_amount_Tax_Invoice'); ?>
                            <!--(<?= $default_currency->code; ?>)-->
                        </td>

                        <td style="text-align:right; padding-right:10px; "><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>
                    </tr>


                    </tfoot>
                </table>
						</p>
                    </div>
                </div>
                <div class="col-xs-5 text-left">
                    <div >
                        <p>
                        <table class="table table-striped jambo_table bulk_action print-table order-table">
				        <tfoot style="-webkit-print-color-adjust:exact;">
					<tr style="-webkit-print-color-adjust:exact;">
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('paid_tax_invoice'); ?>
                            <!--(<?= $default_currency->code; ?>)-->
                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : abs($inv->paid),$currencyCode); ?></td>
                    </tr>
					<?php
					if(abs($inv->paid)!=0){
						foreach ($payments as $payment) {

						?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('paid_by_tax_invoice'); ?>
							<?php
							 if ($payment->paid_by == 'cash' || $payment->paid_by == 'gift_card'){
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

								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)


							<?php
							 }
                            ?>
                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney(abs($payment->amount),$payment->currency); ?></td>
                    </tr>

					<?php
						}
					}
						?>

							<?php
							if(abs($return_sale->paid)!=0){
					        if ($return_sale) {

						?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style=""><?= lang('paid_by_tax_invoice'); ?>
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

                        <td style="text-align:right; "><?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?></td>
                    </tr>

					<?php
						}
						}
                    ?>
					<tr>
                        <td colspan="<?= $col; ?>"
                            style=""><?= lang('balance_tax_invoice'); ?>
                           <!--(<?= $default_currency->code; ?>)-->
                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid)),$currencyCode); ?></td>
                    </tr>
					<?php
					if($inv->shipping!=0){
					?>
					<tr>
                        <td colspan="<?= $col; ?>"
                            style=" "><?= lang('shipping_tax_invoice'); ?>
                            <!--(<?= $default_currency->code; ?>)-->
                        </td>

                        <td style="text-align:right; "><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping),$currencyCode); ?></td>
                    </tr>
					<?php
					}
					?>


                    </tfoot>
                </table>

						</p>
                    </div>
                </div>


                <div class="col-xs-12">
                    <?php
                        if ($inv->note || $inv->note != '') {
                            ?>
                            <div >
                                <p class="bold"><?= lang('note'); ?>:</p>
                                <div><?= $this->sma->decode_html($inv->note); ?></div>
                            </div>
                        <?php
                        }
                        if ($inv->staff_note || $inv->staff_note != '') {
                            ?>
                            <div class=" well-sm staff_note">
                                <p class="bold"><?= lang('staff_note'); ?>:</p>
                                <div><?= $this->sma->decode_html($inv->staff_note); ?></div>
                            </div>
                        <?php
                        } ?>
                </div>

                <?php if ($customer->award_points != 0 && $Settings->each_spent > 0) {
                            ?>
                <div class="col-xs-5 pull-left">
                    <div >
                        <?=
                        '<p>' . lang('this_sale') . ': ' . floor(($inv->grand_total / $Settings->each_spent) * $Settings->ca_point)
                        . '<br>' .
                        lang('total') . ' ' . lang('award_points') . ': ' . $customer->award_points . '</p>'; ?>
                    </div>
                </div>
                <?php
                        } ?>

                <div class="col-xs-5 ">
                    <div >
                        <p>
                            <?= lang('created_by'); ?>: <?= $inv->created_by ? $created_by->first_name . ' ' . $created_by->last_name : $customer->name; ?> <br>
                            <?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?>
                        </p>
                        <?php if ($inv->updated_by) {
                            ?>
                        <p>
                            <?= lang('updated_by'); ?>: <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?><br>
                            <?= lang('update_at'); ?>: <?= $this->sma->hrld($inv->updated_at); ?>
                        </p>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>

           </div>
            </div>
</div>
</body>
</html>
