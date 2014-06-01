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

if(isset($_REQUEST["query"]))
{
    $INDEXPHPWASACCESSED = true;
    session_start();
    
    include_once 'config.php';
    global $connection;
    $dbcon = $connection;

    include_once 'views/admin_view.php';

    if (isset($_SESSION["sturesy_login"]) && $_SESSION["sturesy_login"] == 1)
    {
        $requestQuery = $_REQUEST["query"];
        
        switch($requestQuery)
        {
            case "table":
                request_show_table();
                break;
            case "renewtoken":
                request_renew_token();
                break;
            case "checklecture": 
                request_lecture_id_taken();
                break;
        }
    }
    else
    {
        die("Invalid Session");
    }
}
else
{
    die('<meta http-equiv="refresh" content="0; url=index.php" />');
}


function request_show_table()
{
    global $dbcon;
    $orderby = "";
    if(isset($_REQUEST["order"]))
    {
        $orderby= $_REQUEST['order'];
    }

    show_lecture_table($dbcon->getLectureIDAdminInfos($orderby));
}

function request_renew_token()
{
    global $dbcon;
    if(isset($_REQUEST["l"]) && isset($_REQUEST["o"]) )
    {
        $dbcon->generateNewTokenForLectureID($_REQUEST["l"],$_REQUEST["o"], time());
    }
}


function request_lecture_id_taken()
{
    global $dbcon;
    if(isset($_REQUEST["lecture"]))
    {
        $lecture = $_REQUEST["lecture"];
        
        $result = $dbcon->isLectureIDFree($lecture);
        
        echo $result ? "true" : "false";
    }
}

?>