<?php

$char_per_line = ($printer ? $printer->char_per_line : 42);

if ($pos_settings->remote_printing != 1) {
    if ($Settings->invoice_view > 100) {
        $tax_summary    = $this->gst->taxSummary($rows);
        $re_tax_summary = $this->gst->taxSummary($return_rows);
        $tax_summary    = $tax_summary + $re_tax_summary;
    }

    $store_name = printText(($biller->company && $biller->company != '-' ? $biller->company : $biller->name), $char_per_line) . "\n";

    $header = '';
    $header .= printText(($biller->company && $biller->company != '-' ? $biller->company : $biller->name), $char_per_line);
    if ($biller->address) {
        $header .= printText($biller->address, $char_per_line) . "\n";
    }
    if ($biller->city) {
        $header .= printText($biller->city . ' ' . ($biller->country ? $biller->country : ''), $char_per_line) . "\n";
    }
    $header .= printText(lang('tel') . ': ' . $biller->phone, $char_per_line);
    if (!empty($biller->cf1) && $biller->cf1 != '-') {
        $header .= "\n" + lang('bcf1') . ': ' . $biller->cf1;
    }
    if (!empty($biller->cf2) && $biller->cf2 != '-') {
        $header .= "\n" + lang('bcf2') . ': ' . $biller->cf2;
    }
    if (!empty($biller->cf3) && $biller->cf3 != '-') {
        $header .= "\n" + lang('bcf3') . ': ' . $biller->cf3;
    }
    if (!empty($biller->cf4) && $biller->cf4 != '-') {
        $header .= "\n" + lang('bcf4') . ': ' . $biller->cf4;
    }
    if (!empty($biller->cf5) && $biller->cf5 != '-') {
        $header .= "\n" + lang('bcf5') . ': ' . $biller->cf5;
    }
    if (!empty($biller->cf6) && $biller->cf6 != '-') {
        $header .= "\n" + lang('bcf6') . ': ' . $biller->cf6;
    }
    $header .= "\n\n";
    if ($pos_settings->cf_title1 && $pos_settings->cf_value1) {
        $header .= printText(($pos_settings->cf_title1 . ': ' . $pos_settings->cf_value1), $char_per_line) . "\n";
    }
    if ($pos_settings->cf_title2 && $pos_settings->cf_value2) {
        $header .= printText(($pos_settings->cf_title2 . ': ' . $pos_settings->cf_value2), $char_per_line) . "\n";
    }
    $header .= "\n";

    $info = '';
    $info .= lang('date') . ': ' . $this->sma->hrld($inv->date) . "\n";
    $info .= lang('sale_no_ref') . ': ' . $inv->id . "\n";
    $info .= lang('sales_person') . ': ' . $created_by->first_name . ' ' . $created_by->last_name . "\n\n";
    $info .= lang('customer') . ': ' . ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) . "\n";
    if ($pos_settings->customer_details) {
        if ($customer->vat_no != '-' && $customer->vat_no != '') {
            $info .= lang('vat_no') . ': ' . $customer->vat_no . "\n";
        }
        if ($customer->gst_no != '-' && $customer->gst_no != '') {
            $info .= lang('gst_no') . ': ' . $customer->gst_no . "\n";
        }
        $info .= lang('tel') . ': ' . $customer->phone . "\n";
        $info .= lang('address') . ': ' . $customer->address . "\n";
        $info .= $customer->city . ' ' . $customer->state . ' ' . $customer->country . "\n";
        if (!empty($customer->cf1) && $customer->cf1 != '-') {
            $info .= "\n" . lang('ccf1') . ': ' . $customer->cf1;
        }
        if (!empty($customer->cf2) && $customer->cf2 != '-') {
            $info .= "\n" . lang('ccf2') . ': ' . $customer->cf2;
        }
        if (!empty($customer->cf3) && $customer->cf3 != '-') {
            $info .= "\n" . lang('ccf3') . ': ' . $customer->cf3;
        }
        if (!empty($customer->cf4) && $customer->cf4 != '-') {
            $info .= "\n" . lang('ccf4') . ': ' . $customer->cf4;
        }
        if (!empty($customer->cf5) && $customer->cf5 != '-') {
            $info .= "\n" . lang('ccf5') . ': ' . $customer->cf5;
        }
        if (!empty($customer->cf6) && $customer->cf6 != '-') {
            $info .= "\n" . lang('ccf6') . ': ' . $customer->cf6;
        }
    }
    $info .= "\n";

    $items = '';
    foreach ($rows as $row) {
        $r = 1;
        $items .= printLine(product_name(addslashes('#' . $r . ' ' . $row->product_code), 18) . ': ' . printLine($this->sma->formatQuantity($row->unit_quantity) . ' x ' . $this->sma->formatMoney($row->unit_price) . ':  ' . $this->sma->formatMoney($row->subtotal), 14), $char_per_line, ' ') . "\n";
        $items .= product_name(addslashes('   ' . $row->product_name), $char_per_line) . "\n";
        if (!empty($row->second_name)) {
            $items .= product_name(addslashes('   ' . $row->second_name), $char_per_line) . "\n";
        }
        if (!empty($row->variant)) {
            $items .= product_name(addslashes('   ' . $row->variant), $char_per_line) . "\n";
        }
        if ($row->item_tax != 0) {
            $items .= '   ' . lang('tax') . ' (' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ') ' . $this->sma->formatMoney($row->item_tax) . ($row->hsn_code ? ' (' . lang('hsn_code') . ': ' . $row->hsn_code . ')' : '') . "\n";
        }
        $r++;
    }
    if ($return_rows) {
        foreach ($return_rows as $row) {
            $r = 1;
            $items .= printLine(product_name(addslashes('#' . $r . ' ' . $row->product_code), 18) . ': ' . printLine($this->sma->formatQuantity($row->unit_quantity) . ' x ' . $this->sma->formatMoney($row->unit_price) . ':  ' . $this->sma->formatMoney($row->subtotal), 14), $char_per_line, ' ') . "\n";
            $items .= product_name(addslashes('   ' . $row->product_name), $char_per_line) . "\n";
            if (!empty($row->second_name)) {
                $items .= product_name(addslashes('   ' . $row->second_name), $char_per_line) . "\n";
            }
            if (!empty($row->variant)) {
                $items .= product_name(addslashes('   ' . $row->variant), $char_per_line) . "\n";
            }
            if ($row->item_tax != 0) {
                $items .= '   ' . lang('tax') . ' (' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ') ' . $this->sma->formatMoney($row->item_tax) . ($row->hsn_code ? ' (' . lang('hsn_code') . ': ' . $row->hsn_code . ')' : '') . "\n";
            }
            $r++;
        }
    }
    $items .= "\n";




