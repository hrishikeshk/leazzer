package leazzerScraperMain;

import com.gargoylesoftware.htmlunit.WebClient;
import com.gargoylesoftware.htmlunit.html.DomNode;
import com.gargoylesoftware.htmlunit.html.DomNodeList;
import com.gargoylesoftware.htmlunit.html.HtmlPage;
import com.gargoylesoftware.htmlunit.html.HtmlElement;
import org.w3c.dom.NamedNodeMap;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

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

    public static void fetchDetailsAndPersist(String location, WebClient wc, ParsedPage pp){

    }
}
