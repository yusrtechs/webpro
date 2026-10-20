<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
    $(document).ready(function () {
        CURI = '<?= admin_url('system_settings/whatsapp_log'); ?>';
    });
</script>

<div class="spark-whatsapp-module">
    <!-- Green WhatsApp Header Banner (Matching React Reference) -->
    <div class="spark-whatsapp-banner">
        <div class="spark-whatsapp-banner-content">
            <div class="spark-whatsapp-banner-badge">
                <i class="fa fa-whatsapp"></i>
            </div>
            <div>
                <h1 class="spark-whatsapp-banner-title"><?= lang('whatsapp_service'); ?></h1>
                <p class="spark-whatsapp-banner-subtitle"><?= lang('whatsapp_service_sub'); ?></p>
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

    <!-- 4-Tab Navigation Bar -->
    <div class="spark-whatsapp-nav">
        <a href="<?= admin_url('system_settings/whatsapp_settings'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-cogs"></i>
            <span><?= lang('Whatsapp_settings'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/send_whatsapp'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-paper-plane"></i>
            <span><?= lang('send_whatsapp'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/whatsapp_log'); ?>" class="spark-whatsapp-tab is-active">
            <i class="fa fa-history"></i>
            <span><?= lang('Whatsapp_log'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/whatsapp_templates'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-file-text-o"></i>
            <span><?= lang('Whatsapp_templates'); ?></span>
        </a>
    </div>

    <!-- Main Content Body -->
    <div class="spark-whatsapp-body">
        <?php
        // Count entries from real log string safely
        $total_entries = 0;
        $error_entries = 0;
        if (!empty($log) && strpos($log, lang('log_x_exists')) === false) {
            $raw_lines = array_filter(explode("\n", strip_tags($log)));
            $total_entries = count($raw_lines);
            $error_entries = substr_count($log, 'text-danger') + substr_count($log, 'ERROR');
        }
        $success_entries = max(0, $total_entries - $error_entries);
        ?>

        <!-- Quick Stat Summary Cards (Matching React Screenshot 3) -->
        <div class="spark-whatsapp-stats">
            <div class="spark-whatsapp-stat-card">
                <div class="spark-whatsapp-stat-icon icon-green">
                    <i class="fa fa-commenting-o"></i>
                </div>
                <div>
                    <h4 class="spark-whatsapp-stat-value"><?= $total_entries; ?></h4>
                    <p class="spark-whatsapp-stat-label"><?= lang('total_messages'); ?></p>
                </div>
            </div>

            <div class="spark-whatsapp-stat-card">
                <div class="spark-whatsapp-stat-icon icon-emerald">
                    <i class="fa fa-check-circle-o"></i>
                </div>
                <div>
                    <h4 class="spark-whatsapp-stat-value"><?= $success_entries; ?></h4>
                    <p class="spark-whatsapp-stat-label"><?= lang('delivered'); ?></p>
                </div>
            </div>

            <div class="spark-whatsapp-stat-card">
                <div class="spark-whatsapp-stat-icon icon-amber">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div>
                    <h4 class="spark-whatsapp-stat-value"><?= $date; ?></h4>
                    <p class="spark-whatsapp-stat-label"><?= lang('log_date'); ?></p>
                </div>
            </div>

            <div class="spark-whatsapp-stat-card">
                <div class="spark-whatsapp-stat-icon icon-rose">
                    <i class="fa fa-times-circle-o"></i>
                </div>
                <div>
                    <h4 class="spark-whatsapp-stat-value"><?= $error_entries; ?></h4>
                    <p class="spark-whatsapp-stat-label"><?= lang('failed'); ?></p>
                </div>
            </div>
        </div>

        <!-- Log Viewer Container -->
        <div class="spark-whatsapp-card" style="margin-bottom: 0;">
            <div class="spark-whatsapp-card-title">
                <i class="fa fa-file-text-o"></i>
                <span><?= lang('Whatsapp_log_heading'); ?></span>
            </div>

            <pre style="color: #38bdf8; border: 1px solid var(--spark-border-default, #1e293b); border-radius: 12px; padding: 18px; max-height: 480px; overflow-y: auto; font-family: 'Consolas', 'Monaco', monospace; font-size: 13px; line-height: 1.6;"><?php echo $log; ?></pre>
        </div>
    </div>
</div>
