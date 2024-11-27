<style>
    button.gm-ui-hover-effect {
        visibility: hidden;
    }
    #instructs li{
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 1px;
    }
    #instructs li .active{
        background-color: #fbc918 !important;
    }
    #instructs li .inactive{
        background-color: #133f6d !important;
    }
</style>
<div id="gcctimeApp_location">
    <div class="container-fluid">
        <div class="col-lg-12 p-0 row m-auto">
            <div class="col-lg-5">

                <div class="m-portlet m-portlet--full-height ">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-users"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    Users
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="form-group m-form__group m-0 mb-2">
                            <div class="m-input-icon m-input-icon--right">
                                <input type="text" id="search_user" class="form-control m-input" placeholder="Search">
                                <span class="m-input-icon__icon m-input-icon__icon--right">
                                    <span>
                                        <i class="la la-search"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="tab-content py-2 px-3 border rounded" data-scrollable="true" data-max-height="565" style="height: 565px; overflow: visible; position: relative;" >
                            <div class="tab-pane active" id="app_users_list">
                                <!--begin::Widget 14-->
                                <div class="m-widget4 m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" >
                                    <!--begin::Widget 14 Item-->
                                    <div class="m-widget4__item m-auto" v-for="list in user_list">
                                        <div class="m-widget4__img m-widget4__img--pic">
                                            <img v-bind:src="list.pic_filename" alt="" style="height: 50px;width: 50px;">
                                        </div>
                                        <div class="m-widget4__info">
                                            <span class="m-widget4__title text-uppercase" v-on:mouseover="tooltip(event, list.status)">
                                                {{list.display_name}}  <span v-if="list.status == 2" class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-md m-badge--danger"></span>
                                            </span>
                                            <br>
                                            <span v-text="list.position" class="m-widget4__sub text-uppercase">
                                            </span>
                                        </div>
                                        <div class="m-widget4__ext">
                                            <a href="javascript:void(0)" v-on:click="selectUser(list)" class="m-btn m-btn--pill m-btn--hover-brand btn btn-sm btn-secondary">
                                                <i class="la la-map-marker"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Widget 14-->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-7">
                <div class="m-portlet m-portlet--tab">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="flaticon-route"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    Location
                                </h3>
                            </div>
                        </div>
                        <div id="instructs" class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-md mb-1 mr-1 active"></span>
                                    active
                                </li>
                                <li class="m-portlet__nav-item">
                                    <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-md mb-1 m
                                    r-1 inactive"></span>
                                    inactive
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="col-lg-12 p-0">
                            <div id="select_user" class="m-widget4">
                                <div class="m-widget4__item pt-0" v-if="if_empty(sp_data) > 0">
                                    <div class="m-widget4__img m-widget4__img--pic">
                                        <img v-bind:src="sp_data.pic_filename" alt="" style="height: 50px;width: 50px;">
                                    </div>
                                    <div class="m-widget4__info">
                                        <span class="m-widget4__title text-uppercase">
                                          {{sp_data.display_name}}   <span v-if="sp_data.status == 2" class="m-badge m-badge--metal m-badge--wide text-light">Active</span>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sube text-uppercase" v-text="sp_data.position">
                                        </span>
                                    </div>
                                    <div class="m-widget4__ext">
                                        <a href="javascript:void(0)" id="dateBtn" class="m-btn m-btn--pill m-btn--hover-brand btn btn-sm btn-secondary">
                                            <span>
                                                <i class="la la-calendar-check-o p-1"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-0">
                            <div id="location_map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $.ajax({
        url: baseUrl('gcctime/app_location/get_app_users'),
        type: "GET",
        dataType: "JSON",
        success: function(resp){
            
            if(resp != 0){
                vm.user_list = resp;
            }
        },
        error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection Error");
        }
    });
});

