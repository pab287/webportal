var data = [
    {
        id: "internal",
        text: "INTERNAL"
    },
    {
        id: "external",
        text: "EXTERNAL"
    }
];

$("#type").select2({
    placeholder: 'Select. .',
    width: '100%',
    data: data
});


var data2 = [
    {
        id: "Normal",
        text: "Normal"
    },
    {
        id: "Important",
        text: "Important"
    }
];

$("#priority").select2({
    placeholder: 'Select. .',
    width: '100%',
    data: data2
});

var file_under = $("#select2_file").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("eforms/transmittal/get_company_collection"),
        global: false,
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});


var department = $("#select2_dep").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("eforms/transmittal/get_department_collection"),
        global: false,
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

var requested_by = $("#select2_requested").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
        url: baseUrl("eforms/transmittal/get_request_collection"),
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

var deliver = $("#select2_deliver").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
        url: baseUrl("eforms/transmittal/get_request_collection"),
        global: false,
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

var vehicle = $("#select2_vehicle").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
        url: baseUrl("eforms/transmittal/get_vehicle_collection"),
        global: false,
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

function emp_details() {
    var emp = $('[name="deliver_to"]').val();
    $.ajax({
        url: baseUrl('eforms/Transmittal/ajax_emp_details/') + emp,
        type: "POST",
        dataType: "JSON",
        data: { csrf_token: _csrf_hash },
        success: function (data) {
            var _compDisplay = (parseInt(data.use_str) === 1) ? data.comp_str : data.company_id;
            var _deptDisplay = (parseInt(data.temp_dep_str) === 0) ? data.dep_str : data.department_id;
            var _posDisplay = (parseInt(data.temp_pos_str) === 0) ? data.pos_str : data.position;

            $('[name="deliver_company"]').val(data.company_id + '\n' + data.department_id + '\n' + data.position);

            var tempDisplay = _compDisplay + '\n' + _deptDisplay + '\n' + _posDisplay;
            $('#deliver_company_display').val(tempDisplay);

            /*** if (parseInt(data.use_str) === 1) {
                $('#deliver_company_display').val(data.comp_str + '\n' + data.dep_str + '\n' + data.pos_str);
            } else {
                $('#deliver_company_display').val(data.company_id + '\n' + data.department_id + '\n' + data.position);
            } ***/

            $('[name="deliver_address"]').val(data.company_address);
        }, error: function (jqXHR, textStatus, errorThrown) {
            alert('Error: "ajax_emp_details"');
        }
    });
}

function veh_details() {
    var veh = $('[name="vehicle"]').val();
    $.ajax({
        url: baseUrl('eforms/Transmittal/ajax_vehicle_details/') + veh,
        type: "POST",
        dataType: "JSON",
        data: { csrf_token: _csrf_hash },
        success: function (data) {
            console.log(veh);
            $('[name="driver"]').val(data.driver);
        }, error: function (jqXHR, textStatus, errorThrown) {
            alert('Error: "ajax_vehicle_details"');
        }
    });
}

var d = new Date();
var delivery_date = $('#delivery_date').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy/mm/dd hh:ii',
    minDate: d.setDate(d.getDate() - 14),
});

var search_val = "";
var check = "0";
var tblContent = $("#table-content").DataTable({
    dom: '<"toolbar">rt',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("eforms/transmittal/get_temp_request/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val

        }
    },
    searching: true,
    columns: [
        { data: "description" },
        { data: null, width: "15%", className: "text-center" },
    ],
    columnDefs: [
        { targets: [1], width: "15%" },
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
        check = "1";
        var _actionButton = "";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_content(" + $id + ")'><i class='la la-pencil-square'></i></button>";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete(" + $id + ")'><i class='la la-trash'></i></button>";
        return _actionButton;
    } else {
        return false;
    }
}

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function add_content() {
    save_method = 'add';
    $('#form_content')[0].reset(); // reset form on modals
    $('#modal_form_content').modal('show'); // show bootstrap modal
    $('.modal-title').text('Add Content'); // Set Title to Bootstrap modal title
}

function edit_content(id) {

    save_method = 'update';
    $('#form_content')[0].reset();
    $.ajax({
        url: baseUrl("eforms/transmittal/edit_temp_content/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('[name="id"]').val(data.id);
            $('[name="description"]').val(data.description);

            $('#modal_form_content').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Content'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

function save_content() {
    var url;
    if (save_method == 'add') {
        url = baseUrl("eforms/transmittal/add_temp_content/");
    } else {
        url = baseUrl("eforms/transmittal/update_temp_content/");
    }


    $.validate({
        form: '#form_content',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_content').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        $('#table_v').empty();
                        tblContent.ajax.reload();
                        if (save_method == 'add') {
                            toastr.success(data.message, "Content added successfully!", 5000);
                        } else {
                            toastr.success(data.message, "Updated successfully!", 5000);
                        }

                        $("#modal_form_content").modal("hide");
                    } else {
                        alert('Error get data from ajax');
                    }

                }
            });
            return false;
        },
    });
}

