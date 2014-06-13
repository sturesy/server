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

global $connection;

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
        if(isset($json["target"]))
        {
            if($json["target"] == "fbsheet")
                echo get_feedback_sheet($json);
        }
        else
            echo query_all_votes($json["name"]);
    }
}

function update($json)
{
    if(isset($json["target"]) && $json["target"] == "fbsheet")
        update_feedback_sheet($json);
    else
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
    global $connection;
    
    $result = $connection->getVotesForLectureAndMarkFetched($name);
    
    if($result === false)
    {
        echo "No Data";
    }
    else
    {
        echo json_encode($result, JSON_FORCE_OBJECT);
    }
}

/**
 * Removes all the Votes from the Database, when a Voting has finished
 */  
function clean_votes($json)
{
    global $connection;
    
    $connection->removeVotesForLecture($json["name"]);
}

/**
 * Updates a lecture
 * $json JSON formatted string
 */
function update_lecture_type($json)
{
    global $connection;
    $connection->updateQuestionForLecture($json["name"], $json["question"], $json["answers"], $json["type"],$json["answer"]);
}

function redeem_token($token)
{
	
	if($token == "" || strlen($token) < 20) // token should actually be 40chars, but whatever
		return;
	
    global $connection;
    echo $connection->fetchInformationForTokenRedemption($token);	
	
}
/**
 * Check that given base64-string corresponding json-object and
 * given HASH are matching the calculated values by retrieving the key from the database
 * @param string $base64
 * @param array $json
 * @param string $hash
 * @return boolean true if hash was successfully reproduced from database values
 */
function verify_integrity($base64, $json, $hash)
{
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
        global $connection;
        $lkey = $connection->fetchKeyForLecture($json["name"]);
    }
    $sha = hash_hmac("SHA256", $base64, $lkey); 

    return ($sha === $hash);
}

/***
 * Updates a feedback sheet for a lecture id
 * @param array $json Submitted JSON data
 * @return bool Was the update successful
 */
function update_feedback_sheet($json)
{
    global $connection;
    $result = $connection->updateFeedbackSheetForLecture($json["name"], $json["sheet"]);
    echo($result == true ? "OK" : "ERROR");
}

/***
 * Returns the Feedback Sheet as a JSON String
 * @param array $json Submitted JSON data
 * @return string Feedback Sheet as JSON String
 */
function get_feedback_sheet($json)
{
    global $connection;
    $result = $connection->getFeedbackSheetForLecture($json["name"]);
    return json_encode($result);
}

?>