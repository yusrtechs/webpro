<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// =========================================================================
// 1. SAFE DATA & PERMISSION INITIALIZATION
// =========================================================================
$can_view_sales      = ($Owner || $Admin || !empty($GP['sales-index']) || !empty($GP['sales-add']));
$can_view_purchases  = ($Owner || $Admin || !empty($GP['purchases-index']) || !empty($GP['purchases-add']));
$can_view_products   = ($Owner || $Admin || !empty($GP['products-index']));
$can_view_customers  = ($Owner || $Admin || !empty($GP['customers-index']));
$can_view_suppliers  = ($Owner || $Admin || !empty($GP['suppliers-index']));
$can_view_accounts   = ((isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1) && ($Owner || $Admin));

// Filter context defaults
$active_period      = $active_period ?? 'all';
$display_date_range = $display_date_range ?? lang('all_time');
$from_date_input    = $from_date_input ?? date('Y-m-01');
$to_date_input      = $to_date_input ?? date('Y-m-d');
$warehouse_id       = $warehouse_id ?? null;

// Sales data calculations
$totalSalesVal      = (float)($sales_stats['total_sales'] ?? ($allSales->grand_total ?? 0));
$netSalesVal        = (float)($sales_stats['net_sales'] ?? 0);
$salesPaidVal       = (float)($sales_stats['total_paid'] ?? ($allSales->paid ?? 0));
$salesDueVal        = (float)($sales_stats['total_due'] ?? ($totalSalesVal - $salesPaidVal));
$salesTaxVal        = (float)($sales_stats['total_tax'] ?? 0);
$salesDiscountVal   = (float)($sales_stats['total_discount'] ?? 0);
$salesInvoicesCount = (int)($sales_stats['total_invoices'] ?? ($allSales->total ?? 0));
$salesReturnsCount  = (int)($sales_stats['returned_invoices'] ?? ($rSales->returne_amount ?? 0));
$salesReturnsVal    = (float)($sales_stats['returned_amount'] ?? ($rSales->return_sales ?? 0));

// Purchases data calculations
$totalPurchasesVal      = (float)($purchases_stats['total_purchases'] ?? ($allPurchases->grand_total ?? 0));
$netPurchasesVal        = (float)($purchases_stats['net_purchases'] ?? 0);
$purchasesPaidVal       = (float)($purchases_stats['total_paid'] ?? ($allPurchases->paid ?? 0));
$purchasesDueVal        = (float)($purchases_stats['total_due'] ?? ($totalPurchasesVal - $purchasesPaidVal));
$purchasesTaxVal        = (float)($purchases_stats['total_tax'] ?? 0);
$purchasesDiscountVal   = (float)($purchases_stats['total_discount'] ?? 0);
$purchasesInvoicesCount = (int)($purchases_stats['total_invoices'] ?? ($allPurchases->total ?? 0));
$purchasesReturnsCount  = (int)($purchases_stats['returned_invoices'] ?? ($rPurchases->returne_amount ?? 0));
$purchasesReturnsVal    = (float)($purchases_stats['returned_amount'] ?? ($rPurchases->return_purchase ?? 0));

// Inventory calculations
$invTotalProducts  = (int)($inventory_stats['total_products'] ?? 0);
$invActiveProducts = (int)($inventory_stats['active_products'] ?? 0);
$invLowStockCount  = (int)($inventory_stats['low_stock_products'] ?? 0);
$invOutStockCount  = (int)($inventory_stats['out_of_stock_products'] ?? 0);
$invStockQty       = (float)($inventory_stats['total_stock_quantity'] ?? 0);
$invCostValue      = (float)($inventory_stats['total_cost_value'] ?? 0);
$invSaleValue      = (float)($inventory_stats['total_sale_value'] ?? 0);

// Accounting & Financial calculations
$accRevenue      = (float)($accounting_stats['total_revenue'] ?? $totalSalesVal);
$accExpenses     = (float)($accounting_stats['total_expenses'] ?? $totalPurchasesVal);
$accNetProfit    = (float)($accounting_stats['net_profit'] ?? ($accRevenue - $accExpenses));
$accProfitMargin = (float)($accounting_stats['profit_margin'] ?? (($accRevenue > 0) ? round(($accNetProfit / $accRevenue) * 100, 2) : 0));
$accReceivables  = (float)($accounting_stats['accounts_receivable'] ?? $salesDueVal);
$accPayables     = (float)($accounting_stats['accounts_payable'] ?? $purchasesDueVal);
$accCashBank     = (float)($accounting_stats['cash_bank_balance'] ?? 0);

// Break down Cash & Bank dynamically
$totalCashBal = 0.0;
$totalBankBal = 0.0;
if (!empty($cash_bank_balances)) {
    foreach ($cash_bank_balances as $cb) {
        if (($cb->account_type ?? '') === 'cash') {
            $totalCashBal += (float)$cb->balance;
        } else {
            $totalBankBal += (float)$cb->balance;
        }
    }
}
if ($accCashBank == 0 && ($totalCashBal + $totalBankBal) > 0) {
    $accCashBank = $totalCashBal + $totalBankBal;
}

// Profitability calculations (COGS and Gross Margin)
$cogsVal        = (float)($sales_stats['total_sales'] ?? 0) - (float)($sales_stats['total_profit'] ?? 0);
$grossProfitVal = (float)($sales_stats['total_profit'] ?? ($totalSalesVal - $totalPurchasesVal));
$grossMarginPct = ($totalSalesVal > 0) ? round(($grossProfitVal / $totalSalesVal) * 100, 2) : 0;

// Monthly trend arrays for ECharts
$chartMonths = [];
$chartSales = [];
$chartPurchases = [];
if (!empty($monthly_sales_trends)) {
    foreach ($monthly_sales_trends as $ms) {
        $chartMonths[] = date('M Y', strtotime($ms->month . '-01'));
        $chartSales[]  = (float)$ms->total_sales;
    }
} elseif (!empty($total_sales_canv)) {
    foreach ($total_sales_canv as $ms) {
        $chartMonths[] = $ms->month ?? '';
        $chartSales[]  = (float)($ms->total ?? 0);
    }
}
if (!empty($monthly_purchases_trends)) {
    foreach ($monthly_purchases_trends as $mp) {
        $chartPurchases[] = (float)$mp->total_purchases;
    }
} elseif (!empty($total_purchases_canv)) {
    foreach ($total_purchases_canv as $mp) {
        $chartPurchases[] = (float)$mp->total_purchases;
    }
}
if (empty($chartMonths)) {
    $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
}

