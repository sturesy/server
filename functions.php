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

/**
 * Returns the voting type and corresponding information
 * @param name (string), lecture name
 */
function get_vote_type($name)
{
    global $database; 
	$name = $database->escape_string($name);
	$query = "SELECT question, type, answers, correctanswers, sturesy_lectures.date FROM sturesy_lectures, sturesy_question 
	           WHERE sturesy_question.lecture = id AND sturesy_lectures.lecture = '$name';";


	$result = $database->fetch_array($database->query($query));
	
	if(!$result)
	{
	   return -1;
	}
	else
	{
		return $result;
	}
}


/**
 * If the cookie is not set, set it and then return it
 */
function get_id_cookie()
{

	global $encryption_key;

	if(!isset($_COOKIE["id"]))
	{
		$id = "W".mt_rand(100000000, 999999999);
	
		$cry_id = fnEncrypt($encryption_key.','.$id.",".$encryption_key);
		
		setcookie('id', $cry_id , time() + 3600*3);
		
		return $id;
	}
	else // isset
	{	
		$id = fnDecrypt($_COOKIE['id']);
		
		$arra = explode(",", $id);
				
		if($arra[0] == $encryption_key && $arra[2] == $encryption_key)
		{
			return $arra[1];
		}
		else
		{
			return false;
		}
	}
}

/**
 * Reloads the Page displaying a success message depending on $success-value
 * @param $success type of message to display
 * <li> 1 - vote posted</li>
 * <li> 2 - no answer selected</li>
 * <li> 3 - no text input</li>
 * <li>else - vote already posted</li>
 *
 */
function reload_page($success)
{
	$msg;
	if($success == 1)
	{
		$msg = '<div class="alert alert-success"><h4><center>Vote posted!</center></h4></div>';
	}
	else if($success == 2)
	{
		$msg = '<div class="alert alert-error"><h4><center>Please select an answer!</center></h4></div>';
	}
	else if($success == 3)
	{
		$msg = '<div class="alert alert-error"><h4><center>Please provide an answer!</center></h4></div>';
	}
	else
	{
		$msg = '<div class="alert alert-error"><h4><center>Vote already posted!</center></h4></div>';
	}
?>
<body onLoad="JavaScript:timedRefresh(2000);">
<?php 
echo $msg ;
}


function no_lecture_id()
{
	$lectureid = $_REQUEST["lecture"];
	$msg;
	if(!isset($lectureid) || strlen($lectureid) == 0)
	{
		$msg = "Please enter a Lecture-ID";
	}
	else
	{
		$msg = 'There is currently no Voting with the provided Lecture-ID "<b>'.$lectureid.'</b>"';
	}
?>
	<div class="container" align="center">
		<form class="form-signin">
			<h2>Error</h2>
			<p class="red size20"><?php echo $msg; ?></p>
			<br>
			<button class="btn btn-warning btn-large" onClick="history.go(-1);return true;">
				<i class="icon-arrow-left icon-white"></i> Back
			</button>
		</form>
	</div>
<?php
	include_once("customize/footer.php");
}

function fnEncrypt($sValue)
{
	global $encryptionKey;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	return trim(base64_encode($iv.mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encryptionKey, $sValue, MCRYPT_MODE_CBC, $iv)));
}

function fnDecrypt($sValue)
{
	global $encryptionKey;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$sValue = base64_decode($sValue);
	$iv_dec = substr($sValue, 0, $iv_size);
	$ciphertext_dec = substr($sValue, $iv_size);
	$val = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $encryptionKey, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec));
	return $val;
}

function fetchLectureID($lecturename)
{
    global $database;
    $query = "SELECT * FROM sturesy_lectures WHERE lecture ='$lecturename'";
    $result = $database->query($query);    
    return $database->sql_result($result ,"id");
}

function verify_rest_message($message, $hmac)
{
	global  $encryption_key;
	return hash_hmac('SHA256', $message, $encryption_key) === $hmac;
}

?>