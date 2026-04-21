var _tableRole = $("#table-roles");
var _modalUserRole = $("#modal-user_role-new");
var _modalUserRoleEdit = $("#modal-user_role-edit");
var _modalUserRoleDelete = $("#modal-user_role-delete");
var _modalUserModuleAssign = $("#modal-user_module-assign");
var _modalUserRoleAssign = $("#modal-user_role-assign");
var _modalUserRolePrivilege = $("#modal-user_role-privilege");
var search_val = "";
let globalRawValue = {};

(function ($, undefined) {
  "use strict";
  $.jstree.plugins.noclose = function () {
    this.close_node = $.noop;
  };
})(jQuery);

var _dtUserRole = $("#table-roles").DataTable({
  dom: '<"toolbar">frtlip',
  serverSide: true,
  processing: true,
  searching: false,
  ajax: {
    url: baseUrl("core/roles/get_roles_list"),
    type: "POST",
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash;
      d.search['value'] = search_val;
      return d;
    }, error: function (xhr, error, code) {
      if (error == "parsererror") { 
          _dtUserRole.ajax.reload(null, false); 
          toastr.warning("Re-loading, error in rendering list data!", "MANAGE ROLES LIST");
      }
    }, global: false,
  },
  columns: [
    { data: "name", width: "25%" },
    { data: "description", width: "*" },
    { data: "status", width: "10%" },
    { data: null, className: "text-center", width: "12%" }
  ],
  columnDefs: [
    {
      data: null,
      defaultContent: "",
      targets: 2,
      orderable: false,
      className: "dt-column-center",
      render: function (data, type, row, meta) {
        return roleDatatableStatus(row.status);
      }
    },
    {
      data: null,
      defaultContent: "",
      targets: -1,
      orderable: false,
      render: function (data, type, row, meta) {
        return roleDatatableActions(row.id);
      }
    },
    {
      targets: "_all",
      defaultContent: ""
    }
  ],
  initComplete: function (settings, json) {
    if (typeof roleActionUpdate == "function") {
      roleActionUpdate();
    }
  }
});

jQuery(document).on("click", "#user_role-new", function () {
  var inputs = _modalUserRole.find("input[type=text]");
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

function roleDatatableActions($id) {
  if ($id) {
    var _actionButton = "";
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("assign", _currentActions) !== -1
    ) {
      _actionButton +=
        "<button type='button' class='btn btn-default m-btn btn-sm m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnAssignModule' data-id='" +
        $id +
        "'><i class='fa fa-cubes'></i></button>";
      _actionButton +=
        "<button type='button' class='btn btn-default m-btn btn-sm m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnAssignRole' data-id='" +
        $id +
        "'><i class='la la-tasks'></i></button>";
      _actionButton +=
        " <button type='button' class='btn btn-default m-btn btn-sm  m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnRolePrivilege' data-id='" +
        $id +
        "'><i class='la la-key'></i></button>";
    }
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("edit", _currentActions) !== -1
    ) {
      _actionButton +=
        " <button type='button' class='btn btn-default m-btn btn-sm  m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditRole' data-id='" +
        $id +
        "'><i class='la la-edit'></i></button>";
    }
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("delete", _currentActions) !== -1
    ) {
      _actionButton +=
        " <button type='button' class='btn btn-default m-btn btn-sm m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemoveRole' data-id='" +
        $id +
        "'><i class='la la-trash'></i></button>";
    }
    return _actionButton;
  } else {
    return false;
  }
}

