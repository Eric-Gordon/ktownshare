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

<script>
	function validate() {
		var name = document.getElementById('Name').value.replace(/\s/g, '').length;
		var address = document.getElementById('Address').value.length;
		var phone = document.getElementById('Phone').value.replace(/\D/g, '');
		var license = document.getElementById('License').value;
		var email = document.getElementById('Email').value;
		var password = document.getElementById('password').value;
		if(name < 2){
			alert('Please fill in your name.');
			return false;
		}
		else if(address < 4){
			alert('Please fill in your address.');
			return false;
		}
		else if(phone.length<9 || (phone.match(/^\d+$/)==false)){
			alert("Please fill in your phone number using only digits.");
			return false;
		}
		else if(license.length<9){
			alert("Please fill in your license.");
			return false;
		}
		else if(email.length<2){
			alert("Please fill in your email.");
			return false;
		}
		else if(password.length<4){
			alert("Your password must be at least 4 characters long.");
			return false;
		}
		else{
			return true;
		}
	}
</script>
 
 <?php
//check if the login form has been submitted
if(isset($_POST['createBtn'])){
	
	include_once 'config/connection.php';
	
	$Member_id = mt_rand(100000000,999999999);
	$idcheck = "SELECT Member_id FROM members WHERE Member_id=$Member_id";
 
        // prepare query for execution
        if($stmt = $con->prepare($idcheck)){
         
        // Execute the query
		$stmt->execute();
 
		/* resultset */
		$result = $stmt->get_result();
		// Get the number of rows returned
		$num = $result->num_rows;
		
		if($num>0){
			$Member_id = mt_rand(100000000,999999999);
		}
		} else {
			echo "failed to prepare the SQL";
		}
		
		$usercheck = "SELECT Member_id, Email FROM members WHERE Email=?";
 
        // prepare query for execution
        if($stmt = $con->prepare($usercheck)){
			$stmt->bind_Param("s", $_POST['Email']);
			// Execute the query
			$stmt->execute();
 
			/* resultset */
			$result = $stmt->get_result();
			// Get the number of rows returned
			$num = $result->num_rows;
		
			if($num>0){
				echo "A user with that email already exists";
				header("Location: index.php");
				die();
			}
		} else {
			echo "failed to prepare the SQL";
		}
	
	  $query = "INSERT INTO members (Member_id, Name, Address, Phone, License, Email, Member_fee, isAdmin, password) VALUES ($Member_id, ?, ?, ?, ?, ?, 50, 0, ?)";
 
        // prepare query for execution
        if($stmt = $con->prepare($query)){
			$stmt->bind_Param("ssssss", $_POST['Name'], $_POST['Address'], $_POST['Phone'], $_POST['License'], $_POST['Email'], $_POST['password']);
			//Execute the query
			$stmt->execute();
			//Create a session variable that holds the user's id
			$_SESSION['Member_id'] = $Member_id;
			//Redirect the browser to the profile editing page and kill this page.
			header("Location: profile.php");
			die();
		}			
		else {
			echo "failed to prepare the SQL";
		}
}		
?>
	
 <!-- dynamic content will be here -->
 <form name='signup' id='signup' action='signup.php' method='post' onsubmit="return validate();">
	<h3>Please fill in the information below to sign up for an account</h3>
    <table border='0'>
        <tr>
            <td>Name: </td>
            <td><input type='text' name='Name' id='Name' /></td>
			<td>Address: </td>
            <td><input type='text' name='Address' id='Address'/></td>
        </tr>
        <tr>
            <td>Phone #: </td>
             <td><input type='tel' name='Phone' id='Phone'/></td>
			 <td>License: </td>
             <td><input type='text' name='License' id='License'/></td>
		<tr>
            <td>Email: </td>
            <td><input type='email' name='Email' id='Email'/></td>
			<td>Password: </td>
            <td><input type='password' name='password' id='password'/></td>
        </tr>
        <tr>
            <td></td>
			<td>
                <input type='submit' id='createBtn' name='createBtn' value='Create Account' /> 
            </td>
        </tr>
    </table>
</form>
</body>
</html>