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
    }
    function __destruct()
    {
    }

    function setup()
    {
        $this->lecture_name = $_GET["lecture"];
        $this->sheet = $this->databaseconnection->getFeedbackSheetForLecture($this->lecture_name);
    }

    function panelwithmodule($item, $module)
    {
        ?>
        <div class="panel panel-default">
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

    function displaySheet()
    {
        ?>
        <div class="container">
            <form role="form" method="post" action="index.php?action=feedback_sheet&lecture=<?php echo $this->lecture_name;?>">
                <?php
                foreach($this->sheet as $entry)
                {
                    $mod = null;

                    // extract sheet data
                    $values["text"] = nl2br($entry["description"]);
                    $index = $entry["fbid"];

                    switch($entry["type"]) {
                        case "comment":
                            $mod = new textarea($values, $index);
                            break;
                        case "grades":
                            $values["elements"] = array(1, 2, 3, 4, 5, 6);
                            $mod = new listmodule($values, $index);
                            break;
                    }
                    if($mod != null) {
                        echo $mod->javascript();
                        $this->panelwithmodule($entry, $mod);
                    }
                }
                ?>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    <?php
    }

    function display()
    {
        $this->displaySheet();
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

    }
} 