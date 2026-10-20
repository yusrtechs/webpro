<!DOCTYPE html>
<html lang="ar" dir="<?= ($Settings->user_language == 'arabic') ? 'rtl' : 'ltl'  ?>">
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: "Cairo-Bold", sans-serif;
        font-size: 10px;
        color: #222;
        line-height: 1.4;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    /* الهيدر */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .header-table td {
        vertical-align: middle;
    }
    .header-info {
        font-size: 10px;
        line-height: 1.6;
        /*text-align: left;*/
    }
    .invoice-title-center {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 15px;
        color: #000;
    }
    .brand-name {
        font-size: 20px;
        font-weight: bold;
        color: #0F3A5D;
        margin-bottom: 2px;
    }
    .brand-sub {
        font-size: 8px;
        color: #555;
        letter-spacing: 1px;
    }
    .invoice-title {
        font-size: 25px;
        font-weight: bold;
        color: #000;
        margin-bottom: 4px;
    }

    .divider {
        border-top: 1px solid #ccc;
        margin: 10px 0 15px 0;
    }

    /* صناديق "من" و "إلى" */
    .box-container {
        width: 100%;
        margin-bottom: 15px;
    }
    .info-box {
        width: 48%;
        border: 1px solid #b8b8b8;
        border-radius: 2px;
        padding: 8px;
        vertical-align: top;
    }
    .box-header {
        font-weight: bold;
        background-color: #f5f5f5;
        padding: 3px 6px;
        margin: -8px -8px 8px -8px;
        border-bottom: 1px solid #b8b8b8;
    }

    /* جدول الأصناف */
    .items-table {
        width: 100%;
        margin-bottom: 15px;
    }
    .items-table th {
        background-color: #f0f0f0;
        border: 1px solid #999;
        padding: 5px 3px;
        font-size: 9px;
        text-align: center;
        font-weight: bold;
    }
    .items-table td {
        border: 1px solid #999;
        padding: 6px 4px;
        text-align: center;
        font-size: 9.5px;
    }

    /* جدول الإجماليات والتوقيع */
    .summary-table {
        border: 1px solid #999;
    }
    .summary-table td {
        border: 1px solid #999;
        padding: 4px 8px;
    }
    .bg-light {
        background-color: #f7f7f7;
    }

    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }

    .status-paid {
        color: #2e7d32;
        font-weight: bold;
    }

    .qrimg{
        min-width: 110px;
        max-width: 170px;
        max-height: 170px;
    }
</style>
</head>
<body>

<?php
			if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

			?>
