package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.*;
import com.gargoylesoftware.htmlunit.util.KeyDataPair;
import com.gargoylesoftware.htmlunit.util.NameValuePair;

import java.io.*;
import java.net.URL;
import java.nio.charset.Charset;
import java.util.ArrayList;
import java.util.List;

public class Persistence {

    static String adminUser;
    static String adminPass;

    static void appendFacilityAmenities(Facility f, List<NameValuePair> listKV){
        int num_amenities = 0;
        if(f.facilityAmenities != null){
            num_amenities = f.facilityAmenities.length;
        }
        listKV.add(new NameValuePair("num_amenities", String.valueOf(num_amenities)));
        for(int i = 0; i < num_amenities; ++i){
            String genName = "facility"+String.valueOf(i)+"amenity";
            listKV.add(new NameValuePair(genName, f.facilityAmenities[i]));
        }
    }

    static void appendReviews(Facility f, List<NameValuePair> listKV){
        int num_reviews = 0;
        if(f.reviews != null){
            num_reviews = f.reviews.length;
        }
        listKV.add(new NameValuePair("num_reviews", String.valueOf(num_reviews)));
        for(int i = 0; i < num_reviews; ++i){
            String genName = "review" + String.valueOf(i);
            listKV.add(new NameValuePair(genName + "listing_avail_id", f.reviews[i].listing_avail_id));
            listKV.add(new NameValuePair(genName + "rating", String.valueOf(f.reviews[i].rating)));
            listKV.add(new NameValuePair(genName + "excerpt", f.reviews[i].excerpt));
            listKV.add(new NameValuePair(genName + "title", f.reviews[i].title));
            listKV.add(new NameValuePair(genName + "message", f.reviews[i].message));
            listKV.add(new NameValuePair(genName + "nickname", f.reviews[i].nickname));
            listKV.add(new NameValuePair(genName + "timestamp", f.reviews[i].timestamp));
            listKV.add(new NameValuePair(genName + "stars", f.reviews[i].stars));
        }
    }

    static void appendUnitAmenities(Facility f, List<NameValuePair> listKV, int unitIter){
        int num_unit_amenities = 0;
        if(f.units[unitIter].amenities != null){
            num_unit_amenities = f.units[unitIter].amenities.length;
        }
        listKV.add(new NameValuePair("unit" + String.valueOf(unitIter) + "num_amenities", String.valueOf(num_unit_amenities)));

        for(int j = 0; j < num_unit_amenities; ++j){
            String genName = "unit" + String.valueOf(unitIter) + "_" + String.valueOf(j) + "amenity";
            listKV.add(new NameValuePair(genName, f.units[unitIter].amenities[j]));
        }
    }

    static void appendUnits(Facility f, List<NameValuePair> listKV){
        int num_units = 0;
        if(f.units != null){
            num_units = f.units.length;
        }
        listKV.add(new NameValuePair("num_units", String.valueOf(num_units)));

        for(int i = 0; i < num_units; ++i){
            String genNameUnit = "unit" + String.valueOf(i);
            listKV.add(new NameValuePair(genNameUnit + "size", f.units[i].size));
            listKV.add(new NameValuePair(genNameUnit + "price_freq", f.units[i].priceFreq));
            listKV.add(new NameValuePair(genNameUnit + "price", String.valueOf(f.units[i].price)));
            listKV.add(new NameValuePair(genNameUnit + "promo", f.units[i].promo));
            listKV.add(new NameValuePair(genNameUnit + "description", f.units[i].description));

            appendUnitAmenities(f, listKV, i);
        }
    }

    static void appendFacilityImages(Facility f, List<NameValuePair> listKV){
        int num_images = 0;
        if(f.images != null){
            num_images = f.images.length;
        }
        listKV.add(new NameValuePair("num_images", String.valueOf(num_images)));
        for(int i = 0; i < num_images; ++i){
            String genName = "facility" + String.valueOf(i);
            listKV.add(new NameValuePair(genName + "image_full_url", f.images[i].urlFullSize));
            listKV.add(new NameValuePair(genName + "image_tn_url", f.images[i].urlThumbnail));
        }
    }

    static void appendFacilityFields(Facility f, List<NameValuePair> listKV){
        listKV.add(new NameValuePair("id", f.id));
        listKV.add(new NameValuePair("name", f.name));
        listKV.add(new NameValuePair("about", f.about));
        listKV.add(new NameValuePair("url", f.url));
        listKV.add(new NameValuePair("distance", String.valueOf(f.distance)));
        listKV.add(new NameValuePair("lowest_price", String.valueOf(f.lowestPrice)));

        listKV.add(new NameValuePair("street", f.location.streetAddress));
        listKV.add(new NameValuePair("locality", f.location.addressLocality));
        listKV.add(new NameValuePair("region", f.location.addressRegion));
        listKV.add(new NameValuePair("zip", f.location.postalCode));

        appendFacilityAmenities(f, listKV);

        appendFacilityImages(f, listKV);

        appendReviews(f, listKV);

        appendUnits(f, listKV);
    }

