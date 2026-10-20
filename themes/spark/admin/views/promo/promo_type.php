<script>
    $(document).ready(function () {


		function promo_type_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
    '<span class="label label-success"><i class="fa fa-check"></i> '+lang['active']+'</span>' :
    '<span class="label label-danger"><i class="fa fa-times"></i> '+lang['inactive']+'</span>';
}

        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[2, "asc"], [3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('promo/getPromo_type') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
              },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
				aData[2]=(iDisplayIndex+1);
				//alert(iDisplayIndex)
                var oSettings = oTable.fnSettings();
				$("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);

                nRow.id = aData[0];
                nRow.className = "product_link";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [{ "bSortable": true},  null,  null, {"mRender": promo_type_status}, {"bSortable": false}]
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('Promo_type');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('English_name');?>]", filter_type: "text", data: []},


            {
                column_number: 3, select_type: 'select2',
                select_type_options: {
                    placeholder: '<?=lang('status');?>',
                    width: '100%',
                    style: 'width:100%;',
                    minimumResultsForSearch: -1,
                    allowClear: true
                },
                data: [{value: '1', label: '<?=lang('active');?>'}, {value: '0', label: '<?=lang('inactive');?>'}]
            }
        ], "footer");
    });
</script>
<style>.table td:nth-child(6) {
        text-align: right;
        width: 10%;
    }

    .table td:nth-child(8) {
        text-align: center;
    }</style>
<?php if ($Owner) {
    echo admin_form_open('promo/promo_type_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('Promos_type'); ?></h2>


    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                            <?php echo lang('Sr#'); ?>
                            </th>
                            <th class="col-xs-8"><?php echo lang('Promo_type'); ?></th>
                            <th class="col-xs-8"><?php echo lang('English_name'); ?></th>

                            <th class="col-xs-2"><?php echo lang('Status'); ?></th>
                            <th class="col-xs-2"><?php echo lang('actions'); ?></th>
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

                            </th>

                            <th style="width:100px;"></th>
                            <th style="width:100px;"></th>
                            <th style="width:50px;"></th>
                            <th style="width:85px;"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>

    <script language="javascript">
        $(document).ready(function () {
            $('#set_admin').click(function () {
                $('#usr-form-btn').trigger('click');
            });

        });
    </script>

<?php } ?>