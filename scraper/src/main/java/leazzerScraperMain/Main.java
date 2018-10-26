package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.BrowserVersion;
import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.html.HtmlPage;
import com.google.gson.Gson;

import java.net.URL;
import java.util.Map;

class Response {
    public Integer id;
    public String error;
    public Map<Integer, String> dates;
}

public class Main {

    static String[] validateArgs(String[] args) throws Exception{
        if(args.length < 1){
            throw new Exception("No command line arguments passed. Pass at least one city name or zip code...");
        }
        String[] va = new String[args.length];
        for(int i = 0; i < args.length; ++i){
            va[i] = new String();
            va[i] = args[i].replace("\"", "");
            System.out.println("Received request to fetch: " + va[i]);
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
            System.out.println("Fetching now page# " + pagenum + "... " + arg);
            HtmlPage hp = wc.getPage(new URL(consSearchURL(arg, pagenum)));
            ParsedPage pp = MainSearchPageProcessor.extractFacilities(hp, pagenum == 1);
            if(pagenum == 1){
                System.out.println("Num pages found for " + arg + ": " + pp.pagination.numPages);
                System.out.println("Num facilities found for " + arg + ": " + pp.pagination.numFacilities);
            }
            MainSearchPageProcessor.fetchDetailsAndPersist(arg, wc, pp);
            //printTestFacility(pp);

            System.out.println("Finished processing page# " + pagenum + "... for " + arg);
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

/*String json = "{" +
        "    \"id\": 6," +
        "    \"error\": \"0\"," +
        "    \"dates\": {\"34234\" : \"2011-01-01\", \"87474\" : \"2011-08-09\", \"74857\" : \"2011-09-22\"} " +
        "}";

            Response map = new Gson().fromJson(json, Response.class);

            String json2 = "{ \"success\":true,\n" +
                    "  \"reviews\":{\n" +
                    "    \"206354\":[\n" +
                    "              { \"listing_avail_id\":\"206354\",\n" +
                    "                \"rating\":\"5.0\",\n" +
                    "                \"title\":\"Excellent Experience\",\n" +
                    "                \"message\":\"I moved to this facility from another for the same size unit at almost half the cost. It is about 10 miles more from my home but my stuff is just going to be sitting there for years so I can leave it a little further out and save a lot of money. The owner in the office with extremely nice and even brought us water when we were unloading our stuff into the unit. Definitely not something we would have expected but thought it was very very nice of her. The place is very clean and I feel that my items are going to be secure there. I definitely recommend this place.\",\n" +
                    "                \"excerpt\":\"I moved to this facility from \",\n" +
                    "                \"nickname\":\"Wendy Shinsky\",\n" +
                    "                \"timestamp\":\"2017-08-12 15:59:02\",\n" +
                    "                \"stars\":\"            <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star\\\"><\\/i>\\n    \"      },\n" +
                    "             {\"listing_avail_id\":\"206354\",\n" +
                    "              \"rating\":\"4.5\",\n" +
                    "              \"title\":\"Great Place & Good People\",\n" +
                    "              \"message\":\"Place was easy to find but is a bit far out. Security looks good and the staff is very friendly.\",\n" +
                    "              \"excerpt\":\"Place was easy to find but is \",\n" +
                    "              \"nickname\":\"Scott Honn\",\n" +
                    "              \"timestamp\":\"2015-06-25 13:22:14\",\n" +
                    "              \"stars\":\"            <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star\\\"><\\/i>\\n                <i class=\\\"icon icon--star--half\\\"><\\/i>\\n    \"  }\n" +
                    "           ]\n" +
                    "   },\n" +
                    "   \"message\":\"\"\n" +
                    "}";

            ReviewsResponse r = new Gson().fromJson(json2, ReviewsResponse.class);
*/

            WebClient wc = new WebClient(BrowserVersion.CHROME);
            wc.getCookieManager().setCookiesEnabled(true);
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
