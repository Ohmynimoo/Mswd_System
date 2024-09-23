
// Image Modal Logic
var modal = document.getElementById("lightboxModal");
var modalImg = document.getElementById("modalImage");

var images = document.querySelectorAll(".enlarge-image");
images.forEach(function(image) {
    image.addEventListener("click", function() {
        modal.style.display = "block";
        modalImg.src = this.src;
    });
});

var closeModal = document.getElementsByClassName("close")[0];
closeModal.onclick = function() {
    modal.style.display = "none";
}

modal.onclick = function() {
    modal.style.display = "none";
}

// Submit comment logic
$('#submit-comment').on('click', function() {
    var notificationId = $(this).data('notification-id');
    var comment = $('#comment').val();

    $.ajax({
        url: 'submit_comment.php',
        type: 'POST',
        data: {
            notification_id: notificationId,
            comment: comment
        },
        success: function(response) {
            // Change the class to indicate success
            $('#commentToast').removeClass('bg-danger').addClass('bg-success');
            
            // Show the success message in the toast
            $('#commentToastBody').text(response);

            // Initialize and show the toast manually with a delay of 5 seconds
            var toastElement = document.getElementById('commentToast');
            var toast = new bootstrap.Toast(toastElement, { delay: 5000 }); // Show for 5 seconds
            toast.show();

            $('#comment').val('');  // Clear the comment box
        },
        error: function() {
            // Change the class to indicate an error
            $('#commentToast').removeClass('bg-success').addClass('bg-danger');
            
            // Show the error message in the toast
            $('#commentToastBody').text('Error submitting comment.');

            // Initialize and show the toast manually with a delay of 5 seconds
            var toastElement = document.getElementById('commentToast');
            var toast = new bootstrap.Toast(toastElement, { delay: 5000 }); // Show for 5 seconds
            toast.show();
        }
    });
});

// Send SMS for Interview and update request status to Processing
$('#send-sms-interview').on('click', function() {
    var notificationId = $(this).data('notification-id');  // Get the notification ID
    var mobile = $('#mobile-interview').val();  // Get the mobile number from input
    var message = $('#message-interview').val();  // Get the message from input

    // First, update the status to "Processing"
    $.ajax({
        url: 'update_request_status.php',  // Updated script to handle dynamic status update
        type: 'POST',
        data: {
            notification_id: notificationId,
            status: 'Processing'  // We are setting the status to "Processing"
        },
        success: function(response) {
            if (response.success) {
                // Status updated successfully, now attempt to send the SMS
                $.ajax({
                    url: 'send_sms.php',  // Backend script to handle SMS sending
                    type: 'POST',
                    data: {
                        notification_id: notificationId,
                        mobile: mobile,
                        message: message
                    },
                    success: function(smsResponse) {
                        alert(smsResponse);  // Display success or error message from send_sms.php
                    },
                    error: function() {
                        alert('Error sending SMS.');
                    }
                });
            } else {
                alert('Error updating request status: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating request status.');
        }
    });
});


$('#send-sms-payout').on('click', function() {
    var notificationId = $(this).data('notification-id');  // Get the notification ID from button data attribute
    var mobile = $('#mobile-payout').val();  // Get the mobile number
    var message = $('#message-payout').val();  // Get the SMS message

    // Step 1: Update the request status to "Processing"
    $.ajax({
        url: 'update_request_status.php',  // Backend script to update the status
        type: 'POST',
        data: {
            notification_id: notificationId,
            status: 'Processing'  // Set the status to "Processing"
        },
        success: function(response) {
            if (response.success) {
                // Status updated successfully, now send the SMS
                $.ajax({
                    url: 'send_sms.php',  // Backend script to send SMS
                    type: 'POST',
                    data: {
                        notification_id: notificationId,
                        mobile: mobile,
                        message: message
                    },
                    success: function(smsResponse) {
                        alert(smsResponse);  // Show the response from the send_sms.php
                    },
                    error: function() {
                        alert('Error sending SMS for Payout.');
                    }
                });
            } else {
                // Handle error in updating request status
                alert('Error updating request status: ' + response.message);
            }
        },
        error: function() {
            // Handle AJAX error
            alert('Error updating request status.');
        }
    });
});


//Deny Request button
document.getElementById('deny-request').addEventListener('click', function() {
    var notificationId = this.getAttribute('data-notification-id');
    
    if (confirm('Are you sure you want to deny this request?')) {
        console.log('Deny button clicked, sending request...');

        $.ajax({
            url: 'update_request_status.php',  // PHP file that handles the request
            type: 'POST',
            data: { 
                notification_id: notificationId,  // Pass the notification ID
                status: 'Denied'  // Pass the status as 'Denied'
            },
            success: function(response) {
                console.log('Parsed response:', response);

                if (response.success) {
                    // Show a success message in the toast
                    $('#commentToastBody').text(response.message);
                    var toastElement = new bootstrap.Toast(document.getElementById('commentToast'));
                    toastElement.show();

                    // Optionally reload the page after a short delay to see the updated status
                    setTimeout(function() {
                        window.location.reload();  // Reload to reflect the status change
                    }, 2000);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
                alert('Error: ' + error);
            }
        });
    }
});
