var tableItemRate = $("#table-item_rates");
var modalAddItemRate = $("#modal-add_item_rate");
let modalContainer = $("#modal-container");

if (typeof tableItemRate !== "undefined") {
    var search_val = "";
    var dtItemRate = tableItemRate.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/rates/do_post_event/get_rates_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtProjectUnit.ajax.reload(null, false);
                }
            }
        },
        columns: [
            {
                data: "parent",
            },
            {
                data: "label",
            },
            {
                data: "category",
                render: function (data, meta, row) {
                    return data ? data : "--";
                },
            },
            {
                data: "tariff",
                render: function (data) {
                    return data ? parseFloat(data).toLocaleString(undefined, {minimumFractionDigits: 2}) : "0.00";
                },
            },
            {
                data: "unit",
                render: function (data, meta, row) {
                    return data ? data + " (" + row.uom_code + ")" : "--";
                },
            },
            {
                data: "approved_status",
                render: function (data, meta, row) {
                    const status = parseInt(data);
                    let htmlStatus = "";
                    switch (status) {
                        case 0:
                            htmlStatus = "<span class='m-badge m-badge--warning px-2 m--font-bolder'>Pending</span>";
                            break;
                        case 1:
                            htmlStatus = "<span class='m-badge m-badge--success px-2 m--font-bolder'>Approved</span>";
                            break;
                        case 2:
                            htmlStatus = "<span class='m-badge m-badge--danger px-2 m--font-bolder'>Declined</span>";
                            break;
                        default:
                            return "--";
                    }

                    const htmlRemarks = " <br>" +
                        "<p class='mt-2 mb-0 m--regular-font-size-sm1 text-muted'>Remarks: " + row.approved_remarks + "</p>";
                    return row.approved_remarks !== null && row.approved_remarks ? (htmlStatus + htmlRemarks) : htmlStatus;
                },
                width: "15%",
            },
            {
                data: "approved_by",
                render: function (data) {
                    return data ? data : "--";
                },
                width: "15%",
            },
            {
                data: null,
                width: "10%",
                className: "text-center"
            },
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return tempDataTableActions(row);
                }
            },
            {
                targets: "_all",
                defaultContent: ""
            }
        ],
        pageLength: 20
    });

    function tempDataTableActions(row) {
        let _actionButton = "";
        const id = row.id; // tbl item id
        const rate_id = row.rate_id; // tbl rate id

        if (rate_id) {
            if (_currentActions.includes("edit")) {
                _actionButton +=
                    " <button type='button' " +
                    "         class='btn btn-default m-btn m-btn--hover-accent " +
                    "                m-btn--icon m-btn--icon-only m-btn--pill " +
                    "                btnEdit btnEditItemRate' " +
                    "         data-placement='bottom' " +
                    "         data-toggle='m-tooltip' title='' " +
                    "         data-original-title='Edit Rate' " +
                    "         data-id='" + id + "' onclick='openModal(\"edit\",\"" + row.label + "\", " + id + ", " + rate_id + ")'>" +
                    "         <i class='la la-edit'></i>" +
                    " </button>";
            }
        } else {
            if (_currentActions.includes("new")) {
                _actionButton +=
                    " <button type='button' " +
                    "         class='btn btn-default m-btn m-btn--hover-accent " +
                    "                m-btn--icon m-btn--icon-only m-btn--pill " +
                    "                btnEdit btnEditItemRate' " +
                    "         data-placement='bottom' " +
                    "         data-toggle='m-tooltip' title='' " +
                    "         data-original-title='Add Rate' " +
                    "         data-id='" + id + "' onclick='openModal(\"add\", \"" + row.label + "\", " + id + ")'>" +
                    "         <i class='la la-plus'></i>" +
                    " </button> ";
            }
        }

        if (_currentActions.includes("view")) {
            const historyHoverStyle = rate_id ? "m-btn--hover-accent" : "";
            _actionButton +=
                " <button type='button' " + (rate_id ? "" : "btn-default disabled") +
                "         class='btn " + historyHoverStyle +
                "                m-btn--icon m-btn--icon-only m-btn--pill " +
                "                btnEdit btnEditItemRate' " +
                "         data-placement='bottom' " +
                "         data-toggle='m-tooltip' title='' " +
                "         data-original-title='View History' " +
                "         data-id='" + id + "' onclick='openModal(\"history\", \"" + row.label + "\", " + id + ", " + rate_id + ")'>" +
                "         <i class='la la-history'></i>" +
                " </button>";
        }

        if (_currentActions.includes("approve_action") || _currentActions.includes("approving_authority")) {
            const statusActionIsEnabled = (rate_id && parseInt(row.approved_status) === 0);
            const statusButtonHover = statusActionIsEnabled ? "btn-default m-btn--hover-accent" : "";
            _actionButton += ' ' +
                '<div class="m-dropdown m-dropdown--inline m-dropdown--small ' +
                '            m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"' +
                '         data-dropdown-toggle="click" aria-expanded="true">' +
                '        <button class="m-portlet__nav-link m-dropdown__toggle ' +
                '                  btn m-btn m-btn--icon m-btn--icon-only ' +
                '                  m-btn--pill ' + statusButtonHover + '" ' +
                '                  ' + (statusActionIsEnabled ? "" : "disabled") + '' +
                '                 data-toggle="m-tooltip" data-original-title="Approve or Decline"' +
                '                 data-placement="top" data-delay=\'{\"show\": 300}\' data-skin="dark">' +
                '            <i class="la la-ellipsis-v"></i>' +
                '        </button>' +
                '        <div class="m-dropdown__wrapper">' +
                '            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"' +
                '                  style="left: auto; right: 27.6015px;"></span>' +
                '            <div class="m-dropdown__inner">' +
                '                <div class="m-dropdown__body">' +
                '                    <div class="m-dropdown__content">' +
                '                        <ul class="m-nav">' +
                '                            <li class="m-nav__item">' +
                '                                <a href="javascript:void(0);" class="m-nav__link" ' +
                '                                   onclick="openModal(\'set_status\', \'approve\',' + null + ', ' + rate_id + ')">' +
                '                                    <i class="m-nav__link-icon la la-thumbs-o-up m--font-success"></i>' +
                '                                    <span class="m-nav__link-text m--font-success">' +
                '                                        Approve' +
                '                                    </span>' +
                '                                </a>' +
                '                            </li>' +
                '                            <li class="m-nav__item">' +
                '                                <a href="javascript:void(0);" class="m-nav__link" ' +
                '                                   onclick="openModal(\'set_status\', \'decline\',' + null + ', ' + rate_id + ')">' +
                '                                    <i class="m-nav__link-icon la la-thumbs-o-down m--font-danger"></i>' +
                '                                    <span class="m-nav__link-text m--font-danger">' +
                '                                        Decline' +
                '                                    </span>' +
                '                                </a>' +
                '                            </li>' +
                '                        </ul>' +
                '                    </div>' +
                '                </div>' +
                '            </div>' +
                '        </div>' +
                '    </div>';
        }

        return _actionButton;
    }

    function tempDatatableStatus($isActive) {
        var _html = "";
        if ($isActive == 1) {
            _html =
                "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>";
        } else {
            _html =
                "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>";
        }
        return _html;
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtItemRate.ajax.reload();
    });
}

