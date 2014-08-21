<?php
// Display alert messages (if there are any)
if(isset($_SESSION["alert"])) {
    $message = $_SESSION["alert"];
    echo "<div class=\"container\">";
    echo "<div class=\"alert alert-info\">$message<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button></div>";
    echo "</div>";
    unset($_SESSION["alert"]);
}
?>
<form class="form-signin" id="loginform">
    <input type="hidden" name="action" id="action" value="vote">
	<h2 class="form-signin-heading text-center">Vote</h2>
	<p class="text-center">Please enter a Lecture-ID</p>
	<div class="input-group">
		<span class="input-group-addon glyphicon glyphicon-share-alt"></span>
		<input class="form-control" name="lecture" placeholder="Lecture-ID" type="text">
	</div>
	<br>
	<br>
    <div class="text-center">
        <div class="btn-group">
            <button type="submit" onclick="$('#action').val('vote'); $('#loginform').submit()" class="btn btn-default btn-primary">Vote</button>
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-primary dropdown-toggle" data-toggle="dropdown">
                    Feedback
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu text-left">
                    <li onclick="$('#action').val('feedback_sheet'); $('#loginform').submit()"><a href="#">Fill out Feedback Sheet</a></li>
                    <li onclick="$('#action').val('feedback_live'); $('#loginform').submit()"><a href="#">Live Feedback</a></li>
                </ul>
            </div>
        </div>
    </div>
</form>
