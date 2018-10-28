package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.Page;
import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.WebResponse;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;

public class Persistence {

    static Boolean postFacilityFields(WebClient wc, Facility f){
        Boolean success = false;
        try{

            success = true;
        }
        catch(Exception ex){
            System.out.println("ERROR: Failed to post facility field details... " + f.id);
            ex.printStackTrace();
            System.out.println("Caught exception: " + ex.getMessage());
            success = false;
        }
        return success;
    }

    static byte[] inputStreamToByteArray(InputStream is) throws IOException {
        ByteArrayOutputStream buffer = new ByteArrayOutputStream();
        int nRead;
        byte[] data = new byte[16384];
        while ((nRead = is.read(data, 0, data.length)) != -1) {
            buffer.write(data, 0, nRead);
        }
        buffer.flush();
        return buffer.toByteArray();
    }

    static byte[] downloadImage(String imgUrl, WebClient wc){
        byte[] ba = null;
        try{
            System.out.println("Fetching facility Image: " + imgUrl);
            Page page = wc.getPage(new URL("https:" + imgUrl));
            WebResponse wr = page.getWebResponse();
            if(wr != null){
                //String contentType = wr.getContentType();
                InputStream is = wr.getContentAsStream();
                ba = inputStreamToByteArray(is);
            }
        }
        catch(Exception ex){
            System.out.println("ERROR: Failed fetching facility image: " + imgUrl);
            ex.printStackTrace();
            System.out.println("Caught exception: " + ex.getMessage());
        }
        return ba;
    }

    static void postFacilityImage(WebClient wc, Facility f, byte[] ba){

    }

    public static void persist(WebClient wc, Facility f){
        Boolean success = postFacilityFields(wc, f);
        if(success){
            FacilityImage[] imgs = f.images;
            for(int i = 0; i < imgs.length; ++i){
                byte[] ba = downloadImage(imgs[i].urlFullSize, wc);
                postFacilityImage(wc, f, ba);
            }
        }
        else{
            System.out.println("ERROR: Failed to persist Facility Details..." + f.id);
        }
    }
}
