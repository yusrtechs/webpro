<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (empty($_POST)){
	$_POST['brId']=$this->session->userdata('brId');
}
$v = '';
if ($this->input->post('grpId')) {
    $v .= '&grpId=' . $this->input->post('grpId');
}
if ($this->input->post('coId')) {
    $v .= '&coId=' . $this->input->post('coId');
}
if ($this->input->post('brId')) {
    $v .= '&brId=' . $this->input->post('brId');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('wh')) {
    $v .= '&wh=' . $this->input->post('wh');
}
if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}

?>
<script>$(document).ready(function () {
        CURI = '<?= admin_url('reports/profit_loss/?v=1' . $v); ?>';
    });</script>

<script>$(document).ready(function () {
        <?php
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                        {
                            if(isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1)
                        {}else{
						?>

		                localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
			        	<?php
						}
                        if((isset($this->Settings->enable_grp) && $this->Settings->enable_grp==1) || (isset($this->Settings->enable_co) && $this->Settings->enable_co==1))
                                {}else{
						?>

	                	localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
				        <?php
						}
                        if ($Owner || $Admin)
                                {}else{
						?>

                        localStorage.setItem('sgrpId', '<?=  $this->session->userdata('grpId') ?>');
                        localStorage.setItem('scoId', '<?=  $this->session->userdata('coId') ?>');
                        localStorage.setItem('sbrId', '<?=  $this->session->userdata('brId') ?>');
                        <?php
                            }
                        }
					?>

$('#grpId').change(function () {
            localStorage.setItem('sgrpId', $(this).val());

            var v = $(this).val();
            var uopt = $("#coId");
           // console.log(uopt);

            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getCompaniesBygrpId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $('#coId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_company') ?></option>')
    .val('')
;

$('#brId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;



$('#coId').val('').trigger('change.select2');
$('#brId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });


                        } else {
                            $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#coId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_company') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_company') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_company') ?>'}]
                });
            }
        });



        $('#coId').change(function () {
            localStorage.setItem('scoId', $(this).val());



            var v = $(this).val();
            var uopt = $("#brId");


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBranchesBycoId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {

                            $('#brId')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('Main_branch') ?></option>')
    .val('')
;


$('#brId').val('').trigger('change.select2');

$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
$('#biller').select2("destroy").empty().select2({minimumResultsForSearch: 7});



                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                            });
                            $('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#brId").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('Main_branch') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('Main_branch') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('Main_branch') ?>'}]
                });
            }
        });




        $('#brId').change(function () {
            localStorage.setItem('sbrId', $(this).val());


            var v = $(this).val();
            var uopt = $("#biller");

            var uopt1 = $("#warehouse");






            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getWarehousesBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {


//console.log(scdata[0])
$('#warehouse').select2("destroy").empty().select2({minimumResultsForSearch: 7});




                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt1);

        });
        $('#warehouse').val(scdata[0].id).trigger('change.select2');
                        } else {
                            $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                                placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#warehouse").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('warehouse') ?>").select2({allowClear: false,
                    placeholder: "<?= lang('select') . ' ' . lang('warehouse') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('warehouse') ?>'}]
                });
            }


            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('system_settings/getBillersBybrId') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {

                        if (scdata != null) {



$('#biller')
    .find('option')
    .remove()
    .end()
    .append('<option value=""><?= lang('select') . ' ' . lang('biller') ?></option>')
    .val('')
;





$('#biller').val('').trigger('change.select2');


                            $.each(scdata, function () {
                $("<option />", {value: this.id, text: this.text}).appendTo(uopt);

        });

                        } else {
                            $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                                scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#biller").select2("destroy").empty().attr("placeholder", "<?= lang('select') . ' ' . lang('biller') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select') . ' ' . lang('biller') ?>",
                    scdata: [{id: '', text: '<?= lang('select') . ' ' . lang('biller') ?>'}]
                });
            }












        });


        if (localStorage.getItem('remove_slls')) {
            if (localStorage.getItem('slitems')) {
                localStorage.removeItem('slitems');
            }
			if (localStorage.getItem('pcash_account_1')) {
                localStorage.removeItem('pcash_account_1');
            }
			if (localStorage.getItem('pcc_account_1')) {
                localStorage.removeItem('pcc_account_1');
            }

			if (localStorage.getItem('pcheque_account_1')) {
                localStorage.removeItem('pcheque_account_1');
            }

			if (localStorage.getItem('ptransfer_account_1')) {
                localStorage.removeItem('ptransfer_account_1');
            }
            if (localStorage.getItem('sldiscount')) {
                localStorage.removeItem('sldiscount');
            }
            if (localStorage.getItem('sltax2')) {
                localStorage.removeItem('sltax2');
            }
            if (localStorage.getItem('slref')) {
                localStorage.removeItem('slref');
            }
            if (localStorage.getItem('slshipping')) {
                localStorage.removeItem('slshipping');
            }
            if (localStorage.getItem('slwarehouse')) {
                localStorage.removeItem('slwarehouse');
            }
            if (localStorage.getItem('sgrpId')) {
                localStorage.removeItem('sgrpId');
            }
            if (localStorage.getItem('scoId')) {
                localStorage.removeItem('scoId');
            }
            if (localStorage.getItem('sbrId')) {
                localStorage.removeItem('sbrId');
            }
            if (localStorage.getItem('slnote')) {
                localStorage.removeItem('slnote');
            }
            if (localStorage.getItem('slinnote')) {
                localStorage.removeItem('slinnote');
            }
            if (localStorage.getItem('slcustomer')) {
                localStorage.removeItem('slcustomer');
            }
            if (localStorage.getItem('slbiller')) {
                localStorage.removeItem('slbiller');
            }
            if (localStorage.getItem('slcurrency')) {
                localStorage.removeItem('slcurrency');
            }
            if (localStorage.getItem('sldate')) {
                localStorage.removeItem('sldate');
            }
            if (localStorage.getItem('slsale_status')) {
                localStorage.removeItem('slsale_status');
            }
            if (localStorage.getItem('slpayment_status')) {
                localStorage.removeItem('slpayment_status');
            }
            if (localStorage.getItem('paid_by')) {
                localStorage.removeItem('paid_by');
            }
            if (localStorage.getItem('amount_1')) {
                localStorage.removeItem('amount_1');
            }
            if (localStorage.getItem('paid_by_1')) {
                localStorage.removeItem('paid_by_1');
            }
            if (localStorage.getItem('pcc_holder_1')) {
                localStorage.removeItem('pcc_holder_1');
            }
            if (localStorage.getItem('pcc_type_1')) {
                localStorage.removeItem('pcc_type_1');
            }
            if (localStorage.getItem('pcc_month_1')) {
                localStorage.removeItem('pcc_month_1');
            }
            if (localStorage.getItem('pcc_year_1')) {
                localStorage.removeItem('pcc_year_1');
            }
            if (localStorage.getItem('pcc_no_1')) {
                localStorage.removeItem('pcc_no_1');
            }
            if (localStorage.getItem('cheque_no_1')) {
                localStorage.removeItem('cheque_no_1');
            }
            if (localStorage.getItem('slpayment_term')) {
                localStorage.removeItem('slpayment_term');
            }
            localStorage.removeItem('remove_slls');
        }

        <?php if ($this->session->userdata('remove_slls')) {
    ?>
        if (localStorage.getItem('slitems')) {
            localStorage.removeItem('slitems');
        }
        if (localStorage.getItem('sldiscount')) {
            localStorage.removeItem('sldiscount');
        }
        if (localStorage.getItem('sltax2')) {
            localStorage.removeItem('sltax2');
        }
        if (localStorage.getItem('slref')) {
            localStorage.removeItem('slref');
        }
        if (localStorage.getItem('slshipping')) {
            localStorage.removeItem('slshipping');
        }
        if (localStorage.getItem('slwarehouse')) {
            localStorage.removeItem('slwarehouse');
        }
        if (localStorage.getItem('sgrpId')) {
            localStorage.removeItem('sgrpId');
        }
        if (localStorage.getItem('scoId')) {
            localStorage.removeItem('scoId');
        }
        if (localStorage.getItem('sbrId')) {
            localStorage.removeItem('sbrId');
        }
        if (localStorage.getItem('slnote')) {
            localStorage.removeItem('slnote');
        }
        if (localStorage.getItem('slinnote')) {
            localStorage.removeItem('slinnote');
        }
        if (localStorage.getItem('slcustomer')) {
            localStorage.removeItem('slcustomer');
        }
        if (localStorage.getItem('slbiller')) {
            localStorage.removeItem('slbiller');
        }
        if (localStorage.getItem('slcurrency')) {
            localStorage.removeItem('slcurrency');
        }
        if (localStorage.getItem('sldate')) {
            localStorage.removeItem('sldate');
        }
        if (localStorage.getItem('slsale_status')) {
            localStorage.removeItem('slsale_status');
        }
        if (localStorage.getItem('slpayment_status')) {
            localStorage.removeItem('slpayment_status');
        }
        if (localStorage.getItem('paid_by')) {
            localStorage.removeItem('paid_by');
        }
        if (localStorage.getItem('amount_1')) {
            localStorage.removeItem('amount_1');
        }
        if (localStorage.getItem('paid_by_1')) {
            localStorage.removeItem('paid_by_1');
        }
        if (localStorage.getItem('pcc_holder_1')) {
            localStorage.removeItem('pcc_holder_1');
        }
        if (localStorage.getItem('pcc_type_1')) {
            localStorage.removeItem('pcc_type_1');
        }
        if (localStorage.getItem('pcc_month_1')) {
            localStorage.removeItem('pcc_month_1');
        }
        if (localStorage.getItem('pcc_year_1')) {
            localStorage.removeItem('pcc_year_1');
        }
        if (localStorage.getItem('pcc_no_1')) {
            localStorage.removeItem('pcc_no_1');
        }
        if (localStorage.getItem('cheque_no_1')) {
            localStorage.removeItem('cheque_no_1');
        }
        if (localStorage.getItem('slpayment_term')) {
            localStorage.removeItem('slpayment_term');
        }
        <?php $this->sma->unset_data('remove_slls');
}
        ?>



    });
