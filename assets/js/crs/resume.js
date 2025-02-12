var search_val = "";
var temp_images = [];
var clone_element = null;
var tblResume = $("#table-resume")
    .DataTable({
        dom: 'rtlip',
        serverSide: true,
        processing: true,
        order: [[ 0, "desc" ]],
        ajax: {
            url: baseUrl("crs/get_resume_collection/"),
            dom: "ft",
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.year = selectedYear;
            }
        },
        searching: true,
        columns: [
            {
                data: "id",
                width: "1%",
                "visible": false,
            },
            {
                data: "applied_dt",
            },
            {
                data: "name",
            },
            {
                data: "contact_no",
            },
            {
                data: "school",
                render: function (data) {
                    
                    return formatTag(data);
                }
            },
            {
                data: "course",
                render: function (data) {
                    return formatTag(data);
                }
            },
            {
                data: "position",
                render: function (data) {
                    return formatTag(data);
                }
            },
            {
                data: "tag1",
                render: function (data) {
                    return formatTag(data);
                }
            },
            {
                data: "status",
            },
            {
                data: "description",
            },
            {
                data: "recruitment",
            },
            {
                data: "filename",
            },
            {
                data: null,
                width: "4%",
                className: "text-center",
                orderable: false,
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.status);
                    // return "asd";
                },
            },
        ],buttons: [
            { 
                extend: 'csv',
                title: 'CRS - RESUME REPORT',
                exportOptions: {
                    columns: [1,2,3,4,5,6,7,8,9,10]
                },
                action: function (e, dt, button, config) {
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    logexport(config.title);
                }
            }, { 
                extend: 'excelHtml5',
                title: 'CRS - RESUME REPORT',
                exportOptions: {
                    columns: [1,2,3,4,5,6,7,8,9,10]
                },
                action: function (e, dt, button, config) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    logexport(config.title);
                }
            }, { 
                extend: 'pdfHtml5',
                title: 'CRS - RESUME REPORT',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                titleAttr: 'PDF',
                exportOptions: {
                    columns: [1,2,3,4,5,6,7,8,9,10]
                },
                action: function (e, dt, button, config) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    logexport(config.title);
                }
            }
        ]
    });

    var currentYear = new Date().getFullYear();
    for (var year = 2015; year <= currentYear; year++) {
        $('#year').append(new Option(year, year));
    }
    $('#year').val(currentYear).trigger('change');
    $('#year').select2({
        placeholder: 'Year',
        width: '100%',
        allowClear: true,
    }).on("select2:select", function (e) {
        selectedYear = $(this).val();
        tblResume.ajax.reload();
    }).on("select2:unselecting", function (e) {
        selectedYear = "All";
      tblResume.ajax.reload();
  });

  var selectedYear = $('#year').val();
    
$("#ExportExcel").on("click", function(e) {
    e.preventDefault();
    tblResume.button( '.buttons-excel' ).trigger();
});

$("#ExportCSV").on("click", function(e) {
    e.preventDefault();
    tblResume.button( '.buttons-csv' ).trigger();
});

$("#ExportPDF").on("click", function(e) {
    e.preventDefault();
    tblResume.button( '.buttons-pdf' ).trigger();
});

function deleteTempResume(){
    $("#files").html("");
    $.ajax({
        url: baseUrl("crs/temp_images_delete"),
        type: "POST",
        global: false,
        data: {
            csrf_token: _csrf_hash,
            data: 0
            },
    });
}

$("#tag_id, #edit_resume_tag_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_tag"),
        global: false,
        processResults: function (data) {
            return data;
        },
        delay: 500
    }
});

$("#school_id, #edit_resume_school_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_school"),
        global: false,
        processResults: function (data) {
            return data;
        },
        delay: 500
    }
});

$("#course_id, #edit_resume_course_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_course"),
        global: false,
        processResults: function (data) {
            return data;
        },
        delay: 500
    }
});

$("#position_id, #edit_resume_position_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_position"),
        global: false,
        processResults: function (data) {
            return data;
        },
        delay: 500
    }
});

