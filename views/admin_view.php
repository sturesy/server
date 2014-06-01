<?php
/* 
 * StuReSy - Student Response System
 * Copyright (C) 2012-2014  StuReSy-Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

global $INDEXPHPWASACCESSED;
if($INDEXPHPWASACCESSED !== true)
{
    die('<meta http-equiv="refresh" content="0; url=../index.php" />');
}


/**
 * Shows the login form
 */
function show_login_form()
{
    ?>
<form class="form-signin" method="post">
	<h2 class="form-signin-heading">Admin Panel</h2>
	<div class="input-group">
		<span class="input-group-addon"><i class="glyphicon glyphicon-chevron-right"></i></span>
		<input class="form-control" name="login_user" placeholder="Username" type="text">
	</div>
    <p></p>
	<div class="input-group">
		<span class="input-group-addon"><i class="glyphicon glyphicon-chevron-right"></i></span>
		<input class="form-control" name="login_password" placeholder="Password" type="password">
	</div>
    <p></p>
	<input class="btn btn-primary btn-block btn-lg" value="Login" type="submit">
</form>
<?php
} 


/**
 * Shows the navigationbar
 */
function show_navbar()
{
    ?>
<div class="container-fluid">
    <div class="row"> <!-- row-fluid begin-->
	<div class="col-xs-5 col-sd-3 col-md-3 col-lg-2"><!-- navbar begin -->
    	<div class="well sidebar">
        	<ul class="nav nav-sidebar">
              <li class="text-center"><h4>Administration</h4></li>
              <li><a href="?admin=creation">Creation</a></li>
              <li><a href="?admin=overview">Overview</a></li>
              <li><a href="?admin=logout" style="color:red;">Logout</a></li>
            </ul>
        </div>
    </div><!-- navbar end -->
    <div class="col-xs-7 col-sd-9 col-md-9 col-lg-10" id="pagebody"> <!-- pagebody begin -->
<?php
} 


function close_page_body()
{
?>
        </div>
    </div><!-- pagebody end -->
</div><!-- row-fluid end-->
<?php 
}


/**
 * Shows the view for creating a new lecture
 */
function show_lecture_id_create_dialog($fieldvalues = array("","","",""))
{
?>
<div class="col-sd-4 col-md-4 col-lg-4">
<form method="post" id="createdialog">
	<h2>Lecture-ID Creation</h2>
	<div class="input-group input-prepend input-append">
		<span class="input-group-addon"><i class="glyphicon glyphicon-chevron-right"></i></span>
		<input class="form-control" type="text" name="lecture" placeholder="Lecture-ID" value="<?php echo $fieldvalues[0]?>" id="lecturefield" required="required"/>
		<span class="input-group-addon" id="lecturefree"></span>
	</div>
	<br/>
	<div class="input-group input-prepend">
		<span class="input-group-addon"><i class="glyphicon glyphicon-chevron-right"></i></span>
		<input class="form-control" type="password" name="password" placeholder="Password for Lecture" value="<?php echo $fieldvalues[1]?>" required="required"/>
	</div>
	<br/>
	<div class="input-group input-prepend">
		<span class="input-group-addon"><i class="glyphicon glyphicon-chevron-right"></i></span>
		<input class="form-control" type="text" name="owner" placeholder="Owner of Lecture" value="<?php echo $fieldvalues[2]?>" required="required"/>
	</div>
	<br/>
	<div class="input-group input-prepend">
		<span class="input-group-addon"><i class="glyphicon glyphicon-chevron-right"></i></span>
		<input class="form-control" type="email" name="email" placeholder="eMail" value="<?php echo $fieldvalues[3]?>" required="required"/>
	</div>
	<br/>
	<input type="submit" class="btn btn-primary" value="Create Lecture-ID"/>
</form>
</div>
<?php
}


function show_welcome_screen()
{
?>
<h2>Administration</h2>
<p>Welcome to the Administration page!<p>
<?php 
}


function show_lecture_table($values, $includeDiv = false)
{
    if($includeDiv)
    {
        echo "<div id='overviewtable'>";        
    }
?>
<h2>Lecture-ID Overview</h2>
<table class="table table-striped table-bordered">
    <tr>
    	<th><p class="text-center"><a href="#" onclick="orderTable('lecture');">Lecture-ID</a></p></th>
    	<th><p class="text-center"><a href="#" onclick="orderTable('owner');">Owner</a></p></th>
    	<th><p class="text-center"><a href="#" onclick="orderTable('email');">eMail</a></p></th>
    	<th><p class="text-center"><a href="#" onclick="orderTable('date');">Last used</a></p></th>
    	<th><p class="text-center"><a href="#" onclick="orderTable('token');">Token</a></p></th>
    </tr>
    <tbody>
<?php

foreach ($values as $row)
{
    $token = $row[4];
    if(!$token)
    {
        $token = "<span class='label'>Token already redeemed</span><button class='btn btn-mini' onclick=\"renewToken('$row[0]','$row[1]')\">Renew Token</button>";
    }
    else
    {
        $token ="<span class='label label-info'>Token: $token</span>" ;
    }
    ?>
    <tr>
        <td><?php echo $row[0]?></td>
        <td><?php echo $row[1]?></td>
        <td><?php echo $row[2]?></td>
        <td><p class="text-center"><?php echo $row[3]?></p></td>
        <td><?php echo $token?></td>
    </tr>
    <?php 
}
?>
</tbody>
</table>
<?php 
    if($includeDiv)
    {
        echo "</div>";
    }
}



?>