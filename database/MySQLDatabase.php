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


class MySQLDatabase implements DatabaseConnection
{
    private $mysql;

    function __construct($host, $user, $pwd, $database)
    {
        $this->mysql = mysql_connect($host, $user, $pwd) or die ("Connection Error");
        mysql_select_db($database, $this->mysql) or die("Couldn't Select Database");
    }

    function __destruct()
    {
        if(!is_null($this->mysql))
        {
            mysql_close($this->mysql);
        }
    }
    
    function getLastError()
    {
        return mysql_error($this->mysql);
    }

    function getVotingInformationForLecture($lecturename)
    {
        $lecturename = mysql_real_escape_string($lecturename);

        $query = "SELECT question, type, answers, correctanswers, sturesy_lectures.date FROM sturesy_lectures, sturesy_question
        WHERE sturesy_question.lecture = id AND sturesy_lectures.lecture = '$lecturename';";

        $result =  mysql_query($query,$this->mysql);

        if($result === false)
        {
            return -1;
        }
        else
        {
            $row = mysql_fetch_row($result);
            mysql_free_result($result);
            return $row;
        }
    }

    function postVoteForLecture($name, $id, $vote)
    {
        $name =  mysql_real_escape_string($name);
        $name = $this->getLectureIDFromName($name);

        $id =  mysql_real_escape_string($id);
        $vote =  mysql_real_escape_string($vote);

        if($vote >= 1 && $vote <= 10 || strpos($vote, "[")!= -1)
        {
            $query = "INSERT INTO sturesy_votes (lid, guid, vote, date) VALUES ($name, '$id', '$vote', NOW());";
            $result = mysql_query($query, $this->mysql);
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

        $result = mysql_query($query, $this->mysql);
        $lectureName = mysql_fetch_row($result)[0];
        mysql_free_result($result);
        return $lectureName;
    }
    
    function createNewLectureID($name, $password, $owner, $email)
    {
        $name = mysql_real_escape_string($name);
        $pwd  = mysql_real_escape_string($pwd);
        $owner = mysql_real_escape_string($owner);
        $email = mysql_real_escape_string($email);
    
        $query = sprintf("INSERT INTO sturesy_lectures (lecture, password, owner, email, date, token) VALUES ('%s', '%s','%s', '%s', '%s', '%s')",
                $name, $pwd, $owner, $email, date("Y-m-d H:i:s"), sha1($name.$pwd.$owner));
    
        return mysql_query($query, $this->mysql);
    }
    
    
    function getLectureIDAdminInfos()
    {
        $query = "SELECT lecture,owner,email,date,token FROM sturesy_lectures $orderby";
        $result = mysql_query($query, $this->mysql);
    
    
        $returnval = array();
        if($result !== false)
        {
            
            while($row = mysql_fetch_array($result, MYSQL_BOTH))
            {
                array_push($returnval,$row);
            }
        }
        mysql_free_result($result);
    
        return $returnval;
    }
    
    function generateNewTokenForLectureID($lecture,$owner,$date)
    {
        $lecture = mysql_real_escape_string($lecture);
        $owner = mysql_real_escape_string($owner);
        $token = sha1($lecture.$owner.$date);
    
        $query = "UPDATE sturesy_lectures SET token='$token' WHERE lecture='$lecture' AND owner='$owner'";
        $result = mysql_query($query, $this->mysql);
        return $result;
    }
    

    function isLectureIDFree($lectureid)
    {
        $lectureid = mysql_real_escape_string($lectureid);
        $query = "SELECT lecture from sturesy_lectures WHERE lecture='$lectureid'";
    
        $result = mysql_query($query, $this->mysql);
    
        if($result !== false)
        {
            $numrows = mysql_num_rows($result);
            mysql_free_result($result);
            return $numrows === 0;
        }
    
        return false;
    }
    


}