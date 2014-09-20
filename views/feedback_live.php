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

class feedback_live
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
    }

    function display()
    {
        $lecture_exists = !$this->databaseconnection->isLectureIDFree($this->lecture_name);
        $live_enabled = $this->databaseconnection->isLiveFeedbackEnabledForLecture($this->lecture_name);

        // check if lecture exists and live-feedback is enabled for selected lecture
        if(!$lecture_exists || !$live_enabled) {
            include_once 'views/mainpage.php';
            $_SESSION["alert"] = "<strong>Error:</strong> ";

            if(!$lecture_exists)
                $_SESSION["alert"] .= "Lecture does not exist.";
            elseif(!$live_enabled)
                $_SESSION["alert"] .= "Live Feedback is currently not enabled for this lecture.";

            $mainpage = new mainpage();
            $mainpage->display();
            return;
        }

        // process message submission
        if(isset($_POST["submitmessage"])) {
            $name = $_POST["name"];
            $subject = $_POST["subject"];

            echo "<div class=\"container\">";

            $message = $_POST["message"];
            if(strlen($message) > 0) {
                $this->databaseconnection->submitFeedbackLiveMessageForLecture($this->lecture_name, $name, $subject, $message);
                echo "<div class=\"alert alert-info\">Message submitted.";
            } else {
                echo "<div class=\"alert alert-danger\">Please enter a message.";
            }
            echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button></div></div>";
        }

        ?>
        <div class="container">
            <div style="text-align: center;"><h3>Live-Feedback</h3></div>
        <?php
        $this->displayMessageForm($this->lecture_name);

        echo "</div>";
    }

    function displayMessageForm($lecture)
    {
        ?>
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">Send a message</h3></div>

                <div class="panel-body">
                    This allows you to send a direct feedback message to your lecturer, e.g. to ask a question or remark/point out something.<br/><br/>

                    <form role="form" method="post" action="/index.php?action=feedback_live&lecture=<?php echo $lecture;?>">
                        <input type="hidden" name="submitmessage">
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input type="text" name="name" id="name" class="form-control" value="Anonymous">
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject/Slide:</label>
                            <input type="text" name="subject" id="subject" class="form-control" placeholder="Slide 64, Chapter 2">
                        </div>
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea class="form-control" rows="4" id="message" name="message"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        <?php
    }
}
