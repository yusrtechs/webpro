<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box spark-barcode-print-page">
    <div class="box-header no-print">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i><?= lang('print_barcode_label'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo sprintf(lang('print_barcode_heading'),
                anchor('admin/products/categories', lang('categories')),
                anchor('admin/products/categories', lang('subcategories')),
                anchor('admin/purchases', lang('purchases')),
                anchor('admin/products/transfers', lang('transfers'))
                ); ?></p>

                <div class="well well-sm no-print">
    <!-- إضافة المنتج -->
    <div class="form-group">
        <?= lang("add_product", "add_item"); ?>
        <?= form_input('add_item', '', 'class="form-control" id="add_item" placeholder="' . $this->lang->line("add_item") . '"'); ?>
    </div>

    <!-- نموذج طباعة الباركود -->
    <?= admin_form_open("products/print_barcodes", 'id="barcode-print-form" data-toggle="validator"'); ?>

    <div class="controls table-controls">
        <!-- حقول مخفية لإرسال البيانات -->
        <input type="hidden" name="jsonArr_product_id" id="jsonArr_product_id" value='1'/>
        <input type="hidden" name="jsonArr_quantity" id="jsonArr_quantity" value='1'/>
        <input type="hidden" name="jsonArr_productionDate" id="jsonArr_productionDate" value='1'/>
        <input type="hidden" name="jsonArr_product_unit" id="jsonArr_product_unit" value='1'/>

        <!-- جدول المنتجات -->
        <table id="bcTable" class="table items table-striped table-condensed table-hover">
            <thead>
                <tr>
                    <th class="col-xs-7"><?= lang("product_name") . " (" . $this->lang->line("product_code") . ")"; ?></th>
                    <th class="col-xs-1"><?= lang("quantity"); ?></th>
                    <th class="col-xs-2"><?= lang("unit"); ?></th>
                    <th class="col-xs-2"><?= lang("Production_Date"); ?></th>
                    <th class="text-center" style="width:30px;">
                        <i class="fa fa-trash-o" style="opacity:0.5;"></i>
                    </th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- خيارات الطباعة -->
    <div class="form-group">
        <?= lang('style', 'style'); ?>
        <?php
        $opts = [
            '' => lang('select').' '.lang('style'),
            40 => lang('40_per_sheet'), 30 => lang('30_per_sheet'), 24 => lang('24_per_sheet'),
            20 => lang('20_per_sheet'), 18 => lang('18_per_sheet'), 14 => lang('14_per_sheet'),
            12 => lang('12_per_sheet'), 10 => lang('10_per_sheet'), 50 => lang('continuous_feed')
        ];
        ?>
        <?= form_dropdown('style', $opts, set_value('style', 24), 'class="form-control tip" id="style" required="required"'); ?>

        <!-- إعدادات continuous feed -->
        <div class="row cf-con" style="margin-top:10px; display:none;">
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="input-group">
                        <?= form_input('cf_width', '', 'class="form-control" id="cf_width" placeholder="' . lang("width") . ' (e.g. 50mm, 2in)"'); ?>
                        <span class="input-group-addon"><?= lang('width'); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <div class="input-group">
                        <?= form_input('cf_height', '', 'class="form-control" id="cf_height" placeholder="' . lang("height") . ' (e.g. 30mm, 1in)"'); ?>
                        <span class="input-group-addon"><?= lang('height'); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <?php $oopts = [0 => lang('portrait'), 1 => lang('landscape')]; ?>
                    <?= form_dropdown('cf_orientation', $oopts , '', 'class="form-control" id="cf_orientation"'); ?>
                </div>
            </div>
        </div>

        <span class="help-block"><?= lang('barcode_tip'); ?></span>
    </div>

    <!-- خيارات الحقول للطباعة (بطاقات) -->
    <div class="form-group check-options">
        <?php
        $checkboxes = [
            'brand' => lang('brand'),
            'site_name' => lang('site_name'),
            'product_name' => lang('product_name'),
            'Production_Date' => lang('Production_Date'),
            'expiry' => lang('expiry'),
            'price' => lang('price'),
            'with_tax' => lang('Price_with_VAT'),
            'currencies' => lang('currencies'),
            'unit' => lang('unit'),
            'category' => lang('category'),
            'variants' => lang('variants'),
            'product_image' => lang('product_image'),
            'check_promo' => lang('promo_price')
        ];

        foreach ($checkboxes as $name => $label): ?>
            <label class="checkbox-card">
                <input type="checkbox" name="<?= $name ?>" id="<?= $name ?>" value="1" />
                <span class="checkbox-label"><?= $label ?></span>
            </label>
        <?php endforeach; ?>
    </div>

    <!-- أزرار التحكم -->
    <div class="form-group spark-barcode-actions">
        <?= form_submit('print', lang("update"), 'id="print" class="btn btn-primary"'); ?>
        <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
    </div>

    <?= form_close(); ?>
    <div class="clearfix"></div>
</div>

                <div id="barcode-con">
                    <?php
                        if ($this->input->post('print')) {
                            if (!empty($barcodes)) {
                                $raw_cf_width = $this->input->post('cf_width') ? trim($this->input->post('cf_width')) : '';
                                $raw_cf_height = $this->input->post('cf_height') ? trim($this->input->post('cf_height')) : '';
                                $cf_orientation = (int)$this->input->post('cf_orientation'); // 0: portrait, 1: landscape

                                $parse_dim = function($val, $fallback) {
                                    if (empty($val)) return $fallback;
                                    $val = trim((string)$val);
                                    if (preg_match('/^([0-9.]+)\s*(in|mm|cm|pt|px)?$/i', $val, $m)) {
                                        $num = $m[1];
                                        $unit = !empty($m[2]) ? strtolower($m[2]) : ($num > 10 ? 'mm' : 'in');
                                        return $num . $unit;
                                    }
                                    return $val;
                                };

                                $label_w = $parse_dim($raw_cf_width, '2in');
                                $label_h = $parse_dim($raw_cf_height, '1in');

                                if ($cf_orientation == 1) { // Landscape
                                    $item_w = $label_h;
                                    $item_h = $label_w;
                                    $page_w = $label_h;
                                    $page_h = $label_w;
                                } else {
                                    $item_w = $label_w;
                                    $item_h = $label_h;
                                    $page_w = $label_w;
                                    $page_h = $label_h;
                                }
                    ?>
                    <style type="text/css">
                    @media print {
                        <?php if ($style == 50): ?>
                        @page {
                            size: <?= $page_w ?> <?= $page_h ?> !important;
                            margin: 0mm !important;
                        }
                        html, body {
                            width: <?= $page_w ?> !important;
                            margin: 0 auto !important;
                            padding: 0 !important;
                            background: #ffffff !important;
                            text-align: center !important;
                        }
                        .spark-app,
                        .spark-main,
                        .spark-content,
                        #content,
                        .spark-barcode-print-page,
                        .box,
                        .box-content,
                        .row,
                        .col-lg-12,
                        #barcode-con {
                            width: 100% !important;
                            max-width: 100% !important;
                            margin: 0 auto !important;
                            padding: 0 !important;
                            border: none !important;
                            background: transparent !important;
                            text-align: center !important;
                            float: none !important;
                            box-sizing: border-box !important;
                        }
                        .item.style50 {
                            width: <?= $item_w ?> !important;
                            min-width: <?= $item_w ?> !important;
                            max-width: <?= $item_w ?> !important;
                            height: <?= $item_h ?> !important;
                            min-height: <?= $item_h ?> !important;
                            max-height: <?= $item_h ?> !important;
                            page-break-after: always !important;
                            break-after: page !important;
                            page-break-inside: avoid !important;
                            break-inside: avoid !important;
                            box-sizing: border-box !important;
                            margin: 0 auto !important;
                            padding: 0.5mm 0 !important;
                            display: flex !important;
                            flex-direction: column !important;
                            justify-content: space-between !important;
                            align-items: center !important;
                            text-align: center !important;
                            border: none !important;
                            overflow: hidden !important;
                            background: #ffffff !important;
                            float: none !important;
                        }
                        .item.style50 .div50 {
                            width: 100% !important;
                            max-width: 100% !important;
                            height: 100% !important;
                            border: none !important;
                            padding: 0 !important;
                            margin: 0 auto !important;
                            display: flex !important;
                            flex-direction: column !important;
                            justify-content: space-between !important;
                            align-items: center !important;
                            text-align: center !important;
                            overflow: hidden !important;
                            box-sizing: border-box !important;
                            background: #ffffff !important;
                        }
                        .item.style50 .barcode_site,
                        .item.style50 .barcode_name,
                        .item.style50 .barcode_brand,
                        .item.style50 .barcode_pdate,
                        .item.style50 .barcode_expiry,
                        .item.style50 .barcode_price,
                        .item.style50 .barcode_unit,
                        .item.style50 .barcode_category,
                        .item.style50 .variants {
                            display: block !important;
                            width: 100% !important;
                            max-width: 100% !important;
                            text-align: center !important;
                            margin: 0 auto !important;
                            padding: 0 1mm !important;
                            box-sizing: border-box !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                            white-space: nowrap !important;
                            line-height: 1.25 !important;
                        }
                        .item.style50 .barcode_site {
                            font-size: 8pt !important;
                            font-weight: bold !important;
                        }
                        .item.style50 .barcode_name {
                            font-size: 8.5pt !important;
                            font-weight: bold !important;
                        }
                        .item.style50 .barcode_price {
                            font-size: 9pt !important;
                            font-weight: bold !important;
                        }
                        .item.style50 .barcode_unit,
                        .item.style50 .barcode_pdate,
                        .item.style50 .barcode_expiry {
                            font-size: 7.5pt !important;
                        }
                        .item.style50 .barcode_image {
                            display: flex !important;
                            justify-content: center !important;
                            align-items: center !important;
                            width: 100% !important;
                            margin: 0 auto !important;
                            text-align: center !important;
                            padding: 0 !important;
                            line-height: 0 !important;
                        }
                        .item.style50 img.bcimg,
                        #barcode-con img.bcimg,
                        img.bcimg {
                            display: block !important;
                            margin: 0 auto !important;
                            max-width: 95% !important;
                            height: auto !important;
                            image-rendering: -webkit-optimize-contrast !important;
                            image-rendering: crisp-edges !important;
                            image-rendering: pixelated !important;
                        }
                        <?php elseif ($style == 12 || $style == 18 || $style == 24 || $style == 40): ?>
                        @page {
                            size: A4 portrait !important;
                            margin: 0mm !important;
                        }
                        html, body {
                            margin: 0 !important;
                            padding: 0 !important;
                            background: #ffffff !important;
                        }
                        .barcodea4 {
                            margin: 0 auto !important;
                            padding: 0.1in 0 0 0.1in !important;
                            border: none !important;
                            page-break-after: always !important;
                            break-after: page !important;
                            background: #ffffff !important;
                        }
                        .barcodea4 .item {
                            border: none !important;
                            page-break-inside: avoid !important;
                            break-inside: avoid !important;
                            background: #ffffff !important;
                        }
                        <?php else: ?>
                        @page {
                            size: portrait !important;
                            margin: 0mm !important;
                        }
                        html, body {
                            margin: 0 !important;
                            padding: 0 !important;
                            background: #ffffff !important;
                        }
                        .barcode {
                            margin: 0 auto !important;
                            padding-top: 0.1in !important;
                            border: none !important;
                            page-break-after: always !important;
                            break-after: page !important;
                            background: #ffffff !important;
                        }
                        .barcode .item {
                            border: none !important;
                            page-break-inside: avoid !important;
                            break-inside: avoid !important;
                            background: #ffffff !important;
                        }
                        <?php endif; ?>
                    }
                    @media screen {
                        .item.style50 {
                            width: <?= $item_w ?>;
                            height: <?= $item_h ?>;
                            margin: 10px auto;
                            box-sizing: border-box;
                        }
                        .item.style50 .div50 {
                            width: 100%;
                            height: 100%;
                            border: 1px dotted #ccc;
                            box-sizing: border-box;
                            padding: 4px;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            align-items: center;
                            text-align: center;
                        }
                    }
                    </style>
                    <?php
                                echo '<button type="button" onclick="window.print();return false;" class="btn btn-primary btn-block tip no-print spark-barcode-print-btn" title="'.lang('print').'"><i class="icon fa fa-print"></i> '.lang('print').'</button>';
                                $c = 1;
                                if ($style == 12 || $style == 18 || $style == 24 || $style == 40) {
                                    echo '<div class="barcodea4">';
                                } elseif ($style != 50) {
                                    echo '<div class="barcode">';
                                }
								$rr=1;
                                foreach ($barcodes as $item) {
                                    for ($r = 1; $r <= $item['quantity']; $r++) {
                                        if($item['bcis']==50 && $style==50){
                                            $style1=$style.' style50_10';
                                        }else{
                                            $style1=$style;
                                        }

                                        echo '<div class="item style'.$style1.'" '.
                                        ($style == 50 ? 'style="width:'.$item_w.';height:'.$item_h.';border:0;"' : '')
                                        .'>';
                                        if ($style == 50) {
                                            echo '<div class="div50">';
                                        }
                                        if($item['image']) {
                                            echo '<span class="product_image"><img src="'.base_url('assets/uploads/thumbs/'.$item['image']).'" alt="" /></span>';
                                        }
                                        if($item['brand']) {
                                            echo '<span class="barcode_brand">'.$item['brand'].'</span>';
                                        }
                                        if($item['site']) {
                                            echo '<span class="barcode_site">'.$item['site'].'</span>';
                                        }
                                        if($item['name']) {
                                            echo '<span class="barcode_name">'.$item['name'].'</span>';
                                        }
                                        if($item['productionDate']) {
                                            echo '<span class="barcode_name barcode_pdate">'.$item['productionDate'].'</span>';
                                        }
                                        if($item['expiry']) {
                                            echo '<span class="barcode_name barcode_expiry">'.$item['expiry'].'</span>';
                                        }
                                        if($item['price']) {
                                            if($item['price_after_discount'] && !$item['currencies']) {
                                                echo '<span class="barcode_price" style="text-decoration:line-through;">';
                                            }else{
                                                echo '<span class="barcode_price">';
                                            }
                                            if($item['currencies']) {
                                                foreach ($currencies as $currency) {
                                                    echo $currency->code . ': ' . $this->sma->formatMoney($item['price'] * $currency->rate, 'none').', ';
                                                }
                                            } else {
                                                if($item['currency_code']=="SR"){
                                                    if ($this->Settings->user_language == 'arabic') {
                                                        echo $item['price'].'<img src ="./assets/images/Saudi_Riyal_Symbol-1.png" style="width:14px !important;vertical-align:middle;margin:0 2px;"/>';
                                                    }else{
                                                        echo '<img src ="./assets/images/Saudi_Riyal_Symbol-1.png" style="width:14px !important;vertical-align:middle;margin:0 2px;"/>'.$item['price'];
                                                    }
                                                }else{
                                                    echo $item['price'].$item['currency_code'];
                                                }
                                            }
                                            echo '</span>';
                                        }

                                        if($item['price_after_discount'] && !$item['currencies']) {
                                            echo '<span class="barcode_price">';
                                            if($item['currency_code']=="SR"){
                                                if ($this->Settings->user_language == 'arabic') {
                                                    echo $item['price_after_discount'].'<img src ="./assets/images/Saudi_Riyal_Symbol-1.png" style="width:14px !important;vertical-align:middle;margin:0 2px;"/>';
                                                }else{
                                                    echo '<img src ="./assets/images/Saudi_Riyal_Symbol-1.png" style="width:14px !important;vertical-align:middle;margin:0 2px;"/>'.$item['price_after_discount'];
                                                }
                                            }else{
                                                echo $item['price_after_discount'].$item['currency_code'];
                                            }
                                            echo '</span>';
                                        }
                                        if($item['unit']) {
                                            echo '<span class="barcode_unit">'.lang('unit').': '.$item['prunitName'].'</span>';
                                        }
                                        if($item['category']) {
                                            echo '<span class="barcode_category">'.lang('category').': '.$item['category'].'</span>';
                                        }
                                        if($item['variants']) {
                                            $v_str = '';
                                            foreach ($item['variants'] as $variant) {
                                                $v_str .= ($v_str ? ', ' : '') . $variant->name;
                                            }
                                            echo '<span class="variants">'.lang('variants').': '.$v_str.'</span>';
                                        }
										if($item['barcode2']!='')
                                            echo '<span class="barcode_image"><img src="'.admin_url('products/barcode/'.$item['barcode2'].'/'.$item['bcs'].'/'.$item['bcis'].'?'.$rr).'" alt="'.$item['barcode'].'" class="bcimg" /></span>';
                                        else {
                                            echo '<span class="barcode_image"><img src="'.admin_url('products/barcode/'.$item['barcode'].'/'.$item['bcs'].'/'.$item['bcis']).'" alt="'.$item['barcode'].'" class="bcimg" /></span>';
                                        }
                                        if ($style == 50) {
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        if ($style == 40) {
                                            if ($c % 40 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 30) {
                                            if ($c % 30 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        } elseif ($style == 24) {
                                            if ($c % 24 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 20) {
                                            if ($c % 20 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        } elseif ($style == 18) {
                                            if ($c % 18 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 14) {
                                            if ($c % 14 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        } elseif ($style == 12) {
                                            if ($c % 12 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 10) {
                                            if ($c % 10 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        }
                                        $c++;
										$rr++;
                                    }
                                }
                                if ($style != 50) {
                                    echo '</div>';
                                }
                                echo '<button type="button" onclick="window.print();return false;" class="btn btn-primary btn-block tip no-print spark-barcode-print-btn" title="'.lang('print').'"><i class="icon fa fa-print"></i> '.lang('print').'</button>';
                            } else {
                                echo '<h3>'.lang('no_product_selected').'</h3>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ac = false; bcitems = {};
    if (localStorage.getItem('bcitems')) {
        bcitems = JSON.parse(localStorage.getItem('bcitems'));
    }
    <?php if($items) { ?>
    localStorage.setItem('bcitems', JSON.stringify(<?= $items; ?>));
    <?php } ?>
    $(document).ready(function() {


		$(document).on('click', '#print', function(e) {

		var formData_product_id = JSON.stringify($(".rid").serializeArray());
		$('#jsonArr_product_id').val(formData_product_id);

		var formData_quantity = JSON.stringify($(".quantity").serializeArray());
		$('#jsonArr_quantity').val(formData_quantity);

        var formData_prdate = JSON.stringify($(".prdate").serializeArray());
		$('#jsonArr_productionDate').val(formData_prdate);

		var formData_product_unit = JSON.stringify($(".rprunit").serializeArray());
		$('#jsonArr_product_unit').val(formData_product_unit);

        });


        <?php if ($this->input->post('print')) { ?>
            $( window ).load(function() {





               // $('html, body').animate({
               //     scrollTop: ($("#barcode-con").offset().top)-15
               // }, 1000);
            });
        <?php } ?>
        if (localStorage.getItem('bcitems')) {
            loadItems();
        }
        $("#add_item").autocomplete({
            source: '<?= admin_url('products/get_suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });
        check_add_item_val();

        $('#style').change(function (e) {
            localStorage.setItem('bcstyle', $(this).val());
            if ($(this).val() == 50) {
                $('.cf-con').slideDown();
            } else {
                $('.cf-con').slideUp();
            }
        });
        if (style = localStorage.getItem('bcstyle')) {
            $('#style').val(style);
            $('#style').select2("val", style);
            if (style == 50) {
                $('.cf-con').slideDown();
            } else {
                $('.cf-con').slideUp();
            }
        }

        $('#cf_width').change(function (e) {
            localStorage.setItem('cf_width', $(this).val());
        });
        if (cf_width = localStorage.getItem('cf_width')) {
            $('#cf_width').val(cf_width);
        }

        $('#cf_height').change(function (e) {
            localStorage.setItem('cf_height', $(this).val());
        });
        if (cf_height = localStorage.getItem('cf_height')) {
            $('#cf_height').val(cf_height);
        }

        $('#cf_orientation').change(function (e) {
            localStorage.setItem('cf_orientation', $(this).val());
        });
        if (cf_orientation = localStorage.getItem('cf_orientation')) {
            $('#cf_orientation').val(cf_orientation);
        }

        $(document).on('ifChecked', '#site_name', function(event) {
            localStorage.setItem('bcsite_name', 1);
        });
        $(document).on('ifUnchecked', '#site_name', function(event) {
            localStorage.setItem('bcsite_name', 0);
        });
        $(document).on('ifChecked', '#Production_Date', function(event) {
            localStorage.setItem('bcProduction_Date', 1);
        });
        $(document).on('ifUnchecked', '#Production_Date', function(event) {
            localStorage.setItem('bcProduction_Date', 0);
        });
        $(document).on('ifChecked', '#expiry', function(event) {
            localStorage.setItem('bcexpiry', 1);
        });
        $(document).on('ifUnchecked', '#expiry', function(event) {
            localStorage.setItem('bcexpiry', 0);
        });
        if (site_name = localStorage.getItem('bcsite_name')) {
            if (site_name == 1)
                $('#site_name').iCheck('check');
            else
                $('#site_name').iCheck('uncheck');
        }
        if (Production_Date = localStorage.getItem('bcProduction_Date')) {
            if (Production_Date == 1)
                $('#Production_Date').iCheck('check');
            else
                $('#Production_Date').iCheck('uncheck');
        }

        if (expiry = localStorage.getItem('bcexpiry')) {
            if (expiry == 1)
                $('#expiry').iCheck('check');
            else
                $('#expiry').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#product_name', function(event) {
            localStorage.setItem('bcproduct_name', 1);
        });
        $(document).on('ifUnchecked', '#product_name', function(event) {
            localStorage.setItem('bcproduct_name', 0);
        });
        if (product_name = localStorage.getItem('bcproduct_name')) {
            if (product_name == 1)
                $('#product_name').iCheck('check');
            else
                $('#product_name').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#price', function(event) {
            localStorage.setItem('bcprice', 1);
        });
        $(document).on('ifUnchecked', '#price', function(event) {
            localStorage.setItem('bcprice', 0);
            $('#currencies').iCheck('uncheck');
        });
        if (price = localStorage.getItem('bcprice')) {
            if (price == 1)
                $('#price').iCheck('check');
            else
                $('#price').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#currencies', function(event) {
            localStorage.setItem('bccurrencies', 1);
        });
        $(document).on('ifUnchecked', '#currencies', function(event) {
            localStorage.setItem('bccurrencies', 0);
        });
        if (currencies = localStorage.getItem('bccurrencies')) {
            if (currencies == 1)
                $('#currencies').iCheck('check');
            else
                $('#currencies').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#unit', function(event) {
            localStorage.setItem('bcunit', 1);
        });
        $(document).on('ifUnchecked', '#unit', function(event) {
            localStorage.setItem('bcunit', 0);
        });
        if (unit = localStorage.getItem('bcunit')) {
            if (unit == 1)
                $('#unit').iCheck('check');
            else
                $('#unit').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#category', function(event) {
            localStorage.setItem('bccategory', 1);
        });
        $(document).on('ifChecked', '#brand', function(event) {
            localStorage.setItem('bcbrand', 1);
        });
        $(document).on('ifUnchecked', '#category', function(event) {
            localStorage.setItem('bccategory', 0);
        });
        $(document).on('ifUnchecked', '#brand', function(event) {
            localStorage.setItem('bcbrand', 0);
        });
        if (category = localStorage.getItem('bccategory')) {
            if (category == 1)
                $('#category').iCheck('check');
            else
                $('#category').iCheck('uncheck');
        }
        if (brand = localStorage.getItem('bcbrand')) {
            if (brand == 1)
                $('#brand').iCheck('check');
            else
                $('#brand').iCheck('uncheck');
        }
        $(document).on('ifChecked', '#check_promo', function(event) {
            localStorage.setItem('bccheck_promo', 1);
        });
        $(document).on('ifUnchecked', '#check_promo', function(event) {
            localStorage.setItem('bccheck_promo', 0);
        });
        if (check_promo = localStorage.getItem('bccheck_promo')) {
            if (check_promo == 1)
                $('#check_promo').iCheck('check');
            else
                $('#check_promo').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#with_tax', function(event) {
            localStorage.setItem('bcwith_tax', 1);
        });
        $(document).on('ifUnchecked', '#with_tax', function(event) {
            localStorage.setItem('bcwith_tax', 0);
        });
        if (with_tax = localStorage.getItem('bcwith_tax')) {
            if (with_tax == 1)
                $('#with_tax').iCheck('check');
            else
                $('#with_tax').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#product_image', function(event) {
            localStorage.setItem('bcproduct_image', 1);
        });
        $(document).on('ifUnchecked', '#product_image', function(event) {
            localStorage.setItem('bcproduct_image', 0);
        });
        if (product_image = localStorage.getItem('bcproduct_image')) {
            if (product_image == 1)
                $('#product_image').iCheck('check');
            else
                $('#product_image').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#variants', function(event) {
            localStorage.setItem('bcvariants', 1);
        });
        $(document).on('ifUnchecked', '#variants', function(event) {
            localStorage.setItem('bcvariants', 0);
        });
        if (variants = localStorage.getItem('bcvariants')) {
            if (variants == 1)
                $('#variants').iCheck('check');
            else
                $('#variants').iCheck('uncheck');
        }

        $(document).on('ifChecked', '.checkbox', function(event) {
            var item_id = $(this).attr('data-item-id');
            var vt_id = $(this).attr('id');
            bcitems[item_id]['selected_variants'][vt_id] = 1;
            localStorage.setItem('bcitems', JSON.stringify(bcitems));
        });
        $(document).on('ifUnchecked', '.checkbox', function(event) {
            var item_id = $(this).attr('data-item-id');
            var vt_id = $(this).attr('id');
            bcitems[item_id]['selected_variants'][vt_id] = 0;
            localStorage.setItem('bcitems', JSON.stringify(bcitems));
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete bcitems[id];
            localStorage.setItem('bcitems', JSON.stringify(bcitems));
            $(this).closest('#row_' + id).remove();
        });

        $('#reset').click(function (e) {

            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                    if (localStorage.getItem('bcitems')) {
                        localStorage.removeItem('bcitems');
                    }
                    if (localStorage.getItem('bcstyle')) {
                        localStorage.removeItem('bcstyle');
                    }
                    if (localStorage.getItem('bcsite_name')) {
                        localStorage.removeItem('bcsite_name');
                    }
                    if (localStorage.getItem('bcProduction_Date')) {
                        localStorage.removeItem('bcProduction_Date');
                    }
                    if (localStorage.getItem('bcexpiry')) {
                        localStorage.removeItem('bcexpiry');
                    }
                    if (localStorage.getItem('bcproduct_name')) {
                        localStorage.removeItem('bcproduct_name');
                    }
                    if (localStorage.getItem('bcprice')) {
                        localStorage.removeItem('bcprice');
                    }
                    if (localStorage.getItem('bccurrencies')) {
                        localStorage.removeItem('bccurrencies');
                    }
                    if (localStorage.getItem('bcunit')) {
                        localStorage.removeItem('bcunit');
                    }
                    if (localStorage.getItem('bccategory')) {
                        localStorage.removeItem('bccategory');
                    }
                    if (localStorage.getItem('bcbrand')) {
                        localStorage.removeItem('bcbrand');
                    }
                    // if (localStorage.getItem('cf_width')) {
                    //     localStorage.removeItem('cf_width');
                    // }
                    // if (localStorage.getItem('cf_height')) {
                    //     localStorage.removeItem('cf_height');
                    // }
                    // if (localStorage.getItem('cf_orientation')) {
                    //     localStorage.removeItem('cf_orientation');
                    // }

                    $('#modal-loading').show();
                    window.location.replace("<?= admin_url('products/print_barcodes'); ?>");
                }
            });
        });

        var old_row_qty;
        $(document).on("focus", '.quantity', function () {
            old_row_qty = $(this).val();
        }).on("change", '.quantity', function () {
            var row = $(this).closest('tr');
            if (!is_numeric($(this).val())) {
                $(this).val(old_row_qty);
                bootbox.alert(lang.unexpected_value);
                return;
            }
            var new_qty = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');
            bcitems[item_id].qty = new_qty;
            localStorage.setItem('bcitems', JSON.stringify(bcitems));
        });

        var old_row_prdate;
        $(document).on("focus", '.prdate', function () {
            old_row_prdate = $(this).val();
        }).on("change", '.prdate', function () {
            var row = $(this).closest('tr');

            var new_prdate = ($(this).val()),
            item_id = row.attr('data-item-id');
            bcitems[item_id].productionDate = new_prdate;
            localStorage.setItem('bcitems', JSON.stringify(bcitems));
        });



		$(document).on("change", '.rprunit', function () {

        var row = $(this).closest('tr');
        var new_opt = $(this).val(),
        item_id = row.attr('data-item-id');

        bcitems[item_id].unit = new_opt;
        localStorage.setItem('bcitems', JSON.stringify(bcitems));
    });



    $('#style').val('<?= $print_barcode_settings->style ?>').trigger('change');
    $('#cf_width').val('<?= $print_barcode_settings->width ?>').trigger('change');
    $('#cf_height').val('<?= $print_barcode_settings->height ?>').trigger('change');
    $('#cf_orientation').val('<?= $print_barcode_settings->orientation ?>').trigger('change');
    if('<?= $print_barcode_settings->brand ?>' == 1){
        $('#brand').prop('checked', true).trigger('ifChecked');
    }else{
        $('#brand').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->site_name ?>' == 1){
        $('#site_name').prop('checked', true).trigger('ifChecked');
    }else{
        $('#site_name').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->product_name ?>' == 1){
        $('#product_name').prop('checked', true).trigger('ifChecked');
    }else{
        $('#product_name').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->production_date ?>' == 1){
        $('#Production_Date').prop('checked', true).trigger('ifChecked');
    }else{
        $('#Production_Date').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->expiry ?>' == 1){
        $('#expiry').prop('checked', true).trigger('ifChecked');
    }else{
        $('#expiry').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->price ?>' == 1){
        $('#price').prop('checked', true).trigger('ifChecked');
    }else{
        $('#price').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->currencies ?>' == 1){
        $('#currencies').prop('checked', true).trigger('ifChecked');
    }else{
        $('#currencies').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->unit ?>' == 1){
        $('#unit').prop('checked', true).trigger('ifChecked');
    }else{
        $('#unit').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->category ?>' == 1){
        $('#category').prop('checked', true).trigger('ifChecked');
    }else{
        $('#category').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->variants ?>' == 1){
        $('#variants').prop('checked', true).trigger('ifChecked');
    }else{
        $('#variants').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->product_image ?>' == 1){
        $('#product_image').prop('checked', true).trigger('ifChecked');
    }else{
        $('#product_image').prop('checked', false).trigger('ifUnchecked');
    }
    if('<?= $print_barcode_settings->check_promo ?>' == 1){
        $('#check_promo').prop('checked', true).trigger('ifChecked');
    }else{
        $('#check_promo').prop('checked', false).trigger('ifUnchecked');
    }

    if('<?= $print_barcode_settings->with_tax ?>' == 1){
        $('#with_tax').prop('checked', true).trigger('ifChecked');
    }else{
        $('#with_tax').prop('checked', false).trigger('ifUnchecked');
    }

    });

    function add_product_item(item) {
        ac = true;
        if (item == null) {
            return false;
        }
		item_id = item.id;


		if (bcitems[item_id]) {
            bcitems[item_id].qty = parseFloat(bcitems[item_id].qty) + 1;
        } else {
            bcitems[item_id] = item;
            bcitems[item_id]['selected_variants'] = {};
            $.each(item.variants, function () {
                bcitems[item_id]['selected_variants'][this.id] = 1;
            });
        }

        bcitems[item_id].productionDate = '';

        localStorage.setItem('bcitems', JSON.stringify(bcitems));
        loadItems();
        return true;

    }

    function loadItems () {

        if (localStorage.getItem('bcitems')) {
            $("#bcTable tbody").empty();
            bcitems = JSON.parse(localStorage.getItem('bcitems'));

            $.each(bcitems, function () {

                var item = this;
                var row_no = item.id;
                var vd = '';



				var opt = $("<select name=\"prunit[]\" id=\"prunit_" + row_no + "\"  class=\"form-control select rprunit\" />");



				var v = item.unit;
                console.log(item);

            if (v) {

                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubUnitsForPrinting') ?>/" + row_no,
                    dataType: "json",
                    success: function (data) {
						opt.select2("destroy").empty();
                        //opt.select2("destroy").empty().select2({minimumResultsForSearch: 7});
						    $("<option />", {value: "", text: ""}).appendTo(opt);

                        $.each(data, function () {

                            $("<option />", {value: this.id, text: this.name+' ('+this.code+')'}).appendTo(opt);
                        });
						//opt.val(v).change();
                        opt.select2('val', v);
						//alert('');
						//opt.val(2).trigger('change.select2');
						//alert('');


						//alert(opt.get(0).outerHTML)
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                opt.select2("destroy").empty();
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo(opt);
                opt.select2({minimumResultsForSearch: 7}).select2('val', '');
            }




                var newTr = $('<tr id="row_' + row_no + '" class="row_' + item.id + '" data-item-id="' + item.id + '"></tr>');
                tr_html = '<td><input name="product[]" type="hidden" class="rid" value="' + item.id + '"><span id="name_' + row_no + '">' + item.name + ' (' + item.code + ')</span></td>';
                tr_html += '<td><input class="form-control quantity text-center" name="quantity[]" type="text" value="' + formatDecimal(item.qty) + '" data-id="' + row_no + '" data-item="' + item.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                //if(item.variants) {
                //    $.each(item.variants, function () {
                //        vd += '<input name="vt_'+ item.id +'_'+ this.id +'" type="checkbox" class="checkbox" id="'+this.id+'" data-item-id="'+item.id+'" value="'+this.id+'" '+( item.selected_variants[this.id] == 1 ? '' : '')+' style="display:inline-block;" /><label for="'+this.id+'" class="padding05">'+this.name+'</label>';
                //    });
                //}
                //tr_html += '<td>'+vd+'</td>';
				//alert(opt.get(0).outerHTML);
				tr_html += '<td>'+(opt.get(0).outerHTML)+'</td>';
				tr_html += '<td><input class="form-control prdate tip date" name="prdate[]" type="text" value="'+item.productionDate+'" data-id="' + row_no + '" data-item="' + item.id + '" id="prdate_' + row_no + '" onClick="this.select();"></td>';
                //tr_html += '<td><select id="prunit" class="form-control select rvariant" style="width: 100%;" tabindex="-1" title=""><option value="9">pice (003)</option><option value="12">كرتون 1X6 (004)</option></select></td>';


                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.appendTo("#bcTable");
            });
            $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
            return true;
        }
    }

</script>
