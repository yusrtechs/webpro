<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Define Permission Helper Flags from native Spark ERP permission model
$can_view_manuf       = ($Owner || $Admin || !empty($GP['manufacturing-index']));
$can_add_manuf        = ($Owner || $Admin || !empty($GP['manufacturing-add_manufacturing']));
$can_edit_manuf       = ($Owner || $Admin);
$can_view_bom         = ($Owner || $Admin);
$can_add_bom          = ($Owner || $Admin);
$can_view_bom_details = ($Owner || $Admin);
$can_view_confirmed   = ($Owner || $Admin);
$can_view_progress    = ($Owner || $Admin);
$can_view_prog_detail = ($Owner || $Admin);
$can_view_done        = ($Owner || $Admin);
$can_view_done_detail = ($Owner || $Admin);
$can_view_transfers   = ($Owner || $Admin);
$can_view_destructions= ($Owner || $Admin);
$can_view_cost        = ($Owner || $Admin);
$can_view_any_manuf   = ($Owner || $Admin);

// Build Header Dropdown Actions dynamically
$header_actions = [];
if ($can_view_bom) {
    $header_actions[] = ['url' => admin_url('manufacturing/bom'), 'icon' => 'fa-list-alt', 'label' => lang('Bill_Of_Material')];
}
if ($can_view_manuf) {
    $header_actions[] = ['url' => admin_url('manufacturing'), 'icon' => 'fa-cogs', 'label' => lang('Manufacturing_list')];
}
if ($can_view_confirmed) {
    $header_actions[] = ['url' => admin_url('manufacturing/confirmed'), 'icon' => 'fa-clock-o', 'label' => lang('Confirmed_products')];
}
if ($can_view_progress) {
    $header_actions[] = ['url' => admin_url('manufacturing/progress'), 'icon' => 'fa-refresh', 'label' => lang('In_progress_products')];
}
if ($can_view_done) {
    $header_actions[] = ['url' => admin_url('manufacturing/done'), 'icon' => 'fa-check-circle', 'label' => lang('Done_products')];
}
if ($can_view_transfers) {
    $header_actions[] = ['url' => admin_url('manufacturing/transfers'), 'icon' => 'fa-exchange', 'label' => lang('transfers')];
}
if ($can_view_destructions) {
    $header_actions[] = ['url' => admin_url('manufacturing/destructions'), 'icon' => 'fa-trash-o', 'label' => lang('destruction_inventory')];
}

// Safely prepare data arrays
$stats = $stats ?? [
    'total_orders' => 0,
    'confirmed_orders' => 0,
    'in_progress_orders' => 0,
    'done_orders' => 0,
    'total_planned_qty' => 0,
    'total_produced_qty' => 0,
    'remaining_qty' => 0,
    'completion_rate' => 0,
    'confirmed_pct' => 0,
    'in_progress_pct' => 0,
    'done_pct' => 0,
    'total_boms' => 0,
];

$costs = $costs ?? [
    'material_cost' => 0,
    'labor_cost' => 0,
    'expense_cost' => 0,
    'total_cost' => 0,
    'avg_unit_cost' => 0,
    'material_pct' => 0,
    'labor_pct' => 0,
    'expense_pct' => 0,
];

$monthly_trends = $monthly_trends ?? [];
$top_products = $top_products ?? [];
$material_alerts = $material_alerts ?? [];
$recent_orders = $recent_orders ?? [];

// Prepare chart series data
$trend_months = [];
$trend_planned = [];
$trend_produced = [];
$trend_orders = [];

if (!empty($monthly_trends)) {
    foreach ($monthly_trends as $trend) {
        $trend_months[] = date('M Y', strtotime($trend->month . '-01'));
        $trend_planned[] = (float)$trend->planned_qty;
        $trend_produced[] = (float)$trend->produced_qty;
        $trend_orders[] = (int)$trend->order_count;
    }
}
?>

