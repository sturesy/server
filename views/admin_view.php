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


/**
 * Shows the navigationbar
 */
function show_navbar()
{
    ?>
<div class="row-fluid"> <!-- row-fluid begin-->
	<div class="span2"><!-- navbar begin -->
    	<div class="well sidebar-nav">
        	<ul class="nav nav-list">
              <li class="nav-header">Administration</li>
              <li><a href="?admin=creation">Creation</a></li>
              <li><a href="?admin=overview">Overview</a></li>
              <li><a href="?admin=logout">Logout</a></li>
            </ul>
        </div>
    </div><!-- navbar end -->
    <div class="span9" id="pagebody"> <!-- pagebody begin -->
<?php
} 


function close_page_body()
{
?>
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
<form method="post" id="createdialog">
	<h2>Lecture-ID Creation</h2>
	<div class="input-prepend input-append">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input type="text" name="lecture" placeholder="Lecture-ID" value="<?php echo $fieldvalues[0]?>" id="lecturefield"/>
		<span class="add-on" id="lecturefree" style="visibility:hidden" >text</span>
	</div>
	<br/>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input type="password" name="password" placeholder="Password for Lecture" value="<?php echo $fieldvalues[1]?>"/>
	</div>
	<br/>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input type="text" name="owner" placeholder="Owner of Lecture" value="<?php echo $fieldvalues[2]?>"/>
	</div>
	<br/>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input type="text" name="email" placeholder="eMail" value="<?php echo $fieldvalues[3]?>"/>
	</div>
	<br/>
	<input type="submit" class="btn btn-primary" value="Create Lecture-ID"/>
</form>
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