<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Api Management
            <small>View</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">App Version List for Client : <?php echo $company; ?></h3>
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
                                        <th>Warehouse Code</th>
                                        <th>App Name</th>
                                        <th>App Version</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($app_versions)) {
                                        $count = 1;
                                        foreach ($app_versions as $app_ver) {


                                    ?>
                                            <tr>

                                                <td>
                                                    <?php echo $count ?>
                                                </td>
                                                <td>
                                                    <?php echo $app_ver['code'] ?>
                                                </td>
                                                <td>
                                                    <?php echo $app_ver['app_name'] ?>
                                                </td>
                                                <td>
                                                    <?php echo $app_ver['app_version'] ?>
                                                </td>



                                            </tr>
                                    <?php
                                        $count++;
                                        }
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

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>

<script src="<?php echo base_url(); ?>assets/js/jQuery-2.1.4.min.js"></script>

