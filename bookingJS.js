
function submitBooking(confirmDiv, cname, phone, unumber, snumber, sbname, dsbname, date, time){

    var confirmationDiv = document.getElementById(confirmDiv);
    var url = "bookingprocess.php";

    var formData = new FormData();
    formData.append("name", cname);
    formData.append("phone", phone);
    formData.append("unit", unumber);
    formData.append("street_number", snumber);
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
                confirmationDiv.innerHTML = text;
            });
        }
    )
}

