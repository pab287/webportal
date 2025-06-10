let tblQuestions = null;
let search_val = '';
let isArchived = 0;
tblQuestions = $('#employement_questions').DataTable({
    rowId: 'id',
    dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 newForm'><'col-xl-6 col-lg-6 col-md-6 col-sm-12 p-0 exportSearch'f>>" +
    "<'row'<'col-12'rt>>" +
    "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'li><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
    serverSide: true,
    processing: true,
    searching: false,
    ajax: {
        url: baseUrl("hris/masterfile/get_employee_questions"),
        type: "post",
        dataType: "json",
        data: function(d) {
            d.csrf_token = _csrf_hash;
            d.is_archived = isArchived;
            d.search['value'] = search_val;
        },
    },
    columns: [
        { data: "id", name: "id", visible: false },
        { data: "question", name: "question", width: "90%", title: "question" },
        { data: null, orderable: false, title: "Actions" , render: function(data, type, row) {
            if(isArchived == 1){
                return `<button class="btn btn-focus btn-sm" onclick="onboardingRestore(${row.id})"><i class="la la-mail-reply"></i></button>`;
            }
            return `<button class="btn btn-primary btn-sm" onclick="questionEdit(${row.id})"><i class="la la-edit"></i></button>
                    <button class="btn btn-danger btn-sm" onclick="onboardingItemArchive(${row.id})"><i class="la la-trash"></i></button>`;
        } },
    ],
    initComplete: function () {
        const newForm = `<form id="new_question">
        <div class="form-group m-form__group col-lg-12 col-md-12 col-sm-12">
            <div class="input-group align-items-center">
                <input type="text" class="form-control m-input" name="question" placeholder="Add new question" aria-describedby="basic-addon2" data-validation="required">
                <button class="btn btn-sm btnSave p-0 ml-2 border-0" type="submit" id="newQuestion"><span class="input-group-text">Add New</span></button>
            </div>
        </div>
        </form>`;
        const update_Form = `<form id="update_item" hidden>
        <div class="form-group m-form__group col-lg-12 col-md-12 col-sm-12">
            <div class="input-group align-items-center">
                <input type="text" class="form-control m-input" name="question" aria-describedby="basic-addon2" id="update_question_input" data-validation="required">
                <span id="closeButton" style="position: absolute; right: 130px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 5;">X</span>
                <button class="btn btn-sm btnUpdate p-0 ml-2 border-0" type="submit" id="updateQuestion"><span class="input-group-text">Update Item</span></button>
            </div>
        </div>
        </form>`;        
        
        $(update_Form).appendTo("#employement_questions_wrapper .newForm");
        $(newForm).appendTo("#employement_questions_wrapper .newForm");

        const filterDiv = $('<div>').addClass('dataTables_filter');
        const searchInput = $('<input>').attr('type', 'text').addClass('form-control').attr('placeholder', 'Search...').attr('id', 'generalSearch');
        filterDiv.append(searchInput);
        $(filterDiv).appendTo("#employement_questions_wrapper .exportSearch");
        $('#generalSearch').donetyping(function(callback) {
            search_val = $(this).val();
            tblQuestions.ajax.reload();
        },1000);
        submitForm();
        updateForm();
    },
});

function submitForm() {
    $.validate({
        form : '#new_question',
        lang: 'en',
        onSuccess : function(form) {
            let formData = $(form).serializeArray();
            formData.push({name: 'csrf_token', value: $("#csrf_token").val()});
            $.ajax({
                url: baseUrl("hris/masterfile/add_new_question"),
                type: "post",
                dataType: "json",
                data: formData,
                success: function(response) {
                    if(response.status){
                        $('#new_question')[0].reset();
                        toastr.success(response.message);
                        tblQuestions.ajax.reload(null, false);
                    }
                    else{
                        toastr.error(response.message);
                    }
    
                },
            });
            return false;
        }
    });
}


function questionEdit(id){
    let rowData = tblQuestions.row('#'+id).data();
    itemId = id;
    itemData = rowData.question;
    $('#new_question')[0].reset();
    $('#update_question_input').val(rowData.question);
    $('#update_item').removeAttr('hidden');
    $('#new_question').hide();
    $('#update_question_input').focus();


    $('#closeButton').click(function() {
        console.log('Close button clicked');
        $('#update_item')[0].reset();
        $('#new_question').show();
        $('#update_item').attr('hidden', '');    
    });
}

function onboardingItemArchive(id){
    Swal.fire({
        position: 'top',
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                url: baseUrl("hris/masterfile/archive_question"),
                type: "post",
                dataType: "json",
                data: {id:id, csrf_token:_csrf_hash},
                success: function(response) {
                    if(response.status){
                        toastr.success(response.message);
                        tblQuestions.ajax.reload(null, false);
                    }
                    else{
                        toastr.error(response.message);
                    }
                },
            });
        }
    });
}

function onboardingRestore(id){
    Swal.fire({
        position: 'top',
        title: "Are you sure?",
        // text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
        cancelButtonText: "No, cancel!",
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                url: baseUrl("hris/masterfile/restore_question"),
                type: "post",
                dataType: "json",
                data: {id:id, csrf_token:_csrf_hash},
                success: function(response) {
                    if(response.status){
                        toastr.success(response.message);
                        tblQuestions.ajax.reload(null, false);
                    }
                    else{
                        toastr.error(response.message);
                    }
                },
            });
        }
    });
}

function updateForm(){
    $.validate({
        form : '#update_item',
        lang: 'en',
        onSuccess : function(form) {
            let formData = $(form).serializeArray();
            formData.push({name: 'csrf_token', value: $("#csrf_token").val()});
            formData.push({name: 'id', value: itemId});
            if (itemData == $('#update_question_input').val()){
                $('#update_item')[0].reset();
                toastr.error("Data input is the same as before.");
                $('#new_question').show();
                $('#update_item').attr('hidden', '');
                return false;
            }
            $.ajax({
                url: baseUrl("hris/masterfile/update_question"),
                type: "post",
                dataType: "json",
                data: formData,
                success: function(response) {
                    if(response.status){
                        $('#update_item')[0].reset();
                        toastr.success(response.message);
                        $('#new_question').show();
                        $('#update_item').attr('hidden', '');
                        tblQuestions.ajax.reload(null, false);
                    }
                    else{
                        toastr.error(response.message);
                    }
    
                },
            });
            return false;
        }
    });
}

function getArchivedItems() {
    isArchived = 1 - isArchived;
    const isActive = isArchived === 0;
    
    $('#archived_items .m-nav__link-text').text(isActive ? 'Show Archived Items' : 'Show Active Questions');
    $('#itemHead').text(isActive ? 'List of Active Items' : 'List of Archived Questions');
    $('#new_question').toggle(isActive);
    $('#update_item:visible').attr('hidden', '');
    tblQuestions.ajax.reload();
}