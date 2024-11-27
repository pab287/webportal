let images = [];
let primaryImage = null;
let defaultPrimary = null;
let uploadedImagesToRemove = [];

const dataURItoBlob = (dataURI) => {
    const bytes = dataURI.split(',')[0].indexOf('base64') >= 0
        ? atob(dataURI.split(',')[1])
        : unescape(dataURI.split(',')[1]);
    const mime = dataURI.split(',')[0].split(':')[1].split(';')[0];
    const max = bytes.length;
    const ia = new Uint8Array(max);
    for (let i = 0; i < max; i += 1) ia[i] = bytes.charCodeAt(i);
    return new Blob([ia], { type: mime });
};

const resizeImage = ({ file, maxSize }) => {
    const reader = new FileReader();
    const image = new Image();
    const canvas = document.createElement('canvas');

    const resize = () => {
        let { width, height } = image;

        if (width > height) {
            if (width > maxSize) {
                height *= maxSize / width;
                width = maxSize;
            }
        } else if (height > maxSize) {
            width *= maxSize / height;
            height = maxSize;
        }

        canvas.width = width;
        canvas.height = height;
        canvas.getContext('2d').drawImage(image, 0, 0, width, height);

        const dataUrl = canvas.toDataURL('image/jpeg');

        return dataURItoBlob(dataUrl);
    };

    return new Promise((ok, no) => {
        if (!file.type.match(/image.*/)) {
            no(new Error('Not an image'));
            return;
        }

        reader.onload = (readerEvent) => {
            image.onload = () => ok(resize());
            image.src = readerEvent.target.result;
        };

        reader.readAsDataURL(file);
    });
};

// initialize damage checkbox on exlcude reason dialog
const isDamageVal = $("#exclude-reason-dialog input[name='isDamage']").is(":checked") ? "YES" : "NO";
$(".is-damage-identifier").html(isDamageVal);

const excludeReasonDialog = $("#exclude-reason-dialog");
const removeComponentConfirmDialog = $("#remove-component-confirm-dialog");
const reIncludeComponentConfirmDialog = $("#re-include-component-confirm-dialog");

const id = window.location.pathname.split("/").pop();
getPrimaryPic(id);

let tblAllComponentList = $("#all-component-list")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        order: [[1, "asc"]],
        ajax: {
            url: baseUrl("ams/vehicles/get_vehicle_components_collection/1"),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-all-component-list").val();
            }
        },
        searching: false,
        columns: [
            {
                data: "image",
                width: "7%",
                orderable: false,
                render: function (data, type, row) {
                    if (data) {
                        return '<a data-lightbox="roadtrip" data-title="' + row.primary_pic + '" href="' + data + '">' +
                            '       <div class="table-avatar" style="background-image: url(' + data + ')"></div>' +
                            '   </a>';
                    } else {
                        return '<div class="table-avatar" title="No image available.">' +
                            '       <i class="la la-truck"></i>' +
                            '   </div>';
                    }
                },
                // className: "d-flex justify-content-center"
            },
            { data: "assetacode", width: "13%" },
            {
                data: "description", width: '30%',
                render: function (data, type, row) {
                    /*let template = data;
                    template += (data !== row.description) ? "<br/>" +
                        "<p class='text-muted m--regular-font-size-sm2 mt-1'>" + row.description + "</p>" : "";
                    return template;*/

                    return data;
                }
            },
            {
                data: "",
                width: "8%",
                defaultContent: "",
                className: "text-center",
                orderable: false,
                render: function (data, type, row) {
                    return "<button type='button' " +
                        "        title='Include Component' " +
                        "        class='btn btn-default m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnNew'" +
                        "        onclick='saveComponentToVehicle(" + row.id + ", this)' data-placement='bottom'>" +
                        "<i class='fa fa-check'></i></button>";
                }
            },
        ]
    });

function dateFormatter(date) {
    const month_names = ["Jan", "Feb", "Mar",
        "Apr", "May", "Jun",
        "Jul", "Aug", "Sep",
        "Oct", "Nov", "Dec"];

    const day = date.getDate();
    const month_index = date.getMonth();
    const year = date.getFullYear();

    return year + " " + month_names[month_index] + ", " + day;
}

$("#search-all-component-list")
    .donetyping(function () {
        tblAllComponentList.ajax.reload();
    });

$(".dt-picker").datepicker({
    format: "yyyy/mm/dd",
    todayHighlight: true,
    todayBtn: "linked",
    clearBtn: true,
});

function resetSelect2() {
    $("#select2-company").val('').trigger('change');
    $("#select2-asset-category").val('').trigger('change');
    $("#select2-vehicle-type").val('').trigger('change');
    $("#select2-sub-category").val('').trigger('change');
    $("#select2-location").val('').trigger('change');
    $("#select2-status").val('').trigger('change');
}

function resetDialogSelect2() {
    $("#dlg-select2-company").val('').trigger('change');
    $("#dlg-select2-asset-category").val('').trigger('change');
    $("#dlg-select2-vehicle-type").val('').trigger('change');
    $("#dlg-select2-sub-category").val('').trigger('change');
    $("#dlg-select2-location").val('').trigger('change');
    $("#dlg-select2-status").val('').trigger('change');
}

// init select2 company
$("#select2-company, #dlg-select2-company")
    .select2({
        placeholder: 'Select Company',
        width: '100%',
        ajax: {
            url: baseUrl("ams/vehicles/get_company_collection"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                const results = data.results.map((item) => {
                    return {
                        id: item.code,
                        text: item.description
                    }
                });

                return {
                    results
                }
            }
        }
    });

// init select2 category
$("#select2-asset-category")
    .select2({
        placeholder: 'Select Category',
        width: '100%',
        ajax: {
            url: baseUrl("ams/assets/get_category_collection/vehicle"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                return {
                    results: data.results.map((item) => {
                        return {
                            id: item.cat_id,
                            text: item.text
                        }
                    })
                };
            }

        }
    })
    .on('select2:select', function (e) {
        const data = e.params.data;
        const cat_id = data.id; // category code passed to asset_sub_cat as cat_id

        $.ajax({
            url: baseUrl("ams/vehicles/get_sub_category_collection/?cat_id=" + cat_id),
            type: "POST",
            dataType: "JSON",
            data: { csrf_token: _csrf_hash },
            success: function (response) {
                const select2SubCategory = $("#select2-sub-category");

                select2SubCategory.select2("destroy");

                $("#select2-sub-category option").each(function () {
                    $(this).remove();
                });

                // add empty field to not auto-select first option
                select2SubCategory.select2({ placeholder: 'Select Subcategory', width: '100%', });

                // add empty field to not auto-select first option
                select2SubCategory.append(new Option("", "", false, false)).trigger('change');

                $.each(response, function (key, value) {
                    var newOption = new Option(value.sub_cat_desc, value.sub_cat_code, false, false);
                    select2SubCategory.append(newOption).trigger('change');
                });
            }
        });
    });

$("#dlg-select2-asset-category")
    .select2({
        placeholder: 'Select Category',
        width: '100%',
        ajax: {
            url: baseUrl("ams/assets/get_category_collection/asset"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                return {
                    results: data.results.map((item) => {
                        return {
                            id: item.cat_id,
                            text: item.text
                        }
                    })
                };
            }

        }
    })
    .on('select2:select', function (e) {
        const data = e.params.data;
        const cat_id = data.id; // category code passed to asset_sub_cat as cat_id

        $.ajax({
            url: baseUrl("ams/vehicles/get_sub_category_collection/?cat_id=" + cat_id),
            type: "POST",
            dataType: "JSON",
            data: { csrf_token: _csrf_hash },
            success: function (response) {
                const dlgselect2SubCategory = $("#dlg-select2-sub-category");

                dlgselect2SubCategory.select2("destroy");

                $("#dlg-select2-sub-category option").each(function () {
                    $(this).remove();
                });

                // add empty field to not auto-select first option
                dlgselect2SubCategory.select2({ placeholder: 'Select Subcategory', width: '100%', });
                // add empty field to not auto-select first option
                dlgselect2SubCategory.append(new Option("", "", false, false)).trigger('change');

                $.each(response, function (key, value) {
                    var newOption = new Option(value.sub_cat_desc, value.sub_cat_id, false, false);
                    dlgselect2SubCategory.append(newOption).trigger('change');
                });
            }
        });
    });

