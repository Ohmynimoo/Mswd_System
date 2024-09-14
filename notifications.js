$(document).ready(function() {
  // Fetch notifications via AJAX
  $.ajax({
    type: 'GET',
    url: 'fetch_notifications.php',
    dataType: 'json',
    success: function(data) {
      var notificationMenu = $('#notification-menu');
      notificationMenu.empty(); // Clear previous content

      if (Array.isArray(data) && data.length > 0) {
        $('#notification-count').text(data.length);
        var notificationMenuHtml = '';

        $.each(data, function(index, notification) {
          var notificationHtml = '<li class="nav-item">';
          notificationHtml += '<a class="nav-link notification-link ' + (notification.read === 0 ? 'unread' : '') + '" data-id="' + notification.id + '">';
          notificationHtml += '<div class="notification-message">' + notification.message + '</div>';
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

  // Handle notification click and mark as read
  $('#notification-menu').on('click', '.notification-link', function(event) {
    event.preventDefault();
    var notificationId = $(this).data('id');
    var notificationLink = 'notification_details.php?id=' + notificationId;

    // Mark as read via AJAX
    $.ajax({
      type: 'POST',
      url: 'mark_as_read.php',
      data: { id: notificationId },
      success: function() {
        var currentCount = parseInt($('#notification-count').text());
        $('#notification-count').text(currentCount - 1);
        $(this).removeClass('unread'); // Remove 'unread' class
        window.location.href = notificationLink;
      }.bind(this)
    });
  });
});
