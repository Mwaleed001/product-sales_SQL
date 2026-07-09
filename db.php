<?php
$servername = "localhost";
$username = "root";      
$password = "";           
$dbname = "market_db";  


$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}





// if($conn){
//     echo "alert(connect successfully)";
// }
// else{
//     echo "alert(connect failed)";
// }
?>