<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Clients Information
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
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isGrouping" name="isGrouping" <?= (isset($_POST['isGrouping'])) ? 'checked' : ''; ?>><b> Branches</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
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
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isManufacturing" name="isManufacturing" <?= (isset($_POST['isManufacturing'])) ? 'checked' : ''; ?>><b> Manufacturing System</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="isHR" name="isHR" <?= (isset($_POST['isHR'])) ? 'checked' : ''; ?>><b> HR </b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
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
                                <div class="col-md-12">
                                    <div class="checkbox icheck">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="ClientTransactions" name="ClientTransactions" <?= (isset($_POST['ClientTransactions'])) ? 'checked' : ''; ?>><b> Client Transactions </b>
                                            </label>
                                            <div id="div_date">
                                            <label>
                                            <b> Start Date</b></br><input type="datetime-local" id="start_date" name="start_date"  value="<?= (isset($_POST['ClientTransactions'])) ? $_POST['start_date'] : ''; ?>"/>
                                            </label>
                                            <label>
                                            <b> End Date</b></br><input type="datetime-local" id="end_date" name="end_date"  value="<?= (isset($_POST['ClientTransactions'])) ? $_POST['end_date'] : ''; ?>"/>
                                            </label>
                                            </div>
                                        </div>
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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Classification</th>
                                    <th>Created Info</th>
                                    <th>Renewal Date</th>
                                    <th>Latest Sale</th>
                                    <th>Latest Login</th>
                                    <th>Subscriptions</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($userRecords)) {

                                    foreach ($userRecords as $record) {
                                        $dataInfo=(isset($_POST['ClientTransactions'])) ? $this->client_model->getLatestSale($record->company,$_POST['start_date'],$_POST['end_date']) : null;

                                        if(($dataInfo['latest_date'] && $_POST['start_date'] && $_POST['end_date']) || !$_POST['start_date']){
                                ?>
                                        <tr>
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
                                                <?php echo (isset($_POST['ClientTransactions'])) ? $dataInfo['latest_date'] : '' ?>
                                            </td>

                                            <td>
                                                <?php echo (isset($_POST['ClientTransactions'])) ? $dataInfo['latest_login'] : '' ?>
                                            </td>
                                            <td>
                                                <?php echo $record->num_of_payment ?>
                                            </td>
                                            <td>
                                                <div class="label label-<?= ($record->status == '0') ? 'danger' : 'success' ?>">
                                                    <?= ($record->status == '1') ? 'Active' : 'Inactive'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-info" href="<?php echo base_url() . 'editOldClient/' . $record->id; ?>" title="Edit" target="_blank">
                                                    <i class="fa fa-inbox" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                <?php
                                        }
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
        if('<?= (isset($_POST['ClientTransactions'])) ?>'){
            $("#div_date").css("display", "block");


        }else{
            $("#div_date").css("display", "none");

        }
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
        $('#ClientTransactions').change(function() {
        if(this.checked) {
            $("#div_date").css("display", "block");
        }else{
            $("#div_date").css("display", "none");
        }

        })
    })
</script>