// Quick Actions Links
$quickActions = [
    ['label' => lang('products'), 'url' => admin_url('products'), 'icon' => 'fa fa-barcode', 'perm' => $can_view_products],
    ['label' => lang('add_product'), 'url' => admin_url('products/add'), 'icon' => 'fa fa-plus-circle', 'perm' => ($Owner || $Admin || !empty($GP['products-add']))],
    ['label' => lang('sales'), 'url' => admin_url('sales'), 'icon' => 'fa fa-shopping-cart', 'perm' => $can_view_sales],
    ['label' => lang('add_sale'), 'url' => admin_url('sales/add'), 'icon' => 'fa fa-plus-circle', 'perm' => ($Owner || $Admin || !empty($GP['sales-add']))],
    ['label' => lang('pos'), 'url' => admin_url('pos'), 'icon' => 'fa fa-th-large', 'perm' => (POS && $Settings->pos == 1)],
    ['label' => lang('purchases'), 'url' => admin_url('purchases'), 'icon' => 'fa fa-truck', 'perm' => $can_view_purchases],
    ['label' => lang('customers'), 'url' => admin_url('customers'), 'icon' => 'fa fa-users', 'perm' => $can_view_customers],
    ['label' => lang('suppliers'), 'url' => admin_url('suppliers'), 'icon' => 'fa fa-building', 'perm' => $can_view_suppliers],
    ['label' => lang('Accounting'), 'url' => admin_url('accounts/dashboard'), 'icon' => 'fa fa-book', 'perm' => $can_view_accounts],
];

// Operational Alerts list (Dynamic: rendered only if non-zero count)
$alertsList = [];
if ($can_view_sales) {
    $alertsList[] = [
        'title'       => lang('overdue_invoices_alert'),
        'count'       => $dashboard_alerts->overdue_invoices,
        'description' => lang('overdue_receivables'),
        'icon'        => 'fa fa-exclamation-triangle',
        'color_class' => 'is-orange-red',
        'badge_class' => 'spark-alert-badge-red',
        'link'        => admin_url('sales'),
    ];
}
if ($can_view_purchases) {
    $alertsList[] = [
        'title'       => lang('overdue_payables_alert'),
        'count'       => $dashboard_alerts->overdue_payables,
        'description' => lang('overdue_payables'),
        'icon'        => 'fa fa-clock-o',
        'color_class' => 'is-red-pink',
        'badge_class' => 'spark-alert-badge-red',
        'link'        => admin_url('purchases'),
    ];
}

if ($can_view_products) {
    $alertsList[] = [
        'title'       => lang('out_of_stock'),
        'count'       => $this->site->get_total_qty_alerts(),
        'description' => lang('out_of_stock'),
        'icon'        => 'fa fa-ban',
        'color_class' => 'is-red-pink',
        'badge_class' => 'spark-alert-badge-red',
        'link'        => admin_url('products'),
    ];
}
if ($can_view_purchases) {
    $alertsList[] = [
        'title'       => lang('pending_purchases_alert'),
        'count'       => $dashboard_alerts->pending_orders,
        'description' => lang('pending_purchases_alert'),
        'icon'        => 'fa fa-hourglass-half',
        'color_class' => 'is-blue-cyan',
        'badge_class' => 'spark-alert-badge-blue',
        'link'        => admin_url('purchases'),
    ];
}
if ($can_view_sales) {
    $alertsList[] = [
        'title'       => lang('pending_quotes_alert'),
        'count'       => $dashboard_alerts->pending_quotes,
        'description' => lang('pending_quotes_alert'),
        'icon'        => 'fa fa-file-text-o',
        'color_class' => 'is-red-pink',
        'badge_class' => 'spark-alert-badge-red',
        'link'        => admin_url('sales/quotes_index'),
    ];
}

    $alertsList[] = [
        'title'       => lang('today_tasks'),
        'count'       => $dashboard_alerts->today_tasks,
        'description' => lang('today_tasks'),
        'icon'        => 'fa fa-calendar-check-o',
        'color_class' => 'is-emerald-teal',
        'badge_class' => 'spark-alert-badge-green',
        'link'        => admin_url('calendar'),
    ];

// Department Statistics list
$deptStats = [
    ['title' => lang('sales'), 'value' => $department_stats->sales ?? 0, 'description' => lang('invoices_count'), 'icon' => 'fa fa-shopping-cart', 'color_class' => 'is-emerald-teal', 'link' => admin_url('sales'), 'perm' => $can_view_sales],
    ['title' => lang('purchases'), 'value' => $department_stats->purchases ?? 0, 'description' => lang('invoices_count'), 'icon' => 'fa fa-cart-arrow-down', 'color_class' => 'is-blue-cyan', 'link' => admin_url('purchases'), 'perm' => $can_view_purchases],
    ['title' => lang('products'), 'value' => $department_stats->products ?? 0, 'description' => lang('items_count'), 'icon' => 'fa fa-cubes', 'color_class' => 'is-purple-pink', 'link' => admin_url('products'), 'perm' => $can_view_products],
    ['title' => lang('customers'), 'value' => $department_stats->customers ?? 0, 'description' => lang('customers'), 'icon' => 'fa fa-user', 'color_class' => 'is-orange-red', 'link' => admin_url('customers'), 'perm' => $can_view_customers],
    ['title' => lang('users'), 'value' => $department_stats->users ?? 0, 'description' => lang('users'), 'icon' => 'fa fa-users', 'color_class' => 'is-cyan-blue', 'link' => admin_url('users'), 'perm' => ($Owner || $Admin)],
    ['title' => lang('suppliers'), 'value' => $department_stats->suppliers ?? 0, 'description' => lang('suppliers'), 'icon' => 'fa fa-truck', 'color_class' => 'is-pink-rose', 'link' => admin_url('suppliers'), 'perm' => $can_view_suppliers],
];
?>

