var _tablePrivilege = $("#table-privilege");
var _modalPrivigeEdit = $("#modal-privilege-edit");
var _modalPrivigeDelete = $("#modal-privilege-delete");
var search_val = "";

var _dtPrivilege = $("#table-privilege").DataTable({
  dom: '<"toolbar">frtlip',
  serverSide: true,
  processing: true,
  searching: false,
  ajax: {
    url: baseUrl("core/privilege/get_privilege_list"),
    type: "POST",
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash;
      d.search['value'] = search_val;
      return d;
    }, error: function (xhr, error, code) {
      if (error == "parsererror") { 
        _dtPrivilege.ajax.reload(null, false); 
          toastr.warning("Re-loading, error in rendering list data!", "MANAGE PRIVILEGE");
      }
    }, global: false,
  },
  columns: [
    { data: "name", width: "15%" },
    { data: "label", width: "20%" },
    { data: "description", width: "*" },
    { data: "status", width: "6%" },
    { data: null, className:"text-center", width: "6%" }
  ],
  columnDefs: [
    {
      data: null,
      defaultContent: "",
      targets: -1,
      orderable: false,
      render: function (data, type, row, meta) {
        return privDatatableActions(row.id);
      }
    },
    {
      data: "status",
      defaultContent: "",
      targets: 3,
      orderable: false,
      className: "dt-column-center",
      render: function (data, type, row, meta) {
        return privDatatableStatus(row.status);
      }
    },
    {
      targets: "_all",
      defaultContent: ""
    }
  ],
  initComplete: function (settings, json) {
    if (typeof privActionUpdate == "function") {
      privActionUpdate();
    }
  }
});

/*** var _htmlContent =
  '<button id="privilege-new" type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-privilege-new"><i class="fa fa-plus"></i> New </button>';
$("div.toolbar").html(_htmlContent); ***/

$.validate({
  form: "#form-privilege",
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
          _dtPrivilege.ajax.reload();
          toastr.success(data.toastr_msg, "Added Privilege", 5000);
          $("#modal-privilege-new").modal("hide");
        } else {
          toastr.error(data.toastr_msg, "Error Privilege", 5000);
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
  form: "#form-privilege-edit",
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
          _dtPrivilege.ajax.reload();
          toastr.success(data.toastr_msg, "Update Privilege", 5000);
          $("#modal-privilege-edit").modal("hide");
        } else {
          toastr.error(data.toastr_msg, "Error Privilege", 5000);
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

function privDatatableActions($id) {
  if ($id) {
    var _actionButton = "";
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("edit", _currentActions) !== -1
    ) {
      _actionButton +=
        "<button type='button' class='btn btn-default m-btn btn-sm m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditPriv' data-id='" +
        $id +
        "'><i class='la la-edit'></i></button>";
    }
    if (
      typeof _currentActions !== "undefined" &&
      jQuery.inArray("delete", _currentActions) !== -1
    ) {
      _actionButton +=
        " <button type='button' class='btn btn-default m-btn btn-sm m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemovePriv' data-id='" +
        $id +
        "'><i class='la la-trash'></i></button>";
    }
    return _actionButton;
  } else {
    return false;
  }
}
function privDatatableStatus($isActive) {
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

function privActionUpdate() {
  var _editPrivilege = _tablePrivilege.find(".btnEditPriv");
  if (typeof _editPrivilege !== "undefined") {
    jQuery(document).on("click", ".btnEditPriv", function () {
      var _self = $(this);
      var dataId = _self.data("id");
      if (typeof dataId !== "undefined") {
        $.ajax({
          url: baseUrl("core/privilege/get_privilege_data"),
          type: "POST",
          data: { id: dataId, csrf_token: _csrf_hash },
          dataType: "json",
          success: function (data) {
            if (data.response) {
              _modalPrivigeEdit.find("input.inptId").val(data.value["id"]);
              _modalPrivigeEdit.find("input.inptName").val(data.value["name"]);
              _modalPrivigeEdit
                .find("input.inptLabel")
                .val(data.value["label"]);
              _modalPrivigeEdit
                .find("input.inptDescription")
                .val(data.value["description"]);

              var _checkedActive = _modalPrivigeEdit.find("input#status1");
              var _checkedInactive = _modalPrivigeEdit.find("input#status0");
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
              $(_modalPrivigeEdit).modal("show");
            }
          }
        });
      }
    });
  }
}

jQuery(document).on("click", "#table-privilege .btnRemovePriv", function () {
  var _self = $(this);
  var dataId = _self.data("id");
  var _inptPriv = _modalPrivigeDelete.find("#removePrivilege");
  if (typeof _inptPriv !== "undefined") {
    _inptPriv.val(dataId);
    $(_modalPrivigeDelete).modal("show");
  }
});
jQuery(document).on("click", ".btn-submit-delete", function () {
  var _btnSubmit = jQuery(this);
  var _dataId = jQuery(this)
    .parent(".modal-footer")
    .parent(".modal-content")
    .children("input#removePrivilege")
    .val();
  if (typeof _dataId !== "undefined") {
    jQuery.ajax({
      url: baseUrl("core/privilege/remove_privilege"),
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
          _dtPrivilege.ajax.reload();
          toastr.success(json.toastr_msg, "Remove Privilege", 5000);
          $(_modalPrivigeDelete).modal("hide");
        } else {
          toastr.error(json.toastr_msg, "Error Privilege", 5000);
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
  _dtPrivilege.ajax.reload();
});