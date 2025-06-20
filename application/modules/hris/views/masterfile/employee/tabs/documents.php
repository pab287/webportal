<style>
    .m-widget2 .m-widget2__item .m-widget2__img {
        display: table-cell;
        vertical-align: middle;
    }

    .m-widget2 .m-widget2__item .m-widget2__img.m-widget2__img--logo img {
        width: 3.5rem;
        border-radius: 50%;
    }

    .m-widget2 .m-widget2__item .m-widget2__img.m-widget2__img--pic img {
        width: 4rem;
        border-radius: 50%;
    }

    .m-widget2 .m-widget2__item .m-widget2__img.m-widget2__img--icon img {
        width: 3rem;
    }

    .m-widget2__checkbox {
        padding-top: 0 !important;
        vertical-align: middle !important;
        padding-right: 8px !important;
    }

    .m-widget2__desc {
        width: 70% !important;
    }

    .m-widget2__actions {
        width: 100% !important;
        text-align: right !important;
    }

    #preview-document-dialog .modal-dialog {
        height: 80%;
    }

    #preview-document-dialog .modal-content {
        height: 100%;
    }

    #preview-document-dialog .modal-body {
        padding: 0;
    }

    #preview-document-dialog iframe, embed {
        border: 0;
        width: 100%;
        height: 100%;
    }

    .m-badge {
        padding: 1px 8px;
    }

    .btn[disabled] {
        pointer-events: none;
    }
</style>

