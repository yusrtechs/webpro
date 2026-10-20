<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
/* ==========================================================================
   Spark ERP - Modern Permissions Screen (UI/UX Redesign)
   Fully Scoped under .spark-permissions-page
   Compatible with Bootstrap 3, RTL/LTR, Dark/Light modes
   ========================================================================== */

.spark-permissions-page {
    --perm-primary: #2563eb;
    --perm-primary-hover: #1d4ed8;
    --perm-primary-light: #eff6ff;
    --perm-primary-subtle: rgba(37, 99, 235, 0.08);
    --perm-border: #e2e8f0;
    --perm-border-subtle: #f1f5f9;
    --perm-bg-card: #ffffff;
    --perm-bg-header: #f8fafc;
    --perm-bg-hover: #f8fafc;
    --perm-text-main: #0f172a;
    --perm-text-muted: #64748b;
    --perm-text-sub: #334155;
    --perm-pill-bg: #f8fafc;
    --perm-pill-border: #e2e8f0;
    --perm-pill-hover: #edf2f7;
    --perm-radius: 12px;
    --perm-radius-sm: 8px;
    --perm-radius-xs: 6px;
    --perm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
    --perm-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
    --perm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    --perm-shadow-lg: 0 10px 20px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.03);

    font-family: inherit;
    padding: 6px 0 90px 0;
}

/* Dark Mode Scoped Tokens */
[data-theme="dark"] .spark-permissions-page,
.dark-theme .spark-permissions-page,
body.dark .spark-permissions-page {
    --perm-primary: #3b82f6;
    --perm-primary-hover: #60a5fa;
    --perm-primary-light: rgba(59, 130, 246, 0.15);
    --perm-primary-subtle: rgba(59, 130, 246, 0.12);
    --perm-border: #2d3748;
    --perm-border-subtle: #1e2638;
    --perm-bg-card: #1a202c;
    --perm-bg-header: #141824;
    --perm-bg-hover: #222a3a;
    --perm-text-main: #f8fafc;
    --perm-text-muted: #94a3b8;
    --perm-text-sub: #cbd5e1;
    --perm-pill-bg: #202636;
    --perm-pill-border: #2d3748;
    --perm-pill-hover: #283144;
    --perm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.4);
    --perm-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.4);
    --perm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.45);
    --perm-shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.55);
}

/* Header Banner Card */
.spark-permissions-page .perm-hero-card {
    background: var(--perm-bg-card);
    border: 1px solid var(--perm-border);
    border-radius: var(--perm-radius);
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--perm-shadow);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    position: relative;
    overflow: hidden;
}

.spark-permissions-page .perm-hero-card::before {
    content: "";
    position: absolute;
    top: 0;
    inset-inline-start: 0;
    width: 4px;
    height: 100%;
    background: var(--perm-primary);
}

.spark-permissions-page .perm-hero-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.spark-permissions-page .perm-hero-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--perm-primary-light);
    color: var(--perm-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.spark-permissions-page .perm-hero-title {
    margin: 0 0 4px 0;
    font-size: 19px;
    font-weight: 700;
    color: var(--perm-text-main);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.spark-permissions-page .perm-group-badge {
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--perm-primary-light);
    color: var(--perm-primary);
    border: 1px solid var(--perm-primary-subtle);
}

.spark-permissions-page .perm-hero-sub {
    margin: 0;
    font-size: 13px;
    color: var(--perm-text-muted);
}

.spark-permissions-page .perm-hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.spark-permissions-page .perm-btn-toggle-all {
    background: var(--perm-bg-header);
    border: 1px solid var(--perm-border);
    color: var(--perm-text-sub);
    font-size: 13px;
    font-weight: 500;
    padding: 8px 14px;
    border-radius: var(--perm-radius-sm);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.spark-permissions-page .perm-btn-toggle-all:hover {
    background: var(--perm-pill-hover);
    color: var(--perm-text-main);
    border-color: var(--perm-primary);
}

/* Group Panels / Cards */
.spark-permissions-page .perm-group-card {
    background: var(--perm-bg-card);
    border: 1px solid var(--perm-border);
    border-radius: var(--perm-radius);
    margin-bottom: 20px;
    box-shadow: var(--perm-shadow);
    overflow: hidden;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}

.spark-permissions-page .perm-group-card:hover {
    border-color: rgba(37, 99, 235, 0.3);
    box-shadow: var(--perm-shadow-md);
}

.spark-permissions-page .perm-group-header {
    background: var(--perm-bg-header);
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--perm-border);
    cursor: pointer;
    user-select: none;
    transition: background 0.15s ease;
}

.spark-permissions-page .perm-group-header:hover {
    background: var(--perm-bg-hover);
}

.spark-permissions-page .perm-group-title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.spark-permissions-page .perm-group-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}

