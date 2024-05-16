<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
        <script type="text/javascript" src="bookingJS.js"></script>
        <title>Taxi Booking System</title>
    </head>
    <body>
        <h1 id="booking_header">Taxi Booking System</h1>
        <br>
        <form id="booking_form">
            <div class="booking_info">
                <label for="cname">Customer Name: </label>
                <input type="text" name="cname" id="cname" required>
            </div>
            <div class="booking_info">
                <label for="phone">Phone Number: </label>
                <input type="text" name="phone" id="phone" pattern="\d{10,12}" title="Valid phone numbers require 10-12 digits" required>
            </div>
            <div class="booking_info">
                <label for="unumber">Unit Number: </label>
                <input type="text" name="unumber" id="unumber">
            </div>
            <div class="booking_info">
                <label for="snumber">Street Number: </label>
                <input type="text" name="snumber" id="snumber" required>
            </div>
            <div class="booking_info">
                <label for="sbname">Suburb Name: </label>
                <input type="text" name="sbname" id="sbname">
            </div>
            <div>
                <label for="dsbname">Destination Suburb: </label>
                <input type="text" name="dsbname" id="dsbname">
            </div>

            <div class="booking_info"> <!--Note: The pick up date and time can not be earlier than the current date and time -->
                <?php $date = Date("d/m/Y");?>

                <label for="date">Pick up date: </label>
                <input type="text" name="date" id="date" value="<?=$date?>" pattern="\d{1,2}/\d{2}/\d{4}" 
                    title="Date must follow the format of: dd/mm/yyyy" required>
            </div>
            <div class="booking_info">
                <?php 
                    date_default_timezone_set('Pacific/Auckland');
                    $time = date("H:i")
                ?>
                <label for="time">Pick up time: </label>
                <input type="text" name="time" id="time" value="<?=$time?>" pattern="\d{1,2}:\d{1,2}" 
                    title="Pick up time must be in 24 hour time (HH:MM) e.g., 18:30" required>        
            </div>

            <div class="booking_info">
                <input type="submit" id="submit_button" value="Book" 
                       onclick="submitForm(event)">
            </div>

            <script>
                // Submit form function only allows the submitBooking function to be called when all REQUIRED
                // parts of the form have been submitted.

                function submitForm(event){
                    event.preventDefault();

                    document.getElementById('confirmation_msg').innerHTML = "";

                    if(isFormValid()){
                        submitBooking('confirmation_msg', cname.value, phone.value, unumber.value, snumber.value, sbname.value, dsbname.value, date.value, time.value);
                    }
                    else{
                        console.log("da form was not filled out mayn"); //Need to update a error div.
                        document.getElementById('confirmation_msg').innerHTML = "Error creating booking. Please ensure all required fields are filled out.";
                    }
                }

                function isFormValid(){
                    var form =document.getElementById("booking_form");
                    return form.checkValidity();
                }
            </script>


            <div id="confirmation_msg">

            </div>
        </form>
    </body>
</html>