let user_data, arr;
function re_init($id, $date = new Date()){
    let date;
    const month = $date.getMonth() + 1;
    
    const mindate = {};
    
    if($id){
        $.ajax({
            url: baseUrl('gcctime/app_location/get_users_location'),
            type: "POST",
            dataType: "JSON",
            data:{
                csrf_token: _csrf_hash,
                emp_id: $id
            },
            success: function(resp){
                
                arr = [];
                user_data = [];
                $('#dateBtn').datepicker('setStartDate', moment().format("YYYY-MM-DD"));
                $('#dateBtn').datepicker('setEndDate', moment().format("YYYY-MM-DD"));

                if(resp.status !== 0){
                    user_data = resp.data;
        
                    const data_num = user_data.length - 1;
                    if(month > 9){
                        date = $date.getFullYear()+'-'+month+'-'+$date.getDate();
                    }else{
                        date = $date.getFullYear()+'-0'+month+'-'+$date.getDate();
                    }
                    
                    const dates = [];
                    for(var i = 0; i < user_data.length; i++){
                        const data = user_data[i];
                        
                        const user_date = data["Date"].split(" ");
                        dates.push(user_date[0]);

                        if(date === user_date[0]){
                            arr.push({data: data["location"], date: data["Date"], status: 1});
                            if(data["user_locations"] != "" && data["user_locations"] != null){
                                const users_loc = data["user_locations"];
                                for(var q = 0; q < users_loc.length; q++){
                                    arr.push({data: users_loc[q], date: data["Date"], status: 0});
                                }
                            }
                        }
                    }
                    $('#dateBtn').datepicker('setStartDate', dates[0]);
                    $('#dateBtn').datepicker('setEndDate', dates[dates.length - 1]);
                }
                initMapTemp(arr);
            },
			error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection Error");
			}
        });

    }else{
        return false;
    }
}

function initDatePicker(){
    const tempUserPicker = $("#select_user");
    if(typeof tempUserPicker !== "undefined" && tempUserPicker.length == 1){
        tempPicker = tempUserPicker.find("#dateBtn");
        if(typeof tempPicker !== "undefined" && tempPicker.length == 1 && tempPicker.data("datepicker") == null){
            $("#dateBtn").datepicker({
                todayHighlight: true,
                orientation: "bottom right",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: "yy-mm-dd",
                endDate: moment().format("YYYY-MM-DD"),
            }).on("changeDate", function(e) {
                date = $("#dateBtn").datepicker("getDate");
                arr = [];
                if(date){
                    date = moment(date).format("YYYY-MM-DD");
                    if(user_data != null){
                        
                        for(var i = 0; i < user_data.length; i++){
                            const data = user_data[i];
                            const user_date = data["Date"].split(" ");
                            
                            if(date === user_date[0]){
                                arr.push({data: data["location"], date: data["Date"], status: 1});
                                if(data["user_locations"] != "" && data["user_locations"] != null){
                                    const users_loc = data["user_locations"];
                                    for(var qw = 0; qw < users_loc.length; qw++){
                                        arr.push({data: users_loc[qw], date: data["Date"], status: 0});
                                    }
                                }
                            }
                        }
                    }
                    initMapTemp(arr);
                }   
            });
        }
    }
}

$("#search_user").on("change", function(){
    const search = $(this).val();
    $.ajax({
        url: baseUrl("gcctime/app_location/get_app_users"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            search: search
        },
        success: function(resp){
            vm.user_list = resp;
        },
        error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection Error");
        }
    });
});

var vm = new Vue({
  el: '#app_users_list',
  data: {
    user_list: {}
  },
  methods: {
    defaultImage: function ($emp_id, $image) {
        let temp_image = baseUrl('assets/images/profile/no_image.jpg');
        if($image){
            temp_image = baseUrl('uploads/files/images/employee_files/empcode_'+$emp_id+'/'+$image);
        }
      return temp_image;
    },
    selectUser: function($list){
        $('#dateBtn').datepicker('setDate', null);
        sp.sp_data = $list;
        sp.$mount();
    },
    tooltip: function(e, status){
        if(status == 2){
            $(e.target).tooltip({title: "This user is currently active"});
        }
    }
  }
});

var sp =  new Vue({
  el: '#select_user',
  data: {
    sp_data: {}
  },
  methods: {
    Image: function ($emp_id, $image) {
        let temp_image = baseUrl('assets/images/profile/no_image.jpg');
        if($image){
            temp_image = baseUrl('uploads/files/images/employee_files/empcode_'+$emp_id+'/'+$image);
        }
      return temp_image;
    },
    if_empty: function($data){
        if(Object.keys($data).length > 0){
            return 1;
        }else{
            return 0;
        }
    }
  },
  mounted: function(){
    let _this = this;
    let sp_data = _this.sp_data;
    
    setTimeout(function(){
        re_init(sp_data.id);
        initDatePicker();
    },500);
  }
});

