<?php 
   
   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');
   

   // Get the php file that connects to the database
   require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

   // Determine who the challenger and challengee were in the match
   $query = $connection->prepare("
      select challenger, challengee from challenge
      where challenger = :username OR challengee = :username
   ");
   $query->execute(array(':username'=>$_SESSION[username]));
   $myChallenge = $query->fetch();
   $challenger = $myChallenge[challenger];
   $challengee = $myChallenge[challengee];
   
   // Determine if the challenger is the opponent
   $opponentIsChallenger = $challenger != $_SESSION[username];
   $winner = "";
   $winnerScore = 0;
   $loser = "";
   $loserScore = 0;

   // Go through all the game results and record the results into the database
   for ($gameNum = 1; $gameNum <= 5; $gameNum++) {

      // If either score is blank, then assume that we have looked at all the games
      if ($_POST["playerScore".$gameNum] == "" || $_POST["opponentScore".$gameNum] == "") {
         break;
      }

      $query = $connection->prepare("
         insert into game (winner, loser, played, number, winner_score, loser_score)
         values(:winner, :loser, now(), :gameNum, :winnerScore, :loserScore)
      ");


      // Determine if the challenger or challengee won the game
      if (($opponentIsChallenger) 
         && ((int)$_POST["opponentScore".$gameNum] > (int)$_POST["playerScore".$gameNum]))
      {
         $winner = $challenger;
         $winnerScore = (int)$_POST["opponentScore".$gameNum];
         $loser = $challengee;
         $loserScore = (int)$_POST["playerScore".$gameNum];
      }
      else if (($opponentIsChallenger) 
         && ((int)$_POST["opponentScore".$gameNum] < (int)$_POST["playerScore".$gameNum]))
      {
         $winner = $challengee;
         $winnerScore = (int)$_POST["playerScore".$gameNum];
         $loser = $challenger;
         $loserScore = (int)$_POST["opponentScore".$gameNum];
      }
      else if ((!$opponentIsChallenger) 
         && ((int)$_POST["opponentScore".$gameNum] > (int)$_POST["playerScore".$gameNum]))
      {
         $winner = $challengee;
         $winnerScore = (int)$_POST["playerScore".$gameNum];
         $loser = $challenger;
         $loserScore = (int)$_POST["opponentScore".$gameNum];
      }
      else if ((!$opponentIsChallenger) 
         && ((int)$_POST["opponentScore".$gameNum] < (int)$_POST["playerScore".$gameNum]))
      {
         $winner = $challenger;
         $winnerScore = (int)$_POST["opponentScore".$gameNum];
         $loser = $challengee;
         $loserScore = (int)$_POST["playerScore".$gameNum];
      }

      // Now execute the query
      $query->execute(array(':winner'=>$winner, ':winnerScore'=>$winnerScore, 
         ':loser'=>$loser, ':loserScore'=>$loserScore, ':gameNum'=>$gameNum));
   }
   

   // Move the winner and loser to their appropriate place on the database

   // Delete the challenge that was just completed from the database
?>
