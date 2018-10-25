package leazzerScraperMain;

class Review{
    Double rating;
    String description;
    String title;
    String excerpt;
    public void printTest(){
        System.out.println("rating: " + rating);
        System.out.println("description: " + description);
        System.out.println("title: " + title);
        System.out.println("excerpt: " + excerpt);
    }
}

class FacilityLocation{
    String streetAddress;
    String addressLocality;
    String addressRegion;
    String postalCode;
    public void printTest(){
        System.out.println("location.region: " + addressRegion);
        System.out.println("location.postalCode: " + postalCode);
        System.out.println("location.addressLocality: " + addressLocality);
        System.out.println("location.streetAddress: " + streetAddress);
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
        System.out.println("size: " + size);
        System.out.println("price: " + price);
        System.out.println("promo: " + promo);
        System.out.println("priceFreq: " + priceFreq);
        for(int i = 0; i < amenities.length; ++i){
            System.out.println("Amenity: " + amenities[i]);
        }
    }
}

class FacilityImage{
    String urlFullSize;
    String urlThumbnail;
    public void printTest(){
        System.out.println("Image thumb: " + urlThumbnail + " , Image full : " + urlFullSize);
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
    Review[] reviews;
    FacilityLocation location;
    FacilityUnit[] units;
    FacilityImage[] images;
    double lowestPrice;
    String[] facilityAmenities;

    public void printTest(){
        System.out.println("id: " + id);
        System.out.println("name: " + name);
        System.out.println("url: " + url);
        System.out.println("distance: " + distance);
        System.out.println("about: " + about);
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

public class ParsedPage{
    Facility[] facilities;
    Pagination pagination;
}
