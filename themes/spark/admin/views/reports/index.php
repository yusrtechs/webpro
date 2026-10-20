<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="spark-reports-hub">
    <!-- Reports Hub Header -->
    <div class="spark-reports-header">
        <div class="spark-reports-title-area">
            <div class="spark-reports-header-badge">
                <i class="fa fa-line-chart"></i>
            </div>
            <div>
                <h1 class="spark-reports-page-title"><?= lang('reports'); ?></h1>
                <p class="spark-reports-page-subtitle"><?= lang('all_reports_and_statistics') ?></p>
            </div>
        </div>

        <!-- Actions matching React Header -->
        <div class="spark-reports-actions">
            <a href="<?= admin_url('reports/tax'); ?>" class="spark-btn-reports-primary">
                <i class="fa fa-file-text-o"></i>
                <span><?= lang('tax_report') ?></span>
            </a>
            <?php if (!isset($this->Settings->preAccounting) || $this->Settings->preAccounting != 1) { ?>
                <a href="<?= admin_url('reports/profit_loss'); ?>" class="spark-btn-reports-warning">
                    <i class="fa fa-file-text-o"></i>
                    <span><?= lang('profit_and_loss') ?></span>
                </a>
            <?php } ?>
            <a href="<?= admin_url('reports/users_report'); ?>" class="spark-btn-reports-outline">
                <i class="fa-fw fa fa-users"></i>
                <span><?= lang('staff_report') ?></span>
            </a>
        </div>
    </div>

    <!-- Category Cards (6-Card Grid Matching Exact React Screenshot) -->
    <div class="spark-reports-grid">

        <!-- Card 1: تقارير المبيعات (Sales Reports) -->
        <div class="spark-report-category-card">
            <div class="spark-report-cat-header">
                <div class="spark-report-cat-icon emerald">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <h3 class="spark-report-cat-title"><?= lang('sales_reports') ?></h3>
            </div>

            <div class="spark-report-items-list">
                <a href="<?= admin_url('sales/daily_sales'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('daily_sales'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('daily') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('sales/monthly_sales'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('monthly_sales'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('monthly') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('sales/best_sellers'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('best_sellers'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('top') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('sales/sales_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('sales_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('sales') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>
            </div>

            <!-- Action Button: Opens Sales Reports Hub -->
            <a href="<?= admin_url('sales/reports'); ?>" class="spark-report-cat-btn emerald">
                <i class="fa fa-chevron-left" style="font-size: 11px;"></i>
                <span><?= lang('view_sales_reports') ?></span>
            </a>
        </div>

        <!-- Card 2: تقارير المشتريات (Purchases Reports) -->
        <div class="spark-report-category-card">
            <div class="spark-report-cat-header">
                <div class="spark-report-cat-icon blue">
                    <i class="fa fa-cart-arrow-down"></i>
                </div>
                <h3 class="spark-report-cat-title"><?= lang('purchases_reports') ?></h3>
            </div>

            <div class="spark-report-items-list">
                <a href="<?= admin_url('purchases/monthly_purchases'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('monthly_purchases'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('monthly') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('suppliers/supplier_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('suppliers_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('suppliers') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('purchases/payments_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('payments_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('payments') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('purchases/purchases'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('purchases_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('purchases') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>
            </div>

            <!-- Action Button: Opens Purchases Reports Hub -->
            <a href="<?= admin_url('purchases/reports'); ?>" class="spark-report-cat-btn blue">
                <i class="fa fa-chevron-left" style="font-size: 11px;"></i>
                <span><?= lang('view_purchases_reports') ?></span>
            </a>
        </div>

        <!-- Card 3: تقارير المخزون والمنتجات (Inventory & Products Reports) -->
        <div class="spark-report-category-card">
            <div class="spark-report-cat-header">
                <div class="spark-report-cat-icon purple">
                    <i class="fa fa-archive"></i>
                </div>
                <h3 class="spark-report-cat-title"><?= lang('inventory_and_product_reports') ?></h3>
            </div>

            <div class="spark-report-items-list">
                <a href="<?= admin_url('products/warehouse_stock'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('warehouse_stock'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('stock') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('products/quantity_alerts'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('product_quantity_alerts'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('alerts') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('products/expiry_alerts'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('product_expiry_alerts'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('expiry') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('products/products_item_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('products_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('active') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>
            </div>

            <!-- Action Button: Opens Products Reports Hub -->
            <a href="<?= admin_url('products/reports'); ?>" class="spark-report-cat-btn purple">
                <i class="fa fa-chevron-left" style="font-size: 11px;"></i>
                <span><?= lang('view_inventory_reports') ?></span>
            </a>
        </div>

        <?php if (isset($this->Settings->preAccounting) || $this->Settings->preAccounting == 1) { ?>
        <!-- Card 4: التقارير المالية والمحاسبية (Financial & Accounting Reports) -->
        <div class="spark-report-category-card">
            <div class="spark-report-cat-header">
                <div class="spark-report-cat-icon amber">
                    <i class="fa fa-money"></i>
                </div>
                <h3 class="spark-report-cat-title"><?= lang('financial_reports') ?></h3>
            </div>

            <div class="spark-report-items-list">
                <a href="<?= admin_url('accounts/incomestatement'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('Income_Statement'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('income') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('accounts/balancesheet'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('Balance_Sheet'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('Balance') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('accounts/ownersequity'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('Statement_of_owners_equity'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('equity') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('accounts/trialbalance'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('Trial_balance') ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('trial') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>
            </div>

            <!-- Action Button: Opens Accounting Reports Hub -->
            <a href="<?= admin_url('accounts/reports'); ?>" class="spark-report-cat-btn amber">
                <i class="fa fa-chevron-left" style="font-size: 11px;"></i>
                <span><?= lang('view_accounting_reports') ?></span>
            </a>
        </div>
        <?php } ?>

        <!-- Card 5: تقارير العملاء (Customer Reports) -->
        <div class="spark-report-category-card">
            <div class="spark-report-cat-header">
                <div class="spark-report-cat-icon cyan">
                    <i class="fa fa-headphones"></i>
                </div>
                <h3 class="spark-report-cat-title"><?= lang('customer_reports') ?></h3>
            </div>

            <div class="spark-report-items-list">
                <a href="<?= admin_url('sales/customers_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('customers_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('customers'); ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('sales/agedopeninvoices'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('Aged_Open_Invoices'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('aging') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('sales/customers_payments_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('Customers_Payments'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('payments') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('sales/salesman_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('salesman_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('salesman') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

            </div>

            <!-- Action Button: Opens Customer Reports Hub -->
            <a href="<?= admin_url('sales/reports'); ?>" class="spark-report-cat-btn cyan">
                <i class="fa fa-chevron-left" style="font-size: 11px;"></i>
                <span><?= lang('view_customers_reports') ?></span>
            </a>
        </div>

        <!-- Card 6: تقارير الموردين (Supplier Reports) -->
        <div class="spark-report-category-card">
            <div class="spark-report-cat-header">
                <div class="spark-report-cat-icon rose">
                    <i class="fa fa-briefcase"></i>
                </div>
                <h3 class="spark-report-cat-title"><?= lang('supplier_reports') ?></h3>
            </div>

            <div class="spark-report-items-list">
                <a href="<?= admin_url('suppliers/supplier_report'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('suppliers_report'); ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('suppliers') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>

                <a href="<?= admin_url('purchases/agedopeninvoicessupplier'); ?>" class="spark-report-item">
                    <div class="spark-report-item-title">
                        <i class="fa fa-file-text-o"></i>
                        <span><?= lang('Supplier_debt_aging_report') ?></span>
                    </div>
                    <div class="spark-report-item-meta">
                        <span class="spark-report-badge"><?= lang('aging') ?></span>
                        <i class="fa fa-angle-left spark-report-arrow"></i>
                    </div>
                </a>
            </div>

            <!-- Action Button: Opens Supplier Reports Hub -->
            <a href="<?= admin_url('purchases/reports'); ?>" class="spark-report-cat-btn rose">
                <i class="fa fa-chevron-left" style="font-size: 11px;"></i>
                <span><?= lang('view_suppliers_reports') ?></span>
            </a>
        </div>

    </div>

</div>
