<?php
  require_once('database.inc.php');

  session_start();
  $db = $_SESSION['db'];
  $userId = $_SESSION['userId'];
  $db->openConnection();
  $performanceInfo = $db->getPerformanceInformation($_REQUEST['movieName'], $_REQUEST['showDate']);
  $reservedSeats = $db->getNumberOfReservedSeats($performanceInfo[0]["id"])[0]["count"];
  $theater = $db->getTheaterFromName($performanceInfo[0]["name"]);
  $db->closeConnection();
?>

<html>
<head><title>Booking 3</title><head>
<body><h1>Booking 3</h1>
  Current user: <?php print $userId ?>
  <p>
    Movie '<?php print $_REQUEST['movieName'] ?>'
  <p>
  <p>
    Show date: '<?php print $_REQUEST['showDate'] ?>'
  </p>
  <p>
    Theater: '<?php print $theater[0]["name"] ?>'
  </p>
  <p>
    Seats: <?php print $theater[0]["seats"] ?>
  </p>
  <p>
    Available seats: <?php print intval($theater[0]["seats"]) - intval($reservedSeats) ?>
  </p>
  <form method=post action="booking4.php">
    <input name="movieName" type="hidden" value="<?php print $_REQUEST['movieName'] ?>">
    <input name="showDate" type="hidden" value="<?php print $_REQUEST['showDate'] ?>">
    <input name="theaterName" type="hidden" value="<?php print $theater[0]["seats"] ?>">
    <input type=submit value="Book ticket">
  </form>
</body>
</html>
