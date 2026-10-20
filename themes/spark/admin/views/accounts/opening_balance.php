<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>


<?php

$v = "";
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */
  if ($this->input->post('grpId')) {
    $v .= '&grpId=' . $this->input->post('grpId');
}
if ($this->input->post('coId')) {
    $v .= '&coId=' . $this->input->post('coId');
}
if ($this->input->post('brId')) {
    $v .= '&brId=' . $this->input->post('brId');
}
?>


<script>
    $(document).ready(function () {

        <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		localStorage.setItem('glgrpId', '<?=  $this->session->userdata('grpId') ?>');
				<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

		localStorage.setItem('glcoId', '<?=  $this->session->userdata('coId') ?>');
				<?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

localStorage.setItem('glgrpId', '<?=  $this->session->userdata('grpId') ?>');
localStorage.setItem('glcoId', '<?=  $this->session->userdata('coId') ?>');
localStorage.setItem('glbrId', '<?=  $this->session->userdata('brId') ?>');
				<?php
						}
                    }
						?>

        oTable = $('#dmpData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100,500, -1], [10, 25, 50, 100,500, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('accounts/getopeningbalance/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false, "mRender": checkbox}, {"mRender": fld}, null, {"mRender": decode_html}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "opening_balance_link";
                return nRow;
            },
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('created_by');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang(' note');?>]", filter_type: "text", data: []},

        ], "footer");

        if (localStorage.getItem('remove_obls')) {
            if (localStorage.getItem('obitems')) {
                localStorage.removeItem('obitems');
            }
            if (localStorage.getItem('obref')) {
                localStorage.removeItem('obref');
            }
            if (localStorage.getItem('obwarehouse')) {
                localStorage.removeItem('obwarehouse');
            }
            if (localStorage.getItem('obnote')) {
                localStorage.removeItem('obnote');
            }
            if (localStorage.getItem('obdate')) {
                localStorage.removeItem('obdate');
            }
            if (localStorage.getItem('obbrId')) {
                localStorage.removeItem('obbrId');
            }
            localStorage.removeItem('remove_obls');
        }

        <?php if ($this->session->userdata('remove_obls')) {
    ?>
            if (localStorage.getItem('obitems')) {
                localStorage.removeItem('obitems');
            }
            if (localStorage.getItem('obref')) {
                localStorage.removeItem('obref');
            }
            if (localStorage.getItem('obwarehouse')) {
                localStorage.removeItem('obwarehouse');
            }
            if (localStorage.getItem('obnote')) {
                localStorage.removeItem('obnote');
            }
            if (localStorage.getItem('obdate')) {
                localStorage.removeItem('obdate');
            }
            if (localStorage.getItem('obbrId')) {
                localStorage.removeItem('obbrId');
            }
        <?php $this->sma->unset_data('remove_obls');
}
        ?>
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('grpId') && !$this->input->post('coId') && !$this->input->post('brId')) { ?>
            $('#form').hide();
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideToggle(200);
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp(200);
            return false;
        });
    });
</script>

<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('products/adjustment_actions', 'id="action-form"');
} ?>

