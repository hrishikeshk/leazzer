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
}

public class ParsedPage{
    Facility[] facilities;
    Pagination pagination;
}
