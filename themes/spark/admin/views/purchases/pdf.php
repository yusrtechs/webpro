<!DOCTYPE html>
<html lang="ar" dir="<?= ($Settings->user_language == 'arabic') ? 'rtl' : 'ltl'  ?>">
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: "Cairo-Bold", sans-serif;
        font-size: 11px;
        color: #333;
    }
    .header-table, .info-table, .items-table, .totals-table, .footer-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    .header-table td {
        vertical-align: top;
    }
    .company-title {
        font-size: 18px;
        font-weight: bold;
        color: #1a4a72;
    }
    .company-subtitle {
        font-size: 9px;
        color: #666;
    }
    .invoice-title {
        font-size: 25px;
        font-weight: bold;
        /*text-align: left;*/
    }
    .info-box {
        width: 48%;
        border: 1px solid #ccc;
        padding: 8px;
        vertical-align: top;
    }
    .info-title {
        font-weight: bold;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
        margin-bottom: 5px;
        background-color: #f9f9f9;
    }
    .items-table th {
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        padding: 6px;
        font-size: 10px;
        text-align: center;
    }
    .items-table td {
        border: 1px solid #ccc;
        padding: 6px;
        text-align: center;
    }
    .summary-table {
        width: 45%;
        float: left;
        border-collapse: collapse;
    }
    .summary-table td {
        border: 1px solid #ccc;
        padding: 5px;
    }
    .bg-gray {
        background-color: #f9f9f9;
    }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }
</style>
</head>
<body>

<!-- الهيدر والترويسة -->
<table class="header-table">
    <tr>
        <td width="70%" >
            <div class="invoice-title"><?php
					if($inv->status=="ordered"){
					?>
				    Purchase Request / طلب شراء
					<?php
					}else if($inv->status=="pending"){
					?>
				    Purchase Order / أمر شراء
					<?php
					}else{
					?>
				    Purchase Invoice / فاتورة مشتريات
					<?php
					}
					?>
            </div>
            <div><?= ($inv->status=="ordered" || $inv->status=="pending") ? lang('date') : lang('Inv_date'); ?> : <?= $this->sma->hrld($inv->date); ?></div>
            <div><?php
					   if($inv->status!="ordered" && $inv->status!="pending"){
						?>
					   <?= lang('Invoice_No'); ?> : <?= ($inv->purchase_no ? $inv->purchase_no : $inv->id); ?>
					   <?php
					   }
					   ?>
            </div>
            <div><?= lang('reference_no'); ?> : <?= $inv->reference_no; ?></div>
            <?php if (!empty($inv->due_date)) { ?>
                <div><?= lang('Due_date'); ?> : <?= $this->sma->hrsd($inv->due_date); ?></div>
			<?php } ?>
            <?php if (!empty($inv->return_purchase_ref)) { ?>
                <div><?= lang('return_ref') . ' : ' . $inv->return_purchase_ref; ?>
                <?php if ($inv->return_id) {
                    echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('purchases/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a>';
                } ?></div>
            <?php } ?>
            <?php if (!empty($inv->return_purchase_ex_ref)) { ?>
                <div><?= lang('return_ref_ex') . ' : ' . $inv->return_purchase_ex_ref; ?>
                <?php if ($inv->return_id) {
                    echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('purchases/expense_note/' . $inv->return_ex_id) . '"><i class="fa fa-external-link no-print"></i></a>';
                } ?></div>
            <?php } ?>
            <?php if (!empty($inv->return_surcharge_ex_ref)) { ?>
                <div><?= lang('return_ref_s_ex') . ' : ' . $inv->return_surcharge_ex_ref; ?>
                <?php if ($inv->return_id) {
                    echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('purchases/expense_note/' . $inv->return_ex_s_id) . '"><i class="fa fa-external-link no-print"></i></a>';
                } ?></div>
            <?php } ?>
            <div><?= lang('status'); ?> : <?= $inv->status=="returned" ? lang('Notice_debitor') : ($inv->receiving_status=="pending" ? lang('Purchase_invoice_pendding') : lang($inv->status)); ?></div>
            <div><?= lang('payment_status'); ?> : <?= lang($inv->payment_status); ?></div>
        </td>

        <td width="30%">
            <?php
            $logo=($warehouse->warehouse_logo) ? $warehouse->warehouse_logo : 'logos/'.$Settings->logo;

    $path   = base_url() . 'assets/uploads/' . $logo;
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $data   = file_get_contents($path);
    //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $base64 = $path; ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <img src="<?= $base64; ?>" alt="<?=$this->Settings->site_name; ?>">
                </div>
        </td>
    </tr>
</table>

<hr style="color: #ccc; height: 1px; margin-bottom: 15px;">

<!-- بيانات المورد والعميل -->
<table class="info-table">
    <tr>
        <!-- من (المورد) -->
        <td class="info-box">
            <div class="info-title"><?php echo $this->lang->line('from'); ?>:</div>
            <strong><?= $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name; ?></strong><br>
            <?php
                echo $supplier->address . '<br />' . $supplier->city . ' ' . $supplier->postal_code . ' ' . $supplier->state . ' ' . $supplier->country . '<br>';
            ?>
            <strong><?php echo ($supplier->vat_no ? lang('VAT_Regesteration_Number') . ' : ' . $supplier->vat_no . '<br>' : '<br>');
					if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					echo  lang('BANK_NAME') . ' : ' . $supplier->cf1 . '<br />' . lang('IBAN') . ' : ' . $supplier->cf2. '<br>';
                    }

					echo  lang('tel') . ' : ' . $supplier->phone . '<br />' . lang('email') . ' : ' . $supplier->email; ?></strong>
        </td>
        <td width="4%"></td>
        <!-- إلى (العميل/الفرع) -->
        <td class="info-box">
            <div class="info-title"><?php echo $this->lang->line('to'); ?>:</div>
            <strong><?php
                    if(isset($Settings->isGrouping) && $Settings->isGrouping==1 && $branch){
                ?>
                    <?= $branch->branch_name ? $branch->branch_name : $Settings->site_name; ?>
                <?php
                    }else{
                ?>
                    <?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?>
                <?php
                    }
                ?></strong><br>
                <?php
                    $address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                ?><br>
                <strong><?php echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ' : ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ' : ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ' : ' . $warehouse->email : ''); ?></strong>
        </td>
    </tr>
