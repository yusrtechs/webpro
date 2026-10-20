<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if ($modal) {
    ?>
<div class="modal-dialog no-modal-header" role="document"><div class="modal-content"><div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
    <?php
} else {
        ?><!doctype html>
    <html id="hp">
    <head>
        <meta charset="utf-8">
        <title><?=$page_title . ' ' . lang('no') . ' ' . $inv->id; ?></title>
        <base href="<?=base_url()?>"/>
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <link rel="shortcut icon" href="<?=$assets?>images/icon.png"/>
        <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
        <style type="text/css" media="all">
            body { color: #000; }
            .tbl table { max-width: 74mm; margin-left: 2mm;margin-right: 2mm;  }
			 th {
				font-size:12px;
				width:100%;
	line-height: 1.42857143;
	vertical-align: bottom;
    border-bottom: 2px solid #ddd;
}
			.tbl  td {
	line-height: 1.42857143;
	padding-right:2px;
    vertical-align: top;
    border-bottom: 2px solid #ddd;
}
            #wrapper { max-width: 74mm; margin: 0 auto; padding-top: 20px; padding-right: 10px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; font-weight: bold;}
            h4 { font-weight: bold;font-size:14px; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
                #wrapper { max-width: 74mm; width: 100%; min-width: 250px; margin: 0 auto; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }
        </style>
    </head>

    <body>
        <?php
    } ?>
    <div id="wrapper">
        <div id="receiptData">
            <div class="no-print notorder notorder1">
                <?php
                if ($message) {
                    ?>
                    <div class="alert alert-success">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <?=is_array($message) ? print_r($message, true) : $message; ?>
                    </div>
                    <?php
                } ?>
            </div>
            <div id="receipt-data">
                <div class="text-center">
                    <?= !empty($biller->logo) ? '<img src="' . base_url('assets/uploads/logos/' . $Settings->logo) . '" alt="">' : ''; ?>
                    <h3 style="text-transform:uppercase;"><?= $Settings->site_name; ?></h3>
                    <div class="notorder" style="border-bottom: 2px solid #ddd !important;">
					<?php
                    echo 'VAT No  '.$warehouse->vat_reg_no.'  الرقم الضريبي<br/>';
                    echo 'Tel No  '.$warehouse->phone.'  الهاتف';
                    echo $warehouse->address;
                    ?>
					</div>
                </div>
                <div class="text-center"  style="border-bottom: 2px solid #ddd !important;">
                    <?php
					if($inv->total<0) {

                    echo '<h4 class="notorder">Return Invoice  فاتورة مرتجع</h4>';
                    echo '<h4>Bill No '.$inv->id.' فاتورة رقم</h4>';
					}else{
					echo '<h4 class="notorder">Customer Invoice  فاتورة العميل</h4>';
                    echo '<h4>Bill No '.$inv->id.' فاتورة رقم</h4>';


					}
                    ?>
                </div>
                <div class="text-center" style="border-bottom: 2px solid #ddd !important;">
					<?php

                echo '<p>Date  ' . $this->sma->hrld($inv->date) . ' التاريخ<br>';
				echo '<p class="notorder">Reference : ' . $inv->reference_no . ' المرجع';

                        echo '<br/>';

                if (!empty($inv->return_sale_ref)) {
                    echo '<p class="notorder">Return Reference : ' . $inv->return_sale_ref . ' مرجع المرتجع';
                    //if ($inv->return_id) {
                     //   echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('sales/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
                    //} else {

                    //}
                }
                echo '</p>';
                echo 'Casher ' . $created_by->first_name . ' ' . $created_by->last_name . ' البائع	</p>';
                ?>
				</div>

				<?php
				if($pos_settings->order_type==1){
					?>
				<div class="text-center"  style="border-bottom: 2px solid #ddd !important;">
                    <?php
					if($inv->order_type==1)
                    echo '<h3>Dine inn  محلي</h3>';
                    if($inv->order_type==2)
                    echo '<h3>Take away  سفري</h3>';
                    if($inv->order_type==3)
                    echo '<h3>Delivery  توصيل</h3>';



                    ?>

                </div>
                <?php
				}
				?>


                <table class="tbl">
                    <thead>
                        <tr>
                            <?php
							//if($inv->product_tax!=0){
								?>
                            <!--<th colspan="3" class="text-center">Description <br/> وصف الصنف</th>
                            <th class="text-center">QTY  <br/> الكمية</th>
                            <th class="text-center notorder">Price  <br/> سعر </th>
							<th class="text-center notorder">VAT  <br/> ضريبة</th>
							<th class="text-center notorder">Total  <br/> الإجمالي</th>-->
							<?php
							//}else{
							?>
                            <th colspan="4" class="text-center">Description <br/> وصف الصنف</th>
                            <th class="text-center">QTY  <br/> الكمية</th>
                            <th class="text-center notorder">Price  <br/> سعر </th>
							<th class="text-center notorder">Total  <br/> الإجمالي</th>
							<?php
							//}
							?>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $r           = 1;
                        $category    = 0;
                        $tax_summary = [];

                        foreach ($rows as $row) {
                            //if($inv->product_tax!=0){
                            //echo '<tr><td colspan="3" class="no-border" style="font-size:12px;" ><b>' . product_name($row->product_name, ($printer ? $printer->char_per_line : null)) . '</b></td><td class="no-border "><span  style="width:40px;font-size:11px;">' . $this->sma->formatQuantity($row->unit_quantity) . ' </span></td><td class="no-border notorder"><span  style="width:40px;font-size:11px;"> ' . $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4)) . '</span></td><td class="no-border notorder notorder" ><span  style="width:40px;font-size:11px;">' . $this->sma->formatMoney($row->item_tax) . '</span></td><td class="no-border notorder" ><span  style="width:40px;font-size:11px;">' . $this->sma->formatMoney($row->subtotal) . '</span></td></tr>';
                            //}else{
							echo '<tr><td colspan="4" class="no-border" style="font-size:12px;" ><b>' . product_name($row->product_name, ($printer ? $printer->char_per_line : null)) . '</b></td><td class="no-border "><span  style="width:40px;font-size:12px;">' . $this->sma->formatQuantity($row->unit_quantity) . ' </span></td><td class="no-border notorder"><span  style="width:40px;font-size:12px;"> ' . $this->sma->formatMoney($row->net_unit_price + $this->sma->formatDecimal($row->item_discount / $row->unit_quantity,4)) . '</span></td><td class="no-border notorder notorder" ><span  style="width:40px;font-size:12px;">' . $this->sma->formatMoney($row->subtotal) . '</span></td></tr>';

							//}
							if (!empty($row->second_name)) {
                                echo '<tr><td colspan="7" class="no-border" style="padding-top:0;padding-bottom:0;font-size:12px;">' . $row->second_name . '</td></tr>';
                            }
                            //echo '<tr><td colspan="4" class="no-border border-bottom">' . ($row->item_tax != 0 ? lang('tax') . ' <small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small> ' . $this->sma->formatMoney($row->item_tax) . ($row->hsn_code ? ' (' . lang('hsn_code') . ': ' . $row->hsn_code . ')' : '') : '') . '</td></tr>';
                            $r++;
                        }
                        if ($return_rows) {
                            echo '<tr><td colspan="7" class="no-border"><strong>Returned Items  / الأصناف المرتجعه</strong></td></tr>';
                            foreach ($return_rows as $row) {
                                //if($inv->product_tax!=0){
                            //echo '<tr><td colspan="3" class="no-border" style="font-size:12px;" >' . product_name($row->product_name, ($printer ? $printer->char_per_line : null)) . '</td><td class="no-border "><span  style="width:40px;font-size:12px;">' . $this->sma->formatQuantity($row->unit_quantity) . ' </span></td><td class="no-border notorder"><span  style="width:40px;font-size:12px;"> ' . $this->sma->formatMoney($row->real_unit_price) . '</span></td><td class="no-border notorder" ><span class="pull-left" style="width:40px;font-size:11px;">' . $this->sma->formatMoney($row->item_tax) . '</span></td><td class="no-border notorder" ><span  style="width:40px;font-size:12px;">' . $this->sma->formatMoney($row->subtotal) . '</span></td></tr>';
                            //}else{
							echo '<tr><td colspan="4" class="no-border" style="font-size:12px;" >' . product_name($row->product_name, ($printer ? $printer->char_per_line : null)) . '</td><td class="no-border "><span  style="width:40px;font-size:12px;">' . $this->sma->formatQuantity($row->unit_quantity) . ' </span></td><td class="no-border notorder"><span  style="width:40px;font-size:12px;"> ' . $this->sma->formatMoney($row->real_unit_price) . '</span></td><td class="no-border notorder" ><span  style="width:40px;font-size:12px;">' . $this->sma->formatMoney($row->subtotal) . '</span></td></tr>';
                            //}
								/*if ($pos_settings->item_order == 1 && $category != $row->category_id) {
                                    $category = $row->category_id;
                                    echo '<tr><td colspan="100%" class="no-border"><strong>' . $row->category_name . '</strong></td></tr>';
                                }
                                echo '<tr><td colspan="2" class="no-border">#' . $r . ': &nbsp;&nbsp;' . $row->product_code . '</td><td class="no-border text-center">' . $this->sma->formatQuantity($row->unit_quantity) . ' x ' . $this->sma->formatMoney($row->unit_price) . '</td><td class="no-border"><span class="pull-right">' . $this->sma->formatMoney($row->subtotal) . '</span></td></tr>';
                                echo '<tr><td colspan="4" class="no-border" style="padding-top:0;padding-bottom:0;">' . product_name($row->product_name, ($printer ? $printer->char_per_line : null)) . ($row->variant ? ' (' . $row->variant . ')' : '') . '</td></tr>';
                                if (!empty($row->second_name)) {
                                    echo '<tr><td colspan="4" class="no-border" style="padding-top:0;padding-bottom:0;">' . $row->second_name . '</td></tr>';
                                }
                                echo '<tr><td colspan="4" class="no-border border-bottom">' . ($row->item_tax != 0 ? lang('tax') . ' <small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small> ' . $this->sma->formatMoney($row->item_tax) . ($row->hsn_code ? ' (' . lang('hsn_code') . ': ' . $row->hsn_code . ')' : '') : '') . '</td></tr>';
                                */
								$r++;
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot class="notorder">
                        <?php
                        if ($inv->total_discount != 0) {
                            echo '<tr><th colspan="3">Discount</th><th colspan="1" class="text-center">' . $this->sma->formatMoney($inv->total_discount) . '</th><th colspan="3" class="text-right">الخصم</th></tr>';
                        }
						?>
						<tr>
                            <th colspan="3">Total</th>
                            <th colspan="1"  class="text-center"><?=$this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax- $inv->total_discount) + ($return_sale->total + $return_sale->product_tax- $return_sale->total_discount)) : ($inv->total - $inv->total_discount));?></th>
                            <th colspan="3" class="text-right">الإجمالي</th>
                        </tr>

						<?php
						if ($inv->total_tax != 0) {

						$VAT="";
					if($inv->total_tax!=0)
					$VAT="(".$this->sma->formatDecimal($active_tax,0)."%)";



                            echo '<tr><th colspan="3">VAT '.$VAT.'</th><th colspan="1" class="text-center">' . $this->sma->formatMoney($return_sale ? ($inv->total_tax + $return_sale->total_tax) : $inv->total_tax) . '</th><th colspan="3" class="text-right"><span  style="width:40px;font-size:12px;">ضريبة القيمة المضافة '.$VAT.'</span> </th></tr>';
                        }

                        if ($inv->shipping != 0) {
							if($inv->total<0)
                            echo '<tr><th colspan="3">Shipping</th><th colspan="1" class="text-center">' . $this->sma->formatMoney($inv->shipping) . '</th><th colspan="3" class="text-right">توصيل</th></tr>';
							else
							echo '<tr><th colspan="3">Shipping</th><th colspan="1" class="text-center">' . $this->sma->formatMoney($inv->shipping) . '</th><th colspan="3" class="text-right">توصيل</th></tr>';


						}

                        if ($return_sale) {
                            if ($return_sale->surcharge != 0) {
                                echo '<tr><th colspan="5">' . lang('order_discount') . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale->surcharge) . '</th></tr>';
                            }
                        }



                        if ($pos_settings->rounding || $inv->rounding != 0) {
                            ?>
                            <tr>
                                <th colspan="5"><?=lang('rounding'); ?></th>
                                <th class="text-right"><?= $this->sma->formatMoney($inv->rounding); ?></th>
                            </tr>
                            <tr>
                                <th colspan="5"><?=lang('grand_total'); ?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? (($inv->grand_total + $inv->rounding) + $return_sale->grand_total) : ($inv->grand_total + $inv->rounding)); ?></th>

							</tr>
                            <?php
                        } else {
                            ?>
                            <tr>
                                <th colspan="3">Grand Total</th>
                                <th colspan="1" class="text-center"><?=$this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total); ?></th>
								<th colspan="3" class="text-right">الإجمالي العام</th>
							</tr>
                            <?php
                        }
                        if ($inv->paid < ($inv->grand_total + $inv->rounding)) {
                            ?>
                            <tr>
                                <th colspan="3">Paid Amount</th>
                                <th colspan="1" class="text-center"><?=$this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid); ?></th>
                            	<th colspan="3" class="text-right">مبلغ الدفع</th>
							</tr>
                            <tr>
                                <th colspan="3"><?=lang('due_amount'); ?></th>
                                <th colspan="1" class="text-center"><?=$this->sma->formatMoney(($return_sale ? (($inv->grand_total + $inv->rounding) + $return_sale->grand_total) : ($inv->grand_total + $inv->rounding)) - ($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid)); ?></th>
                            	<th colspan="3" class="text-right">المتبقي</th>
							</tr>
                            <?php
                        } ?>
                    </tfoot>
                </table>
                <?php

				if ($payments) {


                    echo '<table class="table table-striped table-condensed notorder"><tbody>';
                    foreach ($payments as $payment) {
                        echo '<tr>';
                        //if (($payment->paid_by == 'cash' || $payment->paid_by == 'deposit') && $payment->pos_paid) {


						    if ($payment->paid_by == 'cash')
						   echo '<th colspan="3">Paid by : Cash</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='Mada')
							echo '<th colspan="3">Paid by : Mada</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='Visa')
							echo '<th colspan="3">Paid by : Visa</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='STC Pay')
							echo '<th colspan="3">Paid by : STC Pay</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='MasterCard')
							echo '<th colspan="3">Paid by : Master Card</th>';


							 if ($payment->paid_by == 'gift_card'){

							echo '<th colspan="2">Paid by : Gift Card</th>';
                            echo '<th colspan="4">No : xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</th>';
                            echo '<th>Amount : ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</th>';
                            echo '<th>Balance : ' . $this->sma->formatMoney($this->sma->getCardBalance($payment->cc_no)) . '</th>';
							 }else{
							echo '<th colspan="1" class="text-right">Amount </th>';

                            echo '<th colspan="3"  class="text-right">' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (Returned)' : '') . '</th>';
							 }


						   echo '</tr><tr>';

						   if ($payment->paid_by == 'cash')
							echo '<th colspan="3">نقدا : دفع بواسطة  </th>';
                           if ($payment->paid_by == 'CC' && $payment->cc_type=='Mada')
							echo '<th colspan="3"> مدى : دفع بواسطة  </th>';
                           if ($payment->paid_by == 'CC' && $payment->cc_type=='Visa')
							echo '<th colspan="3"> فيزا : دفع بواسطة  </th>';
                           if ($payment->paid_by == 'CC' && $payment->cc_type=='STC Pay')
							echo '<th colspan="3">  دفع بواسطة : STC Pay </th>';
                           if ($payment->paid_by == 'CC' && $payment->cc_type=='MasterCard')
							echo '<th colspan="3"> ماستركارد : دفع بواسطة  </th>';


							if ($payment->paid_by == 'gift_card'){

							echo '<th colspan="2"> دفع بواسطة   :بطاقة هدايا </th>';
                            echo '<th colspan="4">رقم : xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</th>';
                            echo '<th>المبلغ : ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</th>';
                            echo '<th>الرصيد : ' . $this->sma->formatMoney($this->sma->getCardBalance($payment->cc_no)) . '</th>';
							 }else{
							echo '<th colspan="1" class="text-right"> المبلغ  </th>';
                            echo '<th colspan="3" class="text-right">' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (مرتجع)' : '') . '</th>';
							 }

						   //echo '<td>' . lang('change') . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                       /* } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                            echo '<td>' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang('no') . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang('name') . ': ' . $payment->cc_holder . '</td>';
                        } elseif ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                            echo '<td>' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td colspan="2">' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang('cheque_no') . ': ' . $payment->cheque_no . '</td>';
                        } elseif ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                            echo '<td>' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang('no') . ': xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang('balance') . ': ' . $this->sma->formatMoney($this->sma->getCardBalance($payment->cc_no)) . '</td>';
                        } elseif ($payment->paid_by == 'other' && $payment->amount) {
                            echo '<td colspan="2">' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td colspan="2">' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo $payment->note ? '</tr><td colspan="4">' . lang('payment_note') . ': ' . $payment->note . '</td>' : '';
                        }*/
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                }


                if ($return_payments) {
                    echo '<strong class="notorder">Returned Payments / دفع المرتجع</strong><table class="table table-striped table-condensed notorder"><tbody>';
                    foreach ($return_payments as $payment) {
                        $payment->amount = (0 - $payment->amount);

						echo '<tr>';
                        //if (($payment->paid_by == 'cash' || $payment->paid_by == 'deposit') && $payment->pos_paid) {


						    if ($payment->paid_by == 'cash')
						   echo '<th colspan="2">Paid by : Cash</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='Mada')
							echo '<th colspan="2">Paid by : Mada</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='Visa')
							echo '<th colspan="2">Paid by : Visa</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='STC Pay')
							echo '<th colspan="2">Paid by : STC Pay</th>';
                            if ($payment->paid_by == 'CC' && $payment->cc_type=='MasterCard')
							echo '<th colspan="2">Paid by : Master Card</th>';


							 if ($payment->paid_by == 'gift_card'){

							echo '<th colspan="2">Paid by : Gift Card</th>';
                            echo '<th colspan="4">No : xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</th>';
                            echo '<th>Amount : ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</th>';
                            echo '<th>Balance : ' . $this->sma->formatMoney($this->sma->getCardBalance($payment->cc_no)) . '</th>';
							 }else{
							echo '<th colspan="4" class="text-right">Amount : </th>';

                            echo '<th  class="text-right">' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (Returned)' : '') . '</th>';
							 }


						   echo '</tr><tr>';

						   if ($payment->paid_by == 'cash')
							echo '<th colspan="2">نقدا : دفع بواسطة  </th>';
                           if ($payment->paid_by == 'CC' && $payment->cc_type=='Mada')
							echo '<th colspan="2"> مدى : دفع بواسطة  </th>';
                           if ($payment->paid_by == 'CC' && $payment->cc_type=='Visa')
							echo '<th colspan="2"> فيزا : دفع بواسطة  </th>';
						if ($payment->paid_by == 'CC' && $payment->cc_type=='STC Pay')
							echo '<th colspan="2">  دفع بواسطة : STC Pay </th>';

                           if ($payment->paid_by == 'CC' && $payment->cc_type=='MasterCard')
							echo '<th colspan="2"> ماستركارد : دفع بواسطة  </th>';


							if ($payment->paid_by == 'gift_card'){

							echo '<th colspan="2"> دفع بواسطة   :بطاقة هدايا </th>';
                            echo '<th colspan="4">رقم : xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</th>';
                            echo '<th>المبلغ : ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</th>';
                            echo '<th>الرصيد : ' . $this->sma->formatMoney($this->sma->getCardBalance($payment->cc_no)) . '</th>';
							 }else{
							echo '<th colspan="4" class="text-right"> المبلغ : </th>';
                            echo '<th class="text-right">' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (مرتجع)' : '') . '</th>';
							 }

						/*echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td colspan="2">' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang('change') . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                            echo '<td>' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang('no') . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang('name') . ': ' . $payment->cc_holder . '</td>';
                        } elseif ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                            echo '<td>' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td colspan="2">' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang('cheque_no') . ': ' . $payment->cheque_no . '</td>';
                        } elseif ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                            echo '<td>' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang('no') . ': xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang('balance') . ': ' . $this->sma->formatMoney($this->sma->getCardBalance($payment->cc_no)) . '</td>';
                        } elseif ($payment->paid_by == 'other' && $payment->amount) {
                            echo '<td colspan="2">' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td colspan="2">' . lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo $payment->note ? '</tr><td colspan="4">' . lang('payment_note') . ': ' . $payment->note . '</td>' : '';
                        }*/
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                ?>

                <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax + $return_sale->product_tax : $inv->product_tax)) : ''; ?>

                <?= $customer->award_points != 0 && $Settings->each_spent > 0 ? '<p class="text-center">' . lang('this_sale') . ': ' . floor(($inv->grand_total / $Settings->each_spent) * $Settings->ca_point)
                . '<br>' .
                lang('total') . ' ' . lang('award_points') . ': ' . $customer->award_points . '</p>' : ''; ?>
                <?= $inv->note ? '<p class="text-center">' . $this->sma->decode_html($inv->note) . '</p>' : ''; ?>
                <?= $inv->staff_note ? '<p class="no-print"><strong>' . lang('staff_note') . ':</strong> ' . $this->sma->decode_html($inv->staff_note) . '</p>' : ''; ?>
                <?= $biller->invoice_footer ? '<p class="text-center notorder">' . $this->sma->decode_html($biller->invoice_footer) . '</p>' : ''; ?>
            </div>

            <?php
					$company = $this->session->userdata('company');
					?>
                    <div class="order_barcodes text-center notorder">
                <img src="<?= admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                <br>
                <?= $this->sma->qrcode('', $this->sma->barcodeZATCA($Settings->site_name,$warehouse->vat_reg_no,($inv->date),$this->sma->formatDecimal($return_sale ? ($inv->grand_total + $return_sale->grand_total) : abs($inv->grand_total),2),$this->sma->formatDecimal($return_sale ? ($inv->total_tax + $return_sale->total_tax) : abs($inv->total_tax),2)), 2); ?>

				<!--<?= $this->sma->qrcode('link', urlencode(site_url('SalesDetails/view/' . $company . '/' . $inv->hash )), 2); ?>-->
                    </div>
            <div style="clear:both;"></div>
        </div>

        <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
            <hr>
            <?php
            if ($message) {
                ?>
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <?=is_array($message) ? print_r($message, true) : $message; ?>
                </div>
                <?php
            } ?>
            <?php
            if ($modal) {
                ?>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <div class="btn-group" role="group">
                        <?php
                        if ($pos->remote_printing == 1) {
                            echo '<button onclick="window.print();" class="btn btn-block btn-primary">' . lang('print') . '</button>';
                        } else {
                            echo '<button id="pb" class="btn btn-block btn-primary">' . lang('print') . '</button>';
                        } ?>
                    </div>
                    <div class="btn-group" role="group">
                        <a class="btn btn-block btn-success" href="#" id="email"><?= lang('email'); ?></a>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <span class="pull-right col-xs-12">
                    <?php
                    if ($pos->remote_printing == 1) {
                        echo '<button onclick="window.print();" class="btn btn-block btn-primary">' . lang('print') . '</button>';
                    } else {
                        echo '<button id="pb" class="btn btn-block btn-primary">' . lang('print') . '</button>';
                        echo '<button onclick="return openCashDrawer()" class="btn btn-block btn-default">' . lang('open_cash_drawer') . '</button>';
                    } ?>
                </span>
                <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang('email'); ?></a></span>
                <span class="col-xs-12">
                    <a class="btn btn-block btn-warning" href="<?= admin_url('pos'); ?>"><?= lang('back_to_pos'); ?></a>
                </span>
                <?php
            }
            if ($pos->remote_printing == 1) {
                ?>
                <div style="clear:both;"></div>
                <div class="col-xs-12" style="background:#F5F5F5; padding:10px;">
                    <p style="font-weight:bold;">
                        Please don't forget to disble the header and footer in browser print settings.
                    </p>
                    <p style="text-transform: capitalize;">
                        <strong>FF:</strong> File &gt; Print Setup &gt; Margin &amp; Header/Footer Make all --blank--
                    </p>
                    <p style="text-transform: capitalize;">
                        <strong>chrome:</strong> Menu &gt; Print &gt; Disable Header/Footer in Option &amp; Set Margins to None
                    </p>
                </div>
                <?php
            } ?>
            <div style="clear:both;"></div>
        </div>
    </div>

    <?php
    if (!$modal) {
        ?>
        <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
        <script type="text/javascript" src="<?= $assets ?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
        <?php
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function () {

$( '#pb' ).click(function() {
  //var htmlString = $( '#hp' ).html();
  var htmlString = document.getElementById("hp").innerHTML;
  var regex1 = /notorder1"/ig;
  htmlString = htmlString.replace(regex1, '" style="display:none;"');

  var res = htmlString.replace("padding-top:10px;", "display:none;padding-top:10px;");
  var regex = /notorder"/ig;
  var res1 = res.replace(regex, '" style="display:none;"');
  //var elements = res1.getElementsByClassName('notorder')
  /*for (var i = 0; i < elements.length; i++) {
            elements[i].style.display = 'none';
        }
  */
  //notorder
  //alert(res1);
  //$("#buttons").css("display", "none");

  return printReceipt(res,res1);


  //$( this ).text( htmlString );
});


            $('#email').click(function () {
                bootbox.prompt({
                    title: "<?= lang('email_address'); ?>",
                    inputType: 'email',
                    value: "<?= $customer->email; ?>",
                    callback: function (email) {
                        if (email != null) {
                            $.ajax({
                                type: "post",
                                url: "<?= admin_url('pos/email_receipt') ?>",
                                data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: <?= $inv->id; ?>},
                                dataType: "json",
                                success: function (data) {
                                    bootbox.alert({message: data.msg, size: 'small'});
                                },
                                error: function () {
                                    bootbox.alert({message: '<?= lang('ajax_request_failed'); ?>', size: 'small'});
                                    return false;
                                }
                            });
                        }
                    }
                });
                return false;
            });
        });

        <?php
        if ($pos_settings->remote_printing == 1) {
            ?>
            $(window).load(function () {
                window.print();
                return false;
            });
            <?php
        }else
        ?>

    </script>
    <?php /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */ ?>
    <?php include 'remote_printing2.php'; ?>
    <?php
    if ($modal) {
        ?>
    </div>
</div>
</div>
<?php
    } else {
        ?>
</body>
</html>
<?php
    }
?>
