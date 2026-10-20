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

        oTable = $('#CategoryTable').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('fixedassets/getRevaluation') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, {"mRender": fld}, null, null, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"bSortable": false}]
        });
    });
</script>
<?= admin_form_open('fixedassets/tabel_actions', 'id="action-form"') ?>
<div class="spark-fixedassets-revaluation-page">
    <div class="spark-fixedassets-revaluation-header">
        <div class="spark-fixedassets-revaluation-header-start">
            <div class="spark-fixedassets-revaluation-header-icon">
                <i class="fa fa-line-chart"></i>
            </div>
            <div class="spark-fixedassets-revaluation-header-info">
                <h1 class="spark-fixedassets-revaluation-title">
                    <?= lang('Revaluation_of_fixed_asset'); ?>
                </h1>
                <p class="spark-fixedassets-revaluation-subtitle">
                    <?= lang('manage_fixedassets_revaluation_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-fixedassets-revaluation-header-actions">
            <a href="<?= admin_url('fixedassets/add_revaluation'); ?>" data-toggle="modal" data-target="#myModal" id="add" class="spark-btn-primary-action">
                <i class="fa fa-plus-circle"></i>
                <span><?= lang('Add_revaluation'); ?></span>
            </a>
        </div>
    </div>

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


                                <th><?= lang('reference_no'); ?></th>
                                <th><?= lang('date'); ?></th>
                                <th><?= lang('Fixed_asset'); ?></th>
                                <th><?= lang('Fixed_asset_account'); ?></th>
                                <th><?= lang('The_book_value_of_the_asset'); ?></th>
                                <th><?= lang('Revaluation_value'); ?></th>

                                <th style="width:100px;"><?= lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="dataTables_empty">
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

