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
 
function mobile()
{
	global $database;
	global $encryption_key;
	
	header('Content-type: application/json');
	
	if($_REQUEST["mobile"] === "sturesy060")
	{
    	if(isset($_REQUEST["data"]) && isset($_REQUEST["hash"]))
    	{
    	   $json = json_decode(base64_decode($_REQUEST["data"]), true);
    	   if(isset($json["time"]))
    	   {
        	   if((time() - (int)($json["time"]) <= 4) && verify_rest_message($_REQUEST["data"], $_REQUEST["hash"]))
        	   {
                    if(isset($json) && isset($json["command"]))
                	{
                    	switch($json["command"])
                    	{
                        	case "vote": echo post_vote_mobile($json); break;
                        	case "get" : echo get_question($json); break;
                        	case "info": echo get_info(); break;
                    	}
                	}
                	else
                	{
                    	die("no command");
                	}
            	}
            	else
            	{
                	die("unverified");
            	}
        	}
        	else
        	{
        	   die("time up");
        	}
    	}
    	else
    	{
        	die("command mismatch");
    	}
	}
	else
	{
    	die("false");
	}
}


function post_vote_mobile($json)
{
    if(isset($json["id"]) && isset($json["vote"]) && isset($json["lecture"]))
    {
        $id = fetchLectureID($json["lecture"]);
        $result = post_vote($json["lecture"], $json["id"], json_encode($json["vote"]));
        return $result ? "true" : "false";
    }
    else return "false";
}

function get_question($json)
{
    if(isset($json["lecture"]))
    {
        $result = get_vote_type($json["lecture"]);
        $arr = array();
        $arr["question"] = $result["question"];
        $arr["type"] =$result["type"]; 
        $arr["answers"] = json_decode($result["answers"]); 
        $arr["correctanswers"] = json_decode($result["correctanswers"]);
        $arr["date"] = $result["date"];
        
        return json_encode($arr);
    }
 //function get_vote_type($name, $db)
}

function get_info()
{

}


?>