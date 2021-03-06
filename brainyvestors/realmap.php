<?php
session_start();
if(isset($_GET['action']) && ($_GET['action'] == "logout")){
	if(isset($_SESSION['lcdata']))
		unset($_SESSION['lcdata']);
		session_destroy();
}
  $lat_lng = array();
  $address = '';
  if(isset($_POST['address']) && strlen($_POST['address']) > 0){
    $address = $_POST['address'];
    $lat_lng = get_lat_lng($address);
  }
  else if(isset($_GET['auto_latlng']) && strlen($_GET['auto_latlng']) > 0){
    $lat_lng = json_decode($_GET['auto_latlng'], true);
  }

  function file_get_contents_curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  function get_lat_lng($loc){
    $url = "https://maps.googleapis.com/maps/api/geocode/json?key=&address=".urlencode(trim($loc))."&sensor=false";
    ////$url = "https://maps.googleapis.com/maps/api/geocode/json?key=&address=".urlencode(trim($loc))."&sensor=false";
	  $result_string = file_get_contents_curl($url);
	  //error_log($result_string);
    $result = json_decode($result_string, true);
    $lat = $result['results'][0]['geometry']['location']['lat'];
    $lng = $result['results'][0]['geometry']['location']['lng'];
    return array($lat, $lng);
  }

