<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#CURData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('products/getModifiers') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, {"bSortable": false, "mRender": img_hl}, null, {"mRender": decimalFormat}, {"bSortable": false}]
        });
    });
</script>

<div class="spark-branches-page spark-customers-page spark-modifiers-page">

  <!-- 1. Page Header (Matching Spark Component Pattern) -->
  <div class="spark-branches-header spark-customers-header spark-modifiers-header">
    <div class="spark-branches-header-start spark-customers-header-start spark-modifiers-header-start">
      <div class="spark-branches-header-icon spark-customers-header-icon spark-modifiers-header-icon">
        <i class="fa fa-sliders"></i>
      </div>
      <div class="spark-branches-header-info spark-customers-header-info spark-modifiers-header-info">
        <h1 class="spark-branches-title spark-customers-title spark-modifiers-title">
          <?= lang('Modifiers'); ?>
        </h1>
        <p class="spark-branches-subtitle spark-customers-subtitle spark-modifiers-subtitle">
          <?= lang('manage_modifiers_desc'); ?>
        </p>
      </div>
    </div>

    <div class="spark-branches-header-actions spark-customers-header-actions spark-modifiers-header-actions">
      <!-- Add Modifier Primary Button -->
      <a href="<?= admin_url('products/add_modifier'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action" id="add">
        <i class="fa fa-plus-circle"></i>
        <span><?= lang('Add_modifier'); ?></span>
      </a>
    </div>
  </div>

<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo $this->lang->line('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="CURData" class="table  table-hover table-striped reports-table">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th style="min-width:40px; width: 40px; text-align: center;">
                                    <?= lang('image'); ?>
                            </th>
                            <th style="min-width:250px;"><?php echo $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('price_addition'); ?></th>
                            <th style="width:65px;"><?php echo $this->lang->line('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
</div>

