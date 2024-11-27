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

$.validate({
    form: '#change_return_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/change_return_by/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#change_return_form").find("input,textarea, select").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#change_return').modal('hide');
                    $('#change_return_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Updated Successfully!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.ajax({
    url: baseUrl('eforms/accountability/content_detail/') + param_id,
    type: "GET",
    success: function (data) {
        if (data[0].is_contract == 0) {
            $("#return_by").select2({
                placeholder: 'Select. .',
                width: '100%',
                dropdownParent: $("#change_return"),
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
            if (data[0].returned_by_detail == '' || data[0].returned_by_detail == null) {
                var return_by = new Option(data[0].display_name, data[0].issued_to, true, true);
                $('#return_by').append(return_by).trigger('change');
                $("#returned_by").append("<b class='col-8'>" + data[0].display_name + "</b> <button type='button' data-toggle='modal' data-target='#change_return'  class='btn m-btn--pill btn-primary btn-sm'><span class='la la-refresh'></span> Change</button>");
            } else {
                var return_by = new Option(data[0].returned_by_detail, data[0].return_by, true, true);
                $('#return_by').append(return_by).trigger('change');
                $("#returned_by").append("<b class='col-8'>" + data[0].returned_by_detail + "</b> <button type='button' data-toggle='modal' data-target='#change_return'  class='btn m-btn--pill btn-primary btn-sm'><span class='la la-refresh'></span> Change</button>");
            }

            $("#issued_to").append("<b>" + data[0].display_name + "</b>");

        } else {
            $("#return_by").select2({
                placeholder: 'Select. .',
                width: '100%',
                dropdownParent: $("#change_return"),
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

            if (data[0].returned_by_detail == '' || data[0].returned_by_detail == null) {
                var return_by = new Option(data[0].contractor, data[0].issued_to, true, true);
                $('#return_by').append(return_by).trigger('change');
                $("#returned_by").append("<b class='col-8'>" + data[0].contractor + "</b> <button type='button' data-toggle='modal' data-target='#change_return'  class='btn m-btn--pill btn-primary btn-sm'><span class='la la-refresh'></span> Change</button>");
            } else {
                var return_by = new Option(data[0].returned_by_detail, data[0].return_by, true, true);
                $('#return_by').append(return_by).trigger('change');
                $("#returned_by").append("<b class='col-8'>" + data[0].returned_by_detail + "</b> <button type='button' data-toggle='modal' data-target='#change_return'  class='btn m-btn--pill btn-primary btn-sm'><span class='la la-refresh'></span> Change</button>");
            }

            $("#issued_to").append("<b>" + data.contractor + "</b>");
        }
        $("#reference_no").text(data[0].reference_no);
        $("#company").text(data[0].company);
        $("#department").text(data[0].department);
        $("#issued_dt").append("on <b>" + moment(data[0].date_issued).format("MMMM DD, YYYY") + "</b>");
        if (data[0].created_by) {
            $("#created_by").append("<b>" + data.created_by + "</b>");
            $("#created_dt").append(' ON <b>' + moment(data[0].created_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");
        } else {
            $("#created_by").text('N/A');
        }

        if (data[0].last_edited_by) {
            $("#last_edited_by").text(data[0].last_edited_by);
            $("#last_edited_dt").text(' ON ' + moment(data[0].last_edited_dt).format("MMMM DD, YYYY hh:mm a"));
        } else {
            $("#last_edited_by").text('N/A');
        }

        if (data[0].marked_returned_by) {
            $("#marked_returned_by").text(data[0].marked_returned_by);
            $("#marked_returned_dt").text(' ON ' + moment(data[0].marked_returned_dt).format("MMMM DD, YYYY hh:mm a"));
        } else {
            $("#marked_returned_by").text('N/A');
        }

        $("#acctg_noted_remarks").append(data[0].acctg_noted_remarks);
        $("#acctg_noted_by").append("by <b>" + data[0].acctg_noted_by + "</b>");
        $("#acctg_noted_dt").append("on <b>" + moment(data[0].acctg_noted_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");

        $("#hr_noted_remarks").append(data[0].hr_noted_remarks);
        $("#hr_noted_by").append("by <b>" + data[0].hr_noted_by + "</b>");
        $("#hr_noted_dt").append("on <b>" + moment(data[0].hr_noted_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");

        if (data[0].released_by) {
            $("#release_remarks").append(data[0].release_remarks);
            $("#released_by").append("by <b>" + data[0].released_by + "</b>");
            $("#released_dt").append("on <b>" + moment(data[0].released_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");
        } else {
            $("#released_by").text('N/A');
        }

        $("#remarks").text(data[0].remarks);

        vmAccountability.vmAccData = Object.assign({}, data[0])
        vmTab2.vm_tab2 = Object.assign({}, data[0]);
        vmTab2.vm_tab_content = Object.assign({}, data.data_body.data);
        vmTab2.vm_tab_total_amount = Object.assign({}, data.data_body_total_amount);
        
        vmTab1.vm_tab1 = Object.assign({}, data[0]);
        vmTab1.vm_tab_aaf = Object.assign({}, data.data_body.data);
        vmTab1.vm_tab_aaf_totalamount = Object.assign({}, data.data_body_total_amount);
    }
});

var search_val = "";
var tblBody = $("#tblbody_returned").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    destroy: true,
    bPaginate: false,
    bInfo: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/accountability/ret_content_body_detail/") + param_id,
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    footerCallback: function () {
        $("#tblbody_returned tfoot tr").text("");
        var api = this.api();
        $("#tblbody_returned tfoot tr").append("<td></td><td class='text-right'><b>Total: </b></td><td class='text-right'>PHP " + (api.column(2, { page: 'current' }).data().sum()).toLocaleString("PHP", { minimumFractionDigits: 2 }) + "</td>");
        $("#total_accountability_amount").append("PHP " + (api.column(2, { page: 'current' }).data().sum()).toLocaleString("PHP", { minimumFractionDigits: 2 }));
        
        
        vmAccountabilityForm.isLoading = false;
    },
    searching: false,
    columns: [
        { data: "asset_code", width: "15%" },
        { data: "description", width: "50%", render: function (data, type, row, meta) { return descriptionDetail(row.description, row.brand, row.modelno, row.serialno, row.plateno, row.engineno, row.chasisno, row.type, row.desc, row.comp_description); } },
        { data: "amount", width: "15%", className: "text-right" },
        { data: "status", width: "5%", orderable: false, render: function (data, type, row, meta) { return statusDetail(row.is_returned); } },
        { data: "remarks", className: "text-center", orderable: false, render: function (data, type, row, meta) { return remarksModal(row.id, row.is_returned); } },
        { data: null, width: "5%", className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) { return itemDatatableActions(row.id, row.is_returned); },
        }
    ]
});

function remarksModal(id, isReturn) {
    if (isReturn == 0) {
        var _actionButton = "";
        _actionButton += " <button type='button' onclick='edit_unreturned(" + id + ")' data-toggle='modal' data-target='#unreturned_remarks_modal' class='btn m-btn btn-default m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='fa flaticon-chat-1'></i></button>";
        return _actionButton;
    } else {
        var _actionButton = "";
        _actionButton += " <button type='button' onclick='edit_returned(" + id + ")' data-toggle='modal' data-target='#returned_remarks_modal' class='btn m-btn btn-default m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='fa flaticon-chat-1'></i></button>";
        return _actionButton;
    }
}

function itemDatatableActions($id, $isReturned) {
    if ($id) {
        if ($isReturned == 0) {
            var _actionButton = "";
            _actionButton += "<div class='m-checkbox-list'><label class='m-checkbox m-checkbox--success'><input name='checkbox' onchange='checks(" + $id + ")' type='checkbox'><input id='asset_id" + $id + "' type='hidden' name='id[]'><span></span></label></div>";
            return _actionButton;
        } else {
            var _actionButton = "";
            _actionButton += "<div class='m-checkbox-list'><label class='m-checkbox m-checkbox--success'><input name='checkbox' onchange='undochecks(" + $id + ")' type='checkbox' checked><input id='asset_id" + $id + "' type='hidden' name='id[]'><span></span></label></div>";
            return _actionButton;
        }
    } else { return false; }
}

document.getElementById('tblbody_returned').createTFoot().insertRow(0);

function descriptionDetail($desc, $brand, $model, $serial, $plateno, $engineno, $chasisno, $type, $desc_det, $comp_description) {
    if ($brand == '' || $brand == null) { $brand = 'N/A'; }
    if ($model == '' || $model == null) { $model = 'N/A'; }
    if ($serial == '' || $serial == null) { $serial = 'N/A'; }
    if ($plateno == '' || $plateno == null) { $plateno = 'N/A'; }
    if ($engineno == '' || $engineno == null) { $engineno = 'N/A'; }
    if ($chasisno == '' || $chasisno == null) { $chasisno = 'N/A'; }
    if ($type == 'Asset') {
        return '<b>' + $desc + '</b><br>Description: ' + $desc_det + '<br>Brand: ' + $brand + '<br>Model: ' + $model + '<br>Serial: ' + $serial + '<br>' + $comp_description;
    } else if ($type == 'Vehicle') {
        return '<b>' + $desc + '</b><br>Description: ' + $desc_det + '<br>Plate no.: ' + $plateno + '<br>Engine no.: ' + $engineno + '<br>Chasis no.: ' + $chasisno + '<br>' + $comp_description;
    } else {
        return '<b>' + $desc + '</b>';
    }
}

function statusDetail(isReturn) {
    if (isReturn == 0) {
        return '<div class="m-badge m-badge--wide alert alert-warning" role="alert"><strong>Unreturned</strong></div>';
    } else {
        return '<div class="m-badge m-badge--wide alert alert-accent" role="alert"><strong>Cleared</strong></div>';
    }
}

var triggerSaveReturnAcct = function () {
    $("#accountability_form").submit();
    $("#modal-return_accountability").modal("hide");
}

$("#unreturned_remarks_modal").hide();
$("#returned_remarks_modal").hide();
$("#change_return").hide();


function edit_unreturned($id) {
    $.ajax({
        url: baseUrl('eforms/accountability/edit_unreturned_remarks/') + $id,
        type: "get",
        success: function (data) {
            $("#unreturned_remarks").text(data.remarks);
            $("#unreturned_remarks_form input[name= 'id']").val(data.id);
            $("#unreturned_remarks_form input:checkbox").prop('checked', false);
            if (data.is_returned == 0) {
                $("#unreturned_remarks_form input[name='unreturned']").prop('checked', true);
            } else {
                $("#unreturned_remarks_form input[name='returned']").prop('checked', true);
            }
        }
    });

    $.validate({
        form: '#unreturned_remarks_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/accountability/unreturned_remarks/") + $id,
                type: "POST",
                dataType: "json",
                data: $("#unreturned_remarks_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#unreturned_remarks_modal').modal('hide');
                        $('#unreturned_remarks_form')[0].reset();
                        tblBody.ajax.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

function edit_returned($id) {
    $.ajax({
        url: baseUrl('eforms/accountability/edit_returned_remarks/') + $id,
        type: "get",
        success: function (data) {
            $("#returned_remarks").text(data.remarks_returned);
            $("#returned_remarks_form input[name= 'clear_id']").val(data.id);
            $("#returned_remarks_form input:checkbox").prop('checked', false);
            if (data.is_returned == 0) {
                $("#returned_remarks_form input[name='clear_unreturned']").prop('checked', true);
            } else {
                $("#returned_remarks_form input[name='clear_returned']").prop('checked', true);
            }
        }
    });

    $.validate({
        form: '#returned_remarks_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/accountability/returned_remarks/") + $id,
                type: "POST",
                dataType: "json",
                data: $("#returned_remarks_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#returned_remarks_modal').modal('hide');
                        $('#returned_remarks_form')[0].reset();
                        tblBody.ajax.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

function checks($id) {
    if ($("input[name='checkbox']:checked")) {
        $("#asset_id" + $id + "").val($id);

    } else {
        $("#asset_id" + $id + "").val(0);
    }
}

$.validate({
    form: '#accountability_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/return_accountability/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#accountability_form").find("input,textarea, checkbox").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#accountability_form')[0].reset();
                    location.reload(toastr.success(data.toastr_msg, "Updated successfully!", 5000));
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

function undochecks($id) {
    if ($("input[name='checkbox']:checked")) {
        $("#asset_id" + $id + "").val($id);
        $.validate({
            form: '#accountability_form',
            lang: 'en',
            onSuccess: function (form) {
                $.ajax({
                    url: baseUrl("eforms/accountability/undo_return_accountability/") + param_id,
                    type: "POST",
                    dataType: "json",
                    data: $("#accountability_form").find("input,textarea, checkbox").serialize(),
                    beforeSend: function () {
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (data) {
                        if (data) {
                            $('#accountability_modal').modal('hide');
                            $('#accountability_form')[0].reset();
                            location.reload(toastr.success(data.toastr_msg, "Updated successfully!", 5000));
                        } else {
                            toastr.error(data.toastr_msg, "Error!", 5000);
                        }
                        $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                    }
                });
                return false;
            },
        });
    } else {
        $("#asset_id" + $id + "").val(0);
    }
}

// $.ajax({
//     url: baseUrl("eforms/accountability/content_detail/") + param_id,
//     type: "get",
//     success: function (data) {
//         vmTab1.vm_tab1 = Object.assign({}, data);
//         vmTab2.vm_tab2 = Object.assign({}, data);
//         // vmAccountability.vm_tab1 = Object.assign({}, data);
//         console.log(data.data)
//         if (data.is_contract == 0) {
//             $("#print_issued_to").text(data.display_name);
//             $("#print_issued_to2").text(data.display_name);
//             $("#print_issued_by").text(data.display_name);
//             if (data.returned_by == "" || data.returned_by == null) {
//                 $("#print_returned_by").text(data.display_name);
//             } else {
//                 $("#print_returned_by").text(data.returned_by_detail);
//             }
//         } else {
//             $("#print_issued_to").text(data.contractor);
//             $("#print_issued_to2").text(data.contractor);
//             $("#print_issued_by").text(data.contractor);
//             if (data.returned_by == "" || data.returned_by == null) {
//                 $("#print_returned_by").text(data.contractor);
//             } else {
//                 $("#print_returned_by").text(data.returned_by_detail);
//             }
//         }
//         $("#received_by").text(data.display_name);
//     }
// })
var vmAccountabilityForm = new Vue({
  el: "#buttons",
  data: { isLoading: true }
})

var vmAccountability = new Vue({
    el: "#accountability_form_body",
    data: { vmAccData: {}}
});

var vmTab1 = new Vue({
    el: "#print_accountability",
    data: { vm_tab1: {}, vm_tab_aaf: {}, vm_tab_aaf_totalamount: {} }
});

var vmTab2 = new Vue({
    el: "#print_returned_accountability",
    data: { vm_tab2: {}, vm_tab_content: {}, vm_tab_total_amount: {} }
});

var vmAcctLogs = new Vue({
    el: "#accountability-logs",
    data: { rows: {}, count: 0 }
});

$.ajax({
    url: siteUrl("eforms/accountability/get_accountability_logs/" + param_id),
    dataType: "json",
    success: function (json) {
        if (json.response) {
            vmAcctLogs.rows = Object.assign({}, json.rows);
            vmAcctLogs.count = json.count;
        }
    }
})
function printArea() {
    win = window.open();
    var divToPrint = document.getElementById("printableArea");
    win.document.write(divToPrint.outerHTML);
    win.focus();
    win.print();
    win.close();
}

function printAreaReturn() {
    win = window.open();
    var divToPrint = document.getElementById("printableAreaReturned");
    win.document.write(divToPrint.outerHTML);
    win.focus();
    win.print();
    win.close();
}

function model($brand, $model) {
    return $brand + " / " + $model;
}

function formatCalendarDate(data) {
    if (data == "0000-00-00") {
        return "";
    } else {
        return moment(data).format("YYYY/MM/DD");
    }
}
$(document).ready(function () {

    $("#unreturned_remarks_form input:checkbox").on('change', function () {

        $("#unreturned_remarks_form input:checkbox").prop("checked", false);
        $(this).prop('checked', true);
        var name = $(this).prop('name');
        var id = $("#unreturned_remarks_form input[name= 'id']").val();
        $.ajax({
            url: baseUrl("eforms/accountability/remarks"),
            type: "POST",
            data: {
                "csrf_token": _csrf_hash,
                "id": id
            },
            dataType: "json",
            success: function (data) {
                if (data.is_returned == 0) {
                    if (name == "unreturned") {
                        $('#unreturned_remarks_form #unreturned_remarks').val(data['remarks']);
                    } else {
                        $('#unreturned_remarks_form #unreturned_remarks').val(data['remarks_returned']);
                    }
                }
            }
        });

    });

    $("#returned_remarks_form input:checkbox").on('change', function () {
        $("#returned_remarks_form input:checkbox").prop("checked", false);
        $(this).prop('checked', true);
        var name = $(this).prop('name');
        var id = $("#returned_remarks_form input[name= 'clear_id']").val();
        $.ajax({
            url: baseUrl("eforms/accountability/remarks"),
            type: "POST",
            data: {
                "csrf_token": _csrf_hash,
                "id": id
            },
            dataType: "json",
            success: function (data) {
                if (data.is_returned != 0) {
                    if (name == "clear_returned") {
                        console.log("returned remarks");
                        $('#returned_remarks_form #returned_remarks').val(data['remarks_returned']);
                    } else {
                        console.log("returned returned_remarks");
                        $('#returned_remarks_form #returned_remarks').val(data['remarks']);
                    }
                }
            }
        });
    });
});
