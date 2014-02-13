<?php
// $host = "puccini.cs.lth.se";
$host = "127.0.0.1";
$port = 3306;
$username = "root";
$password = "";
$database = "databasteknik";

try {
  $conn = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("select * from users");
  $stmt->execute();
  $result = $stmt->fetchAll();
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage();
}

?>

<html>
<head><title>PDO Connection Test</title><head>
<body>
<h2>PDO Connection Test</h2>

Now is (fetched from puccini):
<?php
    print $result[0][0];
	print ".";
  var_dump($result);
?>
</body>
</html>
