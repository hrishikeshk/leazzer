package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.html.HtmlPage;

import java.net.URL;

public class Main {
    public static void main(String[] args){
        System.out.println("Hello World\n");
        WebClient wc = new WebClient();
        try{
            HtmlPage hp = wc.getPage(new URL("https://www.google.com"));
            System.out.println(hp.asText());
        }
        catch(Exception ex){
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }
}