$('#applied_dt, #edit_resume_applied_dt')
    .datepicker({
        todayHighlight: true,
        autoclose: true,
        pickerPosition: 'bottom left',
        todayBtn: 'linked',
        format: 'mm/dd/yyyy',
        forceParse: false
    });

function formatTag(data) {
    if (data.charAt(0) === ",") {
        return data.substr(1);
    } else {
        return data;
    }
}

function itemDatatableActions($id, $status) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<div class='dropdown'>";
        _actionButton += "<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
        _actionButton += "<i class='fa fa-ellipsis-v'></i>";
        _actionButton += "</a>";
        _actionButton += "<div class='dropdown-menu dropdown-menu-right'>";
        if ($status == "doneinterview") {
            _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='hire_resume(" + $id + ")'><i class='la la-thumbs-o-up'></i>Hire</a>";
            _actionButton += "<div class='dropdown-divider'></div>";
        }
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='view_resume(" + $id + ")'><i class='la la-eye'></i>Preview Application</a>";
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='edit_resume(" + $id + ")'><i class='la la-pencil-square'></i>Edit</a>";
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='archive_resume(" + $id + ")'><i class='la la-folder'></i>Archive</a>";
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='open_delete(" + $id + ")'><i class='la la-trash'></i>Delete</a>";
        _actionButton += " </div>";
        _actionButton += "</div>";

        // _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnViewItem' onclick='view_resume("+$id+")'><i class='la la-eye'></i></button>";
        // _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_resume("+$id+")'><i class='la la-pencil-square'></i></button>";
        // _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='archive_resume("+$id+")'><i class='la la-folder'></i></button>";
        // _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='delete_resume("+$id+")'><i class='la la-trash'></i></button>";
        return _actionButton;
    } else {
        return "";
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    if(search_val.length >= 3){
        $.ajax({
            url: baseUrl("crs/search_confirm_val"),
            type: "post",
            data: {
                csrf_token: _csrf_hash,
                search_val: search_val
            },
            success: function(resp){
                if(resp == true){
                    toastr.error("This user status is currently Blacklisted.", "Invalid Data!", 10000);
                }
                
            }
        });
    }
    
    tblResume.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblResume.ajax.reload();
});

function open_delete($id) {
    $('[name="delete_id"]').val($id);
    $('#modal_form_delete').modal('show'); // show bootstrap modal
    $('.modal-title').text('Delete Confirmation'); // Set Title to Bootstrap modal title
}

function open_resume() {
    var x = document.getElementById("tag_id");
    x.remove(x.selectedIndex);
    x = document.getElementById("school_id");
    x.remove(x.selectedIndex);
    x = document.getElementById("course_id");
    x.remove(x.selectedIndex);
    x = document.getElementById("position_id");
    x.remove(x.selectedIndex);
    save_method = 'add';
    $('#form_document')[0].reset(); // reset form on modals

    $('#modal_form_document').modal('show'); // show bootstrap modal
    $('.modal-title').text('New Resume'); // Set Title to Bootstrap modal title
}

function archive_resume(id) {
    $("#frm-confirm-archive-resume").attr('action', baseUrl('crs/archive_resume/' + id));
    $("#modal-confirm-archive-resume").modal("show");
}

function edit_resume(id) {
    window.open(baseUrl("crs/edit_application_page/?id="+id));
}

