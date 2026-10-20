<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Define Permission Helper Flags from native Spark ERP permission model
$can_view_products    = ($Owner || $Admin || !empty($GP['products-index']));
$can_add_product      = ($Owner || $Admin || !empty($GP['products-add']));
$can_edit_product     = ($Owner || $Admin);
$can_view_cost        = ($Owner || $Admin);
$can_view_price       = ($Owner || $Admin);
$can_view_profit      = ($Owner || $Admin);
$can_view_categories  = ($Owner || $Admin);
$can_view_brands      = ($Owner || $Admin);
$can_view_units       = ($Owner || $Admin);
$can_view_warehouses  = ($Owner || $Admin);
$can_view_transfers   = ($Owner || $Admin);
$can_view_adjustments = ($Owner || $Admin);
$can_view_barcodes    = ($Owner || $Admin);
$can_view_stock_count = ($Owner || $Admin);
$can_view_revaluation = ($Owner || $Admin);

// Build Quick Actions dynamically
$quick_actions = [
    [
        'title' => lang('add_product'),
        'desc'  => lang('add_new_product'),
        'url'   => admin_url('products/add'),
        'icon'  => 'fa-plus-circle',
        'perm'  => $can_add_product,
    ],
    [
        'title' => lang('list_products'),
        'desc'  => lang('products'),
        'url'   => admin_url('products'),
        'icon'  => 'fa-cubes',
        'perm'  => $can_view_products,
    ],
    [
        'title' => lang('categories'),
        'desc'  => lang('product_categories'),
        'url'   => admin_url('products/categories'),
        'icon'  => 'fa-folder-open-o',
        'perm'  => $can_view_categories,
    ],
    [
        'title' => lang('brands'),
        'desc'  => lang('product_brands'),
        'url'   => admin_url('products/brands'),
        'icon'  => 'fa-tag',
        'perm'  => $can_view_brands,
    ],
    [
        'title' => lang('units'),
        'desc'  => lang('measurement_units'),
        'url'   => admin_url('products/units'),
        'icon'  => 'fa-balance-scale',
        'perm'  => $can_view_units,
    ],
    [
        'title' => lang('warehouses'),
        'desc'  => lang('warehouse_stock'),
        'url'   => admin_url('products/warehouses'),
        'icon'  => 'fa-building',
        'perm'  => $can_view_warehouses,
    ],
    [
        'title' => lang('transfers'),
        'desc'  => lang('stock_transfers'),
        'url'   => admin_url('products/transfers'),
        'icon'  => 'fa-exchange',
        'perm'  => $can_view_transfers,
    ],
    [
        'title' => lang('Quantity_adjustments'),
        'desc'  => lang('inventory_adjustments'),
        'url'   => admin_url('products/quantity_adjust'),
        'icon'  => 'fa-sliders',
        'perm'  => $can_view_adjustments,
    ],
];
$quick_actions = array_filter($quick_actions, function($qa) { return !empty($qa['perm']); });

// Build Header Dropdown Actions dynamically
$header_actions = [];
if ($can_view_products) {
    $header_actions[] = ['url' => admin_url('products'), 'icon' => 'fa-cubes', 'label' => lang('list_products')];
}
if ($can_view_categories) {
    $header_actions[] = ['url' => admin_url('products/categories'), 'icon' => 'fa-folder-open-o', 'label' => lang('categories')];
}
if ($can_view_brands) {
    $header_actions[] = ['url' => admin_url('products/brands'), 'icon' => 'fa-tag', 'label' => lang('brands')];
}
if ($can_view_units) {
    $header_actions[] = ['url' => admin_url('products/units'), 'icon' => 'fa-balance-scale', 'label' => lang('units')];
}
if ($can_view_warehouses) {
    $header_actions[] = ['url' => admin_url('products/warehouses'), 'icon' => 'fa-building', 'label' => lang('warehouses')];
}
if ($can_view_transfers) {
    $header_actions[] = ['url' => admin_url('products/transfers'), 'icon' => 'fa-exchange', 'label' => lang('transfers')];
}
if ($can_view_adjustments) {
    $header_actions[] = ['url' => admin_url('products/quantity_adjust'), 'icon' => 'fa-sliders', 'label' => lang('Quantity_adjustments')];
}
if ($can_view_barcodes) {
    $header_actions[] = ['url' => admin_url('products/print_barcodes'), 'icon' => 'fa-barcode', 'label' => lang('print_barcode_label')];
}
if ($can_view_revaluation) {
    $header_actions[] = ['url' => admin_url('products/revaluation'), 'icon' => 'fa-calculator', 'label' => lang('Re-evaluation_of_cost_prices')];
}

