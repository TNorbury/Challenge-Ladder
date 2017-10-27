<?php
   session_start();
?>
<html>
   <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <link rel="stylesheet" href="/resources/stylesheet/style.css">
      <title>Ladder - My Challenges</title>
   </head>

   <body>
      <?php
            // This will include the navbar
         include($_SERVER['DOCUMENT_ROOT'].'/navbar/navbar.html');
      ?>
      All the challenges that have been made against the user, and those that the user has made, will appear here
   </body>
</html>
