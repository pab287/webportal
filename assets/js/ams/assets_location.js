const pointer = {};
const pointer_name = {};
let map;

function initMap(){
    uluru = {lat: 12.8797, lng: 121.7740};
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            uluru = {lat: position.coords.latitude, lng: position.coords.longitude};
        });
    } else {
        console.log("Geolocation is not supported by this browser.");
    }

    var mapOptions = {
        // mapId: "c38a738bd984de98",
        zoom: 6,
        center: uluru,
        mapTypeId: 'satellite'
    };
    var map = new google.maps.Map(document.getElementById('map'), mapOptions);

    $.ajax({
        url: baseUrl("ams/assetperlocation/get_assets_location"),
        dataType: 'json',
        type: 'get',
        success: function(response){
            $.each(response, function(index, item){
                var position = new google.maps.LatLng(item.latitude, item.longitude);
                const marker = new google.maps.Marker({
                    position,
                    map,
                });

                if(item.assets){
                    var url = baseUrl('ams/Assetperlocation/masterfile') + '?locationId='+item.id; 
                    var information = new google.maps.InfoWindow({
                        content: '<br><p style="margin: 0"><b>'+item.assets+'</b> assets found in <b>'+item.location.toUpperCase()+'</b>.</p><div style="text-align:center"><a href="'+url+'" target="_blank">Browse Assets Here</a></div>'
                    });
    
                    marker.addListener('click', function(){
                        information.open(map, marker);
                    });
        
                    Object.assign(pointer_name,{"name": information});
                    Object.assign(pointer,{"name": marker});
                }else{
                    var information = new google.maps.InfoWindow({
                        content: '<br><p>No assets found in <b>'+item.location.toUpperCase()+'</b>.</p>'
                    });

                    marker.addListener('click', function(){
                        information.open(map, marker);
                    });

                    Object.assign(pointer_name,{"name": information});
                    Object.assign(pointer,{"name": marker});
                }

            });
        }
    });
}