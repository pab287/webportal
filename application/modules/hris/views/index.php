<style type="text/css">
    #employee_evaluation_options{
            display: none;
        }
    @media (min-width: 320px) and (max-width: 460px) {
        #search {
            margin-top: 20px;
        }


        /* employee evaluation list */
        #employee_evaluation_options{
            display: block;
        }

        #evaluation_tab {
            display: none;
        }

        /* newly hired/birthday celebrants */
        .personnel-request-summary-container {
            min-width: 1000px;
        }
    }

    @media (min-width: 1400px) and (max-width: 1920px){
        .personnel-request-summary-container {
            min-width: 0px;
        }
    }

    @media (min-width: 450px) and (max-width: 540px){
        .personnel-request-summary-container {
            min-width: 800px;
        }
    }

    @media (min-width: 1400px) and (max-width: 1920px){
        .employee-status-chart-container {
            min-width: 0px;
        }
    }

    @media (min-width: 450px) and (max-width: 540px){
        .employee-status-chart-container {
            min-width: 800px;
        }
    }
    

    

    .employee-name-link {
        color: #141414;
        transition: all 120ms;
        text-decoration: none !important;
    }

    .employee-name-link:hover {
        color: #027cc2;
    }

    /* @media only screen and (max-width: 768px) {
        .active-employees-per-company-container {
            font-weight: bold;
        }

        .employee-status-chart-container {
            font-weight: bold;
        }

        .personnel-request-summary-container {
            font-weight: bold;
        }

        .gender-chart-container {
            font-weight: bold;
        }
    } */

    #manual-limit {
        border: none;
        background-color: transparent;
        cursor: pointer;
    }

    #manual-limit:focus {
        border: none;
        outline: none;
    }
</style>


