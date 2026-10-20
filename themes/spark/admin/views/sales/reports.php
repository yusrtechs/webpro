<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa fa-th"></i><span class="break"></span><?= lang('sales_reports') ?: lang('reports'); ?></h2>
    </div>

    <div class="box-content">
        <div class="row">
            <?php
            $reports = [
                ['best_sellers', 'best_sellers', 'fa-trophy', 'icon-blue', 'Top_selling_products'],
                ['daily_sales', 'daily_sales', 'fa-line-chart', 'icon-green', 'Daily_sales_analysis'],
                ['monthly_sales', 'monthly_sales', 'fa-bar-chart', 'icon-orange', 'Monthly_performance'],
                ['sales_report', 'sales_report', 'fa-file-text', 'icon-red', 'Sales_summary'],
                ['sales', 'Sales_Transaction_Report', 'fa-exchange', 'icon-purple', 'Sales_transaction'],
                ['delivery_report', 'Sales_Delivery_Report', 'fa-truck', 'icon-blue', 'Sales_Delivery_Report'],
                ['payments_report', 'payments_report', 'fa-money', 'icon-green', 'payments_report'],
                ['salesman_report', 'salesman_report', 'fa-user', 'icon-orange', 'Salesman_performance'],
                ['customers_report', 'customers_report', 'fa-users', 'icon-red', 'Customer_analytics'],
                ['customers_payments_report', 'Customers_Payments', 'fa-credit-card', 'icon-purple', 'Customers_Payments'],
                ['agedopeninvoices', 'Aged_Open_Invoices', 'fa-clock-o', 'icon-blue', 'Outstanding_invoices'],
                ['register', 'register_report', 'fa-desktop', 'icon-green', 'Register_summary'],
                ['sales_cn_report', 'Credit_Notes', 'fa-file-o', 'icon-orange', 'Credit_Notes'],
                ['commercial_discounts_report', 'Discount_Allowance', 'fa-tags', 'icon-red', 'Discounts_report']
            ];

            if (!empty($this->Settings->isSubscriptions) && $this->Settings->isSubscriptions == 1) {
                $reports[] = [
                    'customer_subscriptions_report',
                    'Customer_subscriptions_report',
                    'fa-refresh',
                    'icon-purple',
                    'Subscriptions'
                ];
            }

            foreach ($reports as $r):
            ?>
            <div class="col-lg-3 col-md-6 col-sm-6 report-item">
                <a href="<?= admin_url('sales/' . $r[0]) ?>" class="report-card">
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