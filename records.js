$(document).ready(function () {
    const table = initializeDataTable();
    fetchRecords();

    // Initialize the DataTable with specific settings
    function initializeDataTable() {
        return $("#example2").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: true
        });
    }

    // Fetch records and populate the DataTable
    function fetchRecords() {
        $.ajax({
            url: 'fetch_record.php', // Updated URL for fetching records
            method: 'GET',
            success: function (data) {
                updateTableWithRecords(data);
            },
            error: function (xhr) {
                console.error("Error fetching records:", xhr.responseText);
                alert('Error fetching records. Please try again.');
            }
        });
    }    

    // Update the DataTable with fetched records
    function updateTableWithRecords(data) {
        table.clear().draw();
        data.forEach(function (record) {
            const row = createRecordRow(record);
            table.row.add(row).draw();
        });
    }

    // Create a row for the DataTable based on a record
    function createRecordRow(record) {
        return [
            record.firstName,
            record.lastName,
            record.middleName,
            `<button class="btn btn-primary view-button" 
                data-record='${JSON.stringify(record)}' 
                data-toggle="modal" data-target="#recordModal">
                View
            </button>`
        ];
    }

   // Handle form submission to add a new record
    $('#recordForm').submit(function (event) {
        event.preventDefault();

        // Convert main form data into JSON format
        const formData = {
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            middleName: $('#middleName').val(),
            age: $('#age').val(),
            birthPlace: $('#birthPlace').val(),
            address: $('#address').val(),
            education: $('#education').val(),
            income: $('#income').val(),
            occupation: $('#occupation').val(),
            gender: $('#gender').val(),
            mobileNumber: $('#mobileNumber').val(),
            clientType: $('#clientType').val(),
            date: $('#date').val(),
            assistanceType: $('#assistanceType').val(),
            fundType: $('#fundType').val(),
            amount: $('#amount').val(),
            beneficiary: $('#beneficiary').val(),
            familyMembers: [] // Initialize an empty array for family members
        };

        // Collect data from family members section
        $('#familyMembersContainer .card-body').each(function () {
            const familyMember = {
                firstName: $(this).find('input[name="familyFirstName[]"]').val(),
                lastName: $(this).find('input[name="familyLastName[]"]').val(),
                middleName: $(this).find('input[name="familyMiddleName[]"]').val(),
                dateOfBirth: $(this).find('input[name="familyDateOfBirth[]"]').val(),
                gender: $(this).find('select[name="familyGender[]"]').val(),
                relationship: $(this).find('input[name="familyRelationship[]"]').val()
            };
            formData.familyMembers.push(familyMember);
        });

        // Log the data before sending it to the server
        console.log('Data to be sent to the server:', JSON.stringify(formData));
        addNewRecord(formData); // Send as JSON
    });


    // AJAX request to add a new record
    function addNewRecord(formData) {
        $.ajax({
            url: 'add_record.php',
            method: 'POST',
            contentType: 'application/json', // Ensure data is sent as JSON
            data: JSON.stringify(formData), // Convert data to JSON
            success: function () {
                console.log('Record added successfully');
                $('#addRecordForm').toggle(); // Hide the form after adding
                fetchRecords(); // Refresh records
            },
            error: function (xhr) {
                console.error('Error adding record:', xhr.responseText);
                alert('Error adding record. Please try again.');
            }
        });
    }

    // Toggle the Add Record Form visibility
    $('#addRecordButton').click(function () {
        $('#addRecordForm').toggle();
    });

    // Populate the modal with record details on button click
    $('#recordModal').on('show.bs.modal', function (event) {
        const record = $(event.relatedTarget).data('record');
        displayRecordDetails(record, $(this));
    });

    // Display detailed information of the selected record in the modal
    function displayRecordDetails(record, modal) {
        let details = createPersonalInfoCard(record);
        details += createFamilyMembersCard(record.familyMembers);
        modal.find('.modal-body').html(details);
        modal.find('#editRecordButton').data('record', record);
    }

    // Create a card displaying personal information
    function createPersonalInfoCard(record) {
        return `
            <div class="card">
                <div class="card-header"><strong>Personal Information</strong></div>
                <div class="card-body">
                    <p>First Name: ${record.firstName}</p>
                    <p>Last Name: ${record.lastName}</p>
                    <p>Middle Name: ${record.middleName}</p>
                    <p>Age: ${record.age}</p>
                    <p>Birth Place: ${record.birthPlace}</p>
                    <p>Address: ${record.address}</p>
                    <p>Education: ${record.education}</p>
                    <p>Income Per Day: ${record.income}</p>
                    <p>Occupation: ${record.occupation}</p>
                    <p>Mobile Number: ${record.mobileNumber}</p>
                    <p>Gender: ${record.gender}</p>
                    <p>Client Type: ${record.clientType}</p>
                    <p>Date: ${record.date === "0000-00-00" ? "N/A" : record.date}</p>
                    <p>Assistance Type: ${record.assistanceType}</p>
                    <p>Fund Type: ${record.fundType}</p>
                    <p>Amount: ${record.amount}</p>
                    <p>Beneficiary: ${record.beneficiary}</p>
                </div>
            </div><hr>`;
    }

    // Create a card displaying family members
    function createFamilyMembersCard(familyMembers) {
        if (!familyMembers || familyMembers.length === 0) {
            return `<div class="card mt-3"><div class="card-header">Family Members</div><div class="card-body"><p>No family members listed.</p></div></div>`;
        }

        let familyDetails = `<div class="card mt-3"><div class="card-header"><strong>Family Members</strong></div><div class="card-body">`;
        familyMembers.forEach(member => {
            familyDetails += `
                <p>First Name: ${member.firstName}</p>
                <p>Last Name: ${member.lastName}</p>
                <p>Middle Name: ${member.middleName}</p>
                <p>Date of Birth: ${member.dateOfBirth}</p>
                <p>Gender: ${member.gender}</p>
                <p>Relationship: ${member.relationship}</p><hr>`;
        });
        familyDetails += `</div></div>`;
        return familyDetails;
    }

    // Event listener for edit button in the record modal
    $('#editRecordButton').click(function () {
        const record = $(this).data('record');
        if (!record) {
            alert("Error: Record data is missing.");
            return;
        }
        openEditModal(record);
    });
    
    // Open the modal to edit the record with populated fields
    function openEditModal(record) {
        $('#recordModal').modal('hide');
        $('#editRecordModal').modal('show');
        const editForm = generateEditForm(record);
        $('#editRecordModal .modal-body').html(editForm);
    }

    // Generate the HTML form for editing a record
    function generateEditForm(record) {
        return `
            <form id="editForm" class="card-body">
                <input type="hidden" id="editId" name="id" value="${record.id}">
                ${createInputField('First Name', 'editFirstName', 'firstName', record.firstName)}
                ${createInputField('Last Name', 'editLastName', 'lastName', record.lastName)}
                ${createInputField('Middle Name', 'editMiddleName', 'middleName', record.middleName)}
                ${createInputField('Age', 'editAge', 'age', record.age, 'number')}
                ${createInputField('Birth Place', 'editBirthPlace', 'birthPlace', record.birthPlace)}
                ${createInputField('Address', 'editAddress', 'address', record.address)}
                ${createInputField('Education', 'editEducation', 'education', record.education)}
                ${createInputField('Income Per Day', 'editIncome', 'income', record.income, 'number')}
                ${createInputField('Occupation', 'editOccupation', 'occupation', record.occupation)}
                ${createInputField('Mobile Number', 'editMobileNumber', 'mobileNumber', record.mobileNumber, 'number')}
                ${createSelectField('Gender', 'editGender', 'gender', record.gender, ['Male', 'Female'])}
                ${createSelectField('Client Type', 'editClientType', 'clientType', record.clientType, ['4ps', 'Senior Citizen', 'PWD', 'Solo Parent'])}
                ${createSelectField('Assistance Type', 'editAssistanceType', 'assistanceType', record.assistanceType, ['Medical Assistance', 'Burial Assistance', 'Transportation Assistance', 'Educational Assistance', 'Emergency Shelter Assistance', 'Livelihood Assistance'])}
                ${createSelectField('Fund Type', 'editFundType', 'fundType', record.fundType, ['LGU Fund', 'Barangay Fund', 'SK Fund'])}
                ${createInputField('Date', 'editDate', 'date', record.date, 'date')}
                ${createInputField('Amount', 'editAmount', 'amount', record.amount, 'number')}
                ${createInputField('Beneficiary', 'editBeneficiary', 'beneficiary', record.beneficiary)}
            </form>`;
    }
    // Event listener for saving the edited record
    $('#editRecordModal').on('click', '#saveEditButton', function () {
        // Collect the data from the edit form
        const editedRecord = {
            id: $('#editId').val(),
            firstName: $('#editFirstName').val(),
            lastName: $('#editLastName').val(),
            middleName: $('#editMiddleName').val(),
            age: $('#editAge').val(),
            birthPlace: $('#editBirthPlace').val(),
            address: $('#editAddress').val(),
            education: $('#editEducation').val(),
            income: $('#editIncome').val(),
            occupation: $('#editOccupation').val(),
            mobileNumber: $('#editMobileNumber').val(),
            gender: $('#editGender').val(),
            clientType: $('#editClientType').val(),
            date: $('#editDate').val(),
            assistanceType: $('#editAssistanceType').val(),
            fundType: $('#editFundType').val(),
            amount: $('#editAmount').val(),
            beneficiary: $('#editBeneficiary').val()
        };

        console.log("Sending edited record:", editedRecord);

        // AJAX request to update the record using the PUT method
        $.ajax({
            url: 'edit_record.php',
            method: 'PUT', // Use PUT for updates
            data: JSON.stringify(editedRecord), // Convert the data to JSON format
            contentType: 'application/json', // Indicate that the data is in JSON format
            success: function (response) {
                if (response.status === 'success') {
                    console.log('Record edited successfully');
                    $('#editRecordModal').modal('hide');
                    fetchRecords(); // Refresh the records to show the updated data
                } else {
                    console.error('Error editing record:', response.message);
                    alert('Error editing record: ' + response.message);
                }
            },
            error: function (xhr) {
                console.error('Error editing record:', xhr.responseText);
                alert('Error editing record. Please try again.');
            }
        });
    });


    // Helper function to create an input field
    function createInputField(label, id, name, value, type = 'text') {
        return `
            <div class="form-group">
                <label for="${id}">${label}</label>
                <input type="${type}" id="${id}" name="${name}" class="form-control" value="${value || ''}" required>
            </div>`;
    }

    // Helper function to create a select field
    function createSelectField(label, id, name, selectedValue, options) {
        let optionsHtml = options.map(option => 
            `<option value="${option}" ${option === selectedValue ? 'selected' : ''}>${option}</option>`
        ).join('');

        return `
            <div class="form-group">
                <label for="${id}">${label}</label>
                <select class="form-control" id="${id}" name="${name}" required>
                    ${optionsHtml}
                </select>
            </div>`;
    }
});
