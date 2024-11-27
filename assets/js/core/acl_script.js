var _tableAcl = $("#table-access_control");
var _modalAccessControl = $("#modal-access_control");
var _modalAccessControlEdit = $("#modal-access_control-edit");
var _modalAccessControlDelete = $("#modal-access_control-delete");
var _modalAccessControlList = $("#modal-access_control-list");
var _modalAccessControlAction = $("#modal-access_control-actions");
var search_val = "";

var _dtAccessControl = $("#table-access_control").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    searching: false,
    ajax: {
        url: baseUrl("core/access_control/get_acl_list"),
        type: "POST",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            return d;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") { 
                dtModule.ajax.reload(null, false); 
                toastr.warning("Re-loading, error in rendering list data!", "ACCESS CONTROL");
            }
        }, global: false
    },
    columns: [
        { data: "name", width: "25%" },
        { data: "label", width: "*" },
        { data: "url", width: "20%" },
        { data: "identifier", width: "15%" },
        { data: "is_active", width: "5%" },
        { data: null, className: "text-center", width: "8%" }
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return aclDatatableActions(row.id);
            }
        },
        {
            data: "is_active",
            defaultContent: "",
            targets: 4,
            orderable: false,
            className: "dt-column-center",
            render: function (data, type, row, meta) {
                return aclDatatableStatus(row.is_active);
            }
        },
        {
            targets: "_all",
            defaultContent: ""
        }
    ],
    initComplete: function (settings, json) {
        if (typeof aclActionUpdate == "function") {
            aclActionUpdate();
        }
    }
});
/*** var _htmlContent =
    '<button id="access_control-new" type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-access_control"><i class="fa fa-plus"></i> New </button>';
_htmlContent +=
    ' <button id="access_control-list" type="button" class="m-portlet__nav-link btn m-btn--square btn-info btnAssign"><i class="fa fa-list"></i> Tree View </button>';
$("div.toolbar").html(_htmlContent);
***/
jQuery(document).on("click", "#access_control-list", function () {
    var _self = $(this);
    $.ajax({
        url: baseUrl("core/access_control/list_acl"),
        beforeSend: function () {
            if (typeof _self !== "undefined") {
                _self.addClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
            }
        },
        success: function (json) {
            if (json.response) {
                _modalAccessControlList
                    .find(".modal-content")
                    .empty()
                    .append(json.html);

                var treeAcl = _modalAccessControlList.find("#tree_acl-list");

                $(treeAcl)
                    .jstree({
                        core: {
                            data: json.data,
                            check_callback: true
                        },
                        types: {
                            root: { icon: "fa fa-folder" },
                            child: { icon: "fa fa-file" }
                        },
                        plugins: ["dnd", "types"]
                    })
                    .on("ready.jstree", function () {
                        $(this).jstree("open_all");
                    });

                jQuery(_modalAccessControlList).modal("show");
            }
            if (typeof _self !== "undefined") {
                _self.removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
            }
        }
    });
});

function searchAclList(search = "", _self) {
    $.ajax({
        url: baseUrl("core/access_control/list_acl/?search=" + search),
        success: function (json) {
            if (json.response) {
                var treeAcl = _modalAccessControlList.find("#tree_acl-list");
                $(treeAcl).jstree("destroy");

                console.log(json);

                $(treeAcl)
                    .jstree({
                        core: {
                            data: json.data,
                            check_callback: true
                        },
                        types: {
                            root: { icon: "fa fa-folder" },
                            child: { icon: "fa fa-file" }
                        },
                        plugins: ["dnd", "types"]
                    })
                    .on("ready.jstree", function () {
                        $(this).jstree("open_all");
                    });
            }

            /*if (typeof _self !== "undefined") {
                _self.removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
            }*/
        }
    });
}

let _typingTimer;

_modalAccessControlList
    .on("keyup", "#searchBox", function () {
        const _self = $(this);
        const search = _self.val();

        clearTimeout(_typingTimer);
        _typingTimer = setTimeout(function () {
            searchAclList(search, _self);
        }, 1000);
    });

_modalAccessControlList
    .on("keydown", "#searchBox", function () {
        clearTimeout(_typingTimer);
    });

jQuery(document).on(
    "click",
    "#modal-access_control-list .btn-submit-list",
    function () {
        var _self = $(this);
        var treeAcl = _modalAccessControlList.find("#tree_acl-list");
        if (typeof treeAcl !== "undefined") {
            var jsonData = jQuery(treeAcl)
                .jstree(true)
                .get_json("#", { flat: true });
            var _string = JSON.stringify(jsonData);

            $.ajax({
                url: baseUrl("core/access_control/get_json_acl"),
                type: "POST",
                data: { nodes: _string, csrf_token: _csrf_hash },
                dataType: "json",
                beforeSend: function () {
                    if (typeof _self !== "undefined") {
                        _self.addClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                    }
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Access Control List", 5000);
                        $(_modalAccessControlList).modal("hide");
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(json.toastr_msg, "Error Access Control List", 5000);
                    }
                    if (typeof _self !== "undefined") {
                        _self.removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                    }
                }
            });
        }
    }
);

