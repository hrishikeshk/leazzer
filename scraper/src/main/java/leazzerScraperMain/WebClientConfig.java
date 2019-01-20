package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.WebClientOptions;

public class WebClientConfig {
    public WebClient setIgnoreMost(WebClient wc){
        WebClientOptions wco = wc.getOptions();
        wco.setCssEnabled(true);
        wco.setGeolocationEnabled(false);
        wco.setDownloadImages(true);
        wco.setJavaScriptEnabled(true);
        wco.setThrowExceptionOnScriptError(false);
        wco.setThrowExceptionOnFailingStatusCode(true);
        wco.setPopupBlockerEnabled(true);
        wco.setDoNotTrackEnabled(false);
        wco.setAppletEnabled(true);
        wco.setHistoryPageCacheLimit(100);
        wco.setHistorySizeLimit(9999999);
        wco.setUseInsecureSSL(false);
        return wc;
    }
}
