<div class="m-content">
  <div id="image_location" class="m-portlet">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
          <h3 class="m-portlet__head-text">
            Image Location
          </h3>
        </div>
      </div>
    </div>
    <div class="m-portlet__body">
      <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
        <div class="row align-items-center">
          <div class="col-xl-4 order-1 order-xl-2 m--align-left d-flex flex-row" style="padding: 0;">
            
            <div class="m-separator m-separator--dashed d-xl-none"></div>
          </div>
        </div>
        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
          <div class="row align-items-center">
            <div class="col-xl-8 order-2 order-xl-1">
              
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-4 col-md-4 col-lg-4 col-sm-12">
          <div class="m-input-icon m-input-icon--left mb-3" style="border: 1px solid #c3c3c3;">
            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
            <span class="m-input-icon__icon m-input-icon__icon--left">
              <span>
                <i class="la la-search"></i>
              </span>
            </span>
          </div>
          <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll" style="margin-top: -6px; z-index: 1;">
            <table class="table table-striped table-bordered" id="table_image" width="100%">
              <thead>
                <tr>
                  <th>USER</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-8 col-md-8 col-lg-8 col-sm-12">
          <div id="googleMap" style="height:775px; margin-bottom:20px; box-shadow: 0px 0px 8px -1px #cccccc;"></div>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="imageModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Image</h5>
      </div>
      <div class="modal-body">
        <div id="imageLocationArea"></div>
      </div>
      <div class="modal-footer">
        <button id="close" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>

var search_val = "";
var table_image = $("#table_image").DataTable({
  dom: 'rtlip',
	serverSide: true,
  processing: true,
  aaSorting: [],
  ajax: {
      url: baseUrl("gcctime/Gps_locator/getimage"),
      type: "post",
      global: false,
      dataType: "json",
      data: function(d){
        d.csrf_token = _csrf_hash,
        d.search['value'] = search_val
      }
  },
  searching: true,
  columns: [
    { data: "name_with_address", width: "15%"},
    { 
      data: "Action", width: "1%", className: "text-center", render: function (data, type, row, meta) {
        return itemDatatableActions(row);
      }
    },
  ],
});


$('#generalSearch').donetyping(function(callback) {
  search_val = $(this).val();
  table_image.ajax.reload();
});

function itemDatatableActions(row){
	if(row){
    var _actionButton ="";
    _actionButton +="<a class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill mr-1' id='location' value='"+row.latitude+"_"+row.longitude+"_"+row.address+"_"+row.name+"_"+row.datetime+"' ><i class='la la-map-marker'></i></a>";
    if(row.image != ""){
      _actionButton +="<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='view_image("+row.id+")'><i class='la la-camera'></i></a>";
    }
		return _actionButton;
	}else{ return false; }
}

function view_image($bio_id){
  $("#imageModal").modal("show");
  $.ajax({
    url: baseUrl("gcctime/Gps_locator/locationImage"),
    type: 'POST',
    data: {
      csrf_token: _csrf_hash,
      id: $bio_id, 
    },
    dataType: 'json',
    success: function(resp){
      if(resp != ""){
        const hostname = window.location.host;
        if(hostname === "conyxph.com"){
          $("#imageLocationArea").append('<img src="/web/androidapp/storage/'+resp.image+'" class="mb-2 image_locator">');
        }else{
          $("#imageLocationArea").append('<img src="/portaldev/androidapp/storage/'+resp.image+'" class="mb-2 image_locator">');
        }
      }
      $("#close").on('click', function(){
        $("#imageLocationArea .image_locator").remove();
      });
    },
    error: function (request, status, error) {
      toastr.error("Please check your internet connection.", "Connection Error");
    }
  });
}

function initMapTemp() {
  let location;
  
  const uluru = { lat: 10.7043081, lng: 122.9633338 };
  
  const map = new google.maps.Map(document.getElementById("googleMap"), {
    mapId: "61eadc851067d069",
    zoom: 19,
    center: uluru,
    mapTypeId: 'satellite',
  });

  const marker = new google.maps.Marker({
    map: map,
  });

  var contentString = "";
  const infowindow = new google.maps.InfoWindow({maxWidth: 600, maxHeight: 200});
  $("#table_image").on("click", "#location", function(){
    var value_ = $(this).attr("value").split("_");
    var location = value_;
    var address = value_[2];
    var name = value_[3];
    var datetime = value_[4];
    var lati = Number(location[0]);
    var long = Number(location[1]);
    const uluru = { lat: lati, lng: long };

    marker.setPosition(uluru);
    map.setCenter(uluru);

    marker.setAnimation(google.maps.Animation.BOUNCE);
    contentString = '<div style="">' +
                      "<strong>User:</strong> "+ name +
                      "<br/> <strong>Date:</strong> "+ datetime +
                      "<br/> <strong>Address:</strong> "+ address +
                    "</div>";
    infowindow.setContent(contentString);
    infowindow.open(map, marker);
  });

  google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent(contentString);
    infowindow.open(map, marker);
  });
}

</script>