<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Query real KPI metrics directly from database
$ci = &get_instance();
$prefix = $ci->db->dbprefix;

// 1. Total Customers
$q_cus = $ci->db->where('group_name', 'customer')->count_all_results('companies');
$total_customers = (int)$q_cus;

// 2. Active Customers (with at least 1 sale)
$q_act = $ci->db->query("SELECT COUNT(DISTINCT customer_id) as active_customers FROM {$prefix}sales WHERE customer_id > 0");
$active_customers = ($q_act && $q_act->num_rows() > 0) ? (int)($q_act->row()->active_customers ?? 0) : 0;
$active_percentage = ($total_customers > 0) ? round(($active_customers / $total_customers) * 100) : 100;

// 3. Total Sales & Outstanding Due Balance
$q_sal = $ci->db->query("SELECT COUNT(*) as total_sales_count, SUM(grand_total) as total_sales_amount, SUM(paid) as total_paid FROM {$prefix}sales");
$row_sal = ($q_sal && $q_sal->num_rows() > 0) ? $q_sal->row() : null;
$total_sales_count = (int)($row_sal->total_sales_count ?? 0);
$total_sales_raw = (float)($row_sal->total_sales_amount ?? 0);
$total_paid_raw = (float)($row_sal->total_paid ?? 0);
$total_due_raw = max(0, $total_sales_raw - $total_paid_raw);
$due_percentage = ($total_sales_raw > 0) ? round(($total_due_raw / $total_sales_raw) * 100) : 0;

if ($total_sales_raw >= 1000000) {
    $total_sales_display = number_format($total_sales_raw / 1000000, 1) . 'M';
} elseif ($total_sales_raw >= 1000) {
    $total_sales_display = number_format($total_sales_raw / 1000, 1) . 'K';
} else {
    $total_sales_display = $this->sma->formatMoney($total_sales_raw);
}

if ($total_due_raw >= 1000000) {
    $total_due_display = number_format($total_due_raw / 1000000, 1) . 'M';
} elseif ($total_due_raw >= 1000) {
    $total_due_display = number_format($total_due_raw / 1000, 1) . 'K';
} else {
    $total_due_display = $this->sma->formatMoney($total_due_raw);
}
?>

