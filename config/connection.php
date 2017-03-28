<?php
// used to connect to the database
$host = "localhost";
$db_name = "ktownshare";
$username = "13ekg2";
$password = "e68k44g23";
try {
    $con = new mysqli($host,$username,$password, $db_name);
}
 
// show error
catch(Exception $exception){
    echo "Connection error: " . $exception->getMessage();
}
?>