// init select2 vehicle type
$("#select2-vehicle-type, #dlg-select2-vehicle-type")
    .select2({
        placeholder: 'Select Vehicle Type',
        width: '100%',
        ajax: {
            url: baseUrl("ams/vehicles/get_equipment_category_collection"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                const results = $.map(data.results, function (item) {
                    return {
                        text: item.description,
                        id: item.code
                    }
                });

                return {
                    results
                }
            }

        }
    });

// init select2 sub cat - type
$("#select2-sub-category, #dlg-select2-sub-category")
    .select2({
        placeholder: 'Select Subcategory',
        width: '100%',
    });

// init select2 location
$("#select2-location, #dlg-select2-location")
    .select2({
        placeholder: 'Select Area',
        width: '100%',
        ajax: {
            url: baseUrl("ams/vehicles/get_location_collection"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                const results = $.map(data.results, function (item) {
                    return {
                        text: item.location,
                        id: item.id
                    }
                });

                return {
                    results
                }
            }
        }
    });

// init status
$("#select2-status, #dlg-select2-status")
    .select2({
        placeholder: 'Select Status',
        width: '100%',
        ajax: {
            url: baseUrl("ams/vehicles/get_status_collection"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                const results = $.map(data.results, function (item) {
                    return {
                        text: item.name,
                        id: (item.name.replace(/\s/g, '')).toLowerCase()
                    }
                });

                return {
                    results
                }
            }
        }
    });

$(".m-content input[name='purchaseprice']")
    .maskMoney({ thousands: ',', decimal: '.', allowZero: true })
    .on("keyup", function () {
        const amt = $(".m-content input[name='purchaseprice']").val();
        $(".m-content input[name='total_cost']").val(amt);
    });

$(".modal-dialog input[name='purchaseprice']")
    .maskMoney({ thousands: ',', decimal: '.', allowZero: true })
    .on("keyup", function () {
        const amt = $(".modal-dialog input[name='purchaseprice']").val();
        $(".modal-dialog input[name='total_cost']").val(amt);
    });

$("#frm-edit-vehicle").on("submit", function (e) {
    e.preventDefault();

    if ($(this).isValid()) {
        var select2_status = $("#select2-status").val();
        if(select2_status == "junk" || select2_status == "sold" || select2_status == "lost" || select2_status == "destructed"){
            $(".archive-remarks").modal("show");
        }else{
            $('#confirm-update-vehicle').modal("show");
        }
    }
});

$("#images")
    .on("change", function () {
        const files = this.files;
        if (files) {
            for (let i = 0; i < files.length; i++) {
                const FR = new FileReader();
                let file = files[i];
                const name = files[i].name;
                if (images.some(image => image.name === name)) {
                    continue;
                }
                // limit file upload by 1mb
                /*** if(file.size <= 1000000){
                    continue;
                }else{
                    toastr.warning("File size limit exceed.", "Asset Upload Notification", 5000);
                } ***/

                if (file.size > 1000000) {
                    toastr.warning("File size limit exceed.", "Asset Upload Notification", 5000);
                    break;
                }

                resizeImage({ file, maxSize: 800 })
                    .then((resizedImage) => {
                        const reader = new FileReader();
                        reader.readAsDataURL(resizedImage);
                        reader.onloadend = function () {
                            const base64data = reader.result;
                            const image = "<div class='images-preview-container__wrapper d-flex flex-column'>" +
                                "                   <a data-lightbox='roadtrip' data-title='" + files[i].name + "' href='" + base64data + "'>" +
                                "                       <div class='images-preview-container__image' " +
                                "                            style='background-image: url(" + base64data + ")'>" +
                                "                       </div>" +
                                "                   </a>" +
                                "                   <div class='images-preview-container__image__actions d-flex'>" +
                                "                       <button type='button'" +
                                "                               class='mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary" +
                                "                                      m-btn--icon m-btn--icon-only btnNew" + ((i === 0 && !primaryImage) ? ' selected' : '') + "' " +
                                "                               data-name='" + files[i].name + "'" +
                                "                               onclick='setPrimaryPicture(this)'" +
                                "                               data-toggle='m-tooltip' data-original-title='Set as Primary'" +
                                "                               data-skin='dark' data-placement='bottom' " +
                                "                               data-delay='{\"show\": 300}'>" +
                                "                               <i class='la la-check'></i>" +
                                "                       </button>" +
                                "                       <button type='button'" +
                                "                               class='btn btn-default m-btn--hover-danger m-btn m-btn--pill " +
                                "                                      m-btn--icon m-btn--icon-only btnNew' " +
                                "                               data-index='" + i + "' data-name='" + files[i].name + "'" +
                                "                               onclick='removeImage(this)'" +
                                "                               data-toggle='m-tooltip' data-original-title='Remove Image'" +
                                "                               data-skin='dark' data-placement='bottom' " +
                                "                               data-delay='{\"show\": 300}'>" +
                                "                               <i class='la la-trash-o'></i>" +
                                "                       </button>" +
                                "                   </div>" +
                                "               </div>";

                            $(".images-preview-container").append(image);
                            images.push({
                                base64: base64data,
                                size: resizedImage.size,
                                type: resizedImage.type,
                                name: files[i].name,
                                lastModified: files[i].lastModified,
                                lastModifiedDate: files[i].lastModifiedDate
                            });

                            if (i === 0 && !primaryImage) {
                                primaryImage = images[i];
                                $(".primary-image-container").css("background-image", "url('" + primaryImage['base64'] + "')");
                                $(".primary-image-container").find('.images-preview-container__image').css("background-image", "url('" + primaryImage['base64'] + "')");
                                $(".primary-image-container").find('.l_box').attr('url','" + primaryImage[base64] + "');
                            }
                        };
                    })
                    .catch((err) => {
                    });
                toastr.info("Image(s) has been added, click on the save changes to apply the uploaded image.", "Asset Upload Notification", 5000);
                /*FR.addEventListener("load", function (e) {
                    const image = "<div class='images-preview-container__wrapper d-flex flex-column'>" +
                        "                   <a data-lightbox='roadtrip' data-title='" + files[i].name + "' href='" + e.target.result + "'>" +
                        "                       <div class='images-preview-container__image' " +
                        "                            style='background-image: url(" + e.target.result + ")'>" +
                        "                       </div>" +
                        "                   </a>" +
                        "                   <div class='images-preview-container__image__actions d-flex'>" +
                        "                       <button type='button'" +
                        "                               class='mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary" +
                        "                                      m-btn--icon m-btn--icon-only btnNew" + ((i === 0 && !primaryImage) ? ' selected' : '') + "' " +
                        "                               data-name='" + files[i].name + "'" +
                        "                               onclick='setPrimaryPicture(this)'" +
                        "                               data-toggle='m-tooltip' data-original-title='Set as Primary'" +
                        "                               data-skin='dark' data-placement='bottom' " +
                        "                               data-delay='{\"show\": 300}'>" +
                        "                               <i class='la la-check'></i>" +
                        "                       </button>" +
                        "                       <button type='button'" +
                        "                               class='btn btn-default m-btn--hover-danger m-btn m-btn--pill " +
                        "                                      m-btn--icon m-btn--icon-only btnNew' " +
                        "                               data-index='" + i + "' data-name='" + files[i].name + "'" +
                        "                               onclick='removeImage(this)'" +
                        "                               data-toggle='m-tooltip' data-original-title='Remove Image'" +
                        "                               data-skin='dark' data-placement='bottom' " +
                        "                               data-delay='{\"show\": 300}'>" +
                        "                               <i class='la la-trash-o'></i>" +
                        "                       </button>" +
                        "                   </div>" +
                        "               </div>";

                    $(".images-preview-container").append(image);
                    files[i]['base64'] = e.target.result;
                    images.push(files[i]);

                    if (i === 0 && !primaryImage) {
                        primaryImage = files[i];
                        $(".primary-image-container").css("background-image", "url('" + primaryImage['base64'] + "')");
                    }
                });
                FR.readAsDataURL(files[i]);*/
            }
        }
    });

