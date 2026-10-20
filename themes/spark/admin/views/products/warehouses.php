<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {

		function img_hlw(x) {
    var image_link = x == null || x == '' ? 'no_image.jpg' : x;
    return (
        '<div class="text-center"><a href="' +
        site.url +
        'assets/uploads/' +
        image_link +
        '" data-toggle="lightbox"><img src="' +
        site.url +
        'assets/uploads/thumbs/' +
        image_link +
        '" alt="" style="width:30px; height:30px;" /></a></div>'
    );
}

        function tax_type(x) {
            return (x == 1) ? "<?=lang('percentage')?>" : "<?=lang('fixed')?>";
        }

        oTable = $('#CURData').dataTable({
            "aaSorting": [[2, "asc"], [3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('products/getWarehouses') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, { "bSortable": false, "mRender": img_hlw }, { "bSortable": false, "mRender": img_hlw }, null, null, null, null, null, null, null, {"bSortable": false}]
        });
    });
</script>

<div class="spark-branches-page spark-customers-page spark-warehouses-page">

  <!-- 1. Page Header (Matching Customers / Branches Component Pattern) -->
  <div class="spark-branches-header spark-customers-header spark-warehouses-header">
    <div class="spark-branches-header-start spark-customers-header-start spark-warehouses-header-start">
      <div class="spark-branches-header-icon spark-customers-header-icon spark-warehouses-header-icon">
        <i class="fa fa-building-o"></i>
      </div>
      <div class="spark-branches-header-info spark-customers-header-info spark-warehouses-header-info">
        <h1 class="spark-branches-title spark-customers-title spark-warehouses-title"><?= lang('warehouses'); ?></h1>
        <p class="spark-branches-subtitle spark-customers-subtitle spark-warehouses-subtitle"><?= lang('manage_warehouses_desc'); ?></p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-customers-header-actions spark-warehouses-header-actions">
      <!-- Actions Dropdown Button -->
      <div class="dropdown" style="display: inline-block;">
        <button class="spark-btn-secondary-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-tasks"></i>
          <span><?= lang('actions'); ?></span>
          <i class="fa fa-angle-down" style="font-size: 11px; margin-inline-start: 4px;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
          <li>
            <a href="#" id="excel" data-action="export_excel">
              <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
            </a>
          </li>
          <li class="divider"></li>
          <li>
            <a href="#" class="bpo" title="<b><?= $this->lang->line('delete_warehouses'); ?></b>"
               data-content="<p><?= lang('r_u_sure'); ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure'); ?></button> <button class='btn bpo-close'><?= lang('no'); ?></button>" data-html="true" data-placement="left">
              <i class="fa fa-trash-o"></i> <?= lang('delete_warehouses'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <?= admin_form_open('products/warehouse_actions', 'id="action-form"') ?>
  <div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="CURData" class="table table-striped jambo_table bulk_action table-condensed reports-table">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th style="min-width:40px; width: 40px; text-align: center;"><?= lang('Map'); ?></th>
                            <th style="min-width:40px; width: 40px; text-align: center;"><?= lang('Logo'); ?></th>
                            <th class="col-xs-1"><?= lang('code'); ?></th>
                            <th class="col-xs-2"><?= lang('name'); ?></th>
                            <th class="col-xs-2"><?= lang('Branch_name'); ?></th>
                            <th class="col-xs-2"><?= lang('VAT_Regesteration_Number'); ?></th>
                            <th class="col-xs-1"><?= lang('price_group'); ?></th>
                            <th class="col-xs-2"><?= lang('phone'); ?></th>
                            <th class="col-xs-2"><?= lang('address'); ?></th>
                            <th style="width:65px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
  </div>

  <div style="display: none;">
      <input type="hidden" name="form_action" value="" id="form_action"/>
      <?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
  </div>
  <?= form_close() ?>

</div>

<script language="javascript">
    $(document).ready(function () {

        $(document).on('click', '#delete', function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $(document).on('click', '#excel', function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $(document).on('click', '#pdf', function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

    });
</script>