function delete_resume()
{
    $temp = $('[name="delete_id"]').val();
    // ajax delete data to database
    $.ajax({
        url: baseUrl("crs/delete_resume/") + $temp,
        type: "POST",
        // dataType: "JSON",
        global: false,
        data: { csrf_token: _csrf_hash },
        success: function (data) {
            $("#generalSearch").val("");
            search_val = $("#generalSearch").val();
            tblResume.ajax.reload();
            $('#modal_form_delete').modal('hide');
            if (data) {
                toastr.success("Resume was successfully deleted.", "Resume Deleted.", 10000);
            } else {
                toastr.error("An error occurred.", "Error", 10000);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });
}

function view_resume($id) {
    window.open(baseUrl('crs/view_resume/?id=' + $id));
}

function show()
{
    if ($('[name="recruitment"]').val() == "REFERRAL") {
        document.getElementById('referral_text').style.removeProperty('display');
    } else {
        document.getElementById('referral_text').style.display = 'none';
    }
}

/*function save_document() {
    var url;
    if ($('[name="tag_temp"]').val() != null) {
        temp = $('[name="tag_temp"]').val();
    }

    if ($('[name="tag_id"]').val() != null) {
        $.each($('[name="tag_id"]').val(), function (index, value) {
            if (temp == null) {
                temp = value;
            } else {
                temp = temp + "," + value;
            }

        });
        $('[name="tag_temp"]').val(temp);
    }
    if ($('[name="school_temp"]').val() != null) {
        temp = $('[name="school_temp"]').val();
    }

    if ($('[name="school_id"]').val() != null) {
        $.each($('[name="school_id"]').val(), function (index, value) {
            if (temp == null) {
                temp = value;
            } else {
                temp = temp + "," + value;
            }

        });
        $('[name="school_temp"]').val(temp);
    }
    if ($('[name="course_temp"]').val() != null) {
        temp = $('[name="course_temp"]').val();
    }

    if ($('[name="course_id"]').val() != null) {
        $.each($('[name="course_id"]').val(), function (index, value) {
            if (temp == null) {
                temp = value;
            } else {
                temp = temp + "," + value;
            }

        });
        $('[name="course_temp"]').val(temp);
    }
    if ($('[name="position_temp"]').val() != null) {
        temp = $('[name="position_temp"]').val();
    }

    if ($('[name="position_id"]').val() != null) {
        $.each($('[name="position_id"]').val(), function (index, value) {
            if (temp == null) {
                temp = value;
            } else {
                temp = temp + "," + value;
            }

        });
        $('[name="position_temp"]').val(temp);
    }
    if (save_method == "add") {
        url = baseUrl("crs/add_resume/");
    } else {
        url = baseUrl("crs/update_resume/") + $('[name="id"]').val();
    }

    $.validate({
        form: '#form_document',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form_document').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        $('#modal_form_document').modal('hide');
                        tblResume.ajax.reload();

                    } else {
                        alert('Error get data from ajax');
                    }

                }
            });
            return false;
        },
    });

}*/

/*$(document).ready(function () {
    document.getElementById('referral_text').style.display = 'none';
});*/

$("#recruitment, #edit_resume_recruitment")
    .select2({
        width: "100%"
    });
$("#status, #edit_resume_status")
    .select2({
        placeholder: "Select Status",
        width: "100%"
    }).on("select2:select", function (e) {
        var currentValue = e.target.value;
        var _formBlacklisted = $("#reason_blacklisted");
        if (typeof _formBlacklisted !== "undefined") {
            if (currentValue == "blacklisted") {
                if (_formBlacklisted.hasClass("m--hide")) {
                    _formBlacklisted.removeClass("m--hide");
                }
            } else {
                if (!_formBlacklisted.hasClass("m--hide")) {
                    _formBlacklisted.addClass("m--hide");
                }
            }
        }
    });
    $("#closeNewModal").on("click", function(){
        deleteTempResume();
        $("#preview").html("");
        $("#form_document").children();
    });

function hire_resume($id){
    window.open(baseUrl("crs/new_hire/?id="+$id));
}

function hireResumeInfo(formElement) {
    const form = $(formElement);
    const formData = new FormData(formElement);

    if (form.isValid()) {
        $.ajax({
            url: baseUrl("crs/hire_resume"),
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            global: false,
            data: formData,
            success: function (json) {
                if (json.response) {
                    tblResume.ajax.reload();
                    toastr.success(json.toastr_msg, "Resume Hired", { timeOut: 5000 });
                    $("#modal-form_hire").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Resume Hired", { timeOut: 5000 });
                }
            }
        });
    }
}

function archiveResume(formElement) {
    const form = $(formElement);
    const url = form.attr('action');
    $.ajax({
        url,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function (data) {
            tblResume.ajax.reload();
            $("#modal-confirm-archive-resume").modal("hide");
            if (data) {
                toastr.success("Resume was successfully archived.", "Resume Archived.", 10000);
            } else {
                toastr.error("An error occurred.", "Error", 10000);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });
}

$("#position").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_position"),
        processResults: function (data) {
            return data;
        },
        delay: 500
    }
  });


// crs report creator script
//
//
//
//
//

let template = null;
const modalContainer = $('.document-modal-container');
const dropdown = $(".m-dropdown__toggle.export-as");
let selectedFields = [];
$(document).ready(function () {
    $("form").attr('autocomplete','off');
    $('#query-builder')
        .queryBuilder({
            'bt-tooltip-errors': { delay: 100 },
            filters: [
                { id: 'firstname', label: 'Firstname', type: 'string', operators: ['equal','contains','not_equal','begins_with'] },
                { id: 'middlename', label: 'Middlename', type: 'string', operators: ['equal','contains','not_equal','begins_with'] },
                { id: 'lastname', label: 'Lastname', type: 'string', operators: ['equal','contains','not_equal','begins_with'] },
                { id: 'suffix', label: 'Suffix', type: 'string', operators: ['equal','contains','not_equal','begins_with']  },
                { id: 'contact_no', label: 'Contact No', type: 'string', operators: ['equal','contains','not_equal','begins_with'] },
                { id: 'status', label: 'Status', type: 'string' },
                { id: 'school', label: 'School', type: 'string' },
                { id: 'course', label: 'Course', type: 'string' },
                { id: 'position', label: 'Position', type: 'string' },
                { id: 'tag1', label: 'Tag', type: 'string' },
                { id: 'description', label: 'Description', type: 'string' },
                { id: 'recruitment', label: 'Recruitment', type: 'string' },
                { id: 'referral', label: 'Referral', type: 'string' },
                { 
                    id: 'applied_dt',
                    label: 'Applied Date',
                    type: 'date',
                    validation: {
                        format: 'YYYY-MM-DD'
                    },
                    plugin: 'datepicker',
                    plugin_config: {
                        format: 'yyyy-mm-dd',
                        todayBtn: 'linked',
                        todayHighlight: true,
                        autoclose: true
                    }
                }
            ]
        });

    $("#query-builder").find("button").addClass("mb-1");
    $("#query-builder").find("select2-query-builder_rule_0_filter-1e-container").addClass("col-xs-12 col-sm-12 col-lg-12");
    initSelect2OnRules();

    $('#query-builder')
        .on('click', '.btn-success', function () {
            initSelect2OnRules();
        });

    function initSelect2OnRules() {
        $(".rule-filter-container").find('select').select2()
            .on('select2:select', function (e) {
                $(".rule-operator-container").find('select').select2();
            });
    }
    $('#query-builder').on('afterCreateRuleInput.queryBuilder', function(e, rule) {
        if (rule.filter.id === 'applied_dt') {
            $(rule.$el.find('.rule-value-container input')).attr('readonly', true);
        }
    });
});

function selectItem(target, id) { // refactored this a bit, don't pay attention to this being a function
    var option = $(target).children('[value="' + id + '"]');
    option.detach();
    $(target).append(option).change();
}

const dbFieldEl = '' +
    '<option value="firstname">Firstname</option>' +
    '<option value="middlename">Middlename</option>' +
    '<option value="lastname">Lastname</option>' +
    '<option value="suffix">Suffix</option>' +
    '<option value="status">Status</option>' +
    '<option value="school">School</option>' +
    '<option value="course">Course</option>' +
    '<option value="position">Position</option>' +
    '<option value="tag1">Tag</option>' +
    '<option value="description">Description</option>' +
    '<option value="recruitment">Recruitment</option>' +
    '<option value="referral">Referral</option>' +
    '<option value="applied_dt">Applied_dt</option>' +
    '';

const dbSortFieldEl = '' +
    '<option value="firstname">Firstname</option>' +
    '<option value="middlename">Middlename</option>' +
    '<option value="lastname">Lastname</option>' +
    '<option value="suffix">Suffix</option>' +
    '<option value="status">Status</option>' +
    '<option value="school">School</option>' +
    '<option value="course">Course</option>' +
    '<option value="position">Position</option>' +
    '<option value="tag1">Tag</option>' +
    '<option value="description">Description</option>' +
    '<option value="recruitment">Recruitment</option>' +
    '<option value="referral">Referral</option>' +
    '<option value="applied_dt">Applied_dt</option>' +
    '';

$('#field')
    .append(dbFieldEl)
    .select2({
        width: '100%'
    })
    .on('select2:select', function (e) {
        const data = e.params.data;
        selectedFields.push({ id: data.id, text: data.text });
        const strId = data.text.replace(' ', '-');

        const el = '' +
            '<div class="m-widget4__item" id="' + strId + '">' +
            '    <div class="m-widget4__info pl-0">' +
            '           <span class="m-widget4__text">' +
            '           ' + (data.text).toUpperCase() +
            '           </span>' +
            '    </div>' +
            '    <div class="m-widget4__ext">' +
            '           <a href="#" onclick="removeField(\'' + strId + '\',\'' + data.id + '\')" ' +
            '              class="m-widget4__icon">' +
            '                   <i class="la la-remove"></i>' +
            '           </a>' +
            '    </div>' +
            '</div>';
        $(".empty-field-selection").html("");
        $(".m-widget4").append(el);

        selectItem(e.target, e.params.data.id);
    })
    .on('select2:unselect', function (e) {
        const data = e.params.data;
        const strId = data.text.replace(' ', '-');

        const index = selectedFields.findIndex((item) => item.id === data.id);
        selectedFields.splice(index, 1);

        $("#" + strId).remove();
        if (selectedFields.length <= 0) {
            $(".empty-field-selection").html("<p class='lead text-muted mt-3'>Please select at least one field.</p>");
        }
    });

$('#order_field')
    .append(dbSortFieldEl)
    .select2({
        width: '100%',
        placeholder: "Select Order Field",
        allowClear: true
    });

    $('#order_by')
    .append(dbSortFieldEl)
    .select2({
        width: '100%',
        placeholder: "Select Field",
        allowClear: true
    });

const template_select_option = {
    width: '100%',
    placeholder: "Load Template",
    ajax: {
        url: baseUrl('crs/get_field_templates'),
        dataType: 'JSON',
        processResults: function (data) {
            return data;
        }
    },
    allowClear: true
};

function CRSloadTemplate() {
    console.log(template);
    if (template) { // check if not null
        if (template.length) {
            selectedFields = [];
            $(".m-widget4").html("");
            const selections = template.map((item) => {
                selectedFields.push({ id: item.fieldname, text: item.header });
                const strId = item.header.replace(' ', '-');

                const el = '' +
                    '<div class="m-widget4__item" id="' + strId + '">' +
                    '    <div class="m-widget4__info pl-0">' +
                    '           <span class="m-widget4__text">' +
                    '           ' + (item.header).toUpperCase() +
                    '           </span>' +
                    '    </div>' +
                    '    <div class="m-widget4__ext">' +
                    '           <a href="#" onclick="removeField(\'' + strId + '\',\'' + item.fieldname + '\')" ' +
                    '              class="m-widget4__icon">' +
                    '                   <i class="la la-remove"></i>' +
                    '           </a>' +
                    '    </div>' +
                    '</div>';
                $(".empty-field-selection").html("");
                $(".m-widget4").append(el);

                return item.fieldname;
            });

            $('#field').val(selections).change();
        }
    }
}

$('#template')
    .select2(template_select_option)
    .on('select2:select', function (e) {
        templateId = e.params.data.id;
        $.ajax({
            url: baseUrl('crs/get_template_body/' + templateId),
            dataType: 'JSON',
            type: 'GET',
            success: function (response) {

                template = response;
            }
        })
    });

function openAddFieldTemplateDialog() {
    $.ajax({
        url: baseUrl('crs/open_modal'),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: 'crs/modals/add_field_template_dialog',
        },
        success: function (response) {
            let temp_clone_element = "document-modal-container_"+modalContainer.length + 1;
            let temp_clone_element2 = ".document-modal-container_"+modalContainer.length + 1;
            if($(temp_clone_element2).length > 0){
                $(temp_clone_element2).remove();
            }
            clone_element = temp_clone_element;
            const temp_modal = $(modalContainer).clone().removeClass("document-modal-container").addClass(clone_element).appendTo(".m-content");
            temp_modal.empty().html(response.html);
            const selectField = temp_modal.find('select[name="field[]"]');
            selectField
                .append(dbFieldEl)
                .select2({
                    width: '100%'
                })
                .on('select2:select', function (e) {
                    selectItem(e.target, e.params.data.id);
                })
            temp_modal.modal('show');
        }
    });
}



