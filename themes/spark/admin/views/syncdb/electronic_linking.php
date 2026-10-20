<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$foodicsUrl = '';
{
    $json = '{"response_type": "code","name": "Cpe - Arkan","description": "Accounting Software","client_id": "99e315d9-9edc-48cd-9359-d9466b917439","secret": "mP04tZCQodrPFnOiDLvdB2jT3s8FGaYUGvimncIF","redirect": "https://domain1.com/admin/login","install_url": "https://domain1.com/admin/login",
    "scopes": "[general.read,orders.list,inventory.transactions.read,inventory.settings.read,operations.read,customers.list,customers.accounts.read]"
      }';
    $foodicsUrl = 'https://console.foodics.com/authorize?' . http_build_query(json_decode($json));
}
$state = bin2hex(random_bytes(16));
$sallaUrl = 'https://accounts.salla.sa/oauth2/auth?client_id=f19e8e55-5527-4621-ade0-90085f4a08d9&response_type=code&redirect_uri=https://domain2.net/admin/login&scope=offline_access&state='.$state;
?>

<style>
/* ==========================================================================
   Electronic Linking Services — Modern SaaS Layout (Dark & Light Mode)
   ========================================================================== */

.spark-linking-wrapper {
    padding: 8px 0 20px 0;
}

.spark-linking-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 380px));
    gap: 24px;
}

@media (max-width: 767px) {
    .spark-linking-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}

/* ─── Service Card ─── */
.spark-linking-card {
    background-color: var(--spark-surface-card, #ffffff);
    border: 1px solid var(--spark-border-default, #e2e8f0);
    border-radius: var(--spark-radius-xl, 16px);
    box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.05), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.spark-linking-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.12), 0 4px 8px -2px rgba(15, 23, 42, 0.04);
    border-color: var(--spark-primary, #6366f1);
}

/* Dark Mode Overrides for Card Surface */
[data-theme="dark"] .spark-linking-card,
html[data-theme="dark"] .spark-linking-card {
    background-color: var(--spark-surface-elevated, #161f30) !important;
    border-color: var(--spark-border-strong, rgba(255, 255, 255, 0.09)) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45) !important;
}

[data-theme="dark"] .spark-linking-card:hover,
html[data-theme="dark"] .spark-linking-card:hover {
    border-color: var(--spark-primary, #818cf8) !important;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.65), 0 0 0 1px var(--spark-primary, #818cf8) !important;
}

/* ─── Card Logo Header ─── */
.spark-linking-logo-header {
    padding: 24px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, rgba(241, 245, 249, 0.7) 0%, rgba(255, 255, 255, 0.2) 100%);
    border-bottom: 1px solid var(--spark-border-light, #f1f5f9);
    min-height: 110px;
    position: relative;
}

[data-theme="dark"] .spark-linking-logo-header,
html[data-theme="dark"] .spark-linking-logo-header {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%) !important;
    border-bottom-color: rgba(255, 255, 255, 0.08) !important;
}

.spark-linking-logo-pill {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 12px;
    padding: 10px 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    max-width: 85%;
    transition: transform 0.2s ease;
}

.spark-linking-card:hover .spark-linking-logo-pill {
    transform: scale(1.03);
}

[data-theme="dark"] .spark-linking-logo-pill,
html[data-theme="dark"] .spark-linking-logo-pill {
    background: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
}

.spark-linking-logo-pill img {
    max-height: 48px;
    max-width: 180px;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
}

/* ─── Card Content ─── */
.spark-linking-card-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.spark-linking-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.spark-linking-card-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--spark-text-primary, #0f172a);
    margin: 0;
    line-height: 1.4;
}

