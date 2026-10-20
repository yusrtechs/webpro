<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Define Permission Helper Flags from native Spark ERP permission model
$can_view_purchases = ($Owner || $Admin || !empty($GP['purchases-index']));
$can_add_purchase   = ($Owner || $Admin || !empty($GP['purchases-add']));
$can_edit_purchase  = ($Owner || $Admin);
$can_view_payments  = ($Owner || $Admin);
$can_view_returns   = ($Owner || $Admin);
$can_view_expenses  = ($Owner || $Admin);
$can_view_suppliers = ($Owner || $Admin);
$can_view_reports   = ($Owner || $Admin);
$can_view_exp_report= ($Owner || $Admin);
$can_view_cost      = ($Owner || $Admin);

// Build Quick Actions dynamically
$quick_actions = [
    [
        'title' => lang('add_purchase'),
        'desc'  => lang('create_new_purchase'),
        'url'   => admin_url('purchases/add'),
        'icon'  => 'fa-plus-circle',
        'style' => 'background:#f0fdfa; color:#0d9488;',
        'perm'  => $can_add_purchase,
    ],
    [
        'title' => lang('list_purchases'),
        'desc'  => lang('all_purchases'),
        'url'   => admin_url('purchases'),
        'icon'  => 'fa-list',
        'style' => 'background:#eff6ff; color:#3b82f6;',
        'perm'  => $can_view_purchases,
    ],
    [
        'title' => lang('suppliers'),
        'desc'  => lang('supplier_directory'),
        'url'   => admin_url('suppliers'),
        'icon'  => 'fa-truck',
        'style' => 'background:#ecfdf5; color:#10b981;',
        'perm'  => $can_view_suppliers,
    ],
    [
        'title' => lang('payments'),
        'desc'  => lang('received_payments'),
        'url'   => admin_url('purchases/payments_report'),
        'icon'  => 'fa-money',
        'style' => 'background:#ecfeff; color:#06b6d4;',
        'perm'  => $can_view_payments,
    ],
    [
        'title' => lang('purchase_returns'),
        'desc'  => lang('credit_notes'),
        'url'   => admin_url('purchases/returns'),
        'icon'  => 'fa-reply',
        'style' => 'background:#fff1f2; color:#f43f5e;',
        'perm'  => $can_view_returns,
    ],
    [
        'title' => lang('expenses'),
        'desc'  => lang('expenses_management'),
        'url'   => admin_url('purchases/expenses'),
        'icon'  => 'fa-calculator',
        'style' => 'background:#f5f3ff; color:#8b5cf6;',
        'perm'  => $can_view_expenses,
    ],
    [
        'title' => lang('purchase_reports'),
        'desc'  => lang('purchase_analytics'),
        'url'   => admin_url('purchases/reports'),
        'icon'  => 'fa-bar-chart',
        'style' => 'background:#fffbeb; color:#f59e0b;',
        'perm'  => $can_view_reports,
    ],
    [
        'title' => lang('expenses_report'),
        'desc'  => lang('reports'),
        'url'   => admin_url('purchases/expenses_report'),
        'icon'  => 'fa-file-text-o',
        'style' => 'background: var(--spark-primary-subtle, #eef2ff); color: var(--spark-primary, #6366f1);',
        'perm'  => $can_view_exp_report,
    ],
];
$quick_actions = array_filter($quick_actions, function($qa) { return !empty($qa['perm']); });

// Build Header Dropdown Actions dynamically
$header_actions = [];
if ($can_view_purchases) {
    $header_actions[] = ['url' => admin_url('purchases'), 'icon' => 'fa-list', 'label' => lang('list_purchases')];
}
if ($can_view_returns) {
    $header_actions[] = ['url' => admin_url('purchases/returns'), 'icon' => 'fa-reply', 'label' => lang('purchase_returns')];
}
if ($can_view_payments) {
    $header_actions[] = ['url' => admin_url('purchases/payments_report'), 'icon' => 'fa-money', 'label' => lang('payments')];
}
if ($can_view_expenses) {
    $header_actions[] = ['url' => admin_url('purchases/expenses'), 'icon' => 'fa-calculator', 'label' => lang('expenses')];
}
if ($can_view_suppliers) {
    $header_actions[] = ['url' => admin_url('suppliers'), 'icon' => 'fa-truck', 'label' => lang('suppliers')];
}
if ($can_view_reports) {
    $header_actions[] = ['url' => admin_url('purchases/reports'), 'icon' => 'fa-bar-chart', 'label' => lang('purchase_reports')];
}
?>

