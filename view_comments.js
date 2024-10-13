//reupload

$(document).ready(function() {
function fetchNotifications() {
    $.ajax({
        type: 'GET',
        url: 'client_notifications.php',
        dataType: 'json',
        success: function(data) {
            var notificationMenuHtml = '';
            var unreadCount = 0;  // Track unread notifications

            if (Array.isArray(data) && data.length > 0) {
                $.each(data, function(index, notification) {
                    var notificationHtml = '<li class="nav-item dropdown-item notification-link" data-id="' + notification.id + '">';
                    notificationHtml += '<div class="notification-message ' + (notification.is_read === 0 ? 'unread' : '') + '">New Comment In your Uploaded files</div>';
                    notificationHtml += '</li>';

                    // Increment unread count if the notification is unread
                    if (notification.is_read === 0) {
                        unreadCount++;
                    }

                    notificationMenuHtml += notificationHtml;
                });

                // Update the notification count based on unreadCount
                $('#notification-count').text(unreadCount);
            } else {
                $('#notification-count').text(0);
                notificationMenuHtml = '<li class="nav-item dropdown-item"><a class="nav-link">No notifications found.</a></li>';
            }

            $('#notification-menu').html(notificationMenuHtml);
        },
        error: function(xhr, status, error) {
            alert('Error fetching notifications!');
        }
    });
}

// Fetch notifications when the page loads
fetchNotifications();

// Mark all notifications as read when clicking on the Notifications menu
$('.nav-link[href="view_comments.php"]').on('click', function(event) {
    event.preventDefault();  // Prevent the default link action

    // Mark all notifications as read via an AJAX request
    $.ajax({
        type: 'POST',
        url: 'client_notifications_read.php',  // Mark all notifications as read
        success: function(response) {
            // Immediately set the notification count to 0
            $('#notification-count').text(0);
            $('.notification-message').removeClass('unread');  // Remove unread class from all notifications
        },
        error: function(xhr, status, error) {
            alert('Error marking notifications as read!');
        }
    });

    // Redirect to the notifications page
    window.location.href = 'view_comments.php';
});

// Fetch notifications periodically, e.g., every 30 seconds
setInterval(fetchNotifications, 30000);
});