</table>

<?php
                $col = $Settings->indian_gst ? 8 : 7;
                if ($inv->status == 'partial') {
                    $col++;
                }
                if($inv->status=="ordered"){
                    $col=$Settings->indian_gst ? 4 : 3;
                }else{
                    if ($Settings->tax1 && $inv->product_tax > 0) {
                        $col++;
                        $col++;
                    }
                }
            ?>

<!-- جدول المنتجات -->
<table class="items-table">
    <thead>
        <tr>
            <th><?= lang('No_sales'); ?></th>
                        <th style="width:50%;"><?= lang('Product_name_sales'); ?></th>
                        <th><?= lang('Product_unit_sales'); ?></th>
                        <?php if ($Settings->indian_gst) {
                        ?>
                            <th><?= lang('hsn_code'); ?></th>
                        <?php
                    } ?>
                        <th><?= lang('Quantity_sales'); ?></th>
                        <?php
                            if ($inv->status == 'partial') {
                                echo '<th>' . lang('received_parchase') . '</th>';
                            }
                        if($inv->status!="ordered"){
						?>
                        <th><?= lang('Unit_price_sales'); ?></th>
						<?php

						    echo '<th>' . lang('discount_sales') . '</th>';
                        if ($Settings->tax1 && $inv->product_tax > 0) {
                            echo '<th>' . lang('tax_percent_sales') . '</th>';
                            echo '<th>' . lang('VAT_amount_sales') . '</th>';
                        }

                        ?>
                        <th><?= lang('total_price_sales'); ?></th>
						<?php
						}
						?>
        </tr>
    </thead>
    <tbody>
        <?php
					$r     = 1;
                    $sr     = 0;
                    $tax_summary = [];
					$tquantity=0;
					$trquantity=0;

                    foreach ($rows as $row):
					if($row->quantity_received==0)
                    $tquantity=$tquantity+$row->quantity;
                    else
					$tquantity=$tquantity+$row->quantity_received;


				$sr=$r;
                    ?>
        <tr>
                            <td style="text-align:center; vertical-align:middle;"><?= $r; ?></td>
                            <td style="vertical-align:middle;">
                                <?= lang('code'). ' : ' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ': ' . $row->supplier_part_no : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                            </td>
                            <?php if ($Settings->indian_gst) {
                        ?>
                            <td style="text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php
                    } ?>
                            <?php
							    $product_unit_code="";
							    $quantity=0;
							    $quantity=$row->quantity;
								$product_unit_code=$row->product_unit_code;
								$quantity_received=0;
							    $quantity_received=$row->quantity_received;
								$real_unit_cost=0;
							    $real_unit_cost=$row->real_unit_cost;
								//if($inv->status=="partial" || $inv->status=="received" || $inv->status=="ordered"){
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$product_unit_code=$unit->name;
										if($inv->status=="partial" || $inv->status=="received"){
										if($unit->operator=='*'){
                                            if(str_replace("cpesoft_","",$this->db->database)!="deem"){
                                                $quantity=$quantity/$unit->operation_value;
                                            }

											$quantity_received=$quantity_received/$unit->operation_value;
											$real_unit_cost=$real_unit_cost*$unit->operation_value;
										}
										if($unit->operator=='/'){
											if(str_replace("cpesoft_","",$this->db->database)!="deem"){
                                                $quantity=$quantity*$unit->operation_value;
                                            }
											$quantity_received=$quantity_received*$unit->operation_value;
											$real_unit_cost=$real_unit_cost/$unit->operation_value;
										}
										}
									}
								}
								//}

							?>


							<td style="text-align:center; vertical-align:middle;"><?= $product_unit_code?></td>
                            <td style="text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($quantity)) ?></td>
                            <?php
                            if ($inv->status == 'partial') {
								echo '<td style="text-align:center;vertical-align:middle;">' . $this->sma->formatQuantity($quantity_received) . '</td>';
                            }
							if($inv->status!="ordered"){

                            ?>
                            <td>
                               <?= $this->sma->formatMoney($real_unit_cost,' '); ?>
                            </td>
							<?php
							    echo '<td style="vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';
                            ?>
							<?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="vertical-align:middle;">' . $row->tax . '</td>';
                                echo '<td style="vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';
                            }

                            ?>
                            <td><?= $this->sma->formatMoney(abs($row->subtotal),' '); ?></td>
							<?php
							}
							?>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="' . ($col + 1) . '" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
                        foreach ($return_rows as $row):
                        $tquantity=$tquantity+$row->quantity_received;

						?>
                            <tr class="warning">
                                <td style="text-align:center; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= lang('code'). ' : ' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ' : ' . $row->supplier_part_no : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ' : ' . $this->sma->hrsd($row->expiry) : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) {
                            ?>
                                <td style="text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php
                        } ?>



								<?php
								 $product_unit_code="";
							    $quantity=0;
							    $quantity=$row->quantity;
								$product_unit_code=$row->product_unit_code;
								$quantity_received=0;
							    $quantity_received=$row->quantity_received;
								$unit_cost=0;
							    $unit_cost=$row->unit_cost;

								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id){
										$product_unit_code=$unit->name;
										if($unit->operator=='*'){
											$quantity=$quantity/$unit->operation_value;
											$quantity_received=$quantity_received/$unit->operation_value;
											$unit_cost=$unit_cost*$unit->operation_value;
										}
										if($unit->operator=='/'){
											$quantity=$quantity*$unit->operation_value;
											$quantity_received=$quantity_received*$unit->operation_value;
											$unit_cost=$unit_cost/$unit->operation_value;
										}
									}
								}

								?>

								<td style="text-align:center; vertical-align:middle;"><?= $product_unit_code; ?></td>
                                <td style="text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($quantity)); ?></td>
                                <?php
                                if ($inv->status == 'partial') {
                                    echo '<td style="text-align:center;vertical-align:middle;">' . $product_unit_code . '</td>';

								} ?>
                                <td><?= $this->sma->formatMoney($unit_cost,' '); ?></td>
                                <?php
                                    echo '<td style="vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';

	    					        if ($Settings->tax1 && $inv->product_tax > 0) {
                                        echo '<td style="vertical-align:middle;">' . $row->tax . '</td>';
                                        echo '<td style="vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';
                                    }
                                ?>
                                <td><?= $this->sma->formatMoney(abs($row->subtotal),' '); ?></td>
                            </tr>
                            <?php
                            $r++;
							$trquantity=$trquantity+$quantity;

                        endforeach;
                    }
					//
                    ?>
    </tbody>