    static Boolean postWithRetry(WebClient wc, WebRequest wr, Facility f, int attempt){
        Boolean success = false;
        try{
            wc.getPage(wr);
            //WebResponse wrp = p.getWebResponse();
            //String responseString = wrp.getContentAsString();
            //System.out.println(responseString);
            success = true;
        }
        catch(FailingHttpStatusCodeException fh){
            Logger.println("Empty web response: " + fh.getStatusCode() + " : " + fh.getStatusCode() + " : " + fh.getResponse().getContentAsString());
            if(attempt < 10){
                Logger.println("Network related problems: re-attempting ... attempt # " + (attempt + 1) + " : facility # " + f.id);
                try{
                    Thread.sleep(10000 * attempt);
                }
                catch(Exception e){
                    Logger.println("Interrupted sleep, proceeding anyway...");
                }
                success = postWithRetry(wc, wr, f, attempt + 1);
            }
            else{
                Logger.println("ERROR: Failed to post facility field details... " + f.id);
                fh.printStackTrace();
                Logger.println("Caught exception: " + fh.getMessage());
                success = false;
            }
        }
        catch(Exception ex){
            Logger.println("ERROR: Failed to post facility field details... " + f.id);
            ex.printStackTrace();
            Logger.println("Caught exception: " + ex.getMessage());
            success = false;
        }
        return success;
    }

    static Boolean postFacilityFields(WebClient wc, Facility f){
        Boolean success = false;
        String leazzerAdminUrl = "https://www.leazzer.com/admin/facility_detail.php";
        try {
            WebRequest wr = new WebRequest(new URL(leazzerAdminUrl));
            wr.setHttpMethod(HttpMethod.POST);
            wr.setEncodingType(FormEncodingType.URL_ENCODED);
            NameValuePair username = new NameValuePair("username", adminUser);
            NameValuePair password = new NameValuePair("password", adminPass);
            NameValuePair action = new NameValuePair("action", "insertupdate");
            List<NameValuePair> listKV = new ArrayList<NameValuePair>();
            listKV.add(username);
            listKV.add(password);
            listKV.add(action);

            appendFacilityFields(f, listKV);

            wr.setRequestParameters(listKV);

            success = postWithRetry(wc, wr, f, 1);
        }
        catch(Exception ex){
            Logger.println("ERROR: Failed to post facility field details... " + f.id);
            ex.printStackTrace();
            Logger.println("Caught exception: " + ex.getMessage());
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

    static ImageDetail downloadImage(String imgUrl, WebClient wc){
        byte[] ba = null;
        String contentType = null;
        try{
            Logger.println("Fetching facility Image: " + imgUrl);
            Page page = wc.getPage(new URL("https:" + imgUrl));
            WebResponse wr = page.getWebResponse();
            if(wr != null){
                contentType = wr.getContentType();
                InputStream is = wr.getContentAsStream();
                ba = inputStreamToByteArray(is);
            }
        }
        catch(Exception ex){
            Logger.println("ERROR: Failed fetching facility image: " + imgUrl);
            ex.printStackTrace();
            Logger.println("Caught exception: " + ex.getMessage());
        }
        return new ImageDetail(ba, contentType);
    }

    static Boolean postFacilityImage(WebClient wc, Facility f, ImageDetail id, String fileName){
        Boolean success = false;
        String leazzerAdminUrl = "https://www.leazzer.com/admin/facility_detail.php";
        try {
            WebRequest wr = new WebRequest(new URL(leazzerAdminUrl));
            wr.setHttpMethod(HttpMethod.POST);
            wr.setEncodingType(FormEncodingType.MULTIPART);
            NameValuePair username = new NameValuePair("username", adminUser);
            NameValuePair password = new NameValuePair("password", adminPass);
            NameValuePair action = new NameValuePair("action", "uploadimage");
            List<NameValuePair> listKV = new ArrayList<NameValuePair>();
            listKV.add(username);
            listKV.add(password);
            listKV.add(action);

            File imgLocalFile = File.createTempFile("tmp_", fileName);
            imgLocalFile.deleteOnExit();

            FileOutputStream fos = new FileOutputStream(imgLocalFile);
            fos.write(id.ba);
            fos.close();

            listKV.add(new NameValuePair("facility_id", f.id));

            KeyDataPair imgKV = new KeyDataPair("fileToUpload", imgLocalFile, fileName, id.mimeType, Charset.forName("utf-8"));

            listKV.add(imgKV);

            wr.setRequestParameters(listKV);

            success = postWithRetry(wc, wr, f, 1);
        }
        catch(Exception ex){
            Logger.println("ERROR: Failed to post facility image ... " + f.id);
            ex.printStackTrace();
            Logger.println("Caught exception: " + ex.getMessage());
            success = false;
        }
        return success;
    }

    public static void persist(WebClient wc, Facility f){
        Boolean success = postFacilityFields(wc, f);
        if(success){
            FacilityImage[] imgs = f.images;
            if(imgs != null){
                for(int i = 0; i < imgs.length; ++i){
                    String imgUrl = imgs[i].urlFullSize;
                    ImageDetail id = downloadImage(imgUrl, wc);
                    int lastSlash = imgUrl.lastIndexOf("/");
                    String fileName = imgUrl.substring(lastSlash + 1);
                    postFacilityImage(wc, f, id, fileName);
                }
            }
        }
        else{
            Logger.println("ERROR: Failed to persist Facility Details..." + f.id);
        }
    }
}