function roleDatatableStatus($isActive) {
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
/*** $("div.toolbar").html(
  '<button type="button" id="user_role-new" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-user_role-new"><i class="fa fa-plus"></i> New </button>'
); ***/

$.validate({
  form: "#form-roles",
  lang: "en",
  onSuccess: function (form) {
    $.ajax({
      url: form[0].action,
      type: "POST",
      data: $("#form-roles").serialize(),
      dataType: "json",
      beforeSend: function () {
        $(".btn-submit").addClass(
          "m-btn--custom m-loader m-loader--light m-loader--right"
        );
      },
      success: function (data) {
        if (data.response) {
          _dtUserRole.ajax.reload(null, false);
          toastr.success(data.toastr_msg, "Added User Role", 5000);
          $(_modalUserRole).modal("hide");
        } else {
          toastr.error(data.toastr_msg, "Error User Role", 5000);
        }
        $(".btn-submit").removeClass(
          "m-btn--custom m-loader m-loader--light m-loader--right"
        );
      }
    });
    return false;
  }
});

$.validate({
  form: "#form-roles-edit",
  lang: "en",
  onSuccess: function (form) {
    $.ajax({
      url: form[0].action,
      type: "POST",
      data: $("#form-roles-edit").serialize(),
      dataType: "json",
      beforeSend: function () {
        $(".btn-submit").addClass(
          "m-btn--custom m-loader m-loader--light m-loader--right"
        );
      },
      success: function (data) {
        if (data.response) {
          _dtUserRole.ajax.reload(null, false);
          toastr.success(data.toastr_msg, "Update User Role", 5000);
          $(_modalUserRoleEdit).modal("hide");
        } else {
          toastr.error(data.toastr_msg, "Error User Role", 5000);
        }
        $(".btn-submit").removeClass(
          "m-btn--custom m-loader m-loader--light m-loader--right"
        );
      }
    });
    return false;
  }
});

function roleActionUpdate() {
  var _editRole = _tableRole.find(".btnEditRole");
  if (typeof _editRole !== "undefined") {
    jQuery(document).on("click", ".btnEditRole", function () {
      var _self = $(this);
      var dataId = _self.data("id");
      if (typeof dataId !== "undefined") {
        $.ajax({
          url: baseUrl("core/roles/get_role_data"),
          type: "POST",
          data: { id: dataId, csrf_token: _csrf_hash },
          success: function (data) {
            if (data.response) {
              const { value } = data;
              _modalUserRoleEdit.find("input.inptId").val(data.value["id"]);
              _modalUserRoleEdit.find("input.inptName").val(data.value["name"]);
              _modalUserRoleEdit
                .find("textarea.txtDescription")
                .val(data.value["description"]);

              var _checkedActive = _modalUserRoleEdit.find("input#status1");
              var _checkedInactive = _modalUserRoleEdit.find("input#status0");
              if (
                typeof _checkedActive !== "undefined" &&
                typeof _checkedInactive !== "undefined"
              ) {
                if (data.value["status"] == 1) {
                  _checkedActive.prop("checked", true);
                  _checkedInactive.prop("checked", false);
                } else {
                  _checkedActive.prop("checked", false);
                  _checkedInactive.prop("checked", true);
                }
              }
              
              globalRawValue = { ...value };
              $(_modalUserRoleEdit).modal("show");
            }
          }
        });
      }
    });
  }
}

jQuery(document).on("click", "#table-roles .btnRemoveRole", function () {
  var _self = $(this);
  var dataId = _self.data("id");
  var _inptAcl = _modalUserRoleDelete.find("#removeRole");
  if (typeof _inptAcl !== "undefined") {
    _inptAcl.val(dataId);
    $(_modalUserRoleDelete).modal("show");
  }
});
jQuery(document).on("click", ".btn-submit-delete", function () {
  var _btnSubmit = jQuery(this);
  var _dataId = jQuery(this)
    .parent(".modal-footer")
    .parent(".modal-content")
    .children("input#removeRole")
    .val();
  if (typeof _dataId !== "undefined") {
    jQuery.ajax({
      url: baseUrl("core/roles/remove_role"),
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
          _dtUserRole.ajax.reload(null, false);
          toastr.success(json.toastr_msg, "Remove User Role", 5000);
          $(_modalUserRoleDelete).modal("hide");
        } else {
          toastr.error(json.toastr_msg, "Error User Role", 5000);
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

jQuery(document).on("click", "#table-roles .btnAssignModule", function () {
  var _self = jQuery(this);
  var dataId = _self.data("id");
  jQuery.ajax({
    url: baseUrl("core/roles/get_assigned_module_role"),
    type: "POST",
    data: { id: dataId, csrf_token: _csrf_hash },
    dataType: "json",
    success: function (json) {
      if (json.response) {
        _modalUserModuleAssign
          .find(".modal-content")
          .empty()
          .append(json.html);
        var treeRole = _modalUserModuleAssign.find("#tree_module-actions");
        $(treeRole)
          .jstree({
            core: {
              data: json.data,
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

            var _treeItem = $(this).find("li[role=treeitem]");
            if (typeof _treeItem !== "undefined") {
              _treeItem.each(function (i, v) {
                var _id = $(v).attr("id");
                _id = parseInt(_id);
                if (jQuery.inArray(_id, json.role_id) !== -1) {
                  $(this).jstree("select_node", this);
                }
              });
            }
            var jsTreeCheckbox = $(this).find("i.jstree-icon.jstree-checkbox");
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

        $(_modalUserModuleAssign).modal("show");
      }
    }
  });
});

jQuery(document).on("click", "#table-roles .btnAssignRole", function () {
  var _self = jQuery(this);
  var dataId = _self.data("id");
  jQuery.ajax({
    url: baseUrl("core/roles/get_assigned_role"),
    type: "POST",
    data: { id: dataId, csrf_token: _csrf_hash },
    dataType: "json",
    success: function (json) {
      if (json.response) {
        _modalUserRoleAssign
          .find(".modal-content")
          .empty()
          .append(json.html);
        var treeRole = _modalUserRoleAssign.find("#tree_role-list");
        $(treeRole)
          .jstree({
            core: {
              data: json.data,
              check_callback: true,
              themes: { icons: false }
            },
            checkbox: {
              cascade: "down",
              three_state: false
            },
            plugins: ["checkbox", "wholerow"]
          })
          .on("ready.jstree", function () {
            $(this).jstree("open_all");

            /*** var _treeItem = $(this).find("li[role=treeitem]");
            if (typeof _treeItem !== "undefined") {
              _treeItem.each(function (i, v) {
                var _id = $(v).attr("id");
                if (jQuery.inArray(_id, json.role_id) !== -1) {
                  $(this).jstree("select_node", this);
                }
              });
            } ***/
            var jsTreeCheckbox = $(this).find("i.jstree-icon.jstree-checkbox");
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
            /*** var jsTreeParent = $(this).find(".no_checkbox");
            if (
              typeof jsTreeParent !== "undefined" &&
              jsTreeParent.length > 0
            ) {
              jsTreeParent.find(".jstree-checkbox").remove();
            } ***/
          });

        $(_modalUserRoleAssign).modal("show");
      }
    }
  });
});

jQuery(document).on(
  "click",
  "#modal-user_module-assign .btn-submit-save",
  function () {
    var _self = $(this);
    var _id = _self.data("id");
    var treeRole = _modalUserModuleAssign.find("#tree_module-actions");
    if (typeof treeRole !== "undefined") {
      var jsonData = jQuery(treeRole)
        .jstree(true)
        .get_json("#", { flat: true });
      var _string = JSON.stringify(jsonData);

      $.ajax({
        url: baseUrl("core/roles/set_json_module_role"),
        type: "post",
        data: { nodes: _string, id: _id, csrf_token: _csrf_hash },
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
            toastr.success(json.toastr_msg, "Assigned User Role", 5000);
            $(_modalUserModuleAssign).modal("hide");
          } else {
            toastr.error(json.toastr_msg, "Error Assigned User Role", 5000);
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

jQuery(document).on(
  "click",
  "#modal-user_role-assign .btn-submit-save",
  function () {
    var _self = $(this);
    var _id = _self.data("id");
    var treeRole = _modalUserRoleAssign.find("#tree_role-list");
    if (typeof treeRole !== "undefined") {
      var jsonData = jQuery(treeRole)
        .jstree(true)
        .get_json("#", { flat: true });
      var _string = JSON.stringify(jsonData);

      $.ajax({
        url: baseUrl("core/roles/get_json_role"),
        type: "post",
        data: { nodes: _string, id: _id, csrf_token: _csrf_hash },
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
            toastr.success(json.toastr_msg, "Assigned User Role", 5000);
            $(_modalUserRoleAssign).modal("hide");
          } else {
            toastr.error(json.toastr_msg, "Error Assigned User Role", 5000);
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

jQuery(document).on("click", "#table-roles .btnRolePrivilege", function () {
  var _self = $(this);
  var _dataId = _self.data("id");

  $.ajax({
    url: baseUrl("core/roles/get_role_privilege_data"),
    type: "POST",
    data: { id: _dataId, csrf_token: _csrf_hash },
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
        _modalUserRolePrivilege
          .find(".modal-content")
          .empty()
          .append(json.html);
        var treeRole = _modalUserRolePrivilege.find("#tree_role-actions");
        $(treeRole).jstree("destroy");
        $(treeRole)
          .jstree({
            core: {
              data: json.data,
              check_callback: true,
              themes: { icons: false }
            },
            checkbox: {
              cascade: "down",
              three_state: false
            },
            plugins: ["checkbox", "wholerow"]
          })
          .on("ready.jstree", function () {
            $(this).jstree("open_all");
            /*** var _treeItem = $(this).find("li[role=treeitem]");
            if (typeof _treeItem !== "undefined") {
              _treeItem.each(function (i, v) {
                var _id = $(v).attr("id");
                if (jQuery.inArray(_id, json.role_id) !== -1) {
                  $(this).jstree("select_node", this);
                }
              });
            }
            ***/
            var jsTreeCheckbox = $(this).find("i.jstree-icon.jstree-checkbox");
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
            /***
            var jsTreeParent = $(this).find(".no_checkbox");
            if (
              typeof jsTreeParent !== "undefined" &&
              jsTreeParent.length > 0
            ) {
              jsTreeParent.find(".jstree-checkbox").remove();
            } ***/
          });

        setTimeout(function () {
          $(_modalUserRolePrivilege).modal("show");
        }, 500);
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
  "#modal-user_role-privilege .btn-submit-save",
  function () {
    var _self = $(this);
    var _id = _self.data("id");

    var treeRoleActions = _modalUserRolePrivilege.find("#tree_role-actions");
    if (typeof treeRoleActions !== "undefined") {
      var jsonData = jQuery(treeRoleActions)
        .jstree(true)
        .get_json("#", { flat: true });
      var _string = JSON.stringify(jsonData);

      $.ajax({
        url: baseUrl("core/roles/get_json_role_actions"),
        type: "post",
        data: { nodes: _string, id: _id, csrf_token: _csrf_hash },
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
            toastr.success(json.toastr_msg, "Assigned User Role", 5000);
            _modalUserRolePrivilege.modal("hide");
          } else {
            toastr.error(json.toastr_msg, "Error Assigned User Role", 5000);
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
  _dtUserRole.ajax.reload();
});

$(".clonableUserRole", _modalUserRoleEdit).on("click", function(){
  const { id, name, description } = globalRawValue;  
  Swal.fire({
        title: 'Clone User Role?',
        text: "Are you sure you want to clone this user role `"+ description.toUpperCase() +"`?",
        icon: 'question',
        input: "textarea",
        inputLabel: "New User Role Description",
        inputValidator: (result) => {
            return !result && "Role description is required!";
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Clone it!',
        allowOutsideClick: false,
        target: document.querySelector('.modal.show') || document.body,
    }).then((result) => {
        if (result.isConfirmed && typeof result.value !== "undefined" && result.value) {
          $.ajax({
            url: siteUrl("core/roles/clone_user_role"),
            type: "POST",
            data: {
              csrf_token: _csrf_hash,
              id: id,
              name: name,
              description: result.value, 
            },
            dataType: "json",
            success: function(json){
              if(json.response){
                toastr.success(json.toastr_msg, "Clone User Role", 5000);
                _dtUserRole.ajax.reload(null, false);
                $(_modalUserRoleEdit).modal("hide");
              }else{
                toastr.success(json.toastr_msg, "Error - Clone User Role", 5000);
              }
            }
          });
        }
    });
});