$items_order = '';
    foreach ($rows as $row) {
        $r = 1;
        $items_order .= printLine(product_name(addslashes('#' . $r . ' ' . $row->product_code), 18) . ': ' . printLine($this->sma->formatQuantity($row->unit_quantity), 14), $char_per_line, ' ') . "\n";
        $items_order .= product_name(addslashes('   ' . $row->product_name), $char_per_line) . "\n";
        if (!empty($row->second_name)) {
            $items_order .= product_name(addslashes('   ' . $row->second_name), $char_per_line) . "\n";
        }
        if (!empty($row->variant)) {
            $items_order .= product_name(addslashes('   ' . $row->variant), $char_per_line) . "\n";
        }
        $r++;
    }
    if ($return_rows) {
        foreach ($return_rows as $row) {
            $r = 1;
            $items_order .= printLine(product_name(addslashes('#' . $r . ' ' . $row->product_code), 18) . ': ' . printLine($this->sma->formatQuantity($row->unit_quantity), 14), $char_per_line, ' ') . "\n";
            $items_order .= product_name(addslashes('   ' . $row->product_name), $char_per_line) . "\n";
            if (!empty($row->second_name)) {
                $items_order .= product_name(addslashes('   ' . $row->second_name), $char_per_line) . "\n";
            }
            if (!empty($row->variant)) {
                $items_order .= product_name(addslashes('   ' . $row->variant), $char_per_line) . "\n";
            }
            $r++;
        }
    }
    $items_order .= "\n";




    $totals = '';
    $totals .= printLine(lang('total') . ': ' . $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax) + ($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)), $char_per_line) . "\n";
    if ($Settings->indian_gst) {
        if ($inv->cgst > 0) {
            $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
            $totals .= printLine(lang('cgst') . ': ' . ($Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst), $char_per_line) . "\n";
        }
        if ($inv->sgst > 0) {
            $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
            $totals .= printLine(lang('sgst') . ': ' . ($Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst), $char_per_line) . "\n";
        }
        if ($inv->igst > 0) {
            $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
            $totals .= printLine(lang('igst') . ': ' . ($Settings->format_gst ? $this->sma->formatMoney($igst) : $igst), $char_per_line) . "\n";
        }
    }
    if ($inv->order_tax != 0) {
        $totals .= printLine(lang('tax') . ': ' . $this->sma->formatMoney($return_sale ? ($inv->order_tax + $return_sale->order_tax) : $inv->order_tax), $char_per_line) . "\n";
    }
    if ($inv->total_discount != 0) {
        $totals .= printLine(lang('discount') . ': (' . $this->sma->formatMoney($return_sale ? ($inv->product_discount + $return_sale->product_discount) : $inv->product_discount) . ') ' . $this->sma->formatMoney($return_sale ? ($inv->order_discount + $return_sale->order_discount) : $inv->order_discount), $char_per_line) . "\n";
    }
    if ($inv->shipping != 0) {
        $totals .= printLine(lang('shipping') . ': ' . $this->sma->formatMoney($inv->shipping), $char_per_line) . "\n";
    }
    if ($pos_settings->rounding || $inv->rounding != 0) {
        $totals .= printLine(lang('rounding') . ': ' . $this->sma->formatMoney($inv->rounding), $char_per_line) . "\n";
        $totals .= printLine(lang('grand_total') . ': ' . $this->sma->formatMoney($return_sale ? ($this->sma->roundMoney($inv->grand_total + $inv->rounding) + $return_sale->grand_total) : $this->sma->roundMoney($inv->grand_total + $inv->rounding)), $char_per_line) . "\n";
    } else {
        $totals .= printLine(lang('grand_total') . ': ' . $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total), $char_per_line) . "\n";
    }
    $totals .= printLine(lang('paid_amount') . ': ' . $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid), $char_per_line) . "\n";
    $totals .= printLine(lang('due_amount') . ': ' . $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid)), $char_per_line) . "\n";
    $totals .= "\n";

    $payments_text = '';
    if ($payments) {
        foreach ($payments as $payment) {
            if ($payment->paid_by == 'cash' || $payment->paid_by == 'deposit' && $payment->pos_paid) {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('change') . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : '0.00'), $char_per_line) . "\n";
            }
            if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('card_no') . ': xxxx xxxx xxxx ' . substr($payment->cc_no, -4), $char_per_line) . "\n";
            }
            if ($payment->paid_by == 'gift_card') {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('card_no') . ': xxxx xxxx xxxx ' . substr($payment->cc_no, -4), $char_per_line) . "\n";
            }
            if ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('cheque_no') . ': ' . $payment->cheque_no, $char_per_line) . "\n";
                if ($payment->paid_by == 'other' && $payment->amount) {
                    $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                    $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->amount), $char_per_line) . "\n";
                    $payments_text .= printText(lang('payment_note') . ': ' . $payment->note, $char_per_line) . "\n";
                }
            }
        }
    }
    if ($return_payments) {
        $payments_text .= "\n" . printText(lang('return_payments'), $char_per_line) . "\n";
        foreach ($return_payments as $payment) {
            if ($payment->paid_by == 'cash' || $payment->paid_by == 'deposit' && $payment->pos_paid) {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('change') . ': ' . ($payment->pos_balance > 0 ? $this->sma->formatMoney($payment->pos_balance) : '0.00'), $char_per_line) . "\n";
            }
            if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('card_no') . ': xxxx xxxx xxxx ' . substr($payment->cc_no, -4), $char_per_line) . "\n";
            }
            if ($payment->paid_by == 'gift_card') {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('card_no') . ': xxxx xxxx xxxx ' . substr($payment->cc_no, -4), $char_per_line) . "\n";
            }
            if ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->pos_paid), $char_per_line) . "\n";
                $payments_text .= printLine(lang('cheque_no') . ': ' . $payment->cheque_no, $char_per_line) . "\n";
                if ($payment->paid_by == 'other' && $payment->amount) {
                    $payments_text .= printLine(lang('paid_by') . ': ' . lang($payment->paid_by), $char_per_line) . "\n";
                    $payments_text .= printLine(lang('amount') . ': ' . $this->sma->formatMoney($payment->amount), $char_per_line) . "\n";
                    $payments_text .= printText(lang('payment_note') . ': ' . $payment->note, $char_per_line) . "\n";
                }
            }
        }
    }
    $payments_text .= "\n";

    $footer = '';
    if ($Settings->invoice_view > 100)
	{
        if (!empty($tax_summary)) {
            $footer .= lang('tax_summary') . "\n\n";
            $footer .= taxLine(lang('name'), lang('code'), lang('qty'), lang('tax_excl'), lang('tax_amt'), $char_per_line) . "\n";
            $footer .= str_replace("\n", '', drawLine($char_per_line)) . "\n";
            foreach ($tax_summary as $summary) {
                $footer .= taxLine($summary['name'], $summary['code'], $this->sma->formatQuantity($summary['items']), $this->sma->formatMoney($summary['amt']), $this->sma->formatMoney($summary['tax']), $char_per_line) . "\n";
            }
            $footer .= str_replace("\n", '', drawLine($char_per_line)) . "\n";
            $footer .= "\n" . printLine(lang('total_tax_amount') . ':' . $this->sma->formatMoney($inv->product_tax), $char_per_line) . "\n";
            $footer .= str_replace("\n", '', drawLine($char_per_line)) . "\n\n";
        }
    }

    if ($inv->note) {
        $footer .= printText(strip_tags(preg_replace('/\s+/', ' ', $this->sma->decode_html($inv->note))), $char_per_line) . "\n\n";
    }
    if ($biller->invoice_footer) {
        $footer .= printText(str_replace(["\n", "\r"], ['\\n', '\\r'], $biller->invoice_footer), $char_per_line) . "\n\n";
    }

    $receipt = [
        'store_name' => $store_name,
        'header'     => $header,
        'info'       => $info,
        'items'      => $items,
        'totals'     => $totals,
        'payments'   => $payments_text,
        'footer'     => $footer,
    ];

	$order = [
        'store_name' => $store_name,
        'header'     => $header,
        'info'       => $info,
        'items'      => $items_order,
        'footer'     => $footer,
    ];



