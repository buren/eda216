<?php
/*
 * Class Database: interface to the movie database from PHP.
 *
 * You must:
 *
 * 1) Change the function userExists so the SQL query is appropriate for your tables.
 * 2) Write more functions.
 *
 */
class Database {
	private $host;
	private $port;
	private $userName;
	private $password;
	private $database;
	private $conn;

	/**
	 * Constructs a database object for the specified user.
	 */
	public function __construct($host, $port, $userName, $password, $database) {
		$this->host = $host;
		$this->port = $port;
		$this->userName = $userName;
		$this->password = $password;
		$this->database = $database;
	}

	/**
	 * Opens a connection to the database, using the earlier specified user
	 * name and password.
	 *
	 * @return true if the connection succeeded, false if the connection
	 * couldn't be opened or the supplied user name and password were not
	 * recognized.
	 */
	public function openConnection() {
		try {
			$this->conn = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->database",
					$this->userName,  $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$error = "Connection error: " . $e->getMessage();
			print $error . "<p>";
			unset($this->conn);
			return false;
		}
		return true;
	}

	/**
	 * Closes the connection to the database.
	 */
	public function closeConnection() {
		$this->conn = null;
		unset($this->conn);
	}

	/**
	 * Checks if the connection to the database has been established.
	 *
	 * @return true if the connection has been established
	 */
	public function isConnected() {
		return isset($this->conn);
	}

	/**
	 * Execute a database query (select).
	 *
	 * @param $query The query string (SQL), with ? placeholders for parameters
	 * @param $param Array with parameters
	 * @return The result set
	 */
	private function executeQuery($query, $param = null) {
		try {
			$stmt = $this->conn->prepare($query);
			$stmt->execute($param);
			$result = $stmt->fetchAll();
		} catch (PDOException $e) {
			$error = "*** Internal error: " . $e->getMessage() . "<p>" . $query;
			die($error);
		}
		return $result;
	}

	/**
	 * Execute a database update (insert/delete/update).
	 *
	 * @param $query The query string (SQL), with ? placeholders for parameters
	 * @param $param Array with parameters
	 * @return The number of affected rows
	 */
	private function executeUpdate($query, $param = null) {
		try {
			$stmt = $this->conn->prepare($query);
			$stmt->execute($param);
			} catch (PDOException $e) {
			$error = "*** Internal error: " . $e->getMessage() . "<p>" . $query;
			die($error);
		}
		return count($result);
	}

	public function getMovieNames() {
		$sql = "SELECT name FROM movies";
		$resultSet = $this->executeQuery($sql);
		$result = [];
		for ($i=0; $i < count($resultSet); $i++) {
			array_push($result, $resultSet[$i]["name"]);
		}
		return $result;
	}

	public function getMoviePerformances($movieName) {
		$sql = "SELECT show_date FROM movie_performances " .
      "INNER JOIN movies ON movie_performances.movie_id = movies.id " .
      "WHERE movies.name = ?";
    $resultSet = $this->executeQuery($sql, array($movieName));
    $result = [];
    for ($i=0; $i < count($resultSet); $i++) {
    	array_push($result, $resultSet[$i]["show_date"]);
    }
    return $result;
	}

	public function getPerformanceInformation($movieName, $date) {
		$sql = "SELECT * FROM movie_performances
     INNER JOIN movies ON movie_performances.movie_id = movies.id
     INNER JOIN theaters on movie_performances.theater_id = theaters.id
     WHERE movies.name = ? AND movie_performances.show_date = ?";
    $resultSet = $this->executeQuery($sql, array($movieName, $date));
    $result = [];
    for ($i=0; $i < count($resultSet); $i++) {
    	array_push($result, $resultSet[$i]);
    }
    return $result;
	}

	public function makeReservation($moviePerformanceId) {
		if ($this->getAvailableSeats($moviePerformanceId) == 0) {
			return false;
		}

		$username = $_SESSION['userId'];
		$userSQL = "SELECT username, id FROM users WHERE username = ?";
		$userResult = $this->executeQuery($userSQL, array($username));
		$uId = intval($userResult[0]["id"]);

		$this->conn->beginTransaction();

		$reservationSQL = "INSERT INTO reservations (user_id, movie_performance_id) VALUES (?, ?)";
		$affectedRows = $this->executeUpdate($reservationSQL, array($uId, $moviePerformanceId));

		if ($this->getAvailableSeats($moviePerformanceId) < 0) {
			$this->conn->rollBack();
			return false;
		} else {
			$this->conn->commit();
			return true;
		}
	}

	public function getAvailableSeats($moviePerformanceId) {
		$sql = "SELECT * FROM movie_performances
     INNER JOIN movies ON movie_performances.movie_id = movies.id
     INNER JOIN theaters on movie_performances.theater_id = theaters.id
     WHERE movie_performances.id = ?";
     $resultSet = $this->executeQuery($sql, array($moviePerformanceId));
     $theaterSeats = intval($resultSet[0]["seats"]);
     $reservedSeats = intval($this->getNumberOfReservedSeats($moviePerformanceId)[0]["count"]);
     return $theaterSeats - $reservedSeats;
	}

	public function getNumberOfReservedSeats($moviePerformanceId) {
		$movieReservationSQL = "SELECT COUNT(*) as count from reservations WHERE movie_performance_id = ?";
		$result = $this->executeQuery($movieReservationSQL, array($moviePerformanceId));
		return $result;
	}

	public function getTheaterFromName($theaterName) {
		$theaterSQL = "SELECT * FROM theaters WHERE name = ?";
		return $this->executeQuery($theaterSQL, array($theaterName));
	}

	/**
	 * Check if a user with the specified user id exists in the database.
	 * Queries the Users database table.
	 *
	 * @param userId The user id
	 * @return true if the user exists, false otherwise.
	 */
	public function userExists($userId) {
		$sql = "SELECT username FROM users WHERE username = ?";
		$result = $this->executeQuery($sql, array($userId));
		return count($result) == 1;
	}

	/*
	 * *** Add functions ***
	 */
}
?>
