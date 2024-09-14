document.getElementById('addFamilyMember').addEventListener('click', function() {
    var container = document.getElementById('familyMembersContainer');
    var newMember = document.createElement('div');
    newMember.classList.add('card', 'mb-3');  // Styling the new family member with Bootstrap classes
    newMember.innerHTML = `
        <div class="card-header">
            <h4>Family Member</h4>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="familyLastName[]">Last Name</label>
                    <input type="text" class="form-control" name="familyLastName[]" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="familyFirstName[]">First Name</label>
                    <input type="text" class="form-control" name="familyFirstName[]" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="familyMiddleName[]">Middle Name</label>
                    <input type="text" class="form-control" name="familyMiddleName[]">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="familyDateOfBirth[]">Date of Birth</label>
                    <input type="date" class="form-control" name="familyDateOfBirth[]" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="familyGender[]">Gender</label>
                    <select class="form-control" name="familyGender[]" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="familyRelationship[]">Relationship</label>
                    <input type="text" class="form-control" name="familyRelationship[]" required>
                </div>
            </div>
            <button type="button" class="btn btn-danger removeMember">Remove Member</button>
        </div>
    `;
    container.appendChild(newMember);

    // Add event listener to the remove button for the new member
    newMember.querySelector('.removeMember').addEventListener('click', function() {
        container.removeChild(newMember);
    });
});
