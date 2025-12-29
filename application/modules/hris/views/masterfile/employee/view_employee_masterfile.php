<style type="text/css">
    .company-logo {
        border-radius: 0 !important;
        width: 80px;
    }

    tbody > tr > td[data-label] { word-break: break-word; }
    @supports (-moz-appearance:none) {}
    .moz-text-center{ align: center; }
    #mobile-search{ display: none; }
    #to-print{ display: none; }

    .search-with-dropdown-container .search-with-dropdown-suggestion-list, .search-with-mobile-dropdown-container .search-with-mobile-dropdown-suggestion-list{ z-index: 40; }

    @media screen and (max-width: 520px){
        #mobile-search{
            display: block !important;
            margin-top: 1rem !important;
        }
        #web-search{ display: none !important; }
        .mobile-view{ position: relative; }
    }

    .bg-a9{
        background: #a9a8a8;
        border-color: #a9a8a8;
        border: 1px solid #a9a8a8 !important;
    }

    .bg-a9 .m-portlet__head {
        background-color: #a9a8a8;
        border-color: #a9a8a8;
    }

    .bg-a9 .m-portlet__head-text{
        color: #fff !important;
    }

    table tbody tr.highlight td { font-weight: 600; }
    table tbody tr.highlight { background-color: #F2F3F88F; }
</style>

<div class="m-content" id="m-content">
    <div class="row">
        <div class="col-lg-10 offset-lg-1 col-xl-3 offset-xl-0">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div id="left_pane-card" class="m-card-profile">
                    <input type="hidden" id="employee_id" v-text="main.id">
                        <div class="m-card-profile__title m--hide">Title Profile</div>
                        <div class="m-card-profile__pic m-card-user__pic">
                            <div class="m-card-profile__pic-wrapper position-relative">
                                <img id="image--holder" :src="path" :alt="main.firstname">
                            </div>
                            <div class="m-card-profile__details">
                                <span class="m-card-profile__name" v-text="getDisplayName().display_name_1">

                                </span>
                            </div>
                            
                            <div class="rating">
                                <div id="performance-rating"></div>
                                <div id="performance-rating-description" style="margin-top: 4px;"
                                    class="text-muted m--font-boldest m--regular-font-size-sm2"></div>
                                <div id="performance-rating-rehire" style="margin-top: 4px;"
                                class="text-muted m--font-boldest m--regular-font-size-sm2"></div>
                                <div id="performance-rating-remarks" style="margin-top: 4px;"
                                class="text-muted m--font-boldest m--regular-font-size-sm2"></div>
                            </div>

                            <div class=" mt-3 moz-text-center">
                                <div class="d-flex flex-shrink-1 flex-grow-1 pl-4 flex-column">
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                        <div class="col-xl-6">
                                            <span>ID No.</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="main.idno"></span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                        <div class="col-xl-6">
                                            <span>Biometric No.</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="main.biometricno"></span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                        <div class="col-xl-4">
                                            <span>Position:</span>
                                        </div>
                                        <div class="col-xl-8 text-xl-right text-lg-right text-sm-center">
                                            <template v-if="main.is_multiple_position == 1">
                                                <span class="m--font-bolder">{{ formatPosition(main.position) }}</span>
                                            </template>
                                            <template v-else>
                                                <span class="m--font-bolder" v-text="main.position"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                        <div class="col-xl-6">
                                            <span>Status:</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="main.work_status"></span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                        <div class="col-xl-6">
                                            <span>Employee Status:</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="main.employee_status"></span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                        <div class="col-xl-6">
                                            <span>Date Started:</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="formatStartDate(main.date_start)"></span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row" v-if="(main.employee_status != 'Active' && (main.date_end != null || main.date_end != '0000-00-00'))">
                                        <div class="col-xl-6">
                                            <span>Date Ended:</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="formatStartDate(main.date_end)"></span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                        <div class="col-xl-6">
                                            <span>Company:</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="main.company_id"></span>
                                        </div>
                                    </div>
                                    <template>
                                        <div hidden>
                                            <div v-if="main.position === 'owner'">
                                            </div>
                                            <template v-else>
                                                <div v-if="['SUPERVISORY', 'MANAGERIAL', 'EXECUTIVE'].includes(main.level)" class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                                    <div class="col-xl-6">
                                                        <span>Head/Supervisor:</span>
                                                    </div>
                                                    <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                                        <span class="m--font-bolder">Charles Anthony M. Dumancas</span>
                                                    </div>
                                                </div>
                                                <template v-else>
                                                    <div v-if="!supervisor">
                                                    </div>
                                                    <div v-else class="mt-1 text-sm-center text-xl-left text-lg-left row">
                                                        <div class="col-xl-6">
                                                            <span>Head/Supervisor</span>
                                                        </div>
                                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                                            <span class="m--font-bolder" v-text="supervisor"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides left-panel-actions">
                            <li class="m-nav__separator m-nav__separator--fit"></li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnEdit"
                                :href="baseUrl('hris/masterfile/edit_employee_masterfile/') + main.id">
                                    <i class="m-nav__link-icon flaticon-edit"></i>
                                    <span class="m-nav__link-text">Edit Employee Profile</span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnBack"
                                   :href="baseUrl('hris/masterfile/employee')">
                                    <i class="m-nav__link-icon fa fa-arrow-left"></i>
                                    <span class="m-nav__link-text">Back to Employee List</span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a class="m-nav__link btnPrint"
                                   href=""
                                   onclick="event.preventDefault(); printFetch()">
                                    <i class="m-nav__link-icon la la-print"></i>
                                    <span class="m-nav__link-text">Print Data Sheet</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php if (isset($page) && $page == 'profile'): ?>
                <div class="m-portlet">
                    <div class="m-portlet__body p-3">
                        <div class="m-form__group form-group row align-items-center m-0 justify-content-center">
                            <label class="col-sm-4 col-md-4 col-lg-8 col-xl-8 col-form-label">
                                Allow <span style="font-weight: 700">SMS</span> Notification? 
                                <i class="flaticon-questions-circular-button" style="font-size: 14px" data-toggle="m-tooltip" 
                                data-skin='dark' title='Toggle switch to enable/disable SMS notifications.'></i>
                            </label>
                            <div class="col-3">
                                <span class="m-switch m-switch--sm">
                                    <label class="m-0">
                                        <input type="checkbox" checked="checked" :checked="main.allow_sms_notification == 1 ? 'checked' : false" @change="changeSMS($event)">
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-xl-9 offset-xl-0 col-lg-10 offset-lg-1">

            <div class="m-portlet">
                <div class="m-portlet__head pt-4 pb-4">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-file-1"></i>
                            </span>
                            <h3 class="m-portlet__head-text">Employee Data Sheet</h3>
                        </div>
                        <div id="mobile-search" class="search-div row">
                            <?php if(in_array(strtolower("HR_201_SEARCH"),$this->core_layout->getCurrentActions())): ?>
                                <!-- for SEARCH privilege HEAD DEPARTMENT-->
                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 offset-xl-4 offset-lg-4 offset-md-4 offset-sm-0 btnHr_201_search">
                                <div class="form-group m-form__group mb-0" id="search">
                                    <div class="m-input-icon m-input-icon--left position-relative search-with-mobile-dropdown-container">
                                        <input class="form-control m-input form-control-lg search-with-mobile-dropdown"
                                            placeholder="Looking for someone?"
                                            style="height: auto; text-transform: none; border-radius: 3em; padding-left: 3.5rem;">
                                        <span class="m-input-icon__icon m-input-icon__icon--left"
                                            style="width: 4em;">
                                            <span>
                                                <i class="fa fa-search"></i>
                                            </span>
                                        </span>
                                        <span class="m-input-icon__icon m-input-icon__icon--right" style="cursor: pointer;">
                                            <span>
                                                <i class="fa fa-caret-down" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                                <ul class="dropdown-menu dropdown-menu-right" id="column-options" aria-labelledby="btnGroupDrop1" x-placement="bottom-end" style="position: absolute; transform: translate3d(-121px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
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

                                        <div class="position-absolute options-container invisible search-with-mobile-dropdown-suggestion-list">
                                            <ul class="employee-suggestion mb-0">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div id="web-search" class="m-portlet__head-tools">
                        <div class="search-div row">
                            <?php if(in_array(strtolower("HR_201_SEARCH"),$this->core_layout->getCurrentActions())): ?>
                                <!-- for SEARCH privilege HEAD DEPARTMENT-->
                            
                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 offset-xl-4 offset-lg-4 offset-md-4 offset-sm-0 btnHr_201_search">
                                    <div class="form-group m-form__group mb-0" id="search">
                                        <div class="m-input-icon m-input-icon--left position-relative search-with-dropdown-container">
                                            <input class="form-control m-input form-control-lg search-with-dropdown"
                                                placeholder="Looking for someone?"
                                                style="height: auto; text-transform: none; border-radius: 3em; padding-left: 3.5rem;">
                                            <span class="m-input-icon__icon m-input-icon__icon--left"
                                                style="width: 4em;">
                                                <span>
                                                    <i class="fa fa-search"></i>
                                                </span>
                                            </span>
                                            <span class="m-input-icon__icon m-input-icon__icon--right" style="cursor: pointer;">
                                                <span>
                                                    <i class="fa fa-caret-down" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                                    <ul class="dropdown-menu dropdown-menu-right" id="column-options" aria-labelledby="btnGroupDrop1" x-placement="bottom-end" style="position: absolute; transform: translate3d(-121px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
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

                            <?php endif;?>

                        </div>
                    </div>
                </div>
                <div id="data-sheet" class="m-portlet__body data-sheet">
                    <div class="m-portlet__section web-view" id="to-print">
                        <?php $this->load->view("hris/masterfile/employee/sections/personal_information"); ?>
                    </div>
                    <div class="m-portlet__section web-view" id="to-print">
                        <?php $this->load->view("hris/masterfile/employee/sections/employment_data"); ?>
                    </div>
                    <div id="hide-in-print" class="m-portlet__section web-view">
                        <?php $this->load->view("hris/masterfile/employee/sections/employee_data_web", $data, false); ?>
                    </div>
                    <div class="m-portlet__section mobile-view" style="display: none">
                        <?php $this->load->view("hris/masterfile/employee/sections/employee_data", $data, false); ?>
                    </div>
                </div>
            </div>

            <!-- payroll sheet and deductions -->
            <?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($payroll_sheet_data) && is_array($payroll_sheet_data) && count($payroll_sheet_data) > 0) || isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($deductions) && is_array($deductions) && count($deductions) > 0)): ?>

                <div class="m-portlet m-portlet--tabs">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-tools" id="payroll-sheet-payslip">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary">
                                <?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($payroll_sheet_data) && is_array($payroll_sheet_data) && count($payroll_sheet_data) > 0)): ?>
                                    <li class="nav-item m-tabs__item">
                                        <div class="row align-items-center justify-content-between">
                                            <span class="m-portlet__head-icon mr-2">
                                                <i class="flaticon-file-1"></i>
                                            </span>
                                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_user_profile_tab_3" role="tab" aria-expanded="true">Payroll Data Sheet</a>
                                        </div>
                                    </li>
                                <?php endif; ?>

                                <?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($deductions) && is_array($deductions) && count($deductions) > 0)): ?>
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_user_profile_tab_4" role="tab" aria-expanded="false">Deductions</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tab-content">
                                    <?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($payroll_sheet_data) && is_array($payroll_sheet_data) && count($payroll_sheet_data) > 0)): ?>
                                        <div class="tab-pane active" id="m_user_profile_tab_3">
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                                                    <div class="table-responsive-sm">
                                                        <table class="table table-bordered" id="table-payroll_sheet-payslip" style="width: 100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Pay Date</th>
                                                                    <th>Coverage Date</th>
                                                                    <th class="text-right">Gross Pay</th>
                                                                    <th class="text-right">Net Pay</th>
                                                                    <th class="text-center">&nbsp;</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if(isset($payroll_sheet_data) && is_array($payroll_sheet_data) && count($payroll_sheet_data) > 0): ?>
                                                                    <?php foreach ($payroll_sheet_data as $key => $value): ?>
                                                                        <tr class="<?php echo $value->id == $payroll_sheet_max_id ? "highlight":"" ?>">
                                                                            <td>
                                                                                <p class="mb-0">
                                                                                    <span><?php echo date("F d, Y", strtotime($value->pay_date)); ?></span>
                                                                                    <?php if($value->bonus_code): ?>
                                                                                        <span class="m-badge m-badge--danger m-badge--wide ml-2 m--regular-font-size-sm5"><?php echo $value->bonus_code; ?></span>
                                                                                    <?php endif; ?>
                                                                                    <?php if($value->id == $payroll_sheet_max_id): ?>
                                                                                        <span class="m-badge m-badge--success m-badge--wide ml-2 m--regular-font-size-sm5">CURRENT</span>
                                                                                        <?php if (intval($value->printed_payslip) == 1): ?>
                                                                                            <span class="m-badge m-badge--primary m-badge--wide m--regular-font-size-sm5">PRINTED</span>
                                                                                        <?php endif; ?>
                                                                                    <?php endif; ?>
                                                                                </p>
                                                                            </td>
                                                                            <td><?php echo date("F d, Y", strtotime($value->date_start)) ." ~ ". date("F d, Y", strtotime($value->date_end)); ?></td>
                                                                            <td class="text-right">
                                                                                <?php
                                                                                    if ($value->is_bonus == 0) {
                                                                                        echo ($value->printed_payslip == 1) ? number_format($value->gross_pay, 2, ".", ",") : str_repeat('*', strlen(number_format($value->gross_pay, 2, ".", ",")));
                                                                                    } else {
                                                                                        echo number_format($value->gross_pay, 2, ".", ",");
                                                                                    }
                                                                                ?>
                                                                            </td>
                                                                            <td class="text-right">
                                                                                <?php
                                                                                    if ($value->is_bonus == 0) {
                                                                                        echo ($value->printed_payslip == 1) ? number_format($value->net_pay, 2, ".", ",") : str_repeat('*', strlen(number_format($value->net_pay, 2, ".", ",")));
                                                                                    } else {
                                                                                        echo number_format($value->net_pay, 2, ".", ",");
                                                                                    }
                                                                                ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                            <?php if($value->is_bonus == 0): ?>
                                                                                <button type="button" class="btn btn-secondary btn-sm m-btn m-btn--icon m-btn--icon-only btnView_own_request" onClick="getPayrollSheetData(<?= $value->id; ?>, <?=$value->printed_payslip ?>)"><i class="la la-file-text"></i></button>
                                                                            <?php else: ?>
                                                                                <i class="la la-file-text"></i>
                                                                            <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($deductions) && is_array($deductions) && count($deductions) > 0)): ?>
                                        <div class="tab-pane" id="m_user_profile_tab_4">
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                                                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                                        <table id="tbl-loans" class="table display table-bordered table-striped dataTable no-footer" width="100%">
                                                            <thead>
                                                                <th width="30%">Loan Name</th>
                                                                <th>Loaned Amount</th>
                                                                <th>Amt. Pd.</th>
                                                                <th width="10%">Bal.</th>
                                                                <th>
                                                                    <span data-toggle="m-tooltip"
                                                                        data-placement="top"
                                                                        data-original-title="DEDUCTION TYPE"
                                                                        data-skin="dark">
                                                                        TYPE
                                                                    </span>
                                                                </th>
                                                                <th>
                                                                    <span data-toggle="m-tooltip"
                                                                        data-placement="top"
                                                                        data-original-title="PERCENTAGE VALUE OR FIXED AMOUNT VALUE"
                                                                        data-skin="dark">
                                                                        VALUE
                                                                    </span>
                                                                </th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </thead>
                                                            <tbody>
                                                                <?php if(isset($deductions) && is_array($deductions) && count($deductions) > 0): ?>
                                                                    <?php foreach($deductions as $key=> $rs): ?>
                                                                        <tr>
                                                                            <td width="30%">
                                                                                <?php 
                                                                                    $ref = ($rs->reference === '' || $rs->reference === null) ? '' : `<p class='m-0'><small><span class="m--font-bolder">Reference:</span>`.$rs->reference.`</small></p>`; 
                                                                                    $dnRefs = ($rs->debit_note === '' || $rs->debit_note === null) ? '' : "<span class='m--font-primary m--font-boldest m--margin-left-15 m--regular-font-size-lg1'>$rs->debit_note</span>";
                                                                                ?>

                                                                                <p class="mb-1 m--font-bolder"><?=$rs->loan_name ?> <?=$dnRefs ?></p><?=$ref ?>
                                                                                <p class='m-0'><small><span class="m--font-bolder">Created By:</span> <?=$rs->created_by ?></small></p>
                                                                                <p class='m-0'><small><span class="m--font-bolder">Created Date:</span><?=$rs->created_at ?></small></p>
                                                                            </td>
                                                                            <td class="text-right">
                                                                                <span class="m--font-boldest"><?=number_format($rs->amount, 2) ?></span>
                                                                            </td>
                                                                            <td class="text-right m--padding-right-30">
                                                                                <span class="m--font-boldest"><?=number_format($rs->total_amount_paid, 2) ?></span>
                                                                            </td>
                                                                            <td width="10%" class="text-right m--padding-right-30">
                                                                                <?php $balance = floatval($rs->amount) - floatval($rs->total_amount_paid); ?>
                                                                                <span class="m--font-boldest"><?=number_format($balance, 2) ?></span>
                                                                            </td>
                                                                            <td>
                                                                                <?=intval($rs->deduction_type) === 0 ? "Percentage" : "Fix Amount" ?>
                                                                            </td>
                                                                            <td>
                                                                                <?=(intval($rs->deduction_type) == 0) ? number_format($rs->percentage, 2).'%' : number_format($rs->fixed_deduction_amt, 2) ?>
                                                                            </td>
                                                                            <td  class="text-center">
                                                                                <?php 
                                                                                    $tempStatus = intval($rs->active);
                                                                                    $badgeColor = "m-badge--warning";
                                                                                    $badgeText = "Suspended";

                                                                                    if($rs->paid == 1 && $tempStatus !== 2){ $tempStatus = 2; }
                                                                                    $_balance = floatval($rs->amount) - floatval($rs->total_amount_paid);
                                                                                    if($_balance <= 0){ $tempStatus = 2; }

                                                                                    switch($tempStatus) {
                                                                                        case 1:
                                                                                            $badgeColor = "m-badge--info";
                                                                                            $badgeText = "Active";
                                                                                            break;
                                                                                        case 2:
                                                                                            $badgeColor = "m-badge--success";
                                                                                            $badgeText = "Paid";
                                                                                            break;
                                                                                        default:
                                                                                            $badgeColor = "m-badge--warning";
                                                                                            $badgeText = "Suspended";
                                                                                            break;
                                                                                    }

                                                                                    echo "<span class='m-badge m-badge--wide m--font-bolder $badgeColor'>$badgeText</span>";
                                                                                ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php 
                                                                                    echo $this->profile->renderLoanActions($rs);
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- payroll sheet and deductions -->
        </div>
    </div>
