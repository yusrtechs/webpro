<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Define Permission Helper Flags from native Spark ERP permission model
$can_view_tree        = ($Owner || $Admin || !empty($GP['accounts-index']) || !empty($GP['accounts-accounts_tree']));
$can_view_entries     = ($Owner || $Admin || !empty($GP['accounts-entry_index']) || !empty($GP['accounts-entries']));
$can_add_entry        = ($Owner || $Admin || !empty($GP['accounts-add_entry']) || !empty($GP['accounts-entry_index']));
$can_view_entry_det   = ($Owner || $Admin || !empty($GP['accounts-view_entry']) || $can_view_entries);
$can_view_ledgers     = ($Owner || $Admin || !empty($GP['accounts-ledgers']));
$can_view_trialbalance= ($Owner || $Admin || !empty($GP['accounts-trialbalance']));
$can_view_incomestat  = ($Owner || $Admin || !empty($GP['accounts-incomestatement']));
$can_view_balancesheet= ($Owner || $Admin || !empty($GP['accounts-balancesheet']));
$can_view_statement   = ($Owner || $Admin || !empty($GP['accounts-accountstatement']));
$can_view_aging       = ($Owner || $Admin || !empty($GP['accounts-agedopeninvoices']));
$can_view_tax         = ($Owner || $Admin || !empty($GP['accounts-tax']));
$can_view_any_acc     = ($Owner || $Admin || $can_view_tree || $can_view_entries || $can_view_ledgers || $can_view_trialbalance || $can_view_incomestat || $can_view_balancesheet || $can_view_statement || $can_view_aging);

// Build Quick Actions dynamically
$quick_actions = [
    [
        'title' => lang('Entries'),
        'desc'  => lang('all_entries'),
        'url'   => admin_url('accounts/entries'),
        'icon'  => 'fa-file-text-o',
        'perm'  => $can_view_entries,
    ],
    [
        'title' => lang('Accounts_tree'),
        'desc'  => lang('chart_of_accounts'),
        'url'   => admin_url('accounts/accounts_tree'),
        'icon'  => 'fa-sitemap',
        'perm'  => $can_view_tree,
    ],
    [
        'title' => lang('Ledgers'),
        'desc'  => lang('general_ledger'),
        'url'   => admin_url('accounts/ledgers'),
        'icon'  => 'fa-book',
        'perm'  => $can_view_ledgers,
    ],
    [
        'title' => lang('Trial_balance'),
        'desc'  => lang('debit_and_credit'),
        'url'   => admin_url('accounts/trialbalance'),
        'icon'  => 'fa-balance-scale',
        'perm'  => $can_view_trialbalance,
    ],
    [
        'title' => lang('Income_Statement'),
        'desc'  => lang('profit_and_loss'),
        'url'   => admin_url('accounts/incomestatement'),
        'icon'  => 'fa-line-chart',
        'perm'  => $can_view_incomestat,
    ],
    [
        'title' => lang('Balance_Sheet'),
        'desc'  => lang('financial_position'),
        'url'   => admin_url('accounts/balancesheet'),
        'icon'  => 'fa-building',
        'perm'  => $can_view_balancesheet,
    ],
    [
        'title' => lang('Statement_of_owners_equity'),
        'desc'  => lang('equity_statement'),
        'url'   => admin_url('accounts/ownersequity'),
        'icon'  => 'fa-pie-chart',
        'perm'  => $can_view_balancesheet,
    ],
    [
        'title' => lang('tax_report'),
        'desc'  => lang('vat_declaration'),
        'url'   => admin_url('accounts/tax'),
        'icon'  => 'fa-percent',
        'perm'  => $can_view_tax,
    ],
];
$quick_actions = array_filter($quick_actions, function($qa) { return !empty($qa['perm']); });

// Build Header Dropdown Actions dynamically
$header_actions = [];
if ($can_view_entries) {
    $header_actions[] = ['url' => admin_url('accounts/entries'), 'icon' => 'fa-file-text-o', 'label' => lang('Entries')];
}
if ($can_view_tree) {
    $header_actions[] = ['url' => admin_url('accounts/accounts_tree'), 'icon' => 'fa-sitemap', 'label' => lang('Accounts_tree')];
}
if ($can_view_ledgers) {
    $header_actions[] = ['url' => admin_url('accounts/ledgers'), 'icon' => 'fa-book', 'label' => lang('Ledgers')];
}
if ($can_view_trialbalance) {
    $header_actions[] = ['url' => admin_url('accounts/trialbalance'), 'icon' => 'fa-balance-scale', 'label' => lang('Trial_balance')];
}
if ($can_view_incomestat) {
    $header_actions[] = ['url' => admin_url('accounts/incomestatement'), 'icon' => 'fa-line-chart', 'label' => lang('Income_Statement')];
}
if ($can_view_balancesheet) {
    $header_actions[] = ['url' => admin_url('accounts/balancesheet'), 'icon' => 'fa-building', 'label' => lang('Balance_Sheet')];
    $header_actions[] = ['url' => admin_url('accounts/ownersequity'), 'icon' => 'fa-pie-chart', 'label' => lang('Statement_of_owners_equity')];
}
if ($can_view_statement) {
    $header_actions[] = ['url' => admin_url('accounts/accountstatement'), 'icon' => 'fa-list-alt', 'label' => lang('Account_statement')];
}
if ($can_view_tax) {
    $header_actions[] = ['url' => admin_url('accounts/tax'), 'icon' => 'fa-percent', 'label' => lang('tax_report')];
}
if ($can_view_aging) {
    $header_actions[] = ['url' => admin_url('accounts/agedopeninvoices'), 'icon' => 'fa-clock-o', 'label' => lang('Aged_Open_Invoices')];
}

// Prepare safe default values
$active_period = $active_period ?? 'this_month';
$display_date_range = $display_date_range ?? (date('Y-m-01') . ' → ' . date('Y-m-t'));
$from_date_input = $from_date_input ?? date('Y-m-01');
$to_date_input = $to_date_input ?? date('Y-m-d');

