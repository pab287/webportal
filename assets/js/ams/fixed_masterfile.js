let search_val = "";
let accountability_search = "";
let advanced_search = {};
var assets = [];
const dropdown = $("#btn-export-fixed-assets > i");

//init datatable
var tblFixedAsset = $("#table-fixed-asset")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        order: [[0, "asc"]],
        container: 'body',
        ajax: {
            url: baseUrl("ams/assets/get_datatable_request"),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.accountability_search = accountability_search;
                d.advanced_search = advanced_search;
            }
        },
        searching: false,
        columns: [
            {
                data: 'id', orderable: false, render: function (data, type, row, meta) {
                    return formatcheck(data, row);
                }
            },
            {
                data: "image",
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    if (data) {
                        return '<a data-lightbox="roadtrip" data-title="' + row.imageFilename + '" href="' + data + '">' +
                            '       <span class="table-avatar" style="background-image: url(\'' + data + '\')"></span>' +
                            '   </a>';
                    } else {
                        return '<span class="table-avatar" title="No image available.">' +
                            '       <i class="flaticon-open-box"></i>' +
                            '   </span>';
                    }
                },
                className: "d-flex justify-content-center",
            },
            {
                data: "assetacode",
                width: "8%",
                render: function (data, type, row, meta) {
                    let html = "";

                    html += data;

                    if (row.clear_accountability_borrowed == 0) {
                        html += '<span style="margin-left: 3px" class="m--font-warning fa fa-exclamation-circle"></span>';
                    }

                    return html;
                }
            },
            {data: "name", width: "15%"},
            {data: "description", width: "25%"},
            {data: "area", width: "10%"},
            {data: "comp_name", visible: false},
            {data: "dep_name", visible: false},
            {data: "asset_category", width: "20%"},
            {data: "station", width: "10%"},
            {data: "brand", visible: false},
            {data: "serialno", visible: false},
            {data: "po_no", visible: false},
            {data: "check_no"},
            {data: "modelno", visible: false},
            {data: "stat_name", visible: false},
            {
                data: "datepurchased",
                render: function (data) {
                    return data && data !== "0000-00-00" ? moment(data).format("YYYY-MM-DD") : "N/A";
                },
                visible: false
            },
            {
                data: "dateCreated",
                render: function (data) {
                    return data && data !== "0000-00-00" ? moment(data).format("YYYY-MM-DD") : "N/A";
                },
                visible: false
            },
            {data: "accounted_to", visible: false},
            {data: "purchaseprice", visible: false, render: function (data) {
                return numberFormat(data);
            }},
            
            {data: "gl_code", visible: false},
            {data: null, width: "7%", className: "text-center actions"},
        ],
        columnDefs: [{
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.is_borrowed);
                },
            },
            {
                targets: "_all",
                defaultContent: "",
            }],
        buttons: [
            {
                extend: 'excel',
                text: 'EXCEL',
                title: "Fixed Assets - " + moment().format('ll'),
                customize: function (excel) {
                },
                exportOptions: {
                    columns: ':visible:not(:eq(0)):not(.actions)'
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    const data = tblFixedAsset.ajax.params();

                    $.ajax({
                        url: baseUrl("ams/assets/get_datatable_request/1"),
                        type: "POST",
                        dataType: "JSON",
                        data,
                        success: function (response) {
                            dt.rows().remove();
                            dt.rows.add(response.data).draw();
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, node, config);

                            dropdown.removeClass();
                            dropdown.addClass("fa fa-external-link");
                        }
                    });
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                title: "Fixed Assets - " + moment().format('ll'),
                orientation: 'landscape',
                pageSize: 'LEGAL',
                customize: function (doc) {
                    //doc.content[1].table.widths = ['10%', '18%', '18%', '18%', '18%', '18%'];
                    doc.styles.tableHeader.alignment = 'left';
                },
                exportOptions: {
                    columns: ':visible:not(:eq(0)):not(.actions)'
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    const data = tblFixedAsset.ajax.params();
                    $.ajax({
                        url: baseUrl("ams/assets/get_datatable_request/1"),
                        type: "POST",
                        dataType: "JSON",
                        data,
                        success: function (response) {
                            dt.rows().remove();
                            dt.rows.add(response.data).draw();
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, node, config);

                            dropdown.removeClass();
                            dropdown.addClass("fa fa-external-link");
                        }
                    });
                },
            }
        ],
        drawCallback: function () {
            const jsonResponse = tblFixedAsset.ajax.json();
            const acct = parseInt(jsonResponse.acct);
            if (acct === 1) {
                const employee = $("#emp_name option").html().trim();
                toastr.warning(
                    `<div class="m--font-boldest">No Accountability found for ${employee}.</div>`,
                    `<div class="mb-1">Accountability Search Result:</div>`,
                    {
                        timeOut: 5000,
                        positionClass: "toast-top-right toast-opacity-1",
                        closeButton: true,
                    });
            }

            const allCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']").length;
            const checkedCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']:checked").length;
            const checked = allCheckboxes <= checkedCheckboxes;
            $('#selectall').prop('checked', checked);
        },
        pageLength: 20
    });

