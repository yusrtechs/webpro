

<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/af-2.2.2/b-1.5.1/b-colvis-1.5.1/fc-3.2.4/fh-3.1.3/r-2.2.1/sc-1.4.4/sl-1.2.5/datatables.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.16/af-2.2.2/b-1.5.1/b-colvis-1.5.1/b-flash-1.5.1/b-html5-1.5.1/b-print-1.5.1/cr-1.4.1/fc-3.2.4/fh-3.1.3/kt-2.3.2/r-2.2.1/rg-1.0.2/rr-1.2.3/sc-1.4.4/sl-1.2.5/datatables.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
		    $('#my-example').DataTable({


                  "bProcessing": true,
        "sAjaxSource": "<?php echo base_url(); ?>admin/getClientListing",
        "aoColumns": [
		         	{ data: 'sr' },
		         	{ data: 'id' },
		         	{ data: 'name' },
		         	{ data: 'name_en' },
		         	{ data: 'company' },
		         	{ data: 'second_name' },
		         	{ data: 'ISIC_Description_6' },
		         	{ data: 'uname' },
		         	{ data: 'status' },
		         	{ data: 'tel_no' },
		         	{ data: 'address' },
		         	{ data: 'actions' }
		      	],
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
            responsive: true
		   });

		} );
	</script>

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
                            <table width="100%" class="display" style="width:100%" id="my-example">
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