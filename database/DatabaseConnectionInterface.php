<?php



interface DatabaseConnection
{
    
    
    
    function getVotingInformationForLecture($lecturename);
    
    function postVoteForLecture($name, $id, $vote);
    
    function getLectureIDFromName($lecture_name);
    
    
}