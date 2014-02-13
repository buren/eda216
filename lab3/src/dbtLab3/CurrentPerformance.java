package dbtLab3;

public class CurrentPerformance {
    private static CurrentPerformance instance;

    private CurrentPerformance() {}

    private String movieName;
    private String date;
    private String theaterName;
    private int freeSeats = 0;

    private int performanceId = -1;

    public static CurrentPerformance instance() {
        if (instance == null)
            instance = new CurrentPerformance();
        return instance;
    }


    public static void setInstance(CurrentPerformance instance) {
        CurrentPerformance.instance = instance;
    }

    public String getMovieName() {
        return movieName;
    }

    public void setMovieName(String movieName) {
        this.movieName = movieName;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getTheaterName() {
        return theaterName;
    }

    public void setTheaterName(String theaterName) {
        this.theaterName = theaterName;
    }

    public int getFreeSeats() {
        return freeSeats;
    }

    public void setFreeSeats(int freeSeats) {
        this.freeSeats = freeSeats;
    }

    public int getPerformanceId() {
        return performanceId;
    }

    public void setPerformanceId(int performanceId) {
        this.performanceId = performanceId;
    }

}
