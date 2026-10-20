<!--
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
-->
<style type="text/css">


.chat-online {
    color: #34ce57
}

.chat-offline {
    color: #e4606d
}

.chat-messages {
    display: flex;
    flex-direction: column-reverse;
    max-height: 400px;
    overflow-y: scroll
}

.chat-message-left,
.chat-message-right {
    display: flex;
    flex-shrink: 0
}

.chat-message-left {
    margin-right: auto
}

.chat-message-right {
    flex-direction: row-reverse;
    margin-left: auto
}
.py-3 {
    padding-top: 1rem!important;
    padding-bottom: 1rem!important;
}
.px-4 {
    padding-right: 1.5rem!important;
    padding-left: 1.5rem!important;
}
.flex-grow-0 {
    flex-grow: 0!important;
}
.border-top {
    border-top: 1px solid #dee2e6!important;
}
.card {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
}
.row {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}
.col-xl-12 {
    -ms-flex: 0 0 100%;
    flex: 0 0 100%;
    max-width: 100%;
}
.pb-2, .py-2 {
    padding-bottom: .5rem!important;
}
.pt-2, .py-2 {
    padding-top: .5rem!important;
}
.pb-1, .py-1 {
    padding-bottom: .25rem!important;
}
.pt-1, .py-1 {
    padding-top: .25rem!important;
}
.align-items-center {
    -ms-flex-align: center!important;
    align-items: center!important;
}
.d-flex {
    display: -ms-flexbox!important;
    display: flex!important;
}
.mr-1, .mx-1 {
    margin-right: .25rem!important;
}
.rounded-circle {
    border-radius: 50%!important;
}
img {
    vertical-align: middle;
    border-style: none;
}
.pl-3, .px-3 {
    padding-left: 1rem!important;
}
.flex-grow-1 {
    -ms-flex-positive: 1!important;
    flex-grow: 1!important;
}
.p-4 {
    padding: 1.5rem!important;
}
.pb-4, .py-4 {
    padding-bottom: 1.5rem!important;
}
.text-muted {
    color: #6c757d!important;
}
.text-nowrap {
    white-space: nowrap!important;
}
.mt-2, .my-2 {
    margin-top: .5rem!important;
}
.small, small {
    font-size: 80%;
    font-weight: 400;
    padding-right:10px;
}
.pl-3, .px-3 {
    padding-left: 1rem!important;
}
.pr-3, .px-3 {
    padding-right: 1rem!important;
}
.pb-2, .py-2 {
    padding-bottom: .5rem!important;
}
.pt-2, .py-2 {
    padding-top: .5rem!important;
}
.mr-3, .mx-3 {
    margin-right: 1rem!important;
}
.flex-shrink-1 {
    -ms-flex-negative: 1!important;
    flex-shrink: 1!important;
}
.rounded {
    border-radius: .25rem!important;
}
.bg-light {
    background-color: #ecf0f5!important;
}
.font-weight-bold {
    font-weight: 700!important;
}
.mb-1, .my-1 {
    margin-bottom: .25rem!important;
}
.input-group {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -ms-flex-align: stretch;
    align-items: stretch;
    width: 100%;
}
.input-group>.custom-file, .input-group>.custom-select, .input-group>.form-control, .input-group>.form-control-plaintext {
    position: relative;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    margin-bottom: 0;
}
.border-bottom, em {
    color: #fff;
    background: #3c8dbc;
}
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-users"></i> WhatsApp Messages
      <small></small>
    </h1>
  </section>
  <section class="content">

<div class="container p-0">
<h1 class="h3 mb-3">Messages</h1>
<div class="card">
<div class="row g-0">

<div class="col-12 col-lg-7 col-xl-12">
<div class="py-2 px-4 border-bottom d-none d-lg-block">
<div class="d-flex align-items-center py-1">
<div class="position-relative">
<img src="<?php echo base_url(); ?>assets/dist/img/avatar1.png" class="rounded-circle mr-1" alt="" width="40" height="40">
</div>
<div class="flex-grow-1 pl-3">
<strong><?php echo $clientInfo->name; ?></strong>
<div class="text-muted small"><em><?php echo $clientInfo->company; ?></em></div>
</div>
<div>
</div>
</div>
</div>
<div class="position-relative">
<div class="chat-messages p-4">
    <?php
    foreach($whatsAppMessagesInfo as $complaint){
    if(isset($complaint->offline_id)){
        ?>
        <div class="chat-message-left pb-4">
<div>
<img src="<?php echo base_url(); ?>assets/dist/img/avatar1.png" class="rounded-circle mr-1" alt="" width="40" height="40">
<div class="text-muted small text-nowrap mt-2"><?php echo $complaint->date; ?></div>
</div>
<div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
<div class="font-weight-bold mb-1"><?php echo $complaint->client_name; ?></div>
<?php echo $complaint->title; ?>
</div>
</div>
        <?php
    }else{
        ?>
        <div class="chat-message-right pb-4">
<div>
<img src="<?php echo base_url(); ?>assets/dist/img/avatar.png" class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40">
<div class="text-muted small text-nowrap mt-2"><?php echo $complaint->date; ?></div>
</div>
<div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
<div class="font-weight-bold mb-1" style="text-align:right;">You</div>
<?php
echo '<pre style="text-align:center;">';
print_r($complaint->title);
echo '</pre>';
 ?>
</div>
</div>
        <?php
    }
    }
    ?>


</div>
</div>

</div>
</div>
</div>
</div>


</section>
</div>
