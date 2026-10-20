<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
th {
  font-size: 12px;
}

/*
.qrimg{
	padding-bottom:20px;
}
*/

.qrimg{
	padding-bottom:20px;
}

    table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }
	
	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt;}

 

@media print {

    .modal-content th,td {
  font-size: 10px;
}
                /*h1 {padding:1px;}*/
				.well { margin-bottom:-2px;padding:1px;}
				.well1 {margin-top:-15px;padding:1px;}
				
				.table td, .table th {padding: 0.2rem !important;}
				
				.col-xs-1 {
    width: 4.33333333%;
}
				.col-xs-11 {
    width: 95.66666667%;
}
		td {
    border: 1px solid #ddd;
    
}

.from,.to{margin-bottom:15px;margin-top:15px !important;}		
  
  
  div.divFooter {
    position: fixed;
    bottom: 0;
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
  height: 100%;
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
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
            <div class="row bold">
                    <div class="col-xs-12 no-print" >
                
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <button type="button "
            <a href="#" id="pdf" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" title="<?= lang('download_pdf') ?>">
                                
                                <i  class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                               
                 </a>
                           
            </button>
			</div>
           </div>
    <div id="pr_pdf">
		    <div class="row bold">
            <div class="col-xs-6 pull-left" style=" margin-bottom:10px;"> 
					<?php
					if($inv->status=="ordered"){
					?>
				    <span valign="top"><h1  style=" font-size: 25px;">Purchase&nbsp;Request&nbsp;/&nbsp;طلب&nbsp;شراء</h1><span>
					<?php
					}else if($inv->status=="pending"){
					?>
				    <span valign="top"><h1  style=" font-size: 25px;">Purchase&nbsp;Order&nbsp;/&nbsp;أمر&nbsp;شراء</h1><span>
					<?php
					}else{
					?>
				    <span valign="top"><h1  style=" font-size: 25px;">Purchase&nbsp;Invoice&nbsp;/&nbsp;فاتورة&nbsp;مشتريات</h1><span>
					<?php
					}
					?>
				    	
			</div>
					
             <div class="col-xs-6" >
					
            <?php 
			if ($logo) 
			{
    ?>
                <div class="pull-right" style="margin-top:10px;">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>">
                </div>
            <?php
} ?>
			</div>
			</div>
			
			
			<div class="row">
			<div class="col-xs-12">
			
			
			
			<div class="well well-sm">
                <div class="row bold">
                    <div class="col-xs-5">
                    <p class="bold">
                        <?= ($inv->status=="ordered" || $inv->status=="pending") ? lang('date') : lang('Inv_date'); ?> : <?= $this->sma->hrld($inv->date); ?><br>
                       <?php
					   if($inv->status!="ordered" && $inv->status!="pending"){
						?>
					   <?= lang('Invoice_No'); ?> : <?= $inv->id; ?><br>
					   <?php
					   }
					   ?>
                       
					   <?= lang('reference_no'); ?> : <?= $inv->reference_no; ?><br>
                        <!--<?php if (!empty($inv->supply_date)) { ?>
                        <?= lang('Supply_date'); ?> : <?= $this->sma->hrld($inv->supply_date); ?><br>
						<?php } ?>-->
                        <?php if (!empty($inv->due_date)) { ?>
                        <?= lang('Due_date'); ?> : <?= $this->sma->hrsd($inv->due_date); ?><br>
						<?php } ?>
                        <?php if (!empty($inv->return_purchase_ref)) {
        echo lang('return_ref') . ' : ' . $inv->return_purchase_ref;
        if ($inv->return_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('carspurchases/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
                         <?php if (!empty($inv->return_purchase_ex_ref)) {
        echo lang('return_ref_ex') . ' : ' . $inv->return_purchase_ex_ref;
        if ($inv->return_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('carspurchases/expense_note/' . $inv->return_ex_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
       <?php if (!empty($inv->return_surcharge_ex_ref)) {
        echo lang('return_ref_s_ex') . ' : ' . $inv->return_surcharge_ex_ref;
        if ($inv->return_id) {
            echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('carspurchases/expense_note/' . $inv->return_ex_s_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
        } else {
            echo '<br>';
        }
    } ?>
                        <?= lang('status'); ?> : <?= $inv->status=="returned" ? lang('Notice_debitor') : ($inv->receiving_status=="pending" ? lang('Purchase_invoice_pendding') : lang($inv->status)); ?><br>
                        <?= lang('payment_status'); ?> : <?= lang($inv->payment_status); ?>
                    </p>
                    </div>
                    <div class="col-xs-7 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <!--<?= $this->sma->qrcode('link', urlencode(admin_url('carspurchases/view/' . $inv->id)), 2); ?>-->
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="row" style="margin-bottom:15px;">
                <div class="col-xs-6">
                    <?php echo $this->lang->line('from'); ?> : 
                    <h2 style="margin-top:10px;"><?= $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name; ?></h2>
                    <?= $supplier->company                              && $supplier->company != '-' ? '' : 'Attn: ' . $supplier->name ?>

                    <?php
                    echo $supplier->address . '<br />' . $supplier->city . ' ' . $supplier->postal_code . ' ' . $supplier->state . ' ' . $supplier->country . '<br>';

                    /*echo '<p>';

                    if ($supplier->vat_no != '-' && $supplier->vat_no != '') {
                        echo '<br>' . lang('vat_no') . ': ' . $supplier->vat_no;
                    }
                    if ($supplier->gst_no != '-' && $supplier->gst_no != '') {
                        echo '<br>' . lang('gst_no') . ': ' . $supplier->gst_no;
                    }
                    if ($supplier->cf1 != '-' && $supplier->cf1 != '') {
                        echo '<br>' . lang('scf1') . ': ' . $supplier->cf1;
                    }
                    if ($supplier->cf2 != '-' && $supplier->cf2 != '') {
                        echo '<br>' . lang('scf2') . ': ' . $supplier->cf2;
                    }
                    if ($supplier->cf3 != '-' && $supplier->cf3 != '') {
                        echo '<br>' . lang('scf3') . ': ' . $supplier->cf3;
                    }
                    if ($supplier->cf4 != '-' && $supplier->cf4 != '') {
                        echo '<br>' . lang('scf4') . ': ' . $supplier->cf4;
                    }
                    if ($supplier->cf5 != '-' && $supplier->cf5 != '') {
                        echo '<br>' . lang('scf5') . ': ' . $supplier->cf5;
                    }
                    if ($supplier->cf6 != '-' && $supplier->cf6 != '') {
                        echo '<br>' . lang('scf6') . ': ' . $supplier->cf6;
                    }

                    echo '</p>';*/
                    echo ($supplier->vat_no ? lang('VAT_Regesteration_Number') . ' : ' . $supplier->vat_no . '<br>' : '<br>');
					if(isset($this->Settings->IsConstruction) && $this->Settings->IsConstruction==1){
					echo  lang('BANK_NAME') . ' : ' . $supplier->cf1 . '<br />' . lang('IBAN') . ' : ' . $supplier->cf2. '<br>';
                    }
					
					echo  lang('tel') . ' : ' . $supplier->phone . '<br />' . lang('email') . ' : ' . $supplier->email;
                    ?>
                </div>
                <div class="col-xs-6">
                    <?php echo $this->lang->line('to'); ?> : <br/>
                    <h2 style="margin-top:10px;"><?= $warehouse->branch_name ? $warehouse->branch_name : $Settings->site_name; ?></h2>
					
                    <!--<?= $warehouse->name ?>-->

                    <?php
                    $address=$warehouse->address;
                    $address=str_replace("<p>","",$warehouse->address);
                    $address=str_replace("</p>","<br>",$address);
                    echo $address;
                    echo($warehouse->vat_reg_no ? lang('VAT_Regesteration_Number') . ' : ' . $warehouse->vat_reg_no . '<br>' : '') . ($warehouse->phone ? lang('tel') . ' : ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? lang('email') . ' : ' . $warehouse->email : '');
                    ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action print-table order-table pdftable">

                    <thead>

                    <tr style="-webkit-print-color-adjust:exact;">
                        <th><?= lang('no_sales'); ?></th>
                        <!--<th><?= lang('product_code_sales'); ?></th>-->
                        <th class="col-xs-12"><?= lang('product_name_sales'); ?></th>
                       <!-- <th><?= lang('product_unit_sales'); ?></th> -->
                        <?php if ($Settings->indian_gst) {
                        ?>
                            <th><?= lang('hsn_code'); ?></th>
                        <?php
                    } ?>
                        <th><?= lang('quantity_sales'); ?></th>
                        <?php
                            if ($inv->status == 'partial') {
                                echo '<th>' . lang('received_parchase') . '</th>';
                            }
                        if($inv->status!="ordered"){
						?>
                        <th><?= lang('unit_price_sales'); ?></th>
						<!--<th><?= lang('value'); ?></th>-->
                        <?php
						
						//if ($Settings->product_discount && $inv->product_discount != -1) {
                            echo '<th>' . lang('discount_sales') . '</th>';
                        //}
						//echo '<th>' . lang('subtotal') . '</th>';
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
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <!--<td style="vertical-align:middle;">
                                <?= $row->product_code?>
                            </td>-->
							<td style="vertical-align:middle;">
                                <?= lang('code'). ' : ' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ': ' . $row->supplier_part_no : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                            </td>
                            <?php if ($Settings->indian_gst) {
                        ?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
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
											$quantity=$quantity/$unit->operation_value;
											$quantity_received=$quantity_received/$unit->operation_value;
											$real_unit_cost=$real_unit_cost*$unit->operation_value;
										}
										if($unit->operator=='/'){
											$quantity=$quantity*$unit->operation_value;
											$quantity_received=$quantity_received*$unit->operation_value;
											$real_unit_cost=$real_unit_cost/$unit->operation_value;
										}
										}										
									}
								}
								//}
								
							?>
							
							
							<!-- <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $product_unit_code?></td> -->
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($quantity)) ?></td>
                            <?php
                            if ($inv->status == 'partial') {
								/*$product_unit_code="";
								$product_unit_code=$row->product_unit_code;
								$product_unit_base='';
								foreach ($units as $unit){
									if($unit->id == $row->product_unit_id)
										$product_unit_base=$unit->base_unit;
								}
								if($product_unit_base){
								foreach ($units as $unit){
									if($unit->id == $product_unit_base)
										$product_unit_code=$unit->name;
								}
									
								}*/
								
								
								
								
								
                                echo '<td style="text-align:center;vertical-align:middle;width:80px;">' . $this->sma->formatQuantity($quantity_received) . '</td>';
                            }
							if($inv->status!="ordered"){
						
                            ?>
                            <td style="text-align:right; width:100px;">
                               <!-- <?= $row->unit_cost != $row->real_unit_cost && $row->item_discount > 0 ? '<del>' . $this->sma->formatMoney($row->real_unit_cost,' ') . '</del>' : ''; ?>
                                -->
								<?= $this->sma->formatMoney($real_unit_cost,' '); ?>
                            </td>
							<!--<td style="text-align:right; width:100px;">
                                <?= $this->sma->formatMoney($row->real_unit_cost * $row->quantity_received,' ')  ?>
                            </td>-->
                            <?php
							//if ($Settings->product_discount && $inv->product_discount != -1) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';
                            //}?>
							<!--<td style="text-align:right; width:100px;">
                                <?= $this->sma->formatMoney($row->real_unit_cost * $row->quantity_received,' ')  ?>
                            </td>-->
							<?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $row->tax . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';
                            }
                            
                            ?>
                            <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney(abs($row->subtotal),' '); ?></td>
							<?php
							}
							?>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
                        foreach ($return_rows as $row):
                        $tquantity=$tquantity+$row->quantity_received;
						
						?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <!--<td style="vertical-align:middle;">
                                    <?= $row->product_code; ?>
                                </td>-->
                                <td style="vertical-align:middle;">
                                    <?= lang('code'). ' : ' .$row->product_code. '<br>' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ' : ' . $row->supplier_part_no : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ' : ' . $this->sma->hrsd($row->expiry) : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) {
                            ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
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
								
								<!--<td style="width: 80px; text-align:center; vertical-align:middle;"><?= $product_unit_code; ?></td> -->
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity(abs($quantity)); ?></td>
                                <?php
                                if ($inv->status == 'partial') {
                                  //  echo '<td style="text-align:center;vertical-align:middle;width:80px;">' . $product_unit_code . '</td>';
                                 //   echo '<td style="text-align:center;vertical-align:middle;width:80px;">' . $this->sma->formatQuantity(abs($row->quantity_received)) . '</td>';
                                 //    echo '<td style="text-align:center;vertical-align:middle;width:80px;">' . $this->sma->formatQuantity($row->quantity_received) . ' ' . $row->product_unit_code . '</td>';
                            
								} ?>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatMoney($unit_cost,' '); ?></td>
                                <?php
                               //if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_discount),' ') . '</td>';
                             //}
						if ($Settings->tax1 && $inv->product_tax > 0) {
                                //    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $row->tax . '</td>';
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney(abs($row->item_tax),' ') . '</td>';
                            }
                         ?>
                                <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney(abs($row->subtotal),' '); ?></td>
                            </tr>
                            <?php
                            $r++;
							$trquantity=$trquantity+$quantity;
						
                        endforeach;
                    }
					//
                    ?>
					<tr>
                            <td colspan="3" style="text-align:center; width:40px; vertical-align:middle;"><b>Total&nbsp;Quantity&nbsp;/&nbsp;اجمالي&nbsp;الكمية</b></td>
							<td style=" font-weight:bold;"><?= $this->sma->formatDecimal(abs($tquantity+$trquantity),2); ?></td>
							<?php
							if($inv->status!="ordered"){
								?>
								<td colspan="3" style=" font-weight:bold;"></td>
								<?php
					}
					?>
                    
						
                    </tr>
                    </tbody>
                    <!--<tfoot>
                    <?php
                    $col = $Settings->indian_gst ? 10 : 9;
                    if ($inv->status == 'partial') {
                        $col++;
                    }
                    if ($Settings->product_discount && $inv->product_discount != -1) {
                        $col++;
                    }
                    if ($Settings->tax1 && $inv->product_tax > -1) {
                        $col++;
                    }
                    if ($Settings->product_discount && $inv->product_discount != -1 && $Settings->tax1 && $inv->product_tax > -1) {
                        $tcol = $col - 2;
                    } elseif ($Settings->product_discount && $inv->product_discount != -1) {
                        $tcol = $col - 1;
                    } elseif ($Settings->tax1 && $inv->product_tax > -1) {
                        $tcol = $col - 1;
                    } else {
                        $tcol = $col;
                    }
                    ?>
                    <?php if ($inv->grand_total != $inv->total) {
                        ?>
                        <tr>
                            <td colspan="<?= $tcol-2; ?>" class="text-right"><?= lang('total'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <?php
							if ($Settings->product_discount && $inv->product_discount != -1) {
                            echo '<td class="text-right">' . $this->sma->formatMoney($return_purchase ? ($inv->product_discount + $return_purchase->product_discount) : $inv->product_discount) . '</td>';
                        }?>
						<td></td>
						<td></td>
						<?php
                            if ($Settings->tax1 && $inv->product_tax > -1) {
                                echo '<td class="text-right">' . $this->sma->formatMoney($return_purchase ? ($inv->product_tax + $return_purchase->product_tax) : $inv->product_tax) . '</td>';
                            }
                         ?>
                            <td class="text-right"><?= $this->sma->formatMoney($return_purchase ? (($inv->total + $inv->product_tax) + ($return_purchase->total + $return_purchase->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php
                    } ?>
                    <?php
                    if ($return_purchase) {
                        echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('return_total') . ' (' . $default_currency->code . ')</td><td class="text-right">' . $this->sma->formatMoney($return_purchase->grand_total) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('return_surcharge') . ' (' . $default_currency->code . ')</td><td class="text-right">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>

                    <?php if ($Settings->indian_gst) {
                        if ($inv->cgst > 0) {
                            $cgst = $return_purchase ? $inv->cgst + $return_purchase->cgst : $inv->cgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_purchase ? $inv->sgst + $return_purchase->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_purchase ? $inv->igst + $return_purchase->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>

                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('order_discount') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($inv->order_discount_id ? '<small>(' . $inv->order_discount_id . ')</small> ' : '') . $this->sma->formatMoney($return_purchase ? ($inv->order_discount + $return_purchase->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('order_tax') . ' (' . $default_currency->code . ')</td><td class="text-right">' . $this->sma->formatMoney($return_purchase ? ($inv->order_tax + $return_purchase->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('shipping') . ' (' . $default_currency->code . ')</td><td class="text-right">' . $this->sma->formatMoney($inv->shipping) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('total_amount_Tax_Invoice'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->grand_total + $return_purchase->grand_total) : $inv->grand_total); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('paid'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->paid + $return_purchase->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang('balance'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->grand_total + $return_purchase->grand_total) : $inv->grand_total) - ($return_purchase ? ($inv->paid + $return_purchase->paid) : $inv->paid)); ?></td>
                    </tr>

                    </tfoot>-->
                </table>
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
			

            <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, $return_rows, ($return_purchase ? $inv->product_tax + $return_purchase->product_tax : $inv->product_tax), true) : ''; ?>

            <?php
			$tax_active_rate=0;
			if($inv->order_tax>0)
			$tax_active_rate=$inv->tax_active_rate;
				
			?>
			<div class="row inv-details">
			<?php
			if($inv->status!="ordered"){
						?>
               <div class="col-xs-6 pull-right">
                    <div class="well well-sm">
                        <p>
                            <table class="table table-striped jambo_table bulk_action print-table order-table pdftable">
				        <tfoot>
						<tr>
						
						<?php if ($Settings->user_language == 'arabic') {
						?>
	
						
						<?php if($inv->total < 0){ ?>
						
					    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">عدد الأصناف المرتجعة
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">عدد الأصناف المشتراه
                            
                        </td>
						<?php } ?>
					    
                        <td style="text-align:left; font-weight:bold;"><?= (int)$this->sma->formatDecimal(abs($sr),2); ?></td>
						
						<?php if($inv->total < 0){ ?>
					    <td colspan="4"
                            style="text-align:left;font-weight:bold;">Item Return Total
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Item Purchase Total
                            
                        </td>
						<?php } ?>
					    <?php }else{ ?>
						<?php if($inv->total < 0){ ?>
						
					    <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Item Return Total
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Item Purchase Total
                            
                        </td>
						<?php } ?>
					    
                        <td style="text-align:left; font-weight:bold;"><?= (int)$this->sma->formatDecimal(abs($sr),2); ?></td>
						
						<?php if($inv->total < 0){ ?>
					    <td colspan="4"
                            style="text-align:right;font-weight:bold;">عدد الأصناف المرتجعة
                        </td>
						<?php }else{ ?>
					    
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">عدد الأصناف المشتراه
                            
                        </td>
						<?php } ?>
					    
					    <?php } ?>
					    
                    </tr>
						
						<!--
                        <?php if($inv->total < 0){ ?>
					    <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('items_sold_purchase_total_return'); ?>
                        </td>
						<?php }else{ ?>
					    <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('items_sold_purchase_total'); ?>
                        </td>
						<?php } ?>
					        
                        
                        <td style="text-align:right; font-weight:bold;"><?= (int)$this->sma->formatDecimal(abs($tquantity),2); ?></td>
                    </tr>-->
                    <tr>
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	
						
						
					    
                        <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">إجمالي خصم العروض
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_discount + $return_purchase->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Total Discounts
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Total Discounts
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_discount + $return_purchase->total_discount) : abs($inv->total_discount),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">إجمالي خصم العروض
                            
                        </td>
						
					    <?php } ?>
					
					
					<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('discount_total'); ?>
                            
                        </td>
						
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_discount + $return_purchase->total_discount) : abs($inv->total_discount)); ?></td>
					-->	
                    </tr>
                    <tr>
					
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الإجمالي قبل الضريبة
                            
                        </td>
						
                        <?php if($inv->total < 0){ ?>
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount - $return_purchase->total_discount)) : abs($inv->total  - $inv->total_discount),$currencyCode); ?></td>
                    	<?php }else{ ?>
					    <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount + $return_purchase->total_discount)) : $inv->total  - $inv->total_discount,$currencyCode); ?></td>
                    	<?php } ?>
						
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Total Before VAT
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Total Before VAT
                            
                        </td>
						<?php if($inv->total < 0){ ?>
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount - $return_purchase->total_discount)) : abs($inv->total  - $inv->total_discount),$currencyCode); ?></td>
                    	<?php }else{ ?>
					    <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount + $return_purchase->total_discount)) : $inv->total  - $inv->total_discount,$currencyCode); ?></td>
                    	<?php } ?>
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الإجمالي قبل الضريبة
                            
                        </td>
						
					    <?php } ?>
					   
                       <!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('VAT_before_total'); ?>
                           
                        </td>
						<?php if($inv->total < 0){ ?>
					    <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount - $return_purchase->total_discount)) : abs($inv->total  - $inv->total_discount),$currencyCode); ?></td>
                    	<?php }else{ ?>
					    <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total + $return_purchase->total - ($inv->total_discount + $return_purchase->total_discount)) : $inv->total  - $inv->total_discount,$currencyCode); ?></td>
                    	<?php } ?>
						-->
					</tr>
                    <tr>
					<?php
					$VAT="0%";
					if($inv->total_tax!=0 && $inv->product_tax == 0)
					$VAT=$this->sma->formatMoney($inv->tax_active_rate)."%";
				    else if($inv->product_tax != 0)
					$VAT="";
					?>
					
                        <?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">ضريبة القيمة المضافة
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_tax + $return_purchase->total_tax ) : abs($inv->total_tax) ,$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">VAT
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">VAT
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_tax + $return_purchase->total_tax ) : abs($inv->total_tax) ,$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">ضريبة القيمة المضافة
                            
                        </td>
						
					    <?php } ?>
						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('VAT') ?>
                           
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->total_tax + $return_purchase->total_tax ) : abs($inv->total_tax) ); ?></td>
						-->
                    </tr>
						<tr>
						
						
						
						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">المبلغ الكلي
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->grand_total + $return_purchase->grand_total ) : abs($inv->grand_total) ,$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Total Amount
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Total Amount
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->grand_total + $return_purchase->grand_total ) : abs($inv->grand_total) ,$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">المبلغ الكلي
                            
                        </td>
						
					    <?php } ?>
					   
					   <!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('total_amount_Tax_Invoice'); ?>
                           
                        </td>
						
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->grand_total + $return_purchase->grand_total ) : abs($inv->grand_total) ); ?></td>
						-->
                    </tr>
                    

                    </tfoot>
                </table>
						</p>
                    </div>
                </div>
                <div class="col-xs-6 pull-left">
                    <div class="well well-sm">
                        <p>
                        <table class="table table-striped jambo_table bulk_action print-table order-table pdftable" >
				        <tfoot>
					<tr>
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->paid + $return_purchase->paid) : abs($inv->paid),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->paid + $return_purchase->paid) : abs($inv->paid),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">مدفوع
                            
                        </td>
						
					    <?php } ?>
					   <!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('paid_tax_invoice'); ?>
                            
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->paid + $return_purchase->paid) : abs($inv->paid)); ?></td>
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
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?php
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
                            ?>
							<?= $this->sma->formatMoney(abs($payment->amount),$payment->currency); ?>
						
						</td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(abs($payment->amount),$payment->currency); ?>
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
							<?php
							 if ($payment->paid_by == 'bank_transfer'){
								 ?>
								 
								  <br/>(<?= lang('bank_transfer') ?>)<br/><?= lang('bank_details').' : ' ?>
								  (<?= lang($payment->cheque_no) ?>)
								  
								  
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
                            style=" font-weight:bold;"><?= lang('paid_by_tax_invoice'); ?>
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
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(abs($payment->amount)); ?></td>
						-->
                    </tr>
					
					
					
					<tr>
					
					
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">تاريخ الدفع
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->hrld($payment->date); ?></td>
                    	
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid Date
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid Date
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->hrld($payment->date); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">تاريخ الدفع
                            
                        </td>
						
					    <?php } ?>
					   
                        
						
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
					
					
					
					
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">مدفوع بواسطة

                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;">
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
						<?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Paid by
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_payments[0]->amount),$return_payments[0]->currency); ?>
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
					   
					
					
					
                    </tr>
                    
					<?php
						}
						}
                    ?>
					
					<?php
					if($inv->discount_earned!=0){
					?>
					<tr>
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">خصم مكتسب
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->discount_earned  - abs($return_purchase->discount_earned)) : $inv->discount_earned),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Discount Earned
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Discount Earned
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->discount_earned - abs($return_purchase->discount_earned)) : $inv->discount_earned),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">خصم مكتسب
                            
                        </td>
						
					    <?php } ?>
						<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('shipping_tax_invoice'); ?>
                            
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->shipping - $return_purchase->shipping) : $inv->shipping)); ?></td>
						-->
                    </tr>
					<?php
					}
					?>
					
                    <tr>
                        
						
						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الرصيد
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(abs(($return_purchase ? ($inv->grand_total + $return_purchase->grand_total) : abs($inv->grand_total)) - ($return_purchase ? (($inv->paid + $inv->discount_earned) + ($return_purchase->paid + $return_purchase->discount_earned)) : abs($inv->paid + $inv->discount_earned))),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Balance
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Balance
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(abs(($return_purchase ? ($inv->grand_total + $return_purchase->grand_total) : abs($inv->grand_total)) - ($return_purchase ? (($inv->paid + $inv->discount_earned) + ($return_purchase->paid + $return_purchase->discount_earned)) : abs($inv->paid + $inv->discount_earned))),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الرصيد
                            
                        </td>
						<?php } ?>
						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('balance_tax_invoice'); ?>
                           
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->grand_total + $return_purchase->grand_total) : abs($inv->grand_total)) - ($return_purchase ? ($inv->paid + $return_purchase->paid) : abs($inv->paid))); ?></td>
						
						-->
                    </tr>
					<?php
					if($inv->shipping!=0 && $inv->total > 0){
						$shipping=$inv->shipping;
						if($inv->shipping_received!=0)
							$shipping=$inv->shipping_received;
						
					?>
					<tr>
                        
						
						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">الشحن
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ( $shipping ) : $shipping),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Shipping
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Shipping
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ( $shipping ) : $shipping),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">الشحن
                            
                        </td>
						
					    <?php } ?>
						
						<!--
						
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('shipping_tax_invoice'); ?>
                            
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ( $shipping ) : $shipping)); ?></td>
						-->
                    </tr>
					<?php
					}
					
					if($inv->customs_fees!=0){
					?>
					<tr>
					
					
					<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">رسوم جمركية
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->customs_fees + $return_purchase->customs_fees) : $inv->customs_fees),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Customs Fees
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Customs Fees
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->customs_fees + $return_purchase->customs_fees) : $inv->customs_fees),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">رسوم جمركية
                            
                        </td>
						
					    <?php } ?>
						
						<!--
                        <td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('customs_fees_tax_invoice'); ?>
                            
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->customs_fees + $return_purchase->customs_fees) : $inv->customs_fees),$currencyCode); ?></td>
						-->
                    </tr>
					<?php
					}
					?>
					<?php
					if($inv->other_fees!=0){
					?>
					<tr>
                        
						<?php if ($Settings->user_language == 'arabic') {
						?>
	                    <td colspan="<?= $col-4; ?>" style=" text-align:right;font-weight:bold;">رسوم أخرى
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->other_fees + $return_purchase->other_fees) : $inv->other_fees),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:left;font-weight:bold;">Other Fees
                            
                        </td>
						<?php }else{ ?>
						
                        <td colspan="<?= $col-4; ?>" style=" text-align:left;font-weight:bold;">Other Fees
                            
                        </td>
						
                        <td style="text-align:left; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->other_fees + $return_purchase->other_fees) : $inv->other_fees),$currencyCode); ?></td>
                    	
						
                        <td colspan="4"
                            style=" text-align:right;font-weight:bold;">رسوم أخرى
                            
                        </td>
						
					    <?php } ?>
						
						<!--
						<td colspan="<?= $col; ?>"
                            style=" font-weight:bold;"><?= lang('other_fees_tax_invoice'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
						
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->other_fees + $return_purchase->other_fees) : $inv->other_fees)); ?></td>
						-->
                    </tr>
					<?php
					}
					?>
					

                    </tfoot>
                </table>
            
						</p>
                    </div>
                </div>
				
				
				
				
				
				<?php
				}else{
				?>
				<div class="clearfix"></div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('supplier'); ?> : <br> <?= $supplier->company; ?> </p>
                </div>
				<div class="col-xs-4">
                    <p></p>
                </div>
				<div class="col-xs-4" style="padding-bottom:10px;padding-top:10px;">
                    <p><?= lang('Ordered_by'); ?> : <br> <?= $created_by->first_name . ' ' . $created_by->last_name; ?></p>
                </div>
            	<?php
				}
				?>
				
				
				<div class="clearfix"></div>
			    <div class="col-xs-12">
                    <?php
                        if ($inv->note || $inv->note != '') {
                            ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang('note'); ?> : </p>
                                <div><?= $this->sma->decode_html($inv->note); ?></div>
                            </div>
                        <?php
                        }
                        ?>
                </div>
				<div class="clearfix"></div>

                <div class="col-xs-5 pull-right">
                    <div class="well well-sm">
                        <p>
                            <?= lang('created_by'); ?> : <?= $created_by->first_name . ' ' . $created_by->last_name; ?> <br>
                            <?= lang('date'); ?> : <?= $this->sma->hrld($inv->date); ?>
                        </p>
                        <?php if ($inv->updated_by) {
                            ?>
                        <p>
                            <?= lang('updated_by'); ?> : <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?><br>
                            <?= lang('update_at'); ?> : <?= $this->sma->hrld($inv->updated_at); ?>
                        </p>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>
			
			
			</div><!--  end"pr_pdf"-->	
			</div>
            </div>
			
            <?php if (!$Supplier || !$Customer) {/*
                            ?>
                <div class="buttons">
                    <?php if ($inv->attachment) {
                                ?>
                        <div class="btn-group">
                            <a href="<?= admin_url('welcome/download/' . $inv->attachment) ?>" class="tip btn btn-primary" title="<?= lang('attachment') ?>">
                                <i class="fa fa-chain"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('attachment') ?></span>
                            </a>
                        </div>
                    <?php
                            } ?>
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <a href="<?= admin_url('carspurchases/add_payment/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal2" class="tip btn btn-primary" title="<?= lang('add_payment') ?>">
                                <i class="fa fa-dollar"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('add_payment') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('carspurchases/email/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal2" class="tip btn btn-primary" title="<?= lang('email') ?>">
                                <i class="fa fa-envelope-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('email') ?></span>
                            </a>
                        </div>
                        <!--<div class="btn-group">
                            <a href="<?= admin_url('carspurchases/pdf/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('download_pdf') ?>">
                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                            </a>
                        </div>-->
                        <div class="btn-group">
                            <a href="<?= admin_url('carspurchases/edit/' . $inv->id) ?>" class="tip btn btn-warning sledit" title="<?= lang('edit') ?>">
                                <i class="fa fa-edit"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="#" class="tip btn btn-danger bpo" title="<b><?= $this->lang->line('delete') ?></b>"
                                data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('carspurchases/delete/' . $inv->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                                data-html="true" data-placement="top">
                                <i class="fa fa-trash-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php
                       */ } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });
     $('#pdf').click(function (event) {
                event.preventDefault();
        PrPDF('pr_pdf','<?= lang('purchases'. $inv->id); ?>');
		        return false;
            });
</script>
