<?php


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


    function createNewLectureID($name, $password, $owner)
    {
        $name = $this->mysqli->real_escape_string($name);
        $pwd  = $this->mysqli->real_escape_string($pwd);
        $owner = $this->mysqli->real_escape_string($owner);

        $query = sprintf("INSERT INTO sturesy_lectures (lecture, password, owner, date, token) VALUES ('%s', '%s', '%s', '%s', '%s')",
                $name,$pwd,$owner,date("Y-m-d H:i:s"), sha1($name.$pwd.$owner));

        return $this->mysqli->query($query);
    }

}