<!DOCTYPE HTML>
<html>
    <head>
        <title>Welcome to mysite</title>
  
    </head>
<body>

 <?php
  //Create a user session or resume an existing one
 session_start();
 ?>
 
 <?php
 //check if the user clicked the logout link and set the logout GET parameter
if(isset($_GET['logout'])){
	//Destroy the user's session.
	$_SESSION['Member_id']=null;
	session_destroy();
}
 ?>
 
 
 <?php
 //check if the user is already logged in and has an active session
if(isset($_SESSION['Member_id'])){
	//Redirect the browser to the profile editing page and kill this page.
	header("Location: profile.php");
	die();
}
 ?>
 
 <?php
 
//check if the login form has been submitted
if(isset($_POST['loginBtn'])){
 
    // include database connection
    include_once 'config/connection.php'; 

	// SELECT query
        $query = "SELECT Member_id, Email, password FROM members WHERE Email=? AND password=?";
 
        // prepare query for execution
        if($stmt = $con->prepare($query)){
		
        // bind the parameters. This is the best way to prevent SQL injection hacks.
        $stmt->bind_Param("ss", $_POST['Email'], $_POST['password']);
         
        // Execute the query
		$stmt->execute();
 
		/* resultset */
		$result = $stmt->get_result();
		// Get the number of rows returned
		$num = $result->num_rows;
		
		if($num>0){
			//If the username/password matches a user in our database
			//Read the user details
			$myrow = $result->fetch_assoc();
			//Create a session variable that holds the user's id
			$_SESSION['Member_id'] = $myrow['Member_id'];
			//Redirect the browser to the profile editing page and kill this page.
			header("Location: profile.php");
			die();
		} else {
			//If the username/password doesn't matche a user in our database
			// Display an error message and the login form
			echo "Failed to login";
		}
		} else {
			echo "failed to prepare the SQL";
		}
 }
 if(isset($_POST['signUpBtn'])){
	header("Location: signup.php");
	die();
 }
 
?>

<!-- dynamic content will be here -->
 <form name='login' id='login' action='index.php' method='post'>
    <table border='0'>
        <tr>
            <td>Username(e-mail)</td>
            <td><input type='text' name='Email' id='Email' /></td>
        </tr>
        <tr>
            <td>Password</td>
             <td><input type='password' name='password' id='password' /></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type='submit' id='loginBtn' name='loginBtn' value='Log In' />				
            </td> 
        </tr>
    </table>
	<p>If you don't have an account please click the sign up button <input type='submit' id='signUpBtn' name='signUpBtn' value='Sign Up' /><p>
</form>

</body>
</html>