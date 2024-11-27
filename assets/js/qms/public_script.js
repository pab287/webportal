var modalWindow = $("#modalTempContent");
var policyTbl = $("#table-policy");
var search_val = '';
var company = '';
var department = '';
var author = '';
var category = '';
var title = '';

var pdfDoc = null, pageNum = 1;
var scale = 0.7; //Set Scale for zooming PDF.
var resolution = 1; //Set Resolution to Adjust PDF clarity.

var pdfjsLib = window['pdfjs-dist/build/pdf'];
// pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';
pdfjsLib.GlobalWorkerOptions.workerSrc = baseUrl('assets/plugins/pdf/pdf.worker.min.js');

let _category = [];
let _emp = [];
let _comp = [];
let _dept = [];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.category !== "undefined" && _tempContentData.category){ _category = _tempContentData.category; }
    if(typeof _tempContentData.employees !== "undefined" && _tempContentData.employees){ _emp = _tempContentData.employees; }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company){ _comp = _tempContentData.company; }
    if(typeof _tempContentData.department !== "undefined" && _tempContentData.department){ _dept = _tempContentData.department; }
}

if(typeof policyTbl != 'undefined'){
    var table = policyTbl.DataTable({
        searching: false,
        ordering: false,
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("qms/qms/get_policy_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val,
                    d.company = company,
                    d.author = author,
                    d.category = category,
                    d.department = department,
                    d.title = title
            }
        },
        columns: [
            { data: "added_dt" },
            { data: 'filename', width: '15%' },
            { data: "title" },
        ],
        columnDefs: [
            {
                targets: 0,
                visible: false
            },
            {
                targets: 1,
                render: function(data, type, row, meta){
                    var html = '';
                    var url = baseUrl('uploads/files/qms/document/'+ row.id +'/'+data);

                    html += '<div id="pdf_container_'+row.id+'"></div>';

                    if(row.file_exist){
                        previewPDF(row.id, url);
                    }else{
                        var no_file = baseUrl('assets/images/qms/no-document.png');

                        setTimeout(() => {
                            $("#pdf_container_"+row.id).html('<img src="'+no_file+'" width="55%">').append('<p style="font-weight: 400">No file found.</p>');
                        }, 500);
                    }

                    return html;
                }
            },
            {
                targets: 2,
                defaultContent: "",
                render: function(data, type, row, meta){
                    var html = '';

                    html += "<h4><b>"+ row.title +"</b></h4>";
                    html += "<p style='margin: 0'>"+ row.objective +"</p>";
                    html += "<p style='margin: 0'>"+ row.scope +"</p>";

                    if(row.author){
                        html += '<p><small><b>Prepared By:</b> '+ row.author +'</small></p>';
                    }
                    

                    return html;
                }
            }
        ],
        rowId: '1',
        drawCallback: function(settings){
            if($('td').hasClass('dataTables_empty')){
                $("#table-policy.is-grid tbody tr").css('flex', '0 0 100%').css('max-width', '100%');
            }
        }
    });

    table.on('click', 'tbody tr', function(){
        let data = table.row(this).data();

        $.ajax({
            url: baseUrl("qms/qms/get_policy_modal_content/view"), 
            dataType: "json",
            type: "POST",
            data: {csrf_token: _csrf_hash, id: data.id},
            success: function(json){
                if (json.response) {
                    if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                        var tempModalContent = modalWindow.find("#modalTempContainer");
                        tempModalContent.empty().html(json.html);
                        modalWindow.modal("show");

                        var data = json.data;

                        var url = baseUrl('uploads/files/qms/document/'+ data.id +'/'+data.filename);

                        if(data.file_exist){
                            previewPDF(data.id, url, true);

                            tempModalContent.find("#is_file_exist").removeClass('align-items-center');
                        }else{
                            var no_file = baseUrl('assets/images/qms/no-document.png');
                            tempModalContent.find("#view_pdf_container_"+data.id).css('text-align', 'center').html('<img src="'+no_file+'" width="40%">').append('<p style="font-weight: 400">No file found.</p>');

                            tempModalContent.find("#is_file_exist").addClass('align-items-center');
                        }

                        tempModalContent.find('#notif-user').click( function(){
                            Swal.fire({
                                title: 'Older version of `<b>' + data.title.toUpperCase() + '</b>` Policy!',
                                html: "Please Contact QMS Department for more information.",
                                icon: 'error',
                                showCancelButton: true,
                                cancelButtonText: "Cancel",
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                showConfirmButton: false,
                              });
                        });
                    }
                }
            }
        })
    });

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        table.ajax.reload();
    });
}

$('#policy-category').select2({
    width: '100%',
    placeholder:  { id: "-1", text: "Select an Option" },
    data: _category
}).on("select2:select", function (e) {
    var data = e.params.data;
    category = data.id;
});

$('#policy-company').select2({
    width: '100%',
    placeholder:  { id: "-1", text: "Select an Option" },
    data: _comp,
}).on("select2:select", function (e) {
    var data = e.params.data;
    company = data.id;
});

$('#policy-department').select2({
    width: '100%',
    placeholder:  { id: "-1", text: "Select an Option" },
    data: _dept,
}).on("select2:select", function (e) {
    var data = e.params.data;
    department = data.id;
});

