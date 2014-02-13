<?php
  require_once('database.inc.php');

  session_start();
  $db = $_SESSION['db'];
  $userId = $_SESSION['userId'];
  $db->openConnection();
  $performanceInfo = $db->getPerformanceInformation($_REQUEST['movieName'], $_REQUEST['showDate']);
  $moviePerformanceId = $performanceInfo[0]["id"];
  $reservationStatus = $db->makeReservation(intval($moviePerformanceId));
  $db->closeConnection();
?>

<html>
<head><title>Booking 4</title><head>
<body><h1>Booking 4</h1>
  <a href="booking1.php">New booking</a>
  <p>
    <?php
      if ($reservationStatus == true) {
        print "One ticket booked.";
      } else {
        print "Sorry all tickets are booked.";
      }
    ?>
  </p>
</body>
</html>