<div class="spark-branches-page spark-customers-page">

  <!-- 1. Page Header (React Reference Layout) -->
  <div class="spark-branches-header spark-customers-header">
    <div class="spark-branches-header-start spark-customers-header-start">
      <div class="spark-branches-header-icon spark-customers-header-icon">
        <i class="fa fa-users"></i>
      </div>
      <div class="spark-branches-header-info spark-customers-header-info">
        <h1 class="spark-branches-title spark-customers-title"><?= lang('customers'); ?></h1>
        <p class="spark-branches-subtitle spark-customers-subtitle"><?= lang('manage_customers_desc'); ?></p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-customers-header-actions">
      <!-- Add Customer Primary Button -->
      <a href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action" id="add">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('add_customer'); ?></span>
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
            <a href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-plus-circle"></i> <?= lang('add_customer'); ?>
            </a>
          </li>
          <li>
            <a href="<?= admin_url('customers/import_csv'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-file-text-o"></i> <?= lang('import_by_csv'); ?>
            </a>
          </li>
          <li>
            <a href="<?= admin_url('customers/import_excel'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-file-excel-o"></i> <?= lang('import_by_excel'); ?>
            </a>
          </li>
          <?php if ($Owner) { ?>
            <li>
              <a href="#" id="excel" data-action="export_excel">
                <i class="fa fa-download"></i> <?= lang('export_to_excel'); ?>
              </a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="#" class="bpo" title="<b><?= $this->lang->line('delete_customers'); ?></b>"
                 data-content="<p><?= lang('r_u_sure'); ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure'); ?></button> <button class='btn bpo-close'><?= lang('no'); ?></button>" data-html="true" data-placement="left">
                <i class="fa fa-trash-o"></i> <?= lang('delete_customers'); ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>

  <!-- 2. Statistics / KPI Summary Cards (4 Cards) -->
  <div class="spark-branches-kpi-grid spark-customers-kpi-grid">
    <!-- Card 1: Total Customers (إجمالي العملاء) -->
    <div class="spark-branches-kpi-card spark-customers-kpi-card">
      <div class="spark-branches-kpi-top spark-customers-kpi-top">
        <span class="spark-branches-kpi-badge spark-customers-kpi-badge badge-emerald">+<?= $total_customers; ?></span>
        <div class="spark-branches-kpi-icon spark-customers-kpi-icon icon-emerald">
          <i class="fa fa-users"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-customers-kpi-content">
        <div class="spark-branches-kpi-val spark-customers-kpi-val"><?= $total_customers; ?></div>
        <div class="spark-branches-kpi-lbl spark-customers-kpi-lbl"><?= lang('total_customers'); ?></div>
      </div>
    </div>

    <!-- Card 2: Active Customers (العملاء النشطون) -->
    <div class="spark-branches-kpi-card spark-customers-kpi-card">
      <div class="spark-branches-kpi-top spark-customers-kpi-top">
        <span class="spark-branches-kpi-badge spark-customers-kpi-badge badge-cyan"><?= $active_percentage; ?>%</span>
        <div class="spark-branches-kpi-icon spark-customers-kpi-icon icon-cyan">
          <i class="fa fa-user-plus"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-customers-kpi-content">
        <div class="spark-branches-kpi-val spark-customers-kpi-val"><?= $active_customers; ?></div>
        <div class="spark-branches-kpi-lbl spark-customers-kpi-lbl"><?= lang('active_customers'); ?></div>
      </div>
    </div>

    <!-- Card 3: Total Sales (إجمالي المبيعات) -->
    <div class="spark-branches-kpi-card spark-customers-kpi-card">
      <div class="spark-branches-kpi-top spark-customers-kpi-top">
        <span class="spark-branches-kpi-badge spark-customers-kpi-badge badge-amber"><?= number_format($total_sales_count); ?></span>
        <div class="spark-branches-kpi-icon spark-customers-kpi-icon icon-amber">
          <i class="fa fa-dollar"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-customers-kpi-content">
        <div class="spark-branches-kpi-val spark-customers-kpi-val"><?= $total_sales_display; ?></div>
        <div class="spark-branches-kpi-lbl spark-customers-kpi-lbl"><?= lang('total_sales'); ?></div>
      </div>
    </div>

    <!-- Card 4: Outstanding Balance (المستحقات المتبقية) -->
    <div class="spark-branches-kpi-card spark-customers-kpi-card">
      <div class="spark-branches-kpi-top spark-customers-kpi-top">
        <span class="spark-branches-kpi-badge spark-customers-kpi-badge badge-purple"><?= $due_percentage; ?>%</span>
        <div class="spark-branches-kpi-icon spark-customers-kpi-icon icon-purple">
          <i class="fa fa-clock-o"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content spark-customers-kpi-content">
        <div class="spark-branches-kpi-val spark-customers-kpi-val"><?= $total_due_display; ?></div>
        <div class="spark-branches-kpi-lbl spark-customers-kpi-lbl"><?= lang('total_due_balance'); ?></div>
      </div>
    </div>
  </div>

  <!-- 3. Customers Table Card -->
  <?php if ($Owner || $GP['bulk_actions']) {
      echo admin_form_open('customers/customer_actions', 'id="action-form"') ;
  } ?>
  <div class="spark-branches-table-card spark-customers-table-card">
    <div class="table-responsive" style="border: none;">
      <table id="CusData" class="spark-branches-table spark-customers-table table table-hover">
        <thead>
          <tr class="primary">
            <th style="min-width:30px; width: 30px; text-align: center;">
              <input class="checkbox checkth" type="checkbox" name="check"/>
            </th>
            <th><?= lang('code'); ?></th>
            <th><?= lang('customer'); ?></th>
            <th><?= lang('co_type'); ?></th>
            <th><?= lang('email_address'); ?></th>
            <th><?= lang('phone'); ?></th>
            <th><?= lang('price_group'); ?></th>
            <th><?= lang('customer_group'); ?></th>
            <th><?= lang('VAT_Regesteration_Number'); ?></th>
            <th><?= lang('deposit'); ?></th>
            <th><?= lang('award_points'); ?></th>
            <th style="min-width:135px !important; text-align:center;"><?= lang('actions'); ?></th>
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
            <th style="min-width:135px !important;" class="text-center"><?= lang('actions'); ?></th>
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
  var cTable;
  $(document).ready(function () {

    function renderCustomerCode(x) {
      return '<span class="spark-branch-code-badge">' + (x ? x : '-') + '</span>';
    }

    cTable = $('#CusData').dataTable({
      "aaSorting": [[1, "asc"]],
      "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "iDisplayLength": <?= $Settings->rows_per_page ?>,
      'bProcessing': true,
      'bServerSide': true,
      'sAjaxSource': '<?= admin_url('customers/getCustomers') ?>',
      'fnServerData': function (sSource, aoData, fnCallback) {
        aoData.push({
          "name": "<?= $this->security->get_csrf_token_name() ?>",
          "value": "<?= $this->security->get_csrf_hash() ?>"
        });
        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
      },
      'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        nRow.id = aData[0];
        nRow.className = "customer_details_link";
        return nRow;
      },
      "aoColumns": [
        {"bSortable": false, "mRender": checkbox},
        {"mRender": renderCustomerCode},
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        {"mRender": currencyFormat},
        null,
        {"bSortable": false}
      ]
    }).dtFilter([
      {column_number: 1, filter_default_label: "[<?=lang('code');?>]", filter_type: "text", data: []},
      {column_number: 2, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
      {column_number: 3, filter_default_label: "[<?=lang('co_type');?>]", filter_type: "text", data: []},
      {column_number: 4, filter_default_label: "[<?=lang('email_address');?>]", filter_type: "text", data: []},
      {column_number: 5, filter_default_label: "[<?=lang('phone');?>]", filter_type: "text", data: []},
      {column_number: 6, filter_default_label: "[<?=lang('price_group');?>]", filter_type: "text", data: []},
      {column_number: 7, filter_default_label: "[<?=lang('customer_group');?>]", filter_type: "text", data: []},
      {column_number: 8, filter_default_label: "[<?=lang('VAT_Regesteration_Number');?>]", filter_type: "text", data: []},
      {column_number: 9, filter_default_label: "[<?=lang('deposit');?>]", filter_type: "text", data: []},
      {column_number: 10, filter_default_label: "[<?=lang('award_points');?>]", filter_type: "text", data: []},
    ], "footer");

    $('#myModal').on('hidden.bs.modal', function () {
      cTable.fnDraw(false);
    });

    <?php if ($action && $action == 'add') { ?>
      $("#add").trigger("click");
    <?php } ?>

  });
</script>
