<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
#treeview_json .node-disabled {
    display: none;
}
.treeview span.icon {
    width: 14px;
    margin-inline: 4px;
}
.ui-widget-header {
    border: none;
    background: var(--spark-surface-subtle, #888);
    color: var(--spark-text-primary, #ffffff);
    font-weight: bold;
    text-align: center;
}
.ui-widget-content {
    border: none;
    background: transparent;
    color: inherit;
}
</style>

    <!--Required scripts-->
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/modernizr-2.8.3.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/jquery-ui.min2.js"></script>

    <!-- External files for exporting -->
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/FileSaver.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/Blob.js"></script>


    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.core.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.lob.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.ext_core.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.ext_collections.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.ext_text.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.ext_io.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.ext_ui.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.documents.core_core.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.excel_core.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.xml.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.documents.core_openxml.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.excel_serialization_openxml.js"></script>
    <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/infragistics/infragistics.gridexcelexporter.js"></script>

<?php

$levels=$this->input->post('levels') ? $this->input->post('levels') : NULL;
?>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
        <?php if (!$this->input->post('levels') && !$this->input->post('submit_report')) { ?>
            $('#form').hide();
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideToggle(200);
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp(200);
            return false;
        });
    });
</script>

  <script type="text/javascript" charset="utf8" src="<?= $assets ?>js/bootstrap-treeview.min.js"></script>

<div class="spark-accounts-tree-page">
    <div class="spark-accounts-tree-header">
        <div class="spark-accounts-tree-header-start">
            <div class="spark-accounts-tree-header-icon">
                <i class="fa fa-sitemap"></i>
            </div>
            <div class="spark-accounts-tree-header-info">
                <h1 class="spark-accounts-tree-title">
                    <?= lang('Accounts_tree'); ?>
                </h1>
                <p class="spark-accounts-tree-subtitle">
                    <?= lang('manage_accounts_tree_desc'); ?>
                </p>
            </div>
        </div>

        <div class="spark-accounts-tree-header-actions">
            <button type="button" class="spark-btn-secondary-action toggle_down tip" title="<?= lang('show_form') ?>">
                <i class="fa fa-filter"></i>
                <span><?= lang('filter'); ?></span>
            </button>

            <button type="button" id="xls" class="spark-btn-secondary-action tip" title="<?= lang('export_to_excel') ?>" style="cursor: pointer;">
                <i class="fa fa-file-excel-o"></i>
                <span><?= lang('export_to_excel'); ?></span>
            </button>
        </div>
    </div>

  <div class="box">



