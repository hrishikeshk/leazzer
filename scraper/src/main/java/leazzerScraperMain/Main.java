package leazzerScraperMain;

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

    static void processOneArgPages(String arg, WebClient wc, ParsedPage pp){
        try{
            HtmlPage hp = wc.getPage(new URL("https://www.selfstorage.com/search?location=" + arg));
            System.out.println(hp.asText());
        }
        catch(Exception ex){
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }

    static void processOneArgMain(String arg, WebClient wc){
        try{
            HtmlPage hp = wc.getPage(new URL("https://www.selfstorage.com/search?location=" + arg));
            System.out.println(hp.asText());
        }
        catch(Exception ex){
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }

    static void processArgs(String[] va){
        try{
            WebClient wc = new WebClient();
            wc = new WebClientConfig().setIgnoreMost(wc);
            for(int i = 0; i < va.length; ++i){
                processOneArgMain(va[i], wc);
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
