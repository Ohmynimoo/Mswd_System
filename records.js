$(document).ready(function () {
    var table = $("#example2").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": true
    });
    function fetchRecords() {
        $.ajax({
            url: 'add_record.php',
            method: 'GET',
            success: function (data) {
                table.clear().draw();
                data.forEach(function (record) {
                    var dateValue = record.date === "0000-00-00" ? "N/A" : record.date;
                    var row = [
                        record.firstName,
                        record.lastName,
                        record.middleName,
                        `<button class="btn btn-primary view-button" data-record='${JSON.stringify(record)}' data-toggle="modal" data-target="#recordModal">View</button>`
                    ];
                    table.row.add(row).draw();
                });
            },
            error: function (xhr, status, error) {
                console.error("Error fetching records:", xhr.responseText);
                alert('Error fetching records. Please try again.');
            }
        });
    }
    fetchRecords();
    $('#recordForm').submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'add_record.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                console.log('Record added successfully');
                $('#addRecordForm').toggle();
                fetchRecords();
            },
            error: function (xhr, status, error) {
                console.error('Error adding record:', xhr.responseText);
                alert('Error adding record. Please try again.');
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
    // Personal Information Card
    var details = `
        <div class="card">
            <div class="card-header">
                <strong>Personal Information</strong>
            </div>
            <div class="card-body">
                <p>First Name: ${record.firstName}</p>
                <p>Last Name: ${record.lastName}</p>
                <p>Middle Name: ${record.middleName}</p>
                <p>Age:${record.age}</p>
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
        </div>
        <hr>
    `;
    // Family Members Card (All in one card)
    if (record.familyMembers && record.familyMembers.length > 0) {
        details += `
            <div class="card mt-3">
                <div class="card-header">
                    <strong>Family Members</strong>
                </div>
                <div class="card-body">
        `;
        record.familyMembers.forEach(function(familyMember, index) {
            details += `
                <p>First Name:${familyMember.firstName}</p>
                <p>Last Name: ${familyMember.lastName}</p>
                <p>Middle Name:${familyMember.middleName}</p>
                <p>Date of Birth:${familyMember.dateOfBirth}</p>
                <p>Gender: ${familyMember.gender}</p>
                <p>Relationship: ${familyMember.relationship}</p>
                <hr>
            `;
        });
        details += `
                </div>
            </div>
        `;
    } else {
        details += `
            <div class="card mt-3">
                <div class="card-header">
                    Family Members
                </div>
                <div class="card-body">
                    <p>No family members listed.</p>
                </div>
            </div>
        `;
    }
    modalBody.html(details);
    modal.find('#editRecordButton').data('record', record);
});
  $('#editRecordButton').click(function () {
      var record = $(this).data('record');
      if (!record) {
          console.error("Record is undefined");
          alert("Error: Record data is missing.");
          return;
      }
      $('#recordModal').modal('hide');
      $('#editRecordModal').modal('show');

      var editForm = `
          <form id="editForm" class="card-body">
              <input type="hidden" id="editId" name="id" value="${record.id}">
              <div class="form-group">
                  <label for="editFirstName">First Name</label>
                  <input type="text" id="editFirstName" name="firstName" class="form-control" value="${record.firstName || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editLastName">Last Name</label>
                  <input type="text" id="editLastName" name="lastName" class="form-control" value="${record.lastName || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editMiddleName">Middle Name</label>
                  <input type="text" id="editMiddleName" name="middleName" class="form-control" value="${record.middleName || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editAge">Age</label>
                  <input type="number" id="editAge" name="age" class="form-control" value="${record.age || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editBirthPlace">Birth Place</label>
                  <input type="text" id="editBirthPlace" name="birthPlace" class="form-control" value="${record.birthPlace || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editAddress">Address</label>
                  <input type="text" id="editAddress" name="address" class="form-control" value="${record.address || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editEducation">Education</label>
                  <input type="text" id="editEducation" name="education" class="form-control" value="${record.education || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editIncome">Income Per Day</label>
                  <input type="number" id="editIncome" name="income" class="form-control" value="${record.income || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editOccupation">Occupation</label>
                  <input type="text" id="editOccupation" name="occupation" class="form-control" value="${record.occupation || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editMobileNumber">Mobile Number</label>
                  <input type="number" id="editMobileNumber" name="mobileNumber" class="form-control" value="${record.mobileNumber || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editGender">Gender</label>
                  <select class="form-control" id="editGender" name="gender" required>
                      <option value="Male" ${record.gender === 'Male' ? 'selected' : ''}>Male</option>
                      <option value="Female" ${record.gender === 'Female' ? 'selected' : ''}>Female</option>
                  </select>
              </div>
              <div class="form-group">
                  <label for="editClientType">Client Type</label>
                  <select class="form-control" id="editClientType" name="clientType" required>
                      <option value="4ps" ${record.clientType === '4ps' ? 'selected' : ''}>4ps</option>
                      <option value="Senior Citizen" ${record.clientType === 'Senior Citizen' ? 'selected' : ''}>Senior Citizen</option>
                      <option value="PWD" ${record.clientType === 'PWD' ? 'selected' : ''}>Person With Disabilities(PWD)</option>
                      <option value="Solo Parent" ${record.clientType === 'Solo Parent' ? 'selected' : ''}>Solo Parent</option>
                  </select>
              </div>
              <div class="form-group">
                  <label for="editAssistanceType">Assistance Type</label>
                  <select class="form-control" id="editAssistanceType" name="assistanceType" required>
                      <option value="Medical Assistance" ${record.assistanceType === 'Medical Assistance' ? 'selected' : ''}>Medical Assistance</option>
                      <option value="Burial Assistance" ${record.assistanceType === 'Burial Assistance' ? 'selected' : ''}>Burial Assistance</option>
                      <option value="Transportation Assistance" ${record.assistanceType === 'Transportation Assistance' ? 'selected' : ''}>Transportation Assistance</option>
                      <option value="Educational Assistance" ${record.assistanceType === 'Educational Assistance' ? 'selected' : ''}>Educational Assistance</option>
                      <option value="Emergency Shelter Assistance" ${record.assistanceType === 'Emergency Shelter Assistance' ? 'selected' : ''}>Emergency Shelter Assistance</option>
                      <option value="Livelihood Assistance" ${record.assistanceType === 'Livelihood Assistance' ? 'selected' : ''}>Livelihood Assistance</option>
                  </select>
              </div>
              <div class="form-group">
                  <label for="editFundType">Fund Type</label>
                  <select class="form-control" id="editFundType" name="fundType" required>
                      <option value="LGU Fund" ${record.fundType === 'LGU Fund' ? 'selected' : ''}>LGU Fund</option>
                      <option value="Barangay Fund" ${record.fundType === 'Barangay Fund' ? 'selected' : ''}>Barangay Fund</option>
                      <option value="SK Fund" ${record.fundType === 'SK Fund' ? 'selected' : ''}>SK Fund</option>
                  </select>
              </div>
              <div class="form-group">
                  <label for="editDate">Date</label>
                  <input type="date" id="editDate" name="date" class="form-control" value="${record.date || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editAmount">Amount</label>
                  <input type="number" id="editAmount" name="amount" class="form-control" value="${record.amount || ''}" required>
              </div>
              <div class="form-group">
                  <label for="editBeneficiary">Beneficiary</label>
                  <input type="text" id="editBeneficiary" name="beneficiary" class="form-control" value="${record.beneficiary || ''}" required>
              </div>
          </form>
      `;
      $('#editRecordModal .modal-body').html(editForm);
  });

  $('#editRecordModal').on('click', '#saveEditButton', function () {
      var editedRecord = {
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

      $.ajax({
          url: 'edit_record.php',
          method: 'POST',
          data: editedRecord,
          success: function (response) {
              console.log(response);
              if (response.status === 'success') {
                  console.log('Record edited successfully');
                  $('#editRecordModal').modal('hide');
                  fetchRecords();  // Refresh the records to show the updated data
              } else {
                  console.error('Error editing record:', response.message);
                  alert('Error editing record: ' + response.message);
              }
          },
          error: function (xhr, status, error) {
              console.error('Error editing record:', xhr.responseText);
              alert('Error editing record. Please try again.');
          }
      });
  });
});