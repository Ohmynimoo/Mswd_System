$(document).ready(function () {
    var table = $("#example2").DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: true
    });

    function fetchRecords() {
        $.ajax({
            url: 'fetch_record.php',
            method: 'GET',
            success: function (data) {
                table.clear().draw();
                data.forEach(function (record) {
                    var dateValue = record.date === "0000-00-00" ? "N/A" : record.date;
                    var row = [
                        record.firstName,
                        record.lastName,
                        record.middleName,
                        `<button class="btn btn-primary view-button" 
                            data-record='${JSON.stringify(record)}' 
                            data-toggle="modal" data-target="#recordModal">
                            View
                        </button>`,
                        `<button class="btn btn-warning history-button" 
                            data-id="${record.id}" 
                            data-toggle="modal" data-target="#historyModal">
                            View
                        </button>` 
                    ];
                    table.row.add(row).draw();
                });
            },
            error: function (xhr) {
                alert('Error fetching records. Please try again.');
            }
        });
    }
    fetchRecords();

     // Fetch assistance history when history button is clicked
     $('#tableBody').on('click', '.history-button', function () {
        const individualId = $(this).data('id');
        $('#individualId').val(individualId);
        fetchAssistanceHistory(individualId);
    });

    function fetchAssistanceHistory(individualId) {
        $.ajax({
            url: 'get_assistance_history.php',
            method: 'GET',
            data: { individual_id: individualId },
            success: function (data) {
                console.log("Assistance history response:", data);
    
                if (Array.isArray(data) && data.length > 0) {
                    const historyHtml = data.map((record, index) => `
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>Assistance Request #${index + 2}</strong>
                            </div>
                            <div class="card-body">
                                <p><strong>Client Type:</strong> ${record.clientType}</p>
                                <p><strong>Assistance Type:</strong> ${record.assistanceType}</p>
                                <p><strong>Fund Type:</strong> ${record.fundType}</p>
                                <p><strong>Amount:</strong> ${record.amount}</p>
                                <p><strong>Date:</strong> ${record.date}</p>
                                <p><strong>Beneficiary:</strong> ${record.beneficiary}</p>
                            </div>
                        </div>
                    `).join('');
                    $('#historyContent').html(historyHtml);
                } else {
                    $('#historyContent').html('<p>No assistance records found.</p>');
                }
            },
            error: function (xhr) {
                console.error("Error fetching assistance history:", xhr.responseText);
                $('#historyContent').html('<p>Error loading assistance history.</p>');
            }
        });
    }
    

    // Toggle the Add Assistance Form when the button is clicked
    $('#addAssistanceButton').click(function () {
        $('#addAssistanceForm').toggle();  // Toggle the visibility of the form
    });

    // Submit new assistance form and log data for debugging
    $('#newAssistanceForm').submit(function (event) {
        event.preventDefault();

        // Log the form data to confirm all fields are populated
        console.log("Submitting form data:", $(this).serializeArray());

        const formData = $(this).serialize();

        $.ajax({
            url: 'add_assistance.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                console.log("Add assistance response:", response); 
                if (response.status === 'success') {
                    $('#newAssistanceForm')[0].reset();
                    $('#addAssistanceForm').hide();  // Hide the form after successful submission
                    const individualId = $('#individualId').val();
                    fetchAssistanceHistory(individualId);  // Refresh history to show the new record
                    alert('Assistance record added successfully.');
                } else {
                    alert(response.message || 'Error adding assistance record.');
                }
            },
            error: function (xhr) {
                console.error("Error adding assistance:", xhr.responseText);
                alert('Error adding assistance record. Please try again.');
            }
        });
    });

    function isValidMobileNumber(mobileNumber) {
        const regex = /^\d{11}$/;
        return regex.test(mobileNumber);
    }

    $('#recordForm').submit(function (event) {
        event.preventDefault();
        
        var mobileNumber = $('#mobileNumber').val();
        if (!isValidMobileNumber(mobileNumber)) {
            alert('Mobile number must be exactly 11 digits.');
            return;
        }
    
        var formData = $(this).serialize();
        $.ajax({
            url: 'add_record.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 'success') {
                    $('#addRecordForm').toggle();
                    fetchRecords();
                    alert('Record added successfully.');
                } else {
                    alert(response.message || 'An error occurred while adding the record.');
                }
            },
            error: function (xhr) {
                let response = xhr.responseJSON;
                if (response && response.status === 'error') {
                    if (xhr.status === 409) { // Conflict due to duplicate entry
                        alert(response.message || 'Duplicate entry: This record already exists.');
                    } else {
                        alert(response.message || 'Error adding record. Please try again.');
                    }
                } else {
                    alert('Unexpected error occurred. Please try again.');
                }
            }
        });
    });
    
    $('#addRecordButton').click(function () {
        $('#addRecordForm').toggle();
    });

    $('#recordModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var record = button.data('record');
        var modal = $(this);
        var modalBody = modal.find('.modal-body');
    
        // Section for personal information with bold labels
        var details = `
            <div class="card">
                <div class="card-header"><strong>Personal Information</strong></div>
                <div class="card-body">
                    <p><strong>First Name:</strong> ${record.firstName}</p>
                    <p><strong>Last Name:</strong> ${record.lastName}</p>
                    <p><strong>Middle Name:</strong> ${record.middleName}</p>
                    <p><strong>Age:</strong> ${record.age}</p>
                    <p><strong>Birth Place:</strong> ${record.birthPlace}</p>
                    <p><strong>Address:</strong> ${record.address}</p>
                    <p><strong>Education:</strong> ${record.education}</p>
                    <p><strong>Income Per Day:</strong> ${record.income}</p>
                    <p><strong>Occupation:</strong> ${record.occupation}</p>
                    <p><strong>Mobile Number:</strong> ${record.mobileNumber}</p>
                    <p><strong>Gender:</strong> ${record.gender}</p>
                    <hr>
                    <h4>Assistance Request #1</h4>
                    <p><strong>Client Type:</strong> ${record.clientType}</p>
                    <p><strong>Date:</strong> ${record.date === "0000-00-00" ? "N/A" : record.date}</p>
                    <p><strong>Assistance Type:</strong> ${record.assistanceType}</p>
                    <p><strong>Fund Type:</strong> ${record.fundType}</p>
                    <p><strong>Amount:</strong> ${record.amount}</p>
                    <p><strong>Beneficiary:</strong> ${record.beneficiary}</p>
                </div>
            </div>
            <hr>`;
        // Family members section
        if (record.familyMembers && record.familyMembers.length > 0) {
            details += `
                <div class="card mt-3">
                    <div class="card-header"><strong>Family Members</strong></div>
                    <div class="card-body">`;
            
            record.familyMembers.forEach(function (familyMember) {
                details += `
                    <p><strong>First Name:</strong> ${familyMember.firstName}</p>
                    <p><strong>Last Name:</strong> ${familyMember.lastName}</p>
                    <p><strong>Middle Name:</strong> ${familyMember.middleName}</p>
                    <p><strong>Date of Birth:</strong> ${familyMember.dateOfBirth}</p>
                    <p><strong>Gender:</strong> ${familyMember.gender}</p>
                    <p><strong>Relationship:</strong> ${familyMember.relationship}</p>
                    <hr>`;
            });
    
            details += `</div></div>`;
        } else {
            details += `
                <div class="card mt-3">
                    <div class="card-header">Family Members</div>
                    <div class="card-body"><p>No family members listed.</p></div>
                </div>`;
        }
    
        modalBody.html(details);
        modal.find('#editRecordButton').data('record', record);
    });
    

    $('#editRecordButton').click(function () {
        var record = $(this).data('record');
        if (!record) {
            alert("Error: Record data is missing.");
            return;
        }
        $('#recordModal').modal('hide');
        $('#editRecordModal').modal('show');
        var editForm = generateEditForm(record);
        $('#editRecordModal .modal-body').html(editForm);
    });

    function generateEditForm(record) {
        let formHtml = `
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
                ${createSelectField('Assistance Type', 'editAssistanceType', 'assistanceType', record.assistanceType, [
                    'Medical Assistance', 'Burial Assistance', 'Transportation Assistance',
                    'Educational Assistance', 'Emergency Shelter Assistance', 'Livelihood Assistance'
                ])}
                ${createSelectField('Fund Type', 'editFundType', 'fundType', record.fundType, ['LGU Fund', 'Barangay Fund', 'SK Fund'])}
                ${createInputField('Date', 'editDate', 'date', record.date, 'date')}
                ${createInputField('Amount', 'editAmount', 'amount', record.amount, 'number')}
                ${createInputField('Beneficiary', 'editBeneficiary', 'beneficiary', record.beneficiary)}
            </form>`;

        // Handle family members section if there are any
        if (record.familyMembers && record.familyMembers.length > 0) {
            formHtml += `<div class="card mt-3">
                <div class="card-header"><strong>Edit Family Members</strong></div>
                <div id="familyMembersEditSection" class="card-body">`;

            // Use familyMember variable in the loop to access the correct data
            record.familyMembers.forEach(function (familyMember, index) {
                formHtml += `
                    <div class="family-member-form" data-index="${index}">
                        <input type="hidden" name="familyMembers[${index}][id]" value="${familyMember.id || ''}"> <!-- Hidden family member ID -->
                        ${createInputField('Family Member First Name', `editFamilyFirstName${index}`, `familyMembers[${index}][firstName]`, familyMember.firstName)}
                        ${createInputField('Family Member Last Name', `editFamilyLastName${index}`, `familyMembers[${index}][lastName]`, familyMember.lastName)}
                        ${createInputField('Family Member Middle Name', `editFamilyMiddleName${index}`, `familyMembers[${index}][middleName]`, familyMember.middleName)}
                        ${createInputField('Date of Birth', `editFamilyDOB${index}`, `familyMembers[${index}][dateOfBirth]`, familyMember.dateOfBirth, 'date')}
                        ${createSelectField('Gender', `editFamilyGender${index}`, `familyMembers[${index}][gender]`, familyMember.gender, ['Male', 'Female'])}
                        ${createInputField('Relationship', `editFamilyRelationship${index}`, `familyMembers[${index}][relationship]`, familyMember.relationship)}
                    </div>`;
            });

            formHtml += `</div></div>`;
        }

        return formHtml;
    }


    function createInputField(label, id, name, value, type = 'text') {
        return `
            <div class="form-group">
                <label for="${id}">${label}</label>
                <input type="${type}" id="${id}" name="${name}" class="form-control" value="${value || ''}" required>
            </div>`;
    }

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

    $('#editRecordModal').on('click', '#saveEditButton', function () {
        var editedRecord = $('#editForm').serialize();
        $.ajax({
            url: 'edit_record.php',
            method: 'POST',
            data: editedRecord,
            success: function (response) {
                if (response.status === 'success') {
                    $('#editRecordModal').modal('hide');
                    fetchRecords();
                } else {
                    alert('Error editing record: ' + response.message);
                }
            },
            error: function (xhr) {
                alert('Error editing record. Please try again.');
            }
        });
    });
});