let informationTo;
const description = {};
var map;

function initMapTemp(coords = []) {

    const polygonGeo = [];
    const uluru = { lat: 10.7043081, lng: 122.9633338 };
    map = new google.maps.Map(document.getElementById("location_map"), {
        mapId: "61eadc851067d069",
        zoom: 19,
        center: uluru,
        mapTypeId: 'satellite'
    });

    map.addListener("click", function(){
        if (informationTo) {
            informationTo.close();
        }
    });

    var flightPlanCoordinates = [];
    
    var icon = {
        url: baseUrl("/assets/inactive-circle.png"), // url
        scaledSize: new google.maps.Size(25, 25), // scaled size
        origin: new google.maps.Point(0, 0), // origin
        anchor: new google.maps.Point(5, 5) // anchor
    };
    var icon2 = {
        url: baseUrl("/assets/active-circle.png"), // url
        scaledSize: new google.maps.Size(25, 25), // scaled size
        origin: new google.maps.Point(0, 0), // origin
        anchor: new google.maps.Point(5, 5) // anchor
    };
    
    if(coords.length > 1){
        
        var bounds = new google.maps.LatLngBounds();
        
        for(var i = 0;i < coords.length; i++){
            const datetime = coords[i]['date'];
            const time = datetime.split(" ");
            const lat = Number(coords[i]['data']['latitude']);
            const lng =  Number(coords[i]['data']['longitude']);
            const temp_index = i+1;
            const last_index = coords.length;

            flightPlanCoordinates.push({"lat": lat, "lng": lng});
            bounds.extend({"lat": lat, "lng": lng});

            var coords_marker = new google.maps.Marker({
                position: { "lat": lat, "lng": lng },
                map: map,
                draggable: false,
                label: {
                    text: `${temp_index}`,
                    color: last_index==temp_index ? '#FFFFFF' : '#000000',
                },
            });

            if(coords[i]['status'] > 0){
                coords_marker.setIcon(icon2);
            }else{
                coords_marker.setIcon(icon);
            }

            const length = coords.length - 1;

            if(i === length){
                coords_marker.setIcon("");
            }

            coords_marker.addListener('mouseover', function(){
                GetAddress({"lat": lat, "lng": lng}, time[1], map, this);
            });

            coords_marker.addListener('mouseout', function(){
                if(informationTo != null){
                    informationTo.close();
                }
            });
        }
        
        // new google.maps.Polyline({
        //     path: flightPlanCoordinates,
        //     strokeColor: "#FF0000",
        //     strokeOpacity: 1.0,
        //     strokeWeight: 3,
        //     geodesic: true,
        //     map: map
        // });

        map.fitBounds(bounds);

    } else if (coords.length === 1){
        var bounds = new google.maps.LatLngBounds();
        
        const lat = Number(coords[0]['data']['latitude']);
        const lng = Number(coords[0]['data']['longitude']);
        bounds.extend({"lat": lat, "lng": lng});
        const marker = new google.maps.Marker({
            position: { "lat": lat, "lng": lng },
            map: map,
            draggable: false,
        });
        map.fitBounds(bounds);
    } else {
        toastr.warning("No data found...");
    }
}

function GetAddress(latlon, time, map, _this) {
  const travelTo = [];
  var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      location: latlon
    }, function (results, status) {
      const request = {
          placeId: results[1].place_id,
          fields: ["name", "formatted_address", "place_id", "geometry"],
      };
      const service = new google.maps.places.PlacesService(map);
      service.getDetails(request, (place, status) => {
          if (
          status === google.maps.places.PlacesServiceStatus.OK &&
          place &&
          place.geometry &&
          place.geometry.location
          ) {
            Object.assign(description, {"place": place.name});
            if (informationTo) {
                informationTo.close();
            }
            informationTo = new google.maps.InfoWindow({
                content: '<strong>Time:</strong> '+time+'<br><strong>Location:</strong> '+place.name
            });
            informationTo.open(map, _this);
          }
      });
    });
}

</script>