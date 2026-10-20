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
                    <?php
                        $json='{"app_name": "'.$company.'","user_id": "'.$user_id.'","return_url": "https://domain1.com/management-system/clientListingShopApi/'.$id.'","callback_url": "https://domain1.com/management-system/shop/callbackShopApi/'.$id.'","scope": "read_write"}';

                        $oauth_url = 'https://domain1.com/arkanproshop/'.$company.'/wc-auth/v1/authorize?' . http_build_query( json_decode($json) );
                    ?>
                    <a class="btn btn-primary" href="<?php echo $oauth_url; ?>">
                        <i class="fa fa-plus"></i> Add Shop Api</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Shop Api List for Client : <?php echo $company; ?></h3>
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
                                        <th>Consumer Key</th>
                                        <th>Consumer Secret</th>
                                        <th>Permissions</th>



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
                                                    <?php echo $record->key_id ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->consumer_key ?>
                                                </td>
                                                <td>
                                                    <?php echo $record->consumer_secret ?>
                                                </td>

                                                <td>
                                                <div class="label label-success">
                                                    <?php echo $record->permissions ?>
                                                </div>
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