</script>
<style>@media print {
        .fa {
            color: #EEE;
            display: none;
        }

        .small-box {
            border: 1px solid #CCC;
        }
    }
	/*.pltable{padding:0.5em;}
	.pltable1{padding-left:0.2em;padding-right:0.2em;}*/


	</style>

	<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
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


<div class="box">
    <!--<div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-bars"></i><?= lang('profit_loss'); ?></h2>

        <div class="box-icon">
            <div class="form-group choose-date hidden-xs">
                <div class="controls">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text"
                               value="<?= ($start ? $this->sma->hrld($start) : '') . ' - ' . ($end ? $this->sma->hrld($end) : ''); ?>"
                               id="daterange" class="form-control">
                        <span class="input-group-addon"><i class="fa fa-chevron-down"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">

            </ul>
        </div>
    </div>-->
	<div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart nb"></i><?= lang('profit_loss'); ?> <?php
            if ($this->input->post('start_date')) {
                echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
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
						<a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			    </li>
				<li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <p class="introtext"><?= lang('view_pl_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('reports/profit_loss'); ?>
                    <div  class="row">
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
                                            $br[''] = lang('select') . ' ' . lang('Main_branch');
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

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('warehouse'); ?></label>
                                <?php
                                $whs[''] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $whs[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('wh', $whs, (isset($_POST['wh']) ? $_POST['wh'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('warehouse') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
				<div class="row">
				<div class="col-sm-12 col-md-12">

    <div id="pr_pdf">

	<div><table border="0" cellpadding="0" cellspacing="0" class="table">

	<tbody width="100%">
	<thead style="border: 1px solid #00ace6;">
	<tr>
		<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="5" color="#212529"><?= lang('profit_loss'); ?></font></b></td>
		</tr>

    <?php
                    if($this->input->post('grpId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_group').' : '.($this->site->getMainGroupsByID($this->input->post('grpId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('coId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_company').' : '.($this->site->getMainCompaniesByID($this->input->post('coId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>

                    <?php
                    if($this->input->post('brId')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo lang('Main_branch').' : '.($this->site->getMainBranchesByID($this->input->post('brId'))->name);  ?></font></b></td>

					</tr>
                        <?php
                    }
                    ?>
                    <?php
                    if($this->input->post('warehouse')){
                        ?>
                    <tr>
					    <td style="color:#ECF0F1;border:0"  colspan="10"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>


                    <?php
                    }
                    ?>

                    <tr>
                        <td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8" align="center"  class="pltable" ><font size="2" color="#212529"> <?php
                                    echo($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ' : ' . $wh->phone . '<br>'  :  '') . ($wh->email ? lang('email') . ' : ' . $warehouse->email : '');
                                    ?></font></td>

                    </tr>
                    <tr>
                      <td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;-webkit-print-color-adjust:exact !important;"  colspan="10" align="center" class="pltable" ><b><font size="2" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('profit_loss') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></b></font></td>

					</tr>
	</thead>


	<tr>
		<td colspan="8" class="pltable" >
		</td>
	</tr>


	<tr>
		<td  style="color:#ECF0F1;border-top: 1px solid #00ace6;  border-left: 1px solid #00ace6; border-right: 1px solid #00ace6;" colspan="8" class="pltable"   ><b><font  size="3" color="#212529"><?= lang('profit_loss'); ?></font></b></td>

	</tr>
	<tr>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6; border-right: 1px solid #00ace6" colspan="6"  class="pltable"  ><b><font  size="2" color="#212529"><?= lang('revenues'); ?></font></b></td>
		<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6; border-left: 1px solid #00ace6;" colspan="2"  class="pltable" align="center"  ><b><font  size="2" color="#212529"><?= date('Y'); ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney($total_sales->total_amount) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('Less') ?> : <?= lang('return_sales') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney(abs($total_sales->rtotal_amount)) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('Less') ?> : <?= lang('Discounts') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney($total_sales->total_discount) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('Less') ?> : <?= lang('Discount_Allowance') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"><font  size="2"><?= $this->sma->formatMoney($total_sales->discount_allowance) ?></font></td>

	</tr>
	<?php
	$net_sales=$this->sma->formatDecimal($total_sales->total_amount+$total_sales->rtotal_amount-$total_sales->total_discount-$total_sales->discount_allowance,12);
	?>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('net_sales') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left" class="pltable1"  ><b><font  size="2"><?= $this->sma->formatMoney($net_sales) ?></font></b></td>

	</tr>
	<tr style="color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="2" color="#212529"><?= lang('Cost_of_Goods_Sold') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable" align="center"><b><font  size="2" color="#212529"></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Total_Cost_of_Goods_Sold') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1" ><b><font  size="2" ><?= $this->sma->formatMoney($total_cost_goods->item_cost_value) ?></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Gross_Profit') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2"><?= $this->sma->formatMoney($net_sales - $total_cost_goods->item_cost_value) ?></font></b></td>

	</tr>
	<tr style="color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="2" color="#212529"><?= lang('expenses') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2"  class="pltable" align="center"  ><b><font  size="2" color="#212529"></font></b></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1"><font  size="2"><?= lang('expenses') ?></font></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1" ><font  size="2" ><?= $this->sma->formatMoney($total_expenses->total_amount) ?></font></td>

	</tr>
	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf"  colspan="6"  class="pltable1" ><b><font  size="2"> <?= lang('Total_Expenses') ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($total_expenses->total_amount) ?></font></b></td>

	</tr>
	<?php
	$Operating_profit_loss=$this->sma->formatDecimal($net_sales - $total_cost_goods->item_cost_value - $total_expenses->total_amount,12);
	?>
	<tr style="color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Operating_profit_loss'); ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"   ><b><font  size="2"><?= $this->sma->formatMoney($Operating_profit_loss) ?></font></b></td>

	</tr>
	<?php
	$Other_Expenses=$this->sma->formatDecimal( $total_expenses->other_expenses,12);
	?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Other_Expenses') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($Other_Expenses) ?></font></b></td>

	</tr>
	<?php
	$Other_Revenues=$this->sma->formatDecimal($total_sales->other_revenues + $total_revenues->total_amount,12);
	?>

	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Discount_Earned') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($total_purchases->discount_earned) ?></b></font></td>

	</tr>


	<tr>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable1" ><b><font  size="2"><?= lang('Other_Revenues') ?></font></b></td>
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable1"  ><b><font  size="2" ><?= $this->sma->formatMoney($Other_Revenues) ?></b></font></td>

	</tr>
	<?php
	$Net_profit_loss=$this->sma->formatDecimal($Operating_profit_loss - $Other_Expenses + $Other_Revenues + $total_purchases->discount_earned,12);
	?>
	<tr style="color:#212529;">
		<td style="border-top: 1px solid #bfbfbf; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="6"  class="pltable" ><b><font  size="3"><?= lang('Net_profit_loss'); ?></font></b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #bfbfbf; border-left: 1px solid #bfbfbf; border-right: 1px solid #bfbfbf" colspan="2" align="left"  class="pltable"   ><b><font  size="3" <?php if($Net_profit_loss > 0){ echo 'color="green" <i class="fa fa-arrow-up"></i>'; }else if($Net_profit_loss<0) echo 'color="red" <i class="fa fa-arrow-down"></i>'; ?> </font> <?= $this->sma->formatMoney(abs($Net_profit_loss)) ?></b></td>

	</tr>



</tbody></table>

            </div>
			</div>
			</div>
            </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
        PrPDF('pr_pdf','<?= lang('profit_loss'); ?>');
		    return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/profit_loss_xls/0/xls/?v=1' . $v)?>";
            return false;
        });


        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
