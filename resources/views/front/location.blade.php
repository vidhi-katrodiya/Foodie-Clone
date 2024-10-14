<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Google Autocomplete Address Example</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>
  
<body>
    <div class="container mt-5">
        <h4>Laravel Google Autocomplete Address Example</h4>
  
        <div class="form-group">
            <label>Location/City/Address</label>
            <input type="text" name="autocomplete" id="autocomplete" class="form-control" placeholder="Choose Location">
        </div>
  
        <div class="form-group" id="latitudeArea">
            <label>Latitude</label>
            <input type="text" id="latitude" name="latitude" class="form-control">
        </div>
  
        <div class="form-group" id="longtitudeArea">
            <label>Longitude</label>
            <input type="text" name="longitude" id="longitude" class="form-control">
        </div>

         <div class="form-group">
            <label>Destination Address</label>
            <input type="text" name="autocomplete_des" id="autocomplete_des" class="form-control" placeholder="Choose Location">
        </div>
  
        <div class="form-group" id="latitudeArea_des">
            <label>Latitude</label>
            <input type="text" id="latitude_des" name="latitude_des" class="form-control">
        </div>
  
        <div class="form-group" id="longtitudeArea_des">
            <label>Longitude</label>
            <input type="text" name="longitude_des" id="longitude_des" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  
    <script type="text/javascript"
        src="https://maps.google.com/maps/api/js?key=AIzaSyC_mpeoYYKZynug14lspINNCxNGv0nm8W8&libraries=places" ></script>
    <script>
        $(document).ready(function () {
            $("#latitudeArea").addClass("d-none");
            $("#longtitudeArea").addClass("d-none");
        });
        $(document).ready(function () {
            $("#latitudeArea_des").addClass("d-none");
            $("#longtitudeArea_des").addClass("d-none");
        });
    </script>
    <script>
        google.maps.event.addDomListener(window, 'load', initialize);
  
        function initialize() {
            var input = document.getElementById('autocomplete');
            var autocomplete = new google.maps.places.Autocomplete(input);
           
            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                $('#latitude').val(place.geometry['location'].lat());
                $('#longitude').val(place.geometry['location'].lng());
  
                $("#latitudeArea").removeClass("d-none");
                $("#longtitudeArea").removeClass("d-none");
            });
        }
    </script>
    <script type="text/javascript">
        var input_des = document.getElementById('autocomplete_des');
            
            var autocomplete_des = new google.maps.places.Autocomplete_des(input_des);

            autocomplete_des.addListener('place_changed', function () {
                var place = autocomplete_des.getPlace();
                $('#latitude_des').val(place.geometry['location'].lat());
                $('#longitude_des').val(place.geometry['location'].lng());
  
                $("#latitudeArea_des").removeClass("d-none");
                $("#longtitudeArea_des").removeClass("d-none");
            });
    </script>
</body>
</html>