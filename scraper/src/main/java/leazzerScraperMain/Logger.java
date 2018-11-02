package leazzerScraperMain;

import java.io.File;
import java.io.PrintWriter;
import java.util.Date;

public class Logger {
    static File logFile = new File("execScraper" + new Date().toString().replaceAll(" ", "") + ".log");
    static PrintWriter pw;
    static Boolean enableLogFile;
    Logger(Boolean elf){
        enableLogFile = elf;
        if(elf) {
            try {
                pw = new PrintWriter(logFile);
            } catch (Exception e) {
                System.out.println("Failed to instantiate log file...");
                e.printStackTrace();
            }
        }
    }

    static void println(String s){
        System.out.println(s);
        if(enableLogFile)
            pw.println(s);
    }

    static void fclose(){
        if(enableLogFile){
            pw.flush();
            pw.close();
        }
    }
}
