package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.BrowserVersion;
import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.html.HtmlPage;

import java.net.URL;

public class Main {

    static String[] validateArgs(String[] args) throws Exception{
        if(args.length < 1){
            throw new Exception("No command line arguments passed. Pass at least one city name or zip code...");
        }
        String[] va = new String[args.length];
        for(int i = 0; i < args.length; ++i){
            va[i] = new String();
            va[i] = args[i].replace("\"", "");
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
            System.out.println(" ---- facility INFO ... ");
            facs[i].printTest();
        }
    }

    static void processOneArgMain(String arg, WebClient wc, int pagenum, int maxPages){
        try{
            HtmlPage hp = wc.getPage(new URL(consSearchURL(arg, pagenum)));
            ParsedPage pp = MainSearchPageProcessor.extractFacilities(hp, pagenum == 1);
            if(pagenum == 1){
                System.out.println("Num pages: " + pp.pagination.numPages);
                System.out.println("Num facilities: " + pp.pagination.numFacilities);
            }
            MainSearchPageProcessor.fetchDetailsAndPersist(arg, wc, pp);
            printTestFacility(pp);
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
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }

    static void processArgs(String[] va){
        try{
            WebClient wc = new WebClient(BrowserVersion.CHROME);
            wc = new WebClientConfig().setIgnoreMost(wc);
            for(int i = 0; i < va.length; ++i){
                processOneArgMain(va[i], wc, 1, -1);
            }
        }
        catch(Exception ex){
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }

    public static void main(String[] args){
        try{
            String[] va = validateArgs(args);
            processArgs(va);
        }
        catch(Exception ex){
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }
}
