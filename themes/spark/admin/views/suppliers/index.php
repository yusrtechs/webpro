<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Query real KPI metrics directly from database
$ci = &get_instance();
$prefix = $ci->db->dbprefix;

// 1. Total Suppliers
$q_sup = $ci->db->where('group_name', 'supplier')->count_all_results('companies');
$total_suppliers = (int)$q_sup;

// 2. Active Suppliers (with at least 1 purchase)
$q_act = $ci->db->query("SELECT COUNT(DISTINCT supplier_id) as active_suppliers FROM {$prefix}purchases WHERE supplier_id > 0");
$active_suppliers = ($q_act && $q_act->num_rows() > 0) ? (int)($q_act->row()->active_suppliers ?? 0) : 0;
$active_percentage = ($total_suppliers > 0) ? round(($active_suppliers / $total_suppliers) * 100) : 100;

// 3. Total Purchases & Outstanding Due Balance
$q_pur = $ci->db->query("SELECT COUNT(*) as total_purchases_count, SUM(grand_total) as total_purchases_amount, SUM(paid) as total_paid FROM {$prefix}purchases");
$row_pur = ($q_pur && $q_pur->num_rows() > 0) ? $q_pur->row() : null;
$total_purchases_count = (int)($row_pur->total_purchases_count ?? 0);
$total_purchases_raw = (float)($row_pur->total_purchases_amount ?? 0);
$total_paid_raw = (float)($row_pur->total_paid ?? 0);
$total_due_raw = max(0, $total_purchases_raw - $total_paid_raw);
$due_percentage = ($total_purchases_raw > 0) ? round(($total_due_raw / $total_purchases_raw) * 100) : 0;

if ($total_purchases_raw >= 1000000) {
    $total_purchases_display = number_format($total_purchases_raw / 1000000, 1) . 'M';
} elseif ($total_purchases_raw >= 1000) {
    $total_purchases_display = number_format($total_purchases_raw / 1000, 1) . 'K';
} else {
    $total_purchases_display = $this->sma->formatMoney($total_purchases_raw);
}

if ($total_due_raw >= 1000000) {
    $total_due_display = number_format($total_due_raw / 1000000, 1) . 'M';
} elseif ($total_due_raw >= 1000) {
    $total_due_display = number_format($total_due_raw / 1000, 1) . 'K';
} else {
    $total_due_display = $this->sma->formatMoney($total_due_raw);
}
?>

