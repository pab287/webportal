let images = [];
let primaryImage = null;
let defaultPrimary = null;
let uploadedImagesToRemove = [];

const id = window.location.pathname.split("/").pop();
getPrimaryPic(id);

$(".dt-picker").datepicker({
    format: "yyyy/mm/dd",
    todayHighlight: true,
    todayBtn: "linked",
    clearBtn: true,
});

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

function resetSelect2() {
    $("#select2-company").val('').trigger('change');
    $("#select2-asset-category").val('').trigger('change');
    $("#select2-vehicle-type").val('').trigger('change');
    $("#select2-sub-category").val('').trigger('change');
    $("#select2-location").val('').trigger('change');
    $("#select2-status").val('').trigger('change');
}


// init select2 company
$("#select2-company").select2({
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
                    var newOption = new Option(value.sub_cat_desc, value.sub_cat_id, false, false);
                    select2SubCategory.append(newOption).trigger('change');
                });
            }
        });
    });

// init select2 vehicle type
$("#select2-vehicle-type").select2({
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
$("#select2-sub-category")
    .select2({
        placeholder: 'Select Subcategory',
        width: '100%',
    });


// init select2 location
$("#select2-location").select2({
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
$("#select2-status").select2({
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
                    // id: (item.name.replace(/\s/g, '')).toLowerCase()
                    id: item.code
                }
            });

            return {
                results
            }
        }
    }
});

$("input[name='purchaseprice']")
    .maskMoney({ thousands: ',', decimal: '.', allowZero: true })
    .on("keyup", function () {
        const amt = $("input[name='purchaseprice']").val();
        $("input[name='total_cost']").val(amt);
    });

$("#frm-edit-vehicle-component").on("submit", function (e) {
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
                                "                       <button title='Set as Primary' data-placement='bottom'" +
                                "                               type='button'" +
                                "                               class='mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary" +
                                "                                      m-btn--icon m-btn--icon-only btnNew" + ((i === 0 && !primaryImage) ? ' selected' : '') + "' " +
                                "                               data-name='" + files[i].name + "'" +
                                "                               onclick='setPrimaryPicture(this)'>" +
                                "                               <i class='la la-check'></i>" +
                                "                       </button>" +
                                "                       <button title='Remove Image' data-placement='bottom'" +
                                "                               type='button'" +
                                "                               class='btn btn-default m-btn--hover-danger m-btn m-btn--pill " +
                                "                                      m-btn--icon m-btn--icon-only btnNew' " +
                                "                               data-index='" + i + "' data-name='" + files[i].name + "'" +
                                "                               onclick='removeImage(this)'>" +
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
                        "                       <button title='Set as Primary' data-placement='bottom'" +
                        "                               type='button'" +
                        "                               class='mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary" +
                        "                                      m-btn--icon m-btn--icon-only btnNew" + ((i === 0 && !primaryImage) ? ' selected' : '') + "' " +
                        "                               data-name='" + files[i].name + "'" +
                        "                               onclick='setPrimaryPicture(this)'>" +
                        "                               <i class='la la-check'></i>" +
                        "                       </button>" +
                        "                       <button title='Remove Image' data-placement='bottom'" +
                        "                               type='button'" +
                        "                               class='btn btn-default m-btn--hover-danger m-btn m-btn--pill " +
                        "                                      m-btn--icon m-btn--icon-only btnNew' " +
                        "                               data-index='" + i + "' data-name='" + files[i].name + "'" +
                        "                               onclick='removeImage(this)'>" +
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

function formReset() {
    resetSelect2();
    $("#frm-edit-vehicle-component").resetForm();
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
    const form = $("#frm-edit-vehicle-component");
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
                                    window.location.assign(baseUrl("ams/vehicles/vehicle_components_masterfile"));
                                }, 1500);
                            }else{
                                toastr.success(response.message, "Successfully updated.", 50000);
                                setTimeout(() => {
                                    // window.location.assign(baseUrl("ams/assets/fixed_masterfile"));
                                    window.location.assign(baseUrl("ams/vehicles/edit_vehicle_component/" + response.id));
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
                                                           <button title="Set as Primary" data-placement="bottom" 
                                                                   type="button"
                                                                   class="mr-3 btn btn-default m-btn--hover-success m-btn m-btn--pill set-primary
                                                                          m-btn--icon m-btn--icon-only btnNew ${((item.image === _primaryImage) ? ' selected' : '')}" 
                                                                   data-name="${item.image}"
                                                                   onclick='setPrimaryPictureFromUploaded("${item.image}",${id},this)'>
                                                                   <i class="la la-check"></i>
                                                           </button>
                                                           <button title="Remove Image" 
                                                                   data-placement="bottom" type="button"
                                                                   class="btn btn-default m-btn--hover-danger m-btn m-btn--pill 
                                                                          m-btn--icon m-btn--icon-only btnNew" 
                                                                   data-index="${id}" data-name="${item.image}"
                                                                   onclick='removeUploaded("${item.image}",${item.id}, this)'>
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
    resetSelect2();
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
                var url = baseUrl('uploads/files/images/vehicles/' + id + '/' + response.primary_pic);
                $(".primary-image-container").css("background-image", "url('" + primaryImage.base64 + "')");
                $("#primary_pic").attr("data-title", defaultPrimary);
                $("#primary_pic").attr("href", url);
            }
        }
    });
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

