<?php

if (!isset($_COOKIE['statex_id'])) 
{
  header('location: logon.php');  
}  
else
{
  header('location: report.php');
}  

?>
