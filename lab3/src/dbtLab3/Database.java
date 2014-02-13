package dbtLab3;

import java.sql.*;
import java.util.ArrayList;
import java.util.Iterator;

/**
 * Database is a class that specifies the interface to the movie database. Uses
 * JDBC and the MySQL Connector/J driver.
 */
public class Database {
	/**
	 * The database connection.
	 */
	private Connection conn;
    private PreparedStatement ps;

	/**
	 * Create the database interface object. Connection to the database is
	 * performed later.
	 */
	public Database() {
		conn = null;
        ps = null;
    }

	/**
	 * Open a connection to the database, using the specified user name and
	 * password.
	 * 
	 * @param userName
	 *            The user name.
	 * @param password
	 *            The user's password.
	 * @return true if the connection succeeded, false if the supplied user name
	 *         and password were not recognized. Returns false also if the JDBC
	 *         driver isn't found.
	 */
	public boolean openConnection(String userName, String password) {
		try {
			Class.forName("com.mysql.jdbc.Driver");
			conn = DriverManager.getConnection(
					"jdbc:mysql://localhost:3306/databasteknik", userName,
					password);
        } catch (SQLException e) {
			e.printStackTrace();
			return false;
		} catch (ClassNotFoundException e) {
			e.printStackTrace();
			return false;
		}
		return true;
	}

	/**
	 * Close the connection to the database.
	 */
	public void closeConnection() {
		try {
			if (conn != null) {
				conn.close();
			}
		} catch (SQLException e) {
		}
		conn = null;
	}

	/**
	 * Check if the connection to the database has been established
	 * 
	 * @return true if the connection has been established
	 */
	public boolean isConnected() {
		return conn != null;
	}

	public String setCurrentUser(String username) {
        String findUserSQL = "SELECT username, id FROM users WHERE username = ?";
        try {
            PreparedStatement preparedStatement = conn.prepareStatement(findUserSQL);
            conn.prepareStatement(findUserSQL);
            preparedStatement.setString(1, username);
            ResultSet resultSet = preparedStatement.executeQuery();
            String name = null;
            while (resultSet.next()) {
                CurrentUser currentUser = CurrentUser.instance();
                currentUser.loginAs(username);
                currentUser.setId(resultSet.getInt("id"));
                name = username;
            }
            return name;
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    public Iterator<String> fetchMovieNames() {
        ArrayList<String> movieNames = new ArrayList<String>();
        String findMoviesSQL = "SELECT name FROM movies";
        try {
            PreparedStatement preparedStatement = conn.prepareStatement(findMoviesSQL);
            conn.prepareStatement(findMoviesSQL);
            ResultSet resultSet = preparedStatement.executeQuery();
            while (resultSet.next()) {
                movieNames.add(resultSet.getString("name"));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return movieNames.iterator();
    }

    public Iterator<String> findMoviePerformances(String movieName) {
        ArrayList<String> moviePerformances = new ArrayList<String>();
        String findMovieSQL = "SELECT show_date FROM movie_performances " +
                "INNER JOIN movies ON movie_performances.movie_id = movies.id " +
                "WHERE movies.name = ?";
        try {
            PreparedStatement preparedStatement = conn.prepareStatement(findMovieSQL);
            conn.prepareStatement(findMovieSQL);
            preparedStatement.setString(1, movieName);
            ResultSet resultSet = preparedStatement.executeQuery();
            while (resultSet.next()) {
                moviePerformances.add(resultSet.getString("show_date"));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return moviePerformances.iterator();
    }

    public CurrentPerformance setCurrentPerformance(String movieName, String date) {
        CurrentPerformance currentPerformance = CurrentPerformance.instance();

        String findMoviePerformance = "SELECT * FROM movie_performances " +
                "INNER JOIN movies ON movie_performances.movie_id = movies.id " +
                "INNER JOIN theaters on movie_performances.theater_id = theaters.id " +
                "WHERE movies.name = ?" +
                "AND movie_performances.show_date = ?";
        int theaterSeats = -1;
        int moviePerformanceId = -1;
        try {
            PreparedStatement preparedStatement = conn.prepareStatement(findMoviePerformance);
            conn.prepareStatement(findMoviePerformance);
            preparedStatement.setString(1, movieName);
            preparedStatement.setString(2, date);
            ResultSet resultSet = preparedStatement.executeQuery();
            while (resultSet.next()) {
                currentPerformance.setMovieName(resultSet.getString("movies.name"));
                currentPerformance.setDate(resultSet.getString("show_date"));
                currentPerformance.setTheaterName(resultSet.getString("theaters.name"));
                moviePerformanceId = resultSet.getInt("movie_performances.id");
                currentPerformance.setPerformanceId(moviePerformanceId);
                theaterSeats = resultSet.getInt("seats");
            }
                currentPerformance.setFreeSeats(theaterSeats - this.getNumberOfPerformanceReservations(moviePerformanceId));

        } catch (SQLException e) {
            e.printStackTrace();
        }
        return currentPerformance;
    }

    public boolean reserveTicket() {
        CurrentPerformance currentPerformance = CurrentPerformance.instance();
        this.setCurrentPerformance(currentPerformance.getMovieName(), currentPerformance.getDate());

        if (currentPerformance.getFreeSeats() == 0)
            return false;

        String reserveTicketSQL = "INSERT INTO reservations " +
                "(user_id, movie_performance_id) " +
                "VALUES " +
                "(?, ?)";
        try {
            conn.setAutoCommit(false);
            PreparedStatement preparedStatement = conn.prepareStatement(reserveTicketSQL);
            conn.prepareStatement(reserveTicketSQL);
            preparedStatement.setInt(1, CurrentUser.instance().getId());
            preparedStatement.setInt(2, CurrentPerformance.instance().getPerformanceId());
            preparedStatement.executeUpdate();

            // if free seats is less than 0 ? rollback : commit
            this.setCurrentPerformance(currentPerformance.getMovieName(), currentPerformance.getDate());

            if (currentPerformance.getFreeSeats() < 0) {
                conn.rollback();
                conn.setAutoCommit(true);
                return false;
            } else {
                conn.commit();
                conn.setAutoCommit(true);
                return true;
            }

        } catch (SQLException e) {
            e.printStackTrace();
        }
        return false;
    }

    private int getNumberOfPerformanceReservations(int moviePerformanceId) {
        String movieReservationSQL = "SELECT COUNT(*) as count from reservations WHERE movie_performance_id = ?";
        try {
            PreparedStatement preparedStatement = conn.prepareStatement(movieReservationSQL);
            conn.prepareStatement(movieReservationSQL);
            preparedStatement.setInt(1, moviePerformanceId);
            ResultSet resultSet = preparedStatement.executeQuery();
            while (resultSet.next()) {
                return resultSet.getInt("count");
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return -1;
    }

}
