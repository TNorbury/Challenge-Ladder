<?php 
   session_start();
   $username = $_POST['username'];
   $password = $_POST['password'];

   // Connect to the database
   require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

   // Connect to the database to retrieve the user's username and password
   $query = $connection->prepare("select username, password from player where username=:username");
   $query->execute(array(':username'=>$username));
   
   // Get the data returned by the database
   $row = $query->fetch(PDO::FETCH_ASSOC);
   $dbUsername = $row[username];
   $validPassword = $row[password];

   // If the username doesn't exist in the database OR the password is 
   // incorrect, then don't allow them to log in.
   if (!$dbUsername || !password_verify($password, $validPassword)) {
      echo '
      <html>
      <head>
      <script>
      function invalidLogin() {
         alert("The username or password is incorrect");
         window.location = "/index.html";
      }
      </script>
      </head>
      <body onload="invalidLogin()">
      </body>
      </html>
      ';
   }

   // Otherwise, if the username is valid, and the password is correct, then redirect
   // to the ladder
   else if ($dbUsername && password_verify($password, $validPassword)) {
      // Set the session username to the user that just logged in
      $_SESSION["username"] = $username;

      // Redirect to the ladder page
      header("Location: /ladder/ladder.php");
      exit();
   }

?>
