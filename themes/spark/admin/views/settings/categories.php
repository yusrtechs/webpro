<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#CategoryTable').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('products/getCategories') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, {"bSortable": false, "mRender": img_hl}, null, null, null, null, {"bSortable": false}]
        });
    });
</script>
<div class="spark-branches-page spark-customers-page spark-categories-page">

  <!-- 1. Page Header (Matching Spark Component Pattern) -->
  <div class="spark-branches-header spark-customers-header spark-categories-header">
    <div class="spark-branches-header-start spark-customers-header-start spark-categories-header-start">
      <div class="spark-branches-header-icon spark-customers-header-icon spark-categories-header-icon">
        <i class="fa fa-folder-open"></i>
      </div>
      <div class="spark-branches-header-info spark-customers-header-info spark-categories-header-info">
        <h1 class="spark-branches-title spark-customers-title spark-categories-title">
          <?= lang('categories'); ?>
        </h1>
        <p class="spark-branches-subtitle spark-customers-subtitle spark-categories-subtitle">
          <?= lang('manage_categories_desc'); ?>
        </p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-customers-header-actions spark-categories-header-actions">
      <!-- Add Category Primary Button -->
      <a href="<?= admin_url('products/add_category'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action" id="add">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('add_category'); ?></span>
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
            <a href="<?= admin_url('products/add_category'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-plus-circle"></i> <?= lang('add_category'); ?>
            </a>
          </li>
          <li>
            <a href="<?= admin_url('products/import_categories'); ?>" data-toggle="modal" data-target="#myModal">
              <i class="fa fa-plus-circle"></i> <?= lang('import_categories'); ?>
            </a>
          </li>
          <li>
            <a href="#" id="excel" data-action="export_excel">
              <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
            </a>
          </li>
          <li class="divider"></li>
          <li>
            <a href="#" id="delete" data-action="delete">
              <i class="fa fa-trash-o"></i> <?= lang('delete_categories'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

<?= admin_form_open('products/category_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="CategoryTable" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                            <tr>
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkth" type="checkbox" name="check"/>
                                </th>
                                <th style="min-width:40px; width: 40px; text-align: center;">
                                    <?= lang('image'); ?>
                                </th>
                                <th><?= lang('category_code'); ?></th>
                                <th><?= lang('category_name'); ?></th>
                                <th><?= lang('slug'); ?></th>
                                <th><?= lang('parent_category'); ?></th>
                                <th style="width:100px;"><?= lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="dataTables_empty">
                                    <?= lang('loading_data_from_server') ?>
                                </td>
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
<script language="javascript">
    $(document).ready(function () {

        $('#delete').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $('#excel').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $('#pdf').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

    });
</script>
</div>