<div class="m-content" id="employee-documents-wrapper">
    <div class="row">
        <div class="offset-xl-8 offset-lg-8 offset-md-8 offset-sm-12 col-xl-4 col-lg-4 col-md-4 col-sm-12">
            <div class="m-input-icon m-input-icon--left mt-2">
                <input type="text" class="form-control" autocomplete="off"
                       placeholder="Looking for some files?" id="search-files">
                <span class="m-input-icon__icon m-input-icon__icon--left">
                    <span>
                        <i class="la la-search"></i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="accordion">
        <div class="m-widget2">
            <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill"
                        data-toggle="collapse"
                        data-target="#collapsibleDocuments"
                        aria-expanded="true" aria-controls="collapsibleDocuments">
                    <i class="more-less fa fa-chevron-right"></i>
                </button>
                <h5 class="ml-3 mb-0">Documents</h5>
                <span class="pl-2 m-menu__link-badge">
                    <span class="m-badge m-badge--success" id="documents-total-badge"></span>
                </span>
            </div>
            <div id="collapsibleDocuments" class="collapse show">
                <div id="alert-no-document-yet">
                    <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                </div>
                <div class="row"></div>
            </div>
        </div>

        <hr/>

        <div class="m-widget2 mt-4" id="background-check">
            <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill"
                        data-toggle="collapse"
                        data-target="#collapsibleBackgroundCheck"
                        aria-expanded="false" aria-controls="collapsibleBackgroundCheck">
                    <i class="more-less fa fa-chevron-right"></i>
                </button>
                <h5 class="ml-3 mb-0">Background Check</h5>
                <span class="pl-2 m-menu__link-badge">
                    <span class="m-badge m-badge--success" id="background-check-total-badge"></span>
                </span>
            </div>
            <div id="collapsibleBackgroundCheck" class="collapse">
                <div id="alert-no-bgcheck-yet">
                    <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                </div>
                <div class="row"></div>
            </div>
        </div>

        <hr/>

        <div class="m-widget2 mt-4" id="trainings">
            <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill"
                        data-toggle="collapse"
                        data-target="#collapsibleTrainings"
                        aria-expanded="false" aria-controls="collapsibleTrainings">
                    <i class="more-less fa fa-chevron-right"></i>
                </button>
                <h5 class="ml-3 mb-0">Trainings & Seminars</h5>
                <span class="pl-2 m-menu__link-badge">
                    <span class="m-badge m-badge--success" id="trainings-total-badge"></span>
                </span>
            </div>
            <div id="collapsibleTrainings" class="collapse">
                <div id="alert-no-trainings-yet">
                    <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                </div>
                <div class="row"></div>
            </div>
        </div>

        <hr/>

        <div class="m-widget2 mt-4" id="medical-records">
            <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill"
                        data-toggle="collapse"
                        data-target="#collapsibleMedicalRecords"
                        aria-expanded="false" aria-controls="collapsibleMedicalRecords">
                    <i class="more-less fa fa-chevron-right"></i>
                </button>
                <h5 class="ml-3 mb-0">Medical Records</h5>
                <span class="pl-2 m-menu__link-badge">
                    <span class="m-badge m-badge--success" id="medical-total-badge"></span>
                </span>
            </div>
            <div id="collapsibleMedicalRecords" class="collapse">
                <div id="alert-no-medical-records-yet">
                    <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                </div>
                <div class="row"></div>
            </div>
        </div>

        <hr/>

        <div class="m-widget2 mt-4" id="offenses-commendations">
            <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill"
                        data-toggle="collapse"
                        data-target="#collapsibleOffenses"
                        aria-expanded="false" aria-controls="collapsibleOffenses">
                    <i class="more-less fa fa-chevron-right"></i>
                </button>
                <h5 class="ml-3 mb-0">Offenses & Commendations</h5>
                <span class="pl-2 m-menu__link-badge">
                    <span class="m-badge m-badge--success" id="offenses-total-badge"></span>
                </span>
            </div>
            <div id="collapsibleOffenses" class="collapse">
                <div id="alert-no-offenses-commendations-yet">
                    <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                </div>
                <div class="row"></div>
            </div>
        </div>

        <hr/>

        <div class="m-widget2 mt-4" id="licenses-certifications">
            <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill"
                        data-toggle="collapse"
                        data-target="#collapsibleLicensesCertifications"
                        aria-expanded="false" aria-controls="collapsibleLicensesCertifications">
                    <i class="more-less fa fa-chevron-right"></i>
                </button>
                <h5 class="ml-3 mb-0">Licenses And Certifications</h5>
                <span class="pl-2 m-menu__link-badge">
                    <span class="m-badge m-badge--success" id="liscerts-total-badge"></span>
                </span>
            </div>
            <div id="collapsibleLicensesCertifications" class="collapse">
                <div id="alert-no-licenses-certifications-yet">
                    <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                </div>
                <div class="row"></div>
            </div>
        </div>

        <hr/>

        <div class="m-widget2 mt-4" id="performance-evaluation">
            <div class="mb-4 d-flex flex-row align-items-center accordion-header">
                <button class="btn btn-default m-btn--icon m-btn--icon-only m-btn--pill"
                        data-toggle="collapse"
                        data-target="#collapsiblePerformanceEvaluation"
                        aria-expanded="false" aria-controls="collapsiblePerformanceEvaluation">
                    <i class="more-less fa fa-chevron-right"></i>
                </button>
                <h5 class="ml-3 mb-0">Performance Evaluation</h5>
                <span class="pl-2 m-menu__link-badge">
                    <span class="m-badge m-badge--success" id="performance-total-badge"></span>
                </span>
            </div>
            <div id="collapsiblePerformanceEvaluation" class="collapse">
                <div id="alert-no-performance-evaluation-yet">
                    <h6 class="mt-2 text-muted" style="padding-left: 48px;">No record(s) to show.</h6>
                </div>
                <div class="row"></div>
            </div>
        </div>
    </div>

    <!--<div class="accordion" id="accordionExample">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link btnNew" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                            aria-controls="collapseOne">
                        Collapsible Group Item #1
                    </button>
                </h2>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non
                    cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird
                    on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                    nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim
                    aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h2 class="mb-0">
                    <button class="btn btn-link btnNew collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo"
                            aria-expanded="false"
                            aria-controls="collapseTwo">
                        Collapsible Group Item #2
                    </button>
                </h2>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non
                    cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird
                    on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                    nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim
                    aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingThree">
                <h2 class="mb-0">
                    <button class="btn btn-link btnNew collapsed" type="button" data-toggle="collapse" data-target="#collapseThree"
                            aria-expanded="false"
                            aria-controls="collapseThree">
                        Collapsible Group Item #3
                    </button>
                </h2>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non
                    cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird
                    on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                    nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim
                    aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
    </div>-->
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="preview-document-dialog">
    <div class="modal-dialog modal-extra-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>