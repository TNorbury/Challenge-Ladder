<?php
   session_start();
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
      ?>
      This page will display all the people on the ladder, along with various stats (# of games won/lost, win %, etc.) for all of the players.
   </body>
</html>
