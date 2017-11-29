<?php 
   // Connect to the database
   require($_SERVER['DOCUMENT_ROOT'].'/dbConnect/dbConnect.php');

   // Before we start the query we'll hash the password
   $hashPW = password_hash($_POST['password'], PASSWORD_DEFAULT);

   // This is the query that'll add the person to the database
   $query = $connection->prepare("insert into player (name, email, rank, phone,
                                                      username, password) 
                                 select :name, :email, max(rank)+1, :phone, 
                                        :username, :password from player");

   // Execute the query
   $query->execute(array(':name'=>$_POST['name'], ':email'=>$_POST['email'],
      ':phone'=>$_POST['phone'], ':username'=>$_POST['username'], 
      ':password'=>$hashPW));

   // If no rows were inserted, then the user wasn't registered to the database
   if ($query->rowCount() == 0) {
      echo '
      <html>
      <body>
      <script>
         alert("Error: Cou not reigster to the database!");
         window.location="/registerUser.html";
      </script>
      </body>
      </html>';
      die();
   }
   
   // If the user was able to register, display a message and redirect to the
   // login page
   echo '
   <html>
   <body>
   <script>
      alert("You have been registered to the Ladder!");
      window.location="/index.html";
   </script>
   </body>
   </html>';
?>
