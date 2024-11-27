var _tableModule = $("#table-modules"),
  modalAddModule = $("#modal-add_module"),
  modalEditModule = $("#modal-edit_module"),
  modalModuleDelete = $("#modal-module-delete"),
  modalModuleList = $("#modal-module-list"),
  modalModuleAction = $("#modal-module-actions");

var search_val = "";

var dtModule = $("#table-modules").DataTable({
  dom: '<"toolbar">frtlip',
  serverSide: true,
  processing: true,
  searching: false,
  ajax: {
    url: baseUrl("core/module/get_module_list"),
    type: "POST",
    data: function (d) {
      d.csrf_token = _csrf_hash;
      d.search['value'] = search_val;
      return d;
    }, error: function (xhr, error, code) {
      if (error == "parsererror") { 
          dtModule.ajax.reload(null, false); 
          toastr.warning("Re-loading, error in rendering list data!", "MODULES");
      }
  }
  },
  columns: [
    { data: "name", width: "15%" },
    { data: "label", width: "20%" },
    { data: "description", width: "20%" },
    { data: "url", width: "15%" },
    { data: "identifier", width: "15%" },
    { data: "is_active", width: "7%" },
    { data: null, width: "8%" }
  ],
  columnDefs: [
    {
      data: null,
      defaultContent: "",
      targets: -1,
      orderable: false,
      className: "dt-column-center",
      render: function (data, type, row, meta) {
        return tempDatatableActions(row.id);
      }
    },
    {
      data: "is_active",
      defaultContent: "",
      targets: 5,
      orderable: false,
      className: "dt-column-center",
      render: function (data, type, row, meta) {
        return tempDatatableStatus(row.is_active);
      }
    },
    {
      targets: "_all",
      defaultContent: ""
    }
  ],
  initComplete: function (settings, json) {
    if (typeof moduleActionUpdate == "function") {
      moduleActionUpdate();
    }
    /*** $("div.toolbar").html(_htmlContent); ***/
  }
});

/*** var _htmlContent =
  '<button id="module-new" type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-add_module"><i class="fa fa-plus"></i> New </button>';
_htmlContent +=
  ' <button id="module-list" type="button" class="m-portlet__nav-link btn m-btn--square btn-info btnAssign"><i class="fa fa-list"></i> Tree View </button>'; ***/

function tempDatatableActions($id) {
  if ($id) {
    var _actionButton = "";
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("assign", _currentActions) !== -1
    ) {
      _actionButton +=
        "<button type='button' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnAssign btnModuleAction' data-id='" +
        $id +
        "'><i class='la la-key'></i></button>";
    }
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("edit", _currentActions) !== -1
    ) {
      _actionButton +=
        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnEdit btnEditModule' data-id='" +
        $id +
        "'><i class='la la-edit'></i></button>";
    }
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("delete", _currentActions) !== -1
    ) {
      _actionButton +=
        " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnDelete btnRemoveModule' data-id='" +
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

function moduleActionUpdate() {
  var _editModule = _tableModule.find(".btnEditModule");
  if (typeof _editModule !== "undefined") {
    jQuery(document).on("click", ".btnEditModule", function () {
      var _self = $(this);
      var dataId = _self.data("id");
      if (typeof dataId !== "undefined") {
        $.ajax({
          url: baseUrl("core/module/get_current_module"),
          type: "POST",
          data: { id: dataId, csrf_token: _csrf_hash },
          dataType: "json",
          success: function (json) {
            if (json.response) {
              vmEditModule.post = Object.assign({}, json.row);
              modalEditModule.modal("show");
            }
          }
        });
      }
    });
  }
}

function addModuleValidation() {
  $.validate({
    form: "#form-add_module",
    lang: "en",
    onSuccess: function (form) {
      var currentForm = form[0];
      var formUrl = currentForm.action;
      var formData = $(currentForm).serialize();
      $.ajax({
        url: formUrl,
        type: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function () {
          $(form[0])
            .find(".btn-submit")
            .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data.response) {
            currentForm.reset();
            dtModule.ajax.reload();
            modalAddModule.modal("hide");
            toastr.success(data.toastr_msg, "Module Configuration", 5000);
          } else {
            toastr.error(data.toastr_msg, "Module Configuration", 5000);
          }
          $(form[0])
            .find(".btn-submit")
            .removeClass(
              "m-btn--custom m-loader m-loader--light m-loader--right"
            );
        }
      });
      return false;
    }
  });
}

jQuery(document).ready(function () {
  addModuleValidation();
});

var vmEditModule = new Vue({
  el: "#form-edit_module",
  data: { post: {} },
  mounted: function () {
    $.validate({
      form: "#form-edit_module",
      lang: "en",
      onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
        $.ajax({
          url: formUrl,
          type: "POST",
          dataType: "json",
          data: formData,
          beforeSend: function () {
            $(form[0])
              .find(".btn-submit")
              .addClass(
                "m-btn--custom m-loader m-loader--light m-loader--right"
              );
          },
          success: function (data) {
            if (data.response) {
              currentForm.reset();
              dtModule.ajax.reload(null, false);
              modalEditModule.modal("hide");
              toastr.success(data.toastr_msg, "Module Configuration", 5000);
            } else {
              toastr.error(data.toastr_msg, "Module Configuration", 5000);
            }
            $(form[0])
              .find(".btn-submit")
              .removeClass(
                "m-btn--custom m-loader m-loader--light m-loader--right"
              );
          }
        });
        return false;
      }
    });
  }
});

