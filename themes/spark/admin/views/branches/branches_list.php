<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Query real KPI metrics directly from database
$ci = &get_instance();
$total_branches = $ci->db->count_all('main_branches');
$active_branches = $total_branches;
$active_percentage = ($total_branches > 0) ? round(($active_branches / $total_branches) * 100) : 100;
$total_staff = $ci->db->where('active', 1)->count_all_results('users');

$sales_q = $ci->db->select_sum('grand_total')->get('sales');
$total_sales_raw = ($sales_q && $sales_q->num_rows() > 0) ? ($sales_q->row()->grand_total ?? 0) : 0;
if ($total_sales_raw >= 1000000) {
    $total_sales_display = number_format($total_sales_raw / 1000000, 1) . 'M';
} elseif ($total_sales_raw >= 1000) {
    $total_sales_display = number_format($total_sales_raw / 1000, 1) . 'K';
} else {
    $total_sales_display = $this->sma->formatMoney($total_sales_raw);
}
?>

<div class="spark-branches-page">

  <!-- 1. Page Header (React Reference Layout) -->
  <div class="spark-branches-header">
    <div class="spark-branches-header-start">
      <div class="spark-branches-header-icon">
        <i class="fa fa-building-o"></i>
      </div>
      <div class="spark-branches-header-info">
        <h1 class="spark-branches-title"><?= lang('Branches_list'); ?></h1>
        <p class="spark-branches-subtitle"><?= lang('manage_branches_locations'); ?></p>
      </div>
    </div>

    <div class="spark-branches-header-actions">
      <!-- Data sharing Button -->
      <a href="<?= admin_url('branches/main_settings'); ?>" class="spark-btn-primary-action">
        <i class="fa fa-cog"></i>
        <span><?= lang('Data_sharing'); ?></span>
      </a>

      <!-- Branch Settings Button -->
      <a href="<?= admin_url('branches/branches_settings'); ?>" class="spark-btn-secondary-action">
        <i class="fa fa-cog"></i>
        <span><?= lang('Branches_settings'); ?></span>
      </a>
    </div>
  </div>

  <!-- 2. Statistics / KPI Summary Cards (4 Cards) -->
  <div class="spark-branches-kpi-grid">
    <!-- Card 1: Total Branches (إجمالي الفروع) -->
    <div class="spark-branches-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-emerald">+<?= $total_branches; ?></span>
        <div class="spark-branches-kpi-icon icon-emerald">
          <i class="fa fa-building-o"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $total_branches; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('total_branches'); ?></div>
      </div>
    </div>

    <!-- Card 2: Active Branches (الفروع النشطة) -->
    <div class="spark-branches-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-emerald"><?= $active_percentage; ?>%</span>
        <div class="spark-branches-kpi-icon icon-green">
          <i class="fa fa-check"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $active_branches; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('active_branches'); ?></div>
      </div>
    </div>

    <!-- Card 3: Total Employees (إجمالي الموظفين) -->
    <div class="spark-branches-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-cyan">+<?= $total_staff; ?></span>
        <div class="spark-branches-kpi-icon icon-cyan">
          <i class="fa fa-users"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $total_staff; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('total_staff'); ?></div>
      </div>
    </div>

    <!-- Card 4: Total Sales (إجمالي المبيعات) -->
    <div class="spark-branches-kpi-card">
      <div class="spark-branches-kpi-top">
        <span class="spark-branches-kpi-badge badge-amber">+18%</span>
        <div class="spark-branches-kpi-icon icon-amber">
          <i class="fa fa-dollar"></i>
        </div>
      </div>
      <div class="spark-branches-kpi-content">
        <div class="spark-branches-kpi-val"><?= $total_sales_display; ?></div>
        <div class="spark-branches-kpi-lbl"><?= lang('total_sales'); ?></div>
      </div>
    </div>
  </div>

  <!-- 3. Data Table Container -->
  <?= admin_form_open('branches/tabel_actions', 'id="action-form"') ?>
  <div class="spark-branches-table-card">
    <div class="table-responsive" style="border: none;">
      <table id="CategoryTable" class="spark-branches-table table table-hover">
        <thead>
          <tr>
            <th style="min-width:30px; width: 30px; text-align: center;">
              <input class="checkbox checkth" type="checkbox" name="check"/>
            </th>
            <th style="min-width:40px; width: 40px; text-align: center;"><?= lang('Logo'); ?></th>
            <th><?= lang('Main_group'); ?></th>
            <th><?= lang('Main_company'); ?></th>
            <th><?= lang('Code_Branch'); ?></th>
            <th><?= lang('prefix'); ?></th>
            <th><?= lang('Branch'); ?></th>
            <th><?= lang('English_name'); ?></th>
            <th><?= lang('phone'); ?></th>
            <th style="width:110px; text-align: center;"><?= lang('actions'); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="10" class="dataTables_empty" style="text-align: center; padding: 30px;">
              <i class="fa fa-spinner fa-spin" style="margin-inline-end: 6px;"></i> <?= lang('loading_data_from_server') ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
  </div>
  <?= form_close() ?>