$(".m-content")
    .on("submit", "#frm-add-costing",
        function (e) {
            e.preventDefault();
            const url = $(this).attr("action");
            const formData = new FormData(this);
            formData.append("csrf_token", _csrf_hash);
            formData.append("asset_id", id);
            const form = $(this);

            if ($(this).isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            form.resetForm();
                            $("#tab-costing-table").DataTable().ajax.reload();
                            closeModal();
                        }
                    }
                });
            }
        })
    .on("submit", "#confirmation-dialog",
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const table = form.attr("data-table");

            $.ajax({
                url: baseUrl(url),
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "", 5000);
                        $("#" + table).DataTable().ajax.reload();
                        closeModal();
                    } else {
                        toastr.error(response.message, "Error", 5000);
                    }
                }
            })
        })
    .on("submit", "#frm-edit-costing",
        function (e) {
            e.preventDefault();
            const url = $(this).attr("action");
            const formData = new FormData(this);
            const form = $(this);

            if ($(this).isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            form.resetForm();
                            $("#tab-costing-table").DataTable().ajax.reload();
                            closeModal();
                        }
                    }
                });
            }
        })
    .on("submit", "#frm-add-document",
        function (e) {
            e.preventDefault();
            const url = $(this).attr("action") + "/" + id;
            const formData = new FormData(this);
            const form = $(this);

            if ($(this).isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            form.resetForm();
                            $("#tab-documents-table").DataTable().ajax.reload();
                            closeModal();
                        } else {
                            toastr.error(response.message, "Error", 10000);
                        }
                    }
                });
            }
        })
    .on("submit", "#frm-edit-document",
        function (e) {
            e.preventDefault();
            const url = $(this).attr("action");
            const formData = new FormData(this);
            const form = $(this);

            if ($(this).isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            form.resetForm();
                            $("#tab-documents-table").DataTable().ajax.reload();
                            closeModal();
                        } else {
                            toastr.error(response.message, "Error", 10000);
                        }
                    }
                });
            }
        })
    .on("submit", "#frm-create-maintenance-template-log",
        function (e) {
            e.preventDefault();
            const url = $(this).attr("action");
            const formData = new FormData(this);
            const form = $(this);

            $.ajax({
                url,
                type: "POST",
                dataType: "JSON",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        form.resetForm();
                        $("#tab-maintenance-table").DataTable().ajax.reload();
                        toastr.success(response.message, "Maintenance saved.", 10000);
                    } else {
                        toastr.error(response.message, "Error", 10000);
                    }

                    $("#create-maintenance-template-log").modal("hide");
                }
            });
        })
    .on("keyup", "#frm-edit-maintenance-log input[name='intDate']",
        function (e) {
            const days = $(this).val();
            const last_schedule = new Date($("input[name='last_date_perform']").val());
            const next_date_el = $("input[name='next_date_perform']");

            let _next_date = null;
            if (days && parseInt(days) > 0) {
                let last_date = new Date(last_schedule);
                last_date.setDate(last_date.getDate() + parseInt(days));
                _next_date = (last_date.getMonth() + 1) + "/" + last_date.getDate() + "/" + last_date.getFullYear();
            } else {
                _next_date = "";
            }

            $(next_date_el).val(_next_date);
        })
    .on("submit", "#frm-edit-maintenance-log",
        function (e) {
            e.preventDefault();
            const url = $(this).attr("action");
            const formData = new FormData(this);
            const form = $(this);

            if ($(this).isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            form.resetForm();
                            $("#tab-maintenance-table").DataTable().ajax.reload();
                            toastr.success(response.message, "Maintenance updated.", 10000);
                            closeModal();
                        } else {
                            toastr.error(response.message, "Error", 10000);
                        }
                    }
                });
            }
        });

$("#select2-preventive-temp-head")
    .select2({
        placeholder: 'SELECT TEMPLATE',
        width: '100%',
        ajax: {
            url: baseUrl("ams/vehicles/get_preventive_temp_head_collection"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                const results = $.map(data.results, function (item) {
                    return {
                        text: item.description,
                        id: item.equip_type
                    }
                });

                return {
                    results
                }
            }
        }
    });

function setIsDamageStr(el) {
    const isDamagedVal = $(el).is(":checked") ? "YES" : "NO";
    $(".is-damage-identifier").html(isDamagedVal);
}

function formReset() {
    resetSelect2();
    $("#frm-edit-vehicle").resetForm();
}

function removeImage(el) {
    const _name = $(el).attr("data-name");
    const parent = $(el).closest('div.images-preview-container__wrapper');
    $(parent).remove();

    const index = images.findIndex(({ name }) => name === _name);
    if (_name === primaryImage.name) {
        $(".primary-image-container").css("background-image", "");
    }

    images.splice(index, 1);

    if (_name === primaryImage.name) {
        if (images.length) {
            $(".primary-image-container").css("background-image", "url('" + images[0].base64 + "')");
            primaryImage = images[0];

            // set check button active or selected if primary is removed
            const selectButtons = $(".images-preview-container__wrapper .set-primary");
            $(selectButtons).eq(0).addClass("selected");
        } else {
            primaryImage = defaultPrimary;
            const url = baseUrl("uploads/files/images/vehicles/" + id + "/" + primaryImage);
            $(".primary-image-container").css("background-image", "url('" + url + "')");

            const selectButtons = $("button[data-name='" + primaryImage + "']");
            $(selectButtons).addClass("selected");
        }
    }
}

function setPrimaryPictureFromUploaded(name, asset_id, el) {
    const url = baseUrl("uploads/files/images/vehicles/" + asset_id + "/" + name);
    primaryImage = {
        name,
        base64: url,
        uploaded: true
    };

    $(".primary-image-container").css("background-image", "url('" + primaryImage.base64 + "')");
    $(".primary-lightbox").css("href", primaryImage.base64);

    console.log($(".primary-lightbox"));

    $(".images-preview-container__wrapper .set-primary").removeClass("selected");
    $(el).addClass("selected");
}

function setPrimaryPicture(el) {
    const _name = $(el).attr("data-name");
    const index = images.findIndex(({ name }) => name === _name);
    primaryImage = images[index];
    primaryImage.uploaded = false;
    $(".primary-image-container").css("background-image", "url('" + primaryImage.base64 + "')");

    $(".images-preview-container__wrapper .set-primary").removeClass("selected");
    $(el).addClass("selected");
}

function urltoFile(url, filename, mimeType) {
    mimeType = mimeType || (url.match(/^data:([^;]+);/) || '')[1];
    return (fetch(url)
        .then(function (res) {
            return res.arrayBuffer();
        })
        .then(function (buf) {
            return new File([buf], filename, { type: mimeType });
        })
    );
}

const asyncAppendImageToFormData = async (array, formData) => {
    for (const row of array) {
        await urltoFile(row.base64, row.name)
            .then(function (file) {
                formData.append("files[]", file);
            });
    }
};