?>

    <script type="text/javascript">
        function receiptData() {
            return <?=json_encode($receipt); ?>;
        }

        var socket = null;
    </script>

<?php
if (!$pos_settings->remote_printing) {
    ?>
        <script type="text/javascript">
            function openCashDrawer() {
                var ocddata = {
                    'printer': <?= json_encode($printer); ?>
                };
                $.get('<?= admin_url('pos/open_drawer'); ?>', {data: JSON.stringify(ocddata)});
                return false;
            }

            function printReceipt() {
                var receipt_data = receiptData();
                var socket_data = {
                    'printer': <?= json_encode($printer); ?>,
                    'logo': '<?= !empty($biller->logo) ? $biller->logo : ''; ?>',
                    'text': receipt_data,
                    'cash_drawer': <?= isset($modal) ? 0 : 1; ?>, 'drawer_code': '<?= $pos_settings->cash_drawer_codes; ?>'
                };
                $.get('<?= admin_url('pos/p'); ?>', {data: JSON.stringify(socket_data)});
                return false;
            }
        </script>
<?php
} elseif ($pos_settings->remote_printing == 2) {
        ?>
        <script src="<?= $assets ?>plugins/socket.io.min.js" type="text/javascript"></script>
        <script type="text/javascript">

			socket = io.connect('http://localhost:6440', {'reconnection': false});

            function printReceipt() {
                if (socket.connected) {
                    var receipt_data = receiptData();
                    var socket_data = {
                        'printer': <?= json_encode($printer); ?>,
                        'logo': '<?= !empty($biller->logo) ? base_url('assets/uploads/logos/' . $biller->logo) : ''; ?>',
                        'text': receipt_data,
                        'cash_drawer': <?= isset($modal) ? 0 : 1; ?>, 'drawer_code': '<?= $pos_settings->cash_drawer_codes; ?>'
                    };
                    socket.emit('print-now', socket_data);
                    return false;
                } else {
                    bootbox.alert('<?= lang('pos_print_error'); ?>');
                    return false;
                }
            }

            function openCashDrawer() {
                if (socket.connected) {
                    var ocddata = {
                        'printer': <?= json_encode($printer); ?>,
                        'cash_drawer': 1, 'drawer_code': '<?= $pos_settings->cash_drawer_codes; ?>'
                    };
                    socket.emit('open-cashdrawer', ocddata);
                    return false;
                } else {
                    bootbox.alert('<?= lang('pos_print_error'); ?>');
                    return false;
                }
            }
        </script>
<?php
    } elseif ($pos_settings->remote_printing == 3) {

        ?>
		<script type="text/javascript" src="<?= $assets ?>pos/printer/qz-tray.js"></script>
		<script type="text/javascript">
		qz.security.setCertificatePromise(function (resolve, reject) {
    resolve("-----BEGIN CERTIFICATE-----\n" +
            "MIID7TCCAtWgAwIBAgIUBNgowB3EwP3W9eSLAoTkWDV4AcwwDQYJKoZIhvcNAQELBQAwgYQxCzAJBgNVBAYTAlNBMQ4wDAYDVQQIDAVKaWJhbDEOMAwGA1UEBwwFSmliYWwxETAPBgNVBAoMCEFya2FucHJvMQwwCgYDVQQLDANjcGUxEjAQBgNVBAMMCWxvY2FsaG9zdDEgMB4GCSqGSIb3DQEJARYRaW5mb0BhcmthbnByby5jb20wIBcNMjEwNTIyMjEwMzIxWhgPMjA1MjExMTQyMTAzMjFaMIGEMQswCQYDVQQGEwJTQTEOMAwGA1UECAwFSmliYWwxDjAMBgNVBAcMBUppYmFsMREwDwYDVQQKDAhBcmthbnBybzEMMAoGA1UECwwDY3BlMRIwEAYDVQQDDAlsb2NhbGhvc3QxIDAeBgkqhkiG9w0BCQEWEWluZm9AYXJrYW5wcm8uY29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtzSM8vfOhmdoVJhvjaj5GYIa/cAEH4a2VB+z/6hyHv/q3eHJ7zi5QQLOo1dQ7QS0RKE/J04XRG1N0vgx2cWw4nAQp2ckodsWczwgpEDrfEbZ2ZjSx5U5pyKkzFcrLPu5uxbmEzYgNzVVT3KkLE24CrCwKh3e6vcCTp8IbaqiZTtX0pff18dT5JSYSkKwynapfYTlAAHkbruu1m8zzo9iu/5T7T+QsBFdUnb0o2HjgrSI6jHsJRoMmZRS3gI9VXDgQO8e/STfMCltCBzbrD5rsp/EGYgd0+L9Ci91zAMD3owwT2bDDSVysuCuUIAC7CtwbrcjyT+ioxNgEudak3IurwIDAQABo1MwUTAdBgNVHQ4EFgQUlED4+eRHwS71HwUlC6xga5pBr7owHwYDVR0jBBgwFoAUlED4+eRHwS71HwUlC6xga5pBr7owDwYDVR0TAQH/BAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAGtE/gpy8wl+lgoUL87jLmvHQUApoCbTDh7vkEhspu2bNJ9GCsWHXQFq+cq1KX8PFeK/LsAtObV8ygfIYYzLV/pMb03UpwTUPxmduryGwWxp1RUFiKii1Gm6Q3WAPBeBFtqZNzhD2TLsm27Lfz7zgHZeCFYr4SsmWef5qiqs4bhBEk01N2buYPtH+h3kR1D7PV5EWssMSsgS2/U2Tdr1Qd6dF94AZQIwIhCY10RmT0VKDxaO9Dw8iJA+t8lKT0Ev2Ya3KAzMHxKFQ4WLfPeBwunVZ2OHyEP6dZfDsXkT6Bwo4W5ak/v46PCcEw5LCYnNeUZO/QqswIgcOEXnabUsxyg==\n" +
            "-----END CERTIFICATE-----");

});
</script>

		<script type="text/javascript" src="<?= $assets ?>pos/printer/jsrsasign-latest-all-min.js"></script>
		<!--<script type="text/javascript" src="<?= $assets ?>pos/printer/sign-message.js"></script>

		-->


		<script type="text/javascript" src="<?= $assets ?>pos/printer/sample/promise-polyfill-8.1.3.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>pos/printer/sample/whatwg-fetch-3.0.0.min.js"></script>

<!-- Page styling -->
<script type="text/javascript" src="<?= $assets ?>pos/printer/sample/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>pos/printer/sample/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>pos/printer/dependencies/rsvp-3.1.0.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>pos/printer/dependencies/sha-256.min.js"></script>


        <script type="text/javascript">



qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
qz.security.setSignaturePromise(function(toSign) {
   return function(resolve, reject) {
      $.post("assets/signing/sign-message.php", {request: toSign}).then(resolve, reject);
   };
});

		/*
		var privateKey = "-----BEGIN PRIVATE KEY-----\n" +
   "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC0z9FeMynsC8+u\n" +
   "dvX+LciZxnh5uRj4C9S6tNeeAlIGCfQYk0zUcNFCoCkTknNQd/YEiawDLNbxBqut\n" +
   "bMDZ1aarys1a0lYmUeVLCIqvzBkPJTSQsCopQQ9V8WuT252zzNzs68dVGNdCJd5J\n" +
   "NRQykpwexmnjPPv0mvj7i8XgG379TyW6P+WWV5okeUkXJ9eJS2ouDYdR2SM9BoVW\n" +
   "+FgxDu6BmXhozW5EfsnajFp7HL8kQClI0QOc79yuKl3492rH6bzFsFn2lfwWy9ic\n" +
   "7cP8EpCTeFp1tFaD+vxBhPZkeTQ1HKx6hQ5zeHIB5ySJJZ7af2W8r4eTGYzbdRW2\n" +
   "4DDHCPhZAgMBAAECggEATvofR3gtrY8TLe+ET3wMDS8l3HU/NMlmKA9pxvjYfw7F\n" +
   "8h4VBw4oOWPfzU7A07syWJUR72kckbcKMfw42G18GbnBrRQG0UIgV3/ppBQQNg9Y\n" +
   "QILSR6bFXhLPnIvm/GxVa58pOEBbdec4it2Gbvie/MpJ4hn3K8atTqKk0djwxQ+b\n" +
   "QNBWtVgTkyIqMpUTFDi5ECiVXaGWZ5AOVK2TzlLRNQ5Y7US8lmGxVWzt0GONjXSE\n" +
   "iO/eBk8A7wI3zknMx5o1uZa/hFCPQH33uKeuqU5rmphi3zS0BY7iGY9EoKu/o+BO\n" +
   "HPwLQJ3wCDA3O9APZ3gmmbHFPMFPr/mVGeAeGP/BAQKBgQDaPELRriUaanWrZpgT\n" +
   "VnKKrRSqPED3anAVgmDfzTQwuR/3oD506F3AMBzloAo3y9BXmDfe8qLn6kgdZQKy\n" +
   "SFNLz888at96oi+2mEKPpvssqiwE6F3OtEM6yv4DP9KJHaHmXaWv+/sjwjzpFNjs\n" +
   "wGThBxFvrTWRJqBYsM1XNJJ2EQKBgQDUGbTSwHKqRCYWhQ1GPCZKE98l5UtMKvUb\n" +
   "hyWWOXoyoeYbJEMfG1ynX4JeXIkl6YtBjYCqszv9PjHa1rowTZaAPJ0V70zyhTcF\n" +
   "t581ii9LpiejIGrELHvJnW87QmjjStkjwGIqgKLp7Qe6CDjHI9HP1NM0uav/IQLW\n" +
   "pB6wyEz1yQKBgQCuxPut+Ax2rzM05KB9PAnWzO1zt3U/rtm8IAF8uVVGf7r+EDJ0\n" +
   "ZXJO6zj5G8WTEYHz5E86GI4ltBW0lKQoKouqdu27sMrv5trXG/CSImOcTVubQot9\n" +
   "chc1CkOKTp5IeJajafO6j817wZ4N+0gNsbYYEBUCnm/7ojdfT5ficpOoQQKBgQDB\n" +
   "PgKPmaNfGeQR1Ht5qEfCakR/RF/ML79Nq15FdmytQPBjfjBhYQ6Tt+MRkgGqtxOX\n" +
   "UBMQc2iOnGHT3puYcrhScec1GufidhjhbqDxqMrag7HNYDWmMlk+IeA7/4+Mtp8L\n" +
   "gbZuvvCvbLQDfIYueaYpUuBzQ08/jZYGdVU4/+WOcQKBgAGUN0kIB6EM1K/iZ0TN\n" +
   "jlt8P5UEV3ZCyATWFiGZRhhE2WAh8gv1jx4J26pcUs1n8sd2a1h6ZuBSqsyIlNSp\n" +
   "xtKsm3bqQFDHRrPcsBX4nanrw9DzkpH1k/I3WMSdGqkDAR3DtL7yXTJXJo2Sbrp5\n" +
   "EjzSn7DcDE1tL2En/tSVXeUY\n" +
   "-----END PRIVATE KEY-----";

qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
qz.security.setSignaturePromise(function(toSign) {
    return function(resolve, reject) {
        try {
            var pk = KEYUTIL.getKey(privateKey);
            var sig = new KJUR.crypto.Signature({"alg": "SHA512withRSA"});  // Use "SHA1withRSA" for QZ Tray 2.0 and older
            sig.init(pk);
            sig.updateString(toSign);
            var hex = sig.sign();
            console.log("DEBUG: \n\n" + stob64(hextorstr(hex)));
            resolve(stob64(hextorstr(hex)));
        } catch (err) {
            console.error(err);
            reject(err);
        }
    };
});
*/
		/*
		qz.security.setCertificatePromise(function (resolve, reject) {
    resolve("-----BEGIN CERTIFICATE-----\n" +
            "MIID9zCCAt+gAwIBAgIUP2uZoodGe1kL8bUOezMNwDQ/XvwwDQYJKoZIhvcNAQELBQAwgYkxCzAJBgNVBAYTAllFMQ0wCwYDVQQIDARUYWl6MQ0wCwYDVQQHDARUYWl6MRcwFQYDVQQKDA5TYW5oYW4gQ29sbGVnZTELMAkGA1UECwwCc2MxEjAQBgNVBAMMCWxvY2FsaG9zdDEiMCAGCSqGSIb3DQEJARYTYWxoYWp3Njg4QGdtYWlsLmNvbTAgFw0yMTA1MjExODE2NTVaGA8yMDUyMTExMzE4MTY1NVowgYkxCzAJBgNVBAYTAllFMQ0wCwYDVQQIDARUYWl6MQ0wCwYDVQQHDARUYWl6MRcwFQYDVQQKDA5TYW5oYW4gQ29sbGVnZTELMAkGA1UECwwCc2MxEjAQBgNVBAMMCWxvY2FsaG9zdDEiMCAGCSqGSIb3DQEJARYTYWxoYWp3Njg4QGdtYWlsLmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALczKQzQmRju5sZNim3qVKIOUEjaYIdnfcHHiG2LMac9dRZ5RQXpGntO0WLPlBi/73VTjiA1tIXG0t2jsuvSUYzpGoPcAknTaDJQaPH2Gskz/LLRyI19+FSigEk8SgAPGuWeAlWYdeazabAMDEIl+xc3jed65DNgxzIHtpgb/ZeO3dXuJdHbiaDVGeROWD/DWjkk8YqTwxQTtG2q2bd5+1KfSnqZ0U9ioxe55oI1ZA0csOoZ8lkzfbz2uD2P9RwvOPCIAopsEmQG7MulsJcb9NQiFuM6EYdKA6t8gV0DEJpmITOU5hAa3X2fzMfvWL6/OG/tLL1+7GiqIw5tsWrlep8CAwEAAaNTMFEwHQYDVR0OBBYEFC0ARx17fRhJI2uomMoOmf0wpApiMB8GA1UdIwQYMBaAFC0ARx17fRhJI2uomMoOmf0wpApiMA8GA1UdEwEB/wQFMAMBAf8wDQYJKoZIhvcNAQELBQADggEBAK24uBReZ1aWeSiPUt5UlS63s6+Qjw3AVWCxG7kZXAbVeWPnOQE4mrG4PHLe/hJhWrzraNDvQ3KEud3AOyLE5OwS7LGwo6C4WxPBnIYxVtBptuU1v5flu0O8UajdLFHbSIbB5u8XccNenC98bn5M4CSW42u9jNjk15iL0hzQmaWlw0NZ9GKGRfbyz+u0Vijx5ki4XzOoVVD9QYaBBBINXYiv5bIec6f4qYfNpRCpf4banyR5k3FT5QkAAefqJEUNbLj4dSDKu/JPtUW2/e9+Ga5dxsT8mfgx62OxgG3uJ9j9BJQeBrRolN1vesb5yVLzUGrOq0yODJVmI8ESEWSMPEY=\n" +
            "-----END CERTIFICATE-----");

});*/
/*
qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
qz.security.setSignaturePromise(function(toSign) {
   return function(resolve, reject) {
      $.post("pos/signing", {request: toSign}).then(resolve, reject);
   };
});

*/
/*
qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
qz.security.setSignaturePromise(function(toSign) {
	alert("<?= admin_url('pos'); ?>/signing/sign-message.php?request=" + toSign)
   return function(resolve, reject) {
      fetch("<?= admin_url('pos'); ?>/signing/request=" + toSign, {cache: 'no-store', headers: {'Content-Type': 'text/plain'}})
         .then(function(data) { data.ok ? resolve(data.text()) : reject(data.text()); });
   };

});
var privateKey = "-----BEGIN PRIVATE KEY-----\n" +
   "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC0yvZElcYUgN7XYNWQ97wtkW+o8hru5c9NlBmjKBZySNO9XAx5a3+Fvl3COqqj9pfVhMGj9TgiFEKhsuQpSufPhKAixQvI0P/YVfKlRjkhKXdpGw1KfYP5lsL/G+I0zlKBZ5RGtwkfEnU1t67GXrlS2n+Zxz52t5UZrsF3Rq3JPD7DuSbp20gCU29ZuCwVlXKAogOg+aJaYAeFAO3iyqEQwr4k/9x5gh1SVO6WF1Cq36xHiqNKiYRlyKyIH4z2p1XmIB+ot2izcKpNhWc7bAwrtKKYzApqFYJYFM736GScghacCRT01Y732Zhnb6oJP0r0AympmaubzH6lj0dnTRrdAgMBAAECggEAcle/Q4daXNNYnO1QhTw5lrRZ44cjw54y60TUph0PfT5UNHoIJa74WzUYBWSnUcX/J3qsIa9QzjT0I2KJ7RfD3rppsTPgjSSp+z/4k9EDBtc7/4ytbkRN+MgCEeinu3AWPGOvIr5WlYFOAihm8G0uVpl8hFzAQ8M+GJ0vHOnltdREyxOOBiIPq9YlgYtedFRH9E45pPyiCJZhtYIHrRAEoWcXnLEiYopTJMZ1iMUxstPlPPqB1TB8b7rm/O79swgn5IpHwFWWAPn0O03JWO0Dfu1rk+jjFPMjPnl/+N6YRdrrfNA/8Qu7IZful6vlR/LZFrqvvCQO2wDh1kL8mSMo4QKBgQDlT0DYoKR4EaY+Co/3L3QJLfsL9Iv10+nBjZ4V7UxomeeN36NbJRvKgV4EBxvOo/aEokTwCTIlUUi0/gYdefDDMTcZTZX1JQEm1D4Biy/AWVI7zkL5yIGoa+HSb8M/Esc7S24ULcFtCDBjgLV2DcefP0NW4d9k5jeVV2F1515yhQKBgQDJ1g2C6naoU4KfqZWjQIvmnGbiAxeQdxF00GJ1o2/kvg7ScUUdfgxM2wWdJNBrXbunZwRgAdp+7gNy8ssZF+rcnrMHtgGS14UEnitrv8XVM2n9MYpQCBJA5U7bb+ZW3XREG/RWihiiS6TIhctGSJVg2jKIQik4cRapzmr4bY8yeQKBgBQUk54kuyJUkVU/gYno2uVu5FwQpyz4yLCNYTmUVSuXxqIy4lA5+KwMFhqOMM4wp4bH2veWJpEEBDTtRcXRBtdg+O07URVtjyNCVMe304+pgrz/+/d5qnkAMX9HEDb2wwGbQmF7/EM7A1nDTKCw5jk4xgHWt8RTMKzgQQWdJDUtAoGBAL9wYRZoby2PMBlij4TtxwRwGoGK70HePddvg8QSxiHPR13BhA3KLb6EC5oWff0oRcItSxAJEgZMmUbhFVRyxBiICkVpE6sE8q0DnSmxFuL2xjQaVhc84JtfJGGsjGyd1hZ38VRX3a+qUdzEUp8SX2Ui8MUfA9v+H8HmAL4rpKP5AoGARqgY5EidOp6z7woblBgqT42qE0QLOabugPNiTx0mZOPFsiF1YyJH3825rUTDVRyGzC4Vmninnyz9Z222JbUMuPypvbyzOUWsX2WWAyINLjw9dD/c2Jp9fHDZh72ME1/OMMO/gGrw8xYomfD4r5zTY7hl+xe8OZLK2Kzv+teGxLE=\n"
   +"-----END PRIVATE KEY-----";

qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
qz.security.setSignaturePromise(function(toSign) {
    return function(resolve, reject) {
        try {
            var pk = KEYUTIL.getKey(privateKey);
            var sig = new KJUR.crypto.Signature({"alg": "SHA512withRSA"});  // Use "SHA1withRSA" for QZ Tray 2.0 and older
            sig.init(pk);
            sig.updateString(toSign);
            var hex = sig.sign();
            console.log("DEBUG: \n\n" + stob64(hextorstr(hex)));
            resolve(stob64(hextorstr(hex)));
        } catch (err) {
            console.error(err);
            reject(err);
        }
    };
});
*/
/*
			var privateKey = "-----BEGIN PRIVATE KEY-----\n" +
        "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC0yvZElcYUgN7XYNWQ97wtkW+o8hru5c9NlBmjKBZySNO9XAx5a3+Fvl3COqqj9pfVhMGj9TgiFEKhsuQpSufPhKAixQvI0P/YVfKlRjkhKXdpGw1KfYP5lsL/G+I0zlKBZ5RGtwkfEnU1t67GXrlS2n+Zxz52t5UZrsF3Rq3JPD7DuSbp20gCU29ZuCwVlXKAogOg+aJaYAeFAO3iyqEQwr4k/9x5gh1SVO6WF1Cq36xHiqNKiYRlyKyIH4z2p1XmIB+ot2izcKpNhWc7bAwrtKKYzApqFYJYFM736GScghacCRT01Y732Zhnb6oJP0r0AympmaubzH6lj0dnTRrdAgMBAAECggEAcle/Q4daXNNYnO1QhTw5lrRZ44cjw54y60TUph0PfT5UNHoIJa74WzUYBWSnUcX/J3qsIa9QzjT0I2KJ7RfD3rppsTPgjSSp+z/4k9EDBtc7/4ytbkRN+MgCEeinu3AWPGOvIr5WlYFOAihm8G0uVpl8hFzAQ8M+GJ0vHOnltdREyxOOBiIPq9YlgYtedFRH9E45pPyiCJZhtYIHrRAEoWcXnLEiYopTJMZ1iMUxstPlPPqB1TB8b7rm/O79swgn5IpHwFWWAPn0O03JWO0Dfu1rk+jjFPMjPnl/+N6YRdrrfNA/8Qu7IZful6vlR/LZFrqvvCQO2wDh1kL8mSMo4QKBgQDlT0DYoKR4EaY+Co/3L3QJLfsL9Iv10+nBjZ4V7UxomeeN36NbJRvKgV4EBxvOo/aEokTwCTIlUUi0/gYdefDDMTcZTZX1JQEm1D4Biy/AWVI7zkL5yIGoa+HSb8M/Esc7S24ULcFtCDBjgLV2DcefP0NW4d9k5jeVV2F1515yhQKBgQDJ1g2C6naoU4KfqZWjQIvmnGbiAxeQdxF00GJ1o2/kvg7ScUUdfgxM2wWdJNBrXbunZwRgAdp+7gNy8ssZF+rcnrMHtgGS14UEnitrv8XVM2n9MYpQCBJA5U7bb+ZW3XREG/RWihiiS6TIhctGSJVg2jKIQik4cRapzmr4bY8yeQKBgBQUk54kuyJUkVU/gYno2uVu5FwQpyz4yLCNYTmUVSuXxqIy4lA5+KwMFhqOMM4wp4bH2veWJpEEBDTtRcXRBtdg+O07URVtjyNCVMe304+pgrz/+/d5qnkAMX9HEDb2wwGbQmF7/EM7A1nDTKCw5jk4xgHWt8RTMKzgQQWdJDUtAoGBAL9wYRZoby2PMBlij4TtxwRwGoGK70HePddvg8QSxiHPR13BhA3KLb6EC5oWff0oRcItSxAJEgZMmUbhFVRyxBiICkVpE6sE8q0DnSmxFuL2xjQaVhc84JtfJGGsjGyd1hZ38VRX3a+qUdzEUp8SX2Ui8MUfA9v+H8HmAL4rpKP5AoGARqgY5EidOp6z7woblBgqT42qE0QLOabugPNiTx0mZOPFsiF1YyJH3825rUTDVRyGzC4Vmninnyz9Z222JbUMuPypvbyzOUWsX2WWAyINLjw9dD/c2Jp9fHDZh72ME1/OMMO/gGrw8xYomfD4r5zTY7hl+xe8OZLK2Kzv+teGxLE=\n" +
        "-----END PRIVATE KEY-----";

qz.security.setSignaturePromise(function (toSign) {
    return function (resolve, reject) {
        try {
            var pk = new RSAKey();
            pk.readPrivateKeyFromPEMString(strip(privateKey));
            //var hex = pk.signString(toSign, 'sha1');
            //console.log("DEBUG: \n\n" + stob64(hextorstr(hex)));
            //resolve(stob64(hextorstr(hex)));
        } catch (err) {
            console.error(err);
            reject(err);
        }
    };
});

*/
            /*try {

                socket = new WebSocket('ws://127.0.0.1:6441');

                socket.onopen = function () {
                    console.log('Connected');
                    return;
                };
                socket.onclose = function () {
                    console.log('Not Connected');
                    return;
                };
            } catch (e) {
                alert(e);
            }*/


            function openCashDrawer() {
                if (socket.readyState == 1) {
                    var ocddata = {
                        'printer': <?= $pos_settings->local_printers ? "''" : json_encode($printer); ?>,
                    };
                    socket.send(JSON.stringify({
                        type: 'open-cashdrawer',
                        data: ocddata
                    }));
                    return false;
                } else {
                    bootbox.alert('<?= lang('pos_print_error'); ?>');
                    return false;
                }
            }

			//var config ;

            function printReceipt(htmlString,htmlStringorder) {
				//alert(htmlString);
				try{

					function isMobileDevice() {
    var check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
};
/*
qz.websocket.connect().then(function() {
return qz.printers.find(<?= json_encode($printer->title); ?>) // Pass the printer name into the next Promise
}).then(function(printer) {
var config = qz.configs.create(printer, { encoding: "IBM864" }); // Create a default config for the found printer

var data = [{
  type: 'html',
  format: 'plain', // or 'file' if using a URL
  data: htmlString
}];

//return qz.print(config, [data]);
return qz.print(config, data,true);
}).catch(function(e) { console.error(e); });

*/


		   /*
		   var send_data='';
			var receipt_data=<?=json_encode($receipt); ?>;//receiptData();
			 $.each(receipt_data, function() {
                send_data+=this;
            });

            var ord_send_data='';
			var order_data=<?=json_encode($order); ?>;//receiptData();
			$.each(order_data, function() {
                ord_send_data+=this;
            });
			*/
			//
			/*
			if(!qz.websocket.isActive()){

			qz.websocket.connect().then(function() {

return qz.printers.find(<?= json_encode($printer->title); ?>) // Pass the printer name into the next Promise
}).then(function(printer) {
 config = qz.configs.create(printer, { encoding: "IBM864" }); // Create a default config for the found printer

var data = [{
  type: 'html',
  format: 'plain', // or 'file' if using a URL
  data: htmlString
}];

//return qz.print(config, [data]);
return qz.print(config, data,true);
}).catch(function(e) { console.error(e); });
			}else{


			//var config = qz.configs.create(<?= json_encode($printer->title); ?>, { encoding: "IBM864" }); // Create a default config for the found printer

var data = [{
  type: 'html',
  format: 'plain', // or 'file' if using a URL
  data: htmlString
}];

//return qz.print(config, [data]);
 qz.print(config, data).catch("Sample page cannot print commands using this printer language");
			}

*/
		var data = [
        { type: 'html',format: 'plain', data: htmlString }


		];
		var configs = [
        { "printer": <?= json_encode($printer->title); ?> }


		];

           $.each(<?= json_encode($order_printers); ?>, function() {

			   data.push({ type: 'html',format: 'plain', data: htmlStringorder });

			   configs.push({"printer": this.title});
			   //order_printers=this.title;

			   });



function promiseLoop() {

    var chain = [];

    for(var i = 0; i < data.length; i++) {
        (function(i_) {
            //setup this chain link
            var link = function() {
                return qz.printers.find(configs[i_].printer).then(function(found) {
                    return qz.print(qz.configs.create(found, { encoding: "IBM864" }), [data[i_]]);
                });
            };

            chain.push(link);
        })(i);
        //closure ensures this promise's concept of `i` doesn't change
    }

    //can be .connect or `Promise.resolve()`, etc
    var firstLink = new RSVP.Promise(function(r, e) { r(); });

    var lastLink = null;
    chain.reduce(function(sequence, link) {
        lastLink = sequence.then(link);//
        return lastLink;
    }, firstLink);

    //this will be the very last link in the chain
    lastLink.catch(function(err) {
        console.error(err);
    });
}





//Always make sure you are establishing a connection before printing
if(!qz.websocket.isActive()){
	//if(!isMobileDevice())
qz.websocket.connect().then(promiseLoop).catch(e);
//else
//qz.websocket.connect({ host: '192.168.1.104', usingSecure: false }).then(promiseLoop).catch(e);
}else{
promiseLoop();
}


}catch(err){
	//alert(err);
	}

                /*//if (socket.readyState == 1) {
                    var receipt_data = receiptData();
                    var socket_data = {
                        'printer': <?= $pos_settings->local_printers ? "''" : json_encode($printer); ?>,
                        'logo': '<?= !empty($biller->logo) ? base_url('assets/uploads/logos/' . $biller->logo) : ''; ?>',
                        'text': receipt_data,
                        'cash_drawer': <?= isset($modal) ? 0 : 1; ?>, 'drawer_code': '<?= $pos_settings->cash_drawer_codes; ?>'
                    };
                    socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
                    return false;
                //} else {
                    bootbox.alert('<?= lang('pos_print_error'); ?>');
                    return false;
                //}*/
            }
            </script>
<?php
    } ?>
<?php
    if ($pos_settings->auto_print && (!isset($modal) || empty($modal))) {
        ?>
        <script type="text/javascript">
                $(document).ready(function() {
                    //setTimeout(printReceipt, 1000);
                });
        </script>
<?php
    }
}
?>
