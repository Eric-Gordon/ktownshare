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
		width: 9em;
	}
	table tr td {
		width: 9em;
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
if(isset($_POST['reserve'])){
	$vinnum = $_POST['vinnum'];
	if(isset($_POST['res-date'])){
		$resDate = $_POST['res-date'];
		header("Location: reserve.php?vin=$vinnum&resdate=$resDate");
		die();
	}else{
		header("Location: reserve.php?vin=$vinnum");
		die();
	}
}
?>	

<?php
if(isset($_POST['menu-back'])){
	header("Location: profile.php");
	die();
}
?>	

 Welcome  <?php echo $myrow['Name']; ?>, <a href="index.php?logout=1"> Log Out</a><br/>
<!-- dynamic content will be here -->
<form name='Profile' id='Profile' action='profile.php' method='post' >

	<?php
	// View KTSC Locations
	if(isset($_POST['klocations']) || isset($_POST['klocations-back'])){
	  // include database connection
		include_once 'config/connection.php'; 
		//select locations
		$query = "SELECT * FROM parking_locations";
		//prepare statement
		if($stmt = $con->prepare($query)){
			// Execute the query
			$stmt->execute();
			//resultset
			$result = $stmt->get_result();
			//put results into an array
			while($row = $result->fetch_array()){
				$rows[] = $row;
			}
			foreach($rows as $row){ 
					$id = $row['Location_id'];
			?>
				<div  id='klocations-menu' >
					<table>
						<tr>
							<td><?php echo $row['Address']; ?></td>
							<td><button type="submit" name="carsonlocation" id='carsonlocation' value='<?php echo $id; ?>' >Cars</button></td>
						</tr>
						<?php } ?>
						<tr>
							<td><input type='submit' name='menu-back' id='menu-back' value='Back' /></td>
						</tr>
					</table>
				</div>
				<?php
				echo '<style> #profile-menu {display: none;} #klocations-menu {display: block;} </style>';
			} else {
				echo "failed to prepare the SQL";
			}
	}?>
	
	<?php 
	// View available cars
	if(isset($_POST['carsavailable'])){
		?>
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
				</tr>
				<script>
						var calendar = document.getElementById('calendar1');
						calendar.valueAsDate = new Date();
						var dateCheck = document.getElementById('check-date');
						calendar.addEventListener(
							 'change',
							 function() { 
								if((calendar.valueAsDate < new Date())){
									alert ("Please don't go back in time.");
								}
									dateCheck.value = calendar.value;
									document.forms['Profile'].submit();
							},
							 false
						  );
				</script>
			</table>
		</div>		
			<?php
				echo "
				<style>
				#profile-menu {
					display: none;
				}
				#carsavailable-menu1 {
					display: block;
				}
				</style>";
	}
	?>
	
	<?php
	if(isset($_POST['check-date'])){
		$date = $_POST['check-date'];		
		// include database connection
		include_once 'config/connection.php'; 
		//select locations
		$query = "SELECT * FROM cars WHERE VIN NOT IN (SELECT VIN FROM reservations WHERE reservations.Date=?)";
		//prepare statement
		if($stmt = $con->prepare($query)){
			$stmt->bind_Param("s", $date);
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
									document.forms['Profile'].submit();
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
						<td><input type='submit' name='reserve' id='reserve' value='Reserve' /></td>
						<td><input type='hidden' name='vinnum' id='vinnum' value='<?php echo $vin; ?>' /></td>
						<input type='hidden' name='res-date' id='res-date' value='<?php echo $date; ?>'/>
					</tr>
					<?php }}else{?>
						<tr>
							<td>No available cars on this date.</td>
						</tr>
						<?php 
						}?>
					<tr>
						<td><input type='submit' name='menu-back' id='menu-back' value='Back' /></td>
					</tr>
					
				</table>
			</div>
			<?php
				echo "
				<style>
				#profile-menu {
					display: none;
				}
				#carsavailable-menu1 {
					display: none;
				}
				#carsavailable-menu2 {
					display: block;
				}
				</style>";
		}
	}
	?>
	
	<?php
	// View cars at a given location
	if(isset($_POST['carsonlocation'])) {
		$loc_id = $_POST['carsonlocation'];
		include_once 'config/connection.php'; 
		$query = "SELECT VIN, Make, Model, Year, Rent_fee, Odometer FROM cars WHERE Location_id = ?";
		$stmt = $con->prepare($query);
			$stmt->bind_Param("s", $loc_id);
			//Execute the query
			$stmt->execute();
	 
			//resultset
			$result = $stmt->get_result();
			//Get the number of rows returned
			$num = $result->num_rows;
			if($num>0){
			//put results into an array
			while($row = $result->fetch_array()){
				$rows[] = $row;
			}
		?>
		<div  id='carsonlocation-menu'>
			<table>
			<tr>
				<td>Make: </td>
				<td>Model: </td>
				<td>Year: </td>
				<td>Odometer: </td>
				<td>Rental Fee: </td>
			</tr>
				<?php foreach($rows as $row){ 
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
					<td><input type='submit' name='reserve' id='reserve' value='Reserve' /></td>
					<td><input type='hidden' name='vinnum' id='vinnum' value='<?php echo $vin; ?>' /></td>
				</tr>
				<?php } ?>
				<tr>
					<td><input type='submit' name='klocations-back' id='klocations-back' value='Back' /></td>
				</tr>
				
			</table>
		</div>
			<?php
				echo "
				<style>
				#profile-menu {
					display: none;
				}
				#carsonlocation-menu {
					display: block;
				}
				</style>";
		}else{
			echo "no cars at this location";
		}
	}?>
	
	<?php
	if(isset($_POST['reservations'])){
		// include database connection
		include_once 'config/connection.php'; 
		//select locations
		$query = "SELECT Reserve_num, Date, Code, Make, Model, Address, Length FROM reservations, cars, parking_locations WHERE Member_id=? AND reservations.VIN=cars.VIN AND parking_locations.Location_id=cars.Location_id";
		//prepare statement
		if($stmt = $con->prepare($query)){
			$stmt->bind_Param("s", $myrow['Member_id']);
			// Execute the query
			$stmt->execute();
			//resultset
			$result = $stmt->get_result();
			
			$num = $result->num_rows;
			
			if($num>0){
				//put results into an array
				while($row = $result->fetch_array()){
					$rows[] = $row;
				}
				?>
					<table>
						<tr>
							<td>Date: </td>
							<td>Length: </td>
							<td>Reservation Number: </td>
							<td>Address: </td>
							<td>Make: </td>
							<td>Model: </td>
							<td>Code: </td>
						</tr>
						<?php
						foreach($rows as $row){
							$date=$row['Date'];
							$len=$row['Length'];
							$resnum=$row['Reserve_num'];
							$code=$row['Code'];
							$address=$row['Address'];
							$make=$row['Make'];
							$model=$row['Model'];
							?>
						<tr>
							<td><?php echo $date; ?></td>
							<td><?php echo $len; ?></td>
							<td><?php echo $resnum; ?></td>
							<td><?php echo $address; ?></td>
							<td><?php echo $make; ?></td>
							<td><?php echo $model; ?></td>
							<td><?php echo $code; ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td><input type='submit' name='menu-back' id='menu-back' value='Back' /></td>
						</tr>
					</table>
					
					<?php

				}else{
					?>
					<table>
						<tr>
							<td>You have no reservations at this time.</td>
						</tr>
						<tr>
							<td><input type='submit' name='menu-back' id='menu-back' value='Back' /></td>
						</tr>
					</table>
					<?php
			}
		}
		echo "
						<style>
						#profile-menu {
							display: none;
						}
						</style>";
	}	
	?>
	
	<?php // menu ?>
	<div id='profile-menu'>
		<table>
			<tr>
				<td><input type='submit' name='klocations' id='klocations' value='KTSC Locations' /></td>
				<td><input type='submit' name='carsavailable' id='carsavailable' value='Available Cars' /></td>
			</tr>
			<tr>
				<td><input type='submit' name='pickup' id='pickup' value='Pick Up' /></td>
				<td><input type='submit' name='dropoff' id='dropoff' value='Drop Off' /></td>
			</tr>
			<tr>
				<td><input type='submit' name='reservations' id='reservations' value='Reservations' /></td>
				<td><input type='submit' name='history' id='history' value='Rental History' /></td>
			</tr>
		</table>
	</div>	

		<?php
			//2)Find KTSC locations. :)
			//3)Find all cars available to rent on a specific day.
			//4)Reserve a car.
			//5)Pick-up a car (record the time, odometer reading, car status).
			//6)Drop-off a car (record the time, odometer reading, car status).
			//7)Show the member’s rental history.
			//8)Provide a feedbackon a rental.	
		?>	
</form>
</body>
</html>