var search_val = "";
const pointer = {};
const pointer_name = {};
const markersName = {};
const inputGlob = [];
let map;


var tblLocation = $("#table-location").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/maintenance/get_location_collection"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    searching: true,
    columns: [
        {data: "location", width: "90%"},
        {data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,

            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            },
        }
    ]
});


function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   onclick='edit_location(" + $id + ")'" +
            "   data-toggle='m-tooltip'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-placement='bottom'" +
            "   data-original-title='Edit'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</button>";
        _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' " +
            "   onclick='open_delete(" + $id + ")'" +
            "   data-toggle='m-tooltip'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-placement='bottom'" +
            "   data-original-title='Delete'>" +
            "   <i class='la la-trash'></i>" +
            "</button>";
        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblLocation.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblLocation.ajax.reload();
});

function open_location() {
    save_method = 'add';
    $('#form_location')[0].reset();
    $("#form_location #locationCoords").val('');
    $('#modal_form_location').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Location'); // Set Title to Bootstrap modal title

    if(!jQuery.isEmptyObject(pointer) && !jQuery.isEmptyObject(pointer_name)){
        initMap(true);
    }
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function edit_location(id) {

    save_method = 'update';
    $('#form_location')[0].reset();
    $.ajax({
        url: baseUrl("ams/maintenance/edit_location/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="location"]').val(data.location);
            $('#modal_form_location').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Location'); // Set Title to Bootstrap modal title

            if(data.latitude != 0 && data.longitude != 0){
                const coordinate = JSON.stringify({lat: data.latitude, lng: data.longitude});
                $("#form_location #locationCoords").val(coordinate);
                
                initMap(false, data.latitude, data.longitude, data.location);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function delete_location() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("ams/maintenance/delete_location/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: {csrf_token: _csrf_hash},
        success: function (data) {
            //if success reload ajax table
            tblLocation.ajax.reload();
            $("#modal_form_delete").modal("hide");
            toastr.success("Location was successfully deleted.", "Location Deleted.", 10000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error("A problem occurred.", "Error", 10000);
        }
    });


}

function save_location() {
    var url;

    let msg = null;
    if (save_method == 'add') {
        url = baseUrl("ams/maintenance/add_location/");
        msg = {
            title: "New Location Saved",
            message: "New Location was successfully saved."
        };
    } else {
        url = baseUrl("ams/maintenance/update_location/");
        msg = {
            title: "Location Updated",
            message: "New Location was successfully saved."
        };
    }


    $.validate({
        form: '#form_location',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_location').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblLocation.ajax.reload();
                        $("#modal_form_location").modal("hide");
                        toastr.success(msg.message, msg.title, 10000);
                    } else {
                        toastr.error("A problem occurred.", "Error", 10000);
                    }

                    if(!jQuery.isEmptyObject(pointer) && !jQuery.isEmptyObject(pointer_name)){
                        initMap(true);
                    }
                }
            });
            return false;
        },
    });
}

function initMap(destroy = false, lat = null, lng = null, place = null){
    let input;
    let coordsInput;
    let title;
    let uluru;
    uluru = {lat: 10.704365710388265, lng: 122.96319995137281};

    if(destroy){
        pointer_name.name.setMap(null);
        pointer.name.setMap(null);
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            uluru = {lat: position.coords.latitude, lng: position.coords.longitude};
        });
    } else {
        console.log("Geolocation is not supported by this browser.");
    }

    var mapOptions = {
        mapId: "c38a738bd984de98",
        zoom: 19,
        center: uluru,
        mapTypeId: 'satellite'
    };
    var map = new google.maps.Map(document.getElementById('map'), mapOptions);

    $("#location").on('focus', function () {
        input = "location";
        title = '';
        coordsInput = "locationCoords";
        searchMap(input, coordsInput, title, map);
    });

    if(lat && lng){
        
        var position = new google.maps.LatLng(lat, lng);
        const marker = new google.maps.Marker({
            position,
            map,
        });
        map.setCenter(position);

        var information = new google.maps.InfoWindow({
            content: '<br><p>'+place.toUpperCase()+'</p>'
        });
        information.open(map, marker);

        Object.assign(pointer_name,{"name": information});
        Object.assign(pointer,{"name": marker});

        map.addListener("click", (event) => {
            editAddedMarker(event.latLng, map);

            $("#location").focus();
        });
    }else{
        map.addListener("click", (event) => {
            addMarker(event.latLng, map);
            $("#location").focus();
        });
    }
}

