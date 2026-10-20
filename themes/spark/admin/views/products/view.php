<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="spark-product-details">
    <!-- 1. Hero Product Header Card (Shared Top Header) -->
    <div class="spark-product-header-card">
        <div class="spark-product-header-top">
            <div class="spark-product-header-main">
                <div class="spark-product-avatar-wrap">
                    <img src="<?= base_url() ?>assets/uploads/<?= $product->image ?>" alt="<?= $product->name ?>" class="img-responsive"/>
                </div>
                <div class="spark-product-header-info">
                    <h1 class="spark-product-title"><?= $product->name . (SHOP && $product->hide != 1 ? ' (' . lang('shop_views') . ': ' . $product->views . ')' : ''); ?></h1>
                    <div class="spark-product-code-badge">
                        <i class="fa fa-barcode"></i> <?= $product->code; ?>
                    </div>
                    <div class="spark-product-header-meta">
                        <span class="spark-product-meta-item">
                            <i class="fa fa-folder-o"></i> <?= $category->name; ?><?= $subcategory ? ' / ' . $subcategory->name : ''; ?>
                        </span>
                        <?php if ($brand) { ?>
                            <span class="spark-product-meta-item">
                                <i class="fa fa-tag"></i> <?= $brand->name; ?>
                            </span>
                        <?php } ?>
                        <span class="spark-product-meta-item">
                            <i class="fa fa-cube"></i> <?= lang($product->type); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="spark-product-header-actions">
                <a href="<?= admin_url('products/edit/' . $product->id) ?>" class="spark-btn spark-btn-primary spark-btn-sm tip" title="<?= lang('edit') ?>">
                    <i class="fa fa-edit"></i> <?= lang('edit') ?>
                </a>
                <a href="<?= admin_url('products/print_barcodes/' . $product->id) ?>" class="spark-btn spark-btn-outline spark-btn-sm tip" title="<?= lang('print_barcode_label') ?>">
                    <i class="fa fa-print"></i> <?= lang('print_barcode_label') ?>
                </a>
                <a href="<?= admin_url('products/pdf/' . $product->id) ?>" class="spark-btn spark-btn-outline spark-btn-sm tip" title="<?= lang('pdf') ?>">
                    <i class="fa fa-download"></i> <?= lang('pdf') ?>
                </a>
                <a href="#" class="spark-btn spark-btn-danger spark-btn-sm tip bpo" title="<b><?= lang('delete_product') ?></b>"
                   data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('products/delete/' . $product->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                   data-html="true" data-placement="left">
                    <i class="fa fa-trash-o"></i> <?= lang('delete') ?>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Summary Stats Strip (Authentic Data Only) -->
    <div class="spark-product-stats-grid">
        <?php if ($Owner || $Admin || $this->session->userdata('show_price')) { ?>
            <div class="spark-product-stat-card">
                <div class="spark-product-stat-content">
                    <span class="spark-product-stat-label"><?= lang('price'); ?></span>
                    <span class="spark-product-stat-value"><?= $this->sma->formatMoney($product->price); ?></span>
                </div>
                <div class="spark-product-stat-icon primary">
                    <i class="fa fa-tag"></i>
                </div>
            </div>
        <?php } ?>

        <?php if ($Owner || $Admin || $this->session->userdata('show_cost')) { ?>
            <div class="spark-product-stat-card">
                <div class="spark-product-stat-content">
                    <span class="spark-product-stat-label"><?= lang('cost'); ?></span>
                    <span class="spark-product-stat-value"><?= $this->sma->formatMoney($product->cost); ?></span>
                </div>
                <div class="spark-product-stat-icon info">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        <?php } ?>

        <?php if ($product->alert_quantity != 0) { ?>
            <div class="spark-product-stat-card">
                <div class="spark-product-stat-content">
                    <span class="spark-product-stat-label"><?= lang('alert_quantity'); ?></span>
                    <span class="spark-product-stat-value"><?= $this->sma->formatQuantity($product->alert_quantity); ?></span>
                </div>
                <div class="spark-product-stat-icon warning">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
            </div>
        <?php } ?>

        <?php if ($product->promotion && ($Owner || $Admin || $this->session->userdata('show_price'))) { ?>
            <div class="spark-product-stat-card">
                <div class="spark-product-stat-content">
                    <span class="spark-product-stat-label"><?= lang('promotion'); ?></span>
                    <span class="spark-product-stat-value"><?= $this->sma->formatMoney($product->promo_price); ?></span>
                </div>
                <div class="spark-product-stat-icon success">
                    <i class="fa fa-percent"></i>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- 3. Sticky Product Navigation Tabs Bar -->
    <div class="spark-product-tabs-wrapper">
        <ul id="myTab" class="nav nav-tabs">
            <li class="active"><a href="#details" data-toggle="tab" class="tab-grey"><?= lang('product_details') ?></a></li>
            <?php if ($Owner || $Admin) { ?>
                <li class=""><a href="#chart" data-toggle="tab" class="tab-grey"><?= lang('chart') ?></a></li>
            <?php } ?>
            <?php if ($this->site->IsPermissions('sales-index')) { ?>
                <li class=""><a href="#sales" data-toggle="tab" class="tab-grey"><?= lang('sales') ?></a></li>
            <?php } ?>
            <?php if ($this->site->IsPermissions('quotes-index')) { ?>
                <li class=""><a href="#quotes" data-toggle="tab" class="tab-grey"><?= lang('quotes') ?></a></li>
            <?php } ?>

            <?php if ($product->type == 'standard') { ?>
                <?php if ($this->site->IsPermissions('purchases-index')) { ?>
                    <li class=""><a href="#purchases" data-toggle="tab" class="tab-grey"><?= lang('purchases') ?></a></li>
                <?php } ?>
                <?php if ($this->site->IsPermissions('transfers-index')) { ?>
                    <li class=""><a href="#transfers" data-toggle="tab" class="tab-grey"><?= lang('transfers') ?></a></li>
                <?php } ?>
                <?php if ($this->site->IsPermissions('products-adjustments')) { ?>
                    <li class=""><a href="#quantity_adjustments" data-toggle="tab" class="tab-grey"><?= lang('quantity_adjustments') ?></a></li>
                <?php } ?>
                <?php if ($this->site->IsPermissions('products-destruction_inventory')) { ?>
                    <li class=""><a href="#destruction_inventory" data-toggle="tab" class="tab-grey"><?= lang('Inventory_operations') ?></a></li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>

    <!-- 4. Tab Content -->
    <div class="tab-content">
        <div id="details" class="tab-pane fade in active">
            <div class="spark-product-details-grid">
                <!-- Left Column (5fr): Images & Specifications -->
                <div class="spark-details-col">
                    <!-- Section 1: Product Images / Gallery -->
                    <div class="spark-card">
                        <div class="spark-card-header">
                            <h3 class="spark-card-header-title"><i class="fa fa-image" style="color: var(--spark-primary);"></i> <?= lang('images'); ?></h3>
                        </div>
                        <div class="spark-card-body">
                            <div id="multiimages">
                                <?php if (!empty($images)) {
                                    echo '<a class="img-thumbnail" data-toggle="lightbox" data-gallery="multiimages" data-parent="#multiimages" href="' . base_url() . 'assets/uploads/' . $product->image . '" style="margin-right:5px;"><img class="img-responsive" src="' . base_url() . 'assets/uploads/thumbs/' . $product->image . '" alt="' . $product->image . '" style="width:' . $Settings->twidth . 'px; height:' . $Settings->theight . 'px;" /></a>';
                                    foreach ($images as $ph) {
                                        echo '<div class="gallery-image"><a class="img-thumbnail" data-toggle="lightbox" data-gallery="multiimages" data-parent="#multiimages" href="' . base_url() . 'assets/uploads/' . $ph->photo . '" style="margin-right:5px;"><img class="img-responsive" src="' . base_url() . 'assets/uploads/thumbs/' . $ph->photo . '" alt="' . $ph->photo . '" style="width:' . $Settings->twidth . 'px; height:' . $Settings->theight . 'px;" /></a>';
                                        if ($Owner || $Admin || $GP['products-edit']) {
                                            echo '<a href="#" class="delimg" data-item-id="' . $ph->id . '"><i class="fa fa-times"></i></a>';
                                        }
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<img src="' . base_url() . 'assets/uploads/' . $product->image . '" alt="' . $product->name . '" class="img-responsive img-thumbnail"/>';
                                }
                                ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Basic Product Information (Specifications) -->
                    <div class="spark-card">
                        <div class="spark-card-header">
                            <h3 class="spark-card-header-title"><i class="fa fa-info-circle" style="color: var(--spark-primary);"></i> <?= lang('product_details'); ?></h3>
                        </div>
                        <div class="spark-card-body">
                            <div class="spark-spec-list">
                                <div class="spark-spec-item">
                                    <span class="spark-spec-label"><i class="fa fa-tag"></i> <?= lang('name'); ?></span>
                                    <span class="spark-spec-value"><?= $product->name; ?></span>
                                </div>
                                <div class="spark-spec-item">
                                    <span class="spark-spec-label"><i class="fa fa-barcode"></i> <?= lang('code'); ?></span>
                                    <span class="spark-spec-value"><?= $product->code; ?></span>
                                </div>
                                <?php if ($product->item_no) { ?>
                                    <div class="spark-spec-item">
                                        <span class="spark-spec-label"><i class="fa fa-hashtag"></i> <?= lang('Item_no'); ?></span>
                                        <span class="spark-spec-value"><?= $product->item_no; ?></span>
                                    </div>
                                <?php } ?>
                                <div class="spark-spec-item">
                                    <span class="spark-spec-label"><i class="fa fa-cubes"></i> <?= lang('type'); ?></span>
                                    <span class="spark-spec-value"><?= lang($product->type); ?></span>
                                </div>
                                <div class="spark-spec-item">
                                    <span class="spark-spec-label"><i class="fa fa-folder"></i> <?= lang('category'); ?></span>
                                    <span class="spark-spec-value"><?= $category->name; ?></span>
                                </div>
                                <?php if ($product->subcategory_id) { ?>
                                    <div class="spark-spec-item">
                                        <span class="spark-spec-label"><i class="fa fa-folder-open"></i> <?= lang('subcategory'); ?></span>
                                        <span class="spark-spec-value"><?= $subcategory->name; ?></span>
                                    </div>
                                <?php } ?>
                                <?php if ($brand) { ?>
                                    <div class="spark-spec-item">
                                        <span class="spark-spec-label"><i class="fa fa-trademark"></i> <?= lang('brand'); ?></span>
                                        <span class="spark-spec-value"><?= $brand->name; ?></span>
                                    </div>
                                <?php } ?>
                                <div class="spark-spec-item">
                                    <span class="spark-spec-label"><i class="fa fa-balance-scale"></i> <?= lang('unit'); ?></span>
                                    <span class="spark-spec-value"><?= $unit ? $unit->name . ' (' . $unit->code . ')' : ''; ?></span>
                                </div>
                                <?php if ($product->tax_rate) { ?>
                                    <div class="spark-spec-item">
                                        <span class="spark-spec-label"><i class="fa fa-percent"></i> <?= lang('tax_rate'); ?></span>
                                        <span class="spark-spec-value"><?= $tax_rate->name; ?></span>
                                    </div>
                                    <div class="spark-spec-item">
                                        <span class="spark-spec-label"><i class="fa fa-calculator"></i> <?= lang('tax_method'); ?></span>
                                        <span class="spark-spec-value"><?= $product->tax_method == 0 ? lang('inclusive') : lang('exclusive'); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if ($variants) { ?>
                                    <div class="spark-spec-item">
                                        <span class="spark-spec-label"><i class="fa fa-th-list"></i> <?= lang('product_variants'); ?></span>
                                        <span class="spark-spec-value">
                                            <?php foreach ($variants as $variant) {
                                                echo '<span class="spark-badge spark-badge-primary" style="margin-inline-start:4px;">' . $variant->name . '</span> ';
                                            } ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (7fr): Barcode, Warehouses, Prices, Variants, Custom Fields, Combo -->
                <div class="spark-details-col">
                    <!-- Section 3: Barcode & QR Code Card -->
                    <div class="spark-card">
                        <div class="spark-card-header">
                            <h3 class="spark-card-header-title"><i class="fa fa-qrcode" style="color: var(--spark-primary);"></i> <?= lang('barcode_qrcode'); ?></h3>
                        </div>
                        <div class="spark-card-body">
                            <div class="spark-barcode-container">
                                <div class="spark-barcode-img-wrap">
                                    <img src="<?= admin_url('misc/barcode/' . $product->code . '/' . $product->barcode_symbology . '/74/0'); ?>" alt="<?= $product->code; ?>" class="bcimg" />
                                </div>
                                <div class="spark-qr-wrap">
                                    <?= $this->sma->qrcode('link', urlencode(admin_url('products/view/' . $product->id)), 2); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Warehouse Inventory Quantity Table -->
                    <?php if ((!$Supplier || !$Customer) && !empty($warehouses) && ($product->type == 'standard' || $product->type == 'manufacturing' || $product->type == 'combo')) { ?>
                        <div class="spark-card">
                            <div class="spark-card-header">
                                <h3 class="spark-card-header-title"><i class="fa fa-building-o" style="color: var(--spark-primary);"></i> <?= lang('warehouse_quantity'); ?></h3>
                            </div>
                            <div class="spark-card-body spark-card-flush" style="padding:0;">
                                <div class="table-responsive" style="margin-bottom:0; border:none;">
                                    <table class="table spark-table dfTable one-columns">
                                        <thead>
                                        <tr>
                                            <th><?= lang('warehouse_name') ?></th>
                                            <?php if ($product->type != 'combo') { ?><th><?= lang('quantity') . ' (' . lang('rack') . ')'; ?></th><?php } ?>
                                            <th><?= lang('avg_cost') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($warehouses as $warehouse) {
                                            if ($product->type != 'combo') {
                                                if ($warehouse->quantity != 0) {
                                                    $avg_cost = ($warehouse->quantity > 0) ? $this->sma->formatDecimal($warehouse->avg_cost, 4) : 0;
                                                    echo '<tr><td>' . $warehouse->name . ' (' . $warehouse->code . ')</td><td><strong>' . $this->sma->formatQuantity($warehouse->quantity) . '</strong>' . ($warehouse->rack ? ' (' . $warehouse->rack . ')' : '') . '</td><td>' . $avg_cost . '</td></tr>';
                                                }
                                            } else {
                                                $avg_cost = 0;
                                                foreach ($combo_items_avg_cost as $combo_item_avg_cost) {
                                                    if ($combo_item_avg_cost->warehouse_id == $warehouse->id)
                                                        $avg_cost = $this->sma->formatDecimal($combo_item_avg_cost->totalcost, 4);
                                                }
                                                echo '<tr><td>' . $warehouse->name . ' (' . $warehouse->code . ')</td><td>' . $avg_cost . '</td></tr>';
                                            }
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Section 5: Price Groups Table -->
                    <?php if (!empty($product_price)) { ?>
                        <div class="spark-card">
                            <div class="spark-card-header">
                                <h3 class="spark-card-header-title"><i class="fa fa-tags" style="color: var(--spark-primary);"></i> <?= lang('price_groups'); ?></h3>
                            </div>
                            <div class="spark-card-body spark-card-flush" style="padding:0;">
                                <div class="table-responsive" style="margin-bottom:0; border:none;">
                                    <table class="table spark-table dfTable">
                                        <thead>
                                        <tr>
                                            <th><?= lang('name') ?></th>
                                            <th><?= lang('unit'); ?></th>
                                            <th class="text-center"><?= lang('price'); ?></th>
                                            <th class="text-center"><?= lang('barcode'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($product_price as $pr) {
                                            echo '<tr><td>' . $pr->price_groups_name . '</td><td>' . $pr->unit_name . '</td><td class="text-center">' . $this->sma->formatDecimal($pr->product_price) . '</td><td class="text-center">' . $pr->product_code . '</td></tr>';
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Section 6: Product Variants Quantity Table -->
                    <?php if (!empty($options)) { ?>
                        <div class="spark-card">
                            <div class="spark-card-header">
                                <h3 class="spark-card-header-title"><i class="fa fa-th" style="color: var(--spark-primary);"></i> <?= lang('product_variants_quantity'); ?></h3>
                            </div>
                            <div class="spark-card-body spark-card-flush" style="padding:0;">
                                <div class="table-responsive" style="margin-bottom:0; border:none;">
                                    <table class="table spark-table dfTable">
                                        <thead>
                                        <tr>
                                            <th><?= lang('warehouse_name') ?></th>
                                            <th><?= lang('product_variant'); ?></th>
                                            <th><?= lang('quantity') . ' (' . lang('rack') . ')'; ?></th>
                                            <?php if ($Owner || $Admin) {
                                                echo '<th>' . lang('avg_cost') . '</th>';
                                                echo '<th>' . lang('price_addition') . '</th>';
                                            } ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($options as $option) {
                                            if ($option->wh_qty != 0) {
                                                echo '<tr><td>' . $option->wh_name . '</td><td>' . $option->name . '</td><td class="text-center">' . $this->sma->formatQuantity($option->wh_qty) . '</td>';
                                                if ($Owner || $Admin && (!$Customer || $this->session->userdata('show_cost'))) {
                                                    echo '<td class="text-right">' . $this->sma->formatMoney($option->wh_avg_cost) . '</td><td class="text-right">' . $this->sma->formatMoney($option->price) . '</td>';
                                                }
                                                echo '</tr>';
                                            }
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Section 7: Custom Fields Table -->
                    <?php if ($product->cf1 || $product->cf2 || $product->cf3 || $product->cf4 || $product->cf5 || $product->cf6) { ?>
                        <div class="spark-card">
                            <div class="spark-card-header">
                                <h3 class="spark-card-header-title"><i class="fa fa-list-alt" style="color: var(--spark-primary);"></i> <?= lang('custom_fields') ?></h3>
                            </div>
                            <div class="spark-card-body spark-card-flush" style="padding:0;">
                                <div class="table-responsive" style="margin-bottom:0; border:none;">
                                    <table class="table spark-table dfTable two-columns">
                                        <thead>
                                        <tr>
                                            <th><?= lang('custom_field') ?></th>
                                            <th><?= lang('value') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if ($product->cf1) echo '<tr><td>' . lang('pcf1') . '</td><td>' . $product->cf1 . '</td></tr>';
                                        if ($product->cf2) echo '<tr><td>' . lang('pcf2') . '</td><td>' . $product->cf2 . '</td></tr>';
                                        if ($product->cf3) echo '<tr><td>' . lang('pcf3') . '</td><td>' . $product->cf3 . '</td></tr>';
                                        if ($product->cf4) echo '<tr><td>' . lang('pcf4') . '</td><td>' . $product->cf4 . '</td></tr>';
                                        if ($product->cf5) echo '<tr><td>' . lang('pcf5') . '</td><td>' . $product->cf5 . '</td></tr>';
                                        if ($product->cf6) echo '<tr><td>' . lang('pcf6') . '</td><td>' . $product->cf6 . '</td></tr>';
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Section 8: Combo Products Table -->
                    <?php if ($product->type == 'combo') { ?>
                        <div class="spark-card">
                            <div class="spark-card-header">
                                <h3 class="spark-card-header-title"><i class="fa fa-cubes" style="color: var(--spark-primary);"></i> <?= lang('combo_products') ?></h3>
                            </div>
                            <div class="spark-card-body spark-card-flush" style="padding:0;">
                                <div class="table-responsive" style="margin-bottom:0; border:none;">
                                    <table class="table spark-table dfTable one-columns">
                                        <thead>
                                        <tr>
                                            <th><?= lang('product_name') ?></th>
                                            <th><?= lang('quantity') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($combo_items as $combo_item) {
                                            echo '<tr><td>' . $combo_item->name . ' (' . $combo_item->code . ') </td><td>' . $this->sma->formatQuantity($combo_item->qty) . '</td></tr>';
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Section 9: Invoice & Product Details (Full Width) -->
                <?php if ($product->details || $product->product_details) { ?>
                    <div class="spark-details-full">
                        <?php if ($product->details) { ?>
                            <div class="spark-card">
                                <div class="spark-card-header">
                                    <h3 class="spark-card-header-title"><i class="fa fa-file-text-o" style="color: var(--spark-primary);"></i> <?= lang('product_details_for_invoice'); ?></h3>
                                </div>
                                <div class="spark-card-body">
                                    <?= $product->details; ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($product->product_details) { ?>
                            <div class="spark-card">
                                <div class="spark-card-header">
                                    <h3 class="spark-card-header-title"><i class="fa fa-info-circle" style="color: var(--spark-primary);"></i> <?= lang('product_details'); ?></h3>
                                </div>
                                <div class="spark-card-body">
                                    <?= $product->product_details; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('.tip').tooltip();
                });
            </script>
        </div>
    <div id="chart" class="tab-pane fade">
        <script src="<?= $assets; ?>js/hc/highcharts.js"></script>
        <script type="text/javascript">
            $(function () {
                Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
                    return {
                        radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
                        stops: [[0, color], [1, Highcharts.Color(color).brighten(-0.3).get('rgb')]]
                    };
                });
                <?php if ($sold) { ?>
                var sold_chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'soldchart',
                        type: 'line'
                    },
                    credits: {enabled: false},
                    title: {text: ''},
                    xAxis: {
                        categories: [<?php
                    foreach ($sold as $r) {
                        $month = explode('-', $r->month);
                        echo "'" . lang('cal_' . strtolower($month[1])) . ' ' . $month[0] . "', ";
                    } ?>]
                    },
                    yAxis: {min: 0, title: ""},
                    legend: {enabled: false},
                    tooltip: {
                        shared: true,
                        followPointer: true,
                        formatter: function () {
                            var s = '<div class="well well-sm hc-tip" style="margin-bottom:0;min-width:150px;"><h2 style="margin-top:0;">' + this.x + '</h2><table class="table table-striped"  style="margin-bottom:0;">';
                            $.each(this.points, function () {
                                if (this.series.name == '<?= lang('amount'); ?>') {
                                    s += '<tr><td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' +
                                    currencyFormat(this.y) + '</b></td></tr>';
                                } else {
                                    s += '<tr><td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' +
                                    formatQuantity(this.y) + '</b></td></tr>';
                                }
                            });
                            s += '</table></div>';
                            return s;
                        },
                        useHTML: true, borderWidth: 0, shadow: false, valueDecimals: site.settings.decimals,
                        style: {fontSize: '14px', padding: '0', color: '#000000'}
                    },
                    series: [{
                        type: 'spline',
                        name: '<?= lang('sold'); ?>',
                        data: [<?php
                        foreach ($sold as $r) {
                            $month = explode('-', $r->month);
                            echo "['" . lang('cal_' . strtolower($month[1])) . ' ' . $month[0] . "', " . $r->sold . '],';
                        } ?>]
                    }, {
                        type: 'spline',
                        name: '<?= lang('amount'); ?>',
                        data: [<?php
                        foreach ($sold as $r) {
                            $month = explode('-', $r->month);
                            echo "['" . lang('cal_' . strtolower($month[1])) . ' ' . $month[0] . "', " . $r->amount . '],';
                        } ?>]
                    }]
                });
                $(window).resize(function () {
                    if (sold_chart) sold_chart.setSize($('#soldchart').width(), 450);
                });
                <?php } ?>

                <?php if ($purchased) { ?>
                var purchased_chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'purchasedchart',
                        type: 'line'
                    },
                    credits: {enabled: false},
                    title: {text: ''},
                    xAxis: {
                        categories: [<?php
                        foreach ($purchased as $r) {
                            $month = explode('-', $r->month);
                            echo "'" . lang('cal_' . strtolower($month[1])) . ' ' . $month[0] . "', ";
                        } ?>]
                    },
                    yAxis: {min: 0, title: ""},
                    legend: {enabled: false},
                    tooltip: {
                        shared: true,
                        followPointer: true,
                        formatter: function () {
                            var s = '<div class="well well-sm hc-tip" style="margin-bottom:0;min-width:150px;"><h2 style="margin-top:0;">' + this.x + '</h2><table class="table table-striped"  style="margin-bottom:0;">';
                            $.each(this.points, function () {
                                if (this.series.name == '<?= lang('amount'); ?>') {
                                    s += '<tr><td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' +
                                    currencyFormat(this.y) + '</b></td></tr>';
                                } else {
                                    s += '<tr><td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' +
                                    formatQuantity(this.y) + '</b></td></tr>';
                                }
                            });
                            s += '</table></div>';
                            return s;
                        },
                        useHTML: true, borderWidth: 0, shadow: false, valueDecimals: site.settings.decimals,
                        style: {fontSize: '14px', padding: '0', color: '#000000'}
                    },
                    series: [{
                        type: 'spline',
                        name: '<?= lang('purchased'); ?>',
                        data: [<?php
                        foreach ($purchased as $r) {
                            $month = explode('-', $r->month);
                            echo "['" . lang('cal_' . strtolower($month[1])) . ' ' . $month[0] . "', " . $r->purchased . '],';
                        } ?>]
                    }, {
                        type: 'spline',
                        name: '<?= lang('amount'); ?>',
                        data: [<?php
                        foreach ($purchased as $r) {
                            $month = explode('-', $r->month);
                            echo "['" . lang('cal_' . strtolower($month[1])) . ' ' . $month[0] . "', " . $r->amount . '],';
                        } ?>]
                    }]
                });
                $(window).resize(function () {
                    if (purchased_chart) purchased_chart.setSize($('#purchasedchart').width(), 450);
                });
                <?php } ?>

                $('a[href="#chart"]').on('shown.bs.tab', function () {
                    if (typeof sold_chart !== 'undefined' && sold_chart) sold_chart.reflow();
                    if (typeof purchased_chart !== 'undefined' && purchased_chart) purchased_chart.reflow();
                });
            });
        </script>
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-bar-chart-o nb"></i><?= lang('chart'); ?></h2>
            </div>
            <div class="box-content">
                <div class="row">
                    <?php if ($sold) { ?>
                        <div class="col-sm-<?= $purchased ? '6' : '12'; ?>">
                            <div class="spark-card" style="border: 1px solid var(--spark-border-default, #e2e8f0); margin-bottom: 15px;">
                                <div class="spark-card-header">
                                    <h3 class="spark-card-header-title"><i class="fa fa-bar-chart-o" style="color: var(--spark-primary);"></i> <?= lang('sold'); ?></h3>
                                </div>
                                <div class="spark-card-body">
                                    <div id="soldchart" style="width:100%; height:450px;"></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($purchased) { ?>
                        <div class="col-sm-<?= $sold ? '6' : '12'; ?>">
                            <div class="spark-card" style="border: 1px solid var(--spark-border-default, #e2e8f0); margin-bottom: 15px;">
                                <div class="spark-card-header">
                                    <h3 class="spark-card-header-title"><i class="fa fa-bar-chart-o" style="color: var(--spark-primary);"></i> <?= lang('purchased'); ?></h3>
                                </div>
                                <div class="spark-card-body">
                                    <div id="purchasedchart" style="width:100%; height:450px;"></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div id="sales" class="tab-pane fade">
        <?php $warehouse_id = null; ?>
		<?php

$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

//if ($this->input->post('product')) {
    $v .= '&product=' . $product->id;
//}
if ($this->input->post('reference_no')) {
    $v .= '&reference_no=' . $this->input->post('reference_no');
}
if ($this->input->post('customer')) {
    $v .= '&customer=' . $this->input->post('customer');
}
if ($this->input->post('biller')) {
    $v .= '&biller=' . $this->input->post('biller');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('user')) {
    $v .= '&user=' . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= '&serial=' . $this->input->post('serial');
}

if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>
        <script type="text/javascript">
            $(document).ready(function () {
                oTable = $('#SlRData').dataTable({
                    "aaSorting": [[0, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?= admin_url('reports/getSalesReportProducts/?v=1' . $v) ?>',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>"
                        });
                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    },
                    'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                        nRow.id = aData[11]+ '_<?= $pid; ?>';


                        nRow.className = (aData[5] > 0) ? "" : "warning";
                        return nRow;
                    },
                    "aoColumns": [{"mRender": fld}, null, null, null, null,  {"mRender": formatQuantity2},null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                        var qty = 0, gtotal = 0, paid = 0, balance = 0, tax = 0, subtotal = 0;
                        for (var i = 0; i < aaData.length; i++) {
                            qty += parseFloat(aaData[aiDisplay[i]][5]);
                            //gtotal += parseFloat(aaData[aiDisplay[i]][5]);
                            paid += parseFloat(aaData[aiDisplay[i]][7]);
                            balance += parseFloat(aaData[aiDisplay[i]][8]);
                            tax += parseFloat(aaData[aiDisplay[i]][9]);
                            subtotal += parseFloat(aaData[aiDisplay[i]][10]);
                        }
                        var nCells = nRow.getElementsByTagName('th');
                        nCells[5].innerHTML = formatQuantity(parseFloat(qty));
                        //nCells[5].innerHTML = currencyFormat(parseFloat(gtotal));
                        //nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                        nCells[8].innerHTML = currencyFormat(parseFloat(balance));
                        nCells[9].innerHTML = currencyFormat(parseFloat(tax));
                        nCells[10].innerHTML = currencyFormat(parseFloat(subtotal));
                    }
                }).fnSetFilteringDelay().dtFilter([
                    {column_number: 0, filter_default_label: "[<?=lang('date'); ?> (yyyy-mm-dd)]", filter_type: "text", data: []},
                    {column_number: 1, filter_default_label: "[<?=lang('reference_no'); ?>]", filter_type: "text", data: []},
                    {column_number: 2, filter_default_label: "[<?=lang('warehouse'); ?>]", filter_type: "text", data: []},
                    {column_number: 3, filter_default_label: "[<?=lang('biller'); ?>]", filter_type: "text", data: []},
                    {column_number: 4, filter_default_label: "[<?=lang('customer'); ?>]", filter_type: "text", data: []},
                    //{column_number: 5, filter_default_label: "[<?=lang('product_qty'); ?>]", filter_type: "text", data: []},
                    {column_number: 6, filter_default_label: "[<?=lang('unit'); ?>]", filter_type: "text", data: []},
                    {column_number: 7, filter_default_label: "[<?=lang('Unit_Price'); ?>]", filter_type: "text", data: []},
                ], "footer");
            });
        </script>
		<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= $product->name . ' ' . lang('sales'); ?><?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            }
            ?></h2>


                <div class="box-icon">
                    <ul class="btn-tasks">
                        <li class="dropdown">
                            <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                                <i class="icon fa fa-toggle-up"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                                <i class="icon fa fa-toggle-down"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="print" class="tip" title="<?= lang('print') ?>">
                                <i class="icon fa fa-print"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="introtext"><?php echo lang('list_results'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('products/view/' . $product->id.'#sales'); ?>
                    <div class="row">

						<input type="hidden" name="product" value="<?= $product->id ?>" id="report_product_id"/>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang('reference_no'); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>

                        <!--<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang('created_by'); ?></label>
                                <?php
                                $us[''] = lang('select') . ' ' . lang('user');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                                ?>
                            </div>
                        </div>-->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang('customer'); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = lang('select') . ' ' . lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($all_warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                        <div class="table-responsive">
                            <table id="SlRData" class="table table-striped jambo_table bulk_action table-condensed">
                                <thead>
                                <tr>
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('warehouse'); ?></th>
                                    <th><?= lang('biller'); ?></th>
                                    <th><?= lang('customer'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th><?= lang('Unit_Price'); ?></th>
                                    <th><?= lang('Discount'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
									<th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="11"
                                        class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                                </tbody>
                                <tfoot class="dtFilter">
                                <tr class="active">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= lang('Discount'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
                                    <th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="quotes" class="tab-pane fade">
			<?php

$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

//if ($this->input->post('product')) {
    $v .= '&product=' . $product->id;
//}
if ($this->input->post('reference_no')) {
    $v .= '&reference_no=' . $this->input->post('reference_no');
}
if ($this->input->post('customer')) {
    $v .= '&customer=' . $this->input->post('customer');
}
if ($this->input->post('biller')) {
    $v .= '&biller=' . $this->input->post('biller');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('user')) {
    $v .= '&user=' . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= '&serial=' . $this->input->post('serial');
}

if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>

        <script type="text/javascript">
            $(document).ready(function () {
                oTable = $('#QuRData').dataTable({
                    "aaSorting": [[0, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?= admin_url('reports/getQuotesReportProducts/?v=1' . $v) ?>',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>"
                        });
                        $.ajax({ 'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback });
                    },
                    'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                        var oSettings = oTable.fnSettings();
                        nRow.id = aData[11];
                         nRow.className = (aData[5] > 0) ? "" : "warning";
                        return nRow;
                    },
                    "aoColumns": [{"mRender": fld}, null, null, null, null,  {"mRender": formatQuantity2},null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                        var qty = 0, gtotal = 0, paid = 0, balance = 0, tax = 0, subtotal = 0;
                        for (var i = 0; i < aaData.length; i++) {
                            qty += parseFloat(aaData[aiDisplay[i]][5]);
                            //gtotal += parseFloat(aaData[aiDisplay[i]][5]);
                            paid += parseFloat(aaData[aiDisplay[i]][7]);
                            balance += parseFloat(aaData[aiDisplay[i]][8]);
                            tax += parseFloat(aaData[aiDisplay[i]][9]);
                            subtotal += parseFloat(aaData[aiDisplay[i]][10]);
                        }
                        var nCells = nRow.getElementsByTagName('th');
                        nCells[5].innerHTML = formatQuantity(parseFloat(qty));
                        //nCells[5].innerHTML = currencyFormat(parseFloat(gtotal));
                        //nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                        nCells[8].innerHTML = currencyFormat(parseFloat(balance));
                        nCells[9].innerHTML = currencyFormat(parseFloat(tax));
                        nCells[10].innerHTML = currencyFormat(parseFloat(subtotal));
                    }
                }).fnSetFilteringDelay().dtFilter([
                    {column_number: 0, filter_default_label: "[<?=lang('date'); ?> (yyyy-mm-dd)]", filter_type: "text", data: []},
                    {column_number: 1, filter_default_label: "[<?=lang('reference_no'); ?>]", filter_type: "text", data: []},
                    {column_number: 2, filter_default_label: "[<?=lang('warehouse'); ?>]", filter_type: "text", data: []},
                    {column_number: 3, filter_default_label: "[<?=lang('biller'); ?>]", filter_type: "text", data: []},
                    {column_number: 4, filter_default_label: "[<?=lang('customer'); ?>]", filter_type: "text", data: []},
                    //{column_number: 5, filter_default_label: "[<?=lang('product_qty'); ?>]", filter_type: "text", data: []},
                    {column_number: 6, filter_default_label: "[<?=lang('unit'); ?>]", filter_type: "text", data: []},
                    {column_number: 7, filter_default_label: "[<?=lang('Unit_Price'); ?>]", filter_type: "text", data: []},
                ], "footer");
            });
        </script>
				<script type="text/javascript">
    $(document).ready(function () {
        $('#form1').hide();
        $('.toggle_down1').click(function () {
            $("#form1").slideDown();
            return false;
        });
        $('.toggle_up1').click(function () {
            $("#form1").slideUp();
            return false;
        });
    });
</script>
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= $product->name . ' ' . lang('quotes'); ?><?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            }
            ?></h2>


                <div class="box-icon">
                    <ul class="btn-tasks">
                        <li class="dropdown">
                            <a href="#" class="toggle_up1 tip" title="<?= lang('hide_form') ?>">
                                <i class="icon fa fa-toggle-up"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="toggle_down1 tip" title="<?= lang('show_form') ?>">
                                <i class="icon fa fa-toggle-down"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="xls1" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="print1" class="tip" title="<?= lang('print') ?>">
                                <i class="icon fa fa-print"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="introtext"><?php echo lang('list_results'); ?></p>

                <div id="form1">

                    <?php echo admin_form_open('products/view/' . $product->id.'#quotes'); ?>
                    <div class="row">

						<input type="hidden" name="product" value="<?= $product->id ?>" id="report_product_id"/>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang('reference_no'); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>

                        <!--<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang('created_by'); ?></label>
                                <?php
                                $us[''] = lang('select') . ' ' . lang('user');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                                ?>
                            </div>
                        </div>-->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang('customer'); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ''), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('customer') . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = lang('select') . ' ' . lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($all_warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                        <div class="table-responsive">
                            <table id="QuRData" class="table table-striped jambo_table bulk_action table-condensed">
                                <thead>
                                <tr>
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('warehouse'); ?></th>
                                    <th><?= lang('biller'); ?></th>
                                    <th><?= lang('customer'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th><?= lang('Unit_Price'); ?></th>
                                    <th><?= lang('Discount'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
									<th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="11"
                                        class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                                </tbody>
                                <tfoot class="dtFilter">
                                <tr class="active">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= lang('Discount'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
                                    <th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="purchases" class="tab-pane fade">

			<?php

$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

//if ($this->input->post('product')) {
    $v .= '&product=' . $product->id;
//}
if ($this->input->post('reference_no')) {
    $v .= '&reference_no=' . $this->input->post('reference_no');
}
if ($this->input->post('supplier')) {
    $v .= '&supplier=' . $this->input->post('supplier');
}

if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('user')) {
    $v .= '&user=' . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= '&serial=' . $this->input->post('serial');
}

if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>
        <script type="text/javascript">
            $(document).ready(function () {
                oTable = $('#PoRData').dataTable({
                    "aaSorting": [[0, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?= admin_url('reports/getPurchasesReport/?v=1' . $v) ?>',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>"
                        });
                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    },
                    'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                        nRow.id = aData[9];
                        nRow.className = (aData[4] > 0) ? "" : " warning";
                        return nRow;
                    },
                    "aoColumns": [{"mRender": fld}, null, null, null,  {"mRender": formatQuantity2},null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
						var qty = 0, gtotal = 0, paid = 0, balance = 0, tax = 0, subtotal = 0;
                        for (var i = 0; i < aaData.length; i++) {
                            qty += parseFloat(aaData[aiDisplay[i]][4]);
                            //gtotal += parseFloat(aaData[aiDisplay[i]][5]);
                            paid += parseFloat(aaData[aiDisplay[i]][6]);
                            balance += parseFloat(aaData[aiDisplay[i]][7]);
                            tax += parseFloat(aaData[aiDisplay[i]][8]);
                            subtotal += parseFloat(aaData[aiDisplay[i]][9]);
                        }
                        var nCells = nRow.getElementsByTagName('th');
                        nCells[4].innerHTML = formatQuantity(parseFloat(qty));
                        //nCells[5].innerHTML = currencyFormat(parseFloat(gtotal));
                        //nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                        nCells[7].innerHTML = currencyFormat(parseFloat(balance));
                        nCells[8].innerHTML = currencyFormat(parseFloat(tax));
                        nCells[9].innerHTML = currencyFormat(parseFloat(subtotal));
                    }
                }).fnSetFilteringDelay().dtFilter([
                    {column_number: 0, filter_default_label: "[<?=lang('date'); ?> (yyyy-mm-dd)]", filter_type: "text", data: []},
                    {column_number: 1, filter_default_label: "[<?=lang('reference_no'); ?>]", filter_type: "text", data: []},
                    {column_number: 2, filter_default_label: "[<?=lang('warehouse'); ?>]", filter_type: "text", data: []},
                    {column_number: 3, filter_default_label: "[<?=lang('supplier'); ?>]", filter_type: "text", data: []},
                    //{column_number: 4, filter_default_label: "[<?=lang('product_qty'); ?>]", filter_type: "text", data: []},
                    {column_number: 5, filter_default_label: "[<?=lang('unit'); ?>]", filter_type: "text", data: []},
                    {column_number: 6, filter_default_label: "[<?=lang('Unit_Price'); ?>]", filter_type: "text", data: []},
                ], "footer");
            });

        </script>
				<script type="text/javascript">
    $(document).ready(function () {
        $('#form2').hide();
        $('.toggle_down2').click(function () {
            $("#form2").slideDown();
            return false;
        });
        $('.toggle_up2').click(function () {
            $("#form2").slideUp();
            return false;
        });
    });
    </script>
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-star nb"></i><?= $product->name . ' ' . lang('purchases'); ?><?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            }
            ?>
                </h2>

                <div class="box-icon">
                    <ul class="btn-tasks">
                        <li class="dropdown">
                            <a href="#" class="toggle_up2 tip" title="<?= lang('hide_form') ?>">
                                <i class="icon fa fa-toggle-up"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="toggle_down2 tip" title="<?= lang('show_form') ?>">
                                <i class="icon fa fa-toggle-down"></i>
                            </a>
                        </li>
                        <!--<li class="dropdown">
                            <a href="#" id="pdf2" class="tip" title="<?= lang('download_pdf') ?>">
                                <i class="icon fa fa-file-pdf-o"></i>
                            </a>
                        </li>-->
                        <li class="dropdown">
                            <a href="#" id="xls2" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="print2" class="tip" title="<?= lang('print') ?>">
                                <i class="icon fa fa-print"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="introtext"><?php echo lang('list_results'); ?></p>

						<div id="form2">

                    <?php echo admin_form_open('products/view/' . $product->id.'#purchases'); ?>
                    <div class="row">

						<input type="hidden" name="product" value="<?= $product->id ?>" id="report_product_id"/>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang('reference_no'); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ''), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>

                        <!--<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang('created_by'); ?></label>
                                <?php
                                $us[''] = lang('select') . ' ' . lang('user');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                                ?>
                            </div>
                        </div>-->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="supplier"><?= lang('supplier'); ?></label>
                                <?php echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control" id="supplier" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('supplier') . '"'); ?>
                            </div>
                        </div>
                        <!--<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang('biller'); ?></label>
                                <?php
                                $bl[''] = lang('select') . ' ' . lang('biller');
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company && $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('biller') . '"');
                                ?>
                            </div>
                        </div>-->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($all_warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                        <div class="table-responsive">
                            <table id="PoRData" class="table table-striped jambo_table bulk_action table-condensed">
                                <thead>
                                <tr>
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('warehouse'); ?></th>
                                    <th><?= lang('supplier'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th><?= lang('Unit_Price'); ?></th>
                                    <th><?= lang('Discount'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
									<th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="10"
                                        class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                                </tbody>
                                <tfoot class="dtFilter">
                                <tr class="active">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= lang('Discount'); ?></th>
                                    <th><?= lang('VAT_Amount'); ?></th>
                                    <th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="transfers" class="tab-pane fade">
        <script type="text/javascript">
            $(document).ready(function () {
                oTable = $('#TrRData').dataTable({
                    "aaSorting": [[0, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?= admin_url('reports/getTransfersReport/?v=1&product=' . $product->id) ?>',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>"
                        });
                        $.ajax({ 'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback });
                    },
                    'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                        var oSettings = oTable.fnSettings();
                        nRow.id = aData[8];
                        //nRow.className = "transfer_link2";
                        return nRow;
                    },
                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                        var gtotal = 0,cost = 0;
                        for (var i = 0; i < aaData.length; i++) {
                            gtotal += parseFloat(aaData[aiDisplay[i]][7]);;
                            cost += parseFloat(aaData[aiDisplay[i]][6]);;
                        }
                        var nCells = nRow.getElementsByTagName('th');
                        //nCells[6].innerHTML = currencyFormat(formatMoney(cost));
                        nCells[7].innerHTML = currencyFormat(formatMoney(gtotal));
                    },
                    "aoColumns": [{"mRender": fld}, null,
                        {"mRender": formatQuantity2}
                    , null, null,null, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
                }).fnSetFilteringDelay().dtFilter([
                    {column_number: 0, filter_default_label: "[<?=lang('date'); ?> (yyyy-mm-dd)]", filter_type: "text", data: []},
                    {column_number: 1, filter_default_label: "[<?=lang('reference_no'); ?>]", filter_type: "text", data: []},
                    {
                        column_number: 4,
                        filter_default_label: "[<?=lang('warehouse') . ' (' . lang('from') . ')'; ?>]",
                        filter_type: "text", data: []
                    },
                    {
                        column_number: 5,
                        filter_default_label: "[<?=lang('warehouse') . ' (' . lang('to') . ')'; ?>]",
                        filter_type: "text", data: []
                    },
                    {column_number: 6, filter_default_label: "[<?=lang('unit_cost'); ?>]", filter_type: "text", data: []},
                    //{column_number: 6, filter_default_label: "[<?=lang('status'); ?>]", filter_type: "text", data: []},
                ], "footer");
            });
        </script>
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-star-o nb"></i><?= $product->name . ' ' . lang('transfers'); ?>
                </h2>

                <div class="box-icon">
                    <ul class="btn-tasks">
                        <li class="dropdown">
                            <a href="#" id="xls3" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="print3" class="tip" title="<?= lang('print') ?>">
                                <i class="icon fa fa-print"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="introtext"><?php echo lang('list_results'); ?></p>

                        <div class="table-responsive">
                            <table id="TrRData" class="table table-striped jambo_table bulk_action table-condensed">
                                <thead>
                                <tr>
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th><?= lang('warehouse') . ' (' . lang('from') . ')'; ?></th>
                                    <th><?= lang('warehouse') . ' (' . lang('to') . ')'; ?></th>
                                    <th><?= lang('unit_cost'); ?></th>
                                    <th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="7"
                                        class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                                </tbody>
                                <tfoot class="dtFilter">
                                <tr class="active">
                                    <th></th>
                                    <th></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="quantity_adjustments" class="tab-pane fade">

				<?php

$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

//if ($this->input->post('product')) {
    $v .= '&product=' . $product->id;
//}

if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}


if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>

        <script>
            $(document).ready(function () {
                oTable = $('#dmpData').dataTable({
                    "aaSorting": [[0, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?= admin_url('reports/getAdjustmentReport/?v=1' . $v) ?>',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>"
                        });
                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    },
					'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                        nRow.id = aData[7];
                        //nRow.className = "adjustment_link2";
                        return nRow;
                    },
                    "aoColumns": [{"mRender": fld}, null, null, {"mRender": formatQuantity2}, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
                   "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
						var gtotal = 0, cost = 0, qty = 0;
                        for (var i = 0; i < aaData.length; i++) {
                            qty += parseFloat(aaData[aiDisplay[i]][3]);
                            cost += parseFloat(aaData[aiDisplay[i]][5]);
                            gtotal += parseFloat(aaData[aiDisplay[i]][6]);
                        }
                        var nCells = nRow.getElementsByTagName('th');
                        nCells[3].innerHTML = formatQuantity(parseFloat(qty));
                        //nCells[5].innerHTML = currencyFormat(parseFloat(gtotal));
                        //nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                        //nCells[5].innerHTML = currencyFormat(parseFloat(cost));
                        nCells[6].innerHTML = currencyFormat(parseFloat(gtotal));
                    }
                }).fnSetFilteringDelay().dtFilter([
                    {column_number: 0, filter_default_label: "[<?=lang('date'); ?> (yyyy-mm-dd)]", filter_type: "text", data: []},
                    {column_number: 1, filter_default_label: "[<?=lang('reference_no'); ?>]", filter_type: "text", data: []},
                    {column_number: 2, filter_default_label: "[<?=lang('warehouse'); ?>]", filter_type: "text", data: []},
                    {column_number: 5, filter_default_label: "[<?=lang('unit_cost'); ?>]", filter_type: "text", data: []},
                    //{column_number: 4, filter_default_label: "[<?=lang(' note'); ?>]", filter_type: "text", data: []},
                ], "footer");
            });
        </script>
						<script type="text/javascript">
    $(document).ready(function () {
        $('#form5').hide();
        $('.toggle_down5').click(function () {
            $("#form5").slideDown();
            return false;
        });
        $('.toggle_up5').click(function () {
            $("#form5").slideUp();
            return false;
        });
    });
</script>
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= $product->name . ' ' . lang('adjustments_report'); ?><?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            }
            ?></h2>


                <div class="box-icon">
                    <ul class="btn-tasks">
                        <li class="dropdown">
                            <a href="#" class="toggle_up5 tip" title="<?= lang('hide_form') ?>">
                                <i class="icon fa fa-toggle-up"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="toggle_down5 tip" title="<?= lang('show_form') ?>">
                                <i class="icon fa fa-toggle-down"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="xls5" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="print5" class="tip" title="<?= lang('print') ?>">
                                <i class="icon fa fa-print"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="introtext"><?php echo lang('list_results'); ?></p>

                <div id="form5">

                    <?php echo admin_form_open('products/view/' . $product->id.'#quantity_adjustments'); ?>
                    <div class="row">

						<input type="hidden" name="product" value="<?= $product->id ?>" id="report_product_id"/>


                        <!--<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang('created_by'); ?></label>
                                <?php
                                $us[''] = lang('select') . ' ' . lang('user');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                                ?>
                            </div>
                        </div>-->

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($all_warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>



                        <div class="table-responsive">
                            <table id="dmpData" class="table  table-condensed table-hover table-striped">
                                <thead>
                                <tr>
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('warehouse'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th><?= lang('unit_cost'); ?></th>
                                    <th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="7"
                                        class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                                </tbody>
                                <tfoot class="dtFilter">
                                <tr class="active">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th></th>
                                    <th></th>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="destruction_inventory" class="tab-pane fade">
				<?php
$v = '';
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */

//if ($this->input->post('product')) {
    $v .= '&product=' . $product->id;
//}

if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}


if ($this->input->post('start_date')) {
//    $v .= '&start_date=' . substr($this->input->post('start_date'),0,10);
    $v .= '&start_date=' . $this->input->post('start_date');
}//else
	//$v .= '&start_date=' . date('d/m/Y');

if ($this->input->post('end_date')) {
//    $v .= '&end_date=' . substr($this->input->post('end_date'),0,10);
    $v .= '&end_date=' . $this->input->post('end_date');
}//else
	//$v .= '&end_date=' . date('d/m/Y');
//echo $v;
?>

        <script>
            $(document).ready(function () {
                oTable = $('#diData').dataTable({
                    "aaSorting": [[0, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                    "iDisplayLength": <?= $Settings->rows_per_page ?>,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?= admin_url('reports/getDestructionReport/?v=1' . $v) ?>',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?= $this->security->get_csrf_token_name() ?>",
                            "value": "<?= $this->security->get_csrf_hash() ?>"
                        });
                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    },
					'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                        nRow.id = aData[7];
                        //nRow.className = "adjustment_link2";
                        return nRow;
                    },
                    "aoColumns": [{"mRender": fld}, null, null, {"mRender": formatQuantity2}, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}],
                   "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
						var gtotal = 0, cost = 0, qty = 0;
                        for (var i = 0; i < aaData.length; i++) {
                            qty += parseFloat(aaData[aiDisplay[i]][3]);
                            cost += parseFloat(aaData[aiDisplay[i]][5]);
                            gtotal += parseFloat(aaData[aiDisplay[i]][6]);
                        }
                        var nCells = nRow.getElementsByTagName('th');
                        nCells[3].innerHTML = formatQuantity(parseFloat(qty));
                        //nCells[5].innerHTML = currencyFormat(parseFloat(gtotal));
                        //nCells[6].innerHTML = currencyFormat(parseFloat(paid));
                        //nCells[5].innerHTML = currencyFormat(parseFloat(cost));
                        nCells[6].innerHTML = currencyFormat(parseFloat(gtotal));
                    }
                }).fnSetFilteringDelay().dtFilter([
                    {column_number: 0, filter_default_label: "[<?=lang('date'); ?> (yyyy-mm-dd)]", filter_type: "text", data: []},
                    {column_number: 1, filter_default_label: "[<?=lang('reference_no'); ?>]", filter_type: "text", data: []},
                    {column_number: 2, filter_default_label: "[<?=lang('warehouse'); ?>]", filter_type: "text", data: []},
                    {column_number: 5, filter_default_label: "[<?=lang('unit_cost'); ?>]", filter_type: "text", data: []},
                    //{column_number: 4, filter_default_label: "[<?=lang(' note'); ?>]", filter_type: "text", data: []},
                ], "footer");
            });
        </script>
						<script type="text/javascript">
    $(document).ready(function () {
        $('#form6').hide();
        $('.toggle_down6').click(function () {
            $("#form6").slideDown();
            return false;
        });
        $('.toggle_up6').click(function () {
            $("#form6").slideUp();
            return false;
        });
    });
</script>
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= $product->name . ' ' . lang('Inventory_operations_archive'); ?><?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
            }
            ?></h2>


                <div class="box-icon">
                    <ul class="btn-tasks">
                        <li class="dropdown">
                            <a href="#" class="toggle_up6 tip" title="<?= lang('hide_form') ?>">
                                <i class="icon fa fa-toggle-up"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="toggle_down6 tip" title="<?= lang('show_form') ?>">
                                <i class="icon fa fa-toggle-down"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="xls6" class="tip" title="<?= lang('download_xls') ?>">
                                <i class="icon fa fa-file-excel-o"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" id="print6" class="tip" title="<?= lang('print') ?>">
                                <i class="icon fa fa-print"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="introtext"><?php echo lang('list_results'); ?></p>

                <div id="form6">

                    <?php echo admin_form_open('products/view/' . $product->id.'#destruction_inventory'); ?>
                    <div class="row">

						<input type="hidden" name="product" value="<?= $product->id ?>" id="report_product_id"/>


                        <!--<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang('created_by'); ?></label>
                                <?php
                                $us[''] = lang('select') . ' ' . lang('user');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . ' ' . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ''), 'class="form-control" id="user" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('user') . '"');
                                ?>
                            </div>
                        </div>-->

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($all_warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('Start_date', 'Start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('End_date', 'End_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>



                        <div class="table-responsive">
                            <table id="diData" class="table  table-condensed table-hover table-striped">
                                <thead>
                                <tr>
                                    <th><?= lang('date'); ?></th>
                                    <th><?= lang('reference_no'); ?></th>
                                    <th><?= lang('warehouse'); ?></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th><?= lang('unit_cost'); ?></th>
                                    <th><?= lang('Total_Value'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="7"
                                        class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                                </tbody>
                                <tfoot class="dtFilter">
                                <tr class="active">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= lang('product_qty'); ?></th>
                                    <th><?= lang('unit'); ?></th>
                                    <th></th>
                                    <th></th>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /.tab-content -->

</div> <!-- /.spark-product-details -->

    <script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#print').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getSalesReportProducts/print/?v=1' . $v) ?>";
                return false;
            });
            $('#pdf').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getSalesReportProducts/pdf/?v=1' . $v) ?>";
                return false;
            });
            $('#xls').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getSalesReportProducts/0/xls/?v=1' . $v) ?>";
                return false;
            });
            $('#print1').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getQuotesReportProducts/print/?v=1' . $v) ?>";
                return false;
            });
            $('#pdf1').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getQuotesReportProducts/pdf/?v=1' . $v) ?>";
                return false;
            });
            $('#xls1').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getQuotesReportProducts/0/xls/?v=1' . $v) ?>";
                return false;
            });
            $('#pdf2').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getPurchasesReport/pdf/?v=1' . $v) ?>";
                return false;
            });
			$('#print2').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getPurchasesReport/print/?v=1' . $v) ?>";
                return false;
            });

            $('#xls2').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getPurchasesReport/0/xls/?v=1&' . $v) ?>";
                return false;
            });
            $('#print3').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getTransfersReport/print/?v=1&product=' . $product->id)?>";
                return false;
            });
            $('#xls3').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getTransfersReport/0/xls/?v=1&product=' . $product->id)?>";
                return false;
            });
            $('#pdf4').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('products/getadjustments/pdf/?v=1&product=' . $product->id)?>";
                return false;
            });
            $('#xls4').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('products/getadjustments/0/xls/?v=1&product=' . $product->id)?>";
                return false;
            });
            $('#print5').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getAdjustmentReport/print/?v=1' . $v) ?>";
                return false;
            });
            $('#xls5').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getAdjustmentReport/0/xls/?v=1' . $v) ?>";
                return false;
            });
            $('#print6').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getDestructionReport/print/?v=1' . $v) ?>";
                return false;
            });
            $('#xls6').click(function (event) {
                event.preventDefault();
                window.location.href = "<?=admin_url('reports/getDestructionReport/0/xls/?v=1' . $v) ?>";
                return false;
            });
            $('.image').click(function (event) {
                var box = $(this).closest('.box');
                event.preventDefault();
                html2canvas(box, {
                    onrendered: function (canvas) {
                        openImg(canvas.toDataURL());
                    }
                });
                return false;
            });
        });
    </script>

