jQuery(document).ready(function () {
    fileUploadPhoto();
});

var loadAsset = $("#load-asset").DataTable({
    dom: 'rt',
    ordering: false,
    'columnDefs': [
        {
            targets: 0,
            width: "5%",
            className: "text-center",
            orderable: false
        }
    ],
});

$("#inventory-action").select2({
    placeholder: 'Select Action',
    width: '100%',
    data: [
        {
            id: "",
            text: "",
        },
        {
            id: "verified",
            text: "Verified"
        },
        {
            id: "recovered",
            text: "Recovered"
        }
    ]
});

let _items = {};
let _noitems = {};
let _duplicateitems = {};

var fileUploadPhoto = function () {
    var url = baseUrl("ams/inventory/inventory_fileupload/vehicles");
    $("#fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash},
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var items = result.items.data;
                    var noitems = result.items.no_data;
                    var duplicate_items = result.items.duplicate_data;

                    _items = Object.assign({}, _items, items);
                    _noitems = Object.assign({}, _noitems, noitems);
                    _duplicateitems = Object.assign({}, _duplicateitems, duplicate_items);

                    const containerEl = $("#files");
                    containerEl.empty();

                    $.each(data.result.files, function (index, file) {
                        const nodata_count = file.nodata_count || 0;
                        const duplicate_count = file.duplicate_count || 0;
                        const filename = file.name.toUpperCase();
                        containerEl.append(`<div class="d-inline">
                                                <div class="d-inline">
                                                    <span class="m--font-bolder">FILENAME: </span>
                                                    <span class="ml-3">${filename}</span>
                                                </div>
                                            </div>`);
                        if (parseInt(nodata_count) >= 1) {
                            containerEl.append(`<div class="d-inline ml-4">
                                                    <button class="btn btn-danger btn-sm" onclick="openAssetNotFoundModal()">ASSET NOT FOUND(${nodata_count})</button>
                                                </div>`);
                        }

                        if (parseInt(duplicate_count) >= 1) {
                            containerEl.append(`<div class="d-inline ml-2">
                                                    <button class="btn btn-warning btn-sm" onclick="openDuplicatedAssetModal()">DUPLICATE ASSET CODE(${duplicate_count})</button>
                                                </div>`);
                        }

                        containerEl.append(`<div class="d-inline pull-right">
                                                <span style="border-radius: 3px;" 
                                                      class="m-badge m-badge--success m-badge--wide m--font-bolder badge-count">RECORD COUNT: ${file.count}</span>
                                            </div>`);
                    });

                    setTimeout(function () {
                        $('#progress .progress-bar').css('width', '0');
                        triggerUpdateList();
                    }, 1000);
                } else {
                    if (typeof result.data !== "undefined") {
                        toastr.error(result.data.error);
                    }
                    if (typeof result.toastr_msg !== "undefined") {
                        toastr.error(result.toastr_msg);
                    }

                    setTimeout(function () {
                        $('#progress .progress-bar').css('width', '0');
                    }, 1000);
                }
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width', progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
}

function openAssetNotFoundModal() {
    const _modal = $("#asset-not-found-modal");
    const body = $(".modal-body", _modal);
    body.empty();

    const items = Object.values(_noitems);
    items.forEach(item => {
        body.append(`<p class="mb-2 pb-1 m--regular-font-size-lg1 px-2 m--font-bolder" style="border-bottom: 1px solid #efefef;">${item}</p>`);
    });
    _modal.modal("show");
}

function openDuplicatedAssetModal() {
    const _modal = $("#duplicated-asset-modal");
    const body = $(".modal-body", _modal);
    body.empty();

    const items = Object.values(_duplicateitems);
    items.forEach(item => {
        body.append(`<p class="mb-2 pb-1 m--regular-font-size-lg1 px-2 m--font-bolder" style="border-bottom: 1px solid #efefef;">${item}</p>`);
    });
    _modal.modal("show");
}

function triggerUpdateList() {
    var _arrData = [];
    loadAsset.rows().remove();
    jQuery.each(_items, function (k, v) {
        const checkboxTemplate = `<label class="m-checkbox"><input type="checkbox" name="cbSelected[]"><span></span></label>`;
        loadAsset.row.add([checkboxTemplate, v.assetacode, v.n_description, v.last_updated, v.current_status, v.n_remarks]).draw();
        $("select").select2();
    });
}

