var search_val = "";
// var typingTimer; //timer identifier
// var doneTypingInterval = 1000;
var $searchHire = $('#generalSearch');

$searchHire.on('keyup', function() {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(doneTyping1, doneTypingInterval);
});
$searchHire.on('keydown', function() {
  clearTimeout(typingTimer);
});

function doneTyping1() {
  search_val = $searchHire.val();
  tblResume.ajax.reload();
}


var tblResume = $("#table-hired")
    .DataTable({
        dom: 'rtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("crs/get_hired_collection"),
            type: "post",
            global: false,
            dataType: "json",

            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        searching: false,
        order: [0, "desc"],
        columns: [
          {data: "hired_dt",visible: false},
          {data: "id",visible: false},
            { data: "name", width: "13%" },
            {
                data: "school",
                width: "12%",
                render: function (data) {
                    return formatTag(data);
                }
            }, {
                data: "course",
                width: "10%",
                render: function (data) {
                    return formatTag(data);
                }
            }, {
                data: "position",
                width: "10%",
                render: function (data) {
                    return formatTag(data);
                }
            }, {
                data: "tag1",
                width: "15%",
                render: function (data) {
                    return formatTag(data);
                }
            }, {
                data: "recruitment",
                width: "10%"
            }, {
                data: "applied_dt",
                width: "10%"
            }, {
                data: null,
                width: "5%",
                className: "text-center",
                orderable: false,
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.status);
                },
            },
        ]
    });

function formatTag(data) {
    if (data.charAt(0) === ",") {
        return data.substr(1);
    } else {
        return data;
    }
}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if ($.inArray("view", _currentActions) !== -1) {
            _actionButton += "<button type='button' class='btn btnView m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill' onclick='getHiredResume(" + $id + ");'><i class='la la-eye'></i></button>";
        }
        return _actionButton;
    } else {
        return "";
    }
}

var vmHiredResume = new Vue({
    el: "#modal-hired_content",
    data: { row: {} },
});

function getHiredResume($id) {
    if ($id) {
        $.ajax({
            url: siteUrl("crs/get_hired_resume/" + $id),
            dataType: "json",
            success: function (json) {
                vmHiredResume.row = Object.assign({});
                if (json.response) {
                    vmHiredResume.row = Object.assign({}, json.data);
                    $("#modal-hired-resume-dialog").modal("show");
                } else {
                    toastr.error("No data to render", "Hire Personnel", { timeOut: 5000 });
                }
            }
        });
    } else {
        return false;
    }
}