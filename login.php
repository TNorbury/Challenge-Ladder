<?php 
   $username = $_POST['username'];
   $password = $_POST['password'];

   // Connect to the database to retrieve the user's username and password
   $db = pg_connect("host=localhost dbname=ladder user=bitnami password=bitnami");
   $query = "select username, password from player where username='$username'";
   $result = pg_query($db, $query);

   echo '
   <html>
   <head>
   <script>
   ';
   
   // Get the data returned by the database
   $row = pg_fetch_row($result);
   $dbUsername = $row[0];
   $dbPassword = $row[1];

   // If the username doesn't exist in the database OR the password is 
   // incorrect, then don't allow them to log in.
   if (!$dbUsername || $password != $dbPassword) {
      echo '
      function logIn() {
         alert("The username or password is incorrect");
         window.location="/login.html";
      }
      ';
   }

   // Otherwise, if the username is valid, and the password is correct, then redirect
   // to the ladder
   // We'll want to change this so that it decrypts the PW in the database before checking for a match
   else if ($dbUsername && $password == $dbPassword) {
      echo '
      function logIn() {
         window.location="/ladder/ladder.php";
      }
      ';

      // Set the session username to the user that just logged in
      $_SESSION['username'] = $username;
   }

   echo '
   </script>
   </head>
   <body onload="logIn()">
   </body>
   </html>
   ';
?>
