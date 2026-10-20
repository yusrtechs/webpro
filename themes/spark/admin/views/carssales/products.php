<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.tableData table    { page-break-inside:avoid; page-break-after:auto }
.tableData tr    { page-break-inside:avoid; page-break-after:auto }
.tableData  tfoot    { page-break-inside:avoid; page-break-after:auto }
</style>

<?php
$v = '';

if ($this->input->post('product')) {
    $v .= '&product=' . $this->input->post('product');
}
if ($this->input->post('category')) {
    $v .= '&category=' . $this->input->post('category');
}
if ($this->input->post('product_type')) {
    $v .= '&product_type=' . $this->input->post('product_type');
}
if ($this->input->post('brand')) {
    $v .= '&brand=' . $this->input->post('brand');
}
if ($this->input->post('subcategory')) {
    $v .= '&subcategory=' . $this->input->post('subcategory');
}
if ($this->input->post('warehouse')) {
    $v .= '&warehouse=' . $this->input->post('warehouse');
}
if ($this->input->post('start_date')) {
    $v .= '&start_date=' . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= '&end_date=' . $this->input->post('end_date');
}
if ($this->input->post('cf1')) {
    $v .= '&cf1=' . $this->input->post('cf1');
}
if ($this->input->post('cf2')) {
    $v .= '&cf2=' . $this->input->post('cf2');
}
if ($this->input->post('cf3')) {
    $v .= '&cf3=' . $this->input->post('cf3');
}
if ($this->input->post('cf4')) {
    $v .= '&cf4=' . $this->input->post('cf4');
}
if ($this->input->post('cf5')) {
    $v .= '&cf5=' . $this->input->post('cf5');
}
if ($this->input->post('cf6')) {
    $v .= '&cf6=' . $this->input->post('cf6');
}
if ($this->input->post('reference_no')) {
    $v .= "&reference_no=" . $this->input->post('reference_no');
}
if ($this->input->post('sale_status')) {
    $v .= "&sale_status=" . $this->input->post('sale_status');
}

if ($this->input->post('customer')) {
    $v .= "&customer=" . $this->input->post('customer');
}

//echo $v;

?>
<script>
    $(document).ready(function () {
        function resolveAfter2Seconds() {
			$('.dataTables_length').hide();
			$('.dataTables_filter').hide();
            $('.dataTables_paginate').hide();

			var oSettings = oTable.fnSettings();
        oSettings._iDisplayLength = -1;


   oTable.fnDraw();
  return new Promise(resolve => {
    setTimeout(() => {

      resolve('resolved');
    }, 2000);
  });
}

async function asyncCall() {
  //console.log('calling');
  const result = await resolveAfter2Seconds();
  PrPDF('pr_pdf','<?= lang('Invoice_profit_report'); ?>','L');
  $('.dataTables_length').show();
  $('.dataTables_filter').show();
  $('.dataTables_paginate').show();

  //console.log(result);
  // expected output: "resolved"
}


		$('#pdf').click(function (event) {


asyncCall();


		    return false;

        });

        function spb(x) {
            v = x.split('__');
            return '('+currencyFormat(v[0])+') <strong>'+formatMoney(v[1])+'</strong>';
        }

        function pqFormatFWSPrice(x) {
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
           // d += decimalFormat(v[0]);
            d += decimalFormat(v[0]*v[1]);

        }
        return d;
    } else {
        return '';
    }
}
function pqFormatFWSPriceSplit(x) {

var total=0;
var flage=0;

if (x != null) {
var d = '',
 pqc = x.split('___');
for (index = 0; index < pqc.length; ++index) {
 var pq = pqc[index];
 var v = pq.split('__');

 if(index==0){
 flage=v[1];
 total=parseFloat(v[0]) + parseFloat(total);
 if(pqc.length==1)
 d += decimalFormat(total);
 }else{
     if(flage==v[1]){
         total=parseFloat(v[0]) + parseFloat(total);

     }else{
         d += decimalFormat(total);
         flage=v[1];
         total=0;
         total=parseFloat(v[0]) + parseFloat(total);

     }

 }
 //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
 //d += decimalFormat(v[0]);
// total=parseFloat(v[0]) + parseFloat(total);
}
if(pqc.length!=1)
d += decimalFormat(total);

return d;
} else {
return '';
}
}

