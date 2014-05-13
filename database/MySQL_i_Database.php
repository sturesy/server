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
        $this->mysqli->close();
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
        $lectureName = $result->fetch_row()[0];
        $result->close();

        return $lectureName;
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

}