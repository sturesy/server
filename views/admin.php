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

abstract class ModeOfOperation
{
    const SHOWLOGINFORM = 0;
    const USERISLOGGINGIN = 1;
    const USERISLOGGEDIN = 2;
    const CREATINGNEWLECTURE = 3;
    // etc.
}

/**
 * This is the basic Template for controller-classes
 */
class admin
{
    private $databaseconnection;

    private $mode;

    private $javascriptCode = false;

    function __construct(DatabaseConnection &$databaseconnection)
    {
        include_once 'views/admin_view.php';
        include_once 'database/DatabaseConnectionInterface.php';

        $this->databaseconnection = $databaseconnection;
    }
    function __destruct()
    {
    }


    function setup()
    {
        if($this->isUserLoggedIn())
        {
            if($this->isCreatingNewLecture())
            {
                $this->mode = ModeOfOperation::CREATINGNEWLECTURE;
            }
            else
            {
                $this->mode = ModeOfOperation::USERISLOGGEDIN;
            }
        }
        else if($this->isUserCurrentlyLoggingIn())
        {
            $this->mode = ModeOfOperation::USERISLOGGINGIN;
        }
        else
        {
            $this->mode = ModeOfOperation::SHOWLOGINFORM;
        }
    }

    function display()
    {
        switch($this->mode)
        {
            case ModeOfOperation::USERISLOGGINGIN:
                $this->handleUserPerformsLogIn();
                break;

            case ModeOfOperation::USERISLOGGEDIN:
                $this->handleUserIsLoggedIn();
                break;

            case ModeOfOperation::CREATINGNEWLECTURE:
                $this->handleCreatingNewLecture();
                break;

            case ModeOfOperation::SHOWLOGINFORM:
            default: show_login_form();
        }

    }

    function modifiedBodyValues()
    {
        return ""; // <body> tag not modified
    }


    function additionalJavascript()
    {
        return $this->javascriptCode;
    }

    /**
     * Remove function if not necessary
     */
    function handleCookies()
    {
        session_start();
    }


    // ==========================================================================================

    function isUserLoggedIn()
    {
        return isset($_SESSION["sturesy_login"]) && $_SESSION["sturesy_login"] == 1;
    }

    function isUserCurrentlyLoggingIn()
    {
        return isset($_POST["login_user"]) && isset($_POST["login_password"]);
    }

    function isCreatingNewLecture()
    {
        return isset($_POST["lecture"]);
    }

    function javascriptForTableReload()
    {
        return '
                function orderTable(order){
                $("#overviewtable").load("rest.php?query=table&order="+order ,
                function() {
    });}

                function renewToken(l,o){var loadURL = "rest.php?query=renewtoken&l="+l+"&o="+o;$.ajax({url: loadURL}).done(function() {orderTable("lecture");});}
                ';
    }

    function javascriptForLectureCreation()
    {
        return '
                function checkLecture(e){$.post("rest.php?query=checklecture&lecture="+e,function(e){"true"===e?$("#lecturefree").html("<i class=\'glyphicon glyphicon-ok\'>"):$("#lecturefree").html("<i class=\'glyphicon glyphicon-remove\'>")})}$("#lecturefield").keyup(function(){var e=$("#lecturefield").val();0==e.length?$("#lecturefree").html(""):e.length<4?$("#lecturefree").html("too short"):checkLecture(e)});
                ';
    }

    function handleUserPerformsLogIn()
    {
        $user = $_POST["login_user"];
        $pwd = $_POST["login_password"];
        
        global $admin_password;

        if($user === "admin" && $pwd === $admin_password)
        {
            $_SESSION["sturesy_login"] = 1;

            $this->handleUserIsLoggedIn();
        }
        else
        {
            include_once 'functions.php';
            
            show_error("Wrong login information!");
            show_login_form();
            // wrong info
        }
    }

    function handleUserIsLoggedIn()
    {

        if(isset($_GET["admin"]))
        {
            $show = $_GET["admin"];
        }
        else
        {
            $show = "defaultcase";
        }

        switch ($show)
        {
            case "overview":
                {
                    $this->javascriptCode = $this->javascriptForTableReload();
                    show_navbar();
                    show_lecture_table($this->databaseconnection->getLectureIDAdminInfos("lecture"), true);
                    break;
                }
            case "creation":
                {
                    $this->javascriptCode = $this->javascriptForLectureCreation();
                    show_navbar();
                    show_lecture_id_create_dialog();
                    break;
                }
            case "logout":
                {
                    $this->handleLogout();
                    break;
                }
            default: show_navbar();
            show_welcome_screen();
        }
        close_page_body();
    }

    function handleCreatingNewLecture()
    {

        $name = $this->checkPost("lecture");
        $pwd = $this->checkPost("password");
        $owner = $this->checkPost("owner");
        $email = $this->checkPost("email");

        $createdWithSuccess = false;
        if($name !== false && $pwd !== false && $owner !== false && $email !== false)
        {
            $createdWithSuccess = $this->databaseconnection->createNewLectureID($name, $pwd, $owner, $email);
        }

        show_navbar();

        include_once 'functions.php';

        if($createdWithSuccess)
        {
            show_success("Successfully created LectureID: " . $name);
            show_lecture_id_create_dialog();
        }
        else
        {
            $duplicate =  (strpos($this->databaseconnection->getLastError(),"Duplicate") !== false);

            if($duplicate)
            {
                show_error("This LectureID is already taken, please choose a different one.");
            }
            else
            {
                show_error("Couldn't create LectureID");
            }

            $this->javascriptCode = $this->javascriptForLectureCreation();
            show_lecture_id_create_dialog(array("", $pwd, $owner, $email));
        }



        close_page_body();
    }

    function handleLogout()
    {
        echo "<div><div>";
        
        $bool = session_destroy();
        $_SESSION = NULL;
        if($bool)
            show_success("Logged out successfully!<br/>
                    <small>This page will refresh automatically in 3 seconds or <a href='index.php?admin'>click here</a></small>
                    <meta http-equiv='refresh' content='3; url=index.php?admin'/>");
        else
            show_error("There was a problem logging out");
    }


    function checkPost($value)
    {
        if(isset($_POST[$value]))
        {
            return $_POST[$value];
        }
        else return false;
    }

}


?>