function formatcheck(data, row) {
    if (data) {
        let isSelected = assets.includes(data) ? 'checked' : '';
        var _checkButton = "<label class='m-checkbox m-checkbox--state-primary'><input type='checkbox' name='asset_id' class='text-gray'  value='"+ data +"' "+isSelected+"><span></span><label>";
        return _checkButton;
    } else {
        return false;
    }
}

$("#selectall").click( function () {
    var asset_ids = [];
    $('#table-fixed-asset tbody input[type="checkbox"]').prop('checked', this.checked);

    if (!this.checked) {
        $("input:checkbox[name=asset_id]:not(:checked)").each(function(){
            const val = $(this).val();
            const index = assets.indexOf(val);

            // removing all the item that been displayed
            if (index !== -1) {
                assets.splice(index, 1);
            }
        });
        console.log(assets);
    } else {
        $("input:checkbox[name=asset_id]:checked").each(function(){
            const val = $(this).val();
            if (!assets.includes(val)) {
                asset_ids.push(val);
            }
        });
        assets = assets.concat(asset_ids);
    }
});

$("#table-fixed-asset").on("click", "tbody input[type='checkbox']", function () {
    var asset_id = [];
    const allCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']").length;
    const checkedCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']:checked").length;
    const checked = allCheckboxes <= checkedCheckboxes;
    $('#selectall').prop('checked', checked);
    
    if (!this.checked) {
        $("input:checkbox[name=asset_id]:not(:checked)").each(function(){
            const val = $(this).val();
            const index = assets.indexOf(val);

            // removing the item that been displayed
            if (index !== -1) {
                assets.splice(index, 1);
            }
        });
    } else {
        $("input:checkbox[name=asset_id]:checked").each(function(){
            const val = $(this).val();

            // to prevent duplication entries
            if (!assets.includes(val)) {
                asset_id.push(val);
            }
        });
    
        assets = assets.concat(asset_id);
    }
});

function showOrHideColumn(index, el) {
    const column = tblFixedAsset.column(index);
    column.visible($(el)[0].checked);
}

function itemDatatableActions($id, is_borrowed = 0) {
    if ($id) {
        var _actionButton = "";

        if (_currentActions.includes("edit")) {
            _actionButton += "" +
                "<a href='" + baseUrl('ams/assets/edit_fixed_asset/') + $id + "' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' " +
                "   data-toggle='m-tooltip--skin-dark'" +
                "   data-placement='bottom'" +
                "   data-container='body' title='Edit Asset' data-delay='{\"show\": 300}'>" +
                "   <i class='la la-edit'></i>" +
                "</a>";
        }

        if (_currentActions.includes("archive")) {
            _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnDelete' " +
                "   data-id='" + $id + "'" +
                "   onclick='archiveAsset(" + $id + "," + is_borrowed + ")'" +
                "   data-toggle='m-tooltip'" +
                "   data-original-title='Archive Asset'" +
                "   data-placement='bottom' title='Archive Asset' data-placement='bottom'>" +
                "<i class='la la-file-archive-o'></i></button>";
        }

        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblFixedAsset.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblFixedAsset.ajax.reload();
});

function archiveAsset(id, is_borrowed) {
    checkIfAssetIsBorrowedOrAccounted(id)
        .then((done) => {
            const accountability = done.accountability;
            const borrowing_history = done.borrowing_history;
            if (accountability || borrowing_history) {
                const modalAlert = $(".cant-archive-alert-dialog");
                const modalBody = modalAlert.find(".modal-body");
                let el = "";
                if (accountability) {
                    el = "" +
                        "<div class='normal-case m--regular-font-size-lg2'>" +
                        "  <p>Asset, <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.asset_name + "</span> cannot be archived." +
                        "       This asset is still accounted to " +
                        "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.issued_to.toUpperCase(); + "</span>." +
                        "  </p>" +
                        "</div>" +
                        "</br>";
                }

                if (borrowing_history) {
                    el += "" +
                        "<div class='normal-case m--regular-font-size-lg2'>" +
                        "   <p class='mb-1 m--font-bold'>Borrower:</p>" +
                        "   <p>This asset is still in the possession of" +
                        "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + borrowing_history.borrower_name.toUpperCase(); + "</span>." +
                        "   </p>" +
                        "</div>";
                }

                modalBody.html("").append(el);
                modalAlert.modal("show");
            } else {
                const _modal = $(".archive-remarks");
                const form = _modal.find("form");
                form.attr("action", baseUrl("ams/assets/archive_fixed_asset/?id=" + id));
                _modal.modal("show");
            }
        });
}