</div>

<?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($payroll_sheet_data) && is_array($payroll_sheet_data) && count($payroll_sheet_data) > 0)): ?>
    <div id="temp-payslip_content--container">
        <?php $this->load->view("core/profile/modals/payroll_payslip"); ?>
    </div>
    <script>
        const viewPayrollPayslipModal = $("#view-payroll-payslip-modal");
        var vmPayslipContent = new Vue({
            el: "#temp-payslip_content",
            data: { row: {} },
        });

        $(document).ready( function () {
            $("#table-payroll_sheet-payslip").DataTable({ ordering: false });
            $("#table-payroll_sheet-payslip_filter input[type='search']").removeClass("form-control-sm");
        });
        
        const getPayrollSheetData = function(id, $is_printed = 0){
            if(id){
                if ($is_printed == 1) {
                    $.get(siteUrl('core/profile/get_payroll_sheet_data'), { id: id }, "json")
                    .done(function(data){
                        const json = JSON.parse(data);
                        let tempRow = json.response ? Object.assign({}, json.data) : {};
                        vmPayslipContent.row = Object.assign({}, tempRow);
    
                        var data = json.data;
                        var totalOT = parseFloat(vmPayslipContent.row.ot_amount) + parseFloat(vmPayslipContent.row.ot_ndiff_amount);
                        var totalOTHrs = (parseFloat(vmPayslipContent.row.ot_minutes) + parseFloat(vmPayslipContent.row.ot_ndiff_minutes)) / 60;
                        vmPayslipContent.total_ot_hrs = numberFormat(totalOTHrs);
                        vmPayslipContent.ot_hrs = numberFormat(parseFloat(vmPayslipContent.row.ot_minutes)/60);
                        vmPayslipContent.ot_computation = numberFormat(totalOT);
                        vmPayslipContent.ot_ndiff_hrs = numberFormat(parseFloat(vmPayslipContent.row.ot_ndiff_minutes) / 60);
                        vmPayslipContent.ot_ndiff_computation = numberFormat(parseFloat(vmPayslipContent.row.ot_ndiff_amount));
    
                        let tempLoan = [];
                        let tempOthers = [];
                        let totalLoan = parseFloat(vmPayslipContent.row.totalLoan.replace(/,/g, ''));
                        let totalDeduction = 0;
                        let totalOthersDeductions = 0;
                        let overAllTotal = 0;
                        const tempCreatedAdjustments = data.created_adjustments;
    
                        if (data.sss && parseFloat(data.sss) > 0) {
                            totalDeduction = totalDeduction + parseFloat(data.sss.replace(/,/g, ''));
                        }
    
                        if (data.sss_prov && parseFloat(data.sss_prov) > 0) {
                            totalDeduction = totalDeduction + parseFloat(data.sss_prov.replace(/,/g, ''));
                        }
                        
                        if (data.ph && parseFloat(data.ph) > 0) {
                            totalDeduction = totalDeduction + parseFloat(data.ph.replace(/,/g, ''));
                        }
                        
                        if (data.hdmf && parseFloat(data.hdmf) > 0) {
                            totalDeduction = totalDeduction + parseFloat(data.hdmf.replace(/,/g, ''));
                        }
                        
                        if (data.tax && parseFloat(data.tax) > 0) {
                            totalDeduction = totalDeduction + parseFloat(data.tax.replace(/,/g, ''));
                        }
    
                        if (json.data.loans.length > 0) {
                            $.each(json.data.loans, function (index, item) {
                                if (item.loan_name.toLowerCase() != 'charges' && item.loan_name.toLowerCase() != 'under deduction' && item.loan_name.toLowerCase() != 'medical loan') {
                                    var temp_amount = parseFloat(item.amount_due.replace(/,/g, ''));
        
                                    // for adding cash advance with loan adjustments
                                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                                        const created_adjustments = tempCreatedAdjustments.split(",");
                                        var tempAdj = 0;
                                        created_adjustments.forEach((row, i) => {
                                            const temp_adjustment = row.split("||");
                                            const adj_type = parseInt(temp_adjustment[2]);
                                            const temp_status = parseInt(temp_adjustment[3]);
                                            let _temp = parseFloat(item.amount_due);
                                            if (adj_type == 1) {
                                                _temp = parseFloat(temp_amount) + parseFloat(temp_adjustment[1]);
                                            } else {
                                                _temp = parseFloat(temp_amount) - parseFloat(temp_adjustment[1]);
                                            }
    
                                            tempAdj = _temp;
                                            _temp = _temp;
    
                                            if (typeof item.loan_name !== "undefined" && item.loan_name.toLowerCase() == 'cash advance') {
                                                if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                    temp_amount = _temp;
                                                }
                                            } else {
                                                // includes loan adjustments when employee has no cash advance
                                                if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                    if (!tempLoan.some(el => el.loan_name === 'CASH ADVANCE')) {
                                                        tempLoan.push({
                                                            'loan_name' : 'CASH ADVANCE',
                                                            'amount_due' : temp_adjustment[1],
                                                            'loan_type' : adj_type
                                                        });
                                                    }
                                                }
                                            }
                                        });
                                    }
        
                                    tempLoan.push({
                                        'loan_name' : item.loan_name,
                                        'amount_due' : numberFormat(temp_amount),
                                        'loan_type' : item.loan_type
                                    });
                                }
        
                                // for adding the charges to Other Deductions
                                if (item.loan_name.toLowerCase() == 'charges' || item.loan_name.toLowerCase() == 'under deduction' || item.loan_name.toLowerCase() == 'medical loan') {
                                    vmPayslipContent.row.adjustment_deductions.push({
                                        'label' : item.loan_name,
                                        'display_value' : item.amount_due,
                                        'value' : item.amount_due,
                                        'adj_type' : 0
                                    });
        
                                    totalLoan = totalLoan - parseFloat(item.amount_due.replace(/,/g, ''));
                                }
                            });
                        } else {
                            if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                                const created_adjustments = tempCreatedAdjustments.split(",");
                                var tempAdj = 0;
                                created_adjustments.forEach((row, i) => {
                                    const temp_adjustment = row.split("||");
                                    const adj_type = parseInt(temp_adjustment[2]);
                                    const temp_status = parseInt(temp_adjustment[3]);
                                    let _temp = parseFloat(temp_adjustment[1]);
        
                                    tempAdj = _temp;
                                    _temp = formatNumber(_temp);
        
                                    if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                        tempLoan.push({
                                            'loan_name' : 'cash advance',
                                            'amount_due' : _temp,
                                            'loan_type' : adj_type
                                        });
                                    }
                                });
                            }
                        }
    
                        $.each(vmPayslipContent.row.adjustment_deductions, function (index, item) {
                            totalOthersDeductions = totalOthersDeductions + parseFloat(item.display_value.replace(/,/g, ''));
                        });
    
                        console.log(totalDeduction, totalLoan, totalOthersDeductions);
                        overAllTotal = parseFloat(totalDeduction) + parseFloat(totalLoan) + parseFloat(totalOthersDeductions) + parseFloat(vmPayslipContent.row.total_loans_interest);
    
                        vmPayslipContent.row.loans = tempLoan;
                        vmPayslipContent.row.totalLoan = numberFormat(totalLoan);
                        vmPayslipContent.row.total_allowances = numberFormat(vmPayslipContent.row.total_allowances);
                        vmPayslipContent.row.deductions = numberFormat(totalDeduction);
                        vmPayslipContent.row.total_others_deductions = numberFormat(totalOthersDeductions);
                        vmPayslipContent.row.overall_total_deductions = numberFormat(overAllTotal);
                        vmPayslipContent.row.adjustment_d_count = vmPayslipContent.row.adjustment_deductions.length;
    
                        if(json.response){ viewPayrollPayslipModal.modal("show"); }
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Payslip Preview',
                        text: 'Payslip preview cannot be displayed as the payslip is not in printed status.'
                    })
                }
            }
        }

        function formatNumber(value, decimals = 2) {
            return parseFloat(value).toLocaleString("en-US", { maximumFractionDigits: decimals });
        }
    </script>
