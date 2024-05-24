// searchDB function is called upon clicking the HTML GO button. It checks what type of query is being asked and provides feedback.
// First removing the content in the contentDiv and setting the feedbackdiv go visible or not.
function searchDB(query){

    var feedbackDiv = document.getElementById('feedback_div');
    var contentDiv = document.querySelector('.content');
    contentDiv.innerHTML = "";

    if(feedbackDiv.classList.contains('visible')){
        feedbackDiv.classList.remove('visible');
    }

    if(isEmpty(query)){

        var result = retrieveRows(query, "empty");
        return;
    }
    else if(isReference(query)){
        // Trim again as isReference does not return the trimmed string.
        
        trimmed_query = query.trim();
        
        var result = retrieveRows(trimmed_query, "reference");
        return;
    }
    feedbackDiv.classList.add('visible');
    feedbackDiv.innerHTML = "Your Query is invalid. Please enter a valid reference format (BRN00000) or nothing.";
}

// RetrieveRows connects with the server side file and displays the servers response in the content div.
// The function dynamically creates the table and inserts it into the div.

function retrieveRows(query, search_type){
    
    // Select div and create a fetch request.

    var contentDiv = document.querySelector('.content');

    // get feedback div for no matches or error status.
    var feedbackDiv = document.getElementById('feedback_div');
    
    var url = 'admin.php';
    
    var formData = new FormData();
    formData.append("query" , query);
    formData.append("search_type", search_type)
    
    const requestPromise = fetch(url, {
        method: 'POST',
        body: formData
    })
    requestPromise.then(response => response.json()).then(data => {
        
        // Reset the content Div.
        contentDiv.innerHTML = "";
        feedbackDiv.innerHTML = "";
        
        // Determine the status sent from the server
        const status = data.status;
        if(status == "error"){
            feedbackDiv.classList.add('visible');
            feedbackDiv.innerHTML += data.message;
        }
        else if(status == "no_results"){
            feedbackDiv.classList.add('visible');
            feedbackDiv.innerHTML += data.message;
        }
        else if(status == "success"){

            // assign isWithin to the server's result.
            var isWithin = data.isWithin;

            // Dynamically create table and attatch to the div at the end.
            var table = document.createElement('table');

            // Add class name for styling.
            table.classList.add('table');
            
            var thead = document.createElement('thead');
            var headerRow = document.createElement('tr');
            
            // Creating column data.
            var columns = data.columns;
            columns.forEach(col => {
                var th = document.createElement('th');
                th.innerText = col;
                headerRow.appendChild(th);
            });
            
            // Column for the assign button.
            var th_assign = document.createElement('th');
            th_assign.innerHTML = "Assign";
            headerRow.appendChild(th_assign);
            
            thead.appendChild(headerRow);
            
            table.appendChild(thead);
            
            // Creating row elements.
            var tbody = document.createElement('tbody');
            data.rows.forEach(rowData => {
                
                var trBody = document.createElement('tr');
                columns.forEach(col => {
                    var td = document.createElement('td');
                    
                    // Apply a unique identifier to the status value within each row.
                    if(col === 'status'){
                        td.id = "status_" + rowData['reference']; 
                    }
                    
                    td.innerText = rowData[col];
                    trBody.appendChild(td);
                });
                
                // isWithin will be true if the server deems the booking within two hours of the current time
                if((rowData['status'] === "Unassigned") && isWithin){

                    // Creating the assign button element.
                    var td_assign = document.createElement('td');
                    var assign_btn = document.createElement('button');
                    assign_btn.innerText = "Assign";
                    assign_btn.className = "assign-btn";
                    
                    assign_btn.addEventListener('click', function(){
                        
                        // Get the status id associated with this rows 'Assign' button.
                        var statusToChange = "status_" + rowData['reference'];
                        
                        // Retrieve the actual element.
                        var statusCell = document.getElementById(statusToChange);
    
                        // Set to Assigned.
                        statusCell.innerText = "Assigned";
    
                        // Call function to send another fetch request to update it on the server-side.
                        // Pass in reference number to adjust.
                        assignBooking(rowData['reference']);
                        
                    });
                    
                    td_assign.appendChild(assign_btn);
                    trBody.appendChild(td_assign);
                }
                
                tbody.appendChild(trBody);
            });
            
            table.appendChild(tbody);
            contentDiv.appendChild(table);
        }
    })
}


// isEmpty returns TRUE if a string is empty, null, or a string of only spaces and returns FALSE otherwise.
function isEmpty(string){
    return (string == null || (typeof string === "string" && string.trim().length === 0));
}

// isReference uses RegExp.test() to return TRUE if the string matches the pattern and FALSE otherwise.
function isReference(string){
    trimmed_string = string.trim();
    const format = /^BRN\d{5}$/;
    return format.test(trimmed_string);
}

// assignBooking is called when the 'assign' button action listener is invoked.
// It only communicates with the server-side by using the POST method to send the reference (booking) 
// to be assigned.
function assignBooking(reference){

    var url = 'admin.php';
    var formData = new FormData();
    formData.append("update", reference);

    fetch(url, {
        method: 'POST',
        body: formData
    });
}