jQuery(document).on("click", "#module-list", function () {
  var _self = $(this);
  $.ajax({
    url: baseUrl("core/module/get_moduletree_list"),
    beforeSend: function () {
      if (typeof _self !== "undefined") {
        _self.addClass(
          "m-btn--custom m-loader m-loader--light m-loader--right"
        );
      }
    },
    success: function (json) {
      if (json.response) {
        modalModuleList
          .find(".modal-content")
          .empty()
          .append(json.html);
        var treeAcl = modalModuleList.find("#tree_module-list");
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

        jQuery(modalModuleList).modal("show");
      }
      if (typeof _self !== "undefined") {
        _self.removeClass(
          "m-btn--custom m-loader m-loader--light m-loader--right"
        );
      }
    }
  });
});

jQuery(document).on(
  "click",
  "#modal-module-actions .btn-submit-list",
  function () {
    var _self = $(this);
    var dataId = $(this).data("id");
    var _jsTree = modalModuleAction.find("#tree_module-action");
    if (typeof _jsTree !== "undefined") {
      var _jsonData = $(_jsTree)
        .jstree(true)
        .get_json("#", { flat: true });
      var _string = JSON.stringify(_jsonData);

      $.ajax({
        url: baseUrl("core/module/set_module_json_actions"),
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
            toastr.success(json.toastr_msg, "Module - Actions", 5000);
            $(modalModuleAction).modal("hide");
          } else {
            toastr.error(json.toastr_msg, "Error Module - Actions", 5000);
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

jQuery(document).on("click", "#modal-module-list .btn-submit-list", function () {
  var _self = $(this);
  var treeAcl = modalModuleList.find("#tree_module-list");
  if (typeof treeAcl !== "undefined") {
    var jsonData = jQuery(treeAcl)
      .jstree(true)
      .get_json("#", { flat: true });
    var _string = JSON.stringify(jsonData);

    $.ajax({
      url: baseUrl("core/module/get_json_module"),
      type: "POST",
      data: { nodes: _string, csrf_token: _csrf_hash },
      beforeSend: function () {
        if (typeof _self !== "undefined") {
          _self.addClass(
            "m-btn--custom m-loader m-loader--light m-loader--right"
          );
        }
      },
      success: function (json) {
        if (json.response) {
          toastr.success(json.toastr_msg, "Module List", 5000);
          $(modalModuleList).modal("hide");
        } else {
          toastr.error(json.toastr_msg, "Error Module List", 5000);
        }
        if (typeof _self !== "undefined") {
          _self.removeClass(
            "m-btn--custom m-loader m-loader--light m-loader--right"
          );
        }
      }
    });
  }
});

jQuery(document).on("click", "#table-modules .btnModuleAction", function () {
  var _self = $(this);
  var dataId = _self.data("id");
  $.ajax({
    url: baseUrl("core/module/get_assigned_acl_module"),
    type: "POST",
    dataType: "json",
    data: { id: dataId, csrf_token: _csrf_hash },
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
        var _modalContent = modalModuleAction.find(".modal-content");
        if (typeof _modalContent !== "undefined") {
          _modalContent.empty().append(json.html);
          var treeAclAction = modalModuleAction.find("#tree_module-action");
          $(treeAclAction)
            .jstree({
              core: {
                data: json.data,
                dblclick_toggle: false,
                check_callback: true,
                themes: { icons: false }
              },
              checkbox: {
                cascade: "",
                three_state: false
              },
              plugins: ["checkbox", "wholerow"]
            })
            .on("ready.jstree", function () {
              $(this).jstree("open_all");

              var _current = $(this);
              var _treeItem = _current.find("li[role=treeitem]");
              if (typeof _treeItem !== "undefined") {
                _treeItem.each(function (i, v) {
                  var _id = $(v).attr("id");
                  _id = parseInt(_id);
                  if (jQuery.inArray(_id, json.module_id) !== -1) {
                    _current.jstree("select_node", this);
                  }
                });
              }

              var jsTreeCheckbox = $(this).find(
                "i.jstree-icon.jstree-checkbox"
              );
              var jsTreeOcl = $(this).find("i.jstree-icon.jstree-ocl");
              if (typeof jsTreeOcl !== "undefined" && jsTreeOcl.length > 0) {
                jsTreeOcl.remove();
              }
              if (
                typeof jsTreeCheckbox !== "undefined" &&
                jsTreeCheckbox.length > 0
              ) {
                jsTreeCheckbox.css("margin-right", "15px");
              }
            });

          $(modalModuleAction).modal("show");
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

jQuery(document).on("click", "#table-modules .btnRemoveModule", function () {
  var _self = $(this);
  var dataId = _self.data("id");
  var _inptModule = modalModuleDelete.find("#removeModule");
  if (typeof _inptModule !== "undefined") {
    _inptModule.val(dataId);
    $(modalModuleDelete).modal("show");
  }
});

jQuery(document).on("click", ".btn-submit-delete", function () {
  var _btnSubmit = jQuery(this);
  var _dataId = jQuery(this)
    .parent(".modal-footer")
    .parent(".modal-content")
    .children("input#removeModule")
    .val();
  if (typeof _dataId !== "undefined") {
    jQuery.ajax({
      url: baseUrl("core/module/remove_module"),
      type: "post",
      data: { id: _dataId, csrf_token: _csrf_hash },
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
          dtModule.ajax.reload(null, false);
          toastr.success(json.toastr_msg, "Remove Module", 5000);
          $(modalModuleDelete).modal("hide");
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

$('#generalSearch').donetyping(function (callback) {
  search_val = $(this).val();
  dtModule.ajax.reload();
});

