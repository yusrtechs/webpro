<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {

		function get_lang(x) {
			    v = x.split('__');

			var y=v[0];
			if(v[0]=="cash")
				y="<?= lang('cash') ?>";
			if(v[0]=="CC")
				y="<?= lang('CC') ?>";
			if(v[0]=="Cheque")
				y="<?= lang('cheque') ?>";
			if(v[0]=="bank_transfer")
				y="<?= lang('Bank_transfers') ?>";
			if(v[0]=="gift_card")
				y="<?= lang('gift_card') ?>";

			var z=v[1];

            if(z!='')
				return "<div>"+y+" / "+z+"</div>";
			else
				return "<div>"+y+"</div>";



        }


        oTable = $('#UnitTable').dataTable({
            "aaSorting": [[3, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100,500, -1], [10, 25, 50, 100,500, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('accounts/getPaymentTypes') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, null, null, {"bSortable": false, "mRender": get_lang},  {"bSortable": false}],
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "payment_types_link";
                return nRow;
            },
		});
    });
</script>
<?= admin_form_open('system_settings/unit_actions', 'id="action-form"') ?>

<div class="spark-payment-types-page">
    <div class="spark-payment-types-header">
        <div class="spark-payment-types-header-start">
            <div class="spark-payment-types-header-icon">
                <i class="fa fa-credit-card"></i>
            </div>
            <div class="spark-payment-types-header-info">
                <h1 class="spark-payment-types-title">
                    <?= lang('Payment_types'); ?>
                </h1>
                <p class="spark-payment-types-subtitle">
                    <?= lang('manage_payment_types_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-payment-types-header-actions">
            <a href="<?php echo admin_url('accounts/add_payment_type'); ?>" data-toggle="modal" data-target="#myModal" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('Add_payment_type'); ?></span>
            </a>
        </div>
    </div>

<div class="box">

    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="UnitTable" class="table table-striped jambo_table bulk_action reports-table">
                        <thead>
                            <tr>
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkth" type="checkbox" name="check"/>
                                </th>
                                <th><?= lang('Payment_type_name'); ?></th>
                                <th><?= lang('English_name'); ?></th>
                                <th><?= lang('Account'); ?></th>
                                <th><?= lang('Payment_method'); ?></th>
                                <th style="width:100px;"><?= lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="dataTables_empty">
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