?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="icon" href="https://www.brainyvestors.com/images/llogo.jpg" type="image/jpg">
    <title>Real Estate Mapper</title>
    <meta charset="utf-8">
    <style>
      #map {
        height: 100%;
        width: 100%;
      }
      html, body {
        height: 100%;
        width: 100%;
        /*margin-right: 2%;
        padding: 2%;*/
      }
      body {
        font-family:'Century Gothic', CenturyGothic, 'Futura',sans-serif, Verdana;
      }
      /* The Modal (background) */
      .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
      }

      /* Modal Content/Box */
      .modal-content {
        background-color: #F5FCFF;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
        border-radius: 20px;
      }

      /* The Close Button */
      .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
      }

      .close:hover,
      .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
      }
    </style>
  </head>
  <body id="body_capture">
    
  <table style="height:10%;width:100%">
    <tr>
      <td style="background:#F5FCFF;border-radius: 10px;width:60%;">
        <form action="realmap.php" method="post">
          <table style="width:100%">
            <tr>
              <td style="font-size: .7em; width:90%;">Address <input type="text" id="address" name="address" value="<?php echo $address; ?>" style="width:90%;border-radius: 10px;" /></td>
              <td><input type="submit" value="Search" /></td>
            </tr>
          </table>
        </form>
      </td>
      <td style="background:#F5FCFF;border-radius: 10px;">
        <form style="font-size: .7em;">
          <input type="checkbox" id="onemile" onclick="javascript:cMile(1);" >1 Mile</input>
          <input type="checkbox" id="threemile" onclick="javascript:cMile(3);" >3 Mile</input>
          <input type="checkbox" id="fivemile" onclick="javascript:cMile(5);" >5 Mile</input>
          <!-- a onclick="javascript:clearCircles();" style="cursor:pointer" /><img src="images/eraser3.png" height="15px" width="15px" /></a -->
        </form>
      </td>
      <td style="background:#F5FCFF;border-radius: 10px;">
        <!--img src = "images/tools.png" height="20px" width="20px" / -->
          <a onclick="javascript:newPolygonF();" style="cursor:pointer"><img src="images/newpoly.png" height="40px" width="40px" /></a>
          <a onclick="javascript:clearAllPolygons();" style="cursor:pointer"><img src="images/eraser3.png" height="40px" width="40px" /></a>
      </td>
      <td style="background:#F5FCFF;border-radius: 10px;">
        <form>
          <a onclick="javascript:getDemography();" style="cursor:pointer" /><img src="images/di.png" height="40px" width="70px" /></a>
        </form>
        <div id="demographyDiv" class="modal" style="display:none;border-radius: 15px;">
          <div class="modal-content">
            <span class="close">&times;</span>
            <div style="text-align:center">
              Demography and Incomes of prominent localities
            </div>
            <table id="demography" style="border: 1px solid black; border-radius:10px; font-size: .9em;margin-bottom: 5px;margin-left: 75px;width:80%;box-shadow: 5px 5px 5px #888888;overflow-y:auto">
              <tr>
                <td>
                  <b>Locality</b>
                </td>
                <td>
                  <b>Population(2017)</b>
                </td>
                <td>
                  <b>Median Income(2017)</b>
                </td>
                <td>
                  <b>Income Trend</b>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </td>
      <td style="background:#F5FCFF;border-radius: 10px;">
        <?php
        if(isset($_SESSION['lcdata'])){
          echo 'Hello '.$_SESSION['lcdata']['firstname'].'    '.'<a href="realmap.php?action=logout">Logout</a><br />';
          echo '<button style="width:100%" type="button" onclick="take_screenshot()">Email this</button>';
        }
        else{
          echo '<a href="customer/index.php">Login</a>';
        }
        ?>
      </td>
    </tr>
  </table>

  <table style="width:100%;height:90%;table-layout: fixed";">
    <tr style="width:100%;height:100%">
      <td style="width:100%;height:100%">
        <div id="map"></div>
      </td>
      <!--td style="width:150px;height:100px;overflow-y:auto">
        <div id="demographyDiv">
          <div style="text-align:center">Demography and Incomes of prominent localities</div>
          <table id="demography" style="border: 1px solid black; border-radius:4px; font-size: .9em;margin-bottom: 5px;margin-left: 75px;width:80%;box-shadow: 5px 5px 5px #888888;overflow-y:auto">
            <tr>
              <td>
                <b>Locality</b>
              </td>
              <td>
                <b>Population(2017)</b>
              </td>
              <td>
                <b>Median Income(2017)</b>
              </td>
              <td>
                <b>Income Trend</b>
              </td>
            </tr>
          </table>
        </div>
        <div id="places"></div>
      </td-->
    </tr>
  </table>
    <script>
      var map;
      var placesService;
      var numPlaces = 0;
      var bounds;
      var circles = [null, null, null];
      var radius_miles = 10;

      var lastInfoWindow = null;

      var pencilPoly = false;
      var polygonPathsArr = [];
      var polygonMarkersArr = [];
      var currentPolygon = -1;
      var polygonArr = [];

      var placesArraySS = [];

      var auto_lat = -1;
      var auto_lng = -1;

      <?php
        if(count($lat_lng) > 0)
          echo 'var lat_lng_set = true;';
        else
          echo 'var lat_lng_set = false;';
      ?>
      
      window.onload = function(){
      	if (lat_lng_set === false && navigator.geolocation){
        	navigator.geolocation.getCurrentPosition(showPosition, showError);
        }
        else{
          <?php
          if(count($lat_lng) > 0){
            echo 'auto_lat = '.$lat_lng[0].';';
            echo 'auto_lng = '.$lat_lng[1].';';
          }
        ?>
        }
      };

      function showPosition(position){
        /*auto_lat = position.coords.latitude;
        auto_lng = position.coords.longitude;
        map.setCenter(new google.maps.LatLng(auto_lat, auto_lng));
        drawPlaces(auto_lat, auto_lng);
        fetchAADT();*/
        auto_lat = position.coords.latitude;
        auto_lng = position.coords.longitude;
        window.location.href='realmap.php?auto_latlng=' + '[' + auto_lat + ', ' + auto_lng + ']';
      }

      function showError(error){
	  
      }

      function clearAllPolygons(){
        for(var i = 0; i <= currentPolygon; i++){
          if(polygonArr[i] != null && polygonArr[i] != undefined){
            polygonArr[i].setVisible(false);
            polygonArr[i].setMap(null);
            polygonArr[i] = null;
          }
          if(polygonPathsArr[i] != null && polygonPathsArr[i] != undefined){
            polygonPathsArr[i] = null;
          }
          if(polygonMarkersArr[i] != null && polygonMarkersArr[i] != undefined){
            for(var m = 0; m < polygonMarkersArr[i].length; ++m){
              polygonMarkersArr[i][m].setMap(null);
            }
            polygonMarkersArr[i] = null;
          }
        }
        currentPolygon = -1;
        pencilPoly = false;
      }

      function clearCurrentPolygon(){
          if(currentPolygon < 0)
            return;
          if(polygonArr[currentPolygon] != null && polygonArr[currentPolygon] != undefined){
            polygonArr[currentPolygon].setVisible(false);
            polygonArr[currentPolygon].setMap(null);
            polygonArr[currentPolygon] = null;
          }
          if(polygonPathsArr[currentPolygon] != null && polygonPathsArr[currentPolygon] != undefined){
            polygonPathsArr[currentPolygon] = null;
          }
          currentPolygon--;
          pencilPoly = false;
      }

      function newPolygonF(){
        if(polygonArr[currentPolygon] != null && polygonArr[currentPolygon] != undefined){
          currentPolygon++;
        }
        polygonArr[currentPolygon] = null;
        polygonPathsArr[currentPolygon] = null;
        polygonMarkersArr[currentPolygon] = null;
        pencilPoly = true;
      }
    
      function rayCrossesSegment(point, a, b) {
        var px = point.lng(),
            py = point.lat(),
            ax = a.lng(),
            ay = a.lat(),
            bx = b.lng(),
            by = b.lat();
        if (ay > by) {
            ax = b.lng();
            ay = b.lat();
            bx = a.lng();
            by = a.lat();
        }
        // alter longitude to cater for 180 degree crossings
        if (px < 0) {
            px += 360;
        }
        if (ax < 0) {
            ax += 360;
        }
        if (bx < 0) {
            bx += 360;
        }

        if (py == ay || py == by) py += 0.00000001;
        if ((py > by || py < ay) || (px > Math.max(ax, bx))) return false;
        if (px < Math.min(ax, bx)) return true;

        var red = (ax != bx) ? ((by - ay) / (bx - ax)) : Infinity;
        var blue = (ax != px) ? ((py - ay) / (px - ax)) : Infinity;
        return (blue >= red);

      }

      function insidePolygon(point, path) {
        var crossings = 0;

        for (var i = 0; i < path.length; i++) {
          var a = path[i],
            j = i + 1;
          if (j >= path.length) {
              j = 0;
          }
          var b = path[j];
          if (rayCrossesSegment(point, a, b)) {
              crossings++;
          }
        }
  
        return (crossings % 2 == 1);
      };
      
      ////

      var overlay;

      function USGSOverlay(map) {
        this.map_ = map;
        this.div_ = null;
        this.setMap(map);
      }

      USGSOverlay.prototype.onAdd = function() {
        var div = document.createElement('div');
        div.style.position = 'absolute';
        div.innerHTML('<h4><b>Some text overlay... TODO</b></h4>');
        ////div.appendChild(img);

        this.div_ = div;

        // Add the element to the "overlayLayer" pane.
        var panes = this.getPanes();
        panes.overlayLayer.appendChild(div);
      };

      USGSOverlay.prototype.draw = function() {
        
        var overlayProjection = this.getProjection();

        var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
        var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());

        //this.onAdd();
        var div = this.div_;
        div.style.left = sw.x + 'px';
        div.style.top = ne.y + 'px';
        div.style.width = (ne.x - sw.x) + 'px';
        div.style.height = (sw.y - ne.y) + 'px';
        
      };

      USGSOverlay.prototype.onRemove = function() {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
      };
      
      USGSOverlay.prototype.remove = function() {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
      };

      USGSOverlay.prototype.getPosition = function(){
        return this.latlng;
      }

      USGSOverlay.prototype.getDraggable = function(){
        return false;
      }
      ////

      function getInfoFromrevGC(revGCRes, typeLevel){
        var acArr = revGCRes.address_components;
        for(i = 0; i < acArr.length; i++){
          var ac = acArr[i];
          if(ac.types.includes(typeLevel))
            return ac.long_name;
        }
        return '-';
      }

      function fetchDemographyData(stateStr){
        var stateIdMap = {
        'Alabama':1,
        'Alaska':2,
        //'American Samoa':4,
        'Arizona':4,
        'Arkansas':5,
        'California':6,
        'Colorado':8,
        'Connecticut':9,
        'Delaware':10,
        'District of Columbia':11,
        'Florida':12,
        'Georgia':13,
        //'Guam':14,
        'Hawaii':15,
        'Idaho':16,
        'Illinois':17,
        'Indiana':18,
        'Iowa':19,
        'Kansas':20,
        'Kentucky':21,
        'Louisiana':22,
        'Maine':23,
        'Maryland':24,
        'Massachusetts':25,
        'Michigan':26,
        'Minnesota':27,
        'Mississippi':28,
        'Missouri':29,
        'Montana':30,
        'Nebraska':31,
        'Nevada':32,
        'New Hampshire':33,
        'New Jersey':34,
        'New Mexico':35,
        'New York':36,
        'North Carolina':37,
        'North Dakota':38,
        //'Northern Mariana Islands':39,
        'Ohio':39,
        'Oklahoma':40,
        'Oregon':41,
        'Pennsylvania':42,
        //'Puerto Rico':43,
        'Rhode Island':44,
        'South Carolina':45,
        'South Dakota':46,
        'Tennessee':47,
        'Texas':48,
        //'U.S. Minor Outlying Islands':49,
        'Utah':49,
        //'Vermont':50,
        //'Virgin Islands (U.S.)':52,
        'Virginia':51,
        //'Washington':54,
        //'West Virginia':55,
        'Wisconsin':55,
        //'Wyoming':57
        };
        var state = stateIdMap[stateStr];
        if(state < 10)
          state = '0' + state;
        var query2016 = 'https://api.census.gov/data/2016/acs/acs5?get=B01003_001E,B06011_001E,NAME&for=place:*&in=state:' + state + '&key=7f6d9735efdc792ffebf89bc316f4afe3b29795f';
        var result_string2016 = js_http(query2016);
        var result2016 = [[]];
        if(result_string2016.length > 0)
          result2016 = JSON.parse(result_string2016);

        var query2017 = 'https://api.census.gov/data/2017/acs/acs5?get=B01003_001E,B06011_001E,NAME&for=place:*&in=state:' + state + '&key=7f6d9735efdc792ffebf89bc316f4afe3b29795f';
        var result_string2017 = js_http(query2017);
        var result2017 = [[]];
        if(result_string2017.length > 0)
          result2017 = JSON.parse(result_string2017);
        
        return [result2016, result2017];
      }
      
      function sanitizeCity(city){
        
      }
      
      var span = document.getElementsByClassName("close")[0];
      span.onclick = function() {
        var modal = document.getElementById("demographyDiv");
        modal.style.display = "none";
      }

      window.onclick = function(event) {
        var modal = document.getElementById("demographyDiv");
        if (event.target == modal) {
          modal.style.display = "none";
        }
      }

      function showDemography(demArray2){
        //// **** ////
        var modal = document.getElementById("demographyDiv");
        modal.style.display = "block";
        
        var numPlaces = demArray2[0].length;
        var placesList = document.getElementById('demography');
        while (placesList.childElementCount > 1) {
          placesList.removeChild(placesList.lastChild);
        }
        for(i = 1; i < numPlaces; i++){
          if(demArray2[1][i][1] <= 0)
            continue;
          var tr = document.createElement('tr');
          
          var td0 = document.createElement('td');
          td0.textContent = demArray2[1][i][2];
          
          var td1 = document.createElement('td');
          td1.textContent = demArray2[1][i][0];
          
          var td2 = document.createElement('td');
          td2.textContent = '$ ' + demArray2[1][i][1];
          
          var td3 = document.createElement('td');
          if(demArray2[1][i][1] >= demArray2[0][i][1])
            td3.innerHTML = '<img src="images/aadt_up.png" style="display:inline;width:10px;height:10px;" alt="increased : 2016 income: ' + demArray2[0][i][1] + '" />';
          else
            td3.innerHTML = '<img src="images/aadt_down.png" style="display:inline;width:10px;height:10px;" alt="reduced : 2016 income: ' + demArray2[0][i][1] + '" />';
          
          tr.appendChild(td0);
          tr.appendChild(td1);
          tr.appendChild(td2);
          tr.appendChild(td3);
          
          placesList.appendChild(tr);
        }
      }

      function getDemography(){
        // get Map center or circle center
        var center = map.getCenter();
        /*if(circle1 != null && circle1 != undefined)
          center = circle1.getCenter();
        if(circle3 != null && circle3 != undefined)
          center = circle3.getCenter();
        if(circle5 != null && circle5 != undefined)
          center = circle5.getCenter();*/
        // get reverse geocoding - using latlng of center and get location address components
        var revGC = new google.maps.Geocoder();
        var latlng = {lat: center.lat(),
                      lng: center.lng()
                     };
        revGC.geocode({ location: latlng}, function(results, status) {
            if (status === 'OK') {
              if (results[0]) {
                // get State and then state number from mapping
                var stateStr = getInfoFromrevGC(results[0], 'administrative_area_level_1');
                // get city name
                var city = getInfoFromrevGC(results[0], 'locality');
                // get 2016 & 2017 record of demography for all places in State
                var dem2Arr = fetchDemographyData(stateStr);
                //// get record of this city if it is in demography record
                
                // print 4 numbers and trend on right panel space
                showDemography(dem2Arr);
              } else {
                window.alert('No results found');
              }
            } else {
              window.alert('Reverse Geocoder failed due to: ' + status);
            }
        });
      }
      
      function rad_idx(r){
        if(r == 1)
          return 0;
        if(r == 3)
          return 1;
        return 2;
      }
      
      function cMile(r_m){
        var idx = rad_idx(r_m);
        var lrs = ['onemile', 'threemile', 'fivemile'];
        var show = document.getElementById(lrs[idx]).checked;
        if(show == true){
          cMileI(r_m)
        }
        else{
          circles[idx].setVisible(false);
          circles[idx] = null;
        }
      }
      
      function cMileI(r_m){
        radius_miles = r_m;
        var circle = circles[rad_idx(r_m)];
        if(circle != undefined && circle != null){
          circle.setRadius(radius_miles * 1.6 * 1000);
          circle.setVisible(true);
        }
        else{
          circles[rad_idx(r_m)] = new google.maps.Circle({
            center: map.getCenter(),
            editable: true,
            fillColor: 'grey',
            fillOpacity: 0.2,
            map: map,
            radius: radius_miles * 1.6 * 1000
          });
          var circle = circles[rad_idx(r_m)];
          var circleRCEvent = new google.maps.event.addListener(circle, 'radius_changed', function() {
            var mapCenter = circle.getCenter();
            drawPlaces(mapCenter.lat(), mapCenter.lng());
            ////fetchAADT();
            refreshAADTS();
          });
          var circleCCEvent = new google.maps.event.addListener(circle, 'center_changed', function() {
            var mapCenter = circle.getCenter();
            drawPlaces(mapCenter.lat(), mapCenter.lng());
            ////fetchAADT();
            refreshAADTS();
          });
        }
        
        var mapCenter = circle.getCenter();
        
        drawPlaces(mapCenter.lat(), mapCenter.lng());
        map.setCenter(circle.getCenter());
        ////fetchAADT();
        refreshAADTS();
      }

      function clearCircles(){
        for(var i = 0; i < 3; i++){
          if(circles[i] != null)
            circles[i].setVisible(false);
        }
        document.getElementById('onemile').checked = false;
        document.getElementById('threemile').checked = false;
        document.getElementById('fivemile').checked = false;
        circles = [null, null, null];
      }

      function addInfoWindow(marker, contentString){
        var infowindow = new google.maps.InfoWindow({
              content: contentString
            });
        //infowindow.setContent(contentString);
        infowindow.setContent(marker.aadt);
        infowindow.open(map, marker);
      }

      function createAADTMarkers(places, polyCheck){
        for (var i = 0, place; place = places[i]; i++) {
          if(polyCheck === true && !insidePolygon(new google.maps.LatLng(place.lat, place.lng), polygonPathsArr[currentPolygon]))
            continue;
          var iconAADT = '/images/aadt_up.png';
          if(place.aadt_2017 < place.aadt_2016){
            //iconAADT = '/images/aadt_down.png';
            iconAADT = '/images/aadtdown.gif';
          }
          var image = {
                        url: iconAADT,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(20, 20)
          };
          var marker = new google.maps.Marker({
                                  map: map,
                                  icon: image,
                                  title: 'Traffic (2017): ' + place.aadt_2017,
                                  position: {lat: place.lat, lng: place.lng}
                      });
          marker.setAnimation(google.maps.Animation.DROP);
          //marker.setLabel('2017: ' + place.aadt_2017 + ', 2016: ' + place.aadt_2016 + '}');
          marker['aadt'] = '<b>Traffic Average(2017): </b>' + place.aadt_2017 + ' vehicles/day<br />' +
                                '<b>Traffic Average(2016): </b>' + place.aadt_2016 + ' vehicles/day<br />';
          var aadtFlagClickEvent = new google.maps.event.addListener(marker, 'click', function() {
            /*var contentString = '<b>Traffic Average(2017): </b>' + place.aadt_2017 + '<br />' +
                                '<b>Traffic Average(2016): </b>' + place.aadt_2016 + '<br />' +
                                '<b>(lat,lng): </b>' + '(' + place.lat + ', ' + place.lng + ')' + '<br />';*/
            var contentString = '<b>(lat,lng): </b>' + '(' + this.position.lat() + ', ' + this.position.lng() + ')' + '<br />';
            /*var infowindow = new google.maps.InfoWindow({
              content: contentString
            });
            infowindow.open(map, this);*/
            addInfoWindow(this, contentString);
          });
        }
      }

      function js_http(url){
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.open( "GET", url, false );
        xmlHttp.send( null );
        return xmlHttp.responseText;
      }

      function js_http_a0(url, polyCheck){
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onload = function() {
          if (this.readyState == 4 && this.status == 200) {
            var result = JSON.parse(this.responseText);

            var trafficPlaces = [];
            var features = result.features;
            for(i = 0; (features != null && features != undefined) && i < features.length; i++){
              trafficPlaces.push({ lat: features[i].geometry.y, 
                               lng: features[i].geometry.x, 
                               aadt_2016: features[i].attributes.AADT_2016,
                               aadt_2017: features[i].attributes.AADT_2017
                             });
            }
            createAADTMarkers(trafficPlaces, polyCheck);
          }
          else
            console.log('Not drawn: ' + this.readyState + " : " + this.status);
        };
        xmlHttp.open( "GET", url, true );
        xmlHttp.send( null );
      }

      function fetchAADT(){
        var c = map.getCenter();
        var circleDrawn = false;
        for(var i = 0; i < 3; ++i){
          var circle = circles[i];
          if(circle != null && circle != undefined){
            c = circle.getCenter();
            fetchAADTI(c, false);
            circleDrawn = true;
          }
        }
        if(circleDrawn == false)
          fetchAADTI(c, false);
      }

      function fetchAADTI(c, polyCheck){
        var lat = c.lat();
        var lng = c.lng();
        var nwlat = lat - 0.01;
        var nwlng = lng + 0.01;
        var selat = lat + 0.01;
        var selng = lng - 0.01;

        var query = 'https://services.arcgis.com/KTcxiTD9dsQw4r7Z/arcgis/rest/services/TxDOT_AADT_Annuals/FeatureServer/0/query?where=1%3D1&outFields=OBJECTID,DIST_NM,CNTY_NM,T_FLAG,AADT_2017,AADT_2016,ZLEVEL,GlobalID&geometry=' + nwlng + '%2C' + nwlat + '%2C' + selng + '%2C3' + selat + '&geometryType=esriGeometryEnvelope&inSR=4326&spatialRel=esriSpatialRelIntersects&returnDistinctValues=true&outSR=4326&f=json';

        /*
        var result_string = js_http(query);
        var result = JSON.parse(result_string);

        var trafficPlaces = [];
        var features = result.features;
        for(i = 0; i < features.length; i++){
          trafficPlaces.push({ lat: features[i].geometry.y, 
                               lng: features[i].geometry.x, 
                               aadt_2016: features[i].attributes.AADT_2016,
                               aadt_2017: features[i].attributes.AADT_2017
                             });
        }
        createAADTMarkers(trafficPlaces);
        */
      }

      function createMarkers(places, icon){
        //var placesList = document.getElementById('places');

        for (var i = 0, place; place = places[i]; i++) {
          ////console.log('place # ' + i + ' : ' + place.name + ' : ' + JSON.stringify(place));
          if(place.name.includes('Walmart') && !place.name.includes('Walmart Supercenter'))
            continue;
          if(!place.name.includes('Walmart') && icon.includes('wm_l'))
            continue;
          if(place.name.includes('Kroger') && place.name != 'Kroger')
            continue;
          if(place.name.includes('Walgreens') && place.name != 'Walgreens')
            continue;
          if(place.name.includes('Target') && place.name != 'Target')
            continue;
          var image = {
                        url: icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
          };

          var marker = new google.maps.Marker({
                                  map: map,
                                  icon: image,
                                  title: place.name,
                                  position: place.geometry.location
                      });
          marker.setAnimation(google.maps.Animation.DROP);
          placesArraySS.push({ic:icon, lat: place.geometry.location.lat(), lng: place.geometry.location.lng()});
          /*
          var marker = new google.maps.Marker({
                                  map: map,
                                  place: {
                                    placeId: place.place_id,
                                    location: place.geometry.location
                                  }
                      });*/
          
          //var li = document.createElement('li');
          //li.textContent = place.name;
          //placesList.appendChild(li);

          bounds.extend(place.geometry.location);
          numPlaces++;
        }
        ////map.fitBounds(bounds);
        //map.setCenter(bounds.getCenter());
        if(numPlaces > 1){
          map.setZoom(14);
        }
      }

      function getPlaces(place, icon, lt, lg){
        var r_m = radius_miles * 1.6 * 1000;
        var lat = lt;
        var lng = lg;
        for(var i = 0; i < 3; i++){
          var circle = circles[i];
          if(circle != null && circle != undefined){
            r_m = circle.getRadius();
            lat = circle.getCenter().lat();
            lng = circle.getCenter().lng();
            getPlacesI(place, icon, lt, lg, r_m, lat, lng);
          }
        }
      }

      function getPlacesI(place, icon, lt, lg, r_m, lat, lng){
        var r_m = radius_miles * 1.6 * 1000;
        var lat = lt;
        var lng = lg;

        var request = {
          query: place,
          fields: ['name', 'geometry'],
          locationBias: {
            center: {
              lat: lat,
              lng: lng
            },
            radius: r_m
          }
        };
        var placesService = new google.maps.places.PlacesService(map);
        placesService.findPlaceFromQuery(request, function(results, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            console.log('num results for ' + place + ': ' + results.length);
            createMarkers(results, icon);
          }
          else console.log('failed places status for ' + place + ': ' + status);
        });
      }

      function getTextPlaces(place, icon, lat, lng){
        var r_m = radius_miles * 1.6 * 1000;
        var hasCircle = false;
        for(var i = 0; i < 3; i++){
          var circle = circles[i];
          if(circle != null && circle != undefined){
            r_m = circle.getRadius();
            getTextPlacesI(place, icon, lat, lng, r_m);
            hasCircle = true;
          }
        }
        if(hasCircle == false){
          getTextPlacesI(place, icon, lat, lng, r_m);
        }
      }

      function getTextPlacesI(place, icon, lat, lng, r_m){
        var request = {
          query: place,
          fields: ['name', 'geometry'],
          location: new google.maps.LatLng(lat, lng),
          radius: r_m
        };
        var placesService = new google.maps.places.PlacesService(map);
        placesService.textSearch(request, function(results, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            //console.log('num results for ' + place + ': ' + results.length);
            createMarkers(results, icon);
            //for (var i = 0; i < results.length; i++) {
              //createMarker(results[i]);
            //}
            //map.setCenter(results[0].geometry.location);
          }
          else console.log('failed TEXT places status for ' + place + ': ' + status);
        });
      }

      function drawPlaces(lat, lng){
        numPlaces = 0;
        placesArraySS = [];
        getTextPlaces('Walmart', '/images/wm_l.jpg', lat, lng);
        getTextPlaces('Walgreens', '/images/wg_l.png', lat, lng);
        getTextPlaces('CVS', '/images/cvs_l.png', lat, lng);
        getTextPlaces('McDonalds', '/images/md_l.png', lat, lng);
        
        getTextPlaces('Kroger', '/images/kroger_l.png', lat, lng);
        getTextPlaces('Target', '/images/target_l.png', lat, lng);
        getTextPlaces('Olive Garden', '/images/og_l.png', lat, lng);
      }

      ////
      function createMarkersPolygon(places, icon){
        for (var i = 0, place; place = places[i]; i++) {
          if(place.name.includes('Walmart') && !place.name.includes('Walmart Supercenter'))
            continue;
          if(!place.name.includes('Walmart') && icon.includes('wm_l'))
            continue;
          if(place.name.includes('Kroger') && place.name != 'Kroger')
            continue;
          if(place.name.includes('Walgreens') && place.name != 'Walgreens')
            continue;
          if(place.name.includes('Target') && place.name != 'Target')
            continue;
          if(!insidePolygon(place.geometry.location, polygonPathsArr[currentPolygon]))
            continue;
          var image = {
                        url: icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
          };

          var marker = new google.maps.Marker({
                                  map: map,
                                  icon: image,
                                  title: place.name,
                                  position: place.geometry.location
                      });
          marker.setAnimation(google.maps.Animation.DROP);
          bounds.extend(place.geometry.location);
        }
      }

      function getTextPlacesPolygon(place, icon, lat, lng){
        var r_m = 10 * 1.6 * 1000;
        getTextPlacesIPolygon(place, icon, lat, lng, r_m);
      }

      function getTextPlacesIPolygon(place, icon, lat, lng, r_m){
        var request = {
          query: place,
          fields: ['name', 'geometry'],
          location: new google.maps.LatLng(lat, lng),
          radius: r_m
        };
        var placesService = new google.maps.places.PlacesService(map);
        placesService.textSearch(request, function(results, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            createMarkersPolygon(results, icon);
          }
          else console.log('failed Polygon: TEXT places status for ' + place + ': ' + status);
        });
      }

      function drawPlacesPolygon(lat, lng){
        placesArraySS = [];
        getTextPlacesPolygon('Walmart', '/images/wm_l.jpg', lat, lng);
        getTextPlacesPolygon('Walgreens', '/images/wg_l.png', lat, lng);
        getTextPlacesPolygon('CVS', '/images/cvs_l.png', lat, lng);
        getTextPlacesPolygon('McDonalds', '/images/md_l.png', lat, lng);
        
        getTextPlacesPolygon('Kroger', '/images/kroger_l.png', lat, lng);
        getTextPlacesPolygon('Target', '/images/target_l.png', lat, lng);
        getTextPlacesPolygon('Olive Garden', '/images/og_l.png', lat, lng);
      }
      ////

      function smallDiffLocation(i, j){
        var smallDiff = 0.001;
        if(i >= j){
          if(i - j <= smallDiff)
            return true;
        }
        else if(j - i <= smallDiff)
          return true;
        return false;
      }

      function polygonMarker(latLng){
        if(currentPolygon < 0)
              currentPolygon = 0;
        if(polygonPathsArr[currentPolygon] == null || polygonPathsArr[currentPolygon] == undefined){
          polygonPathsArr[currentPolygon] = [];
          polygonMarkersArr[currentPolygon] = [];
          polygonArr[currentPolygon] = null;
        }

        var image = {
                        url: '/images/vertex.png',
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(10, 25),
                        scaledSize: new google.maps.Size(25, 25)
        };
        var marker = new google.maps.Marker({
                                  map: map,
                                  icon: image,
                                  position: latLng
        });
          ////
          if(polygonArr[currentPolygon] != null && polygonArr[currentPolygon] != undefined){
            /*polygonArr[currentPolygon].setVisible(false);
            polygonArr[currentPolygon].setMap(null);
            polygonArr[currentPolygon] = null;
            */
            polygonArr[currentPolygon].setPath(polygonPathsArr[currentPolygon]);
          }
          else{
            polygonArr[currentPolygon] = new google.maps.Polyline({
              path: polygonPathsArr[currentPolygon],
              strokeColor: '#FF0000',
              strokeOpacity: 0.4,
              strokeWeight: 2,
              fillColor: '#FF0000',
              fillOpacity: 0.1,
              clickable: false
            });
            polygonArr[currentPolygon].setMap(map);
          }
            
                  /*polygonArr[currentPolygon] = new google.maps.Polygon({
                    paths: polygonPathsArr[currentPolygon],
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.4,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.1
                  });*/
          ////
          if(polygonMarkersArr[currentPolygon].length > 0)
            marker.setClickable(false);
          else{
             var markerClickEvent = google.maps.event.addListener(marker, 'click', function(event) {
                if(polygonPathsArr[currentPolygon].length >= 3){
                  pencilPoly = false;
                  var lastPath = polygonPathsArr[currentPolygon];
                  lastPath.push(latLng);
                  polygonArr[currentPolygon].setPath(lastPath);
                  var polyLat = polygonPathsArr[currentPolygon][0].lat();
                  var polyLng = polygonPathsArr[currentPolygon][0].lng();
                  for(i = 1; i < polygonPathsArr[currentPolygon].length; i++){
                    polyLat += polygonPathsArr[currentPolygon][i].lat();
                    polyLng += polygonPathsArr[currentPolygon][i].lng();
                  }
                  polyLat /= polygonPathsArr[currentPolygon].length;
                  polyLng /= polygonPathsArr[currentPolygon].length;
              
                  drawPlacesPolygon(polyLat, polyLng);
                  ////fetchAADTI(new google.maps.LatLng(polyLat, polyLng), true);
                  refreshAADTS();
                }
             });          
          }
          polygonMarkersArr[currentPolygon].push(marker);
      }

      function drawOnMap(lat, lng){
        placesArraySS = [];
        drawPlaces(lat, lng);
        ////fetchAADT();
        ////getDemography();
        
        var mapClickEvent = google.maps.event.addListener(map, 'click', function(me) {
          if(lastInfoWindow !== null && lastInfoWindow !== undefined)
            lastInfoWindow.close();
            
          if(pencilPoly === true){
            if(currentPolygon < 0)
              currentPolygon = 0;
            if(polygonPathsArr[currentPolygon] == null || polygonPathsArr[currentPolygon] == undefined){
              polygonPathsArr[currentPolygon] = [];
              polygonMarkersArr[currentPolygon] = [];
              polygonArr[currentPolygon] = null;
            }
            //if(polygonArr[currentPolygon] != null && polygonArr[currentPolygon] != undefined)
              //return;
            polygonPathsArr[currentPolygon].push(me.latLng);
            polygonMarker(me.latLng);
          }
        });
        
        var marker = new google.maps.Marker({
                                  map: map,
                                  position: new google.maps.LatLng(lat, lng)
        });
      }

      function fixAADTColumns(firstLine){
        var fieldNames = new Array('Location ID','Date','Source','Count','Roadway','Direction','From','To','City','County');
        var columnIdxs = {};
        var fields = firstLine.split(',');
        for(var x = 0; x < fields.length; ++x){
          for(var y = 0; y < fieldNames.length; ++y){
            if(fields[x] == fieldNames[y]){
              columnIdxs[fieldNames[y]] = x;
              break;
            }
          }
        }
        return columnIdxs;
      }

      function parseAADTLine(columnIdxs, line){
        var idx = 0;
        var z = 0;
        var fieldNames = new Array('Location ID','Date','Source','Count','Roadway','Direction','From','To','City','County');
        var fields = new Array('Location ID','Date','Source','Count','Roadway','Direction','From','To','City','County');
        while(idx < line.length){
          var idx_s = line.indexOf('"', idx);
          if(idx_s == -1)
            return fields;
          var idx_e = line.indexOf('"', idx_s + 1);
          if(idx_e == -1)
            return fields;
          fields[columnIdxs[fieldNames[z]]] = line.substring(idx_s + 1, idx_e);
          ++z;
          idx = idx_e + 1;
        }
        return fields;
      }

      function js_http_aadt(url){
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onload = function() {
          if (this.readyState == 4 && this.status == 200) {
            //var result = JSON.parse(this.responseText);
            var lines = this.responseText.split('\n');
            
            var trafficPlaces = [];
            var columnIdxs = fixAADTColumns(lines[0]);
            for(var line = 1; line < lines.length; ++line){
            ////for(var line = 1; line < 2; ++line){
              //var fields = lines[line].replace(/"/g, '').split(',');
              if(lines[line].length < 1)
                continue;
              var fields = parseAADTLine(columnIdxs, lines[line]);
              trafficPlaces.push({location: fields[0],
                                  date: fields[1],
                                  count: fields[3],
                                  roadway: fields[4],
                                  direction: fields[5],
                                  from: fields[6],
                                  to: fields[7],
                                  city: fields[8],
                                  county: fields[9]
                                });
            }
            refreshAADTMarkers(trafficPlaces, 0);
          }
          else
            console.log('Not drawn new AADT source data : ' + this.readyState + " : " + this.status);
        };
        xmlHttp.open( "GET", url, true );
        ////xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttp.setRequestHeader('Accept', '*/*');
        xmlHttp.send( null );
      }

      function parseForLatLng(responseText){
        var lines = responseText.split('\n');
        var latlng = {};
        for(var i = 0; i < lines.length; ++i){
          if(lines[i].includes('Longitude')){
            var scIndx = lines[i].indexOf(";");
            var tdEnd = lines[i].indexOf("</td>");
            var lng = lines[i].substring(scIndx + 1, tdEnd - 2);
            latlng['lng'] = parseFloat(lng.trim());
          }
          if(lines[i].includes('Latitude')){
            var scIndx = lines[i].indexOf(";");
            var tdEnd = lines[i].indexOf("</td>");
            var lng = lines[i].substring(scIndx + 1, tdEnd - 2);
            latlng['lat'] = parseFloat(lng.trim());
          }
        }
        return latlng;
      }

      function addInfoWindowAADT(marker, contentString){
        var infowindow = new google.maps.InfoWindow({
              content: contentString
            });
        if(lastInfoWindow !== null && lastInfoWindow !== undefined){
          lastInfoWindow.close();
        }
        //infowindow.setContent(contentString);
        infowindow.setContent(marker.aadt);
        infowindow.open(map, marker);
        lastInfoWindow = infowindow;
      }
      
      function js_http_aadt_inner(place, url){
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onload = function() {
          if (this.readyState == 4 && this.status == 200) {
            {
              var latlng = parseForLatLng(this.responseText);
              var image = {
                        url: '/images/addr_icon.png',
                        size: new google.maps.Size(120, 120),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
              };
              var marker = new google.maps.Marker({
                                  map: map,
                                  icon: image,
                                  title: 'Traffic (' +  place.date + '): ' + place.count,
                                  position: {lat: latlng.lat, lng: latlng.lng}
                      });
              marker.setAnimation(google.maps.Animation.DROP);

              marker['aadt'] = '<b>Traffic Average: </b>' + place.count + ' vehicles/day<br />'+
                                '<b>Roadway: </b>' + place.roadway + ', <b>Direction: </b>' + place.direction + '<br />' +
                                '<b>From: </b>' + place.from + ', <b>To: </b>' + place.to + '<br />' +
                                '<b>City: </b>' + place.city + ', <b>County: </b>' + place.county + '<br />' +
                                '<a target="_new" href = "https://www.brainyvestors.com/aadt_hist.php?loc=' + place.location +'">Trend Graph</a>';
              var aadtFlagClickEvent = new google.maps.event.addListener(marker, 'click', function() {

                var contentString = '<b>(lat,lng): </b>' + '(' + latlng.lat + ', ' + latlng.lng + ')' + '<br />';
                addInfoWindowAADT(this, contentString);
              });
            }
          }
          else
            console.log('Not drawn new AADT source data : ' + this.readyState + " : " + this.status);
        };
        xmlHttp.open( "GET", url, true );
        ////xmlHttp.setRequestHeader('Accept', '*/*');
        xmlHttp.send( null );
      }

      function refreshAADTMarkers(places, startIdx){
        var z = 0;
        var p = startIdx;
        for(; (p < places.length && z < 10); ++p){
          ++z;
          js_http_aadt_inner(places[p], 'aadtproxy.php?aadtproxy=' + encodeURI('LocsData.asp?id_loc=' + places[p].location) );
          ////js_http_aadt_inner(places[p], 'aadtproxy.php?aadtproxy=' + encodeURI('https://resources.nctcog.org/trans/data/trafficcounts/LocsData.asp?id_loc=' + places[p].location) );
        }
        if(p < places.length)
          setTimeout(refreshAADTMarkers, 1000, places, p);
      }
