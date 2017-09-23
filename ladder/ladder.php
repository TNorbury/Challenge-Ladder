<html>
   <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <title>Ladder</title>
   </head>

   <body style="background: #99b3ff;">
      <div class="container" style="background: #0000;">
         <?php
            $db_connection = pg_connect("host=localhost dbname=ladder user=bitnami password=bitnami");

            $result = pg_query($db_connection ,"select rank, name from player");

            echo "<table class='table table-hover table-bordered'>\n";
            echo "<thead class='thead-inverse'>\n";
            echo "<tr>\n";
            echo "<th>Rank</th>\n";
            echo "<th>Player</th>\n";
            echo "</tr>\n";
            echo "</thead>\n";

            while ($row = pg_fetch_row($result)) {
               echo "<tr class='table-light'>\n";

               echo "<td>$row[0]</td>\n";
               echo "<td>$row[1]</td>\n";

               echo "</tr>\n";
            }
         
            echo "</table>\n";
         ?>
      </div>
   </body>
</html>
