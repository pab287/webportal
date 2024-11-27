$(".travelOrderFromIcon .icon").on("click", function(){
  if($(".travelOrderOptionFrom").is(":visible")){
    $(".travelOrderOptionFrom").hide();
  }else{
    $(".travelOrderOptionFrom").show();
  }

  $.ajax({
    url: baseUrl("eforms/travel_order/sites_options/"),
    type: "GET",
    dataType: "JSON",
    success: function(resp){
      if(resp.length > 0){
        vmTab3.checker = true;
      }else{
        vmTab3.checker = false;
      }
      vmTab3.vm_tab3 = Object.assign({}, resp);
    }
  });
});

var vmTab3 = new Vue({
  el: "#travel_option_from",
  data: { 
    vm_tab3: [], checker: false,
  },
  methods: {
    selectedSite: function (id, input_id) {
      $.ajax({
        url: baseUrl("eforms/travel_order/site_selected/"),
        type: "POST",
        data: {
          csrf_token: _csrf_hash,
          sites_id: id,
        },
        dataType: "JSON",
        success: function(resp){
          const coords = {"lat": Number(resp.latitude), "lng": Number(resp.longtitude)};
          if(resp.site_name != null){
            $("#travelFrom").val(resp.site_name);
            Object.assign(markersName,{"fromTitle": resp.site_name});
            $("#formNewTravelFrom").val(JSON.stringify(coords));
            if($(".travelOrderOptionFrom").is(":visible")){
              $(".travelOrderOptionFrom").hide();
            }else{
              $(".travelOrderOptionFrom").show();
            }
            const bounds = new google.maps.LatLngBounds();
            let fromMarker;
            if(pointer.from != null){
              pointer.from.setMap(null);
              fromMarker = new google.maps.Marker();
              fromMarker.setPosition(coords);
              fromMarker.setMap(map);
              Object.assign(pointer,{"from": fromMarker});
              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });
              informationFrom.open(map, fromMarker);
            }else{
              fromMarker = new google.maps.Marker();
              fromMarker.setPosition(coords);
              fromMarker.setMap(map);
              Object.assign(pointer,{"from": fromMarker});
              map.setCenter(coords);
              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });
              informationFrom.open(map, fromMarker);
            }
            const from_marker = pointer.from;
            const from_title = markersName.fromTitle;
            const to_marker = pointer.to;
            const to_title = markersName.toTitle;
            if(Object.keys(pointer).length == 2){
              if(from_marker != null && from_title != null){
                  bounds.extend(from_marker.getPosition());
                  map.fitBounds(bounds);
                  var informationFrom = new google.maps.InfoWindow({
                      content: '<h6>From</h6><br><p>'+String(from_title).toUpperCase()+'</p>'
                  });
                  informationFrom.open(map, from_marker);
              }
              if(to_marker != null && to_title != null){
                  bounds.extend(to_marker.getPosition());
                  map.fitBounds(bounds);
                  var informationFrom = new google.maps.InfoWindow({
                      content: '<h6>To</h6><br><p>'+String(to_title).toUpperCase()+'</p>'
                  });
                  informationFrom.open(map, to_marker);
              }
            }
          } 
        }
      });
    }
  }
});

$(".travelOrderToIcon .icon").on("click", function(){
  if($(".travelOrderOptionTo").is(":visible")){
    $(".travelOrderOptionTo").hide();
  }else{
    $(".travelOrderOptionTo").show();
  }

  $.ajax({
    url: baseUrl("eforms/travel_order/sites_options/"),
    type: "GET",
    dataType: "JSON",
    success: function(resp){
      if(resp.length > 0){
        vmTab2.checker = true;
      }else{
        vmTab2.checker = false;
      }
      vmTab2.vm_tab2 = Object.assign({}, resp);
    }
  });
});



var vmTab2 = new Vue({
  el: "#travel_option_to",
  data: { 
    vm_tab2: [], checker: false,
  },
  methods: {
    selectedSite: function (id) {
      $.ajax({
        url: baseUrl("eforms/travel_order/site_selected/"),
        type: "POST",
        data: {
          csrf_token: _csrf_hash,
          sites_id: id,
        },
        dataType: "JSON",
        success: function(resp){
          const coords = {"lat": Number(resp.latitude), "lng": Number(resp.longtitude)};
          if(resp.site_name != null){
            $("#travelTo").val(resp.site_name);
            Object.assign(markersName,{"toTitle": resp.site_name});
            $("#formNewTravelTo").val(JSON.stringify(coords));
            if($(".travelOrderOptionTo").is(":visible")){
              $(".travelOrderOptionTo").hide();
            }else{
              $(".travelOrderOptionTo").show();
            }
            const bounds = new google.maps.LatLngBounds();
            let toMarker;
            if(pointer.to != null){
              pointer.to.setMap(null);
              toMarker = new google.maps.Marker();
              toMarker.setPosition(coords);
              toMarker.setMap(map);
              Object.assign(pointer,{"to": toMarker});
              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name)+'</p>'
              });
              informationFrom.open(map, toMarker);
            }else{
              toMarker = new google.maps.Marker();
              toMarker.setPosition(coords);
              toMarker.setMap(map);
              Object.assign(pointer,{"to": toMarker});
              map.setCenter(coords);
              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name)+'</p>'
              });
              informationFrom.open(map, toMarker);
            }
            const from_marker = pointer.from;
            const from_title = markersName.fromTitle;
            const to_marker = pointer.to;
            const to_title = markersName.toTitle;
          if(Object.keys(pointer).length == 2){
              if(from_marker != null && from_title != null){
                bounds.extend(from_marker.getPosition());
                map.fitBounds(bounds);
                var informationFrom = new google.maps.InfoWindow({
                    content: '<h6>From</h6><br><p>'+String(from_title)+'</p>'
                });
                informationFrom.open(map, from_marker);
              }
              if(to_marker != null && to_title != null){
                bounds.extend(to_marker.getPosition());
                map.fitBounds(bounds);
                var informationFrom = new google.maps.InfoWindow({
                    content: '<h6>To</h6><br><p>'+String(to_title)+'</p>'
                });
                informationFrom.open(map, to_marker);
              }
            }
          } 
          }
      });
    }
  }
});