</div>

<script type="text/javascript">
  var oTable;
  $(document).ready(function () {

    function renderBranchAvatar(x, type, row) {
      var logo = x;
      var name = row[6] || '';
      var nameEn = row[7] || '';
      var code = row[4] || '';

      var avatarHtml = '';
      if (logo && logo !== '' && logo !== 'null' && logo !== 'no_image.jpg') {
        avatarHtml = '<img src="' + site.url + 'assets/uploads/thumbs/' + logo + '" class="spark-branch-avatar" alt="" />';
      } else {
        var colors = ['avatar-orange', 'avatar-teal', 'avatar-emerald', 'avatar-blue'];
        var colorIdx = (name.length + code.length) % colors.length;
        avatarHtml = '<div class="spark-branch-avatar ' + colors[colorIdx] + '"><i class="fa fa-building-o"></i></div>';
      }

      return '<div class="spark-branch-item-cell">' +
               avatarHtml +
               '<div class="spark-branch-name-box">' +
                 '<span class="spark-branch-primary-name">' + name + '</span>' +
                 (nameEn ? '<span class="spark-branch-secondary-name">' + nameEn + '</span>' : '') +
               '</div>' +
             '</div>';
    }

    function renderBranchCode(x) {
      return '<span class="spark-branch-code-badge">' + (x ? x : '-') + '</span>';
    }

    function renderPhone(x) {
      if (x && x !== '') {
        return '<span class="spark-branch-loc-item"><i class="fa fa-phone"></i> <span>' + x + '</span></span>';
      }
      return '<span style="color:#94a3b8;">-</span>';
    }

    oTable = $('#CategoryTable').dataTable({
      "aaSorting": [[4, "asc"]],
      "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "iDisplayLength": <?= $Settings->rows_per_page ?>,
      'bProcessing': true,
      'bServerSide': true,
      'sAjaxSource': '<?= admin_url('branches/getBranchesSettings') ?>',
      'fnServerData': function (sSource, aoData, fnCallback) {
        aoData.push({
          "name": "<?= $this->security->get_csrf_token_name() ?>",
          "value": "<?= $this->security->get_csrf_hash() ?>"
        });
        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
      },
      "aoColumns": [
        {"bSortable": false, "mRender": checkbox},
        {"bSortable": false, "mRender": renderBranchAvatar},
        null,
        null,
        {"mRender": renderBranchCode},
        null,
        null,
        null,
        {"mRender": renderPhone},
        {"bSortable": false}
      ],
      "fnDrawCallback": function () {
        // Style action buttons inside table rows
        $('#CategoryTable tbody tr').each(function () {
          $(this).find('td:last-child a').each(function () {
            var $a = $(this);
            if ($a.hasClass('po-delete') || $a.hasClass('po')) {
              $a.addClass('spark-branch-action-btn btn-delete');
            } else if ($a.find('.fa-edit').length) {
              $a.addClass('spark-branch-action-btn btn-edit');
            } else {
              $a.addClass('spark-branch-action-btn btn-view');
            }
          });
        });
      }
    });

    // Hide intermediate columns by default
    oTable.fnSetColumnVis(2, false);
    oTable.fnSetColumnVis(3, false);
    oTable.fnSetColumnVis(5, false);
    oTable.fnSetColumnVis(6, false);
    oTable.fnSetColumnVis(7, false);

  });
</script>
