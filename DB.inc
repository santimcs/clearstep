<?php
   $hostName = "localhost";
   $databaseName = "stock";
   $username = "root";
   $password = "";

   function showerror( )
   {
      die("Error " . mysql_errno( ) . " : " . mysql_error( ));
   }
?>