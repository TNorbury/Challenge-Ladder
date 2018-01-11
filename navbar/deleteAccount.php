<?php 
   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');

   // Get the php file that connects to the database
   require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

   // Start off by getting the rank of the current player
   $query = $connection->prepare("
      select rank from player
      where username = :username
   ");
   $query->execute(array(':username'=>$_SESSION[username]));

   // Get the rank from the query result
   $rank = $query->fetch()[rank];

   // Delete all challenges that the player is associated with
   $query = $connection->prepare("
      delete from challenge
      where challenger = :username or challengee = :username
   ");
   $query->execute(array(':username'=>$_SESSION[username]));

   // Delete all games the player was involved in
   $query = $connection->prepare("
      delete from game
      where winner = :username or loser = :username
   ");
   $query->execute(array(':username'=>$_SESSION[username]));

   // Delete the player from the DB
   $query = $connection->prepare("
      delete from player
      where username = :username
   ");
   $query->execute(array(':username'=>$_SESSION[username]));

   // Get all the people who ranked below the player who was just deleted
   $query = $connection->prepare("
      select username, rank from player
      where rank > :deletedRank
   ");
   $query->execute(array(':deletedRank'=>$rank));

   // Iterate over all the people below the deleted player and increase their rank
   foreach($query->fetchAll() as $row) {
      $newRank = (int)$row[rank] - 1;

      $query = $connection->prepare("
         update player
         set rank = :newRank
         where username = :username
      ");
      $query->execute(array(':newRank'=>$newRank, ':username'=>$row[username]));
   }

   // Now redirect the user to the logout page (this will ensure that their session is destoryed
   echo "
   <body>
   <script>
      alert('You have been removed from the ladder');
      window.location='/navbar/logout.php';
   </script>
   </body>
   ";
?>
