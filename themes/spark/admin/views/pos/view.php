<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if ($modal) { ?>
<div class="modal-dialog no-modal-header modal-lg" role="document"><div class="modal-content"><div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
    <?php
} else {
    ?><!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?=$page_title . " " . lang("no") . " " . $inv->id;?></title>
        <base href="<?=base_url()?>"/>
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <link rel="shortcut icon" href="<?=$assets?>images/icon.png"/>
        <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
        <link rel="stylesheet" href="<?=$assets?>styles/style_rtl.css" type="text/css"/>
        <style type="text/css" media="all">

		body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6
		{
			font-family: 'Din regular' !important;
		}
            body { color: #000; }
            #wrapper { max-width: 780px; margin: 0 auto; padding-top: 20px;font-family: 'Din regular'; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
			.font_big{ font-size:16px;font-weight: bold;}
			.text_center{text-align:center}
            @media print {
                .no-print { display: none; }
                #wrapper { max-width: 780px; width: 100%; min-width: 250px; margin: 0 auto; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }
			.table {

				margin-bottom: 8px;
			}
        </style>
    </head>

    <body>
        <?php
    } ?>
    <div id="wrapper" style="padding:1px;">
        <div id="receiptData">
            <div class="no-print">
                <?php
                if ($message) {
                    ?>
                    <div class="alert alert-success" style="margin:0px;">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <?=is_array($message) ? print_r($message, true) : $message;?>
                    </div>
                    <?php
                } ?>
				 <span class="col-xs-12">
                    <a class="btn btn-block btn-warning" href="<?= admin_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
                </span>
            </div>
            <div id="receipt-data">
                <div class="text-center">

					<table class="table table-striped table-condensed" style="margin:0">
						<tbody>
							<tr>



								<td style="border:none">
									<div class="col-sm-64">
										<?= !empty($biller->logo) ? '<img src="'.base_url('assets/uploads/logos/'.$biller->logo).'" style="max-width: 100%;margin-top: 0px;" alt="">' : ''; ?>
									</div>
								</td>

							</tr>

						</tbody>
					</table>
					<?php
					echo "<div class='col-sm-634' style='padding:0;font-size: 22px;'>";
					echo lang("sale_no_ref") . ": " . $inv->id . "<br>";

					echo "</div>";
					?>
                </div>

				<div class="order_barcodes text-center" style="margin-bottom:10px;">
					<h2 style="margin:0"><?=lang('sale_invoice.')?></h2>
					<?php if($pos_settings->local==1){?>
					<h4 id="bill_type2"><?=lang($inv->order_type)?></h4>
					<h4 id="bill_type2"><?=$inv->driver?></h4>
					<h4 id="bill_type2"><?=$inv->add1?></h4>
					<?php } ?>

				</div>

				<div class="col-sm-12">  <?= lang("رقم الفاتورة") . ": " . $inv->id ?></div>
				<div class="col-sm-12">  <?php echo  lang("date") . ": " . $this->sma->hrld($inv->date); ?></div>
				<div class="col-sm-12"><?=lang("رقم المستخدم") . ": " . $biller->name;?></div>
				<div class="col-sm-12"><?=lang("اسم العميل") . ": " . $customer->name;?></div>
				<div class="col-sm-12"><?=lang("الفرع") . ": " .$biller->company ?></div>
				<div class="col-sm-12"> <?=$pos_settings->cf_title1 . ": " . $pos_settings->cf_value1 ?></div>
				<div class="col-sm-12" style="height:20px"></div>
				<div style="background:#f5f5f5;padding:5px;text-align:center;margin-bottom:1px;"> <?=lang('Items')?></div>
                <table class="table table-striped table-condensed" border="1">
					<thead>
					  <tr style="text-align:center">
						<th  style="text-align:right;padding:0" >م</th>
						<th  style="text-align:right" >كود الصنف</th>
						<th colspan="2" style="text-align:center">اسم الصنف</th>
						<th  style="text-align:center">الوحدة</th>
						<th class="text_center">الكمية</th>
						<th class="text_center">سعر الوحدة</th>
						<th class="text_center">القيمة</th>
						<th class="text_center">الخصم</th>
						<th class="text_center">الاجمالي</th>
						<th class="text_center">نسبة الضريبة</th>
						<th class="text_center">قيمة الضريبة</th>
						<th class="text_center">الصافي</th>
					  </tr>
					</thead>
                    <tbody>
                        <?php
                        $r = 1; $category = 0;$product_tax=0;
                        $tax_summary = array();
                        foreach ($rows as $row) {
                            if ($pos_settings->item_order == 1 && $category != $row->category_id) {
                                $category = $row->category_id;
                                echo '<tr><td colspan="100%" class="no-border"><strong>'.$row->category_name.'</strong></td></tr>';
                            }
                            echo '<tr><td  class="no-border">'.$r.'</td><td  class="no-border">'.$row->product_code.'</td><td colspan="2" class="no-border">'.product_name($row->product_name, ($printer ? $printer->char_per_line : null)) .' <br> '. ($row->comment). ($row->variant ? ' (' . $row->variant . ')' : '') . '<span class="pull-right"></span></td><td>'.$row->product_unit_code.'</td>

							<td class="no-border border-bottom font_big text_center">' . $this->sma->formatQuantity($row->unit_quantity) . ' </td><td class="font_big text_center"> '.$this->sma->formatMoney($row->real_unit_price) .'</td><td class="font_big text_center">'. ($this->sma->formatMoney($row->real_unit_price * $row->unit_quantity)) .'</td><td class="font_big text_center">'. $this->sma->formatMoney($row->item_discount) .'</td><td class="font_big text_center">'. $this->sma->formatMoney($row->net_unit_price * $row->quantity) .'</td><td class="font_big text_center">'. $row->tax .'</td> <td class="no-border border-bottom  font_big text_center">' . $this->sma->formatMoney($row->item_tax).'</td><td class="no-border border-bottom  font_big text_center">' . $this->sma->formatMoney($row->subtotal) . '</td>
							</tr>';
                            //echo '<tr><td class="no-border border-bottom">' . $this->sma->formatQuantity($row->quantity) . ' x '.$this->sma->formatMoney($row->unit_price).($row->item_tax != 0 ? ' - '.lang('tax').' <small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small> '.$this->sma->formatMoney($row->item_tax).' ('.lang('hsn_code').': '.$row->hsn_code.')' : '').'</td><td class="no-border border-bottom text-right">' . $this->sma->formatMoney($row->subtotal) . '</td></tr>';
							$product_tax = $product_tax + $row->item_tax;
                            $r++;
                        }

                        if ($return_rows) {
                            echo '<tr class="warning text-right"><td colspan="100%" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                            foreach ($return_rows as $row) {
                                if ($pos_settings->item_order == 1 && $category != $row->category_id) {
                                    $category = $row->category_id;
                                    echo '<tr><td colspan="100%" class="no-border"><strong>'.$row->category_name.'</strong></td></tr>';
                                }
                                echo '<tr><td colspan="2" class="no-border">#' . $r . ': &nbsp;&nbsp;' . product_name($row->product_name, ($printer ? $printer->char_per_line : null)) . ($row->variant ? ' (' . $row->variant . ')' : '') . '<span class="pull-right">' . ($row->tax_code ? '*'.$row->tax_code : '') . '</span></td></tr>';
                                echo '<tr><td class="no-border border-bottom">' . $this->sma->formatQuantity($row->unit_quantity) . ' x '.$this->sma->formatMoney($row->unit_price).($row->item_tax != 0 ? ' - '.lang('tax').' <small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small> '.$this->sma->formatMoney($row->item_tax).' ('.lang('hsn_code').': '.$row->hsn_code.')' : '').'</td><td class="no-border border-bottom text-right">' . $this->sma->formatMoney($row->subtotal) . '</td></tr>';

                                // echo '<tr><td class="no-border border-bottom">' . $this->sma->formatQuantity($row->quantity) . ' x ';
                                // if ($row->item_discount != 0) {
                                //     echo '<del>' . $this->sma->formatMoney($row->net_unit_price + ($row->item_discount / $row->quantity) + ($row->item_tax / $row->quantity)) . '</del> ';
                                // }
                                // echo $this->sma->formatMoney($row->net_unit_price + ($row->item_tax / $row->quantity)) . '</td><td class="no-border border-bottom text-right">' . $this->sma->formatMoney($row->subtotal) . '</td></tr>';
                                $r++;
                            }
                        }

                        ?>
                    </tbody>
                    <tfoot style="border: 0px solid #fff;">
                        <tr  class="text-right">
                            <td class="text-right" colspan="7"><?=lang("total_without_tax");?></td>

						   <td class="text-right" ><?=$this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax)+($return_sale->total + $return_sale->product_tax)) : ($inv->total));?></td>
						</tr>
						<tr>
							<td class="text-right" colspan="7" ><?= lang("tax") . '</td><td class="text-right">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax+$return_sale->order_tax) : $inv->order_tax + $product_tax) ?></td>
						</tr>
						<tr>
							<td class="text-right" colspan="7"><?=lang("net_grand");?></td>

							<td class="text-right" style="width:120px;" ><?=$this->sma->formatMoney($return_sale ? (($inv->grand_total + $inv->rounding)+$return_sale->grand_total) : ($inv->grand_total + $inv->rounding));?></td>
                        </tr>
                        <?php /*
                        if ($inv->order_tax != 0) {
                            echo '<tr><th class="text-right">' . lang("tax") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax+$return_sale->order_tax) : $inv->order_tax) . '</th></tr>';
                        }*/
                        if ($inv->order_discount != 0) {
                            echo '<tr><th class="text-right">' . lang("order_discount") . '</th><th class="text-right">' . $this->sma->formatMoney($inv->order_discount) . '</th></tr>';
                        }

                        if ($inv->shipping != 0) {
                            echo '<tr><th class="text-right">' . lang("shipping") . '</th><th class="text-right">' . $this->sma->formatMoney($inv->shipping) . '</th></tr>';
                        }

                        if ($return_sale) {
                            if ($return_sale->surcharge != 0) {
                                echo '<tr><th class="text-right">' . lang("order_discount") . '</th><th class="text-right">' . $this->sma->formatMoney($return_sale->surcharge) . '</th></tr>';
                            }
                        }

                        if ($Settings->indian_gst) {
                            if ($inv->cgst > 0) {
                                $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
                                echo '<tr><td class="text-right">' . lang('cgst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                            }
                            if ($inv->sgst > 0) {
                                $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
                                echo '<tr><td>' . lang('sgst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                            }
                            if ($inv->igst > 0) {
                                $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
                                echo '<tr><td>' . lang('igst') .'</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                            }
                        }

                        if ($pos_settings->rounding || $inv->rounding != 0) {
                            ?>
                            <tr>
                                <th><?=lang("rounding");?></th>
                                <th class="text-right"><?= $this->sma->formatMoney($inv->rounding);?></th>
                            </tr>
                            <tr>
                                <th class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? (($inv->grand_total + $inv->rounding)+$return_sale->grand_total) : ($inv->grand_total + $inv->rounding));?></th>
                            </tr>
                            <?php
                        } else {
                            ?>
                            <!--<tr>
                                <th class="text-right"><?=lang("grand_total");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total);?></th>
                            </tr>-->
                            <?php
                        }
                        if ($inv->paid < $inv->grand_total) {
                            ?>
                            <tr>
                                <th><?=lang("paid_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid);?></th>
                            </tr>
                            <tr>
                                <th><?=lang("due_amount");?></th>
                                <th class="text-right"><?=$this->sma->formatMoney(($return_sale ? (($inv->grand_total + $inv->rounding)+$return_sale->grand_total) : ($inv->grand_total + $inv->rounding)) - ($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid));?></th>
                            </tr>
                            <?php
                        } ?>
                    </tfoot>
                </table>
                <?php
                if ($payments) {
                    echo '<table class="table table-striped table-condensed"><tbody>';
                    foreach ($payments as $payment) {
                        echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                        } elseif ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                        } elseif ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("no") . ': ' . $payment->cc_no . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif ($payment->paid_by == 'other' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'defer' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                             echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
							echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'visa' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						   echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'span' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'master' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'student_card' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'coupon' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'staff_services' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'partiel_cash' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'partiel_span' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
						elseif ($payment->paid_by == 'instegram' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
							echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
						    echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }

                if ($inv->paid < $inv->grand_total) {
                    echo '<table class="table table-striped table-condensed"><tbody>';

                    echo '<td>' . lang("paid_by") . ': ' . lang('defer') . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney(($return_sale ? (($inv->grand_total + $inv->rounding)+$return_sale->grand_total) : ($inv->grand_total + $inv->rounding)) - ($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid)) . '</td>';
                            echo '<td>' . lang("change") . ': 0 </td>';
                    echo '</tbody></table>';
                }

                if ($return_payments) {
                    echo '<strong>'.lang('return_payments').'</strong><table class="table table-striped table-condensed"><tbody>';
                    foreach ($return_payments as $payment) {
                        $payment->amount = (0-$payment->amount);
                        echo '<tr>';
                        if (($payment->paid_by == 'cash' || $payment->paid_by == 'deposit') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                        } elseif ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                        } elseif ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("no") . ': ' . $payment->cc_no . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo '<td>' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif ($payment->paid_by == 'other' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->sma->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . ($payment->return_id ? ' (' . lang('returned') . ')' : '') . '</td>';
                            echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                ?>

                <?= $Settings->invoice_view > 100 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>

                <!--<?= $customer->award_points != 0 && $Settings->each_spent > 0 ? '<p class="text-center">'.lang('this_sale').': '.floor(($inv->grand_total/$Settings->each_spent)*$Settings->ca_point)
                .'<br>'.

                /*lang('total').' '.lang('award_points').': '. $customer->award_points . */'</p>' : ''; ?>-->
				<div class="no-print">
					<?php if($pos->add5==1)
					{?>
				<?= $inv->staff_note ? '<p class="no-print"><strong>' . lang('staff_note') . ':</strong> ' . $this->sma->decode_html($inv->staff_note) . '</p>' : ''; ?>
						<div class="table-responsive">
							<table
								class="table  table-striped table-condensed dfTable ">
								<thead>
								<tr>
									<th><?= lang('product_name') ?></th>
									<th><?= lang('quantity') ?></th>
									<th><?= lang('cost') ?></th>
									<th><?= lang('tax') ?></th>
									<th><?= lang('total') ?></th>
								</tr>
								</thead>
								<tbody>

								<?php $cost=0; foreach ($combo_items as $combo_item)
								{
									echo '<tr><td>' . $combo_item->product_name . ' (' . $combo_item->product_code . ') </td><td>' . $this->sma->formatQuantity($combo_item->quantity) . '</td><td>'.$this->sma->formatMoney($combo_item->net_unit_cost).'</td><td>'.$combo_item->item_tax.'</td><td>'.($combo_item->quantity*$combo_item->net_unit_cost+$combo_item->item_tax).'</td></tr>';
									$cost = $cost+($combo_item->quantity*$combo_item->net_unit_cost+$combo_item->item_tax);

								} ?>
								</tbody>
							</table>
						</div>
						<p><strong><?=lang('cost')?> : <?=$cost?></strong></p>
						<p>  <strong><?=lang("grand_total");?> : <?=$inv->grand_total?></strong></p>
						<p>  <strong><?=lang("profit");?> : <?=$inv->grand_total-$cost?></strong></p>
					<?php }?>
				</div>
				<?php if($pos->add3==1){?>
				<div class="col-sm-4" style="width:25%;float:left">
					<?= !empty($Settings->logo2) ? '<img src="'.base_url('assets/uploads/logos/'.$Settings->logo2).'" style="max-width: 150px;margin-top: 0px;float: left;" alt="">' : ''; ?>
				</div>
				<?php } ?>
				<img src="<?= admin_url('misc/barcode/'.$this->sma->base64url_encode($inv->reference_no).'/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                <?= $inv->note ? '<p class="text-center">' . $this->sma->decode_html($inv->note) . '</p>' : ''; ?>

                <?= $biller->invoice_footer ? '<p class="text-center">'.$this->sma->decode_html($biller->invoice_footer).'</p>' : ''; ?>
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
                    <?=is_array($message) ? print_r($message, true) : $message;?>
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
                            echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                        } else {
                            echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                        }

                        ?>
                    </div>
                    <div class="btn-group" role="group">
                        <a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a>
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
                        echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                    } else {
                        echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                        echo '<button onclick="return openCashDrawer()" class="btn btn-block btn-default">'.lang("open_cash_drawer").'</button>';
                    }
                    ?>
                </span>
                <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span>
                <span class="col-xs-12">
                    <a class="btn btn-block btn-warning" href="<?= admin_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
                </span>
                <?php
            }
            if ($pos->remote_printing == 1) {
                ?>
                <div style="clear:both;"></div>
                <div class="col-xs-12" style="background:#F5F5F5; padding:10px;">
                    <p style="font-weight:bold;">
                        <?= lang('Please_don_not_forget_to_disble_the_header_and_footer_in_browser_print_settings.'); ?>
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
    if( ! $modal) {
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
            $('#email').click(function () {
                bootbox.prompt({
                    title: "<?= lang("email_address"); ?>",
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
        }
        ?>

    </script>
    <?php /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */ ?>
    <?php include 'remote_printing.php'; ?>
    <?php
    if($modal) {
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

<script>
$(document).keydown(function(event) {
	// enter has keyCode = 13, change it if you want to use another button
	if (event.keyCode == 13) {

		window.location.href = "<?= admin_url('pos'); ?>";
	}
});
</script>