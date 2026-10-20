<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

  if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

if ($this->input->post('product')) {
    $v .= "&product=" . $this->input->post('product');
}
if ($this->input->post('reference_no')) {
    $v .= "&reference_no=" . $this->input->post('reference_no');
}
if ($this->input->post('supplier')) {
    $v .= "&supplier=" . $this->input->post('supplier');
}
if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
		$warehouse_id= $this->input->post('warehouse');

}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('serial')) {
    $v .= "&serial=" . $this->input->post('serial');
}
if ($this->input->post('status')) {
    $v .= "&status=" . $this->input->post('status');
}
if ($this->input->post('payment_status')) {
    $v .= "&payment_status=" . $this->input->post('payment_status');
}

?>


<script>
    $(document).ready(function () {
		
		function p_status(x) {
	//alert(lang.approved)
	//alert(lang)
	/*$.each(lang, function () {
            alert(this);
	    });*/
	
	if (x == null) {
        return '';
    } else if (x == 'pending') {
		
		x='purchase_pending';
        return '<div class="text-center"><span class="row_status label label-info">' + lang[x] + '</span></div>';
    } else if (x == 'unapproved' || x == 'غير معتمد') {
        return '<div class="text-center"><span class="row_status label label-warning">' + x + '</span></div>';
    } else if (x == 'completed' || x == 'paid' || x == 'sent' || x == 'received') {
        return '<div class="text-center"><span class="row_status label label-success">' + lang[x] + '</span></div>';
    } else if (x == 'approved' || x == 'معتمد') {
        return '<div class="text-center"><span class="row_status label label-success">' + x + '</span></div>';
    } else if (x == 'partial' || x == 'transferring' || x == 'ordered') {
        return '<div class="text-center"><span class="row_status label label-info">' + lang[x] + '</span></div>';
    } else if (x == 'due' || x == 'returned') {
        return '<div class="text-center"><span class="row_status label label-danger">' + lang[x] + '</span></div>';
    } else {
        return '<div class="text-center"><span class="row_status label label-default">' + x + '</span></div>';
    }
}
		
		function decimalFormatInPurchases(x){
			//alert(x)
			    var d = site.settings.decimals;
				var result= parseFloat(x).toFixed(d);
				//alert(result);
				if(result==(-0.01) || result==(-0.00))
					result=parseFloat(0).toFixed(d);
				return '<div class="text-center">' + result + '</div>';
				
    
		}
		function decimalFormatInPurchases2(x){
			//alert(x)
			    var d = site.settings.decimals;
				var result= parseFloat(x).toFixed(d);
				//alert(result);
				if(result==(-0.01) || result==(-0.00))
					result=parseFloat(0).toFixed(d);
				//return '<div class="text-center">' + result + '</div>';
				return result;
				
    
		}
		
        oTable = $('#POData').dataTable({
            "aaSorting": [[1, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?=lang('all')?>"]],
            "iDisplayLength": <?=$Settings->rows_per_page?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?=admin_url('carspurchases/getPurchases/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?=$this->security->get_csrf_token_name()?>",
                    "value": "<?=$this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"bSortable": false,"mRender": checkbox}, {"mRender": fld}, null, null, {"mRender": p_status}, null, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormat}, {"mRender": decimalFormatInPurchases}, {"mRender": pay_status}, {"bSortable": false,"mRender": attachment}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "purchase_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0, paid = 0, discount_earned = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total += parseFloat(aaData[aiDisplay[i]][6]);
                    paid += parseFloat(aaData[aiDisplay[i]][7]);
					discount_earned += parseFloat(aaData[aiDisplay[i]][8]);
                    balance += parseFloat(decimalFormatInPurchases2(aaData[aiDisplay[i]][9]));
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[6].innerHTML = decimalFormat(total);
                nCells[7].innerHTML = decimalFormat(paid);
                nCells[8].innerHTML = decimalFormat(discount_earned);
                nCells[9].innerHTML = decimalFormat(balance);
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('ref_no');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Car_Owner');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('purchase_status');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Buyer');?>]", filter_type: "text", data: []},
            {column_number: 10, filter_default_label: "[<?=lang('payment_status');?>]", filter_type: "text", data: []},
        ], "footer");

        <?php if ($this->session->userdata('remove_pols')) {
    ?>
        if (localStorage.getItem('poitems')) {
            localStorage.removeItem('poitems');
        }
        if (localStorage.getItem('podiscount')) {
            localStorage.removeItem('podiscount');
        }
        if (localStorage.getItem('potax2')) {
            localStorage.removeItem('potax2');
        }
        if (localStorage.getItem('poshipping')) {
            localStorage.removeItem('poshipping');
        }
        if (localStorage.getItem('poref')) {
            localStorage.removeItem('poref');
        }
        if (localStorage.getItem('powarehouse')) {
            localStorage.removeItem('powarehouse');
        }
        if (localStorage.getItem('ponote')) {
            localStorage.removeItem('ponote');
        }
        if (localStorage.getItem('posupplier')) {
            localStorage.removeItem('posupplier');
        }
        if (localStorage.getItem('pocurrency')) {
            localStorage.removeItem('pocurrency');
        }
        if (localStorage.getItem('poextras')) {
            localStorage.removeItem('poextras');
        }
        if (localStorage.getItem('podate')) {
            localStorage.removeItem('podate');
        }
        if (localStorage.getItem('postatus')) {
            localStorage.removeItem('postatus');
        }
        if (localStorage.getItem('popayment_term')) {
            localStorage.removeItem('popayment_term');
        }
        <?php $this->sma->unset_data('remove_pols');
}
        ?>
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        //$('#form').hide();
        <?php if ($this->input->post('supplier')) {
    ?>
        $('#supplier').val(<?= $this->input->post('supplier') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "suppliers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "suppliers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });

        $('#supplier').val(<?= $this->input->post('supplier') ?>);
        <?php
} ?>
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>


<!--
<?php if ($Owner || $GP['bulk_actions']) {
            echo admin_form_open('carspurchases/purchase_actions', 'id="action-form"');
        }
?>
-->
<?php echo admin_form_open("carspurchases/index", 'id="action-form"'); ?>
 
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i><?=lang('purchases') . ' (' . ($warehouse_id ? $warehouse->name : lang('All_showroom')) . ')';
				 if ($this->input->post('start_date')) {
                echo " ".lang('From')." " . $this->input->post('start_date') . " ".lang('to')." " . $this->input->post('end_date');
            }
           ?>
		   </h2>

         <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
		<div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang('actions')?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?=admin_url('carspurchases/add')?>">
                                <i class="fa fa-plus-circle"></i> <?=lang('add_purchase')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=admin_url('carspurchases/purchase_by_csv')?>">
                                <i class="fa fa-plus-circle"></i> <?=lang('add_purchase_by_csv')?>
                            </a>
                        </li>
                        <!--<li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?=lang('export_to_excel')?>
                            </a>
                        </li>-->
                        <li>
                            <a href="#" id="combine" data-action="combine">
                                <i class="fa fa-file-pdf-o"></i> <?=lang('combine_to_pdf')?>
                            </a>
                        </li>
                        <!--<li class="divider"></li>
                        <li>
                            <a href="#" class="bpo" title="<b><?=lang('delete_purchases')?></b>"
                                data-content="<p><?=lang('r_u_sure')?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?=lang('i_m_sure')?></a> <button class='btn bpo-close'><?=lang('no')?></button>"
                                data-html="true" data-placement="left">
                                <i class="fa fa-trash-o"></i> <?=lang('delete_purchases')?>
                            </a>
                        </li>-->
                    </ul>
                </li>
           <!--      <?php if (!empty($warehouses)) {
    ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?=lang('Showroom')?>"></i></a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?=admin_url('carspurchases')?>"><i class="fa fa-building-o"></i> <?=lang('All_showroom')?></a></li>
                            <li class="divider"></li>
                            <?php
                                foreach ($warehouses as $warehouse) {
                                    echo '<li ' . ($warehouse_id && $warehouse_id == $warehouse->id ? 'class="active"' : '') . '><a href="' . admin_url('carspurchases/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                                } ?>
                        </ul>
                    </li>
                <?php
}
                ?> -->
            </ul>
        </div>
		 <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown"><a href="#" id="excel" class="tip" data-action="export_excel" title="<?= lang('download_xls') ?>"><i class="icon fa fa-file-excel-o"></i></a></li>
				
				<!--<li class="dropdown">
						<a href="#" id="pdf" class="tip"  title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li> -->
			</ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>
				<div id="form">
               
                    <div class="row">
                         <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                  
                        
                         <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('reference_no', 'poref'); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>
          
                       
                        
                        
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('Car_Owner', 'supplier'); ?>
                                <?php echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control" id="supplier"'); ?> 
								</div>
                        </div>
                       <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("Showroom"); ?></label>
                                <?php
                                $wh[""] = lang('select').' '.lang('Showroom');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Showroom") . '"');
                                ?>
                            </div>
                        </div>
                           <div class="col-sm-3">
                            <div class="form-group">

                                <?= lang('purchase_status', 'postatus'); ?>
                                <?php
                                $post = ['0' => lang('all'),'received' => lang('received'), 'partial' => lang('partial'), 'pending' => lang('purchase_pending'), 'ordered' => lang('ordered'), 'returned' => lang('returned')];
                                echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('status') . '" style="width:100%;" ');
          
                                ?>

                            </div>
                            </div>
                              <?php if ($Owner || $Admin || $GP['sales-payments']) {
                                    ?>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('payment_status', 'slpayment_status'); ?>
                                <?php $pst = ['0' => lang('all'),'pending' => lang('pending'), 'due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid')];
                                    echo form_dropdown('payment_status', $pst, (isset($_POST['payment_status']) ? $_POST['payment_status'] : ""), 'class="form-control input-tip"  id="slpayment_status"'); ?>

                            </div>
                        </div>
                        <?php
                                } else {
                                    echo form_hidden('payment_status', 'pending');
                                }
                        ?>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'id="submit_report" class="btn btn-primary"'); ?> </div>
                    </div>
                    

                </div>
                <div class="clearfix"></div>


                <div class="table-responsive">
                    <table id="POData" class="table table-striped jambo_table bulk_action" cellpadding="0" cellspacing="0" border="0">
                        <thead>
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('date'); ?></th>
                            <th><?= lang('ref_no'); ?></th>
                            <th><?= lang('Car_Owner'); ?></th>
                            <th><?= lang('purchase_status'); ?></th>
                            <th><?= lang('Buyer'); ?></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Earned'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th><?= lang('payment_status'); ?></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:100px;"><?= lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?=lang('loading_data_from_server');?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?= lang('grand_total'); ?></th>
                            <th><?= lang('paid'); ?></th>
                            <th><?= lang('Discount_Earned'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th></th>
                            <th style="min-width:30px; width: 30px; text-align: center;"><i class="fa fa-chain"></i></th>
                            <th style="width:100px; text-align: center;"><?= lang('actions'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
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
$(document).ready(function () {
            
$('#submit_report').click(function (e) {
            e.preventDefault();
			
			$('#action-form').attr('action', '<?= admin_url('carspurchases/index') ?>');
			$('#action-form-submit').trigger('click');

        });
$('#excel').click(function (e) {
            e.preventDefault();
			
			$('#action-form').attr('action', '<?= admin_url('carspurchases/purchase_actions') ?>');
            
        });
$('#combine').click(function (e) {
            e.preventDefault();
			
			$('#action-form').attr('action', '<?= admin_url('carspurchases/purchase_actions') ?>');
            
        });
	});
			
</script>
