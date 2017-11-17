<?php
   session_start();

   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');
?>
<html>
   <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <link rel="stylesheet" href="/resources/stylesheet/style.css">
      <title>Ladder - Stats</title>
   </head>

   <body>
      <?php
         // This will include the navbar
         include($_SERVER['DOCUMENT_ROOT'].'/navbar/navbar.html');

         // Get the php file that connects to the database
         require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

         // Get all the players and their stats from the database
         $result = $connection->query("
            select name, match_win_pct, game_win_pct, avg_win_margin, avg_lose_margin 
            from (
               select p.name, p.rank,
               coalesce( (select cast(count(*) as float(2)) from match_view as m
                  where m.winner = p.username)
                  /
                  (select cast(count(*) as float(2)) from match_view as m
                  where m.winner = p.username or m.loser = p.username), 0.0)
                  as match_win_pct,
               coalesce( (select cast(count(*) as float(2)) from game as g
                  where g.winner = p.username)
                  /
                  (select cast(count(*) as float(2)) from game as g
                  where g.winner = p.username or g.loser = p.username), 0.0)
                  as game_win_pct,
               coalesce( (select avg(winner_score - loser_score) from game as g
                  where p.username=g.winner), 0.0) as avg_win_margin,
               coalesce( (select avg(winner_score - loser_score) from game as g
                  where p.username=g.loser), 0.0)  as avg_lose_margin
               from player as p
               where exists (select * from match_view as m where p.username = m.winner or
                  p.username=m.loser)
               union
               select name, rank, 0.0, 0.0, 0.0, 0.0 from player
               where not exists (select * from match_view where username = winner or 
                  username = loser)
            ) as unordered_stats
            order by rank ASC
         ");

      ?>

      <div class="container mt-5">
         <div class="row justify-content-center">
            <div class="col-8">
               <table class="table table-hover border border-dark">
                  <thead class="thead-inverse">
                     <tr>
                        <th>Name</th>
                        <th>Match Win %</th>
                        <th>Game Win %</th>
                        <th>Avg. Win Margin</th>
                        <th>Avg. Loss Margin</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        foreach($result->fetchAll() as $resultRow)
                        {
                           echo "
                              <tr class='table-light'>
                                 <td>$resultRow[name]</td> 
                                 <td align='center'>".(round($resultRow[match_win_pct], 2)*100)."%</td> 
                                 <td align='center'>".(round($resultRow[game_win_pct], 2)*100)."%</td> 
                                 <td align='center'>".round($resultRow[avg_win_margin], 2)."</td> 
                                 <td align='center'>".round($resultRow[avg_lose_margin], 2)."</td> 
                              </tr>";
                        }
                     ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>

   </body>
</html>
