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
                                        <div class="col-xl-6">
                                            <span>Position:</span>
                                        </div>
                                        <div class="col-xl-6 text-xl-right text-lg-right text-sm-center">
                                            <span class="m--font-bolder" v-text="main.position"></span>
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
                                    <div class="mt-1 text-sm-center text-xl-left text-lg-left row">
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
                                        <div>
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
                                :href="baseUrl() + '/hris/masterfile/edit_employee_masterfile/' + main.id">
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

            <?php if(isset($profile_payroll_sheet, $show_payroll_payslip) && $show_payroll_payslip && $profile_payroll_sheet && (isset($payroll_sheet_data) && is_array($payroll_sheet_data) && count($payroll_sheet_data) > 0)): ?>
            <div class="m-portlet">
                <div class="m-portlet__head pt-4 pb-4">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="flaticon-file-1"></i>
                            </span>
                            <h3 class="m-portlet__head-text">Payroll Data Sheet</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
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
                                                                <?php endif; ?>
                                                        </p>
                                                    </td>
                                                    <td><?php echo date("F d, Y", strtotime($value->date_start)) ." ~ ". date("F d, Y", strtotime($value->date_end)); ?></td>
                                                    <td class="text-right"><?php echo number_format($value->gross_pay, 2, ".", ","); ?></td>
                                                    <td class="text-right"><?php echo number_format($value->net_pay, 2, ".", ","); ?></td>
                                                    <td class="text-center">
                                                    <?php if($value->is_bonus == 0): ?>
                                                        <button type="button" class="btn btn-secondary btn-sm m-btn m-btn--icon m-btn--icon-only btnView_own_request" onClick="getPayrollSheetData(<?= $value->id; ?>)"><i class="la la-file-text"></i></button>
                                                    <?php else: ?>
                                                        <i class="la la-file-text"></i>
                                                    <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <?php $this->load->view("core/profile/modals/payroll_payslip"); ?>
                                <script>
                                    const viewPayrollPayslipModal = $("#view-payroll-payslip-modal");

                                    var vmPayslipContent = new Vue({
                                        el: "#temp-payslip_content",
                                        data: { row: {} },
                                    });

                                    $("#table-payroll_sheet-payslip").DataTable({ ordering: false });
                                    $("#table-payroll_sheet-payslip_filter input[type='search']").removeClass("form-control-sm");
                                    const getPayrollSheetData = function(id){
                                        if(id){
                                            $.get(siteUrl('core/profile/get_payroll_sheet_data'), { id: id }, "json")
                                            .done(function(data){
                                                const json = JSON.parse(data);
                                                let tempRow = json.response ? Object.assign({}, json.data) : {};
                                                vmPayslipContent.row = Object.assign({}, tempRow);
                                                if(json.response){ viewPayrollPayslipModal.modal("show"); }
                                            });
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
