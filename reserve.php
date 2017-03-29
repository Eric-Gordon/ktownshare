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
		table tr td{
			width: 8em;
		}
	</style>";
?>

 <?php
  //Create a user session or resume an existing one
 session_start();
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

<?php
if(isset($_POST['cancel'])){
	header("Location: profile.php");
	die();
}
?>

<?php
if(isset($_POST['reserve'])){
	$vin = $_POST['vin'];
	$date = $_POST['date'];
	$length = $_POST['len'];
	$x = 8; // Amount of digits
	$min = pow(10,$x);
	$max = (pow(10,$x+1)-1);
	$reserveNum = rand($min, $max);
	$y = 3; // Amount of digits
	$min2 = pow(10,$y);
	$max2 = (pow(10,$y+1)-1);
	$code = rand($min2, $max2);
	
	// include database connection
    include_once 'config/connection.php'; 
	
	// SELECT query
        $query = "INSERT INTO reservations (Reserve_num, Member_id, VIN, Date, Code, Length) VALUES (?, ?, ?, ?, ?, ?)";
		// prepare query for execution
		$stmt = $con->prepare($query);
		$stmt->bind_Param("ssssss", $reserveNum, $myrow['Member_id'], $vin, $date, $code, $length);
        // Execute the query
		$stmt->execute();
		
	header("Location: profile.php");
	die();
}		
	
?>