<div class="spark-opening-balance-page">
    <div class="spark-opening-balance-header">
        <div class="spark-opening-balance-header-start">
            <div class="spark-opening-balance-header-icon">
                <i class="fa fa-balance-scale"></i>
            </div>
            <div class="spark-opening-balance-header-info">
                <h1 class="spark-opening-balance-title">
                    <?= lang('Opening_balance'); ?>
                </h1>
                <p class="spark-opening-balance-subtitle">
                    <?= lang('manage_opening_balance_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-opening-balance-header-actions">
            <?php if (isset($this->Settings->isGrouping) && $this->Settings->isGrouping == 1) { ?>
                <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                    <i class="fa fa-filter"></i>
                    <span><?= lang('filter'); ?></span>
                </button>
            <?php } ?>

            <a href="<?= admin_url('accounts/add_opening_balance'); ?>" class="spark-btn-primary-action">
                <i class="fa fa-plus"></i>
                <span><?= lang('Add_opening_balance'); ?></span>
            </a>

            <div class="dropdown">
                <button type="button" class="spark-btn-secondary-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                    <span><?= lang('actions'); ?></span>
                    <i class="fa fa-chevron-down spark-dropdown-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-<?= $Settings->user_language == 'arabic' ? 'left' : 'right'; ?> tasks-menus" role="menu" aria-labelledby="dLabel">
                    <li>
                        <a href="<?= admin_url('accounts/add_opening_balance_by_excel'); ?>">
                            <i class="fa fa-file-excel-o"></i> <?= lang('Add_opening_balance_by_excel'); ?>
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
                <p class="introtext"><?= lang('list_results'); ?></p>
                <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
											?>
                <div id="form">

                    <div class="row">
                    <?php
                            if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                                        {
                                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                                        {
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                        <?= lang('Main_group', 'Main_group') ?>
                                        <?php
                                        $grp[''] = lang('select') . ' ' . lang('Main_group');
                                        foreach ($mainGroups as $mainGroup) {
                                            $grp[$mainGroup->id] = $mainGroup->name;
                                        }
                                        echo form_dropdown('grpId', $grp, (isset($_POST['grpId']) ? $_POST['grpId'] : ''), 'class="form-control select" id="grpId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                        {
                                        ?>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        <?= lang('Main_company', 'Main_company') ?>
                                        <?php
                                        $co[''] = lang('select') . ' ' . lang('Main_company');
                                        if(($this->Settings->enable_grp==1)&& !$this->input->post('grpId')){}else{

                                        foreach ($mainCompanies as $mainCompany) {
                                            $co[$mainCompany->id] = $mainCompany->name;
                                        }
                                    }

                                        echo form_dropdown('coId', $co, (isset($_POST['coId']) ? $_POST['coId'] : ''), 'class="form-control select" id="coId"   style="width:100%"')
                                        ?>
                                    </div>
                                    </div>
                                    <?php }
                                     if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1) || (isset($this->Settings->enable_br) && $this->Settings->enable_br==1))
                                     {
                                    ?>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            <?= lang('Main_branch', 'Main_branch') ?>
                                            <?php

                                            if((($this->Settings->enable_grp==1 || $this->Settings->enable_co==1)) && !$this->input->post('coId')){}else{
												if ($Owner || $Admin){
													foreach ($mainBranches as $mainBranche) {
														$br[$mainBranche->id] = $mainBranche->name;
													}
													}else{
														$br[$this->session->userdata('brId')] = $this->site->getMainBranchesByID($this->session->userdata('brId'))->name;
													}
                                        }
                                            echo form_dropdown('brId', $br, (isset($_POST['brId']) ? $_POST['brId'] : $this->session->userdata('brId')), 'class="form-control select" id="brId"   style="width:100%"')
                                            ?>

                                        </div>
                                        </div>
                                        <?php }
                                        } ?>



                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                    </div>

                </div>
                <?php
                            }
											?>
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="dmpData" class="table  table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th class="col-xs-2"><?= lang('date'); ?></th>
                            <th class="col-xs-2"><?= lang('created_by'); ?></th>
                            <th class="col-xs-2"><?= lang('note'); ?></th>
                            <th style="min-width:75px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th><th></th><th></th>
                            <th style="width:75px; text-align:center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php if ($Owner || $GP['bulk_actions']) {
                                ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?=form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
    </div>
    <?=form_close()?>
<?php
                            }
?>


<script>
	$(document).ready(function() {

		$('#submit_report').click(function(e) {
			e.preventDefault();

			$('#action-form').attr('action', '<?= admin_url('accounts/opening_balance') ?>');
			$('#action-form-submit').trigger('click');

		});

	});
</script>