function pqFormatFWSTotalPrice(x) {
    var total=0;
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
            //d += decimalFormat(v[0]);
            //alert(v[0])
            //total=parseFloat(v[0]) + parseFloat(total);
            total=parseFloat(v[0]*v[1]) + parseFloat(total);

        }
          return '<strong>'+decimalFormat(total)+'</strong>';
    } else {
        return '0';
    }
}
function pqFormatFWSTotalPrice2(x) {
    var total=0;
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
            //d += decimalFormat(v[0]);
            //alert(v[0])
            //total=parseFloat(v[0]) + parseFloat(total);
            total=parseFloat(v[0]*v[1]) + parseFloat(total);

        }
        return total;
    //    return '<strong>'+decimalFormat(total)+'</strong>';
    } else {
        return '0';
    }
}
function pqFormatFWSTotalPrice1(x) {
    var total=0;
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
            //d += decimalFormat(v[0]);
            //alert(v[0])
            //total=parseFloat(v[0]) + parseFloat(total);

            total=parseFloat(v[0]) + parseFloat(total);


        }
        return '<strong>'+decimalFormat(total)+'</strong>';
    } else {
        return '0';
    }
}


function pqFormatFWSTotalPriceFloat(x) {
    var total=0;
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
            //d += decimalFormat(v[0]);
            total=parseFloat (parseFloat(v[0]) + (total));


        }
        return total;
    } else {
        return '0';
    }
}

function pqFormatFWSPriceSplitFloat(x) {

var total=0;
var flage=0;

if (x != null) {
var d = '',
 pqc = x.split('___');
for (index = 0; index < pqc.length; ++index) {
 var pq = pqc[index];
 var v = pq.split('__');

 if(index==0){
 flage=v[1];
 total=parseFloat(v[0]) + parseFloat(total);
 if(pqc.length==1)
 d =parseFloat (d + parseFloat(total));
 }else{
     if(flage==v[1]){
         total=parseFloat(v[0]) + parseFloat(total);

     }else{
        d =parseFloat (d + parseFloat(total));
         flage=v[1];
         total=0;
         total=parseFloat(v[0]) + parseFloat(total);

     }

 }
 //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
 //d += decimalFormat(v[0]);
// total=parseFloat(v[0]) + parseFloat(total);
}
if(pqc.length!=1)
d =parseFloat (d + parseFloat(total));

return d;
} else {
return '';
}
}
function pqFormatFWSTotalPriceMinusFloat(x) {
    var total=0;
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
            //d += decimalFormat(v[0]);

        if(v[1])
            total=parseFloat(parseFloat((-1)*v[0]) + (total));
            else
            total=parseFloat(parseFloat((-1)*v[0]) + (total));


		}
        return total;
    } else {
        return '0';
    }
}

function pqFormatFWSTotalPriceMinus(x) {
    var total=0;
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
            //d += decimalFormat(v[0]);
           // total=parseFloat((-1)*v[0]) + parseFloat(total);
           // total=parseFloat(v[0]) + parseFloat(total);
            total=parseFloat (parseFloat((-1)*v[0]) + (total));



        }
    //    return '<strong>'+decimalFormat(total)+'</strong>';
        return total;
    } else {
        return '0';
    }
}
function pqFormatFWSPriceFloat(x) {

if (x != null) {
var d = '',
 pqc = x.split('___');
for (index = 0; index < pqc.length; ++index) {
 var pq = pqc[index];
 var v = pq.split('__');
 //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
 d =parseFloat (d + parseFloat(v[0]*v[1]));

 // d += parseFloat(v[0]*v[1]).replace('<div class="text-center">', '').replace('</div>', '');

}

return d;
} else {
return '';
}
}


function pqFormatFWS(x) {
           // alert(x);
    if (x != null) {
        var d = '',
            pqc = x.split('___');
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split('__');
            //d += v[0] + ' <br> <?= lang('quantity') ?> : ' + formatQuantity2(v[1]) + '<br>';
            d += v[0] + ' <br>';
        }
        return d;
    } else {
        return '';
    }
}

