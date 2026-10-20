<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {

    function s_status(x) {
	if (x == '0') {
        return '<div class="text-center"><span class="row_status label label-danger">' + lang['inactive'] + '</span></div>';
    } else if (x == '1') {
        return '<div class="text-center"><span class="row_status label label-success">' + lang['active'] + '</span></div>';
    }
	}

    function t_status(x) {
	if (x == '1') {
        return '<div class="text-center">' + lang['menu_item'] + '</div>';
    } else if (x == '2') {
        return '<div class="text-center">' + lang['sub_menu'] + '</div>';
    }
	}

        oTable = $('#ObjectTable').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('modules/getObjects/'.$id) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, {"bSortable": false, "mRender": img_hl}, null,null,null,null,{"mRender": s_status},{"mRender": t_status},null,null,null,{"mRender": fld}, {"bSortable": false}]
        });
    });
</script>
<?= admin_form_open('modules/object_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-th-list"></i><?= lang('Objects'); ?></h2>


        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?php echo admin_url('modules/add_object/'.$id); ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus"></i> <?= lang('Add_object') ?>
                            </a>
                        </li>
                     <!--   <li>
                            <a href="<?php echo admin_url('modules/import_objects'); ?>" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus"></i> <?= lang('Import_objects') ?>
                            </a>
                        </li>-->
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" id="delete" data-action="delete">
                                <i class="fa fa-trash-o"></i> <?= lang('Delete_objects') ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown"><a href="<?php echo admin_url('modules/add_object/'.$id); ?>"
                                        data-toggle="modal" data-target="#myModal"><i class="icon fa fa-plus"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="ObjectTable" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                            <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkth" type="checkbox" name="check"/>
                                </th>
                                <th style="min-width:40px; width: 40px; text-align: center;">
                                    <?= lang('image'); ?>
                                </th>

                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('English_name'); ?></th>
                                <th><?php echo $this->lang->line('description'); ?></th>
                                <th><?php echo $this->lang->line('Directory'); ?></th>
                                <th><?php echo $this->lang->line('Status'); ?></th>

                                <th><?php echo $this->lang->line('type'); ?></th>
                                <th><?php echo $this->lang->line('sort'); ?></th>
                                <th><?php echo $this->lang->line('Rank_sub_menu'); ?></th>
                                <th><?php echo $this->lang->line('created_by'); ?></th>
                                <th><?php echo $this->lang->line('Added_on'); ?></th>

                                <th style="width:100px;"><?= lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="dataTables_empty">
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