$(document).on("click", ".btn-remove-row", function () {
    var _self = $(this);
    var _row = _self.closest("tr");
    if (typeof _row !== "undefined" && _row.length > 0) {
        _row.remove();
    }
});

$(document).on("click", ".btn-nodata", function () {
    if (typeof _noitems == "object") {
        var modalHeader = _modal.find(".modal-content .modal-header .modal-title");
        var modalBody = _modal.find(".modal-content .modal-body");
        if (typeof modalBody !== "undefined" && modalBody.length > 0) {
            modalHeader.empty();
            modalHeader.text("Assetcode not found on the system");
            modalBody.empty();

            var _html = "";
            var _count = 0;
            jQuery.each(_noitems, function (i, v) {
                $('<p class="custom-p"/>').html(v).appendTo(modalBody);
                _count++;
            });

            if (_count > 0) {
                var modalFooter = _modal.find(".modal-content .modal-footer");
                var _nodata = modalFooter.find("button.export-nodata");

                var btnHtml = "<button type='button' class='btn btn-success export-nodata'><i class='fa fa-download'></i> Export</button>";
                if (typeof _nodata !== "undefined" && _nodata.length == 0) {
                    modalFooter.prepend(btnHtml);
                }
            }
        }
        _modal.modal("show");
    }
});

$(document).on("click", ".export-nodata", function () {
    if (typeof _noitems == "object") {
        $.ajax({
            url: base_url('ams/inventory/create_csv'),
            type: "post",
            dataType: "json",
            data: {items: _noitems, type: "asset"},
            success: function (json) {
                if (json.response) {
                    var _url = base_url('inventory/export_csv');
                    _url = _url + "/" + json.filename;
                    window.open(_url, "_blank");
                }
            }
        });
    }
});

$(document).on("click", ".btn-duplicate", function () {
    if (typeof _duplicateitems == "object") {
        var modalHeader = _modal.find(".modal-content .modal-header .modal-title");
        var modalBody = _modal.find(".modal-content .modal-body");
        if (typeof modalBody !== "undefined" && modalBody.length > 0) {
            modalHeader.empty();
            modalHeader.text("Duplicate assetcode on uploaded textfile");
            modalBody.empty();

            var _html = "";
            jQuery.each(_duplicateitems, function (i, v) {
                $('<p class="custom-p"/>').html(v).appendTo(modalBody);
            });
        }

        var modalFooter = _modal.find(".modal-content .modal-footer");
        var _nodata = modalFooter.find("button.export-nodata");
        if (typeof _nodata !== "undefined" && _nodata.length > 0) {
            _nodata.remove();
        }

        _modal.modal("show");
    }
});


$('#select-all').on('click', function () {
    $('#load-asset tbody input[type="checkbox"]').prop('checked', this.checked);
});

$('#load-asset tbody')
    .on('change', 'input[type="checkbox"]', function () {
        const cbEl = $('#load-asset tbody input[type="checkbox"]').length;
        const checked = $('#load-asset tbody input[type="checkbox"]:checked').length;

        $("#select-all").prop("checked", parseInt(cbEl) === parseInt(checked));
    });

function inventoryCheck(inventory_status) {
    const button = $("#inventory-action-dropdown");
    let assets = [];
    const table = $("#load-asset tbody");
    const rows = table.find('input[name="cbSelected[]"]:checked').closest('tr');

    let doneLoop = false;

    $.each(rows, function (i, row) {
        const id = $("input[name='id[]']", row).val();
        const status = $("select[name='status[]']", row).val();
        const remarks = $("textarea[name='remarks[]']", row).val();
        assets.push({id, status, remarks});
        doneLoop = (i + 1) === rows.length;
    });

    if (doneLoop) {
        $.ajax({
            url: baseUrl("ams/inventory/save_inventory_check/2"),
            type: "POST",
            data: {
                assets,
                csrf_token: _csrf_hash,
                inventory_status
            },
            dataType: "JSON",
            beforeSend: function () {
                button.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
            },
            success: function (response) {
                const toast = response.success ? "success" : "error";
                toastr[toast](response.message, response.title, {timeOut: 10000});
                button.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                rows.remove();
                $("#select-all").prop("checked", false);
                _items = {};
                $(".badge-count").html(`RECORD COUNT: ${table.find("tr").length}`);
            }
        });
    }
}

function exportList() {
    const csvContent = "data:text/csv;charset=utf-8," + Object.values(_noitems).join(',');
    const encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "not_found_assets.csv");
    document.body.appendChild(link); // Required for FF

    link.click();
}