[data-theme="dark"] .spark-linking-card-title,
html[data-theme="dark"] .spark-linking-card-title {
    color: var(--spark-text-primary, #f1f5f9) !important;
}

.spark-linking-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 600;
    background: var(--spark-success-subtle, #ecfdf5);
    color: var(--spark-success, #10b981);
    border: 1px solid var(--spark-success-border, #a7f3d0);
    white-space: nowrap;
}

[data-theme="dark"] .spark-linking-status,
html[data-theme="dark"] .spark-linking-status {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}

.spark-linking-status .status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

.spark-linking-card-desc {
    color: var(--spark-text-secondary, #64748b);
    font-size: 13.5px;
    line-height: 1.65;
    margin: 0 0 20px 0;
    min-height: 65px;
    flex-grow: 1;
}

[data-theme="dark"] .spark-linking-card-desc,
html[data-theme="dark"] .spark-linking-card-desc {
    color: var(--spark-text-secondary, #94a3b8) !important;
}

/* ─── Card Actions ─── */
.spark-linking-card-footer {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-top: 16px;
    border-top: 1px solid var(--spark-border-light, #f1f5f9);
    margin-top: auto;
    flex-wrap: wrap;
}

[data-theme="dark"] .spark-linking-card-footer,
html[data-theme="dark"] .spark-linking-card-footer {
    border-top-color: rgba(255, 255, 255, 0.08) !important;
}

.spark-linking-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: var(--spark-radius-md, 8px);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    line-height: 1.4;
    border: 1px solid transparent;
}

.spark-linking-btn-primary {
    background: var(--spark-primary, #6366f1);
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);
}

.spark-linking-btn-primary:hover:not(:disabled) {
    background: var(--spark-primary-hover, #4f46e5);
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
    transform: translateY(-1px);
    color: #ffffff !important;
}

.spark-linking-btn-primary:disabled {
    opacity: 0.55;
    cursor: not-allowed;
    box-shadow: none;
}

.spark-linking-btn-outline {
    background: transparent;
    color: var(--spark-text-secondary, #475569) !important;
    border-color: var(--spark-border-strong, #cbd5e1);
}

.spark-linking-btn-outline:hover {
    background: var(--spark-surface-hover, #f8fafc);
    color: var(--spark-primary, #6366f1) !important;
    border-color: var(--spark-primary, #6366f1);
    transform: translateY(-1px);
}

[data-theme="dark"] .spark-linking-btn-outline,
html[data-theme="dark"] .spark-linking-btn-outline {
    color: var(--spark-text-primary, #f1f5f9) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.04) !important;
}

[data-theme="dark"] .spark-linking-btn-outline:hover,
html[data-theme="dark"] .spark-linking-btn-outline:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: var(--spark-primary, #818cf8) !important;
    color: var(--spark-primary, #818cf8) !important;
}

/* ─── Empty State ─── */
.spark-linking-empty {
    padding: 60px 20px;
    text-align: center;
    background: var(--spark-surface-subtle, #f8fafc);
    border: 2px dashed var(--spark-border-default, #e2e8f0);
    border-radius: 16px;
    width: 100%;
}

[data-theme="dark"] .spark-linking-empty,
html[data-theme="dark"] .spark-linking-empty {
    background: rgba(255, 255, 255, 0.02) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.spark-linking-empty-icon {
    font-size: 48px;
    color: var(--spark-text-muted, #94a3b8);
    margin-bottom: 16px;
}

.spark-linking-empty-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--spark-text-primary, #0f172a);
    margin-bottom: 8px;
}

[data-theme="dark"] .spark-linking-empty-title,
html[data-theme="dark"] .spark-linking-empty-title {
    color: var(--spark-text-primary, #f1f5f9) !important;
}

.spark-linking-empty-desc {
    font-size: 14px;
    color: var(--spark-text-secondary, #64748b);
    max-width: 420px;
    margin: 0 auto;
}

[data-theme="dark"] .spark-linking-empty-desc,
html[data-theme="dark"] .spark-linking-empty-desc {
    color: var(--spark-text-secondary, #94a3b8) !important;
}
</style>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plug"></i><?= $page_title ?></h2>
    </div>
    <div class="box-content">
        <div class="spark-linking-wrapper">
            <div class="spark-linking-grid">

                <?php if ($foodics) { ?>
                    <article class="spark-linking-card">
                        <div class="spark-linking-logo-header">
                            <div class="spark-linking-logo-pill">
                                <img src="<?= base_url('assets/uploads/logos/' . $foodics->logo) ?>" alt="<?= htmlspecialchars($foodics->name) ?>">
                            </div>
                        </div>
                        <div class="spark-linking-card-content">
                            <div class="spark-linking-card-top">
                                <h3 class="spark-linking-card-title">
                                    <?= $Settings->user_language == 'arabic' ? $foodics->name : $foodics->ename; ?>
                                </h3>
                                <span class="spark-linking-status">
                                    <span class="status-dot"></span>
                                    <?= lang('active') ? lang('active') : ($Settings->user_language == 'arabic' ? 'نشط' : 'Active'); ?>
                                </span>
                            </div>

                            <p class="spark-linking-card-desc">
                                <?= $Settings->user_language == 'arabic' ? $foodics->description : $foodics->edescription; ?>
                            </p>

                            <div class="spark-linking-card-footer">
                                <a id="subscription" <?= (empty($foodicsUrl) ? 'disabled' : ''); ?> class="spark-linking-btn spark-linking-btn-primary">
                                    <i class="fa fa-external-link"></i> <?= lang('Subscription') ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php } ?>

                <?php if ($jisr) { ?>
                    <article class="spark-linking-card">
                        <div class="spark-linking-logo-header">
                            <div class="spark-linking-logo-pill">
                                <img src="<?= base_url('assets/uploads/logos/' . $jisr->logo) ?>" alt="<?= htmlspecialchars($jisr->name) ?>">
                            </div>
                        </div>
                        <div class="spark-linking-card-content">
                            <div class="spark-linking-card-top">
                                <h3 class="spark-linking-card-title">
                                    <?= $Settings->user_language == 'arabic' ? $jisr->name : $jisr->ename; ?>
                                </h3>
                                <span class="spark-linking-status">
                                    <span class="status-dot"></span>
                                    <?= lang('active') ? lang('active') : ($Settings->user_language == 'arabic' ? 'نشط' : 'Active'); ?>
                                </span>
                            </div>

                            <p class="spark-linking-card-desc">
                                <?= $Settings->user_language == 'arabic' ? $jisr->description : $jisr->edescription; ?>
                            </p>

                            <div class="spark-linking-card-footer">
                                <a href="<?= admin_url('sync/jisr_subscription_settings'); ?>" data-toggle="modal" data-target="#myModal" class="spark-linking-btn spark-linking-btn-primary">
                                    <i class="fa fa-sliders"></i> <?= lang('Subscription_settings') ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php } ?>

                <?php if ($salla) { ?>
                    <article class="spark-linking-card">
                        <div class="spark-linking-logo-header">
                            <div class="spark-linking-logo-pill">
                                <img src="<?= base_url('assets/uploads/logos/' . $salla->logo) ?>" alt="<?= htmlspecialchars($salla->name) ?>">
                            </div>
                        </div>
                        <div class="spark-linking-card-content">
                            <div class="spark-linking-card-top">
                                <h3 class="spark-linking-card-title">
                                    <?= $Settings->user_language == 'arabic' ? $salla->name : $salla->ename; ?>
                                </h3>
                                <span class="spark-linking-status">
                                    <span class="status-dot"></span>
                                    <?= lang('active') ? lang('active') : ($Settings->user_language == 'arabic' ? 'نشط' : 'Active'); ?>
                                </span>
                            </div>

                            <p class="spark-linking-card-desc">
                                <?= $Settings->user_language == 'arabic' ? $salla->description : $salla->edescription; ?>
                            </p>
                            <?php
                            if(empty($sallaSettings->access_token)){
                            ?>
                            <div class="spark-linking-card-footer">
                                <a id="subscription_salla"  class="spark-linking-btn spark-linking-btn-primary">
                                    <i class="fa fa-external-link"></i> <?= lang('Subscription') ?>
                                </a>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                    </article>
                <?php } ?>


                <?php if (!$foodics && !$jisr && !$salla) { ?>
                    <div class="spark-linking-empty">
                        <div class="spark-linking-empty-icon">
                            <i class="fa fa-plug"></i>
                        </div>
                        <div class="spark-linking-empty-title">
                            <?= $Settings->user_language == 'arabic' ? 'لا توجد خدمات ربط إلكتروني مفعلة' : 'No Linking Services Enabled'; ?>
                        </div>
                        <p class="spark-linking-empty-desc">
                            <?= $Settings->user_language == 'arabic' ? 'يمكنك تفعيل خدمات الربط الإلكتروني (مثل سلة أو جسر أو فودكس) من خلال إعدادات النظام للتكامل مع تطبيقاتك المفضلة.' : 'You can enable integration services (such as Salla or Jisr or Foodics) in system settings to connect your tools.'; ?>
                        </p>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#subscription').click(function (e) {
            e.preventDefault();
            var win = window.open('<?= $foodicsUrl; ?>', '_new');
            if (win) {
                win.focus();
            } else {
                alert('Please allow popups for this website');
            }
        });

        $('#subscription_salla').click(function (e) {
            e.preventDefault();
            var win = window.open('<?= $sallaUrl; ?>', '_new');
            if (win) {
                win.focus();
            } else {
                alert('Please allow popups for this website');
            }
        });
    });
</script>