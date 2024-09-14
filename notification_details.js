// script.js

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
            alert(response);
            $('#comment').val('');
        },
        error: function() {
            alert('Error submitting comment.');
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
