<?php
  $cname = $_POST['name'];
  $phone = $_POST['phone'];
  $unit = $_POST['unit'];
  $street_number = $_POST['street_number'];
  $suburb = $_POST['suburb'];
  $dest_suburb = $_POST['dest_suburb'];
  $date = $_POST['date'];
  $time = $_POST['time'];
?>

<!-- Database connection -->
<?php
    $servername = "webdev.aut.ac.nz";
    $username = "qwv9850";
    $dbname = "qwv9850";
    $pswd = "";

    $conn = mysqli_connect($servername, $username, $pswd, $dbname);

    if($conn->connect_error){
        die("Connection to DB failed: " .$conn->connect_error);
    }
    else{
        // Successful DB connection
        // create table.

        $query = "SHOW TABLES LIKE 'bookings'";

        $result = $conn->query($query);

        if($result->num_rows == 0){
            $query = "CREATE TABLE bookings(
                ";

                // continue to create table.
        }
    }




?>