<?php
   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');

   // Get the php file that connects to the database
   require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

   // Prepare a sql statement to add the challenge to the database
   $challengeQuery = $connection->prepare("
      insert into challenge(challenger, challengee, issued, scheduled)
      values(:challengerUsername, :challengeeUsername, now(), :challengeDate)
   ");

   // Execute the sql statement
   $challengeQuery->execute(array('challengerUsername'=>$_SESSION[username], 'challengeeUsername'=>$_POST[challengeeUsername], 'challengeDate'=>$_POST[challengeDate]));

   // Make sure that the challenge was added
   if ($challengeQuery->rowCount() == 0) {
      echo "ERROR: Couldn't issue the challenge";
      die();
   }

   // Redirect the user (ladder page or challenge page?)
   echo "
      <body>
      <script>
         alert('You have sucessfully challenged $_POST[challengee]');
         window.location='/ladder/ladder.php';
      </script>
      </body>
   ";
?>
