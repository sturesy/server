<?php




function show_login_form()
{
    ?>
<form class="form-signin" method="post">
	<h2 class="form-signin-heading">Admin Panel</h2>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input class="span3" type="text" name="login_user" placeholder="Username"/>
	</div>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input class="span3" type="password" name="login_password" placeholder="Password"/>
	</div>
	<input type="submit" class="btn btn-primary btn-large btn-block" value="Login"/>
</form>
<?php
} 




?>