function add_transmittal() {
    var url;
    url = baseUrl("eforms/transmittal/add_transmittal/");
    if (tblContent.data().length == 0) {
        $('#table_v').empty();
        $('#table_v').append('<p><font color="#FF0000">Required. Add atleast 1 Content</font></p>');
    }

    $.validate({
        form: '#form_transmittal',
        lang: 'en',
        onSuccess: function (form) {
            if (tblContent.data().length !== 0) {
                $('#table_v').empty();
                var disabled = $('#form_transmittal').find('textarea:disabled, input:disabled').removeAttr('disabled');
                $.ajax({
                    url: url,
                    type: "POST",
                    data: $('#form_transmittal').serialize(),
                    dataType: "JSON",

                    success: function (data) {
                        if (data.status) {
                            disabled.attr('disabled', 'disabled');
                            window.location.assign(baseUrl("eforms/transmittal/masterfile"));
                        } else {
                            alert('Error get data from ajax');
                        }

                    }
                });
            }

            return false;
        },
    });

    file_under.on("change", function (e) {
        var self = $(e.target);
        self.validate();
    });

    department.on("change", function (e) {
        var self = $(e.target);
        self.validate();
    });

    requested_by.on("change", function (e) {
        var self = $(e.target);
        self.validate();
    });

    deliver.on("change", function (e) {
        var self = $(e.target);
        self.validate();
    });

    vehicle.on("change", function (e) {
        var self = $(e.target);
        self.validate();
    });

    $("#delivery_dt").on("change", function (e) {
        var self = $(e.target);
        self.validate();
    });

}

function delete_content() {
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("eforms/transmittal/delete_temp_content/") + $temp,
        type: "POST",
        dataType: "JSON",
        data: { csrf_token: _csrf_hash },
        success: function (data) {
            //if success reload ajax table
            tblContent.ajax.reload();
            toastr.success(data.toastr_msg, "Removed successfully!", 5000);
            $("#modal_form_delete").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            toastr.error(data.toastr_msg, "Error removing items!", 5000);
        }
    });
}

$("#clear_modal").hide();

function clear_content() {
    $('.modal-title').text('Clear'); // Set Title to Bootstrap modal title
    $("#clear_modal").modal("show");
    $.validate({
        form: '#clear_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/transmittal/delete_temp_all_content/"),
                type: "POST",
                dataType: "json",
                data: $("#clear_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#clear_modal').modal('hide');
                        tblContent.ajax.reload();
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
}

function type_change() {
    var type = $('[name="type"]').val();
    if (type == "internal") {
        $('#deliver_company').attr('disabled', 'disabled');
        document.getElementById('delivery_to_in').style.removeProperty('display');
        document.getElementById('delivery_to_ex').style.display = 'none';
        document.getElementById('row_department').style.display = 'none';
        document.getElementById('row_courier').style.display = 'none';
    }
    if (type == "external") {
        $('#form_transmittal').find('textarea:disabled, input:disabled').removeAttr('disabled');

        document.getElementById('delivery_to_in').style.display = 'none';
        document.getElementById('delivery_to_ex').style.removeProperty('display');
        document.getElementById('row_department').style.removeProperty('display');
        document.getElementById('row_courier').style.removeProperty('display');
    }
}

function change_other() {
    radiobtn = document.getElementById("service");
    radiobtn.checked = false;

    document.getElementById('other_remark').style.removeProperty('display');
    document.getElementById('service_veh').style.display = 'none';
    document.getElementById('service_driver').style.display = 'none';
}

function change_service() {
    radiobtn = document.getElementById("other");
    radiobtn.checked = false;

    document.getElementById('other_remark').style.display = 'none';
    document.getElementById('service_veh').style.removeProperty('display');
    document.getElementById('service_driver').style.removeProperty('display');
}

$(document).ready(function () {
    document.getElementById('delivery_to_ex').style.display = 'none';
    document.getElementById('row_department').style.display = 'none';
    document.getElementById('row_courier').style.display = 'none';
    document.getElementById('other_remark').style.display = 'none';
    radiobtn = document.getElementById("service");
    radiobtn.checked = true;
});
