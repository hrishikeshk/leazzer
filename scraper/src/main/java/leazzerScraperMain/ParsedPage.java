package leazzerScraperMain;

class Review{
    Double rating;
    String description;
    String title;
    String excerpt;
}

class FacilityLocation{
    String streetAddress;
    String addressLocality;
    String addressRegion;
    String postalCode;
}

class FacilityUnit{
    String size;
    String[] amenities;
    double price;
    String description;
    String promo;
}

class FacilityImage{
    String urlFullSize;
    String urlThumbnail;
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
        System.out.println("location.region: " + location.addressRegion);
        System.out.println("location.postalCode: " + location.postalCode);
        System.out.println("location.addressLocality: " + location.addressLocality);
        System.out.println("location.streetAddress: " + location.streetAddress);
    }
}

public class ParsedPage{
    Facility[] facilities;
    Pagination pagination;
}