<div class="spark-dashboard">

  <!-- =========================================================================
       1. GLOBAL HEADER & FILTER TOOLBAR
       ========================================================================= -->
  <div class="spark-dashboard-header">
    <div class="spark-dashboard-title-group" style="display: flex; align-items: center; gap: 14px; flex-direction: row;">
      <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--spark-gradient-primary, linear-gradient(135deg, #6366f1, #7c3aed)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35); flex-shrink: 0;">
        <i class="fa fa-th-large"></i>
      </div>
      <div>
        <h1 class="spark-dashboard-title"><?= lang('dashboard'); ?></h1>
        <p class="spark-dashboard-subtitle"><?= lang('main_dashboard_subtitle'); ?></p>
      </div>
    </div>
    <div class="spark-dashboard-meta">
      <div class="spark-dashboard-date-pill">
        <i class="fa fa-calendar"></i>
        <span><?= $display_date_range; ?></span>
      </div>
      <button type="button" class="spark-btn spark-btn-outline spark-btn-sm" onclick="window.print();">
        <i class="fa fa-print"></i> <?= lang('print_dashboard'); ?>
      </button>
      <?php if (POS && $Settings->pos == 1) { ?>
        <a href="<?= admin_url('pos'); ?>" class="spark-btn spark-btn-primary spark-btn-sm">
          <i class="fa fa-th-large"></i> <?= lang('pos'); ?>
        </a>
      <?php } ?>
    </div>
  </div>
  <?php if ($Owner || $Admin): ?>
  <!-- Global Filter Toolbar (Matches Accounting Dashboard) -->
  <div class="spark-acc-filter-wrapper">
    <div class="spark-acc-filter-container">
      <!-- Period Presets -->
      <div class="spark-acc-period-presets">
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'today') ? 'active' : ''; ?>" onclick="SparkFilter.setPeriod('today')">
          <i class="fa fa-clock-o"></i> <?= lang('today'); ?>
        </a>
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'this_week') ? 'active' : ''; ?>" onclick="SparkFilter.setPeriod('this_week')">
          <i class="fa fa-calendar-minus-o"></i> <?= lang('this_week'); ?>
        </a>
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'this_month') ? 'active' : ''; ?>" onclick="SparkFilter.setPeriod('this_month')">
          <i class="fa fa-calendar"></i> <?= lang('this_month'); ?>
        </a>
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'this_quarter') ? 'active' : ''; ?>" onclick="SparkFilter.setPeriod('this_quarter')">
          <i class="fa fa-pie-chart"></i> <?= lang('this_quarter'); ?>
        </a>
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'this_year') ? 'active' : ''; ?>" onclick="SparkFilter.setPeriod('this_year')">
          <i class="fa fa-calendar-check-o"></i> <?= lang('this_year'); ?>
        </a>
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'fiscal_year') ? 'active' : ''; ?>" onclick="SparkFilter.setPeriod('fiscal_year')">
          <i class="fa fa-university"></i> <?= lang('fiscal_year'); ?>
        </a>
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'custom') ? 'active' : ''; ?>" onclick="SparkFilter.toggleCustomDrawer()">
          <i class="fa fa-sliders"></i> <?= lang('custom_range'); ?>
        </a>
        <a href="javascript:void(0);" class="spark-acc-filter-btn <?= ($active_period == 'all') ? 'active' : ''; ?>" onclick="SparkFilter.setPeriod('all')">
          <i class="fa fa-globe"></i> <?= lang('all_time'); ?>
        </a>
      </div>

      <!-- Warehouse Selector & Active Range Badge -->
      <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <div class="spark-acc-active-range-badge">
          <i class="fa fa-calendar-check-o"></i>
          <span><?= $display_date_range; ?></span>
        </div>

        <?php if (!empty($warehouses) && ($Owner || $Admin || empty($this->session->userdata('warehouse_id')))) { ?>
          <select class="spark-acc-date-input" id="sparkGlobalWarehouseSelect" style="width: auto; min-width: 160px; font-weight: 500;" onchange="SparkFilter.setWarehouse(this.value)">
            <option value=""><?= lang('all_warehouses'); ?></option>
            <?php foreach ($warehouses as $wh) { ?>
              <option value="<?= $wh->id; ?>" <?= ($warehouse_id == $wh->id) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($wh->name); ?>
              </option>
            <?php } ?>
          </select>
        <?php } ?>
      </div>
    </div>

    <!-- Custom Date Range Drawer -->
    <div id="sparkCustomDrawer" class="spark-acc-custom-drawer" style="display: <?= ($active_period == 'custom') ? 'block' : 'none'; ?>;">
      <form method="get" action="<?= admin_url('welcome'); ?>" class="spark-acc-custom-form">
        <?php if ($warehouse_id) { ?>
          <input type="hidden" name="warehouse" value="<?= $warehouse_id; ?>">
        <?php } ?>
        <input type="hidden" name="period" value="custom">

        <div class="spark-acc-form-group">
          <label style="margin: 0;"><?= lang('start_date'); ?>:</label>
          <input type="date" name="start_date" class="spark-acc-date-input" value="<?= $from_date_input; ?>">
        </div>

        <div class="spark-acc-form-group">
          <label style="margin: 0;"><?= lang('end_date'); ?>:</label>
          <input type="date" name="end_date" class="spark-acc-date-input" value="<?= $to_date_input; ?>">
        </div>

        <div class="spark-acc-form-actions">
          <button type="submit" class="spark-btn spark-btn-primary spark-btn-sm" style="height: 32px; border-radius: 6px; padding: 4px 12px; font-size: 12px;">
            <i class="fa fa-filter"></i> <?= lang('apply_filter'); ?>
          </button>
          <a href="<?= admin_url('welcome'); ?>" class="spark-btn spark-btn-outline spark-btn-sm" style="height: 32px; border-radius: 6px; padding: 4px 12px; font-size: 12px;">
            <i class="fa fa-refresh"></i> <?= lang('reset_filter'); ?>
          </a>
        </div>
      </form>
    </div>
  </div>

  <?php endif; ?>

  <!-- QUICK ACTIONS (Only rendered if permitted actions exist; takes full natural width, no empty column reserved) -->
  <?php
  $permittedQuickActions = array_filter($quickActions, function($qa) {
      return !empty($qa['perm']);
  });
  if (!empty($permittedQuickActions)) { ?>
    <div class="spark-widget-card" style="margin-bottom: 24px;">
      <div class="spark-card-header">
        <h3 class="spark-card-title">
          <i class="fa fa-bolt"></i> <?= lang('quick_links'); ?>
        </h3>
      </div>
      <div class="spark-card-body">
        <div class="spark-quick-actions-grid">
          <?php foreach ($permittedQuickActions as $qa) { ?>
            <a href="<?= $qa['url']; ?>" class="spark-quick-action-btn">
              <i class="<?= $qa['icon']; ?>"></i>
              <span><?= $qa['label']; ?></span>
            </a>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>


  <!-- =========================================================================
       2. OPERATIONAL ALERTS ("ATTENTION REQUIRED")
       ========================================================================= -->
  <?php if (!empty($alertsList)) { ?>
    <div class="spark-dashboard-alerts-grid">
      <?php foreach ($alertsList as $alt) { ?>
        <a href="<?= $alt['link']; ?>" class="spark-alert-item-card">
          <div class="spark-alert-item-inner">
            <div class="spark-alert-item-icon <?= $alt['color_class']; ?>">
              <i class="<?= $alt['icon']; ?>"></i>
            </div>
            <div class="spark-alert-item-content">
              <div class="spark-alert-item-header">
                <h4 class="spark-alert-item-title"><?= $alt['title']; ?></h4>
                <span class="spark-alert-item-badge <?= $alt['badge_class']; ?>"><?= number_format($alt['count']); ?></span>
              </div>
              <p class="spark-alert-item-desc"><?= $alt['description']; ?></p>
            </div>
          </div>
        </a>
      <?php } ?>
    </div>
  <?php } ?>

  <!-- =========================================================================
       3. DEPARTMENT STATISTICS (6 SUMMARY CARDS)
       ========================================================================= -->
  <div class="spark-dept-stats-panel">
    <div class="spark-dept-stats-header">
      <div class="spark-dept-stats-header-icon">
        <i class="fa fa-bar-chart"></i>
      </div>
      <h2 class="spark-dept-stats-title"><?= lang('department_statistics'); ?></h2>
    </div>
    <div class="spark-dept-stats-grid">
      <?php foreach ($deptStats as $dept) {
        if ($dept['perm']) { ?>
          <a href="<?= $dept['link']; ?>" class="spark-dept-stat-card">
            <div class="spark-dept-stat-icon <?= $dept['color_class']; ?>">
              <i class="<?= $dept['icon']; ?>"></i>
            </div>
            <h3 class="spark-dept-stat-value"><?= number_format($dept['value']); ?></h3>
            <p class="spark-dept-stat-title"><?= $dept['title']; ?></p>
            <p class="spark-dept-stat-desc"><?= $dept['description']; ?></p>
          </a>
      <?php } } ?>
    </div>
  </div>

  <!-- =========================================================================
       4. MASTER EXECUTIVE KPI SUMMARY GRID (SALES, PURCHASES, INVENTORY, FINANCIAL)
       ========================================================================= -->
  <div class="spark-stat-grid">
    <!-- SALES KPIs -->
    <?php if ($Owner || $Admin) { ?>
      <!-- Card 1: Gross Sales -->
      <div class="spark-stat-card">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('Total_Salles'); ?></span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa fa-line-chart"></i>
          </div>
        </div>
        <h3 class="spark-stat-value"><?= $this->sma->formatMoney($totalSalesVal); ?></h3>
        <div class="spark-stat-footer">
          <span><?= lang('Sales_Invoices'); ?>: <strong><?= number_format($salesInvoicesCount); ?></strong></span>
          <span class="spark-stat-pill spark-badge-success"><i class="fa fa-arrow-up"></i> <?= lang('sales'); ?></span>
        </div>
      </div>

      <!-- Card 2: Sales Paid & Due -->
      <div class="spark-stat-card spark-stat-card-success">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('Total_Paid'); ?> (<?= lang('sales'); ?>)</span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa fa-check-circle"></i>
          </div>
        </div>
        <h3 class="spark-stat-value" style="color: var(--spark-success, #10b981);"><?= $this->sma->formatMoney($salesPaidVal); ?></h3>
        <div class="spark-stat-footer">
          <span><?= lang('Due_Amount'); ?>: <strong><?= $this->sma->formatMoney($salesDueVal); ?></strong></span>
          <span class="spark-stat-pill spark-badge-info"><?= lang('collected_rate'); ?>: <?= ($totalSalesVal > 0) ? round(($salesPaidVal / $totalSalesVal) * 100, 1) : 0; ?>%</span>
        </div>
      </div>
    <?php } ?>

    <!-- PURCHASES KPIs -->
    <?php if ($Owner || $Admin) { ?>
      <!-- Card 3: Total Purchases -->
      <div class="spark-stat-card spark-stat-card-warning">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('Total_Purchases'); ?></span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa fa-truck"></i>
          </div>
        </div>
        <h3 class="spark-stat-value"><?= $this->sma->formatMoney($totalPurchasesVal); ?></h3>
        <div class="spark-stat-footer">
          <span><?= lang('Purchases_Invoices'); ?>: <strong><?= number_format($purchasesInvoicesCount); ?></strong></span>
          <span class="spark-stat-pill spark-badge-warning"><?= lang('purchases'); ?></span>
        </div>
      </div>

      <!-- Card 4: Purchases Paid & Due -->
      <div class="spark-stat-card spark-stat-card-purple">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('Total_Paid'); ?> (<?= lang('purchases'); ?>)</span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa fa-credit-card"></i>
          </div>
        </div>
        <h3 class="spark-stat-value"><?= $this->sma->formatMoney($purchasesPaidVal); ?></h3>
        <div class="spark-stat-footer">
          <span><?= lang('Due_Amount'); ?>: <strong><?= $this->sma->formatMoney($purchasesDueVal); ?></strong></span>
        </div>
      </div>
    <?php } ?>

    <!-- INVENTORY KPIs -->
    <?php if ($Owner || $Admin) { ?>
      <!-- Card 5: Total Stock Quantity -->
      <div class="spark-stat-card">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('total_stock_quantity'); ?></span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa fa-cubes"></i>
          </div>
        </div>
        <h3 class="spark-stat-value"><?= number_format($invStockQty); ?></h3>
        <div class="spark-stat-footer">
          <span><?= lang('products'); ?>: <strong><?= number_format($invTotalProducts); ?></strong></span>
          <span class="spark-stat-pill spark-badge-info"><?= lang('active'); ?>: <?= number_format($invActiveProducts); ?></span>
        </div>
      </div>

      <!-- Card 6: Inventory Value (Cost) -->
      <div class="spark-stat-card spark-stat-card-info">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('stock_value_cost'); ?></span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa fa-database"></i>
          </div>
        </div>
        <h3 class="spark-stat-value"><?= $this->sma->formatMoney($invCostValue); ?></h3>
        <div class="spark-stat-footer">
          <span><?= lang('stock_value_sale'); ?>: <strong><?= $this->sma->formatMoney($invSaleValue); ?></strong></span>
        </div>
      </div>
    <?php } ?>

    <!-- FINANCIAL & ACCOUNTING KPIs -->
    <?php if ($Owner || $Admin) { ?>
      <!-- Card 7: Cash & Bank Total -->
      <div class="spark-stat-card spark-stat-card-success">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('total_cash_and_bank'); ?></span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa fa-university"></i>
          </div>
        </div>
        <h3 class="spark-stat-value" style="color: var(--spark-success, #10b981);"><?= $this->sma->formatMoney($accCashBank); ?></h3>
        <div class="spark-stat-footer">
          <span><?= lang('cash_balance'); ?>: <?= $this->sma->formatMoney($totalCashBal); ?></span>
        </div>
      </div>

      <!-- Card 8: Net Profit / Loss -->
      <div class="spark-stat-card <?= ($accNetProfit >= 0) ? 'spark-stat-card-success' : 'spark-stat-card-danger'; ?>">
        <div class="spark-stat-header">
          <span class="spark-stat-label"><?= lang('profit_and_loss'); ?></span>
          <div class="spark-stat-icon-wrapper">
            <i class="fa <?= ($accNetProfit >= 0) ? 'fa-arrow-circle-up' : 'fa-arrow-circle-down'; ?>"></i>
          </div>
        </div>
        <h3 class="spark-stat-value" style="color: <?= ($accNetProfit >= 0) ? 'var(--spark-success, #10b981)' : 'var(--spark-danger, #ef4444)'; ?>;">
          <?= $this->sma->formatMoney($accNetProfit); ?>
        </h3>
        <div class="spark-stat-footer">
          <span><?= lang('gross_margin'); ?>: <strong><?= $accProfitMargin; ?>%</strong></span>
          <span class="spark-stat-pill spark-badge-primary"><?= lang('profit'); ?></span>
        </div>
      </div>
    <?php } ?>
  </div>

  <!-- =========================================================================
       5. INTERACTIVE CHARTS & VISUAL ANALYTICS (BALANCED 2-COLUMN GRID)
       ========================================================================= -->
  <div class="spark-dashboard-grid">
    <?php if ($Owner || $Admin) { ?>
      <!-- Chart 1: Sales vs Purchases Monthly Trend -->
      <div class="spark-chart-card">
        <div class="spark-card-header">
          <h3 class="spark-card-title">
            <i class="fa fa-area-chart"></i> <?= lang('sales_vs_purchases'); ?>
          </h3>
          <span class="spark-badge spark-badge-primary"><?= $display_date_range; ?></span>
        </div>
        <div class="spark-card-body">
          <div id="sparkSalesPurchasesChart" class="spark-chart-container"></div>
        </div>
      </div>
    <?php } ?>

    <!-- Chart 2: Profit & Loss / Financial Breakdown -->
    <?php if ($can_view_accounts) { ?>
      <div class="spark-chart-card">
        <div class="spark-card-header">
          <h3 class="spark-card-title">
            <i class="fa fa-pie-chart"></i> <?= lang('revenue_vs_expenses'); ?>
          </h3>
        </div>
        <div class="spark-card-body">
          <div id="sparkProfitChart" class="spark-chart-container"></div>
        </div>
      </div>
    <?php } ?>

    <?php if ($can_view_accounts) { ?>
      <!-- Chart 3: Receivables vs Payables Dues -->
      <div class="spark-chart-card">
        <div class="spark-card-header">
          <h3 class="spark-card-title">
            <i class="fa fa-bar-chart"></i> <?= lang('receivables_vs_payables'); ?>
          </h3>
        </div>
        <div class="spark-card-body">
          <div style="display: flex; flex-direction: column; gap: 16px;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 11px; font-weight: 600; color: var(--spark-text-secondary); text-transform: uppercase;"><?= lang('total_receivables'); ?></span>
                <span style="font-size: 12px; font-weight: 700; color: var(--spark-info, #06b6d4);"><?= $this->sma->formatMoney(array_sum($aged_receivables ?? [0])); ?></span>
              </div>
              <div id="sparkReceivablesChart" style="width: 100%; height: 120px;"></div>
            </div>
            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 11px; font-weight: 600; color: var(--spark-text-secondary); text-transform: uppercase;"><?= lang('total_payables'); ?></span>
                <span style="font-size: 12px; font-weight: 700; color: var(--spark-warning, #f97316);"><?= $this->sma->formatMoney(array_sum($aged_payables ?? [0])); ?></span>
              </div>
              <div id="sparkPayablesChart" style="width: 100%; height: 120px;"></div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>

    <!-- Leaderboard: Best Selling Products -->
    <?php if ($Owner || $Admin) { ?>
      <div class="spark-widget-card">
        <div class="spark-card-header">
          <h3 class="spark-card-title">
            <i class="fa fa-trophy"></i> <?= lang('top_selling_products'); ?>
          </h3>
        </div>
        <div class="spark-card-body">
          <ul class="spark-leaderboard-list">
            <?php
            $rank = 1;
            foreach ($top_selling_products as $product) { ?>
              <li class="spark-leaderboard-item">
                <div class="spark-leaderboard-info">
                  <span class="spark-leaderboard-rank"><?= $rank++; ?></span>
                  <span class="spark-leaderboard-name" title="<?= $product->name; ?>"><?= $product->name; ?></span>
                </div>
                <span class="spark-leaderboard-qty"><?= (float)$product->sold_qty; ?> <?= lang('quantity'); ?> &bull; <?= $this->sma->formatMoney($product->sales_amount ?? 0); ?></span>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    <?php } ?>
  </div>

  <!-- =========================================================================
       6. DEEP MODULAR ANALYTICS SECTIONS
       ========================================================================= -->

  <!-- A. SALES ANALYTICS & SUMMARY TABLE -->
  <?php if ($Owner || $Admin) { ?>
    <div class="spark-table-card" style="margin-bottom: 24px;">
      <div class="spark-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--spark-border);">
        <h3 class="spark-card-title"><i class="fa fa-line-chart"></i> <?= lang('sales_analytics'); ?></h3>
      </div>
      <div class="spark-card-body" style="padding: 0;">
        <div class="spark-table-wrapper">
          <table class="spark-dashboard-table">
            <thead>
              <tr>
                <th><?= lang('metric'); ?></th>
                <th style="text-align: end;"><?= lang('amount'); ?></th>
                <th><?= lang('notes'); ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong><?= lang('gross_sales'); ?></strong></td>
                <td style="text-align: end; font-weight: 600;"><?= $this->sma->formatMoney($totalSalesVal); ?></td>
                <td><?= lang('Sales_Invoices'); ?>: <?= number_format($salesInvoicesCount); ?></td>
              </tr>
              <tr>
                <td><?= lang('discount'); ?></td>
                <td style="text-align: end; color: var(--spark-danger, #ef4444);"><?= $this->sma->formatMoney($salesDiscountVal); ?></td>
                <td><?= lang('total_discount'); ?></td>
              </tr>
              <tr>
                <td><?= lang('tax'); ?></td>
                <td style="text-align: end;"><?= $this->sma->formatMoney($salesTaxVal); ?></td>
                <td><?= lang('total_tax'); ?></td>
              </tr>
              <tr>
                <td><?= lang('sales_returns'); ?></td>
                <td style="text-align: end; color: var(--spark-danger, #ef4444);"><?= $this->sma->formatMoney($salesReturnsVal); ?></td>
                <td><?= lang('Returne_Invoices'); ?>: <?= number_format($salesReturnsCount); ?></td>
              </tr>
              <tr>
                <td><strong><?= lang('net_sales'); ?></strong></td>
                <td style="text-align: end; font-weight: 700; color: var(--spark-primary, #6366f1);"><?= $this->sma->formatMoney($netSalesVal); ?></td>
                <td><?= lang('net_sales'); ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- B. PURCHASES ANALYTICS & SUMMARY TABLE -->
  <?php if ($Owner || $Admin) { ?>
    <div class="spark-table-card" style="margin-bottom: 24px;">
      <div class="spark-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--spark-border);">
        <h3 class="spark-card-title"><i class="fa fa-truck"></i> <?= lang('purchase_analytics'); ?></h3>
      </div>
      <div class="spark-card-body" style="padding: 0;">
        <div class="spark-table-wrapper">
          <table class="spark-dashboard-table">
            <thead>
              <tr>
                <th><?= lang('metric'); ?></th>
                <th style="text-align: end;"><?= lang('amount'); ?></th>
                <th><?= lang('notes'); ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong><?= lang('gross_purchases'); ?></strong></td>
                <td style="text-align: end; font-weight: 600;"><?= $this->sma->formatMoney($totalPurchasesVal); ?></td>
                <td><?= lang('Purchases_Invoices'); ?>: <?= number_format($purchasesInvoicesCount); ?></td>
              </tr>
              <tr>
                <td><?= lang('discount'); ?></td>
                <td style="text-align: end; color: var(--spark-success, #10b981);"><?= $this->sma->formatMoney($purchasesDiscountVal); ?></td>
                <td><?= lang('total_discount'); ?></td>
              </tr>
              <tr>
                <td><?= lang('tax'); ?></td>
                <td style="text-align: end;"><?= $this->sma->formatMoney($purchasesTaxVal); ?></td>
                <td><?= lang('total_tax'); ?></td>
              </tr>
              <tr>
                <td><?= lang('purchase_returns'); ?></td>
                <td style="text-align: end; color: var(--spark-danger, #ef4444);"><?= $this->sma->formatMoney($purchasesReturnsVal); ?></td>
                <td><?= lang('Returne_Invoices'); ?>: <?= number_format($purchasesReturnsCount); ?></td>
              </tr>
              <tr>
                <td><strong><?= lang('net_purchases'); ?></strong></td>
                <td style="text-align: end; font-weight: 700; color: var(--spark-primary, #6366f1);"><?= $this->sma->formatMoney($netPurchasesVal); ?></td>
                <td><?= lang('net_purchases'); ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- C. INVENTORY & WAREHOUSE COMPARISON TABLE -->
  <?php if ($Owner || $Admin) { ?>
    <div class="spark-table-card" style="margin-bottom: 24px;">
      <div class="spark-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--spark-border);">
        <h3 class="spark-card-title"><i class="fa fa-building-o"></i> <?= lang('warehouse_comparison'); ?></h3>
      </div>
      <div class="spark-card-body" style="padding: 0;">
        <div class="spark-table-wrapper">
          <table class="spark-dashboard-table">
            <thead>
              <tr>
                <th><?= lang('warehouse'); ?></th>
                <th style="text-align: end;"><?= lang('total_stock_quantity'); ?></th>
                <th style="text-align: end;"><?= lang('stock_value_cost'); ?></th>
                <th style="text-align: end;"><?= lang('sales'); ?></th>
                <th style="text-align: end;"><?= lang('purchases'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($warehouse_comparison as $whRow) { ?>
                <tr>
                  <td><strong><a href="<?= admin_url('welcome?warehouse=' . $whRow->id . '&period=' . $active_period); ?>" style="color: var(--spark-primary);"><?= $whRow->name; ?></a></strong> (<?= $whRow->code; ?>)</td>
                  <td style="text-align: end; font-weight: 600;"><?= number_format($whRow->stock_qty); ?></td>
                  <td style="text-align: end;"><?= $this->sma->formatMoney($whRow->stock_cost); ?></td>
                  <td style="text-align: end; color: var(--spark-success, #10b981);"><?= $this->sma->formatMoney($whRow->sales_total); ?></td>
                  <td style="text-align: end;"><?= $this->sma->formatMoney($whRow->purchases_total); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- D. DYNAMIC CASH & BANK ACCOUNTS BREAKDOWN (FROM sma_gl_accounts_settings) -->
  <?php if ($can_view_accounts && !empty($cash_bank_balances)) { ?>
    <div class="spark-table-card" style="margin-bottom: 24px;">
      <div class="spark-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--spark-border);">
        <h3 class="spark-card-title"><i class="fa fa-university"></i> <?= lang('cash_and_bank'); ?></h3>
      </div>
      <div class="spark-card-body" style="padding: 0;">
        <div class="spark-table-wrapper">
          <table class="spark-dashboard-table">
            <thead>
              <tr>
                <th><?= lang('Account_code'); ?></th>
                <th><?= lang('Account_name'); ?></th>
                <th><?= lang('type'); ?></th>
                <th style="text-align: end;"><?= lang('balance'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cash_bank_balances as $cbAcc) { ?>
                <tr>
                  <td><strong><?= $cbAcc->accountID; ?></strong></td>
                  <td><?= $Settings->user_language == 'arabic' ? ($cbAcc->accountName ?: $cbAcc->accountNameEn) : ($cbAcc->accountNameEn ?: $cbAcc->accountName); ?></td>
                  <td>
                    <span class="spark-badge <?= ($cbAcc->account_type == 'cash') ? 'spark-badge-success' : 'spark-badge-primary'; ?>">
                      <?= ($cbAcc->account_type == 'cash') ? lang('cash_balance') : lang('bank_balance'); ?>
                    </span>
                  </td>
                  <td style="text-align: end; font-weight: 600; color: var(--spark-success, #10b981);"><?= $this->sma->formatMoney($cbAcc->balance); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- E. PROFITABILITY SUMMARY -->
  <?php if ($can_view_accounts) { ?>
    <div class="spark-table-card" style="margin-bottom: 24px;">
      <div class="spark-card-header" style="padding: 16px 20px; border-bottom: 1px solid var(--spark-border);">
        <h3 class="spark-card-title"><i class="fa fa-calculator"></i> <?= lang('profitability_summary'); ?></h3>
      </div>
      <div class="spark-card-body" style="padding: 0;">
        <div class="spark-table-wrapper">
          <table class="spark-dashboard-table">
            <thead>
              <tr>
                <th><?= lang('metric'); ?></th>
                <th style="text-align: end;"><?= lang('amount'); ?></th>
                <th><?= lang('percentage'); ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong><?= lang('sales'); ?></strong></td>
                <td style="text-align: end; font-weight: 600;"><?= $this->sma->formatMoney($totalSalesVal); ?></td>
                <td>100%</td>
              </tr>
              <tr>
                <td><?= lang('cost_of_goods_sold'); ?></td>
                <td style="text-align: end; color: var(--spark-danger, #ef4444);"><?= $this->sma->formatMoney($cogsVal); ?></td>
                <td><?= ($totalSalesVal > 0) ? round(($cogsVal / $totalSalesVal) * 100, 1) : 0; ?>%</td>
              </tr>
              <tr>
                <td><strong><?= lang('gross_profit'); ?></strong></td>
                <td style="text-align: end; font-weight: 700; color: var(--spark-success, #10b981);"><?= $this->sma->formatMoney($grossProfitVal); ?></td>
                <td><strong><?= $grossMarginPct; ?>%</strong></td>
              </tr>
              <tr>
                <td><?= lang('expenses'); ?></td>
                <td style="text-align: end; color: var(--spark-danger, #ef4444);"><?= $this->sma->formatMoney($accExpenses); ?></td>
                <td><?= ($totalSalesVal > 0) ? round(($accExpenses / $totalSalesVal) * 100, 1) : 0; ?>%</td>
              </tr>
              <tr>
                <td><strong><?= lang('net_profit'); ?></strong></td>
                <td style="text-align: end; font-weight: 700; color: <?= ($accNetProfit >= 0) ? 'var(--spark-success, #10b981)' : 'var(--spark-danger, #ef4444)'; ?>;">
                  <?= $this->sma->formatMoney($accNetProfit); ?>
                </td>
                <td><strong><?= $accProfitMargin; ?>%</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- =========================================================================
       7. RECENT TRANSACTIONS TABBED CARD
       ========================================================================= -->
  <div class="spark-table-card">
    <div class="spark-tabs-header">
      <?php if ($can_view_sales) { ?>
        <button type="button" class="spark-tab-btn is-active" data-target="tab-sales">
          <i class="fa fa-shopping-cart"></i> <?= lang('recent_sales'); ?>
        </button>
        <button type="button" class="spark-tab-btn" data-target="tab-quotes">
          <i class="fa fa-file-text-o"></i> <?= lang('recent_quotes'); ?>
        </button>
      <?php } ?>
      <?php if ($can_view_purchases) { ?>
        <button type="button" class="spark-tab-btn <?= (!$can_view_sales) ? 'is-active' : ''; ?>" data-target="tab-purchases">
          <i class="fa fa-truck"></i> <?= lang('recent_purchases'); ?>
        </button>
      <?php } ?>
      <button type="button" class="spark-tab-btn" data-target="tab-transfers">
        <i class="fa fa-arrows-h"></i> <?= lang('recent_transfers'); ?>
      </button>
      <button type="button" class="spark-tab-btn" data-target="tab-payments">
        <i class="fa fa-money"></i> <?= lang('recent_payments'); ?>
      </button>
    </div>

    <div class="spark-card-body" style="padding: 0;">
      <!-- Tab 1: Sales -->
      <?php if ($can_view_sales) { ?>
        <div id="tab-sales" class="spark-tab-content is-active">
          <div class="spark-table-wrapper">
            <table class="spark-dashboard-table">
              <thead>
                <tr>
                  <th><?= lang('date'); ?></th>
                  <th><?= lang('reference_no'); ?></th>
                  <th><?= lang('customer'); ?></th>
                  <th><?= lang('status'); ?></th>
                  <th style="text-align: end;"><?= lang('total'); ?></th>
                  <th style="text-align: end;"><?= lang('paid'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_sales)) {
                  foreach ($recent_sales as $row) { ?>
                    <tr>
                      <td><?= $row->date ? date('Y-m-d', strtotime($row->date)) : ''; ?></td>
                      <td><strong><a href="<?= admin_url('sales/modal_view/' . $row->id); ?>" data-toggle="modal" data-target="#myModal" style="color: var(--spark-primary, #2563eb);"><?= $row->reference_no; ?></a></strong></td>
                      <td><?= $row->customer; ?></td>
                      <td>
                        <span class="spark-badge <?= ($row->sale_status == 'completed') ? 'spark-badge-success' : 'spark-badge-warning'; ?>">
                          <?= lang($row->sale_status ?? 'completed'); ?>
                        </span>
                      </td>
                      <td style="text-align: end; font-weight: 600;"><?= $this->sma->formatMoney($row->grand_total); ?></td>
                      <td style="text-align: end;"><?= $this->sma->formatMoney($row->paid); ?></td>
                    </tr>
                <?php } } else { ?>
                  <tr><td colspan="6" style="text-align: center; color: var(--spark-text-secondary);"><?= lang('no_data_available'); ?></td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tab 2: Quotes -->
        <div id="tab-quotes" class="spark-tab-content">
          <div class="spark-table-wrapper">
            <table class="spark-dashboard-table">
              <thead>
                <tr>
                  <th><?= lang('date'); ?></th>
                  <th><?= lang('reference_no'); ?></th>
                  <th><?= lang('customer'); ?></th>
                  <th><?= lang('status'); ?></th>
                  <th style="text-align: end;"><?= lang('total'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($quotes)) {
                  foreach ($quotes as $row) { ?>
                    <tr>
                      <td><?= $row->date ? date('Y-m-d', strtotime($row->date)) : ''; ?></td>
                      <td><strong><a href="<?= admin_url('quotes/modal_view/' . $row->id); ?>" data-toggle="modal" data-target="#myModal" style="color: var(--spark-primary, #2563eb);"><?= $row->reference_no; ?></a></strong></td>
                      <td><?= $row->customer; ?></td>
                      <td><span class="spark-badge spark-badge-info"><?= lang($row->status ?? 'pending'); ?></span></td>
                      <td style="text-align: end; font-weight: 600;"><?= $this->sma->formatMoney($row->grand_total); ?></td>
                    </tr>
                <?php } } else { ?>
                  <tr><td colspan="5" style="text-align: center; color: var(--spark-text-secondary);"><?= lang('no_data_available'); ?></td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php } ?>

      <!-- Tab 3: Purchases -->
      <?php if ($can_view_purchases) { ?>
        <div id="tab-purchases" class="spark-tab-content <?= (!$can_view_sales) ? 'is-active' : ''; ?>">
          <div class="spark-table-wrapper">
            <table class="spark-dashboard-table">
              <thead>
                <tr>
                  <th><?= lang('date'); ?></th>
                  <th><?= lang('reference_no'); ?></th>
                  <th><?= lang('supplier'); ?></th>
                  <th><?= lang('status'); ?></th>
                  <th style="text-align: end;"><?= lang('total'); ?></th>
                  <th style="text-align: end;"><?= lang('paid'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_purchases)) {
                  foreach ($recent_purchases as $row) { ?>
                    <tr>
                      <td><?= $row->date ? date('Y-m-d', strtotime($row->date)) : ''; ?></td>
                      <td><strong><a href="<?= admin_url('purchases/modal_view/' . $row->id); ?>" data-toggle="modal" data-target="#myModal" style="color: var(--spark-primary, #2563eb);"><?= $row->reference_no; ?></a></strong></td>
                      <td><?= $row->supplier; ?></td>
                      <td>
                        <span class="spark-badge <?= ($row->status == 'received') ? 'spark-badge-success' : 'spark-badge-warning'; ?>">
                          <?= lang($row->status ?? 'received'); ?>
                        </span>
                      </td>
                      <td style="text-align: end; font-weight: 600;"><?= $this->sma->formatMoney($row->grand_total); ?></td>
                      <td style="text-align: end;"><?= $this->sma->formatMoney($row->paid); ?></td>
                    </tr>
                <?php } } else { ?>
                  <tr><td colspan="6" style="text-align: center; color: var(--spark-text-secondary);"><?= lang('no_data_available'); ?></td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php } ?>

      <!-- Tab 4: Transfers -->
      <div id="tab-transfers" class="spark-tab-content">
        <div class="spark-table-wrapper">
          <table class="spark-dashboard-table">
            <thead>
              <tr>
                <th><?= lang('date'); ?></th>
                <th><?= lang('reference_no'); ?></th>
                <th><?= lang('from'); ?></th>
                <th><?= lang('to'); ?></th>
                <th><?= lang('status'); ?></th>
                <th style="text-align: end;"><?= lang('total'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($transfers)) {
                foreach ($transfers as $row) { ?>
                  <tr>
                    <td><?= $row->date ? date('Y-m-d', strtotime($row->date)) : ''; ?></td>
                    <td><strong><a href="<?= admin_url('transfers/view/' . $row->id); ?>" data-toggle="modal" data-target="#myModal" style="color: var(--spark-primary, #2563eb);"><?= $row->transfer_no; ?></a></strong></td>
                    <td><?= $row->from_warehouse_name; ?></td>
                    <td><?= $row->to_warehouse_name; ?></td>
                    <td><span class="spark-badge spark-badge-success"><?= lang($row->status ?? 'completed'); ?></span></td>
                    <td style="text-align: end; font-weight: 600;"><?= $this->sma->formatMoney($row->grand_total); ?></td>
                  </tr>
              <?php } } else { ?>
                <tr><td colspan="6" style="text-align: center; color: var(--spark-text-secondary);"><?= lang('no_data_available'); ?></td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 5: Payments -->
      <div id="tab-payments" class="spark-tab-content">
        <div class="spark-table-wrapper">
          <table class="spark-dashboard-table">
            <thead>
              <tr>
                <th><?= lang('date'); ?></th>
                <th><?= lang('reference_no'); ?></th>
                <th><?= lang('party_name'); ?></th>
                <th><?= lang('paid_by'); ?></th>
                <th style="text-align: end;"><?= lang('amount'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recent_payments)) {
                foreach ($recent_payments as $payRow) { ?>
                  <tr>
                    <td><?= $payRow->date ? date('Y-m-d', strtotime($payRow->date)) : ''; ?></td>
                    <td><strong><a href="<?= admin_url('sales/payment_note/' . $payRow->id); ?>" data-toggle="modal" data-target="#myModal" style="color: var(--spark-primary, #2563eb);"><?= $payRow->reference_no; ?></a></strong></td>
                    <td><?= $payRow->party_name; ?></td>
                    <td><span class="spark-badge spark-badge-info"><?= lang($payRow->paid_by ?? 'cash'); ?></span></td>
                    <td style="text-align: end; font-weight: 600; color: var(--spark-success, #10b981);"><?= $this->sma->formatMoney($payRow->amount); ?></td>
                  </tr>
              <?php } } else { ?>
                <tr><td colspan="5" style="text-align: center; color: var(--spark-text-secondary);"><?= lang('no_data_available'); ?></td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- =========================================================================
     8. ECHARTS DATA PAYLOAD & SCRIPTS
     ========================================================================= -->
<script type="text/javascript">
  window.SparkDashboardData = {
    assetsUrl: <?= json_encode($assets, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
    months: <?= json_encode($chartMonths); ?>,
    monthlySales: <?= json_encode($chartSales); ?>,
    monthlyPurchases: <?= json_encode($chartPurchases); ?>,
    plRevenues: <?= (float)$accRevenue; ?>,
    plExpenses: <?= (float)$accExpenses; ?>,
    plProfit: <?= (float)$accNetProfit; ?>,
    dueS: <?= (float)$salesDueVal; ?>,
    dueP: <?= (float)$purchasesDueVal; ?>,
    agedReceivables: <?= json_encode($aged_receivables ?? [0, 0, 0, 0]); ?>,
    agedPayables: <?= json_encode($aged_payables ?? [0, 0, 0, 0]); ?>,
    lang: {
      sales: <?= json_encode(lang('sales')); ?>,
      purchases: <?= json_encode(lang('purchases')); ?>,
      profit_and_loss: <?= json_encode(lang('profit_and_loss')); ?>,
      Revenues: <?= json_encode(lang('Revenues')); ?>,
      expenses: <?= json_encode(lang('expenses')); ?>,
      profit: <?= json_encode(lang('profit')); ?>,
      Customers_Dues: <?= json_encode(lang('Customers_Dues')); ?>,
      Suppliers_Dues: <?= json_encode(lang('Suppliers_Dues')); ?>,
      days: <?= json_encode(lang('days') ?: 'Days'); ?>
    }
  };

  window.SparkFilter = {
    setPeriod: function(period) {
      var url = new URL(window.location.href);
      url.searchParams.set('period', period);
      url.searchParams.delete('start_date');
      url.searchParams.delete('end_date');
      url.searchParams.delete('from');
      url.searchParams.delete('to');
      window.location.href = url.toString();
    },
    setWarehouse: function(whId) {
      var url = new URL(window.location.href);
      if (whId) {
        url.searchParams.set('warehouse', whId);
      } else {
        url.searchParams.delete('warehouse');
      }
      window.location.href = url.toString();
    },
    toggleCustomDrawer: function() {
      var d = document.getElementById('sparkCustomDrawer');
      if (d) {
        d.style.display = (d.style.display === 'none' || d.style.display === '') ? 'block' : 'none';
      }
    }
  };
</script>
<script src="<?= $assets; ?>js/spark-dashboard.js"></script>