var fTotal=0,currentRow='0';
        oTable = $('#PrRData').dataTable({
            "aaSorting": [[0, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('carssales/getProductsReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                //if(iDisplayIndex==0)
                //fTotal=0;
                //alert(pqFormatFWSTotalPrice1(aData[6]))
                nRow.id = aData[10];

                nRow.className = "invoice_link2";
                return nRow;
            },
            "aoColumns": [null, {"bSearchable": false,"mRender": pqFormatFWS},null, {"bSortable": false,"mRender": pqFormatFWSPriceSplit}, {"bSortable": false,"mRender": pqFormatFWSPriceSplit}, {"mRender": decimalFormat}, {"bSearchable": false,"mRender": pqFormatFWSTotalPrice1},  {"bSearchable": false,"mRender": pqFormatFWSPrice},  {"bSearchable": false,"mRender": pqFormatFWSTotalPrice}, {
					"mRender": function(value, type, row, meta) {
						/*if(user_language=='arabic'){
						if ($.trim(row[2]) != "Adjusting")
							return sma_lang[row[2]];
						else if (sma_lang[row[3]])
							return sma_lang[row[3]];
						else
							return row[3];
					}else{
						if ($.trim(row[2]) != "Adjusting")
							return row[2];
						else
							return row[3];
                            '<strong>'+decimalFormat(total)+'</strong>';

					}*/

                    if(row[10]!=currentRow){


                    if(parseFloat(pqFormatFWSTotalPrice2(row[8]))<0){
                        //fTotal= fTotal +(parseFloat((-1) * parseFloat(pqFormatFWSTotalPriceMinus(row[9]))));
                        fTotal= fTotal +(parseFloat((pqFormatFWSTotalPrice2(row[8]) - pqFormatFWSTotalPriceFloat(row[6]))));
                        //alert(fTotal)
                        currentRow=row[10]+"";

                    }else{

                        //fTotal= fTotal +(parseFloat(parseFloat(pqFormatFWSTotalPriceMinus(row[9]))));
                        fTotal= fTotal +(parseFloat(parseFloat((pqFormatFWSTotalPrice2(row[8]) - pqFormatFWSTotalPriceFloat(row[6])))));
                        currentRow=row[10]+"";
                    }

                }

                if(parseFloat(pqFormatFWSTotalPrice2(row[8]))<0){
                        //alert(fTotal)
                        //var txt='<strong>'+decimalFormat(parseFloat((-1) * parseFloat(pqFormatFWSTotalPriceMinus(row[9]))))+'</strong>'
                        var txt='<strong>'+decimalFormat(parseFloat( (pqFormatFWSTotalPrice2(row[8]) - pqFormatFWSTotalPriceFloat(row[6]))))+'</strong>'
                            return txt;

                    }else{
                        //alert(pqFormatFWSTotalPriceFloat(row[6]))
                        //alert(pqFormatFWSTotalPrice2(row[8]) - pqFormatFWSTotalPriceFloat(row[6]))
                        //var txt='<strong>'+decimalFormat(pqFormatFWSTotalPriceMinus(row[9]))+'</strong>';
                        var txt='<strong>'+decimalFormat((pqFormatFWSTotalPrice2(row[8]) - pqFormatFWSTotalPriceFloat(row[6])))+'</strong>';
                        return txt;
                    }


					}
				}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var pq = 0, sq = 0, bq = 0, pa = 0, sa = 0, ba = 0, pl = 0, gc = 0, rv=0;
                for (var i = 0; i < aaData.length; i++) {

					//p = (aaData[aiDisplay[i]][2]).split('__');
                    //s = (aaData[aiDisplay[i]][3]).split('__');
                    //b = (aaData[aiDisplay[i]][5]).split('__');

                    pq += parseFloat(pqFormatFWSPriceSplitFloat(aaData[aiDisplay[i]][3]));
                    //pq += parseFloat(p[0]);
                    pa += parseFloat(pqFormatFWSPriceSplitFloat(aaData[aiDisplay[i]][4]));
                  //  sq += currencyFormat(aaData[aiDisplay[i]][4]);
                    sq += parseFloat(aaData[aiDisplay[i]][5]);

                    sa += parseFloat(pqFormatFWSTotalPriceFloat(aaData[aiDisplay[i]][6]));
                   // gc += parseFloat(pqFormatFWSTotalPriceFloat(aaData[aiDisplay[i]][8]));
                    gc += parseFloat(pqFormatFWSPriceFloat(aaData[aiDisplay[i]][8]));
                    pl += parseFloat(pqFormatFWSPriceFloat(aaData[aiDisplay[i]][7]))

                    rv += parseFloat(pqFormatFWSTotalPriceMinusFloat(aaData[aiDisplay[i]][9]));

                    //ba += parseFloat(aaData[aiDisplay[i]][9]);

                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[3].innerHTML = '<div class="text-right">'+currencyFormat(pq)+'</div>';
                nCells[4].innerHTML = '<div class="text-right">'+currencyFormat(pa)+'</div>';
                nCells[5].innerHTML = '<div class="text-right">'+currencyFormat(sq)+'</div>';
                nCells[6].innerHTML = '<div class="text-right">'+currencyFormat(sa)+'</div>';
                nCells[7].innerHTML = currencyFormat(parseFloat(pl));
                nCells[8].innerHTML = '<div class="text-right">'+formatMoney(gc)+'</div>';
                nCells[9].innerHTML = '<div class="text-right">'+currencyFormat(fTotal)+'</div>';
                fTotal=0;
                //nCells[9].innerHTML = '<div class="text-right">'+formatMoney(ba)+'</div>';
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('product_code');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},

        ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        // $('#category').select2({allowClear: true, placeholder: "<?= lang('select'); ?>", minimumResultsForSearch: 7}).select2('destroy');
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_subcategory') ?>",
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
        });
        <?php if (isset($_POST['category']) && !empty($_POST['category'])) {
    ?>
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $_POST['category'] ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                        placeholder: "<?= lang('no_subcategory') ?>",
                        data: scdata
                    });
                }
            }
        });
        <?php
} ?>
    });
