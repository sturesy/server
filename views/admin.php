<?php

include_once 'views/admin_view.php';


abstract class ModeOfOperation
{
    const SHOWLOGINFORM = 0;
    const USERISLOGGINGIN = 1;
    const USERISLOGGEDIN = 2;
    // etc.
}

/**
 * This is the basic Template for controller-classes
 */
class admin
{
    private $databaseconnection;


    private $mode;

    function __construct(&$databaseconnection)
    {
        $this->databaseconnection = $databaseconnection;
    }
    function __destruct()
    {
    }


    function setup()
    {
        if($this->isUserLoggedIn())
        {
            $this->mode = ModeOfOperation::USERISLOGGEDIN;
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
                /*HANDLE LOGIN*/
                break;

            case ModeOfOperation::USERISLOGGEDIN:
                /* Show admin overview*/
                break;
            case ModeOfOperation::SHOWLOGINFORM:
            default: show_login_form();
        }

    }

    /**
     * Remove function if not necessary
     * @return string
     */
    function modifiedBodyValues()
    {
        return ""; // <body> tag not modified
    }


    /**
     * Remove function if not necessary
     * @return string
     */
    function additionalJavascript()
    {
        return ""; // additional javascript
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
        return isset($_SESSION["login"]) && $_SESSION["login"] == 1;
    }

    function isUserCurrentlyLoggingIn()
    {
        return isset($_POST["login_user"]) && isset($_POST["login_password"]);
    }
}


?>