<div class="m-content">
    <!-- START HEADER AND SEARCH BOX -->
    <div class="row d-flex flex-row align-items-end position-relative"
         style="z-index: 1;">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <h4>
                DASHBOARD
                <small class="text-muted">HUMAN RESOURCE INFORMATION SYSTEM</small>
            </h4>
        </div>
        <div class="offset-xl-2 offset-lg-2 offset-md-2 offset-sm-0 col-xl-4 col-lg-4 col-md-4 col-sm-12">
            <div class="form-group m-form__group mb-0" id="search">
                <div class="m-input-icon m-input-icon--left position-relative search-with-dropdown-container">
                    <input class="form-control m-input form-control-lg search-with-dropdown" placeholder="Looking for someone?" style="height: auto; text-transform: none;">
                    <button id="manual-limit" class="m-input-icon__icon m-input-icon__icon--left" data-toggle="m-tooltip" data-placement="top" data-original-title="Click this for manual Search of Employee">
                        <span>
                            <i class="fa fa-search"></i>
                        </span>
                    </button>
                    <span class="m-input-icon__icon m-input-icon__icon--right" style="cursor: pointer;">
                        <span>
                            <i class="fa fa-caret-down" id="search_filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                            <ul class="dropdown-menu dropdown-menu-right" id="column-options" aria-labelledby="btnGroupDrop1" x-placement="bottom-end">
                                <li class="dropdown-item">
                                    <label class="m-checkbox mb-0">
                                        <input type="checkbox" name="search_filter" oninput="checkFilter()" value="skills">
                                        SKILLS
                                        <span></span>
                                    </label>
                                </li>
                                <li class="dropdown-item">
                                    <label class="m-checkbox mb-0">
                                        <input type="checkbox" name="search_filter" oninput="checkFilter()" value="education">
                                        EDUCATION
                                        <span></span>
                                    </label>
                                </li>
                            </ul>
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

    <!-- START::ANALYTICS SECTION 1 -->
    <div class="row position-relative" style="display: none;">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <!-- START ACTIVE EMPLOYEES PER COMPANY -->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                ACTIVE EMPLOYEES BY COMPANY
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="active-employees-per-company-container-pie" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
            <!-- END ACTIVE EMPLOYEES PER COMPANY -->
        </div>
    </div>
    <!-- END::ANALYTICS SECTION 1 -->

    <!-- START GENDER & EMPLOYEE STATUS -->
    <div class="row mt-5 position-relative">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" id="employee_status_by_company">
            <!-- START ACTIVE EMPLOYEES PER COMPANY -->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                ACTIVE EMPLOYEES BY COMPANY
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body" style='overflow-x: auto;'>
                    <!-- div class="active-employees-per-company-container _container" style="min-width: 1000px;"></div -->
                    <div class="active-employees-per-company-container2 _container" style="min-width: 1000px;"></div>
                    <!--<div class="d-flex justify-content-center align-items-center m--font-boldest2 mt-3"
                         id="active-employees-graph-total" style="color: #656565;">
                        TOTAL: <span class="ml-2" style="font-size: 16px;">0</span>
                     </div> -->
                </div>
            </div>
            <!-- END ACTIVE EMPLOYEES PER COMPANY -->
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" id="employee_status">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title w-100" style="display: flex; justify-content: space-between;">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div class="colmn-1 col-9 p-0">
                                    <span class="m-portlet__head-icon"><i class="flaticon-graph"></i></span>
                                    <h3 class="m-portlet__head-text">EMPLOYEE STATUS</h3>
                                </div>

                                <div class="colmn-2 col-3 p-0">
                                    <div class="m-select2 m-select2--pill">
                                        <form>
                                            <select id="select2_company" name="select2_company" data-validation="false" class="form-control"></select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body" style='overflow-x: auto;'>
                    <div class="d-flex justify-content-center align-items-center m--font-boldest2 mt-3" id="employee-status-graph-company" style="color: #656565;">
                        <p style="font-size: 16px;"><span style="text-transform: uppercase;"></span></p>
                    </div>
                    <div class="employee-status-chart-container _container" style="min-width: 1000px; "></div>
                    <div class="d-flex justify-content-center align-items-center m--font-boldest2 mt-3" id="employee-status-graph-total" style="color: #656565;">
                        TOTAL: <span class="ml-2" style="font-size: 16px;">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END GENDER & EMPLOYEE STATUS -->

    <!---->
    <div class="row">
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12" id="personnel_chart">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-user"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Personnel Request Summary
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body position-relative" style='overflow-x: auto;'>
                    <div class="personnel-request-summary-container _container"></div>
                    <div class="d-flex justify-content-center align-items-center m--font-boldest2 mt-3"
                         id="personnel-request-graph-total" style="color: #656565;">
                        TOTAL: <span class="ml-2" style="font-size: 16px;">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12" id="gender_chart">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-graph"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                GENDER
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body position-relative">
                    <div class="gender-chart-container _container"></div>
                    <div class="d-flex justify-content-center align-items-center m--font-boldest2 mt-3"
                         id="gender-graph-total" style="color: #656565;">
                        TOTAL: <span class="ml-2" style="font-size: 16px;">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---->

    <!-- START EMPLOYEE ANNIVERSARY -->
    <div class="m-portlet" style="display:none;">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="la la-certificate"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        Employee Anniversary
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="demographics-avatar-container">
                <?php foreach ($employees_with_anniversary as $row) {
                    $image_path = "uploads/files/images/employee_files/empcode_" . $row->id . "/" . $row->pic_filename;
                    $image = base_url("assets/images/profile/no_image.jpg");
                    if (file_exists(realpath($image_path))) {
                        $image = base_url($image_path);
                    }
                    // $image = 'http://bcd.gccph.com/hris/uploads/' . $row->pic_filename;
                    ?>
                    <div class="d-flex flex-column align-items-center
                        <?= $row->highlight == 1 ? 'm--font-boldest text-info' : '' ?>">
                        <div class="demographics-avatar" style="background-image: url('<?= $image ?>')"></div>
                        <div class="text-center mt-2 <?= $row->highlight == 1 ? 'm--font-boldest text-info' : 'm--font-bolder' ?>">
                            <a href="<?= base_url('hris/masterfile/view_employee_masterfile/' . $row->id) ?>"
                               class="employee-name-link" <?= $row->highlight == 1 ? 'style="color: inherit;"' : "" ?>>
                                <?= $row->employee_name ?>
                            </a>
                        </div>
                        <div class="text-center"><?= $row->anniversary ?></div>
                        <div class="text-center"><?= $row->years_in_service ?></div>
                    </div>
                <?php } ?>
            </div>

            <?php if (empty($employees_with_anniversary)) { ?>
                <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-info alert-dismissible mb-0"
                     role="alert">
                    <span class="m--font-boldest">No employee(s) with anniversary for the month of</span>
                    <strong>
                        <?= date('F') . "." ?>
                    </strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- END EMPLOYEE ANNIVERSARY -->

    <!-- START TURNOVER RATE -->
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-graph"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        TURNOVER RATE
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm"
                    role="tablist" id="evaluation_tab">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active"
                            data-toggle="tab" role="tab" href="" onclick="loadTurnoverRate()">
                            As OF Today
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link"
                            data-toggle="tab" role="tab" href="javascript:void(0)" onclick="loadTurnoverRate('1')">
                            Last Month
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link"
                            data-toggle="tab" role="tab" href="javascript:void(0)" onclick="loadTurnoverRate('3')">
                            Last 3 Months
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link"
                            data-toggle="tab" role="tab" href="javascript:void(0)" onclick="loadTurnoverRate('6')">
                            Last 6 Months
                        </a>
                    </li>
                    <li class="nav-item dropdown m-tabs__item">
                        <a class="nav-link m-tabs__link dropdown-toggle" 
                            data-toggle="dropdown" href="javascript:void(0)">Year</a>
                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">
                            <?php foreach ($years as $yr): if($yr->years): ?>
                                <a class="dropdown-item text-center" 
                                    data-toggle="tab" 
                                    href="javascript:void(0)"
                                    onclick="loadTurnoverRateByYear('<?php echo $yr->years; ?>')"><?= $yr->years; ?></a>
                            <?php endif; endforeach; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="retention-chart-container _container" style="height: 500px;"></div>
            <!-- div class="chartdiv" style="height: 500px;"></div -->
        </div>
    </div>
    <!-- START TURNOVER RATE -->

    <!-- START ON LEAVE EMPLOYEES -->
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-graph"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        ON LEAVE EMPLOYEES
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="table-responsive">
                <table class="table table-hover m-table m-table--head-no-border"
                       id="table-on-leave-employees" width="100%">
                    <thead>
                    <tr>
                        <th>STATUS</th>
                        <th>EMPLOYEE</th>
                        <th>NATURE</th>
                        <th>TYPE</th>
                        <th>DATE & TIME</th>
                        <th>REFERENCE NO.</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END ON LEAVE EMPLOYEES -->

    <!-- START BIRTHDAY CELEBRANTS -->
    <div class="m-portlet" style="display: none;">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="la la-birthday-cake"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        BIRTHDAY CELEBRANTS
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="demographics-avatar-container">
                <?php foreach ($employees_with_birthday as $row) {
                    $image_path = "uploads/files/images/employee_files/empcode_" . $row->id . "/" . $row->pic_filename;
                    $image = base_url("assets/images/profile/no_image.jpg");
                    if (file_exists(realpath($image_path))) {
                        $image = base_url($image_path);
                    }

                    // $image = 'http://bcd.gccph.com/hris/uploads/' . $row->pic_filename;
                    ?>
                    <div class="d-flex flex-column align-items-center
                        <?= $row->highlight == 1 ? 'm--font-boldest text-info' : '' ?>">
                        <div class="demographics-avatar" style="background-image: url('<?= $image ?>')"></div>
                        <div class="text-center mt-2 <?= $row->highlight == 1 ? 'm--font-boldest text-info' : 'm--font-bolder' ?>">
                            <a href="<?= base_url('hris/masterfile/view_employee_masterfile/' . $row->id) ?>"
                               class="employee-name-link" <?= $row->highlight == 1 ? 'style="color: inherit;"' : "" ?>>
                                <?= $row->employee_name ?>
                            </a>
                        </div>
                        <div class="text-center"><?= $row->bday ?></div>
                    </div>
                <?php } ?>
            </div>

            <?php if (empty($employees_with_birthday)) { ?>
                <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-info alert-dismissible mb-0"
                     role="alert">
                    <span class="m--font-boldest">No birthday celebrants for the month of</span>
                    <strong>
                        <?= date('F') . "." ?>
                    </strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- END BIRTHDAY CELEBRANTS -->

    <!-- START NEWLY HIRED -->
    <div class="m-portlet m-portlet--tabs">
        <div class="m-portlet__head">
            <div class="m-portlet__head-tools" id="newly_hired">
                <ul class="nav nav-tabs m-tabs m-tabs-line" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab" aria-expanded="true">
                            <i class="la la-user-plus"></i>
                            Newly Hired</a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_tab_1_2" role="tab" aria-expanded="false">
                            <i class="la la-birthday-cake"></i>Birthday Celebrants
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_tab_1_3" role="tab" aria-expanded="false">
                            <i class="la la-birthday-cake"></i>Upcoming Birthday Celebrants
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="tab-content" style="height: 600px; overflow-y: auto;">
                <div class="tab-pane active overflow-auto" id="m_portlet_tab_1_1">
                    <div class="demographics-avatar-container">
                    <?php foreach ($newly_hired as $row) {
                        $image_path = "uploads/files/images/employee_files/empcode_" . $row->id . "/" . $row->pic_filename;
                        $image = base_url("assets/images/profile/no_image.jpg");
                        if (file_exists(realpath($image_path))) {
                            $image = base_url($image_path);
                        }

                        // $image = 'http://bcd.gccph.com/hris/uploads/' . $row->pic_filename;
                        ?>
                        <div class="d-flex flex-column align-items-center">
                            <div class="demographics-avatar" style="background-image: url('<?= $image ?>')"></div>
                            <div class="text-center mt-2 m--font-bolder">
                                <a href="<?= base_url('hris/masterfile/view_employee_masterfile/' . $row->id) ?>"
                                class="employee-name-link">
                                    <?= $row->employee_name ?>
                                </a>
                            </div>
                            <div class="text-center"><?= $row->date_start ?></div>
                        </div>
                    <?php } ?>
                    </div>

                    <?php if (empty($newly_hired)) { ?>
                        <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-info alert-dismissible mb-0"
                            role="alert">
                            <span class="m--font-boldest">No newly hired employee for the month of</span>
                            <strong>
                                <?= date('F') . "." ?>
                            </strong>
                        </div>
                    <?php } ?>
                </div>
                <div class="tab-pane" id="m_portlet_tab_1_2">
                    <div class="demographics-avatar-container">
                    <?php foreach ($employees_with_birthday as $row) {
                        $image_path = "uploads/files/images/employee_files/empcode_" . $row->id . "/" . $row->pic_filename;
                        $image = base_url("assets/images/profile/no_image.jpg");
                        if (file_exists(realpath($image_path))) {
                            $image = base_url($image_path);
                        }

                        // $image = 'http://bcd.gccph.com/hris/uploads/' . $row->pic_filename;
                        ?>
                        <div class="d-flex flex-column align-items-center
                            <?= $row->highlight == 1 ? 'm--font-boldest text-info' : '' ?>">
                            <div class="demographics-avatar" style="background-image: url('<?= $image ?>')"></div>
                            <div class="text-center mt-2 <?= $row->highlight == 1 ? 'm--font-boldest text-info' : 'm--font-bolder' ?>">
                                <a href="<?= base_url('hris/masterfile/view_employee_masterfile/' . $row->id) ?>"
                                class="employee-name-link" <?= $row->highlight == 1 ? 'style="color: inherit;"' : "" ?>>
                                    <?= $row->employee_name ?>
                                </a>
                            </div>
                            <div class="text-center"><?= $row->bday ?></div>
                        </div>
                    <?php } ?>
                    </div>
                <?php if (empty($employees_with_birthday)) { ?>
                    <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-info alert-dismissible mb-0"
                        role="alert">
                        <span class="m--font-boldest">No birthday celebrants for the month of</span>
                        <strong>
                            <?= date('F') . "." ?>
                        </strong>
                    </div>
                <?php } ?> 
                </div>
                <div class="tab-pane" id="m_portlet_tab_1_3">
                    <div class="demographics-avatar-container">
                    <?php foreach ($next_employees_with_birthday as $row) {
                        $image_path = "uploads/files/images/employee_files/empcode_" . $row->id . "/" . $row->pic_filename;
                        $image = base_url("assets/images/profile/no_image.jpg");
                        if (file_exists(realpath($image_path))) {
                            $image = base_url($image_path);
                        }

                        // $image = 'http://bcd.gccph.com/hris/uploads/' . $row->pic_filename;
                        ?>
                        <div class="d-flex flex-column align-items-center m--font-bold">
                            <div class="demographics-avatar" style="background-image: url('<?= $image ?>')"></div>
                            <div class="text-center mt-2 m--font-bolder">
                                <a href="<?= base_url('hris/masterfile/view_employee_masterfile/' . $row->id) ?>"
                                class="employee-name-link">
                                    <?= $row->employee_name ?>
                                </a>
                            </div>
                            <div class="text-center"><?= $row->bday ?></div>
                        </div>
                    <?php } ?>
                    <?php if (empty($next_employees_with_birthday)) { ?>
                        <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-info alert-dismissible mb-0"
                            role="alert">
                            <span class="m--font-boldest">No birthday celebrants for the month of</span>
                            <strong>
                                <?= date('F', strtotime("+1 month")) . "." ?>
                            </strong>
                        </div>
                    <?php } ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END NEWLY HIRED -->

    <!-- START EVALUATION LIST -->
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-graph"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        EMPLOYEE EVALUATION LIST
                    </h3>
                </div>
                
            </div>
            <div class="m-portlet__head-tools">
                <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist" id="evaluation_tab">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab" role="tab" href="#evaluation_list_tab_content" onclick="loadEvaluationTable();">
                            3rd Month
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab" role="tab" href="#evaluation_list_tab_content" onclick="loadEvaluationTable('2nd');">
                            4.5TH Month
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab" role="tab" href="#evaluation_list_tab_content" onclick="loadEvaluationTable('final');">
                            FINAL EVALUATION
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item dropdown">
                        <a class="nav-link m-tabs__link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">
                            <span class="overdue_eval_stage_text">Overdue</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right mt-2" x-placement="bottom-end">
                            <a class="dropdown-item" data-toggle="tab" role="tab" href="#overdue_list_tab_content" onclick="loadOverdueEvaluationTable(1)">3rd Month</a>
                            <a class="dropdown-item" data-toggle="tab" role="tab" href="#overdue_list_tab_content" onclick="loadOverdueEvaluationTable(2)">4.5TH Month</a>
                            <a class="dropdown-item" data-toggle="tab" role="tab" href="#overdue_list_tab_content" onclick="loadOverdueEvaluationTable(3)">Final Evaluation</a>
                            <!-- <div class="dropdown-divider"></div>
                            <a class="dropdown-item" data-toggle="tab" role="tab" href="#overdue_list_tab_content" onclick="loadOverdueEvaluationTable(0)">All</a> -->
                        </div>
                    </li>
                </ul>

                <div id="employee_evaluation_options">    
                    <select id="" class="employee_evaluation_options form-control" onchange="loadEvaluationTable()">
                        <option value=" ">3rd Month</option>
                        <option value="2nd">4.5th Month</option>
                        <option value="final">FINAL EVALUATION</option>
                        <option value="overdue">Overdue</option>
                    </select>  
                </div>      
            </div>
            
        </div>
        <div class="m-portlet__body">   
            <div id="employee_eval_list" class="tab-content">
                <div class="tab-pane active" aria-expanded="false" role="tabpanel" id="evaluation_list_tab_content">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="col-2">
                            <button id="exportBtn_eval" title="Export" type="button" class="btn btnExport btn-success m-btn--pill m-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-external-link"></i>
                                <span>Export</span>
                                <span class="dropdown-toggle"></span>
                            </button>

                            <div class="dropdown-menu mt-2" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(268px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <a href="javascript:void(0);" class="dropdown-item datatable-csv" id="eval_ExportCSV">
                                    <i class="m-nav__link-icon la la-file-o"></i>
                                    <span class="m-nav__link-text">CSV</span>
                                </a>

                                <a href="javascript:void(0);" class="dropdown-item datatable-pdf" id="eval_ExportPDF">
                                    <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                    <span class="m-nav__link-text">PDF</span>
                                </a>

                                <a href="javascript:void(0);" class="dropdown-item datatable-excel" id="eval_ExportExcel">
                                    <i class="m-nav__link-icon la la-file-excel-o"></i>
                                    <span class="m-nav__link-text">EXCEL</span>
                                </a>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="m-input-icon m-input-icon--left">
                                <input type="text" class="form-control m-input" placeholder="Search..." id="EvalSearch" style="border: 1px solid #c3c3c3;">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span><i class="la la-search"></i></span>
                                </span>
                            </div>
                        </div>
                    </div>
                
                    <div class="table-responsive">
                        <table class="table table-hover m-table m-table--head-no-border" id="table-employee-evaluation" width="100%">
                            <thead>
                                <tr>
                                    <th class="toggle-all notExport text-center">
										<input type="checkbox" id="cb-select-all"> <span></span>
									</th>
                                    <th>ID NO.</th>
                                    <th>EMPLOYEE</th>
                                    <th>COMPANY</th>
                                    <th>POSITION</th>
                                    <th>DATE HIRED</th>
                                    <th>EVALUATION DATE</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" aria-expanded="false" role="tabpanel" id="overdue_list_tab_content">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="col-2">
                            <button id="exportBtn_eval_overdue" title="Export" type="button" class="btn btnExport btn-success m-btn--pill m-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-external-link"></i>
                                <span>Export</span>
                                <span class="dropdown-toggle"></span>
                            </button>

                            <div class="dropdown-menu mt-2" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(268px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <a href="javascript:void(0);" class="dropdown-item datatable-csv" id="eval_due_ExportCSV">
                                    <i class="m-nav__link-icon la la-file-o"></i>
                                    <span class="m-nav__link-text">CSV</span>
                                </a>

                                <a href="javascript:void(0);" class="dropdown-item datatable-pdf" id="eval_due_ExportPDF">
                                    <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                    <span class="m-nav__link-text">PDF</span>
                                </a>

                                <a href="javascript:void(0);" class="dropdown-item datatable-excel" id="eval_due_ExportExcel">
                                    <i class="m-nav__link-icon la la-file-excel-o"></i>
                                    <span class="m-nav__link-text">EXCEL</span>
                                </a>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="m-input-icon m-input-icon--left">
                                <input type="text" class="form-control m-input" placeholder="Search..." id="overdueEvalSearch" style="border: 1px solid #c3c3c3;">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span><i class="la la-search"></i></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover m-table m-table--head-no-border" id="table-employee-evaluation-overdue" width="100%">
                            <thead>
                                <tr>
                                    <th class="toggle-all notExport text-center">
										<input type="checkbox" id="cb-select-all"> <span></span>
									</th>
                                    <th>ID NO.</th>
                                    <th>EMPLOYEE</th>
                                    <th>COMPANY</th>
                                    <th>POSITION</th>
                                    <th>DATE HIRED</th>
                                    <th>EVAL STAGE</th>
                                    <th>EVAL DATE</th>
                                    <th>OVERDUE</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END EVALUATION LIST -->
</div> 

<style>
	.v-middle {
		vertical-align: middle!important;
	}

	#table-employee-evaluation-overdue tbody td.select-checkbox:before,
    #table-employee-evaluation tbody td.select-checkbox:before {
		top: 0!important;
		bottom: 0!important;
		left: 0!important;
		right: 0!important;
		margin: auto!important;
		border: 1px solid #767676;
		border-radius: 2px!important;
		height: 13px!important;
		width: 13px!important;
	}

	#table-employee-evaluation-overdue tbody td.select-checkbox:after,
    #table-employee-evaluation tbody td.select-checkbox:after {
		position: absolute!important;
		top: -8px!important;
		bottom: 0!important;
		left: 0!important;
		right: 0!important;
		margin: auto!important;
	}

	#table-employee-evaluation-overdue tbody tr.selected td.select-checkbox:before,
    #table-employee-evaluation tbody tr.selected td.select-checkbox:before {
		border: 1px solid #ffffff !important;
	}
</style>