<table class="header-table">
    <tr>
        <!-- 3. بيانات الفاتورة (اليسار) -->
        <td width="35%" class="header-info">
            <strong><?= lang('Inv_date'); ?> :</strong> <?= $this->sma->hrld($inv->date); ?><br>
            <strong><?= lang('Invoice_No'); ?> :</strong> <?= $Settings->sales_invoice_prefix ? $Settings->sales_invoice_prefix."".($inv->sale_no ? $inv->sale_no : $inv->id) : ($inv->sale_no ? $inv->sale_no : $inv->id); ?><br>
            <strong><?= lang('reference_no'); ?> :</strong> <?= $inv->reference_no; ?><br>
            <?php if ($Settings->IsConstruction && !empty($inv->manual_payment)) { ?>
                        <strong><?= lang('Contract_Number'); ?> :</strong> <?= $inv->manual_payment; ?><br>
						<?php } ?>
                    <?php if ($Settings->isShipping && !empty($inv->delivery_note_number)) { ?>
                        <strong><?= lang('Delivery_note_number'); ?> :</strong> <?= $inv->delivery_note_number; ?><br>
						<?php } ?>
                        <?php if (!empty($inv->due_date)) { ?>
                        <strong><?= lang('Due_date'); ?> :</strong> <?= $this->sma->hrsd($inv->due_date); ?><br>
						<?php } ?>
                        <?php if (!empty($inv->return_sale_ref)) {
        echo '<strong>'.lang('return_ref') . ' :</strong> ' . $inv->return_sale_ref;
        if ($inv->return_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
    <?php if (!empty($inv->return_debit_ref)) {
        echo '<strong>'.lang('Notice_reference') . ' :</strong> ' . $inv->return_debit_ref;
        if ($inv->debit_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $inv->debit_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
    <?php if (!empty($inv->return_credit_ref)) {
        echo '<strong>'.lang('Notice_reference') . ' :</strong> ' . $inv->return_credit_ref;
        if ($inv->credit_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $inv->credit_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
    <strong><?= lang('sale_status'); ?> :</strong> <?= $inv->sale_status=="returned" ? lang('Notice_creditor') : ($inv->debit_id ? lang('Debit_note') : ($inv->credit_id ? lang('Credit_note') : lang($inv->sale_status))); ?> - <?= lang($inv->payment_status); ?>
        </td>
        <td width="30%" class="text-center">
             <?php
						$QR=$this->sma->barcodeZATCA($wh_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total))*$currencyRate,2),$this->sma->formatDecimal(($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax))*$currencyRate,2));
						?>

            <barcode code="<?= $invQR ? $invQR : ($warehouse->vat_reg_no ?  $QR : '') ?>" type="QR" class="barcode" size="1.1" error="M" />
        </td>
        <td width="35%">

            <?php
            $logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;

    $path   = base_url() . 'assets/uploads/' . $logo;
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $data   = file_get_contents($path);
    //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $base64 = $path; ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <img src="<?= $base64; ?>" alt="<?=$this->Settings->site_name; ?>" style="width:230px;">
                </div>

        </td>

    </tr>
</table>

<div class="invoice-title-center">
    <?php
                if($inv->sale_status=="returned" || $inv->credit_id){
                    ?>
                        إشعار دائن / Credit Note
                       <?php
                }else{
					if($customer->co_type==2){// || abs($inv->grand_total)>=1000){
                        ?>
                        فاتورة مبيعات / Sale Invoice
                       <?php
					}else if($customer->vat_no!=""){// || abs($inv->grand_total)>=1000){
					?>
				     فاتورة ضريبية / Tax Invoice
					<?php
					}else{
					?>
				     فاتورة ضريبية مبسطة / Simplified Tax Invoice
					<?php
					}
                }
					?>

</div>

<div class="divider"></div>

<!-- معلومات البائع والمشتري -->
<table class="box-container">
    <tr>
        <!-- من: البائع -->
        <td class="info-box">
            <div class="box-header"><?php echo $this->lang->line('from'); ?> :</div>
            <?php
                    if(isset($Settings->isGrouping) && $Settings->isGrouping==1 && $branch){
                ?>
                    <strong><?= $branch->branch_name ? $branch->branch_name : $Settings->site_name; ?></strong><br>
                <?php
                    }else{
                ?>
                    <strong><?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></strong><br>
                <?php
                    }
                ?>
            <?php
                    if(isset($Settings->preAccounting) && $Settings->preAccounting==1 && $Settings->isDisplayWarehouseOnInvoice==1)
                    {
                        echo lang('warehouse') . ' : ' . $warehouse->name . '<br>';
                    }
					$address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    if (preg_match('/<br\s*\/?>/i', $address)) {
                        echo $address;
                    } else {
                        echo $address."<br>";
                    }
                    echo($warehouse->vat_reg_no ? '<strong>'.lang('VAT_Regesteration_Number') . ' :</strong> ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->cr_no ? '<strong>'.lang('CR_Number') . ' :</strong> ' . $warehouse->cr_no . '<br>' : '') . ($warehouse->phone ? '<strong>'.lang('tel') . ' :</strong> ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? '<strong>'.lang('email') . ' :</strong> ' . $warehouse->email : '');

                    ?>
        </td>

        <td width="4%"></td>

        <!-- إلى: العميل -->
        <td class="info-box">
            <div class="box-header"><?php echo $this->lang->line('to'); ?> :</div>
            <strong><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></strong><br>
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
        </td>
    </tr>
</table>

<?php
                $col = $Settings->indian_gst ? 7 : 6;
                if ($inv->status == 'partial') {
                    $col++;
                }
                if($inv->order_type==1){
                    $col++;
                    if ($Settings->tax1 && $inv->product_tax > 0) {
                        $col++;
                        $col++;
                    }
                }
            ?>


<table class="items-table">
    <thead>
        <tr>
            <th width="7%"><?= lang('No_sales'); ?></th>
            <th width="50%"><?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? lang('Service_name_sales') : lang('Product_code_sales'); ?></span></th>
            <th width="7%"><?= lang('Product_unit_sales'); ?></th>
            <th width="7%"><?= lang('Quantity_sales'); ?></th>
            <th width="10%"><?= lang('Unit_price_sales'); ?></th>
            <?php
				if($inv->order_type==1){
			?>
                <th width="8%"><?= lang('Discount_sales') ?></th>
                <?php
				    if ($Settings->tax1 && $inv->product_tax > 0) {
			    ?>
                    <th width="7%"><?= lang('Tax_percent_sales') ?></th>
                    <th width="10%"><?= lang('VAT_amount_sales') ?></th>
                <?php
				    }
			    ?>
            <?php
				}
			?>
            <th width="10%"><?= lang('Total_price_sales'); ?></th>
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
							<?php
							if($row->promo_free==1){
								?>
							<img src="<?= base_url() . 'assets/images/free.jpg' ?>" width="10%">
							<?php
							}
							?>
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

							<td><?=$product_unit_code?></td>
                            <?php if ($Settings->indian_gst) {
							?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php
							} ?>

                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity); ?></td>

							<td style="width:100px;">
                                <?= $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4),' '); ?>
                            </td>
							<?php
							if($inv->order_type==1){
							    echo '<td style="width: 100px; vertical-align:middle;">' .  $this->sma->formatMoney($row->item_discount,' ') . '</td>';


							if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; vertical-align:middle;">' . $row->tax . '</td>';
                                echo '<td style="width: 100px; vertical-align:middle;">' . $this->sma->formatMoney($row->item_tax,' ') . '</td>';
                            }
                            echo '<td style="width: 120px; vertical-align:middle;">' . $this->sma->formatMoney($row->subtotal,' ') . '</td>';

							}else{
							    echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' . $this->sma->formatDecimal(($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity))*$row->unit_quantity) . '</td>';
                            }
                            ?>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="' . ($col) . '" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
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
                                <td style="width:100px;"><?= $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4),' '); ?></td>
                                <?php
                                if($inv->order_type==1){
								if ($Settings->tax1 && $inv->product_tax > 0)
								{
                                    echo '<td style="width: 100px; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->item_discount)) . '</td>';
                                    echo '<td style="width: 100px; vertical-align:middle;">' . abs($row->tax) . '</td>';
                                echo '<td style="width: 100px; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';

								}else{
                                     echo '<td style="width: 100px; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';
                                }
						   	    echo '<td style="width: 120px; vertical-align:middle;">' .  $this->sma->formatMoney(abs($row->subtotal),' ') . '</td>';

								}else{

								      echo '<td style="width: 120px; text-align:center; vertical-align:middle;">' . $this->sma->formatDecimal(($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity))*$row->unit_quantity) . '</td>';

								}

						?>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>

    </tbody>