$stats = $stats ?? [
    'total_revenue' => 0,
    'total_expenses' => 0,
    'net_profit' => 0,
    'profit_margin' => 0,
    'accounts_receivable' => 0,
    'accounts_payable' => 0,
    'customers_due_count' => 0,
    'suppliers_due_count' => 0,
    'cash_bank_balance' => 0,
    'total_assets' => 0,
    'total_liabilities' => 0,
    'period_entries_count' => 0,
    'total_period_debit' => 0,
    'total_period_credit' => 0,
];

$financial_position = $financial_position ?? [
    'total_assets' => 0,
    'total_liabilities' => 0,
    'total_equity' => 0,
    'assets_pct' => 50,
    'liabilities_pct' => 50,
];

$monthly_trends = $monthly_trends ?? [];
$expense_breakdown = $expense_breakdown ?? [];
$revenue_breakdown = $revenue_breakdown ?? [];
$cash_bank_balances = $cash_bank_balances ?? [];
$top_receivables = $top_receivables ?? [];
$top_payables = $top_payables ?? [];
$attention_items = $attention_items ?? [
    'overdue_receivables_count' => 0,
    'overdue_receivables_amount' => 0,
    'overdue_payables_count' => 0,
    'overdue_payables_amount' => 0,
    'unposted_entries_count' => 0,
];
$recent_transactions = $recent_transactions ?? [];

// Prepare ECharts Trend data
$trend_months = [];
$trend_rev = [];
$trend_exp = [];
$trend_profit = [];

if (!empty($monthly_trends)) {
    foreach ($monthly_trends as $trend) {
        $trend_months[] = date('M Y', strtotime($trend['month'] . '-01'));
        $trend_rev[] = (float)$trend['revenue'];
        $trend_exp[] = (float)$trend['expense'];
        $trend_profit[] = (float)$trend['profit'];
    }
}
?>

