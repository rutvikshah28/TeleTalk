<?php 
session_start();
$db = new mysqli("localhost", "rrs680", "rut28vik", "rrs680");
$email = $_SESSION["email"];
$q1 = "UPDATE usersTT SET loggedIn = false WHERE email = '$email'";
$r1 = $db->query($q1);
// delete all of the session variables
session_unset();
session_destroy();
	
// redirect the user back to the login page
header("Location: home.php");
exit();

?>
