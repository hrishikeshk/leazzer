package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.BrowserVersion;
import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.html.HtmlPage;

import java.net.URL;

public class Main {

    static String[] validateArgs(String[] args) throws Exception{
        if(args.length < 3){
            throw new Exception("Incorrect number of command line arguments passed. Pass admin user/pass and at least one city name or zip code...");
        }
        Persistence.adminUser = args[0].replace("\"", "");
        Persistence.adminPass = args[1].replace("\"", "");
        String[] va = new String[args.length - 2];
        for(int i = 2; i < args.length; ++i){
            va[i - 2] = new String();
            va[i - 2] = args[i].replace("\"", "");
            Logger.println("Received request to fetch: " + va[i - 2]);
        }
        return va;
    }

    static String consSearchURL(String arg, int pagenum){
        if(pagenum == 1){
            return "https://www.selfstorage.com/search?location=" + arg;
        }
        else{
            return "https://www.selfstorage.com/search?location=" + arg + "&page=" + String.valueOf(pagenum);
        }
    }

    static void printTestFacility(ParsedPage pp){

        Facility[] facs = pp.facilities;
        for(int i = 0; i < facs.length; ++i){
            Logger.println(" ---- facility INFO ... ");
            facs[i].printTest();
        }
    }

    static void processOneArgMain(String arg, WebClient wc, int pagenum, int maxPages){
        try{
            Logger.println("Fetching now page# " + pagenum + "... " + arg);
            HtmlPage hp = wc.getPage(new URL(consSearchURL(arg, pagenum)));
            ParsedPage pp = MainSearchPageProcessor.extractFacilities(hp, pagenum == 1);
            if(pagenum == 1){
                Logger.println("Num pages found for " + arg + ": " + pp.pagination.numPages);
                Logger.println("Num facilities found for " + arg + ": " + pp.pagination.numFacilities);
            }
            MainSearchPageProcessor.fetchDetailsAndPersist(arg, wc, pp);
            //printTestFacility(pp);

            Logger.println("Finished processing page# " + pagenum + "... for " + arg);
            if(maxPages == -1) {
                Pagination pageInfo = pp.pagination;
                int numPages = pageInfo.numPages;
                if(pagenum < numPages)
                    processOneArgMain(arg, wc, pagenum + 1, numPages);
            }
            else if(pagenum < maxPages){
                processOneArgMain(arg, wc, pagenum + 1, maxPages);
            }
        }
        catch(Exception ex){
            ex.printStackTrace();
            Logger.println("Caught exception: " + ex.getMessage());
        }
    }

    static void processArgs(String[] va){
        try{
            WebClient wc = new WebClient(BrowserVersion.CHROME);
            wc.getCookieManager().setCookiesEnabled(true);
            wc = new WebClientConfig().setIgnoreMost(wc);
            for(int i = 0; i < va.length; ++i){
                processOneArgMain(va[i], wc, 1, -1);
            }
        }
        catch(Exception ex){
            Logger.println("Caught exception: " + ex.getMessage());
        }
    }

    public static void main(String[] args){
        try{
            new Logger();

            Logger.println("Starting scraper www.selfstorage.com ... ");

            String[] va = validateArgs(args);
            processArgs(va);

            Logger.println("Finished scraper www.selfstorage.com");
            Logger.fclose();
        }
        catch(Exception ex){
            System.out.println("Caught exception: " + ex.getMessage());
            Logger.fclose();
        }
    }
}
