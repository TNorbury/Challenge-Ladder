<?php
   session_start();

   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');
?>
<html>
   <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <link rel="stylesheet" href="/resources/stylesheet/style.css">
      <title>Ladder</title>

      <script>
         // This function changes the necessary text within the modal
         function openChallengeModal(challengee, challengeeUsername) {
           // This will set text in the modal to reflect the person to be challeneged
           document.getElementById("challengeModalHeader").innerHTML = "Challenge " + challengee + "!";  
           document.getElementById("challengeModalName").value = challengee;
           document.getElementById("challengeModalUsername").value = challengeeUsername;

           // Reset the date field to its default value
           document.getElementById("challengeModalDate").value = "mm/dd/yyyy";
         }
      </script>
   </head>

   <body>
      <?php
         // This will include the navbar
         include($_SERVER['DOCUMENT_ROOT'].'/navbar/navbar.html');

         // Get the current user's username
         $username = $_SESSION["username"];
      ?>
      <div class="container mt-5">
         <div class="row justify-content-center">
            <div class="col-7 ">
               <?php
                  
                  // Get the php file that connects to the database
                  require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

                  // This will get all the players that are in the ladder
                  $ladderResult = $connection->query("select rank, name, username from player order by rank"); 

                  // This will prepare a query that will get all the people that can be challenged by the current user
                  $challengeesQuery = $connection->prepare(
                     "select p.name as challenger, c.name as challengee, c.username as challengeeUsername
                        from player as p, player as c 
                        where p.username = :username and
                        c.rank between (p.rank-3) and (p.rank-1) and
                        not exists (select * from challenge
                           where (challenger = c.username or
                                  challengee = c.username or
                                  challenger = p.username or
                                  challengee = p.username) and
                           not accepted isNull)"
                  );

                  // Now we'll execute the above query to get all the users available to challenge
                  $challengeesQuery->execute(array(':username'=>$username));
                  // Put all of the player the user can challenge into an array
                  $challengees = array();
                  foreach($challengeesQuery->fetchAll() as $resultRow) {
                     array_push($challengees, $resultRow['challengeeusername']);
                  }

                  // Create a table to display the results of the query
                  echo "<table class='table table-hover'>\n";
                  echo "<thead class='thead-inverse'>\n";
                  echo "<tr>\n";
                  echo "<th>Rank</th>\n";
                  echo "<th colspan='2'>Player</th>\n";
                  echo "</tr>\n";
                  echo "</thead>\n";
                 
                  // Iterate over the query results and display them as a "ladder"
                  echo "<tbody>";
                  foreach($ladderResult->fetchAll() as $resultRow)
                  {
                     // If this row is the row of the current user, then highlight it
                     if ($username == $resultRow[username])
                     {
                        echo "<tr class='table-primary'>\n";
                     }
                     else 
                     {
                        echo "<tr class='table-light'>\n";
                     }

                     echo "<td>$resultRow[rank]</td>\n";

                     // If this person can be challenged by the user, then put a button to challenge them
                     if (in_array($resultRow[username], $challengees))
                     {
                        echo "<td>$resultRow[name]</td>
                              <td align='right'>
                                 <button class='btn btn-info btn-sm' data-toggle='modal' data-target='#challengeModal' onclick='openChallengeModal(\"$resultRow[name]\", \"$resultRow[username]\")'>Challenge</button>
                              </td>";
                     }
                     else
                     {
                        echo "<td colspan=2 style='height:56px'>$resultRow[name]</td>";
                     }

                     echo "</tr>\n";
                  }
                  echo "</tbody>";
               
                  echo "</table>\n";
               ?>
            </div>
         </div>
      </div>

      <!-- Modal for issuing challenges -->
      <div id="challengeModal" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="challengeModalHeader" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               
               <div class="modal-header">
                  <h5 class="modal-title" id="challengeModalHeader">Challenge This Person!</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>

               <form action="/ladder/issueChallenge.php" method="post">
               <div class="modal-body">
                  <div class="d-flex justify-content-between">
                     <div><label for="challengee">Challengee: </label></div>
                     <div><input type="text" id="challengeModalName" name="challengee" readonly></div>
                  </div>
                  <div class="d-flex justify-content-between">
                     <div><label for="challengeDate">Challenge Date: </label></div>
                     <div><input type="date" name="challengeDate" id="challengeModalDate" required></div>
                  </div>
                  <input type="hidden" name="challengeeUsername" id="challengeModalUsername" readonly>
               </div>

               <div class="modal-footer">
                  <input type="submit" class="btn" value="Issue Challenge"></input>
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
