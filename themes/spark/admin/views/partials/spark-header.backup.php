<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<header class="spark-header no-print">
  <!-- Brand / Toggle -->
  <div style="display: flex; align-items: center; gap: 12px;">
    <button class="spark-header-toggle" id="sparkSidebarToggle" type="button" aria-label="Toggle Sidebar" title="القائمة">
      <i class="fa fa-bars"></i>
    </button>

    <a class="spark-header-brand" href="<?= admin_url() ?>">
      <?php if ($Settings->logo2) { ?>
        <img src="<?= base_url('assets/uploads/logos/' . $Settings->logo2); ?>" alt="<?= $site_name ?>" class="spark-header-logo" />
      <?php } ?>
      <span class="spark-header-brand-text"><?= $site_name ?></span>
    </a>

    <!-- Spark Module App Launcher Dropdown -->
    <?php
    $dbname = str_replace("cpesoft_", "", $this->db->database);
    $launcherModules = [
      ['id' => 'welcome', 'title' => lang('dashboard'), 'icon' => 'fa fa-dashboard', 'url' => admin_url(), 'perm' => ($this->Settings->accountingOnly == 0), 'target' => '_self'],
      ['id' => 'branches', 'title' => lang('branches'), 'icon' => 'fa fa-building-o', 'url' => admin_url('branches'), 'perm' => (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1), 'target' => '_self'],
      ['id' => 'accounts', 'title' => lang('accounts'), 'icon' => 'fa fa-calculator', 'url' => admin_url('accounts/dashboard'), 'perm' => (isset($this->Settings->preAccounting) && $this->Settings->preAccounting == 1), 'target' => '_self'],
      ['id' => 'cars', 'title' => lang('cars'), 'icon' => 'fa fa-car', 'url' => admin_url('cars'), 'perm' => (isset($this->Settings->isCars) && $this->Settings->isCars == 1), 'target' => '_self'],
      ['id' => 'products', 'title' => lang('products'), 'icon' => 'fa fa-barcode', 'url' => admin_url('products'), 'perm' => ($this->Settings->accountingOnly == 0 && (!isset($this->Settings->isCars) || $this->Settings->isCars != 1)), 'target' => '_self'],
      ['id' => 'carssales', 'title' => lang('carssales'), 'icon' => 'fa fa-dollar', 'url' => admin_url('carssales'), 'perm' => (isset($this->Settings->isCars) && $this->Settings->isCars == 1), 'target' => '_self'],
      ['id' => 'carspurchases', 'title' => lang('carspurchases'), 'icon' => 'fa fa-truck', 'url' => admin_url('carspurchases'), 'perm' => (isset($this->Settings->isCars) && $this->Settings->isCars == 1), 'target' => '_self'],
      ['id' => 'sales', 'title' => lang('sales'), 'icon' => 'fa fa-heart', 'url' => ($Owner || $Admin) ? admin_url('sales') : admin_url('sales/dashboard'), 'perm' => ($this->Settings->accountingOnly == 0 && (!isset($this->Settings->isCars) || $this->Settings->isCars != 1)), 'target' => '_self'],
      ['id' => 'purchases', 'title' => lang('purchases'), 'icon' => 'fa fa-star', 'url' => ($Owner || $Admin) ? admin_url('purchases') : admin_url('purchases/dashboard'), 'perm' => ($this->Settings->accountingOnly == 0 && (!isset($this->Settings->isCars) || $this->Settings->isCars != 1)), 'target' => '_self'],
      ['id' => 'hr', 'title' => lang('HR'), 'icon' => 'fa fa-users', 'url' => admin_url('auth/login_hr/'), 'perm' => (($Owner || $Admin) && isset($this->Settings->isHR) && $this->Settings->isHR == 1), 'target' => '_blank'],
      ['id' => 'crm', 'title' => lang('CRM'), 'icon' => 'fa fa-sitemap', 'url' => admin_url('auth/login_crm/'), 'perm' => ($dbname == "arkanpro" || $dbname == "spark"), 'target' => '_blank'],
      ['id' => 'fixedassets', 'title' => lang('Fixed_assets'), 'icon' => 'fa fa-cubes', 'url' => admin_url('fixedassets'), 'perm' => (isset($this->Settings->isFixedAsset) && $this->Settings->isFixedAsset == 1), 'target' => '_self'],
      ['id' => 'tailor', 'title' => lang('Tailor'), 'icon' => 'fa fa-scissors', 'url' => admin_url('tailor/tailor'), 'perm' => (isset($this->Settings->isTailer) && $this->Settings->isTailer == 1), 'target' => '_self'],
      ['id' => 'manufacturing', 'title' => lang('Manufacturing'), 'icon' => 'fa fa-industry', 'url' => admin_url('manufacturing'), 'perm' => (isset($this->Settings->IsManufacturing) && $this->Settings->IsManufacturing == 1), 'target' => '_self'],
      ['id' => 'auth', 'title' => lang('User_tools'), 'icon' => 'fa fa-cogs', 'url' => admin_url('users'), 'perm' => ($Owner || $Admin || !empty($GP['users-index'])), 'target' => '_self'],
      ['id' => 'shop_settings', 'title' => lang('shop'), 'icon' => 'fa fa-shopping-cart', 'url' => admin_url('shop_settings/shop_sync'), 'perm' => (isset($this->Settings->isStore) && $this->Settings->isStore == 1 && ($Owner || $Admin)), 'target' => '_self'],
      ['id' => 'carsreports', 'title' => lang('reports'), 'icon' => 'fa fa-bar-chart', 'url' => admin_url('carsreports/dashboard'), 'perm' => (isset($this->Settings->isCars) && $this->Settings->isCars == 1), 'target' => '_self'],
      ['id' => 'reports', 'title' => lang('reports'), 'icon' => 'fa fa-bar-chart-o', 'url' => admin_url('reports/dashboard'), 'perm' => ($this->Settings->accountingOnly == 0 && (!isset($this->Settings->isCars) || $this->Settings->isCars != 1)), 'target' => '_self'],
      ['id' => 'sync', 'title' => lang('Integrations'), 'icon' => 'fa fa-cloud-download', 'url' => admin_url('sync/dashboard'), 'perm' => (isset($this->Settings->isElectronicLinking) && $this->Settings->isElectronicLinking == 1), 'target' => '_self']
    ];
    ?>
    <div class="dropdown spark-module-switcher" style="display: inline-block;">
      <button class="spark-launcher-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="<?= lang('modules') ?? 'الوحدات'; ?>" title="<?= lang('modules') ?? 'الوحدات'; ?>">
        <i class="fa fa-th spark-launcher-icon"></i>
        <span class="spark-launcher-text hidden-xs"><?= lang('modules') ?? 'الوحدات'; ?></span>
        <i class="fa fa-angle-down spark-launcher-arrow hidden-xs"></i>
      </button>
      <div class="dropdown-menu spark-module-grid-menu">
        <div class="spark-module-grid-header">
          <span class="spark-module-grid-title"><i class="fa fa-th"></i> <?= lang('modules') ?? 'تطبيقات النظام'; ?></span>
        </div>
        <div class="spark-module-grid">
          <?php foreach ($launcherModules as $mod) {
            if ($mod['perm']) {
              $isActive = ($m === $mod['id']);
          ?>
            <a href="<?= $mod['url']; ?>" target="<?= $mod['target']; ?>" class="spark-module-item <?= $isActive ? 'is-active' : ''; ?>" title="<?= $mod['title']; ?>">
              <div class="spark-module-item-icon">
                <i class="<?= $mod['icon']; ?>"></i>
              </div>
              <span class="spark-module-item-title"><?= $mod['title']; ?></span>
            </a>
          <?php } } ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Header Right Actions -->
  <div class="spark-header-actions">
    <!-- POS Quick Button -->
    <?php if (POS && $Settings->pos == 1) { ?>
      <a class="spark-header-action" title="<?= lang('pos') ?>" href="<?= admin_url('pos') ?>">
        <i class="fa fa-th-large"></i>
      </a>
    <?php } ?>

    <!-- Calculator -->
    <div class="dropdown" style="display: inline-block;">
      <a class="spark-header-action dropdown-toggle" data-toggle="dropdown" href="#" title="<?= lang('calculator') ?>">
        <i class="fa fa-calculator"></i>
      </a>
      <ul class="dropdown-menu pull-right calc" style="padding: 8px;">
        <li class="dropdown-content">
          <span id="inlineCalc"></span>
        </li>
      </ul>
    </div>

    <!-- Alerts Dropdown (Quantity / Expiry / Shop alerts) -->
    <?php if (($Owner || $Admin || (isset($GP['reports-quantity_alerts']) && $GP['reports-quantity_alerts']) || (isset($GP['reports-expiry_alerts']) && $GP['reports-expiry_alerts'])) && (($qty_alert_num ?? 0) > 0 || ($exp_alert_num ?? 0) > 0 || ($shop_sale_alerts ?? 0) || ($shop_payment_alerts ?? 0))) {
      $totalAlerts = ($qty_alert_num ?? 0) + (($Settings->product_expiry) ? ($exp_alert_num ?? 0) : 0) + ($shop_sale_alerts ?? 0) + ($shop_payment_alerts ?? 0);
    ?>
      <div class="dropdown" style="display: inline-block;">
        <a class="spark-header-action dropdown-toggle" data-toggle="dropdown" href="#" title="<?= lang('alerts') ?>">
          <i class="fa fa-exclamation-triangle"></i>
          <?php if ($totalAlerts > 0) { ?>
            <span class="spark-header-badge"><?= $totalAlerts ?></span>
          <?php } ?>
        </a>
        <ul class="dropdown-menu pull-right">
          <?php if (($qty_alert_num ?? 0) > 0) { ?>
            <li>
              <a href="<?= admin_url('reports/quantity_alerts') ?>">
                <span class="label label-danger pull-right" style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
              </a>
            </li>
          <?php } ?>
          <?php if ($Settings->product_expiry && ($exp_alert_num ?? 0) > 0) { ?>
            <li>
              <a href="<?= admin_url('reports/expiry_alerts') ?>">
                <span class="label label-danger pull-right" style="margin-top:3px;"><?= $exp_alert_num; ?></span>
                <span style="padding-right: 35px;"><?= lang('expiry_alerts') ?></span>
              </a>
            </li>
          <?php } ?>
          <?php if ($shop_sale_alerts ?? 0) { ?>
            <li>
              <a href="<?= admin_url('sales?shop=yes&delivery=no') ?>">
                <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_sale_alerts; ?></span>
                <span style="padding-right: 35px;"><?= lang('sales_x_delivered') ?></span>
              </a>
            </li>
          <?php } ?>
          <?php if ($shop_payment_alerts ?? 0) { ?>
            <li>
              <a href="<?= admin_url('sales?shop=yes&attachment=yes') ?>">
                <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_payment_alerts; ?></span>
                <span style="padding-right: 35px;"><?= lang('manual_payments') ?></span>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>

    <!-- Notifications Dropdown ($info) -->
    <?php if (!empty($info)) { ?>
      <div class="dropdown" style="display: inline-block;">
        <a class="spark-header-action dropdown-toggle" data-toggle="dropdown" href="#" title="<?= lang('notifications') ?>">
          <i class="fa fa-info-circle"></i>
          <span class="spark-header-badge"><?= sizeof($info) ?></span>
        </a>
        <ul class="dropdown-menu pull-right content-scroll">
          <li class="dropdown-header"><i class="fa fa-info-circle"></i> <?= lang('notifications'); ?></li>
          <li class="dropdown-content">
            <div class="scroll-div">
              <div class="top-menu-scroll">
                <ol class="oe">
                  <?php foreach ($info as $n) {
                    echo '<li>' . $n->comment . '</li>';
                  } ?>
                </ol>
              </div>
            </div>
          </li>
        </ul>
      </div>
    <?php } ?>

    <!-- Calendar Events Dropdown -->
    <?php if (!empty($events)) { ?>
      <div class="dropdown hidden-xs" style="display: inline-block;">
        <a class="spark-header-action dropdown-toggle" data-toggle="dropdown" href="#" title="<?= lang('calendar') ?>">
          <i class="fa fa-calendar"></i>
          <span class="spark-header-badge"><?= sizeof($events) ?></span>
        </a>
        <ul class="dropdown-menu pull-right content-scroll">
          <li class="dropdown-header"><i class="fa fa-calendar"></i> <?= lang('upcoming_events'); ?></li>
          <li class="dropdown-content">
            <div class="top-menu-scroll">
              <ol class="oe">
                <?php foreach ($events as $event) {
                  echo '<li>' . date($dateFormats['php_ldate'], strtotime($event->start)) . ' <strong>' . $event->title . '</strong><br>' . $event->description . '</li>';
                } ?>
              </ol>
            </div>
          </li>
          <li class="dropdown-footer">
            <a href="<?= admin_url('calendar') ?>" class="btn-block link">
              <i class="fa fa-calendar"></i> <?= lang('calendar') ?>
            </a>
          </li>
        </ul>
      </div>
    <?php } else { ?>
      <a class="spark-header-action hidden-xs" title="<?= lang('calendar') ?>" href="<?= admin_url('calendar') ?>">
        <i class="fa fa-calendar"></i>
      </a>
    <?php } ?>

    <!-- Language Switcher Dropdown -->
    <div class="dropdown hidden-xs" style="display: inline-block;">
      <a class="spark-header-action dropdown-toggle" data-toggle="dropdown" href="#" title="<?= lang('language') ?>">
        <img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt="" style="width: 18px; height: 12px; object-fit: cover;">
      </a>
      <ul class="dropdown-menu pull-right">
        <?php
        $scanned_lang_dir = array_map(function ($path) {
          return basename($path);
        }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
        foreach ($scanned_lang_dir as $entry) {
        ?>
          <li>
            <a href="<?= admin_url('welcome/language/' . $entry); ?>">
              <img src="<?= base_url('assets/images/' . $entry . '.png'); ?>" class="language-img" style="width: 16px; margin-inline-end: 6px;">
              <?= ucwords($entry); ?>
            </a>
          </li>
        <?php } ?>
        <li class="divider"></li>
        <li>
          <a href="<?= admin_url('welcome/toggle_rtl') ?>">
            <i class="fa fa-align-<?= $Settings->user_language == 'arabic' ? 'right' : 'left'; ?>"></i>
            <?= lang('toggle_alignment') ?>
          </a>
        </li>
      </ul>
    </div>

    <!-- User Profile Dropdown -->
    <div class="dropdown" style="display: inline-block;">
      <a class="spark-header-user dropdown-toggle" data-toggle="dropdown" href="#">
        <img src="<?= $this->session->userdata('avatar') ? base_url() . 'assets/uploads/avatars/thumbs/' . $this->session->userdata('avatar') : base_url('assets/images/' . $this->session->userdata('gender') . '.png'); ?>" class="spark-header-avatar" alt="Avatar">
        <span class="spark-header-username"><?= $this->session->userdata('username'); ?></span>
        <i class="fa fa-angle-down" style="font-size: 12px; opacity: 0.7;"></i>
      </a>
      <?php if ($this->session->userdata('username') != 'demo') { ?>
        <ul class="dropdown-menu pull-right">
          <li>
            <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>">
              <i class="fa fa-user"></i> <?= lang('profile'); ?>
            </a>
          </li>
          <li>
            <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>">
              <i class="fa fa-key"></i> <?= lang('change_password'); ?>
            </a>
          </li>
          <li class="divider"></li>
          <li>
            <a href="<?= admin_url('logout'); ?>">
              <i class="fa fa-sign-out"></i> <?= lang('logout'); ?>
            </a>
          </li>
        </ul>
      <?php } ?>
    </div>

  </div>
</header>
