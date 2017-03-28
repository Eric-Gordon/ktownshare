<!DOCTYPE HTML>
<html>
    <head>
        <title>Welcome to mysite</title>
  
    </head>
<body>
<?php
	echo "
	<style>
	input[type=submit]{
		width: 10em;
	}
	</style>";
?>

 <?php
  //Create a user session or resume an existing one
 session_start();
 ?>
 
 <?php
 if(isset($_GET['vin'])){
	 $vin = $_GET['vin'];
 }
 ?>
 
 <?php
if(isset($_SESSION['Member_id'])){
   // include database connection
    include_once 'config/connection.php'; 
	
	// SELECT query
        $query = "SELECT Member_id, Email, Name FROM members WHERE Member_id=?";
 
        // prepare query for execution
        $stmt = $con->prepare($query);
		
        // bind the parameters. This is the best way to prevent SQL injection hacks.
        $stmt->bind_Param("s", $_SESSION['Member_id']);
        // Execute the query
		$stmt->execute();
 
		// results 
		$result = $stmt->get_result();
		
		// Row data
		$myrow = $result->fetch_assoc();
		
} else {
	//User is not logged in. Redirect the browser to the login index.php page and kill this page.
	header("Location: index.php");
	die();
}
?>

	<form name='reserve' id='reserve' action='reserve.php' method='post' >
		
	</form>
</body>
</html>