function updateVehicle() {
    const form = $("#frm-edit-vehicle");
    const formData = new FormData(form[0]);

    asyncAppendImageToFormData(images, formData)
        .then(() => {
            if (primaryImage) {
                formData.append("primary_pic", primaryImage.name);
                formData.append("primary_pic_from_uploaded", (primaryImage.uploaded !== undefined ? primaryImage.uploaded : ""));
            }

            formData.append("uploadedImagesToRemove", JSON.stringify(uploadedImagesToRemove));

            $.ajax({
                url: baseUrl("ams/vehicles/update_vehicle"),
                type: "POST",
                dataType: "JSON",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        const _images = images; // store hoisted images to local variable to be cleared

                        if (response.fail_uploads.length) {
                            const l = response.fail_uploads.length;
                            $("#fail-upload-alert-dialog .image-list").html("");
                            $("#fail-upload-alert-dialog .auto-selected-primary").html("");

                            for (let i = 0; i < l; i++) {
                                const item = response.fail_uploads[i];
                                const image = _images.find(({ name }) => name === item.filename);
                                const primary = _images.find(({ name }) => name === response.primary_pic);
                                const isPrimary = item.isPrimary ? "Yes" : "No";

                                const error = jQuery(item.message).text();
                                const el = $("button[data-name='" + item.filename + "']").closest('div.images-preview-container__wrapper');
                                $(el).remove();

                                const element = "" +
                                    "                   <div class='row no-gutters'>" +
                                    "                        <div class='col-xl-4 col-sm-12'>" +
                                    "                            <div class='p-1 d-inline-block' " +
                                    "                                 style='border: 1px solid #f3f3f3; border-radius: 3px; position: relative;'>" +
                                    "                                <div class='images-preview-container__image'" +
                                    "                                     style='background-image: url(" + image.base64 + "); border-radius: 3px;'></div>" +
                                    "                            </div>" +
                                    "                        </div>" +
                                    "                        <div class='col-xl-8 col-sm-12 mt-2 mt-xl-0 mt-lg-0 mt-md-0'>" +
                                    "                            <div>" +
                                    "                                <span class='less-emphasis normal-case'>Filename: </span>" +
                                    "                                <span class='more-emphasis normal-case'>" + item.filename + "</span>" +
                                    "                            </div>" +
                                    "                            <div class='mt-2'>" +
                                    "                                <span class='less-emphasis normal-case text-danger'>Error: </span>" +
                                    "                                <span class='more-emphasis normal-case'>" + error + "</span>" +
                                    "                            </div>" +
                                    "                            <div class='mt-2'>" +
                                    "                                <span class='less-emphasis normal-case'>Primary Picture: </span>" +
                                    "                                <span class='more-emphasis normal-case'>" + isPrimary + "</span>" +
                                    "                            </div>" +
                                    "                        </div>" +
                                    "                    </div>";

                                $("#fail-upload-alert-dialog .image-list").append(element);
                            }

                            if (response.uploading_primary_pic_failed && response.uploaded.length) {
                                const primaryImagUrl = baseUrl("uploads/files/images/vehicles/" + $("input[name='id']").val() + "/" + response.primary_pic);
                                const elAutoSelectedPrimary = "" +
                                    "                   <div style='text-transform: none; font-weight: 400; font-size: 14px;' class='mt-5 mb-3'>" +
                                    "                       Failed on uploading selected primary picture. The system has auto-selected the picture below as primary." +
                                    "                    </div>" +
                                    "                    <div class='row no-gutters'>" +
                                    "                        <div class='col-xl-4 col-sm-12'>" +
                                    "                            <div class='p-1 d-inline-block'" +
                                    "                                 style='border: 1px solid #f3f3f3; border-radius: 3px; position: relative;'>" +
                                    "                                <div class='images-preview-container__image' " +
                                    "                                     style='background-image: url(" + primaryImagUrl + "); border-radius: 3px;'></div>" +
                                    "                            </div>" +
                                    "                        </div>" +
                                    "                        <div class='col-xl-8 col-sm-12 mt-2 mt-xl-0 mt-lg-0 mt-md-0'>" +
                                    "                            <div>" +
                                    "                                <span class='less-emphasis normal-case'>Filename: </span>" +
                                    "                                <span class='more-emphasis normal-case'> " + response.primary_pic + " </span>" +
                                    "                            </div>" +
                                    "                        </div>" +
                                    "                    </div>" +
                                    "                    <div style='text-transform: none; font-weight: 400; font-size: 12px;' class='mt-2 text-muted'>" +
                                    "                        If you want to change primary picture you can go to masterfile > search the vehicle then click edit on actions." +
                                    "                    </div>";

                                $("#fail-upload-alert-dialog .auto-selected-primary").html("").append(elAutoSelectedPrimary);
                                $(".primary-image-container")
                                    .css("background-image", "url(" + baseUrl('uploads/files/images/vehicles/' + id + '/' + response.primary_pic) + ")");
                                primaryImage = {
                                    name: response.primary_pic,
                                    base64: baseUrl("uploads/files/images/vehicles/" + id + "/" + response.primary_pic),
                                    uploaded: true
                                };
                            }
                            $("#fail-upload-alert-dialog").modal("show");
                        } else {
                            if(response.status == 'archived'){
                                toastr.success(response.message, "Successfully updated.", 50000);
                                $('#confirm-update-vehicle').modal("hide");
                                setTimeout(() => {
                                    // window.location.assign(baseUrl("ams/assets/fixed_masterfile"));
                                    window.location.assign(baseUrl("ams/vehicles/vehicle_masterfile"));
                                }, 1500);
                            }else{
                                toastr.success(response.message, "Successfully updated.", 50000);
                                setTimeout(() => {
                                    // window.location.assign(baseUrl("ams/assets/fixed_masterfile"));
                                    window.location.assign(baseUrl("ams/vehicles/edit_vehicle/" + response.id));
                                }, 1500);
                            }
                        }

                        if (response.uploaded.length) {
                            $('.images-preview-container__wrapper').remove();
                            $.each(response.uploaded, function (i, item) {
                                const _primaryImage = primaryImage.name.replace(/ /g, "_"); // to compare filename without spaces
                                const image = `<div class="d-flex flex-column images-preview-container__wrapper isUploaded">
                                                       <a data-lightbox="roadtrip" data-title="${item.image}" 
                                                            href="${baseUrl("uploads/files/images/vehicles/" + id + "/" + item.image)}">
                                                           <div class="images-preview-container__image" 
                                                                style="background-image: url('${baseUrl("uploads/files/images/vehicles/" + id + "/" + item.image)}')">
                                                           </div>
                                                       </a>
                                                       <div class="images-preview-container__image__actions d-flex">
                                                           <button type="button"
                                                                   class="mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary
                                                                          m-btn--icon m-btn--icon-only btnNew ${((item.image === _primaryImage) ? ' selected' : '')}" 
                                                                   data-name="${item.image}"
                                                                   onclick='setPrimaryPictureFromUploaded("${item.image}",${id},this)'
                                                                   data-toggle="m-tooltip" data-original-title="Set as Primary"
                                                                   data-skin="dark" data-placement="bottom" 
                                                                   data-delay='{\"show\": 300}'>
                                                                   <i class="la la-check"></i>
                                                           </button>
                                                           <button type="button"
                                                                   class="btn btn-default m-btn--hover-danger m-btn m-btn--pill 
                                                                          m-btn--icon m-btn--icon-only btnNew" 
                                                                   data-index="${i}" data-name="${item.image}"
                                                                   onclick='removeUploaded("${item.image}",${item.id}, this)'
                                                                   data-toggle="m-tooltip" data-original-title="Remove Image"
                                                                   data-skin="dark" data-placement="bottom" 
                                                                   data-delay='{\"show\": 300}'>
                                                                   <i class="la la-trash-o"></i>
                                                           </button>
                                                       </div>
                                                   </div>`;

                                $(".images-preview-container").append(image);
                            });
                        } else {
                            images = [];
                            primaryImage = null;
                            $(".primary-image-container").css("background-image", "none");
                            getPrimaryPic(id);
                        }

                        if (response.fail_uploads.length === _images.length) {
                            const elAutoSelectedPrimary = "" +
                                "    <div style='text-transform: none; font-weight: 400; font-size: 14px;' class='mt-5 mb-3 text-primary'>" +
                                "       Failed on uploading all images." +
                                "    </div>";
                            $("#fail-upload-alert-dialog .auto-selected-primary").html("").append(elAutoSelectedPrimary);
                        }
                    } else {
                        toastr.error(response.message, "Error", 50000);
                    }
                    images = [];
                    $('#confirm-update-vehicle').modal("hide");
                }
            });
        });
}

