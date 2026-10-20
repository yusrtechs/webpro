<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Prepare safe default values
$active_period = $active_period ?? 'this_month';
$display_date_range = $display_date_range ?? (date('Y-m-01') . ' → ' . date('Y-m-t'));
$from_date_input = $from_date_input ?? date('Y-m-01');
$to_date_input = $to_date_input ?? date('Y-m-d');

$cash_flow = $cash_flow ?? [
    'opening_cash' => 0.0,
    'operating' => ['items' => [], 'inflow' => 0.0, 'outflow' => 0.0, 'net' => 0.0],
    'investing' => ['items' => [], 'inflow' => 0.0, 'outflow' => 0.0, 'net' => 0.0],
    'financing' => ['items' => [], 'inflow' => 0.0, 'outflow' => 0.0, 'net' => 0.0],
    'net_change' => 0.0,
    'closing_cash' => 0.0,
    'cash_accounts' => [],
    'reconciliation' => ['gl_balance' => 0.0, 'report_balance' => 0.0, 'difference' => 0.0, 'is_matched' => true],
];

$is_rtl = ($this->Settings->user_language == 'arabic');
$currency = $this->Settings->default_currency ?? 'SAR';
?>

<style>
/* Scoped Cash Flow Styles */
.spark-cf-wrapper {
    font-family: inherit;
    color: var(--spark-text, #1e293b);
}

.spark-cf-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    padding: 16px 20px;
    background: var(--spark-surface, #ffffff);
    border: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
    border-radius: 14px;
    box-shadow: var(--spark-shadow-xs, 0 1px 3px rgba(0,0,0,0.03));
}

.spark-cf-title-group h1 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px 0;
    color: var(--spark-text, #0f172a);
    display: flex;
    align-items: center;
    gap: 10px;
}

.spark-cf-title-group p {
    margin: 0;
    font-size: 13px;
    color: var(--spark-text-secondary, #64748b);
}

.spark-cf-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.spark-cf-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid var(--spark-border-default, var(--spark-border, #e2e8f0));
    background: var(--spark-surface-elevated, #f8fafc);
    color: var(--spark-text, #334155);
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.spark-cf-btn:hover {
    background: var(--spark-surface-hover, #f1f5f9);
    border-color: var(--spark-primary, #6366f1);
    color: var(--spark-primary, #6366f1);
}

.spark-cf-btn-primary {
    background: var(--spark-primary, #6366f1) !important;
    border-color: var(--spark-primary, #6366f1) !important;
    color: #ffffff !important;
}

.spark-cf-btn-primary:hover {
    opacity: 0.92;
    transform: translateY(-1px);
    color: #ffffff !important;
}

/* Filter Toolbar */
.spark-cf-filter-wrapper {
    background: var(--spark-surface, #ffffff);
    border: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 20px;
    box-shadow: var(--spark-shadow-xs, 0 1px 3px rgba(0,0,0,0.03));
}

.spark-cf-filter-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.spark-cf-period-presets {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}

.spark-cf-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--spark-text-secondary, #64748b);
    background: var(--spark-surface-elevated, #f8fafc);
    border: 1px solid var(--spark-border-default, var(--spark-border, #e2e8f0));
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.2s ease;
    cursor: pointer;
}

.spark-cf-filter-btn:hover {
    color: var(--spark-primary, #6366f1);
    border-color: var(--spark-primary, #6366f1);
    background: var(--spark-primary-subtle, rgba(99, 102, 241, 0.08));
}

.spark-cf-filter-btn.active {
    color: #ffffff !important;
    background: var(--spark-primary, #6366f1) !important;
    border-color: var(--spark-primary, #6366f1) !important;
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.25);
    font-weight: 600;
}

.spark-cf-custom-range {
    display: flex;
    align-items: center;
    gap: 8px;
}

.spark-cf-date-input {
    height: 34px;
    padding: 4px 10px;
    font-size: 12.5px;
    border: 1px solid var(--spark-border-default, var(--spark-border, #e2e8f0));
    border-radius: 6px;
    background: var(--spark-surface-elevated, var(--spark-surface, #ffffff));
    color: var(--spark-text, #1e293b);
    width: 135px;
    color-scheme: light dark;
}

.spark-cf-date-input:focus {
    outline: none;
    border-color: var(--spark-primary, #6366f1);
    box-shadow: 0 0 0 3px var(--spark-focus-ring, rgba(99, 102, 241, 0.2));
}

.spark-cf-active-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: var(--spark-primary-subtle, rgba(99, 102, 241, 0.08));
    border: 1px solid var(--spark-border-strong, rgba(99, 102, 241, 0.2));
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--spark-primary, #6366f1);
}

/* KPI Summary Cards */
.spark-cf-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.spark-cf-kpi-card {
    background: var(--spark-surface, #ffffff);
    border: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
    border-radius: 12px;
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--spark-shadow-xs, 0 1px 3px rgba(0,0,0,0.02));
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.spark-cf-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--spark-shadow-md, 0 4px 12px rgba(0,0,0,0.06));
}

.spark-cf-kpi-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.spark-cf-kpi-title {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--spark-text-secondary, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.spark-cf-kpi-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}

.spark-cf-kpi-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--spark-text, #0f172a);
    line-height: 1.2;
}

.spark-cf-kpi-subtext {
    font-size: 11.5px;
    color: var(--spark-text-muted, #64748b);
    margin-top: 6px;
}

/* KPI Theme Accents */
.kpi-opening .spark-cf-kpi-icon { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
.kpi-operating .spark-cf-kpi-icon { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.kpi-investing .spark-cf-kpi-icon { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
.kpi-financing .spark-cf-kpi-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
.kpi-net .spark-cf-kpi-icon { background: var(--spark-purple-subtle, rgba(139, 92, 246, 0.15)); color: var(--spark-purple, #8b5cf6); }
.kpi-closing .spark-cf-kpi-icon { background: var(--spark-primary-subtle, rgba(99, 102, 241, 0.15)); color: var(--spark-primary, #6366f1); }

.text-positive { color: #10b981 !important; }
.text-negative { color: #ef4444 !important; }
.text-neutral { color: var(--spark-text, #0f172a) !important; }

/* Sections Styling */
.spark-cf-section {
    background: var(--spark-surface, #ffffff);
    border: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
    border-radius: 14px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: var(--spark-shadow-xs, 0 1px 3px rgba(0,0,0,0.02));
}

.spark-cf-section-header {
    padding: 16px 20px;
    background: var(--spark-surface-elevated, #f8fafc);
    border-bottom: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.spark-cf-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--spark-text, #0f172a);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.spark-cf-section-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #ffffff;
    flex-shrink: 0;
}

.spark-cf-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}

.spark-cf-table th {
    padding: 12px 18px;
    background: var(--spark-surface, #ffffff);
    color: var(--spark-text-secondary, #64748b);
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
}

.spark-cf-table td {
    padding: 12px 18px;
    border-bottom: 1px solid var(--spark-border-light, var(--spark-border, #f1f5f9));
    color: var(--spark-text, #1e293b);
}

.spark-cf-table td code {
    background: var(--spark-surface-elevated, rgba(99, 102, 241, 0.08));
    color: var(--spark-primary, #6366f1);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
    border: 1px solid var(--spark-border-light, transparent);
}

.spark-cf-table tbody tr:hover td {
    background: var(--spark-surface-hover, #f8fafc);
}

.spark-cf-table-subtotal td {
    background: var(--spark-surface-elevated, #f8fafc) !important;
    font-weight: 700;
    border-top: 2px solid var(--spark-border-strong, var(--spark-border, #e2e8f0));
    border-bottom: 1px solid var(--spark-border-strong, var(--spark-border, #e2e8f0));
    font-size: 14px;
    color: var(--spark-text, #0f172a);
}

.spark-cf-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.spark-cf-badge-cash { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
.spark-cf-badge-bank { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.spark-cf-badge-success { background: rgba(16, 185, 129, 0.18); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); }
.spark-cf-badge-danger { background: rgba(239, 68, 68, 0.18); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25); }

.spark-cf-empty-state {
    padding: 36px 20px;
    text-align: center;
    color: var(--spark-text-muted, #94a3b8);
    font-size: 13.5px;
}

/* Reconciliation Card */
.spark-cf-recon-metrics-wrapper {
    padding: 16px 20px;
    background: var(--spark-surface-elevated, #f8fafc);
    border-bottom: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
}

.spark-cf-recon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 0;
}

.spark-cf-recon-box {
    background: var(--spark-surface, #ffffff);
    border: 1px solid var(--spark-border-light, var(--spark-border, #e2e8f0));
    border-radius: 10px;
    padding: 14px 16px;
}

.spark-cf-recon-label {
    font-size: 12px;
    color: var(--spark-text-secondary, #64748b);
    font-weight: 600;
    margin-bottom: 4px;
}

.spark-cf-recon-val {
    font-size: 18px;
    font-weight: 700;
    color: var(--spark-text, #0f172a);
}

/* Explicit Dark Theme Overrides for Total Reliability */
[data-theme="dark"] .spark-cf-header,
[data-theme="dark"] .spark-cf-filter-wrapper,
[data-theme="dark"] .spark-cf-kpi-card,
[data-theme="dark"] .spark-cf-section,
[data-theme="dark"] .spark-cf-recon-box,
[data-theme="dark"] .spark-cf-table th {
    background: var(--spark-surface, #111636);
    border-color: var(--spark-border, #1E2756);
}

[data-theme="dark"] .spark-cf-section-header,
[data-theme="dark"] .spark-cf-recon-metrics-wrapper,
[data-theme="dark"] .spark-cf-btn,
[data-theme="dark"] .spark-cf-filter-btn,
[data-theme="dark"] .spark-cf-table-subtotal td {
    background: var(--spark-surface-elevated, #1C234E) !important;
    border-color: var(--spark-border, #1E2756) !important;
}

[data-theme="dark"] .spark-cf-date-input {
    background: var(--spark-surface-elevated, #1C234E);
    border-color: var(--spark-border, #1E2756);
    color: var(--spark-text, #F1F5F9);
}

[data-theme="dark"] .spark-cf-table tbody tr:hover td {
    background: var(--spark-surface-hover, #1C234E);
}

[data-theme="dark"] .spark-cf-table td {
    border-color: var(--spark-border-light, #151B40);
    color: var(--spark-text, #F1F5F9);
}

/* Print Styles */
@media print {
    .no-print,
    .spark-sidebar,
    .spark-header,
    .spark-cf-filter-wrapper,
    .spark-cf-actions,
    .breadcrumb,
    .alerts-con,
    .spark-backdrop {
        display: none !important;
    }
    
    body, .spark-app, .spark-main, .spark-content {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .spark-cf-section, .spark-cf-header, .spark-cf-kpi-card, .spark-cf-recon-card {
        box-shadow: none !important;
        border: 1px solid #cccccc !important;
        background: #ffffff !important;
        page-break-inside: avoid;
    }
    
    .spark-cf-table th, .spark-cf-table td {
        border-color: #dddddd !important;
        color: #000000 !important;
    }
    
    .spark-cf-kpi-value, .spark-cf-section-title {
        color: #000000 !important;
    }
}
</style>

<div class="spark-cf-wrapper">

    <!-- Header Block -->
    <div class="spark-cf-header">
        <div class="spark-cf-title-group">
            <h1>
                <i class="fa fa-exchange text-primary"></i>
                <?= lang('cash_flow_statement'); ?>
            </h1>
            <p><?= lang('cash_flow_statement_desc'); ?></p>
        </div>

        <div class="spark-cf-actions no-print">
            <span class="spark-cf-active-badge">
                <i class="fa fa-calendar"></i>
                <?= htmlspecialchars($display_date_range); ?>
            </span>
            <button type="button" class="spark-cf-btn" onclick="window.print();">
                <i class="fa fa-print"></i>
                <?= lang('print_statement'); ?>
            </button>
            <!-- <button type="button" class="spark-cf-btn" onclick="exportCashFlowExcel();">
                <i class="fa fa-file-excel-o"></i>
                <?= lang('export_excel'); ?>
            </button> -->
            <a href="<?= admin_url('accounts/cash_flow?period=' . $active_period); ?>" class="spark-cf-btn spark-cf-btn-primary">
                <i class="fa fa-refresh"></i>
                <?= lang('refresh'); ?>
            </a>
        </div>
    </div>

    <!-- Period Filter Toolbar (Exact Accounting Dashboard Logic) -->
    <div class="spark-cf-filter-wrapper no-print">
        <div class="spark-cf-filter-container">
            <div class="spark-cf-period-presets">
                <a href="<?= admin_url('accounts/cash_flow?period=today'); ?>" class="spark-cf-filter-btn <?= ($active_period == 'today') ? 'active' : ''; ?>">
                    <?= lang('today'); ?>
                </a>
                <a href="<?= admin_url('accounts/cash_flow?period=this_week'); ?>" class="spark-cf-filter-btn <?= ($active_period == 'this_week') ? 'active' : ''; ?>">
                    <?= lang('this_week'); ?>
                </a>
                <a href="<?= admin_url('accounts/cash_flow?period=this_month'); ?>" class="spark-cf-filter-btn <?= ($active_period == 'this_month') ? 'active' : ''; ?>">
                    <?= lang('this_month'); ?>
                </a>
                <a href="<?= admin_url('accounts/cash_flow?period=this_quarter'); ?>" class="spark-cf-filter-btn <?= ($active_period == 'this_quarter') ? 'active' : ''; ?>">
                    <?= lang('this_quarter'); ?>
                </a>
                <a href="<?= admin_url('accounts/cash_flow?period=this_year'); ?>" class="spark-cf-filter-btn <?= ($active_period == 'this_year') ? 'active' : ''; ?>">
                    <?= lang('this_year'); ?>
                </a>
                <a href="<?= admin_url('accounts/cash_flow?period=fiscal_year'); ?>" class="spark-cf-filter-btn <?= ($active_period == 'fiscal_year') ? 'active' : ''; ?>">
                    <?= lang('fiscal_year'); ?>
                </a>
                <a href="<?= admin_url('accounts/cash_flow?period=all'); ?>" class="spark-cf-filter-btn <?= ($active_period == 'all') ? 'active' : ''; ?>">
                    <?= lang('all_time'); ?>
                </a>
            </div>

            <!-- Custom Date Range Form -->
            <form method="get" action="<?= admin_url('accounts/cash_flow'); ?>" class="spark-cf-custom-range">
                <input type="hidden" name="period" value="custom">
                <input type="date" name="from" value="<?= htmlspecialchars($from_date_input); ?>" class="spark-cf-date-input" title="<?= lang('from'); ?>" required>
                <span>→</span>
                <input type="date" name="to" value="<?= htmlspecialchars($to_date_input); ?>" class="spark-cf-date-input" title="<?= lang('to'); ?>" required>
                <button type="submit" class="spark-cf-btn">
                    <i class="fa fa-filter"></i>
                    <?= lang('apply_filter'); ?>
                </button>
            </form>
        </div>
    </div>

    <!-- 6 KPI Summary Cards -->
    <div class="spark-cf-kpi-grid">
        <!-- 1. Opening Cash Balance -->
        <div class="spark-cf-kpi-card kpi-opening">
            <div class="spark-cf-kpi-header">
                <span class="spark-cf-kpi-title"><?= lang('opening_cash_balance'); ?></span>
                <div class="spark-cf-kpi-icon"><i class="fa fa-money"></i></div>
            </div>
            <div class="spark-cf-kpi-value text-neutral">
                <?= $this->sma->formatMoney($cash_flow['opening_cash']); ?>
            </div>
            <div class="spark-cf-kpi-subtext"><?= lang('start_date'); ?></div>
        </div>

        <!-- 2. Operating Cash Flow -->
        <div class="spark-cf-kpi-card kpi-operating">
            <div class="spark-cf-kpi-header">
                <span class="spark-cf-kpi-title"><?= lang('operating_cash_flow'); ?></span>
                <div class="spark-cf-kpi-icon"><i class="fa fa-shopping-cart"></i></div>
            </div>
            <div class="spark-cf-kpi-value <?= ($cash_flow['operating']['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                <?= ($cash_flow['operating']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['operating']['net'])) . ')' : $this->sma->formatMoney($cash_flow['operating']['net'])); ?>
            </div>
            <div class="spark-cf-kpi-subtext"><?= lang('operating_activities'); ?></div>
        </div>

        <!-- 3. Investing Cash Flow -->
        <div class="spark-cf-kpi-card kpi-investing">
            <div class="spark-cf-kpi-header">
                <span class="spark-cf-kpi-title"><?= lang('investing_cash_flow'); ?></span>
                <div class="spark-cf-kpi-icon"><i class="fa fa-building"></i></div>
            </div>
            <div class="spark-cf-kpi-value <?= ($cash_flow['investing']['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                <?= ($cash_flow['investing']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['investing']['net'])) . ')' : $this->sma->formatMoney($cash_flow['investing']['net'])); ?>
            </div>
            <div class="spark-cf-kpi-subtext"><?= lang('investing_activities'); ?></div>
        </div>

        <!-- 4. Financing Cash Flow -->
        <div class="spark-cf-kpi-card kpi-financing">
            <div class="spark-cf-kpi-header">
                <span class="spark-cf-kpi-title"><?= lang('financing_cash_flow'); ?></span>
                <div class="spark-cf-kpi-icon"><i class="fa fa-university"></i></div>
            </div>
            <div class="spark-cf-kpi-value <?= ($cash_flow['financing']['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                <?= ($cash_flow['financing']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['financing']['net'])) . ')' : $this->sma->formatMoney($cash_flow['financing']['net'])); ?>
            </div>
            <div class="spark-cf-kpi-subtext"><?= lang('financing_activities'); ?></div>
        </div>

        <!-- 5. Net Change in Cash -->
        <div class="spark-cf-kpi-card kpi-net">
            <div class="spark-cf-kpi-header">
                <span class="spark-cf-kpi-title"><?= lang('net_change_in_cash'); ?></span>
                <div class="spark-cf-kpi-icon"><i class="fa fa-exchange"></i></div>
            </div>
            <div class="spark-cf-kpi-value <?= ($cash_flow['net_change'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                <?= ($cash_flow['net_change'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['net_change'])) . ')' : $this->sma->formatMoney($cash_flow['net_change'])); ?>
            </div>
            <div class="spark-cf-kpi-subtext"><?= lang('net_movement'); ?></div>
        </div>

        <!-- 6. Closing Cash Balance -->
        <div class="spark-cf-kpi-card kpi-closing">
            <div class="spark-cf-kpi-header">
                <span class="spark-cf-kpi-title"><?= lang('closing_cash_balance'); ?></span>
                <div class="spark-cf-kpi-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="spark-cf-kpi-value text-neutral">
                <?= $this->sma->formatMoney($cash_flow['closing_cash']); ?>
            </div>
            <div class="spark-cf-kpi-subtext"><?= lang('end_date'); ?></div>
        </div>
    </div>

    <!-- Section 1: Operating Activities (الأنشطة التشغيلية) -->
    <div class="spark-cf-section">
        <div class="spark-cf-section-header">
            <h3 class="spark-cf-section-title">
                <div class="spark-cf-section-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <?= lang('operating_activities'); ?>
            </h3>
            <span class="spark-cf-badge <?= ($cash_flow['operating']['net'] >= 0) ? 'spark-cf-badge-success' : 'spark-cf-badge-danger'; ?>">
                <?= lang('net_movement'); ?>: <?= ($cash_flow['operating']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['operating']['net'])) . ')' : $this->sma->formatMoney($cash_flow['operating']['net'])); ?>
            </span>
        </div>

        <?php if (!empty($cash_flow['operating']['items'])): ?>
            <div class="table-responsive">
                <table class="spark-cf-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;"><?= lang('code'); ?></th>
                            <th><?= lang('Account_name'); ?></th>
                            <th style="width: 160px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('cash_inflow'); ?></th>
                            <th style="width: 160px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('cash_outflow'); ?></th>
                            <th style="width: 170px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('net_movement'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cash_flow['operating']['items'] as $item): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($item['code']); ?></code></td>
                                <td>
                                    <strong><?= htmlspecialchars($is_rtl ? $item['name'] : ($item['name_en'] ?: $item['name'])); ?></strong>
                                    <?php if (!$is_rtl && !empty($item['name'])): ?>
                                        <small class="text-muted d-block"><?= htmlspecialchars($item['name']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-positive">
                                    <?= ($item['inflow'] > 0) ? '+' . $this->sma->formatMoney($item['inflow']) : '0.00'; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-negative">
                                    <?= ($item['outflow'] > 0) ? '-' . $this->sma->formatMoney($item['outflow']) : '0.00'; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>; font-weight: 600;" class="<?= ($item['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                                    <?= ($item['net'] < 0 ? '(' . $this->sma->formatMoney(abs($item['net'])) . ')' : $this->sma->formatMoney($item['net'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="spark-cf-table-subtotal">
                            <td colspan="2"><?= lang('net_operating_cash_flow'); ?></td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-positive">
                                +<?= $this->sma->formatMoney($cash_flow['operating']['inflow']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-negative">
                                -<?= $this->sma->formatMoney($cash_flow['operating']['outflow']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="<?= ($cash_flow['operating']['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                                <?= ($cash_flow['operating']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['operating']['net'])) . ')' : $this->sma->formatMoney($cash_flow['operating']['net'])); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="spark-cf-empty-state">
                <i class="fa fa-info-circle fa-2x mb-2 d-block text-muted"></i>
                <?= lang('no_cash_movement'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 2: Investing Activities (الأنشطة الاستثمارية) -->
    <div class="spark-cf-section">
        <div class="spark-cf-section-header">
            <h3 class="spark-cf-section-title">
                <div class="spark-cf-section-icon" style="background: linear-gradient(135deg, #06b6d4, #0284c7);">
                    <i class="fa fa-building"></i>
                </div>
                <?= lang('investing_activities'); ?>
            </h3>
            <span class="spark-cf-badge <?= ($cash_flow['investing']['net'] >= 0) ? 'spark-cf-badge-success' : 'spark-cf-badge-danger'; ?>">
                <?= lang('net_movement'); ?>: <?= ($cash_flow['investing']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['investing']['net'])) . ')' : $this->sma->formatMoney($cash_flow['investing']['net'])); ?>
            </span>
        </div>

        <?php if (!empty($cash_flow['investing']['items'])): ?>
            <div class="table-responsive">
                <table class="spark-cf-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;"><?= lang('code'); ?></th>
                            <th><?= lang('Account_name'); ?></th>
                            <th style="width: 160px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('cash_inflow'); ?></th>
                            <th style="width: 160px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('cash_outflow'); ?></th>
                            <th style="width: 170px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('net_movement'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cash_flow['investing']['items'] as $item): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($item['code']); ?></code></td>
                                <td>
                                    <strong><?= htmlspecialchars($is_rtl ? $item['name'] : ($item['name_en'] ?: $item['name'])); ?></strong>
                                    <?php if (!$is_rtl && !empty($item['name'])): ?>
                                        <small class="text-muted d-block"><?= htmlspecialchars($item['name']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-positive">
                                    <?= ($item['inflow'] > 0) ? '+' . $this->sma->formatMoney($item['inflow']) : '0.00'; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-negative">
                                    <?= ($item['outflow'] > 0) ? '-' . $this->sma->formatMoney($item['outflow']) : '0.00'; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>; font-weight: 600;" class="<?= ($item['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                                    <?= ($item['net'] < 0 ? '(' . $this->sma->formatMoney(abs($item['net'])) . ')' : $this->sma->formatMoney($item['net'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="spark-cf-table-subtotal">
                            <td colspan="2"><?= lang('net_investing_cash_flow'); ?></td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-positive">
                                +<?= $this->sma->formatMoney($cash_flow['investing']['inflow']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-negative">
                                -<?= $this->sma->formatMoney($cash_flow['investing']['outflow']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="<?= ($cash_flow['investing']['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                                <?= ($cash_flow['investing']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['investing']['net'])) . ')' : $this->sma->formatMoney($cash_flow['investing']['net'])); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="spark-cf-empty-state">
                <i class="fa fa-info-circle fa-2x mb-2 d-block text-muted"></i>
                <?= lang('no_cash_movement'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 3: Financing Activities (الأنشطة التمويلية) -->
    <div class="spark-cf-section">
        <div class="spark-cf-section-header">
            <h3 class="spark-cf-section-title">
                <div class="spark-cf-section-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fa fa-university"></i>
                </div>
                <?= lang('financing_activities'); ?>
            </h3>
            <span class="spark-cf-badge <?= ($cash_flow['financing']['net'] >= 0) ? 'spark-cf-badge-success' : 'spark-cf-badge-danger'; ?>">
                <?= lang('net_movement'); ?>: <?= ($cash_flow['financing']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['financing']['net'])) . ')' : $this->sma->formatMoney($cash_flow['financing']['net'])); ?>
            </span>
        </div>

        <?php if (!empty($cash_flow['financing']['items'])): ?>
            <div class="table-responsive">
                <table class="spark-cf-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;"><?= lang('code'); ?></th>
                            <th><?= lang('Account_name'); ?></th>
                            <th style="width: 160px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('cash_inflow'); ?></th>
                            <th style="width: 160px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('cash_outflow'); ?></th>
                            <th style="width: 170px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('net_movement'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cash_flow['financing']['items'] as $item): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($item['code']); ?></code></td>
                                <td>
                                    <strong><?= htmlspecialchars($is_rtl ? $item['name'] : ($item['name_en'] ?: $item['name'])); ?></strong>
                                    <?php if (!$is_rtl && !empty($item['name'])): ?>
                                        <small class="text-muted d-block"><?= htmlspecialchars($item['name']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-positive">
                                    <?= ($item['inflow'] > 0) ? '+' . $this->sma->formatMoney($item['inflow']) : '0.00'; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-negative">
                                    <?= ($item['outflow'] > 0) ? '-' . $this->sma->formatMoney($item['outflow']) : '0.00'; ?>
                                </td>
                                <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>; font-weight: 600;" class="<?= ($item['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                                    <?= ($item['net'] < 0 ? '(' . $this->sma->formatMoney(abs($item['net'])) . ')' : $this->sma->formatMoney($item['net'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="spark-cf-table-subtotal">
                            <td colspan="2"><?= lang('net_financing_cash_flow'); ?></td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-positive">
                                +<?= $this->sma->formatMoney($cash_flow['financing']['inflow']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="text-negative">
                                -<?= $this->sma->formatMoney($cash_flow['financing']['outflow']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="<?= ($cash_flow['financing']['net'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                                <?= ($cash_flow['financing']['net'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['financing']['net'])) . ')' : $this->sma->formatMoney($cash_flow['financing']['net'])); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="spark-cf-empty-state">
                <i class="fa fa-info-circle fa-2x mb-2 d-block text-muted"></i>
                <?= lang('no_cash_movement'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 4: Cash & Bank Reconciliation (مطابقة أرصدة الخزائن والبنوك) -->
    <div class="spark-cf-section">
        <div class="spark-cf-section-header">
            <h3 class="spark-cf-section-title">
                <div class="spark-cf-section-icon" style="background: var(--spark-gradient-primary, linear-gradient(135deg, #6366f1, #4f46e5));">
                    <i class="fa fa-check-square-o"></i>
                </div>
                <?= lang('cash_and_bank_reconciliation'); ?>
            </h3>
            <div>
                <?php if ($cash_flow['reconciliation']['is_matched']): ?>
                    <span class="spark-cf-badge spark-cf-badge-success">
                        <i class="fa fa-check-circle"></i>
                        <?= lang('reconciled_successfully'); ?> (Δ = 0.00)
                    </span>
                <?php else: ?>
                    <span class="spark-cf-badge spark-cf-badge-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        <?= lang('reconciliation_difference'); ?>: <?= $this->sma->formatMoney($cash_flow['reconciliation']['difference']); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reconciliation Top Metrics -->
        <div class="spark-cf-recon-metrics-wrapper">
            <div class="spark-cf-recon-grid">
                <div class="spark-cf-recon-box">
                    <div class="spark-cf-recon-label"><?= lang('opening_cash_balance'); ?></div>
                    <div class="spark-cf-recon-val"><?= $this->sma->formatMoney($cash_flow['opening_cash']); ?></div>
                </div>
                <div class="spark-cf-recon-box">
                    <div class="spark-cf-recon-label"><?= lang('net_change_in_cash'); ?></div>
                    <div class="spark-cf-recon-val <?= ($cash_flow['net_change'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                        <?= ($cash_flow['net_change'] < 0 ? '(' . $this->sma->formatMoney(abs($cash_flow['net_change'])) . ')' : '+' . $this->sma->formatMoney($cash_flow['net_change'])); ?>
                    </div>
                </div>
                <div class="spark-cf-recon-box">
                    <div class="spark-cf-recon-label"><?= lang('closing_cash_balance'); ?> (<?= lang('report_calculated_balance'); ?>)</div>
                    <div class="spark-cf-recon-val"><?= $this->sma->formatMoney($cash_flow['closing_cash']); ?></div>
                </div>
                <div class="spark-cf-recon-box">
                    <div class="spark-cf-recon-label"><?= lang('actual_cash_balance'); ?> (<?= lang('gl_cumulative_balance'); ?>)</div>
                    <div class="spark-cf-recon-val"><?= $this->sma->formatMoney($cash_flow['reconciliation']['gl_balance']); ?></div>
                </div>
            </div>
        </div>

        <!-- Detailed Accounts Reconciliation Table -->
        <div class="table-responsive">
            <table class="spark-cf-table" id="cashFlowAccountsTable">
                <thead>
                    <tr>
                        <th style="width: 130px;"><?= lang('code'); ?></th>
                        <th><?= lang('Account_name'); ?></th>
                        <th style="width: 110px;"><?= lang('type'); ?></th>
                        <th style="width: 150px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('Opening_balance'); ?></th>
                        <th style="width: 150px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('net_movement'); ?></th>
                        <th style="width: 150px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('closing_cash_balance'); ?></th>
                        <th style="width: 150px; text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= lang('actual_cash_balance'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_open = 0.0;
                    $total_mov = 0.0;
                    $total_close = 0.0;
                    $total_gl = 0.0;
                    foreach ($cash_flow['cash_accounts'] as $acc): 
                        $total_open += $acc['opening'];
                        $total_mov += $acc['movement'];
                        $total_close += $acc['closing'];
                        $total_gl += $acc['gl_closing'];
                    ?>
                        <tr>
                            <td><code><?= htmlspecialchars($acc['accountID']); ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($is_rtl ? $acc['accountName'] : ($acc['accountNameEn'] ?: $acc['accountName'])); ?></strong>
                            </td>
                            <td>
                                <?php if ($acc['type'] === 'bank'): ?>
                                    <span class="spark-cf-badge spark-cf-badge-bank"><i class="fa fa-university"></i> <?= lang('bank_account'); ?></span>
                                <?php else: ?>
                                    <span class="spark-cf-badge spark-cf-badge-cash"><i class="fa fa-money"></i> <?= lang('cash_account'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;">
                                <?= $this->sma->formatMoney($acc['opening']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="<?= ($acc['movement'] >= 0) ? 'text-positive' : 'text-negative'; ?>">
                                <?= ($acc['movement'] < 0 ? '(' . $this->sma->formatMoney(abs($acc['movement'])) . ')' : ($acc['movement'] > 0 ? '+' . $this->sma->formatMoney($acc['movement']) : '0.00')); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>; font-weight: 600;">
                                <?= $this->sma->formatMoney($acc['closing']); ?>
                            </td>
                            <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>; font-weight: 600;">
                                <?= $this->sma->formatMoney($acc['gl_closing']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="spark-cf-table-subtotal">
                        <td colspan="3"><?= lang('total'); ?></td>
                        <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= $this->sma->formatMoney($total_open); ?></td>
                        <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;" class="<?= ($total_mov >= 0) ? 'text-positive' : 'text-negative'; ?>">
                            <?= ($total_mov < 0 ? '(' . $this->sma->formatMoney(abs($total_mov)) . ')' : '+' . $this->sma->formatMoney($total_mov)); ?>
                        </td>
                        <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= $this->sma->formatMoney($total_close); ?></td>
                        <td style="text-align: <?= $is_rtl ? 'left' : 'right'; ?>;"><?= $this->sma->formatMoney($total_gl); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<!-- Client-side Excel Export Utility -->
<script>
function exportCashFlowExcel() {
    var table = document.getElementById('cashFlowAccountsTable');
    if (!table) return;
    var html = table.outerHTML;
    var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    downloadLink.href = url;
    downloadLink.download = 'cash_flow_statement_' + '<?= date('Y-m-d'); ?>' + '.xls';
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
