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

/*
 * CURRENT VERSION : 0.6.0 
 */

include("config.php");
include("functions.php");
include("mobile.php");

$ID = get_id_cookie();

if(!isset($_REQUEST["mobile"])) // normal webpage
{
	include_once("customize/header.php");
	include_once("customize/prebody.php");

	if(isset($_REQUEST["cmd"]) && isset($_REQUEST["type"]))
	{
		$res = $ID;
		if($ID)
		{
			switch($_REQUEST["type"])
			{
				case "s": $res = handle_single_vote(); break;
				case "m": $res = handle_multiple_vote(); break;
				case "t": $res = handle_text_vote(); break;
			}
				
		}
		reload_page($res); // see functions.php
	}
	else if(isset($_REQUEST["lecture"]))
	{
		$lecture = $_REQUEST["lecture"];

		$votetype = get_vote_type($lecture);

		if($votetype == -1)
		{
			die(no_lecture_id()); // see functions.php
		}
		else
		{
			display_voting($lecture,$votetype);
		}
	}
	else // display lecture-id Dialog
	{
		include_once("customize/login.php");
	}
	include_once("customize/footer.php");
}
else
{
	mobile(); // see mobile.php
}

function display_voting($lecture, $votetype)
{
	display_question($lecture, $votetype);
	switch($votetype[1])
	{
		case "singlechoice" : display_single_choice($lecture, $votetype);break;

		case "multiplechoice" : display_multiple_choice($lecture, $votetype);break;

		case "textchoice": display_text_choice($lecture, $votetype); break;
	}
}

function display_single_choice($lecture, $votetype)
{
	$answers = json_decode($votetype[2]);

	echo '<table class="table table-condensed" align="center">
			<tr>';
	for ($i = 0; $i < count($answers); $i++)
	{
		echo get_vote_button($lecture,$i);

		if(($i+1) % 2 == 0)
		{
			echo "</tr><tr>";
		}
	}
	echo '</tr>
			<tr>
			<td colspan="2" style="text-align:center;">
			<button type="submit" class="btn btn-large" onclick="reload();">
			<i class="icon-refresh"></i> Reload
			</button>
			</td>
			</tr>
			</table>';
}

function display_multiple_choice($lecture, $votetype)
{
	$answers = json_decode($votetype[2]);
	 
	echo '<form method="post" action="index.php?lecture='.$lecture.'">
			<input type="hidden" name="cmd" value="multiple"/>
			<input type="hidden" name="type" value="m"/>
			<table class="table table-condensed" align="center">
			<tr>';
	for ($i = 0; $i < count($answers); $i++)
	{
		$c = chr(65+$i);
		echo "<td>
		<center>
		<input type='checkbox' id='check$i' name='$c' value='true'><label for='check$i'>$c</label>
		</center>
		</td>";
		if(($i+1) % 2 == 0)
		{
			echo "</tr><tr>";
		}
	}

	echo '</tr>
			<tr>
			<td colspan="2" style="text-align:center;">
			<button type="submit" class="btn btn-large">
			<i class="icon-ok"></i> Submit
			</button>
			</td>
			</tr>
			</table>
			</form>
			<table class="table table-condensed" align="center"><tr>
			<td colspan="2" style="text-align:center;">
			<button type="button" class="btn btn-large" onclick="reload();">
			<i class="icon-refresh"></i> Reload
			</button>
			</td>
			</tr>
			</table>';

}


function display_text_choice($lecture, $votetype)
{
	?>
<br>
<center>
	<form method="post" action="index.php?lecture=<?php echo $lecture;?>">
		<label>Answer:</label> <input type="text" placeholder="" name="text">
		<input type="hidden" name="cmd" value="text" /> <input type="hidden"
			name="type" value="t" /><br> <input type="submit"
			class="btn btn-large" value="Submit" />
	</form>
	<br>


	<button type="submit" class="btn btn-large" onclick="reload();">
		<i class="icon-refresh"></i> Reload
	</button>
</center>
<?php
}

function display_question($lecture, $votetype)
{
	echo '<p class="text-center"><small>Lecture-ID: '.$lecture.'</small></p>
		<h4><center>'.$votetype[0].'</center></h4>';
}

function get_vote_button($lecture,$value)
{

	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
	$iv = str_replace(",", "-", $iv);
	global $ID;
	$lol = fnEncrypt($iv.",".$ID.',vote,'.$value);


	return '<td>
		<form method="post" action="index.php?lecture='.$lecture.'">
		<input type="hidden" name="cmd" value="'.$lol.'"/>
		<input type="hidden" name="type" value="s"/>
		<input type="submit" class="btn btn-large btn-block" value="'.chr(65+$value).'" />
		</form>
		</td>';
}


function handle_single_vote()
{
	global $ID;
	global $_REQUEST;
	$stuff = explode(",", fnDecrypt($_REQUEST['cmd']));

	if($stuff[1]==$ID && $stuff[2]=='vote')
	{
		$vote = $stuff[3];
		return post_vote($_REQUEST["lecture"], $ID, $vote);
	}
	else return false;
}

function handle_multiple_vote()
{
	global $ID;
	global $_REQUEST;

	$votes = array();
	for($i = 0; $i < 10; $i++)
	{
		$ch = chr(65+$i);
		if(isset($_REQUEST[$ch]))
		{
			array_push($votes, $i);
		}
	}

	if(sizeof($votes) > 0)
	{
		return post_vote($_REQUEST["lecture"], $ID, json_encode($votes));
	}
	else
	{
		return 2;
	}
	return true;
}


function handle_text_vote()
{
	global $ID;
	global $_REQUEST;

	if(isset($_REQUEST["text"]) && strlen($_REQUEST["text"]) > 0)
	{
		return post_vote($_REQUEST["lecture"], $ID, json_encode($_REQUEST["text"]));
	}
	else
	{
		return 3;
	}
}

/**
 * Posts a Vote for a Lecture
 * @param name (string), lecture name
 * @param id (string), the provided device id
 * @param vote (int), the submitted vote
 */
function post_vote($name, $id, $vote)
{
	global $database;
	$name = $database->escape_string($name);
	$name = fetchLectureID($name);

	$id = $database->escape_string($id);
	$vote = $database->escape_string($vote);

	if($vote >= 1 && $vote <= 10 || strpos($vote, "[")!= -1)
	{
		$query = "INSERT INTO sturesy_votes (lid, guid, vote, date) VALUES ($name, '$id', '$vote', NOW());";
		$result = $database->query($query);
		return ($result == "1");
	}
	else
	{
		return false;
	}
}


?>