function resetAllFields(form) {
    form.resetForm();
    resetDialogSelect2();
    $(".primary-image-container").css("background-image", "");
    $(".images-preview-container").html("");
    images = [];
    primaryImage = null;
}

function openFileSelect() {
    $("#images").val("").trigger("click");
}

function removeUploaded(name, id, el) {
    if (primaryImage) {
        if (name === primaryImage.name) {
            $("#alert-dialog").modal("show");
            return;
        }
    }

    uploadedImagesToRemove.push(id);
    $(el).closest("div.images-preview-container__wrapper").remove();
}

function getPrimaryPic(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/get_primary_pic/" + id),
        type: "get",
        dataType: "json",
        success: function (response) {
            if (response.primary_pic) {
                primaryImage = {
                    name: response.primary_pic,
                    base64: baseUrl("uploads/files/images/vehicles/" + id + "/" + response.primary_pic),
                    uploaded: true
                };
                defaultPrimary = response.primary_pic;
                var url = baseUrl('uploads/files/images/vehicles/' + id + '/' + defaultPrimary);
                $(".primary-image-container").css("background-image", "url('" + primaryImage.base64 + "')");
                $("#primary_pic").attr("data-title", defaultPrimary);
                $("#primary_pic").attr("href", url);
            }
        }
    });
}

function loadTab(el, view, reloadTable = false) {
    const tab = $(el).attr("href");
    const table = tab + '-table';
    const currentTabContent = $(tab).html();

    if (currentTabContent && reloadTable) {
        $(table).DataTable().ajax.reload();
        return;
    }

    $.ajax({
        url: baseUrl("ams/vehicles/load_view_tab/?view=" + view),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            const view = response.view;
            $(tab).html(view);
            loadTable(tab, table);
            triggerActionPrivileges();
        }
    });
}

function loadTable(tab, table) {
    const _tab = tab.replace("#", ""); // remove number sign
    let tbl;

    switch (_tab) {
        case 'tab-components':
            if ($.inArray("new", _currentActions) !== -1) {
                $("#tab-components .btnNew").attr('hidden', false);
            }else{
                $("#tab-components .btnNew").attr('hidden', true);
            }
            tbl = $(table).DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                ajax: {
                    url: baseUrl("ams/vehicles/get_vehicle_components/" + id),
                    type: "post",
                    global: false,
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.search['value'] = $("#search-vehicle-components").val();
                    }
                },
                searching: false,
                columns: [
                    { data: "asset_code", width: "13%", render: function(data, type, row){
                        if (data) {
                            return '<a href="'+ baseUrl('ams/vehicles/edit_vehicle_component/' + row.asset_id) +'"><span class="">' + data + '</span></a>';
                        } else {
                            return data;
                        }
                    } },
                    { data: "description" },
                    { data: "remarks", width: "20%" },
                    {
                        data: "status",
                        width: "15%",
                        render: function (data) {
                            const badgeClass = parseInt(data) === 1 ? " m-badge--metal" : " m-badge--success";
                            const status = parseInt(data) === 1 ? "Excluded" : "Included";
                            return "<span style='border-radius: 3em;' " +
                                "         class='px-3 py-1 m-badge" + badgeClass + "'>" + status + "</span>";
                        }
                    },
                    {
                        data: "",
                        width: "8%",
                        defaultContent: "",
                        className: "text-center",
                        orderable: false,
                        render: function (data, type, row) {
                            const includeBtn = "<button type='button' " +
                                "        title='Include'" +
                                "        class='btn btn-default m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
                                "        onclick='includeComponent(" + row.id + ",\"" + table + "\")'" +
                                "        data-placement='bottom'> " +
                                "<i class='fa fa-plus'></i></button> ";
                            const excludeBtn = "<button type='button' " +
                                "        title='Exclude'" +
                                "        class='btn btn-default m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
                                "        onclick='excludeComponent(" + row.id + ",\"" + table + "\")'" +
                                "        data-placement='bottom'>" +
                                "<i class='fa fa-reply'></i></button> ";

                            const btn = (parseInt(row.isExcluded) === 1) ? includeBtn : excludeBtn;
                            return "" + btn +
                                "<button type='button' " +
                                "        title='Remove' " +
                                "        class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
                                "        onclick='removeComponent(" + row.id + ",\"" + table + "\")'" +
                                "        data-placement='bottom'>" +
                                "<i class='fa fa-trash'></i></button>";
                        }
                    },
                ]
            });

            $("#search-vehicle-components")
                .donetyping(function () {
                    tbl.ajax.reload();
                });
            break;
        case 'tab-costing':
            if ($.inArray("new", _currentActions) !== -1) {
                $("#tab-costing .btnNew").attr('hidden', false);
            }else{
                $("#tab-costing .btnNew").attr('hidden', true);
            }
            tbl = $(table).DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                ajax: {
                    url: baseUrl("ams/vehicles/get_costing/" + id),
                    type: "post",
                    global: false,
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.search['value'] = $("#search-vehicle-costing").val();
                    }
                },
                searching: false,
                columns: [
                    { data: "description" },
                    {
                        data: "price",
                        width: "15%",
                        render: function (data, type, row) {
                            return parseFloat(data).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    },
                    {
                        data: "",
                        defaultContent: "",
                        orderable: false,
                        width: "10%",
                        className: "text-center",
                        render: function (data, type, row) {
                            return "" +
                                "   <button type='button' " +
                                "        title='Click to Edit.' onclick='openEditCostingDialog(" + row.id + ")'" +
                                "        class='btn btn-default m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
                                "        data-placement='bottom'>" +
                                "       <i class='fa fa-pencil-square-o'></i>" +
                                "   </button>" +
                                "   <button type='button' " +
                                "        title='Click to Delete.' onclick='openConfirmDeleteCostingDialog(" + row.id + ")'" +
                                "        class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem'" +
                                "        data-placement='bottom'>" +
                                "       <i class='fa fa-trash'></i>" +
                                "   </button>";
                        }
                    }
                ]
            });

            $("#search-vehicle-costing")
                .donetyping(function () {
                    tbl.ajax.reload();
                });
            break;
        case 'tab-documents':
            if ($.inArray("new", _currentActions) !== -1) {
                $("#tab-documents .btnNew").attr('hidden', false);
            }else{
                $("#tab-documents .btnNew").attr('hidden', true);
            }
            tbl = $(table).DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                ajax: {
                    url: baseUrl("ams/vehicles/get_vehicle_documents/" + id),
                    type: "post",
                    global: false,
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.search['value'] = $("#search-vehicle-document").val();
                    }
                },
                searching: false,
                columns: [
                    {
                        data: "description"
                    },
                    {
                        data: "filename",
                        width: "40%"
                    },
                    {
                        data: "",
                        defaultContent: "",
                        orderable: false,
                        width: "15%",
                        className: "text-center",
                        render: function (data, type, row) {
                            return "" +
                                "   <a href='" + baseUrl("uploads/files/" + row.asset_id + "/" + row.filename) + "'" +
                                "        download='" + row.filename + "' title='Download' " +
                                "        data-placement='bottom'" +
                                "        class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnDownloadFile'>" +
                                "       <i class='fa fa-download'></i>" +
                                "   </a>" +
                                "   <button type='button' " +
                                "        title='Edit' data-placement='bottom'" +
                                "        onclick='opeEditDocumentDialog(" + row.id + ")'" +
                                "        class='btn btn-default m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'>" +
                                "       <i class='fa fa-pencil-square-o'></i>" +
                                "   </button>" +
                                "   <button type='button' " +
                                "        title='Delete' data-placement='bottom'" +
                                "        onclick='openConfirmDeleteDocumentDialog(" + row.id + ")'" +
                                "        class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem'>" +
                                "       <i class='fa fa-trash'></i>" +
                                "   </button>";
                        }
                    }
                ]
            });

            $("#search-vehicle-document")
                .donetyping(function () {
                    tbl.ajax.reload();
                });
            break;
        case 'tab-accountability':
            tbl = $(table).DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                ajax: {
                    url: baseUrl("ams/vehicles/get_vehicle_accountability/" + id),
                    type: "post",
                    global: true,
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.search['value'] = $("#search-vehicle-accountability").val();
                    }
                },
                searching: false,
                columns: [
                    {
                        data: "date_issued",
                        width: "15%",
                    },
                    {
                        data: "reference_no",
                        width: "20%"
                    },
                    {
                        data: "employee",
                        width: "20%",
                        render: function (data, type, row) {
                            return "" +
                                " <p class='mb-0'>" + data + "</p>" +
                                " <p class='text-muted mb-0' id='asset_borrower_company'>" +  row.company + "</p>";
                        }
                    },
                    {
                        data: "overdue",
                        width: "10%"
                    },
                    {
                        data: "status",
                        width: "20%",
                        render: function (data, type, row) {
                            if (row.status === "Cancelled") {
                                return "" +
                                " <p class='mb-0'>Cancelled/Archived</p>" +
                                " <p class='text-muted mb-0'>" +  row.module + "</p>";
                            } else {
                                switch (parseInt(data)) {
                                    case 1:
                                        return "" +
                                        " <p class='mb-0'>Returned</p>" +
                                        " <p class='text-muted mb-0'>" +  row.module + "</p>";
                                        break;
                                    case 2:
                                        return "" +
                                        " <p class='mb-0'>Partially Returned</p>" +
                                        " <p class='text-muted mb-0'>" +  row.module + "</p>";
                                        break;
                                    default:
                                        return "" +
                                        " <p class='mb-0'>Current</p>" +
                                        " <p class='text-muted mb-0'>" +  row.module + "</p>";
                                        break;
                                }
                            }
                        }
                    },
                ]
            });

            $("#search-vehicle-accountability")
                .donetyping(function () {
                    tbl.ajax.reload();
                });
            break;
        case 'tab-maintenance':
            tbl = $(table).DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                ajax: {
                    url: baseUrl("ams/vehicles/get_vehicle_maintenance_log/" + id),
                    type: "post",
                    global: false,
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.search['value'] = $("#search-vehicle-maintenance").val();
                    }
                },
                searching: false,
                columns: [
                    {
                        data: "description"
                    },
                    {
                        data: "last_value"
                    },
                    {
                        data: "last_date_perform"
                    },
                    {
                        data: "next_value"
                    },
                    {
                        data: "intDate"
                    },
                    {
                        data: "next_date_perform"
                    },
                    {
                        data: "",
                        orderable: false,
                        render: function (data, type, row) {
                            return "" +
                                "   <button type='button' data-placement='bottom'" +
                                "        title='Edit' onclick='openEditMaintenanceItem(" + row.id + ")'" +
                                "        class='btn btn-default m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'>" +
                                "       <i class='fa fa-pencil-square-o'></i>" +
                                "   </button>" +
                                "   <button type='button' " +
                                "        title='Delete' data-placement='bottom' " +
                                "        onclick='openConfirmDeleteMaintenanceItem(" + row.id + ")'" +
                                "        class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem'>" +
                                "       <i class='fa fa-trash'></i>" +
                                "   </button>";
                        }
                    }
                ]
            });

            $("#search-vehicle-maintenance")
                .donetyping(function () {
                    tbl.ajax.reload();
                });
            break;
        default:
            break;
    }

    $(".tab-clear-search")
        .on("click", function () {
            $("#search-vehicle-components").val("");
            $("#search-vehicle-costing").val("");
            $("#search-vehicle-document").val("");
            $("#search-vehicle-accountability").val("");
            $("#search-vehicle-maintenance").val("");
            tbl.ajax.reload();
        });
}