<style>
.spark-acc-filter-wrapper {
  background: var(--spark-surface, #ffffff);
  border: 1px solid var(--spark-border, #e2e8f0);
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.spark-acc-filter-container {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.spark-acc-period-presets {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
}
.spark-acc-filter-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  font-size: 12.5px;
  font-weight: 500;
  color: var(--spark-text-secondary, #64748b);
  background: var(--spark-card-bg, #f8fafc);
  border: 1px solid var(--spark-border, #e2e8f0);
  border-radius: 8px;
  text-decoration: none !important;
  transition: all 0.2s ease;
  cursor: pointer;
}
.spark-acc-filter-btn:hover {
  color: var(--spark-primary, #6366f1);
  border-color: var(--spark-primary, #6366f1);
  background: rgba(99, 102, 241, 0.05);
}
.spark-acc-filter-btn.active {
  color: #ffffff !important;
  background: var(--spark-primary, #6366f1) !important;
  border-color: var(--spark-primary, #6366f1) !important;
  box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3);
  font-weight: 600;
}
.spark-acc-active-range-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  background: rgba(99, 102, 241, 0.08);
  border: 1px solid rgba(99, 102, 241, 0.2);
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  color: var(--spark-primary, #6366f1);
}
.spark-acc-entries-pill {
  background: var(--spark-primary, #6366f1);
  color: #ffffff;
  padding: 2px 7px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.spark-acc-custom-drawer {
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px dashed var(--spark-border, #e2e8f0);
}
.spark-acc-custom-form {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
}
.spark-acc-form-group {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: var(--spark-text, #1e293b);
}
.spark-acc-date-input {
  border-radius: 6px !important;
  border: 1px solid var(--spark-border, #cbd5e1) !important;
  padding: 4px 10px !important;
  font-size: 12px !important;
  height: 32px !important;
}
.spark-acc-form-actions {
  display: flex;
  align-items: center;
  gap: 6px;
}
[data-theme="dark"] .spark-acc-filter-wrapper {
  background: #1e293b;
  border-color: #334155;
}
[data-theme="dark"] .spark-acc-filter-btn {
  background: #0f172a;
  border-color: #334155;
  color: #94a3b8;
}
[data-theme="dark"] .spark-acc-date-input {
  background: #0f172a !important;
  color: #f8fafc !important;
  border-color: #334155 !important;
}
</style>

<div class="spark-dashboard spark-accounting-dashboard">

  <!-- =========================================================================
       1. PAGE HEADER & ACTIONS
       ========================================================================= -->
  <div class="spark-accounting-header">
    <div class="spark-accounting-header-start">
      <div class="spark-accounting-header-icon">
        <i class="fa fa-calculator"></i>
      </div>
      <div class="spark-accounting-header-info">
        <h1 class="spark-accounting-title"><?= lang('accounting_dashboard'); ?></h1>
        <p class="spark-accounting-subtitle"><?= lang('accounting_overview'); ?></p>
      </div>
    </div>

    <div class="spark-accounting-header-actions">
      <div class="spark-dashboard-date-pill">
        <i class="fa fa-calendar"></i>
        <span><?= date('Y-m-d'); ?></span>
      </div>

      <!-- Print Dashboard Action -->
      <button type="button" class="spark-btn spark-btn-outline spark-btn-sm" onclick="window.print();">
        <i class="fa fa-print"></i> <?= lang('print_dashboard'); ?>
      </button>

      <!-- New Journal Entry Action -->
      <?php if ($can_add_entry): ?>
        <a href="<?= admin_url('accounts/add_entry'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn spark-btn-primary spark-btn-sm">
          <i class="fa fa-plus-circle"></i> <?= lang('new_journal_entry'); ?>
        </a>
      <?php endif; ?>

      <!-- Accounts Guide Action -->
      <?php if ($can_view_tree): ?>
        <a href="<?= admin_url('accounts/accountsguide'); ?>" class="spark-btn spark-btn-outline spark-btn-sm">
          <i class="fa fa-book"></i> <?= lang('Accounts_guide'); ?>
        </a>
      <?php endif; ?>

      <!-- Actions Dropdown -->
      <?php if (!empty($header_actions)): ?>
        <div class="dropdown" style="display: inline-block;">
          <button class="spark-btn spark-btn-outline spark-btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-tasks"></i>
            <span><?= lang('actions'); ?></span>
            <i class="fa fa-angle-down" style="font-size: 11px; margin-inline-start: 4px;"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu">
            <?php foreach ($header_actions as $ha): ?>
              <li>
                <a href="<?= $ha['url']; ?>">
                  <i class="fa <?= $ha['icon']; ?>"></i> <?= $ha['label']; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($Owner || $Admin): ?>
  <!-- =========================================================================
       1.1 REAL-TIME PERIOD FILTER BAR
       ========================================================================= -->
  <div class="spark-acc-filter-wrapper">
    <div class="spark-acc-filter-container">
      <div class="spark-acc-period-presets">
        <a href="<?= admin_url('accounts/dashboard?period=today'); ?>" class="spark-acc-filter-btn <?= ($active_period == 'today') ? 'active' : ''; ?>">
          <i class="fa fa-clock-o"></i> <?= lang('today'); ?>
        </a>
        <a href="<?= admin_url('accounts/dashboard?period=this_week'); ?>" class="spark-acc-filter-btn <?= ($active_period == 'this_week') ? 'active' : ''; ?>">
          <i class="fa fa-calendar-minus-o"></i> <?= lang('this_week'); ?>
        </a>
        <a href="<?= admin_url('accounts/dashboard?period=this_month'); ?>" class="spark-acc-filter-btn <?= ($active_period == 'this_month') ? 'active' : ''; ?>">
          <i class="fa fa-calendar"></i> <?= lang('this_month'); ?>
        </a>
        <a href="<?= admin_url('accounts/dashboard?period=this_quarter'); ?>" class="spark-acc-filter-btn <?= ($active_period == 'this_quarter') ? 'active' : ''; ?>">
          <i class="fa fa-pie-chart"></i> <?= lang('this_quarter'); ?>
        </a>
        <a href="<?= admin_url('accounts/dashboard?period=this_year'); ?>" class="spark-acc-filter-btn <?= ($active_period == 'this_year') ? 'active' : ''; ?>">
          <i class="fa fa-calendar-check-o"></i> <?= lang('this_year'); ?>
        </a>
        <a href="<?= admin_url('accounts/dashboard?period=fiscal_year'); ?>" class="spark-acc-filter-btn <?= ($active_period == 'fiscal_year') ? 'active' : ''; ?>">
          <i class="fa fa-university"></i> <?= lang('fiscal_year'); ?>
        </a>
        <a href="javascript:void(0);" id="btn_toggle_custom_filter" class="spark-acc-filter-btn <?= ($active_period == 'custom') ? 'active' : ''; ?>">
          <i class="fa fa-sliders"></i> <?= lang('custom_range'); ?>
        </a>
        <a href="<?= admin_url('accounts/dashboard?period=all'); ?>" class="spark-acc-filter-btn <?= ($active_period == 'all') ? 'active' : ''; ?>">
          <i class="fa fa-globe"></i> <?= lang('all_time'); ?>
        </a>
      </div>

      <div class="spark-acc-active-range-badge">
        <i class="fa fa-calendar-check-o"></i>
        <span><?= htmlspecialchars($display_date_range); ?></span>
        <?php if (!empty($stats['period_entries_count']) && $can_view_entries) { ?>
          <span class="spark-acc-entries-pill"><i class="fa fa-file-text-o"></i> <?= number_format($stats['period_entries_count']); ?> <?= lang('entries'); ?></span>
        <?php } ?>
      </div>
    </div>

    <!-- Custom Date Range Form Drawer -->
    <div id="spark_custom_date_drawer" class="spark-acc-custom-drawer" style="<?= ($active_period == 'custom') ? 'display: block;' : 'display: none;'; ?>">
      <form action="<?= admin_url('accounts/dashboard'); ?>" method="get" class="spark-acc-custom-form">
        <input type="hidden" name="period" value="custom">
        <div class="spark-acc-form-group">
          <label><i class="fa fa-calendar"></i> <?= lang('from'); ?>:</label>
          <input type="date" name="from" class="form-control input-sm spark-acc-date-input" value="<?= htmlspecialchars($from_date_input); ?>" required>
        </div>
        <div class="spark-acc-form-group">
          <label><i class="fa fa-calendar"></i> <?= lang('to'); ?>:</label>
          <input type="date" name="to" class="form-control input-sm spark-acc-date-input" value="<?= htmlspecialchars($to_date_input); ?>" required>
        </div>
        <div class="spark-acc-form-actions">
          <button type="submit" class="spark-btn spark-btn-primary spark-btn-sm">
            <i class="fa fa-filter"></i> <?= lang('apply_filter'); ?>
          </button>
          <a href="<?= admin_url('accounts/dashboard?period=this_month'); ?>" class="spark-btn spark-btn-outline spark-btn-sm">
            <i class="fa fa-refresh"></i> <?= lang('reset'); ?>
          </a>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- =========================================================================
       9. QUICK ACTIONS HUB
       ========================================================================= -->
  <?php if (!empty($quick_actions)): ?>
    <div class="spark-accounting-grid-row">
      <div class="spark-acc-col-12">
        <div class="spark-acc-card">
          <div class="spark-acc-card-header">
            <h3 class="spark-acc-card-title">
              <i class="fa fa-bolt"></i> <?= lang('quick_actions'); ?>
            </h3>
          </div>
          <div class="spark-acc-card-body">
            <div class="spark-acc-actions-grid">
              <?php foreach ($quick_actions as $qa): ?>
                <a href="<?= $qa['url']; ?>" class="spark-acc-action-tile">
                  <div class="spark-acc-action-icon">
                    <i class="fa <?= $qa['icon']; ?>"></i>
                  </div>
                  <div class="spark-acc-action-info">
                    <span class="spark-acc-action-title"><?= $qa['title']; ?></span>
                    <span class="spark-acc-action-desc"><?= $qa['desc']; ?></span>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       2. KPI STATISTICS SUMMARY (8 Cards Grid)
       ========================================================================= -->
  <div class="spark-accounting-kpi-grid">
    <!-- Card 1: Total Revenue -->
    <?php if ($Owner || $Admin): ?>
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon icon-indigo">
            <i class="fa fa-line-chart"></i>
          </div>
          <span class="spark-accounting-kpi-badge badge-indigo">
            <?= lang('total_revenue'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val"><?= $this->sma->formatMoney($stats['total_revenue']); ?></div>
          <div class="spark-accounting-kpi-lbl"><?= lang('total_revenue'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('sales'); ?>: <strong><?= $this->sma->formatMoney($stats['total_revenue']); ?></strong></span>
          <i class="fa fa-arrow-up" style="color: var(--spark-success);"></i>
        </div>
      </div>

      <!-- Card 2: Total Expenses -->
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon icon-rose">
            <i class="fa fa-credit-card"></i>
          </div>
          <span class="spark-accounting-kpi-badge badge-rose">
            <?= lang('total_expenses'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val"><?= $this->sma->formatMoney($stats['total_expenses']); ?></div>
          <div class="spark-accounting-kpi-lbl"><?= lang('total_expenses'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('purchases'); ?> &bull; <?= lang('overhead_cost'); ?></span>
          <i class="fa fa-arrow-down" style="color: var(--spark-danger);"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 3: Net Profit -->
    <?php if ($Owner || $Admin): ?>
      <?php $is_profitable = ($stats['net_profit'] >= 0); ?>
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon <?= $is_profitable ? 'icon-emerald' : 'icon-rose'; ?>">
            <i class="fa fa-area-chart"></i>
          </div>
          <span class="spark-accounting-kpi-badge <?= $is_profitable ? 'badge-emerald' : 'badge-rose'; ?>">
            <?= $stats['profit_margin']; ?>% <?= lang('profit_margin'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val" style="color: <?= $is_profitable ? 'var(--spark-success)' : 'var(--spark-danger)'; ?>;">
            <?= $this->sma->formatMoney($stats['net_profit']); ?>
          </div>
          <div class="spark-accounting-kpi-lbl"><?= lang('net_profit'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('profit_margin'); ?>: <strong><?= $stats['profit_margin']; ?>%</strong></span>
          <i class="fa fa-calculator"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 4: Accounts Receivable -->
    <?php if ($Owner || $Admin): ?>
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon icon-cyan">
            <i class="fa fa-arrow-circle-down"></i>
          </div>
          <span class="spark-accounting-kpi-badge badge-cyan">
            <?= (int)$stats['customers_due_count']; ?> <?= lang('customers'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val"><?= $this->sma->formatMoney($stats['accounts_receivable']); ?></div>
          <div class="spark-accounting-kpi-lbl"><?= lang('accounts_receivable'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('customers_with_due'); ?>: <strong><?= (int)$stats['customers_due_count']; ?></strong></span>
          <i class="fa fa-users"></i>
        </div>
      </div>

      <!-- Card 5: Accounts Payable -->
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon icon-amber">
            <i class="fa fa-arrow-circle-up"></i>
          </div>
          <span class="spark-accounting-kpi-badge badge-amber">
            <?= (int)$stats['suppliers_due_count']; ?> <?= lang('suppliers'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val"><?= $this->sma->formatMoney($stats['accounts_payable']); ?></div>
          <div class="spark-accounting-kpi-lbl"><?= lang('accounts_payable'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('suppliers_with_due'); ?>: <strong><?= (int)$stats['suppliers_due_count']; ?></strong></span>
          <i class="fa fa-truck"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 6: Cash & Bank Available -->
    <?php if ($Owner || $Admin): ?>
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon icon-teal">
            <i class="fa fa-bank"></i>
          </div>
          <span class="spark-accounting-kpi-badge badge-teal">
            <?= lang('cash_and_bank'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val"><?= $this->sma->formatMoney($stats['cash_bank_balance']); ?></div>
          <div class="spark-accounting-kpi-lbl"><?= lang('cash_and_bank'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('available_balance'); ?></span>
          <i class="fa fa-money"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 7: Total Assets & Card 8: Total Liabilities -->
    <?php if ($Owner || $Admin): ?>
      <!-- Card 7: Total Assets -->
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon icon-purple">
            <i class="fa fa-building"></i>
          </div>
          <span class="spark-accounting-kpi-badge badge-purple">
            <?= lang('total_assets'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val"><?= $this->sma->formatMoney($stats['total_assets']); ?></div>
          <div class="spark-accounting-kpi-lbl"><?= lang('total_assets'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('Assets'); ?></span>
          <i class="fa fa-cubes"></i>
        </div>
      </div>

      <!-- Card 8: Total Liabilities -->
      <div class="spark-accounting-kpi-card">
        <div class="spark-accounting-kpi-top">
          <div class="spark-accounting-kpi-icon icon-blue">
            <i class="fa fa-shield"></i>
          </div>
          <span class="spark-accounting-kpi-badge badge-blue">
            <?= lang('total_liabilities'); ?>
          </span>
        </div>
        <div class="spark-accounting-kpi-content">
          <div class="spark-accounting-kpi-val"><?= $this->sma->formatMoney($stats['total_liabilities']); ?></div>
          <div class="spark-accounting-kpi-lbl"><?= lang('total_liabilities'); ?></div>
        </div>
        <div class="spark-accounting-kpi-footer">
          <span><?= lang('Liabilities'); ?></span>
          <i class="fa fa-balance-scale"></i>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- =========================================================================
       3. FINANCIAL PERFORMANCE & FINANCIAL POSITION ROW
       ========================================================================= -->
  <?php
    $show_perf = ($Owner || $Admin);
    $show_pos  = ($Owner || $Admin);
  ?>
  <?php if ($show_perf || $show_pos): ?>
    <div class="spark-accounting-grid-row">
      <!-- Financial Performance -->
      <?php if ($show_perf): ?>
        <?php $is_profitable = ($stats['net_profit'] >= 0); ?>
        <div class="<?= $show_pos ? 'spark-acc-col-6' : 'spark-acc-col-12'; ?>">
          <div class="spark-acc-card">
            <div class="spark-acc-card-header">
              <h3 class="spark-acc-card-title">
                <i class="fa fa-dashboard"></i> <?= lang('financial_performance'); ?>
              </h3>
              <span class="spark-accounting-kpi-badge <?= $is_profitable ? 'badge-emerald' : 'badge-rose'; ?>">
                <?= $stats['profit_margin']; ?>% <?= lang('profit_margin'); ?>
              </span>
            </div>
            <div class="spark-acc-card-body">
              <div class="spark-acc-perf-container">
                <div class="spark-acc-perf-meter">
                  <div class="spark-acc-perf-header">
                    <span style="font-size: 13px; font-weight: 600; color: var(--spark-text-secondary);"><?= lang('profit_margin'); ?></span>
                    <span class="spark-acc-perf-pct"><?= $stats['profit_margin']; ?>%</span>
                  </div>
                  <div class="spark-acc-progress-track">
                    <div class="spark-acc-progress-fill <?= $is_profitable ? 'fill-success' : 'fill-danger'; ?>" style="width: <?= min(100, max(0, $stats['profit_margin'])); ?>%;"></div>
                  </div>
                </div>

                <div class="spark-acc-perf-stats-grid">
                  <div class="spark-acc-perf-stat-box">
                    <span class="spark-acc-perf-stat-val" style="color: var(--spark-primary);"><?= $this->sma->formatMoney($stats['total_revenue']); ?></span>
                    <span class="spark-acc-perf-stat-lbl"><?= lang('total_revenue'); ?></span>
                  </div>
                  <div class="spark-acc-perf-stat-box">
                    <span class="spark-acc-perf-stat-val" style="color: var(--spark-danger);"><?= $this->sma->formatMoney($stats['total_expenses']); ?></span>
                    <span class="spark-acc-perf-stat-lbl"><?= lang('total_expenses'); ?></span>
                  </div>
                  <div class="spark-acc-perf-stat-box">
                    <span class="spark-acc-perf-stat-val" style="color: <?= $is_profitable ? 'var(--spark-success)' : 'var(--spark-danger)'; ?>;"><?= $this->sma->formatMoney($stats['net_profit']); ?></span>
                    <span class="spark-acc-perf-stat-lbl"><?= lang('net_profit'); ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Financial Position -->
      <?php if ($show_pos): ?>
        <div class="<?= $show_perf ? 'spark-acc-col-6' : 'spark-acc-col-12'; ?>">
          <div class="spark-acc-card">
            <div class="spark-acc-card-header">
              <h3 class="spark-acc-card-title">
                <i class="fa fa-balance-scale"></i> <?= lang('financial_position'); ?>
              </h3>
              <a href="<?= admin_url('accounts/balancesheet'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                <i class="fa fa-building"></i> <?= lang('Balance_Sheet'); ?>
              </a>
            </div>
            <div class="spark-acc-card-body">
              <div class="spark-acc-perf-container">
                <div class="spark-acc-perf-meter">
                  <div class="spark-acc-perf-header">
                    <span style="font-size: 13px; font-weight: 600; color: var(--spark-text-secondary);"><?= lang('total_assets'); ?> <?= lang('vs'); ?> <?= lang('total_liabilities'); ?></span>
                    <span class="spark-acc-perf-pct"><?= $financial_position['assets_pct']; ?>%</span>
                  </div>
                  <div class="spark-acc-progress-track">
                    <div class="spark-acc-progress-fill fill-primary" style="width: <?= min(100, $financial_position['assets_pct']); ?>%;"></div>
                  </div>
                </div>

                <div class="spark-acc-perf-stats-grid">
                  <div class="spark-acc-perf-stat-box">
                    <span class="spark-acc-perf-stat-val" style="color: #8b5cf6;"><?= $this->sma->formatMoney($financial_position['total_assets']); ?></span>
                    <span class="spark-acc-perf-stat-lbl"><?= lang('total_assets'); ?></span>
                  </div>
                  <div class="spark-acc-perf-stat-box">
                    <span class="spark-acc-perf-stat-val" style="color: #64748b;"><?= $this->sma->formatMoney($financial_position['total_liabilities']); ?></span>
                    <span class="spark-acc-perf-stat-lbl"><?= lang('total_liabilities'); ?></span>
                  </div>
                  <div class="spark-acc-perf-stat-box">
                    <span class="spark-acc-perf-stat-val" style="color: #0d9488;"><?= $this->sma->formatMoney($financial_position['total_equity']); ?></span>
                    <span class="spark-acc-perf-stat-lbl"><?= lang('total_equity'); ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       4. REVENUE & EXPENSE TREND CHART + EXPENSE BREAKDOWN ROW
       ========================================================================= -->
  <?php
    $show_trend = ($Owner || $Admin);
    $show_exp_bd = ($Owner || $Admin);
  ?>
  <?php if ($show_trend || $show_exp_bd): ?>
    <div class="spark-accounting-grid-row">
      <!-- Trend Chart Card -->
      <?php if ($show_trend): ?>
        <div class="<?= $show_exp_bd ? 'spark-acc-col-7' : 'spark-acc-col-12'; ?>">
          <div class="spark-acc-card">
            <div class="spark-acc-card-header">
              <h3 class="spark-acc-card-title">
                <i class="fa fa-line-chart"></i> <?= lang('revenue_trend'); ?>
              </h3>
              <span class="spark-accounting-kpi-badge badge-indigo">
                <i class="fa fa-calendar"></i> <?= lang('last_12_months'); ?>
              </span>
            </div>
            <div class="spark-acc-card-body" style="padding: 16px;">
              <?php if (!empty($monthly_trends)) { ?>
                <div id="echart_accounting_trend" style="width: 100%; height: 320px;"></div>
              <?php } else { ?>
                <div class="spark-acc-empty-state">
                  <i class="fa fa-bar-chart"></i>
                  <div class="spark-acc-empty-title"><?= lang('no_trend_data'); ?></div>
                  <div class="spark-acc-empty-desc"><?= lang('no_accounting_transactions'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Expense Breakdown Analysis Card -->
      <?php if ($show_exp_bd): ?>
        <div class="<?= $show_trend ? 'spark-acc-col-5' : 'spark-acc-col-12'; ?>">
          <div class="spark-acc-card">
            <div class="spark-acc-card-header">
              <h3 class="spark-acc-card-title">
                <i class="fa fa-pie-chart" style="color: #f43f5e;"></i> <?= lang('expense_breakdown'); ?>
              </h3>
              <span class="spark-accounting-kpi-badge badge-rose">
                <?= lang('cost_analysis'); ?>
              </span>
            </div>
            <div class="spark-acc-card-body">
              <?php if (!empty($expense_breakdown)) { ?>
                <div class="spark-acc-breakdown-list">
                  <?php foreach ($expense_breakdown as $exp) { ?>
                    <div class="spark-acc-breakdown-item">
                      <div class="spark-acc-breakdown-header">
                        <span class="spark-acc-breakdown-title">
                          <i class="fa fa-circle" style="font-size: 8px; color: #f43f5e;"></i>
                          <?= htmlspecialchars($exp->accountName); ?>
                        </span>
                        <span class="spark-acc-breakdown-val"><?= $this->sma->formatMoney($exp->amount); ?></span>
                      </div>
                      <div class="spark-acc-progress-track" style="height: 6px;">
                        <div class="spark-acc-progress-fill fill-danger" style="width: <?= min(100, $exp->pct); ?>%;"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-acc-empty-state">
                  <i class="fa fa-credit-card"></i>
                  <div class="spark-acc-empty-title"><?= lang('no_expense_data'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       5. REVENUE BREAKDOWN & CASH / BANK BALANCES ROW
       ========================================================================= -->
  <?php
    $show_rev_bd  = ($Owner || $Admin);
    $show_cash_bk = ($Owner || $Admin);
  ?>
  <?php if ($show_rev_bd || $show_cash_bk): ?>
    <div class="spark-accounting-grid-row">
      <!-- Revenue Breakdown Card -->
      <?php if ($show_rev_bd): ?>
        <div class="<?= $show_cash_bk ? 'spark-acc-col-6' : 'spark-acc-col-12'; ?>">
          <div class="spark-acc-card">
            <div class="spark-acc-card-header">
              <h3 class="spark-acc-card-title">
                <i class="fa fa-money" style="color: #10b981;"></i> <?= lang('revenue_breakdown'); ?>
              </h3>
              <a href="<?= admin_url('accounts/incomestatement'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                <i class="fa fa-external-link"></i> <?= lang('Income_Statement'); ?>
              </a>
            </div>
            <div class="spark-acc-card-body">
              <?php if (!empty($revenue_breakdown)) { ?>
                <div class="spark-acc-breakdown-list">
                  <?php foreach ($revenue_breakdown as $rev) { ?>
                    <div class="spark-acc-breakdown-item">
                      <div class="spark-acc-breakdown-header">
                        <span class="spark-acc-breakdown-title">
                          <i class="fa fa-circle" style="font-size: 8px; color: #10b981;"></i>
                          <?= htmlspecialchars($rev->accountName); ?>
                        </span>
                        <span class="spark-acc-breakdown-val" style="color: var(--spark-success);"><?= $this->sma->formatMoney($rev->amount); ?></span>
                      </div>
                      <div class="spark-acc-progress-track" style="height: 6px;">
                        <div class="spark-acc-progress-fill fill-success" style="width: <?= min(100, $rev->pct); ?>%;"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-acc-empty-state">
                  <i class="fa fa-line-chart"></i>
                  <div class="spark-acc-empty-title"><?= lang('no_revenue_data'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Cash & Bank Balances -->
      <?php if ($show_cash_bk): ?>
        <div class="<?= $show_rev_bd ? 'spark-acc-col-6' : 'spark-acc-col-12'; ?>">
          <div class="spark-acc-card">
            <div class="spark-acc-card-header">
              <h3 class="spark-acc-card-title">
                <i class="fa fa-bank" style="color: #06b6d4;"></i> <?= lang('cash_bank_balances'); ?>
              </h3>
              <?php if ($can_view_ledgers): ?>
                <a href="<?= admin_url('accounts/ledgers'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                  <i class="fa fa-book"></i> <?= lang('Ledgers'); ?>
                </a>
              <?php endif; ?>
            </div>
            <div class="spark-acc-card-body">
              <?php if (!empty($cash_bank_balances)) { ?>
                <div class="spark-acc-cash-list">
                  <?php foreach ($cash_bank_balances as $cb) {
                    $is_bank = (stripos($cb->accountName, 'بنك') !== false || stripos($cb->accountName, 'bank') !== false || $cb->account_type == 'bank');
                  ?>
                    <?php if ($can_view_statement): ?>
                      <a href="<?= admin_url('accounts/accountstatement?account=' . $cb->id); ?>" class="spark-acc-cash-item">
                        <div class="spark-acc-cash-left">
                          <div class="spark-acc-cash-icon" style="background-color: <?= $is_bank ? 'var(--spark-primary-subtle, #eef2ff)' : '#ecfdf5'; ?>; color: <?= $is_bank ? 'var(--spark-primary, #6366f1)' : '#10b981'; ?>;">
                            <i class="fa <?= $is_bank ? 'fa-bank' : 'fa-money'; ?>"></i>
                          </div>
                          <div>
                            <div class="spark-acc-cash-name"><?= htmlspecialchars($cb->accountName); ?></div>
                            <div class="spark-acc-cash-code"><?= htmlspecialchars($cb->accountID); ?></div>
                          </div>
                        </div>
                        <div class="spark-acc-cash-bal"><?= $this->sma->formatMoney($cb->balance); ?></div>
                      </a>
                    <?php else: ?>
                      <div class="spark-acc-cash-item">
                        <div class="spark-acc-cash-left">
                          <div class="spark-acc-cash-icon" style="background-color: <?= $is_bank ? 'var(--spark-primary-subtle, #eef2ff)' : '#ecfdf5'; ?>; color: <?= $is_bank ? 'var(--spark-primary, #6366f1)' : '#10b981'; ?>;">
                            <i class="fa <?= $is_bank ? 'fa-bank' : 'fa-money'; ?>"></i>
                          </div>
                          <div>
                            <div class="spark-acc-cash-name"><?= htmlspecialchars($cb->accountName); ?></div>
                            <div class="spark-acc-cash-code"><?= htmlspecialchars($cb->accountID); ?></div>
                          </div>
                        </div>
                        <div class="spark-acc-cash-bal"><?= $this->sma->formatMoney($cb->balance); ?></div>
                      </div>
                    <?php endif; ?>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-acc-empty-state">
                  <i class="fa fa-bank"></i>
                  <div class="spark-acc-empty-title"><?= lang('no_cash_bank_data'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       6. RECEIVABLES & PAYABLES ROW
       ========================================================================= -->
  <?php
    $show_rec = ($Owner || $Admin);
    $show_pay = ($Owner || $Admin);
  ?>
  <?php if ($show_rec || $show_pay): ?>
    <div class="spark-accounting-grid-row">
      <!-- Receivables -->
      <div class="spark-acc-col-6">
        <div class="spark-acc-card">
          <div class="spark-acc-card-header">
            <h3 class="spark-acc-card-title">
              <i class="fa fa-arrow-circle-down" style="color: #06b6d4;"></i> <?= lang('accounts_receivable'); ?>
            </h3>
            <?php if ($can_view_aging): ?>
              <a href="<?= admin_url('accounts/agedopeninvoices'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                <i class="fa fa-clock-o"></i> <?= lang('Aged_Open_Invoices'); ?>
              </a>
            <?php endif; ?>
          </div>
          <div class="spark-acc-card-body">
            <div class="spark-acc-dues-summary">
              <span class="spark-acc-dues-lbl"><?= lang('total_outstanding'); ?></span>
              <span class="spark-acc-dues-val" style="color: var(--spark-info);"><?= $this->sma->formatMoney($stats['accounts_receivable']); ?></span>
            </div>

            <?php if (!empty($top_receivables)) { ?>
              <div class="spark-acc-breakdown-list">
                <?php foreach ($top_receivables as $cust) { ?>
                  <div class="spark-acc-due-item">
                    <div class="spark-acc-due-info">
                      <span class="spark-acc-due-name"><?= htmlspecialchars($cust->company ?: $cust->name); ?></span>
                      <span class="spark-acc-due-phone"><?= htmlspecialchars($cust->phone ?: '—'); ?></span>
                    </div>
                    <span class="spark-acc-due-amount" style="color: var(--spark-info);">
                      <?= $this->sma->formatMoney($cust->due_amount); ?>
                    </span>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="spark-acc-empty-state">
                <i class="fa fa-check-circle" style="color: var(--spark-success);"></i>
                <div class="spark-acc-empty-title"><?= lang('no_receivables'); ?></div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <!-- Payables -->
      <div class="spark-acc-col-6">
        <div class="spark-acc-card">
          <div class="spark-acc-card-header">
            <h3 class="spark-acc-card-title">
              <i class="fa fa-arrow-circle-up" style="color: #f59e0b;"></i> <?= lang('accounts_payable'); ?>
            </h3>
            <?php if ($can_view_aging): ?>
              <a href="<?= admin_url('accounts/agedopeninvoicessupplier'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                <i class="fa fa-clock-o"></i> <?= lang('Supplier_debt_aging_report'); ?>
              </a>
            <?php endif; ?>
          </div>
          <div class="spark-acc-card-body">
            <div class="spark-acc-dues-summary">
              <span class="spark-acc-dues-lbl"><?= lang('total_outstanding'); ?></span>
              <span class="spark-acc-dues-val" style="color: var(--spark-warning);"><?= $this->sma->formatMoney($stats['accounts_payable']); ?></span>
            </div>

            <?php if (!empty($top_payables)) { ?>
              <div class="spark-acc-breakdown-list">
                <?php foreach ($top_payables as $supp) { ?>
                  <div class="spark-acc-due-item">
                    <div class="spark-acc-due-info">
                      <span class="spark-acc-due-name"><?= htmlspecialchars($supp->company ?: $supp->name); ?></span>
                      <span class="spark-acc-due-phone"><?= htmlspecialchars($supp->phone ?: '—'); ?></span>
                    </div>
                    <span class="spark-acc-due-amount" style="color: var(--spark-warning);">
                      <?= $this->sma->formatMoney($supp->due_amount); ?>
                    </span>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="spark-acc-empty-state">
                <i class="fa fa-check-circle" style="color: var(--spark-success);"></i>
                <div class="spark-acc-empty-title"><?= lang('no_payables'); ?></div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       7. ATTENTION REQUIRED WIDGET
       ========================================================================= -->
  <?php
    $show_att_rec = ($Owner || $Admin);
    $show_att_pay = ($Owner || $Admin);
    $show_att_unp = ($Owner || $Admin);
  ?>
  <?php if ($show_att_rec || $show_att_pay || $show_att_unp): ?>
    <div class="spark-accounting-grid-row">
      <div class="spark-acc-col-12">
        <div class="spark-acc-card">
          <div class="spark-acc-card-header">
            <h3 class="spark-acc-card-title">
              <i class="fa fa-bell" style="color: #f59e0b;"></i> <?= lang('attention_required'); ?>
            </h3>
          </div>
          <div class="spark-acc-card-body">
            <div class="spark-acc-attention-grid">
              <!-- Overdue Receivables -->
              <?php if ($show_att_rec): ?>
                <div class="spark-acc-attention-card att-danger">
                  <div class="spark-acc-attention-icon">
                    <i class="fa fa-exclamation-circle"></i>
                  </div>
                  <div class="spark-acc-attention-info">
                    <span class="spark-acc-attention-val"><?= $this->sma->formatMoney($attention_items['overdue_receivables_amount']); ?></span>
                    <span class="spark-acc-attention-lbl"><?= (int)$attention_items['overdue_receivables_count']; ?> <?= lang('overdue_receivables'); ?></span>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Overdue Payables -->
              <?php if ($show_att_pay): ?>
                <div class="spark-acc-attention-card att-warning">
                  <div class="spark-acc-attention-icon">
                    <i class="fa fa-clock-o"></i>
                  </div>
                  <div class="spark-acc-attention-info">
                    <span class="spark-acc-attention-val"><?= $this->sma->formatMoney($attention_items['overdue_payables_amount']); ?></span>
                    <span class="spark-acc-attention-lbl"><?= (int)$attention_items['overdue_payables_count']; ?> <?= lang('overdue_payables'); ?></span>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Unposted Entries -->
              <?php if ($show_att_unp): ?>
                <div class="spark-acc-attention-card att-info">
                  <div class="spark-acc-attention-icon">
                    <i class="fa fa-pencil-square-o"></i>
                  </div>
                  <div class="spark-acc-attention-info">
                    <span class="spark-acc-attention-val"><?= number_format($attention_items['unposted_entries_count']); ?></span>
                    <span class="spark-acc-attention-lbl"><?= lang('unposted_entries'); ?></span>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       8. RECENT ACCOUNTING TRANSACTIONS TABLE
       ========================================================================= -->
  <?php if ($Owner || $Admin): ?>
    <div class="spark-accounting-grid-row">
      <div class="spark-acc-col-12">
        <div class="spark-acc-card">
          <div class="spark-acc-card-header">
            <h3 class="spark-acc-card-title">
              <i class="fa fa-list-alt"></i> <?= lang('recent_accounting_transactions'); ?>
            </h3>
            <a href="<?= admin_url('accounts/entries'); ?>" class="spark-btn spark-btn-outline spark-btn-sm">
              <i class="fa fa-external-link"></i> <?= lang('view_all_entries'); ?>
            </a>
          </div>
          <div class="spark-acc-card-body" style="padding: 0;">
            <?php if (!empty($recent_transactions)) { ?>
              <div class="spark-acc-table-responsive">
                <table class="spark-acc-table">
                  <thead>
                    <tr>
                      <th><?= lang('entry_number'); ?></th>
                      <th><?= lang('reference'); ?></th>
                      <th><?= lang('date'); ?></th>
                      <th><?= lang('description'); ?></th>
                      <th><?= lang('entry_type'); ?></th>
                      <th><?= lang('created_by'); ?></th>
                      <th><?= lang('status'); ?></th>
                      <?php if ($can_view_entry_det): ?>
                        <th style="text-align: center;"><?= lang('actions'); ?></th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_transactions as $entry) {
                      $is_posted = ($entry->entryStatus == 1);
                    ?>
                      <tr>
                        <td style="font-weight: 700; font-family: var(--spark-font-mono);">
                          <?= htmlspecialchars($entry->entry_no ?: ('#' . $entry->entryID)); ?>
                        </td>
                        <td style="font-family: var(--spark-font-mono); color: var(--spark-text-secondary);">
                          <?= htmlspecialchars($entry->reference_no ?: '—'); ?>
                        </td>
                        <td>
                          <i class="fa fa-calendar-o" style="color: var(--spark-text-muted); margin-inline-end: 4px;"></i>
                          <?= $this->sma->hrsd($entry->entryDate); ?>
                        </td>
                        <td style="font-weight: 500; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                          <?= htmlspecialchars($entry->entryDescription ?: '—'); ?>
                        </td>
                        <td>
                          <span class="spark-accounting-kpi-badge badge-slate">
                            <?= htmlspecialchars(lang($entry->entryType)); ?>
                          </span>
                        </td>
                        <td>
                          <i class="fa fa-user-o" style="color: var(--spark-text-muted); margin-inline-end: 4px;"></i>
                          <?= htmlspecialchars($entry->creator_name ?: '—'); ?>
                        </td>
                        <td>
                          <span class="spark-accounting-kpi-badge <?= $is_posted ? 'badge-emerald' : 'badge-amber'; ?>">
                            <?= $is_posted ? lang('posted') : lang('draft'); ?>
                          </span>
                        </td>
                        <?php if ($can_view_entry_det): ?>
                          <td style="text-align: center;">
                            <div class="btn-group">
                              <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                <?= lang('actions'); ?> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?>" role="menu">
                                <li><a href="<?= admin_url('accounts/view_entries/' . $entry->entryID); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> <?= lang('Entry_details'); ?></a></li>
                              </ul>
                            </div>
                          </td>
                        <?php endif; ?>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="spark-acc-empty-state">
                <i class="fa fa-inbox"></i>
                <div class="spark-acc-empty-title"><?= lang('no_accounting_transactions'); ?></div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<!-- ECharts Library Script & Initialization -->
<script src="<?= $assets ?>template1/vendors/echarts/dist/echarts.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    // Custom Date Range drawer toggle
    $('#btn_toggle_custom_filter').on('click', function(e) {
        e.preventDefault();
        $('#spark_custom_date_drawer').slideToggle(200);
    });

    if (typeof (echarts) === 'undefined') {
        return;
    }

    var chartDom = document.getElementById('echart_accounting_trend');
    if (!chartDom) {
        return;
    }

    var isDark = $('html').attr('data-theme') === 'dark' || $('body').attr('data-theme') === 'dark';
    var textColor = isDark ? '#94a3b8' : '#64748b';
    var gridBorder = isDark ? '#1f2937' : '#f1f5f9';
    var brandPrimary = getComputedStyle(document.documentElement).getPropertyValue('--spark-primary').trim() || '#6366f1';

    var myChart = echarts.init(chartDom);
    var option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                crossStyle: {
                    color: '#999'
                }
            }
        },
        legend: {
            data: ['<?= lang('total_revenue'); ?>', '<?= lang('total_expenses'); ?>', '<?= lang('net_profit'); ?>'],
            textStyle: {
                color: textColor
            },
            top: 0
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '40px',
            containLabel: true
        },
        xAxis: [
            {
                type: 'category',
                data: <?= json_encode($trend_months); ?>,
                axisPointer: {
                    type: 'shadow'
                },
                axisLine: {
                    lineStyle: {
                        color: gridBorder
                    }
                },
                axisLabel: {
                    color: textColor
                }
            }
        ],
        yAxis: [
            {
                type: 'value',
                axisLine: {
                    lineStyle: {
                        color: gridBorder
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: gridBorder
                    }
                },
                axisLabel: {
                    color: textColor
                }
            }
        ],
        series: [
            {
                name: '<?= lang('total_revenue'); ?>',
                type: 'bar',
                data: <?= json_encode($trend_rev); ?>,
                itemStyle: {
                    color: brandPrimary,
                    borderRadius: [4, 4, 0, 0]
                }
            },
            {
                name: '<?= lang('total_expenses'); ?>',
                type: 'bar',
                data: <?= json_encode($trend_exp); ?>,
                itemStyle: {
                    color: '#f43f5e',
                    borderRadius: [4, 4, 0, 0]
                }
            },
            {
                name: '<?= lang('net_profit'); ?>',
                type: 'line',
                smooth: true,
                data: <?= json_encode($trend_profit); ?>,
                itemStyle: {
                    color: '#10b981'
                },
                lineStyle: {
                    width: 3
                }
            }
        ]
    };

    myChart.setOption(option);

    $(window).on('resize', function () {
        if (myChart) {
            myChart.resize();
        }
    });
});
</script>
