$(document).ready(function() {
  function loadNotifications() {
    $.ajax({
      type: 'GET',
      url: 'fetch_notifications.php',
      dataType: 'json',
      success: function(data) {
        var notificationMenu = $('#notification-menu');
        notificationMenu.empty();

        // Update unread count
        if (data.unread_count !== undefined) {
          $('#notification-count').text(data.unread_count); // Set unread count
        }

        // Insert search bar at the top
        notificationMenu.append('<li class="nav-item"><input type="text" class="form-control" id="notification-search" placeholder="Search by client name..." /></li>');

        if (Array.isArray(data.notifications) && data.notifications.length > 0) {
          var notificationMenuHtml = '';
          $.each(data.notifications, function (index, notification) {
            var notificationClass = (notification.is_read === 0 || notification.is_read === '0') ? 'unread' : 'read';
            var badgeHtml = (notificationClass === 'unread') ? '<span class="badge badge-warning">New</span>' : '';
            var notificationHtml = '<li class="nav-item">';
            notificationHtml += '<a class="nav-link notification-link ' + notificationClass + '" data-id="' + notification.id + '">';
            notificationHtml += '<div class="notification-message" title="' + notification.message + '">' + notification.message + ' ' + badgeHtml + '</div>';
            notificationHtml += '</a></li>';
            notificationMenuHtml += notificationHtml;
        });
        

          notificationMenu.append(notificationMenuHtml);
        } else {
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
    var clickedNotification = $(this);

    $.ajax({
      type: 'POST',
      url: 'mark_as_read.php',
      data: { id: notificationId },
      dataType: 'json',
      success: function(response) {
        if (response.unread_count !== undefined) {
          $('#notification-count').text(response.unread_count); // Update unread count
        }

        // Mark the notification as read visually
        clickedNotification.removeClass('unread').addClass('read');

        // Redirect to notification details page
        var notificationLink = 'notification_details.php?id=' + notificationId;
        window.location.href = notificationLink;
      },
      error: function() {
        alert('Error marking notification as read!');
      }
    });
  });
});
