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
            case "delete": delete($json);
                        break;
            case "live": live($json);
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
            else if($json["target"] == "fb")
                echo get_feedback($json);
        }
        else
            query_all_votes($json["name"]);
    }
}

function update($json)
{
    if(isset($json["name"])) {
        if (isset($json["target"]) && $json["target"] == "fbsheet")
            update_feedback_sheet($json);
        else
            update_lecture_type($json);
    }
}

function clean($json)
{
    if(isset($json["name"]))
    {
        clean_votes($json);
    }
}

function redeem($json)
{
	if(isset($json["token"]))
	{
		redeem_token($json["token"]);
	}
}

function delete($json)
{
    if(isset($json["name"]) && isset($json["items"]))
    {
        delete_feedback_questions($json["name"], $json["items"]);
    }
}

function live($json)
{
    global $connection;
    if(isset($json["name"]) && isset($json["action"])) {
        $action = $json["action"];

        if($action == "setstate" && isset($json["enabled"])) {
            // clear previous live feedback (e.g. from unterminated sessions)
            $connection->deleteLiveFeedback($json["name"]);
            if($connection->setLiveFeedbackState($json["name"], $json["enabled"]))
                echo "OK";
        }
        else if($action == "poll") {
            echo json_encode(selectdelete_live($json["name"]));
        }
    }
}

/**
 * This function will fetch the latest messages and delete exactly these
 * @param $name name of lecture
 * @return array new messages
 */
function selectdelete_live($name)
{
    global $connection;
    $result = $connection->getLiveFeedbackForLecture($name);

    $ids = array();
    foreach($result as $message) {
        $ids[] = $message["msgid"];
    }
    $connection->deleteLiveFeedback($name, $ids);

    return $result;
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
    return json_encode(array_values($result));
}

/***
 * Returns the user-submitted feedback to a lecture
 * @param array $json Submitted JSON data
 * @return string Collected feedbac
 */
function get_feedback($json)
{
    // turn on gzip compression
    if (extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler")) {
        ini_set("zlib.output_compression", "On");
    }
    global $connection;
    return json_encode($connection->getFeedbackForLecture($json["name"]));
}

/**
 * Deletes a list of feedback IDs for a lecture
 * @param string $name Name of lecture
 * @param array $items Feedback Items to delete
 */
function delete_feedback_questions($name, $items)
{
    global $connection;
    return $connection->deleteFeedbackItems($name, $items);
}

?>