</table>

<br>

<!-- المبالغ والإجماليات -->
<table width="100%">
    <tr>
        <td width="45%" style="vertical-align: top;">
            <table class="items-table" style="width: <?= $inv->status=='ordered' ? '100%;' : '90%;'?>">
                <?php
					if($inv->status!="ordered"){
				?>
                <tr>


						<?php if($inv->total < 0){ ?>

					    <td class="bg-gray bold">عدد الأصناف المرتجعة / Item Return Total
                        </td>
						<?php }else{ ?>

                        <td class="bg-gray bold">عدد الأصناف المشتراه / Item Purchase Total

                        </td>
						<?php } ?>

                        <td><?= (int)$this->sma->formatDecimal(abs($sr),2); ?></td>



                </tr>
                <?php
					}
				?>
                <tr>
                    <td class="bg-gray bold">اجمالي الكمية / Total Quantity</td>
                    <td><?= $this->sma->formatDecimal(abs($tquantity+$trquantity),2); ?></td>
                </tr>
            </table>
        </td>
        <?php
			if($inv->status!="ordered"){
		?>

        <?php
			if(isset($inv->currency_code)){
				$currencyCode= $inv->currency_code;
				$currencyRate= $inv->currency_rate;
			}else{
				$currencyCode= $Settings->default_currency;
				$currencyRate= 1;
			}

		?>
        <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, $return_rows, ($return_purchase ? $inv->product_tax + $return_purchase->product_tax : $inv->product_tax), true) : ''; ?>

        <?php
			$tax_active_rate=0;
			if($inv->order_tax>0)
			$tax_active_rate=$inv->tax_active_rate;

		?>

        <td width="55%" style="vertical-align: top;">
            <table class="summary-table" style="width: 100%;">
                <tr>
                    <td class="bg-gray">إجمالي خصم العروض / Total Discounts</td>
                    <td class="text-left"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_discount + $return_purchase->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                </tr>
                <tr>
                    <td class="bg-gray">الإجمالي قبل الضريبة / Total Before VAT</td>
                    <?php if($inv->total < 0){ ?>
                        <td class="text-left"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount - $return_purchase->total_discount)) : abs($inv->total  - $inv->total_discount),$currencyCode); ?></td>
                    	<?php }else{ ?>
					    <td class="text-left"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount + $return_purchase->total_discount)) : $inv->total  - $inv->total_discount,$currencyCode); ?></td>
                    <?php } ?>
                </tr>
                <?php
					$VAT="0%";
					if($inv->total_tax!=0 && $inv->product_tax == 0)
					$VAT=$this->sma->formatMoney($inv->tax_active_rate)."%";
				    else if($inv->product_tax != 0)
					$VAT="";
					?>
                <tr>
                    <td class="bg-gray">ضريبة القيمة المضافة / VAT</td>
                    <td class="text-left"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_tax + $return_purchase->total_tax ) : abs($inv->total_tax) ,$currencyCode); ?></td>
                </tr>
                <tr style="background-color: #eaeff5;">
                    <td class="bold">المبلغ الكلي / Total Amount</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney($return_purchase ? ($inv->grand_total + $return_purchase->grand_total ) : abs($inv->grand_total) ,$currencyCode); ?></td>
                </tr>
                <tr>
                    <td>مدفوع / Paid</td>
                    <td class="text-left"><?= $this->sma->formatMoney($return_purchase ? ($inv->paid + $return_purchase->paid) : abs($inv->paid),$currencyCode); ?></td>
                </tr>
                <?php
					if(abs($inv->paid)!=0){
						foreach ($payments as $payment) {

						?>
                <tr>
                    <td>مدفوع بواسطة / Paid by</td>
                    <td class="text-left"><?= $this->sma->formatMoney(abs($payment->amount),$payment->currency); ?> <?php
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
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>

								  (<?= lang('bank_transfer') ?>)<br/><?= lang('bank_details').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)


							<?php
							 }
                            ?></td>
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
					if(abs($return_purchase->paid)!=0){
						if ($return_purchase) {

						?>
                <tr>
                    <td>مدفوع بواسطة / Paid by</td>
                    <td class="text-left"><?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?> <?php
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
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>

								  (<?= lang('bank_transfer') ?>)<br/><?= lang('bank_details').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)


							<?php
							 }
                            ?></td>
                </tr>
                <?php
					    }
					}
						?>
                <?php
					if($inv->discount_earned!=0){
					?>
                <tr>
                    <td class="bold">خصم مكتسب / Discount Earned</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_purchase ? ($inv->discount_earned  - abs($return_purchase->discount_earned)) : $inv->discount_earned),$currencyCode); ?></td>
                </tr>
                <?php
					}
				?>
                <tr>
                    <td class="bold">الرصيد / Balance</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(abs(($return_purchase ? ($inv->grand_total + $return_purchase->grand_total) : abs($inv->grand_total)) - ($return_purchase ? (($inv->paid + $inv->discount_earned) + ($return_purchase->paid + $return_purchase->discount_earned)) : abs($inv->paid + $inv->discount_earned))),$currencyCode); ?></td>
                </tr>
                <?php
					if($inv->shipping!=0 && $inv->total > 0){
						$shipping=$inv->shipping;
						if($inv->shipping_received!=0)
							$shipping=$inv->shipping_received;

					?>
                <tr>
                    <td class="bold">الشحن / Shipping</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_purchase ? ( $shipping ) : $shipping),$currencyCode); ?></td>
                </tr>
                <?php
					}
				?>
                <?php
					if($inv->customs_fees!=0){
					?>
                <tr>
                    <td class="bold">رسوم جمركية / Customs Fees</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_purchase ? ($inv->customs_fees + $return_purchase->customs_fees) : $inv->customs_fees),$currencyCode); ?></td>
                </tr>
                <?php
					}
				?>
                <?php
					if($inv->other_fees!=0){
					?>
                <tr>
                    <td class="bold">رسوم أخرى / Other Fees</td>
                    <td class="text-left bold"><?= $this->sma->formatMoney(($return_purchase ? ($inv->other_fees + $return_purchase->other_fees) : $inv->other_fees),$currencyCode); ?></td>
                </tr>
                <?php
					}
				?>

            </table>
        </td>
        <?php
			}
		?>
    </tr>
</table>
<?php
	if($inv->status=="ordered"){
?>
<div>
    <table width="100%">
        <tr>
            <td width="50%">
                <?= lang('supplier'); ?> : <br> <?= $supplier->company; ?>
            </td>
            <td width="50%" class="text-left">
                <?= lang('Ordered_by'); ?> : <br> <?= $created_by->first_name . ' ' . $created_by->last_name; ?>
            </td>
        </tr>
    </table>
</div>
<?php
	}
?>
<br>
<?php
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
?>



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