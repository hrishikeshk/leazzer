<?php
  $lat_lng = array();
  $address = '';
  $city = '';
  $zip = '';
  if(isset($_POST['address']) && strlen($_POST['address']) > 0){
    $address = $_POST['address'];
    $lat_lng = get_lat_lng($address);
  }
  else if(isset($_POST['city']) && strlen($_POST['city']) > 0){
    $city = $_POST['city'];
    $lat_lng = get_lat_lng($city);
  }
  else if(isset($_POST['zip']) && strlen($_POST['zip']) > 0){
    $zip = $_POST['zip'];
    $lat_lng = get_lat_lng($zip);
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
    $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&address=".urlencode(trim($loc))."&sensor=false";
	  $result_string = file_get_contents_curl($url);
	  ////error_log($result_string);
    $result = json_decode($result_string, true);
    $lat = $result['results'][0]['geometry']['location']['lat'];
    $lng = $result['results'][0]['geometry']['location']['lng'];
    return array($lat, $lng);
  }

?>

<!DOCTYPE html>
<html>
  <head>
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
    </style>
  </head>
  <body>
  <table>
  <tr>
  <td>
  <form action="realmap.php" method="post">
    <table>
    <tr>
      <td>Address: <input type="text" id="address" name="address" value="<?php echo $address; ?>" /> -- OR -- </td>
      <td>City: <input type="text" id="city" name="city" value="<?php echo $city; ?>" /> -- OR -- </td>
      <td>Zip: <input type="text" id="zip" name="zip" value="<?php echo $zip; ?>" /></td>
      <td><input type="submit" value="View Location" /></td>
    </tr>
    </table>
  </form>
  <br/>
  <form>
    Draw circular region to drill down: 
    <input type="radio" id="onemile" onclick="javascript:cMile(1);" >1 Mile</input>
    <input type="radio" id="threemile" onclick="javascript:cMile(3);" >3 Mile</input>
    <input type="radio" id="fivemile" onclick="javascript:cMile(5);" >5 Mile</input>
    <input type="button" value="Clear Circle" onclick="javascript:clearCircle();" />
  </form>
  <br />
  </td>
  <td>
  <!-- Legend: <br />
  <img src="/realmap/images/wm_l.jpg" height="10px" width="10px" /> - Walmart <br />
  <img src="/realmap/images/wg_l.png" height="10px" width="10px" /> - Walgreens <br />
  <img src="/realmap/images/cvs_l.png" height="10px" width="10px" /> - CVS <br />
  <img src="/realmap/images/md_l.png" height="10px" width="10px" /> - McDonalds -->
  </td>
  </tr>
  </table>
  <table style="width:100%;height:100%">
    <tr style="width:100%;height:100%">
      <td style="width:60%;height:40%">
        <table style="width:100%;height:100%">
          <tr style="width:100%;height:100%">
            <td style="width:100%;height:100%">
              <div id="map"></div>
            </td>
          </tr>
        </table>
      </td>
      <td style="width:50%;height:50%">
        <div id="demographyDiv">
          <table id="demography" style="border: 1px solid black; border-radius:4px; font-size: .9em;margin-bottom: 5px;margin-left: 75px;width:80%;box-shadow: 5px 5px 5px #888888;">
            <tr>
              <td>
                <b>Locality</b>
              </td>
              <td>
                <b>Population(2017)</b>
              </td>
              <td>
                <b>Median income(2017)</b>
              </td>
            </tr>
          </table>
        </div>
        <div id="places"></div>
      </td>
    </tr>
  </table>
  <script>
      var map;
      var placesService;
      var numPlaces = 0;
      var bounds;
      var circle;
      var radius_miles = 10;
      var circleRCEvent;
      var circleCCEvent;

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
        var query2016 = 'https://api.census.gov/data/2016/acs/acs1?get=B01003_001E,B06011_001E,NAME&for=place:*&in=state:' + state + '&key=7f6d9735efdc792ffebf89bc316f4afe3b29795f';
        var result_string2016 = js_http(query2016);
	      //console.log("2016 :" + result_string2016 + ":");
        var result2016 = [[]];
        if(result_string2016.length > 0)
          result2016 = JSON.parse(result_string2016);

        var query2017 = 'https://api.census.gov/data/2017/acs/acs1?get=B01003_001E,B06011_001E,NAME&for=place:*&in=state:' + state + '&key=7f6d9735efdc792ffebf89bc316f4afe3b29795f';
        var result_string2017 = js_http(query2017);
	      //console.log("2017 :" + result_string2017 + ":");
        var result2017 = [[]];
        if(result_string2017.length > 0)
          result2017 = JSON.parse(result_string2017);
        
        return [result2016, result2017];
      }
      
      function sanitizeCity(city){
        
      }
      
      function showDemography(demArray2){
        var numPlaces = demArray2[0].length;
        var placesList = document.getElementById('demography');
        for(i = 1; i < numPlaces; i++){
          var tr = document.createElement('tr');
          
          var td0 = document.createElement('td');
          td0.textContent = demArray2[1][i][2];
          
          var td1 = document.createElement('td');
          td1.textContent = demArray2[1][i][0];
          
          var td2 = document.createElement('td');
          td2.textContent = '$ ' + demArray2[1][i][1];
          
          tr.appendChild(td0);
          tr.appendChild(td1);
          tr.appendChild(td2);
          
          placesList.appendChild(tr);
        }
      }

      function getDemography(){
        // get Map center or circle center
        var center = map.getCenter();
        if(circle != null && circle != undefined)
          center = circle.getCenter();
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
      
      function cMile(r_m){
        radius_miles = r_m;
        if(circle != undefined && circle != null){
          circle.setRadius(radius_miles * 1.6 * 1000);
          circle.setVisible(true);
        }
        else{
          circle = new google.maps.Circle({
            center: map.getCenter(),
            editable: true,
            fillColor: 'grey',
            fillOpacity: 0.2,
            map: map,
            radius: radius_miles * 1.6 * 1000
          });
          circleRCEvent = new google.maps.event.addListener(circle, 'radius_changed', function() {
            var mapCenter = circle.getCenter();
            drawPlaces(mapCenter.lat(), mapCenter.lng());
            fetchAADT();
          });
          circleCCEvent = new google.maps.event.addListener(circle, 'center_changed', function() {
            var mapCenter = circle.getCenter();
            drawPlaces(mapCenter.lat(), mapCenter.lng());
            fetchAADT();
          });
        }
        if(radius_miles != 1)
          document.getElementById('onemile').checked = false;
        if(radius_miles != 3)
          document.getElementById('threemile').checked = false;
        if(radius_miles != 5)
          document.getElementById('fivemile').checked = false;
        
        var mapCenter = circle.getCenter();
        
        drawPlaces(mapCenter.lat(), mapCenter.lng());
        map.setCenter(circle.getCenter());
        fetchAADT();
      }

      function clearCircle(){
        circle.setVisible(false);
        document.getElementById('onemile').checked = false;
        document.getElementById('threemile').checked = false;
        document.getElementById('fivemile').checked = false;
        circle = null;
      }

      function addInfoWindow(marker, contentString){
        var infowindow = new google.maps.InfoWindow({
              content: contentString
            });
        //infowindow.setContent(contentString);
        infowindow.setContent(marker.aadt);
        infowindow.open(map, marker);
      }

      function createAADTMarkers(places){
        for (var i = 0, place; place = places[i]; i++) {
           var iconAADT = '/realmap/images/aadt_up.png';
          if(place.aadt_2017 < place.aadt_2016)
            iconAADT = '/realmap/images/aadt_down.png';
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

      function fetchAADT(){
        var c = map.getCenter();
        if(circle != null && circle != undefined)
          c = circle.getCenter();
        var lat = c.lat();
        var lng = c.lng();
        var nwlat = lat - 0.2;
        var nwlng = lng + 0.2;
        var selat = lat + 0.2;
        var selng = lng - 0.2;

        var query = 'https://services.arcgis.com/KTcxiTD9dsQw4r7Z/arcgis/rest/services/TxDOT_AADT_Annuals/FeatureServer/0/query?where=1%3D1&outFields=OBJECTID,DIST_NM,CNTY_NM,T_FLAG,AADT_2017,AADT_2016,ZLEVEL,GlobalID&geometry=' + nwlng + '%2C' + nwlat + '%2C' + selng + '%2C3' + selat + '&geometryType=esriGeometryEnvelope&inSR=4326&spatialRel=esriSpatialRelIntersects&returnDistinctValues=true&outSR=4326&f=json';

        var result_string = js_http(query);
	      //console.log(result_string);
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
      }
      
      function createMarkers(places, icon){
        //var placesList = document.getElementById('places');

        for (var i = 0, place; place = places[i]; i++) {
          ////console.log('place # ' + i + ' : ' + place.name + ' : ' + JSON.stringify(place));
          if(place.name.includes('Walmart') && !place.name.includes('Walmart Supercenter'))
            continue;
          if(!place.name.includes('Walmart') && icon.includes('wm_l'))
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
        if(circle != null && circle != undefined){
          r_m = circle.getRadius();
          lat = circle.getCenter().lat();
          lng = circle.getCenter().lng();
        }
////console.log('Places data: ' + lat + ' : ' + lng + ' : ' + r_m + ' : ' + place);
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
            //for (var i = 0; i < results.length; i++) {
              //createMarker(results[i]);
            //}
            //map.setCenter(results[0].geometry.location);
          }
          else console.log('failed places status for ' + place + ': ' + status);
        });
      }

      function getTextPlaces(place, icon, lat, lng){
        var r_m = radius_miles * 1.6 * 1000;
        if(circle != null && circle != undefined)
          r_m = circle.getRadius();
//console.log('TEXT Places data: ' + lat + ' : ' + lng + ' : ' + r_m + ' : ' + place);
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
        /* getPlaces('Walmart', '/realmap/images/wm_l.jpg', lat, lng);
        getPlaces('Walgreens', '/realmap/images/wg_l.png', lat, lng);
        getPlaces('CVS', '/realmap/images/cvs_l.png', lat, lng);
        getPlaces('McDonalds', '/realmap/images/md_l.png', lat, lng);*/
        numPlaces = 0;
        getTextPlaces('Walmart', '/realmap/images/wm_l.jpg', lat, lng);
        getTextPlaces('Walgreens', '/realmap/images/wg_l.png', lat, lng);
        getTextPlaces('CVS', '/realmap/images/cvs_l.png', lat, lng);
        getTextPlaces('McDonalds', '/realmap/images/md_l.png', lat, lng);
      }

      function initMap(){
        <?php
          if(count($lat_lng) > 0){
            echo 'var lat = '.$lat_lng[0].';';
            echo 'var lng = '.$lat_lng[1].';';
          }
          else{
            echo 'var lat = 40.397;';
            echo 'var lng = -101.644;';
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
        drawPlaces(lat, lng);
        fetchAADT();
        getDemography();
      }

  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&callback=initMap&libraries=places,drawing"
    async defer></script>
  <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&callback=initMap"
    async defer></script> -->
    <!-- script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&libraries=places"
        async defer></script -->
  </body>
</html>

