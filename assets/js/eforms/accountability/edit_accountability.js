var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

param_id = getUrlParameter('id');

$.ajax({
    url: baseUrl("eforms/accountability/content_detail/") + param_id,
    type: "get",
    success: function (data) {
        if (data.is_urgent == 1) {
            $("#urgent").prop('checked', true);
        }

        if (data.is_contract == 0) {
            var issued_to = new Option(data.display_name, data.issued_to, true, true);
            $('#issued_to').append(issued_to).trigger('change');
            $('#issued_to').removeAttr("disabled");
            $('#contractor').attr("disabled", "disabled");

        } else {
            var contractor = new Option(data.contractor, data.issued_to, true, true);
            $('#contractor').append(contractor).trigger('change');
            $('#con_but').hide();
            $('#emp_but').show();
            $('#contractor').removeAttr("disabled");
            $('#issued_to').attr("disabled", "disabled");
            $('#contract_check').val(1);
        }
        $("#issue_dt").val(moment(data.date_issued).format("MM/DD/Y"));
        $("#company_to").val(data.company);
        $("#department_to").val(data.department);
    }
})

$("#issued_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    ajax: {
        url: baseUrl("eforms/accountability/issued_to_lookup"),
        dataType: "json",
        global: false,
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

$("#issued_to").on("select2:select", function () {
    $.ajax({
        type: "GET",
        data: { data: $("#issued_to option:selected").attr("value") },
        url: baseUrl("eforms/accountability/get_file_under"),
        dataType: "json",
        success: function (json) {
            $("#company_to").val(json.company);
            $("#department_to").val(json.department);
        }
    });
});

$("#contractor").select2({
    placeholder: 'Select. .',
    width: '100%',
    ajax: {
        url: baseUrl("eforms/accountability/contractor_lookup"),
        dataType: "json",
        global: false,
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

$("#contractor").on("select2:select", function () {
    $("#company_to").val("CONTRACTOR");
    $("#department_to").val("CONTRACTOR");
});

$('#issue_dtpicker').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickTime: false,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    maxView: 4,
    minView: 2,
    format: 'mm/dd/yyyy',
});

$('#emp_but').hide();
$('#con_but').on("click", function () {
    $('#con_but').hide();
    $('#emp_but').show();
    $('#contractor').removeAttr("disabled");
    $('#issued_to').attr("disabled", "disabled");
    $('#contract_check').val(1);
    $('#issued_to').val("");
    $('#issued_to').text("");
});

$('#emp_but').on("click", function () {
    $('#emp_but').hide();
    $('#con_but').show();
    $('#issued_to').removeAttr("disabled");
    $('#contractor').attr("disabled", "disabled");
    $('#contract_check').val(0);
    $('#contractor').val("");
    $('#contractor').text("");
});

$("#add_asset_modal").hide();
$("#add_vehicle_modal").hide();
$("#add_multiple_modal").hide();
$("#edit_asset_modal").hide();
$("#delete_asset_modal").hide();
$("#clear_asset_modal").hide();

var search_val = "";
var tblTemp = $("#tbltemp").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    bPaginate: false,
    bInfo: true,
    ajax: {
        url: baseUrl("eforms/accountability/content_body_detail/") + param_id,
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: false,
    columns: [
        { data: "asset_code", width: "15%" },
        {
            data: "description", width: "40%", render: function (data, type, row, meta) {
                return descriptionDetail(row.description, row.brand, row.modelno, row.serialno, row.plateno, row.engineno, row.chasisno, row.type, row.desc);
            }
        },
        { data: "remarks" },
        { data: "amount", width: "10%", className: "text-right" },
        { data: null, width: "10%", className: "text-center" },
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

function descriptionDetail($desc, $brand, $model, $serial, $plateno, $engineno, $chasisno, $type, $desc_det) {
    if ($brand == '' || $brand == null) {
        $brand = 'N/A';
    }
    if ($model == '' || $model == null) {
        $model = 'N/A';
    }
    if ($serial == '' || $serial == null) {
        $serial = 'N/A';
    }
    if ($type == 'Asset') {
        return '<b>' + $desc + '</b><br>Description: ' + $desc_det + '<br>Brand: ' + $brand + '<br>Model: ' + $model + '<br>Serial: ' + $serial;
    } else {
        return '<b>' + $desc + '</b><br>Description: ' + $desc_det + '<br>Plate no.: ' + $plateno + '<br>Engine no.: ' + $engineno + '<br>Chasis no.: ' + $chasisno;
    }
}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if ($.inArray("edit", _currentActions) !== -1) {
            _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_asset_temp(" + $id + ")' data-toggle='modal' data-target='#edit_asset_modal' ><i class='la la-pencil-square'></i></button>";
        }
        if ($.inArray("delete", _currentActions) !== -1) {
            _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='delete_temp(" + $id + ")' data-toggle='modal' data-target='#delete_asset_modal'><i class='la la-trash'></i></button>";
        }
        _actionButton = _actionButton ? _actionButton : "---";
        return _actionButton;
    } else {
        return false;
    }
}

function delete_temp($id) {
    $.validate({
        form: '#delete_asset_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/accountability/delete") + "/" + $id,
                type: "POST",
                dataType: "json",
                data: $("#delete_asset_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if (json.response) {
                        $('#delete_asset_modal').modal('hide');
                        tblTemp.ajax.reload();
                        $("#tbladdedlist").DataTable({ destroy: true, data: true });
                        toastr[json.state](json.toastr_msg, "Removed successfully!", { timeOut: 5000 });
                    } else {
                        toastr[json.state](json.toastr_msg, "Error removing item!", { timeOut: 5000 });
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

function edit_asset_temp($id) {
    $.ajax({
        url: baseUrl("eforms/accountability/edit_details/") + $id,
        type: 'GET',
        success: function (data) {
            $("#edit_asset_id").val(data.id);
            $("#edit_asset_cost").val(data.amount);
            $("#edit_asset_qty").val(data.quantity);
            $("#edit_asset_code").val(data.asset_code);
            $("#edit_asset_name").text(data.description);
            $("#edit_asset_desc").text(data.description + '\nBrand: ' + data.brand + '\nModel: ' + data.modelno + '\nSerial: ' + data.serialno);
            $("#edit_asset_remarks").text(data.remarks);
            return data;
        }
    });

    $.validate({
        form: '#edit_asset_form',
        lang: 'en',
        onSuccess: function (form) {
            var disabled = $('#edit_asset_form').find('textarea:disabled, input:disabled').removeAttr('disabled');
            $.ajax({
                url: baseUrl("eforms/accountability/edit_asset/") + $id,
                type: "POST",
                dataType: "json",
                data: $("#edit_asset_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#edit_asset_modal').modal('hide');
                        $('#edit_asset_form')[0].reset();
                        tblTemp.ajax.reload();
                        disabled.attr('disabled', 'disabled');
                        toastr.success(data.toastr_msg, "Updated Successfully!", 5000);
                    } else {
                        disabled.attr('disabled', 'disabled');
                        toastr.error(data.toastr_msg, "Error updating item!", 5000);
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

$("#clear_temp").on("click", function () {
    $.validate({
        form: '#clear_asset_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/accountability/clear/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#clear_asset_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#clear_asset_modal').modal('hide');
                        tblTemp.ajax.reload();
                        toastr.success(data.toastr_msg, "Removed successfully!", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Error removing items!", 5000);
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
});

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblTemp.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblTemp.ajax.reload();
});

$("#tblassetcomp").DataTable({
    dom: '<"toolbar">frtlip',
    searching: false,
    destroy: true
});

$("#tblnewasset").DataTable({
    dom: '<"toolbar">frtlip',
    searching: false,
});

$("#tblnewvehicle").DataTable({
    dom: '<"toolbar">frtlip',
    searching: false,
});

$("#tblvehiclecomp").DataTable({
    dom: '<"toolbar">frtlip',
    searching: false,
});

$("#tblassetcomp_edit").DataTable({
    dom: '<"toolbar">frtlip',
    searching: false,
});

$("#tbladdedlist").DataTable({
    dom: '<"toolbar">frtlip',
    searching: false,
});

$("#asset_search").on("click", function () {
    var code = $("#asset").val();
    var tblNewAsset = $("#tblnewasset").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        destroy: true,
        ajax: {
            url: baseUrl("eforms/accountability/new_asset_temp/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.code = code;
                d.accountability_id = param_id;
            }
        },
        searching: false,
        columns: [
            {
                data: "assetacode", render: function (data, type, row, meta) {
                    return assetCode(row.assetacode, row.is_borrowed);
                }
            },
            {
                data: "name", render: function (data, type, row, meta) {
                    return assetName(row.name, row.is_borrowed);
                }
            },
            { data: "assetname" },
            { data: null, width: "5%", className: "text-center" },
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return assetDatatableActions(row.id, row.isComponent, row.assetacode, row.name);
                },
            }
        ]
    });

    function assetDatatableActions($id, $component, $assetcode, $name) {
        if ($id) {
            if ($assetcode && $name) {
                var _actionButton = "";
                _actionButton += " <button type='button' onclick='getAssetDetail(" + $id + ', ' + $component + ")' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-search'></i></button>";
                return _actionButton;
            } else {
                var _actionButton = "";
                _actionButton += " <button type='button' onclick='noAssetCode()' class='btn m-btn btn-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-search'></i></button>";
                return _actionButton;
            }
        } else {
            return false;
        }
    }
});

function getAssetDetail($id, $component) {
    $.ajax({
        url: baseUrl('eforms/accountability/get_asset_detail/') + $id,
        type: "get",
        success: function (data) {
            $("#asset_id").val(data.id);
            $("#asset_cost").val(data.purchaseprice);
            $("#asset_qty").val(1);
            $("#asset_code").val(data.assetacode);
            $("#asset_name").text(data.name);
            $("#asset_desc").text(data.assetname + '\nBrand: ' + data.brand + '\nModel: ' + data.modelno + '\nSerial: ' + data.serialno);
            return data;
        }
    });

    var tblAssetComp = $("#tblassetcomp").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        destroy: true,
        ajax: {
            url: baseUrl("eforms/accountability/asset_comp_temp/") + $id,
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        searching: false,
        columns: [
            {
                data: "assetacode", render: function (data, type, row, meta) {
                    return assetCode(row.assetacode, row.is_borrowed);
                }
            },
            {
                data: "name", render: function (data, type, row, meta) {
                    return assetName(row.name, row.is_borrowed);
                }
            },
            { data: "assetname" }
        ],
    });
}

function assetCode(assetCode, isBorrowed) {
    if (isBorrowed == 0) {
        if (assetCode == '' || assetCode == null) {
            return "<span class='m--font-danger'>No asset code</span>";
        } else {
            return assetCode;
        }
    }
}

function assetName(name, isBorrowed) {
    if (isBorrowed == 0) {
        if (name == '' || name == null) {
            return "<span class='m--font-danger'>No asset name</span>";
        } else {
            return name;
        }
    }
}

$("#vehicle_search").on("click", function () {
    var code = $("#vehicle").val();
    var tblNewVehicle = $("#tblnewvehicle").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        destroy: true,
        ajax: {
            url: baseUrl("eforms/accountability/new_vehicle_temp/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.code = code;
                d.accountability_id = param_id;
            }
        },
        searching: false,
        columns: [
            {
                data: "gen_code", render: function (data, type, row, meta) {
                    return vehicleCode(row.gen_code, row.is_borrowed);
                }
            },
            {
                data: "name", render: function (data, type, row, meta) {
                    return vehicleName(row.name, row.is_borrowed);
                }
            },
            { data: "description" },
            { data: "plateno" },
            { data: null, width: "5%", className: "text-center" },
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return assetDatatableActions(row.id, row.isCompo, row.gen_code, row.name);
                },
            }
        ]
    });

    function assetDatatableActions($id, $component, $assetcode, $name) {
        if ($id) {
            if ($assetcode && $name) {
                var _actionButton = "";
                _actionButton += " <button type='button'  onclick='getVehicleDetail(" + $id + ', ' + $component + ")'  class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-search'></i></button>";
                return _actionButton;
            } else {
                var _actionButton = "";
                _actionButton += " <button type='button' onclick='noAssetCode()' class='btn m-btn btn-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-search'></i></button>";
                return _actionButton;
            }
        } else {
            return false;
        }
    }
});

function vehicleCode(assetCode, isBorrowed) {
    //if(isBorrowed==0){
    if (assetCode == "" || assetCode == null) {
        return "<span class='m--font-danger'>No asset code</span>";
    } else {
        return assetCode;
    }
    //}
}

function vehicleName(name, isBorrowed) {
    //if(isBorrowed==0){
    if (name == "" || name == null) {
        return "<span class='m--font-danger'>No asset name</span>";
    } else {
        return name;
    }
    //}
}

function getVehicleDetail($id, $component) {
    $.ajax({
        url: baseUrl('eforms/accountability/get_vehicle_detail/') + $id,
        type: "get",
        success: function (data) {
            $("#vehicle_id").val(data.id);
            $("#vehicle_cost").val(data.purchaseprice);
            $("#vehicle_qty").val(1);
            $("#vehicle_code").val(data.gen_code);
            $("#vehicle_name").text(data.name);
            $("#vehicle_desc").text(data.description + '\nBrand: ' + data.brand + '\nModel: ' + data.model + '\nPlate No.: ' + data.plateno);
            return data;
        }
    });

    if ($component == 0) {
        var tblVehicleComp = $("#tblvehiclecomp").DataTable({
            dom: '<"toolbar">frtlip',
            serverSide: true,
            processing: true,
            destroy: true,
            ajax: {
                url: baseUrl("eforms/accountability/vehicle_comp_temp/") + $id,
                type: "post",
                dataType: "json",
                data: function (d) {
                    d.csrf_token = _csrf_hash,
                        d.search['value'] = search_val
                }
            },
            searching: false,
            columns: [
                {
                    data: "gen_code", render: function (data, type, row, meta) {
                        return vehicleCode(row.gen_code, row.is_borrowed);
                    }
                },
                {
                    data: "name", render: function (data, type, row, meta) {
                        return vehicleName(row.name, row.is_borrowed);
                    }
                },
                { data: "description" }
            ],
        });
    }
}

$("#multiple_search").on("click", function () {
    var code = $("#multiple").val();
    var tblMultiple = $("#tblmultiple").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        destroy: true,
        ajax: {
            url: baseUrl("eforms/accountability/multiple_temp/"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.code = code;
                d.accountability_id = param_id;
            }
        },
        searching: false,
        columns: [
            {
                data: "assetacode", render: function (data, type, row, meta) {
                    return assetCode(row.assetacode, row.is_borrowed);
                }
            },
            {
                data: "name", render: function (data, type, row, meta) {
                    return assetName(row.name, row.is_borrowed);
                }
            },
            { data: "assetname" },
            { data: null, width: "5%", className: "text-center" },
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return multipleDatatableActions(row.id, row.isComponent, row.assetacode, row.name);
                },
            }
        ]
    });
});

