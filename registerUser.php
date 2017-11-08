<?php 
   // Connect to the database
   require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

   // This is the query that'll add the person to the database
   $query = $connection->prepare("insert into player (name, email, rank, phone,
                                                      username, password) 
                                 select :name, :email, max(rank)+1, :phone, 
                                        :username, :password from player");

   // Execute the query
   $query->execute(array(':name'=>$_POST['name'], ':email'=>$_POST['email'],
      ':phone'=>$_POST['phone'], ':username'=>$_POST['username'], 
      ':password'=>$_POST['password']));

   // If no rows were inserted, then the user wasn't registered to the database
   if ($query->rowCount() == 0) {
      echo "Error: Could not register to the database";
      die();
   }
   
   // If the user was able to register, display a message and redirect to the
   // login page
   echo '
   <html>
   <head>
   <script>
   function redirect() {
      alert("You have been registered to the Ladder!");
      window.location="/login.html";
   }
   </script>
   </head>
   <body onload="redirect()">
   </body>
   </html>';
?>
