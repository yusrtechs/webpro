<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css" />
<?php
$id = $clientInfo->id;
$name = $clientInfo->name;
$name_en = $clientInfo->name_en;
$company = $clientInfo->company;
$company_type = $clientInfo->company_type;
$tel_no = $clientInfo->tel_no;
$activity_id = $clientInfo->activity_id;
$address = $clientInfo->address;
$reason = $clientInfo->reason;
$orginalrenewal_date = $clientInfo->orginalrenewal_date;
$syncEveryMinues = $clientInfo->syncEveryMinues;
$inv_no = $clientInfo->inv_no;
$status = $clientInfo->status;
$agent_id = $clientInfo->agent_id ? $clientInfo->agent_id  : null;
$isAccounting = $clientInfo->isAccounting;
$isGrouping = $clientInfo->isGrouping;
$automatedEntries = $clientInfo->automatedEntries;
$automatedEntriesOffline = $clientInfo->automatedEntriesOffline;
$isManufacturing = $clientInfo->isManufacturing;
$isStore = $clientInfo->isStore;
$isSynchronizewithZATCA = $clientInfo->isSynchronizewithZATCA;
$isNearpay = $clientInfo->isNearpay;
$isPOS = $clientInfo->isPOS;
$isQuroosh = $clientInfo->isQuroosh;
$isFoodics = $clientInfo->isFoodics;
$isFixedAsset = $clientInfo->isFixedAsset;
$siteurl = $clientInfo->siteurl;
$isAutoSynchronizeZATCA = $clientInfo->isAutoSynchronizeZATCA;
$isAutoSynchronizeZATCAPOS = $clientInfo->isAutoSynchronizeZATCAPOS;
$isHR = $clientInfo->isHR;
$recipient="";
if($tel_no){
    $len = strlen($tel_no);
    if($len>8 && is_numeric($tel_no)){
        if(substr($tel_no, 0, 1) === "00"){
            $recipient=$tel_no;
        }else{
            if($len>10){
                if(substr($tel_no, 0, 1) === "+"){
                    $recipient="00".substr($tel_no, 1);
                }else{
                    $recipient="00".$tel_no;
                }
            }else{
                if(substr($tel_no, 0, 1) === "0"){
                    $recipient="00966".substr($tel_no, 1);
                }else{
                    $recipient="00966".$tel_no;
                }
            }
        }
    }
}


