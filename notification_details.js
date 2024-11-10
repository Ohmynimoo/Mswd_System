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
            $('#commentToast').removeClass('bg-danger').addClass('bg-success');
            $('#commentToastBody').text(response);
            var toastElement = document.getElementById('commentToast');
            var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
            toast.show();
            $('#comment').val('');
        },
        error: function() {
            $('#commentToast').removeClass('bg-success').addClass('bg-danger');
            $('#commentToastBody').text('Error submitting comment.');
            var toastElement = document.getElementById('commentToast');
            var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
            toast.show();
        }
    });
});

// Send SMS for Interview and update request status to Processing
$('#send-sms-interview').on('click', function() {
    if (confirm('Are you sure you want to send the interview SMS?')) {
        var notificationId = $(this).data('notification-id');
        var mobile = $('#mobile-interview').val();
        var message = $('#message-interview').val();

        $.ajax({
            url: 'update_request_status.php',
            type: 'POST',
            data: {
                notification_id: notificationId,
                status: 'Processing'
            },
            success: function(response) {
                if (response.success) {
                    $.ajax({
                        url: 'send_sms.php',
                        type: 'POST',
                        data: {
                            notification_id: notificationId,
                            mobile: mobile,
                            message: message
                        },
                        success: function(smsResponse) {
                            $('#commentToast').removeClass('bg-danger').addClass('bg-success');
                            $('#commentToastBody').text('Sending SMS successfully');
                            var toastElement = document.getElementById('commentToast');
                            var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
                            toast.show();
                        },
                        error: function() {
                            $('#commentToast').removeClass('bg-success').addClass('bg-danger');
                            $('#commentToastBody').text('Error sending SMS.');
                            var toastElement = document.getElementById('commentToast');
                            var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
                            toast.show();
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
    }
});

// Send SMS for Payout and update request status to Approved
$('#send-sms-payout').on('click', function() {
    if (confirm('Are you sure you want to send the payout SMS?')) {
        var notificationId = $(this).data('notification-id');
        var mobile = $('#mobile-payout').val();
        var message = $('#message-payout').val();

        $.ajax({
            url: 'update_request_status.php',
            type: 'POST',
            data: {
                notification_id: notificationId,
                status: 'Approved'
            },
            success: function(response) {
                console.log('Status update response:', response);
                if (response.success) {
                    $.ajax({
                        url: 'send_sms.php',
                        type: 'POST',
                        data: {
                            notification_id: notificationId,
                            mobile: mobile,
                            message: message
                        },
                        success: function(smsResponse) {
                            $('#commentToast').removeClass('bg-danger').addClass('bg-success');
                            $('#commentToastBody').text('Sending SMS successfully');
                            var toastElement = document.getElementById('commentToast');
                            var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
                            toast.show();
                        },
                        error: function() {
                            $('#commentToast').removeClass('bg-success').addClass('bg-danger');
                            $('#commentToastBody').text('Error sending SMS for Payout.');
                            var toastElement = document.getElementById('commentToast');
                            var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
                            toast.show();
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
    }
});

// Deny Request button
document.getElementById('deny-request').addEventListener('click', function() {
    var notificationId = this.getAttribute('data-notification-id');
    
    if (confirm('Are you sure you want to deny this request?')) {
        console.log('Deny button clicked, sending request...');

        $.ajax({
            url: 'update_request_status.php',
            type: 'POST',
            data: { 
                notification_id: notificationId,
                status: 'Denied'
            },
            success: function(response) {
                console.log('Parsed response:', response);

                if (response.success) {
                    $('#commentToastBody').text('Deny Request Successfully');
                    var toastElement = document.getElementById('commentToast');
                    var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
                    toast.show();

                    setTimeout(function() {
                        window.location.reload();
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
