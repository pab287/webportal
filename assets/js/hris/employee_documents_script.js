const data = _tempContentData || null;
const id = data.data.id || 0;
const documentContainer = $("#employee-documents-wrapper .m-widget2 .row");
const trainingsContainer = $("#trainings .row");
const medicalRecordsContainer = $("#medical-records .row");
const offensesContainer = $("#offenses-commendations .row");
const performanceEvalContainer = $("#performance-evaluation .row");
const bgcheckContainer = $("#background-check .row");

loadDocuments();

function loadDocuments() {
    $.ajax({
        url: baseUrl("hris/masterfile/get_employee_documents_for_tab/" + id),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            searchKey: $('#search-files').val()
        },
        dataType: "JSON",
        success: function (response) {
            const documents = response.documents;
            const trainings = response.trainings;
            const medical_records = response.medical;
            const offenses = response.offenses;
            const performance = response.performance;
            const bgcheck = response.bgcheck;

            const allowedFileTypes = [
                {
                    _type: ["jpg", "jpeg", "png", "PNG", "JPEG", "JPG"],
                    icon: "jpg.svg",
                    color: "success"
                },
                {
                    _type: ["docx", "DOCX"],
                    icon: "doc.svg",
                    color: "info"
                },
                {
                    _type: ["pdf", "PDF"],
                    icon: "pdf.svg",
                    color: "danger"
                },
                {
                    _type: ["none", "NONE"],
                    icon: "default.svg",
                    color: "metal"
                },
            ];

            if (documents.length) {
                $('#alert-no-document-yet').html('');
                $('#documents-total-badge').html(documents.length + ' Files');
            } else {
                $('#documents-total-badge').html(0);
                $('#alert-no-document-yet').html('<h6 class="mt-2 text-muted"' +
                    'style="padding-left: 48px;">' +
                    'No documents to show.' +
                    '</h6>');
            }

            if (bgcheck.length) {
                $('#alert-no-bgcheck-yet').html('');
                $('#background-check-total-badge').html(bgcheck.length + ' Files');
            } else {
                $('#background-check-total-badge').html(0);
                $('#alert-no-bgcheck-yet').html('<h6 class="mt-2 text-muted"' +
                    'style="padding-left: 48px;">' +
                    'No documents to show.' +
                    '</h6>');
            }

            if (trainings.length) {
                $('#alert-no-trainings-yet').html('');
                $('#trainings-total-badge').html(trainings.length + ' Files');
            } else {
                $('#trainings-total-badge').html(0);
                $('#alert-no-trainings-yet').html('<h6 class="mt-2 text-muted"' +
                    'style="padding-left: 48px;">' +
                    'No trainings & seminars to show.' +
                    '</h6>');
            }

            if (medical_records.length) {
                $('#alert-no-medical-records-yet').html('');
                $('#medical-total-badge').html(medical_records.length + ' Files');
            } else {
                $('#medical-total-badge').html(0);
                $('#alert-no-medical-records-yet').html('<h6 class="mt-2 text-muted"' +
                    'style="padding-left: 48px;">' +
                    'No medical records to show.' +
                    '</h6>');
            }

            if (offenses.length) {
                $('#alert-no-offenses-commendations-yet').html('');
                $('#offenses-total-badge').html(offenses.length + ' Files');
            } else {
                $('#offenses-total-badge').html(0);
                $('#alert-no-offenses-commendations-yet').html('<h6 class="mt-2 text-muted"' +
                    'style="padding-left: 48px;">' +
                    'No offenses and commendations to show.' +
                    '</h6>');
            }

            if (performance.length) {
                $('#alert-no-performance-evaluation-yet').html('');
                $('#performance-total-badge').html(performance.length + ' Files');
            } else {
                $('#performance-total-badge').html(0);
                $('#alert-no-performance-evaluation-yet').html('<h6 class="mt-2 text-muted"' +
                    'style="padding-left: 48px;">' +
                    'No performance evaluation to show.' +
                    '</h6>');
            }

            /*alert-no-trainings-yet
            alert-no-medical-records-yet
            alert-no-offenses-commendations-yet*/

            documentContainer.html('');
            documents.forEach((document) => {
                let icon = '';
                let color = '';
                const doc_type = document.doc_type ? document.doc_type : "N/A";
                const date_uploaded = document.date_uploaded ? document.date_uploaded : "No specified date.";
                let ext = document.doc_filename ? document.doc_filename.split(".") : "";
                ext = ext[ext.length - 1];
                if(document.exists == false){ ext = 'none'; }
                allowedFileTypes.forEach((item, i) => {
                    if (item._type.includes(ext)) {
                        icon = item.icon;
                        color = item.color;
                    }
                });

                const icon_path = baseUrl('assets/images/file_icons/' + icon);
                // const filepath = baseUrl(document.filepath);

                let filepath = document.filepath;

                let previewButton = '';
                if (icon === 'pdf.svg' || icon === 'doc.svg') {
                    previewButton = '' +
                        '    <button ' + (document.exists ? '' : 'disabled') +
                        '           title="Preview" data-title="Preview File"' +
                        '           onclick="previewDocument(\'' + filepath + '\', \'' + icon + '\', \'' + document.doc_filename + '\')"' +
                        '           class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (document.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '       <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'jpg.svg') {
                    previewButton = '' +
                        '    <a data-lightbox="roadtrip" class="lbox" data-title="' + document.doc_filename + '" href="' + filepath + '"></a>' +
                        '    <button title="Download" ' +
                        '            onclick="openLightBox(this)" ' + (document.exists ? '' : 'disabled') +
                        '            class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView mr-1 ' + (document.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '            <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'default.svg') {
                    previewButton = '';
                }

                const isDisabled = document.exists ? '': 'disabled';
                const hasClass = document.exists ? 'm-btn--hover-primary' : '';

                let currentAction = `<button ${isDisabled} 
                    title="Download" data-original-title="Download" 
                    onclick="downloadDocument(\'${filepath}\')"
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ${hasClass}">
                    <i class="la la-download"></i>
                </button>`;

                if(document.exists == false){
                    currentAction = `<a href='javascript:void(0);' title='File Not Found!' data-original-title='File Not Found!'
                    class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-danger btnNotFound'>
                    <i class='la la-exclamation'></i></a>`;
                }

                if(document.to_replace == true && document.exists == false){
                    const { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath } = document;
                    const toReplace = Object.assign( { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath });

                    const docStringify = JSON.stringify(toReplace);
                    currentAction = `<button 
                        title="Download File And Re-upload" data-original-title="Download File And Re-upload" data-raw='${docStringify}'
                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-warning btnDownloadFile">
                        <i class="la la-download"></i>
                    </button>`;
                }

                const rowTemplate = '' +
                    '            <div class="col-xl-6">' +
                    '                <div class="m-widget2__item m-widget2__item--' + color + '">' +
                    '                    <div class="m-widget2__checkbox">' +
                    '                        <div class="m-widget2__img m-widget2__img--icon">' +
                    '                            <img src="' + icon_path + '" alt="" width="39" height="39">' +
                    '                        </div>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__desc">' +
                    '                       <span class="m-widget2__text">' +
                    '                           ' + doc_type + 
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                              ' + document.doc_filename +
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                           <span class="m-widget2__link">' +
                    '                              ' + date_uploaded + 
                    '                           </span>' +
                    '                       </span>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__actions">' +
                    '                        ' + previewButton + currentAction +
                    '                    </div>' +
                    '                </div>' +
                    '            </div>';

                documentContainer.append(rowTemplate);
            });

            bgcheckContainer.html('');
            bgcheck.forEach((document) => {
                let icon = '';
                let color = '';
                const doc_type = document.doc_type ? document.doc_type : "N/A";
                const date_uploaded = document.date_uploaded ? document.date_uploaded : "No specified date.";
                let ext = document.doc_filename ? document.doc_filename.split(".") : "";
                ext = ext[ext.length - 1];
                allowedFileTypes.forEach((item, i) => {
                    if (item._type.includes(ext)) {
                        icon = item.icon;
                        color = item.color;
                    }
                });
                const icon_path = baseUrl('assets/images/file_icons/' + icon);
                const filepath = document.filepath;

                let previewButton = '';
                if (icon === 'pdf.svg' || icon === 'doc.svg') {
                    previewButton = '' +
                        '    <button ' + (document.exists ? '' : 'disabled') +
                        '           title="Preview"' +
                        '           onclick="previewDocument(\'' + filepath + '\', \'' + icon + '\', \'' + document.doc_filename + '\')"' +
                        '           class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (document.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '       <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'jpg.svg') {
                    previewButton = '' +
                        '    <a data-lightbox="roadtrip" class="lbox" data-title="' + document.doc_filename + '" href="' + filepath + '"></a>' +
                        '    <button title="Download"' +
                        '            onclick="openLightBox(this)" ' + (document.exists ? '' : 'disabled') +
                        '            data-toggle="m-tooltip" ' +
                        '            data-original-title="asdasd"' +
                        '            class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (document.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '            <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'default.svg') {
                    previewButton = '';
                }

                const isDisabled = document.exists ? '': 'disabled';
                const hasClass = document.exists ? 'm-btn--hover-primary' : '';

                let currentAction = `<button ${isDisabled} 
                    title="Download" data-original-title="Download" 
                    onclick="downloadDocument(\'${filepath}\')"
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ${hasClass}">
                    <i class="la la-download"></i>
                </button>`;

                if(document.exists == false){
                    currentAction = `<a href='javascript:void(0);' title='File Not Found!' data-original-title='File Not Found!'
                    class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-danger btnNotFound'>
                    <i class='la la-exclamation'></i></a>`;
                }

                if(document.to_replace == true && document.exists == false){
                    const { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath } = document;
                    const toReplace = Object.assign( { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath });

                    const docStringify = JSON.stringify(toReplace);
                    currentAction = `<button 
                        title="Download File And Re-upload" data-original-title="Download File And Re-upload" data-raw='${docStringify}'
                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-warning btnDownloadFile">
                        <i class="la la-download"></i>
                    </button>`;
                }

                const rowTemplate = '' +
                    '            <div class="col-xl-6">' +
                    '                <div class="m-widget2__item m-widget2__item--' + color + '">' +
                    '                    <div class="m-widget2__checkbox">' +
                    '                        <div class="m-widget2__img m-widget2__img--icon">' +
                    '                            <img src="' + icon_path + '" alt="">' +
                    '                        </div>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__desc">' +
                    '                       <span class="m-widget2__user-name">' +
                    '                              ' + document.doc_filename +
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                           <span class="m-widget2__link">' +
                    '                              ' + date_uploaded +
                    '                           </span>' +
                    '                       </span>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__actions">' +
                    '                        ' + previewButton + currentAction
                    '                    </div>' +
                    '                </div>' +
                    '            </div>';

                bgcheckContainer.append(rowTemplate);
            });

            trainingsContainer.html('');
            trainings.forEach((training) => {
                let icon = '';
                let color = '';
                const doc_type = training.doc_type ? training.doc_type : "N/A";
                const date_uploaded = training.date_uploaded ? training.date_uploaded : "No specified date.";
                let ext = training.doc_filename ? training.doc_filename.split(".") : "";
                ext = ext[ext.length - 1];
                allowedFileTypes.forEach((item, i) => {
                    if (item._type.includes(ext)) {
                        icon = item.icon;
                        color = item.color;
                    }
                });
                const icon_path = baseUrl('assets/images/file_icons/' + icon);
                // const filepath = baseUrl(training.filepath);

                const filepath = training.filepath;

                let previewButton = '';
                if (icon === 'pdf.svg' || icon === 'doc.svg') {
                    previewButton = '' +
                        '    <button ' + (training.exists ? '' : 'disabled') +
                        '           title="' + (training.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '           data-original-title="' + (training.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '           onclick="previewDocument(\'' + filepath + '\', \'' + icon + '\', \'' + training.doc_filename + '\')"' +
                        '           class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (training.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '       <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'jpg.svg') {
                    previewButton = '' +
                        '    <a data-lightbox="roadtrip" class="lbox" data-title="' + training.doc_filename + '" href="' + filepath + '"></a>' +
                        '    <button onclick="openLightBox(this)" ' + (training.exists ? '' : 'disabled') +
                        '            title="' + (training.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '            data-original-title="' + (training.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '            class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (training.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '            <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'default.svg') {
                    previewButton = '';
                }

                const isDisabled = training.exists ? '': 'disabled';
                const hasClass = training.exists ? 'm-btn--hover-primary' : '';

                let currentAction = `<button ${isDisabled} 
                    title="Download" data-original-title="Download" 
                    onclick="downloadDocument(\'${filepath}\')"
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ${hasClass}">
                    <i class="la la-download"></i>
                </button>`;

                if(training.exists == false){
                    currentAction = `<a href='javascript:void(0);' title='File Not Found!' data-original-title='File Not Found!'
                    class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-danger btnNotFound'>
                    <i class='la la-exclamation'></i></a>`;
                }

                if(training.to_replace == true && training.exists == false){
                    const { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath } = training;
                    const toReplace = Object.assign( { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath });

                    const docStringify = JSON.stringify(toReplace);
                    currentAction = `<button 
                        title="Download File And Re-upload" data-original-title="Download File And Re-upload" data-raw='${docStringify}'
                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-warning btnDownloadFile">
                        <i class="la la-download"></i>
                    </button>`;
                }

                const rowTemplate = '' +
                    '            <div class="col-xl-6">' +
                    '                <div class="m-widget2__item m-widget2__item--' + color + '">' +
                    '                    <div class="m-widget2__checkbox">' +
                    '                        <div class="m-widget2__img m-widget2__img--icon">' +
                    '                            <img src="' + icon_path + '" alt="">' +
                    '                        </div>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__desc">' +
                    '                       <span class="m-widget2__user-name">' +
                    '                              ' + training.doc_filename +
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                           <span class="m-widget2__link">' +
                    '                              ' + date_uploaded +
                    '                           </span>' +
                    '                       </span>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__actions">' +
                    '                        ' + previewButton + currentAction +
                    '                    </div>' +
                    '                </div>' +
                    '            </div>';

                trainingsContainer.append(rowTemplate);
            });

            medicalRecordsContainer.html('');
            medical_records.forEach((medical) => {
                let icon = '';
                let color = '';
                const doc_type = medical.doc_type ? medical.doc_type : "N/A";
                const date_uploaded = medical.date_uploaded ? medical.date_uploaded : "No specified date.";
                let ext = medical.doc_filename ? medical.doc_filename.split(".") : "";
                ext = ext[ext.length - 1];
                allowedFileTypes.forEach((item, i) => {
                    if (item._type.includes(ext)) {
                        icon = item.icon;
                        color = item.color;
                    }
                });
                const icon_path = baseUrl('assets/images/file_icons/' + icon);
                const filepath = medical.filepath;

                let previewButton = '';
                if (icon === 'pdf.svg' || icon === 'doc.svg') {
                    previewButton = '' +
                        '    <button ' + (medical.exists ? '' : 'disabled') +
                        '           title="' + (medical.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '           data-original-title="' + (medical.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '           onclick="previewDocument(\'' + filepath + '\', \'' + icon + '\', \'' + medical.doc_filename + '\')"' +
                        '           class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (medical.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '       <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'jpg.svg') {
                    previewButton = '' +
                        '    <a data-lightbox="roadtrip" class="lbox" data-title="' + medical.doc_filename + '" href="' + filepath + '"></a>' +
                        '    <button onclick="openLightBox(this)" ' + (medical.exists ? '' : 'disabled') +
                        '            title="' + (medical.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '            data-original-title="' + (medical.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '            class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (medical.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '            <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'default.svg') {
                    previewButton = '';
                }

                const isDisabled = medical.exists ? '': 'disabled';
                const hasClass = medical.exists ? 'm-btn--hover-primary' : '';

                let currentAction = `<button ${isDisabled} 
                    title="Download" data-original-title="Download" 
                    onclick="downloadDocument(\'${filepath}\')"
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ${hasClass}">
                    <i class="la la-download"></i>
                </button>`;

                if(medical.exists == false){
                    currentAction = `<a href='javascript:void(0);' title='File Not Found!' data-original-title='File Not Found!'
                    class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-danger btnNotFound'>
                    <i class='la la-exclamation'></i></a>`;
                }

                if(medical.to_replace == true && medical.exists == false){
                    const { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath } = medical;
                    const toReplace = Object.assign( { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath });

                    const docStringify = JSON.stringify(toReplace);
                    currentAction = `<button 
                        title="Download File And Re-upload" data-original-title="Download File And Re-upload" data-raw='${docStringify}'
                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-warning btnDownloadFile">
                        <i class="la la-download"></i>
                    </button>`;
                }

                const rowTemplate = '' +
                    '            <div class="col-xl-6">' +
                    '                <div class="m-widget2__item m-widget2__item--' + color + '">' +
                    '                    <div class="m-widget2__checkbox">' +
                    '                        <div class="m-widget2__img m-widget2__img--icon">' +
                    '                            <img src="' + icon_path + '" alt="">' +
                    '                        </div>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__desc">' +
                    '                       <span class="m-widget2__user-name">' +
                    '                              ' + medical.doc_filename +
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                           <span class="m-widget2__link">' +
                    '                              ' + date_uploaded +
                    '                           </span>' +
                    '                       </span>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__actions">' +
                    '                        ' + previewButton + currentAction +
                    '                    </div>' +
                    '                </div>' +
                    '            </div>';

                medicalRecordsContainer.append(rowTemplate);
            });

            offensesContainer.html('');
            offenses.forEach((offense) => {
                let icon = '';
                let color = '';
                const doc_type = offense.doc_type ? offense.doc_type : "N/A";
                const date_uploaded = offense.date_uploaded ? offense.date_uploaded : "No specified date.";
                let ext = offense.doc_filename ? offense.doc_filename.split(".") : "";
                ext = ext[ext.length - 1];
                allowedFileTypes.forEach((item, i) => {
                    if (item._type.includes(ext)) {
                        icon = item.icon;
                        color = item.color;
                    }
                });
                const icon_path = baseUrl('assets/images/file_icons/' + icon);
                const filepath = offense.filepath;

                let previewButton = '';
                if (icon === 'pdf.svg' || icon === 'doc.svg') {
                    previewButton = '' +
                        '    <button ' + (offense.exists ? '' : 'disabled') +
                        '           title="' + (offense.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '           data-original-title="' + (offense.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '           onclick="previewDocument(\'' + filepath + '\', \'' + icon + '\', \'' + offense.doc_filename + '\')"' +
                        '           class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (offense.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '       <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'jpg.svg') {
                    previewButton = '' +
                        '    <a data-lightbox="roadtrip" class="lbox" data-title="' + offense.doc_filename + '" href="' + filepath + '"></a>' +
                        '    <button onclick="openLightBox(this)" ' + (offense.exists ? '' : 'disabled') +
                        '            title="' + (offense.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '            data-original-title="' + (offense.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '            class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (offense.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '            <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'default.svg') {
                    previewButton = '';
                }

                const isDisabled = offense.exists ? '': 'disabled';
                const hasClass = offense.exists ? 'm-btn--hover-primary' : '';

                let currentAction = `<button ${isDisabled} 
                    title="Download" data-original-title="Download" 
                    onclick="downloadDocument(\'${filepath}\')"
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ${hasClass}">
                    <i class="la la-download"></i>
                </button>`;

                if(offense.exists == false){
                    currentAction = `<a href='javascript:void(0);' title='File Not Found!' data-original-title='File Not Found!'
                    class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-danger btnNotFound'>
                    <i class='la la-exclamation'></i></a>`;
                }

                if(offense.to_replace == true && offense.exists == false){
                    const { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath } = offense;
                    const toReplace = Object.assign( { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath });

                    const docStringify = JSON.stringify(toReplace);
                    currentAction = `<button 
                        title="Download File And Re-upload" data-original-title="Download File And Re-upload" data-raw='${docStringify}'
                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-warning btnDownloadFile">
                        <i class="la la-download"></i>
                    </button>`;
                }

                const rowTemplate = '' +
                    '            <div class="col-xl-6">' +
                    '                <div class="m-widget2__item m-widget2__item--' + color + '">' +
                    '                    <div class="m-widget2__checkbox">' +
                    '                        <div class="m-widget2__img m-widget2__img--icon">' +
                    '                            <img src="' + icon_path + '" alt="">' +
                    '                        </div>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__desc">' +
                    '                       <span class="m-widget2__text">' +
                    '                           ' + doc_type +
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                              ' + offense.doc_filename +
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                           <span class="m-widget2__link">' +
                    '                              ' + date_uploaded +
                    '                           </span>' +
                    '                       </span>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__actions">' +
                    '                        ' + previewButton + currentAction +
                    '                    </div>' +
                    '                </div>' +
                    '            </div>';

                offensesContainer.append(rowTemplate);
            });

            performanceEvalContainer.html('');
            performance.forEach((performance) => {
                let icon = '';
                let color = '';
                const doc_type = performance.doc_type ? performance.doc_type : "N/A";
                const date_uploaded = performance.date_uploaded ? performance.date_uploaded : "No specified date.";
                let ext = performance.doc_filename ? performance.doc_filename.split(".") : "";
                ext = ext[ext.length - 1];
                allowedFileTypes.forEach((item, i) => {
                    if (item._type.includes(ext)) {
                        icon = item.icon;
                        color = item.color;
                    }
                });
                const icon_path = baseUrl('assets/images/file_icons/' + icon);
                const filepath = performance.filepath;

                let previewButton = '';
                if (icon === 'pdf.svg' || icon === 'doc.svg') {
                    previewButton = '' +
                        '    <button ' + (performance.exists ? '' : 'disabled') +
                        '           title="' + (performance.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '           data-original-title="' + (performance.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '           onclick="previewDocument(\'' + filepath + '\', \'' + icon + '\', \'' + performance.doc_filename + '\')"' +
                        '           class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (performance.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '       <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'jpg.svg') {
                    previewButton = '' +
                        '    <a data-lightbox="roadtrip" class="lbox" data-title="' + performance.doc_filename + '" href="' + filepath + '"></a>' +
                        '    <button onclick="openLightBox(this)" ' + (performance.exists ? '' : 'disabled') +
                        '            title="' + (performance.exists ? 'Preview' : 'No file in directory.') + '" ' +
                        '            data-original-title="' + (performance.exists ? 'Preview' : 'No file in directory.') + '"' +
                        '            class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ' + (performance.exists ? 'm-btn--hover-primary' : '') + '">' +
                        '            <i class="la la-eye"></i>' +
                        '    </button>';
                } else if (icon === 'default.svg') {
                    previewButton = '';
                }

                const isDisabled = performance.exists ? '': 'disabled';
                const hasClass = performance.exists ? 'm-btn--hover-primary' : '';

                let currentAction = `<button ${isDisabled} 
                    title="Download" data-original-title="Download" 
                    onclick="downloadDocument(\'${filepath}\')"
                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView ${hasClass}">
                    <i class="la la-download"></i>
                </button>`;

                if(performance.exists == false){
                    currentAction = `<a href='javascript:void(0);' title='File Not Found!' data-original-title='File Not Found!'
                    class='btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-danger btnNotFound'>
                    <i class='la la-exclamation'></i></a>`;
                }

                if(performance.to_replace == true && performance.exists == false){
                    const { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath } = performance;
                    const toReplace = Object.assign( { id, emp_id, to_replace_filename, added_by_name, to_replace_filepath });

                    const docStringify = JSON.stringify(toReplace);
                    currentAction = `<button 
                        title="Download File And Re-upload" data-original-title="Download File And Re-upload" data-raw='${docStringify}'
                        class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView m-btn--hover-warning btnDownloadFile">
                        <i class="la la-download"></i>
                    </button>`;
                }

                const rowTemplate = '' +
                    '            <div class="col-xl-6">' +
                    '                <div class="m-widget2__item m-widget2__item--' + color + '">' +
                    '                    <div class="m-widget2__checkbox">' +
                    '                        <div class="m-widget2__img m-widget2__img--icon">' +
                    '                            <img src="' + icon_path + '" alt="">' +
                    '                        </div>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__desc">' +
                    '                       <span class="m-widget2__user-name">' +
                    '                              ' + performance.doc_filename +
                    '                       </span>' +
                    '                       <br>' +
                    '                       <span class="m-widget2__user-name">' +
                    '                           <span class="m-widget2__link">' +
                    '                              ' + date_uploaded +
                    '                           </span>' +
                    '                       </span>' +
                    '                    </div>' +
                    '                    <div class="m-widget2__actions">' +
                    '                        ' + previewButton + currentAction +
                    '                    </div>' +
                    '                </div>' +
                    '            </div>';

                performanceEvalContainer.append(rowTemplate);
            });
        }
    });
}

function openLightBox(el) {
    $(el).prev().trigger('click');
}

function previewDocument(url, icon, filename) {
    let src = url;
    if (icon === "doc.svg") {
        src = "https://docs.google.com/gview?url=" + url + "&embeded=true";
    }

    const modal = $("#preview-document-dialog");
    const modal_title = modal.find(".modal-title");
    const modal_body = modal.find(".modal-body");
    const preview = "<embed src='" + src + "' />";
    modal_title.html(filename);
    modal_body.html(preview);
    modal.modal("show");
}

function downloadDocument(url) {
    let a = document.createElement('a');
    a.href = url;
    a.download = url.split('/').pop();
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

const expandedButtons = $('button[aria-expanded="true"]');
expandedButtons.each(function (i, el) {
    $(el).css('transform', 'rotate(90deg)');
})

$('.accordion').on('hide.bs.collapse', function (e) {
    const el = $(e.target)
        .prev('.accordion-header')
        .find(".btn");
    el.css('transform', 'rotate(0deg)');
});
$('.accordion').on('show.bs.collapse', function (e) {
    const el = $(e.target)
        .prev('.accordion-header')
        .find(".btn");
    el.css('transform', 'rotate(90deg)');
});

$('#search-files')
    .donetyping(function () {
        loadDocuments();
    });

$('body, .modal-body')
    .tooltip({
        selector: '[title]',
        skin: "dark",
        delay: {
            show: 500
        },
        placement: "top"
    });

jQuery(document).on("click", ".btnNotFound", function(){
    Swal.fire({
        title: 'No Attachment File!',
        html: "<strong class='m--font-danger'>Attachment</strong> file not found!",
        icon: 'warning',
    });
});

jQuery(document).on("click", ".btnDownloadFile", function(){
    const rawData = $(this).data('raw');
    Swal.fire({
        title: 'Download File?',
        html: `Are you sure you want to download this file for re-upload?<br><br>File Name:<br><h6 class="m--font-danger">${rawData.to_replace_filename}</h6>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Download it!'
      }).then((result) => {
        if (result.isConfirmed) { downloadDocument(rawData.to_replace_filepath); }
    });
});