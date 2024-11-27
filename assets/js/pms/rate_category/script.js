const tblRateCategory = $("#table-rate-category");
const confirmModal = $("#modal-archive-confirmation");
const modalContainer = $("#modal-container");

let dtRateCategory = tblRateCategory
    .DataTable({
        dom: "rtlip",
        ajax: {
            url: baseUrl("pms/rate_category/get_rate_category"),
            type: "POST",
            dataType: "JSON",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search.value = $("#generalSearch").val();
            }
        },
        processing: true,
        serverSide: true,
        search: false,
        columns: [
            {
                data: "category",
            },
            {
                data: "parent_category",
                render: function (data) {
                    return data ? "<span class='text-primary m--font-bolder'>" + data + "</span>" : "<span class='text-muted'>--</span>";
                }
            },
            {
                data: "",
                width: "8%",
                className: "text-center",
                render: function (data, meta, row) {
                    let buttons = "";

                    buttons = "" +
                        "<button " +
                        "   class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-primary'" +
                        "   data-toggle='m-tooltip' data-original-title='Edit Category' " +
                        "   data-skin='dark' data-delay='{\"show\": 300}'" +
                        "   data-placement='bottom' onclick='openEditCategoryModal(\"" + row.category + "\", " + row.id + ")'>>" +
                        "   <i class='fa fa-edit'></i>" +
                        "</button>";

                    buttons += "" +
                        "<button " +
                        "   class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-warning'" +
                        "   data-toggle='m-tooltip' data-original-title='Archive' " +
                        "   data-skin='dark' data-delay='{\"show\": 300}'" +
                        "   data-placement='bottom' onclick='openConfirmArchive(\"" + row.category + "\", " + row.id + ")'>" +
                        "   <i class='la la-file-archive-o'></i>" +
                        "</button>";

                    return buttons;
                },
                orderable: false,
            },
        ],
    });


$("#generalSearch")
    .donetyping(function () {
        dtRateCategory.ajax.reload();
    });


function openConfirmArchive(category, id) {
    const form = $("#frm-archive-category")[0];
    const p = confirmModal.find(".modal-body > p");
    p.html("Are you sure to archive <span class='m--font-bolder' " +
        "   style='border-bottom: 1px dotted grey;'>" + category + "</span> category?")

    form.setAttribute("cat-id", id);
    confirmModal.modal("show");
}

$("#frm-archive-category")
    .on("submit", function (e) {
        e.preventDefault();
        const catId = $(this).attr("cat-id");

        $.ajax({
            url: baseUrl("pms/rate_category/archive_category/" + catId),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response) {
                    toastr.success("Category successfully archived.", "Category Archived.", 10000);
                } else {
                    toastr.error("An error occurred while archiving.", "Archive Error", 10000);
                }

                confirmModal.modal("hide");
                dtRateCategory.ajax.reload();
            },
        });
    })

function openNewCategoryModal() {
    $.ajax({
        url: baseUrl("pms/rate_category/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/rate_category/modal/add_category_modal",
        },
        success: function (response) {
            const html = response.html;
            modalContainer.empty().append(html);
            modalContainer.modal("show");
        }
    });
}

function openEditCategoryModal(category, id) {
    $.ajax({
        url: baseUrl("pms/rate_category/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/rate_category/modal/edit_category_modal",
            function_name: "getCategoryDetail",
            formData: {category, id},
            model: "Rate_category_m"
        },
        success: function (response) {
            const html = response.html;
            modalContainer.empty().append(html);
            modalContainer.modal("show");
        }
    });
}


$(".m-content")
    .on("submit", "#frm-add-rate-category", function (e) {
        e.preventDefault();
        const url = $(this).attr("action");
        const formData = new FormData(this);
        formData.append("csrf_token", _csrf_hash);

        $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response) {
                    toastr.success("New category was successfully saved.", "Category Saved", 10000);
                    dtRateCategory.ajax.reload();
                } else {
                    toastr.error("An error occurred while saving.", "Save Error", 10000);
                }

                modalContainer.modal("hide");
            }
        })
    })
    .on("submit", "#frm-edit-rate-category", function (e) {
        e.preventDefault();
        const url = $(this).attr("action");
        const formData = new FormData(this);
        formData.append("csrf_token", _csrf_hash);

        $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response) {
                    toastr.success("Category was successfully updated.", "Category Updated", 10000);
                    dtRateCategory.ajax.reload();
                } else {
                    toastr.error("An error occurred while updating.", "Update Error", 10000);
                }

                modalContainer.modal("hide");
            }
        })
    })
    .on("submit", "#frm-category-tree", function (e) {
        e.preventDefault();
        const url = $(this).attr("action");
        const tree = $(this).find("#category-tree");
        const data = tree.jstree(true).get_json("#", { flat: true });

        $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            data: {
                csrf_token: _csrf_hash,
                tree: data
            },
            success: function (response) {
                if (response) {
                    toastr.success("Category arrangement was successfully updated.", "Arrangement Updated", 10000);
                    dtRateCategory.ajax.reload();
                } else {
                    toastr.error("An error occurred while updating.", "Update Error", 10000);
                }

                modalContainer.modal("hide");
            }
        });
    });


function openTreeView() {
    $.ajax({
        url: baseUrl("pms/rate_category/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/rate_category/modal/treeview_category_modal",
            init_modal_data_function: "getCategoryForTree"
        },
        success: function (response) {
            const html = response.html;
            modalContainer.empty().append(html);

            const tree = modalContainer.find("#category-tree");
            loadTree(tree, response.info, false);

            $("#search-tree")
                .donetyping(function () {
                    const key = $(this).val();
                    searchTree(tree, key);
                });

            modalContainer.modal("show");
        }
    });
}

function searchTree(tree, key) {
    $.ajax({
        url: baseUrl("pms/rate_category/search_tree"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            key
        },
        success: function (response) {
            loadTree(tree, response.info, true);
        }
    });
}

function loadTree(tree, data, destroy) {
    if (destroy) {
        $(tree).jstree("destroy");
    }

    $(tree)
        .jstree({
            core: {
                data,
                check_callback: true
            },
            types: {
                root: {icon: "fa fa-folder"},
                child: {icon: "fa fa-file"}
            },
            plugins: ["dnd", "types"]
        })
        .on("ready.jstree", function () {
            $(this).jstree("open_all");
        });
}