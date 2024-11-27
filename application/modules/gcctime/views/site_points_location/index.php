<div id="sitePointLocationArea" class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Site Point Location
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<div id="somecomponent" class="row" style="margin-bottom: 45px;">
						<div id="siteLocationForm" class="col-lg-3">
							<div class="m-alert m-alert--outline m-alert--outline-2x alert alert-metal alert-dismissible fade show py-4 m--font-success text-center" role="alert">
								<strong>
									NOTE:
								</strong>
								Here you can set a location along with geo-fence restriction.
							</div>
							<form id="addLocation">
								<strong style="font-weight: bold; font-size: 14px; color: #818181;">SITE NAME</strong>
								<div class="form-group">
									<input type="text"  name="site_name" id="address" class="form-control" style="font-weight: bold;" />
								</div>
								<div class="form-row mb-3">
									<div class="col">
										<strong style="font-weight: bold; font-size: 14px; color: #818181;">LATITUDE</strong>
										<input type="hidden" name="id" id="id"/>
										<input type="text" name="latitude" id="lat" class="form-control" />
									</div>
									<div class="col">
										<strong style="font-weight: bold; font-size: 14px; color: #818181;">LONGITUDE</strong>
										<input type="text" name="longitude" id="long" class="form-control" />
									</div>
								</div>
								<button type="submit" class="btn btn-primary btnNew" id="addLocationSite" style="font-weight: bold;">Add</button>
								<button type="button" class="btn btn-default btnNew m--hide" id="cancel_save">Cancel</button>
								<button type="button" id="geofenceBtn" class="btn btn-success btnGeofence pull-right" style="font-weight: bold;">Show Geofence</button>
							</form>
						</div>
						<div id="mapArea" class="col-lg-9 p-0">
							<input id="mapInput" class="controls" type="text" placeholder="Search Places Here..." style="height: 35px; width: 100%; border: solid 1px #c3c3c3; background-color: #f4f5f8;" />
							<div id="map" class="sitePointMap" class="p-0"></div>
						</div>
					</div>
					<!--begin::Section-->
					<div class="m-section">
						<div class="m-section__content">
							<!--begin: Datatable -->
							<div class="col-xl-12 order-1 order-xl-2 m--align-right d-flex flex-row-reverse mt-3 p-0">
								<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;width:350px;">
									<input type="text" class="form-control m-input m-input--solid"
											placeholder="Search..." id="siteLocationSearch">
									<span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
								</div>
							</div>
							<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
								<table class="table table-striped table-bordered" id="table_site_restriction">
									<thead>
										<tr>
											<th>Site name</th>
											<th>Latitude</th>
											<th>Longitude</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
							<!--end: Datatable -->
						</div>
					</div>
					<!--end::Section-->
				</div>
				<!--end::Form-->
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_archived" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
            <input type="hidden" name="id" id="archive_id">
			<div class="modal-header">
				<h5 class="modal-title" style="font-weight: bold; color: #5e5e5e;">
					Archive Site Point
				</h5>
			</div>
			<div class="modal-body" id="archive_text">
				
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-danger btnArchive" style="font-weight: bold;" onclick="archivedSiteLocation()">
					Archive
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="m_add" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" style="font-weight: bold; color: #5e5e5e;">
					Site Point
				</h5>
			</div>
			<div class="modal-body">
				<div class="mb-2" id="add_text">
					Are you sure you want to add?
				</div>

				<div id="showGeo"></div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btnSave" style="font-weight: bold;" onclick="addSiteLocation()">
					Save
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<script>

	$("#siteLocationSearch").donetyping(function (callback) {
		search_val = $(this).val();
		site_restric_tbl.ajax.reload();
	});

	var search_val = "";
	var site_restric_tbl = $("#table_site_restriction").DataTable({
		dom: '<"toolbar">frtlip',
		serverSide: true,
		processing: true,
		//order: [ 1, "desc" ],
		aaSorting: [],
		ajax: {
			url: baseUrl("gcctime/site_points_location/get_all_site"),
			type: "post",
			global: false,
			dataType: "json",
			data: function (d) {
				d.csrf_token = _csrf_hash;
				d.search['value'] = search_val;
			}
		},
		searching: false,
		columns: [
			{
				data: "site_name",
				width: "50%", 
				render: function (data) {
					return "<strong style='color: #525252;'>"+data+"</strong>";
				}
			},
			{
				data: "latitude",
				width: "25%"
			},
			{
				data: "longtitude",
				width: "25%"
			},
			{
				data: null,
				width: "5%",
				className: "text-center",
				orderable: false,
				render: function (data, type, row, meta) {
					return itemDatatableActions(row);
				},
			},
		],
	});

	function itemDatatableActions(row) {
		if (row) {
			var _actionButton = "";
			_actionButton += "<div class='dropdown'>";
			_actionButton += "<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
			_actionButton += "<i class='fa fa-ellipsis-v'></i>";
			_actionButton += "</a>";
			_actionButton += "<div class='dropdown-menu dropdown-menu-right'>";
			_actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' id='EditSiteLocation' value='" + row.id + "'><i class='la la-pencil-square'></i>Edit</a>";
			_actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='site_location_delete(" + row.id +","+"\"" + row.site_name + "\")'><i class='la la-trash'></i>Delete</a>";
			_actionButton += " </div>";
			_actionButton += "</div>";
			return _actionButton;
		} else {
			return "";
		}
	}

	function site_location_delete(id, site_name){
		const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${site_name}</strong>?</p>`;
		$('#m_archived').modal('show');
		$('#archive_text').empty().html(temp);
		$("#m_archived input[name=id]").val(id);
	}

	function archivedSiteLocation(){
		var id = document.getElementById('archive_id').value;
		$.ajax({
			url: baseUrl("gcctime/site_points_location/delete_location"),
			type: "POST",
			dataType: "json",
			data: {
				csrf_token: _csrf_hash,
				id: id
			},
			success: function(resp){
				if(resp === 1){
					site_restric_tbl.ajax.reload();
					$('#m_archived').modal('hide');
					refreshData();
				}
			},
			error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection Error");
			}
		});
	}

	// need to enable google map billing to work
	// note! do note delete
	const polygonGeo = [];
	var geoJson = {};

	function initMapTemp() {
		const uluru = { lat: 10.7043081, lng: 122.9633338 };
		const map = new google.maps.Map(document.getElementById("map"), {
			mapId: "61eadc851067d069",
			zoom: 19,
			center: uluru,
			mapTypeId: 'satellite'
		});

		const marker = new google.maps.Marker({
			position: uluru,
			map: map,
			draggable: true,
		});

		const drawingManager = new google.maps.drawing.DrawingManager({
			drawingControl: false,
			drawingControlOptions: {
			position: google.maps.ControlPosition.TOP_CENTER,
			drawingModes: [
				google.maps.drawing.OverlayType.POLYGON,
			],
			},
			markerOptions: {
			icon:
				"https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
			},
		});

		searchSite(map, marker);

		drawingManager.addListener('polygoncomplete', function (polygon) {
			coordinates = (polygon.getPath().getArray());
			for(var i = 0;i < coordinates.length; i++ ){
				polygonGeo.push(coordinates[i].toJSON());
			}

			$("#addLocationSite").on("click", function(){
				polygon.setMap(null);
				drawingManager.setDrawingMode(null);
				site_restric_tbl.ajax.reload();
			});
		});

		// this code id for travel order set up
		//  drawingManager.addListener('markercomplete', function (mark) {
		// 	   markFlag = mark.position.toJSON();
		//  });

		marker.addListener('click', function (markie) {
			const coords = markie.latLng;
			$("#lat").val(coords.lat);
			$("#long").val(coords.lng);
			GetAddress(coords, map);
		});

		map.addListener("click", (mapsMouseEvent) => {
			const coords = mapsMouseEvent.latLng;
			const placeId = mapsMouseEvent.placeId;
			const addresscoords = mapsMouseEvent.latLng.toJSON();

			marker.setPosition(coords);

			$("#lat").val(coords.lat);
			$("#long").val(coords.lng);
			GetAddress(coords, map, placeId);
		});

		$("#table_site_restriction").on("click", "#EditSiteLocation", function(){
			$id = $(this).attr('value');
			$.ajax({
				url: baseUrl("gcctime/site_points_location/edit_location"),
				type: "POST",
				dataType: "json",
				data: {
					csrf_token: _csrf_hash,
					id: $id
				},
				success: function(resp){
					const coord = { lat: Number(resp.latitude), lng: Number(resp.longtitude) };
					map.setCenter(coord);
					marker.setPosition(coord);
					$("#address").val(resp.site_name);
					$("#lat").val(resp.latitude);
					$("#long").val(resp.longtitude);
					$("#id").val($id);
					$("#addLocationSite").text("Update");
					$("#cancel_save").removeClass("m--hide");
					$("html, body").animate({ scrollTop: 0 }, "slow");

					const location = resp.geofence_polygon;

					geoJson = {
						"type": "FeatureCollection",
						"features": [
							{
								"type": "Feature",
								"properties": {
									"stroke": "#555555",
									"stroke-width": 2,
									"stroke-opacity": 1
								},
								"geometry": {
									"coordinates": [
										location
									],
									"type": "Polygon",
								}
							}
						]
					};
				},
				error: function (request, status, error) {
					toastr.error("Please check your internet connection.", "Connection Error");
				}
			});
		});

		$("#geofenceBtn").on("click", function(){
			if(drawingManager.drawingControl === true){
				drawingManager.setOptions({
					drawingControl: false
				});
				$("#geofenceBtn").text("Show Geofence");

				if(!$.isEmptyObject(geoJson)){
					map.data.forEach( function (feature){
						map.data.remove(feature);
					});
				}
			}else{
				drawingManager.setMap(map);
				drawingManager.setOptions({
					drawingControl: true
				});
				$("#geofenceBtn").text("Hide Geofence");

				if(!$.isEmptyObject(geoJson)){
					map.data.addGeoJson(geoJson);
        			map.data.setStyle({fillColor:"black", fillOpacity: 0.5, strokeColor:"yellow", strokeWeight:2, strokeOpacity:1.0});
				}
			}
		});

		$("#cancel_save").on("click", function(){
			refreshData();
			$("#addLocationSite").text("Add");

			if(!$.isEmptyObject(geoJson)){
				map.data.forEach( function (feature){
					map.data.remove(feature);
				});

				geoJson = {};
			}
		});

		$("#addLocation").on("submit", function(e){
			e.preventDefault();
			var lat = $(this).find('#lat').val();
			var lat = $(this).find('#lat').val();
			var address = $(this).find('#address').val();
			if(!address){
				toastr.warning("Please enter site name");
			} else if (!lat) {
				toastr.warning("Please set a marker on the map");
			} else {
    			const temp = `<p>Are you sure you wan't to save <strong class='m--font-boldest'>${address}</strong>?</p>`;
				$('#add_text').empty().html(temp);

				var html = "";
				html += '<p class="mb-1"><b>Geofence Coordinates</b></p>';
				html += '<ul>';

				if(!jQuery.isEmptyObject(polygonGeo)){
					$.each(polygonGeo, function(e, v){
						html += `<li><div>Lng: ${v.lng} - lat: ${v.lat}</div>`;
					});
				}else{
					html += '<li><div>No Assigned Coordinates</div></li>';
				}

				html += '</ul>';
				$("#showGeo").empty().html(html);

				$('#m_add').modal('show');
			}
		});
	}

	function addSiteLocation(){
		var address = document.getElementById('address').value;
		var lat = document.getElementById('lat').value;
		var long = document.getElementById('long').value;
		var id = document.getElementById('id').value;
		
		if(!jQuery.isEmptyObject(polygonGeo)){
			$.ajax({
				url: baseUrl("gcctime/site_points_location/add_new_location"),
				type: "POST",
				dataType: "json",
				data: {
					csrf_token: _csrf_hash,
					id: id,
					siteName: address,
					latitude: lat,
					longitude: long,
					geofence: polygonGeo,
				},
				success: function(resp){
					if(resp === 1){
						refreshData();
						site_restric_tbl.ajax.reload();
						toastr.success("Successfully saved");
					} else if(resp == 2){

						Swal.fire({
							title: 'Site Point Location',
							text: 'Site name already exist!',
							icon: 'error',
						}).then((result) => {
							if (result.isConfirmed) {
								$('#m_add').modal('hide');
							}
						});
					} else {
						toastr.error("Error on the backend");
					}
				},
				error: function (request, status, error) {
					toastr.error("Please check your internet connection.", "Connection Error");
				}
			});
		}else{
			Swal.fire({
				title: 'Site Point Location',
				text: 'Assigning of Geolocation is Required!',
				icon: 'error',
			}).then((result) => {
				if (result.isConfirmed) {
					$('#m_add').modal('hide');
				}
			});
		}
	}

	function refreshData(){
		$("#id").val("");
		$("#address").val("");
		$("#lat").val("");
		$("#long").val("");
		$("#addLocationSite").text("Add");
		$('#m_add').modal('hide');
		$("#cancel_save").addClass("m--hide");
		polygonGeo.splice(0, polygonGeo.length);
	}

	function searchSite(map, marker){
		const input = document.getElementById("mapInput");
		const searchBox = new google.maps.places.SearchBox(input);
		map.addListener("bounds_changed", () => {
			searchBox.setBounds(map.getBounds());
		});

		searchBox.addListener("places_changed", () => {
			const places = searchBox.getPlaces();

			if (places.length == 0) {
				return;
			}
			const bounds = new google.maps.LatLngBounds();
			places.forEach((place) => {
				if (!place.geometry || !place.geometry.location) {
					console.log("Returned place contains no geometry");
					return;
				}
				marker.setPosition(place.geometry.location);
				marker.setTitle(place.name);
				const placeCoords = place.geometry.location.toJSON();
				//$("#address").val(place.name);
				$("#lat").val(placeCoords.lat);
				$("#long").val(placeCoords.lng);
				if (place.geometry.viewport) {
					bounds.union(place.geometry.viewport);
				} else {
					bounds.extend(place.geometry.location);
				}
			});
			map.fitBounds(bounds);
		});
	}

	function GetAddress(latlon, map, placeId) {
		var geocoder = geocoder = new google.maps.Geocoder();
		
		if(placeId != null){
			const request = {
				placeId: placeId,
				fields: ["name", "formatted_address", "place_id", "geometry"],
			};
			const infowindow = new google.maps.InfoWindow();
			const service = new google.maps.places.PlacesService(map);

			service.getDetails(request, (place, status) => {
				if (
				status === google.maps.places.PlacesServiceStatus.OK &&
				place &&
				place.geometry &&
				place.geometry.location
				) {
					//$("#address").val(place.name);
				}
			});
		}else{
			geocoder.geocode({
				location: latlon
			}, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						//$("#address").val(results[0].formatted_address);
					}
				}
			});
		}
	}

</script>