function checkIfAssetIsBorrowedOrAccounted(asset_id) {
    return new Promise((resolve, reject) => {
        return $.ajax({
            url: baseUrl("ams/assets/check_if_asset_is_borrowed_or_accounted"),
            dataType: "JSON",
            type: "POST",
            data: {
                csrf_token: _csrf_hash,
                asset_id,
                type: "asset",
                isComponent: 0
            },
            success: function (response) {
                resolve(response);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                reject(errorThrown);
            }
        });
    });
}

function openAlertModal(title, message, function_name, action, color) {
    $.ajax({
        url: baseUrl("ams/assets/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {title, message, action, color},
            path: "alert_dialog",
            function_name
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

$(".m-content")
    .on("submit", "#confirmation-dialog",
        function (e) {
            e.preventDefault();
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");

            $.ajax({
                url: baseUrl(url),
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "", 5000);
                        tblFixedAsset.ajax.reload();
                        closeModal();
                    } else {
                        toastr.error(response.message, "Error", 5000);
                    }
                }
            })
        })
    .on("submit", ".archive-remarks form", function (e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr("action");
        const formData = new FormData(this);

        if (form.isValid()) {
            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "Archive successful.", 10000);
                        form.resetForm();
                        tblFixedAsset.ajax.reload();
                        $(".archive-remarks").modal("hide");
                    } else {
                        toastr.error(response.message, "Error", 10000);
                    }
                }
            })
        }
    });

function closeModal() {
    const modal = $(".document-modal-container");
    modal.modal("hide");
}

function clearSearch() {
    search_val = "";
    $("#generalSearch").val("");
    tblFixedAsset.ajax.reload();
}

$("#emp_name").select2({
    dropdownParent: $("#modal-accountability-search"),
    placeholder: "SELECT AN EMPLOYEE",
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_employee_name"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#location").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: "SELECT AN OPTION",
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_location"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$(".dt-picker").datepicker({
    format: "yyyy-mm-dd",
    todayHighlight: true,
    todayBtn: "linked",
    clearBtn: true,
});

$("#cat").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: "SELECT AN OPTION",
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_category"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#station").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: "SELECT AN OPTION",
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_station"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$('#accountability_search').on("click", function (callback) {
    accountability_search = $("#emp_name").val();
    tblFixedAsset.ajax.reload();
    $("#modal-accountability-search").modal("hide");
});

$('#advanced_search').on("click", function (callback) {
    advanced_search['assetacode'] = $("#asset_code").val();
    advanced_search['name'] = $("#asset_name").val();
    advanced_search['assetname'] = $("#asset_desc").val();
    advanced_search['area'] = $("#location").val();
    advanced_search['description'] = $("#cat").val();
    advanced_search['serialno'] = $("#serialno").val();
    advanced_search['station'] = $("#station").val();
    advanced_search['po_no'] = $("#po_no").val();
    advanced_search['dateCreated_from'] = $("#date_created_from").val();
    advanced_search['dateCreated_to'] = $("#date_created_to").val();
    tblFixedAsset.ajax.reload();
    $("#modal-advance-search").modal("hide");
});

function clear_adv_search() {
    $('#frm-advance-search')[0].reset();
    $("#location").val("").trigger('change');
    $("#cat").val("").trigger('change');
    $("#station").val("").trigger('change');

    advanced_search = {};
    $("#modal-advance-search").modal("hide");
    tblFixedAsset.ajax.reload();
}

function clear_acc_search() {
    $('#frm-accountability-search')[0].reset();
    $("#emp_name").val("").trigger('change');

    accountability_search = "";
    $("#modal-accountability-search").modal("hide");
    tblFixedAsset.ajax.reload();
}