</script>
<script type="text/javascript">
      <?php if ($this->input->post('customer')) { ?>
        $('#customer').val(<?= $this->input->post('customer') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "customers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });

        $('#customer').val(<?= $this->input->post('customer') ?>);
        <?php } ?>

    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>

<?=$content_report?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i><?= lang('Invoice_profit_report'); ?> <?php
            if ($this->input->post('start_date')) {
               // echo 'From ' . $this->input->post('start_date') . ' to ' . $this->input->post('end_date');
                echo lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date');
            }
            ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                 <li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
               <!-- -->
                <li class="dropdown">
						<a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>">
							<i class="icon fa fa-file-pdf-o"></i>
						</a>
			     </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open('carssales/products_report'); ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang("reference_no"); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ""), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>
                         <div class="col-sm-4" style="display:none;">
                            <div class="form-group" >
                                <?= lang('category', 'category') ?>
                                <?php
                                $cat[''] = lang('select') . ' ' . lang('category');
                                foreach ($categories as $category) {
                                    $cat[$category->id] = $category->name;
                                }
                                echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ''), 'class="form-control select" id="category" placeholder="' . lang('select') . ' ' . lang('category') . '" style="width:100%"')
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4" style="display:none;">
                            <div class="form-group">
                                <?= lang('subcategory', 'subcategory') ?>
                                <div class="controls" id="subcat_data"> <?php
                                    echo form_input('subcategory', (isset($_POST['subcategory']) ? $_POST['subcategory'] : ''), 'class="form-control" id="subcategory"  placeholder="' . lang('select_category_to_load') . '"');
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4"  style="display:none;">
                            <div class="form-group">
                                <?= lang('brand', 'brand') ?>
                                <?php
                                $bt[''] = lang('select') . ' ' . lang('brand');
                                foreach ($brands as $brand) {
                                    $bt[$brand->id] = $brand->name;
                                }
                                echo form_dropdown('brand', $bt, (isset($_POST['brand']) ? $_POST['brand'] : ''), 'class="form-control select" id="brand" placeholder="' . lang('select') . ' ' . lang('brand') . '" style="width:100%"')
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang('Showroom'); ?></label>
                                <?php
                                $wh[''] = lang('select') . ' ' . lang('Showroom');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Showroom') . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('sale_status', 'slsale_status'); ?>
                                <?php $sst = ['0' => lang('all'),'completed' => lang('completed'), 'pending' => lang('pending'), 'returned' => lang('returned')];
                                echo form_dropdown('sale_status', $sst, (isset($_POST['sale_status']) ? $_POST['sale_status'] : ""), 'class="form-control input-tip" required="required" id="slsale_status"'); ?>

                            </div>
                            </div>


                        <div class="col-sm-4" style="display:none;">
                            <div class="form-group">
                                <label class="control-label" for="product_type"><?= lang('product_type'); ?></label>
                            <?php
                                $opts = ['' => lang('select') . ' ' . lang('product_type'), 'standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service')];
                                echo form_dropdown('product_type', $opts, (isset($_POST['product_type']) ? $_POST['product_type'] :  ''), 'class="form-control" id="product_type" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('product_type') . '"');
							?>
                            </div>
                        </div>

                  <!--      <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf1', 'cf1') ?>
                                <?= form_input('cf1', (isset($_POST['cf1']) ? $_POST['cf1'] : ''), 'class="form-control tip" id="cf1"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf2', 'cf2') ?>
                                <?= form_input('cf2', (isset($_POST['cf2']) ? $_POST['cf2'] : ''), 'class="form-control tip" id="cf2"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf3', 'cf3') ?>
                                <?= form_input('cf3', (isset($_POST['cf3']) ? $_POST['cf3'] : ''), 'class="form-control tip" id="cf3"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf4', 'cf4') ?>
                                <?= form_input('cf4', (isset($_POST['cf4']) ? $_POST['cf4'] : ''), 'class="form-control tip" id="cf4"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf5', 'cf5') ?>
                                <?= form_input('cf5', (isset($_POST['cf5']) ? $_POST['cf5'] : ''), 'class="form-control tip" id="cf5"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf6', 'cf6') ?>
                                <?= form_input('cf6', (isset($_POST['cf6']) ? $_POST['cf6'] : ''), 'class="form-control tip" id="cf6"') ?>
                            </div>
                        </div>  -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang("customer"); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>
                <div id="pr_pdf">

                <table border="0" cellpadding="0" cellspacing="0" class="table">

					<thead style="border: 1px solid #00ace6;">
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"  align="center" class="pltable" ><b><font  size="4" color="#212529"><?= lang('Invoice_profit_report'); ?></font></b></td>
					</tr>
					<tr>
					<td style="color:#ECF0F1;border:0;"  colspan="8"   align="center" class="pltable" ><b><font size="3" color="#212529"><?php echo($wh->name ?  $wh->name  : $Settings->site_name);  ?></font></b></td>

					</tr>
					<tr>
						<td style="color:#ECF0F1;border-bottom: 1px solid #00ace6;"  colspan="8"  align="center" class="pltable" ><b><font size="3" color="#212529">



						<?php
                    echo($this->input->post('start_date') ?  lang('period') .' '. lang('Invoice_profit_report') .' '. lang('From') .' '. $this->input->post('start_date') . ' ' . lang('to') .' '. $this->input->post('end_date')  : '');// . ($wh->address ?  $wh->address  : '') . ($wh->phone ? lang('tel') . ': ' . $wh->phone . '<br>' : '') . ($wh->email ? lang('email') . ': ' . $warehouse->email : '');
                    ?></font></b></td>

					</tr>
					</thead>
					</table>

                <div class="table-responsive " >
                    <table id="PrRData"
                           class="table table-striped  table-condensed table-hover dfTable reports-table tableData"
                           style="margin-bottom:5px;">
                        <thead>
                        <tr class="active">
                            <th><?= lang('Invoice_No'); ?></th>
                            <th style="min-width:200px;max-width:200px;"><?= lang('product_name') .' - '.lang('Service'); ?></th>
                            <th><?= lang("customer"); ?></th>
                            <th><?= lang('cost'); ?></th>
                            <th><?= lang('Car_expenses'); ?></th>
                            <th><?= lang('Invoice_expenses'); ?></th>
                            <th><?= lang('purchased_total'); ?></th>
                            <th><?= lang('price'); ?></th>
                            <th><?= lang('sold_total'); ?></th>
                            <th><?= lang('profit_loss'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?= lang('cost'); ?></th>
                            <th><?= lang('Car_expenses'); ?></th>
                            <th><?= lang('Invoice_expenses'); ?></th>
                            <th><?= lang('purchased_total'); ?></th>
                            <th><?= lang('price'); ?></th>
                            <th><?= lang('sold_total'); ?></th>
                            <th><?= lang('profit_loss'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('carssales/getProductsReport/0/xls/?v=1' . $v)?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
