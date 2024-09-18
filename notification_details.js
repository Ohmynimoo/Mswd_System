
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



// Send SMS logic
$('#send-sms').on('click', function() {
    var notificationId = $(this).data('notification-id');
    var mobile = $('#mobile').val();
    var message = $('#message').val();

    $.ajax({
        url: 'send_sms.php',
        type: 'POST',
        data: {
            notification_id: notificationId,
            mobile: mobile,
            message: message
        },
        success: function(response) {
            alert('SMS sent successfully.');
        },
        error: function() {
            alert('Error sending SMS.');
        }
    });
});
