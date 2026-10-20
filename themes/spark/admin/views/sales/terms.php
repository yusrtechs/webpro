<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {

        function tc_status(x) {
	        if (x == '0') {
                return '<div class="text-center"><span class="row_status label label-danger">' + lang['inactive'] + '</span></div>';
            } else if (x == '1') {
                return '<div class="text-center"><span class="row_status label label-success">' + lang['active'] + '</span></div>';
            }
	    }

        oTable = $('#CGData').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getTermsConditions') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bVisible": false}, null,null,{"mRender": tc_status}, {"bSortable": false}]
        });
    });
</script>
<?= admin_form_open('sales/terms_actions', 'id="action-form"') ?>

<div class="spark-terms-page">
    <div class="spark-terms-header">
        <div class="spark-terms-header-start">
            <div class="spark-terms-header-icon">
                <i class="fa fa-file-text"></i>
            </div>
            <div class="spark-terms-header-info">
                <h1 class="spark-terms-title">
                    <?= lang('Terms_and_conditions'); ?>
                </h1>
                <p class="spark-terms-subtitle">
                    <?= lang('manage_terms_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-terms-header-actions">
            <a href="<?php echo admin_url('sales/add_terms_conditions'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('Add_terms_and_conditions'); ?></span>
            </a>

        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo $this->lang->line('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="CGData" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?php echo $this->lang->line('Name'); ?></th>
                            <th style="min-width:200px;"><?php echo $this->lang->line('description'); ?></th>
                            <th><?php echo $this->lang->line('Status'); ?></th>
                            <th style="max-width:85px;"><?php echo $this->lang->line('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>

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

