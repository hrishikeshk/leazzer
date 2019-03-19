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
        height: 80%;
        width: 80%;
      }
      html, body {
        height: 100%;
        margin: 2%;
        padding: 0;
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
  <div id="map"></div>
  <div id="places"></div>
  <script>
      var map;
      var placesService;
      var infoWindow;
      var numPlaces = 0;
      var bounds;
      var circle;
      var radius_miles = 10;
      var circleRCEvent;
      var circleCCEvent;
      
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
          });
          circleCCEvent = new google.maps.event.addListener(circle, 'center_changed', function() {
            var mapCenter = circle.getCenter();
            drawPlaces(mapCenter.lat(), mapCenter.lng());
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
      }

      function clearCircle(){
        circle.setVisible(false);
        document.getElementById('onemile').checked = false;
        document.getElementById('threemile').checked = false;
        document.getElementById('fivemile').checked = false;
        circle = null;
      }

      function createMarkers(places, icon){
        var placesList = document.getElementById('places');

        for (var i = 0, place; place = places[i]; i++) {
          ////console.log('place # ' + i + ' : ' + place.name + ' : ' + JSON.stringify(place));
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
          
          var li = document.createElement('li');
          li.textContent = place.name;
          placesList.appendChild(li);

          bounds.extend(place.geometry.location);
          numPlaces++;
        }
        ////map.fitBounds(bounds);
        //map.setCenter(bounds.getCenter());
        if(numPlaces > 1){
          map.setZoom(12);
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
            console.log('num results for ' + place + ': ' + results.length);
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
          zoom: 14
        });
        bounds = new google.maps.LatLngBounds();
        drawPlaces(lat, lng);
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

