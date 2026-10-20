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
                <div class="form-group">
                    <a class="btn btn-primary" href="<?php echo base_url() . 'addNewApi/' . $id; ?>">
                        <i class="fa fa-plus"></i> Add Api</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Api List for Client : <?php echo $company; ?></h3>
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
                                        <th>Reference</th>
                                        <th>Key</th>
                                        <th>Sync Status</th>
                                        <th>Company</th>
                                        <th>Date Created</th>
                                        <th>Warehouse Code</th>
                                        <th>Latest Sync</th>

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
                                                    <?php echo $record->reference ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->key ?>
                                                </td>
                                                <td>
                                                    <div class="label label-<?php
                                                                            if ($record->is_private_key == '1')
                                                                                echo 'danger';
                                                                            else if ($record->is_private_key == '0')
                                                                                echo 'success';
                                                                            ?>">
                                                        <?php
                                                        if ($record->is_private_key == '0')
                                                            echo 'On';
                                                        else if ($record->is_private_key == '1')
                                                            echo 'Off';
                                                        ?>
                                                    </div>

                                                </td>
                                                <td>
                                                    <?php echo $record->company ?>
                                                </td>
                                                <td>
                                                    <?php echo date('Y-m-d H:i:s',$record->date_created) ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $dataInfo=$this->api_model->getLatestLog($record->key);

                                                    echo $dataInfo ? $dataInfo->warehouse_code : null ?>
                                                </td>
                                                <td>
                                                    <?php echo $dataInfo ? date('m/d/Y H:i:s', $dataInfo->time) : null ?>
                                                </td>
                                                <?php
                                                $status = 0;
                                                if ($record->is_private_key == '0')
                                                    $status = 1;
                                                ?>


                                                <td class="text-center">
                                                    <a class="btn " href="<?= base_url() . 'editApiSync/' . $id . '/' . $record->key . '/' . $status; ?>" title="Api Setting">
                                                        <div class="label label-<?php
                                                                                if ($record->is_private_key == '0')
                                                                                    echo 'danger';
                                                                                else if ($record->is_private_key == '1')
                                                                                    echo 'success';
                                                                                ?>">
                                                            <?php
                                                            if ($record->is_private_key == '1')
                                                                echo 'Active';
                                                            else if ($record->is_private_key == '0')
                                                                echo 'Inactive';
                                                            ?>
                                                        </div>
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