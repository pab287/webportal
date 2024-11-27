const isGen = $("input[name='isGen']");
$("input[name='gen_code']").attr("disabled", isGen.is(":checked"));

let images = [];
let primaryImage = null;

$(".dt-picker").datepicker({
    todayHighlight: true,
    format: "yyyy/mm/dd"
});

toastr.options = {
    positionClass: "toast-top-right toast-opacity-1",
    timeOut: "15000",
    closeButton: true,
    newestOnTop: true,
};

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
    $("#select2-asset-department").val('').trigger('change');
    $("#select2-station").val('').trigger('change');
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
                    // var newOption = new Option(value.sub_cat_desc, value.sub_cat_code, false, false);
                    var newOption = new Option(value.sub_cat_desc, value.sub_cat_id, false, false);
                    select2SubCategory.append(newOption).trigger('change');
                });
            }
        });
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
                    id: item.code // (item.name.replace(/\s/g, '')).toLowerCase()
                }
            });

            return {
                results
            }
        }
    }
});

// init department
$("#select2-asset-department").select2({
    placeholder: 'Select Department',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_department_collection"),
        dataType: "JSON",
        delay: 500,
        processResults: function (data) {
            const results = $.map(data.results, function (item) {
                return {
                    text: item.description,
                    id: item.id
                }
            });

            return {
                results
            }
        }
    }
});

$("#select2-station").select2({
    placeholder: 'Select Station',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_station_collection"),
        dataType: "JSON",
        delay: 500,
        processResults: function (data) {
            const results = $.map(data.results, function (item) {
                return {
                    text: item.station,
                    id: item.id
                }
            });

            return {
                results
            }
        }
    }
});

$(".money-mask")
    .maskMoney({ thousands: ',', decimal: '.', allowZero: true });

$("#frm-new-fixed-asset").on("submit", function (e) {
    e.preventDefault();

    if ($(this).isValid()) {
        $('#confirm-save').attr("data-mode", "save-and-reset");
        $('#confirm-save').modal("show");
    }
});

function saveAndContinue() {
    if ($("#frm-new-fixed-asset").isValid()) {
        $('#confirm-save').attr("data-mode", "save-and-continue");
        $('#confirm-save').modal("show");
    }
}

function openGenerateMulipleModal() {
    if ($("#frm-new-fixed-asset").isValid()) {
        $("#generate-multiple-asset-modal").modal("show");
    }
}

$("#generate-multiple-asset-modal")
    .on("shown.bs.modal", function () {
        $(this).find("input").focus();
    });


$("#frm-generate-multiple-asset")
    .on("submit", function (e) {
        e.preventDefault();
        if ($(this).isValid()) {
            $('#confirm-save').attr("data-mode", "multiple");
            $('#confirm-save').modal("show");
        }
    });


function formReset() {
    resetSelect2();
    $("#frm-new-vehicle").resetForm();
}

