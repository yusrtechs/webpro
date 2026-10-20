<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    .spark-report-card {
        background-color: var(--spark-surface-card, #ffffff);
        border: 1px solid var(--spark-border-default, #e2e8f0);
        border-radius: 14px;
        box-shadow: var(--spark-shadow-xs, 0 1px 3px rgba(15, 23, 42, 0.05));
        overflow: hidden;
        margin-bottom: 20px;
        transition: all 0.2s ease;
    }
    .spark-report-card:hover {
        box-shadow: var(--spark-shadow-md, 0 4px 12px rgba(15, 23, 42, 0.08));
    }
    .spark-report-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--spark-border-light, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: transparent;
    }
    .spark-report-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--spark-text-primary, #0f172a);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .spark-report-card-body {
        padding: 16px;
        background: transparent;
    }
    .spark-report-card-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        background: var(--spark-primary-subtle, rgba(99, 102, 241, 0.12));
        color: var(--spark-primary, #6366f1);
    }
    [data-theme="dark"] .spark-report-card {
        background-color: var(--spark-surface-card, #111827) !important;
        border-color: var(--spark-border, #1f2937) !important;
    }
    [data-theme="dark"] .spark-report-card-header {
        border-bottom-color: var(--spark-border, #1f2937) !important;
    }
    [data-theme="dark"] .spark-report-card-title {
        color: var(--spark-text-primary, #f1f5f9) !important;
    }
    [data-theme="dark"] .spark-report-card-badge {
        background: rgba(99, 102, 241, 0.2) !important;
        color: #818cf8 !important;
    }
    [data-theme="dark"] .highcharts-background {
        fill: transparent !important;
    }
    .highcharts-container, .highcharts-container svg {
        background: transparent !important;
    }
</style>

<script src="<?= $assets; ?>js/hc/highcharts.js"></script>
<script type="text/javascript">
    $(function () {
        function renderBestSellerCharts() {
            var isDark = $('html').attr('data-theme') === 'dark' || $('body').attr('data-theme') === 'dark' || (localStorage.getItem('spark-theme') === 'dark');
            var textColor = isDark ? '#94a3b8' : '#475569';
            var textPrimary = isDark ? '#f1f5f9' : '#0f172a';
            var gridColor = isDark ? 'rgba(255, 255, 255, 0.07)' : 'rgba(0, 0, 0, 0.06)';
            var tooltipBg = isDark ? '#1f2937' : '#ffffff';
            var tooltipBorder = isDark ? '#374151' : '#e2e8f0';
            
            var brandPrimary = '#6366f1';
            var brandSecondary = '#8b5cf6';
            if (isDark) {
                brandPrimary = '#818cf8';
                brandSecondary = '#a78bfa';
            }

            function getChartConfig(seriesData) {
                return {
                    chart: {
                        type: 'column',
                        backgroundColor: 'transparent',
                        style: {
                            fontFamily: "'Inter', 'Cairo', 'Tajawal', sans-serif"
                        }
                    },
                    title: { text: '' },
                    credits: { enabled: false },
                    xAxis: {
                        type: 'category',
                        lineColor: gridColor,
                        tickColor: gridColor,
                        labels: {
                            rotation: -60,
                            style: {
                                fontSize: '12px',
                                color: textColor,
                                fontFamily: "'Inter', 'Cairo', 'Tajawal', sans-serif"
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: '' },
                        gridLineColor: gridColor,
                        labels: {
                            style: {
                                fontSize: '12px',
                                color: textColor,
                                fontFamily: "'Inter', 'Cairo', 'Tajawal', sans-serif"
                            }
                        }
                    },
                    legend: { enabled: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        borderColor: tooltipBorder,
                        borderRadius: 8,
                        shadow: {
                            color: 'rgba(0, 0, 0, 0.25)',
                            offsetX: 0,
                            offsetY: 4,
                            opacity: 0.15,
                            width: 10
                        },
                        style: {
                            color: textPrimary,
                            fontSize: '13px',
                            fontFamily: "'Inter', 'Cairo', 'Tajawal', sans-serif"
                        }
                    },
                    plotOptions: {
                        column: {
                            borderRadius: 6,
                            borderWidth: 0,
                            pointPadding: 0.1,
                            groupPadding: 0.15,
                            color: {
                                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                                stops: [
                                    [0, brandPrimary],
                                    [1, brandSecondary]
                                ]
                            }
                        }
                    },
                    series: [{
                        name: '<?= lang('sold'); ?>',
                        data: seriesData,
                        dataLabels: {
                            enabled: true,
                            rotation: -90,
                            color: isDark ? '#f1f5f9' : '#0f172a',
                            align: 'right',
                            y: -25,
                            style: {
                                fontSize: '12px',
                                fontWeight: '600',
                                textOutline: isDark ? '2px #111827' : '2px #ffffff',
                                fontFamily: "'Inter', 'Cairo', 'Tajawal', sans-serif"
                            }
                        }
                    }]
                };
            }

            <?php if ($m1bs) { ?>
            $('#m1bschart').highcharts(getChartConfig([
                <?php
                foreach ($m1bs as $r) {
                    if ($r->quantity > 0) {
                        echo "['" . addslashes($r->product_name) . '<br>(' . addslashes($r->product_code) . ")', " . $r->quantity . '],';
                    }
                } ?>
            ]));
            <?php } ?>

            <?php if ($m2bs) { ?>
            $('#m2bschart').highcharts(getChartConfig([
                <?php
                foreach ($m2bs as $r) {
                    if ($r->quantity > 0) {
                        echo "['" . addslashes($r->product_name) . '<br>(' . addslashes($r->product_code) . ")', " . $r->quantity . '],';
                    }
                } ?>
            ]));
            <?php } ?>

            <?php if ($m3bs) { ?>
            $('#m3bschart').highcharts(getChartConfig([
                <?php
                foreach ($m3bs as $r) {
                    if ($r->quantity > 0) {
                        echo "['" . addslashes($r->product_name) . '<br>(' . addslashes($r->product_code) . ")', " . $r->quantity . '],';
                    }
                } ?>
            ]));
            <?php } ?>

            <?php if ($m4bs) { ?>
            $('#m4bschart').highcharts(getChartConfig([
                <?php
                foreach ($m4bs as $r) {
                    if ($r->quantity > 0) {
                        echo "['" . addslashes($r->product_name) . '<br>(' . addslashes($r->product_code) . ")', " . $r->quantity . '],';
                    }
                } ?>
            ]));
            <?php } ?>
        }

        renderBestSellerCharts();

        // Listen to dynamic theme toggles
        if (window.MutationObserver) {
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.attributeName === 'data-theme') {
                        renderBestSellerCharts();
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
            if (document.body) {
                observer.observe(document.body, { attributes: true, attributeFilter: ['data-theme'] });
            }
        }
    });
</script>

<?= $content_report ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue">
            <i class="fa-fw fa fa-line-chart"></i>
            <?= lang('best_sellers') . ' (' . ($warehouse ? $warehouse->name : lang('all_warehouses')) . ')'; ?>
        </h2>
        <?php if (!empty($warehouses)) { ?>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?= lang('warehouses') ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= admin_url('reports/best_sellers') ?>"><i class="fa fa-building-o"></i> <?= lang('all_warehouses') ?></a></li>
                        <li class="divider"></li>
                        <?php
                        foreach ($warehouses as $warehouse) {
                            echo '<li><a href="' . admin_url('reports/best_sellers/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                        } ?>
                    </ul>
                </li>
            </ul>
        </div>
        <?php } ?>
    </div>
    <div class="box-content">

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-sm-6">
            <div class="spark-report-card">
                <div class="spark-report-card-header">
                    <h3 class="spark-report-card-title">
                        <i class="fa fa-calendar text-primary"></i> <?= $m1; ?>
                    </h3>
                    <span class="spark-report-card-badge"><?= lang('best_sellers'); ?></span>
                </div>
                <div class="spark-report-card-body">
                    <div id="m1bschart" style="width:100%; height:450px;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="spark-report-card">
                <div class="spark-report-card-header">
                    <h3 class="spark-report-card-title">
                        <i class="fa fa-calendar text-primary"></i> <?= $m2; ?>
                    </h3>
                    <span class="spark-report-card-badge"><?= lang('best_sellers'); ?></span>
                </div>
                <div class="spark-report-card-body">
                    <div id="m2bschart" style="width:100%; height:450px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="spark-report-card">
                <div class="spark-report-card-header">
                    <h3 class="spark-report-card-title">
                        <i class="fa fa-calendar text-primary"></i> <?= $m3; ?>
                    </h3>
                    <span class="spark-report-card-badge"><?= lang('best_sellers'); ?></span>
                </div>
                <div class="spark-report-card-body">
                    <div id="m3bschart" style="width:100%; height:450px;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="spark-report-card">
                <div class="spark-report-card-header">
                    <h3 class="spark-report-card-title">
                        <i class="fa fa-calendar text-primary"></i> <?= $m4; ?>
                    </h3>
                    <span class="spark-report-card-badge"><?= lang('best_sellers'); ?></span>
                </div>
                <div class="spark-report-card-body">
                    <div id="m4bschart" style="width:100%; height:450px;"></div>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>


