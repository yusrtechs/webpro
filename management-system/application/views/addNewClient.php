<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Client Management
            <small>Add / Edit</small>
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->



                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Enter Client information</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="addClient" action="<?php echo base_url() ?>addNewClientSubmit" method="post" role="form" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('name'); ?>" id="name" name="name" maxlength="128">
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name_en">Second Name</label>
                                        <input type="text" class="form-control required " id="name_en" value="<?php echo set_value('name_en'); ?>" name="name_en" maxlength="128">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company">Company</label>
                                        <input type="text" class="form-control" id="company" value="<?php echo set_value('company'); ?>" name="company" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_type">Classification</label>
                                        <select class="form-control" id="company_type" name="company_type">
                                            <?php
                                            if (!empty($classification)) {
                                                foreach ($classification as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->id; ?>">
                                                        <?php echo $rl->second_name ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="div_with_products" style="display:none;">
                            <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="with_products" name="with_products" checked><b> With products</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tel_no">Tel No</label>
                                        <input type="text" class="form-control" id="tel_no" value="<?php echo set_value('tel_no'); ?>" name="tel_no" maxlength="55">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="activity_id">Commercial Activity</label>
                                        <select class="form-control" id="activity_id" name="activity_id">
                                        <option value='0'> Select activity </option>
                                            <?php
                                            if (!empty($activities)) {
                                                foreach ($activities as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->id; ?>">
                                                        <?php echo $rl->ISIC_Description_6 ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Agent By</label>
                                        <select class="form-control" id="agent_id" name="agent_id">
                                            <option value="">Choose The Agent</option>
                                            <?php foreach ($agents as $agent) { ?>
                                                <option value="<?= $agent->id ?>"><?= $agent->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1">
                                                <?php echo "Active" ?>
                                            </option>
                                            <option value="0">
                                                <?php echo "Inactive" ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control required" id="address" name="address" rows="4">
                                            <?php echo set_value('address'); ?>
                                        </textarea>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>

                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inv_no">Inv No</label>
                                        <input type="text" class="form-control" id="inv_no" value="<?php echo set_value('inv_no'); ?>" name="inv_no" maxlength="15">
                                    </div>
                            </div>

                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isGrouping" name="isGrouping"><b> Branches</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isAccounting" name="isAccounting"><b> Accounting System</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isManufacturing" name="isManufacturing"><b> Manufacturing System</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isPOS" name="isPOS"><b> Online POS </b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isStore" name="isStore"><b> Shop </b>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>



                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Save" />
                            <input type="reset" class="btn btn-default" value="Reset" />
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo base_url(); ?>assets/js/addUser.js" type="text/javascript"></script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>

<script>
$(document).ready(function(){

            // Initialize select2
            $("select").select2();

            $('#company_type').change(function () {
			//localStorage.setItem('potax2', $(this).val());

			var company_type = $(this).val();
            if(company_type==2)
			$("#div_with_products").css("display", "block");
            else
            $("#div_with_products").css("display", "none");


			return;
		});
        });
</script>