jQuery(document).on("click", "#access_control-new", function () {
    var inputs = _modalAccessControl.find("input[type=text]");
    if (typeof inputs !== "undefined") {
        jQuery.each(inputs, function (index, object) {
            if (index == 0) {
                setTimeout(function () {
                    jQuery(object).focus();
                }, 500);
            }
            jQuery(object).val("");
        });
    }
});

$.validate({
    form: "#form-access_control",
    lang: "en",
    onSuccess: function (form) {
        var _url = form[0].action;
        var _data = jQuery(form[0]).serialize();
        var _btnSubmit = $(form[0]).find(".btn-submit");

        $.ajax({
            url: _url,
            type: "POST",
            data: _data,
            dataType: "json",
            beforeSend: function () {
                if (typeof _btnSubmit !== "undefined") {
                    _btnSubmit.addClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            },
            success: function (data) {
                if (data.response) {
                    _dtAccessControl.ajax.reload();
                    toastr.success(data.toastr_msg, "Added Access Control", 5000);
                    $("#modal-access_control").modal("hide");
                } else {
                    toastr.error(data.toastr_msg, "Error Access Control", 5000);
                }
                if (typeof _btnSubmit !== "undefined") {
                    _btnSubmit.removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            }
        });
        return false;
    }
});

$.validate({
    form: "#form-access_control-edit",
    lang: "en",
    onSuccess: function (form) {
        var _url = form[0].action;
        var _data = jQuery(form[0]).serialize();
        var _btnSubmit = $(form[0]).find(".btn-submit");

        $.ajax({
            url: _url,
            type: "POST",
            data: _data,
            dataType: "json",
            beforeSend: function () {
                if (typeof _btnSubmit !== "undefined") {
                    _btnSubmit.addClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            },
            success: function (data) {
                if (data.response) {
                    _dtAccessControl.ajax.reload();
                    toastr.success(data.toastr_msg, "Update Access Control", 5000);
                    $("#modal-access_control-edit").modal("hide");
                } else {
                    toastr.error(data.toastr_msg, "Error Access Control", 5000);
                }
                if (typeof _btnSubmit !== "undefined") {
                    _btnSubmit.removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            }
        });
        return false;
    }
});

function aclDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if (
            typeof _currentActions !== "undefined" &&
            jQuery.inArray("assign", _currentActions) !== -1
        ) {
            _actionButton +=
                "<button type='button' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnAssign btnAclAction' data-id='" +
                $id +
                "'><i class='la la-key'></i></button>";
        }
        if (
            typeof _currentActions !== "undefined" &&
            jQuery.inArray("edit", _currentActions) !== -1
        ) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnEdit btnEditAcl' data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
        }
        if (
            typeof _currentActions !== "undefined" &&
            jQuery.inArray("delete", _currentActions) !== -1
        ) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnDelete btnRemoveAcl' data-id='" +
                $id +
                "'><i class='la la-trash'></i></button>";
        }

        if (!_actionButton) {
            _actionButton = "---";
        }
        return _actionButton;
    } else {
        return false;
    }
}

function aclDatatableStatus($isActive) {
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

function aclActionUpdate() {
    var _editAcl = _tableAcl.find(".btnEditAcl");
    if (typeof _editAcl !== "undefined") {
        jQuery(document).on("click", ".btnEditAcl", function () {
            var _self = $(this);
            var dataId = _self.data("id");
            if (typeof dataId !== "undefined") {
                $.ajax({
                    url: baseUrl("core/access_control/get_acl_data"),
                    type: "POST",
                    data: { id: dataId, csrf_token: _csrf_hash },
                    dataType: "json",
                    success: function (data) {
                        if (data.response) {
                            _modalAccessControlEdit
                                .find("input.inptId")
                                .val(data.value["id"]);
                            _modalAccessControlEdit
                                .find("input.inptName")
                                .val(data.value["name"]);
                            _modalAccessControlEdit
                                .find("input.inptLabel")
                                .val(data.value["label"]);
                            _modalAccessControlEdit
                                .find("input.inptUrl")
                                .val(data.value["url"]);
                            _modalAccessControlEdit
                                .find("input.inptIdentifier")
                                .val(data.value["identifier"]);
                            _modalAccessControlEdit
                                .find("input.inptIcon")
                                .val(data.value["icon"]);

                            var _checkedActive = _modalAccessControlEdit.find(
                                "input#status1"
                            );
                            var _checkedInactive = _modalAccessControlEdit.find(
                                "input#status0"
                            );
                            if (
                                typeof _checkedActive !== "undefined" &&
                                typeof _checkedInactive !== "undefined"
                            ) {
                                if (data.value["is_active"] == 1) {
                                    _checkedActive.prop("checked", true);
                                    _checkedInactive.prop("checked", false);
                                } else {
                                    _checkedActive.prop("checked", false);
                                    _checkedInactive.prop("checked", true);
                                }
                            }
                            $(_modalAccessControlEdit).modal("show");
                        }
                    }
                });
            }
        });
    }
}

jQuery(document).on("click", "#table-access_control .btnRemoveAcl", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var _inptAcl = _modalAccessControlDelete.find("#removeAcl");
    if (typeof _inptAcl !== "undefined") {
        _inptAcl.val(dataId);
        $(_modalAccessControlDelete).modal("show");
    }
});