function openModal(mode, label, id = null, rate_id = null) {
    let path, function_name, formData;

    switch (mode) {
        case "add":
            path = "pms/rates/modals/add_rate_modal";
            function_name = "setModalTitle";
            formData = {label, id};
            break;
        case "edit":
            path = "pms/rates/modals/edit_rate_modal";
            function_name = "getRateDetails";
            formData = {label, rate_id};
            break;
        case "set_status":
            path = "pms/rates/modals/approve_or_decline_modal";
            function_name = "setModalTitle";
            formData = {label, rate_id};
            break;
        default:
            path = "pms/rates/modals/rate_update_history_modal";
            function_name = "setModalTitle";
            formData = {label, id, rate_id};
    }

    $.ajax({
        url: baseUrl("pms/rates/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path,
            function_name,
            model: "Rates_m",
            formData,
            init_modal_data_function: "getCategory"
        },
        success: function (response) {
            const html = response.html;
            const category = response.info.category;

            modalContainer.empty().append(html);

            if (mode === 'add' || mode === 'edit') {
                const tree = modalContainer.find("#category-tree");
                $(tree)
                    .jstree({
                        core: {
                            data: category,
                            check_callback: true
                        },
                        types: {
                            root: {icon: "fa fa-folder"},
                            child: {icon: "fa fa-file"}
                        },
                        plugins: ["types", "wholerow", "conditionalselect"]
                    })
                    .on("ready.jstree", function () {
                        $(this).jstree("open_all");

                        if (mode === "edit") {
                            const category_id = $("#category-id").val();
                            $(this).jstree(true).select_node(category_id);
                            $("#category-label").css("color", "#000");
                        }
                    });

                tree.on('select_node.jstree', function (e, data) {
                    const node = data.node;

                    if (node.children.length > 0) {
                        tree.jstree(true).deselect_node(data.node);
                    } else {
                        $("#category-id").val(node.id);
                        $("#category-text").css("border-color", "#ebedf2");

                        $("#category-text").removeClass("error");
                        $(".tree-dropdown-trigger").removeClass("has-error");

                        $("#category-label").css("color", "#34bfa3");

                        $("#category-tree").css("display", "none");

                        $("#category-text").val(node.original.text);
                    }
                });

                $("#category-text").on("click", function () {
                    if ($("#category-tree").css("display") === "block") {
                        $("#category-tree").css("display", "none");
                    } else {
                        $("#category-tree").css("display", "block");
                    }
                });
            }

            modalContainer.modal("show");
        }
    });
}

