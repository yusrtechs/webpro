<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Query real KPI metrics directly from database
$ci = &get_instance();
$prefix = $ci->db->dbprefix;

// 1. Total Assets Count
$total_assets = $ci->db->where('active', 1)->count_all_results('ast_fixed_assets');

// 2. Total Acquisition Cost
$cost_q = $ci->db->query("SELECT SUM(CASE WHEN grand_total > 0 THEN grand_total ELSE cost * IF(quantity > 0, quantity, 1) END) as total_cost FROM {$prefix}ast_fixed_assets WHERE active = 1");
$total_cost = ($cost_q && $cost_q->num_rows() > 0) ? (float)($cost_q->row()->total_cost ?? 0) : 0;

// 3. Accumulated Depreciation
$depr_q = $ci->db->query("
    SELECT SUM(t.accumulated_value) as total_depreciation
    FROM (
        SELECT d.fixed_asset_id, d.accumulated_value
        FROM {$prefix}ast_depreciations d
        INNER JOIN (
            SELECT fixed_asset_id, MAX(id) as max_id
            FROM {$prefix}ast_depreciations
            GROUP BY fixed_asset_id
        ) m ON d.id = m.max_id
        INNER JOIN {$prefix}ast_fixed_assets a ON a.id = d.fixed_asset_id
        WHERE a.active = 1
    ) t
");
$total_depreciation = ($depr_q && $depr_q->num_rows() > 0) ? (float)($depr_q->row()->total_depreciation ?? 0) : 0;

// 4. Net Book Value
$net_book_value = max(0, $total_cost - $total_depreciation);

$depr_percent = ($total_cost > 0) ? round(($total_depreciation / $total_cost) * 100) : 0;
$net_percent = ($total_cost > 0) ? round(($net_book_value / $total_cost) * 100) : 100;

if ($total_cost >= 1000000) {
    $total_cost_display = number_format($total_cost / 1000000, 1) . 'M';
} elseif ($total_cost >= 100000) {
    $total_cost_display = number_format($total_cost / 1000, 1) . 'K';
} else {
    $total_cost_display = $this->sma->formatMoney($total_cost);
}

if ($total_depreciation >= 1000000) {
    $total_depr_display = number_format($total_depreciation / 1000000, 1) . 'M';
} elseif ($total_depreciation >= 100000) {
    $total_depr_display = number_format($total_depreciation / 1000, 1) . 'K';
} else {
    $total_depr_display = $this->sma->formatMoney($total_depreciation);
}

if ($net_book_value >= 1000000) {
    $net_value_display = number_format($net_book_value / 1000000, 1) . 'M';
} elseif ($net_book_value >= 100000) {
    $net_value_display = number_format($net_book_value / 1000, 1) . 'K';
} else {
    $net_value_display = $this->sma->formatMoney($net_book_value);
}

$v = '';
if ($this->input->post('product')) {
    $v .= '&product=' . $this->input->post('product');
}
if ($this->input->post('category')) {
    $v .= '&category=' . $this->input->post('category');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
?>

<div class="spark-branches-page spark-assets-page">

  <!-- 1. Page Header (React Reference Layout) -->
  <div class="spark-branches-header spark-assets-header">
    <div class="spark-branches-header-start spark-assets-header-start">
      <div class="spark-branches-header-icon spark-assets-header-icon">
        <i class="fa fa-cubes"></i>
      </div>
      <div class="spark-branches-header-info spark-assets-header-info">
        <h1 class="spark-branches-title spark-assets-title"><?= lang('Fixed_assets'); ?></h1>
        <p class="spark-branches-subtitle spark-assets-subtitle"><?= lang('manage_fixed_assets'); ?></p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-assets-header-actions">
      <!-- Add Fixed Asset Button -->
      <a href="<?= admin_url('fixedassets/add_fixed_asset'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('Add_fixed_asset'); ?></span>
      </a>

      <!-- Groups Button -->
      <a href="<?= admin_url('fixedassets/group'); ?>" class="spark-btn-secondary-action">
        <i class="fa fa-folder-open-o"></i>
        <span><?= lang('Fixed_assets_group'); ?></span>
      </a>

      <!-- Depreciation Report Button -->
      <a href="<?= admin_url('fixedassets/depreciation_report'); ?>" class="spark-btn-secondary-action">
        <i class="fa fa-line-chart"></i>
        <span><?= lang('depreciation_report'); ?></span>
      </a>
    </div>
  </div>

  <!-- 2. Statistics / KPI Summary Cards (4 Cards) -->
  <div class="spark-branches-kpi-grid spark-assets-kpi-grid">
    <!-- Card 1: Total Assets (إجمالي الأصول) -->
    <div class="spark-branches-kpi-card spark-assets-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-emerald">+<?= $total_assets; ?></span>
        <div class="spark-branches-kpi-icon icon-emerald">
          <i class="fa fa-cubes"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $total_assets; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('total_assets'); ?></div>
      </div>
    </div>

    <!-- Card 2: Total Acquisition Cost (قيمة الأصول) -->
    <div class="spark-branches-kpi-card spark-assets-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-cyan">100%</span>
        <div class="spark-branches-kpi-icon icon-cyan">
          <i class="fa fa-money"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $total_cost_display; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('assets_value'); ?></div>
      </div>
    </div>

    <!-- Card 3: Accumulated Depreciation (مجمع الإهلاك) -->
    <div class="spark-branches-kpi-card spark-assets-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-amber"><?= $depr_percent; ?>%</span>
        <div class="spark-branches-kpi-icon icon-amber">
          <i class="fa fa-hourglass-half"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $total_depr_display; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('accumulated_depreciation'); ?></div>
      </div>
    </div>

    <!-- Card 4: Net Book Value (صافي قيمة الأصول) -->
    <div class="spark-branches-kpi-card spark-assets-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-purple"><?= $net_percent; ?>%</span>
        <div class="spark-branches-kpi-icon icon-emerald">
          <i class="fa fa-balance-scale"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $net_value_display; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('net_assets_value'); ?></div>
      </div>
    </div>
  </div>

  <!-- 3. Data Table Container -->
  <?= admin_form_open('products/index', 'id="action-form"') ?>
  <div class="spark-branches-table-card spark-assets-table-card">
    <div class="table-responsive" style="border: none;">
      <table id="PRData" class="spark-branches-table spark-assets-table table table-hover">
        <thead>
          <tr>
            <th style="min-width:30px; width: 30px; text-align: center;"><?= lang('Sr#'); ?></th>
            <th style="min-width:30px; width: 30px; text-align: center;">
              <input class="checkbox checkth" type="checkbox" name="check"/>
            </th>
            <th style="min-width:40px; width: 40px; text-align: center;"><?= lang('image'); ?></th>
            <th><?= lang('code'); ?></th>
            <th><?= lang('name'); ?></th>
            <th><?= lang('Fixed_assets_group'); ?></th>
            <th style="text-align: center;"><?= lang('status'); ?></th>
            <th><?= lang('created_by'); ?></th>
            <th style="width:100px; text-align: center;"><?= lang('actions'); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="9" class="dataTables_empty" style="text-align: center; padding: 30px;">
              <i class="fa fa-spinner fa-spin" style="margin-inline-end: 6px;"></i> <?= lang('loading_data_from_server'); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
  </div>
  <?= form_close() ?>

</div>

<script type="text/javascript">
  var oTable;
  $(document).ready(function () {

    function renderAssetCode(x) {
      return '<span class="spark-branch-code-badge">' + (x ? x : '-') + '</span>';
    }

    oTable = $('#PRData').dataTable({
      "aaSorting": [[0, "desc"]],
      "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "iDisplayLength": <?= $Settings->rows_per_page ?>,
      'bProcessing': true,
      'bServerSide': true,
      'sAjaxSource': '<?= admin_url('fixedassets/getFixedAssets/?v=1' . $v) ?>',
      'fnServerData': function (sSource, aoData, fnCallback) {
        aoData.push({
          "name": "<?= $this->security->get_csrf_token_name() ?>",
          "value": "<?= $this->security->get_csrf_hash() ?>"
        });
        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
      },
      'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        nRow.id = aData[0];
        return nRow;
      },
      "aoColumns": [
        {"bVisible": false},
        {"bSortable": false, "mRender": checkbox},
        {"bSortable": false, "mRender": img_hl},
        {"mRender": renderAssetCode},
        null,
        null,
        {"mRender": fixed_asset_status},
        null,
        {"bSortable": false}
      ]
    });

  });
</script>
