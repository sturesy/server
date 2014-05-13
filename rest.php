<?php 

if(isset($_REQUEST["query"]))
{
    $INDEXPHPWASACCESSED = true;
    session_start();
    include_once 'config.php';
    include_once 'views/admin_view.php';
    $dbcon = getConnection();

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
    $orderby='';
    if(isset($_REQUEST["order"]))
    {
        $orderby = 'ORDER BY '.$database->escape_string($_REQUEST['order']);
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