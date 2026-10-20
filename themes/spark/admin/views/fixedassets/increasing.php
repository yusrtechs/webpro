<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {

    function lang_increasing(x) {
    if(x==1){
        return '<?= lang("Maintenance") ?>';
    }else if(x==2){
        return '<?= lang("Repair") ?>';
    }else if(x==3){
        return '<?= lang("Upgraded") ?>';
    }else if(x==4){
        return '<?= lang("Purchase_spare_parts") ?>';
    }else{
        return x;
    }
}

function lang_increasing_effect(x) {
    if(x==1){
        return '<?= lang("cost") ?>';
    }else if(x==2){
        return '<?= lang("Useful_life") ?>';
    }else{
        return x;
    }
}

        oTable = $('#CategoryTable').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('fixedassets/getIncreasing') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, {"mRender": fldo}, {"mRender": fldo}, null, null, {"mRender": lang_increasing}, {"mRender": lang_increasing_effect}, {"mRender": currencyFormat},null, {"mRender": pay_status}, {"bSortable": false}]
        });
    });
</script>
<?= admin_form_open('fixedassets/tabel_actions', 'id="action-form"') ?>
<div class="spark-fixedassets-increasing-page">
    <div class="spark-fixedassets-increasing-header">
        <div class="spark-fixedassets-increasing-header-start">
            <div class="spark-fixedassets-increasing-header-icon">
                <i class="fa fa-level-up"></i>
            </div>
            <div class="spark-fixedassets-increasing-header-info">
                <h1 class="spark-fixedassets-increasing-title">
                    <?= lang('Increasing_the_fixed_asset'); ?>
                </h1>
                <p class="spark-fixedassets-increasing-subtitle">
                    <?= lang('manage_fixedassets_increasing_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-fixedassets-increasing-header-actions">
            <a href="<?= admin_url('fixedassets/add_increasing'); ?>" data-toggle="modal" data-target="#myModal" id="add" class="spark-btn-primary-action">
                <i class="fa fa-plus-circle"></i>
                <span><?= lang('Add_increasing'); ?></span>
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
                                <th><?= lang('Start_date'); ?></th>
                                <th><?= lang('End_date'); ?></th>
                                <th><?= lang('Fixed_asset'); ?></th>
                                <th style="min-width:100px;"><?= lang('supplier'); ?></th>
                                <th><?= lang('Type_of_fixed_asset_increase'); ?></th>
                                <th><?= lang('The_effect_of_increasing_on'); ?></th>
                                <th><?= lang('cost'); ?></th>
                                <th><?= lang('Useful_life'); ?></th>
                                <th class="col-xs-1"><?= lang('payment_status'); ?></th>

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

