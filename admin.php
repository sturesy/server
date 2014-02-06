<?php 
/*
 * StuReSy - Student Response System
 * Copyright (C) 2012-2013  StuReSy-Team
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
 
include("config.php");

session_start();

if(!isset($_REQUEST["query"]))
{
	include_once("customize/header.php"); 
	include_once("customize/prebody.php"); 

	if(isset($_GET["logout"]))
	{
		$bool = session_destroy();
		$_SESSION = NULL;
		if($bool)
			show_success("Logged out successfully!<br/>
						<small>This page will refresh automatically in 3 seconds or <a href='admin.php'>click here</a></small>
						<meta http-equiv='refresh' content='3; url=admin.php'/>");
		else
			show_error("There was a problem logging out");
	}
	else
	{
		if (isset($_POST["login_user"]) && isset($_POST["login_password"]))
		{
			if ("admin" == $_POST["login_user"] &&  $admin_password == $_POST["login_password"])
		    {
		    	$_SESSION["login"] = 1;
		    	$_GET = NULL;
		    }
		    else
		    {
			    show_error("Wrong login information provided");
		    }
		}
		
		if (!isset($_SESSION["login"]) || $_SESSION["login"] != 1) // NO VALID SESSION
		{
			show_login_form();
		}
		else // VALID SESSION
		{
			show_navbar();
			if(isset($_GET["create"]))
			{
				if(isset($_POST["lecture"]) && isset($_POST["password"]) && isset($_POST["owner"]))
				{
					insert_lecture();
				}
					show_create_dialog();
			}
			else if (isset($_GET["overview"]))
			{
				echo "<div id='overviewtable'>";
				show_lectures('');
				echo "</div>";
			}
			else
			{
				echo "<h2>Administration</h2><br/>Welcome to the Administration page";
			}
	
			echo "</div></div>";// close navbar
		}
	}
?><script type="text/javascript">function orderTable(order){$("#overviewtable").load("?query=table&order="+order);}
function renewToken(l,o,d){var loadURL = "?query=renewtoken&l="+l+"&o="+o+"&d="+d;$.ajax({url: loadURL}).done(function() {orderTable("lecture");});}
</script><?php

	include_once("customize/footer.php");
}
else
{
	if (isset($_SESSION["login"]) && $_SESSION["login"] == 1)
	{
		$requestQuery = $_REQUEST["query"];
		if($requestQuery == "table")
		{
			$orderby='';
			if(isset($_REQUEST["order"]))
			{
				$orderby = 'ORDER BY '.$database->escape_string($_REQUEST['order']);
			}
			show_lectures($orderby);
		}
		else if($requestQuery == "renewtoken")
		{
			if(isset($_REQUEST["l"]) && isset($_REQUEST["o"]) && isset($_REQUEST["d"]))
			{
				generate_new_token($_REQUEST["l"],$_REQUEST["o"],$_REQUEST["d"]);
			}
		}
	}
	else
	{
		die("Invalid Session");
	}
}




// ======================================================================================================
// Functions
// ======================================================================================================
function show_navbar()
{
?>
<div class="row-fluid">
	<div class="span2">
    	<div class="well sidebar-nav">
        	<ul class="nav nav-list">
              <li class="nav-header">Administration</li>
              <li><a href="?create">Creation</a></li>
              <li><a href="?overview">Overview</a></li>
              <li><a href="?logout">Logout</a></li>
            </ul>
        </div>
    </div>
    <div class="span9" id="pagebody">
<?php
} 
 
 
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
 
function insert_lecture()
{
	global $database;
	$lecture = $_REQUEST["lecture"];
	$pw = $_REQUEST["password"];
	$owner = $_REQUEST["owner"];
	if($lecture != "" && $pw != "" && $owner != "")
	{
		$result = create_lecture($lecture, $pw, $owner, $database);
		
		if($database->error() == $database->db_no_error())
		{
			$token = sha1($lecture.$pw.$owner);
			$lb = "%0D%0A%0D%0A";
			show_success("Token: <b>".$token.
			'</b><br><a href="mailto:?body=Hello '.$owner.'!'.$lb.
			'I have created the Lecture-ID: '.$lecture.' for you.'.$lb.
			'Please redeem the following Token from the StuReSy-Settings:'.$lb.
			$token.'">Send Token via eMail</a>');
		}
		else
		{
			$error = (strpos($database->error(),"Duplicate") !== false)? "Duplicate Lecture-ID <b>".$lecture."</b>" : "";		
			show_error($error);
		}
	}
	else
	{
		show_error("All fields are necessary");	
	}
} 
 
/**
 * Creates the Table with the necessary input fields
 */
