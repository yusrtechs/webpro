<?php defined('BASEPATH') or exit('No direct script access allowed');
 //$this->sma->print_arrays($post);
?>
<form name="target" id="target" action="https://companypro.com.sa/crm/admin/authentication/login" method="post">
  <input type="hidden" value="<?= $post['company']; ?>"  name="company_name"/>
  <input type="hidden" value="<?= $post['email']; ?>"  name="email"/>
  <input type="hidden" value="<?= $post['password']; ?>"  name="password"/>
  <input type="hidden" value="0" name="remember">

</form>
<script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $( "#target" ).trigger( "submit" );
        });
    </script>
