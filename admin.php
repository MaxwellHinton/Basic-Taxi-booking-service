<?php

    // Set up DB connection

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

    // Handle status update request. Exit when completed as we dont need to run the rest of the code.
    if(isset($_POST['update'])){
        $ref_toUpdate = $_POST['update'];

        $stmt = "UPDATE bookings SET status = 'Assigned' WHERE reference = '$ref_toUpdate'";

        try{
            // Returns True on success, false otherwise.
            $result = $conn->query($stmt);
        }
        catch(Exception $e){
            echo json_encode(["status" => "error", "message" => "Error with database query: " . $e->getMessage()]);
        }

        $conn->close();
        exit;
    }

    // Handle other requests: Searching by reference, and empty searching.

    $query = $_POST['query'];
    $search_type = $_POST['search_type'];
    date_default_timezone_set("Pacific/Auckland");

    if($search_type == "reference"){

        // Get current time for comparison.
        // Comparing the time here determines whether or not the row will be loaded with an 'assign' button.
        $timeTwoHoursLater = date("H:i", strtotime('+2 hours'));
        $current_time = date("H:i");

        // Statement to query the database via a bookings reference.
        $stmt = "SELECT * FROM bookings WHERE reference = '$query'";
    
        try{
            $result = $conn->query($stmt);

            if($result->num_rows == 0){
                echo json_encode(["status" => "no_results", "message" => "No matches were found for the reference: " .$query]);
            }
            else{
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $columns = array_keys($rows[0]);

                // Get the associated booking and its time. Already will be in HH:MM format.
                $rowTime = $rows[0]['time'];

                $withinTwoHours = (($rowTime > $current_time) && ($rowTime < $timeTwoHoursLater));

                echo json_encode(["status" => "success", "columns" => $columns, "rows" => $rows, "isWithin" => $withinTwoHours]); 
            }
        }
        catch(Exception $e){
            echo json_encode(["status" => "error", "message" => "Error with database query: " . $e->getMessage()]);
        }
    }
    else{
        // Query the database for all bookings that are unassigned and within the next two hours.
        
        $timeTwoHoursLater = date("H:i", strtotime('+2 hours'));
        $current_time = date("H:i");
        
        $stmt = "SELECT * FROM bookings WHERE status = 'Unassigned' AND time BETWEEN '$current_time' AND '$timeTwoHoursLater'";

        try{
            $result = $conn->query($stmt);

            if($result->num_rows == 0){
                echo json_encode(["status" => "no_results", "message" => "No matches were found that are unassigned and/or within the next 2 hours."]);
            }
            else{
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $columns = array_keys($rows[0]);

                // isWithin can be set to true because we only select rows that are within 2 hours.
                echo json_encode(["status" => "success", "columns" => $columns, "rows" => $rows, "isWithin" => true]);

            }
        }
        catch(Exception $e){
            echo json_encode(["status" => "Error", "message" => "Error with database query: " . $e->getMessage()]);
        }
    }

    $conn->close();
    