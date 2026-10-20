<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Client Management
            <small>List</small>
        </h1>
    </section>
    <section class="content">
    <div class="row">
    <div class="col-xs-12">
      <div class="text-right">
        <a class="btn btn-primary" href="<?php echo base_url().'addNewProcedure/'.$id; ?>">
          <i class="fa fa-plus"></i> Add Procedure</a>
      </div>
      <div class="box">
        <div class="box-header">
        <h3 class="box-title">Client procedures : <?php echo $company; ?></h3>
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
					<th>ID</th>
                    <th>Request By</th>
                    <th>Procedure Type</th>
                    <th>Description</th>
                    <th>Copied Company Name</th>
                    <th>Copy to Warehouse Codes</th>
                    <th>Created by User</th>
                    <th>Creation Date</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                      if(!empty($procedureRecords))
                      {
                        $count=1;
                          foreach($procedureRecords as $record)
                          {
                      ?>
                    <tr>
                      <td>
                        <?php echo $count ?>
                      </td>
                      <td>
                        <?php echo $record->request_by ?>
                      </td>

                      <td>
                        <?php echo $record->procedure_type ?>
                      </td>
                      <td>
                        <?php echo $record->comment ?>
                      </td>
                      <td>
                        <?php echo $record->copied_company_name ?>
                      </td>
                      <td>
                        <?php echo $record->copy_to_warehouse_code ?>
                      </td>



                      <td>
                        <?php echo $record->name ?>
                      </td>

                      <td>
                        <?php echo $record->createdDtm ?>
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
    </div>
</section>
</div>