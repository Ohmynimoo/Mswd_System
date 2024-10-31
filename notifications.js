  $(document).ready(function() {
    function loadNotifications() {
      $.ajax({
        type: 'GET',
        url: 'fetch_notifications.php',
        dataType: 'json',
        success: function(data) {
          var notificationMenu = $('#notification-menu');
          notificationMenu.empty();

          if (Array.isArray(data) && data.length > 0) {
            $('#notification-count').text(data.length);

            var notificationMenuHtml = '';
            var now = new Date();  // Current date and time

            $.each(data, function(index, notification) {
              var notificationDate = new Date(notification.notification_date); // Parse notification date
              var isNew = (now - notificationDate) <= 1 * 60 * 60 * 1000;  // 1 hour in milliseconds
              var notificationHtml = '<li class="nav-item">';
              notificationHtml += '<a class="nav-link notification-link ' + (isNew ? 'new' : '') + '" data-id="' + notification.id + '">';
              notificationHtml += '<div class="notification-message" title="' + notification.message + '">' + notification.message + '</div>';
              notificationHtml += '</a></li>';
              notificationMenuHtml += notificationHtml;
            });

          notificationMenu.html(notificationMenuHtml);
        } else {
          $('#notification-count').text(0);
          notificationMenu.html('<li class="nav-item"><a class="nav-link">No notifications found.</a></li>');
        }
      },
      error: function(xhr, status, error) {
        alert('Error fetching notifications!');
      }
    });
  }

  loadNotifications();

  $('#notification-menu').on('click', '.notification-link', function(event) {
    event.preventDefault();
    var notificationId = $(this).data('id');
    var notificationLink = 'notification_details.php?id=' + notificationId;
    var clickedNotification = $(this);

    $.ajax({
      type: 'POST',
      url: 'mark_as_read.php',
      data: { id: notificationId },
      dataType: 'json',
      success: function(response) {
        if (response.unread_count !== undefined) {
          $('#notification-count').text(response.unread_count);
        }

        if (clickedNotification.hasClass('unread')) {
          clickedNotification.removeClass('unread');
        }

        window.location.href = notificationLink;
      },
      error: function() {
        alert('Error marking notification as read!');
      }
    });
  });
});
