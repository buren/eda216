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
      if ($reservationStatus[0]["id"] > 0) {
        print "One ticket booked: {$reservationStatus[0]["id"]}";
      } else {
        print "Sorry all tickets are booked.";
      }
    ?>
  </p>
</body>
</html>
