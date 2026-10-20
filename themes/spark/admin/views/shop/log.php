<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
    $(document).ready(function () {
        CURI = '<?= admin_url('system_settings/sms_log'); ?>';
    });
</script>

<div class="spark-sms-module">
    <!-- Blue SMS Header Banner -->
    <div class="spark-sms-banner">
        <div class="spark-sms-banner-content">
            <div class="spark-sms-banner-badge">
                <i class="fa fa-history"></i>
            </div>
            <div>
                <h1 class="spark-sms-banner-title"><?= lang('sms_log'); ?></h1>
                <p class="spark-sms-banner-subtitle"><?= lang('sms_log_sub'); ?></p>
            </div>
        </div>

        <!-- Date Filter in Header Toolbar -->
        <div class="hidden-xs" style="max-width: 220px;">
            <div class="input-group">
                <span class="input-group-addon" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff;"><i class="fa fa-calendar"></i></span>
                <input type="text" value="<?= $date; ?>" id="log-date" class="form-control" style="background: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.3); font-weight:700; color: #0f172a; height: 40px; border-radius: 0 10px 10px 0;">
            </div>
        </div>
    </div>

    <!-- 3-Tab Navigation Bar -->
    <div class="spark-sms-nav">
        <a href="<?= admin_url('system_settings/sms_settings'); ?>" class="spark-sms-tab">
            <i class="fa fa-cogs"></i>
            <span><?= lang('sms_settings'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/send_sms'); ?>" class="spark-sms-tab">
            <i class="fa fa-paper-plane"></i>
            <span><?= lang('send_sms'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/sms_log'); ?>" class="spark-sms-tab is-active">
            <i class="fa fa-history"></i>
            <span><?= lang('sms_log'); ?></span>
        </a>
    </div>

    <!-- Main Content Body -->
    <div class="spark-sms-body">
        <?php
        // Count entries from real log string safely without fake data
        $total_entries = 0;
        $error_entries = 0;
        if (!empty($log) && strpos($log, lang('log_x_exists')) === false) {
            $raw_lines = array_filter(explode("\n", strip_tags($log)));
            $total_entries = count($raw_lines);
            $error_entries = substr_count($log, 'text-danger') + substr_count($log, 'SMS.ERROR') + substr_count($log, 'ERROR');
        }
        $success_entries = max(0, $total_entries - $error_entries);
        ?>

        <!-- Quick Stat Summary Cards -->
        <div class="spark-sms-stats">
            <div class="spark-sms-stat-card">
                <div class="spark-sms-stat-icon icon-blue">
                    <i class="fa fa-envelope-o"></i>
                </div>
                <div>
                    <h4 class="spark-sms-stat-value"><?= $total_entries; ?></h4>
                    <p class="spark-sms-stat-label"><?= lang('total_messages'); ?></p>
                </div>
            </div>

            <div class="spark-sms-stat-card">
                <div class="spark-sms-stat-icon icon-indigo">
                    <i class="fa fa-check-circle-o"></i>
                </div>
                <div>
                    <h4 class="spark-sms-stat-value"><?= $success_entries; ?></h4>
                    <p class="spark-sms-stat-label"><?= lang('delivered'); ?></p>
                </div>
            </div>

            <div class="spark-sms-stat-card">
                <div class="spark-sms-stat-icon icon-amber">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div>
                    <h4 class="spark-sms-stat-value"><?= $date; ?></h4>
                    <p class="spark-sms-stat-label"><?= lang('log_date'); ?></p>
                </div>
            </div>

            <div class="spark-sms-stat-card">
                <div class="spark-sms-stat-icon icon-rose">
                    <i class="fa fa-times-circle-o"></i>
                </div>
                <div>
                    <h4 class="spark-sms-stat-value"><?= $error_entries; ?></h4>
                    <p class="spark-sms-stat-label"><?= lang('failed'); ?></p>
                </div>
            </div>
        </div>

        <!-- Log Viewer Container -->
        <div class="spark-sms-card" style="margin-bottom: 0;">
            <div class="spark-sms-card-title">
                <i class="fa fa-file-text-o"></i>
                <span><?= lang('sms_log_heading'); ?></span>
            </div>

            <pre style="color: #38bdf8; border: 1px solid var(--spark-border-default, #1e293b); border-radius: 12px; padding: 18px; max-height: 480px; overflow-y: auto; font-family: 'Consolas', 'Monaco', monospace; font-size: 13px; line-height: 1.6;"><?php echo $log; ?></pre>
        </div>
    </div>
</div>
