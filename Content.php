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

class Content
{
    private $databaseconnection;
    private $subcontent = false;

    private $user_id_cookie = false;

    function __construct($user_id_cookie)
    {
        include 'config.php';
        global $connection;
        $this->databaseconnection = $connection;

        $this->user_id_cookie = $user_id_cookie;

        if(isset($_GET["lecture"]) && $_GET["lecture"] != "" && isset($_GET["action"]))
        {
            switch($_GET["action"]) {
                case "vote":
                    include_once 'views/lecture.php';
                    $this->subcontent = new lecture($this->databaseconnection, $this->user_id_cookie);
                    break;
                case "feedback_sheet":
                    include_once 'views/feedback_sheet.php';
                    $this->subcontent = new feedback_sheet($this->databaseconnection, $this->user_id_cookie);
                    break;
                case "feedback_live":
                    include_once 'views/feedback_live.php';
                    $this->subcontent = new feedback_live($this->databaseconnection, $this->user_id_cookie);
                    break;
            }
        }
        else if(isset($_GET["admin"]))
        {
            include_once 'views/admin.php';
            $this->subcontent = new admin($this->databaseconnection);
        }
        else
        {
            $this->fallBackSubcontent();
        }
    }

    function __destruct()
    {
    }


    function handleCookies()
    {
        if(method_exists($this->subcontent, "handleCookies"))
        {
            $this->subcontent->handleCookies();
        }
    }
    
    function setup()
    {
        if(method_exists($this->subcontent, "setup"))
        {
            $this->subcontent->setup();
        }
    }

    function display()
    {
        $this->subcontent->display();
    }

    function fallBackSubcontent()
    {
        include_once 'views/mainpage.php';
        $this->subcontent = new mainpage();
    }


    function modifiedBodyValues()
    {
        if(method_exists($this->subcontent, "modifiedBodyValues"))
        {
            return $this->subcontent->modifiedBodyValues();
        }
        else return "";
    }

    function additionalJavascript()
    {
        if(method_exists($this->subcontent, "additionalJavascript"))
        {
            return $this->subcontent->additionalJavascript();
        }
        else
        {
            return false;
        }
    }
}

?>