function searchMap(inputField, coordsField, title, map){
    const searchInput = document.getElementById(inputField);
    const searchBox = new google.maps.places.SearchBox(searchInput);

    map.addListener("bounds_changed", () => {
        searchBox.setBounds(map.getBounds());
    });

    searchBox.addListener("places_changed", () => {
        const places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        $("#"+coordsField).val(JSON.stringify(places[0].geometry.location));

        if(Object.keys(pointer).length < 2){
            if(Object.keys(pointer).length === 1){
                if(inputField === "thisLocation" && typeof pointer.name != "undefined"){
                    pointer_name.name.setMap(null);
                    pointer.name.setMap(null);
                }
            }
        }else{
            pointer_name.name.setMap(null);
            pointer.name.setMap(null);
        }

        siteMarkers(inputField, title, places[0].name, places[0].geometry.location, map);
    });
}

function siteMarkers (inputField, title, placeName, coords, map, funcname = ""){
    const bounds = new google.maps.LatLngBounds();
    const newMarker = new google.maps.Marker();
    newMarker.setPosition(coords);
    newMarker.setMap(map);

    if(!jQuery.isEmptyObject(pointer) && !jQuery.isEmptyObject(pointer_name)){
        pointer_name.name.setMap(null);
        pointer.name.setMap(null);
    }

    if(typeof inputField !== 'undefined' && inputField){
        // var information = new google.maps.InfoWindow();
        var information = new google.maps.InfoWindow({
            content: '<br><p>'+placeName.toUpperCase()+'</p>'
        });

        bounds.extend(newMarker.position);
        information.open(map, newMarker);
        Object.assign(pointer_name,{"name": information});
        Object.assign(pointer,{"name": newMarker});
    }

    const _name = pointer.name;
    const _title = markersName.title;

    if(funcname != ""){
        if(Object.keys(pointer).length != 0){
            if(typeof inputField !== 'undefined'  && inputField){
                _name.addListener('click', function(){
                    information.open(map, _name);
                });
            }
        }
    }else{
        if(Object.keys(pointer).length != 0){
            if(typeof inputField !== 'undefined'  && inputField){
                newMarker.addListener('click', function(){
                    information.open(map, newMarker);
                });
            }
        }
    }

    if(Object.keys(pointer).length == 2){
        if(_name != null){
            pointer_name.name.setMap(null);
            bounds.extend(_name.getPosition());
            map.fitBounds(bounds);
            var information = new google.maps.InfoWindow({
                content: '<br><p>'+placeName.toUpperCase()+'</p>'
            });
            information.open(map, _name);
            Object.assign(pointer_name,{"name": information});
        }
    }
}

function addMarker(position, map){
    if(!jQuery.isEmptyObject(pointer) && !jQuery.isEmptyObject(pointer_name)){
        pointer_name.name.setMap(null);
        pointer.name.setMap(null);
    }

    const marker = new google.maps.Marker({
        position,
        map,
    });

    var information = new google.maps.InfoWindow();

    $("#locationCoords").val(JSON.stringify(position));
    // information.open(map, marker);
    Object.assign(pointer_name,{"name": information});
    Object.assign(pointer,{"name": marker});
}

function editAddedMarker(position, map){
    if(!jQuery.isEmptyObject(pointer) && !jQuery.isEmptyObject(pointer_name)){
        pointer_name.name.setMap(null);
        pointer.name.setMap(null);
    }

    const marker = new google.maps.Marker({
        position,
        map,
    });

    $("#locationCoords").val(JSON.stringify(position));

    var information = new google.maps.InfoWindow();
    Object.assign(pointer_name,{"name": information});
    Object.assign(pointer,{"name": marker});
}