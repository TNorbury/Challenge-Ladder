      <?php 
         $name = $_POST['name'];
         $username = $_POST['username'];
         $password = $_POST['password'];
         $email = $_POST['email'];
         $phone = $_POST['phone'];

         // Connect to the database
         $db = pg_connect("host=localhost dbname=ladder user=bitnami password=bitnami");

         // This is the query we'll send to the database
         $query = "insert into player (name, email, rank, phone, username, password) select '$name', '$email', max(rank)+1, '$phone', '$username', '$password' from player";

         // Query the database
         $result = pg_query($db, $query);

         if ($result == false) {
            echo "Error: Could not register to the database";
            die();
         }
         
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
