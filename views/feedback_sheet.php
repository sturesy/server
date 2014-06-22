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

include_once 'database/DatabaseConnectionInterface.php';

include_once 'views/modules/interface.module.php';
include_once 'views/modules/textarea.module.php';
include_once 'views/modules/list.module.php';
include_once 'views/modules/long_list.module.php';

class feedback_sheet
{
    private $databaseconnection;
    private $user_id_cookie;
    private $lecture_name;
    private $sheet;

    function __construct(DatabaseConnection &$databaseconnection, $user_id_cookie)
    {
        $this->databaseconnection = $databaseconnection;
        $this->user_id_cookie = $user_id_cookie;
        $this->js = "";
    }
    function __destruct()
    {
    }

    function setup()
    {
        $this->lecture_name = $_GET["lecture"];
        $this->sheet = $this->databaseconnection->getFeedbackSheetForLecture($this->lecture_name);
    }

    function panelwithmodule($item, $module, $mark)
    {
        ?>
        <div class="panel panel-default <?php echo $mark ? "panel-danger" : "";?>">
            <div class="panel-heading">
                <?php
                if($item["mandatory"] == true)
                    echo "<span title=\"Mandatory\" class=\"pull-right glyphicon glyphicon-exclamation-sign\"></span>";
                ?>
                <h3 class="panel-title"><?php echo $item["title"];?></h3>
            </div>
            <div class="panel-body">
                <?php $module->html();?>
            </div>
        </div>
    <?php
    }

    function displaySheet($forgottenItems = null)
    {
        ?>
        <div class="container">
            <?php
                if($forgottenItems != null && count($forgottenItems) > 0) {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong>Warning!</strong> Some mandatory fields have not been filled out. Please re-check your submission.
                    </div>
                    <?php
                }
            ?>
            <form role="form" method="post" action="index.php?action=feedback_sheet&lecture=<?php echo $this->lecture_name;?>">
                <input type="hidden" name="submitfeedback">
                <?php
                // display each item of feedback sheet
                foreach($this->sheet as $entry)
                {
                    $mod = null;

                    // extract sheet data
                    $values["description"] = nl2br($entry["description"]);
                    $fbid = $entry["fbid"];
                    if(isset($entry["input"]))
                        $values["input"] = $entry["input"];

                    switch($entry["type"]) {
                        case "comment":
                            $mod = new textarea($values, $fbid);
                            break;
                        case "grades":
                            $values["elements"] = array(1, 2, 3, 4, 5, 6);
                            $mod = new listmodule($values, $fbid);
                            break;
                    }
                    if($mod != null) {
                        $markPanel = $forgottenItems != null && in_array($entry["fbid"], $forgottenItems);
                        $this->panelwithmodule($entry, $mod, $markPanel);
                        $this->js .= $mod->javascript() . "\n";
                    }
                }
                ?>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    <?php
    }

    function displaySuccessPage()
    {
        ?>
        <div class="container">
            <div class="panel panel-default panel-success">
                <div class="panel-heading">Submission completed</div>
                <div class="panel-body">
                    <strong>Thank you.</strong> Your feedback has been submitted and will be processed.
                </div>
            </div>
        </div>
        <?php
    }

    function processSubmission()
    {
        // have all the mandatory items been responsed to?
        $forgottenItems = array();
        foreach($this->sheet as $entry) {
            $fbid = $entry["fbid"];

            // re-enter previous data if available
            if(isset($_POST[$fbid])) {
                $this->sheet[$fbid]["input"] = $_POST[$fbid];
            }
            // determine mandatory items that have not been responded to
            else if($entry["mandatory"] && (!isset($_POST[$fbid]) || $_POST[$fbid] == "")) {
                $forgottenItems[] = $fbid;
            }
        }

        // redisplay form and mark items that are mandatory
        if(count($forgottenItems) > 0) {
            $this->displaySheet($forgottenItems);
        }
        else {
            $results = array();
            foreach($this->sheet as $entry) {
                if(isset($entry["input"]))
                    $results[] = array("input" => $entry["input"], "fbid" => $entry["fbid"]);
            }
            $this->databaseconnection->submitFeedbackForLecture($this->lecture_name, $this->user_id_cookie, $results);
            $this->displaySuccessPage();
        }
    }

    function display()
    {
        $lecture_exists = !$this->databaseconnection->isLectureIDFree($this->lecture_name);
        if(!$lecture_exists) {
            include_once 'views/mainpage.php';
            $_SESSION["alert"] = "<strong>Error:</strong> Invalid Lecture-ID.";
            $mainpage = new mainpage();
            $mainpage->display();
            return;
        }

        // have we received a filled out form?
        if(isset($_POST["submitfeedback"])) {
            $this->processSubmission();
        }
        else {
            if($this->databaseconnection->userHasSubmittedForLecture($this->lecture_name, $this->user_id_cookie))
                $this->displaySuccessPage();
            else
                $this->displaySheet();
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
        return $this->js; // additional javascript
    }

    /**
     * Remove function if not necessary
     */
    function handleCookies()
    {

    }
} 