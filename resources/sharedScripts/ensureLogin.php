<?php
   session_start();

   // If there isn't a current user logged in to the session redirect them to
   // login page
   if ($_SESSION["username"] == "")
   {
      header("Location: /login.html");
   }

   // Otherwise, just carry on
?>