function openEditFieldTemplateDialog() {
    if (template.length) {
        const selections = template.map((item) => {
            return item.fieldname;
        });

        $.ajax({
            url: baseUrl('crs/open_modal'),
            type: "POST",
            dataType: "JSON",
            data: {
                csrf_token: _csrf_hash,
                path: 'crs/modals/edit_field_template_dialog',
                function_name: 'getTemplateData',
                formData: {
                    id: templateId
                },
                model: 'crs/Document_model'
            },
            success: function (response) {
                modalContainer.html(response.html);
                const selectField = modalContainer.find('select[name="field[]"]');
                selectField
                    .append(dbFieldEl)
                    .select2({
                        width: '100%'
                    })
                    .on('select2:select', function (e) {
                        selectItem(e.target, e.params.data.id);
                    })
                    .val(selections).change();
                modalContainer.modal('show');
            }
        });
    }
}

function initDatatable(columns, fields, order_field, order_by, criteria) {
    let _columns = [];
    columns.forEach((column) => {
        let _column = null;
        const _columnIdNo = column.id.indexOf('CAST(idno AS int)') !== -1;
        const _columnCompany = column.id.indexOf('UPPER(IF(company.id IS NULL, company_id, company.code))') !== -1;
        const _columnDepartment = column.id.indexOf('UPPER(IF(department.id IS NULL, department_id, department.code))') !== -1;
        const _columnPosition = column.id.indexOf('UPPER(IF(position.id IS NULL, position, position.name))') !== -1;
        const _columnSalaryRate = column.id.indexOf('CAST(REPLACE(salaries.sal_rate,TRIM(\',\'),\'\') AS DECIMAL(10,2))') !== -1;
        const _columnEducDegree = column.id.indexOf('educ.educ_degree') !== -1;

        if (_columnCompany) {
            _column = column.id.replace('UPPER(IF(company.id IS NULL, company_id, company.code)) ', '');
        } else if (_columnDepartment) {
            _column = column.id.replace('UPPER(IF(department.id IS NULL, department_id, department.code)) ', '');
        } else if (_columnPosition) {
            _column = column.id.replace('UPPER(IF(position.id IS NULL, position, position.name)) ', '');
        } else if (_columnSalaryRate) {
            _column = column.id.replace('CAST(REPLACE(salaries.sal_rate,TRIM(\',\'),\'\') AS DECIMAL(10,2)) ', '');
        } else if (_columnIdNo) {
            _column = column.id.replace('CAST(idno AS int) ', '');
        } else if (_columnEducDegree) {
            _column = column.id.replace("educ.", "");
        } else {
            _column = column.id.replace("", "");
        }

        if (_column === "sal_rate") {
            _columns.push({
                data: _column,
                orderable: false,
                render: function (data) {
                    return data ? data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : null;
                },
                className: 'text-right'
            });
        } else {
            _columns.push({ data: _column, orderable: false });
        }
    });

    dtReport = $('#table-generated-report').DataTable({
            dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'>>" +
                "<'row'<'col-12'rt>>" +
                "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
            destroy: true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'CRS - APPLICANT REPORT',
                    action: function (e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('crs/generate_employee_report/1'), 'excelHtml5')
                            .then(() => {
                                dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'CRS - APPLICANT REPORT',
                    action: function (e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('crs/generate_employee_report/1'), 'pdfHtml5')
                            .then(() => {
                                dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    }
                },
                {
                    extend: 'print',
                    title: 'CRS - APPLICANT REPORT',
                    action: function (e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('crs/generate_employee_report/1'), 'print')
                            .then(() => {
                                dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    }
                }
            ],
            serverSide: true,
            processing: true,
            searching: false,
            ordering: false,
            ajax: {
                url: baseUrl('crs/generate_employee_report'),
                type: 'post',
                dataType: 'json',
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                    d.fields = fields;
                    d.order_field = order_field;
                    d.order_by = order_by;
                    d.criteria = criteria;
                }
            },
            columns: _columns,
            columnDefs: [
                {
                    orderable: false,
                    targets: 0
                }
            ],
            initComplete: function () {
                const dropdown = '' +
                    '       <div class="m-dropdown m-dropdown--inline m-dropdown--align-left" ' +
                    '             data-dropdown-toggle="hover" aria-expanded="true">' +
                    '            <button class="m-dropdown__toggle btn btn-success dropdown-toggle export-as">' +
                    '                EXPORT AS' +
                    '            </button>' +
                    '            <div class="m-dropdown__wrapper">' +
                    '                <div class="m-dropdown__inner">' +
                    '                    <div class="m-dropdown__body">' +
                    '                        <div class="m-dropdown__content">' +
                    '                            <ul class="m-nav">' +
                    '                                <li class="m-nav__item">' +
                    '                                    <a id="export-as-excel" style="cursor:pointer;" ' +
                    '                                       onclick="exportAs(\'excel\'); return false;" class="m-nav__link">' +
                    '                                        <i class="m-nav__link-icon fa fa-file-excel-o m--font-success"></i>' +
                    '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
                    '                                           Excel File' +
                    '                                        </span>' +
                    '                                    </a>' +
                    '                                </li>' +
                    '                                <li class="m-nav__item">' +
                    '                                    <a id="export-as-pdf" style="cursor:pointer;" ' +
                    '                                       onclick="exportAs(\'pdf\'); return false;" class="m-nav__link">' +
                    '                                        <i class="m-nav__link-icon fa fa-file-pdf-o m--font-danger"></i>' +
                    '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
                    '                                          PDF File' +
                    '                                        </span>' +
                    '                                    </a>' +
                    '                                </li>' +
                    '                                <li class="m-nav__item">' +
                    '                                    <a id="export-as-print" style="cursor:pointer;" ' +
                    '                                       onclick="exportAs(\'print\'); return false;" class="m-nav__link">' +
                    '                                        <i class="m-nav__link-icon fa fa-print m--font-info"></i>' +
                    '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
                    '                                          Print' +
                    '                                        </span>' +
                    '                                    </a>' +
                    '                                </li>' +
                    '                            </ul>' +
                    '                        </div>' +
                    '                    </div>' +
                    '                </div>' +
                    '            </div>' +
                    '        </div>';

                $(dropdown).appendTo("#table-generated-report_wrapper .exportDropdown");
            },
            pageLength: 100
        });
}

function generateReport(form) {
    if ($(form).isValid()) {
        $('.empty-table-message').html("");
        let criteria = $("#query-builder").queryBuilder('getSQL');
        criteria = criteria ? criteria.sql : null;
        console.log(criteria);
        const table = $("#table-generated-report");
        if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }
        const thead = table.find('thead');
        const tbody = table.find('tbody');
        if (tbody.children().length > 0) {
            tbody.empty();
        }
        let th = "";
        selectedFields.forEach((field) => {
            th += "<th>" + field.text + "</th>";
        });
        const fields = $('select[name="field[]"]').val();
        const order_field = $('select[name=order_field]').val();
        const order_by = $('select[name="order_by"]').val();
        thead.html("").append("<tr>" + th + "</tr>");
        initDatatable(selectedFields, fields, order_field, order_by, criteria);
    }
}