?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Client Management
            <small>Add / Edit</small>
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-4">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><b>Enter Client information</b></h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->

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

                    <div class="row">
                        <div class="col-md-12">
                            <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                        </div>
                    </div>
                    <form role="form" action="<?php echo base_url() ?>editClient" method="post" id="editClient" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="Full Name" name="name" value="<?php echo $name; ?>" maxlength="128">
                                        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name_en">Second Name</label>
                                        <input type="text" class="form-control" id="name_en" placeholder="Enter Second Name" name="name_en" value="<?php echo $name_en; ?>" maxlength="128">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company">Company</label>
                                        <input type="text" class="form-control" id="company" readonly placeholder="company Name" name="company" value="<?php echo $company; ?>" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_type">Classification</label>
                                        <select class="form-control" id="company_type" name="company_type">
                                            <?php
                                            if (!empty($classification)) {
                                                foreach ($classification as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->id; ?>" <?php if ($rl->id == $company_type) {
                                                                                                echo "selected=selected";
                                                                                            } ?>>
                                                        <?php echo $rl->second_name ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tel_no">Tel No</label>
                                        <input type="text" class="form-control" id="tel_no" value="<?php echo $tel_no; ?>" name="tel_no" maxlength="55">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="activity_id">Commercial Activity</label>
                                        <select class="form-control" id="activity_id" name="activity_id">
                                        <option value='0'> Select activity </option>
                                            <?php
                                            if (!empty($activities)) {
                                                foreach ($activities as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->id; ?>" <?php if ($rl->id == $activity_id) {
                                                                                                echo "selected=selected";
                                                                                            } ?>>
                                                        <?php echo $rl->ISIC_Description_6 ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="agent_id">Agent By</label>
                                        <select class="form-control" id="agent_id" name="agent_id">
                                            <option value="">Choose The Agent</option>
                                            <?php foreach ($agents as $agent) { ?>
                                                <option value="<?= $agent->id ?>" <?= ($agent->id == $agent_id) ? 'selected' : '' ?>><?= $agent->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1" <?php if ($status == 1) {
                                                                    echo "selected=selected";
                                                                } ?>>
                                                <?php echo "Active" ?>
                                            </option>
                                            <option value="0" <?php if ($status == 0) {
                                                                    echo "selected=selected";
                                                                } ?>>
                                                <?php echo "Inactive" ?>
                                            </option>
                                            <?php if ($status == 0) {
                                                ?>
                                                <option value="2"> Active with change renew date</option>
                                                <?php
                                                                    } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="renewal_date">Sync Every Minutes</label>
                                        <input id="syncEveryMinues" type="number" name="syncEveryMinues" value="<?php echo $syncEveryMinues; ?>" class="form-control " placeholder="Sync Every Minutes" autocomplete="off" />

                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inv_no">Inv No</label>
                                        <input id="inv_no" type="number" name="inv_no" value="<?php echo $inv_no; ?>" class="form-control " placeholder="Inv No" autocomplete="off" />

                                    </div>

                                </div>

                            </div>

                            <div class="row reason" style="display:none;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="reason">Inactive reason</label>
                                        <textarea class="form-control required" id="reason" name="reason" rows="4">
                                            <?php echo $reason; ?>
                                        </textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control required" id="address" name="address" rows="4">
                                            <?php echo $address; ?>
                                        </textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer text-center">
                            <input type="submit" class="btn btn-primary" value="Save" />
                            <input type="reset" class="btn btn-default" value="Reset" />
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><b>Service Details</b></h3>
                        <div style="float:right">
                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal">
                            Renew Subscription
                        </button>
                        <?php
                        if($company_type!=9){
                        ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal1">
                            Add Service
                        </button>
                        <?php if ($isGrouping == 1) {
                            ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal8">
                            Add Branch
                        </button>
                        <?php }
                            ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal2">
                            Add Warehouse
                        </button>
                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal3">
                            Add User
                        </button>
                        <?php
                        if($isNearpay){
                            ?>

                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal7">
                            Add JWT Nearpay
                        </button>
                        <?php
                        }
                        }
                        ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal4">
                            E-Contract
                        </button>
                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal5">
                            WhatsApp Settings
                        </button>
                        <!--<button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModal6">
                            Pricing
                        </button>-->
                        </div>
                    </div>
                    <div class="box-body">
                        <table width="100%" class="table table-striped table-bordered table-hover text-center" id="dataTables-service">
                            <thead>
                                <tr>
                                    <th>Created By</th>
                                    <th>Services</th>
                                    <!-- <th>Paid At</th> -->
                                    <th>Subscribe At</th>
                                    <th>Contract</th>
                                    <th>Inv No</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addClientSubscription" method="post" id="addClientSubscription" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Subscription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <b> Subscription Date</b>
                                        <input type="datetime-local" id="renewed_at" name="renewed_at" value="<?php echo $orginalrenewal_date; ?>" class="required" required />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>

                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inv_no">Inv No</label>
                                        <input type="text" class="form-control" id="inv_no" value="<?php echo set_value('inv_no'); ?>" name="inv_no" maxlength="15">
                                    </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addElectronicContract" method="post" id="addElectronicContract" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company; ?>" name="company" />
        <input type="hidden" value="<?php echo $isAccounting; ?>" name="isAccounting" />
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Electronic Contract</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="party2name">Party 2 Name</label>
                                        <input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->party2name : "") ?>" id="party2name" name="party2name" required />
                                    </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                <label for="contract_date"> Contract Date</label>
                                    <input type="date" class="form-control required" id="contract_date" name="contract_date" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->contract_date : date('Y-m-d')) ?>" class="required" required />
                                </div>
                            </div>

                            <div class="col-md-3">
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="party2info">Party 2 Info</label>
                                    <textarea class="form-control required" id="party2info" name="party2info" rows="4" style="direction: rtl;"><?php echo ($clientElectronicContract ? $clientElectronicContract->party2info : "السجل التجاري رقم
وتاريخه   هـ
الصادر من مدينة
الرقم الضريبي  :  "); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="party2address">Party 2 ِAddress</label>
                                    <textarea class="form-control required" id="party2address" name="party2address" rows="4" style="direction: rtl;"><?php echo ($clientElectronicContract ? $clientElectronicContract->party2address : "العنوان : المملكة العربية السعودية
مدينة  :
هاتف :  ".$recipient."
ايميل : "); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="party2address">Contract Value</label>
                                    <div dir="rtl">
                                        <table style="width:100%; border:0.75pt solid #000000; border-collapse:collapse;">
                                            <tbody>
                                                <tr>
                                                    <td style="width:50%; border-left:0.75pt solid #000000; border-bottom:0.75pt solid #000000; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top; background-color:#e5e5e5;">
                                                        <p dir="rtl" style="text-align: center; font-size: 14pt;"><span style="font-family:'Sakkal Majalla';">البيان</span></p>
                                                    </td>
                                                    <td style="width:20%; border-right:0.75pt solid #000000; border-left:0.75pt solid #000000; border-bottom:0.75pt solid #000000; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top; background-color:#e5e5e5;">
                                                        <p dir="rtl" style="text-align: center; font-size: 14pt;"><span style="font-family:'Sakkal Majalla';">المبلغ بالريال السعودي&nbsp;</span></p>
                                                    </td>
                                                    <td style="width:30%; border-right:0.75pt solid #000000; border-bottom:0.75pt solid #000000; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top; background-color:#e5e5e5;">
                                                        <p dir="rtl" style="text-align: center; font-size: 14pt;"><span style="font-family:'Sakkal Majalla';">الفترة&nbsp;</span></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->desc_1 : "رسوم التأسيس جهاز ( 0 ) طابعة ( 0 )") ?>" id="desc_1" name="desc_1" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" onchange="calc(1)" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->amount_1 : "0") ?>" id="amount_1" name="amount_1" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:30%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->period_1 : "") ?>" id="period_1" name="period_1" style="font-size:16px;"/></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->desc_2 : "رسوم التجديد السنوي للسنة القادمة") ?>" id="desc_2" name="desc_2" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" onchange="calc(2)" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->amount_2 : "0") ?>" id="amount_2" name="amount_2" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:30%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->period_2 : "") ?>" id="period_2" name="period_2" style="font-size:16px;"/></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->desc_3 : "الرسوم للبرنامج حاليا") ?>" id="desc_3" name="desc_3" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" onchange="calc(3)" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->amount_3 : "0") ?>" id="amount_3" name="amount_3" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:30%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->period_3 : "") ?>" id="period_3" name="period_3" style="font-size:16px;"/></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->desc_4 : "إضافة طابعات عدد ( 0 )") ?>" id="desc_4" name="desc_4" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" onchange="calc(4)" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->amount_4 : "0") ?>" id="amount_4" name="amount_4" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:30%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->period_4 : "") ?>" id="period_4" name="period_4" style="font-size:16px;"/></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->desc_5 : "االتدريب لمدة ( 5 ) ساعات") ?>" id="desc_5" name="desc_5" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" onchange="calc(5)" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->amount_5 : "0") ?>" id="amount_5" name="amount_5" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:30%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->period_5 : "") ?>" id="period_5" name="period_5" style="font-size:16px;"/></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->desc_6 : "الدعم الفني  والصيانة عن بعد لمدة العقد") ?>" id="desc_6" name="desc_6" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" onchange="calc(6)" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->amount_6 : "0") ?>" id="amount_6" name="amount_6" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:30%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="text" class="form-control required" value="<?php echo ($clientElectronicContract ? $clientElectronicContract->period_6 : "") ?>" id="period_6" name="period_6" style="font-size:16px;"/></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%; background-color:#e5e5e5;;border:0.75pt solid #000000;">
                                                        <p dir="rtl" style="text-align: right; font-size: 14pt;"><strong><span style="font-family:'Sakkal Majalla';">المجمـــــــــــــــــــــــــــــوع قبل الضريبة</span></strong><strong><span style="font-family:'Sakkal Majalla';">&nbsp;&nbsp;&nbsp;</span></strong></p>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" readonly value="<?php echo ($clientElectronicContract ? $clientElectronicContract->total : "0") ?>" id="total" name="total" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td style="width:30%; border-top:0.75pt solid #000000; border-right:0.75pt solid #000000; border-bottom:0.75pt solid #000000; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top; background-color:#e5e5e5;">
                                                        <p dir="rtl" style="text-align: right; font-size: 14pt;"><span style="font-family:'Sakkal Majalla';" dir="ltr">&nbsp;</span></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%; background-color:#e5e5e5;">
                                                        <p dir="rtl" style="text-align: right; font-size: 14pt;"><strong><span style="font-family:'Sakkal Majalla';">قيمة الضريبة المضافة</span></strong><strong><span style="font-family:'Sakkal Majalla';">&nbsp;&nbsp;&nbsp;&nbsp;</span></strong><span style="font-family:'Sakkal Majalla'; font-weight:bold;" dir="ltr">15 %</span> <span style="font-family:'Sakkal Majalla'; font-weight:bold;" dir="ltr">&nbsp;</span></p>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" readonly value="<?php echo ($clientElectronicContract ? $clientElectronicContract->tax : "0") ?>" id="tax" name="tax" style="font-size:16px;"/></span>
                                                    </td>
                                                    <td rowspan="2" style="width:30%; border-top:0.75pt solid #000000; border-right:0.75pt solid #000000; border-bottom:0.75pt solid #000000; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top; background-color:#e5e5e5;">
                                                        <p dir="rtl" style="text-align: right; font-size: 14pt;"><span style="font-family:'Sakkal Majalla';">&nbsp;</span></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:50%; border-top:0.75pt solid #000000; border-left:0.75pt solid #000000; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top; background-color:#e5e5e5;">
                                                        <p dir="rtl" style="text-align: right; font-size: 14pt;"><strong><span style="font-family:'Sakkal Majalla';">الاجمالي النهائي&nbsp;</span></strong></p>
                                                    </td>
                                                    <td style="width:20%;border:0.75pt solid #000000;">
                                                        <span dir="rtl" style="text-align: right; font-size: 18pt;"><input type="number" class="form-control required" readonly value="<?php echo ($clientElectronicContract ? $clientElectronicContract->subtotal : "0") ?>" id="subtotal" name="subtotal" style="font-size:16px;"/></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <?php
                    if($clientElectronicContract){
                        ?>
                        <a href="<?php echo base_url().$clientElectronicContract->contract_attachment ?>" class="btn btn-warning" target="_blank">Preview Electronic Contract  <i class="fa fa-paperclip"></i></a>
                        <a href="<?php echo base_url().'sendWhatsAppDodument/'.$clientElectronicContract->company.'/'.$id ?>" class="btn btn-success">Send Electronic Contract  <i class="fa fa-whatsapp fa-lg"></i></a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal8" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addClientBranch" method="post" id="addClientBranch" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Branch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="branch_code">Branch Code</label>
                                        <input type="text" class="form-control required" value="<?php echo $branch_code; ?>" id="branch_code" name="branch_code" readonly required />
                                    </div>
                            </div>

                            <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="branch_prefix">Branch Prefix</label>
                                        <input type="text" class="form-control required" value="<?php echo $branch_prefix; ?>" id="branch_prefix" name="branch_prefix" readonly required />
                                    </div>
                            </div>

                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="branch_name">Branch Name</label>
                                        <input type="text" class="form-control required" value="" id="branch_name" name="branch_name" required />
                                    </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addClientWarehouse" method="post" id="addClientWarehouse" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Warehouse</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                            <?php if ($isGrouping == 1) {
                            ?>
                            <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="brId">Branch</label>
                                        <select class="form-control required"  id="brId" name="brId"style="width:100%;">
                                            <?php
                                            if (!empty($main_branches)) {
                                                foreach ($main_branches as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->id; ?>">
                                                        <?php echo $rl->name ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                            </div>
                            </div>
                            <?php
                                } ?>
                        <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="warehouse_code">Warehouse Code</label>
                                        <input type="text" class="form-control required" value="<?php echo $warehouse_code; ?>" id="warehouse_code" name="warehouse_code" readonly required />
                                    </div>
                            </div>

                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="warehouse_name">Warehouse Name</label>
                                        <input type="text" class="form-control required" value="" id="warehouse_name" name="warehouse_name" required />
                                    </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addClientUser" method="post" id="addClientUser" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <input type="hidden" value="" name="group_name" id="group_name" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                            <?php if ($isGrouping == 1) {
                            ?>
                            <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="brId">Branch</label>
                                        <select class="form-control required"  id="brId" name="brId" style="width:100%;">
                                            <?php
                                            if (!empty($main_branches)) {
                                                foreach ($main_branches as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl->id; ?>">
                                                        <?php echo $rl->name ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                            </div>
                            </div>
                            <?php
                                } ?>
                        <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name *</label>
                                        <input type="text" class="form-control required" value="" id="first_name" name="first_name"  required />
                                    </div>
                            </div>

                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control required" value="" id="last_name" name="last_name"  />
                                    </div>
                            </div>
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Username *</label>
                                        <input type="text" class="form-control" id="username" placeholder="User Name" name="username" value="" maxlength="20" required />
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="" maxlength="128">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone number</label>
                                        <input type="text" class="form-control" id="phone" placeholder="Phone Number" name="phone" value="" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_id">Group *</label>
                                        <select class="form-control required" id="group_id" name="group_id" style="width:100%;" required>
                                            <option value="">Select Group</option>
                                            <?php
                                            if (!empty($groups)) {
                                                foreach ($groups as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl['id']; ?>" >
                                                        <?php echo $rl['name'] ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="div_groups" style="display:none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="warehouse">Warehouse</label>
                                        <select class="form-control required" id="warehouse" name="warehouse" style="width:100%;">
                                            <option value="">Select Warehouse</option>
                                            <?php
                                            if (!empty($warehouses)) {
                                                foreach ($warehouses as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl['id']; ?>" >
                                                        <?php echo $rl['name'] ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="biller">Biller</label>
                                        <select class="form-control required" id="biller" name="biller" style="width:100%;">
                                            <option value="">Create New Biller</option>
                                            <?php
                                            if (!empty($billers)) {
                                                foreach ($billers as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl['id']; ?>" >
                                                        <?php echo $rl['company'] ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                        <div class="row">
                            <div class="col-md-12" style="display:none;">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
 <!-- Modal -->
<div class="modal fade" id="exampleModal7" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addClientNearpay" method="post" id="addClientNearpay" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <input type="hidden" value="" name="group_name" id="group_name" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add JWT Nearpay</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">



                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="t_id">Terminal ID</label>
                                        <input type="text" class="form-control" id="t_id" placeholder="Terminal ID" name="t_id" value="" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="terminal_id">TID</label>
                                        <input type="text" class="form-control" id="terminal_id" placeholder="TID" name="terminal_id" value="" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="biller_jwt">Biller</label>
                                        <select class="form-control required" id="biller_jwt" name="biller_jwt" style="width:100%;">
                                            <option value="">Select Biller</option>
                                            <?php
                                            if (!empty($billers)) {
                                                foreach ($billers as $rl) {
                                            ?>
                                                    <option value="<?php echo $rl['id']; ?>" >
                                                        <?php echo $rl['company'] ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>




                        <div class="row">
                            <div class="col-md-12" style="display:none;">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
 <!-- Modal -->
<div class="modal fade" id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addWhatsAppSettings" method="post" id="addWhatsAppSettings" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <input type="hidden" value="" name="group_name" id="group_name" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">WhatsApp Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="gateway">Gateway *</label>
                                    <select class="form-control required" id="gateway" name="gateway" style="width:100%;">
                                        <option value="arkanprowhatsapp">
                                            ArkanPro Whatsapp
                                        </option>
                                        <option value="4whatsapp">
                                            4whatsapp
                                        </option>
                                        <option value="deletearkanprowhatsapp">
                                            Delete ArkanPro Whatsapp
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_whatsapp" style="display:none;">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="4whatsapp_user">User *</label>
                                        <input type="text" class="form-control required" value="" id="4whatsapp_user" name="4whatsapp_user"   />
                                    </div>
                            </div>

                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="4whatsapp_password">Password *</label>
                                        <input type="text" class="form-control required" value="" id="4whatsapp_password" name="4whatsapp_password"  />
                                    </div>
                            </div>
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="4whatsapp_code">Company code *</label>
                                        <input type="text" class="form-control" id="4whatsapp_code" placeholder="Company code" name="4whatsapp_code" value=""  />
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="4whatsapp_phone">Mobile No * Ex: (9665XXXXXXXX)</label>
                                        <input type="text" class="form-control" id="4whatsapp_phone" placeholder="Enter Mobile No" name="4whatsapp_phone" value="" />
                                    </div>
                                </div>
                            </div>







                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal6" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addClientPricing" method="post" id="addClientPricing" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pricing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="total">Total</label>
                                        <input type="text" class="form-control required" value="<?php echo $total; ?>" id="total" name="total"  required />
                                    </div>
                            </div>

                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="discount">Discount</label>
                                        <input type="text" class="form-control required" value="<?php echo $discount; ?>" id="discount" name="discount" required />
                                    </div>
                            </div>

                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="grand_total">Grand Total</label>
                                        <input type="text" class="form-control required" value="<?php echo $grand_total; ?>" id="grand_total" name="grand_total" required />
                                    </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form role="form" action="<?php echo base_url() ?>addClientService" method="post" id="addClientService" role="form" enctype="multipart/form-data">
        <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" />
        <input type="hidden" value="<?php echo $company_type; ?>" name="company_type" />
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <b> Date</b>
                                        <input type="datetime-local" id="renewed_at" name="renewed_at" value="<?php echo date('Y-m-d H:i:s'); ?>" class="required" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isGrouping" name="isGrouping" <?php if ($isGrouping == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> Branches</b>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isAccounting" name="isAccounting" <?php if ($isAccounting == 1) {
                                                                    echo "checked disabled";
                                                                } ?>> <b> Accounting System</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </label>
                                        <br>
                                        <br>
                                        &emsp;
                                        <?php if($isAccounting==1) {
										?>
                                        <label>
                                            <input type="checkbox" id="automatedEntries" name="automatedEntries" <?php if ($automatedEntries == 1) {
                                                                    echo "checked disabled";
                                                                } ?> ><b> Online Automated Entries </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </label>
                                        <label>
                                            <input type="checkbox" id="automatedEntriesOffline" name="automatedEntriesOffline" <?php if ($automatedEntriesOffline == 1) {
                                                                    echo "checked disabled";
                                                                } ?> ><b> Offline Automated Entries </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </label>
                                        <label>
                                            <input type="checkbox" id="isFoodics" name="isFoodics" <?php if ($isFoodics == 1) {
                                                                    echo "checked disabled";
                                                                } ?> ><b> Foodics </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </label>
                                        <label>
                                            <input type="checkbox" id="isFixedAsset" name="isFixedAsset" <?php if ($isFixedAsset == 1) {
                                                                    echo "checked disabled";
                                                                } ?> ><b> Fixed Assets </b>
                                        </label>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isManufacturing" name="isManufacturing" <?php if ($isManufacturing == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> Manufacturing System</b>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isHR" name="isHR" <?php if ($isHR == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> HR </b>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isPOS" name="isPOS" <?php if ($isPOS == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> Online POS </b>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isQuroosh" name="isQuroosh" <?php if ($isQuroosh == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> Quroosh </b>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isStore" name="isStore" <?php if ($isStore == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> Shop </b>
                                        </label>
                                    </div>
                                </div>
                            </div><div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isNearpay" name="isNearpay" <?php if ($isNearpay == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> Nearpay </b>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox icheck">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="isSynchronizewithZATCA" name="isSynchronizewithZATCA" <?php if ($isSynchronizewithZATCA == 1) {
                                                                    echo "checked disabled";
                                                                } ?>><b> Zatca Phase(2) </b>
                                        </label>

                                        <br>
                                        <br>
                                        &emsp;
                                        <?php if($isSynchronizewithZATCA==1) {
										?>

                                        <label>
                                            <input type="checkbox" id="isAutoSynchronizeZATCA" name="isAutoSynchronizeZATCA" <?php if ($isAutoSynchronizeZATCA == 1) {
                                                                    echo "checked disabled";
                                                                } ?> ><b> Automated Synchronize with ZATCA (Online) </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </label>

                                        <br>
                                        <br>
                                        &emsp;
                                        <label>
                                            <input type="checkbox" id="isAutoSynchronizeZATCAPOS" name="isAutoSynchronizeZATCAPOS" <?php if ($isAutoSynchronizeZATCAPOS == 1) {
                                                                    echo "checked disabled";
                                                                } ?> ><b> Automated Synchronize with ZATCA (Offline) </b>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </label>

                                        <br>
                                        <?php }
										?>

                                    </div>
                                </div>
                            </div>





                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="contract_attachment">Contract Attachement</label>
                                    <input type="file" class="form-control required" id="contract_attachment" name="contract_attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $("select").select2();
        $('#isAccounting').click(function() {
            $('#automatedEntries').attr('checked', false)
            $('#automatedEntriesOffline').attr('checked', false)

            $('#automatedEntries').prop('disabled', !$(this).is(':checked'))
            $('#automatedEntriesOffline').prop('disabled', !$(this).is(':checked'))

        });
        jQuery('ul.pagination li a').click(function(e) {
            e.preventDefault();
            var link = jQuery(this).get(0).href;
            jQuery("#searchList").attr("action", link);
            jQuery("#searchList").submit();
        });

        jQuery('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        jQuery('.resetFilters').click(function() {
            $(this).closest('form').find("input[type=text]").val("");
        })

        $('#dataTables-service').dataTable({
            dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-9'i><'col-sm-3'B>>" +
                "<'row'<'col-sm-7 col-centered'p>>",
            lengthMenu: [
                [10, 15, 25, 50, -1],
                [10, 15, 25, 50, "All"]
            ],

            //Dil
            language: {
                select: {
                    rows: "%d row selected."
                },

                url: "<?php echo base_url(); ?>assets/json/English.json"
            },
            buttons: [{
                    extend: "print",
                    text: "Print",
                    exportOptions: {
                        orthogonal: 'export',
                        columns: ':visible'
                    },
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        orthogonal: 'export'
                    },
                    text: "Excel",
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        orthogonal: 'export'
                    },
                    text: "PDF",
                }
            ],
            "order": [],
            responsive: true,
            ajax: {
                url: '<?= base_url("/editOldClientDT/$id") ?>',
                type: "POST",
            },
            order: [
                [2, 'desc']
            ],
            columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return row[11];
                    }
                }, {
                    targets: 1,
                    render: function(data, type, row, meta) {
                        html = '';
                        var siteurl= '<?= $siteurl ?>';
                        if (row[3] == "1") {
                            html += `<span class="badge badge-success"> Point Of Sell</span> `;
                        }
                        if (row[4] == "1") {
                            html += `<span class="badge badge-success"> Accounting System </span> `;
                        }
                        if (row[5] == "1") {
                            html += `<span class="badge badge-success"> Online Automated Entries </span> `;
                        }
                        if (row[7] == "1") {
                            html += `<span class="badge badge-success"> Offline Automated Entries </span> `;
                        }
                        if (row[6] == "1") {
                            html += `<span class="badge badge-success"> Manufacturing System </span> `;
                        }
                        if (row[8] == "1") {
                            html += `<span class="badge badge-success"> Renew Subscription </span> `;
                        }

                        if (row[13] == "1") {
                            html += `<span class="badge badge-success"> Foodics </span> `;
                        }

                        if (row[14] == "1") {
                            html += `<span class="badge badge-success"> Shop  </span> <a href="` + siteurl+ `" target="_blank"><i class="fa fa-paperclip"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;` ;
                        }

                        if (row[15] == "1") {
                            html += `<span class="badge badge-success"> Fixed Assets </span> `;
                        }

                        if (row[16] == "1") {
                            html += `<span class="badge badge-success"> Branches </span> `;
                        }

                        if (row[17] == "1") {
                            html += `<span class="badge badge-success"> Zatca Phase(2) </span> `;
                        }

                        if (row[18] == "1") {
                            html += `<span class="badge badge-success"> Add Warehouse with code `+row[9]+` </span> `;
                        }

                        if (row[19] == "1") {
                            html += `<span class="badge badge-success"> Add User `+row[9]+` </span> `;
                        }

                        if (row[20] == "1") {
                            html += `<span class="badge badge-success"> Automated Synchronize with ZATCA (Online) </span> `;
                        }

                        if (row[21] == "1") {
                            html += `<span class="badge badge-success"> HR </span> `;
                        }

                        if (row[22] == "1") {
                            html += `<span class="badge badge-success"> Add WhatsApp Settings `+row[9]+` </span> `;
                        }else if (row[22] == "2") {
                            html += `<span class="badge badge-success"> Delete ArkanPro Whatsapp </span> `;
                        }

                        if (row[24] == "1") {
                            html += `<span class="badge badge-success"> Automated Synchronize with ZATCA (Offline)</span> `;
                        }

                        if (row[25] == "1") {
                            html += `<span class="badge badge-success"> Quroosh </span> `;
                        }
                        if (row[26] == "1") {
                            html += `<span class="badge badge-success"> Nearpay </span> `;
                        }

                        if (row[27] == "1") {
                            html += `<span class="badge badge-success"> Add JWT Nearpay `+row[9]+` </span> `;
                        }

                        if (row[28] == "1") {
                            html += `<span class="badge badge-success"> Add Branch with code `+row[9]+` </span> `;
                        }


                        return html;
                    }
                }, {
                    targets: 2,
                    render: function(data, type, row, meta) {
                        return row[2]
                    }
                },
                {
                    targets: 3,
                    render: function(data, type, row, meta) {
                        if( row[12]){
                        return `<a href="` + baseURL + row[12] + `" target="_blank"><i class="fa fa-paperclip"></i></a>`;
                        }else{
                            return '';
                        }
                    }
                }, {
                    targets: 4,
                    render: function(data, type, row, meta) {
                        return row[23]
                    }
                }
                // , {
                //     targets: 4,
                //     render: function(data, type, row, meta) {
                //         return `<button class="btn btn-sm btn-info" title="Edit"><i class="fa fa-pencil"></i></button>`
                //     }
                // }
            ]
        });



        $('#group_id').change(function () {
			//localStorage.setItem('potax2', $(this).val());

			var group_id = $(this).val();
            $('#group_name').val($("#group_id option:selected").text());
            if(group_id>4)
			$("#div_groups").css("display", "block");
            else
            $("#div_groups").css("display", "none");


			return;
		});


        $('#gateway').change(function () {
			//localStorage.setItem('potax2', $(this).val());

			var gateway = $(this).val();
            if(gateway=="4whatsapp")
			$("#div_whatsapp").css("display", "block");
            else
            $("#div_whatsapp").css("display", "none");


			return;
		});



        var status = $('#status').val();
            if(status==0)
			$(".reason").css("display", "block");
            else
            $(".reason").css("display", "none");



        $('#status').change(function () {

			var status = $(this).val();
            if(status==0)
			$(".reason").css("display", "block");
            else
            $(".reason").css("display", "none");


			return;
		});





    });

    function calc(no){
            if($("#amount_"+no).val()>0){}else{
                $("#amount_"+no).val(0);
            }
            var total=0,tax=0,subtotal=0;
            total=parseFloat($("#amount_1").val()) + parseFloat($("#amount_2").val()) + parseFloat($("#amount_3").val()) + parseFloat($("#amount_4").val()) + parseFloat($("#amount_5").val()) + parseFloat($("#amount_6").val());
            total=(total - ((total*15)/115));
            $("#total").val(parseFloat(total).toFixed(2));
            tax=((total*15)/100);
            $("#tax").val(parseFloat(tax).toFixed(2));
            subtotal=parseFloat(total) + parseFloat(tax);
            $("#subtotal").val(parseFloat(subtotal).toFixed(2));
        }
</script>