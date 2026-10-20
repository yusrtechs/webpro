<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Define Permission Helper Flags from native Spark ERP permission model
$can_view_sales      = ($Owner || $Admin || !empty($GP['sales-index']));
$can_add_sale        = ($Owner || $Admin || !empty($GP['sales-add']));
$can_edit_sale       = ($Owner || $Admin);
$can_view_payments   = ($Owner || $Admin);
$can_view_quotes     = ($Owner || $Admin);
$can_view_deliveries = ($Owner || $Admin);
$can_view_customers  = ($Owner || $Admin);
$can_view_reports    = ($Owner || $Admin);
$can_view_cost       = ($Owner || $Admin);

// Build Quick Actions dynamically
$quick_actions = [
    [
        'title' => lang('add_sale'),
        'desc'  => lang('create_new_invoice'),
        'url'   => admin_url('sales/add'),
        'icon'  => 'fa-plus-circle',
        'style' => 'background: var(--spark-primary-subtle, #eef2ff); color: var(--spark-primary, #6366f1);',
        'perm'  => $can_add_sale,
    ],
    [
        'title' => lang('list_sales'),
        'desc'  => lang('all_invoices'),
        'url'   => admin_url('sales'),
        'icon'  => 'fa-list',
        'style' => 'background:#eff6ff; color:#3b82f6;',
        'perm'  => $can_view_sales,
    ],
    [
        'title' => lang('quotes'),
        'desc'  => lang('quotations_management'),
        'url'   => admin_url('sales/quotes_index'),
        'icon'  => 'fa-file-text-o',
        'style' => 'background:#f5f3ff; color:#8b5cf6;',
        'perm'  => $can_view_quotes,
    ],
    [
        'title' => lang('payments'),
        'desc'  => lang('received_payments'),
        'url'   => admin_url('sales/payments_report'),
        'icon'  => 'fa-money',
        'style' => 'background:#ecfeff; color:#06b6d4;',
        'perm'  => $can_view_payments,
    ],
    [
        'title' => lang('customers'),
        'desc'  => lang('client_directory'),
        'url'   => admin_url('customers'),
        'icon'  => 'fa-users',
        'style' => 'background:#f0fdfa; color:#0d9488;',
        'perm'  => $can_view_customers,
    ],
    [
        'title' => lang('sales_reports'),
        'desc'  => lang('sales_analytics'),
        'url'   => admin_url('sales/reports'),
        'icon'  => 'fa-bar-chart',
        'style' => 'background:#fffbeb; color:#f59e0b;',
        'perm'  => $can_view_reports,
    ],
];
$quick_actions = array_filter($quick_actions, function($qa) { return !empty($qa['perm']); });

// Build Header Dropdown Actions dynamically
$header_actions = [];
if ($can_view_sales) {
    $header_actions[] = ['url' => admin_url('sales'), 'icon' => 'fa-list', 'label' => lang('list_sales')];
}
if ($can_view_quotes) {
    $header_actions[] = ['url' => admin_url('sales/quotes_index'), 'icon' => 'fa-file-text-o', 'label' => lang('quotes')];
}
if ($can_view_payments) {
    $header_actions[] = ['url' => admin_url('sales/payments_report'), 'icon' => 'fa-money', 'label' => lang('payments')];
}
if ($can_view_deliveries) {
    $header_actions[] = ['url' => admin_url('sales/deliveries'), 'icon' => 'fa-truck', 'label' => lang('deliveries')];
}
if ($can_view_customers) {
    $header_actions[] = ['url' => admin_url('customers'), 'icon' => 'fa-users', 'label' => lang('customers')];
}
if ($can_view_reports) {
    $header_actions[] = ['url' => admin_url('sales/reports'), 'icon' => 'fa-bar-chart', 'label' => lang('sales_reports')];
}
?>

