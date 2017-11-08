<?php
   session_start();
?>
<html>
   <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <link rel="stylesheet" href="/resources/stylesheet/style.css">
      <title>Ladder</title>
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
                              <td align='right'><button class='btn btn-info btn-sm'>Challenge</button></td>";
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
   </body>
</html>