var tblMultiple = $("#tblmultiple").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    destroy: true,
    ajax: {
        url: baseUrl("eforms/accountability/multiple_temp/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.code = $("#mutliple").val() || "";
            d.accountability_id = param_id;
        }
    },
    searching: false,
    columns: [
        {
            data: "assetacode", render: function (data, type, row, meta) {
                return assetCode(row.assetacode, row.is_borrowed);
            }
        },
        {
            data: "name", render: function (data, type, row, meta) {
                return assetName(row.name, row.is_borrowed);
            }
        },
        { data: "assetname" },
        { data: null, width: "5%", className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return multipleDatatableActions(row.id, row.isComponent, row.assetacode, row.name);
            },
        }
    ]
});

function multipleDatatableActions($id, $component, $assetcode, $name) {
    if ($id) {
        if ($assetcode && $name) {
            var _actionButton = "";
            _actionButton += " <button onclick='multipleAssetDetail(" + $id + ', ' + $component + ")' type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-plus'></i></button>";
            return _actionButton;
        } else {
            var _actionButton = "";
            _actionButton += " <button type='button' onclick='noAssetCode()' class='btn m-btn btn-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-ban'></i></button>";
            return _actionButton;
        }
    } else {
        return false;
    }
}

function multipleAssetDetail($id, $component) {
    $.ajax({
        url: baseUrl('eforms/accountability/get_asset_detail/') + $id,
        type: "get",
        success: function (data) {
            if (data) {
                $.ajax({
                    url: baseUrl("eforms/accountability/save_multiple_edit/") + param_id,
                    formData: { csrf_token: _csrf_hash },
                    data: {
                        id: data.id,
                        price: data.purchaseprice,
                        qty: 1,
                        asset_code: data.assetacode,
                        name: data.name
                    },
                    dataType: "json",
                    beforeSend: function () {
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (data) {
                        state = data.state;
                        tblAddedList.ajax.reload();
                        tblTemp.ajax.reload();
                        if (state == "Warning!") {
                            toastr.warning(data.message, "Warning", 5000);
                        } else if (state == "Success!") {
                            toastr.success("", data.message, 5000);
                        } else if (state == "Error!") {
                            toastr.error(data.message, "Error", 5000);
                        }
                        $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                    }
                });
            }
            return data;
        }
    });

    var tblAddedList = $("#tbladdedlist").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        destroy: true,
        ajax: {
            url: baseUrl("eforms/accountability/content_body_detail/") + param_id,
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        searching: false,
        columns: [
            { data: "asset_code", width: "20%" },
            {
                data: "description", render: function (data, type, row, meta) {
                    return descriptionDetail(row.description, row.brand, row.modelno, row.serialno, row.plateno, row.engineno, row.chasisno, row.type);
                }
            },
            { data: "amount", width: "20%", className: "text-right" },
            { data: null, width: "5%", className: "text-center" },
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return addedListDatatableActions(row.id);
                },
            }
        ]
    });

    function addedListDatatableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton += " <button type='button' onclick='delete_temp(" + $id + ")' data-toggle='modal' data-target='#delete_asset_modal' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='fa fa-remove'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }
}

