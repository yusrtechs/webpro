<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-users"></i> All Suggestions and Complaints
      <small></small>
    </h1>
  </section>
  <section class="content">
    <div class="col-xs-12">
      <div class="text-right">
        <!--<a class="btn btn-primary" href="<?php echo base_url(); ?>addNewTask">
          <i class="fa fa-plus"></i> Add Task</a>-->
      </div>
      <div class="box">
        <div class="box-header">
          <div class="box-tools">
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
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
            <div class="panel-body">
              <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                  <tr>
                    <th>SR</th>
                    <th>ID</th>
                    <th>Copmany Name</th>
                    <th>Copmany</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Status</th>


                    <th>Warehouse Code</th>
                    <th>Creation Date</th>
                    <th>Respond Date</th>
                    <th>Actions</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $count=1;
                      if(!empty($taskRecords))
                      {
                          foreach($taskRecords as $record)
                          {
                      ?>
                    <tr>
                    <td>
                        <?php echo $count ?>
                      </td>
                      <td>
                        <?php echo $record->id ?>
                      </td>
                      <td>
                        <?php echo $record->client_name ?>
                      </td>
                      <td>
                        <?php echo $record->company ?>
                      </td>
                      <td>
                        <?php echo $record->title ?>
                      </td>
                      <td>
                        <?php echo $record->details ?>
                      </td>
                      <td>
                        <div class="label label-<?php
                        if ($record->typeId == '1')
                        echo 'danger';
                        else if ($record->typeId == '2')
                        echo 'warning';
                        else if ($record->typeId == '3')
                        echo 'info'
                        ?>">
                          <?php echo $record->type ?>
                        </div>
                      </td>

                      <td>
                        <div class="label label-<?php
                        if ($record->statusId == '1')
                        echo 'warning';
                        else if ($record->statusId == '2')
                        echo 'info';
                        else if ($record->statusId == '3')
                        echo 'success';
                        ?>">
                          <?php echo $record->status ?>
                        </div>
                      </td>



                      <td>
                        <?php echo $record->warehouse_code ?>
                      </td>
                      <td>
                        <?php echo $record->date ?>
                      </td>
                      <td>
                        <?php echo $record->respond_date ?>
                      </td>
                      <td class="text-center" style="min-width:150px;">
                        <a class="btn btn-sm btn-info" href="<?php echo base_url().'editOldComplaint/'.$record->id; ?>" title="Edit">
                          <i class="fa fa-pencil"></i>
                        </a> |
                        <a class="btn btn-sm btn-info" href="<?php echo base_url().'complaintsNote/'.$record->id; ?>" title="Chat">
                          <i class="fa fa-comments-o"></i>
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