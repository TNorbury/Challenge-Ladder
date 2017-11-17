<?php

   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');

   // Get the php file that connects to the database
   require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

   // If the challenge is accepted
   if (isset($_POST[acceptChallenge]))
   {
      // Update DB so that it shows challenge as accepted
      $query = $connection->prepare("
         update challenge
            set accepted=now()
         where challenger = :challengerUsername 
            and challengee = :challengeeUsername 
            and scheduled = :challengeDate
      ");

      // Execute the query and then make sure that it updated something
      $query->execute(array(':challengerUsername'=>$_POST[challengerUsername], ':challengeeUsername'=>$_SESSION[username], ':challengeDate'=>$_POST[challengeDate]));

      if ($query->rowCount() != 1) {
         echo "Couldn't accept the challenge";
         die();
      }

      // Delete all other challenges by or against the user
      $query = $connection->prepare("
         delete from challenge
         where (challenger = :user or challengee = :user) 
            and accepted isnull
      ");

      // Execute the query
      $query->execute(array(':user'=>$_SESSION[username]));

      // Alert the user that the challenge has been accepted and redirect 
      // them to challenge page
      echo "
         <body>
         <script>
            alert('You have accepted $_POST[challengerUsername]\'s challenge');
            window.location = '/challenges/challenges.php';
         </script>
         </body>
      ";
   }

   // Otherwise if the challenge was rejected
   else if (isset($_POST[rejectChallenge]))
   {
      // Remove the challenge from the database
      $query = $connection->prepare("
         delete from challenge
         where challenger = :challengerUsername
            and challengee = :challengeeUsername
            and scheduled = :challengeDate
      ");

      $query->execute(array(':challengerUsername'=>$_POST[challengerUsername], ':challengeeUsername'=>$_SESSION[username], ':challengeDate'=>$_POST[challengeDate]));
      
      if ($query->rowCount() != 1) {
        echo "Couldn't reject the challenge";
        die();
      }

      // Alert the user that the challenge has been rejected and redirect them
      // to the challenge page
      echo "
         <body>
         <script>
            alert('You have rejected $_POST[challengerUsername]\'s challenge');
            window.location = '/challenges/challenges.php';
         </script>
         </body>
      ";
   }
?>