function exportAs(type) {
    dropdown.removeClass();
    dropdown.addClass("fa fa-spinner fa-spin");

    setTimeout(() => {
        switch (type) {
            case "excel":
                tblFixedAsset.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblFixedAsset.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}


$("#column-options.dropdown-menu")
    .click(function (e) {
        e.stopPropagation();
    });

$("#modal-print-barcode").on("shown.bs.modal", function () {
    let name;
    const jsonResponse = tblFixedAsset.ajax.json();
    const data = jsonResponse.data;
    const barcodeContainer = $("#barcodes div.row");
    barcodeContainer.empty();
    if(assets.length > 0){
        data.forEach((item, i) => {
            if(assets.includes(item.id)){
                if(item.name){
                    name = item.name;
                }else{
                    name = 'NO ASSIGNED NAME';
                }
                barcodeContainer.append(`<div class="col-6 text-center mb-1">
                    <p class="barcode-label" style="margin-bottom: 17px;">${name}</p>
                    <p class="barcode-code" 
                        style="font-size:1.2rem; font-family: barcode, sans-serif;">*${item.assetacode}*</p>
                    </div>`);
            }
        });
    }else{
        barcodeContainer.append(`<div class="col-1"></div><div class="col-10 text-center bold"><h5>NO ASSET SELECTED. PLEASE CHECK AT LEAST ONE ASSET.</h5></div><div class="col-1"></div>`);
        
    }
});

$("#select2-status").select2({
    placeholder: 'Select Status',
    width: '100%'
});
    
$('body, .modal-body')
.tooltip({
    selector: '[title]',
    skin: "dark",
    delay: {
        show: 300
    }
});

$("#modal-mass-archive").on("shown.bs.modal", function () {
    if (assets.length > 0) {
        vmData.count = assets.length; //initial count of assets
        vmData.assets = assets.length; //count for assets after removing items with accountability, borrowing and mother assets

        $.ajax({
            url: baseUrl('ams/assets/check_multiple_if_borrowed_or_accounted'),
            dataType: "JSON",
            type: "POST",
            data: {
                csrf_token : _csrf_hash,
                type : 'asset',
                isComponent: 0,
                ids : assets
            },
            success: function (response) {
                vmData.isAssetClear = vmData.isEmpty(response.accountability) && vmData.isEmpty(response.borrowing_history) ? true : false;
                vmData.rows = Object.assign({}, response);

                if (vmData.isEmpty(response.accountability) && vmData.isEmpty(response.borrowing_history)) {
                    archiveSelect2();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
});

$("#modal-mass-archive").on("hidden.bs.modal", function () {
    vmData.rows = Object.assign({});
    vmData.count = 0;
    vmData.assets = 0;
    vmData.isAssetClear = false;
});

const vmData = new Vue({
    el: "#archive-list",
    data: { rows: {}, count: 0, assets: 0, isAssetClear: false, selectedAssets: {} },
    methods: {
        isEmpty(arr){
            return $.isEmptyObject(arr);
        }, removeAsset(index, id, type) {
            const instance = this;

            /** removing the item to the checkbox ids */
            const i = assets.indexOf(id);

            if (i !== -1) {
                assets.splice(i, 1);
                $(`input[type=checkbox][value='${id}']`).prop('checked', false);
            }
            /** removing the item to the checkbox ids */

            /** removing to the list */
            instance.rows[type].splice(index, 1);
            instance.isAssetClear = instance.isEmpty(instance.rows.accountability) && instance.isEmpty(instance.rows.borrowing_history) ? true : false;
            instance.count = assets.length;
            instance.assets = assets.length;
            /** removing to the list */

            $(".tooltip.bs-tooltip-top").empty();

            const allCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']").length;
            const checkedCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']:checked").length;
            const checked = allCheckboxes <= checkedCheckboxes;
            $('#selectall').prop('checked', checked);

            if (instance.isAssetClear) {
                archiveSelect2();
            }
        }, removeArchive(index, id) {
            const instance = this;
            const i = assets.indexOf(id);

            if (i !== -1) {
                assets.splice(i, 1);
                $(`input[type=checkbox][value='${id}']`).prop('checked', false);
            }

            instance.selectedAssets.splice(index, 1);
            instance.count = assets.length;
            instance.assets = assets.length;
            instance.isAssetClear = instance.selectedAssets.length > 0 ? true : false; 

            $(".tooltip.bs-tooltip-top").empty();

            const allCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']").length;
            const checkedCheckboxes = $("#table-fixed-asset tbody input[type='checkbox']:checked").length;
            const checked = allCheckboxes <= checkedCheckboxes;
            $('#selectall').prop('checked', checked);
        }
    }
})

function archiveSelect2(){
    $.ajax({
        url: baseUrl('ams/assets/get_selected_for_archive'),
        dataType: "JSON",
        type: "GET",
        data: {
            isComponent: 0,
            ids : assets
        },
        success: function(response) {
            vmData.selectedAssets = response.data;
        }
    })

    setTimeout( function() {
        $("#archive-select2-status").select2({
            dropdownParent: $("#modal-mass-archive"),
            placeholder: 'Select Status',
            width: '100%'
        });
    }, 500);
}

$.validate({
    form : '#mass-archive-form',
	lang: 'en',
	onSuccess : function(form) {
		var _data = form.serializeArray();
        _data.push({ name: 'ids', value: assets }, { name: 'type', value: 'fixed'});

        $.ajax({
            url: baseUrl('ams/assets/mass_archive_assets'),
			type: "POST",
			data: _data,
            beforeSend: function(){
				$(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
			},
            success: function(response){
                if (response.status) {
                    toastr.success(response.msg, "", 5000);
                    $("#modal-mass-archive").modal('hide');
                    tblFixedAsset.ajax.reload();
                } else {
                    toastr.error(response.msg, "", 5000);
                }
            }
        });

        return false;
    }
})