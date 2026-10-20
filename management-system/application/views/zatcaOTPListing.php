<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Api Management
            <small>Add</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <?php
                                    if ($zatca['binarySecurityToken']=='') {
                                        $count = 1;

                                    ?>
                <div class="form-group">
                    <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal">
                        Add Zatca OTP
                    </button>
                </div>
                <?php
                                    }else if ($zatca2['binarySecurityToken']=='') {
                                    ?>

                <div class="form-group">
                    <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal2">
                        Add Zatca OTP 2
                    </button>
                </div>

                                    <?php
                                    }
                                    ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Zatca OTP List for Client : <?php echo $company; ?></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
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
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>

                                        <th>SR#</th>
                                        <th>Key</th>
                                        <th>Secret</th>
                                        <th>requestID</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($zatca)) {
                                        $count = 1;

                                    ?>
                                            <tr>

                                                <td>
                                                    1
                                                </td>
                                                <td>
                                                    <?php echo $zatca['binarySecurityToken'] ?>
                                                </td>
                                                <td>
                                                    <?php echo $zatca['secret'] ?>
                                                </td>
                                                <td>
                                                    <?php echo $zatca['requestID'] ?>
                                                </td>



                                            </tr>
                                            <?php
                                    if (!empty($zatca2)) {


                                    ?>
                                            <tr>

                                                <td>
                                                    2
                                                </td>
                                                <td>
                                                    <?php echo $zatca2['binarySecurityToken'] ?>
                                                </td>
                                                <td>
                                                    <?php echo $zatca2['secret'] ?>
                                                </td>
                                                <td>
                                                    <?php echo $zatca2['requestID'] ?>
                                                </td>



                                            </tr>
                                    <?php

                                    }
                                    ?>
                                    <?php

                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addZatcaOTP" method="post" id="addZatcaOTP" role="form">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company; ?>" name="company" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Zatca OTP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <b> OTP</b>
                                        <input type="text" id="otp" name="otp" value="" class="form-control required" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <b> csr</b>
                                        <textarea name="csr" class="form-control required" id="csr"></textarea>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModal2Label" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addZatcaOTP2" method="post" id="addZatcaOTP2" role="form">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company; ?>" name="company" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModal2Label">Add Zatca OTP 2</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <b> OTP</b>
                                        <input type="text" id="otp" name="otp" value="" class="form-control required" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <b>csr</b>
                                        <textarea name="csr" class="form-control required" id="csr"></textarea>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>

<script src="<?php echo base_url(); ?>assets/js/jQuery-2.1.4.min.js"></script>