$("#images").on("change", function () {
    const files = this.files;
    if (files) {
        for (let i = 0; i < files.length; i++) {
            const FR = new FileReader();
            let file = files[i];
            const name = files[i].name;
            if (images.some(image => image.name === name)) {
                continue;
            }

            /*** limit file upload by 1mb ***/
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
                            "                   <a data-lightbox='roadtrip' data-title='comp_1.jpg' href='" + base64data + "'>" +
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
                            "                               data-delay='{\"show\": 300}' data-dark='skin' data-placement='bottom'>" +
                            "                               <i class='la la-check'></i>" +
                            "                       </button>" +
                            "                       <button type='button'" +
                            "                               class='btn btn-default m-btn--hover-danger m-btn m-btn--pill " +
                            "                                      m-btn--icon m-btn--icon-only btnNew' " +
                            "                               data-index='" + i + "' data-name='" + files[i].name + "'" +
                            "                               onclick='removeImage(this)'" +
                            "                               data-toggle='m-tooltip' data-original-title='Remove Image'" +
                            "                               data-delay='{\"show\": 300}' data-dark='skin' data-placement='bottom'>" +
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
                    }
                })
                .catch((err) => {
                });
            toastr.info("Image(s) has been added, click on the save changes to apply the uploaded image.", "Asset Upload Notification", 5000);

            /*FR.addEventListener("load", function (e) {
                const image = "<div class='images-preview-container__wrapper d-flex flex-column'>" +
                    "                   <a data-lightbox='roadtrip' data-title='comp_1.jpg' href='" + e.target.result + "'>" +
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
                    "                               data-delay='{\"show\": 300}' data-dark='skin' data-placement='bottom'>" +
                    "                               <i class='la la-check'></i>" +
                    "                       </button>" +
                    "                       <button type='button'" +
                    "                               class='btn btn-default m-btn--hover-danger m-btn m-btn--pill " +
                    "                                      m-btn--icon m-btn--icon-only btnNew' " +
                    "                               data-index='" + i + "' data-name='" + files[i].name + "'" +
                    "                               onclick='removeImage(this)'" +
                    "                               data-toggle='m-tooltip' data-original-title='Remove Image'" +
                    "                               data-delay='{\"show\": 300}' data-dark='skin' data-placement='bottom'>" +
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

function setDisabledOnAssetCode() {
    const isRequired = isGen.is(":checked") ? "" : "required";
    $("input[name='gen_code']")
        .attr({ "disabled": isGen.is(":checked"), "data-validation": isRequired })
        .val("");
}

function setTotalCost() {
    const addtlCost = parseFloat($("input[name='beg_addcost']").val().toString().replace(/,/g, "")) || 0;
    const purchaseprice = parseFloat($("input[name='purchaseprice']").val().toString().replace(/,/g, "")) || 0;
    const totalCost = addtlCost + purchaseprice;
    $("input[name='total_cost']").val(numberWithCommas(totalCost));
}

function numberWithCommas(x) {
    let parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
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


function saveNewAsset() {
    const form = $("#frm-new-fixed-asset");
    const formData = new FormData(form[0]);
    const mode = $("#confirm-save").attr("data-mode");
    formData.append("mode", mode);

    asyncAppendImageToFormData(images, formData)
        .then(() => {
            $('#confirm-save').modal("hide");
            $("#generate-multiple-asset-modal").modal("hide");

            if (primaryImage) {
                formData.append("primary_pic", primaryImage.name);
            }

            const generateCount = $("#frm-generate-multiple-asset").find("[name='count']").val();
            const saveTimes = (mode === "multiple") ? generateCount : 1;

            let ctr = 0;
            for (let i = 0; i < saveTimes; i++) {
                $.ajax({
                    url: baseUrl("ams/assets/save_new_asset"),
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            ctr += 1;
                            const _images = images; // store hoisted images to local variable to be cleared
                            let primary = null;

                            if (response.fail_uploads.length) {
                                const l = response.fail_uploads.length;
                                $("#fail-upload-alert-dialog .image-list").html("");
                                $("#fail-upload-alert-dialog .auto-selected-primary").html("");

                                for (let i = 0; i < l; i++) {
                                    const item = response.fail_uploads[i];
                                    const image = _images.find(({ name }) => name === item.filename);
                                    primary = _images.find(({ name }) => name === response.primary_pic);
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
                                        "                        If you want to change primary picture you can go to masterfile > search the asset then click edit on actions." +
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
                            }

                            const time = (mode === "multiple") ? (i * 300) : 0;
                            toastr.success(response.message, "Successfully saved.");

                            if (mode === "save-and-reset") {
                                resetAllFields(form);
                                // setTimeout(() => {
                                //     window.location.assign(baseUrl("ams/assets/edit_fixed_asset/" + response.id));
                                // }, 86400);
                            }else{
                                resetAllFields(form);
                                // setTimeout(() => {
                                //     window.location.assign(baseUrl("ams/assets/new_fixed_asset"));
                                // }, 86400);
                            }

                            if (mode === "multiple") {
                                if (ctr === parseInt(saveTimes)) {
                                    $("#frm-generate-multiple-asset").find("[name='count']").val("1");
                                    toastr.info("Multiple Assets successfully saved.", "Successfully saved.");

                                    // setTimeout(() => {
                                    //     window.location.assign(baseUrl("ams/assets/fixed_masterfile"));
                                    // }, 86400);
                                }
                            }
                        } else {
                            if (response.status === "duplicated") {
                                toastr.warning(response.message, "Duplicated Asset Code");
                            } else {
                                toastr.error(response.message, "Error");
                            }
                        }
                    }
                });

                // if (i === saveTimes) {
                //     setTimeout(() => {
                //         window.location.assign(baseUrl("ams/assets/new_fixed_asset"));
                //     }, 1500);
                // }
            }
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
            primaryImage = null;
        }
    }
}

function setPrimaryPicture(el) {
    const _name = $(el).attr("data-name");
    const index = images.findIndex(({ name }) => name === _name);
    primaryImage = images[index];
    $(".primary-image-container").css("background-image", "url('" + primaryImage.base64 + "')");

    $(".images-preview-container__wrapper .set-primary").removeClass("selected");
    $(el).addClass("selected");
}

$('body')
    .tooltip({
        selector: '[data-toggle]'
    });

$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

var brandModelOnEdit = function () {
    var typingTimer;
    var doneTypingInterval = 5000;
    var doneTyping = function (e) {
        console.log(e.target);
    }

    $('.lock-asset_name').on("keyup", function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping(e), doneTypingInterval);
    });
}

function generate_asset_name(){
    var brand_name = $("#asset_brand").val().toLowerCase();
    var model_name = $("#asset_model").val().toLowerCase();
    var name = "";
    
    if((brand_name == "n/a" || brand_name == "none") && (model_name == "n/a" || model_name == "none")){
        name = "";
        $("#asset_name").attr('readonly', false);
    }else if((model_name == "n/a" || model_name == "none") && (brand_name != "n/a" || brand_name != "none")){
        name = brand_name;
    }else if((brand_name == "n/a" || brand_name == "none") && (model_name != "n/a" || model_name != "none")){
        name = model_name;
    }else{
        name = brand_name.concat(" ", model_name);
    }
    $("#asset_name").val(name);
    
}

$(document).ready(function(){
    $("input[name='datepurchased']").change(function(e){
        var date_purchased = $("input[name='datepurchased']").val();
        var date_recovered = $("input[name='recovered_date']").val();
    
        if(date_purchased == ""){
            $("#recovered_date").attr('hidden', false);
        }else{
            $("#recovered_date").attr('hidden', true);
        }

    });

    $("input[name='recovered_date']").change(function(e){
        var date_purchased = $("input[name='datepurchased']").val();
        var date_recovered = $("input[name='recovered_date']").val();
    
        if(date_recovered == ""){
            $("#purchased_date").attr('hidden', false);
        }else{
            $("#purchased_date").attr('hidden', true);
        }

    });

    if($("#asset_checkno").val().length >= 50){
        $("#checkno_container").removeClass('col-xl-3');
        $("#checkno_container").addClass('col-xl-12');
    }else{  
        $("#checkno_container").removeClass('col-xl-12 col-lg-12 col-md-12 col-sm-12');
        $("#checkno_container").addClass('col-xl-3 col-lg-3 col-md-3 col-sm-12');
    }
    $("#asset_checkno").keyup(function(){
        if($("#asset_checkno").val().length >= 50){
            $("#checkno_container").removeClass('col-xl-3');
            $("#checkno_container").addClass('col-xl-12');
        }else{  
            $("#checkno_container").removeClass('col-xl-12 col-lg-12 col-md-12 col-sm-12');
            $("#checkno_container").addClass('col-xl-3 col-lg-3 col-md-3 col-sm-12');
        }
    });
    
});
