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
	$vinnum = $_POST['reserve'];
	header("Location: reserve.php?vin=$vinnum");
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
							<td><button type="submit" name="carsonlocation" id='carsonlocation' value="<?php echo $id; ?>" >  Cars  </button></td>
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
		// include database connection
		include_once 'config/connection.php'; 
		//select locations
		$query = "SELECT * FROM cars WHERE isAvailable=1";
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
			?>
		<div  id='carsavailable-menu'>
			<table>
				<?php foreach($rows as $row){ 
				$vin = $row['VIN'];
				$make = $row['Make'];
				$model = $row['Model'];
				$year = $row['Year'];
				$fee =  $row['Rent_fee'];
				$odom = $row['Odometer'];
				?>
				<tr>
					<td>Make: <?php echo $make;?>, Model: <?php echo $model;?>, Year: <?php echo $year;?>, Odometer: <?php echo $odom;?>, Rental Fee: <?php echo $fee; ?></td>
					<td><button type='submit' name='reserve' id='reserve' value='<?php echo $vin; ?>' > Reserve </button></td>
				</tr>
				<?php } ?>
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
				#carsavailable-menu {
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
				<?php foreach($rows as $row){ 
				$vin = $row['VIN'];
				$make = $row['Make'];
				$model = $row['Model'];
				$year = $row['Year'];
				$fee =  $row['Rent_fee'];
				$odom = $row['Odometer'];
				?>
				<tr>
					<td>Make: <?php echo $make;?>, Model: <?php echo $model;?>, Year: <?php echo $year;?>, Odometer: <?php echo $odom;?>, Rental Fee: <?php echo $fee; ?></td>
					<td><button type='submit' name='reserve' id='reserve' value='<?php echo $vin; ?>' > Reserve </button></td>
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