<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#CURData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('system_settings/getCurrencies') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, null, null, null, null, null, {"bSortable": false}]
        });
    });
</script>
<?= admin_form_open('system_settings/currency_actions', 'id="action-form"') ?>
<div class="spark-currencies-page">
    <div class="spark-currencies-header">
        <div class="spark-currencies-header-start">
            <div class="spark-currencies-header-icon">
                <i class="fa fa-money"></i>
            </div>
            <div class="spark-currencies-header-info">
                <h1 class="spark-currencies-title">
                    <?= $page_title ?>
                </h1>
                <p class="spark-currencies-subtitle">
                    <?= lang('manage_currencies_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-currencies-header-actions">
            <a href="<?= admin_url('system_settings/add_currency'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('add_currency') ?></span>
            </a>

            <button type="button" id="excel" data-action="export_excel" class="spark-btn-secondary-action tip" title="<?= lang('export_to_excel') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('export_to_excel') ?></span>
            </button>
        </div>
    </div>

<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="CURData" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('currency_code'); ?></th>
                            <th><?= lang('currency_name'); ?></th>
                            <th><?= lang('Fraction_name'); ?></th>
                            <th><?= lang('exchange_rate'); ?></th>
                            <th><?= lang('currency_symbol'); ?></th>
                            <th><?= lang('Fraction_symbol'); ?></th>
                            <th style="width:65px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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

