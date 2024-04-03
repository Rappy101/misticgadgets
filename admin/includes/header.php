<?php
require '../config/function.php';
require 'authentication.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="icon" href="../icon1.ico">
  <title>Mistic Gadgets</title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />


  <link href="assets/css/styles.css" rel="stylesheet" />
  <link href="assets/css/datac.css" rel="stylesheet" />
  <link href="assets/css/custom.css" rel="stylesheet" />


  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



  
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

  
  <script>
    // Function to fetch product data via AJAX
    function fetchProductsAndDisplayNotifications() {
        // Make an AJAX request to fetch product data
        $.ajax({
            url: 'check_stock.php',
            method: 'GET',
            dataType: 'json', // Expect JSON response
            success: function(response) {
                // Check if response is an array
                if (Array.isArray(response)) {
                    // Display notifications based on the 'quantity' data retrieved
                    var notificationCount = 0; // Initialize notification count
                    response.forEach(function(product) {
                        if (product.quantity == 0) {
                            showNotification('OUT OF STOCK! Only  ' + product.quantity + ' left for ' + product.name, 'out-of-stock');
                            notificationCount++;
                        } else if (product.quantity <= 3) {
                            showNotification('VERY LOW STOCK! Only ' + product.quantity + ' left for ' + product.name, 'low-stock');
                            notificationCount++;
                        }
                    });

                    // Update notification count display
                    updateNotificationCount(notificationCount);

                    // Show "No Notification" message if there are no notifications
                    if (notificationCount === 0) {
                        showNoNotificationMessage();
                    }
                } else {
                    console.error("Invalid response format. Expected an array.");
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Function to dynamically add a notification to the list
    function showNotification(message, className) {
        var notificationList = document.getElementById('notificationList');
        var notificationItem = document.createElement('li');
        notificationItem.textContent = message;
        notificationItem.className = className; // Add class to the notification item
        notificationList.appendChild(notificationItem);
    }

    // Function to show "No Notification" message
    function showNoNotificationMessage() {
        var notificationList = document.getElementById('notificationList');
        var noNotificationItem = document.createElement('li');
        noNotificationItem.textContent = "No Notification";
        notificationList.appendChild(noNotificationItem);
    }

    // Function to update notification count display
    function updateNotificationCount(count) {
        var notificationCountElement = document.getElementById('notificationCount');
        notificationCountElement.textContent = count;
        notificationCountElement.style.display = count > 0 ? 'block' : 'none'; // Show count if there are notifications
    }

    // Call the function to fetch products and display notifications when the page is ready
    $(document).ready(function() {
        fetchProductsAndDisplayNotifications();
    });
</script>







</head>

<body class="sb-nav-fixed">


  <?php include('navbar.php'); ?>

  <div id="layoutSidenav">

    <?php include('sidebar.php'); ?>

    <div id="layoutSidenav_content">

      <main>