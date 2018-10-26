package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.Page;
import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.WebResponse;
import com.gargoylesoftware.htmlunit.html.*;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

public class MainSearchPageProcessor {

    static Pagination consPagination(HtmlPage hp, Boolean isFirstPage){
        Pagination pageInfo = null;
        if (isFirstPage) {
            HtmlElement he = hp.getBody();
            DomNode dn = he.getFirstByXPath("/html/body/main/div[2]");

            String[] splitStr = dn.asText().split(" ");
            String infoStrPages = splitStr[3];
            String infoStrNumFacilities = splitStr[4].substring(1);
            int numPages = Integer.parseInt(infoStrPages);
            int numFacilities = Integer.parseInt(infoStrNumFacilities);

            pageInfo = new Pagination();
            pageInfo.numPages = numPages;
            pageInfo.numFacilities = numFacilities;
        }
        return pageInfo;
    }

    static void extractLocation(DomNode locationNode, Facility f){
        FacilityLocation l = new FacilityLocation();

        DomNode sa = locationNode.querySelector(".street");
        if(sa != null)
            l.streetAddress = sa.getTextContent();
        DomNode al = locationNode.querySelector(".city");
        if(al != null)
            l.addressLocality = al.getTextContent();
        DomNode ar = locationNode.querySelector(".state");
        if(ar != null)
            l.addressRegion = ar.getTextContent();
        DomNode pc = locationNode.querySelector(".zip");
        if(pc != null)
            l.postalCode = pc.getTextContent();

        f.location = l;
        DomNode distNode =  locationNode.querySelector(".facility-distance");
        if(distNode != null)
            f.distance = Double.parseDouble(distNode.getTextContent().split(" ")[0]);
    }

    static Facility extractOneFacility(DomNode facNode){
        Facility f = new Facility();

        NamedNodeMap m = facNode.getAttributes();
        f.id = m.getNamedItem("data-facility-id").getNodeValue();
        DomNode detailsNode = facNode.querySelector(".facility-details");
        if(detailsNode != null){
            DomNode nameSpanNode = detailsNode.querySelector(".fn");
            if(nameSpanNode != null)
                f.name = nameSpanNode.getTextContent();

            DomNode urlNode = detailsNode.querySelector(".link--facility");
            if(urlNode != null){
                NamedNodeMap m_attrs = urlNode.getAttributes();
                f.url = m_attrs.getNamedItem("href").getNodeValue();
                if(f.name == null){
                    f.name = urlNode.getTextContent();
                }
            }
            DomNode locationNode = detailsNode.querySelector(".facility-location");
            if(locationNode != null){
                extractLocation(locationNode, f);
            }
        }
        return f;
    }

    public static ParsedPage extractFacilities(HtmlPage hp, Boolean isFirstPage){
        Pagination pageInfo = consPagination(hp, isFirstPage);
        HtmlElement body = hp.getBody();

        HtmlElement dn = body.getFirstByXPath("//*[@id=\"facilities-list\"]");
        DomNodeList<DomNode> dnList = dn.querySelectorAll(".facility");
        Iterator<DomNode> iter = dnList.iterator();
        List<Facility> facilities = new ArrayList<Facility>();

        while(iter.hasNext()){
            DomNode he = iter.next();
            facilities.add(extractOneFacility(he));
        }

        ParsedPage pp = new ParsedPage();
        pp.pagination = pageInfo;
        pp.facilities = facilities.toArray(new Facility[facilities.size()]);

        return pp;
    }

    static void extractImageUrls(HtmlElement body, Facility f){
        HtmlElement galleryNode = body.querySelector(".gallery");
        if(galleryNode != null){
            Iterable<DomElement> imgs = galleryNode.getChildElements();
            Iterator<DomElement> iter = imgs.iterator();
            List<FacilityImage> imgList = new ArrayList<FacilityImage>();
            while(iter.hasNext()){
                DomElement elem = iter.next();
                FacilityImage fi = new FacilityImage();
                fi.urlThumbnail = elem.getAttribute("data-thumb");
                fi.urlFullSize = fi.urlThumbnail;
                imgList.add(fi);
            }
            f.images = imgList.toArray(new FacilityImage[imgList.size()]);
        }
    }

    static void extractUnitDetails(HtmlElement body, Facility f){
        DomNodeList<DomNode> units = body.querySelectorAll(".facility-unit-with-form");
        FacilityUnit[] unitArr = new FacilityUnit[units.size()];
        for(int i = 0; i < units.size(); ++i){
            DomNode dn = units.get(i);
            DomNode unitNodeInner = dn.querySelector(".facility-unit");
            if(unitNodeInner != null){
                NamedNodeMap m = unitNodeInner.getAttributes();
                if(m != null){
                    unitArr[i] = new FacilityUnit();
                    Node sizeNode = m.getNamedItem("data-size");
                    if(sizeNode != null)
                        unitArr[i].size = sizeNode.getNodeValue();
                    Node amenitiesNode = m.getNamedItem("data-amenities");
                    if(amenitiesNode != null){
                        unitArr[i].amenities = amenitiesNode.getNodeValue().split(",");
                    }
                }
            }
            DomNode priceNode = dn.querySelector(".unit-price");
            if(priceNode != null){
                unitArr[i].price = Double.parseDouble(priceNode.getTextContent().substring(1));
                DomNode freqNode = dn.querySelector(".unit-price-frequency");
                if(freqNode != null){
                    unitArr[i].priceFreq = freqNode.getTextContent();
                }
            }
        }
        f.units = unitArr;
    }

    static void extractFacilityReviews(Facility f, WebClient wc){
        try{
            System.out.println("Fetching facility reviews: " + f.url);
            //Page page = wc.getPage(new URL("https://www.selfstorage.com/search/reviews?facilityIds[]=206354"));
            Page page = wc.getPage(new URL("https://www.selfstorage.com/search/reviews?facilityIds[]=" + f.id));
            WebResponse wr = page.getWebResponse();
            if(wr != null){
                String json = wr.getContentAsString();
                ReviewsResponse map = new Gson().fromJson(json, ReviewsResponse.class);
                // new TypeToken<ReviewsResponse>() {}.getType()
                f.reviews = map.reviews.get(f.id).toArray(new ReviewEntry[map.reviews.size()]);
            }
        }
        catch(Exception ex){
            System.out.println("ERROR: FAILED fetching facility reviews: " + f.url);
            ex.printStackTrace();
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }

    static void processOneFacilityDetail(Facility f, WebClient wc){
        try{
            System.out.println("Fetching facility detail: " + f.url);
            HtmlPage hp = wc.getPage(new URL("https://www.selfstorage.com" + f.url));
            HtmlElement body = hp.getBody();

            HtmlElement aboutNode = body.querySelector(".facility-description");
            if(aboutNode != null)
                f.about = aboutNode.getTextContent();

            extractImageUrls(body, f);
            extractUnitDetails(body, f);
            extractFacilityReviews(f, wc);
            System.out.println("Finished fetching facility with details, now persisting ... " + f.id);

            Persistence.persist(wc, f);

            System.out.println("Finished persisting facility with details... " + f.id);
        }
        catch(Exception ex){
            System.out.println("ERROR: Failed fetching facility detail: " + f.url);
            ex.printStackTrace();
            System.out.println("Caught exception: " + ex.getMessage());
        }
    }

    public static void fetchDetailsAndPersist(String location, WebClient wc, ParsedPage pp){
        Facility[] facs = pp.facilities;
        for(int i = 0; i < facs.length; ++i){
            Facility f = facs[i];
            processOneFacilityDetail(f, wc);
        }
    }
}