function includeComponent(id, table) {
    const form = reIncludeComponentConfirmDialog.find("form.modal-content");
    const action = baseUrl("ams/vehicles/re_include_component/" + id);
    form.attr("action", action);
    form.attr("data-table", table);
    reIncludeComponentConfirmDialog.modal("show");
}

function excludeComponent(id, table) {
    const form = excludeReasonDialog.find("form.modal-content");
    const action = baseUrl("ams/vehicles/exclude_component/" + id);
    form.attr("action", action);
    form.attr("data-table", table);
    excludeReasonDialog.modal("show");
}

function removeComponent(id, table) {
    const form = removeComponentConfirmDialog.find("form.modal-content");
    const action = baseUrl("ams/vehicles/remove_vehicle_component/" + id);
    form.attr("action", action);
    form.attr("data-table", table);
    removeComponentConfirmDialog.modal("show");
}

function processIncludeComponent(el, event) {
    event.preventDefault();
    const url = $(el).attr("action");
    const table = $(el).attr("data-table");

    $.ajax({
        url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response.success) {
                reIncludeComponentConfirmDialog.modal("hide");
                $(table).DataTable().ajax.reload();
                tblAllComponentList.ajax.reload();
                $(el).resetForm();
                toastr.success(response.message, "Component included.", 10000);
            } else {
                toastr.error(response.message, "Error", 10000);
            }
        }
    })
}

function processExcludeComponent(el, event) {
    event.preventDefault();
    const url = $(el).attr("action");
    const table = $(el).attr("data-table");

    if ($(el).isValid()) {
        $.ajax({
            url,
            type: "POST",
            data: $(el).serialize(),
            dataType: "JSON",
            success: function (response) {
                if (response.success) {
                    excludeReasonDialog.modal("hide");
                    $(table).DataTable().ajax.reload();
                    $(el).resetForm();
                    toastr.success(response.message, "Component excluded.", 10000);
                } else {
                    toastr.error(response.message, "Error", 10000);
                }
            }
        })
    }
}

function processRemoveComponent(el, event) {
    event.preventDefault();
    const url = $(el).attr("action");
    const table = $(el).attr("data-table");

    $.ajax({
        url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response.success) {
                removeComponentConfirmDialog.modal("hide");
                $(table).DataTable().ajax.reload();
                tblAllComponentList.ajax.reload();
                $(el).resetForm();
                toastr.success(response.message, "Component Removed.", 10000);
            } else {
                toastr.error(response.message, "Error", 10000);
            }
        }
    })
}

function clearSearchAllComponentList() {
    $("#search-all-component-list").val("");
    tblAllComponentList.ajax.reload();
}

function saveComponentToVehicle(_id, el) {
    $.ajax({
        url: baseUrl("ams/vehicles/save_component_to_vehicle"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            vehicle_id: _id,
            parent_id: id
        },
        success: function (response) {
            if (response.success) {
                $("#tab-components-table").DataTable().ajax.reload();
                toastr.success(response.message, "Component added.", 10000);
            } else {
                toastr.error(response.message, "Error", 10000);
            }
        }
    });
    tblAllComponentList.ajax.reload();
}