function loadTemplate() {
    if (template) { // check if not null
        if (template.length) {
            selectedFields = [];
            $(".m-widget4").html("");
            const selections = template.map((item) => {
                selectedFields.push({ id: item.fieldname, text: item.header });
                const strId = item.header.replace(' ', '-');

                const el = '' +
                    '<div class="m-widget4__item" id="' + strId + '">' +
                    '    <div class="m-widget4__info pl-0">' +
                    '           <span class="m-widget4__text">' +
                    '           ' + (item.header).toUpperCase() +
                    '           </span>' +
                    '    </div>' +
                    '    <div class="m-widget4__ext">' +
                    '           <a href="#" onclick="removeField(\'' + strId + '\',\'' + item.fieldname + '\')" ' +
                    '              class="m-widget4__icon">' +
                    '                   <i class="la la-remove"></i>' +
                    '           </a>' +
                    '    </div>' +
                    '</div>';
                $(".empty-field-selection").html("");
                $(".m-widget4").append(el);

                return item.fieldname;
            });

            $('#field').val(selections).change();
        }
    }
}
function exportAs(type) {
    dropdown.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
    let clearHere = false;

    setTimeout(() => {
        switch (type) {
            case "excel":
                dtReport.button(".buttons-excel").trigger();
                break;
            case "pdf":
                dtReport.button(".buttons-pdf").trigger();
                break;
            case "print":
                dtReport.button(".buttons-print").trigger();
                break;
        }

        if (clearHere) {
            dropdown.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
        }
    }, 150);
}