modalContainer.on("show.bs.modal", function () {
    $(this).find("#unit")
        .select2({
            placeholder: "Select Unit",
            width: "100%",
            dropdownParent: modalContainer
        });

    $(this).find("#approved_by")
        .select2({
            placeholder: "Select Approver",
            width: "100%",
            dropdownParent: modalContainer
        });

    $(".money").maskMoney({allowZero: true});


    $(this).find("#category_id")
        .select2({
            placeholder: "Select Category",
            width: "100%",
            dropdownParent: modalContainer
        });

    const historyTable = $(this).find("#tbl-rate-update-history");
    if (historyTable.length) {
        const rate_id = $(this).find("#rate-id").val();
        const dtHistoryTable = historyTable.dataTable({
            dom: "frtlip",
            ajax: {
                url: baseUrl("pms/rates/get_rate_update_history/" + rate_id),
                dataType: "JSON",
                type: "POST",
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                }
            },
            columns: [
                {
                    data: "category",
                    render: function (data) {
                        return data ? data : "---";
                    }
                },
                {
                    data: "tariff",
                    render: function (data) {
                        return parseFloat(data).toLocaleString(undefined, {minimumFractionDigits: 2});
                    }
                },
                {data: "unit"},
                {
                    data: "approved_status",
                    render: function (data, meta, row) {
                        const status = parseInt(data);
                        let htmlStatus = "";
                        switch (status) {
                            case 0:
                                htmlStatus = "<span class='m-badge m-badge--warning px-2 m--font-bolder'>Pending</span>";
                                break;
                            case 1:
                                htmlStatus = "<span class='m-badge m-badge--success px-2 m--font-bolder'>Approved</span>";
                                break;
                            case 2:
                                htmlStatus = "<span class='m-badge m-badge--danger px-2 m--font-bolder'>Declined</span>";
                                break;
                            default:
                                return "--";
                        }

                        const htmlRemarks = " <br>" +
                            "<p class='mt-2 mb-0 m--regular-font-size-sm1 text-muted'>Remarks: " + row.approved_remarks + "</p>";
                        return row.approved_remarks !== null && row.approved_remarks ? (htmlStatus + htmlRemarks) : htmlStatus;
                    },
                    width: "15%",
                },
                {
                    data: "approved_by",
                    width: "15%",
                },
                {
                    data: "date_approved",
                    render: function (data, meta, row) {
                        if (data) {
                            return moment(data).format("MMM DD, YYYY") + "<br/>" + moment(data).format("hh:mm:ss a");
                        }

                        return "---";
                    },
                    width: "10%"
                },
                {
                    data: "created_by",
                    width: "15%",
                },
                {
                    data: "created_at",
                    render: function (data, meta, row) {
                        if (data) {
                            return moment(data).format("MMM DD, YYYY") + "<br/>" + moment(data).format("hh:mm:ss a");
                        }

                        return "---";
                    },
                    width: "12%"
                },
            ],
            serverSide: true,
            processing: true,
            initComplete: function () {
                var api = dtHistoryTable.api();
                let timer = 0;
                $('.dataTables_filter input')
                    .unbind('.DT')
                    .bind('keyup.DT', function (e) {
                        var value = this.value;

                        clearTimeout(timer);

                        timer = setTimeout(function () {
                            api.search(value).draw();
                        }, 1000);
                    });
            },
            order: [[7, "desc"]],
            createdRow: function (row, data, index) {
                if (parseInt(data.is_current) === 1) {
                    $(row).css("background-color", "#36a3f724");
                }
            },
            autoWidth: false,
        });
    }
});

$(".m-content")
    .on("submit", "#frm-approve-or-decline", function (e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr("action");

        $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            data: form.serialize(),
            success: function (response) {
                if (response.success) {
                    dtItemRate.ajax.reload();
                    modalContainer.modal("hide");
                    let msgTitle = "";
                    if (parseInt(response.status) === 1) {
                        msgTitle = "Rate was Approved.";
                    } else {
                        msgTitle = "Rate was Declined.";
                    }

                    toastr.success("Status successfully updated.", msgTitle, 10000);
                } else {
                    toastr.error("An error occurred.", "Status Update Error", 10000);
                }
            }
        });
    });

$(document)
    .mouseup(function (e) {
        const container = $('.tree-dropdown-container');
        const searchList = $('.tree');

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            searchList.css("display", "none");
        }
    });