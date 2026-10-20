<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Sales Information
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-3">
                <!-- general form elements -->
                <div class="box box-primary">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="" method="post" id="" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_type">Classification</label>
                                        <select class="form-control" id="company_type" name="company_type">
                                            <option value="-99" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '-99') ? 'selected' : ''; ?>>All Classifications </option>
                                            <option value="1" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '1') ? 'selected' : ''; ?>>General </option>
                                            <option value="2" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '2') ? 'selected' : ''; ?>>Supermarket - Grocery </option>
                                            <option value="3" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '3') ? 'selected' : ''; ?>>Restaurants </option>
                                            <option value="4" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '4') ? 'selected' : ''; ?>>Contracting </option>
                                            <option value="5" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '5') ? 'selected' : ''; ?>>Services </option>
                                            <option value="6" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '6') ? 'selected' : ''; ?>>Lundry </option>
                                            <option value="7" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '7') ? 'selected' : ''; ?>>Car Showrooms </option>
                                            <option value="8" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '8') ? 'selected' : ''; ?>>Tailoring </option>
                                            <option value="9" <?= (isset($_POST['company_type']) && $_POST['company_type'] == '9') ? 'selected' : ''; ?>>HR </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="-99" <?= (isset($_POST['status']) && $_POST['status'] == '1') ? 'selected' : ''; ?>>All </option>
                                            <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == '1') ? 'selected' : ''; ?>>Active </option>
                                            <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == '0') ? 'selected' : ''; ?>>Inactive </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="display:none;">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isGrouping" name="isGrouping" <?= (isset($_POST['isGrouping'])) ? 'checked' : ''; ?>><b> Branches</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display:none;">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isAccounting" name="isAccounting" <?= (isset($_POST['isAccounting'])) ? 'checked' : ''; ?>><b> Accounting System</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <label>
                                                <input type="checkbox" id="automatedEntries" name="automatedEntries" <?= (isset($_POST['automatedEntries'])) ? 'checked' : 'disabled'; ?>><b> Online Automated Entries </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <label>
                                                <input type="checkbox" id="automatedEntriesOffline" name="automatedEntriesOffline" <?= (isset($_POST['automatedEntriesOffline'])) ? 'checked' : 'disabled'; ?>><b> Offline Automated Entries </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <label>
                                                <input type="checkbox" id="isFoodics" name="isFoodics" <?= (isset($_POST['isFoodics'])) ? 'checked' : 'disabled'; ?>><b> Electronic Linking (Foodics) </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <label>
                                                <input type="checkbox" id="isFixedAsset" name="isFixedAsset" <?= (isset($_POST['isFixedAsset'])) ? 'checked' : 'disabled'; ?>><b> Fixed Assets </b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display:none;">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isManufacturing" name="isManufacturing" <?= (isset($_POST['isManufacturing'])) ? 'checked' : ''; ?>><b> Manufacturing System</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display:none;">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isHR" name="isHR" <?= (isset($_POST['isHR'])) ? 'checked' : ''; ?>><b> HR </b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display:none;">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isPOS" name="isPOS" <?= (isset($_POST['isPOS'])) ? 'checked' : ''; ?>><b> Online POS </b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display:none;">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isStore" name="isStore" <?= (isset($_POST['isStore'])) ? 'checked' : ''; ?>><b> Shop </b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <b> Start Date</b></br><input type="date" id="start_date" name="start_date"  value="<?= (isset($_POST['start_date'])) ? $_POST['start_date'] : ''; ?>"/>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label>
                                                <b> End Date</b></br><input type="date" id="end_date" name="end_date"  value="<?= (isset($_POST['end_date'])) ? $_POST['end_date'] : ''; ?>"/>
                                            </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-center">
                            <input type="submit" class="btn btn-primary" value="Search" />
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>SR#</th>
                                    <th>ID</th>
                                    <th>Customer&nbsp;Info</th>
                                    <th>Company</th>
                                    <th>Classification</th>
                                    <th>Created Info</th>
                                    <th>Renewal Date</th>
                                    <th>Tel No</th>
                                    <th>Modules</th>
                                    <th>Api Keys</th>
                                    <th>Warehouses</th>
                                    <th>Status</th>
                                    <th>Updated By</th>
                                    <th>Updated At</th>
                                    <th>Inactive reason</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($userRecords)) {
                                    $count = 1;
                                    foreach ($userRecords as $record) {




                                        $api_count=$this->api_model->getCountofKeyV2($record->company);
                                        $api_c=$api_count->count;
                                        if($api_c>1){
                                            $api_c=($api_c-1);
                                        }else{
                                            $api_c='';
                                        }

                                        $w_count=$this->client_model->getCountOfWarehouses($record->company);
                                        if($w_count>1){
                                            $w_count=($w_count-1);
                                        }else{
                                            $w_count='';
                                        }
                                ?>
                                        <tr>
                                            <td>
                                                <?php echo $count ?>
                                            </td>
                                            <td>
                                                <?php echo $record->id ?>
                                            </td>
                                            <td>
                                                <b>Name : </b><?php echo $record->name ?>
                                                <br>
                                                <b>Name En : </b><?php echo $record->name_en ?>
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
                                                <?php echo $record->renewal_date ?>
                                            </td>

                                            <td>
                                                <?php echo $record->tel_no ?>
                                            </td>

                                            <td>
                                                <?php
                                                $modules='';
                                                $mo_count=0;
                                                if($record->isGrouping==1){
                                                    $modules.='Branches ';
                                                    $mo_count++;
                                                }
                                                if($record->isAccounting==1){
                                                    if($mo_count>0){
                                                        $modules.=', ';
                                                    }
                                                    $modules.='Accounting ';
                                                    $mo_count++;
                                                }
                                                if($record->isManufacturing==1){
                                                    if($mo_count>0){
                                                        $modules.=', ';
                                                    }
                                                    $modules.='Manufacturing ';
                                                    $mo_count++;
                                                }
                                                if($record->isFixedAsset==1){
                                                    if($mo_count>0){
                                                        $modules.=', ';
                                                    }
                                                    $modules.='Fixed Assets ';
                                                    $mo_count++;
                                                }
                                                if($record->isHR==1){
                                                    if($mo_count>0){
                                                        $modules.=', ';
                                                    }
                                                    $modules.='HR ';
                                                    $mo_count++;
                                                }
                                                echo $modules;
                                                ?>
                                            </td>

                                            <td>
                                                <?php echo $api_c ?>
                                            </td>
                                            <td>
                                                <?php echo $w_count ?>
                                            </td>
                                            <td>
                                                <div class="label label-<?= ($record->status == '0') ? 'danger' : 'success' ?>">
                                                    <?= ($record->status == '1') ? 'Active' : 'Inactive'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                if(($record->status == '0')){
                                                echo $this->user_model->getUserInfo($record->updatedBy)[0]->name;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if(($record->status == '0')){
                                                    echo $record->updated_at;
                                                 }
                                                 ?>
                                            </td>
                                            <td>
                                                <?php if(($record->status == '0')){
                                                    echo $record->reason;
                                                 }
                                                ?>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-info" href="<?php echo base_url() . 'editOldClient/' . $record->id; ?>" title="Edit" target="_blank">
                                                    <i class="fa fa-inbox" aria-hidden="true"></i>
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
    </section>
</div>
<script>
    $(document).ready(function() {

        $('#isAccounting').click(function() {
            $('#automatedEntries').attr('checked', false)
            $('#automatedEntriesOffline').attr('checked', false)
            $('#isFoodics').attr('checked', false)
            $('#isFixedAsset').attr('checked', false)

            $('#automatedEntries').prop('disabled', !$(this).is(':checked'))
            $('#automatedEntriesOffline').prop('disabled', !$(this).is(':checked'))
            $('#isFoodics').prop('disabled', !$(this).is(':checked'))
            $('#isFixedAsset').prop('disabled', !$(this).is(':checked'))

        })

    })
</script>