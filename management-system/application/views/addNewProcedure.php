<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Client Management
            <small>Add / Edit Procedures</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Enter procedure information for client : <?php echo $company; ?></h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="addNewProcedure" action="<?php echo base_url().'addNewProcedures/'.$id; ?>" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="request_by">Request By</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('request_by'); ?>" id="request_by" name="request_by">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Procedure Type</label>
                                        <select class="form-control required" id="ptId" name="ptId"">
                                            <?php
                                            if(!empty($procedures_types))
                                            {
                                                foreach ($procedures_types as $rl)
                                                {
                                                    ?>
                                                <option value="<?php echo $rl->ptId ?>" <?php if($rl->ptId == set_value('ptId')) {echo "selected=selected";} ?>>
                                                    <?php echo $rl->procedure_type ?>
                                                </option>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div_copy_products" style="display:none;">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="request_by">Copied Company Name (Ex: doyen)</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('copied_company_name'); ?>" id="copied_company_name" name="copied_company_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="request_by">Copy to Warehouse Codes (Ex: w_1,w_2,w_3)</label>
                                        <input type="text" class="form-control required" value="<?php echo set_value('copy_to_warehouse_code'); ?>" id="copy_to_warehouse_code" name="copy_to_warehouse_code">
                                    </div>
                                </div>
                                </div>
                            <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="comment">Procedure Description</label>
                                        <textarea class="form-control required" id="comment" name="comment" rows="4">
                                            <?php echo set_value('comment'); ?>
                                        </textarea>
                                    </div>
                                </div>

                                <div class="row">
                                </div>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <input type="submit" class="btn btn-primary" value="Send" />
                                <input type="reset" class="btn btn-default" value="Reset" />
                            </div>
                    </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php
                    $this->load->helper('form');
                    $error = $this->session->flashdata('error');
                    if($error)
                    {
                ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                        <?php } ?>
                        <?php
                    $success = $this->session->flashdata('success');
                    if($success)
                    {
                ?>
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                        <?php } ?>

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
    <script type="text/javascript">
    $(document).ready(function () {
        $('#ptId').change(function () {
			//localStorage.setItem('potax2', $(this).val());

			var ptId = $(this).val();
            if(ptId==3)
			$("#div_copy_products").css("display", "block");
            else
            $("#div_copy_products").css("display", "none");


			return;
		});
    });
</script>