/* Category Specific Icon Themes */
.spark-permissions-page .icon-warehouse { background: #fef3c7; color: #d97706; }
.spark-permissions-page .icon-sales { background: #dbeafe; color: #2563eb; }
.spark-permissions-page .icon-purchases { background: #e0e7ff; color: #4f46e5; }
.spark-permissions-page .icon-accounting { background: #d1fae5; color: #059669; }
.spark-permissions-page .icon-manufacturing { background: #ede9fe; color: #7c3aed; }
.spark-permissions-page .icon-reports { background: #cffafe; color: #0891b2; }
.spark-permissions-page .icon-others { background: #f1f5f9; color: #475569; }
.spark-permissions-page .icon-showroom { background: #fee2e2; color: #dc2626; }

[data-theme="dark"] .spark-permissions-page .icon-warehouse { background: rgba(217, 119, 6, 0.2); color: #fbbf24; }
[data-theme="dark"] .spark-permissions-page .icon-sales { background: rgba(37, 99, 235, 0.2); color: #60a5fa; }
[data-theme="dark"] .spark-permissions-page .icon-purchases { background: rgba(79, 70, 229, 0.2); color: #a5b4fc; }
[data-theme="dark"] .spark-permissions-page .icon-accounting { background: rgba(5, 150, 105, 0.2); color: #34d399; }
[data-theme="dark"] .spark-permissions-page .icon-manufacturing { background: rgba(124, 58, 237, 0.2); color: #c4b5fd; }
[data-theme="dark"] .spark-permissions-page .icon-reports { background: rgba(8, 145, 178, 0.2); color: #38bdf8; }
[data-theme="dark"] .spark-permissions-page .icon-others { background: rgba(148, 163, 184, 0.2); color: #cbd5e1; }
[data-theme="dark"] .spark-permissions-page .icon-showroom { background: rgba(220, 38, 38, 0.2); color: #f87171; }

.spark-permissions-page .perm-group-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--perm-text-main);
}

.spark-permissions-page .perm-group-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.spark-permissions-page .perm-chevron {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--perm-text-muted);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), background 0.15s ease;
}

.spark-permissions-page .perm-group-card.is-collapsed .perm-chevron {
    transform: rotate(-180deg);
}

.spark-permissions-page [dir="rtl"] .perm-group-card.is-collapsed .perm-chevron {
    transform: rotate(180deg);
}

.spark-permissions-page .perm-group-body {
    padding: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Modern Permission Table */
.spark-permissions-page .perm-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
}

.spark-permissions-page .perm-table thead th {
    background: var(--perm-bg-header);
    color: var(--perm-text-muted);
    font-size: 12px;
    font-weight: 600;
    padding: 12px 16px;
    border-top: none;
    border-bottom: 1px solid var(--perm-border);
    white-space: nowrap;
    vertical-align: middle;
}

.spark-permissions-page .perm-th-action {
    text-align: center;
    width: 110px;
    min-width: 90px;
}

.spark-permissions-page .perm-th-misc {
    min-width: 240px;
}

.spark-permissions-page .perm-col-check-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    font-weight: 600;
    color: var(--perm-text-sub);
    margin: 0;
    padding: 3px 6px;
    border-radius: 4px;
    transition: background 0.15s ease, color 0.15s ease;
}

.spark-permissions-page .perm-col-check-label:hover {
    background: var(--perm-pill-hover);
    color: var(--perm-primary);
}

.spark-permissions-page .perm-table tbody tr {
    border-bottom: 1px solid var(--perm-border-subtle);
    transition: background 0.12s ease;
}

.spark-permissions-page .perm-table tbody tr:last-child {
    border-bottom: none;
}

.spark-permissions-page .perm-table tbody tr:hover {
    background: var(--perm-bg-hover);
}

.spark-permissions-page .perm-table tbody td {
    padding: 13px 16px;
    vertical-align: middle;
    border-top: none;
    border-bottom: 1px solid var(--perm-border-subtle);
}

.spark-permissions-page .perm-module-name {
    font-weight: 600;
    color: var(--perm-text-main);
    font-size: 13.5px;
    min-width: 150px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.spark-permissions-page .perm-module-name i {
    color: var(--perm-text-muted);
    font-size: 12px;
}

.spark-permissions-page .perm-cell-action {
    text-align: center;
    vertical-align: middle;
}

/* Sub-permission Pills / Badges */
.spark-permissions-page .perm-pills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
}

.spark-permissions-page .perm-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    background: var(--perm-pill-bg);
    border: 1px solid var(--perm-pill-border);
    border-radius: var(--perm-radius-sm);
    color: var(--perm-text-sub);
    font-size: 11.5px;
    font-weight: 500;
    cursor: pointer;
    margin: 0;
    transition: all 0.15s ease;
    user-select: none;
}

.spark-permissions-page .perm-pill:hover {
    background: var(--perm-pill-hover);
    border-color: var(--perm-primary);
    color: var(--perm-text-main);
}

.spark-permissions-page .perm-pill label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
    font-size: 11.5px;
    color: inherit;
}

/* Native & iCheck Checkbox Enhancements */
.spark-permissions-page input[type="checkbox"].checkbox,
.spark-permissions-page input[type="checkbox"]:not(.skip) {
    cursor: pointer;
    width: 17px;
    height: 17px;
    margin: 0;
    accent-color: var(--perm-primary);
    vertical-align: middle;
}

.spark-permissions-page .icheckbox_square-blue,
.spark-permissions-page .icheckbox_square {
    border-radius: 4px !important;
    vertical-align: middle;
    margin: 0 !important;
}

/* Sticky Bottom Action Bar */
.spark-permissions-page .perm-action-bar {
    position: fixed;
    bottom: 20px;
    inset-inline: 24px;
    max-width: 1140px;
    margin: 0 auto;
    background: var(--perm-bg-card);
    border: 1px solid var(--perm-border);
    border-radius: var(--perm-radius);
    padding: 12px 24px;
    box-shadow: var(--perm-shadow-lg);
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1000;
    backdrop-filter: blur(8px);
    gap: 16px;
}

@media (min-width: 1400px) {
    .spark-permissions-page .perm-action-bar {
        max-width: 1300px;
    }
}

.spark-permissions-page .perm-action-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--perm-text-muted);
}

.spark-permissions-page .perm-action-info-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--perm-primary-light);
    color: var(--perm-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}

.spark-permissions-page .perm-action-buttons {
    display: flex;
    align-items: center;
    gap: 12px;
}

.spark-permissions-page .btn-save-perm {
    background: var(--perm-primary);
    color: #ffffff !important;
    font-size: 13.5px;
    font-weight: 600;
    padding: 9px 24px;
    border-radius: var(--perm-radius-sm);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 10px -1px rgba(37, 99, 235, 0.35);
    transition: all 0.2s ease;
}

.spark-permissions-page .btn-save-perm:hover {
    background: var(--perm-primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 6px 14px -1px rgba(37, 99, 235, 0.45);
}

.spark-permissions-page .btn-cancel-perm {
    background: transparent;
    color: var(--perm-text-muted) !important;
    font-size: 13.5px;
    font-weight: 500;
    padding: 8px 18px;
    border-radius: var(--perm-radius-sm);
    border: 1px solid var(--perm-border);
    cursor: pointer;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.spark-permissions-page .btn-cancel-perm:hover {
    background: var(--perm-bg-hover);
    color: var(--perm-text-main) !important;
    border-color: var(--perm-border);
}

/* Reports Specific Layout Inside Tables */
.spark-permissions-page .perm-report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 8px;
    padding: 4px 0;
}

/* Mobile & Tablet Optimizations */
@media (max-width: 991px) {
    .spark-permissions-page .perm-action-bar {
        bottom: 12px;
        inset-inline: 12px;
        padding: 10px 16px;
    }

    .spark-permissions-page .perm-hero-card {
        padding: 16px 18px;
    }

    .spark-permissions-page .perm-hero-title {
        font-size: 17px;
    }
}

@media (max-width: 767px) {
    .spark-permissions-page .perm-action-info {
        display: none;
    }

    .spark-permissions-page .perm-action-buttons {
        width: 100%;
        justify-content: space-between;
    }

    .spark-permissions-page .btn-save-perm,
    .spark-permissions-page .btn-cancel-perm {
        flex: 1;
        justify-content: center;
    }

    .spark-permissions-page .perm-table {
        min-width: 650px;
    }
}
</style>

<div class="spark-permissions-page">

    <!-- Page Context Hero Card -->
    <div class="perm-hero-card">
        <div class="perm-hero-left">
            <div class="perm-hero-icon">
                <i class="fa fa-shield"></i>
            </div>
            <div>
                <h1 class="perm-hero-title">
                    <?= lang('group_permissions'); ?>
                    <?php if (isset($group) && !empty($group)): ?>
                        <span class="perm-group-badge">
                            <i class="fa fa-users"></i> <?= htmlspecialchars($group->description ? $group->description : $group->name, ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8'); ?>)
                        </span>
                    <?php endif; ?>
                </h1>
                <p class="perm-hero-sub"><?= lang('set_permissions'); ?></p>
            </div>
        </div>
        <div class="perm-hero-actions">
            <button type="button" class="perm-btn-toggle-all" id="permToggleAllGroups">
                <i class="fa fa-compress" id="permToggleAllIcon"></i>
                <span id="permToggleAllText"><?= (isset($Settings->user_language) && $Settings->user_language == 'arabic') ? 'طي الكل' : 'Collapse All'; ?></span>
            </button>
        </div>
    </div>

    <?php if (!empty($p)) {
        if ($p->group_id != 1) {
            echo admin_form_open('system_settings/permissions/' . $id, ['id' => 'permissionsForm']); ?>

            <!-- ============================================================= -->
            <!-- 1. SHOWROOM / VEHICLES (When isCars == 1)                     -->
            <!-- ============================================================= -->
            <?php if ($this->Settings->accountingOnly == 0 && isset($this->Settings->isCars) && $this->Settings->isCars == 1) { ?>
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-showroom">
                                <i class="fa fa-car"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('Showroom'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table reports-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('module_name'); ?></th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_view_cars">
                                                <input class="checkbox pcheckth" id="checkth_view_cars" type="checkbox" name="check" value="view_cars"/>
                                                <span><?= lang('view'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_add_cars">
                                                <input class="checkbox pcheckth" id="checkth_add_cars" type="checkbox" name="check" value="add_cars"/>
                                                <span><?= lang('add'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_edit_cars">
                                                <input class="checkbox pcheckth" id="checkth_edit_cars" type="checkbox" name="check" value="edit_cars" />
                                                <span><?= lang('edit'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_delete_cars">
                                                <input class="checkbox pcheckth" id="checkth_delete_cars" type="checkbox" name="check" value="delete_cars" />
                                                <span><?= lang('delete'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-misc">
                                            <label class="perm-col-check-label" for="checkth_misc_cars">
                                                <input class="checkbox pcheckth" id="checkth_misc_cars" type="checkbox" name="check" value="misc_cars"/>
                                                <span><?= lang('Others'); ?></span>
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Vehicles'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="cars-index" <?php echo $p->{'cars-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="cars-add_vehicle" <?php echo $p->{'cars-add_vehicle'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="cars-edit_vehicle" <?php echo $p->{'cars-edit_vehicle'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="cars-delete_brand" <?php echo $p->{'cars-delete_brand'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="cars-vehicle_details" class="checkbox multi-select_misc_cars" name="cars-vehicle_details" <?php echo $p->{'cars-vehicle_details'} ? 'checked' : ''; ?>>
                                                    <label for="cars-vehicle_details"><?= lang('Details_of_Vehicle') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="cars-modal_view" class="checkbox multi-select_misc_cars" name="cars-modal_view" <?php echo $p->{'cars-modal_view'} ? 'checked' : ''; ?>>
                                                    <label for="cars-modal_view"><?= lang('Cars_card') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="cars-showroom_xls" class="checkbox multi-select_misc_cars" name="cars-showroom_xls" <?php echo $p->{'cars-showroom_xls'} ? 'checked' : ''; ?>>
                                                    <label for="cars-showroom_xls"><?= lang('excel') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Showrooms'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="cars-showrooms" <?php echo $p->{'cars-showrooms'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="cars-add_showroom" <?php echo $p->{'cars-add_showroom'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="cars-edit_showroom" <?php echo $p->{'cars-edit_showroom'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="cars-delete_showroom" <?php echo $p->{'cars-delete_showroom'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="cars-xls" class="checkbox multi-select_misc_cars" name="cars-xls" <?php echo $p->{'cars-xls'} ? 'checked' : ''; ?>>
                                                    <label for="cars-xls"><?= lang('excel') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Services'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="cars-services" <?php echo $p->{'cars-services'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="cars-add_service" <?php echo $p->{'cars-add_service'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="cars-edit_service" <?php echo $p->{'cars-edit_service'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="cars-delete_service" <?php echo $p->{'cars-delete_service'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('brands'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="cars-brands" <?php echo $p->{'cars-brands'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="cars-add_brand" <?php echo $p->{'cars-add_brand'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="cars-edit_brand" <?php echo $p->{'cars-edit_brand'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="cars-delete_brand" <?php echo $p->{'cars-delete_brand'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Car_Type'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="cars-models" <?php echo $p->{'cars-models'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="cars-add_model" <?php echo $p->{'cars-add_model'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="cars-edit_model" <?php echo $p->{'cars-edit_model'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="cars-delete_model" <?php echo $p->{'cars-delete_model'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('categories'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="cars-categories" <?php echo $p->{'cars-categories'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="cars-add_category" <?php echo $p->{'cars-add_category'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="cars-edit_category" <?php echo $p->{'cars-edit_category'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="cars-delete_category" <?php echo $p->{'cars-delete_category'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Features'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="cars-features" <?php echo $p->{'cars-features'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="cars-add_feature" <?php echo $p->{'cars-add_feature'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="cars-edit_feature" <?php echo $p->{'cars-edit_feature'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="cars-delete_feature" <?php echo $p->{'cars-delete_feature'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Transfers_between_showrooms'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_cars" name="transfers-index" <?php echo $p->{'transfers-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_cars" name="transfers-add" <?php echo $p->{'transfers-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_cars" name="transfers-edit" <?php echo $p->{'transfers-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_cars" name="transfers-delete" <?php echo $p->{'transfers-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-email" class="checkbox multi-select_misc_cars" name="transfers-email" <?php echo $p->{'transfers-email'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-email"><?= lang('email') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-pdf" class="checkbox multi-select_misc_cars" name="transfers-pdf" <?php echo $p->{'transfers-pdf'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-pdf"><?= lang('pdf') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <!-- ============================================================= -->
            <!-- 1. WAREHOUSES & PRODUCTS (When isCars == 0)                   -->
            <!-- ============================================================= -->
            <?php } elseif ($this->Settings->accountingOnly == 0) { ?>
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-warehouse">
                                <i class="fa fa-cubes"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('warehouses'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table reports-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('module_name'); ?></th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_view_prod">
                                                <input class="checkbox pcheckth" id="checkth_view_prod" type="checkbox" name="check" value="view_prod"/>
                                                <span><?= lang('view'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_add_prod">
                                                <input class="checkbox pcheckth" id="checkth_add_prod" type="checkbox" name="check" value="add_prod"/>
                                                <span><?= lang('add'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_edit_prod">
                                                <input class="checkbox pcheckth" id="checkth_edit_prod" type="checkbox" name="check" value="edit_prod" />
                                                <span><?= lang('edit'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_delete_prod">
                                                <input class="checkbox pcheckth" id="checkth_delete_prod" type="checkbox" name="check" value="delete_prod" />
                                                <span><?= lang('delete'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-misc">
                                            <label class="perm-col-check-label" for="checkth_misc_prod">
                                                <input class="checkbox pcheckth" id="checkth_misc_prod" type="checkbox" name="check" value="misc_prod"/>
                                                <span><?= lang('Others'); ?></span>
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('warehouses'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_prod" name="products-warehouses" <?php echo $p->{'products-warehouses'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_prod" name="products-add_warehouse" <?php echo $p->{'products-add_warehouse'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_prod" name="products-edit_warehouse" <?php echo $p->{'products-edit_warehouse'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_prod" name="products-delete_warehouse" <?php echo $p->{'products-delete_warehouse'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-adjustments" class="checkbox multi-select_misc_prod" name="products-adjustments" <?php echo $p->{'products-adjustments'} ? 'checked' : ''; ?>>
                                                    <label for="products-adjustments"><?= lang('inventory_opening_balance') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-approved_adjustments" class="checkbox multi-select_misc_prod" name="products-approved_adjustments" <?php echo $p->{'products-approved_adjustments'} ? 'checked' : ''; ?>>
                                                    <label for="products-approved_adjustments"><?= lang('Approved_inventory_opening_balance') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-destruction_inventory" class="checkbox multi-select_misc_prod" name="products-destruction_inventory" <?php echo $p->{'products-destruction_inventory'} ? 'checked' : ''; ?>>
                                                    <label for="products-destruction_inventory"><?= lang('destruction_inventory') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-manual_inventory" class="checkbox multi-select_misc_prod" name="products-manual_inventory" <?php echo $p->{'products-manual_inventory'} ? 'checked' : ''; ?>>
                                                    <label for="products-manual_inventory"><?= lang('manual_inventory') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-stock_count" class="checkbox multi-select_misc_prod" name="products-stock_count" <?php echo $p->{'products-stock_count'} ? 'checked' : ''; ?>>
                                                    <label for="products-stock_count"><?= lang('stock_counts') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('products'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_prod" name="products-index" <?php echo $p->{'products-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_prod" name="products-add" <?php echo $p->{'products-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_prod" name="products-edit" <?php echo $p->{'products-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_prod" name="products-delete" <?php echo $p->{'products-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-cost" class="checkbox multi-select_misc_prod" name="products-cost" <?php echo $p->{'products-cost'} ? 'checked' : ''; ?>>
                                                    <label for="products-cost"><?= lang('product_cost') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-price" class="checkbox multi-select_misc_prod" name="products-price" <?php echo $p->{'products-price'} ? 'checked' : ''; ?>>
                                                    <label for="products-price"><?= lang('product_price') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-barcode" class="checkbox multi-select_misc_prod" name="products-barcode" <?php echo $p->{'products-barcode'} ? 'checked' : ''; ?>>
                                                    <label for="products-barcode"><?= lang('print_barcodes') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('transfers'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_prod" name="transfers-index" <?php echo $p->{'transfers-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_prod" name="transfers-add" <?php echo $p->{'transfers-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_prod" name="transfers-edit" <?php echo $p->{'transfers-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_prod" name="transfers-delete" <?php echo $p->{'transfers-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-ordered" class="checkbox multi-select_misc_prod" name="transfers-ordered" <?php echo $p->{'transfers-ordered'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-ordered"><?= lang('order') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-pending" class="checkbox multi-select_misc_prod" name="transfers-pending" <?php echo $p->{'transfers-pending'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-pending"><?= lang('pending') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-sent" class="checkbox multi-select_misc_prod" name="transfers-sent" <?php echo $p->{'transfers-sent'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-sent"><?= lang('sent') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-received" class="checkbox multi-select_misc_prod" name="transfers-received" <?php echo $p->{'transfers-received'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-received"><?= lang('receive') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-email" class="checkbox multi-select_misc_prod" name="transfers-email" <?php echo $p->{'transfers-email'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-email"><?= lang('email') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers-pdf" class="checkbox multi-select_misc_prod" name="transfers-pdf" <?php echo $p->{'transfers-pdf'} ? 'checked' : ''; ?>>
                                                    <label for="transfers-pdf"><?= lang('pdf') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="transfers_date" class="checkbox multi-select_misc_prod" name="transfers_date" <?php echo $p->{'transfers_date'} ? 'checked' : ''; ?>>
                                                    <label for="transfers_date"><?= lang('date') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('units'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_prod" name="products-units" <?php echo $p->{'products-units'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_prod" name="products-add_unit" <?php echo $p->{'products-add_unit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_prod" name="products-edit_unit" <?php echo $p->{'products-edit_unit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_prod" name="products-delete_unit" <?php echo $p->{'products-delete_unit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-units_xls" class="checkbox multi-select_misc_prod" name="products-units_xls" <?php echo $p->{'products-units_xls'} ? 'checked' : ''; ?>>
                                                    <label for="products-units_xls"><?= lang('excel') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('categories'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_prod" name="products-categories" <?php echo $p->{'products-categories'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_prod" name="products-add_category" <?php echo $p->{'products-add_category'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_prod" name="products-edit_category" <?php echo $p->{'products-edit_category'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_prod" name="products-delete_category" <?php echo $p->{'products-delete_category'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-category_xls" class="checkbox multi-select_misc_prod" name="products-category_xls" <?php echo $p->{'products-category_xls'} ? 'checked' : ''; ?>>
                                                    <label for="products-category_xls"><?= lang('excel') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Warehouse Reports Row -->
                                    <tr>
                                        <td class="perm-module-name">
                                            <label class="perm-col-check-label" for="checkth_report_warehouse">
                                                <input class="checkbox pcheckth" id="checkth_report_warehouse" type="checkbox" name="check" value="report_warehouse"/>
                                                <span><i class="fa fa-bar-chart"></i> <?= lang('reports'); ?></span>
                                            </label>
                                        </td>
                                        <td colspan="5">
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-warehouse_stock" class="checkbox multi-select_report_warehouse" name="products-warehouse_stock" <?php echo $p->{'products-warehouse_stock'} ? 'checked' : ''; ?>>
                                                    <label for="products-warehouse_stock"><?= lang('warehouse_stock') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-adjustments_report" class="checkbox multi-select_report_warehouse" name="products-adjustments_report" <?php echo $p->{'products-adjustments_report'} ? 'checked' : ''; ?>>
                                                    <label for="products-adjustments_report"><?= lang('adjustments_report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-products_item_report" class="checkbox multi-select_report_warehouse" name="products-products_item_report" <?php echo $p->{'products-products_item_report'} ? 'checked' : ''; ?>>
                                                    <label for="products-products_item_report"><?= lang('products_report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-products_report" class="checkbox multi-select_report_warehouse" name="products-products_report" <?php echo $p->{'products-products_report'} ? 'checked' : ''; ?>>
                                                    <label for="products-products_report"><?= lang('products_report_proff_less') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-quantity_alerts" class="checkbox multi-select_report_warehouse" name="products-quantity_alerts" <?php echo $p->{'products-quantity_alerts'} ? 'checked' : ''; ?>>
                                                    <label for="products-quantity_alerts"><?= lang('product_quantity_alerts') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-expiry_alerts" class="checkbox multi-select_report_warehouse" name="products-expiry_alerts" <?php echo $p->{'products-expiry_alerts'} ? 'checked' : ''; ?>>
                                                    <label for="products-expiry_alerts"><?= lang('product_expiry_alerts') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-warehouse_count" class="checkbox multi-select_report_warehouse" name="products-warehouse_count" <?php echo $p->{'products-warehouse_count'} ? 'checked' : ''; ?>>
                                                    <label for="products-warehouse_count"><?= lang('warehouse_count') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-warehouse_store" class="checkbox multi-select_report_warehouse" name="products-warehouse_store" <?php echo $p->{'products-warehouse_store'} ? 'checked' : ''; ?>>
                                                    <label for="products-warehouse_store"><?= lang('warehouse_store') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="products-destruction" class="checkbox multi-select_report_warehouse" name="products-destruction" <?php echo $p->{'products-destruction'} ? 'checked' : ''; ?>>
                                                    <label for="products-destruction"><?= lang('destruction_archive') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- ============================================================= -->
            <!-- 2. SALES SECTION                                              -->
            <!-- ============================================================= -->
            <?php if ($this->Settings->accountingOnly == 0) { ?>
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-sales">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('sales'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table reports-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('module_name'); ?></th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_view_sal">
                                                <input class="checkbox pcheckth" id="checkth_view_sal" type="checkbox" name="check" value="view_sal"/>
                                                <span><?= lang('view'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_add_sal">
                                                <input class="checkbox pcheckth" id="checkth_add_sal" type="checkbox" name="check" value="add_sal"/>
                                                <span><?= lang('add'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_edit_sal">
                                                <input class="checkbox pcheckth" id="checkth_edit_sal" type="checkbox" name="check" value="edit_sal" />
                                                <span><?= lang('edit'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_delete_sal">
                                                <input class="checkbox pcheckth" id="checkth_delete_sal" type="checkbox" name="check" value="delete_sal" />
                                                <span><?= lang('delete'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-misc">
                                            <label class="perm-col-check-label" for="checkth_misc_sal">
                                                <input class="checkbox pcheckth" id="checkth_misc_sal" type="checkbox" name="check" value="misc_sal"/>
                                                <span><?= lang('Others'); ?></span>
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('sales'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="sales-index" <?php echo $p->{'sales-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="sales-add" <?php echo $p->{'sales-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="sales-edit" <?php echo $p->{'sales-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="sales-delete" <?php echo $p->{'sales-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-csv" class="checkbox multi-select_misc_sal" name="sales-csv" <?php echo $p->{'sales-csv'} ? 'checked' : ''; ?>>
                                                    <label for="sales-csv"><?= lang('add_sale_by_csv') ?></label>
                                                </span>
                                                <?php if (!isset($this->Settings->isCars) || $this->Settings->isCars != 1) { ?>
                                                    <?php if (POS) { ?>
                                                        <span class="perm-pill">
                                                            <input type="checkbox" value="1" id="pos-index" class="multi-select_misc_sal" name="pos-index" <?php echo $p->{'pos-index'} ? 'checked' : ''; ?>>
                                                            <label for="pos-index"><?= lang('pos') ?></label>
                                                        </span>
                                                    <?php } ?>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="pos-return" class="multi-select_misc_sal" name="pos-return" <?php echo $p->{'pos-return'} ? 'checked' : ''; ?>>
                                                        <label for="pos-return"><?= lang('pos_return') ?></label>
                                                    </span>
                                                <?php } ?>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-return_sales" class="multi-select_misc_sal" name="sales-return_sales" <?php echo $p->{'sales-return_sales'} ? 'checked' : ''; ?>>
                                                    <label for="sales-return_sales"><?= lang('return_sales') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-email" class="multi-select_misc_sal" name="sales-email" <?php echo $p->{'sales-email'} ? 'checked' : ''; ?>>
                                                    <label for="sales-email"><?= lang('email') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-pdf" class="multi-select_misc_sal" name="sales-pdf" <?php echo $p->{'sales-pdf'} ? 'checked' : ''; ?>>
                                                    <label for="sales-pdf"><?= lang('pdf') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('payments'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="sales-payments" <?php echo $p->{'sales-payments'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="sales-add_payment" <?php echo $p->{'sales-add_payment'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="sales-edit_payment" <?php echo $p->{'sales-edit_payment'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="sales-delete_payment" <?php echo $p->{'sales-delete_payment'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-payment_note" class="multi-select_misc_sal" name="sales-payment_note" <?php echo $p->{'sales-payment_note'} ? 'checked' : ''; ?>>
                                                    <label for="sales-payment_note"><?= lang('Payment_Note') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('deliveries'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="sales-deliveries" <?php echo $p->{'sales-deliveries'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="sales-add_delivery" <?php echo $p->{'sales-add_delivery'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="sales-edit_delivery" <?php echo $p->{'sales-edit_delivery'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="sales-delete_delivery" <?php echo $p->{'sales-delete_delivery'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-pdf_delivery" class="multi-select_misc_sal" name="sales-pdf_delivery" <?php echo $p->{'sales-pdf_delivery'} ? 'checked' : ''; ?>>
                                                    <label for="sales-pdf_delivery"><?= lang('pdf') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('gift_cards'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="sales-gift_cards" <?php echo $p->{'sales-gift_cards'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="sales-add_gift_card" <?php echo $p->{'sales-add_gift_card'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="sales-edit_gift_card" <?php echo $p->{'sales-edit_gift_card'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="sales-delete_gift_card" <?php echo $p->{'sales-delete_gift_card'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-payment_note_gift_card" class="multi-select_misc_sal" name="sales-payment_note_gift_card" <?php echo $p->{'sales-payment_note_gift_card'} ? 'checked' : ''; ?>>
                                                    <label for="sales-payment_note_gift_card"><?= lang('Payment_Note') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-charge_gift_card" class="multi-select_misc_sal" name="sales-charge_gift_card" <?php echo $p->{'sales-charge_gift_card'} ? 'checked' : ''; ?>>
                                                    <label for="sales-charge_gift_card"><?= lang('Charge_gift_card') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name">
                                            <i class="fa fa-circle-o"></i> <?= (isset($this->Settings->isCars) && $this->Settings->isCars == 1) ? lang('Reserved_vehicle') : lang('quotes'); ?>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="quotes-index" <?php echo $p->{'quotes-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="quotes-add" <?php echo $p->{'quotes-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="quotes-edit" <?php echo $p->{'quotes-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="quotes-delete" <?php echo $p->{'quotes-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="quotes-email" class="multi-select_misc_sal" name="quotes-email" <?php echo $p->{'quotes-email'} ? 'checked' : ''; ?>>
                                                    <label for="quotes-email"><?= lang('email') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="quotes-pdf" class="multi-select_misc_sal" name="quotes-pdf" <?php echo $p->{'quotes-pdf'} ? 'checked' : ''; ?>>
                                                    <label for="quotes-pdf"><?= lang('pdf') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) { ?>
                                        <tr>
                                            <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('quotes'); ?></td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="offers-index" <?php echo $p->{'offers-index'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="offers-add" <?php echo $p->{'offers-add'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="offers-edit" <?php echo $p->{'offers-edit'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="offers-delete" <?php echo $p->{'offers-delete'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td>
                                                <div class="perm-pills-container">
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="offers-email" class="multi-select_misc_sal" name="offers-email" <?php echo $p->{'offers-email'} ? 'checked' : ''; ?>>
                                                        <label for="offers-email"><?= lang('email') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="offers-pdf" class="multi-select_misc_sal" name="offers-pdf" <?php echo $p->{'offers-pdf'} ? 'checked' : ''; ?>>
                                                        <label for="offers-pdf"><?= lang('pdf') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="offers-xls" class="multi-select_misc_sal" name="offers-xls" <?php echo $p->{'offers-xls'} ? 'checked' : ''; ?>>
                                                        <label for="offers-xls"><?= lang('excel') ?></label>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('customers'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="customers-index" <?php echo $p->{'customers-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="customers-add" <?php echo $p->{'customers-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="customers-edit" <?php echo $p->{'customers-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="customers-delete" <?php echo $p->{'customers-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="customers-deposits" class="multi-select_misc_sal" name="customers-deposits" <?php echo $p->{'customers-deposits'} ? 'checked' : ''; ?>>
                                                    <label for="customers-deposits"><?= lang('deposits') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="customers-delete_deposit" class="multi-select_misc_sal" name="customers-delete_deposit" <?php echo $p->{'customers-delete_deposit'} ? 'checked' : ''; ?>>
                                                    <label for="customers-delete_deposit"><?= lang('delete_deposit') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php if (!isset($this->Settings->isCars) || $this->Settings->isCars != 1) { ?>
                                        <tr>
                                            <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('promos'); ?></td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="promos-index" <?php echo $p->{'promos-index'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="promos-add" <?php echo $p->{'promos-add'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="promos-edit" <?php echo $p->{'promos-edit'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="perm-cell-action">
                                                <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="promos-delete" <?php echo $p->{'promos-delete'} ? 'checked' : ''; ?>>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('price_groups'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_sal" name="sales-price_groups" <?php echo $p->{'sales-price_groups'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_sal" name="sales-add_price_group" <?php echo $p->{'sales-add_price_group'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_sal" name="sales-edit_price_group" <?php echo $p->{'sales-edit_price_group'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_sal" name="sales-delete_price_group" <?php echo $p->{'sales-delete_price_group'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="sales-group_product_prices" class="multi-select_misc_sal" name="sales-group_product_prices" <?php echo $p->{'sales-group_product_prices'} ? 'checked' : ''; ?>>
                                                    <label for="sales-group_product_prices"><?= lang('group_product_prices') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Sales Reports Row -->
                                    <tr>
                                        <td class="perm-module-name">
                                            <label class="perm-col-check-label" for="checkth_report_sales">
                                                <input class="checkbox pcheckth" id="checkth_report_sales" type="checkbox" name="check" value="report_sales"/>
                                                <span><i class="fa fa-bar-chart"></i> <?= lang('reports'); ?></span>
                                            </label>
                                        </td>
                                        <td colspan="5">
                                            <div class="perm-pills-container">
                                                <?php if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) { ?>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-daily_sales" class="checkbox multi-select_report_sales" name="sales-daily_sales" <?php echo $p->{'sales-daily_sales'} ? 'checked' : ''; ?>>
                                                        <label for="sales-daily_sales"><?= lang('daily_sales') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-monthly_sales" class="checkbox multi-select_report_sales" name="sales-monthly_sales" <?php echo $p->{'sales-monthly_sales'} ? 'checked' : ''; ?>>
                                                        <label for="sales-monthly_sales"><?= lang('monthly_sales') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-sales_report" class="checkbox multi-select_report_sales" name="sales-sales_report" <?php echo $p->{'sales-sales_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-sales_report"><?= lang('sales_report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-transaction_report" class="checkbox multi-select_report_sales" name="sales-transaction_report" <?php echo $p->{'sales-transaction_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-transaction_report"><?= lang('Sales_Transaction_Report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-payments_report" class="checkbox multi-select_report_sales" name="sales-payments_report" <?php echo $p->{'sales-payments_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-payments_report"><?= lang('payments_report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-customers_report" class="checkbox multi-select_report_sales" name="sales-customers_report" <?php echo $p->{'sales-customers_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-customers_report"><?= lang('customers_report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-credit_notes" class="checkbox multi-select_report_sales" name="sales-credit_notes" <?php echo $p->{'sales-credit_notes'} ? 'checked' : ''; ?>>
                                                        <label for="sales-credit_notes"><?= lang('Credit_Notes') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-commercial_discounts" class="checkbox multi-select_report_sales" name="sales-commercial_discounts" <?php echo $p->{'sales-commercial_discounts'} ? 'checked' : ''; ?>>
                                                        <label for="sales-commercial_discounts"><?= lang('Discount_Allowance') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-customers_payments" class="checkbox multi-select_report_sales" name="sales-customers_payments" <?php echo $p->{'sales-customers_payments'} ? 'checked' : ''; ?>>
                                                        <label for="sales-customers_payments"><?= lang('Customers_Payments') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="carssales-products_report" class="checkbox multi-select_report_sales" name="carssales-products_report" <?php echo $p->{'carssales-products_report'} ? 'checked' : ''; ?>>
                                                        <label for="carssales-products_report"><?= lang('Invoice_profit_report') ?></label>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-best_sellers" class="checkbox multi-select_report_sales" name="sales-best_sellers" <?php echo $p->{'sales-best_sellers'} ? 'checked' : ''; ?>>
                                                        <label for="sales-best_sellers"><?= lang('best_sellers') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-daily_sales" class="checkbox multi-select_report_sales" name="sales-daily_sales" <?php echo $p->{'sales-daily_sales'} ? 'checked' : ''; ?>>
                                                        <label for="sales-daily_sales"><?= lang('daily_sales') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-monthly_sales" class="checkbox multi-select_report_sales" name="sales-monthly_sales" <?php echo $p->{'sales-monthly_sales'} ? 'checked' : ''; ?>>
                                                        <label for="sales-monthly_sales"><?= lang('monthly_sales') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-sales_report" class="checkbox multi-select_report_sales" name="sales-sales_report" <?php echo $p->{'sales-sales_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-sales_report"><?= lang('sales_report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-transaction_report" class="checkbox multi-select_report_sales" name="sales-transaction_report" <?php echo $p->{'sales-transaction_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-transaction_report"><?= lang('Sales_Transaction_Report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-delivery_report" class="checkbox multi-select_report_sales" name="sales-delivery_report" <?php echo $p->{'sales-delivery_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-delivery_report"><?= lang('Sales_Delivery_Report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-payments_report" class="checkbox multi-select_report_sales" name="sales-payments_report" <?php echo $p->{'sales-payments_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-payments_report"><?= lang('payments_report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-customers_report" class="checkbox multi-select_report_sales" name="sales-customers_report" <?php echo $p->{'sales-customers_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-customers_report"><?= lang('customers_report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-customers_payments" class="checkbox multi-select_report_sales" name="sales-customers_payments" <?php echo $p->{'sales-customers_payments'} ? 'checked' : ''; ?>>
                                                        <label for="sales-customers_payments"><?= lang('Customers_Payments') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-register_report" class="checkbox multi-select_report_sales" name="sales-register_report" <?php echo $p->{'sales-register_report'} ? 'checked' : ''; ?>>
                                                        <label for="sales-register_report"><?= lang('register_report') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-credit_notes" class="checkbox multi-select_report_sales" name="sales-credit_notes" <?php echo $p->{'sales-credit_notes'} ? 'checked' : ''; ?>>
                                                        <label for="sales-credit_notes"><?= lang('Credit_Notes') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="sales-commercial_discounts" class="checkbox multi-select_report_sales" name="sales-commercial_discounts" <?php echo $p->{'sales-commercial_discounts'} ? 'checked' : ''; ?>>
                                                        <label for="sales-commercial_discounts"><?= lang('Discount_Allowance') ?></label>
                                                    </span>
                                                    <span class="perm-pill">
                                                        <input type="checkbox" value="1" id="accounts-dashboard_acc_report_acc" class="checkbox" name="accounts-dashboard_acc" <?php echo $p->{'accounts-dashboard_acc'} ? 'checked' : ''; ?>>
                                                        <label for="accounts-dashboard_acc_report_acc"><?= lang('dashboard') ?></label>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <!-- ============================================================= -->
            <!-- 3. PURCHASES SECTION                                          -->
            <!-- ============================================================= -->
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-purchases">
                                <i class="fa fa-truck"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('purchases'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table reports-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('module_name'); ?></th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_view_purchases">
                                                <input class="checkbox pcheckth" id="checkth_view_purchases" type="checkbox" name="check" value="view_pur"/>
                                                <span><?= lang('view'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_add_purchases">
                                                <input class="checkbox pcheckth" id="checkth_add_purchases" type="checkbox" name="check" value="add_pur"/>
                                                <span><?= lang('add'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_edit_purchases">
                                                <input class="checkbox pcheckth" id="checkth_edit_purchases" type="checkbox" name="check" value="edit_pur" />
                                                <span><?= lang('edit'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_delete_purchases">
                                                <input class="checkbox pcheckth" id="checkth_delete_purchases" type="checkbox" name="check" value="delete_pur" />
                                                <span><?= lang('delete'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-misc">
                                            <label class="perm-col-check-label" for="checkth_misc_purchases">
                                                <input class="checkbox pcheckth" id="checkth_misc_purchases" type="checkbox" name="check" value="misc_pur"/>
                                                <span><?= lang('Others'); ?></span>
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('purchases'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_pur" name="purchases-index" <?php echo $p->{'purchases-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_pur" name="purchases-add" <?php echo $p->{'purchases-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_pur" name="purchases-edit" <?php echo $p->{'purchases-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_pur" name="purchases-delete" <?php echo $p->{'purchases-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-csv" class="multi-select_misc_pur" name="purchases-csv" <?php echo $p->{'purchases-csv'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-csv"><?= lang('add_purchase_by_csv') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-return_purchases" class="multi-select_misc_pur" name="purchases-return_purchases" <?php echo $p->{'purchases-return_purchases'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-return_purchases"><?= lang('return_purchases') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-update_status" class="multi-select_misc_pur" name="purchases-update_status" <?php echo $p->{'purchases-update_status'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-update_status"><?= lang('Update_purchases_status') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-ordered" class="multi-select_misc_pur" name="purchases-ordered" <?php echo $p->{'purchases-ordered'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-ordered"><?= lang('ordered') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-pending" class="multi-select_misc_pur" name="purchases-pending" <?php echo $p->{'purchases-pending'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-pending"><?= lang('purchase_pending') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-received" class="multi-select_misc_pur" name="purchases-received" <?php echo $p->{'purchases-received'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-received"><?= lang('Receiving_purchases') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-email" class="multi-select_misc_pur" name="purchases-email" <?php echo $p->{'purchases-email'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-email"><?= lang('email') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-pdf" class="multi-select_misc_pur" name="purchases-pdf" <?php echo $p->{'purchases-pdf'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-pdf"><?= lang('pdf') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('suppliers'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_pur" name="suppliers-index" <?php echo $p->{'suppliers-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_pur" name="suppliers-add" <?php echo $p->{'suppliers-add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_pur" name="suppliers-edit" <?php echo $p->{'suppliers-edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_pur" name="suppliers-delete" <?php echo $p->{'suppliers-delete'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('expenses'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_pur" name="purchases-expenses" <?php echo $p->{'purchases-expenses'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_pur" name="purchases-add_expense" <?php echo $p->{'purchases-add_expense'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_pur" name="purchases-edit_expense" <?php echo $p->{'purchases-edit_expense'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_pur" name="purchases-delete_expense" <?php echo $p->{'purchases-delete_expense'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('payments'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_pur" name="purchases-payments" <?php echo $p->{'purchases-payments'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_pur" name="purchases-add_payment" <?php echo $p->{'purchases-add_payment'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_pur" name="purchases-edit_payment" <?php echo $p->{'purchases-edit_payment'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_pur" name="purchases-delete_payment" <?php echo $p->{'purchases-delete_payment'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-email_payment" class="multi-select_misc_pur" name="purchases-email_payment" <?php echo $p->{'purchases-email_payment'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-email_payment"><?= lang('email') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Purchases Reports Row -->
                                    <tr>
                                        <td class="perm-module-name">
                                            <label class="perm-col-check-label" for="checkth_report_purchases">
                                                <input class="checkbox pcheckth" id="checkth_report_purchases" type="checkbox" name="check" value="report_purchases"/>
                                                <span><i class="fa fa-bar-chart"></i> <?= lang('reports'); ?></span>
                                            </label>
                                        </td>
                                        <td colspan="5">
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-daily_purchases_report_purchases" class="checkbox multi-select_report_purchases" name="purchases-daily_purchases" <?php echo $p->{'purchases-daily_purchases'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-daily_purchases_report_purchases"><?= lang('daily_purchases') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-monthly_purchases_report_purchases" class="checkbox multi-select_report_purchases" name="purchases-monthly_purchases" <?php echo $p->{'purchases-monthly_purchases'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-monthly_purchases_report_purchases"><?= lang('monthly_purchases') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="reports-purchases_report_purchases" class="checkbox multi-select_report_purchases" name="reports-purchases" <?php echo $p->{'reports-purchases'} ? 'checked' : ''; ?>>
                                                    <label for="reports-purchases_report_purchases"><?= lang('purchases_report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-expenses_report_report_purchases" class="checkbox multi-select_report_purchases" name="purchases-expenses_report" <?php echo $p->{'purchases-expenses_report'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-expenses_report_report_purchases"><?= lang('expenses_report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="reports-suppliers_report_purchases" class="checkbox multi-select_report_purchases" name="reports-suppliers" <?php echo $p->{'reports-suppliers'} ? 'checked' : ''; ?>>
                                                    <label for="reports-suppliers_report_purchases"><?= lang('suppliers_report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-Debit_Notes_report_purchases" class="checkbox multi-select_report_purchases" name="purchases-Debit_Notes" <?php echo $p->{'purchases-Debit_Notes'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-Debit_Notes_report_purchases"><?= lang('Debit_Notes') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-payments_report_report_purchases" class="checkbox multi-select_report_purchases" name="purchases-payments_report" <?php echo $p->{'purchases-payments_report'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-payments_report_report_purchases"><?= lang('payments_report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="purchases-expense_payments_report_report_purchases" class="checkbox multi-select_report_purchases" name="purchases-expense_payments_report" <?php echo $p->{'purchases-expense_payments_report'} ? 'checked' : ''; ?>>
                                                    <label for="purchases-expense_payments_report_report_purchases"><?= lang('expense_payments_report') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- ============================================================= -->
            <!-- 4. ACCOUNTING SECTION                                         -->
            <!-- ============================================================= -->
            <?php if (isset($this->Settings->preAccounting) && $this->Settings->preAccounting == 1) { ?>
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-accounting">
                                <i class="fa fa-calculator"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('Accounting'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table reports-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('module_name'); ?></th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_view_acc">
                                                <input class="checkbox pcheckth" id="checkth_view_acc" type="checkbox" name="check" value="view_acc"/>
                                                <span><?= lang('view'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_add_acc">
                                                <input class="checkbox pcheckth" id="checkth_add_acc" type="checkbox" name="check" value="add_acc"/>
                                                <span><?= lang('add'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_edit_acc">
                                                <input class="checkbox pcheckth" id="checkth_edit_acc" type="checkbox" name="check" value="edit_acc" />
                                                <span><?= lang('edit'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_delete_acc">
                                                <input class="checkbox pcheckth" id="checkth_delete_acc" type="checkbox" name="check" value="delete_acc" />
                                                <span><?= lang('delete'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_details_acc">
                                                <input class="checkbox pcheckth" id="checkth_details_acc" type="checkbox" name="check" value="details_acc"/>
                                                <span><?= lang('Details'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-misc">
                                            <label class="perm-col-check-label" for="checkth_misc_acc">
                                                <input class="checkbox pcheckth" id="checkth_misc_acc" type="checkbox" name="check" value="misc_acc"/>
                                                <span><?= lang('Others'); ?></span>
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Accounts_tree'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-accounts_tree" <?php echo $p->{'accounts-accounts_tree'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_account" <?php echo $p->{'accounts-add_account'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-edit_account" <?php echo $p->{'accounts-edit_account'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_acc" name="accounts-delete_account" <?php echo $p->{'accounts-delete_account'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-tree_modal_view" <?php echo $p->{'accounts-tree_modal_view'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('inventory_opening_balance'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-opening_balance" <?php echo $p->{'accounts-opening_balance'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_opening_balance" <?php echo $p->{'accounts-add_opening_balance'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-edit_opening_balance" <?php echo $p->{'accounts-edit_opening_balance'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-view_opening_balance" <?php echo $p->{'accounts-view_opening_balance'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Entries'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-entry_index" <?php echo $p->{'accounts-entry_index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-entry_add" <?php echo $p->{'accounts-entry_add'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-entry_edit" <?php echo $p->{'accounts-entry_edit'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-view_entries" <?php echo $p->{'accounts-view_entries'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" class="checkbox multi-select_misc_acc" name="accounts-update_entries_status" <?php echo $p->{'accounts-update_entries_status'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-update_entries_status"><?= lang('update_status') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Accounting_Period'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-accounting_period_index" <?php echo $p->{'accounts-accounting_period_index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_accounting_period" <?php echo $p->{'accounts-add_accounting_period'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_acc" name="accounts-delete_accounting_period" <?php echo $p->{'accounts-delete_accounting_period'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-update_accounting_period_status" class="checkbox multi-select_misc_acc" name="accounts-update_accounting_period_status" <?php echo $p->{'accounts-update_accounting_period_status'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-update_accounting_period_status"><?= lang('update_status') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-fiscal_year_closing" class="checkbox multi-select_misc_acc" name="accounts-fiscal_year_closing" <?php echo $p->{'accounts-fiscal_year_closing'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-fiscal_year_closing"><?= lang('Fiscal_year_closing') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Receipts'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-receipts_index" <?php echo $p->{'accounts-receipts_index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_receipt" <?php echo $p->{'accounts-add_receipt'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-edit_receipt" <?php echo $p->{'accounts-edit_receipt'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-view_receipt" <?php echo $p->{'accounts-view_receipt'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Catch_receipts'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-catch_receipts_index" <?php echo $p->{'accounts-catch_receipts_index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_catch_receipt" <?php echo $p->{'accounts-add_catch_receipt'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_catch_receipt" name="accounts-edit_catch_receipt" <?php echo $p->{'accounts-edit_catch_receipt'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-view_catch_receipt" <?php echo $p->{'accounts-view_catch_receipt'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Refunding_bonds'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-refunding_bonds_index" <?php echo $p->{'accounts-refunding_bonds_index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_refunding_bonds" <?php echo $p->{'accounts-add_refunding_bonds'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-edit_refunding_bonds" <?php echo $p->{'accounts-edit_refunding_bonds'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-view_refunding_bond" <?php echo $p->{'accounts-view_refunding_bond'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Notice_creditor'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-notice_creditor_index" <?php echo $p->{'accounts-notice_creditor_index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_notice_creditor" <?php echo $p->{'accounts-add_notice_creditor'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-edit_notice_creditor" <?php echo $p->{'accounts-edit_notice_creditor'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-view_notice_creditor" <?php echo $p->{'accounts-view_notice_creditor'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Payment_types'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-payment_types_index" <?php echo $p->{'accounts-payment_types_index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_payment_type" <?php echo $p->{'accounts-add_payment_type'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-edit_payment_type" <?php echo $p->{'accounts-edit_payment_type'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action"></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-view_payment_types" <?php echo $p->{'accounts-view_payment_types'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Cost_centers'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_acc" name="accounts-cost_centers" <?php echo $p->{'accounts-cost_centers'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_acc" name="accounts-add_cost_center" <?php echo $p->{'accounts-add_cost_center'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_acc" name="accounts-edit_cost_center" <?php echo $p->{'accounts-edit_cost_center'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_acc" name="accounts-delete_cost_center" <?php echo $p->{'accounts-delete_cost_center'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_acc" name="accounts-cost_centers_view_popup" <?php echo $p->{'accounts-cost_centers_view_popup'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <!-- Accounting Reports Row -->
                                    <tr>
                                        <td class="perm-module-name">
                                            <label class="perm-col-check-label" for="checkth_report_acc">
                                                <input class="checkbox pcheckth" id="checkth_report_acc" type="checkbox" name="check" value="report_acc"/>
                                                <span><i class="fa fa-bar-chart"></i> <?= lang('reports'); ?></span>
                                            </label>
                                        </td>
                                        <td colspan="6">
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-ledgers_report_acc" class="checkbox multi-select_report_acc" name="accounts-ledgers" <?php echo $p->{'accounts-ledgers'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-ledgers_report_acc"><?= lang('Ledgers') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-trialbalance_report_acc" class="checkbox multi-select_report_acc" name="accounts-trialbalance" <?php echo $p->{'accounts-trialbalance'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-trialbalance_report_acc"><?= lang('Trial_balance') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-entriesreport_report_acc" class="checkbox multi-select_report_acc" name="accounts-entriesreport" <?php echo $p->{'accounts-entriesreport'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-entriesreport_report_acc"><?= lang('Entries_Report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-incomestatement_report_acc" class="checkbox multi-select_report_acc" name="accounts-incomestatement" <?php echo $p->{'accounts-incomestatement'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-incomestatement_report_acc"><?= lang('Income_Statement') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-balancesheet_report_acc" class="checkbox multi-select_report_acc" name="accounts-balancesheet" <?php echo $p->{'accounts-balancesheet'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-balancesheet_report_acc"><?= lang('Balance_Sheet') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-accountstatement_report_acc" class="checkbox multi-select_report_acc" name="accounts-accountstatement" <?php echo $p->{'accounts-accountstatement'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-accountstatement_report_acc"><?= lang('Account_statement') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-agedopeninvoices_report_acc" class="checkbox multi-select_report_acc" name="accounts-agedopeninvoices" <?php echo $p->{'accounts-agedopeninvoices'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-agedopeninvoices_report_acc"><?= lang('Aged_Open_Invoices') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="accounts-distributioncostcenters_report_acc" class="checkbox multi-select_report_acc" name="accounts-distributioncostcenters" <?php echo $p->{'accounts-distributioncostcenters'} ? 'checked' : ''; ?>>
                                                    <label for="accounts-distributioncostcenters_report_acc"><?= lang('Cost_centers_Report') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- ============================================================= -->
            <!-- 5. MANUFACTURING SECTION                                      -->
            <!-- ============================================================= -->
            <?php if ($this->Settings->accountingOnly == 0 && isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing == 1) { ?>
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-manufacturing">
                                <i class="fa fa-cogs"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('Manufacturing'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table reports-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('module_name'); ?></th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_view_manuf">
                                                <input class="checkbox pcheckth" id="checkth_view_manuf" type="checkbox" name="check" value="view_manuf"/>
                                                <span><?= lang('view'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_add_manuf">
                                                <input class="checkbox pcheckth" id="checkth_add_manuf" type="checkbox" name="check" value="add_manuf"/>
                                                <span><?= lang('add'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_edit_manuf">
                                                <input class="checkbox pcheckth" id="checkth_edit_manuf" type="checkbox" name="check" value="edit_manuf" />
                                                <span><?= lang('edit'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_delete_manuf">
                                                <input class="checkbox pcheckth" id="checkth_delete_manuf" type="checkbox" name="check" value="delete_manuf" />
                                                <span><?= lang('delete'); ?></span>
                                            </label>
                                        </th>
                                        <th class="perm-th-action">
                                            <label class="perm-col-check-label" for="checkth_details_manuf">
                                                <input class="checkbox pcheckth" id="checkth_details_manuf" type="checkbox" name="check" value="details_manuf"/>
                                                <span><?= lang('Details'); ?></span>
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Bill_Of_Material'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_manuf" name="manufacturing-bom" <?php echo $p->{'manufacturing-bom'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_manuf" name="manufacturing-add_bom" <?php echo $p->{'manufacturing-add_bom'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_manuf" name="manufacturing-bom_update" <?php echo $p->{'manufacturing-bom_update'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_manuf" name="manufacturing-deletebom" <?php echo $p->{'manufacturing-deletebom'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_manuf" name="manufacturing-bom_details" <?php echo $p->{'manufacturing-bom_details'} ? 'checked' : ''; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="perm-module-name"><i class="fa fa-circle-o"></i> <?= lang('Manufacturing_list'); ?></td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_view_manuf" name="manufacturing-index" <?php echo $p->{'manufacturing-index'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_add_manuf" name="manufacturing-add_manufacturing" <?php echo $p->{'manufacturing-add_manufacturing'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_edit_manuf" name="manufacturing-edit_manufacturing" <?php echo $p->{'manufacturing-edit_manufacturing'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_delete_manuf" name="manufacturing-deletemanufacturing" <?php echo $p->{'manufacturing-deletemanufacturing'} ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="perm-cell-action">
                                            <input type="checkbox" value="1" class="checkbox multi-select_details_manuf" name="manufacturing-done_view" <?php echo $p->{'manufacturing-done_view'} ? 'checked' : ''; ?>>
                                        </td>
                                    </tr>
                                    <!-- Manufacturing Status Items Row -->
                                    <tr>
                                        <td colspan="6">
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="manufacturing-confirmed" class="checkbox" name="manufacturing-confirmed" <?php echo $p->{'manufacturing-confirmed'} ? 'checked' : ''; ?>>
                                                    <label for="manufacturing-confirmed"><?= lang('Confirmed_products') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="manufacturing-progress" class="checkbox" name="manufacturing-progress" <?php echo $p->{'manufacturing-progress'} ? 'checked' : ''; ?>>
                                                    <label for="manufacturing-progress"><?= lang('In_progress_products') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="manufacturing-done" class="checkbox" name="manufacturing-done" <?php echo $p->{'manufacturing-done'} ? 'checked' : ''; ?>>
                                                    <label for="manufacturing-done"><?= lang('Done_products') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="manufacturing-checkstock" class="checkbox" name="manufacturing-checkstock" <?php echo $p->{'manufacturing-checkstock'} ? 'checked' : ''; ?>>
                                                    <label for="manufacturing-checkstock"><?= lang('Check_stock') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="manufacturing-update_status" class="checkbox" name="manufacturing-update_status" <?php echo $p->{'manufacturing-update_status'} ? 'checked' : ''; ?>>
                                                    <label for="manufacturing-update_status"><?= lang('update_status') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="manufacturing-progress_details" class="checkbox" name="manufacturing-progress_details" <?php echo $p->{'manufacturing-progress_details'} ? 'checked' : ''; ?>>
                                                    <label for="manufacturing-progress_details"><?= lang('In_progress_product_details') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- ============================================================= -->
            <!-- 6. OTHER REPORTS SECTION                                      -->
            <!-- ============================================================= -->
            <?php if ($this->Settings->accountingOnly == 0) { ?>
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-reports">
                                <i class="fa fa-line-chart"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('Other_reports'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table reports-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('reports'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="reports-tax" class="checkbox" name="reports-tax" <?php echo $p->{'reports-tax'} ? 'checked' : ''; ?>>
                                                    <label for="reports-tax"><?= lang('tax_report') ?></label>
                                                </span>
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="reports-users_report" class="checkbox" name="reports-users_report" <?php echo $p->{'reports-users_report'} ? 'checked' : ''; ?>>
                                                    <label for="reports-users_report"><?= lang('staff_report') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <!-- ============================================================= -->
            <!-- 7. OTHERS SECTION                                             -->
            <!-- ============================================================= -->
                <div class="perm-group-card">
                    <div class="perm-group-header collapse-link">
                        <div class="perm-group-title-wrap">
                            <div class="perm-group-icon icon-others">
                                <i class="fa fa-sliders"></i>
                            </div>
                            <h2 class="perm-group-title"><?= lang('Others'); ?></h2>
                        </div>
                        <div class="perm-group-controls">
                            <div class="perm-chevron">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="perm-group-body x_content">
                        <div class="table-responsive">
                            <table class="table perm-table others-table">
                                <thead>
                                    <tr>
                                        <th><?= lang('Others'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="perm-pills-container">
                                                <span class="perm-pill">
                                                    <input type="checkbox" value="1" id="bulk_actions" class="checkbox" name="bulk_actions" <?php echo $p->{'bulk_actions'} ? 'checked' : ''; ?>>
                                                    <label for="bulk_actions"><?= lang('bulk_actions') ?></label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- ============================================================= -->
            <!-- STICKY BOTTOM ACTION BAR                                      -->
            <!-- ============================================================= -->
            <div class="perm-action-bar">
                <div class="perm-action-info">
                    <div class="perm-action-info-icon">
                        <i class="fa fa-info"></i>
                    </div>
                    <span>
                        <?= (isset($Settings->user_language) && $Settings->user_language == 'arabic') ? 'تعديل صلاحيات المجموعة:' : 'Editing permissions for:'; ?>
                        <strong><?= htmlspecialchars(isset($group->name) ? $group->name : '', ENT_QUOTES, 'UTF-8'); ?></strong>
                    </span>
                </div>
                <div class="perm-action-buttons">
                    <a href="<?= admin_url('system_settings/user_groups'); ?>" class="btn-cancel-perm">
                        <i class="fa fa-times"></i>
                        <span><?= lang('cancel'); ?></span>
                    </a>
                    <button type="submit" class="btn-save-perm">
                        <i class="fa fa-check"></i>
                        <span><?= lang('update'); ?></span>
                    </button>
                </div>
            </div>

            <?php echo form_close();
        } else {
            echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> ' . $this->lang->line('group_x_allowed') . '</div>';
        }
    } else {
        echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> ' . $this->lang->line('group_x_allowed') . '</div>';
    } ?>

</div>

<script type="text/javascript">
$(document).ready(function () {

    // ── Helper: Extract chtype from element class ───────────────────────────
    function getChtypeFromElement(el) {
        if (!el) return null;
        var className = typeof el.className === 'string' ? el.className : $(el).attr('class') || '';
        var match = className.match(/multi-select_([a-zA-Z0-9_-]+)/);
        return match ? match[1] : null;
    }

    // ── Column / Header Checkbox Synchronization ────────────────────────────
    var isSyncing = false;

    function setColumnState(chtype, isChecked) {
        if (!chtype || isSyncing) return;
        isSyncing = true;

        var action = isChecked ? 'check' : 'uncheck';
        var $targets = $('.multi-select_' + chtype);
        var $headers = $('.pcheckth[value="' + chtype + '"]');

        // 1. Update native checked properties
        $targets.prop('checked', isChecked);
        $headers.prop('checked', isChecked);

        // 2. Update iCheck UI if iCheck plugin is initialized
        if (typeof $.fn.iCheck === 'function') {
            $targets.iCheck(action);
            $headers.iCheck(action);
        }

        isSyncing = false;
    }

    function syncHeaderFromRows(chtype) {
        if (!chtype || isSyncing) return;

        var $targets = $('.multi-select_' + chtype);
        if (!$targets.length) return;

        var total = $targets.length;
        var checked = $targets.filter(':checked').length;
        var allChecked = (total > 0 && checked === total);

        var $headers = $('.pcheckth[value="' + chtype + '"]');
        if ($headers.length) {
            $headers.each(function() {
                var $th = $(this);
                if ($th.is(':checked') !== allChecked) {
                    isSyncing = true;
                    $th.prop('checked', allChecked);
                    if (typeof $.fn.iCheck === 'function') {
                        $th.iCheck(allChecked ? 'check' : 'uncheck');
                    }
                    isSyncing = false;
                }
            });
        }
    }

    // Header checkbox event listeners (iCheck events)
    $(document).on('ifChecked', '.pcheckth', function(e) {
        //setColumnState($(this).val(), true);
    });

    $(document).on('ifUnchecked', '.pcheckth', function(e) {
        //setColumnState($(this).val(), false);
    });

    // Header checkbox event listener (Native change fallback)
    $(document).on('change', '.pcheckth', function(e) {
        //setColumnState($(this).val(), $(this).is(':checked'));
    });

    // Row checkboxes event listeners (iCheck event)
    $(document).on('ifChanged', 'input[class*="multi-select_"]', function(e) {
        var chtype = getChtypeFromElement(this);
        if (chtype) {
            syncHeaderFromRows(chtype);
        }
    });

    // Row checkboxes event listeners (Native change event)
    $(document).on('change', 'input[class*="multi-select_"]', function(e) {
        var chtype = getChtypeFromElement(this);
        if (chtype) {
            syncHeaderFromRows(chtype);
        }
    });

    // Initial synchronization of header checkboxes on page load
    $('.pcheckth').each(function() {
        var chtype = $(this).val();
        if (chtype) {
            syncHeaderFromRows(chtype);
        }
    });

    // ── Collapsible Card Panels ──────────────────────────────────────────────
    $(document).on('click', '.collapse-link', function (e) {
        // Prevent collapsing if clicked directly on an interactive control inside header
        if ($(e.target).is('input, label, a, button, .checkbox, .iCheck-helper, [class*="icheckbox"]')) {
            return;
        }

        var $card = $(this).closest('.perm-group-card');
        var $content = $card.find('.x_content');

        $content.slideToggle(200, function() {
            $card.toggleClass('is-collapsed', !$content.is(':visible'));
        });
    });

    // ── Expand All / Collapse All Toggle Button ──────────────────────────────
    var allCollapsed = true;
    $('#permToggleAllGroups').on('click', function() {
        allCollapsed = !allCollapsed;
        var isAr = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';

        if (allCollapsed) {
            $('.perm-group-card .x_content').slideUp(200);
            $('.perm-group-card').addClass('is-collapsed');
            $('#permToggleAllIcon').removeClass('fa-compress').addClass('fa-expand');
            $('#permToggleAllText').text(isAr ? 'توسيع الكل' : 'Expand All');
        } else {
            $('.perm-group-card .x_content').slideDown(200);
            $('.perm-group-card').removeClass('is-collapsed');
            $('#permToggleAllIcon').removeClass('fa-expand').addClass('fa-compress');
            $('#permToggleAllText').text(isAr ? 'طي الكل' : 'Collapse All');
        }
    });

    var isAr = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
    $('.perm-group-card .x_content').slideUp(100);
    $('.perm-group-card').addClass('is-collapsed');
    $('#permToggleAllIcon').removeClass('fa-compress').addClass('fa-expand');
    $('#permToggleAllText').text(isAr ? 'توسيع الكل' : 'Expand All');

        $(document).on('ifChecked', '.pcheckth', function(event) {

        var chtype=$(this).val();
    $('#checkth_'+chtype).iCheck('check');
    $('.multi-select_'+chtype).each(function() {
        $(this).iCheck('check');
    });
});
$(document).on('ifUnchecked', '.pcheckth', function(event) {
    var chtype=$(this).val();
    $('#checkth_'+chtype).iCheck('uncheck');
    $('.multi-select_'+chtype).each(function() {
        $(this).iCheck('uncheck');
    });
});


});
</script>