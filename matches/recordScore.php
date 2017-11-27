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

   // Go through all the game results and record the results into the database
   for ($gameNum = 1; $gameNum <= 5; $gameNum++) {

      // If either score is blank, then assume that we have looked at all the games
      if ($_POST["playerScore".$gameNum] == "" || $_POST["opponentScore".$gameNum] == "") {
         break;
      }

      $query = $connection->prepare("
         insert into game (winner, loser, played, number, winner_score, loser_score)
         values(:winner, :loser, CURRENT_DATE, :gameNum, :winnerScore, :loserScore)
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
         $winnerScore = (int)$_POST["opponentScore".$gameNum];
         $loser = $challenger;
         $loserScore = (int)$_POST["playerScore".$gameNum];
      }
      else if ((!$opponentIsChallenger) 
         && ((int)$_POST["opponentScore".$gameNum] < (int)$_POST["playerScore".$gameNum]))
      {
         $winner = $challenger;
         $winnerScore = (int)$_POST["playerScore".$gameNum];
         $loser = $challengee;
         $loserScore = (int)$_POST["opponentScore".$gameNum];
      }

      // Now execute the query
      $query->execute(array(':winner'=>$winner, ':winnerScore'=>$winnerScore, 
         ':loser'=>$loser, ':loserScore'=>$loserScore, ':gameNum'=>$gameNum));
   }
   
   // Move the winner and loser to their appropriate place on the database
   // Determine if the match winner was the challengee or challenger.
   if (($opponentIsChallenger && $_POST[matchWinner] == "Opponent") 
      || (!$opponentIsChallenger && $_POST[matchWinner] == "Player")) 
   {
      // Challenger is the winner. We need to readjust the ladder.
      
      // Get challenger and challengee's current rank
      $query = $connection->prepare("
         select username, rank from player
         where username = :challenger or username = :challengee
      ");
      $query->execute(array(':challenger'=>$challenger, ':challengee'=>$challengee));

      // Iterate through the results of the query (there should only be two) 
      // and get the rank of the challenger and challengee
      foreach($query->fetchAll() as $row) {
         if ($row[username] == $challenger) {
            $challengerRank = $row[rank];
         }
         else {
            $challengeeRank = $row[rank];
         }
      }
      
      // Set the challenger's rank to 0, so that the other ranks can be 
      // adjusted without causing any unique value violations
      $query = $connection->prepare("
         update player
         set rank = 0
         where username = :challenger
      ");
      $query->execute(array(':challenger'=>$challenger));
      // Get all the players who need their rank update from the database
      $query = $connection->prepare("
         select username, rank from player
         where rank >= :challengeeRank and rank < :challengerRank
         order by rank DESC
      ");
      $query->execute(array(':challengeeRank'=>$challengeeRank, 
         ':challengerRank'=>$challengerRank));

      // Go through all these people and update their rank in the database
      foreach($query->fetchAll() as $row)
      {
         $newRank = (int)$row[rank] + 1;
         
         $query = $connection->prepare("
            update player
            set rank = :newRank
            where username = :username
         ");
         $query->execute(array(':newRank'=>$newRank, ':username'=>$row[username]));
      }

      // Set challenger's rank equal to challengee's (old) rank
      $query = $connection->prepare("
         update player
         set rank = :challengeeRank
         where username = :challenger
      ");
      $query->execute(array(':challengeeRank'=>$challengeeRank, ':challenger'=>$challenger));
   }

   // Delete the challenge that was just completed from the database
   $query = $connection->prepare("
      delete from challenge
      where challenger = :challenger and challengee = :challengee
   ");
   $query->execute(array(':challengee'=>$challengee, ':challenger'=>$challenger));

   // Redirect the user to the ladder now that their match is over
   header("Location: /ladder/ladder.php");
?>