<div class="container">
<div class="row">
	<div class="col-sm-12">
    <table id="treegrid1"  style="display:none;"></table>
    <div id="form">

                    <?php echo admin_form_open('accounts/accounts_tree',' autocomplete="off"'); ?>
                    <div class="col-sm-12">




						<div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="levels"><?= lang('Levels'); ?></label>
                                <?php
                                $levels_arr['0'] = lang('all');
                                $levels_arr['1'] = lang('Level_1');
                                $levels_arr['2'] = lang('Level_2');
                                $levels_arr['3'] = lang('Level_3');
                                $levels_arr['4'] = lang('Level_4');
                                $levels_arr['5'] = lang('Level_5');
                                $levels_arr['6'] = lang('Level_6');
                                $levels_arr['7'] = lang('Level_7');

                                echo form_dropdown('levels', $levels_arr, (isset($_POST['levels']) ? $_POST['levels'] : ''), 'class="form-control" id="levels" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('Levels') . '"');
                                ?>
                            </div>
                        </div>



					</div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>






	</div>
	</div>
  <div class="panel panel-default">
    <div class="panel-heading">
	<p class="introtext"><?=lang('tree_results');?></p>

      </div>











    <div class="panel-body">

      <div class="row">
        <div class="col-sm-4">
          <!-- <form> -->
            <div class="form-group">
			  <input type="input" class="form-control" placeholder="<?= lang('Search') ?>" id="input-search"  value="">
            </div>

          <!-- </form> -->
        </div>

      </div>

	  <div class="col-md-12" id="treeview_json">

      </div>
    </div>
  </div>
</div>
</div>
</div>


<script type="text/javascript">
$(document).ready(function(){

   var treeData;
   var levels='<?= $levels ?>';
   if(!levels)
	   levels=100;

   $.ajax({
        type: "GET",
        url: '<?= admin_url('Accounts/getAccountItem'); ?>',

        dataType: "json",
        success: function(response)
        {

           initTree(response)
        }
  });

  function initTree(treeData) {

$('#treeview_json').treeview({
    data: treeData,         // data is not optional
    levels: levels,
});



$("#treegrid1").igTreeGrid({
                width: "100%",
                dataSource: treeData, //Project Plan data,
                autoGenerateColumns: false,
                primaryKey: "id",
                columns: [
                    { headerText: "ID", key: "id", width: "120px", dataType: "number" },
                    { headerText: "<?= lang('Account_No'); ?>", key: "accountID", width: "130px", dataType: "string" },
                    { headerText: "<?= lang('Account_name'); ?>", key: "name", width: "250px", dataType: "string" }

                ],
                childDataKey: "nodes",
                initialExpandDepth: 1,
                renderExpansionIndicatorColumn: true
            });

/*var treeData = $(selectors.tree).treeview(true);
			//console.log(treeData.getNode(13));
			console.log(treeData);
 */
  }

  function deleteAccount() {
	 $('#treeview_json').treeview({data: treeData});
  }



  $("#xls").on("click", function () {
                $.ig.GridExcelExporter.exportGrid($("#treegrid1"), {
                    fileName: "<?= lang('Accounts_tree') ?>"
                });
            });


});


//https://github.com/jonmiles/bootstrap-treeview

//https://raw.githubusercontent.com/jonmiles/bootstrap-treeview/master/public/js/bootstrap-treeview.js
//https://rawgit.com/jonmiles/bootstrap-treeview/master/public/js/bootstrap-treeview.js

//https://raw.githubusercontent.com/jonmiles/bootstrap-treeview/master/public/css/bootstrap-treeview.css
//https://rawgit.com/jonmiles/bootstrap-treeview/master/public/css/bootstrap-treeview.css

// Dependencies
//Bootstrap v3.3.4 (>= 3.0.0)
//jQuery v2.1.3 (>= 1.9.0)


// Add this suggested feature to hide non-matched elements on the search (parent chain should remain visible)
// https://github.com/jonmiles/bootstrap-treeview/issues/101


/*
var search = function(e) {
  var pattern = $('#input-search').val();
  var options = {
    ignoreCase: $('#chk-ignore-case').is(':checked'),
    exactMatch: $('#chk-exact-match').is(':checked'),
    revealResults: $('#chk-reveal-results').is(':checked')
  };
  var results = $searchableTree.treeview('search', [ pattern, options ]);

  var output = '<p>' + results.length + ' matches found</p>';
  $.each(results, function (index, result) {
    output += '<p>- ' + result.text + '</p>';
  });
  $('#search-output').html(output);
}

$('#btn-search').on('click', search);
$('#input-search').on('keyup', search);

$('#btn-clear-search').on('click', function (e) {
  $searchableTree.treeview('clearSearch');
  $('#input-search').val('');
  $('#search-output').html('');
});
*/

//$(function () {
    var selectors = {
        'tree': '#treeview_json',
        'input': '#input-search',
        'reset': '#btn-clear-search'
    };


    var lastPattern = ''; // closure variable to prevent redundant operation

    // collapse and enable all before search //
    function reset(tree) {
        tree.collapseAll();
        tree.enableAll();
    }

    // find all nodes that are not related to search and should be disabled:
    // This excludes found nodes, their children and their parents.
    // Call this after collapsing all nodes and letting search() reveal.
    //
    function collectUnrelated(nodes) {
        var unrelated = [];
        $.each(nodes, function (i, n) {
            if (!n.searchResult && !n.state.expanded) { // no hit, no parent
                unrelated.push(n.nodeId);
            }
            if (!n.searchResult && n.nodes) { // recurse for non-result children
                $.merge(unrelated, collectUnrelated(n.nodes));
            }
        });
        return unrelated;
    }

    // search callback
    var search = function (e) {
        var pattern = $(selectors.input).val();
        if (pattern === lastPattern) {
            return;
        }
        lastPattern = pattern;
        var tree = $(selectors.tree).treeview(true);
		//console.log(tree);

        reset(tree);
        if (pattern.length < 3) { // avoid heavy operation
            tree.clearSearch();
        } else {
            tree.search(pattern);
            // get all root nodes: node 0 who is assumed to be
            //   a root node, and all siblings of node 0.
            var roots = tree.getSiblings(0);

            roots.push(tree.getNode(0));
            //first collect all nodes to disable, then call disable once.
             //  Calling disable on each of them directly is extremely slow!
            var unrelated = collectUnrelated(roots);
            tree.disableNode(unrelated, {silent: true});
        }
    };

    // typing in search field
    $(selectors.input).on('keyup', search);

    // clear button
    $(selectors.reset).on('click', function (e) {
        $(selectors.input).val('');
        var tree = $(selectors.tree).treeview(true);
        reset(tree);
        tree.clearSearch();
    });
//});

</script>