</table>
<?php
			if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

			?>


<table width="100%" style="margin-top: 5px;">
    <tr>
        <td width="48%" style="vertical-align: top;">
            <?php
                if($Settings->isDisplayItemsSold!=0){
            ?>

            <table class="summary-table" width="100%">
                <tr>
                    <?php if($inv->total < 0){ ?>

					    <td class="bg-light bold" width="80%"><?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? 'عدد الخدمات المرتجعة / Services Return Total' : 'عدد الأصناف المرتجعة / Item Return Total'; ?>
                        </td>
						<?php }else{ ?>

                        <td class="bg-light bold" width="80%"> <?=  ($Settings->IsServices || $Settings->IsAutomotiveServices) ? 'عدد الخدمات المقدمة / Services Provided Total' : 'عدد الأصناف المباعة / Item Sold Total'; ?>
                        </td>
						<?php } ?>
                    <td class="text-center bold"><?= ($Settings->isDisplayItemsSold==1) ? (int)$this->sma->formatDecimal(abs($r-1),2) : ($fraction > 0 ? $this->sma->formatDecimal(abs($tquantity),2) : (int)$this->sma->formatDecimal(abs($tquantity),2)); ?></td>
                </tr>
            </table>
            <?php
                }
            ?>

            <br><br><br>

            <table width="100%">
                <tr>
                    <td width="50%" class="text-center">
                        <strong><?= lang('Prepared_by'); ?> :</strong><br><br>
                        &nbsp;
                    </td>
                    <td width="50%" class="text-center">
                        <strong><?= lang('Receiver_Signature'); ?> :</strong><br><br>
                        ...............................
                    </td>
                </tr>
            </table>
        </td>

        <td width="4%"></td>
        <?php
            $db_name=str_replace("cpesoft_","",$this->db->database);
        ?>
        <!-- الجزء الأيسر: الحسابات التفصيلية -->
        <td width="48%" style="vertical-align: top;">
            <table class="summary-table" width="100%">
                <tr>
                    <td class="bg-light">إجمالي خصم العروض / Total Discounts</td>
                    <td class="text-left"><?= $this->sma->formatMoney($return_sale ? ($inv->total_discount + $return_sale->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                </tr>
                <tr>
                    <td class="bg-light"><?= $db_name=="rama" ? 'الإجمالي بعد الاحتجاز / Total Amount After Retention' : 'الإجمالي قبل الضريبة / Total Before VAT' ?></td>
                    <td class="text-left"><?= $this->sma->formatMoney($return_sale ? ($inv->total + $return_sale->total) : abs($inv->total - $inv->total_discount),$currencyCode); ?></td>
                </tr>
                <?php
						$VAT="";
					if($inv->total_tax!=0)
					$VAT=$this->sma->formatDecimal($active_tax,0);

					?>
                <tr>
                    <td class="bg-light">ضريبة القيمة المضافة (%<?= $VAT ?>) / VAT (<?= $VAT ?>%)</td>
                    <td class="text-left"><?= $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),$currencyCode); ?></td>
                </tr>
                <tr style="background-color: #eef3f8;">
                    <td class="bold">المبلغ الكلي / Total Amount</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),$currencyCode); ?></td>
                </tr>
                <tr>
                    <td>مدفوع / Paid</td>
                    <td class="text-left"><?= $this->sma->formatMoney($return_sale ? ($inv->paid  + $return_sale->paid ) : abs($inv->paid ),$currencyCode); ?></td>
                </tr>
                <?php
					if(abs($inv->paid)!=0){
						foreach ($payments as $payment) {

						?>

                <tr>
                    <td>مدفوع بواسطة / Paid by</td>
                    <td class="text-left"><?php
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

								  (<?= lang('cheque') ?>)<br/><?= lang('cheque_no').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)


							<?php
							 }
                            ?>
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>

								  (<?= lang('bank_transfer') ?>)


							<?php
							 }
                            ?><?= $this->sma->formatMoney(abs($payment->amount ),$payment->currency); ?></td>
                </tr>
                <tr>
                    <td>تاريخ الدفع / Paid Date</td>
                    <td class="text-left"><?= $this->sma->hrld($payment->date); ?></td>
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
                    <td>مدفوع بواسطة / Paid by</td>
                    <td class="text-left"><?php
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
                            <?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?></td>
                </tr>
                <tr>
                    <td>تاريخ الدفع / Paid Date</td>
                    <td class="text-left"><?= $this->sma->hrld($return_payments[0]->date); ?></td>
                </tr>
                <?php
			    		}
		    		}
                ?>
                <?php
					if($inv->discount_allowance!=0){
					?>
                <tr>
                    <td class="bold">خصم مسموح به / Discount Allowance</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_sale ? ($inv->discount_allowance  - abs($return_sale->discount_allowance)) : $inv->discount_allowance),$currencyCode); ?></td>
                </tr>
                <?php
					}
				?>
                <tr>
                    <td class="bold">الرصيد / Balance</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance - $inv->donation) + ($return_sale->paid + $return_sale->discount_allowance - $return_sale->donation - $return_sale->donation) : ($inv->paid + $inv->discount_allowance - $inv->donation))),$currencyCode); ?></td>
                </tr>
                <?php
					if($inv->shipping!=0){
				?>
                <tr>
                    <td class="bold">الشحن / Shipping</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_sale ? ($inv->shipping - $return_sale->shipping) : $inv->shipping),$currencyCode); ?></td>
                </tr>
                <?php
					}
				?>
                <?php
					if($inv->donation!=0){
				?>
                <tr>
                    <td class="bold">التبرع / Donation</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_sale ? ($inv->donation - $return_sale->donation) : $inv->donation),$currencyCode); ?></td>
                </tr>
                <?php
					}
				?>
                <?php
					if ($Settings->IsInsuranceBusiness && $inv->insurance_business !=0) {
                        if($Settings->insuranceType==1){
				?>
                <tr>
                    <td class="bold">تأمين الأعمال <?=  $inv->insurance_percentage ? '%'.(int)$inv->insurance_business : '' ?> / Insurance business <?= $inv->insurance_percentage ? (int)$inv->insurance_business.'%' :'' ?></td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($inv->insurance_percentage ? ($return_sale ? (($inv->total + $return_sale->total) * $inv->insurance_business / 100) : (abs($inv->total - $inv->total_discount) * $inv->insurance_business / 100 )) : $inv->insurance_business),$currencyCode); ?></td>
                </tr>
                <tr>
                    <td class="bold">المستحق صرفه / Net Payable</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney((($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance) + ($return_sale->paid + $return_sale->discount_allowance) : ($inv->paid + $inv->discount_allowance))) - (($inv->insurance_percentage ? ($return_sale ? (($inv->total + $return_sale->total) * $inv->insurance_business / 100) : (abs($inv->total - $inv->total_discount) * $inv->insurance_business / 100 )) : $inv->insurance_business))),$currencyCode); ?></td>
                </tr>
                <?php
					    }else{
                            ?>
                <tr>
                    <td class="bold">تأمين الأعمال <?=  $inv->insurance_percentage ? '%'.(int)$inv->insurance_business : '' ?> / Insurance business <?= $inv->insurance_percentage ? (int)$inv->insurance_business.'%' :'' ?></td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($inv->insurance_percentage ? ($return_sale ? (($inv->grand_total + $return_sale->grand_total) * $inv->insurance_business / 100) : (abs($inv->grand_total) * $inv->insurance_business / 100 )) : $inv->insurance_business),$currencyCode); ?></td>
                </tr>
                <tr>
                    <td class="bold">المستحق صرفه / Net Payable</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney((($return_sale ? (($inv->grand_total) + $return_sale->grand_total) : abs($inv->grand_total)) - abs(($return_sale ? ($inv->paid + $inv->discount_allowance) + ($return_sale->paid + $return_sale->discount_allowance) : ($inv->paid + $inv->discount_allowance))) - (($inv->insurance_percentage ? ($return_sale ? (($inv->grand_total + $return_sale->grand_total) * $inv->insurance_business / 100) : (abs($inv->grand_total) * $inv->insurance_business / 100 )) : $inv->insurance_business)) - ($advance_payment_discount) - ($advance_payment_discount_tax)),$currencyCode); ?></td>
                </tr>
                            <?php
                        }

                    }
				?>
            </table>
        </td>
    </tr>
