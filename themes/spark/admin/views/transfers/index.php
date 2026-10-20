<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#TOData').dataTable({
            "aaSorting": [[1, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('transfers/getTransfers') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false,"mRender": checkbox}, {"mRender": fld}, null, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": transfer_status}, {"bSortable": false,"mRender": attachment}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "transfer_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var row_total = 0, tax = 0, gtotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    row_total += parseFloat(aaData[aiDisplay[i]][5]);
                    tax += parseFloat(aaData[aiDisplay[i]][6]);
                    gtotal += parseFloat(aaData[aiDisplay[i]][7]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[5].innerHTML = currencyFormat(formatMoney(row_total));
                nCells[6].innerHTML = currencyFormat(formatMoney(tax));
                nCells[7].innerHTML = currencyFormat(formatMoney(gtotal));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('ref_no');?>]", filter_type: "text", data: []},
            {
                column_number: 3,
                filter_default_label: "[<?=lang('warehouse') . ' (' . lang('from') . ')';?>]",
                filter_type: "text", data: []
            },
            {
                column_number: 4,
                filter_default_label: "[<?=lang('warehouse') . ' (' . lang('to') . ')';?>]",
                filter_type: "text", data: []
            },
            {column_number: 8, filter_default_label: "[<?=lang('Status');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<div class="spark-branches-page spark-customers-page spark-transfers-page">

  <!-- 1. Page Header (Matching Spark Component Pattern) -->
  <div class="spark-branches-header spark-customers-header spark-transfers-header">
    <div class="spark-branches-header-start spark-customers-header-start spark-transfers-header-start">
      <div class="spark-branches-header-icon spark-customers-header-icon spark-transfers-header-icon">
        <i class="fa fa-exchange"></i>
      </div>
      <div class="spark-branches-header-info spark-customers-header-info spark-transfers-header-info">
        <h1 class="spark-branches-title spark-customers-title spark-transfers-title">
          <?= lang('transfers'); ?>
        </h1>
        <p class="spark-branches-subtitle spark-customers-subtitle spark-transfers-subtitle">
          <?= lang('manage_transfers_desc'); ?>
        </p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-customers-header-actions spark-transfers-header-actions">
      <!-- Add Transfer Primary Button -->
      <a href="<?= admin_url('products/transfers_add'); ?>" class="spark-btn-primary-action" id="add">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('add_transfer'); ?></span>
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
            <a href="<?= admin_url('products/transfers_add'); ?>">
              <i class="fa fa-plus-circle"></i> <?= lang('add_transfer'); ?>
            </a>
          </li>
          <li>
            <a href="#" id="excel" data-action="export_excel">
              <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
            </a>
          </li>
          <li>
            <a href="#" id="combine" data-action="combine">
              <i class="fa fa-file-pdf-o"></i> <?= lang('combine_to_pdf'); ?>
            </a>
          </li>
          <li class="divider"></li>
          <li>
            <a href="#" class="bpo" title="<b><?= $this->lang->line('delete_transfers'); ?></b>"
               data-content="<p><?= lang('r_u_sure'); ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure'); ?></button> <button class='btn bpo-close'><?= lang('no'); ?></button>"
               data-html="true" data-placement="left">
              <i class="fa fa-trash-o"></i> <?= lang('delete_transfers'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('transfers/transfer_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="TOData" cellpadding="0" cellspacing="0" border="0"
                           class="table  table-condensed table-hover table-striped">
                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('date'); ?></th>
                            <th><?= lang('ref_no'); ?></th>
                            <th><?= lang('warehouse') . ' (' . lang('from') . ')'; ?></th>
                            <th><?= lang('warehouse') . ' (' . lang('to') . ')'; ?></th>
                            <th><?= lang('total'); ?></th>
                            <th><?= lang('product_tax'); ?></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('Status'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:100px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:100px; text-align: center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner || $GP['bulk_actions']) {
    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php
} ?>
</div>