/*
//// https://resources.nctcog.org/trans/data/trafficcounts/LocsData.asp?id_loc=50875
     https://resources.nctcog.org/trans/data/trafficcounts/createexcel.asp

<head>
<link rel="stylesheet" type="text/css" href="TrafficGraphs.css" />
<link href='https://resources.nctcog.org/style-sheets/hide.css' rel='stylesheet' type='text/css' /></head>

	
	
<table>
             
            <tr class="style5">
                <td><b>Location ID:</b>&nbsp;50875&nbsp;</td>
				<td>&nbsp;</td>
				<td><b>Longitude:</b>&nbsp;-97.030562 </td>
				<td>&nbsp;</td>
                <td><b>Latitude:</b>&nbsp;32.837007 </td>
                
            </tr>
            <tr class="style5">
                <td><b>Roadway:</b>&nbsp;SH183 WB</td>
				<td>&nbsp;</td>
				<td><b>Between:</b>&nbsp;SH183 ONRAMP WB </td>
				<td>&nbsp;</td>
                <td><b>And:</b>&nbsp;SH183 ONRAMP WB </td>
				<td>&nbsp;</td>
				<td><b>City:</b>&nbsp;Irving </td>
				<td>&nbsp;</td>
                <td><b>County:</b>&nbsp;Dallas </td>
            </tr>

            
</table>

https://resources.nctcog.org/trans/data/trafficcounts/HistoricTrafficCounts_Report2.asp?id_loc=50875&unhide=1

https://www.nctcog.org/trans/data/info/traffic-count-information-systems/traffic-count-abbreviations

*/


      function refreshAADTS(){
        bounds = map.getBounds();
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();
        var left = sw.lng();
        var right = ne.lng();
        var top = ne.lat();
        var bottom = sw.lat();
        // https://resources.nctcog.org/trans/data/trafficcounts/createexcel.asp?left=-96.49558378085032&right=-96.44576376005546&top=32.94681392860088&bottom=32.92376227328064
        ////var url = "aadtproxy.php?aadtproxy=" + encodeURI("https://resources.nctcog.org/trans/data/trafficcounts/createexcel.asp?left=" + left + "&right=" + right + "&top=" + top + "&bottom=" + bottom);
        var url = "aadtproxy.php?aadtproxy=" + encodeURI("createexcel.asp?left=" + left + ";right=" + right + ";top=" + top + ";bottom=" + bottom);
        js_http_aadt(url );
      }

      function initMap(){
        <?php
          if(count($lat_lng) > 0){
            echo 'var lat = '.$lat_lng[0].';';
            echo 'var lng = '.$lat_lng[1].';';
          }
          else{
            echo 'var lat = 32.9312;';
            echo 'var lng = -96.4597;';
          }
        ?>
        map = new google.maps.Map(document.getElementById('map'), {
          center: {
            lat: lat, 
            lng: lng
          },
          zoom: 16
        });
        bounds = new google.maps.LatLngBounds();
        //bounds.extend(map.getCenter());
        USGSOverlay.prototype = new google.maps.OverlayView();
        overlay = new USGSOverlay(map);

        placesArraySS = [];
        drawOnMap(lat, lng);
        
        /*google.maps.event.addListener(map, 'center_changed', function() {
          refreshAADTS();
        });
        
        google.maps.event.addListener(map, 'bounds_changed', function() {
          refreshAADTS();
        });
        google.maps.event.addListener(map, 'zoom_changed', function() {
          refreshAADTS();
        });
        */
        setTimeout(refreshAADTS, 2000);
        
      }

      function js_http_async_sc(url, data){
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onload = function() {
          if (this.readyState == 4 && this.status == 200) {
            //alert('Captured Map location and email sent.');
          }
          else
            console.log('Not captured/emailed due to: ' + this.readyState + " : " + this.status);
        };
        xmlHttp.open( "POST", url, true );
        xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttp.send( data );
      }

      function take_screenshot(){
        var staticMapUrl = 'https://maps.googleapis.com/maps/api/staticmap';
        var staticMapOpts = "?center=" + map.getCenter().lat() + "," + map.getCenter().lng();
        staticMapOpts += ";size=800x600;scale=2";
        staticMapOpts += ";maptype=" + map.getMapTypeId();
        staticMapOpts += ";zoom=" + map.getZoom();
        staticMapOpts += ";markers=" + map.getCenter().lat() + "," + map.getCenter().lng();
        for(var z = 0; z < placesArraySS.length; ++z){
          //staticMapOpts += ";markers=icon:https://www.brainyvestors.com" + placesArraySS[z].ic + "%7C" + placesArraySS[z].lat + "," + placesArraySS[z].lng;
        }
        js_http_async_sc("email_ss.php", "data=" + staticMapUrl + staticMapOpts);
        /*
        html2canvas(document.body, {
          onrendered: function(canvas){
            var img = canvas.toDataURL().replace("image/png", "image/octet-stream");  // here is the most important part because if you dont replace you will get a DOM 18 exception.;
            window.location.href=img;
            ////js_http_async_sc("email_ss.php", {data: img});
          }
       });
       */
       /*
       html2canvas(document.querySelector("#body_capture", {useCORS: true, allowTaint: true})).then(canvas => {
         var img = canvas.toDataURL();
         js_http_async_sc("email_ss.php", "data=" + img);
       });
       */
       
       /*
       var canvas1 = document.getElementById("body_capture");        
       if (canvas1.getContext) {
         var ctx = canvas1.getContext("2d");                
         var myImage = canvas1.toDataURL("image/png").replace("image/png", "image/octet-stream");  // here is the most important part because if you dont replace you will get a DOM 18 exception.
         window.location.href=image;
;
       }
       else
         console.log('No context canvas');
         
         http://talkerscode.com/webtricks/take-screenshot-of-a-webpage-using-html5-and-javascript.php
         http://html2canvas.hertzen.com/configuration
         https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/toDataURL
         https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/toDataURL
         https://stackoverflow.com/questions/10673122/how-to-save-canvas-as-an-image-with-canvas-todataurl
         https://turbofuture.com/computers/HTML5-Tutorial-How-To-Use-Canvas-toDataURL
         
         https://stackoverflow.com/questions/35997961/file-attachment-with-phpmailer
         https://stackoverflow.com/questions/3708153/send-email-with-phpmailer-embed-image-in-body
         https://www.php.net/manual/en/function.file-put-contents.php
         
         https://stackoverflow.com/questions/16235161/save-current-google-map-as-image
         AADT_Historic
       */
     }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap&libraries=places,drawing" async defer></script>
    <!-- script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap&libraries=places,drawing"
    async defer></script -->
  <!-- <script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap"
    async defer></script> -->
    <!-- script src="https://maps.googleapis.com/maps/api/js?key=&libraries=places"
        async defer></script 
        -->
    <script type="text/javascript" src="js/html2canvas.js"></script>

  </body>
</html>