// Prepare safe default values
$stats = $stats ?? [
    'total_products'          => 0,
    'active_products'         => 0,
    'inactive_products'       => 0,
    'low_stock_products'      => 0,
    'out_of_stock_products'   => 0,
    'total_stock_quantity'    => 0,
    'total_cost_value'        => 0,
    'total_sale_value'        => 0,
    'potential_profit'        => 0,
    'potential_profit_margin' => 0,
    'categories_count'        => 0,
    'brands_count'            => 0,
    'warehouses_count'        => 0,
];

$monthly_trends         = $monthly_trends ?? [];
$top_selling            = $top_selling ?? [];
$top_stock              = $top_stock ?? [];
$category_distribution  = $category_distribution ?? [];
$warehouse_distribution = $warehouse_distribution ?? [];
$stock_alerts           = $stock_alerts ?? [];
$recent_products        = $recent_products ?? [];
$warehouses             = $warehouses ?? [];
$warehouse_id           = $warehouse_id ?? null;

// Calculate inventory status percentages safely
$total_prod = max(1, (int)$stats['total_products']);
$active_pct = round(((int)$stats['active_products'] / $total_prod) * 100, 1);
$inactive_pct = round(((int)$stats['inactive_products'] / $total_prod) * 100, 1);
$low_stock_pct = round(((int)$stats['low_stock_products'] / $total_prod) * 100, 1);
$out_of_stock_pct = round(((int)$stats['out_of_stock_products'] / $total_prod) * 100, 1);

// Prepare ECharts Trend data
$trend_months = [];
$trend_sales = [];
$trend_purchases = [];

if (!empty($monthly_trends)) {
    foreach ($monthly_trends as $trend) {
        $trend_months[] = date('M Y', strtotime($trend['month'] . '-01'));
        $trend_sales[] = (float)$trend['sales_qty'];
        $trend_purchases[] = (float)$trend['purchase_qty'];
    }
}
?>

