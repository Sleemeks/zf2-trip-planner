<?php
// $content = file_get_contents("http://submit.shutterstock.com/show_component.mhtml?component_path=download_map%2Frecent_downloads.mh");
// var_dump($content);
// exit;
?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

<script>

// var client = new XMLHttpRequest();
// client.open('GET', 'http://submit.shutterstock.com/show_component.mhtml?component_path=download_map%2Frecent_downloads.mh');
// client.onreadystatechange = function() {
  // alert(client.responseText);
// }
// client.send();

    var FrankfurtStartX = 50.1066854;
    var FrankfurtStartY = 8.6373771;

    var Center = new google.maps.LatLng(FrankfurtStartX, FrankfurtStartY);
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;

    function initialize() {
        var properties = {
            center: Center,
            zoom: 3,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("map"), properties);

        // var marker = new google.maps.Marker({
            // position: Center,
        // });

        // marker.setMap(map);


		var json = '[{"longitude":"-90.4","media_id":"372432775","latitude":"38.6","time":1456593910,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/372432775.jpg","media_type":"photo"},{"longitude":"39.7","media_id":"368872448","latitude":"47.2","time":1456593277,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/368872448.jpg","media_type":"photo"},{"longitude":"-86.8","media_id":"371187440","latitude":"40.5","time":1456235725,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/371187440.jpg","media_type":"photo"},{"longitude":"-3.0","media_id":"372432775","latitude":"52.9","time":1455700725,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/372432775.jpg","media_type":"photo"},{"longitude":"-3.4","media_id":"372432775","latitude":"58.6","time":1455683219,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/372432775.jpg","media_type":"photo"},{"longitude":"-74.9","media_id":"368872448","latitude":"40.0","time":1455646144,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/368872448.jpg","media_type":"photo"},{"longitude":"23.7","media_id":"368872640","latitude":"38.0","time":1455537788,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/368872640.jpg","media_type":"photo"},{"longitude":"3.6","media_id":"371187461","latitude":"51.2","time":1454763626,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/371187461.jpg","media_type":"photo"}]';
		// '[{"longitude":"-3.0","media_id":"372432775","latitude":"52.9","time":1455700725,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/372432775.jpg","media_type":"photo"},{"longitude":"-3.4","media_id":"372432775","latitude":"58.6","time":1455683219,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/372432775.jpg","media_type":"photo"},{"longitude":"-74.9","media_id":"368872448","latitude":"40.0","time":1455646144,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/368872448.jpg","media_type":"photo"},{"longitude":"23.7","media_id":"368872640","latitude":"38.0","time":1455537788,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/368872640.jpg","media_type":"photo"},{"longitude":"3.6","media_id":"371187461","latitude":"51.2","time":1454763626,"thumb_url":"http://image.shutterstock.com/thumb_small/0/0/371187461.jpg","media_type":"photo"}]';
		obj = JSON.parse(json);

		for (var i = 0; i < obj.length; i++){
		
		// console.log(obj[i].latitude);
		  var marker = new google.maps.Marker({
		    position: {lat: parseFloat(obj[i].latitude), lng: parseFloat(obj[i].longitude)},
		    map: map
		  });	
			// console.log();
			
		}

// console.log(obj);
		
		// console.log(obj);

        // var waypoints = [];
        // via = new google.maps.LatLng(50.407840, 7.605635);
        // waypoints = []; // init an empty waypoints array
        // waypoints.push({
            // location: via,
            // stopover: true
        // });
        // addRoute("Frankfurt am Main", "Koblenz", waypoints);
        // addRoute("Koblenz", "Mainz");
// 
// 
// 
        // via = new google.maps.LatLng(50.631672, 7.221543);
// 
        // waypoints = []; // init an empty waypoints array
        // waypoints.push({
            // location: via,
            // stopover: true
        // });
// 
        // addRoute("Koblenz", "Bonn", waypoints);
        // addRoute("Koblenz", "Cologne");
        // addRoute("Koblenz", "Dortmund");
        // addRoute("Koblenz", "Dusseldorf");
        // addRoute("Koblenz", "Amsterdam");
// 
// 
// 
        // setTimeout(function(){
            // addRoute("Koblenz", "Trier");
            // addRoute("Koblenz", "Aachen");
            // addRoute("Koblenz", "Potsdam");
            // addRoute("Koblenz", "Berlin");
            // addRoute("Koblenz", "Berlin");
            // addRoute("Koblenz", "Eindhoven");
            // addRoute("Koblenz", "Rotterdam");
            // addRoute("Koblenz", "Luxembourg");
            // addRoute("Koblenz", "Munich");
            // addRoute("Koblenz", "Dresden");
        // }, 10000);
// 
        // setTimeout(function(){
// 
            // addRoute("Koblenz", "Poznan");
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Kolding",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Copenhagen",
                // stopover: true
            // });
// 
            // addRoute("Koblenz", "Gothenburg", waypoints);
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Budapest",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Vienna",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Prague",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Karlovy Vary",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Prague",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Bratislava",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Budapest",
                // stopover: true
            // });
            // addRoute("Koblenz", "Poznan", waypoints);
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Lviv",
                // stopover: true
            // });
            // addRoute("Frankfurt", "Dnipropetrowsk", waypoints);
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Vienna",
                // stopover: true
            // });
            // waypoints.push({
                // location: "liptovsky jan",
                // stopover: true
            // });
            // addRoute("Koblenz", "liptovsky mikulas", waypoints);
// 
            // var germanBorderFussen = new google.maps.LatLng(47.548054, 10.659526);
            // addRoute("Koblenz", germanBorderFussen);
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Trento",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Civitavecchia",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Rome",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Saline Sadun",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Florence",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Venice",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Trento",
                // stopover: true
            // });
// 
            // addRoute(germanBorderFussen, germanBorderFussen, waypoints, true);
            // addRoute(germanBorderFussen, Kaunertal, "", true);
// 
        // }, 20000);
// 
        // setTimeout(function() {
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Zell am See",
                // stopover: true
            // });
            // addRoute("Koblenz", "Kaprun", waypoints);
// 
            // var parisBorder = new google.maps.LatLng(49.472774, 6.121519);
            // addRoute("Koblenz", parisBorder);
            // addRoute(parisBorder, "Paris", '', true);
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Maastricht",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Liege",
                // stopover: true
            // });
            // addRoute("Koblenz", "Koblenz", waypoints);
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Pasewalk",
                // stopover: true
            // });
            // addRoute("Berlin", "Szczecin", waypoints);
// 
            // addRoute("Koblenz", "Kassel");
// 
            // addRoute("Koblenz", "Brugge");
            // addRoute("Koblenz", "Trier");
            // addRoute("Koblenz", "Hook of Holland");
            // addRoute("Hook of Holland", "The Hague");
// 
// 
        // }, 30000);
        // setTimeout(function() {
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Magdeburg",
                // stopover: true
            // });
            // addRoute("Koblenz", "Berlin", waypoints);
// 
            // addRoute("Koblenz", "Amden");
// 
            // addRoute("Frankfurt", "Colmar, France");
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Bilbao",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Orbaneja del Castillo",
                // stopover: true
            // });
            // waypoints.push({
                // location: "Gijon",
                // stopover: true
            // });
// 
            // addRoute("Colmar, France", "Porto", waypoints, false, true);
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Urdos",
                // stopover: true
            // });
// 
            // addRoute("Porto", "Freiburg", waypoints, false, true);
            // addRoute("Freiburg", "Frankfurt");
// 
// 
        // }, 40000);
// 
        // setTimeout(function() {
// 
            // addRoute("Frankfurt", "Maastricht");
// 
            // addRoute("Maastricht", "Amersfoort", [], true);
// 
            // addRoute("Frankfurt", "Wetzlar", [], true);
// 
            // addRoute("Frankfurt", "Berlin");
// 
            // waypoints = []; // init an empty waypoints array
            // waypoints.push({
                // location: "Dresden",
                // stopover: true
            // });
            // addRoute("Frankfurt", "Ozarowice", waypoints, false, true);
// 
            // addRoute("Frankfurt am Main", "Aachen");
// 
            // waypoints = [];
            // waypoints.push({
                // location: "Antwerpen",
                // stopover: true
            // });
            // addRoute("Aachen", "Brugge", waypoints, true, true);
// 
            // waypoints = [];
            // waypoints.push({
                // location: "Ghent",
                // stopover: true
            // });
            // addRoute("Brugge", "Aachen", waypoints, true, true);
// 
            // waypoints = [];
            // waypoints.push({
                // location: "Bouillon",
                // stopover: true
            // });
            // addRoute("Frankfurt am Main", "Paris", waypoints, false, true);
// 
// 
        // }, 50000);
// 
        // setTimeout(function() {
// 
            // addRoute("Hattersheimer Straße 27, Frankfurt", "Karres 73, 6460 Karres", [], false, true);
            // addRoute("Karres 73, 6460 Karres", "Via Cappuccina, 30172 Venezia", [], false, true);
            // addRoute("Via Cappuccina, 30172 Venezia", "Monfalcone", [], false, true);
            // addRoute("Monfalcone GO, Italy", "Komarno, Slovakia", [], false, true);
// 
            // addRoute("Komarno, Slovakia", "Liptovsky Jan, Slovakia", []);
// 
            // addRoute("Liptovsky Jan, Slovakia", "023 56 Makov Slovakia", []);
            // addRoute("Liptovsky Jan, Slovakia", "027 32 Zuberec", []);
// 
            // addRoute("023 56 Makov Slovakia", "Prague", [], false, true);
// 
// 
// 
            // addRoute("Prague", "Frankfurt", [], false, true);
// 
        // }, 60000);

    }
    function addRoute(startX, startY, waypoints, avoidSpeedway, avoidToll){

        var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});

//        console.log(directionsDisplay.markerOptions());

        directionsDisplay.setMap(map);

//        var start = new google.maps.LatLng(startX, startY);
//        var end = new google.maps.LatLng(endX, endY);

        var start = startX;
        var end = startY;

        if (typeof waypoints == "undefined" || waypoints == ""){
            waypoints = [];
        }

        avoidSpeedwayFlag = false;
        if (avoidSpeedway == true){
            avoidSpeedwayFlag = true;
        }
        avoidTollFlag = false;
        if (avoidToll == true){
            avoidTollFlag = true;
        }

        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING,
            waypoints: waypoints,
            avoidHighways: avoidSpeedwayFlag,
            avoidTolls: avoidTollFlag
        };


        directionsService.route(request, function (result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
            } else {
                alert("couldn't get directions:" + status);
            }
        });


    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
<style>
    html,
    body,
    #map {
        margin: 0;
        padding: 0;
        height: 100%;
    }
</style>
<div id="map"></div>