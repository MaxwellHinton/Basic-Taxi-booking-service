// Submit form function is called before attempting to process the request on the server side, in-order to provide immediate feedback to the user.
// it calls a helper function to assess if every REQUIRED field has been filled out.

function submitForm(event){
    event.preventDefault();
    
    document.getElementById('reference').innerHTML = "";

    // Need to check the date and time is not less than the current date/time.

    var current_date_time = new Date();
    var current_date = current_date_time.toLocaleDateString('en-NZ');

    // HH:MM format described in parameters.
    var current_time = current_date_time.toLocaleTimeString('en-NZ', {hour12: false, hour: '2-digit', minute: '2-digit'});

    var selected_time = document.getElementById('time').value;
    var selected_date = document.getElementById('date').value;

    if(!compare_dates(current_date, selected_date)){
        
        document.getElementById('reference').innerHTML = "Error: You can not make a booking for a past date!";
    }
    else if(selected_time < current_time){
        document.getElementById('reference').innerHTML = "Error: You can not make a booking for a past time!";
    }
    else{
        // The user has entered a valid date and time.

        if(isFormValid()){
            submitBooking('reference', cname.value, phone.value, unumber.value, snumber.value, stname.value, sbname.value, dsbname.value, selected_date, selected_time);
        }
        else{
            //Need to update a error div.
            document.getElementById('reference').innerHTML = "Error creating booking. Please ensure all required fields are filled out.";
        }
    }
}

// Function to compare if a date is less than another.
// returns TRUE if the selected date is LESS THAN the current date, and FALSE otherwise.
function compare_dates(current_date, selected_date){
    
    var curr_date_arr = current_date.split('/');
    var c_day = parseInt(curr_date_arr[0], 10);
    var c_month = parseInt(curr_date_arr[1], 10) - 1;
    var c_year = parseInt(curr_date_arr[2], 10);

    var selected_date_arr = selected_date.split('/');
    var s_day = parseInt(selected_date_arr[0], 10);
    var s_month = parseInt(selected_date_arr[1], 10) - 1;
    var s_year = parseInt(selected_date_arr[2]);
    
    if(s_year < c_year){ 
        return false;
    }
    else if(s_month < c_month){
        return false;
    }
    else if(s_day < c_day){
        return false;
    }

    return true;
} 

// Verifies all required parts of the form have been filled out.
function isFormValid(){
    var form =document.getElementById("booking_form");
    return form.checkValidity();
}

// Booking function that interacts with the server-side file booking.php
function submitBooking(targetDiv, cname, phone, unumber, snumber, stname, sbname, dsbname, date, time){

    var referenceDiv = document.getElementById(targetDiv);
    var url = "bookingprocess.php";

    var formData = new FormData();
    formData.append("name", cname);
    formData.append("phone", phone);
    formData.append("unit", unumber);
    formData.append("street_number", snumber);
    formData.append("street_name", stname);
    formData.append("suburb", sbname);
    formData.append("dest_suburb", dsbname);
    formData.append("date", date);
    formData.append("time", time);

    const requestPromise = fetch(url, {
        method : 'POST',
        body : formData
    });
    requestPromise.then(
        function(response){
            response.text().then(function(text){
                referenceDiv.innerHTML = text;
            });
        }
    )
}