Welcome  <?php echo $myrow['Name']; ?>, <a href="index.php?logout=1"> Log Out</a><br/>

	<form name='Reserve' id='Reserve' action='reserve.php' method='post' >
	<?php
			if(isset($_POST['check-date'])){
				$vin = $_POST['vin'];
				$date = $_POST['check-date'];		
				// include database connection
				include_once 'config/connection.php'; 
				//select locations
				$query = "SELECT * FROM cars WHERE VIN NOT IN (SELECT VIN FROM reservations WHERE reservations.Date=?) AND VIN=?";
				//prepare statement
				if($stmt = $con->prepare($query)){
					$stmt->bind_Param("ss", $date, $vin);
					// Execute the query
					$stmt->execute();
					//resultset
					$result = $stmt->get_result();
					
					$num = $result->num_rows;
				
					?>
					<div  id='carsavailable-menu2'>
						<table>
							<tr>
								<td>Select Date: </td>
								<td><input type='date' name='calendar2' id='calendar2' min='<?php echo date("Y/m/d"); ?>'/></td>
							</tr>	
							<tr>
								<td><input type='hidden' name='check-date' id='check-date' value='' /></td>
							</tr>
							<script>
								var calendar = document.getElementById('calendar2');
								var time = <?php echo json_encode ( $date ) ?>;
								calendar.value = time;
								var dateCheck = document.getElementById('check-date');
								calendar.addEventListener(
									 'change',
									 function() { 
										if(calendar.valueAsDate < new Date()){
											alert ("Please don't go back in time.");
										}
											dateCheck.value = calendar.value;
											document.forms['Reserve'].submit();
									 },
									 false
								  );
							</script>
							<?php 
							if($num>0){?>
							<tr>
								<td>Make: </td>
								<td>Model: </td>
								<td>Year: </td>
								<td>Odometer: </td>
								<td>Rental Fee: </td>
							</tr>
							<?php
								//put results into an array
								while($row = $result->fetch_array()){
									$rows[] = $row;
								}
							foreach($rows as $row){
									$vin = $row['VIN'];
									$make = $row['Make'];
									$model = $row['Model'];
									$year = $row['Year'];
									$fee =  $row['Rent_fee'];
									$odom = $row['Odometer'];
							?>
							<tr>
								<td><?php echo $make;?></td>
								<td><?php echo $model;?></td>
								<td><?php echo $year;?></td>
								<td><?php echo $odom;?></td>
								<td><?php echo $fee;?></td>
							</tr>
							<tr>
								<td>Length of reservation(days)</td>
								<td><input type='number' name='len' id='len' value='1' min='1' max='5' /></td>
							</tr>
							<tr>
								<td><input type='submit' name ='cancel' id='cancel' value='Cancel' /></td>
								<td><input type='submit' name='reserve' id='reserve' value='Confirm Reservation' /></td>
								<td><input type='hidden' name='vin' id='vin' value='<?php echo $vin; ?>' /></td>
								<td><input type='hidden' name='date' id='date' value='<?php echo $date; ?>' /></td>
							</tr>
							<?php }}else{?>
								<tr>
									<td>No available cars on this date.</td>
								</tr>
								<tr>
									<td><input type='submit' name ='cancel' id='cancel' value='Cancel' /></td>
									<td><input type='hidden' name='vin' id='vin' value='<?php echo $vin; ?>' /></td>
								</tr>
								<?php 
								}?>
						</table>
					</div>
					<?php
						echo "
						<style>
						#carsavailable-menu1 {
							display: none;
						}
						#carsavailable-menu2 {
							display: block;
						}
						tableable tr td{
							width: 8em;
						}
						</style>";
				}
			}
	?>
		<div id='date-selector'>
		<?php
		 if(isset($_GET['vin'])){
			 $vin = $_GET['vin'];
			 if(isset($_GET['resdate'])){
				 $date = $_GET['resdate'];
				// include database connection
				include_once 'config/connection.php'; 
				// SELECT query
				$query = "SELECT * FROM cars WHERE VIN=?";
				// prepare query for execution
				$stmt = $con->prepare($query);
				// bind the parameters. This is the best way to prevent SQL injection hacks.
				$stmt->bind_Param("s", $vin);
				// Execute the query
				$stmt->execute();
			 	// results 
				$result = $stmt->get_result();
				$myrow = $result->fetch_assoc();
				?>
				<table>
					<tr>
						<td>Make: </td>
						<td>Model: </td>
						<td>Year: </td>
						<td>Odometer: </td>
						<td>Rental Fee: </td>
					</tr>
					<?php
						$vin = $myrow['VIN'];
						$make = $myrow['Make'];
						$model = $myrow['Model'];
						$year = $myrow['Year'];
						$fee =  $myrow['Rent_fee'];
						$odom = $myrow['Odometer'];
					?>
					<tr>
						<td><?php echo $make;?></td>
						<td><?php echo $model;?></td>
						<td><?php echo $year;?></td>
						<td><?php echo $odom;?></td>
						<td><?php echo $fee;?></td>
					</tr>
					<tr>
						<td>Length of reservation(days)</td>
						<td><input type='number' name='len' id='len' value='1' min='1' max='5' /></td>
					</tr>
					<tr>
						<td><input type='submit' name ='cancel' id='cancel' value='Cancel' /></td>
						<td><input type='submit' name='reserve' id='reserve' value='Confirm Reservation' /></td>
						<td><input type='hidden' name='vin' id='vin' value='<?php echo $vin; ?>' /></td>
						<td><input type='hidden' name='date' id='date' value='<?php echo $date; ?>' /></td>
					</tr>
				</table>
		</div>
			<?php
			}else{?>
				<div  id='carsavailable-menu1'>
					<table>
						<tr>
							<td>Select Date: </td>
							<td><input type='date' name='calendar1' id='calendar1' min='<?php echo date("Y/m/d"); ?>'/></td>
						</tr>
						<tr>
							<td><input type='submit' name='menu-back' id='menu-back' value='Back' /></td>
						</tr>				
						<tr>
							<td><input type='hidden' name='check-date' id='check-date' value='' /></td>
							<td><input type='hidden' name='vin' id='vin' value='<?php echo $vin; ?>' /></td>
						</tr>
						<script>
								var calendar = document.getElementById('calendar1');
								calendar.value = new Date();
								var dateCheck = document.getElementById('check-date');
								calendar.addEventListener(
									 'change',
									 function() { 
										if(calendar.valueAsDate < new Date()){
											alert ("Please don't go back in time.");
										}
											dateCheck.value = calendar.value;
											document.forms['Reserve'].submit();
									 },
									 false
								  );
						</script>
					</table>
				</div>
		</div>
					<?php
					echo ' <style>
					tableable tr td{
						width: 8em;
					}
					</style>';
			}
		 }
	?>
	</form>
</body>
</html>