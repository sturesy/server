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

include_once 'functions.php';
include_once 'views/lecture_view.php';

include_once 'database/DatabaseConnectionInterface.php';

class lecture
{
    private $databaseconnection;
    private $user_id_cookie;


    private $lecture_name;
    private $lecture_infos;

    private $bodyOnLoadModifcation ="";

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
        
        if($this->hasUserIssuedVote())
        {
            // we'll be reloading later
            $this->bodyOnLoadModifcation = 'onLoad="JavaScript:timedRefresh(2000);"';
        }
    }

    function display()
    {
        $this->lecture_infos = $this->databaseconnection->getVotingInformationForLecture($this->lecture_name);
         
        if($this->lecture_infos === NULL)
        {
            $msg;
            if(!isset($this->lecture_name) || strlen($this->lecture_name) == 0)
            {
                $msg = "Please enter a Lecture-ID";
            }
            else
            {
                $msg = 'There is currently no Voting with the provided Lecture-ID "<b>'.$this->lecture_name.'</b>"';
            }
            display_no_lecture_id($msg);
        }
        else if($this->hasUserIssuedVote())
        {
            $this->handleVoting();
        }
        else
        {
            $this->display_voting();
        }

    }


    function hasUserIssuedVote()
    {
        return isset($_POST["cmd"]) && isset($_POST["type"]);
    }


    function display_voting()
    {
        display_question($this->lecture_name, $this->lecture_infos[0]);
        switch($this->lecture_infos[1])
        {
            case "singlechoice" :
                display_single_choice($this->lecture_name, json_decode($this->lecture_infos[2]), $this->prepare_single_choice_vote_buttons());
                break;

            case "multiplechoice" :
                display_multiple_choice($this->lecture_name, json_decode($this->lecture_infos[2]));
                break;

            case "textchoice":
                display_text_choice($this->lecture_name);
                break;
        }
    }

    function prepare_single_choice_vote_buttons()
    {
        $result = array();
        $array = json_decode($this->lecture_infos[2]);
        for ($i = 0; $i < count($array); $i++)
        {
            $lol = fnEncrypt("garbage,".$this->user_id_cookie.',vote,'.$i);
            array_push($result, array($lol , chr(65+$i)));
        }
        return $result;
    }


    function handleVoting()
    {
        $response = $this->user_id_cookie;
        if($this->user_id_cookie)
        {
            switch($_POST["type"])
            {
                case "s": $response = $this->handle_single_vote(); break;
                case "m": $response = $this->handle_multiple_vote(); break;
                case "t": $response = $this->handle_text_vote(); break;
            }
             
        }
        reload_page($response); // see functions.php
    }


    function handle_single_vote()
    {
        $stuff = explode(",", fnDecrypt($_REQUEST['cmd']));

        if($stuff[1]==$this->user_id_cookie && $stuff[2]=='vote')
        {
            $vote = $stuff[3];
            return  $this->databaseconnection->postVoteForLecture($this->lecture_name, $this->user_id_cookie, $vote);
        }
        else
        {
            return false;
        }
    }

    function handle_multiple_vote()
    {
        $votes = array();
        for($i = 0; $i < 10; $i++)
        {
            $ch = chr(65+$i);
            if(isset($_REQUEST[$ch]))
            {
                array_push($votes, $i);
            }
        }

        if(sizeof($votes) > 0)
        {
            return $this->databaseconnection->postVoteForLecture($this->lecture_name, $this->user_id_cookie, json_encode($votes));
        }
        else
        {
            return 2;
        }
    }


    function handle_text_vote()
    {
        global $ID;
        global $_REQUEST;

        if(isset($_REQUEST["text"]) && strlen($_REQUEST["text"]) > 0)
        {
            return $this->databaseconnection->postVoteForLecture($this->lecture_name, $this->user_id_cookie, json_encode($_REQUEST["text"]));
        }
        else
        {
            return 3;
        }
    }


    function modifiedBodyValues()
    {
        return $this->bodyOnLoadModifcation; 
    }

    /**
     * Remove function if not necessary
     * @return string
     */
    //     function additionalJavascript()
    //     {
    //         return ""; // additional javascript
    //     }

}

?>