<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<style>
     .quantity_adjustment_link {
    cursor: pointer;
}
</style>
<script>
    $('body').on('click', '.quantity_adjustment_link td:not(:first-child, :nth-last-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'products/view_adjust/' + $(this).parent('.quantity_adjustment_link').attr('id'),
        });
        $('#myModal').modal('show');
    });
    $(document).ready(function () {
        oTable = $('#dmpData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('products/getadjusts/' . ($warehouse ? $warehouse->id : '')); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, {"mRender": fld}, null, null, null, {"mRender": decode_html}, {"bSortable": false,"mRender": attachment}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "quantity_adjustment_link";
                return nRow;
            },
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('warehouse');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('created_by');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang(' note');?>]", filter_type: "text", data: []},
        ], "footer");

        if (localStorage.getItem('remove_qals')) {
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('qawarehouse')) {
                localStorage.removeItem('qawarehouse');
            }
            if (localStorage.getItem('qanote')) {
                localStorage.removeItem('qanote');
            }
            if (localStorage.getItem('qadate')) {
                localStorage.removeItem('qadate');
            }
            localStorage.removeItem('remove_qals');
        }

        <?php if ($this->session->userdata('remove_qals')) {
            ?>
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('qawarehouse')) {
                localStorage.removeItem('qawarehouse');
            }
            if (localStorage.getItem('qanote')) {
                localStorage.removeItem('qanote');
            }
            if (localStorage.getItem('qadate')) {
                localStorage.removeItem('qadate');
            }
            <?php $this->sma->unset_data('remove_qals');
        }
        ?>
    });
</script>

<div class="spark-branches-page spark-customers-page spark-quantity-adjust-page">

  <!-- 1. Page Header (Matching Spark Component Pattern) -->
  <div class="spark-branches-header spark-customers-header spark-quantity-adjust-header">
    <div class="spark-branches-header-start spark-customers-header-start spark-quantity-adjust-header-start">
      <div class="spark-branches-header-icon spark-customers-header-icon spark-quantity-adjust-header-icon">
        <i class="fa fa-sliders"></i>
      </div>
      <div class="spark-branches-header-info spark-customers-header-info spark-quantity-adjust-header-info">
        <h1 class="spark-branches-title spark-customers-title spark-quantity-adjust-title">
          <?= lang('Quantity_adjustments') . ($warehouse ? ' (' . $warehouse->name . ')' : ''); ?>
        </h1>
        <p class="spark-branches-subtitle spark-customers-subtitle spark-quantity-adjust-subtitle">
          <?= lang('manage_quantity_adjust_desc'); ?>
        </p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-customers-header-actions spark-quantity-adjust-header-actions">
      <?php if (!empty($warehouses)) { ?>
      <!-- Warehouse Selector Dropdown -->
      <div class="dropdown" style="display: inline-block;">
        <button class="spark-btn-secondary-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-building-o"></i>
          <span><?= $warehouse ? $warehouse->name : lang('all_warehouses'); ?></span>
          <i class="fa fa-angle-down" style="font-size: 11px; margin-inline-start: 4px;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
          <li><a href="<?= admin_url('products/quantity_adjust') ?>"><i class="fa fa-building-o"></i> <?= lang('all_warehouses') ?></a></li>
          <li class="divider"></li>
          <?php foreach ($warehouses as $wh) {
            echo '<li><a href="' . admin_url('products/quantity_adjust/' . $wh->id) . '"><i class="fa fa-building"></i> ' . $wh->name . '</a></li>';
          } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Add Adjustment Primary Button -->
      <a href="<?= admin_url('products/add_adjust'); ?>" class="spark-btn-primary-action" id="add">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('add_adjustment_count'); ?></span>
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
            <a href="<?= admin_url('products/add_adjust'); ?>">
              <i class="fa fa-plus-circle"></i> <?= lang('add_adjustment_count'); ?>
            </a>
          </li>
          <li>
            <a href="<?= admin_url('products/add_adjust_by_excel'); ?>">
              <i class="fa fa-plus-circle"></i> <?= lang('Add_adjustment_by_excel'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

<?php if ($Owner || ($GP && $GP['bulk_actions'])) {
            echo admin_form_open('products/adjustment_actions', 'id="action-form"');
}
?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="dmpData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th class="col-xs-2"><?= lang('date'); ?></th>
                            <th class="col-xs-2"><?= lang('reference_no'); ?></th>
                            <th class="col-xs-2"><?= lang('warehouse'); ?></th>
                            <th class="col-xs-2"><?= lang('created_by'); ?></th>
                            <th><?= lang('note'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="min-width:75px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:75px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($Owner || ($GP && $GP['bulk_actions'])) {
    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?=form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
    </div>
    <?=form_close()?>
    <?php
}
?>
</div>

