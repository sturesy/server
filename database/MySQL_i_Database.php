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

include_once 'DatabaseConnectionInterface.php';

class MySQLiDatabase implements DatabaseConnection
{
    private $mysqli;

    function __construct($host, $user, $password, $database)
    {
        $this->mysqli = new mysqli($host, $user, $password, $database);
    }

    function __destruct()
    {
        if(!ini_get("mysqli.allow_persistent"))
        {
            // connection pooling is disallowed
            // so closing connection manually
            $this->mysqli->close();
        }
    }


    function getLastError()
    {
        return $this->mysqli->error;
    }

    function getVotingInformationForLecture($lecturename)
    {
        $lecturename = $this->mysqli->real_escape_string($lecturename);

        $query = "SELECT question, type, answers, correctanswers, sturesy_lectures.date FROM sturesy_lectures, sturesy_question
        WHERE sturesy_question.lecture = id AND sturesy_lectures.lecture = '$lecturename';";

        $result =  $this->mysqli->query($query);

        if($result === false)
        {
            return -1;
        }
        else
        {
            $row = $result->fetch_row();
            $result->close();
            return $row;
        }
    }


    /**
     * Posts a Vote for a Lecture
     * @param name (string), lecture name
     * @param id (string), the provided device id
     * @param vote (string), the submitted vote
     * @return bool whether the query was successful
     */
    function postVoteForLecture($name, $id, $vote)
    {
        $name = $this->mysqli->real_escape_string($name);
        $name = $this->getLectureIDFromName($name);

        $id = $this->mysqli->real_escape_string($id);
        $vote = $this->mysqli->real_escape_string($vote);

        if($vote >= 1 && $vote <= 10 || strpos($vote, "[")!= -1)
        {
            $query = "INSERT INTO sturesy_votes (lid, guid, vote, date) VALUES ($name, '$id', '$vote', NOW());";
            $result = $this->mysqli->query($query);
            return ($result === TRUE);
        }
        else
        {
            return false;
        }
    }


    function getLectureIDFromName($lecture_name)
    {
        $query = "SELECT id FROM sturesy_lectures WHERE lecture ='$lecture_name'";

        $result = $this->mysqli->query($query);
        $lectureid = $result->fetch_row();
        $result->close();

        return $lectureid[0];
    }


