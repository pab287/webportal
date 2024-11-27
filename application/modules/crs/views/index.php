<div class="m-content">
    <!-- START HEADER AND SEARCH BOX -->
    <div class="row d-flex flex-row align-items-end position-relative"
         style="z-index: 1;">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <h4>
                DASHBOARD
                <small class="text-muted">COMPANY RECRUITMENT SYSTEM</small>
            </h4>
        </div>
        <div class="offset-xl-2 offset-lg-2 offset-md-2 offset-sm-0 col-xl-4 col-lg-4 col-md-4 col-sm-12">
            <div class="form-group m-form__group mb-0" id="search">
                <div class="m-input-icon m-input-icon--left position-relative search-with-dropdown-container">
                    <input class="form-control m-input form-control-lg search-with-dropdown"
                           placeholder="Looking for someone?"
                           style="height: auto; text-transform: none;">
                    <span class="m-input-icon__icon m-input-icon__icon--left">
                        <span>
                            <i class="fa fa-search"></i>
                        </span>
                    </span>

                    <div class="position-absolute options-container invisible search-with-dropdown-suggestion-list">
                        <ul class="employee-suggestion mb-0">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END HEADER AND SEARCH BOX -->


    <div class="m-portlet mt-5">
        <div class="m-portlet__body  m-portlet__body--no-padding">
            <div class="row m-row--no-padding m-row--col-separator-xl">
                <div class="col-md-12 col-lg-6 col-xl-6">
                    <a href="<?php echo base_url('crs/resume'); ?>" style="text-decoration: none;">
                        <button id="active_button" class="btn m-btn--square btn-default btn-block">
                            <div class="m-widget24">
                                <div class="m-widget24__item text-left">
                                    <h3 class="m-widget24__title">
                                        Active Resume
                                    </h3>
                                    <br>
                                    <span id="resume" class="m-widget24__stats">
							        </span>
                                    <div class="m--space-10"></div>
                                    <div class="progress m-progress--sm">
                                        <div id="progress_resume" class="progress-bar m--bg-primary" role="progressbar" aria-valuenow="50"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="m-widget24__change">
								        Percent
							        </span>
                                    <span id="percent_resume" class="m-widget24__number">
							        </span>
                                </div>
                            </div>
                        </button>
                    </a>
                </div>

                <div class="col-md-12 col-lg-6 col-xl-6">
                    <a href="<?php echo base_url('crs/archive'); ?>" style="text-decoration: none;">
                        <button class="btn m-btn--square  btn-default btn-block">
                            <div class="m-widget24 text-left">
                                <div class="m-widget24__item">
                                    <h3 class="m-widget24__title">
                                        Archived Resume
                                    </h3>
                                    <br>
                                    <span id="archive" class="m-widget24__stats"></span>
                                    <div class="m--space-10"></div>
                                    <div class="progress m-progress--sm">
                                        <div id="progress_archive" class="progress-bar m--bg-info" role="progressbar" aria-valuenow="50"
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="m-widget24__change">Percent</span>
                                    <span id="percent_archive" class="m-widget24__number"></span>
                                </div>
                            </div>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="m-portlet ">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Resume Position List
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab"
                           onclick="loadTab('#m_widget11_tab1_content')"
                           role="tab" style="cursor: pointer;">
                            Supervisory
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab"
                           onclick="loadTab('#m_widget11_tab2_content')"
                           role="tab" style="cursor: pointer;">
                            Managerial
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab"
                           onclick="loadTab('#m_widget11_tab3_content')"
                           role="tab" style="cursor: pointer;">
                            Skilled Rank and File
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab"
                           onclick="loadTab('#m_widget11_tab4_content')"
                           role="tab" style="cursor: pointer;">
                            Rank and File
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body  m-portlet__body--no-margin">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="m_widget11_tab1_content">
                    <!--begin::Widget 11-->
                    <div class="m-widget11">
                        <!--begin: Datatable -->
                        <div class="row">
                            <div class="col-md-6">
                                <div style="width: 100%; height: 630px;" id="chartdiv2"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                    <table class="table table-striped table-bordered" id="table-supervisory" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--end: Datatable -->
                    </div>
                </div>
                <div class="tab-pane fade" id="m_widget11_tab2_content">
                    <!--begin::Widget 11-->
                    <div class="m-widget11">
                        <div class="row">
                            <div class="col-md-6">
                                <div style="width: 100%; height: 630px;" id="chartdiv3">
                                </div>
                            </div>
                            <!--begin: Datatable -->
                            <div class="col-md-6">
                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                    <table class="table table-striped table-bordered" id="table-managerial" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--end: Datatable -->
                    </div>
                </div>
                <div class="tab-pane fade" id="m_widget11_tab3_content">
                    <!--begin::Widget 11-->
                    <div class="m-widget11">
                        <div class="row">
                            <div class="col-md-6">
                                <div style="width: 100%; height: 630px;" id="chartdiv4">
                                </div>
                            </div>
                            <!--begin: Datatable -->
                            <div class="col-md-6">
                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                    <table class="table table-striped table-bordered" id="table-skilled" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--end: Datatable -->
                    </div>
                </div>
                <div class="tab-pane fade" id="m_widget11_tab4_content">
                    <!--begin::Widget 11-->
                    <div class="m-widget11">
                        <div class="row">
                            <div class="col-md-6">
                                <div style="width: 100%; height: 630px;" id="chartdiv5">
                                </div>
                            </div>
                            <!--begin: Datatable -->
                            <div class="col-md-6">
                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                    <table class="table table-striped table-bordered" id="table-rank" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--end: Datatable -->
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="m-portlet ">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Recruitment Source
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__body  m-portlet__body--no-padding">
                <div style="width: 100%; height: 400px;" id="chartdiv">
                </div>
            </div>
        </div>

    </div>
    <div class="m-portlet ">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Recent Uploads Resume
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body  m-portlet__body--no-margin">
            <!--begin: Datatable -->
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                <table class="table table-striped table-bordered" id="table-resume" width="100%">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>School</th>
                        <th>Course</th>
                        <th>Position</th>
                        <th>Recruitment</th>
                    </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
            <!--end: Datatable -->
        </div>
    </div>

</div>
<style>
    .ui-autocomplete {
        background-color: #ffffff;
    }

</style>
