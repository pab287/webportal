var search_val = "";
let advanced_search = {};
var assets_components = [];
const dropdown = $("#btn-export-asset-components > i");
let acctTable;
let borrTable;

//init datatable
var tblAssetComponents = $("#table-asset-components")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        order: [[0, "asc"]],
        ajax: {
            url: baseUrl("ams/assets/get_components_masterfile_list"),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
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
                className: "d-flex justify-content-center"
            },
            {
                data: "assetacode",
                width: "8%"
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
            {data: "status", visible: false},
            {
                data: "datepurchased",
                render: function (data) {
                    return data && data !== "0000-00-00" ? moment(data).format("YYYY-MM-DD") : "N/A";
                }, visible: false
            },
            {
                data: "dateCreated",
                render: function (data) {
                    return data && data !== "0000-00-00" ? moment(data).format("YYYY-MM-DD") : "N/A";
                }, visible: false
            },
            {data: "is_accounted", visible: false},
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
                title: "Asset Components - " + moment().format('ll'),
                customize: function (excel) {
                },
                exportOptions: {
                    columns: ':visible:not(:eq(0)):not(.actions)'
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    const data = tblAssetComponents.ajax.params();

                    $.ajax({
                        url: baseUrl("ams/assets/get_components_masterfile_list/1"),
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
                title: "Asset Components - " + moment().format('ll'),
                orientation: 'landscape',
                pageSize: 'LEGAL',
                customize: function (doc) {
                    doc.styles.tableHeader.alignment = 'left';
                },
                exportOptions: {
                    columns: ':visible:not(:eq(0)):not(.actions)'
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    const data = tblAssetComponents.ajax.params();
                    $.ajax({
                        url: baseUrl("ams/assets/get_components_masterfile_list/1"),
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
        pageLength: 20
    });

function itemDatatableActions($id, is_borrowed) {
    if ($id) {
        var _actionButton = "";
        if (_currentActions.includes("edit")) {
            _actionButton += "" +
                "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
                "   href='" + baseUrl('ams/assets/edit_asset_component/') + $id + "'" +
                "   data-toggle='m-tooltip' data-original-title='Edit Component' data-placement='bottom'" +
                "   data-delay='{\"show\": 300}'>" +
                "   <i class='la la-edit'></i>" +
                "</a>";
        }

        if (_currentActions.includes("archive")) {
            _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon " +
                "m-btn--icon-only m-btn--pill btnCancel' " +
                "data-id='" + $id + "' onclick='archiveAssetComponent(" + $id + ", " + is_borrowed + ")'" +
                "   data-toggle='m-tooltip' data-original-title='Archive Component' data-placement='bottom'" +
                "   data-delay='{\"show\": 300}'>" +
                "<i class='la la-file-archive-o'></i></button>";
        }

        return _actionButton;
    } else {
        return false;
    }
}

function formatcheck(data, row) {
    if (data) {
        var _checkButton = "<label class='m-checkbox m-checkbox--state-primary'><input type='checkbox' name='asset_id' class='text-gray'  value='"+ data +"'><span></span><label>";
        return _checkButton;
    } else {
        return false;
    }
}

$("#selectall").click(function () {
    var asset_ids = [];
    $('#table-asset-components tbody input[type="checkbox"]').prop('checked', this.checked);
    
    $("input:checkbox[name=asset_id]:checked").each(function(){
        asset_ids.push($(this).val());
    });
    assets_components = asset_ids;
});

$("#table-asset-components").on("click", "tbody input[type='checkbox']", function () {
    var asset_id = [];
    const allCheckboxes = $("#table-asset-components tbody input[type='checkbox']").length;
    const checkedCheckboxes = $("#table-asset-components tbody input[type='checkbox']:checked").length;
    const checked = allCheckboxes <= checkedCheckboxes;
    $('#selectall').prop('checked', checked);
    
    $("input:checkbox[name=asset_id]:checked").each(function(){
        asset_id.push($(this).val());
    });
    assets_components = asset_id;
});

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblAssetComponents.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblAssetComponents.ajax.reload();
});

function archiveAssetComponent(id, is_borrowed) {
    checkIfAssetIsBorrowedOrAccounted(id)
        .then((done) => {
            const accountability = done.accountability;
            const borrowing_history = done.borrowing_history;
            const has_mother_asset = done.has_mother_asset;
            const mother_asset_accountability = done.mother_asset_accountability;

            if (accountability || (borrowing_history || mother_asset_accountability)) { // changed && to || because it prevents prompting the borrowed item
                const modalAlert = $(".cant-archive-alert-dialog");
                const modalBody = modalAlert.find(".modal-body");
                let el = "";
                // if (accountability && mother_asset_accountability) { // commented because it returns blank modal if the component is accounted without mother asset
                if (accountability) {
                    if (has_mother_asset && mother_asset_accountability) {
                        el = "" +
                            "<div class='normal-case m--regular-font-size-lg2'>" +
                            "  <p>" +
                            "       Asset, <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.asset_name + "</span> cannot be archived." +
                            "       Mother asset of this component with asset name, <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" +
                            "   " + mother_asset_accountability.asset_name + "</span> is still accounted to " +
                            "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + mother_asset_accountability.issued_to + "</span>." +
                            "  </p>" +
                            "</div>" +
                            "</br>";
                    } else {
                        el = "" +
                            "<div class='normal-case m--regular-font-size-lg2'>" +
                            "  <p>Asset, <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.asset_name + "</span> cannot be archived." +
                            "       This asset is still accounted to " +
                            "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.issued_to + "</span>." +
                            "  </p>" +
                            "</div>" +
                            "</br>";
                    }
                }

                if (borrowing_history) {
                    el += "" +
                        "<div class='normal-case m--regular-font-size-lg2'>" +
                        "   <p class='mb-1 m--font-bold'>Borrower:</p>" +
                        "   <p>This asset is still in the possession of" +
                        "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + borrowing_history.borrower_name + "</span>." +
                        "   </p>" +
                        "</div>";
                }

                modalBody.html("").append(el);
                modalAlert.modal("show");
            } else {
                const _modal = $(".archive-remarks");
                const form = _modal.find("form");
                form.attr("action", baseUrl("ams/assets/archive_asset_component/?id=" + id));
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
                isComponent: 1
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
                        tblAssetComponents.ajax.reload();
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
                        tblAssetComponents.ajax.reload();
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
    tblAssetComponents.ajax.reload();
}

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

$(".dt-picker").datepicker({
    format: "yyyy-mm-dd",
    todayHighlight: true,
    todayBtn: "linked",
    clearBtn: true,
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
    tblAssetComponents.ajax.reload();
    $("#modal-advance-search").modal("hide");
});

function clear_adv_search() {
    $('#frm-advance-search')[0].reset();
    $("#location").val("").trigger('change');
    $("#cat").val("").trigger('change');
    $("#station").val("").trigger('change');

    advanced_search = {};
    $("#modal-advance-search").modal("hide");
    tblAssetComponents.ajax.reload();
}

function exportAs(type) {
    dropdown.removeClass();
    dropdown.addClass("fa fa-spinner fa-spin");

    setTimeout(() => {
        switch (type) {
            case "excel":
                tblAssetComponents.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblAssetComponents.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}

function showOrHideColumn(index, el) {
    const column = tblAssetComponents.column(index);
    column.visible($(el)[0].checked);
}

$("#column-options.dropdown-menu")
    .click(function (e) {
        e.stopPropagation();
    });

$("#modal-print-barcode")
    .on("shown.bs.modal", function () {
        let name;
        const jsonResponse = tblAssetComponents.ajax.json();
        const data = jsonResponse.data;
        const barcodeContainer = $("#barcodes div.row");
        barcodeContainer.empty();
        if(assets_components.length > 0){
            data.forEach((item, i) => {
                if(item.name){
                    name = item.name;
                }else{
                    name = 'NO ASSIGNED NAME';
                }
                if(assets_components.includes(item.id)){
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

$("#modal-mass-archive").on("shown.bs.modal", function () {
    if (assets_components.length > 0) {
        vmData.count = assets_components.length; //initial count of assets
        vmData.assets = assets_components.length; //count for assets after removing items with accountability, borrowing and mother assets

        $.ajax({
            url: baseUrl('ams/assets/check_multiple_if_borrowed_or_accounted'),
            dataType: "JSON",
            type: "POST",
            data: {
                csrf_token : _csrf_hash,
                type : 'asset',
                isComponent: 1,
                ids : assets_components
            },
            success: function (response) {
                vmData.isAssetClear = vmData.isEmpty(response.accountability) && vmData.isEmpty(response.borrowing_history) ? true : false;
                vmData.rows = {...response};

                setTimeout( function () {
                    acctTable = $('#archive-accountability-table').DataTable({
                        paging: false,
                        searching: false,
                        ordering: false,
                        info: false,
                        responsive: true
                    });

                    acctTable.rows().every(function() {
                        let row = this.node();
                        let components = $(row).data('components');

                        if (components) {
                            let html = '';
                            if (components.length > 0) {
                                html += `<table class="table table-sm table-bordered" width="100%">`;
                                    html += '<thead>';
                                        html += '<tr>';
                                            html += '<th>Components</th>';
                                        html += '</tr>';
                                    html += '</thead>';
                                    html += '<tbody>';
                                        $.each(components, function (index, item) {
                                            html += '<tr>';
                                                html += `<td>${item.asset_name}</td>`;
                                            html += '</tr>';
                                        });
                                    html += '</tbody>';
                                html += `</table>`;

                                this.child(html).show();
                                $(row).addClass('shown');
                            }
                        }
                    });

                    borrTable = $('#archive-borrowing-table').DataTable({
                        paging: false,
                        searching: false,
                        ordering: false,
                        info: false,
                        responsive: true
                    });

                    borrTable.rows().every(function() {
                        let _row = this.node();
                        let _components = $(_row).data('components');

                        if (_components) {
                            let _html = '';
                            if (_components.length > 0) {
                                _html += `<table class="table table-sm table-bordered" width="100%">`;
                                    _html += '<thead>';
                                        _html += '<tr>';
                                            _html += '<th>Components</th>';
                                        _html += '</tr>';
                                    _html += '</thead>';
                                    _html += '<tbody>';
                                        $.each(_components, function (index, item) {
                                            _html += '<tr>';
                                                _html += `<td>${item.asset_name}</td>`;
                                            _html += '</tr>';
                                        });
                                    _html += '</tbody>';
                                _html += `</table>`;

                                this.child(_html).show();
                                $(row).addClass('shown');
                            }
                        }
                    });

                }, 500);

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
    vmData.rows = {...{} };
    vmData.count = 0;
    vmData.assets = 0;
    vmData.isAssetClear = false;
});

const vmData = new Vue({
    el: "#archive-list",
    data: { rows: {}, count: 0, assets: 0, isAssetClear: false },
    methods: {
        isEmpty(arr){
            return $.isEmptyObject(arr);
        }, removeAsset(index, ids, type) {
            const instance = this;

            const row = instance.rows[type].find(({ asset_id }) => asset_id === ids);
            let assetIds = row.components.map(item => item.asset_id);

            /** removing the item to the checkbox ids */
            if (type == 'accountability') {
                if (assetIds.length > 0) {
                    let remaining = assets_components.filter(id => !assetIds.includes(id));
                    assets_components = remaining;
    
                    assetIds.forEach(id => {
                        let checkbox = document.querySelector(`input[type="checkbox"][value="${id}"]`);
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    });
                }
            } else {
                const i = assets_components.indexOf(ids);
                assets_components.splice(i, 1);
                $(`input[type=checkbox][value='${ids}']`).prop('checked', false);
            }
            
            /** removing to the list */
            instance.rows[type].splice(index, 1);
            let _table = type == 'accountability' ? acctTable : borrTable;

            /** removes the child components when removing the parent */
            _table.rows(function (idx, data, node) {
                return $(node).data('asset-id') == ids;
            }).every(function () {
                if (this.child && this.child.isShown()) {
                    this.child.hide();
                }
                this.remove();
            });
            _table.draw();
            /** removes the child components when removing the parent */

            instance.isAssetClear = instance.isEmpty(instance.rows.accountability) && instance.isEmpty(instance.rows.borrowing_history) ? true : false;
            instance.count = assets_components.length;
            instance.assets = assets_components.length;
            /** removing to the list */

            $(".tooltip.bs-tooltip-top").empty();

            const allCheckboxes = $("#table-asset-components tbody input[type='checkbox']").length;
            const checkedCheckboxes = $("#table-asset-components tbody input[type='checkbox']:checked").length;
            const checked = allCheckboxes <= checkedCheckboxes;
            $('#selectall').prop('checked', checked);

            if (instance.isAssetClear) {
                archiveSelect2();
            }
        }
    }
})

function archiveSelect2(){
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
        _data.push({ name: 'ids', value: assets_components }, { name: 'type', value: 'component'});

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
                    tblAssetComponents.ajax.reload();
                } else {
                    toastr.error(response.msg, "", 5000);
                }
            }
        });

        return false;
    }
})