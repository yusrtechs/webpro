<?php
$host="http://localhost/arkanproshop/".$username."/auth.php";
?>
<form name="target" id="target" action="<?= $host; ?>" method="post">

				<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
									<input type="hidden" name="username" value="<?= $username; ?>">
									<input type="hidden" name="password" value="<?= $user_password; ?>">

</form>

<script>
    $(document).ready(function () {
    $( "#target" ).trigger( "submit" );
    });
</script>