<div class="spark-sales-dashboard">

  <!-- =========================================================================
       1. PAGE HEADER
       ========================================================================= -->
  <div class="spark-sales-header">
    <div class="spark-sales-header-start">
      <div class="spark-sales-header-icon">
        <i class="fa fa-line-chart"></i>
      </div>
      <div>
        <h1 class="spark-sales-title"><?= lang('sales_dashboard'); ?></h1>
        <p class="spark-sales-subtitle"><?= lang('manage_sales_desc'); ?></p>
      </div>
    </div>

    <div class="spark-sales-header-actions">
      <!-- Date Range Filter -->
      <select class="spark-sales-select" id="salesDateRangeSelect" onchange="applySalesFilter()">
        <option value="all" <?= ($date_range == 'all') ? 'selected' : ''; ?>><?= lang('all_time'); ?></option>
        <option value="today" <?= ($date_range == 'today') ? 'selected' : ''; ?>><?= lang('today'); ?></option>
        <option value="this_week" <?= ($date_range == 'this_week') ? 'selected' : ''; ?>><?= lang('this_week'); ?></option>
        <option value="this_month" <?= ($date_range == 'this_month') ? 'selected' : ''; ?>><?= lang('this_month'); ?></option>
        <option value="this_year" <?= ($date_range == 'this_year') ? 'selected' : ''; ?>><?= lang('this_year'); ?></option>
      </select>

      <!-- Warehouse Selector -->
      <?php if (!empty($warehouses) && ($Owner || $Admin || empty($this->session->userdata('warehouse_id')))) { ?>
        <select class="spark-sales-select" id="salesWarehouseSelect" onchange="applySalesFilter()">
          <option value=""><?= lang('all_warehouses'); ?></option>
          <?php foreach ($warehouses as $wh) { ?>
            <option value="<?= $wh->id; ?>" <?= ($warehouse_id == $wh->id) ? 'selected' : ''; ?>>
              <?= htmlspecialchars($wh->name); ?>
            </option>
          <?php } ?>
        </select>
      <?php } ?>

      <!-- Current Date Pill -->
      <div class="spark-sales-select" style="display:inline-flex; align-items:center; gap:6px; cursor:default; background-color:var(--spark-surface-card, #ffffff);">
        <i class="fa fa-calendar-o" style="color:var(--spark-primary, #6366f1);"></i>
        <span><?= date('Y-m-d'); ?></span>
      </div>

      <!-- Action: Print Dashboard -->
      <button type="button" class="spark-btn spark-btn-outline spark-btn-sm" onclick="window.print();">
        <i class="fa fa-print"></i> <?= lang('print_dashboard'); ?>
      </button>

      <!-- Action: Add Sale -->
      <?php if ($can_add_sale): ?>
        <a href="<?= admin_url('sales/add'); ?>" class="spark-btn spark-btn-primary spark-btn-sm" style="display:inline-flex; align-items:center; gap:6px; height:34px; border-radius:var(--spark-radius-md, 8px); padding-inline:14px; font-weight:600; text-decoration:none;">
          <i class="fa fa-plus"></i> <?= lang('add_sale'); ?>
        </a>
      <?php endif; ?>

      <!-- Actions Dropdown -->
      <?php if (!empty($header_actions)): ?>
        <div class="btn-group">
          <button type="button" class="spark-btn spark-btn-outline spark-btn-sm dropdown-toggle" data-toggle="dropdown" style="height:34px; border-radius:var(--spark-radius-md, 8px); padding-inline:12px;">
            <i class="fa fa-ellipsis-v"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?>" role="menu" style="border-radius:var(--spark-radius-md, 8px); box-shadow:0 10px 25px rgba(0,0,0,0.1); border:1px solid var(--spark-border-default, #e2e8f0);">
            <?php foreach ($header_actions as $ha): ?>
              <li><a href="<?= $ha['url']; ?>"><i class="fa <?= $ha['icon']; ?>"></i> <?= $ha['label']; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- =========================================================================
       8. ROW 6: QUICK ACTIONS HUB (8 TILES)
       ========================================================================= -->
  <?php if (!empty($quick_actions)): ?>
    <div class="spark-sales-grid-row">
      <div class="spark-sales-col-12">
        <div class="spark-sales-card">
          <div class="spark-sales-card-header">
            <h3 class="spark-sales-card-title">
              <i class="fa fa-bolt" style="color:#f59e0b;"></i>
              <?= lang('quick_actions'); ?>
            </h3>
            <span class="spark-sales-kpi-badge badge-amber"><?= lang('shortcuts'); ?></span>
          </div>
          <div class="spark-sales-card-body">
            <div class="spark-sales-actions-grid">
              <?php foreach ($quick_actions as $qa): ?>
                <a href="<?= $qa['url']; ?>" class="spark-sales-action-tile">
                  <div class="spark-sales-action-icon" style="<?= $qa['style']; ?>">
                    <i class="fa <?= $qa['icon']; ?>"></i>
                  </div>
                  <div class="spark-sales-action-info">
                    <span class="spark-sales-action-title"><?= $qa['title']; ?></span>
                    <span class="spark-sales-action-desc"><?= $qa['desc']; ?></span>
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
       2. KPI STATISTICS GRID (8 CARDS)
       ========================================================================= -->
  <div class="spark-sales-kpi-grid">
    <?php if ($Owner || $Admin): ?>
      <!-- Card 1: Total Sales -->
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-indigo">
            <i class="fa fa-line-chart"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-indigo">
            <?= lang('gross_sales'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val"><?= $this->sma->formatMoney($stats['total_sales'] ?? 0); ?></span>
          <span class="spark-sales-kpi-lbl"><?= lang('total_sales'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <span><?= lang('invoices'); ?>: <strong><?= number_format($stats['total_invoices'] ?? 0); ?></strong></span>
          <span><i class="fa fa-file-text-o"></i> <?= lang('active'); ?></span>
        </div>
      </div>

      <!-- Card 2: Net Sales -->
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-blue">
            <i class="fa fa-calculator"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-blue">
            <?= lang('net_sales'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val"><?= $this->sma->formatMoney($stats['net_sales'] ?? 0); ?></span>
          <span class="spark-sales-kpi-lbl"><?= lang('net_sales'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <span><?= lang('tax'); ?>: <strong><?= $this->sma->formatMoney($stats['total_tax'] ?? 0); ?></strong></span>
          <span><?= lang('discount'); ?>: <strong><?= $this->sma->formatMoney($stats['total_discount'] ?? 0); ?></strong></span>
        </div>
      </div>

      <!-- Card 3: Total Invoices -->
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-purple">
            <i class="fa fa-files-o"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-purple">
            <?= lang('sales_volume'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val"><?= number_format($stats['total_invoices'] ?? 0); ?></span>
          <span class="spark-sales-kpi-lbl"><?= lang('total_invoices'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <span><?= lang('customers'); ?>: <strong><?= number_format($stats['total_customers'] ?? 0); ?></strong></span>
          <span><?= lang('paid'); ?>: <strong><?= number_format($stats['paid_invoices'] ?? 0); ?></strong></span>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($Owner || $Admin): ?>
      <!-- Card 4: Total Paid -->
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-emerald">
            <i class="fa fa-check-circle-o"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-emerald">
            <i class="fa fa-arrow-down"></i> <?= lang('collected'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val" style="color:#10b981;"><?= $this->sma->formatMoney($stats['total_paid'] ?? 0); ?></span>
          <span class="spark-sales-kpi-lbl"><?= lang('total_paid'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <?php
            $paid_rate = (($stats['total_sales'] ?? 0) > 0) ? round((($stats['total_paid'] ?? 0) / $stats['total_sales']) * 100, 1) : 0;
          ?>
          <span><?= lang('collection_rate'); ?>: <strong><?= $paid_rate; ?>%</strong></span>
          <span><i class="fa fa-check"></i></span>
        </div>
      </div>

      <!-- Card 5: Total Due -->
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-amber">
            <i class="fa fa-clock-o"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-amber">
            <?= lang('due'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val" style="color:#f59e0b;"><?= $this->sma->formatMoney($stats['total_due'] ?? 0); ?></span>
          <span class="spark-sales-kpi-lbl"><?= lang('total_due'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <span><?= lang('unpaid'); ?>: <strong><?= number_format($stats['unpaid_invoices'] ?? 0); ?></strong></span>
          <span><?= lang('partial'); ?>: <strong><?= number_format($stats['partial_invoices'] ?? 0); ?></strong></span>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 6: Total Profit (Requires Cost Permission) -->
    <?php if ($can_view_cost): ?>
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-rose">
            <i class="fa fa-diamond"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-rose">
            <?= lang('gross_profit'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val" style="color:<?= (($stats['total_profit'] ?? 0) >= 0) ? '#10b981' : '#f43f5e'; ?>;">
            <?= $this->sma->formatMoney($stats['total_profit'] ?? 0); ?>
          </span>
          <span class="spark-sales-kpi-lbl"><?= lang('total_profit'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <span><?= lang('profit_margin'); ?>: <strong><?= number_format($stats['profit_margin'] ?? 0, 1); ?>%</strong></span>
          <span><i class="fa fa-line-chart"></i></span>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($Owner || $Admin): ?>
      <!-- Card 7: Average Invoice -->
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-cyan">
            <i class="fa fa-balance-scale"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-cyan">
            <?= lang('average'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val"><?= $this->sma->formatMoney($stats['average_invoice_value'] ?? 0); ?></span>
          <span class="spark-sales-kpi-lbl"><?= lang('average_invoice_value'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <span><?= lang('cash_sales'); ?>: <strong><?= $this->sma->formatMoney($stats['cash_sales'] ?? 0); ?></strong></span>
          <span><i class="fa fa-money"></i></span>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 8: Profit Margin (Requires Cost Permission) -->
    <?php if ($can_view_cost): ?>
      <div class="spark-sales-kpi-card">
        <div class="spark-sales-kpi-top">
          <div class="spark-sales-kpi-icon icon-teal">
            <i class="fa fa-percent"></i>
          </div>
          <span class="spark-sales-kpi-badge badge-teal">
            <?= lang('profit_margin'); ?>
          </span>
        </div>
        <div class="spark-sales-kpi-content">
          <span class="spark-sales-kpi-val"><?= number_format($stats['profit_margin'] ?? 0, 1); ?>%</span>
          <span class="spark-sales-kpi-lbl"><?= lang('profit_margin'); ?></span>
        </div>
        <div class="spark-sales-kpi-footer">
          <span><?= lang('credit_sales'); ?>: <strong><?= $this->sma->formatMoney($stats['credit_sales'] ?? 0); ?></strong></span>
          <span><i class="fa fa-credit-card"></i></span>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- =========================================================================
       3. ROW 1: SALES PERFORMANCE & STATUS BREAKDOWN (2 COLUMNS)
       ========================================================================= -->
  <?php if ($Owner || $Admin): ?>
    <?php
      $paid_rate = (($stats['total_sales'] ?? 0) > 0) ? round((($stats['total_paid'] ?? 0) / $stats['total_sales']) * 100, 1) : 0;
    ?>
    <div class="spark-sales-grid-row">
      <!-- Left: Sales Performance Breakdown -->
      <div class="spark-sales-col-6">
        <div class="spark-sales-card">
          <div class="spark-sales-card-header">
            <h3 class="spark-sales-card-title">
              <i class="fa fa-pie-chart" style="color:var(--spark-primary, #6366f1);"></i>
              <?= lang('sales_performance'); ?>
            </h3>
            <span class="spark-sales-kpi-badge badge-indigo"><?= lang('overview'); ?></span>
          </div>
          <div class="spark-sales-card-body">
            <div class="spark-sales-status-item" style="margin-bottom:14px;">
              <div class="spark-sales-status-header">
                <span class="spark-sales-status-label"><?= lang('collected_vs_due'); ?></span>
                <span class="spark-sales-status-count"><?= $paid_rate; ?>% <?= lang('collected'); ?></span>
              </div>
              <div class="spark-sales-progress-track" style="height:10px;">
                <div class="spark-sales-progress-fill fill-success" style="width: <?= min(100, max(0, $paid_rate)); ?>%;"></div>
              </div>
            </div>

            <div class="spark-sales-perf-grid">
              <div class="spark-sales-perf-box">
                <span class="spark-sales-perf-lbl"><?= lang('gross_sales'); ?></span>
                <span class="spark-sales-perf-val" style="color: var(--spark-primary, #6366f1);"><?= $this->sma->formatMoney($stats['total_sales'] ?? 0); ?></span>
              </div>
              <div class="spark-sales-perf-box">
                <span class="spark-sales-perf-lbl"><?= lang('net_sales'); ?></span>
                <span class="spark-sales-perf-val" style="color:#3b82f6;"><?= $this->sma->formatMoney($stats['net_sales'] ?? 0); ?></span>
              </div>
              <div class="spark-sales-perf-box">
                <span class="spark-sales-perf-lbl"><?= lang('total_paid'); ?></span>
                <span class="spark-sales-perf-val" style="color:#10b981;"><?= $this->sma->formatMoney($stats['total_paid'] ?? 0); ?></span>
              </div>
              <div class="spark-sales-perf-box">
                <span class="spark-sales-perf-lbl"><?= lang('total_due'); ?></span>
                <span class="spark-sales-perf-val" style="color:#f59e0b;"><?= $this->sma->formatMoney($stats['total_due'] ?? 0); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Sales & Payment Status Breakdown -->
      <div class="spark-sales-col-6">
        <div class="spark-sales-card">
          <div class="spark-sales-card-header">
            <h3 class="spark-sales-card-title">
              <i class="fa fa-tasks" style="color:#10b981;"></i>
              <?= lang('sales_status'); ?>
            </h3>
            <span class="spark-sales-kpi-badge badge-emerald"><?= number_format($status_summary['total_invoices'] ?? 0); ?> <?= lang('invoices'); ?></span>
          </div>
          <div class="spark-sales-card-body">
            <div class="spark-sales-status-list">
              <!-- Completed -->
              <div class="spark-sales-status-item">
                <div class="spark-sales-status-header">
                  <span class="spark-sales-status-label"><i class="fa fa-check-circle" style="color:#10b981;"></i> <?= lang('completed'); ?></span>
                  <span class="spark-sales-status-count"><?= number_format($status_summary['completed_count'] ?? 0); ?> (<?= $status_summary['completed_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-sales-progress-track">
                  <div class="spark-sales-progress-fill fill-success" style="width: <?= $status_summary['completed_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>

              <!-- Pending -->
              <div class="spark-sales-status-item">
                <div class="spark-sales-status-header">
                  <span class="spark-sales-status-label"><i class="fa fa-hourglass-half" style="color:#f59e0b;"></i> <?= lang('pending'); ?></span>
                  <span class="spark-sales-status-count"><?= number_format($status_summary['pending_count'] ?? 0); ?> (<?= $status_summary['pending_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-sales-progress-track">
                  <div class="spark-sales-progress-fill fill-warning" style="width: <?= $status_summary['pending_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>

              <!-- Paid Status -->
              <div class="spark-sales-status-item">
                <div class="spark-sales-status-header">
                  <span class="spark-sales-status-label"><i class="fa fa-money" style="color:#06b6d4;"></i> <?= lang('paid'); ?></span>
                  <span class="spark-sales-status-count"><?= number_format($status_summary['paid_count'] ?? 0); ?> (<?= $status_summary['paid_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-sales-progress-track">
                  <div class="spark-sales-progress-fill fill-cyan" style="width: <?= $status_summary['paid_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>

              <!-- Due Status -->
              <div class="spark-sales-status-item">
                <div class="spark-sales-status-header">
                  <span class="spark-sales-status-label"><i class="fa fa-exclamation-circle" style="color:#f43f5e;"></i> <?= lang('due'); ?></span>
                  <span class="spark-sales-status-count"><?= number_format($status_summary['due_count'] ?? 0); ?> (<?= $status_summary['due_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-sales-progress-track">
                  <div class="spark-sales-progress-fill fill-danger" style="width: <?= $status_summary['due_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       4. ROW 2: SALES TREND CHART & PAYMENT METHODS (2 COLUMNS)
       ========================================================================= -->
  <?php
    $show_trend = $can_view_sales;
    $show_payments = ($can_view_sales || $can_view_payments);
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-sales-grid-row">
      <!-- Left: ECharts Sales Movement Trend -->
      <?php if ($show_trend): ?>
        <div class="<?= $show_payments ? 'spark-sales-col-7' : 'spark-sales-col-12'; ?>">
          <div class="spark-sales-card">
            <div class="spark-sales-card-header">
              <h3 class="spark-sales-card-title">
                <i class="fa fa-area-chart" style="color:var(--spark-primary, #6366f1);"></i>
                <?= lang('sales_trend'); ?>
              </h3>
              <span class="spark-sales-kpi-badge badge-indigo"><?= lang('monthly_sales'); ?></span>
            </div>
            <div class="spark-sales-card-body">
              <div id="sparkSalesTrendChart" style="width: 100%; height: 320px;"></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Right: Payment Methods -->
      <?php if ($show_payments): ?>
        <div class="<?= $show_trend ? 'spark-sales-col-5' : 'spark-sales-col-12'; ?>">
          <div class="spark-sales-card">
            <div class="spark-sales-card-header">
              <h3 class="spark-sales-card-title">
                <i class="fa fa-credit-card" style="color:#0d9488;"></i>
                <?= lang('payment_methods'); ?>
              </h3>
              <span class="spark-sales-kpi-badge badge-teal"><?= lang('distribution'); ?></span>
            </div>
            <div class="spark-sales-card-body">
              <?php if (!empty($payment_distribution)) { ?>
                <div class="spark-sales-status-list">
                  <?php foreach ($payment_distribution as $pay) {
                    $method_icon = 'fa-money';
                    $method_class = 'fill-primary';
                    if ($pay->payment_method == 'cash') {
                        $method_icon = 'fa-money';
                        $method_class = 'fill-success';
                    } elseif ($pay->payment_method == 'bank_transfer') {
                        $method_icon = 'fa-university';
                        $method_class = 'fill-blue';
                    } elseif ($pay->payment_method == 'CC') {
                        $method_icon = 'fa-credit-card';
                        $method_class = 'fill-purple';
                    } elseif ($pay->payment_method == 'deposit') {
                        $method_icon = 'fa-shield';
                        $method_class = 'fill-cyan';
                    }
                  ?>
                    <div class="spark-sales-status-item">
                      <div class="spark-sales-status-header">
                        <span class="spark-sales-status-label">
                          <i class="fa <?= $method_icon; ?>" style="color: var(--spark-primary, #6366f1);"></i>
                          <?= lang($pay->payment_method) ? lang($pay->payment_method) : ucfirst(str_replace('_', ' ', $pay->payment_method)); ?>
                        </span>
                        <span class="spark-sales-status-count">
                          <?= $this->sma->formatMoney($pay->total_amount); ?> (<?= $pay->pct; ?>%)
                        </span>
                      </div>
                      <div class="spark-sales-progress-track">
                        <div class="spark-sales-progress-fill <?= $method_class; ?>" style="width: <?= $pay->pct; ?>%;"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-sales-empty-state">
                  <i class="fa fa-credit-card"></i>
                  <span class="spark-sales-empty-title"><?= lang('no_sales_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       5. ROW 3: TOP SELLING PRODUCTS & TOP CUSTOMERS (2 COLUMNS)
       ========================================================================= -->
  <?php
    $show_top_prods = $can_view_sales;
    $show_top_custs = ($can_view_sales || $can_view_customers);
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-sales-grid-row">
      <!-- Left: Top Selling Products -->
      <?php if ($show_top_prods): ?>
        <div class="<?= $show_top_custs ? 'spark-sales-col-6' : 'spark-sales-col-12'; ?>">
          <div class="spark-sales-card">
            <div class="spark-sales-card-header">
              <h3 class="spark-sales-card-title">
                <i class="fa fa-trophy" style="color:#f59e0b;"></i>
                <?= lang('top_selling_products'); ?>
              </h3>
              <span class="spark-sales-kpi-badge badge-amber"><?= lang('top_5'); ?></span>
            </div>
            <div class="spark-sales-card-body">
              <?php if (!empty($top_products)) { ?>
                <div class="spark-sales-ranked-list">
                  <?php
                  $p_rank = 1;
                  foreach ($top_products as $prod) {
                    $rank_cls = ($p_rank == 1) ? 'rank-1' : (($p_rank == 2) ? 'rank-2' : (($p_rank == 3) ? 'rank-3' : ''));
                  ?>
                    <div class="spark-sales-ranked-item">
                      <div class="spark-sales-ranked-left">
                        <div class="spark-sales-rank-num <?= $rank_cls; ?>"><?= $p_rank; ?></div>
                        <div>
                          <div class="spark-sales-ranked-name"><?= htmlspecialchars($prod->name); ?></div>
                          <div class="spark-sales-ranked-code"><?= htmlspecialchars($prod->code); ?> &bull; <?= number_format($prod->sold_qty); ?> <?= $prod->unit_name ?? ''; ?></div>
                        </div>
                      </div>
                      <div class="spark-sales-ranked-right">
                        <span class="spark-sales-ranked-val"><?= $this->sma->formatMoney($prod->sales_amount); ?></span>
                        <?php if ($can_view_cost): ?>
                          <span class="spark-sales-ranked-sub" style="color:#10b981;"><?= lang('profit'); ?>: <?= $this->sma->formatMoney($prod->profit); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php
                    $p_rank++;
                  } ?>
                </div>
              <?php } else { ?>
                <div class="spark-sales-empty-state">
                  <i class="fa fa-cube"></i>
                  <span class="spark-sales-empty-title"><?= lang('no_sales_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Right: Top Customers -->
      <?php if ($show_top_custs): ?>
        <div class="<?= $show_top_prods ? 'spark-sales-col-6' : 'spark-sales-col-12'; ?>">
          <div class="spark-sales-card">
            <div class="spark-sales-card-header">
              <h3 class="spark-sales-card-title">
                <i class="fa fa-users" style="color:#3b82f6;"></i>
                <?= lang('top_customers'); ?>
              </h3>
              <span class="spark-sales-kpi-badge badge-blue"><?= lang('top_5'); ?></span>
            </div>
            <div class="spark-sales-card-body">
              <?php if (!empty($top_customers)) { ?>
                <div class="spark-sales-ranked-list">
                  <?php
                  $c_rank = 1;
                  foreach ($top_customers as $cust) {
                    $rank_cls = ($c_rank == 1) ? 'rank-1' : (($c_rank == 2) ? 'rank-2' : (($c_rank == 3) ? 'rank-3' : ''));
                    $cust_title = !empty($cust->company) ? $cust->company : (!empty($cust->name) ? $cust->name : lang('customer'));
                  ?>
                    <div class="spark-sales-ranked-item">
                      <div class="spark-sales-ranked-left">
                        <div class="spark-sales-rank-num <?= $rank_cls; ?>"><?= $c_rank; ?></div>
                        <div>
                          <div class="spark-sales-ranked-name">
                            <?php if ($can_view_reports || $can_view_customers): ?>
                              <a href="<?= admin_url('sales/customer_report/' . $cust->id); ?>" style="color:inherit; text-decoration:none;">
                                <?= htmlspecialchars($cust_title); ?>
                              </a>
                            <?php else: ?>
                              <?= htmlspecialchars($cust_title); ?>
                            <?php endif; ?>
                          </div>
                          <div class="spark-sales-ranked-code"><?= number_format($cust->invoice_count); ?> <?= lang('invoices'); ?></div>
                        </div>
                      </div>
                      <div class="spark-sales-ranked-right">
                        <span class="spark-sales-ranked-val"><?= $this->sma->formatMoney($cust->total_sales); ?></span>
                        <span class="spark-sales-ranked-sub" style="color:#10b981;"><?= lang('paid'); ?>: <?= $this->sma->formatMoney($cust->total_paid); ?></span>
                      </div>
                    </div>
                  <?php
                    $c_rank++;
                  } ?>
                </div>
              <?php } else { ?>
                <div class="spark-sales-empty-state">
                  <i class="fa fa-users"></i>
                  <span class="spark-sales-empty-title"><?= lang('no_sales_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       6. ROW 4: ATTENTION REQUIRED & WAREHOUSE SALES (2 COLUMNS)
       ========================================================================= -->
  <?php
    $show_attention = ($can_view_sales || $can_view_payments);
    $show_wh_sales  = $can_view_sales;
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-sales-grid-row">
      <!-- Left: Attention Required (Overdue & Unpaid) -->
      <?php if ($show_attention): ?>
        <div class="<?= $show_wh_sales ? 'spark-sales-col-6' : 'spark-sales-col-12'; ?>">
          <div class="spark-sales-card">
            <div class="spark-sales-card-header">
              <h3 class="spark-sales-card-title">
                <i class="fa fa-exclamation-triangle" style="color:#f43f5e;"></i>
                <?= lang('attention_required'); ?>
              </h3>
              <span class="spark-sales-kpi-badge badge-rose"><?= lang('alerts'); ?></span>
            </div>
            <div class="spark-sales-card-body">
              <div class="spark-sales-attention-grid">
                <!-- Alert 1: Overdue -->
                <div class="spark-sales-attention-card att-danger">
                  <div class="spark-sales-attention-icon">
                    <i class="fa fa-calendar-times-o"></i>
                  </div>
                  <div class="spark-sales-attention-info">
                    <span class="spark-sales-attention-val"><?= number_format($attention_items['overdue_count'] ?? 0); ?></span>
                    <span class="spark-sales-attention-lbl"><?= lang('overdue_invoices'); ?></span>
                    <span style="font-size:11px; font-weight:700;"><?= $this->sma->formatMoney($attention_items['overdue_amount'] ?? 0); ?></span>
                  </div>
                </div>

                <!-- Alert 2: Unpaid -->
                <div class="spark-sales-attention-card att-warning">
                  <div class="spark-sales-attention-icon">
                    <i class="fa fa-clock-o"></i>
                  </div>
                  <div class="spark-sales-attention-info">
                    <span class="spark-sales-attention-val"><?= number_format($attention_items['unpaid_count'] ?? 0); ?></span>
                    <span class="spark-sales-attention-lbl"><?= lang('unpaid_invoices'); ?></span>
                    <span style="font-size:11px; font-weight:700;"><?= $this->sma->formatMoney($attention_items['unpaid_amount'] ?? 0); ?></span>
                  </div>
                </div>

                <!-- Alert 3: Partial -->
                <div class="spark-sales-attention-card att-info">
                  <div class="spark-sales-attention-icon">
                    <i class="fa fa-adjust"></i>
                  </div>
                  <div class="spark-sales-attention-info">
                    <span class="spark-sales-attention-val"><?= number_format($attention_items['partial_count'] ?? 0); ?></span>
                    <span class="spark-sales-attention-lbl"><?= lang('partial_payments'); ?></span>
                    <span style="font-size:11px; font-weight:700;"><?= $this->sma->formatMoney($attention_items['partial_amount'] ?? 0); ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Right: Warehouse Sales Distribution -->
      <?php if ($show_wh_sales): ?>
        <div class="<?= $show_attention ? 'spark-sales-col-6' : 'spark-sales-col-12'; ?>">
          <div class="spark-sales-card">
            <div class="spark-sales-card-header">
              <h3 class="spark-sales-card-title">
                <i class="fa fa-building-o" style="color:#8b5cf6;"></i>
                <?= lang('warehouse_sales'); ?>
              </h3>
              <span class="spark-sales-kpi-badge badge-purple"><?= count($warehouse_distribution ?? []); ?> <?= lang('warehouses'); ?></span>
            </div>
            <div class="spark-sales-card-body">
              <?php if (!empty($warehouse_distribution)) { ?>
                <div class="spark-sales-status-list">
                  <?php
                  $wh_count = 0;
                  foreach ($warehouse_distribution as $wh_sale) {
                    if ($wh_count >= 5 && (float)$wh_sale->total_sales == 0) continue;
                    $wh_count++;
                  ?>
                    <div class="spark-sales-status-item">
                      <div class="spark-sales-status-header">
                        <span class="spark-sales-status-label">
                          <i class="fa fa-building" style="color:#8b5cf6;"></i>
                          <?= htmlspecialchars($wh_sale->name); ?>
                        </span>
                        <span class="spark-sales-status-count">
                          <?= $this->sma->formatMoney($wh_sale->total_sales); ?> (<?= $wh_sale->pct; ?>%)
                        </span>
                      </div>
                      <div class="spark-sales-progress-track">
                        <div class="spark-sales-progress-fill fill-purple" style="width: <?= $wh_sale->pct; ?>%;"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-sales-empty-state">
                  <i class="fa fa-building"></i>
                  <span class="spark-sales-empty-title"><?= lang('no_sales_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       7. ROW 5: RECENT SALES INVOICES TABLE (1 COLUMN)
       ========================================================================= -->
  <?php if ($Owner || $Admin): ?>
    <div class="spark-sales-grid-row">
      <div class="spark-sales-col-12">
        <div class="spark-sales-card">
          <div class="spark-sales-card-header">
            <h3 class="spark-sales-card-title">
              <i class="fa fa-history" style="color:var(--spark-primary, #6366f1);"></i>
              <?= lang('recent_sales'); ?>
            </h3>
            <a href="<?= admin_url('sales'); ?>" class="spark-btn spark-btn-outline spark-btn-sm" style="height:30px; border-radius:var(--spark-radius-md, 8px); padding-inline:10px; font-size:12px; text-decoration:none;">
              <i class="fa fa-list"></i> <?= lang('view_all'); ?>
            </a>
          </div>
          <div class="spark-sales-card-body" style="padding:0;">
            <?php if (!empty($recent_sales)) { ?>
              <div class="spark-sales-table-responsive">
                <table class="spark-sales-table">
                  <thead>
                    <tr>
                      <th><?= lang('reference_no'); ?></th>
                      <th><?= lang('customer'); ?></th>
                      <th><?= lang('date'); ?></th>
                      <th><?= lang('warehouse'); ?></th>
                      <th><?= lang('grand_total'); ?></th>
                      <th><?= lang('paid'); ?></th>
                      <th><?= lang('balance'); ?></th>
                      <th><?= lang('payment_status'); ?></th>
                      <?php if ($can_view_sales || $can_edit_sale || $can_view_payments): ?>
                        <th><?= lang('actions'); ?></th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_sales as $sale) {
                      $p_badge = 'badge-slate';
                      if ($sale->payment_status == 'paid') {
                          $p_badge = 'badge-emerald';
                      } elseif ($sale->payment_status == 'partial') {
                          $p_badge = 'badge-cyan';
                      } elseif ($sale->payment_status == 'due' || $sale->payment_status == 'unpaid') {
                          $p_badge = 'badge-rose';
                      } elseif ($sale->payment_status == 'pending') {
                          $p_badge = 'badge-amber';
                      }
                    ?>
                      <tr>
                        <td style="font-weight:700; font-family:var(--spark-font-mono, monospace);">
                          <?= htmlspecialchars($sale->reference_no); ?>
                        </td>
                        <td><?= htmlspecialchars($sale->customer); ?></td>
                        <td style="color:var(--spark-text-secondary, #64748b); font-size:12px;">
                          <?= date('Y-m-d H:i', strtotime($sale->date)); ?>
                        </td>
                        <td><?= htmlspecialchars($sale->warehouse_name ?? '-'); ?></td>
                        <td style="font-weight:700;"><?= $this->sma->formatMoney($sale->grand_total); ?></td>
                        <td style="color:#10b981; font-weight:600;"><?= $this->sma->formatMoney($sale->paid); ?></td>
                        <td style="color:<?= ($sale->due > 0) ? '#f43f5e' : 'inherit'; ?>; font-weight:600;">
                          <?= $this->sma->formatMoney($sale->due); ?>
                        </td>
                        <td>
                          <span class="spark-sales-kpi-badge <?= $p_badge; ?>">
                            <?= lang($sale->payment_status) ? lang($sale->payment_status) : ucfirst($sale->payment_status); ?>
                          </span>
                        </td>
                        <?php if ($can_view_sales || $can_edit_sale || $can_view_payments): ?>
                          <td style="text-align:center;">
                            <div class="btn-group">
                              <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                <?= lang('actions'); ?> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?>" role="menu">
                                <?php if ($can_view_sales): ?>
                                  <li><a href="<?= admin_url('sales/modal_view/' . $sale->id); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> <?= lang('sale_details'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($can_edit_sale): ?>
                                  <li><a href="<?= admin_url('sales/edit/' . $sale->id); ?>"><i class="fa fa-edit"></i> <?= lang('edit_sale'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($can_view_payments): ?>
                                  <li><a href="<?= admin_url('sales/payments/' . $sale->id); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-money"></i> <?= lang('view_payments'); ?></a></li>
                                <?php endif; ?>
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
              <div class="spark-sales-empty-state">
                <i class="fa fa-file-text-o"></i>
                <span class="spark-sales-empty-title"><?= lang('no_sales_data'); ?></span>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<!-- =========================================================================
     9. ECHARTS INITIALIZATION & SCRIPT
     ========================================================================= -->
<?php
// Prepare ECharts dataset
$chart_months = [];
$chart_sales  = [];
$chart_paid   = [];
$chart_due    = [];
$chart_profit = [];

if (!empty($monthly_trends)) {
    foreach ($monthly_trends as $t) {
        $chart_months[] = $t->month;
        $chart_sales[]  = (float)$t->total_sales;
        $chart_paid[]   = (float)$t->total_paid;
        $chart_due[]    = (float)$t->total_due;
        if ($can_view_cost) {
            $chart_profit[] = (float)($t->profit ?? 0);
        }
    }
}
?>

<!-- ECharts Library Script & Initialization -->
<script src="<?= $assets ?>template1/vendors/echarts/dist/echarts.min.js"></script>
<script type="text/javascript">
function applySalesFilter() {
    var range = document.getElementById('salesDateRangeSelect').value;
    var wh = document.getElementById('salesWarehouseSelect') ? document.getElementById('salesWarehouseSelect').value : '';
    var url = '<?= admin_url('sales/dashboard'); ?>?date_range=' + encodeURIComponent(range);
    if (wh) {
        url += '&warehouse=' + encodeURIComponent(wh);
    }
    window.location.href = url;
}

$(document).ready(function() {
    if (typeof echarts === 'undefined') {
        return;
    }

    var chartDom = document.getElementById('sparkSalesTrendChart');
    if (!chartDom) return;

    var myChart = echarts.init(chartDom);

    var isDark = $('html').attr('data-theme') === 'dark';
    var brandPrimary = getComputedStyle(document.documentElement).getPropertyValue('--spark-primary').trim() || '#6366f1';

    var months = <?= json_encode($chart_months); ?>;
    var salesData = <?= json_encode($chart_sales); ?>;
    var paidData = <?= json_encode($chart_paid); ?>;
    var dueData = <?= json_encode($chart_due); ?>;
    var canViewCost = <?= $can_view_cost ? 'true' : 'false'; ?>;
    var profitData = <?= $can_view_cost ? json_encode($chart_profit) : '[]'; ?>;

    var legendItems = [
        '<?= lang('gross_sales'); ?>',
        '<?= lang('total_paid'); ?>',
        '<?= lang('total_due'); ?>'
    ];
    if (canViewCost) {
        legendItems.push('<?= lang('gross_profit'); ?>');
    }

    var seriesList = [
        {
            name: '<?= lang('gross_sales'); ?>',
            type: 'bar',
            data: salesData,
            itemStyle: { color: brandPrimary, borderRadius: [4, 4, 0, 0] }
        },
        {
            name: '<?= lang('total_paid'); ?>',
            type: 'bar',
            data: paidData,
            itemStyle: { color: '#10b981', borderRadius: [4, 4, 0, 0] }
        },
        {
            name: '<?= lang('total_due'); ?>',
            type: 'bar',
            data: dueData,
            itemStyle: { color: '#f59e0b', borderRadius: [4, 4, 0, 0] }
        }
    ];

    if (canViewCost) {
        seriesList.push({
            name: '<?= lang('gross_profit'); ?>',
            type: 'line',
            smooth: true,
            data: profitData,
            itemStyle: { color: '#f43f5e' },
            lineStyle: { width: 3 }
        });
    }

    var option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                crossStyle: { color: isDark ? '#94a3b8' : '#64748b' }
            },
            backgroundColor: isDark ? '#1e293b' : '#ffffff',
            borderColor: isDark ? '#334155' : '#e2e8f0',
            textStyle: { color: isDark ? '#f8fafc' : '#0f172a' }
        },
        legend: {
            data: legendItems,
            textStyle: { color: isDark ? '#94a3b8' : '#64748b' },
            bottom: 0
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            top: '8%',
            containLabel: true
        },
        xAxis: [
            {
                type: 'category',
                data: months,
                axisPointer: { type: 'shadow' },
                axisLine: { lineStyle: { color: isDark ? '#334155' : '#e2e8f0' } },
                axisLabel: { color: isDark ? '#94a3b8' : '#64748b' }
            }
        ],
        yAxis: [
            {
                type: 'value',
                axisLine: { lineStyle: { color: isDark ? '#334155' : '#e2e8f0' } },
                splitLine: { lineStyle: { color: isDark ? '#1e293b' : '#f1f5f9' } },
                axisLabel: { color: isDark ? '#94a3b8' : '#64748b' }
            }
        ],
        series: seriesList
    };

    myChart.setOption(option);

    window.addEventListener('resize', function() {
        if (myChart) {
            myChart.resize();
        }
    });
});
</script>
