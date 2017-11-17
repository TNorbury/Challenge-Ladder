<?php
   session_start();

   // Make sure there is somebody logged in
   require($_SERVER['DOCUMENT_ROOT'].'/resources/sharedScripts/ensureLogin.php');
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

         // Get the php file that connects to the database
         require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

         // Prepare and execute the query to get all the challenges made 
         // against the user
         $othersChallengesQuery = $connection->prepare("
            select challenger, scheduled, name from challenge
               join player on challenger = username
            where challengee = :username
         ");
         $othersChallengesQuery->execute(array(':username'=>$_SESSION[username]));

         // Prepare and execute the query to get all the challenges the user has made
         $myChallengesQuery = $connection->prepare("
            select challengee, scheduled, name from challenge
               join player on challengee = username
            where challenger = :username
         ");
         $myChallengesQuery->execute(array(':username'=>$_SESSION[username]));


      ?>
      
      <div class="container mt-5">
         <!-- This will have all the challenges made against the user  -->
         <h5 class="row justify-content-center">Challenges Issued Against Me</h5>
         <div class="row justify-content-center">
            <div class="col-6">
               <table class="table border border-dark">
                  <thead class="thead-inverse">
                     <tr>
                        <th>Player</th>
                        <th style="text-align:center;">Challenge Date</th>
                        <th></th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        foreach ($othersChallengesQuery->fetchAll() as $row) {
                           echo "
                              <tr class='table-light'>
                                 <form action='http://dhansen.cs.georgefox.edu/~dhansen/Classes/ClientServer/Protected/Examples/echoForm.php' method='post'>
                                    <td>$row[name]</td>
                                    <td align='right'><input style='border:none;' name='challengeDate' value='$row[scheduled]' readonly></input></td>
                                    <input type='hidden' name='challengeeUsername' value='$row[challenger]' readonly></input>
                                    <td align='right'>
                                       <input class='btn btn-primary btn-sm' type='submit' name='acceptChallenge' value='Accept'></input>
                                       <input class='btn btn-primary btn-sm' type='submit' name='rejectChallenge' value='Reject'></input>
                                 </form>
                              </tr>
                          ";
                        }
                     ?>
                  </tbody>
               </table>
            </div>
         </div>

         <!-- This will have all the challenges the user has made  -->
         <br/>
         <h5 class="row justify-content-center">Challenges Issued Against Others</h5>
         <div class="row justify-content-center">
            <div class="col-6">
               <table class="table border border-dark">
                  <thead class="thead-inverse">
                     <tr>
                        <th>Player</th>
                        <th style="text-align: right">Challenge Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        foreach ($myChallengesQuery->fetchAll() as $row) {
                           echo "
                              <tr class='table-light'>
                                 <td>$row[name]</td>
                                 <td align='right'>$row[scheduled]</td>
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