function openNewCostingDialog() {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: null,
            path: "vehicles/modals/new_costing",
            function_name: null
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function closeModal() {
    const modal = $(".document-modal-container");
    modal.modal("hide");
}

function openConfirmDeleteCostingDialog(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Remove Cost",
                message: "Are you sure to remove this costing data?",
                action: "ams/vehicles/remove_vehicle_cost/?id=" + id,
                table: "tab-costing-table"
            },
            path: "confirmation_dialog",
            function_name: "pass_data_to_dialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function openEditCostingDialog(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: { id },
            path: "vehicles/modals/edit_costing",
            function_name: "get_costing_item"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function openAddDocumentDialog() {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: null,
            path: "vehicles/modals/new_document",
            function_name: null
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function showFilename(el) {
    const fileContainerLabel = $(el).siblings(".custom-file-control");
    if (el.files && el.files.length) {
        const file = el.files[0];
        fileContainerLabel.html("<div style='width: 75%;' class='text-truncate'>" + file.name + "</div>");
    } else {
        const current_filename = $("#frm-edit-document").find("input[name='current_filename']").val() || "Choose File...";
        fileContainerLabel.html(current_filename);
    }
}

function openConfirmDeleteDocumentDialog(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Remove Document",
                message: "Are you sure to remove this document?",
                action: "ams/vehicles/remove_document/?id=" + id,
                table: "tab-documents-table"
            },
            path: "confirmation_dialog",
            function_name: "pass_data_to_dialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function opeEditDocumentDialog(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: { id },
            path: "vehicles/modals/edit_document",
            function_name: "get_document_item"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function loadPreventiveTemplateBody() {
    const pth_id = $("#select2-preventive-temp-head").val();
    const vh_id = id;
    $.ajax({
        url: baseUrl("ams/vehicles/load_preventive_template_body/" + pth_id),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response) {
                let date = new Date();
                date = date.getMonth() + 1 + "/" + date.getDate() + "/" + date.getFullYear();
                const tableBody = $("#tbl-preventive-template tbody");
                tableBody.empty();
                const _data = response.data;
                $.each(_data, function (i, item) {
                    const html = "" +
                        "<tr>" +
                        "   <td>" + item.description + "</td>" +
                        "   <td>" +
                        "       <div class='form-group m-form__group'>" +
                        "           <input type='text'" +
                        "                  class='form-control m-input m-input--square text-right' value='0' name='last_value[]'>" +
                        "       </div>" +
                        "       <input type='hidden' name='ptbd_id[]' value='" + item.id + "'>" +
                        "       <input type='hidden' name='pth_id[]' value='" + item.pth_id + "'>" +
                        "       <input type='hidden' name='vh_id[]' value='" + vh_id + "'>" +
                        "       <input type='hidden' name='status[]' value='1'>" +
                        "   </td>" +
                        "   <td>" +
                        "       <div class='form-group m-form__group'>" +
                        "           <div class='input-group date dt-picker-dynamic'>" +
                        "               <input type='text' class='form-control m-input'" +
                        "                      autocomplete='off' value='" + date + "' name='last_date_perform[]'>" +
                        "               <span class='input-group-addon'>" +
                        "                   <i class='la la-calendar'></i>" +
                        "               </span>" +
                        "           </div>" +
                        "       </div>" +
                        "   </td>" +
                        "   <td>" +
                        "       <div class='form-group m-form__group'>" +
                        "           <input type='text' " +
                        "                  class='form-control m-input m-input--square text-right' value='" + item.interval_val + "'" +
                        "                  name='next_value[]'>" +
                        "       </div>" +
                        "   </td>" +
                        "   <td>" +
                        "       <div class='form-group m-form__group'>" +
                        "           <input type='text' " +
                        "                  class='form-control m-input m-input--square text-right' value='0'" +
                        "                  name='intDate[]' oninput='populateNextDate(this)'>" +
                        "       </div>" +
                        "   </td>" +
                        "   <td>" +
                        "       <div class='form-group m-form__group'>" +
                        "           <div class='input-group date'>" +
                        "               <input type='text' class='form-control m-input'" +
                        "                      name='next_date_perform[]' autocomplete='off' readonly>" +
                        "               <span class='input-group-addon'>" +
                        "                   <i class='la la-calendar'></i>" +
                        "               </span>" +
                        "           </div>" +
                        "       </div>" +
                        "   </td>" +
                        "</tr>";
                    tableBody.append(html);
                });

                $(".dt-picker-dynamic").datepicker({
                    todayHighlight: true,
                    todayBtn: "linked",
                    clearBtn: true,
                });
            } else {
                toastr.error("An error occurred while loading data.", "Error", 10000);
            }
        }
    });
}

function populateNextDate(el) {
    const days = $(el).val();
    const last_date_val = $(el).closest("tr").find("td:eq(2) input").val();
    const next_date_el = $(el).closest("tr").find("td:eq(5) input");

    let _next_date = null;
    if (days && parseInt(days) > 0) {
        let last_date = new Date(last_date_val);
        last_date.setDate(last_date.getDate() + parseInt(days));
        _next_date = (last_date.getMonth() + 1) + "/" + last_date.getDate() + "/" + last_date.getFullYear();
    } else {
        _next_date = "";
    }

    $(next_date_el).val(_next_date);
}

function openEditMaintenanceItem(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: { id },
            path: "vehicles/modals/edit_maintenance_item",
            function_name: "get_maintenance_item"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function openConfirmDeleteMaintenanceItem(item_id, all = 0) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: all ? "Remove all maintenance log" : "Remove Maintenance Log",
                message: all ? "Are you sure to remove entire maintenance logs?" : "Are you sure to remove this maintenance item?",
                action: "ams/vehicles/remove_maintenance_item/?id=" + item_id + "&vh_id=" + id + "&all=" + all,
                table: "tab-maintenance-table"
            },
            path: "confirmation_dialog",
            function_name: "pass_data_to_dialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}
// $("#frm-new-vehicle-component-dialog").submit(function(e){
//     e.preventDefault();
//     saveNewVehicleComponent();
// })