<div class="spark-purchases-dashboard">

  <!-- =========================================================================
       1. PAGE HEADER
       ========================================================================= -->
  <div class="spark-purchases-header">
    <div class="spark-purchases-header-start">
      <div class="spark-purchases-header-icon">
        <i class="fa fa-shopping-cart"></i>
      </div>
      <div>
        <h1 class="spark-purchases-title"><?= lang('purchases_dashboard'); ?></h1>
        <p class="spark-purchases-subtitle"><?= lang('manage_purchases_desc'); ?></p>
      </div>
    </div>

    <div class="spark-purchases-header-actions">
      <!-- Date Range Filter -->
      <select class="spark-purchases-select" id="purchasesDateRangeSelect" onchange="applyPurchasesFilter()">
        <option value="all" <?= ($date_range == 'all') ? 'selected' : ''; ?>><?= lang('all_time'); ?></option>
        <option value="today" <?= ($date_range == 'today') ? 'selected' : ''; ?>><?= lang('today'); ?></option>
        <option value="this_week" <?= ($date_range == 'this_week') ? 'selected' : ''; ?>><?= lang('this_week'); ?></option>
        <option value="this_month" <?= ($date_range == 'this_month') ? 'selected' : ''; ?>><?= lang('this_month'); ?></option>
        <option value="this_year" <?= ($date_range == 'this_year') ? 'selected' : ''; ?>><?= lang('this_year'); ?></option>
      </select>

      <!-- Warehouse Selector -->
      <?php if (!empty($warehouses) && ($Owner || $Admin || empty($this->session->userdata('warehouse_id')))) { ?>
        <select class="spark-purchases-select" id="purchasesWarehouseSelect" onchange="applyPurchasesFilter()">
          <option value=""><?= lang('all_warehouses'); ?></option>
          <?php foreach ($warehouses as $wh) { ?>
            <option value="<?= $wh->id; ?>" <?= ($warehouse_id == $wh->id) ? 'selected' : ''; ?>>
              <?= htmlspecialchars($wh->name); ?>
            </option>
          <?php } ?>
        </select>
      <?php } ?>

      <!-- Current Date Pill -->
      <div class="spark-purchases-select" style="display:inline-flex; align-items:center; gap:6px; cursor:default; background-color:var(--spark-surface-card, #ffffff);">
        <i class="fa fa-calendar-o" style="color:var(--spark-primary, #0d9488);"></i>
        <span><?= date('Y-m-d'); ?></span>
      </div>

      <!-- Action: Print Dashboard -->
      <button type="button" class="spark-btn spark-btn-outline spark-btn-sm" onclick="window.print();">
        <i class="fa fa-print"></i> <?= lang('print_dashboard'); ?>
      </button>

      <!-- Action: Add Purchase -->
      <?php if ($can_add_purchase): ?>
        <a href="<?= admin_url('purchases/add'); ?>" class="spark-btn spark-btn-primary spark-btn-sm" style="display:inline-flex; align-items:center; gap:6px; height:34px; border-radius:var(--spark-radius-md, 8px); padding-inline:14px; font-weight:600; text-decoration:none;">
          <i class="fa fa-plus"></i> <?= lang('add_purchase'); ?>
        </a>
      <?php endif; ?>

      <!-- Action: Payments -->
      <?php if ($can_view_payments): ?>
        <a href="<?= admin_url('purchases/payments_report'); ?>" class="spark-btn spark-btn-outline spark-btn-sm" style="display:inline-flex; align-items:center; gap:6px; height:34px; border-radius:var(--spark-radius-md, 8px); padding-inline:14px; font-weight:600; text-decoration:none;">
          <i class="fa fa-money"></i> <?= lang('payments'); ?>
        </a>
      <?php endif; ?>

      <!-- Actions Dropdown -->
      <?php if (!empty($header_actions)): ?>
        <div class="btn-group">
          <button type="button" class="spark-btn spark-btn-outline spark-btn-sm dropdown-toggle" data-toggle="dropdown" style="height:34px; border-radius:var(--spark-radius-md, 8px); padding-inline:12px;">
            <i class="fa fa-ellipsis-v"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-<?= ($Settings->user_language == 'arabic') ? 'left' : 'right'; ?>" role="menu" style="border-radius:var(--spark-radius-md, 8px); box-shadow:0 10px 25px rgba(0,0,0,0.1); border:1px solid var(--spark-border-default, #e2e8f0);">
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
    <div class="spark-purchases-grid-row">
      <div class="spark-purchases-col-12">
        <div class="spark-purchases-card">
          <div class="spark-purchases-card-header">
            <h3 class="spark-purchases-card-title">
              <i class="fa fa-bolt" style="color:#f59e0b;"></i>
              <?= lang('quick_actions'); ?>
            </h3>
            <span class="spark-purchases-kpi-badge badge-amber"><?= lang('shortcuts'); ?></span>
          </div>
          <div class="spark-purchases-card-body">
            <div class="spark-purchases-actions-grid">
              <?php foreach ($quick_actions as $qa): ?>
                <a href="<?= $qa['url']; ?>" class="spark-purchases-action-tile">
                  <div class="spark-purchases-action-icon" style="<?= $qa['style']; ?>">
                    <i class="fa <?= $qa['icon']; ?>"></i>
                  </div>
                  <div class="spark-purchases-action-info">
                    <span class="spark-purchases-action-title"><?= $qa['title']; ?></span>
                    <span class="spark-purchases-action-desc"><?= $qa['desc']; ?></span>
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
  <div class="spark-purchases-kpi-grid">
    <?php if ($Owner || $Admin): ?>
      <!-- Card 1: Total Purchases -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-indigo">
            <i class="fa fa-shopping-cart"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-indigo">
            <?= lang('total_purchases'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val"><?= $this->sma->formatMoney($stats['total_purchases'] ?? 0); ?></span>
          <span class="spark-purchases-kpi-lbl"><?= lang('total_purchases'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <span><?= lang('invoices'); ?>: <strong><?= number_format($stats['total_invoices'] ?? 0); ?></strong></span>
          <span><i class="fa fa-file-text-o"></i> <?= lang('active'); ?></span>
        </div>
      </div>

      <!-- Card 2: Net Purchases -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-blue">
            <i class="fa fa-calculator"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-blue">
            <?= lang('net_purchases'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val"><?= $this->sma->formatMoney($stats['net_purchases'] ?? 0); ?></span>
          <span class="spark-purchases-kpi-lbl"><?= lang('net_purchases'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <span><?= lang('tax'); ?>: <strong><?= $this->sma->formatMoney($stats['total_tax'] ?? 0); ?></strong></span>
          <span><?= lang('discount'); ?>: <strong><?= $this->sma->formatMoney($stats['total_discount'] ?? 0); ?></strong></span>
        </div>
      </div>

      <!-- Card 3: Total Invoices -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-purple">
            <i class="fa fa-files-o"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-purple">
            <?= lang('purchase_volume'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val"><?= number_format($stats['total_invoices'] ?? 0); ?></span>
          <span class="spark-purchases-kpi-lbl"><?= lang('total_invoices'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <span><?= lang('suppliers'); ?>: <strong><?= number_format($stats['total_suppliers'] ?? 0); ?></strong></span>
          <span><?= lang('paid'); ?>: <strong><?= number_format($stats['paid_invoices'] ?? 0); ?></strong></span>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($Owner || $Admin): ?>
      <!-- Card 4: Total Paid -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-emerald">
            <i class="fa fa-check-circle-o"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-emerald">
            <i class="fa fa-arrow-up"></i> <?= lang('paid'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val" style="color:#10b981;"><?= $this->sma->formatMoney($stats['total_paid'] ?? 0); ?></span>
          <span class="spark-purchases-kpi-lbl"><?= lang('total_paid'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <?php 
            $paid_rate = (($stats['total_purchases'] ?? 0) > 0) ? round((($stats['total_paid'] ?? 0) / $stats['total_purchases']) * 100, 1) : 0;
          ?>
          <span><?= lang('payment_rate'); ?>: <strong><?= $paid_rate; ?>%</strong></span>
          <span><i class="fa fa-check"></i></span>
        </div>
      </div>

      <!-- Card 5: Total Due -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-amber">
            <i class="fa fa-clock-o"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-amber">
            <?= lang('due'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val" style="color:#f59e0b;"><?= $this->sma->formatMoney($stats['total_due'] ?? 0); ?></span>
          <span class="spark-purchases-kpi-lbl"><?= lang('total_due'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <span><?= lang('unpaid'); ?>: <strong><?= number_format($stats['unpaid_invoices'] ?? 0); ?></strong></span>
          <span><?= lang('partial'); ?>: <strong><?= number_format($stats['partial_invoices'] ?? 0); ?></strong></span>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($Owner || $Admin): ?>
      <!-- Card 6: Purchase Cost -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-rose">
            <i class="fa fa-cubes"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-rose">
            <?= lang('item_cost'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val"><?= $this->sma->formatMoney($stats['total_item_cost'] ?? 0); ?></span>
          <span class="spark-purchases-kpi-lbl"><?= lang('total_purchases_cost'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <span><?= lang('shipping'); ?>: <strong><?= $this->sma->formatMoney($stats['total_shipping'] ?? 0); ?></strong></span>
          <span><i class="fa fa-truck"></i></span>
        </div>
      </div>

      <!-- Card 7: Average Purchase -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-cyan">
            <i class="fa fa-balance-scale"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-cyan">
            <?= lang('average'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val"><?= $this->sma->formatMoney($stats['average_purchase_value'] ?? 0); ?></span>
          <span class="spark-purchases-kpi-lbl"><?= lang('average_purchase_value'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <span><?= lang('received'); ?>: <strong><?= number_format($stats['received_invoices'] ?? 0); ?></strong></span>
          <span><i class="fa fa-check-square-o"></i></span>
        </div>
      </div>

      <!-- Card 8: Return Rate -->
      <div class="spark-purchases-kpi-card">
        <div class="spark-purchases-kpi-top">
          <div class="spark-purchases-kpi-icon icon-teal">
            <i class="fa fa-reply"></i>
          </div>
          <span class="spark-purchases-kpi-badge badge-teal">
            <?= lang('returns'); ?>
          </span>
        </div>
        <div class="spark-purchases-kpi-content">
          <span class="spark-purchases-kpi-val" style="color:<?= (($stats['total_returns'] ?? 0) > 0) ? '#f43f5e' : 'inherit'; ?>;">
            <?= $this->sma->formatMoney($stats['total_returns'] ?? 0); ?>
          </span>
          <span class="spark-purchases-kpi-lbl"><?= lang('purchase_returns'); ?></span>
        </div>
        <div class="spark-purchases-kpi-footer">
          <span><?= lang('return_rate'); ?>: <strong><?= number_format($stats['return_rate'] ?? 0, 1); ?>%</strong></span>
          <span><i class="fa fa-pie-chart"></i></span>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- =========================================================================
       3. ROW 1: PURCHASE PERFORMANCE & STATUS BREAKDOWN (2 COLUMNS)
       ========================================================================= -->
  <?php if ($Owner || $Admin): ?>
    <?php 
      $paid_rate = (($stats['total_purchases'] ?? 0) > 0) ? round((($stats['total_paid'] ?? 0) / $stats['total_purchases']) * 100, 1) : 0;
    ?>
    <div class="spark-purchases-grid-row">
      <!-- Left: Purchase Performance Breakdown -->
      <div class="spark-purchases-col-6">
        <div class="spark-purchases-card">
          <div class="spark-purchases-card-header">
            <h3 class="spark-purchases-card-title">
              <i class="fa fa-pie-chart" style="color:var(--spark-primary, #0d9488);"></i>
              <?= lang('purchase_performance'); ?>
            </h3>
            <span class="spark-purchases-kpi-badge badge-indigo"><?= lang('overview'); ?></span>
          </div>
          <div class="spark-purchases-card-body">
            <div class="spark-purchases-status-item" style="margin-bottom:14px;">
              <div class="spark-purchases-status-header">
                <span class="spark-purchases-status-label"><?= lang('paid_vs_due'); ?></span>
                <span class="spark-purchases-status-count"><?= $paid_rate; ?>% <?= lang('paid'); ?></span>
              </div>
              <div class="spark-purchases-progress-track" style="height:10px;">
                <div class="spark-purchases-progress-fill fill-success" style="width: <?= min(100, max(0, $paid_rate)); ?>%;"></div>
              </div>
            </div>

            <div class="spark-purchases-perf-grid">
              <div class="spark-purchases-perf-box">
                <span class="spark-purchases-perf-lbl"><?= lang('total_purchases'); ?></span>
                <span class="spark-purchases-perf-val" style="color: var(--spark-primary, #0d9488);"><?= $this->sma->formatMoney($stats['total_purchases'] ?? 0); ?></span>
              </div>
              <div class="spark-purchases-perf-box">
                <span class="spark-purchases-perf-lbl"><?= lang('net_purchases'); ?></span>
                <span class="spark-purchases-perf-val" style="color:#3b82f6;"><?= $this->sma->formatMoney($stats['net_purchases'] ?? 0); ?></span>
              </div>
              <div class="spark-purchases-perf-box">
                <span class="spark-purchases-perf-lbl"><?= lang('total_paid'); ?></span>
                <span class="spark-purchases-perf-val" style="color:#10b981;"><?= $this->sma->formatMoney($stats['total_paid'] ?? 0); ?></span>
              </div>
              <div class="spark-purchases-perf-box">
                <span class="spark-purchases-perf-lbl"><?= lang('total_due'); ?></span>
                <span class="spark-purchases-perf-val" style="color:#f59e0b;"><?= $this->sma->formatMoney($stats['total_due'] ?? 0); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Purchase & Payment Status Breakdown -->
      <div class="spark-purchases-col-6">
        <div class="spark-purchases-card">
          <div class="spark-purchases-card-header">
            <h3 class="spark-purchases-card-title">
              <i class="fa fa-tasks" style="color:#10b981;"></i>
              <?= lang('purchase_status'); ?>
            </h3>
            <span class="spark-purchases-kpi-badge badge-emerald"><?= number_format($status_summary['total_invoices'] ?? 0); ?> <?= lang('invoices'); ?></span>
          </div>
          <div class="spark-purchases-card-body">
            <div class="spark-purchases-status-list">
              <!-- Received -->
              <div class="spark-purchases-status-item">
                <div class="spark-purchases-status-header">
                  <span class="spark-purchases-status-label"><i class="fa fa-check-circle" style="color:#10b981;"></i> <?= lang('received'); ?></span>
                  <span class="spark-purchases-status-count"><?= number_format($status_summary['received_count'] ?? 0); ?> (<?= $status_summary['received_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-purchases-progress-track">
                  <div class="spark-purchases-progress-fill fill-success" style="width: <?= $status_summary['received_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>

              <!-- Pending / Ordered -->
              <div class="spark-purchases-status-item">
                <div class="spark-purchases-status-header">
                  <span class="spark-purchases-status-label"><i class="fa fa-hourglass-half" style="color:#f59e0b;"></i> <?= lang('pending'); ?></span>
                  <span class="spark-purchases-status-count"><?= number_format($status_summary['pending_count'] ?? 0); ?> (<?= $status_summary['pending_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-purchases-progress-track">
                  <div class="spark-purchases-progress-fill fill-warning" style="width: <?= $status_summary['pending_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>

              <!-- Paid Status -->
              <div class="spark-purchases-status-item">
                <div class="spark-purchases-status-header">
                  <span class="spark-purchases-status-label"><i class="fa fa-money" style="color:#06b6d4;"></i> <?= lang('paid'); ?></span>
                  <span class="spark-purchases-status-count"><?= number_format($status_summary['paid_count'] ?? 0); ?> (<?= $status_summary['paid_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-purchases-progress-track">
                  <div class="spark-purchases-progress-fill fill-cyan" style="width: <?= $status_summary['paid_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>

              <!-- Due Status -->
              <div class="spark-purchases-status-item">
                <div class="spark-purchases-status-header">
                  <span class="spark-purchases-status-label"><i class="fa fa-exclamation-circle" style="color:#f43f5e;"></i> <?= lang('due'); ?></span>
                  <span class="spark-purchases-status-count"><?= number_format($status_summary['due_count'] ?? 0); ?> (<?= $status_summary['due_pct'] ?? 0; ?>%)</span>
                </div>
                <div class="spark-purchases-progress-track">
                  <div class="spark-purchases-progress-fill fill-danger" style="width: <?= $status_summary['due_pct'] ?? 0; ?>%;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       4. ROW 2: PURCHASE TREND CHART & PAYMENT METHODS (2 COLUMNS)
       ========================================================================= -->
  <?php
    $show_trend = $can_view_purchases;
    $show_payments = ($can_view_purchases || $can_view_payments);
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-purchases-grid-row">
      <!-- Left: ECharts Purchase Movement Trend -->
      <?php if ($show_trend): ?>
        <div class="<?= $show_payments ? 'spark-purchases-col-7' : 'spark-purchases-col-12'; ?>">
          <div class="spark-purchases-card">
            <div class="spark-purchases-card-header">
              <h3 class="spark-purchases-card-title">
                <i class="fa fa-area-chart" style="color:var(--spark-primary, #0d9488);"></i>
                <?= lang('purchase_trend'); ?>
              </h3>
              <span class="spark-purchases-kpi-badge badge-indigo"><?= lang('monthly_purchases'); ?></span>
            </div>
            <div class="spark-purchases-card-body">
              <div id="sparkPurchasesTrendChart" style="width: 100%; height: 320px;"></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Right: Payment Methods -->
      <?php if ($show_payments): ?>
        <div class="<?= $show_trend ? 'spark-purchases-col-5' : 'spark-purchases-col-12'; ?>">
          <div class="spark-purchases-card">
            <div class="spark-purchases-card-header">
              <h3 class="spark-purchases-card-title">
                <i class="fa fa-credit-card" style="color:#0d9488;"></i>
                <?= lang('payment_methods'); ?>
              </h3>
              <span class="spark-purchases-kpi-badge badge-teal"><?= lang('distribution'); ?></span>
            </div>
            <div class="spark-purchases-card-body">
              <?php if (!empty($payment_distribution)) { ?>
                <div class="spark-purchases-status-list">
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
                    <div class="spark-purchases-status-item">
                      <div class="spark-purchases-status-header">
                        <span class="spark-purchases-status-label">
                          <i class="fa <?= $method_icon; ?>" style="color: var(--spark-primary, #0d9488);"></i>
                          <?= lang($pay->payment_method) ? lang($pay->payment_method) : ucfirst(str_replace('_', ' ', $pay->payment_method)); ?>
                        </span>
                        <span class="spark-purchases-status-count">
                          <?= $this->sma->formatMoney($pay->total_amount); ?> (<?= $pay->pct; ?>%)
                        </span>
                      </div>
                      <div class="spark-purchases-progress-track">
                        <div class="spark-purchases-progress-fill <?= $method_class; ?>" style="width: <?= $pay->pct; ?>%;"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-purchases-empty-state">
                  <i class="fa fa-credit-card"></i>
                  <span class="spark-purchases-empty-title"><?= lang('no_purchases_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       5. ROW 3: TOP PURCHASED PRODUCTS & TOP SUPPLIERS (2 COLUMNS)
       ========================================================================= -->
  <?php
    $show_top_prods = $can_view_purchases;
    $show_top_supps = ($can_view_purchases || $can_view_suppliers);
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-purchases-grid-row">
      <!-- Left: Top Purchased Products -->
      <?php if ($show_top_prods): ?>
        <div class="<?= $show_top_supps ? 'spark-purchases-col-6' : 'spark-purchases-col-12'; ?>">
          <div class="spark-purchases-card">
            <div class="spark-purchases-card-header">
              <h3 class="spark-purchases-card-title">
                <i class="fa fa-cubes" style="color:#f59e0b;"></i>
                <?= lang('top_purchased_products'); ?>
              </h3>
              <span class="spark-purchases-kpi-badge badge-amber"><?= lang('top_5'); ?></span>
            </div>
            <div class="spark-purchases-card-body">
              <?php if (!empty($top_products)) { ?>
                <div class="spark-purchases-ranked-list">
                  <?php
                  $p_rank = 1;
                  foreach ($top_products as $prod) {
                    $rank_cls = ($p_rank == 1) ? 'rank-1' : (($p_rank == 2) ? 'rank-2' : (($p_rank == 3) ? 'rank-3' : ''));
                  ?>
                    <div class="spark-purchases-ranked-item">
                      <div class="spark-purchases-ranked-left">
                        <div class="spark-purchases-rank-num <?= $rank_cls; ?>"><?= $p_rank; ?></div>
                        <div>
                          <div class="spark-purchases-ranked-name"><?= htmlspecialchars($prod->name); ?></div>
                          <div class="spark-purchases-ranked-code"><?= htmlspecialchars($prod->code); ?> &bull; <?= number_format($prod->purchased_qty); ?> <?= $prod->unit_name ?? ''; ?></div>
                        </div>
                      </div>
                      <div class="spark-purchases-ranked-right">
                        <span class="spark-purchases-ranked-val"><?= $this->sma->formatMoney($prod->purchase_amount); ?></span>
                        <span class="spark-purchases-ranked-sub" style="color:var(--spark-text-secondary);"><?= lang('avg_cost'); ?>: <?= $this->sma->formatMoney(($prod->purchased_qty > 0) ? ($prod->purchase_amount / $prod->purchased_qty) : 0); ?></span>
                      </div>
                    </div>
                  <?php
                    $p_rank++;
                  } ?>
                </div>
              <?php } else { ?>
                <div class="spark-purchases-empty-state">
                  <i class="fa fa-cube"></i>
                  <span class="spark-purchases-empty-title"><?= lang('no_purchases_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Right: Top Suppliers -->
      <?php if ($show_top_supps): ?>
        <div class="<?= $show_top_prods ? 'spark-purchases-col-6' : 'spark-purchases-col-12'; ?>">
          <div class="spark-purchases-card">
            <div class="spark-purchases-card-header">
              <h3 class="spark-purchases-card-title">
                <i class="fa fa-truck" style="color:#3b82f6;"></i>
                <?= lang('top_suppliers'); ?>
              </h3>
              <span class="spark-purchases-kpi-badge badge-blue"><?= lang('top_5'); ?></span>
            </div>
            <div class="spark-purchases-card-body">
              <?php if (!empty($top_suppliers)) { ?>
                <div class="spark-purchases-ranked-list">
                  <?php
                  $s_rank = 1;
                  foreach ($top_suppliers as $supp) {
                    $rank_cls = ($s_rank == 1) ? 'rank-1' : (($s_rank == 2) ? 'rank-2' : (($s_rank == 3) ? 'rank-3' : ''));
                    $supp_title = !empty($supp->company) ? $supp->company : (!empty($supp->name) ? $supp->name : lang('supplier'));
                  ?>
                    <div class="spark-purchases-ranked-item">
                      <div class="spark-purchases-ranked-left">
                        <div class="spark-purchases-rank-num <?= $rank_cls; ?>"><?= $s_rank; ?></div>
                        <div>
                          <div class="spark-purchases-ranked-name">
                            <?php if ($can_view_reports || $can_view_suppliers): ?>
                              <a href="<?= admin_url('reports/supplier_report/' . $supp->id); ?>" style="color:inherit; text-decoration:none;">
                                <?= htmlspecialchars($supp_title); ?>
                              </a>
                            <?php else: ?>
                              <?= htmlspecialchars($supp_title); ?>
                            <?php endif; ?>
                          </div>
                          <div class="spark-purchases-ranked-code"><?= number_format($supp->invoice_count); ?> <?= lang('invoices'); ?></div>
                        </div>
                      </div>
                      <div class="spark-purchases-ranked-right">
                        <span class="spark-purchases-ranked-val"><?= $this->sma->formatMoney($supp->total_purchases); ?></span>
                        <span class="spark-purchases-ranked-sub" style="color:#10b981;"><?= lang('paid'); ?>: <?= $this->sma->formatMoney($supp->total_paid); ?></span>
                      </div>
                    </div>
                  <?php
                    $s_rank++;
                  } ?>
                </div>
              <?php } else { ?>
                <div class="spark-purchases-empty-state">
                  <i class="fa fa-truck"></i>
                  <span class="spark-purchases-empty-title"><?= lang('no_purchases_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       6. ROW 4: ATTENTION REQUIRED & WAREHOUSE PURCHASES (2 COLUMNS)
       ========================================================================= -->
  <?php
    $show_attention = ($can_view_purchases || $can_view_payments);
    $show_wh_purch  = $can_view_purchases;
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-purchases-grid-row">
      <!-- Left: Attention Required (Overdue & Unpaid) -->
      <?php if ($show_attention): ?>
        <div class="<?= $show_wh_purch ? 'spark-purchases-col-6' : 'spark-purchases-col-12'; ?>">
          <div class="spark-purchases-card">
            <div class="spark-purchases-card-header">
              <h3 class="spark-purchases-card-title">
                <i class="fa fa-exclamation-triangle" style="color:#f43f5e;"></i>
                <?= lang('attention_required'); ?>
              </h3>
              <span class="spark-purchases-kpi-badge badge-rose"><?= lang('alerts'); ?></span>
            </div>
            <div class="spark-purchases-card-body">
              <div class="spark-purchases-attention-grid">
                <!-- Alert 1: Overdue -->
                <div class="spark-purchases-attention-card att-danger">
                  <div class="spark-purchases-attention-icon">
                    <i class="fa fa-calendar-times-o"></i>
                  </div>
                  <div class="spark-purchases-attention-info">
                    <span class="spark-purchases-attention-val"><?= number_format($attention_items['overdue_count'] ?? 0); ?></span>
                    <span class="spark-purchases-attention-lbl"><?= lang('overdue_invoices'); ?></span>
                    <span style="font-size:11px; font-weight:700;"><?= $this->sma->formatMoney($attention_items['overdue_amount'] ?? 0); ?></span>
                  </div>
                </div>

                <!-- Alert 2: Unpaid -->
                <div class="spark-purchases-attention-card att-warning">
                  <div class="spark-purchases-attention-icon">
                    <i class="fa fa-clock-o"></i>
                  </div>
                  <div class="spark-purchases-attention-info">
                    <span class="spark-purchases-attention-val"><?= number_format($attention_items['unpaid_count'] ?? 0); ?></span>
                    <span class="spark-purchases-attention-lbl"><?= lang('unpaid_invoices'); ?></span>
                    <span style="font-size:11px; font-weight:700;"><?= $this->sma->formatMoney($attention_items['unpaid_amount'] ?? 0); ?></span>
                  </div>
                </div>

                <!-- Alert 3: Partial -->
                <div class="spark-purchases-attention-card att-info">
                  <div class="spark-purchases-attention-icon">
                    <i class="fa fa-adjust"></i>
                  </div>
                  <div class="spark-purchases-attention-info">
                    <span class="spark-purchases-attention-val"><?= number_format($attention_items['partial_count'] ?? 0); ?></span>
                    <span class="spark-purchases-attention-lbl"><?= lang('partial_payments'); ?></span>
                    <span style="font-size:11px; font-weight:700;"><?= $this->sma->formatMoney($attention_items['partial_amount'] ?? 0); ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Right: Warehouse Purchase Distribution -->
      <?php if ($show_wh_purch): ?>
        <div class="<?= $show_attention ? 'spark-purchases-col-6' : 'spark-purchases-col-12'; ?>">
          <div class="spark-purchases-card">
            <div class="spark-purchases-card-header">
              <h3 class="spark-purchases-card-title">
                <i class="fa fa-building-o" style="color:#8b5cf6;"></i>
                <?= lang('warehouse_purchases'); ?>
              </h3>
              <span class="spark-purchases-kpi-badge badge-purple"><?= count($warehouse_distribution ?? []); ?> <?= lang('warehouses'); ?></span>
            </div>
            <div class="spark-purchases-card-body">
              <?php if (!empty($warehouse_distribution)) { ?>
                <div class="spark-purchases-status-list">
                  <?php
                  $wh_count = 0;
                  foreach ($warehouse_distribution as $wh_purch) {
                    if ($wh_count >= 5 && (float)$wh_purch->total_purchases == 0) continue;
                    $wh_count++;
                  ?>
                    <div class="spark-purchases-status-item">
                      <div class="spark-purchases-status-header">
                        <span class="spark-purchases-status-label">
                          <i class="fa fa-building" style="color:#8b5cf6;"></i>
                          <?= htmlspecialchars($wh_purch->name); ?>
                        </span>
                        <span class="spark-purchases-status-count">
                          <?= $this->sma->formatMoney($wh_purch->total_purchases); ?> (<?= $wh_purch->pct; ?>%)
                        </span>
                      </div>
                      <div class="spark-purchases-progress-track">
                        <div class="spark-purchases-progress-fill fill-purple" style="width: <?= $wh_purch->pct; ?>%;"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-purchases-empty-state">
                  <i class="fa fa-building"></i>
                  <span class="spark-purchases-empty-title"><?= lang('no_purchases_data'); ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       7. ROW 5: RECENT PURCHASES INVOICES TABLE (1 COLUMN)
       ========================================================================= -->
  <?php if ($Owner || $Admin): ?>
    <div class="spark-purchases-grid-row">
      <div class="spark-purchases-col-12">
        <div class="spark-purchases-card">
          <div class="spark-purchases-card-header">
            <h3 class="spark-purchases-card-title">
              <i class="fa fa-history" style="color:var(--spark-primary, #0d9488);"></i>
              <?= lang('recent_purchases'); ?>
            </h3>
            <a href="<?= admin_url('purchases'); ?>" class="spark-btn spark-btn-outline spark-btn-sm" style="height:30px; border-radius:var(--spark-radius-md, 8px); padding-inline:10px; font-size:12px; text-decoration:none;">
              <i class="fa fa-list"></i> <?= lang('view_all'); ?>
            </a>
          </div>
          <div class="spark-purchases-card-body" style="padding:0;">
            <?php if (!empty($recent_purchases)) { ?>
              <div class="spark-purchases-table-responsive">
                <table class="spark-purchases-table">
                  <thead>
                    <tr>
                      <th><?= lang('reference_no'); ?></th>
                      <th><?= lang('supplier'); ?></th>
                      <th><?= lang('date'); ?></th>
                      <th><?= lang('warehouse'); ?></th>
                      <th><?= lang('grand_total'); ?></th>
                      <th><?= lang('paid'); ?></th>
                      <th><?= lang('balance'); ?></th>
                      <th><?= lang('payment_status'); ?></th>
                      <th><?= lang('status'); ?></th>
                      <?php if ($can_view_purchases || $can_edit_purchase || $can_view_payments): ?>
                        <th><?= lang('actions'); ?></th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_purchases as $purchase) { 
                      $p_badge = 'badge-slate';
                      if ($purchase->payment_status == 'paid') {
                          $p_badge = 'badge-emerald';
                      } elseif ($purchase->payment_status == 'partial') {
                          $p_badge = 'badge-cyan';
                      } elseif ($purchase->payment_status == 'due' || $purchase->payment_status == 'unpaid') {
                          $p_badge = 'badge-rose';
                      } elseif ($purchase->payment_status == 'pending') {
                          $p_badge = 'badge-amber';
                      }

                      $s_badge = 'badge-slate';
                      if ($purchase->status == 'received' || $purchase->status == 'completed') {
                          $s_badge = 'badge-emerald';
                      } elseif ($purchase->status == 'ordered' || $purchase->status == 'pending') {
                          $s_badge = 'badge-blue';
                      } elseif ($purchase->status == 'returned') {
                          $s_badge = 'badge-rose';
                      }
                    ?>
                      <tr>
                        <td style="font-weight:700; font-family:var(--spark-font-mono, monospace);">
                          <?= htmlspecialchars($purchase->reference_no); ?>
                        </td>
                        <td><?= htmlspecialchars($purchase->supplier); ?></td>
                        <td style="color:var(--spark-text-secondary, #64748b); font-size:12px;">
                          <?= date('Y-m-d H:i', strtotime($purchase->date)); ?>
                        </td>
                        <td><?= htmlspecialchars($purchase->warehouse_name ?? '-'); ?></td>
                        <td style="font-weight:700;"><?= $this->sma->formatMoney($purchase->grand_total); ?></td>
                        <td style="color:#10b981; font-weight:600;"><?= $this->sma->formatMoney($purchase->paid); ?></td>
                        <td style="color:<?= ($purchase->due > 0) ? '#f43f5e' : 'inherit'; ?>; font-weight:600;">
                          <?= $this->sma->formatMoney($purchase->due); ?>
                        </td>
                        <td>
                          <span class="spark-purchases-kpi-badge <?= $p_badge; ?>">
                            <?= lang($purchase->payment_status) ? lang($purchase->payment_status) : ucfirst($purchase->payment_status); ?>
                          </span>
                        </td>
                        <td>
                          <span class="spark-purchases-kpi-badge <?= $s_badge; ?>">
                            <?= lang($purchase->status) ? lang($purchase->status) : ucfirst($purchase->status); ?>
                          </span>
                        </td>
                        <?php if ($can_view_purchases || $can_edit_purchase || $can_view_payments): ?>
                          <td style="text-align:center;">
                            <div class="btn-group">
                              <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                <?= lang('actions'); ?> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?>" role="menu">
                                <?php if ($can_view_purchases): ?>
                                  <li><a href="<?= admin_url('purchases/modal_view/' . $purchase->id); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> <?= lang('purchase_details'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($can_edit_purchase): ?>
                                  <li><a href="<?= admin_url('purchases/edit/' . $purchase->id); ?>"><i class="fa fa-edit"></i> <?= lang('edit_purchase'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($can_view_payments): ?>
                                  <li><a href="<?= admin_url('purchases/payments/' . $purchase->id); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-money"></i> <?= lang('view_payments'); ?></a></li>
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
              <div class="spark-purchases-empty-state">
                <i class="fa fa-file-text-o"></i>
                <span class="spark-purchases-empty-title"><?= lang('no_purchases_data'); ?></span>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<!-- =========================================================================
     9. ECHARTS LIBRARY SCRIPT & INITIALIZATION
     ========================================================================= -->
<?php
// Prepare ECharts dataset
$chart_months    = [];
$chart_purchases = [];
$chart_paid      = [];
$chart_due       = [];
$chart_returns   = [];

if (!empty($monthly_trends)) {
    foreach ($monthly_trends as $t) {
        $chart_months[]    = $t->month;
        $chart_purchases[] = (float)$t->total_purchases;
        $chart_paid[]      = (float)$t->total_paid;
        $chart_due[]       = (float)$t->total_due;
        $chart_returns[]   = (float)($t->returns ?? 0);
    }
}
?>

<script src="<?= $assets ?>template1/vendors/echarts/dist/echarts.min.js"></script>
<script type="text/javascript">
function applyPurchasesFilter() {
    var range = document.getElementById('purchasesDateRangeSelect').value;
    var wh = document.getElementById('purchasesWarehouseSelect') ? document.getElementById('purchasesWarehouseSelect').value : '';
    var url = '<?= admin_url('purchases/dashboard'); ?>?date_range=' + encodeURIComponent(range);
    if (wh) {
        url += '&warehouse=' + encodeURIComponent(wh);
    }
    window.location.href = url;
}

$(document).ready(function() {
    if (typeof echarts === 'undefined') {
        return;
    }

    var chartDom = document.getElementById('sparkPurchasesTrendChart');
    if (!chartDom) return;

    var myChart = echarts.init(chartDom);
    var isDark = $('html').attr('data-theme') === 'dark';

    var months = <?= json_encode($chart_months); ?>;
    var purchasesData = <?= json_encode($chart_purchases); ?>;
    var paidData = <?= json_encode($chart_paid); ?>;
    var dueData = <?= json_encode($chart_due); ?>;
    var returnsData = <?= json_encode($chart_returns); ?>;

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
            data: [
                '<?= lang('total_purchases'); ?>',
                '<?= lang('total_paid'); ?>',
                '<?= lang('total_due'); ?>',
                '<?= lang('purchase_returns'); ?>'
            ],
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
        series: [
            {
                name: '<?= lang('total_purchases'); ?>',
                type: 'bar',
                data: purchasesData,
                itemStyle: { color: '#0d9488', borderRadius: [4, 4, 0, 0] }
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
            },
            {
                name: '<?= lang('purchase_returns'); ?>',
                type: 'line',
                smooth: true,
                data: returnsData,
                itemStyle: { color: '#f43f5e' },
                lineStyle: { width: 3 }
            }
        ]
    };

    myChart.setOption(option);

    window.addEventListener('resize', function() {
        if (myChart) {
            myChart.resize();
        }
    });
});
</script>
