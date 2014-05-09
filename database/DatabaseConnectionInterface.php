<?php


/**
 * Interface for all database actions
 */
interface DatabaseConnection
{
    // ========================================
    // Voting functions:
    // ========================================

    function getVotingInformationForLecture($lecturename);

    function postVoteForLecture($name, $id, $vote);

    function getLectureIDFromName($lecture_name);

    // ========================================
    // Administration functions:
    // ========================================

    function createNewLectureID($name, $password, $owner);

}