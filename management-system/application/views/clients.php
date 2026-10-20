<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Client Management
            <small>Add, Edit</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url(); ?>addNewClient">
                        <i class="fa fa-plus"></i> Add Client</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Client List</h3>
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
                                        <th>SR</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Second Name</th>
                                        <th>Company</th>
                                        <th>Classification</th>
                                        <th>Activity</th>
                                        <th>Created Info</th>
                                        <th>Status</th>
                                        <th>Tel No</th>
                                        <th>Address</th>
                                        <th style="min-width:100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($userRecords)) {
                                        $count = 1;
                                        foreach ($userRecords as $record) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <?php echo $count ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->id ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->name ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->name_en ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->company ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->second_name ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->ISIC_Description_6 ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->uname . "<br/>" . $record->created_at ?>
                                                </td>
                                                <td>
                                                    <div class="label label-<?php
                                                                            if ($record->status == '0')
                                                                                echo 'danger';
                                                                            else if ($record->status == '1')
                                                                                echo 'success';
                                                                            ?>">
                                                        <?php
                                                        if ($record->status == '1')
                                                            echo 'Active';
                                                        else if ($record->status == '0')
                                                            echo 'Inactive';
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo $record->tel_no ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->address ?>
                                                </td>

                                                <td class="text-center" style="min-width:200px;">
                                                    <a class="btn btn-sm btn-primary" href="<?= base_url() . 'clientListingApi/' . $record->id; ?>" title="Api Setting">
                                                        <i class="fa fa-retweet"></i>
                                                    </a> |
                                                    <?php if ($record->isStore == '1'){ ?>
                                                    <a class="btn btn-sm btn-danger" href="<?= base_url() . 'clientListingShopApi/' . $record->id; ?>" title="Api Setting">
                                                        <i class="fa fa-shopping-cart menu-icon"></i>
                                                    </a> |
                                                    <?php  } ?>
                                                    <a class="btn btn-sm btn-info" href="<?php echo base_url() . 'editOldClient/' . $record->id; ?>" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a> |
                                                    <a class="btn btn-sm btn-warning" href="<?php echo base_url().'clientProcedures/'.$record->id; ?>" title="Procedures">
                                                        <i class="fa fa-wrench"></i>
                                                    </a>
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