function loadTab(el, view, reloadTable = false) {
    const tab = $(el).attr("href");
    const table = tab + '-table';
    const currentTabContent = $(tab).html();
    const _tab = tab.replace("#", "");
    // if (currentTabContent && reloadTable) {
    //     $(table).DataTable().ajax.reload();
    //     return;
    // }

    
    $.ajax({
        url: baseUrl("ams/vehicles/load_view_tab/?view=" + view),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            const view = response.view;
            $(tab).html(view);
            loadTable(tab, table);
        }
    });
}


// var tbl_bor = $('#tab-borrowing-table').DataTable({
//     dom: '<"toolbar">frtlip',
//     serverSide: true,
//     // processing: true,
//     ajax: {
//         url: baseUrl("ams/vehicles/get_vehicle_component_borrowing/" + id),
//         type: "post",
//         dataType: "json",
//         data: function (d) {
//             d.csrf_token = _csrf_hash;
//             d.search = $("#search-vehicle-borrowing-history").val();
//         }
//     },
//     searching: false,
//     columns: [
//         {
//             data: "date_trans",
//             width: "15%",
//             // render: function (data) {
//             //     return dateFormatter(new Date(data));
//             // }
//         },
//         {
//             data: "reference_no",
//             width: "20%"
//         },
//         {
//             data: "employee",
//             render: function (data, type, row) {
//                 return "" +
//                     " <p class='mb-0'>" + data + "</p>" +
//                     " <p class='text-muted mb-0' id='asset_borrower_company'>" +  row.company + "</p>";
//             }
//         },
//         {
//             data: "status",
//             width: "30%",
//             render: function (data) {
//                 return parseInt(data) === 0 ? 'Current' : 'Returned';
//             }
//         }
//         , {
//             data: 'days_overdue',
//             width: '15%',
//             className: 'text-center',
//             render: function (data, type, row) {
//                 if (parseInt(row.status) === 1) {
//                     return 0;
//                 } else {
//                     return parseInt(data) <= 0 ? 0 : data;
//                 }
//             }
//         }
//     ]
// });
// $("#search-vehicle-borrowing-history")
//     .donetyping(function () {
//         tbl_bor.ajax.reload();
//     });


// function loadAccountabilityTab(){
var tbl_acc = $('#tab-accountability-table').DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/vehicles/get_vehicles_accountability_borrowing/" + id),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search = $("#search-vehicle-accountability").val();
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
            data: "borrower",
            width: "20%",
            render: function (data, type, row) {
                return "" +
                    " <p class='mb-0'>" + data + "</p>" +
                    " <p class='text-muted mb-0' id='asset_borrower_company'>" +  row.company + "</p>";
            }
        },
        {
            data: "over_due",
            width: "10%"
        },
        {
            data: "status",
            width: "20%",
            render: function (data, type, row) {
                // return row.parent_status
                if (row.parent_status === "Cancelled") {
                    return "" +
                    " <p class='mb-0'>Cancelled/Archived</p>" +
                    " <p class='text-muted mb-0'>" +  row.module + "</p>";
                } else {
                    if(row.status == 1){
                        return "" +
                        " <p class='mb-0'>Returned</p>" +
                        " <p class='text-muted mb-0'>" +  row.module + "</p>";
                    }else if(row.status == 2){
                        return "" +
                        " <p class='mb-0'>Partially Returned</p>" +
                        " <p class='text-muted mb-0'>" +  row.module + "</p>";
                    }else{
                        return "" +
                        " <p class='mb-0'>Current</p>" +
                        " <p class='text-muted mb-0'>" +  row.module + "</p>";
                    }
                }
            }
        },
    ]
});
$("#search-vehicle-accountability")
    .donetyping(function () {
        tbl_acc.ajax.reload();
    });
// }
// function loadTable(tab, table) {
//     const _tab = tab.replace("#", ""); // remove number sign
//     let tbl;

