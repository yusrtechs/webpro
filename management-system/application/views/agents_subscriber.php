<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-building-o"></i> Agents Management
            <small>Add, Edit</small>
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-4">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Enter agent information</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fname">Name</label>
                                    <?= $agent_info->name; ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <?= $agent_info->email; ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <?= $agent_info->phone; ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <?= $agent_info->address; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">

                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Subscriber List</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <div class="row">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>SR</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Second Name</th>
                                        <th>Company</th>
                                        <th>Classification</th>
                                        <th>Created Info</th>
                                        <th>Status</th>
                                        <th style="min-width:100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($agent_subscriber)) {
                                        $count = 1;
                                        foreach ($agent_subscriber as $record) {
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
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-info" href="<?php echo base_url() . 'editOldClient/' . $record->id; ?>" title="Edit">
                                                        <i class="fa fa-pencil"></i>
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
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo base_url(); ?>assets/js/addUser.js" type="text/javascript"></script>