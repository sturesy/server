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

include("config.php");
include_once("functions.php");

global $connection; // TODO REPLACE DATABASE CLASS

if(isset($_REQUEST["data"]) && isset($_REQUEST["hash"]))
{
    $key = $_REQUEST["hash"];
    $json = json_decode(base64_decode($_REQUEST["data"]), true);

    $trusted = verify_integrity($_REQUEST["data"],$json, $key);    

    if($trusted)
    {
        parseJSON($json);
    }
    else
    {
        die("untrusted");
    }
}
else
{
    header('Location: index.php') ;
}


function parseJSON($json)
{
    if(isset($json) && isset($json["command"])) 
    {
        switch($json["command"])
        {
        	case "info": info($json);
        				break;
        	case "update": update($json);
        				break;
        	case "get": get($json);
        				break;
        	case "clean": clean($json);
        				break;
        	case "redeem": redeem($json);
        				break;
        }
    }
}


function info($json)
{
    echo "sturesy 0.6.0";
}

function get($json)
{
    if(isset($json["name"]))
    {
        echo query_all_votes($json["name"]);
    }
}

function update($json)
{
    update_lecture_type($json);
}

function clean($json)
{
    if(isset($json["name"]))
    {
        echo clean_votes($json);
    }
}

function redeem($json)
{
	if(isset($json["token"]))
	{
		echo redeem_token($json["token"]);
	}
}

/**
 * Returns the Votes of the specified lecture
 * in format: guid,vote;guid,vote; 
 */ 
function query_all_votes($name)
{
    global $database;
    $name = $database->escape_string($name);
  
    $query = "SELECT sturesy_votes.guid, sturesy_votes.vote, sturesy_votes.date, sturesy_votes.lid
              FROM sturesy_lectures, sturesy_votes
              WHERE lecture ='$name' 
              AND id = lid
              AND fetched != 1 ;";
            
    $result = $database->query($query);
  
    $returnval = "";
    $inarray ="";

    $rows = array();
    
    while($row = $database->fetch_array($result))
    { 
        $returnval .=  $row[0].",".$row[1].",".$row[2].";";
     
        $inarray .= "'".$row[0]."',";
        $id = $row[3];
        //array_push($rows, $row);
        array_push($rows, array($row[0],json_decode($row[1]),$row[2]));
    } 
         
    if(strlen($returnval) > 0)
    {
        $inarray = substr($inarray, 0, -1); //remove last ","  
        $updatequery = "UPDATE sturesy_votes SET fetched = 1 WHERE lid = '$id' AND guid in ($inarray) ";
        $database->query($updatequery);
        return json_encode($rows, JSON_FORCE_OBJECT);
    }
    else
    {
        return "No Data";    
    }
}

/**
 * Removes all the Votes from the Database, when a Voting has finished
 */  
function clean_votes($json)
{
    global $database;
    $name = $database->escape_string($json["name"]);
    
    $quer = "DELETE FROM sturesy_votes using sturesy_lectures inner join sturesy_votes 
            on (sturesy_lectures.id = sturesy_votes.lid) 
            where sturesy_lectures.lecture ='$name';";
   
    $database->query($quer);
}

/**
 * Updates a lecture
 * $json JSON formatted string
 */
function update_lecture_type($json)
{
    global $database;
    $name = $database->escape_string($json["name"]);
    
    $lectureid = fetchLectureID($name);

    $question = $database->escape_string($json["question"]);
    
    $type = $database->escape_string($json["type"]);
   
    $answers = $database->escape_string(json_encode($json["answers"]));
      
    $correct = $database->escape_string(json_encode($json["answer"]));
        
    $query = "INSERT INTO sturesy_question (lecture,type,question,answers,correctanswers)
            VALUES ($lectureid, '$type', '$question', '$answers', '$correct') 
            ON DUPLICATE KEY UPDATE type = '$type', question = '$question' , answers = '$answers' , correctanswers = '$correct'";
                
    $query2 = "DELETE FROM sturesy_votes WHERE lid = $lectureid";
    
    $query3 = "UPDATE sturesy_lectures SET date=NOW() WHERE id=$lectureid";
          
    $result = $database->query($query);
    if($result)
    {
    	$database->query($query2);
    	$database->query($query3);
    }
	//$database->sql_result($result,0);
}

function redeem_token($token)
{
    global $database;
	$token = $database->escape_string($token);
	
	if($token == "" || strlen($token) < 20) // token should actually be 40chars, but whatever
		return;
	
	$query = sprintf("SELECT lecture, password FROM sturesy_lectures WHERE token ='%s'",$token);
	
	$queryresult = $database->query($query);
	
	$remquery = sprintf("UPDATE sturesy_lectures SET token='' WHERE token ='%s'",$token);
	
	$database->query($remquery);

	$result ="";
	while($row = $database->fetch_array($queryresult))
	{
		$result .= $row[0].";".$row[1];
		break;
	}
	echo $result;
	
}

function verify_integrity($base64, $json, $hash)
{
    global $database; 
    
    if(isset($json["time"]))
    {
        if(time() - (int)($json["time"]) > 4)
            return false; // only 4 seconds time difference between sending
    }
    else
    {
        return false;
    }

    if($json["command"] == "info" || $json["command"] == "redeem")
    {
        $lkey = "info";
    }
    else
    {
        $lecturename = $database->escape_string($json["name"]);
        $lkey = fetchKey($lecturename);
    }
    $sha = hash_hmac("SHA256", $base64, $lkey); 

    return ($sha === $hash);
}

function fetchKey($lecturename)
{
    global $database;
    $query = "SELECT password FROM sturesy_lectures WHERE lecture ='$lecturename'";
    return $database->sql_result($database->query($query),"password");
}
?>