$('.m-content')
    .on('submit', '#frm-create-field-template', function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');

        const select = form.find('select[name="field[]"]');
        let templateItems = [];
        const selected = select.find('option');
        for (let i = 0; i < selected.length; i++) {
            selected[i].selected && templateItems.push({ header: selected[i].text, fieldname: selected[i].value });
        }

        formData.append('templateItems', JSON.stringify(templateItems));

        if (form.isValid()) {
            $.ajax({
                url,
                type: 'POST',
                dataType: 'JSON',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, 'Template Created.', 10000);
                    } else {
                        toastr.error(response.message, 'Error.', 10000);
                    }

                    modalContainer.modal('hide');
                }
            });
        }
    })
    .on('submit', '#frm-edit-field-template', function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');

        const select = form.find('select[name="field[]"]');
        let templateItems = [];
        const selected = select.find('option');
        for (let i = 0; i < selected.length; i++) {
            selected[i].selected && templateItems.push({ header: selected[i].text, fieldname: selected[i].value });
        }

        formData.append('templateItems', JSON.stringify(templateItems));

        if (form.isValid()) {
            $.ajax({
                url,
                type: 'POST',
                dataType: 'JSON',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        const data = response.data;
                        template = data.template_body;

                        $('#template')
                            .empty()
                            .select2(template_select_option);

                        // Set the value, creating a new option if necessary
                        if ($('#template').find("option[value='" + data.id + "']").length) {
                            $('#template').val(data.id).trigger('change');
                        } else {
                            // Create a DOM Option and pre-select by default
                            var newOption = new Option(data.text, data.id, true, true);
                            // Append it to the select
                            $('#template').append(newOption).trigger('change');
                        }

                        toastr.success(response.message, 'Template Updated..', 10000);
                    } else {
                        toastr.error(response.message, 'Error.', 10000);
                    }

                    modalContainer.modal('hide');
                }
            });
        }
    });

async function getExportData(e, dt, node, config, self, url, type) {
    const data = dt.ajax.params();
    const result = await $.ajax({
        url,
        type: "POST",
        dataType: "JSON",
        data,
        success: function (response) {
            dt.rows().remove();
            dt.rows.add(response.data).draw();
            $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
        }
    });

    return result;
}

function logexport(type){
    $.ajax({
        url: baseUrl("crs/log_export/"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
            csrf_token : _csrf_hash,
            type : type,
        },
    });
}