<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa fa-th"></i><span class="break"></span><?= lang('purchases_reports') ?: lang('reports'); ?></h2>
    </div>

    <div class="box-content">
        <div class="row">
            <?php
            $reports = [
                ['daily_purchases', 'daily_purchases', 'fa-calendar', 'icon-blue', 'Daily_purchases_report'],
                ['monthly_purchases', 'monthly_purchases', 'fa-bar-chart', 'icon-green', 'Monthly_purchases_analysis'],
                ['purchases', 'purchases_report', 'fa-shopping-cart', 'icon-purple', 'Detailed_purchases_report'],
                ['expenses_report', 'expenses_report', 'fa-money', 'icon-red', 'Expenses_and_costs_report'],
                ['supplier_report', 'suppliers_report', 'fa-truck', 'icon-orange', 'Suppliers_performance_report'],
                ['agedopeninvoicessupplier', 'Supplier_debt_aging_report', 'fa-clock-o', 'icon-red', 'Supplier_debt_aging_analysis'],
                ['purchases_dn', 'Debit_Notes', 'fa-reply', 'icon-blue', 'Debit_Notes'],
                ['payments_report', 'payments_report', 'fa-credit-card', 'icon-green', 'Supplier_payments_report'],
                ['expense_payments_report', 'expense_payments_report', 'fa-usd', 'icon-purple', 'expense_payments_report'],
            ];

            foreach ($reports as $r):
            ?>
            <div class="col-lg-3 col-md-6 col-sm-6 report-item">
                <?php if ($r[0] == 'supplier_report') { ?>
                    <a href="<?= admin_url('suppliers/' . $r[0]) ?>" class="report-card">
                <?php } else { ?>
                    <a href="<?= admin_url('purchases/' . $r[0]) ?>" class="report-card">
                <?php } ?>
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