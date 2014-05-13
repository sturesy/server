<?php

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
$("#lecturefield").keyup(function() 
{ 
	var data = $("#lecturefield").val();
	if(data.length == 0)
	{
		$( "#lecturefree" ).html( "" ).css("visibility", "hidden");
	}
	else if(data.length < 4 )
	{
		$( "#lecturefree" ).html( "too short" ).removeAttr("style");
	}
	else
	{
		checkLecture(data);
	}
	}
);

function checkLecture(lecture){
	$.post( "rest.php?query=checklecture&lecture="+lecture, 
		function( data ) {
                if(data==="true")
                {
                $( "#lecturefree" ).html( "<i class=\'icon-ok\'>" ).removeAttr("style");
                }
                else
                {
                $( "#lecturefree" ).html( "<i class=\'icon-remove\'>" ).removeAttr("style");
                }
			
		}
	);
}
                ';
    }

    function handleUserPerformsLogIn()
    {
        $user = $_POST["login_user"];
        $pwd = $_POST["login_password"];

        if($user === "admin" && $pwd === "test")
        {
            $_SESSION["sturesy_login"] = 1;


            $this->handleUserIsLoggedIn();
        }
        else
        {
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
                    show_lecture_table($this->databaseconnection->getLectureIDAdminInfos(), true);
                    close_page_body();
                    break;
                }
            case "creation":
                {
                    $this->javascriptCode = $this->javascriptForLectureCreation();
                    show_navbar();
                    show_lecture_id_create_dialog();
                    close_page_body();
                    break;
                }
            case "logout":
                {
                    break;
                }
            default: show_navbar();
            show_welcome_screen();
            close_page_body();
        }
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

            show_lecture_id_create_dialog(array("", $pwd, $owner, $email));
        }



        close_page_body();
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