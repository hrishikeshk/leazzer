package leazzerScraperMain;

import java.io.File;
import java.io.PrintWriter;
import java.util.Date;

public class Logger {
    static File logFile = new File("execScraper" + new Date().toString() + ".log");
    static PrintWriter pw;
    Logger(){
        try {
            pw = new PrintWriter(logFile);
        }
        catch(Exception e){
            System.out.println("Failed to instantiate log file...");
            e.printStackTrace();
        }
    }

    static void println(String s){
        System.out.println(s);
        pw.println(s);
    }

    static void fclose(){
        pw.flush();
        pw.close();
    }
}