function show_create_dialog()
{
?>
<form method="post" id="createdialog">
	<h2>Lecture-ID Creation</h2>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input type="text" name="lecture" placeholder="Lecture-ID"/>
	</div><br/>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input type="password" name="password" placeholder="Password for Lecture"/>
	</div><br/>
	<div class="input-prepend">
		<span class="add-on"><i class="icon-chevron-right"></i></span>
		<input type="text" name="owner" placeholder="Owner of Lecture"/>
	</div><br/>
	<input type="submit" class="btn btn-primary" value="Create Lecture-ID"/>
</form>
<?php
}

function show_success($msg)
{
?>
<div class="alert alert-success"><center><h2>Success</h2><?php echo $msg ?></center></div>
<?php
}

function show_error($msg)
{
?>
<div class="alert alert-error"><center><h2>Error</h2><?php echo $msg ?></center></div>
<?php
}

/**
 *  Creates a new LectureID in the Database
 *  @param name (string), lecturename
 *  @param pwd (string), password
 *  @param owner (string), name of owner
 */
function create_lecture($name, $pwd, $owner, $db)
{
  $name = $db->escape_string($name);
  $pwd  = $db->escape_string($pwd);
  $owner = $db->escape_string($owner);
  
  $query = sprintf("INSERT INTO sturesy_lectures (lecture, password, owner, date, token) VALUES ('%s', '%s', '%s', '%s', '%s')",
    $name,$pwd,$owner,date("Y-m-d H:i:s"), sha1($name.$pwd.$owner));

  $result = $db->query($query);
}



function show_lectures($orderby)
{
	global $database;
	$query = "SELECT lecture,owner,date,token FROM sturesy_lectures $orderby";
	$result = $database->query($query);
?>
<h2>Lecture-ID Overview</h2>
<style>.table th, .table td { border-top: 1px ridge grey; }</style>
<table class='table table-striped table-bordered'>
<tr>
	<th><p class='text-center'><a href="#" onclick="orderTable('lecture');">Lecture-ID</a></p></th>
	<th><p class='text-center'><a href="#" onclick="orderTable('owner');">Owner</a></p></th>
	<th><p class='text-center'><a href="#" onclick="orderTable('date');">Last used</a></p></th>
	<th><p class='text-center'><a href="#" onclick="orderTable('token');">Token</a></p></th>
</tr>
<tbody>
<?php
	while($row = $database->fetch_array($result))
	{
		$token = $row[3];
		if(!$token)
		{
			$token = "<span class='label'>Token already redeemed</span><button class='btn btn-mini' onclick=\"renewToken('$row[0]','$row[1]','$row[2]')\">Renew Token</button>";
		}
		else
		{
			$token ="<span class='label label-info'>Token: $token</span>" ;
		}
		echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$token</td></tr>\n";
	}
	
	echo "</tbody></table>";
}


function generate_new_token($lecture,$owner,$date)
{
	global $database;
	$lecture = $database->escape_string($lecture);
	$owner = $database->escape_string($owner);
	$token = sha1($lecture.$owner.$date);
	
	$query = "UPDATE sturesy_lectures SET token='$token' WHERE lecture='$lecture' AND owner='$owner'";
	$result = $database->query($query);
}

?>