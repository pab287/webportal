var search_val = "";

var tblLoans = $("#table-loans").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("payroll/loans/masterfile/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: false,
    columns: [
        { data: "code", width: "12%" },
        { data: "loan_name" },
        { 
            data: "has_ref", render: function(data){
                if(data == 1){
                    return 'Yes';
                }else{
                    return 'No';
                }
            }
        },
        {
            data: "loan_type", width: "12%", render: function (data) {
                return data && parseInt(data) !== 1 ? "Internal" : "External";
            }
        },
        {
            data: "loan_class", width: "12%", orderable: false, render: function (data) {
                let tempHtml = '---';
                const tempData = parseInt(data);
                switch (tempData) {
                    case 1: tempHtml = 'SSS LOAN'; break;
                    case 2: tempHtml = 'HDMF LOAN'; break;
                    default: tempHtml = '---'; break;
                }

                return tempHtml;
            }
        },
        { data: null, width: "10%", className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
        }
    ]
});

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <button type='button' onclick='Edit(" + $id + ")' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' data-toggle='m-tooltip' data-original-title='Edit' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-pencil-square'></i></button>";
        _actionButton += " <button type='button' onclick='Archive(" + $id + ")' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchive' data-toggle='m-tooltip' data-original-title='Archive' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-file-archive-o'></i></button>";
        return _actionButton;
    } else { return false; }
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblLoans.ajax.reload();
});

$("#reload_dtTbl").on("click", function () {
    tblLoans.ajax.reload();
});

function Save() {
    $.validate({
        form: '#frm-add',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("payroll/loans/save"),
                type: "POST",
                dataType: "json",
                data: $("#frm-add").find("input").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                        $("#modal-add").modal("hide");
                        $('#frm-add')[0].reset();
                        $('#employee').text("");
                        tblLoans.ajax.reload();
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

function Edit(id) {
    $("#modal-edit").modal("show");
    $.ajax({
        url: baseUrl("payroll/loans/edit/") + id,
        type: "GET",
        dataType: "json",
        success: function (data) {
            $("#code").val(data.code);
            $("#name").val(data.loan_name);
            const isExternal = parseInt(data.loan_type) == 1 ? true : false;
            vmEditLoans.is_external = isExternal;
            vmEditLoans.prop_classification = parseInt(data.loan_class) > 0 ? data.loan_class : 1;

            if (typeof data.loan_type !== "undefined") {
                if (data.loan_type == 1) {
                    $("#loan_type0").prop("checked", false);
                    $("#loan_type1").prop("checked", true);
                } else {
                    $("#loan_type0").prop("checked", true);
                    $("#loan_type1").prop("checked", false);
                }
            }

            if(typeof data.has_ref !== 'undefined'){
                if(data.has_ref == 1){
                    $("#has_ref").prop('checked', true);
                }else{
                    $("#has_ref").prop('checked', false);
                }
            }
        }
    });

    $.validate({
        form: '#frm-edit',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("payroll/loans/update/") + id,
                type: "POST",
                dataType: "json",
                data: $("#frm-edit").find("input").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                        $("#modal-edit").modal("hide");
                        tblLoans.ajax.reload();
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

function Archive(id) {
    $("#modal-delete").modal("show");

    $.validate({
        form: '#frm-delete',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("payroll/loans/delete/") + id,
                type: "POST",
                dataType: "json",
                data: $("#frm-delete").find("input").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        $('#modal-delete').modal('hide');
                        toastr.success(data.toastr_msg, "Successfully archived", 5000);
                        tblLoans.ajax.reload();
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

var vmNewLoans = new Vue({
    el: "#frm-add",
    data: { is_external: false },
    methods: {
        triggerChecked: function (e) {
            const _this = this;
            const tempValue = e.target.value;
            _this.is_external = parseInt(tempValue) == 1 ? true : false;
            return _this;
        }
    }
});

var vmEditLoans = new Vue({
    el: "#frm-edit",
    data: { is_external: false, prop_classification: 1 },
    methods: {
        triggerChecked: function (e) {
            const _this = this;
            const tempValue = e.target.value;
            _this.is_external = parseInt(tempValue) == 1 ? true : false;
            return _this;
        }
    }
});