</table>

<br>
<?php
if ($Settings->terms_renderer && !empty($Settings->terms_attachment)) {

                    }else{
	if ($inv->note || $inv->note != '') {
?>
<div>
    <table width="100%">
        <tr class="info-box">
            <td width="100%">
                <strong><?= lang('note'); ?> : </strong><br> <?= $this->sma->decode_html($inv->note); ?>
            </td>
        </tr>
    </table>
</div>
<?php
	}
}
if ($inv->staff_note || $inv->staff_note != '') {
?>
<div>
    <table width="100%">
        <tr class="info-box">
            <td width="100%">
                <strong><?= lang('staff_note'); ?> : </strong><br> <?= $this->sma->decode_html($inv->staff_note); ?>
            </td>
        </tr>
    </table>
</div>
<?php
	}
?>

<br>
<div>
    <table width="100%">
        <tr class="info-box">
            <td width="50%">
                <?php if ($customer->award_points != 0 && $Settings->each_spent > 0) {
                            ?>
                        <?= lang('this_sale') . ' : ' . floor(($inv->grand_total / $Settings->each_spent) * $Settings->ca_point)
                        . '<br>' .
                        lang('total') . ' ' . lang('award_points') . ': ' . $customer->award_points; ?>

                <?php
                        } ?>

            </td>
            <td width="50%" class="text-left">


            </td>
        </tr>
    </table>
