<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa fa-th"></i><span class="break"></span><?= lang('inventory_and_product_reports') ?: lang('reports'); ?></h2>
    </div>

    <div class="box-content">
        <div class="row">
            <?php
            $reports = [
                ['warehouse_stock', 'Warehouse_stock', 'fa-building', 'icon-blue', 'Warehouse_stock_overview'],
                ['adjustments_report', 'adjustments_report', 'fa-sliders', 'icon-orange', 'inventory_opening_balance'],
                ['product_motion_report', 'Product_motion_report', 'fa-exchange', 'icon-green', 'Product_movement_history'],
                ['products_item_report', 'products_report', 'fa-cubes', 'icon-purple', 'Detailed_products_report'],
                ['products_report', 'products_report_proff_less', 'fa-cube', 'icon-blue', 'Products_summary_report'],
                ['quantity_alerts', 'product_quantity_alerts', 'fa-exclamation-triangle', 'icon-red', 'Low_stock_alerts'],
                ['expiry_alerts', 'product_expiry_alerts', 'fa-clock-o', 'icon-orange', 'Expiry_date_alerts'],
                ['warehouse_count', 'warehouse_count', 'fa-list-alt', 'icon-green', 'Inventory_counting_report'],
                ['warehouse_store', 'warehouse_store', 'fa-home', 'icon-purple', 'warehouse_store'],
                ['destruction', 'Inventory_operations_archive', 'fa-archive', 'icon-red', 'Inventory_operations_archive'],
            ];

            foreach ($reports as $r):
            ?>
            <div class="col-lg-3 col-md-6 col-sm-6 report-item">
                <a href="<?= admin_url('products/' . $r[0]) ?>" class="report-card">
                    <div class="report-flex">
                        <div class="report-icon <?= $r[3] ?>">
                            <i class="fa <?= $r[2] ?>"></i>
                        </div>
                        <div>
                            <div class="report-title">
                                <?= lang($r[1]) ?: $r[1] ?>
                            </div>
                            <div class="report-desc">
                                <?= lang($r[4]) ?: $r[4] ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>