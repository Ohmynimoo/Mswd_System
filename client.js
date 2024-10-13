$(document).ready(function() {
    function fetchNotifications() {
        $.ajax({
            type: 'GET',
            url: 'client_notifications.php',
            dataType: 'json',
            success: function(data) {
                var notificationMenuHtml = '';

                if (Array.isArray(data) && data.length > 0) {
                    $('#notification-count').text(data.length);
                    $.each(data, function(index, notification) {
                        var notificationHtml = '<li class="nav-item dropdown-item notification-link" data-id="' + notification.id + '">';
                        notificationHtml += '<div class="notification-message ' + (notification.is_read === 0 ? 'unread' : '') + '">Comment In Your Request</div>';
                        notificationHtml += '</li>';
                        notificationMenuHtml += notificationHtml;
                    });
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

    // Handle click on notification
    $('#notification-menu').on('click', '.notification-link', function(event) {
        event.preventDefault();
        var notificationId = $(this).data('id');
        var notificationLink = 'view_comments.php?notification_id=' + notificationId;
        
        // Mark as read
        $.ajax({
            type: 'POST',
            url: 'mark_as_read.php',
            data: { id: notificationId },
            success: function(response) {
                var currentCount = parseInt($('#notification-count').text());
                $('#notification-count').text(currentCount - 1);
                $(this).find('.notification-message').removeClass('unread'); // Remove unread class
                window.location.href = notificationLink; // Redirect to notification details
            }.bind(this)
        });
    });

    // Fetch notifications periodically, e.g., every 30 seconds
    setInterval(fetchNotifications, 30000);
});