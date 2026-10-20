<?php

$id = '';
$title = '';
$details = '';
$typeId = '';
$statusId = '';
$client_name = '';
$company = '';
$warehouse_code = '';
$date = '';
$respond_by = '';
$respond_date = '';
$respond = '';




if (!empty($complaintInfo)) {
    foreach ($complaintInfo as $uf) {
        $id = $uf->id;
        $title = $uf->title;
        $details = $uf->details;
        $typeId = $uf->typeId;
        $statusId = $uf->statusId;
        $client_name = $uf->client_name;
        $company = $uf->company;
        $warehouse_code = $uf->warehouse_code;
        $date = $uf->date;
        $respond_by = $uf->respond_by;
        $respond_date = $uf->respond_date;
        $respond = $uf->respond;
    }
}


?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Suggestions and Complaints Management

        </h1>
    </section>
    <section class="content">
        <div class="row">
        <div class="col-md-12">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if ($error) {
                ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
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
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Suggestions and Complaints Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="addNewComplaint" action="<?php echo base_url() ?>editComplaint" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company"> Company</label>
                                        <input type="hidden" name="complaintId" id="complaintId" value="<?php echo $id; ?>">
                                        <input type="text" class="form-control " value="<?php echo $company; ?>" id="company" name="company" readonly>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_name"> Company Name</label>
                                        <input type="text" class="form-control " value="<?php echo $client_name; ?>" id="client_name" name="client_name" readonly>
                                    </div>

                                </div>



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="title"> Title</label>
                                        <input type="text" class="form-control " value="<?php echo $title; ?>" id="title" name="title" readonly>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role">Type</label>
                                        <select class="form-control " id="type" name="type" disabled>
                                            <option value="0">Select type</option>
                                            <?php
                                            if (!empty($complaints_type)) {
                                                foreach ($complaints_type as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->typeId ?>" <?php if ($rl->typeId == $typeId) {
                                                                                                        echo "selected=selected";
                                                                                                    } ?>>
                                                        <?php echo $rl->type ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="warehouse_code"> Warehouse Code</label>
                                        <input type="text" class="form-control " value="<?php echo $warehouse_code; ?>" id="warehouse_code" name="warehouse_code" readonly>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date"> Creation Date</label>
                                        <input type="text" class="form-control " value="<?php echo $date; ?>" id="date" name="date" readonly>
                                    </div>

                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="details"> Description</label>
                                        <textarea class="form-control" id="details" name="details" rows="4" readonly>
                                                <?php echo $details; ?>
                                            </textarea>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control required" id="status" name="status">
                                                    <option value="0">Select Status</option>
                                                    <?php
                                                    if (!empty($complaints_situations)) {
                                                        foreach ($complaints_situations as $rl) {
                                                    ?>
                                                            <option value="<?php echo $rl->statusId ?>" <?php if ($rl->statusId == $statusId) {
                                                                                                            echo "selected=selected";
                                                                                                        } ?>>
                                                                <?php echo $rl->status ?>
                                                            </option>
                                                    <?php
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </div>


                                        </div>


                                    </div>

                                    <div class="row" id="div_respond">
                                    <?php

                                        if($respond_by){
                                            ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="respond_by"> Respond By</label>
                                                <input type="text" class="form-control " value="<?php echo ($respond_by ? $this->user_model->getUserInfo($respond_by)[0]->name : ''); ?>" id="respond_by" name="respond_by" readonly>
                                            </div>
                                        </div>
                                        <?php

                                    }

                                        if($respond_date){
                                            ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="respond_date"> Respond Date</label>
                                                <input type="text" class="form-control " value="<?php echo ($respond_date ? $respond_date : ''); ?>" id="respond_date" name="respond_date" readonly>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="respond">Respond Description</label>
                                                <textarea class="form-control" id="respond" name="respond" rows="4" >
                                                <?php echo $respond; ?>
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- /.box-body -->

                                        <div class="box-footer">
                                            <input type="submit" class="btn btn-primary" value="Save" />
                                            <a  class="btn btn-default" href="<?php echo base_url() ?>complaints" >Back</a>
                                        </div>
                                    </div>
                                </div>
                    </form>
                </div>
            </div>

        </div>
    </section>

</div>

<script>
    $(document).ready(function() {
        if('<?= ($statusId==3) ?>'){
            $("#div_respond").css("display", "block");


        }else{
            $("#div_respond").css("display", "none");

        }



        $('#status').change(function() {

        if($(this).val()==3) {
            $("#div_respond").css("display", "block");
        }else{
            $("#div_respond").css("display", "none");
        }

        })
    })
</script>