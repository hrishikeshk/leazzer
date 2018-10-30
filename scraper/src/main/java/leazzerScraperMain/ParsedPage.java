package leazzerScraperMain;

import java.util.List;
import java.util.Map;

class ReviewEntry{
    String listing_avail_id;
    double rating;
    String title;
    String message;
    String excerpt;
    String nickname;
    String timestamp;
    String stars;
    public void printTest(){
        Logger.println("rating: " + rating);
        Logger.println("description: " + message);
        Logger.println("title: " + title);
        Logger.println("excerpt: " + excerpt);
    }
}

class FacilityLocation{
    String streetAddress;
    String addressLocality;
    String addressRegion;
    String postalCode;
    public void printTest(){
        Logger.println("location.region: " + addressRegion);
        Logger.println("location.postalCode: " + postalCode);
        Logger.println("location.addressLocality: " + addressLocality);
        Logger.println("location.streetAddress: " + streetAddress);
    }
}

class FacilityUnit{
    String size;
    String[] amenities;
    double price;
    String description;
    String promo;
    String priceFreq;
    public void printTest(){
        Logger.println("size: " + size);
        Logger.println("price: " + price);
        Logger.println("promo: " + promo);
        Logger.println("priceFreq: " + priceFreq);
        for(int i = 0; i < amenities.length; ++i){
            Logger.println("Amenity: " + amenities[i]);
        }
    }
}

class FacilityImage{
    String urlFullSize;
    String urlThumbnail;
    public void printTest(){
        Logger.println("Image thumb: " + urlThumbnail + " , Image full : " + urlFullSize);
    }
}

class Pagination{
    int numPages;
    int numFacilities;
}

class Facility {
    String id;
    String name;
    String about;
    String url;
    double distance;
    ReviewEntry[] reviews;
    FacilityLocation location;
    FacilityUnit[] units;
    FacilityImage[] images;
    double lowestPrice;
    String[] facilityAmenities;

    public void printTest(){
        Logger.println("id: " + id);
        Logger.println("name: " + name);
        Logger.println("url: " + url);
        Logger.println("distance: " + distance);
        Logger.println("about: " + about);
        location.printTest();
        for(int i = 0; i < reviews.length; ++i){
            reviews[i].printTest();
        }
        for(int i = 0; i < units.length; ++i){
            units[i].printTest();
        }
        for(int i = 0; i < images.length; ++i){
            images[i].printTest();
        }
    }
}

class ReviewsResponse{
    Boolean success;
    Map<String, List<ReviewEntry>> reviews;
    String message;
}

class ImageDetail{
    byte[] ba;
    String mimeType;
    ImageDetail(byte[] a, String m){
        ba = a;
        mimeType = m;
    }
}

public class ParsedPage{
    Facility[] facilities;
    Pagination pagination;
}
