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
            <button type="submit" class="btn btn-default btn-primary">Vote</button>
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
