<?php
   session_start();

   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');
?>
<html>
   <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <link rel="stylesheet" href="/resources/stylesheet/style.css">
      <title>Ladder - Matches</title>

      <script>
         // This keeps track of the number of games that are on the score card
         numGames = 3;

         function addMoreGames() {
            // If less than five games have been played, then add another row 
            // to the score card.
            if (numGames < 5) {
               numGames++;

               // Unhide the score row
               document.getElementById("scoreRow"+numGames).style.display = "";   

               // If a 5th row has been added, then hide the add row button
               if (numGames == 5 ) {
                  document.getElementById("moreScoreRowsButton").style = "display:none";
               }
            }
         }

         /**
          * This function makes sure that the game scores have appropriate values
          */
         function validateScoreValues() {
            validScore = true;

            // Iterate over all the game scores and ensure that they're valid
            for (var gameNum = 1; gameNum <= numGames && validScore; gameNum++) {
               playerScore = document.getElementById("playerScore" + gameNum).value;
               opponentScore = document.getElementById("opponentScore" + gameNum).value;

               // Make sure that one player has scored at least 15 points and 
               // that they have at least 2 more points than the other player
               if ((playerScore < 15 && opponentScore < 15) 
                  || (playerScore - opponentScore < 2 
                     && opponentScore - playerScore < 2)) 
               {
                  validScore = false;
                  document.getElementById("invalidScoreWarning").style.display = "";
                  document.getElementById("invalidScoreWarning").style = "color:red";
                  document.getElementById("invalidScoreWarning").innerHTML = "Game " 
                     + gameNum + ": 15 points are required to win and you must win by two points";
                  document.getElementById("submitScoreButton").disabled = true;
               }
            }

            // If all the scores are valid, then hid the error message
            if (validScore) {
               document.getElementById("invalidScoreWarning").style.display = "none";
               document.getElementById("submitScoreButton").disabled = false;
            }
         }

         /**
          * This function makes sure that a player has won 3 games
          */
         function validateNumWins() {
            playerWins = 0;
            opponentWins = 0;

            // Iterate over all the game scores and count the number of wins
            for (var gameNum = 1; gameNum <= numGames && validScore; gameNum++) {
               playerScore = document.getElementById("playerScore" + gameNum).value;
               opponentScore = document.getElementById("opponentScore" + gameNum).value;

               // If this function is being called we can assume that all the 
               // score values have been validated. As such we just have to 
               // see which score is higher
               if (playerScore > opponentScore) {
                  playerWins ++;
               }
               else {
                  opponentWins ++;
               }
            }

            winnerFound =  (playerWins == 3 || opponentWins == 3);

            // If a winner wasn't found then alert the user
            if (!winnerFound) {
               alert("One player must win 3 games");
            }

            // Otherwise, if a winner was found, then put that into the form
            else {
               if (playerWins == 3) {
                  document.getElementById("matchWinner").value = "Player";
               }
               else {
                  document.getElementById("matchWinner").value = "Opponent";
               }
            }

            return winnerFound;
         }
      </script>
   </head>

   <body>
      <?php
            // This will include the navbar
         include($_SERVER['DOCUMENT_ROOT'].'/navbar/navbar.html');

         // Get the php file that connects to the database
         require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');
      ?>

      <!-- This will display the user's current match -->
      <?php
         $query = $connection->prepare("
            select challenger, challenger.name as challengerName, challengee, challengee.name as challengeeName, scheduled from challenge
               join player as challenger on challenge.challenger = challenger.username
               join player as challengee on challenge.challengee = challengee.username
            where not accepted isnull and (challenger.username = :username OR challengee.username = :username);
         ");

         $query->execute(array(':username'=>$_SESSION[username]));

         // If there was something returned then display a box for reporting scores
         $myMatchResult = $query->fetch();
         if ($myMatchResult != false) {
            echo "
               <div class='container mt-5'>
                  <div class='row justify-content-center'>
                     <div class='col-5 border border-dark' style='padding:12px 20px; background-color:white;'>
                        <h4>Your Match</h4>
                        <br/>
                        <div class='row justify-content-between'>
                           <div class='col-5'>";

            // If the user is the challenger then display the challengee's name
            if ($myMatchResult[challenger] == $_SESSION[username]) {
               echo $myMatchResult[challengeename];
            }

            // Otherwise, if the user is the challengee then display the challenger's name
            else if ($myMatchResult[challengee] == $_SESSION[username]) {
               echo $myMatchResult[challengername]; 
            }

            echo "
                           </div>
                           <div align='right' class='col-5'>
                              <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#scoreModal'>Report Scores</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            ";   
         }
      ?>

      <!-- This will display all other matches that are currently going on -->
      <?php 
         $query = $connection->prepare("
            select challenger.name as challengerName, challengee.name as challengeeName, scheduled from challenge
               join player as challenger on challenge.challenger = challenger.username
               join player as challengee on challenge.challengee = challengee.username
            where not accepted isnull and challenger != :username and challengee != :username;
         ");
         $query->execute(array(':username'=>$_SESSION[username]));
      ?>
      <div class="container mt-5">
         <div class="row justify-content-center">
            <div class="col-5 border border-dark" style="padding:12px 20px; background-color:white;">
               <h4>Current Matches</h4>
               <br/>
               <table class="table">
                  <thead class="thead thead-inverse">
                     <tr>
                        <th>Challenger</th>
                        <th>Challengee</th>
                        <th>Game Time</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php 
                     foreach($query->fetchAll() as $row) {
                        echo "
                           <tr>
                              <td>$row[challengername]</td>
                              <td>$row[challengeename]</td>
                              <td>$row[scheduled]</td>
                           </tr>
                        ";
                     }
                  ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <!-- Modal for reporting scores -->
      <div id="scoreModal" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="scoreModalHeader" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               
               <div class="modal-header">
                  <h5 class="modal-title" id="scoreModalHeader">Report the Scores of the Games</h5>
               </div>

               <form action="/matches/recordScore.php" method="post" onsubmit="return validateNumWins()">
                  <div class="modal-body">
                     <div id="gameScoreRows">

                        <div class="form-row">
                           <div class="form-group">
                              <label for="playerScore1">Your Score:</label>
                              <input type="number" class="form-control" id="playerScore1" name="playerScore1" placeholder="Game 1" oninput="validateScoreValues()" required>
                           </div>
                           <div class="form-group">
                              <label for="opponentScore1">Your Opponent's Score:</label>
                              <input type="number" class="form-control" id="opponentScore1" name="opponentScore1" placeholder="Game 1" oninput="validateScoreValues()" required>
                           </div>
                        </div>

                        <div class="form-row">
                           <div class="form-group">
                              <input type="number" class="form-control" id="playerScore2" name="playerScore2" placeholder="Game 2" oninput="validateScoreValues()" required>
                           </div>
                           <div class="form-group">
                              <input type="number" class="form-control" id="opponentScore2" name="opponentScore2" placeholder="Game 2" oninput="validateScoreValues()" required>
                           </div>
                        </div>

                        <div class="form-row">
                           <div class="form-group">
                              <input type="number" class="form-control" id="playerScore3" name="playerScore3" placeholder="Game 3" oninput="validateScoreValues()" required>
                           </div>
                           <div class="form-group">
                              <input type="number" class="form-control" id="opponentScore3" name="opponentScore3" placeholder="Game 3" oninput="validateScoreValues()" required>
                           </div>
                        </div>
                        
                        <div id="scoreRow4" style="display:none" class="form-row">
                           <div class="form-group">
                              <input type="number" class="form-control" id="playerScore4" name="playerScore4" placeholder="Game 4" oninput="validateScoreValues()">
                           </div>
                           <div class="form-group">
                              <input type="number" class="form-control" id="opponentScore4" name="opponentScore4" placeholder="Game 4" oninput="validateScoreValues()">
                           </div>
                        </div>

                        <div id="scoreRow5" style="display:none" class="form-row">
                           <div class="form-group">
                              <input type="number" class="form-control" id="playerScore5" name="playerScore5" placeholder="Game 5" oninput="validateScoreValues()">
                           </div>
                           <div class="form-group">
                              <input type="number" class="form-control" id="opponentScore5" name="opponentScore5" placeholder="Game 5" oninput="validateScoreValues()">
                           </div>
                        </div>

                     </div>
                     <button id="moreScoreRowsButton" type="button" class="btn" onclick="addMoreGames()">Add Game</button>
                     <div id="invalidScoreWarning" style="display:none;"></div>

                     <input type="hidden" id="matchWinner" name="matchWinner"></input>
                  </div>

                  <div class="modal-footer">
                     <input id="submitScoreButton" type="submit" class="btn" value="Report Scores"></input>
                     <input type="reset" class="btn" data-dismiss="modal" value="Cancel"></input>
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Get bootstrap JavaScript -->
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
   </body>
</html>