<div class="spark-dashboard spark-manufacturing-dashboard">

  <!-- =========================================================================
       1. PAGE HEADER & ACTIONS
       ========================================================================= -->
  <div class="spark-manufacturing-header">
    <div class="spark-manufacturing-header-start">
      <div class="spark-manufacturing-header-icon">
        <i class="fa fa-industry"></i>
      </div>
      <div class="spark-manufacturing-header-info">
        <h1 class="spark-manufacturing-title"><?= lang('manufacturing_dashboard'); ?></h1>
        <p class="spark-manufacturing-subtitle"><?= lang('manage_manufacturing_desc'); ?></p>
      </div>
    </div>

    <div class="spark-manufacturing-header-actions">
      <div class="spark-dashboard-date-pill">
        <i class="fa fa-calendar"></i>
        <span><?= date('Y-m-d'); ?></span>
      </div>

      <!-- Print Dashboard Action -->
      <button type="button" class="spark-btn spark-btn-outline spark-btn-sm" onclick="window.print();">
        <i class="fa fa-print"></i> <?= lang('print_dashboard'); ?>
      </button>

      <!-- Add Manufacturing Order Action -->
      <?php if ($can_add_manuf): ?>
        <a href="<?= admin_url('manufacturing/add_manufacturing'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn spark-btn-primary spark-btn-sm">
          <i class="fa fa-plus-circle"></i> <?= lang('Add_manufacturing'); ?>
        </a>
      <?php endif; ?>

      <!-- Add BOM Action -->
      <?php if ($can_add_bom): ?>
        <a href="<?= admin_url('manufacturing/add_bom'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn spark-btn-outline spark-btn-sm">
          <i class="fa fa-file-text-o"></i> <?= lang('Add_Bill_Of_Material'); ?>
        </a>
      <?php endif; ?>

      <!-- Actions Dropdown Button -->
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
       2. KPI STATISTICS SUMMARY (6 Cards Grid)
       ========================================================================= -->
  <div class="spark-manufacturing-kpi-grid">
    <?php if ($can_view_any_manuf): ?>
      <!-- Card 1: Total Manufacturing Orders -->
      <div class="spark-manufacturing-kpi-card">
        <div class="spark-manufacturing-kpi-top">
          <div class="spark-manufacturing-kpi-icon icon-indigo">
            <i class="fa fa-industry"></i>
          </div>
          <span class="spark-manufacturing-kpi-badge badge-indigo">
            <?= (int)$stats['total_boms']; ?> <?= lang('active_boms'); ?>
          </span>
        </div>
        <div class="spark-manufacturing-kpi-content">
          <div class="spark-manufacturing-kpi-val"><?= number_format($stats['total_orders']); ?></div>
          <div class="spark-manufacturing-kpi-lbl"><?= lang('total_orders'); ?></div>
        </div>
        <div class="spark-manufacturing-kpi-footer">
          <span><?= lang('Bill_Of_Material'); ?>: <strong><?= (int)$stats['total_boms']; ?></strong></span>
          <i class="fa fa-cogs"></i>
        </div>
      </div>

      <!-- Card 2: Orders In Progress -->
      <div class="spark-manufacturing-kpi-card">
        <div class="spark-manufacturing-kpi-top">
          <div class="spark-manufacturing-kpi-icon icon-amber">
            <i class="fa fa-refresh"></i>
          </div>
          <span class="spark-manufacturing-kpi-badge badge-amber">
            <?= $stats['in_progress_pct']; ?>%
          </span>
        </div>
        <div class="spark-manufacturing-kpi-content">
          <div class="spark-manufacturing-kpi-val"><?= number_format($stats['in_progress_orders']); ?></div>
          <div class="spark-manufacturing-kpi-lbl"><?= lang('orders_in_progress'); ?></div>
        </div>
        <div class="spark-manufacturing-kpi-footer">
          <span><?= lang('In_progress_products'); ?></span>
          <i class="fa fa-hourglass-half"></i>
        </div>
      </div>

      <!-- Card 3: Completed Orders -->
      <div class="spark-manufacturing-kpi-card">
        <div class="spark-manufacturing-kpi-top">
          <div class="spark-manufacturing-kpi-icon icon-emerald">
            <i class="fa fa-check-circle"></i>
          </div>
          <span class="spark-manufacturing-kpi-badge badge-emerald">
            <?= $stats['done_pct']; ?>%
          </span>
        </div>
        <div class="spark-manufacturing-kpi-content">
          <div class="spark-manufacturing-kpi-val"><?= number_format($stats['done_orders']); ?></div>
          <div class="spark-manufacturing-kpi-lbl"><?= lang('completed_orders'); ?></div>
        </div>
        <div class="spark-manufacturing-kpi-footer">
          <span><?= lang('Done_products'); ?></span>
          <i class="fa fa-check"></i>
        </div>
      </div>

      <!-- Card 4: Pending / Confirmed Orders -->
      <div class="spark-manufacturing-kpi-card">
        <div class="spark-manufacturing-kpi-top">
          <div class="spark-manufacturing-kpi-icon icon-cyan">
            <i class="fa fa-clock-o"></i>
          </div>
          <span class="spark-manufacturing-kpi-badge badge-cyan">
            <?= $stats['confirmed_pct']; ?>%
          </span>
        </div>
        <div class="spark-manufacturing-kpi-content">
          <div class="spark-manufacturing-kpi-val"><?= number_format($stats['confirmed_orders']); ?></div>
          <div class="spark-manufacturing-kpi-lbl"><?= lang('pending_orders'); ?></div>
        </div>
        <div class="spark-manufacturing-kpi-footer">
          <span><?= lang('Confirmed_products'); ?></span>
          <i class="fa fa-pause-circle-o"></i>
        </div>
      </div>

      <!-- Card 5: Total Production Volume -->
      <div class="spark-manufacturing-kpi-card">
        <div class="spark-manufacturing-kpi-top">
          <div class="spark-manufacturing-kpi-icon icon-purple">
            <i class="fa fa-cubes"></i>
          </div>
          <span class="spark-manufacturing-kpi-badge badge-purple">
            <?= $stats['completion_rate']; ?>%
          </span>
        </div>
        <div class="spark-manufacturing-kpi-content">
          <div class="spark-manufacturing-kpi-val"><?= $this->sma->formatQuantity($stats['total_produced_qty']); ?></div>
          <div class="spark-manufacturing-kpi-lbl"><?= lang('total_production_volume'); ?></div>
        </div>
        <div class="spark-manufacturing-kpi-footer">
          <span><?= lang('planned_quantity'); ?>: <strong><?= $this->sma->formatQuantity($stats['total_planned_qty']); ?></strong></span>
          <i class="fa fa-pie-chart"></i>
        </div>
      </div>
    <?php endif; ?>

    <!-- Card 6: Total Production Cost (Requires Cost Permission) -->
    <?php if ($can_view_cost): ?>
      <div class="spark-manufacturing-kpi-card">
        <div class="spark-manufacturing-kpi-top">
          <div class="spark-manufacturing-kpi-icon icon-rose">
            <i class="fa fa-money"></i>
          </div>
          <span class="spark-manufacturing-kpi-badge badge-rose">
            <?= $this->sma->formatMoney($costs['avg_unit_cost']); ?> / <?= lang('unit'); ?>
          </span>
        </div>
        <div class="spark-manufacturing-kpi-content">
          <div class="spark-manufacturing-kpi-val"><?= $this->sma->formatMoney($costs['total_cost']); ?></div>
          <div class="spark-manufacturing-kpi-lbl"><?= lang('production_cost'); ?></div>
        </div>
        <div class="spark-manufacturing-kpi-footer">
          <span><?= lang('material_cost'); ?>: <strong><?= $this->sma->formatMoney($costs['material_cost']); ?></strong></span>
          <i class="fa fa-calculator"></i>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- =========================================================================
       3. PRODUCTION STATUS & PERFORMANCE ROW
       ========================================================================= -->
  <?php if ($can_view_any_manuf): ?>
    <div class="spark-manufacturing-grid-row">
      <!-- Card: Production Status Overview -->
      <div class="spark-man-col-6">
        <div class="spark-man-card">
          <div class="spark-man-card-header">
            <h3 class="spark-man-card-title">
              <i class="fa fa-tasks"></i> <?= lang('status_overview'); ?>
            </h3>
            <span class="spark-manufacturing-kpi-badge badge-indigo">
              <?= number_format($stats['total_orders']); ?> <?= lang('all_manufacturing_orders'); ?>
            </span>
          </div>
          <div class="spark-man-card-body">
            <div class="spark-man-status-list">
              <!-- Confirmed (Pending) -->
              <div class="spark-man-status-item">
                <div class="spark-man-status-header">
                  <span class="spark-man-status-label">
                    <span class="spark-man-status-dot dot-confirmed"></span>
                    <?= lang('Confirmed_products'); ?>
                  </span>
                  <span class="spark-man-status-numbers">
                    <span><?= number_format($stats['confirmed_orders']); ?></span>
                    <span class="spark-manufacturing-kpi-badge badge-cyan"><?= $stats['confirmed_pct']; ?>%</span>
                  </span>
                </div>
                <div class="spark-man-progress-track">
                  <div class="spark-man-progress-fill fill-confirmed" style="width: <?= $stats['confirmed_pct']; ?>%;"></div>
                </div>
              </div>

              <!-- In Progress (Produce) -->
              <div class="spark-man-status-item">
                <div class="spark-man-status-header">
                  <span class="spark-man-status-label">
                    <span class="spark-man-status-dot dot-produce"></span>
                    <?= lang('In_progress_products'); ?>
                  </span>
                  <span class="spark-man-status-numbers">
                    <span><?= number_format($stats['in_progress_orders']); ?></span>
                    <span class="spark-manufacturing-kpi-badge badge-amber"><?= $stats['in_progress_pct']; ?>%</span>
                  </span>
                </div>
                <div class="spark-man-progress-track">
                  <div class="spark-man-progress-fill fill-produce" style="width: <?= $stats['in_progress_pct']; ?>%;"></div>
                </div>
              </div>

              <!-- Completed (Done) -->
              <div class="spark-man-status-item">
                <div class="spark-man-status-header">
                  <span class="spark-man-status-label">
                    <span class="spark-man-status-dot dot-done"></span>
                    <?= lang('Done_products'); ?>
                  </span>
                  <span class="spark-man-status-numbers">
                    <span><?= number_format($stats['done_orders']); ?></span>
                    <span class="spark-manufacturing-kpi-badge badge-emerald"><?= $stats['done_pct']; ?>%</span>
                  </span>
                </div>
                <div class="spark-man-progress-track">
                  <div class="spark-man-progress-fill fill-done" style="width: <?= $stats['done_pct']; ?>%;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Production Performance -->
      <div class="spark-man-col-6">
        <div class="spark-man-card">
          <div class="spark-man-card-header">
            <h3 class="spark-man-card-title">
              <i class="fa fa-dashboard"></i> <?= lang('production_performance'); ?>
            </h3>
            <span class="spark-manufacturing-kpi-badge badge-purple">
              <?= $stats['completion_rate']; ?>% <?= lang('completion_rate'); ?>
            </span>
          </div>
          <div class="spark-man-card-body">
            <div class="spark-man-perf-container">
              <div class="spark-man-perf-meter">
                <div class="spark-man-perf-header">
                  <span style="font-size: 13px; font-weight: 600; color: var(--spark-text-secondary);"><?= lang('production_completion'); ?></span>
                  <span class="spark-man-perf-pct"><?= $stats['completion_rate']; ?>%</span>
                </div>
                <div class="spark-man-perf-track">
                  <div class="spark-man-progress-fill fill-primary" style="width: <?= min(100, $stats['completion_rate']); ?>%;"></div>
                </div>
              </div>

              <div class="spark-man-perf-stats-grid">
                <div class="spark-man-perf-stat-box">
                  <span class="spark-man-perf-stat-val"><?= $this->sma->formatQuantity($stats['total_planned_qty']); ?></span>
                  <span class="spark-man-perf-stat-lbl"><?= lang('planned_quantity'); ?></span>
                </div>
                <div class="spark-man-perf-stat-box">
                  <span class="spark-man-perf-stat-val" style="color: var(--spark-success);"><?= $this->sma->formatQuantity($stats['total_produced_qty']); ?></span>
                  <span class="spark-man-perf-stat-lbl"><?= lang('produced_quantity'); ?></span>
                </div>
                <div class="spark-man-perf-stat-box">
                  <span class="spark-man-perf-stat-val" style="color: var(--spark-warning);"><?= $this->sma->formatQuantity($stats['remaining_qty']); ?></span>
                  <span class="spark-man-perf-stat-lbl"><?= lang('remaining_quantity'); ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       4. PRODUCTION TREND CHART & MANUFACTURING COST ROW
       ========================================================================= -->
  <?php
    $show_man_trend = $can_view_any_manuf;
    $show_man_cost  = $can_view_cost;
  ?>
  <?php if ($show_man_trend || $show_man_cost): ?>
    <div class="spark-manufacturing-grid-row">
      <!-- Production Trend Chart Card -->
      <?php if ($show_man_trend): ?>
        <div class="<?= $show_man_cost ? 'spark-man-col-7' : 'spark-man-col-12'; ?>">
          <div class="spark-man-card">
            <div class="spark-man-card-header">
              <h3 class="spark-man-card-title">
                <i class="fa fa-line-chart"></i> <?= lang('production_trend'); ?>
              </h3>
              <span class="spark-manufacturing-kpi-badge badge-indigo">
                <i class="fa fa-calendar"></i> <?= lang('last_12_months'); ?>
              </span>
            </div>
            <div class="spark-man-card-body" style="padding: 16px;">
              <?php if (!empty($monthly_trends)) { ?>
                <div id="echart_production_trend" style="width: 100%; height: 320px;"></div>
              <?php } else { ?>
                <div class="spark-man-empty-state">
                  <i class="fa fa-bar-chart"></i>
                  <div class="spark-man-empty-title"><?= lang('no_trend_data'); ?></div>
                  <div class="spark-man-empty-desc"><?= lang('no_manufacturing_orders'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Manufacturing Cost Analysis Card (Guarded by Cost Permission) -->
      <?php if ($show_man_cost): ?>
        <div class="<?= $show_man_trend ? 'spark-man-col-5' : 'spark-man-col-12'; ?>">
          <div class="spark-man-card">
            <div class="spark-man-card-header">
              <h3 class="spark-man-card-title">
                <i class="fa fa-calculator"></i> <?= lang('production_cost'); ?>
              </h3>
              <span class="spark-manufacturing-kpi-badge badge-rose">
                <?= lang('cost_analysis'); ?>
              </span>
            </div>
            <div class="spark-man-card-body">
              <div class="spark-man-cost-container">
                <!-- Total Banner -->
                <div class="spark-man-cost-total-banner">
                  <span class="spark-man-cost-total-lbl"><?= lang('total_production_cost'); ?></span>
                  <span class="spark-man-cost-total-val"><?= $this->sma->formatMoney($costs['total_cost']); ?></span>
                </div>

                <!-- Material Cost -->
                <div class="spark-man-cost-row">
                  <div class="spark-man-cost-item-left">
                    <div class="spark-man-cost-item-icon" style="background-color: #ecfdf5; color: #10b981;">
                      <i class="fa fa-cubes"></i>
                    </div>
                    <div>
                      <div style="font-weight: 600;"><?= lang('material_cost'); ?></div>
                      <div style="font-size: 11px; color: var(--spark-text-muted);"><?= $costs['material_pct']; ?>% <?= lang('of_total'); ?></div>
                    </div>
                  </div>
                  <div class="spark-man-cost-item-val"><?= $this->sma->formatMoney($costs['material_cost']); ?></div>
                </div>

                <!-- Labor Cost -->
                <div class="spark-man-cost-row">
                  <div class="spark-man-cost-item-left">
                    <div class="spark-man-cost-item-icon" style="background-color: var(--spark-primary-subtle, #eef2ff); color: var(--spark-primary, #6366f1);">
                      <i class="fa fa-users"></i>
                    </div>
                    <div>
                      <div style="font-weight: 600;"><?= lang('labor_cost'); ?></div>
                      <div style="font-size: 11px; color: var(--spark-text-muted);"><?= $costs['labor_pct']; ?>% <?= lang('of_total'); ?></div>
                    </div>
                  </div>
                  <div class="spark-man-cost-item-val"><?= $this->sma->formatMoney($costs['labor_cost']); ?></div>
                </div>

                <!-- Overhead & Expenses -->
                <div class="spark-man-cost-row">
                  <div class="spark-man-cost-item-left">
                    <div class="spark-man-cost-item-icon" style="background-color: #fffbeb; color: #f59e0b;">
                      <i class="fa fa-bolt"></i>
                    </div>
                    <div>
                      <div style="font-weight: 600;"><?= lang('overhead_cost'); ?></div>
                      <div style="font-size: 11px; color: var(--spark-text-muted);"><?= $costs['expense_pct']; ?>% <?= lang('of_total'); ?></div>
                    </div>
                  </div>
                  <div class="spark-man-cost-item-val"><?= $this->sma->formatMoney($costs['expense_cost']); ?></div>
                </div>

                <!-- Avg Unit Cost -->
                <div class="spark-man-cost-row" style="background-color: var(--spark-surface-subtle); padding: 10px 12px; border-radius: var(--spark-radius-md); border-bottom: none;">
                  <div class="spark-man-cost-item-left">
                    <i class="fa fa-tag" style="color: var(--spark-primary);"></i>
                    <span style="font-weight: 600;"><?= lang('avg_cost_per_unit'); ?></span>
                  </div>
                  <span class="spark-manufacturing-kpi-badge badge-indigo" style="font-size: 12px; padding: 4px 10px;">
                    <?= $this->sma->formatMoney($costs['avg_unit_cost']); ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       5. RAW MATERIAL ALERTS & TOP PRODUCTS ROW
       ========================================================================= -->
  <?php
    $show_mat_alerts = ($can_view_any_manuf || $can_view_cost);
    $show_top_prods  = $can_view_any_manuf;
  ?>
  <?php if ($show_mat_alerts || $show_top_prods): ?>
    <div class="spark-manufacturing-grid-row">
      <!-- Raw Material Stock Alerts -->
      <?php if ($show_mat_alerts): ?>
        <div class="<?= $show_top_prods ? 'spark-man-col-6' : 'spark-man-col-12'; ?>">
          <div class="spark-man-card">
            <div class="spark-man-card-header">
              <h3 class="spark-man-card-title">
                <i class="fa fa-exclamation-triangle" style="color: var(--spark-warning);"></i> <?= lang('raw_material_alerts'); ?>
              </h3>
            </div>
            <div class="spark-man-card-body">
              <?php if (!empty($material_alerts)) { ?>
                <div class="spark-man-alerts-list">
                  <?php foreach ($material_alerts as $alert) {
                    $is_critical = ((float)$alert->current_stock <= 0);
                  ?>
                    <div class="spark-man-alert-item">
                      <div class="spark-man-alert-info">
                        <span class="spark-man-alert-name"><?= htmlspecialchars($alert->name); ?></span>
                        <span class="spark-man-alert-code"><?= htmlspecialchars($alert->code); ?></span>
                      </div>
                      <div class="spark-man-alert-right">
                        <span class="spark-man-alert-stock" style="color: <?= $is_critical ? 'var(--spark-danger)' : 'var(--spark-warning)'; ?>;">
                          <?= $this->sma->formatQuantity($alert->current_stock); ?> <?= htmlspecialchars($alert->unit_name ?? ''); ?>
                        </span>
                        <span class="spark-manufacturing-kpi-badge <?= $is_critical ? 'badge-rose' : 'badge-amber'; ?>">
                          <?= $is_critical ? lang('critical_stock') : lang('low_stock'); ?>
                        </span>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-man-empty-state">
                  <i class="fa fa-check-circle" style="color: var(--spark-success);"></i>
                  <div class="spark-man-empty-title"><?= lang('no_raw_material_alerts'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Top Manufactured Products -->
      <?php if ($show_top_prods): ?>
        <div class="<?= $show_mat_alerts ? 'spark-man-col-6' : 'spark-man-col-12'; ?>">
          <div class="spark-man-card">
            <div class="spark-man-card-header">
              <h3 class="spark-man-card-title">
                <i class="fa fa-trophy" style="color: #f59e0b;"></i> <?= lang('top_manufactured_products'); ?>
              </h3>
              <?php if ($can_view_bom): ?>
                <a href="<?= admin_url('manufacturing/bom'); ?>" class="spark-btn spark-btn-outline spark-btn-xs">
                  <i class="fa fa-list"></i> <?= lang('Bill_Of_Material'); ?>
                </a>
              <?php endif; ?>
            </div>
            <div class="spark-man-card-body">
              <?php if (!empty($top_products)) { ?>
                <div class="spark-man-top-list">
                  <?php
                  $rank = 1;
                  foreach ($top_products as $prod) { ?>
                    <div class="spark-man-top-item">
                      <div class="spark-man-top-rank"><?= $rank++; ?></div>
                      <div class="spark-man-top-details">
                        <span class="spark-man-top-name"><?= htmlspecialchars($prod->product_name ?? $prod->bom_name); ?></span>
                        <span class="spark-man-top-code">
                          <?= htmlspecialchars($prod->product_code ?? ('BOM #' . $prod->bom_id)); ?> &bull;
                          <strong><?= (int)$prod->order_count; ?></strong> <?= lang('orders'); ?>
                        </span>
                      </div>
                      <div class="spark-man-top-volume">
                        <?= $this->sma->formatQuantity($prod->produced_qty); ?>
                        <div style="font-size: 10px; color: var(--spark-text-muted); font-weight: normal;"><?= lang('produced'); ?></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="spark-man-empty-state">
                  <i class="fa fa-cubes"></i>
                  <div class="spark-man-empty-title"><?= lang('no_top_products'); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- =========================================================================
       6. RECENT MANUFACTURING ORDERS TABLE
       ========================================================================= -->
  <?php if ($can_view_any_manuf): ?>
    <div class="spark-manufacturing-grid-row">
      <div class="spark-man-col-12">
        <div class="spark-man-card">
          <div class="spark-man-card-header">
            <h3 class="spark-man-card-title">
              <i class="fa fa-list-alt"></i> <?= lang('recent_manufacturing_orders'); ?>
            </h3>
            <?php if ($can_view_manuf): ?>
              <a href="<?= admin_url('manufacturing'); ?>" class="spark-btn spark-btn-outline spark-btn-sm">
                <i class="fa fa-external-link"></i> <?= lang('view_all_orders'); ?>
              </a>
            <?php endif; ?>
          </div>
          <div class="spark-man-card-body" style="padding: 0;">
            <?php if (!empty($recent_orders)) { ?>
              <div class="spark-man-table-responsive">
                <table class="spark-man-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?= lang('Bill_Of_Material'); ?></th>
                      <th><?= lang('planned_quantity'); ?></th>
                      <th><?= lang('produced_quantity'); ?></th>
                      <th><?= lang('Deadline_Start'); ?></th>
                      <th><?= lang('created_by'); ?></th>
                      <th><?= lang('status'); ?></th>
                      <?php if ($can_view_confirmed || $can_view_prog_detail || $can_view_done || $can_edit_manuf || $can_view_bom_details): ?>
                        <th style="text-align: center;"><?= lang('actions'); ?></th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_orders as $order) {
                      $badge_class = 'badge-cyan';
                      $status_label = lang('confirmed');
                      if ($order->status == 'produce') {
                        $badge_class = 'badge-amber';
                        $status_label = lang('In_progress_products');
                      } elseif ($order->status == 'done') {
                        $badge_class = 'badge-emerald';
                        $status_label = lang('Done_products');
                      }
                    ?>
                      <tr>
                        <td style="font-weight: 700; font-family: var(--spark-font-mono);">
                          #<?= $order->id; ?>
                        </td>
                        <td style="font-weight: 600;">
                          <?= htmlspecialchars($order->bom_name); ?>
                        </td>
                        <td>
                          <?= $this->sma->formatQuantity($order->planned_qty); ?>
                        </td>
                        <td style="font-weight: 700; color: var(--spark-primary);">
                          <?= $this->sma->formatQuantity($order->produced_qty); ?>
                        </td>
                        <td>
                          <i class="fa fa-calendar-o" style="color: var(--spark-text-muted); margin-inline-end: 4px;"></i>
                          <?= $this->sma->hrsd($order->deadline_start); ?>
                        </td>
                        <td>
                          <i class="fa fa-user-o" style="color: var(--spark-text-muted); margin-inline-end: 4px;"></i>
                          <?= htmlspecialchars($order->creator_name ?? '—'); ?>
                        </td>
                        <td>
                          <span class="spark-manufacturing-kpi-badge <?= $badge_class; ?>">
                            <?= $status_label; ?>
                          </span>
                        </td>
                        <?php if ($can_view_confirmed || $can_view_prog_detail || $can_view_done || $can_edit_manuf || $can_view_bom_details): ?>
                          <td style="text-align: center;">
                            <div class="btn-group">
                              <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                <?= lang('actions'); ?> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?>" role="menu">
                                <?php if ($order->status == 'confirmed' && $can_view_confirmed): ?>
                                  <li><a href="<?= admin_url('manufacturing/confirmed'); ?>"><i class="fa fa-eye"></i> <?= lang('Confirmed_products'); ?></a></li>
                                <?php elseif ($order->status == 'produce' && $can_view_prog_detail): ?>
                                  <li><a href="<?= admin_url('manufacturing/progress_details/' . $order->id); ?>"><i class="fa fa-sliders"></i> <?= lang('In_progress_product_details'); ?></a></li>
                                <?php elseif ($order->status == 'done' && $can_view_done): ?>
                                  <li><a href="<?= admin_url('manufacturing/done'); ?>"><i class="fa fa-check-square-o"></i> <?= lang('Done_products'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($can_view_bom_details && !empty($order->bom_id)): ?>
                                  <li><a href="<?= admin_url('manufacturing/bom_details/' . $order->bom_id); ?>"><i class="fa fa-list-alt"></i> <?= lang('Bill_Of_Material_details'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($can_edit_manuf && $order->status != 'done'): ?>
                                  <li><a href="<?= admin_url('manufacturing/edit_manufacturing/' . $order->id); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> <?= lang('Edit_manufacturing'); ?></a></li>
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
              <div class="spark-man-empty-state">
                <i class="fa fa-inbox"></i>
                <div class="spark-man-empty-title"><?= lang('no_recent_orders'); ?></div>
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

    var chartDom = document.getElementById('echart_production_trend');
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
            data: ['<?= lang('planned_quantity'); ?>', '<?= lang('produced_quantity'); ?>'],
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
                name: '<?= lang('planned_quantity'); ?>',
                type: 'bar',
                data: <?= json_encode($trend_planned); ?>,
                itemStyle: {
                    color: brandPrimary,
                    borderRadius: [4, 4, 0, 0]
                }
            },
            {
                name: '<?= lang('produced_quantity'); ?>',
                type: 'bar',
                data: <?= json_encode($trend_produced); ?>,
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
