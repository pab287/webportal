<div class="m-content">
	<div class="row">
		<div class="col-lg-6">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								GPS
							</h3>
						</div>
					</div>
				</div>
				<head>
					<meta name="viewport" content="initial-scale=1.0, width=device-width" />
					<link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.0/mapsjs-ui.css?dp-version=1533195059" />
					<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-core.js"></script>
					<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-service.js"></script>
					<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-ui.js"></script>
					<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>
				</head>
				
				<div id="map" style="width: 100%; height: 500px; background: grey" ></div>
					<div class="m-portlet__body">
						<!--begin::Section-->
						<div class="m-section">
							<div class="m-section__content">
								<table class="table table-bordered table-hover" id="table_gps">
									<thead>
										<tr>
											<th>ID</th>
											<th>NAME</th>
											<th>ADDRESS</th>
											<th>Datetime</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
						<!--end::Section-->
					</div>
					<!--end::Form-->
				</div>
			</div>
			<div class="col-lg-6">
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Mobile Attendance
								</h3>
							</div>
						</div>
					</div>
					<div class="m-portlet__body">
						<!--begin::Section-->
						<div class="m-section">
							<div class="m-section__content">
								<table class="table table-bordered table-hover" id="table_gps_attendance">
									<thead>
										<tr>
											<th>
												Name
											</th>
											<th>
												Time IN/OUT
											</th>
											<!-- <th>
												Time Out
											</th> -->
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
						</div>
						<!--end::Section-->
					</div>
					<!--end::Form-->
				</div>
			</div>
		</div>
	</div>
</div><!-- 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3bnhZkUNKmWcNCqrbvuIaHOT9PH61FGA&callback=myMap"src="script.js"></script>

 -->
<!--  <script async defer src="https://maps.googleapis.com/maps/js?key=AIzaSyB3bnhZkUNKmWcNCqrbvuIaHOT9PH61FGA&callback=initMap"></script> -->
 <script  type="text/javascript"  charset="UTF-8">
 	//Step 1: initialize communication with the platform
var platform = new H.service.Platform({
  app_id: 'devportal-demo-20180625',
  app_code: '9v2BkviRwi9Ot26kp2IysQ',
  useHTTPS: true
});
var pixelRatio = window.devicePixelRatio || 1;
var defaultLayers = platform.createDefaultLayers({
  tileSize: pixelRatio === 1 ? 256 : 512,
  ppi: pixelRatio === 1 ? undefined : 320
});

var map = new H.Map(document.getElementById('map'),
  defaultLayers.normal.map, {pixelRatio: pixelRatio});

var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

// Create the default UI components
var ui = H.ui.UI.createDefault(map, defaultLayers);
moveMapToBerlin();
 	function moveMapToBerlin(){
  map.setCenter({lat:10.7043602, lng:122.9633725});
  map.setZoom(15);
   var Marker = new H.map.Marker({lat:10.7043602, lng:122.9633725});
  map.addObject(Marker);
}
function map_set(a,b){

$('#map').empty();
var platform = new H.service.Platform({
  app_id: 'devportal-demo-20180625',
  app_code: '9v2BkviRwi9Ot26kp2IysQ',
  useHTTPS: true
});
var pixelRatio = window.devicePixelRatio || 1;
var defaultLayers = platform.createDefaultLayers({
  tileSize: pixelRatio === 1 ? 256 : 512,
  ppi: pixelRatio === 1 ? undefined : 320
});


var map = new H.Map(document.getElementById('map'),
  defaultLayers.normal.map, {pixelRatio: pixelRatio});


var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

var ui = H.ui.UI.createDefault(map, defaultLayers);
moveMapTolocation(map,a,b);
 
}

function moveMapTolocation(map,a,b){

 const [latitude, longitude] = a.split(',');
 	var userLng = parseFloat(longitude);
	var userLat = parseFloat(latitude);
 
   map.setCenter({lat:userLng, lng:userLat});
  map.setZoom(15);

   var Marker = new H.map.Marker({lat:userLng, lng:userLat});
  map.addObject(Marker);


}
	//for gps data table
	$("#table_gps").DataTable({
		"ajax" : "<?php echo site_url("androidapp/location/getAllLocation");?>"
	});

	// for gps attendance data table
	$("#table_gps_attendance").DataTable({
		"ajax" : "<?php echo site_url("androidapp/location/getAllLocation");?>"
	});

	$(document).ready(function(){
		console.log(site_url("gps/getgpsattendance"));
	});

</script>