</div>

<?php if ($Settings->terms_renderer && !empty($Settings->terms_attachment))
    {
?>
<div>
    <table width="100%">
        <tr class="info-box">
            <td width="100%">
                <?= lang('Warranty_and_returns_policy'); ?> : <?= $this->sma->qrcode('link',urlencode('https://tinyurl.com/bdehjdcx/'.$Settings->terms_attachment) , 2); ?>
            </td>
        </tr>
    </table>
</div>
<?php
    }
?>

<?php if ($Settings->IsAutomotiveServices) {
?>
<div>
    <table width="100%">
        <tr class="info-box">
            <td width="50%">

                            <?= lang('Plate_Number'); ?> : <?= $inv->plate_no; ?> <br>
                            <?= lang('Exit_date'); ?> : <?= $this->sma->hrld($inv->exit_date); ?>
                        </td>
            <td width="50%" class="text-left">
            </td>
        </tr>
    </table>
</div>
<?php
} ?>

<?php if ($Settings->IsRental) {
?>
<div>
    <table width="100%">
        <tr class="info-box">
            <td width="50%">

                            <?= lang('Rent_start_date'); ?> : <?= (isset($inv->rent_start_date) ? $this->sma->hrsd($inv->rent_start_date) : ''); ?> <br>
                            <?= lang('Rent_end_date'); ?> : <?= (isset($inv->rent_end_date) ? $this->sma->hrsd($inv->rent_end_date) : ''); ?>
                        </td>
            <td width="50%" class="text-left">
            </td>
        </tr>
    </table>
</div>
<?php
} ?>


<br><br>

<!-- الفوتر ومعلومات النظام -->
<div style="font-size: 9px; color: #555; border-top: 1px solid #ccc; padding-top: 5px;">
    <table width="100%">
        <tr>
            <td width="50%">
                <?= lang('created_by'); ?> : <?= $created_by->first_name . ' ' . $created_by->last_name; ?>
                            <?= lang('date'); ?> : <?= $this->sma->hrld($inv->date); ?>
                            <?php if ($inv->updated_by) { ?>
                                <br><?= lang('updated_by'); ?> : <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?>
                                <?= lang('update_at'); ?> : <?= $this->sma->hrld($inv->updated_at); ?>
                            <?php } ?>

            </td>
            <td width="50%" class="text-left">

            </td>
        </tr>
    </table>
</div>

</body>
</html>