<?php endif; ?>
<?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($deductions) && is_array($deductions) && count($deductions) > 0)): ?>
    <?php $this->load->view("payroll/employee_profile/modals/loan_payment_history_modal"); ?>
    <script>
        const loanPaymentHistoryModal = $("#loan-payment-history-modal");
        $(document).ready( function () {
            $("#tbl-loans").DataTable({ ordering: false });
            $("#tbl-loans input[type='search']").removeClass("form-control-sm");
        });

        function openLoanPaymentHistoryModal(id) {
            loanPaymentHistoryModal.attr("data-id", id);
            loanPaymentHistoryModal.modal("show");
        }

        loanPaymentHistoryModal.on("show.bs.modal", function () {
            const id = $(this).attr("data-id");

            /** nav tab issue fixes ***/
            const cTab = $("#employee--loan_payment_history .nav-link.active").attr("href");
            $("#employee--loan_payment_history .tab-pane").removeClass("active show");
            $(cTab).addClass("active show");
            
            $("#employee--loan_payment_history .nav-link").on("click", function () {
                const tab = $(this).attr("href");
                $("#employee--loan_payment_history .tab-pane").removeClass("active show");
                $(tab).addClass("active show");
            });
            /** nav tab issue fixes ***/

            $("#tab_payments table", this).DataTable({
                dom: "frtlp",
                serverSide: false,
                destroy: true,
                ajax: {
                    url: baseUrl(`core/profile/get_employee_loan_payment_history/${id}`),
                    type: "GET",
                    dataType: "JSON"
                },
                autoWidth: false,
                columns: [
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">${moment(data.date_start).format("MMM. DD, YYYY")}</span>`
                                + " - " + `<span class="m--font-boldest">${moment(data.date_end).format("MMM. DD, YYYY")}</span>`;
                        }
                    },
                    {
                        width: "30%",
                        data: null,
                        render: function (data, type, row) {
                            return `<div class="m--font-bolder">${row.firstname} ${row.lastname}</div>
                                    <div class="m--regular-font-size-sm1 text-muted">${moment(row.posted_at).format("lll")}</div>`;
                        }
                    },
                    {
                        width: "25%",
                        data: "amount_due",
                        className: "text-right",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">
                                        ${parseFloat(data).toLocaleString("en-US", {maximumFractionDigits: 2})}
                                    </span>`;
                        }
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    const api = this.api();
                    const total = api
                        .column(2)
                        .data()
                        .reduce(function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0);

                    $(api.column(2).footer()).html(
                        `<span class="m--font-boldest m--regular-font-size-lg1">
                            ${parseFloat(total).toLocaleString("en-US", {maximumFractionDigits: 2})}
                        </span>`
                    );
                }
            });

            $("#tab_interest_charges table", this).DataTable({
                dom: "frtlp",
                serverSide: false,
                destroy: true,
                ordering: false,
                ajax: {
                    url: baseUrl(`core/profile/get_employee_loan_iterest_charge_history/${id}`),
                    type: "GET",
                    dataType: "JSON"
                },
                autoWidth: false,
                columns: [
                    {
                        data: "pay_date",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">${moment(row.date_start).format("MMM. DD, YYYY")}</span>`
                                + " - " + `<span class="m--font-boldest">${moment(row.date_end).format("MMM. DD, YYYY")}</span>`;
                        }
                    },
                    {
                        width: "30%",
                        data: null,
                        render: function (data, type, row) {
                            return `<div class="m--font-bolder">${row.firstname} ${row.lastname}</div>
                                    <div class="m--regular-font-size-sm1 text-muted">${moment(row.posted_at).format("lll")}</div>`;
                        }
                    },
                    {
                        width: "15%",
                        data: "amount_due",
                        className: "text-right",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">
                                        ${parseFloat(data).toLocaleString("en-US", {maximumFractionDigits: 2})}
                                    </span>`;
                        }
                    },
                    {
                        width: "15%",
                        data: "total_interest_amount",
                        className: "text-right",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">
                                        ${parseFloat(data).toLocaleString("en-US", {maximumFractionDigits: 2})}
                                    </span>`;
                        }
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    const api = this.api();
                    const total = api
                        .column(3)
                        .data()
                        .reduce(function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0);

                    $(api.column(3).footer()).html(
                        `<span class="m--font-boldest m--regular-font-size-lg1">
                            ${parseFloat(total).toLocaleString("en-US", {maximumFractionDigits: 2})}
                        </span>`
                    );
                }
            });

            $.ajax({
                url : baseUrl(`core/profile/get_employee_loan_remarks/${id}`),
                type: "GET",
                dataType: "JSON",
                success: function(response){
                    $("#_for_remarks").text(response.remarks);
                }
            });
        });

    </script>
<?php endif; ?>