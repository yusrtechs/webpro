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

    function get_lang(x) {
			var y=x;
			if(x=="percent")
				y="<?= lang('Percentage_discount') ?>";
			if(x=="fixed")
				y="<?= lang('Fixed_discount') ?>";


            return y; // onclick=\"this.select();\"
        }

    oTable=$('#GCData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('sales/getCoupons') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            }, null, null, null, null, {"mRender": get_lang}, {"mRender": decimalFormat}, null, null,  {"mRender": s_status}, {"mRender": fsd}, {"bSortable": false}]
        });
        oTable.fnSetColumnVis( 1, false );
    });
</script>
<?= admin_form_open('sales/coupons_actions', 'id="action-form"') ?>

<div class="spark-coupons-page">
    <div class="spark-coupons-header">
        <div class="spark-coupons-header-start">
            <div class="spark-coupons-header-icon">
                <i class="fa fa-ticket"></i>
            </div>
            <div class="spark-coupons-header-info">
                <h1 class="spark-coupons-title">
                    <?= lang('Coupons'); ?>
                </h1>
                <p class="spark-coupons-subtitle">
                    <?= lang('manage_coupons_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-coupons-header-actions">
            <a href="<?= admin_url('sales/add_coupon'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('add_coupon'); ?></span>
            </a>

            <div class="dropdown">
                <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                    <span><?= lang('actions'); ?></span>
                    <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
                    <li>
                        <a href="#" id="excel" data-action="export_excel">
                            <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo $this->lang->line('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="GCData" class="table table-striped jambo_table bulk_action">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th><?php echo $this->lang->line('Number_of_coupons'); ?></th>
                            <th><?php echo $this->lang->line('Coupon_codes'); ?></th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th><?php echo $this->lang->line('Discount_type'); ?></th>
                            <th><?php echo $this->lang->line('Coupon_value'); ?></th>
                            <th><?php echo $this->lang->line('created_by'); ?></th>
                            <th><?php echo $this->lang->line('customer'); ?></th>
                            <th><?php echo $this->lang->line('Status'); ?></th>
                            <th><?php echo $this->lang->line('Coupon_expiry_date'); ?></th>
                            <th style="width:65px;"><?php echo $this->lang->line('actions'); ?></th>
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
