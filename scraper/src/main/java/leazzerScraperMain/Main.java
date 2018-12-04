package leazzerScraperMain;

import java.util.Calendar;
import java.util.Date;

public class Main {

    public static void main(String[] args){
        Calendar cal = Calendar.getInstance();
        Date now = cal.getTime();

        cal.set(Calendar.YEAR, 2019);
        cal.set(Calendar.MONTH, Calendar.MARCH);
        cal.set(Calendar.DAY_OF_MONTH, 1);

        if(now.before(cal.getTime()))
            SwingMain.swingMainEntry();
    }
}
