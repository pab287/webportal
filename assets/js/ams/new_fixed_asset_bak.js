//submit
// OLD NEW FIXED ASSET JS

$.validate({
    form: '#frm_newAsset',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: form[0].action,
            type: "POST",
            dataType: "json",
            data: $("#frm_newAsset").find("input,select,textarea,checkbox").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.status) {
                    toastr.success(data.toastr_msg, "Notification", 5000);
                    window.location.replace(baseUrl("ams/assets/fixed_masterfile"));
                } else {
                    toastr.error(data.toastr_msg, "Notification", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

// init select2 company
$("#select2_company").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_company_collection"),
        processResults: function (data) {
            return data;
        }

    }
});

// init select2 department
$("#select2_department").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_department_collection"),
        processResults: function (data) {
            return data;
        }

    }
});

// init select2 category
$("#select2_category").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_category_collection/asset"),
        processResults: function (data) {
            return data;
        }

    }
});

$('#select2_category').on('select2:select', function (e) {
    var data = e.params.data;
    var id = data.id;
    $.ajax({
        url: baseUrl("ams/assets/type_lookup"),
        type: "POST",
        dataType: "JSON",
        data: {cat_id: data.cat_id, csrf_token: _csrf_hash},
        success: function (data) {
            $("#select2_type").select2("destroy");

            $("#select2_type option").each(function () {
                $(this).remove();
            });

            $("#select2_type").select2({placeholder: 'Select option', width: '100%',});
            $.each(data.results, function (key, value) {
                var newOption = new Option(value.text, value.id, false, true);
                $('#select2_type').append(newOption).trigger('change');
            });

            if ($('#isGen').is(':checked')) {

                $.ajax({
                    url: baseUrl("ams/assets/generate_asset_code"),
                    type: "POST",
                    dataType: "JSON",
                    data: {cat_id: id, sub_cat_id: $("#select2_type").val(), csrf_token: _csrf_hash},
                    success: function (data) {
                        $("input[name=assetacode]").val(data.assetacode);
                    }
                });

            }
        }
    });
});

// init select2 sub cat - type
$("#select2_type").select2({
    placeholder: 'Select option',
    width: '100%',
});

$('#select2_type').on('select2:select', function (e) {
    var data = e.params.data;
    var cat_id = $("#select2_category").val();

    if ($('#isGen').is(':checked')) {

        $.ajax({
            url: baseUrl("ams/assets/generate_asset_code"),
            type: "POST",
            dataType: "JSON",
            data: {cat_id: cat_id, sub_cat_id: data.id, csrf_token: _csrf_hash},
            success: function (data) {
                $("input[name=assetacode]").val(data.assetacode);
            }
        });

    }
});

// init select2 location
$("#select2_station").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_location_collection"),
        processResults: function (data) {
            return data;
        }
    }
});

// init select2 location
$("#select2_area").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_area_collection"),
        processResults: function (data) {
            return data;
        }
    }
});

// init select2 location
$("#select2_status").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_status_collection"),
        processResults: function (data) {
            return data;
        }
    }
});

// init date_purchased_picker, date_received_picker
$('#date_purchased_picker, #date_received_picker').datepicker({
    todayHighlight: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    }
});

function isChecked() {
    if (document.getElementById('isGen').checked) {
        $('[name="assetacode"]').attr('readOnly', 'readOnly');
        $('[name="assetacode"]').attr('required', false);
        if ($('[name="assetacode"]').hasClass('err')) {
            $('[name="assetacode"]').removeClass('err');
        }
    } else {
        $('[name="assetacode"]').removeAttr('readOnly');
        $('[name="assetacode"]').attr('required', true);
    }

    var category = $("#select2_category").val();
    var type = $("#select2_type").val();

    if (category != null && type != null) {
        $.ajax({
            url: baseUrl("ams/assets/generate_asset_code"),
            type: "POST",
            dataType: "JSON",
            data: {cat_id: category, sub_cat_id: type, csrf_token: _csrf_hash},
            success: function (data) {
                $("input[name=assetacode]").val(data.assetacode);
            }
        });
    }
}

// currency format
// $("#purchaseprice").inputmask('999,999,999.99', {
//   numericInput: true,
//   placeholder: "",
//   autoUnmask: true,
// }); 