function saveNewVehicleComponent() {
    const form = $("#frm-new-vehicle-component-dialog");
    const formData = new FormData(form[0]);
    const mode = $("#confirm-save-new-vehicle-component").attr("data-mode");
    formData.append("mode", mode);
    if($("#frm-new-vehicle-component-dialog").isValid()){
    asyncAppendImageToFormData(images, formData)
        .then(() => {
            if (primaryImage) {
                formData.append("primary_pic", primaryImage.name);
            }

            const generateCount = $("#frm-generate-multiple-vehicle-component").find("[name='count']").val();
            const saveTimes = (mode === "multiple") ? generateCount : 1;

            let ctr = 0;
            for (let i = 0; i < saveTimes; i++) {
                $.ajax({
                    url: baseUrl("ams/vehicles/save_new_vehicle/1/" + id),
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            ctr += 1;
                            const _images = images; // store hoisted images to local variable to be cleared

                            if (response.fail_uploads.length) {
                                const l = response.fail_uploads.length;
                                $("#fail-upload-alert-dialog .image-list").html("");
                                $("#fail-upload-alert-dialog .auto-selected-primary").html("");

                                for (let i = 0; i < l; i++) {
                                    const item = response.fail_uploads[i];
                                    const image = _images.find(({name}) => name === item.filename);
                                    primary = _images.find(({name}) => name === response.primary_pic);
                                    const isPrimary = item.isPrimary ? "Yes" : "No";

                                    const error = jQuery(item.message).text();

                                    const element = "" +
                                        "                   <div class='row no-gutters'>" +
                                        "                        <div class='col-xl-4 col-sm-12'>" +
                                        "                            <div class='p-1 d-inline-block' " +
                                        "                                 style='border: 1px solid #f3f3f3; border-radius: 3px; position: relative;'>" +
                                        "                                <div class='images-preview-container__image'" +
                                        "                                     style='background-image: url(" + image.base64 + "); border-radius: 3px;'></div>" +
                                        "                            </div>" +
                                        "                        </div>" +
                                        "                        <div class='col-xl-8 col-sm-12 mt-2 mt-xl-0 mt-lg-0 mt-md-0'>" +
                                        "                            <div>" +
                                        "                                <span class='less-emphasis normal-case'>Filename: </span>" +
                                        "                                <span class='more-emphasis normal-case'>" + item.filename + "</span>" +
                                        "                            </div>" +
                                        "                            <div class='mt-2'>" +
                                        "                                <span class='less-emphasis normal-case text-danger'>Error: </span>" +
                                        "                                <span class='more-emphasis normal-case'>" + error + "</span>" +
                                        "                            </div>" +
                                        "                            <div class='mt-2'>" +
                                        "                                <span class='less-emphasis normal-case'>Primary Picture: </span>" +
                                        "                                <span class='more-emphasis normal-case'>" + isPrimary + "</span>" +
                                        "                            </div>" +
                                        "                        </div>" +
                                        "                    </div>";

                                    $("#fail-upload-alert-dialog .image-list").append(element);
                                }

                                if (response.uploading_primary_pic_failed && primary) {
                                    const elAutoSelectedPrimary = "" +
                                        "                   <div style='text-transform: none; font-weight: 400; font-size: 14px;' class='mt-5 mb-3'>" +
                                        "                       Failed on uploading selected primary picture. The system has auto-selected the picture below as primary." +
                                        "                    </div>" +
                                        "                    <div class='row no-gutters'>" +
                                        "                        <div class='col-xl-4 col-sm-12'>" +
                                        "                            <div class='p-1 d-inline-block'" +
                                        "                                 style='border: 1px solid #f3f3f3; border-radius: 3px; position: relative;'>" +
                                        "                                <div class='images-preview-container__image' " +
                                        "                                     style='background-image: url(" + primary.base64 + "); border-radius: 3px;'></div>" +
                                        "                            </div>" +
                                        "                        </div>" +
                                        "                        <div class='col-xl-8 col-sm-12 mt-2 mt-xl-0 mt-lg-0 mt-md-0'>" +
                                        "                            <div>" +
                                        "                                <span class='less-emphasis normal-case'>Filename: </span>" +
                                        "                                <span class='more-emphasis normal-case'> " + primary.name + " </span>" +
                                        "                            </div>" +
                                        "                        </div>" +
                                        "                    </div>" +
                                        "                    <div style='text-transform: none; font-weight: 400; font-size: 12px;' class='mt-2 text-muted'>" +
                                        "                        If you want to change primary picture you can go to masterfile > search the vehicle then click edit on actions." +
                                        "                    </div>";

                                    $("#fail-upload-alert-dialog .auto-selected-primary").append(elAutoSelectedPrimary);
                                }

                                if (response.fail_uploads.length === _images.length) {
                                    const elAutoSelectedPrimary = "" +
                                        "    <div style='text-transform: none; font-weight: 400; font-size: 14px;' class='mt-5 mb-3 text-primary'>" +
                                        "       Failed on uploading all images." +
                                        "    </div>";
                                    $("#fail-upload-alert-dialog .auto-selected-primary").html("").append(elAutoSelectedPrimary);
                                }
                                $("#fail-upload-alert-dialog").modal("show");
                            } else {
                                toastr.success(response.message, "Successfully saved.", 50000);
                                $('#new-vehicle-component-dialog').modal("hide");
                                $("#tab-components-table").DataTable().ajax.reload(null, false);
                                // setTimeout(() => {
                                //     window.location.assign(baseUrl("ams/vehicles/edit_vehicle/" + id));
                                // }, 1500);
                            }

                            resetAllFields(form);
                            if (mode === "multiple") {
                                if (ctr === parseInt(saveTimes)) {
                                    $("#frm-generate-multiple-vehicle-component").find("[name='count']").val("1");
                                    toastr.info("Multiple Assets successfully saved.", "Successfully saved.");
                                    $(".modal").modal('hide');
                                    $("#tab-components-table").DataTable().ajax.reload(null, false);
                                }
                            }
                        } else {
                            toastr.error(response.message, "Error", 50000);
                        }

                        $('#confirm-save-new-vehicle-component').modal("hide");
                    }
                });
            }
        });
    }
}

$('body, .modal-body')
    .tooltip({
        selector: '[title]',
        skin: "dark",
        delay: {
            show: 300
        }
    });

$("#inventory-status").select2({
    placeholder: 'Select',
    dropdownParent: $("#inventory-check-modal"),
    width: '100%',
    data: [
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


$('#inventory-dt').datepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'mm/dd/yyyy',
});

$("#inventory-dt").datepicker("setDate", new Date());

function inv_check() {
    var asset_type = $("#inventory-status").val();
    var date_inv = $("#date_inv").val();
    $.ajax({
        url: baseUrl("ams/vehicles/inventory_check"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            id: id,
            asset_type: asset_type,
            inventory_check_date: date_inv
        },
        success: function (response) {
            $("#inventory-check-modal").modal("hide");
            if (response.status) {
                $("#inv_checked_by").val(response.checked_by);
                $("#inv_checked_date").val(moment(response.checked_date).format('MMMM DD, YYYY'));
                toastr.success("Inventory checked information updated.", "Inventory Checked.", { timeOut: 10000 });
            }
        }
    });
}

function edit_generate_vehicle_name(){
    var brand_name = $("#edit_vehicle_manufacturer").val().toLowerCase();
    var model_name = $("#edit_vehicle_model").val().toLowerCase();
    var name = "";
    
    if((brand_name == "n/a" || brand_name == "none") && (model_name == "n/a" || model_name == "none")){
        name = "";
        $("#edit_vehicle_name").attr('readonly', false);
    }else if((model_name == "n/a" || model_name == "none") && (brand_name != "n/a" || brand_name != "none")){
        name = brand_name;
    }else if((brand_name == "n/a" || brand_name == "none") && (model_name != "n/a" || model_name != "none")){
        name = model_name;
    }else{
        name = brand_name.concat(" ", model_name);
    }
    $("#edit_vehicle_name").val(name);
    
}

$("input[name='datepurchased']").change(function(){
    var date_purchased = $("input[name='datepurchased']").val();
    var date_recovered = $("input[name='recovered_date']").val();

    if(date_purchased == ""){
        $("#recovered_date").attr('hidden', false);
    }else{
        $("#recovered_date").attr('hidden', true);
    }
});

$("input[name='recovered_date']").change(function(){
    var date_purchased = $("input[name='datepurchased']").val();
    var date_recovered = $("input[name='recovered_date']").val();

    if(date_recovered == ""){
        $("#purchased_date").attr('hidden', false);
    }else{
        $("#purchased_date").attr('hidden', true);
    }
});


function openGenerateMulipleModal() {
    if ($("#frm-new-vehicle-component-dialog").isValid()) {
        $("#generate-multiple-vehicle-component-modal").modal("show");
    }
}

$("#generate-multiple-vehicle-component-modal")
    .on("shown.bs.modal", function () {
        $(this).find("input").focus();
    });

$("#frm-generate-multiple-vehicle-component")
    .on("submit", function (e) {
        e.preventDefault();
        if ($(this).isValid()) {
            $('#confirm-save-new-vehicle-component').attr("data-mode", "multiple");
            $('#confirm-save-new-vehicle-component').modal("show");
        }
    });

$(".archive-remarks .btnArchive").on("click", function (e) {
    e.preventDefault();
    var archive_remarks = $("#archive-remarks-text-area").val();
    $("#archive_remarks_update").val(archive_remarks);
    $(".archive-remarks").modal("hide");
    $('#confirm-update-vehicle').modal("show");
});    