<?php
if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: ../");
    exit();
}
?>


<section id="content">
<nav>
			<i class='bx bx-menu toggle-sidebar' ></i>
			<form action="search.php" method="get">
				<div class="form-group">
					<input type="text" placeholder="Search..." name="search">
					<i class='bx bx-search icon' ></i>
				</div>
			</form>
			<a href="#" class="nav-link" onclick="toggleNotificationBox()">
                 <!-- Inside the profile section where you want to show notifications -->
                 <i class='bx bxs-bell icon' id="notificationBell"></i>
                  <span id="notificationCount" class="badge"></span> <!-- Span tag for notification count -->
                  <div id="notificationBox" class="notification-box" style="display: none;">
                   <!-- Notification content will be displayed here -->
                </div>
            </a>
         <style>
			.notification-toggle {
    position: relative;
}

#notificationCount {
    display: none; /* Initially hide the badge */
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: red;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 12px;
    text-align: center;
    line-height: 20px;
}

.notification-box {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 400px;
    background-color: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
}

			</style>
			<script>
				function toggleNotificationBox() {
    var notificationBox = document.getElementById("notificationBox");
    var notificationCount = document.getElementById("notificationCount");

    if (notificationBox.style.display === "none") {
        // Make AJAX request to fetch notifications
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "Admin/notification.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var notifications = JSON.parse(xhr.responseText);
                if (notifications.length > 0) {
                    var notificationHTML = "<ul>";
                    notifications.forEach(function(notification) {
                        notificationHTML += "<li>" + notification + "</li>";
                    });
                    notificationHTML += "</ul>";

                    notificationBox.innerHTML = notificationHTML;
                    notificationBox.style.display = "block";

                    notificationCount.innerHTML = notifications.length;
                    notificationCount.style.display = "inline"; // Display notification count
                } else {
                    notificationBox.innerHTML = "No notifications available.";
                    notificationBox.style.display = "block";

                    notificationCount.style.display = "none"; // Hide notification count when box is empty
                }
            }
        };
        xhr.send();
    } else {
        notificationBox.style.display = "none";
        notificationCount.style.display = "none"; // Optionally hide notification count when box is closed
    }
}

				</script>
			<a href="#" class="nav-link">
				<i class='bx bxs-message-square-dots icon' ></i>
				<span class="badge">8</span>
			</a>
			<span class="divider"></span>
			<div class="profile">
                <?php
                // $username = $_SESSION['username'];
                $imageURL = "logo.png";
                echo "<img src='{$imageURL}' alt='Admin Image'>";
                ?>
                <ul class="profile-link">
                    <li><a href="_profile.php" id="profile-link" class="body-link" data-section="profile"><i class='bx bxs-user-circle icon'></i> Student Profile</a></li>
                    <li><a href="#" class="body-link" data-section="settings"><i class='bx bxs-cog'></i> Settings</a></li>
                    <li><a href="?logout=1"><i class='bx bxs-log-out-circle'></i> Logout</a></li>
                </ul>
            </div>


		</nav>
