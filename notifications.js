$(document).ready(function() {
  function loadNotifications() {
    $.ajax({
      type: 'GET',
      url: 'fetch_notifications.php',
      dataType: 'json',
      success: function(data) {
        var notificationMenu = $('#notification-menu');
        notificationMenu.empty();

        // Insert search bar at the top
        notificationMenu.append('<li class="nav-item"><input type="text" class="form-control" id="notification-search" placeholder="Search by client name..." /></li>');

        if (Array.isArray(data) && data.length > 0) {
          $('#notification-count').text(data.length);

          // Store notifications for filtering
          var notificationMenuHtml = '';
          var now = new Date();

          $.each(data, function(index, notification) {
            var notificationDate = new Date(notification.notification_date);
            var isNew = (now - notificationDate) <= 1 * 60 * 60 * 1000;
            var notificationHtml = '<li class="nav-item">';
            notificationHtml += '<a class="nav-link notification-link ' + (isNew ? 'new' : '') + '" data-id="' + notification.id + '">';
            notificationHtml += '<div class="notification-message" title="' + notification.message + '">' + notification.message + '</div>';
            notificationHtml += '</a></li>';
            notificationMenuHtml += notificationHtml;
          });

          notificationMenu.append(notificationMenuHtml);
        } else {
          $('#notification-count').text(0);
          notificationMenu.append('<li class="nav-item"><a class="nav-link">No notifications found.</a></li>');
        }
      },
      error: function(xhr, status, error) {
        alert('Error fetching notifications!');
      }
    });
  }

  loadNotifications();

  // Filter notifications based on search input
  $('#notification-menu').on('keyup', '#notification-search', function() {
    var searchTerm = $(this).val().toLowerCase();
    $('#notification-menu .notification-link').each(function() {
      var message = $(this).text().toLowerCase();
      $(this).toggle(message.includes(searchTerm));
    });
  });

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
