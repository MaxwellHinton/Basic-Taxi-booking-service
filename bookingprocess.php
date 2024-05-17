<?php
    session_start();    
                   
    if(!isset($_SESSION["ref_number"])){    
        $_SESSION["ref_number"] = 1;        
    }
    else{
        $_SESSION["ref_number"]++;
    }

    //Generating the booking reference number. 
    $ref_num = $_SESSION['ref_number'];
    $br = "BRN" . str_pad($ref_num, 5, "0", STR_PAD_LEFT);

    $cname = $_POST['name'];
    $phone = $_POST['phone'];
    $unit = $_POST['unit'];
    $st_number = $_POST['street_number'];
    $st_name = $_POST['street_name'];
    $suburb = $_POST['suburb'];
    $dest_suburb = $_POST['dest_suburb'];
    $date = $_POST['date'];
    $time = $_POST['time'];
?>



<!-- Database connection -->
<?php
    $servername = "localhost";
    $username = "root";
    $dbname = "a2database";
    $pswd = "";
    
    try{

        $conn = new mysqli($servername, $username, $pswd, $dbname);
    }
    catch(Exception $e){
        echo $e->getMessage();
    }

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
                reference VARCHAR(8),
                cname TEXT NOT NULL,
                phone INT NOT NULL,
                unit INT,
                streetNo INT NOT NULL,
                streetName TEXT NOT NULL,
                suburb TEXT,
                destSuburb TEXT,
                date TEXT NOT NULL,
                time TEXT NOT NULL,
                PRIMARY KEY (reference)
                )";

            try{
                $conn->query($query);
            }catch(Exception $e){
                echo "Error executing table creation query: " . $e->getMessage();
            }
        }

        try{
            $stmt = $conn->prepare(
                "INSERT INTO bookings(reference, cname, phone, unit, streetNo, streetName, suburb,
                                      destSuburb, date, time)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if($stmt === false){
                die("Statement preperation failed: " . $conn->errno . "Error: " . $conn->error);
            }

            $stmt->bind_param("ssiiisssss", 
                $br, $cname, $phone, $unit, $st_number, $st_name,
                $suburb, $dest_suburb, $date, $time
            );


            if(!$stmt->execute()){
                echo "Execution of statement failed: " .$stmt->errno . " | " . $stmt->error;
            }
            else{

                echo "<p><h3>Thank you for your booking!</h3>" 
                . "<br>Booking reference number: " . $br
                . "<br>Pickup time: " . $time
                . "<br>Pickup date: " . $date . "</p>";
            }
            $stmt->close();
            $conn->close();
        }
        catch(Exception $e){
            echo "Error creating booking: " . $e->getMessage();
        }
    }
?>
