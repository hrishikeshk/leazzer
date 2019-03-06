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
  <form action="realmap.php" method="post">
    <table>
    <tr>
      <td>Address: <input type="text" id="address" name="address" /> -- OR -- </td>
      <td>City: <input type="text" id="city" name="city" /> -- OR -- </td>
      <td>Zip: <input type="text" id="zip" name="zip" /></td>
      <td><input type="submit" value="View Location" /></td>
    </tr>
    </table>
  </form>
  <br />
  <div id="map"></div>
  <script>
      var map;
      var placesService;
      var infoWindow;
      function createMarkers(places){
        var bounds = new google.maps.LatLngBounds();
        var placesList = document.getElementById('places');

        for (var i = 0, place; place = places[i]; i++) {
          console.log('place # ' + i + ' : ' + place.name);
          var image = {
                        url: place.icon,
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

          var li = document.createElement('li');
          li.textContent = place.name;
          placesList.appendChild(li);

          bounds.extend(place.geometry.location);
        }
        map.fitBounds(bounds);
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
          zoom: 11
        });
        var request = {
          query: 'Walmart',
          fields: ['name', 'geometry'],
          locationBias: {
            lat: lat,
            lng: lng,
            radius: 5000
          }
        };
        var placesService = new google.maps.places.PlacesService(map);
        placesService.findPlaceFromQuery(request, function(results, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            console.log('num results: ' + results.length);
            createMarkers(results);
            //for (var i = 0; i < results.length; i++) {
              //createMarker(results[i]);
            //}
            map.setCenter(results[0].geometry.location);
          }
          else console.log('failed places status: ' + status);
        });
        
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