jQuery(document).on("click", ".btn-submit-delete", function () {
    var _btnSubmit = jQuery(this);
    var _dataId = jQuery(this)
        .parent(".modal-footer")
        .parent(".modal-content")
        .children("input#removeAcl")
        .val();
    if (typeof _dataId !== "undefined") {
        jQuery.ajax({
            url: baseUrl("core/access_control/remove_acl"),
            type: "POST",
            data: { id: _dataId, csrf_token: _csrf_hash },
            dataType: "json",
            beforeSend: function () {
                if (typeof _btnSubmit !== "undefined") {
                    if (
                        !_btnSubmit.hasClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        )
                    ) {
                        _btnSubmit.addClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                    }
                }
            },
            success: function (json) {
                if (json.response) {
                    _dtAccessControl.ajax.reload();
                    toastr.success(json.toastr_msg, "Remove Access Control", 5000);
                    $(_modalAccessControlDelete).modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Error Access Control", 5000);
                }
                if (typeof _btnSubmit !== "undefined") {
                    if (
                        _btnSubmit.hasClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        )
                    ) {
                        _btnSubmit.removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                    }
                }
            }
        });
    }
});
jQuery(document).on("click", "#table-access_control .btnAclAction", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("core/access_control/get_acl_actions"),
        type: "POST",
        data: { id: dataId, csrf_token: _csrf_hash },
        dataType: "json",
        beforeSend: function () {
            if (typeof _self !== "undefined") {
                if (
                    !_self.hasClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    )
                ) {
                    _self.addClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            }
        },
        success: function (json) {
            if (json.response) {
                var _modalContent = _modalAccessControlAction.find(".modal-content");
                if (typeof _modalContent !== "undefined") {
                    _modalContent.empty().append(json.html);
                    var treeAclAction = _modalAccessControlAction.find(
                        "#tree_acl-action"
                    );
                    $(treeAclAction)
                        .jstree({
                            core: {
                                data: json.data,
                                check_callback: true
                            },
                            types: {
                                root: { icon: "la la-pencil-square" }
                            },
                            plugins: ["checkbox", "wholerow", "types"]
                        })
                        .on("ready.jstree", function () {
                            console.log(json.acl_id);
                            var _current = $(this);
                            var _treeItem = _current.find("li[role=treeitem]");
                            if (typeof _treeItem !== "undefined") {
                                _treeItem.each(function (i, v) {
                                    var _id = $(v).attr("id");
                                    _id = parseInt(_id);
                                    if (jQuery.inArray(_id, json.acl_id) !== -1) {
                                        _current.jstree("select_node", this);
                                    }
                                });
                            }
                        });

                    $(_modalAccessControlAction).modal("show");
                }
            }
            if (typeof _self !== "undefined") {
                if (
                    _self.hasClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    )
                ) {
                    _self.removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            }
        }
    });
});

jQuery(document).on(
    "click",
    "#modal-access_control-actions .btn-submit-list",
    function () {
        var _self = $(this);
        var dataId = $(this).data("id");
        var _jsTree = _modalAccessControlAction.find("#tree_acl-action");
        if (typeof _jsTree !== "undefined") {
            var _jsonData = $(_jsTree)
                .jstree(true)
                .get_json("#", { flat: true });
            var _string = JSON.stringify(_jsonData);

            $.ajax({
                url: baseUrl("core/access_control/set_acl_json_actions"),
                type: "POST",
                data: { nodes: _string, id: dataId, csrf_token: _csrf_hash },
                dataType: "json",
                beforeSend: function () {
                    if (typeof _self !== "undefined") {
                        if (
                            !_self.hasClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            )
                        ) {
                            _self.addClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            );
                        }
                    }
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Access Control - Actions", 5000);
                        $(_modalAccessControlAction).modal("hide");
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error Access Control - Actions",
                            5000
                        );
                    }
                    if (typeof _self !== "undefined") {
                        _self.removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                    }
                }
            });
        }
    }
);

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    _dtAccessControl.ajax.reload();
});