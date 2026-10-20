<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if ($Owner || $Admin) {
    foreach ($monthly_sales as $month_sale) {
        $months[]    = date('M-Y', strtotime($month_sale->month));
        $sales[]     = $month_sale->sales;
        $tax1[]      = $month_sale->tax1;
        $tax2[]      = $month_sale->tax2;
        $purchases[] = $month_sale->purchases;
        $tax3[]      = $month_sale->ptax;
    } ?>
    <style type="text/css" media="screen">
        .tooltip-inner {
            max-width: 500px;
        }
		
		
	
		
		
    </style>
    <script src="<?= $assets; ?>js/hc/highcharts.js"></script>
    <script type="text/javascript">
        $(function () {
            Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
                return {
                    radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
                    stops: [[0, color], [1, Highcharts.Color(color).brighten(-0.3).get('rgb')]]
                };
            });
            $('#chart').highcharts({
                chart: {},
                credits: {enabled: false},
                title: {text: ''},
                xAxis: {categories: <?= json_encode($months); ?>},
                yAxis: {min: 0, title: ""},
                tooltip: {
                    shared: true,
                    followPointer: true,
                    formatter: function () {
                        if (this.key) {
                            return '<div class="tooltip-inner hc-tip" style="margin-bottom:0;">' + this.key + '<br><strong>' + currencyFormat(this.y) + '</strong> (' + formatNumber(this.percentage) + '%)';
                        } else {
                            var s = '<div class="well well-sm hc-tip" style="margin-bottom:0;"><h2 style="margin-top:0;">' + this.x + '</h2><table class="table table-striped"  style="margin-bottom:0;">';
                            $.each(this.points, function () {
                                s += '<tr><td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' +
                                currencyFormat(this.y) + '</b></td></tr>';
                            });
                            s += '</table></div>';
                            return s;
                        }
                    },
                    useHTML: true,
                    borderWidth: 0,
                    shadow: false,
                    valueDecimals: site.settings.decimals,
                    style: {
                        fontSize: '14px',
                        padding: '0',
                        color: '#000000'
                    }
                },
                series: [{
                    type: 'column',
                    name: '<?= lang('sp_tax'); ?>',
                    data: [<?php
                    echo implode(', ', $tax1); ?>]
                },
                    {
                        type: 'column',
                        name: '<?= lang('order_tax'); ?>',
                        data: [<?php
                    echo implode(', ', $tax2); ?>]
                    },
                    {
                        type: 'column',
                        name: '<?= lang('sales'); ?>',
                        data: [<?php
                    echo implode(', ', $sales); ?>]
                    }, {
                        type: 'spline',
                        name: '<?= lang('purchases'); ?>',
                        data: [<?php
                    echo implode(', ', $purchases); ?>],
                        marker: {
                            lineWidth: 2,
                            states: {
                                hover: {
                                    lineWidth: 4
                                }
                            },
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }, {
                        type: 'spline',
                        name: '<?= lang('pp_tax'); ?>',
                        data: [<?php
                    echo implode(', ', $tax3); ?>],
                        marker: {
                            lineWidth: 2,
                            states: {
                                hover: {
                                    lineWidth: 4
                                }
                            },
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }, {
                        type: 'pie',
                        name: '<?= lang('stock_value'); ?>',
                        data: [
                            ['', 0],
                            ['', 0],
                            ['<?= lang('stock_value_by_price'); ?>', <?php echo $stock->stock_by_price; ?>],
                            ['<?= lang('stock_value_by_cost'); ?>', <?php echo $stock->stock_by_cost; ?>],
                        ],
                        center: [80, 42],
                        size: 80,
                        showInLegend: false,
                        dataLabels: {
                            enabled: false
                        }
                    }]
            });
        });
    </script>
<?php
} ?>
    <div class="box">
        <div class="box-header">
            <h2 class="blue"><i class="fa fa-th"></i><span class="break"></span><?= lang('reports') ?></h2>
        </div>
        <div class="box-content">
            <div class="row">
                <!--<div class="col-md-2 col-xs-4 padding1010">
                    <a class="bblue white quick-button" href="<?= admin_url('products/warehouse_stock') ?>">
                        <i class="fa fa-building"></i>

                        <p><?= lang('warehouse_stock') ?></p>
                    </a>
                </div>
                <div class="col-md-2 col-xs-4 padding1010">
                    <a class="bblue white quick-button" href="<?= admin_url('products/adjustments_report') ?>">
                        <i class="fa fa-bar-chart-o"></i>

                        <p><?= lang('adjustments_report') ?></p>
                    </a>
                </div>
                <div class="col-md-2 col-xs-4 padding1010">
                    <a class="bblue white quick-button" href="<?= admin_url('products/products_report') ?>">
                        <i class="fa fa-barcode"></i>

                        <p><?= lang('products_report') ?></p>
                    </a>
                </div>
				<div class="col-md-2 col-xs-4 padding1010">
                    <a class="bred white quick-button" href="<?= admin_url('products/quantity_alerts') ?>">
                        <i class="fa fa-bar-chart-o"></i>

                        <p><?= lang('product_quantity_alerts') ?></p>
                    </a>
                </div>

                <div class="col-md-2 col-xs-4 padding1010">
                    <a class="bred white quick-button" href="<?= admin_url('products/expiry_alerts') ?>">
                        <i class="fa fa-bar-chart-o"></i>

                        <p><?= lang('product_expiry_alerts') ?></p>
                    </a>
                </div>

                
                <div class="col-md-2 col-xs-4 padding1010">
                    <a class="bdarkGreen white quick-button" href="<?= admin_url('products/warehouse_count') ?>">
                        <i class="fa fa-calendar-o"></i>

                        <p><?= lang('warehouse_count') ?></p>
                    </a>
                </div>

                <div class="col-md-2 col-xs-4 padding1010">
                    <a class="bdarkGreen white quick-button" href="<?= admin_url('products/warehouse_store') ?>">
                        <i class="fa fa-calendar-o"></i>

                        <p class="padding05"><?= lang('warehouse_store') ?></p>
                    </a>
                </div>

                <div class="col-md-2 col-xs-4 padding1010">
                    <a class="bdarkGreen white quick-button" href="<?= admin_url('products/destruction') ?>">
                        <i class="fa fa-calendar-o"></i>

                        <p><?= lang('destruction_archive') ?></p>
                    </a>
                </div>-->
				
				<div class="x_content">
                  <!-- <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/best_sellers') ?>';"><i class="fa fa-building"></i> <span class="padding05"><?= lang('best_sellers') ?></span></button> -->
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/daily_sales') ?>';"><i class="fa fa-bar-chart-o"></i> <span class="padding05"><?= lang('daily_sales') ?></span></button>
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/monthly_sales') ?>';"><i class="fa fa-barcode"></i> <span class="padding05"><?= lang('monthly_sales') ?></span></button>
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/sales_report') ?>';"><i class="fa fa-barcode"></i> <span class="padding05"><?= lang('sales_report') ?></span></button>
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/sales') ?>';"><i class="fa fa-barcode"></i> <span class="padding05"><?= lang('Sales_Transaction_Report') ?></span></button>
                <!--  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/delivery_report') ?>';"><i class="fa fa-bar-chart-o"></i> <span class="padding05"><?= lang('Sales_Delivery_Report') ?></span></button> -->
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/payments_report') ?>';"><i class="fa fa-bar-chart-o"></i> <span class="padding05"><?= lang('payments_report') ?></span></button>
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/customers_report') ?>';"><i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('customers_report') ?></span></button>
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/customers_payments_report') ?>';"><i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('Customers_Payments') ?></span></button>
                <!-- <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/register') ?>';"><i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('register_report') ?></span></button> -->
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/sales_cn_report') ?>';"><i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('Credit_Notes') ?></span></button>
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/commercial_discounts_report') ?>';"><i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('Discount_Allowance') ?></span></button>
                 <!-- <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/products_item_report') ?>';"><i class="fa fa-barcode"></i> <span class="padding05"><?= lang('products_report') ?></span></button> -->
                  <button type="button" style="width:200px;margin:2px;" class="btn btn-primary" onclick="location.href='<?= admin_url('carssales/products_report') ?>';"><i class="fa fa-barcode"></i> <span class="padding05"><?= lang('Invoice_profit_report') ?></span></button>
                  
                </div>
				<!--
				<div class="dropdown hidden-xs">
                        <a class="btn bblue white" title="<?= lang('warehouse_stock') ?>" data-placement="bottom" href="<?= admin_url('products/warehouse_stock') ?>">
                            <i class="fa fa-building"></i> <span class="padding05"><?= lang('warehouse_stock') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bblue white" title="<?= lang('adjustments_report') ?>" data-placement="bottom" href="<?= admin_url('products/adjustments_report') ?>">
                            <i class="fa fa-bar-chart-o"></i> <span class="padding05"><?= lang('adjustments_report') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bblue white" title="<?= lang('products_report') ?>" data-placement="bottom" href="<?= admin_url('products/products_item_report') ?>">
                            <i class="fa fa-barcode"></i> <span class="padding05"><?= lang('products_report') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bblue white" title="<?= lang('products_report_proff_less') ?>" data-placement="bottom" href="<?= admin_url('products/products_report') ?>">
                            <i class="fa fa-barcode"></i> <span class="padding05"><?= lang('products_report_proff_less') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bred white" title="<?= lang('product_quantity_alerts') ?>" data-placement="bottom" href="<?= admin_url('products/quantity_alerts') ?>">
                            <i class="fa fa-bar-chart-o"></i> <span class="padding05"><?= lang('product_quantity_alerts') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bred white" title="<?= lang('product_expiry_alerts') ?>" data-placement="bottom" href="<?= admin_url('products/expiry_alerts') ?>">
                            <i class="fa fa-bar-chart-o"></i> <span class="padding05"><?= lang('product_expiry_alerts') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bdarkGreen white" title="<?= lang('warehouse_count') ?>" data-placement="bottom" href="<?= admin_url('products/warehouse_count') ?>">
                            <i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('warehouse_count') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bdarkGreen white" title="<?= lang('warehouse_store') ?>" data-placement="bottom" href="<?= admin_url('products/warehouse_store') ?>">
                            <i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('warehouse_store') ?></span>
                        </a>
                </div>
                <div class="dropdown hidden-xs">
                        <a class="btn bdarkGreen white" title="<?= lang('destruction_archive') ?>" data-placement="bottom" href="<?= admin_url('products/destruction') ?>">
                            <i class="fa fa-calendar-o"></i> <span class="padding05"><?= lang('destruction_archive') ?></span>
                        </a>
                </div>
				
				-->
                    

                
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