$('#policy-author').select2({
    width: '100%',
    placeholder:  { id: "-1", text: "Select an Option" },
    data: _emp,
}).on("select2:select", function (e) {
    var data = e.params.data;
    author = data.id;
});

function previewPDF(id, url, isModal = false){
    pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
        pdfDoc = pdfDoc_;
        
        //Reference the Container DIV.
        var pdf_container;
        if(isModal){
            pdf_container = document.getElementById("view_pdf_container_" + id);
            pdf_container.style.display = "block";
        }else{
            pdf_container = document.getElementById("pdf_container_" + id);
            pdf_container.style.display = "block";
        }

        //Loop and render all pages.
        // for (var i = 1; i <= pdfDoc.numPages; i++) {
        //     RenderPage(pdf_container, i);
        // }
        RenderPage(pdf_container, pageNum, isModal);

    });
}

function RenderPage(pdf_container, num, isModal = false) {
    pdfDoc.getPage(num).then(function (page) {
        //Create Canvas element and append to the Container DIV.
        var canvas = document.createElement('canvas');
        canvas.id = 'pdf-' + num;
        ctx = canvas.getContext('2d');
        pdf_container.append(canvas);
        
        //Create and add empty DIV to add SPACE between pages.
        // var spacer = document.createElement("div");
        // spacer.style.height = "20px";
        // pdf_container.appendChild(spacer);

        //Set the Canvas dimensions using ViewPort and Scale.
        var viewport = page.getViewport({ scale: scale });

        if(isModal){
            if(viewport.height >= 500){
                $("#modalTempContent #pdf-"+num).css('max-width', '250px');
            }

            if(viewport.width >= 500){
                $("#modalTempContent #pdf-"+num).css('max-width', '450px');
            }

            canvas.height = resolution * viewport.height;
            canvas.width = resolution * viewport.width;  
        }else{
            canvas.height = resolution * viewport.height;
            canvas.width = resolution * viewport.width;
        }

        //Render the PDF page.
        var renderContext = {
            canvasContext: ctx,
            viewport: viewport,
            transform: [resolution, 0, 0, resolution, 0, 0]
        };

        page.render(renderContext);
    });
};

function submit_filter(){
    var _company = $("#policy-company").val();
    var _department = $("#policy-department").val();
    var _author = $("#policy-author").val();
    var _category = $("#policy-category").val();
    var _title = $("#policy-title").val();

    company = _company != 'Select an Option' ? _company : "";
    department = _department != 'Select an Option' ? _department : "";
    author = _author != 'Select an Option' ? _author : "";
    category = _category != 'Select an Option' ? _category : "";
    title = _title != 'Select an Option' ? _title : "";

    if(company || department || author || category || title){
        table.ajax.reload();
    }
}

function clear_filter(){
    if(company || author || category || title || department){
        company = "";
        author = "";
        category = "";
        title = "";
        department = "";

        $("#policy-company").val('').trigger('change');
        $("#policy-department").val('').trigger('change');
        $("#policy-author").val('').trigger('change');
        $("#policy-category").val('').trigger('change');
        $("#policy-title").val('');
    
        table.ajax.reload();
    }
}

$(".btnContainer").find('button').click( function(){
    var type = $(this).attr('id');

    if(type === 'list'){
        $("#table-policy")
            .removeClass('is-grid')
            .addClass('table')
            .addClass('table-striped')
            .addClass('table-bordered');

            $('#list').addClass('btn-primary').addClass('active');
            $('#grid').removeClass('btn-primary').removeClass('active');
    }else{
        $("#table-policy")
            .addClass('is-grid')
            .removeClass('table')
            .removeClass('table-striped')
            .removeClass('table-bordered');

            $('#list').removeClass('btn-primary').removeClass('active');
            $('#grid').addClass('btn-primary').addClass('active');
    }
});

function downloadPDF(id, filename, type){
    $.ajax({
        url: baseUrl('qms/qms/count_download/'),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            id : id,
            type: type,
            filename: filename,
        },
        beforeSend: function () {

            if(type == 'download'){
                $("#modalTempContent")
                    .find("#pdfDownload")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }else{
                $("#modalTempContent")
                    .find("#pdfPrint")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        },
        success: function(response){

            if(response.state){

                if(type == 'download'){
                    toastr.info('PDF Downloaded!', "Document", 5000);
    
                    var url = baseUrl('uploads/files/qms/document/'+ id +'/'+ filename);
                    const a = document.createElement('a')
                    a.href = url
                    a.download = url.split('/').pop()
                    document.body.appendChild(a)
                    a.click()
                    document.body.removeChild(a);

                    $("#modalTempContent").find("#pdf_download").text(response.total_download);
                }else{
                    toastr.info('Document Printed!', "Document", 5000);

                    var url = baseUrl('qms/print_document/' + id);
                    window.open(url, '_blank');

                    $("#modalTempContent").find("#pdf_print").text(response.total_download);
                }
            }else{
                toastr.error(response.toastr_msg, "Document", 5000);
            }

            if(type == 'download'){
                $("#modalTempContent")
                    .find("#pdfDownload")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }else{
                $("#modalTempContent")
                    .find("#pdfPrint")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        }
    });
}