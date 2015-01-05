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
    
    // ========================================
    // Relay.php functions:
    // ========================================
    
    function getVotesForLectureAndMarkFetched($lecturename);
    
    function removeVotesForLecture($lecturename);
    
    function updateQuestionForLecture($lecturename, $question, $answers, $type, $correctanswers);
    
    function fetchInformationForTokenRedemption($token);
    
    function fetchKeyForLecture($lecturename);

    function updateFeedbackSheetForLecture($lecturename, $sheet);

    function getFeedbackSheetForLecture($lecture);

    function getFeedbackForLecture($lecture);

    function deleteFeedbackItems($lecture, $ids);

    function setLiveFeedbackState($lecturename, $state);

    function deleteLiveFeedback($lecturename, $ids=null);

    function getLiveFeedbackForLecture($lecturename);

    // ========================================
    // feedback_sheet.php functions:
    // ========================================
    function submitFeedbackForLecture($guid, $responses);

    function userHasSubmittedForLecture($lecture, $guid);

    // ========================================
    // feedback_live.php functions:
    // ========================================
    function isLiveFeedbackEnabledForLecture($lecturename);

    function submitFeedbackLiveMessageForLecture($lecturename, $guid, $name = null, $subject = null, $message = null);
}
