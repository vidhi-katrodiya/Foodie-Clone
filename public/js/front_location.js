
function initialise() {
    
  var mapOptions, map, marker, searchBox, city, state,area,
    infoWindow = '',
    addressEl = document.querySelector( '#address' ),
    latEl = document.querySelector( '#lat' ),
    latlongEl = document.querySelector( '#latlang' ),
    longEl = document.querySelector( '#lng' ),
    element = document.getElementById( 'map' );
    city = document.querySelector( '#city' );
    area = document.querySelector( '#area' );
    country = document.querySelector( '#country' );
    state = document.querySelector( '#state' );
    pincode = document.querySelector( '#pincode' );
    mapOptions = 
    {
      // How far the maps zooms in.
      zoom: 15,
      // Current Lat and Long position of the pin/
      center: new google.maps.LatLng( -34.397,150.644),
      // center : {
      //  lat: -34.397,
      //  lng: 150.644
      // },
      disableDefaultUI: false, // Disables the controls like zoom control on the map if set to true
      scrollWheel: true, // If set to false disables the scrolling on the map.
      draggable: true, // If set to false , you cannot move the map around.
      // mapTypeId: google.maps.MapTypeId.HYBRID, // If set to HYBRID its between sat and ROADMAP, Can be set to SATELLITE as well.
      // maxZoom: 11, // Wont allow you to zoom more than this
      // minZoom: 9  // Wont allow you to go more up.
    };
    /**
     * Creates the map using google function google.maps.Map() by passing the id of canvas and
     * mapOptions object that we just created above as its parameters.
     *
     */
    // Create an object map with the constructor function Map()
    map = new google.maps.Map( element, mapOptions ); // Till this like of code it loads up the map.
    mark= '../public/front/img/mark.png';
    infoWindow = new google.maps.InfoWindow();
      if (navigator.geolocation) 
      {
        navigator.geolocation.getCurrentPosition(
        (position) => {
          var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var latlang=lat+","+lng;
                console.log( latlang );
                var google_map_pos = new google.maps.LatLng( lat, lng );
 
                /* Use Geocoder to get address */
                var google_maps_geocoder = new google.maps.Geocoder();
                google_maps_geocoder.geocode(
                    { 'latLng': google_map_pos },
                    function( results, status ) {
                        if ( status == google.maps.GeocoderStatus.OK && results[0] ) {
                            var address=results[0].formatted_address;
                              resultArray =  results[0].address_components;
                                console.log( resultArray);
                                //console.log( resultArray[2].types[2] );
                                $("#lng").val(lng);
                                $("#lat").val(lat);
                                
                                $("#latlang").val(latlang);
                                  for( var i = 0; i < resultArray.length; i++ ) 
                                  {
                                    if ( resultArray[ i ].types[0] == "administrative_area_level_3"   ) {
                                       var citi = resultArray[ i ].long_name;
                                      console.log( citi );
                                      $("#city").val(citi);
                                      city.value = citi;
                                      
                                    }
                                  }
                                  for( var j = 0; j < resultArray.length; j++ ) 
                                  {
                                    if ( resultArray[ j ].types[0] == 'administrative_area_level_1'  ) 
                                    {
                                      var state = resultArray[ j ].long_name;
                                      console.log( state );
                                      $("#state").val(state);
                                      state.value = state;
                                    }
                                  }
                                  for( var n = 0; n < resultArray.length; n++ ) 
                                  {
                                    if ( resultArray[ n ].types[2] == 'sublocality_level_1'  ) 
                                    {
                                      var area = resultArray[ n ].long_name;
                                      console.log( area );
                                      $("#area").val(area);
                                      area.value = area;
                                    }
                                  }
                                   for( var m = 0; m < resultArray.length; m++ ) 
                                  {
                                    if ( resultArray[ m ].types[0] == "country"  ) 
                                    {
                                      var country = resultArray[ m ].long_name;
                                      console.log( country );
                                      $("#country").val(country);
                                      country.value = country;
                                    }
                                  }
                                  
                                  for( var k = 0; k < resultArray.length; k++ ) 
                                  {
                                    if ( resultArray[ k ].types[0] == 'postal_code'  ) 
                                    {
                                      pincode = resultArray[ k ].long_name;
                                      console.log( pincode );
                                      $("#pincode").val(pincode);
                                      pincode.value = pincode;
                                    }
                                  }
                                $("#address").val(address);
                            console.log( results[0].formatted_address );
                        }
                    }
                );
          const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };
          marker = new google.maps.Marker({
              position:pos,
              map: map,
              icon: mark,
              draggable: true
          });
          infoWindow.open( map, marker );
          searchBox = new google.maps.places.SearchBox( addressEl );
          /**
           * When the place is changed on search box, it takes the marker to the searched location.
           */
          google.maps.event.addListener( searchBox, 'places_changed', function () 
          {
              var places = searchBox.getPlaces(),
              bounds = new google.maps.LatLngBounds(),
              i, place, lat, long, resultArray,
              addresss = places[0].formatted_address;
              for( i = 0; place = places[i]; i++ ) 
              {
                bounds.extend( place.geometry.location );
                marker.setPosition( place.geometry.location );  // Set marker position new.
              }
              map.fitBounds( bounds );  // Fit to the bound
              map.setZoom( 15 ); // This function sets the zoom to 15, meaning zooms to level 15.
              // console.log( map.getZoom() );
              lat = marker.getPosition().lat();
              long = marker.getPosition().lng();
              latEl.value = lat;
              longEl.value = long;
              
              resultArray =  places[0].address_components;
              console.log( resultArray );
              // Get the city and set the city input value to the one selected
              for( var i = 0; i < resultArray.length; i++ ) 
              {
                if ( resultArray[ i ].types[0] == 'administrative_area_level_3' ) {
                  citi = resultArray[ i ].long_name;
                  console.log( citi );
                  $("#city").val(citi);
                  city.value = citi;
                }
              }
              for( var j = 0; j < resultArray.length; j++ ) 
              {
                if ( resultArray[ j ].types[0] && 'administrative_area_level_1' === resultArray[ j ].types[0] ) 
                {
                  state = resultArray[ j ].long_name;
                  console.log( state );
                  $("#state").val(state);
                  state.value = state;
                }
              }
              for( var n = 0; n < resultArray.length; n++ ) 
              {
                if ( resultArray[ n ].types[0] == 'sublocality_level_1'  ) 
                {
                  var area = resultArray[ n ].long_name;
                  console.log( area );
                  $("#area").val(area);
                  area.value = area;
                }
              }
              for( var m = 0; m < resultArray.length; m++ ) 
              {
                if ( resultArray[ m ].types[0] == "country"  ) 
                {
                  var country = resultArray[ m ].long_name;
                  console.log( country );
                  $("#country").val(country);
                  country.value = country;
                }
              }
              for( var k = 0; k < resultArray.length; k++ ) 
              {
                if ( resultArray[ k ].types[0] && 'postal_code' === resultArray[ k ].types[0] ) 
                {
                  pincode = resultArray[ k ].long_name;
                  console.log( pincode );
                  $("#pincode").val(pincode);
                  pincode.value = pincode;
                }
              }
              // Closes the previous info window if it already exists
              if ( infoWindow ) 
              {
                infoWindow.close();
              }
              /*
               Creates the info Window at the top of the marker
              */
              infoWindow = new google.maps.InfoWindow({
                content: addresss
              });

              infoWindow.open( map, marker );
          });
          /**
           * Finds the new position of the marker when the marker is dragged.
           */
          google.maps.event.addListener( marker, "dragend", function ( event ) 
          {
            var lat, long, address, resultArray, citi;
            console.log( 'i am dragged' );
            lat = marker.getPosition().lat();
            long = marker.getPosition().lng();
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode( { latLng: marker.getPosition() }, function ( result, status ) 
            {
              if ( 'OK' === status ) 
              {  // This line can also be written like if ( status == google.maps.GeocoderStatus.OK ) {
                address = result[0].formatted_address;
                resultArray =  result[0].address_components;
                //console.log( resultArray );
                // Get the city and set the city input value to the one selected
                for( var i = 0; i < resultArray.length; i++ ) 
                {
                  if ( resultArray[ i ].types[0] == 'administrative_area_level_3') 
                  {
                    citi = resultArray[ i ].long_name;
                    console.log( citi );
                    $("#city").val(citi);
                    city.value = citi;
                  }
                }
                for( var j = 0; j < resultArray.length; j++ ) 
                {
                  if ( resultArray[ j ].types[0] && 'administrative_area_level_1' === resultArray[ j ].types[0] ) 
                  {
                    state = resultArray[ j ].long_name;
                   $("#state").val(state);
                    console.log( state );
                    state.value = state;
                  }
                }
                for( var n = 0; n < resultArray.length; n++ ) 
              {
                if ( resultArray[ n ].types[2] == 'sublocality_level_1'  ) 
                {
                  var area = resultArray[ n ].long_name;
                  console.log( area );
                  $("#area").val(area);
                  area.value = area;
                }
              }
              for( var m = 0; m < resultArray.length; m++ ) 
              {
                if ( resultArray[ m ].types[0] == "country"  ) 
                {
                  var country = resultArray[ m ].long_name;
                  console.log( country );
                  $("#country").val(country);
                  country.value = country;
                }
              }
               for( var k = 0; k < resultArray.length; k++ ) 
               {
                if ( resultArray[ k ].types[0] && 'postal_code' === resultArray[ k ].types[0] ) 
                {
                  pincode = resultArray[ k ].long_name;
                  $("#pincode").val(pincode);
                  console.log( pincode );
                  pincode.value = pincode;
                }
              }
              addressEl.value = address;
              latEl.value = lat;
              longEl.value = long;
              //latlongEl.value = latlang;
            } 
            else 
            {
              console.log( 'Geocode was not successful for the following reason: ' + status );
            }
            // Closes the previous info window if it already exists
            if ( infoWindow ) 
            {
              infoWindow.close();
            }
            /**
             * Creates the info Window at the top of the marker
             */
            infoWindow = new google.maps.InfoWindow({
              content: address
            });
            infoWindow.open( map, marker );
          });
        });
         /* infoWindow.setPosition(pos);
          infoWindow.setContent("Location found.");
          infoWindow.open(map);
          map.setCenter(pos);*/
        },
        () => {
          handleLocationError(true, infoWindow, map.getCenter());
        }
      );
    } 
    else 
    {
      handleLocationError(false, infoWindow, map.getCenter());
    }
   
}
  


