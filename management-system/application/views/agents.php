<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-building-o"></i> Agents Management
            <small>Add, Edit, Delete</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>agents/new">
                        <i class="fa fa-plus"></i> Add Agent</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Agents List</h3>
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
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Number Clients</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($agentsRecords)) {
                                        foreach ($agentsRecords as $record) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <?php echo $record->id ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->name ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->email ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->phone ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->address ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->clients_total; ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->created_at ?>
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-primary" href="<?= base_url() . 'agents/history/' . $record->id; ?>" title="log history">
                                                        <i class="fa fa-history"></i>
                                                    </a>
                                                    |
                                                    <a class="btn btn-sm btn-info" href="<?= base_url() . 'agents/' . $record->id; ?>" title="Edit Agent">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php
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