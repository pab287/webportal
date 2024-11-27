const modalContainer = $("#modal-container");
const tblUnits = $("#table-units");

const dtTblUnits = tblUnits
    .DataTable({
        dom: "rtlip",
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("pms/rates/get_uom"),
            type: "POST",
            dataType: "JSON",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search.value = $("#generalSearch").val()
            }
        },
        columns: [
            {
                data: "uom_code",
                width: "20%",
            },
            {
                data: "uom_desc",
            },
            {
                data: "",
                width: "8%",
                orderable: false,
                className: "text-center",
                render: function (data, meta, row) {
                    let buttons = "";

                    buttons += " " +
                        "<button class='btn btn-default m-btn--icon m-btn m-btn--icon-only m-btn--pill m-btn--hover-primary'" +
                        "        data-toggle='m-tooltip' data-original-title='Edit' data-placement='bottom'" +
                        "        data-skin='dark' data-delay='{\"show\": 300}'" +
                        "       onclick='openEditUnitModal(" + row.id + ", \"" + row.uom_code + "\", \"" + row.uom_desc + "\")'>" +
                        "    <i class='fa fa-edit'></i>" +
                        "</button>";

                    return buttons;
                }
            }
        ]
    });


function openAddUnitModal() {
    $.ajax({
        url: baseUrl("pms/rates/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/units/modals/add_unit_modal",
        },
        success: function (response) {
            const html = response.html;

            modalContainer.empty().append(html);
            modalContainer.modal("show");
        }
    });
}

function openEditUnitModal(id, uom_code, uom_desc) {
    $.ajax({
        url: baseUrl("pms/rates/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/units/modals/edit_unit_modal",
            model: "Rates_m",
            formData: {id, uom_code, uom_desc},
            function_name: "setUnitData"
            // init_modal_data_function: ""
        },
        success: function (response) {
            const html = response.html;

            modalContainer.empty().append(html);
            modalContainer.modal("show");
        }
    });
}

$("#generalSearch")
    .donetyping(function () {
        dtTblUnits.ajax.reload();
    });