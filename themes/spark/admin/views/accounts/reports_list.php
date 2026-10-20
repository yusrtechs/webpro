<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Accounting Reports Hub — Matching Spark ERP Sales/Purchases/Products Reports Standard -->
<div class="box" style="margin-bottom: 24px;">
    <div class="box-header">
        <h2 class="blue"><i class="fa fa-line-chart"></i><span class="break"></span><?= lang('reports') . ' — ' . (lang('financial_reports') ?: 'تقارير المحاسبة والمالية'); ?></h2>
        <div class="box-icon" style="margin-inline-start: auto; display: flex; align-items: center; gap: 8px;">
            <div class="report-search-wrap" style="position: relative; min-width: 240px;">
                <input type="text" id="reportSearchInput" class="form-control input-sm" placeholder="<?= lang('search') ?>..." style="height: 34px; border-radius: var(--spark-radius-md, 8px); padding-inline-start: 32px; font-size: 12px;">
                <i class="fa fa-search" style="position: absolute; inset-block-start: 50%; transform: translateY(-50%); inset-inline-start: 11px; color: var(--spark-text-muted, #94a3b8); font-size: 12px; pointer-events: none;"></i>
            </div>
        </div>
    </div>

    <div class="box-content">
        <div class="report-grid" id="accountingReportsGrid">
            <?php
            // Exact 14 Accounting Reports matching Spark ERP Accounting Suite
            $accounting_reports = [
                ['accountsguide', 'Accounts_guide', 'fa-sitemap', 'icon-blue', 'accounts_guide_desc'],
                ['ledgers', 'Ledgers', 'fa-book', 'icon-green', 'general_ledger_desc'],
                ['trialbalance', 'Trial_balance', 'fa-balance-scale', 'icon-orange', 'trial_balance_desc'],
                ['entriesreport', 'Entries_Report', 'fa-list-alt', 'icon-purple', 'entries_report_desc'],
                ['incomestatement', 'Income_Statement', 'fa-line-chart', 'icon-green', 'income_statement_desc'],
                ['ownersequity', 'Statement_of_owners_equity', 'fa-pie-chart', 'icon-blue', 'statement_of_owners_equity_desc'],
                ['balancesheet', 'Balance_Sheet', 'fa-bar-chart', 'icon-orange', 'balance_sheet_desc'],
                ['cash_flow', 'cash_flow_statement', 'fa-exchange', 'icon-blue', 'cash_flow_statement_desc'],
                ['accountstatement', 'Account_statement', 'fa-file-text-o', 'icon-purple', 'account_statement_desc'],
                ['tax', 'tax_report', 'fa-percent', 'icon-red', 'tax_report_desc'],
                ['agedopeninvoices', 'Aged_Open_Invoices', 'fa-users', 'icon-blue', 'aged_debt_customers_desc'],
                ['agedopeninvoicessupplier', 'Supplier_debt_aging_report', 'fa-truck', 'icon-orange', 'aged_debt_suppliers_desc'],
                ['debtliability', 'Debt_and_Liability', 'fa-credit-card', 'icon-red', 'debt_and_liability_desc'],
                ['distributioncostcenters', 'Cost_centers_Report', 'fa-building-o', 'icon-green', 'cost_centers_desc'],
                ['collection_report', 'Collection_report', 'fa-money', 'icon-purple', 'collection_report_desc']
            ];

            foreach ($accounting_reports as $r):
                $report_url   = admin_url('accounts/' . $r[0]);
                $report_title = lang($r[1]) ?: $r[1];
                $report_desc  = lang($r[4]) ?: $r[4];
            ?>
            <div class="report-item" data-report-title="<?= htmlspecialchars(strtolower($report_title), ENT_QUOTES, 'UTF-8') ?>" data-report-desc="<?= htmlspecialchars(strtolower($report_desc), ENT_QUOTES, 'UTF-8') ?>">
                <a href="<?= $report_url ?>" class="report-card">
                    <div class="report-flex">
                        <div class="report-icon <?= $r[3] ?>">
                            <i class="fa <?= $r[2] ?>"></i>
                        </div>
                        <div class="report-content">
                            <div class="report-title">
                                <?= $report_title ?>
                            </div>
                            <div class="report-desc">
                                <?= $report_desc ?>
                            </div>
                        </div>
                        <div class="report-arrow">
                            <i class="fa fa-angle-left report-arrow-icon"></i>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="noReportsFound" style="display: none; padding: 32px 16px; text-align: center; color: var(--spark-text-muted, #94a3b8); font-size: 13px;">
            <i class="fa fa-search" style="font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.4;"></i>
            <?= lang('no_match_found') ?: 'لا توجد نتائج مطابقة' ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('reportSearchInput');
    if (!searchInput) return;
    
    var items = document.querySelectorAll('#accountingReportsGrid .report-item');
    var noResults = document.getElementById('noReportsFound');
    
    searchInput.addEventListener('input', function () {
        var query = this.value.trim().toLowerCase();
        var visibleCount = 0;
        
        items.forEach(function (item) {
            var title = item.getAttribute('data-report-title') || '';
            var desc = item.getAttribute('data-report-desc') || '';
            var text = (item.textContent || '').trim().toLowerCase();
            
            if (!query || title.indexOf(query) !== -1 || desc.indexOf(query) !== -1 || text.indexOf(query) !== -1) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        if (noResults) {
            noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    });
});
</script>