<div class="spark-dashboard spark-products-dashboard">

  <!-- =========================================================================
       1. PAGE HEADER & ACTIONS
       ========================================================================= -->
  <div class="spark-products-header">
    <div class="spark-products-header-start">
      <div class="spark-products-header-icon">
        <i class="fa fa-cube"></i>
      </div>
      <div>
        <h1 class="spark-products-title"><?= lang('products_dashboard'); ?></h1>
        <p class="spark-products-subtitle"><?= lang('manage_products_desc'); ?></p>
      </div>
    </div>

    <div class="spark-products-header-actions">
      <!-- Warehouse Filter Selector -->
      <?php if (!empty($warehouses) && count($warehouses) > 1 && ($Owner || $Admin || empty($this->session->userdata('warehouse_id')))) { ?>
        <select class="spark-products-wh-select" onchange="location.href='<?= admin_url('products/dashboard?warehouse='); ?>' + this.value;">
          <option value=""><?= lang('all_warehouses'); ?></option>
          <?php foreach ($warehouses as $wh) { ?>
            <option value="<?= $wh->id; ?>" <?= ($warehouse_id == $wh->id) ? 'selected' : ''; ?>>
              <?= htmlspecialchars($wh->name); ?>
            </option>
          <?php } ?>
        </select>
      <?php } ?>

      <!-- Date Pill -->
      <div class="spark-dashboard-date-pill">
        <i class="fa fa-calendar"></i>
        <span><?= date('Y-m-d'); ?></span>
      </div>

      <!-- Action: Print Dashboard -->
      <button type="button" class="spark-btn spark-btn-outline spark-btn-sm" onclick="window.print();">
        <i class="fa fa-print"></i> <?= lang('print_dashboard'); ?>
      </button>

      <!-- Add Product Primary Action -->
      <?php if ($can_add_product): ?>
        <a href="<?= admin_url('products/add'); ?>" class="spark-btn spark-btn-primary spark-btn-sm">
          <i class="fa fa-plus-circle"></i> <?= lang('add_product'); ?>
        </a>
      <?php endif; ?>

      <!-- Stock Counts Action -->
      <?php if ($can_view_stock_count): ?>
        <a href="<?= admin_url('products/stock_counts'); ?>" class="spark-btn spark-btn-outline spark-btn-sm">
          <i class="fa fa-list-ol"></i> <?= lang('stock_counts'); ?>
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

  <!-- =========================================================================
       8. QUICK ACTIONS HUB
       ========================================================================= -->
  <?php if (!empty($quick_actions)): ?>
    <div class="spark-products-grid-row">
      <div class="spark-prod-col-12">
        <div class="spark-prod-card">
          <div class="spark-prod-card-header">
            <h3 class="spark-prod-card-title">
              <i class="fa fa-bolt" style="color: #f59e0b;"></i> <?= lang('quick_actions'); ?>
            </h3>
          </div>
          <div class="spark-prod-card-body">
            <div class="spark-prod-actions-grid">
              <?php foreach ($quick_actions as $qa): ?>
                <a href="<?= $qa['url']; ?>" class="spark-prod-action-tile">
                  <div class="spark-prod-action-icon">
                    <i class="fa <?= $qa['icon']; ?>"></i>
                  </div>
                  <div class="spark-prod-action-info">
                    <span class="spark-prod-action-title"><?= $qa['title']; ?></span>
                    <span class="spark-prod-action-desc"><?= $qa['desc']; ?></span>
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
  <div class="spark-products-kpi-grid">
    <?php if ($Owner || $Admin): ?>
      <!-- Card 1: Total Products -->
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon icon-indigo">
            <i class="fa fa-cubes"></i>
          </div>
          <span class="spark-products-kpi-badge badge-indigo">
            <?= (int)$stats['categories_count']; ?> <?= lang('categories'); ?>
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val"><?= number_format($stats['total_products']); ?></div>
          <div class="spark-products-kpi-lbl"><?= lang('total_products'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('brands'); ?>: <strong><?= (int)$stats['brands_count']; ?></strong></span>
          <a href="<?= admin_url('products'); ?>" style="color: var(--spark-primary);"><i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <!-- Card 2: Active Products -->
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon icon-emerald">
            <i class="fa fa-check-circle"></i>
          </div>
          <span class="spark-products-kpi-badge badge-emerald">
            <?= $active_pct; ?>%
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val"><?= number_format($stats['active_products']); ?></div>
          <div class="spark-products-kpi-lbl"><?= lang('active_products'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('inactive_products'); ?>: <strong><?= number_format($stats['inactive_products']); ?></strong></span>
          <i class="fa fa-circle" style="color: var(--spark-success); font-size: 8px;"></i>
        </div>
      </div>

      <!-- Card 3: Out of Stock -->
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon icon-rose">
            <i class="fa fa-times-circle"></i>
          </div>
          <span class="spark-products-kpi-badge badge-rose">
            <?= $out_of_stock_pct; ?>%
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val" style="color: var(--spark-danger);"><?= number_format($stats['out_of_stock_products']); ?></div>
          <div class="spark-products-kpi-lbl"><?= lang('out_of_stock_products'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('critical_stock'); ?></span>
          <i class="fa fa-exclamation-triangle" style="color: var(--spark-danger);"></i>
        </div>
      </div>

      <!-- Card 4: Low Stock -->
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon icon-amber">
            <i class="fa fa-bell"></i>
          </div>
          <span class="spark-products-kpi-badge badge-amber">
            <?= $low_stock_pct; ?>%
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val" style="color: var(--spark-warning);"><?= number_format($stats['low_stock_products']); ?></div>
          <div class="spark-products-kpi-lbl"><?= lang('low_stock_products'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('alerts'); ?></span>
          <i class="fa fa-clock-o" style="color: var(--spark-warning);"></i>
        </div>
      </div>

      <!-- Card 5: Total Stock Quantity -->
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon icon-cyan">
            <i class="fa fa-archive"></i>
          </div>
          <span class="spark-products-kpi-badge badge-cyan">
            <?= (int)$stats['warehouses_count']; ?> <?= lang('warehouses'); ?>
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val"><?= number_format($stats['total_stock_quantity'], 2); ?></div>
          <div class="spark-products-kpi-lbl"><?= lang('total_stock_quantity'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('inventory_overview'); ?></span>
          <i class="fa fa-cubes"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 6: Total Cost Value -->
    <?php if ($can_view_cost): ?>
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon icon-purple">
            <i class="fa fa-money"></i>
          </div>
          <span class="spark-products-kpi-badge badge-purple">
            <?= lang('stock_value'); ?>
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val"><?= $this->sma->formatMoney($stats['total_cost_value']); ?></div>
          <div class="spark-products-kpi-lbl"><?= lang('total_cost_value'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('product_cost'); ?></span>
          <i class="fa fa-calculator"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 7: Total Retail Value -->
    <?php if ($can_view_price): ?>
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon icon-teal">
            <i class="fa fa-line-chart"></i>
          </div>
          <span class="spark-products-kpi-badge badge-teal">
            <?= lang('retail_value'); ?>
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val"><?= $this->sma->formatMoney($stats['total_sale_value']); ?></div>
          <div class="spark-products-kpi-lbl"><?= lang('total_sale_value'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('selling_price'); ?></span>
          <i class="fa fa-tag"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 8: Potential Profit (Requires BOTH Cost and Price Permissions) -->
    <?php if ($can_view_profit): ?>
      <?php $is_profit = ($stats['potential_profit'] >= 0); ?>
      <div class="spark-products-kpi-card">
        <div class="spark-products-kpi-top">
          <div class="spark-products-kpi-icon <?= $is_profit ? 'icon-emerald' : 'icon-rose'; ?>">
            <i class="fa fa-area-chart"></i>
          </div>
          <span class="spark-products-kpi-badge <?= $is_profit ? 'badge-emerald' : 'badge-rose'; ?>">
            <?= $stats['potential_profit_margin']; ?>% <?= lang('potential_profit_margin'); ?>
          </span>
        </div>
        <div class="spark-products-kpi-content">
          <div class="spark-products-kpi-val" style="color: <?= $is_profit ? 'var(--spark-success)' : 'var(--spark-danger)'; ?>;">
            <?= $this->sma->formatMoney($stats['potential_profit']); ?>
          </div>
          <div class="spark-products-kpi-lbl"><?= lang('potential_profit'); ?></div>
        </div>
        <div class="spark-products-kpi-footer">
          <span><?= lang('margin'); ?>: <strong><?= $stats['potential_profit_margin']; ?>%</strong></span>
          <i class="fa fa-percent"></i>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- =========================================================================
       3. INVENTORY STATUS & INVENTORY VALUATION ROW
       ========================================================================= -->
  <?php
    $show_inv_status = $can_view_products;
    $show_inv_valuation = ($can_view_cost || $can_view_price);
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-products-grid-row">
      <!-- Inventory Status -->
      <?php if ($show_inv_status): ?>
        <div class="<?= $show_inv_valuation ? 'spark-prod-col-6' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-pie-chart" style="color: var(--spark-primary, #6366f1);"></i> <?= lang('inventory_status'); ?>
              </h3>
              <span class="spark-products-kpi-badge badge-indigo">
                <?= number_format($stats['total_products']); ?> <?= lang('products'); ?>
              </span>
            </div>
            <div class="spark-prod-card-body">
              <div class="spark-prod-status-list">
                <!-- Active -->
                <div class="spark-prod-status-item">
                  <div class="spark-prod-status-header">
                    <span class="spark-prod-status-label">
                      <i class="fa fa-circle" style="color: #10b981; font-size: 9px;"></i> <?= lang('active_products'); ?>
                    </span>
                    <span class="spark-prod-status-count"><?= number_format($stats['active_products']); ?> (<?= $active_pct; ?>%)</span>
                  </div>
                  <div class="spark-prod-progress-track">
                    <div class="spark-prod-progress-fill fill-success" style="width: <?= min(100, $active_pct); ?>%;"></div>
                  </div>
                </div>

                <!-- Out of Stock -->
                <div class="spark-prod-status-item">
                  <div class="spark-prod-status-header">
                    <span class="spark-prod-status-label">
                      <i class="fa fa-circle" style="color: #f43f5e; font-size: 9px;"></i> <?= lang('out_of_stock_products'); ?>
                    </span>
                    <span class="spark-prod-status-count"><?= number_format($stats['out_of_stock_products']); ?> (<?= $out_of_stock_pct; ?>%)</span>
                  </div>
                  <div class="spark-prod-progress-track">
                    <div class="spark-prod-progress-fill fill-danger" style="width: <?= min(100, $out_of_stock_pct); ?>%;"></div>
                  </div>
                </div>

                <!-- Low Stock -->
                <div class="spark-prod-status-item">
                  <div class="spark-prod-status-header">
                    <span class="spark-prod-status-label">
                      <i class="fa fa-circle" style="color: #f59e0b; font-size: 9px;"></i> <?= lang('low_stock_products'); ?>
                    </span>
                    <span class="spark-prod-status-count"><?= number_format($stats['low_stock_products']); ?> (<?= $low_stock_pct; ?>%)</span>
                  </div>
                  <div class="spark-prod-progress-track">
                    <div class="spark-prod-progress-fill fill-warning" style="width: <?= min(100, $low_stock_pct); ?>%;"></div>
                  </div>
                </div>

                <!-- Inactive -->
                <div class="spark-prod-status-item">
                  <div class="spark-prod-status-header">
                    <span class="spark-prod-status-label">
                      <i class="fa fa-circle" style="color: #64748b; font-size: 9px;"></i> <?= lang('inactive_products'); ?>
                    </span>
                    <span class="spark-prod-status-count"><?= number_format($stats['inactive_products']); ?> (<?= $inactive_pct; ?>%)</span>
                  </div>
                  <div class="spark-prod-progress-track">
                    <div class="spark-prod-progress-fill" style="background-color: #64748b; width: <?= min(100, $inactive_pct); ?>%;"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Inventory Valuation -->
      <?php if ($show_inv_valuation): ?>
        <?php $is_profit_val = ($stats['potential_profit'] >= 0); ?>
        <div class="<?= $show_inv_status ? 'spark-prod-col-6' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-balance-scale" style="color: #8b5cf6;"></i> <?= lang('inventory_valuation'); ?>
              </h3>
              <?php if ($can_view_profit): ?>
                <span class="spark-products-kpi-badge <?= $is_profit_val ? 'badge-emerald' : 'badge-rose'; ?>">
                  <?= $stats['potential_profit_margin']; ?>% <?= lang('margin'); ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="spark-prod-card-body">
              <?php if ($can_view_profit): ?>
                <div class="spark-prod-status-item">
                  <div class="spark-prod-status-header">
                    <span style="font-size: 13px; font-weight: 600; color: var(--spark-text-secondary);"><?= lang('total_cost_value'); ?> vs <?= lang('total_sale_value'); ?></span>
                    <span class="spark-prod-status-count"><?= $stats['potential_profit_margin']; ?>%</span>
                  </div>
                  <div class="spark-prod-progress-track" style="height: 10px;">
                    <div class="spark-prod-progress-fill <?= $is_profit_val ? 'fill-teal' : 'fill-danger'; ?>" style="width: <?= min(100, max(0, $stats['potential_profit_margin'])); ?>%;"></div>
                  </div>
                </div>
              <?php endif; ?>

              <div class="spark-prod-val-grid">
                <?php if ($can_view_cost): ?>
                  <div class="spark-prod-val-box">
                    <span class="spark-prod-val-num" style="color: #8b5cf6;"><?= $this->sma->formatMoney($stats['total_cost_value']); ?></span>
                    <span class="spark-prod-val-lbl"><?= lang('total_cost_value'); ?></span>
                  </div>
                <?php endif; ?>
                <?php if ($can_view_price): ?>
                  <div class="spark-prod-val-box">
                    <span class="spark-prod-val-num" style="color: #0d9488;"><?= $this->sma->formatMoney($stats['total_sale_value']); ?></span>
                    <span class="spark-prod-val-lbl"><?= lang('total_sale_value'); ?></span>
                  </div>
                <?php endif; ?>
                <?php if ($can_view_profit): ?>
                  <div class="spark-prod-val-box">
                    <span class="spark-prod-val-num" style="color: <?= $is_profit_val ? 'var(--spark-success)' : 'var(--spark-danger)'; ?>;"><?= $this->sma->formatMoney($stats['potential_profit']); ?></span>
                    <span class="spark-prod-val-lbl"><?= lang('potential_profit'); ?></span>
                  </div>
                  <div class="spark-prod-val-box">
                    <span class="spark-prod-val-num" style="color: var(--spark-primary);"><?= $stats['potential_profit_margin']; ?>%</span>
                    <span class="spark-prod-val-lbl"><?= lang('potential_profit_margin'); ?></span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       4. STOCK MOVEMENT TREND + CATEGORY DISTRIBUTION ROW
       ========================================================================= -->
  <?php
    $show_trend = $can_view_products;
    $show_cat_dist = $can_view_categories;
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-products-grid-row">
      <!-- Movement Trend Chart Card -->
      <?php if ($show_trend): ?>
        <div class="<?= $show_cat_dist ? 'spark-prod-col-7' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-line-chart" style="color: var(--spark-primary, #6366f1);"></i> <?= lang('stock_movement_trend'); ?>
              </h3>
              <span class="spark-products-kpi-badge badge-indigo">
                <i class="fa fa-calendar"></i> <?= lang('last_12_months'); ?>
              </span>
            </div>
            <div class="spark-prod-card-body" style="padding: 16px;">
              <?php if (!empty($monthly_trends)) { ?>
                <div id="echart_products_trend" style="width: 100%; height: 320px;"></div>
              <?php } else { ?>
                <div class="spark-prod-empty-state">
                  <i class="fa fa-bar-chart"></i>
                  <div class="spark-prod-empty-title"><?= lang('no_data'); ?></div>
                  <div class="spark-prod-empty-desc"><?= lang('no_products_found'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Category Stock Distribution Card -->
      <?php if ($show_cat_dist): ?>
        <div class="<?= $show_trend ? 'spark-prod-col-5' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-folder-open-o" style="color: #06b6d4;"></i> <?= lang('category_distribution'); ?>
              </h3>
              <a href="<?= admin_url('products/categories'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                <i class="fa fa-external-link"></i> <?= lang('categories'); ?>
              </a>
            </div>
            <div class="spark-prod-card-body">
              <?php if (!empty($category_distribution)) { ?>
                <div class="spark-prod-dist-list">
                  <?php foreach ($category_distribution as $cat) { ?>
                    <div class="spark-prod-dist-item">
                      <div class="spark-prod-dist-header">
                        <span class="spark-prod-dist-name">
                          <i class="fa fa-circle" style="font-size: 8px; color: #06b6d4;"></i>
                          <?= htmlspecialchars($cat->name); ?>
                          <small style="color: var(--spark-text-muted);">(<?= (int)$cat->product_count; ?> <?= lang('products'); ?>)</small>
                        </span>
                        <?php if ($can_view_price || $can_view_cost): ?>
                          <span class="spark-prod-dist-val"><?= $this->sma->formatMoney($cat->total_value); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="spark-prod-progress-track" style="height: 6px;">
                        <div class="spark-prod-progress-fill fill-cyan" style="width: <?= min(100, $cat->pct); ?>%;"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-prod-empty-state">
                  <i class="fa fa-folder-o"></i>
                  <div class="spark-prod-empty-title"><?= lang('no_data'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       5. TOP SELLING PRODUCTS & TOP VALUED STOCK ROW
       ========================================================================= -->
  <?php
    $show_top_selling = $can_view_products;
    $show_top_stock   = ($can_view_products && ($can_view_cost || $can_view_price));
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-products-grid-row">
      <!-- Top Selling Products -->
      <?php if ($show_top_selling): ?>
        <div class="<?= $show_top_stock ? 'spark-prod-col-6' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-trophy" style="color: #f59e0b;"></i> <?= lang('top_selling_products'); ?>
              </h3>
              <span class="spark-products-kpi-badge badge-amber">
                <?= lang('top_5'); ?>
              </span>
            </div>
            <div class="spark-prod-card-body">
              <?php if (!empty($top_selling)) { ?>
                <div class="spark-prod-ranked-list">
                  <?php $rank = 1; foreach ($top_selling as $item) { ?>
                    <div class="spark-prod-ranked-item">
                      <div class="spark-prod-ranked-left">
                        <span class="spark-prod-rank-num rank-<?= $rank; ?>"><?= $rank; ?></span>
                        <div>
                          <div class="spark-prod-ranked-name"><?= htmlspecialchars($item->name); ?></div>
                          <div class="spark-prod-ranked-code"><?= htmlspecialchars($item->code); ?></div>
                        </div>
                      </div>
                      <div class="spark-prod-ranked-right">
                        <span class="spark-prod-ranked-val" style="color: var(--spark-primary);"><?= number_format($item->sold_qty, 2); ?> <?= htmlspecialchars($item->unit_name ?: ''); ?></span>
                        <?php if ($can_view_price): ?>
                          <span class="spark-prod-ranked-sub"><?= $this->sma->formatMoney($item->sales_amount); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php $rank++; } ?>
                </div>
              <?php } else { ?>
                <div class="spark-prod-empty-state">
                  <i class="fa fa-trophy"></i>
                  <div class="spark-prod-empty-title"><?= lang('no_data'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Top Valued Stock Products -->
      <?php if ($show_top_stock): ?>
        <div class="<?= $show_top_selling ? 'spark-prod-col-6' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-diamond" style="color: #8b5cf6;"></i> <?= lang('top_stock_products'); ?>
              </h3>
              <span class="spark-products-kpi-badge badge-purple">
                <?= lang('top_5'); ?>
              </span>
            </div>
            <div class="spark-prod-card-body">
              <?php if (!empty($top_stock)) { ?>
                <div class="spark-prod-ranked-list">
                  <?php $rank = 1; foreach ($top_stock as $item) { ?>
                    <div class="spark-prod-ranked-item">
                      <div class="spark-prod-ranked-left">
                        <span class="spark-prod-rank-num rank-<?= $rank; ?>"><?= $rank; ?></span>
                        <div>
                          <div class="spark-prod-ranked-name"><?= htmlspecialchars($item->name); ?></div>
                          <div class="spark-prod-ranked-code"><?= htmlspecialchars($item->code); ?></div>
                        </div>
                      </div>
                      <div class="spark-prod-ranked-right">
                        <?php if ($can_view_cost): ?>
                          <span class="spark-prod-ranked-val" style="color: #8b5cf6;"><?= $this->sma->formatMoney($item->stock_value); ?></span>
                        <?php endif; ?>
                        <span class="spark-prod-ranked-sub"><?= number_format($item->quantity, 2); ?> <?= htmlspecialchars($item->unit_name ?: ''); ?></span>
                      </div>
                    </div>
                  <?php $rank++; } ?>
                </div>
              <?php } else { ?>
                <div class="spark-prod-empty-state">
                  <i class="fa fa-archive"></i>
                  <div class="spark-prod-empty-title"><?= lang('no_data'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       6. STOCK ALERTS & WAREHOUSE DISTRIBUTION ROW
       ========================================================================= -->
  <?php
    $show_alerts = ($can_view_products || $can_view_adjustments);
    $show_wh_dist = $can_view_warehouses;
  ?>
  <?php if ($Owner || $Admin): ?>
    <div class="spark-products-grid-row">
      <!-- Stock Alerts -->
      <?php if ($show_alerts): ?>
        <div class="<?= $show_wh_dist ? 'spark-prod-col-6' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-bell" style="color: #f43f5e;"></i> <?= lang('stock_alerts'); ?>
              </h3>
              <span class="spark-products-kpi-badge badge-rose">
                <?= (int)$stats['out_of_stock_products'] + (int)$stats['low_stock_products']; ?> <?= lang('alerts'); ?>
              </span>
            </div>
            <div class="spark-prod-card-body">
              <?php if (!empty($stock_alerts)) { ?>
                <div class="spark-prod-alert-list">
                  <?php foreach ($stock_alerts as $alert) {
                    $is_critical = ($alert->alert_level == 'critical' || $alert->current_stock <= 0);
                  ?>
                    <div class="spark-prod-alert-item <?= $is_critical ? 'alert-critical' : 'alert-warning'; ?>">
                      <div class="spark-prod-alert-left">
                        <span class="spark-prod-alert-name"><?= htmlspecialchars($alert->name); ?></span>
                        <span class="spark-prod-alert-code"><?= htmlspecialchars($alert->code); ?></span>
                      </div>
                      <div class="spark-prod-alert-right">
                        <span class="spark-prod-alert-stock" style="color: <?= $is_critical ? 'var(--spark-danger)' : 'var(--spark-warning)'; ?>;">
                          <?= number_format($alert->current_stock, 2); ?> <?= htmlspecialchars($alert->unit_name ?: ''); ?>
                        </span>
                        <span class="spark-products-kpi-badge <?= $is_critical ? 'badge-rose' : 'badge-amber'; ?>">
                          <?= $is_critical ? lang('out_of_stock') : lang('low_stock_products'); ?>
                        </span>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-prod-empty-state">
                  <i class="fa fa-check-circle" style="color: var(--spark-success);"></i>
                  <div class="spark-prod-empty-title"><?= lang('no_alerts'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Warehouse Distribution -->
      <?php if ($show_wh_dist): ?>
        <div class="<?= $show_alerts ? 'spark-prod-col-6' : 'spark-prod-col-12'; ?>">
          <div class="spark-prod-card">
            <div class="spark-prod-card-header">
              <h3 class="spark-prod-card-title">
                <i class="fa fa-building" style="color: #0d9488;"></i> <?= lang('warehouse_distribution'); ?>
              </h3>
              <a href="<?= admin_url('products/warehouses'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                <i class="fa fa-external-link"></i> <?= lang('warehouses'); ?>
              </a>
            </div>
            <div class="spark-prod-card-body">
              <?php if (!empty($warehouse_distribution)) { ?>
                <div class="spark-prod-dist-list">
                  <?php
                    $wh_limit = 6;
                    $wh_count = 0;
                    foreach ($warehouse_distribution as $wh) {
                      if ($wh_count >= $wh_limit) break;
                  ?>
                    <div class="spark-prod-dist-item">
                      <div class="spark-prod-dist-header">
                        <span class="spark-prod-dist-name">
                          <i class="fa fa-circle" style="font-size: 8px; color: #0d9488;"></i>
                          <?= htmlspecialchars($wh->name); ?>
                          <small style="color: var(--spark-text-muted);">(<?= (int)$wh->product_count; ?> <?= lang('products'); ?>)</small>
                        </span>
                        <span class="spark-prod-dist-val"><?= number_format($wh->total_qty, 2); ?></span>
                      </div>
                      <div class="spark-prod-progress-track" style="height: 6px;">
                        <div class="spark-prod-progress-fill fill-teal" style="width: <?= min(100, $wh->pct); ?>%;"></div>
                      </div>
                    </div>
                  <?php $wh_count++; } ?>
                </div>
              <?php } else { ?>
                <div class="spark-prod-empty-state">
                  <i class="fa fa-building-o"></i>
                  <div class="spark-prod-empty-title"><?= lang('no_data'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       7. RECENTLY ADDED PRODUCTS TABLE
       ========================================================================= -->
  <?php if ($Owner || $Admin): ?>
    <div class="spark-products-grid-row">
      <div class="spark-prod-col-12">
        <div class="spark-prod-card">
          <div class="spark-prod-card-header">
            <h3 class="spark-prod-card-title">
              <i class="fa fa-list-alt" style="color: var(--spark-primary, #6366f1);"></i> <?= lang('recent_products'); ?>
            </h3>
            <a href="<?= admin_url('products'); ?>" class="spark-btn spark-btn-outline spark-btn-sm">
              <i class="fa fa-external-link"></i> <?= lang('list_products'); ?>
            </a>
          </div>
          <div class="spark-prod-card-body" style="padding: 0;">
            <?php if (!empty($recent_products)) { ?>
              <div class="spark-prod-table-responsive">
                <table class="spark-prod-table">
                  <thead>
                    <tr>
                      <th><?= lang('product_code'); ?></th>
                      <th><?= lang('product_name'); ?></th>
                      <th><?= lang('category'); ?></th>
                      <?php if ($can_view_cost): ?>
                        <th><?= lang('product_cost'); ?></th>
                      <?php endif; ?>
                      <?php if ($can_view_price): ?>
                        <th><?= lang('product_price'); ?></th>
                      <?php endif; ?>
                      <th><?= lang('current_stock'); ?></th>
                      <th><?= lang('product_type'); ?></th>
                      <?php if ($can_view_products || $can_edit_product): ?>
                        <th style="text-align: center;"><?= lang('actions'); ?></th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_products as $prod) { ?>
                      <tr>
                        <td style="font-weight: 700; font-family: var(--spark-font-mono);">
                          <?= htmlspecialchars($prod->code); ?>
                        </td>
                        <td style="font-weight: 600; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                          <?= htmlspecialchars($prod->name); ?>
                        </td>
                        <td>
                          <span class="spark-products-kpi-badge badge-cyan">
                            <?= htmlspecialchars($prod->category_name ?: 'Default'); ?>
                          </span>
                        </td>
                        <?php if ($can_view_cost): ?>
                          <td style="color: #8b5cf6; font-weight: 600;">
                            <?= $this->sma->formatMoney($prod->cost); ?>
                          </td>
                        <?php endif; ?>
                        <?php if ($can_view_price): ?>
                          <td style="color: #0d9488; font-weight: 600;">
                            <?= $this->sma->formatMoney($prod->price); ?>
                          </td>
                        <?php endif; ?>
                        <td style="font-weight: 700; color: <?= ((float)$prod->quantity > 0) ? 'var(--spark-text-primary)' : 'var(--spark-danger)'; ?>;">
                          <?= number_format((float)$prod->quantity, 2); ?> <?= htmlspecialchars($prod->unit_name ?: ''); ?>
                        </td>
                        <td>
                          <span class="spark-products-kpi-badge badge-slate">
                            <?= htmlspecialchars(lang($prod->type)); ?>
                          </span>
                        </td>
                        <?php if ($can_view_products || $can_edit_product): ?>
                          <td style="text-align: center;">
                            <div class="btn-group">
                              <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                <?= lang('actions'); ?> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?>" role="menu">
                                <?php if ($can_view_products): ?>
                                  <li><a href="<?= admin_url('products/view/' . $prod->id); ?>"><i class="fa fa-file-text-o"></i> <?= lang('product_details'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($can_edit_product): ?>
                                  <li><a href="<?= admin_url('products/edit/' . $prod->id); ?>"><i class="fa fa-edit"></i> <?= lang('edit_product'); ?></a></li>
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
              <div class="spark-prod-empty-state">
                <i class="fa fa-inbox"></i>
                <div class="spark-prod-empty-title"><?= lang('no_products_found'); ?></div>
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
    if (typeof (echarts) === 'undefined') {
        return;
    }

    var chartDom = document.getElementById('echart_products_trend');
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
            data: ['<?= lang('sales_quantity'); ?>', '<?= lang('purchase_quantity'); ?>'],
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
                name: '<?= lang('quantity'); ?>',
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
                name: '<?= lang('sales_quantity'); ?>',
                type: 'bar',
                data: <?= json_encode($trend_sales); ?>,
                itemStyle: {
                    color: brandPrimary,
                    borderRadius: [4, 4, 0, 0]
                }
            },
            {
                name: '<?= lang('purchase_quantity'); ?>',
                type: 'bar',
                data: <?= json_encode($trend_purchases); ?>,
                itemStyle: {
                    color: '#10b981',
                    borderRadius: [4, 4, 0, 0]
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
