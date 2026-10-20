<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#GPData').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            "oTableTools": {
                "sSwfPath": "assets/media/swf/copy_csv_xls_pdf.swf",
                "aButtons": ["csv", {"sExtends": "pdf", "sPdfOrientation": "landscape", "sPdfMessage": ""}, "print"]
            },
            "aoColumns": [{"bSortable": false}, null, null, null, {"bSortable": false}
            ]

        });
    });
</script>

<div class="spark-user-groups-page">
    <div class="spark-user-groups-header">
        <div class="spark-user-groups-header-start">
            <div class="spark-user-groups-header-icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="spark-user-groups-header-info">
                <h1 class="spark-user-groups-title">
                    <?= lang('groups'); ?>
                </h1>
                <p class="spark-user-groups-subtitle">
                    <?= lang('manage_user_groups_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-user-groups-header-actions">
            <a href="<?= admin_url('system_settings/create_group'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('add_group') ?></span>
            </a>

            <button type="button" id="excelProducts" data-action="export_excel" class="spark-btn-secondary-action tip" title="<?= lang('export_to_excel') ?>">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('export_to_excel') ?></span>
            </button>

            <button type="button" id="deleteGroups" data-action="delete" class="spark-btn-secondary-action tip" title="<?= lang('delete_groups') ?>">
                <i class="fa fa-trash-o"></i>
                <span><?= lang('delete_groups') ?></span>
            </button>
        </div>
    </div>

<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo $this->lang->line('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="GPData" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?php echo $this->lang->line('group_id'); ?></th>
                            <th><?php echo $this->lang->line('group_name'); ?></th>
                            <th><?php echo $this->lang->line('group_description'); ?></th>
                            <th style="width:45px;"><?php echo $this->lang->line('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($groups as $group) {
                            ?>
                            <tr>
                                <td>
                                    <div class="text-center"><input class="checkbox multi-select" type="checkbox" name="val[]"
                                                   value="<?= $group->id ?>"/></div>
                                </td>
                                <td><?php echo $group->id; ?></td>
                                <td><?php echo $group->name; ?></td>
                                <td><?php echo $group->description; ?></td>
                                <td style="text-align:center;">
                                    <?php echo '<a class="tip" title="' . $this->lang->line('change_permissions') . '" href="' . admin_url('system_settings/permissions/' . $group->id) . '"><i class="fa fa-tasks"></i></a> <a class="tip" title="' . $this->lang->line('edit_group') . '" data-toggle="modal" data-target="#myModal" href="' . admin_url('system_settings/edit_group/' . $group->id) . '"><i class="fa fa-edit"></i></a> <a href="#" class="tip po" title="' . $this->lang->line('delete_group') . '" data-content="<p>' . lang('r_u_sure') . '</p><a class=\'btn btn-danger\' href=\'' . admin_url('system_settings/delete_group/' . $group->id) . '\'>' . lang('i_m_sure') . '</a> <button class=\'btn po-close\'>' . lang('no') . '</button>"><i class="fa fa-trash-o"></i></a>'; ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