<div class="spark-branches-page spark-suppliers-page">

  <!-- 1. Page Header (React Reference Layout) -->
  <div class="spark-branches-header spark-suppliers-header">
    <div class="spark-branches-header-start spark-suppliers-header-start">
      <div class="spark-branches-header-icon spark-suppliers-header-icon">
        <i class="fa fa-truck"></i>
      </div>
      <div class="spark-branches-header-info spark-suppliers-header-info">
        <h1 class="spark-branches-title spark-suppliers-title"><?= lang('Suppliers'); ?></h1>
        <p class="spark-branches-subtitle spark-suppliers-subtitle"><?= lang('manage_suppliers_desc'); ?></p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-suppliers-header-actions">
      <!-- Add Supplier Primary Button -->
      <a href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action" id="add">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('add_supplier'); ?></span>
      </a>

      <!-- Actions Dropdown Button -->
      <div class="dropdown" style="display: inline-block;">
        <button class="spark-btn-secondary-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-tasks"></i>
          <span><?= lang('actions'); ?></span>
          <i class="fa fa-angle-down" style="font-size: 11px; margin-inline-start: 4px;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
          <li>
            <a href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-plus-circle"></i> <?= lang('add_supplier'); ?>
            </a>
          </li>
          <li>
            <a href="<?= admin_url('suppliers/import_csv'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-file-text-o"></i> <?= lang('import_by_csv'); ?>
            </a>
          </li>
          <li>
            <a href="<?= admin_url('suppliers/import_excel'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-file-excel-o"></i> <?= lang('import_by_excel'); ?>
            </a>
          </li>
          <li>
            <a href="#" id="excel" data-action="export_excel">
              <i class="fa fa-download"></i> <?= lang('export_to_excel'); ?>
            </a>
          </li>
          <li class="divider"></li>
          <li>
            <a href="#" class="bpo" title="<b><?= $this->lang->line('delete_suppliers'); ?></b>"
               data-content="<p><?= lang('r_u_sure'); ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure'); ?></button> <button class='btn bpo-close'><?= lang('no'); ?></button>" data-html="true" data-placement="left">
              <i class="fa fa-trash-o"></i> <?= lang('delete_suppliers'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 2. Statistics / KPI Summary Cards (4 Cards) -->
  <div class="spark-branches-kpi-grid spark-suppliers-kpi-grid">
    <!-- Card 1: Total Suppliers (إجمالي الموردين) -->
    <div class="spark-branches-kpi-card spark-suppliers-kpi-card">
      <div class="spark-branches-kpi-top spark-suppliers-kpi-top">
        <span class="spark-branches-kpi-badge spark-suppliers-kpi-badge badge-emerald">+<?= $total_suppliers; ?></span>
        <div class="spark-branches-kpi-icon spark-suppliers-kpi-icon icon-emerald">
          <i class="fa fa-truck"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-suppliers-kpi-content">
        <div class="spark-branches-kpi-val spark-suppliers-kpi-val"><?= $total_suppliers; ?></div>
        <div class="spark-branches-kpi-lbl spark-suppliers-kpi-lbl"><?= lang('total_suppliers'); ?></div>
      </div>
    </div>

    <!-- Card 2: Active Suppliers (الموردون النشطون) -->
    <div class="spark-branches-kpi-card spark-suppliers-kpi-card">
      <div class="spark-branches-kpi-top spark-suppliers-kpi-top">
        <span class="spark-branches-kpi-badge spark-suppliers-kpi-badge badge-cyan"><?= $active_percentage; ?>%</span>
        <div class="spark-branches-kpi-icon spark-suppliers-kpi-icon icon-cyan">
          <i class="fa fa-check-circle"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-suppliers-kpi-content">
        <div class="spark-branches-kpi-val spark-suppliers-kpi-val"><?= $active_suppliers; ?></div>
        <div class="spark-branches-kpi-lbl spark-suppliers-kpi-lbl"><?= lang('active_suppliers'); ?></div>
      </div>
    </div>

    <!-- Card 3: Total Purchases (إجمالي المشتريات) -->
    <div class="spark-branches-kpi-card spark-suppliers-kpi-card">
      <div class="spark-branches-kpi-top spark-suppliers-kpi-top">
        <span class="spark-branches-kpi-badge spark-suppliers-kpi-badge badge-amber"><?= number_format($total_purchases_count); ?></span>
        <div class="spark-branches-kpi-icon spark-suppliers-kpi-icon icon-amber">
          <i class="fa fa-shopping-cart"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-suppliers-kpi-content">
        <div class="spark-branches-kpi-val spark-suppliers-kpi-val"><?= $total_purchases_display; ?></div>
        <div class="spark-branches-kpi-lbl spark-suppliers-kpi-lbl"><?= lang('total_purchases'); ?></div>
      </div>
    </div>

    <!-- Card 4: Outstanding Balance (المستحقات المتبقية) -->
    <div class="spark-branches-kpi-card spark-suppliers-kpi-card">
      <div class="spark-branches-kpi-top spark-suppliers-kpi-top">
        <span class="spark-branches-kpi-badge spark-suppliers-kpi-badge badge-purple"><?= $due_percentage; ?>%</span>
        <div class="spark-branches-kpi-icon spark-suppliers-kpi-icon icon-purple">
          <i class="fa fa-clock-o"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-suppliers-kpi-content">
        <div class="spark-branches-kpi-val spark-suppliers-kpi-val"><?= $total_due_display; ?></div>
        <div class="spark-branches-kpi-lbl spark-suppliers-kpi-lbl"><?= lang('total_due_balance'); ?></div>
      </div>
    </div>
  </div>

  <!-- 3. Suppliers Table Card -->
  <?php if ($Owner || $GP['bulk_actions']) {
      echo admin_form_open('suppliers/supplier_actions', 'id="action-form"');
  } ?>
  <div class="spark-branches-table-card spark-suppliers-table-card">
    <div class="table-responsive" style="border: none;">
      <table id="SupData" class="spark-branches-table spark-suppliers-table table table-hover">
        <thead>
          <tr class="primary">
            <th style="min-width:30px; width: 30px; text-align: center;">
              <input class="checkbox checkth" type="checkbox" name="check"/>
            </th>
            <th><?= lang('code'); ?></th>
            <th><?= lang('company'); ?></th>
            <th><?= lang('name'); ?></th>
            <th><?= lang('co_type'); ?></th>
            <th><?= lang('email_address'); ?></th>
            <th><?= lang('phone'); ?></th>
            <th><?= lang('city'); ?></th>
            <th><?= lang('country'); ?></th>
            <th><?= lang('VAT_Regesteration_Number'); ?></th>
            <th><?= lang('gst_no'); ?></th>
            <th style="min-width:105px; text-align:center;"><?= lang('actions'); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="12" class="dataTables_empty" style="text-align: center; padding: 30px;">
              <i class="fa fa-spinner fa-spin" style="margin-inline-end: 6px;"></i> <?= lang('loading_data_from_server'); ?>
            </td>
          </tr>
        </tbody>
        <tfoot class="dtFilter">
          <tr class="active">
            <th style="min-width:30px; width: 30px; text-align: center;">
              <input class="checkbox checkft" type="checkbox" name="check"/>
            </th>
            <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
            <th style="min-width:105px; text-align:center;"><?= lang('actions'); ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <?php if ($Owner || $GP['bulk_actions']) { ?>
    <div style="display: none;">
      <input type="hidden" name="form_action" value="" id="form_action"/>
      <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
  <?php } ?>

</div>

<script type="text/javascript">
  var oTable;
  $(document).ready(function () {

    function renderSupplierCode(x) {
      return '<span class="spark-branch-code-badge">' + (x ? x : '-') + '</span>';
    }

    oTable = $('#SupData').dataTable({
      "aaSorting": [[1, "asc"]],
      "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "iDisplayLength": <?= $Settings->rows_per_page ?>,
      'bProcessing': true,
      'bServerSide': true,
      'sAjaxSource': '<?= admin_url('suppliers/getSuppliers') ?>',
      'fnServerData': function (sSource, aoData, fnCallback) {
        aoData.push({
          "name": "<?= $this->security->get_csrf_token_name() ?>",
          "value": "<?= $this->security->get_csrf_hash() ?>"
        });
        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
      },
      'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        nRow.id = aData[0];
        nRow.className = "supplier_details_link";
        return nRow;
      },
      "aoColumns": [
        {"bSortable": false, "mRender": checkbox},
        {"mRender": renderSupplierCode},
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        {"bSortable": false}
      ]
    }).dtFilter([
      {column_number: 1, filter_default_label: "[<?=lang('code');?>]", filter_type: "text", data: []},
      {column_number: 2, filter_default_label: "[<?=lang('company');?>]", filter_type: "text", data: []},
      {column_number: 3, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
      {column_number: 4, filter_default_label: "[<?=lang('co_type');?>]", filter_type: "text", data: []},
      {column_number: 5, filter_default_label: "[<?=lang('email_address');?>]", filter_type: "text", data: []},
      {column_number: 6, filter_default_label: "[<?=lang('phone');?>]", filter_type: "text", data: []},
      {column_number: 7, filter_default_label: "[<?=lang('city');?>]", filter_type: "text", data: []},
      {column_number: 8, filter_default_label: "[<?=lang('country');?>]", filter_type: "text", data: []},
      {column_number: 9, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
      {column_number: 10, filter_default_label: "[<?=lang('gst_no');?>]", filter_type: "text", data: []},
    ], "footer");

    <?php if ($action && $action == 'add') { ?>
      $("#add").trigger("click");
    <?php } ?>

  });
</script>