//     switch (_tab) {
//         case 'tab-borrowing':
//             tbl = $(table).DataTable({
//                 dom: '<"toolbar">frtlip',
//                 serverSide: true,
//                 processing: true,
//                 ajax: {
//                     url: baseUrl("ams/vehicles/get_vehicle_component_borrowing/" + id),
//                     type: "post",
//                     dataType: "json",
//                     data: function (d) {
//                         d.csrf_token = _csrf_hash;
//                         d.search['value'] = $("#search-vehicle-borrowing-history").val();
//                     }
//                 },
//                 searching: false,
//                 columns: [
//                     {
//                         data: "date_trans",
//                         width: "15%",
//                         // render: function (data) {
//                         //     return dateFormatter(new Date(data));
//                         // }
//                     },
//                     {
//                         data: "reference_no",
//                         width: "20%"
//                     },
//                     {
//                         data: "employee",
//                         render: function (data, type, row) {
//                             return "" +
//                                 " <p class='mb-0'>" + data + "</p>" +
//                                 " <p class='text-muted mb-0' id='asset_borrower_company'>" +  row.company + "</p>";
//                         }
//                     },
//                     {
//                         data: "status",
//                         width: "30%",
//                         render: function (data) {
//                             return parseInt(data) === 0 ? 'Current' : 'Returned';
//                         }
//                     }
//                     , {
//                         data: 'days_overdue',
//                         width: '15%',
//                         className: 'text-center',
//                         render: function (data, type, row) {
//                             if (parseInt(row.status) === 1) {
//                                 return 0;
//                             } else {
//                                 return parseInt(data) <= 0 ? 0 : data;
//                             }
//                         }
//                     }
//                 ]
//             });

//             $("#search-vehicle-borrowing-history")
//                 .donetyping(function () {
//                     tbl.ajax.reload();
//                 });
//             break;
//         case 'tab-accountability':
//             tbl = $(table).DataTable({
//                 dom: '<"toolbar">frtlip',
//                 serverSide: true,
//                 processing: true,
//                 ajax: {
//                     url: baseUrl("ams/vehicles/get_vehicle_accountability/" + id),
//                     type: "post",
//                     dataType: "json",
//                     data: function (d) {
//                         d.csrf_token = _csrf_hash;
//                         d.search['value'] = $("#search-vehicle-accountability").val();
//                     }
//                 },
//                 searching: false,
//                 columns: [
//                     {
//                         data: "date_issued",
//                         width: "15%",
//                         // render: function (data) {
//                         //     return dateFormatter(new Date(data));
//                         // }
//                     },
//                     {
//                         data: "reference_no",
//                         width: "20%"
//                     },
//                     {
//                         data: "employee",
//                         render: function (data, type, row) {
//                             return "" +
//                                 " <p class='mb-0'>" + data + "</p>" +
//                                 " <p class='text-muted mb-0'>" + row.company + "</p>";
//                         }
//                     },
//                     {
//                         data: "status",
//                         width: "30%",
//                         render: function (data, type, row) {
//                             if (row.acct_status === "Cancelled") {
//                                 return "Cancelled";
//                             } else {
//                                 switch (parseInt(data)) {
//                                     case 1:
//                                         return "Returned";
//                                         break;
//                                     case 2:
//                                         return "Partially Returned";
//                                         break;
//                                     default:
//                                         return "Current";
//                                         break;
//                                 }
//                             }
//                         }
//                     }
//                 ]
//             });

//             $("#search-vehicle-accountability")
//                 .donetyping(function () {
//                     tbl.ajax.reload();
//                 });
//             break;
//         default:
//             return "asdasd";
//             break;
//     }

//     
// }
$(".tab-clear-search")
        .on("click", function () {
            // $("#search-vehicle-components").val("");
            // $("#search-vehicle-costing").val("");
            // $("#search-vehicle-document").val("");
            $("#search-vehicle-accountability").val("");
            $("#search-vehicle-borrowing-history").val("");
            // $("#search-vehicle-maintenance").val("");
            $("#tab-accountability-table").ajax.reload();
        });

$('#inventory-dt').datepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'mm/dd/yyyy',
});

$("#inventory-dt").datepicker("setDate", new Date());

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

function edit_generate_vehicle_comp_name(){
    var brand_name = $("#edit_comp_vehicle_brand").val().toLowerCase();
    var model_name = $("#edit_comp_vehicle_model").val().toLowerCase();
    var name = "";
    
    if((brand_name == "n/a" || brand_name == "none") && (model_name == "n/a" || model_name == "none")){
        name = "";
        $("#edit_comp_vehicle_name").attr('readonly', false);
    }else if((model_name == "n/a" || model_name == "none") && (brand_name != "n/a" || brand_name != "none")){
        name = brand_name;
    }else if((brand_name == "n/a" || brand_name == "none") && (model_name != "n/a" || model_name != "none")){
        name = model_name;
    }else{
        name = brand_name.concat(" ", model_name);
    }
    $("#edit_comp_vehicle_name").val(name);
}   

$(".archive-remarks .btnArchive").on("click", function (e) {
    e.preventDefault();
    var archive_remarks = $("#archive-remarks-text-area").val();
    $("#archive_remarks_update").val(archive_remarks);
    $(".archive-remarks").modal("hide");
    $('#confirm-update-vehicle').modal("show");
});    