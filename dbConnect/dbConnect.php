<?php
   $dbHost = "localhost";
   $dbDatabase = "ladder";
   $dbUsername = "bitnami";
   $dbPassword = "bitnami";

   // Try to connect to the database
   try {
      $connection = 
         new PDO("pgsql:dbname='$dbDatabase' host='$dbHost' password='$dbPassword' user='$dbUsername'",
            $dbUsername, $dbPassword, array(PDO::ATTR_PERSISTENT => true));
   }
   catch (PDOException $e) {
      print "Error!:" + $e->getMessage() ."<br/>";
      die();
   }
?>