function noAssetCode() {
    toastr.error("Notification: No asset code/name found!");
}

$.validate({
    form: '#add_asset_form',
    lang: 'en',
    onSuccess: function (form) {
        var disabled = $('#add_asset_form').find('textarea:disabled, input:disabled').removeAttr('disabled');
        $.ajax({
            url: baseUrl("eforms/accountability/save_asset_edit/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#add_asset_form").find("input,select,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                // console.log(data);
                state = data.state;
                $('#add_asset_modal').modal('hide');
                $('#add_asset_form')[0].reset();
                $('#asset_name').text("");
                $('#asset_desc').text("");
                tblTemp.ajax.reload();
                disabled.attr('disabled', 'disabled');
                if (state == "Warning!") {
                    toastr.warning(data.message, "Warning", 5000);
                } else if (state == "Success!") {
                    toastr.success("", data.message, 5000);
                } else if (state == "Error!") {
                    toastr.error(data.message, "Error", 5000);
                }
                $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#add_vehicle_form',
    lang: 'en',
    onSuccess: function (form) {
        var disabled = $('#add_vehicle_form').find('textarea:disabled, input:disabled').removeAttr('disabled');
        $.ajax({
            url: baseUrl("eforms/accountability/save_vehicle_edit/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#add_vehicle_form").find("input,select,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                state = data.state;
                $('#add_vehicle_modal').modal('hide');
                $('#add_vehicle_form')[0].reset();
                $('#vehicle_name').text("");
                $('#vehicle_desc').text("");
                tblTemp.ajax.reload();
                disabled.attr('disabled', 'disabled');
                if (state == "Warning!") {
                    toastr.warning(data.message, "Warning", 5000);
                } else if (state == "Success!") {
                    toastr.success("", data.message, 5000);
                } else if (state == "Error!") {
                    toastr.error(data.message, "Error", 5000);
                }
                $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#frm_status_new',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/save_edit/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#frm_status_new").find("input,select,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.toastr_status != "error") {
                    window.location.href = baseUrl("eforms/accountability/view_accountability?id=") + param_id;
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});