    function createNewLectureID($name, $password, $owner, $email)
    {
        $name = $this->mysqli->real_escape_string($name);
        $password  = $this->mysqli->real_escape_string($password);
        $owner = $this->mysqli->real_escape_string($owner);
        $email = $this->mysqli->real_escape_string($email);

        $query = sprintf("INSERT INTO sturesy_lectures (lecture, password, owner, email, date, token) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                $name,$password,$owner,$email,date("Y-m-d H:i:s"), sha1($name.$password.$owner));

        return $this->mysqli->query($query);
    }

    function getLectureIDAdminInfos($orderby_attachment = "")
    {
        if(strlen($orderby_attachment) > 0)
        {
            $orderby_attachment = "ORDER BY " . $this->mysqli->real_escape_string($orderby_attachment);
        }

        $query = "SELECT lecture,owner,email,date,token FROM sturesy_lectures $orderby_attachment";
        $result = $this->mysqli->query($query);
        $returnval = array();
        if($result !== false)
        {
            while($row = $result->fetch_array(MYSQLI_BOTH))
            {
                array_push($returnval,$row);
            }
        }

        $result->free();

        return $returnval;
    }

    function generateNewTokenForLectureID($lecture,$owner,$date)
    {
        $lecture = $this->mysqli->real_escape_string($lecture);
        $owner = $this->mysqli->real_escape_string($owner);
        $token = sha1($lecture.$owner.$date);

        $query = "UPDATE sturesy_lectures SET token='$token' WHERE lecture='$lecture' AND owner='$owner'";
        $result = $this->mysqli->query($query);
        return $result;
    }

    function isLectureIDFree($lectureid)
    {
        $lectureid = $this->mysqli->real_escape_string($lectureid);
        $query = "SELECT lecture from sturesy_lectures WHERE lecture='$lectureid'";

        $result = $this->mysqli->query($query);

        if($result !== false)
        {
            $numrows = $result->num_rows;
            $result->free();

            return $numrows === 0;
        }

        return false;
    }

    // ========================================
    // Relay.php functions:
    // ========================================

    function getVotesForLectureAndMarkFetched($name)
    {
        $name = $this->mysqli->real_escape_string($name);

        $query = "SELECT sturesy_votes.guid, sturesy_votes.vote, sturesy_votes.date, sturesy_votes.lid
        FROM sturesy_lectures, sturesy_votes WHERE lecture ='$name' AND id = lid AND fetched != 1 ;";

        $result = $this->mysqli->query($query);
        
        if($result !== false)
        {

            $returnval = "";
            $inarray ="";

            $rows = array();

            while($row = $result->fetch_array())
            {
                $returnval .=  $row[0].",".$row[1].",".$row[2].";";
                 
                $inarray .= "'".$row[0]."',";

                $id = $row[3];

                array_push($rows, array($row[0],json_decode($row[1]),$row[2]));
            }

            $result->free();
             
            if(strlen($returnval) > 0)
            {
                $inarray = substr($inarray, 0, -1); //remove last ","

                $updatequery = "UPDATE sturesy_votes SET fetched = 1 WHERE lid = '$id' AND guid in ($inarray) ";
                $this->mysqli->query($updatequery);


                return $rows;
            }
        }

        return false;
    }
    
    function removeVotesForLecture($name)
    {
        $name = $this->mysqli->real_escape_string($name);
        
        $query = "DELETE FROM sturesy_votes using sturesy_lectures INNER JOIN sturesy_votes
        ON (sturesy_lectures.id = sturesy_votes.lid) WHERE sturesy_lectures.lecture ='$name';";
        
        $this->mysqli->query($query);
    }
    
    function updateQuestionForLecture($lecturename, $question, $answers, $type, $correctanswers)
    {
        
        $lecturename = $this->mysqli->real_escape_string($lecturename);
        
        $question = $this->mysqli->real_escape_string($question);
        
        $type = $this->mysqli->real_escape_string($type);
         
        $answers = $this->mysqli->real_escape_string(json_encode($answers));
        
        $correctanswers = $this->mysqli->real_escape_string(json_encode($correctanswers));
        
        $lectureid = $this->getLectureIDFromName($lecturename);
        
        if(is_numeric($lectureid))
        {
            $query = "INSERT INTO sturesy_question (lecture,type,question,answers,correctanswers)
            VALUES ($lectureid, '$type', '$question', '$answers', '$correctanswers')
            ON DUPLICATE KEY UPDATE type = '$type', question = '$question' , answers = '$answers' , correctanswers = '$correctanswers'";

            $query2 = "DELETE FROM sturesy_votes WHERE lid = $lectureid";

            $query3 = "UPDATE sturesy_lectures SET date=NOW() WHERE id = $lectureid";

            $result = $this->mysqli->query($query);
            if($result !== false)
            {
                $this->mysqli->query($query2);
                $this->mysqli->query($query3);
            }
        }
        
        
    }
    
    function fetchInformationForTokenRedemption($token)
    {
        $token = $this->mysqli->real_escape_string($token);
        
        $query = sprintf("SELECT lecture, password FROM sturesy_lectures WHERE token ='%s'",$token);
        
        $queryresult = $this->mysqli->query($query);
        if($queryresult !== false)
        {
            $remquery = sprintf("UPDATE sturesy_lectures SET token='' WHERE token ='%s'",$token);

            $this->mysqli->query($remquery);

            $result ="";
            while($row = $queryresult->fetch_array())
            {
                $result .= $row[0].";".$row[1];
                break;
            }
            
            $queryresult->free();
            return $result;
        }
        return "";
    }
    
    function fetchKeyForLecture($lecturename)
    {
        $lecturename = $this->mysqli->real_escape_string($lecturename);
        
        $query = "SELECT password FROM sturesy_lectures WHERE lecture ='$lecturename'";
        
        $result = $this->mysqli->query($query);
        
        $r = $result->fetch_array();
        $result->free();
        
        return $r[0];
    }

    function clearSheetForLectureId($lectureid)
    {
        $query = "DELETE FROM sturesy_fbsheets WHERE lid = '$lectureid'";
        return $this->mysqli->query($query);
    }

    function updateFeedbackSheetForLecture($lecturename, $sheet)
    {
        $success = true;
        $lectureid = $this->getLectureIDFromName($lecturename);

        $query = "INSERT INTO sturesy_fbsheets (fbid, lid, title, description, type, mandatory, extra, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE title=values(title), description=values(description), type=values(type),
            mandatory=values(mandatory), extra=values(extra), position=values(position)";
        $stmt = $this->mysqli->prepare($query);

        $this->mysqli->query("START TRANSACTION");
        foreach ($sheet as $currentsheet) {
            $fbid = $currentsheet["fbid"];
            $title = $currentsheet["title"];
            $desc = $currentsheet["description"];
            $type = $currentsheet["type"];
            $mandatory = (int)$currentsheet["mandatory"];
            $extra = $currentsheet["extra"];
            $position = $currentsheet["position"];

            $stmt->bind_param("iisssisi", $fbid, $lectureid, $title, $desc, $type, $mandatory, $extra, $position);
            $stmt->execute();
            $success &= ($stmt->errno == 0);
        }
        $stmt->close();
        $this->mysqli->query("COMMIT");
        return $success;
    }

    function getFeedbackSheetForLecture($lecture)
    {
        $lectureid = $this->getLectureIDFromName($lecture);

        $query = "SELECT fbid, title, description, type, mandatory, extra FROM sturesy_fbsheets WHERE lid = '$lectureid'
                    ORDER BY position ASC";

        $result = $this->mysqli->query($query);

        $rows = array();
        while(($row = $result->fetch_array(MYSQL_ASSOC))) {
            $row["fbid"] = (int)$row["fbid"];
            $row["mandatory"] = (bool)$row["mandatory"];
            $rows[$row["fbid"]] = $row; // index by feedback id
        }

        return $rows;
    }

    function getFeedbackForLecture($lecture)
    {
        $lectureid = $this->getLectureIDFromName($lecture);
        $query = "SELECT fbid, guid, response FROM sturesy_fb JOIN sturesy_fbsheets USING (fbid) WHERE lid = '$lectureid'";

        $result = $this->mysqli->query($query);

        $rows = array();
        while(($row = $result->fetch_array(MYSQL_ASSOC))) {
            $fbid = $row["fbid"];
            unset($row["fbid"]);
            $rows[$fbid][] = $row; // index by feedback id
        }
        return $rows;
    }

    function deleteFeedbackItems($lecture, $ids)
    {
        $lectureid = $this->getLectureIDFromName($lecture);
        $query = "DELETE FROM sturesy_fbsheets WHERE fbid = ? AND lid = ?";
        $stmt = $this->mysqli->prepare($query);

        foreach($ids as $id) {
            $stmt->bind_param("ii", $id, $lectureid);
            $stmt->execute();
        }
        $result = $stmt->affected_rows > 0;
        $stmt->close();
        return $result;
    }


    // ========================================
    // feedback_sheet.php functions:
    // ========================================

    function submitFeedbackForLecture($guid, $responses)
    {
        $success = true;
        $query = "INSERT INTO sturesy_fb (fbid, guid, response) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($query);

        $this->mysqli->query("START TRANSACTION");
        foreach ($responses as $response) {
            $fbid = $response["fbid"];
            $input = $response["input"];

            $stmt->bind_param("iss", $fbid, $guid, $input);
            $stmt->execute();
            $success &= ($stmt->errno == 0);
        }
        $stmt->close();
        $this->mysqli->query("COMMIT");

        return $success;
    }

    function userHasSubmittedForLecture($lecture, $guid)
    {
        $lectureid = $this->getLectureIDFromName($lecture);
        $query = "SELECT count(1) FROM sturesy_fb INNER JOIN sturesy_fbsheets USING(fbid) WHERE lid = ? AND guid = ? LIMIT 1";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("is", $lectureid, $guid);
        $stmt->execute();

        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0;
    }

    // ========================================
    // feedback_live.php functions:
    // ========================================

    function isLiveFeedbackEnabledForLecture($lecturename)
    {
        $lectureid = $this->getLectureIDFromName($lecturename);
        if(!$lectureid)
            return false;

        $query = "SELECT live_feedback_enabled FROM sturesy_lectures WHERE id = ?";
        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param("i", $lectureid);
        $stmt->execute();

        $stmt->bind_result($enabled);
        $stmt->fetch();
        $stmt->close();

        return $enabled != 0;
    }

    function submitFeedbackLiveMessageForLecture($lecturename, $guid, $name = null, $subject = null, $message = null)
    {
        $lectureid = $this->getLectureIDFromName($lecturename);
        if(!$lectureid)
            return false;

        $query = "INSERT INTO sturesy_livemessages (lid, name, subject, message, guid) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param("issss", $lectureid, $name, $subject, $message, $guid);
        $stmt->execute();
        $result = $stmt->affected_rows == 1;

        $stmt->close();
        return $result;
    }

    function setLiveFeedbackState($lecturename, $state)
    {
        $lectureid = $this->getLectureIDFromName($lecturename);
        $query = "UPDATE sturesy_lectures SET live_feedback_enabled=? WHERE id=?";
        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param("ii", $state, $lectureid);
        $result = $stmt->execute();

        $stmt->close();
        return $result;
    }

    /**
     * Deletes all or a specified set of messages from the database.
     * @param string $lecturename targetted lecture
     * @param array $ids if not null, all messages with the specified IDs will be deleted
     * @return bool whether the query was successful
     */
    function deleteLiveFeedback($lecturename, $ids=null)
    {
        $lectureid = $this->getLectureIDFromName($lecturename);

        // delete all for given lecture
        if ($ids == null) {
            $query = "DELETE FROM sturesy_livemessages WHERE lid=?";

            $stmt = $this->mysqli->prepare($query);

            $stmt->bind_param("i", $lectureid);
            $stmt->execute();

            $result = $stmt->affected_rows > 0;
            $stmt->close();
            return $result;
        } else { // delete only selected messages
            $query = "DELETE FROM sturesy_livemessages WHERE msgid = ? AND lid = ?";
            $stmt = $this->mysqli->prepare($query);

            foreach($ids as $id) {
                $stmt->bind_param("ii", $id, $lectureid);
                $stmt->execute();
            }
            $result = $stmt->affected_rows > 0;
            $stmt->close();
            return $result;
        }
    }

    function getLiveFeedbackForLecture($lecturename)
    {
        $lectureid = $this->getLectureIDFromName($lecturename);
        $query = "SELECT msgid, guid, name, subject, message, date FROM sturesy_livemessages WHERE lid = '$lectureid'";

        $result = $this->mysqli->query($query);

        $rows = array();
        while(($row = $result->fetch_array(MYSQL_ASSOC))) {
            $rows[] = $row;
        }
        return $rows;
    }

}
