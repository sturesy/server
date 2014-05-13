<?php


/**
 * Interface for all database actions
 */
interface DatabaseConnection
{
    
    
    
    
    // ========================================
    // General functions:
    // ========================================
    
    function getLastError();
    
    // ========================================
    // Voting functions:
    // ========================================

    function getVotingInformationForLecture($lecturename);

    function postVoteForLecture($name, $id, $vote);

    function getLectureIDFromName($lecture_name);

    // ========================================
    // Administration functions:
    // ========================================

    function createNewLectureID($name, $password, $owner, $email);

    /**
     * Returns a list of lecture-ids with: name, owner, last-used-date and token
     */
    function getLectureIDAdminInfos();
    
    function generateNewTokenForLectureID($lecture,$owner,$date);
    
    function isLectureIDFree($lectureid);

}