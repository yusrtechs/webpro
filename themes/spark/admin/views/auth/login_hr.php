<?php defined('BASEPATH') or exit('No direct script access allowed');
 //$this->sma->print_arrays($post);
?>
<form name="target" id="target" action="<?=base_url().'hr/auth/process' ?>" method="post">
  <input type="hidden" value="<?= $post['company']; ?>"  name="company_name"/>
  <input type="hidden" value="<?= $post['identity']; ?>"  name="username"/>
  <input type="hidden" value="<?= $post['password']; ?>"  name="password"/>


</form>
<script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $( "#target" ).trigger( "submit" );
        });
    </script>
