package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.WebClientOptions;

public class WebClientConfig {
    public WebClient setIgnoreMost(WebClient wc){
        WebClientOptions wco = wc.getOptions();
        wco.setCssEnabled(false);
        wco.setGeolocationEnabled(false);
        wco.setDownloadImages(true);
        wco.setJavaScriptEnabled(false);
        wco.setThrowExceptionOnScriptError(false);
        wco.setThrowExceptionOnFailingStatusCode(true);
        wco.setPopupBlockerEnabled(true);

        wco.setUseInsecureSSL(true);

        return wc;
    }
}
