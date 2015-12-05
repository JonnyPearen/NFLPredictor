<?php

/**
 * Teams.php
 * Model for teams with hard-coded data to start.
 *
 * Version 1
 * @author James Ensom
 */
class Teams extends MY_Model {

    // Constructor
    public function __construct() {
        parent::__construct("teams", "id");
    }
    
    // retrieve team codes
    public function getAllTeamCodes() {
        $teamCodes = array();
        // iterate over the data until we find all the ones we want
        $all = $this->all();
        foreach ($all as $record)
            if ($record->id != "NYG") {
                $teamCodes[] = array('id' => $record->id, 'name' => $record->name);  
            }
        return $teamCodes;
    }
    
    // retrieve AFC teams
    public function getAFC() {
        $teamsAFC = array();
        // iterate over the data until we find all the ones we want
        $all = $this->all();
        foreach ($all as $record)
            if ($record->conference == "AFC") {
                $teamsAFC[] = array('id' => $record->id, 'name' => $record->name, 'city' => $record->city, 'conference' => $record->conference, 'division' => $record->division, 'logo' => $record->logo);  
            }
        return $teamsAFC;
    }
    
    // retrieve NFC teams
    public function getNFC() {
        $teamsNFC = array();
        // iterate over the data until we find all the ones we want
        $all = $this->all();
        foreach ($all as $record)
            if ($record->conference == "NFC") {
                $teamsNFC[] = array('id' => $record->id, 'name' => $record->name, 'city' => $record->city, 'conference' => $record->conference, 'division' => $record->division, 'logo' => $record->logo);
            }
        return $teamsNFC;
    }

    public function updateScores($team) {
       
         // $firstEntry = array(
         //        'team' => $homeTeam,
         //        'opponent' => $awayTeam,
         //        'date' => $gameDate,
         //        'score' => $homeTeamScore,
         //        'win' => $isWin,
         //        'isHomeGame' => true
         //    );

        //get current values from HISTORY table passed from history model.
        $teamCode = $team['team'];
        $isWin = $team['win'];
        $pointsToAdd = $team['score'];
        $pointsAgainst = $team['scoreAgainst'];

        //queries to add data to the teams table. 
        //if loss
        if ($isWin == false) {
            //create query
            $query = $this->db->get_where('teams', array('id' => $teamCode));
            //return result as row
            $teamObj = $query->row();
            //get current team score and increment by new value
            $currTeamScore = $teamObj->points_for + $pointsToAdd;
            //get current points against team and increment by new value
            $currScoreAgainst = $teamObj->points_against + $pointsAgainst;
            //create new updated team object
            $udTeam = $teamObj;        
            //assign team points to points_for column  
            $udTeam->points_for = $currTeamScore;
            //assign team points against to points_against
            $udTeam->points_against = $currScoreAgainst;
            //assign net_points to new value
            $udTeam->net_points = $currTeamScore - $currScoreAgainst;
            //increment losses
            $udTeam->losses += 1;
            //update table
            $this->db->replace('teams', $udTeam);
        } else { //if win
            $query = $this->db->get_where('teams', array('id' => $teamCode));
            $teamObj = $query->row();
            $currTeamScore = $teamObj->points_for + $pointsToAdd;
            $currScoreAgainst = $teamObj->points_against + $pointsAgainst;
            $udTeam = $teamObj;
            $udTeam->points_for = $currTeamScore;
            $udTeam->points_against = $currScoreAgainst;
            $udTeam->net_points = $currTeamScore - $currScoreAgainst;
            $udTeam->wins += 1;
            $this->db->replace('teams', $udTeam);
        }
    }

}
