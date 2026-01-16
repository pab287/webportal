<style>
    @media (min-width: 768px) {
        .modal-full {
            width: 100%;
            max-width: 90%;
        }
    }

    .table-cb {
        display: inline;
        padding-left: 16px;
    }

    .table-cb span {
        height: 12px;
        width: 12px;
        display: inline;
    }

    .table-cb span:after {
        width: 3px;
        height: 8px;
    }

    .no-sort:before, .no-sort:after {
        display: none !important;
    }

    .custom-adjustment-total-divider {
        border-bottom: 1px dotted grey;
        display: inline-block;
        width: auto;
        padding-bottom: 2px;
    }

    .m-portlet .input-group .form-control{ z-index: auto; }
    .modal .m-dropdown { position: absolute; }

    @media screen and (min-width: 1200px) {
        #view-timesheet-modal .modal-dialog {
            max-width: 60%;
        }
    }
    table tr.m--bg-ps_existing--posted{ background-color: #ffe0e0 !important; }

    /* HTML: <div class="loader"></div> */
    .pulse {
        font-size: 1.1rem;
        color: #008000; /* lock color */
        animation: lockPulse 1.5s infinite ease-in-out;
    }

    @keyframes lockPulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.6;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-payroll_sheet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Action Tools
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li id="tempFilterHistory" class="m-portlet__nav-item">
                                <template v-if="count > 0">
                                    <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" @click="renderModalHistory()">
                                        <span data-toggle="m-tooltip" data-placement="top" data-original-title="Search Filter History" data-skin="white">
                                            <i class="la la-rotate-left"></i>
                                        </span>
                                    </a>
                                </template>
                            </li>
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);"  data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                    <i class="la la-angle-down"></i>
                                </a>
                            </li>
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon m-dropdown__toggle">
                                    <i class="la la-ellipsis-v"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__section m-nav__section--first">
                                                        <span class="m-nav__section-text">
                                                            Quick Actions
                                                        </span>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnAdvance_search" onclick="toggleParameterModal()">
                                                            <i class="m-nav__link-icon la la-cog pr-1"></i>
                                                            <span class="m-nav__link-text">
                                                                Parameters
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnAdvance_search" onclick="tempRegeneratePayroll()">
                                                            <i class="m-nav__link-icon la la-search pr-1"></i>
                                                            <span class="m-nav__link-text">
                                                                Generate
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                <div class="m-form m-form--label-align-right m--margin-bottom-30">
                        <div class="align-items-center">
                            <form class="m-form has-validation-callback" id="frm-filter">
                                <div class="row">
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label class="required mb-1" style="font-weight: 600;">Company</label>
                                            <select name="company" id="company" class="form-control" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label for="" class="required mb-1" style="font-weight: 600;">
                                                Payout Classification
                                            </label>
                                            <select class="form-control" id="payout_schedule" data-validation="required" name="payout_schedule">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label for="" class="required mb-1" style="font-weight: 600;">
                                                Sequence
                                            </label>
                                            <select class="form-control" id="payroll_sequence"
                                                    data-validation="required" name="sequence">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 pr-0 mt-2">
                                        <div class="form-group m-form__group">
                                            <label class="required mb-1" style="font-weight: 600;">Pay Date</label>
                                            <div class="input-group date" id="pay-date">
                                                <input type="text" class="form-control m-input"
                                                    data-validation="required"
                                                    name="pay_date" autocomplete="off"
                                                    placeholder="MMM. DD, YYYY">
                                                <span class="input-group-addon">
													<i class="la la-calendar-check-o"></i>
												</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label for="date-range" class="required mb-1"
                                                style="font-weight: 600;">
                                                SELECT DATE RANGE
                                            </label>
                                            <div class="input-group" id="date-picker">
                                                <input type="text" class="form-control m-input" readonly=""
                                                placeholder="MMM. DD, YYYY - MMM. DD, YYYY"
                                                data-validation="required" id="date-range"
                                                name="date_range">
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-xl-3 col-lg-3 col-md-3 text-right">
                                        <button type="button" class="btn btn-warning m-btn m-btn--icon btnAdvance_search m--margin-top-25 text-white" 
                                            data-toggle="modal"
                                            data-target="#payroll-parameters-modal"
                                            data-toggle="m-tooltip" data-original-title="Set Payroll parameters"
                                            data-skin="dark"
                                            data-delay='{"show": 500}'>
                                            <span>
                                                <i class="la la-cog pr-1"></i>
                                                Parameters
                                            </span>
                                        </button>
                                        <button type="submit"
                                            class="btn btn-info m-btn m-btn--icon btnAdvance_search m--margin-top-25">
                                            <span><i class="fa fa-search"></i><span>GENERATE</span></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-xl-11 col-lg-11 col-md-11 col-sm-11 mt-2">
                                        <div class="form-group m-form__group">
                                            <label for="payroll_group" class="mb-1" style="font-weight: 600;">
                                                PAYROLL GROUP
                                                <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                            </label>
                                            <select class="form-control" id="payroll_group" multiple="multiple"></select>
                                        </div>
                                    </div>
                                    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 ">
                                        <button type="button" class="btn btn-warning btnReset btnAdvance_search pull-right m--margin-top-25 text-white" onclick="resetFilter(this)">
                                                <span><i class="fa fa-refresh "></i>RESET</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label class="mb-1" style="font-weight: 600;">
                                                Employee 
                                                <span class="m-form__help" style="text-transform: none; font-width: 600;">(Optional)</span>
                                            </label>
                                            <select name="employees[]" id="employees" class="form-control"
                                            multiple="multiple"></select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm mb-0" data-portlet="true">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Payroll Sheet
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul id="ps--notification" class="m-portlet__nav">
                            <li class="m-portlet__nav-item" v-if="count > 0">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" :class="notification_clicked === false ? 'm-animate-shake':''" 
                                    data-toggle="modal" data-target="#modal-ps--notification" @click="showModalNotification">
                                    <span data-toggle="m-tooltip" data-placement="top" data-original-title="Notifications" data-skin="dark">
                                        <i class="flaticon-music-1 m--font-danger"></i>
                                    </span>
                                </a>
                            </li>
                            <li class="m-portlet__nav-item" v-if="contribution_count > 0">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" :class="contribution_clicked === false ? 'm-animate-shake':''" 
                                    data-toggle="modal" data-target="#modal-ps--contribution_report" @click="tickContributionNotification">
                                    <span data-toggle="m-tooltip" data-placement="top" data-original-title="Contribution Report" data-skin="dark">
                                        <i class="flaticon-piggy-bank m--font-warning"></i>
                                    </span>
                                </a>
                            </li>
                            <li class="m-portlet__nav-item" v-if="count > 0">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon m-animate-fade-in"
                                    data-toggle="modal" data-target="#modal-ps--site_location_report">
                                    <span data-toggle="m-tooltip" data-placement="top" data-original-title="Site Location Report" data-skin="dark">
                                        <i class="flaticon-map-location m--font-brand"></i>
                                    </span>
                                </a>
                            </li>
                            <li class="m-portlet__nav-item" v-if="absentee_count > 0">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" data-toggle="modal" data-target="#modal-ps--absentee_report">
                                    <span data-toggle="m-tooltip" data-placement="top" data-original-title="Attendance Report" data-skin="dark">
                                        <i class="flaticon-file-1 m--font-brand"></i>
                                    </span>
                                </a>
                            </li>
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" data-portlet-tool="fullscreen" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                    <i class="la la-expand"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-bottom-30">
                        <div class="align-items-center">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-left d-flex flex-row">
                                    <div class="m-form__group form-group">
                                        <label for="">Show Entries</label>
                                        <div class="m-checkbox-inline">
                                            <label class="m-checkbox">
                                                <input type="radio" class="show_posted_record" name="show_posted" value="_all" checked="checked" />ALL
                                                <span></span>
                                            </label>
                                            <label class="m-checkbox">
                                                <input type="radio" class="show_posted_record" name="show_posted" value="posted">POSTED
                                                <span></span>
                                            </label>
                                            <label class="m-checkbox">
                                                <input type="radio" class="show_posted_record" name="show_posted" value="unposted">UNPOSTED
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 text-right d-flex flex-row align-items-end justify-content-end">
                                    <div class="flex-grow-0 flex-shrink-0 mr-2">
                                        <button type="button" class="btn btn-success btnSave m-btn m-btn--icon"
                                                onclick="confirmPosting()">
                                            <span>
                                                <i class="fa fa-check"></i>
                                                <span class="m--font-boldest">Post</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="flex-grow-0 flex-shrink-0 mr-2">
                                        <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                                onclick="printPayrollSheet(this)">
                                            <span>
                                                <i class="fa fa-print"></i>
                                                <span class="m--font-boldest">Print</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="flex-grow-0 flex-shrink-0">
                                        <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                                onclick="exportExcelPayrollSheet(this)">
                                            <span>
                                                <i class="fa fa-download"></i>
                                                <span class="m--font-boldest">Export Excel</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive-sm">
                            <table class="table table-bordered" id="table-payroll-sheet" style="width: 100%">
                                <thead>
                                <tr>
                                    <th scope="col" class="no-sort align-middle" rowspan="2">
                                        <label for="cb-select-all" class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                            <input type="checkbox" id="cb-select-all">
                                            <span></span>
                                        </label>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">#</th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">EMPLOYEE</th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">RATE</th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip" id="allow"
                                            data-placement="top"
                                            data-original-title="STANDARD ALLOWANCE"
                                            data-skin="dark">
                                            ALLOW
                                        </span></th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                            data-placement="top"
                                            data-original-title="DAYS WORKED"
                                            data-skin="dark">
                                            DAYS
                                        </span>
                                    </th>
                                    <th scope="col" rowspan="2" class="align-middle">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="OVERTIME (AMT)"
                                              data-skin="dark">
                                            OT
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="OVERTIME NIGHT DIFFERENTIAL (AMT)"
                                              data-skin="dark">
                                            OT.ND
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="HOLIDAY"
                                              data-skin="dark">
                                            HOL
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="REGULAR NIGHT DIFFERENTIAL (AMT)"
                                              data-skin="dark">
                                            R.ND
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="BASIC PAY"
                                              data-skin="dark">
                                            BASIC
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="ALLOWANCE"
                                              data-skin="dark" id="allowance">
                                            ALLOWANCE
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="OT ALLOWANCE"
                                              data-skin="dark" id="ot_allowance">
                                            OT.ALLW
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="ADJUSTMENTS"
                                              data-skin="dark" id="adjustment">
                                            ADJUSTMENTS
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="GROSS PAY"
                                              data-skin="dark">
                                            GROSS
                                        </span>
                                    </th>
                                    <th scope="col" class="align-middle" rowspan="2">SSS</th>
                                    <th scope="col" class="align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="SSS PROVIDENT"
                                              data-skin="dark">
                                              S.PROV
                                        </span></th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="PHILHEALTH"
                                              data-skin="dark">
                                            PHIC
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="PAG-IBIG"
                                              data-skin="dark">
                                            HDMF
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">TAX</th>
                                    <th scope="col" class="text-center" colspan="4">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="LOANS"
                                              data-skin="dark">
                                                LOANS
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center align-middle" rowspan="2">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="NET PAY"
                                              data-skin="dark">
                                            NET PAY
                                        </span>
                                    </th>
                                    <th scope="col" rowspan="2"></th>
                                </tr>
                                <tr>
                                    <th scope="col" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="COMPANY LOANS"
                                              data-skin="dark">
                                            C.A.
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="TOTAL CHARGES"
                                              data-skin="dark">
                                            CHRG
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center">
                                        <span data-toggle="m-tooltip"
                                            data-placement="top"
                                            data-original-title="SSS LOANS"
                                            data-skin="dark">
                                            SSS
                                        </span>
                                    </th>
                                    <th scope="col" class="text-center">
                                        <span data-toggle="m-tooltip"
                                            data-placement="top"
                                            data-original-title="HDMF LOANS"
                                            data-skin="dark">
                                            HDMF
                                        </span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="m--hide">
                                    <tr>
                                        <th scope="col" class="m--font-boldest" colspan="6">GRAND TOTAL</th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col" colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="portlet--signatories" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <template v-if="count > 0">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm mb-0 mt-2">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Printable Signatories
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" class="btnEdit m-portlet__nav-link m-portlet__nav-link--icon" @click="openModalSignatory()">
                                    <i class="la la-pencil"></i>
                                </a>
                            </li>
                            <template v-if="row.allow_reset === true">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);" class="btnEdit m-portlet__nav-link m-portlet__nav-link--icon" @click="resetModalSignatory()">
                                        <i class="la la-refresh"></i>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <template v-if="count > 0">
                        <div class="row justify-content-center">
                            <template v-for="(item, index) in row.meta_field">
                                <template v-if="item.is_active === true">
                                    <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                                        <div class="form-group m-form__group text-center">
                                            <label class="m--font-boldest">{{item.label}}</label>
                                            <p class="m--font-bolder mb-0">{{item.value}}</p>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>
                                    NO ASSIGNED SIGNATORIES!
                                </strong>
                                Please add/update the signatory.
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            </template>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-payroll">
    <form id="frm-payroll">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payroll sheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered row-border"
                               id="table-payroll-sheet" width="100%">
                            <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Rate</th>
                                <th>Days</th>
                                <th>Legal Holidays</th>
                                <th>Under Time Amount</th>
                                <th>Amount</th>
                                <th>Over Time Amount</th>
                                <th>Over Time Hours</th>
                                <th>N.Different'l Amount</th>
                                <th>N.Different'l Hours</th>
                                <th>Other/Adj2</th>
                                <th>ECOLA</th>
                                <th>Gross Pay</th>
                                <th>SSS</th>
                                <th>---</th>
                                <th>Pagibig</th>
                                <th>Tax</th>
                                <th>Loans</th>
                                <th>Net Due</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="confirm-payroll-posting">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Posting</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0 m--regular-font-size-lg3 m--font-bolder">Are you sure to post selected record?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSave" onclick="postPayrollSheet()">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="confirm-undo-posting">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('payroll/confirm_undo_posting') ?>"
                data-url="<?= base_url('payroll/confirm_undo_posting') ?>"
                id="frm-undo-posting">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Undo Posting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 m--regular-font-size-lg3 m--font-bolder"></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnUndo_posting">Yes</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-confirm-approval-created-adjustment">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="frm-approval-created-adjustments" action="<?= base_url('payroll/confirm_adjustment_approval') ?>">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" value="0" id="adj_id" />
                <input type="hidden" name="status" value="0" id="adj_status" />
                <div class="modal-header">
                    <h5 class="modal-title">Payroll Sheet Adjustment Approval</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 m--regular-font-size-lg3 m--font-bolder">Are you sure you want to <span id="temp_status">approve</span> this adjustment?</p>
                    <div class="form-group mt-1">
                        <label for="">Remarks *</label>
                        <textarea name="approval_remarks" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">Yes</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--notification">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="tempPsNotificationContent">
                <template v-if="count > 0">
                    <h5><span class="m--font-danger">{{count}}</span> <span class="text-muted">Employee(s) without contribution/deduction account number</span></h5>
                    <div class="m-widget3 mt-4">
                        <div class="m-widget3__item" v-for="(item, index) in rows">
                            <div class="m-widget3__body mb-2">
                                <div class="row">
                                    <div class="col-4 col-md-4 col-lg-4 col-xl-4 col-sm-12">
                                        <span class="m--font-bolder">{{item.name}}</span>
                                    </div>
                                    <div class="col-8 col-md-8 col-lg-8 col-xl-8 col-sm-12">
                                        <template v-for="(ii, ee) in item.contribution_account">
                                            <span class="m-badge m-badge--danger m-badge--wide m-badge--rounded mr-1">{{ii}}</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--absentee_report">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet - Attendance Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="tempPsAbsenteeContent">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="table-absentee_report" class="table table-striped table-bordered row-border" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--contribution_report">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet - Existing Contribution Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="tempPsContributionContent">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="table-contribution_report" class="table table-striped table-bordered row-border" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--site_location_report">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet - Employee Site Location Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="table-site_location_report" class="table table-striped table-bordered row-border" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--filter_history">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet - Filter History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="filterHistoryContent" class="modal-body">
                <template v-if="count > 0">
                    <div class="m-widget4">
                        <div class="m-widget4__item" v-for="item in rows">
                            <div class="m-widget4__info pl-0">
                                <div class="row">
                                    <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                        <p class="m-widget4__title mb-0">{{item.company_code}}</p> 
                                        <p class="m-widget4__text mb-0">{{item.payout_description}} - SEQUENCE {{item.payout_sequence}} </p>
                                    </div>
                                    <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                        <p class="m-widget4__title mb-0">PAYDATE - {{item.pay_date}}</p> 
                                        <p class="m-widget4__text mb-0">{{item.date_range}}</p>
                                    </div>
                                </div>	
                                <div class="row mt-2" v-if="item.group_count > 0">
                                    <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                        <p class="m-widget4__title mb-0">PAYROLL GROUP</p> 
                                    </div>						 		 
                                    <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                        <template v-for="group in item.group_collection">
                                            <span class="m-badge m-badge--brand m-badge--wide mr-1 mb-1">{{group.text}}</span>
                                        </template>
                                    </div>						 		 
                                </div>
                                <div class="row mt-2" v-if="item.employees_count > 0">
                                    <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                        <p class="m-widget4__title mb-0">EMPLOYEE</p> 
                                    </div>						 		 
                                    <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                        <template v-for="emp in item.employees_collection">
                                            <span class="m-badge m-badge--success m-badge--wide mr-1 mb-1">{{emp.text}}</span>
                                        </template>
                                    </div>						 		 
                                </div>
                            </div>
                            <div class="m-widget4__ext">
                                <a class="m-btn m-btn--pill m-btn--hover-brand btn btn-sm btn-secondary btnAdvance_search" @click="generateFilterHistory(item)">SET FILTER</a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--signatory">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updatePrintableSignatories" method="post" action="<?php echo site_url("payroll/update_printable_signatories"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.id" />
                <input type="hidden" name="signatory_id" v-model="row.signatory_id" />
                <input type="hidden" name="user_id" v-model="row.user_id" />
                <div class="modal-body">
                    <template v-if="count > 0">
                        <template v-for="(item, index) in row.meta_field">
                        <div class="form-group m-form__group row">
                            <label class="col-3 col-form-label">{{item.label}}</label>
                            <div class="col-8">
                                <select class="form-control m-input select2--value" 
                                    data-validation="required" 
                                    :name="'value['+index+']'" 
                                    :disabled="item.is_active === false">
                                    <option :value="item.value" selected>{{item.value}}</option>
                                </select>
                            </div>
                            <div class="col-1 text-right">
                                <span class="m-switch m-switch--sm">
                                    <label>
                                        <input type="checkbox" :checked="item.is_active === true" @click="activeSignatory(event)" />
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>
                                    NO ASSIGNED SIGNATORIES!
                                </strong>
                                Please add/update the signatory.
                            </div>
                        </div>
                    </template>
                </div>
                <template v-if="count > 0">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Update</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--reset-signatory">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="reset-signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Reset - Payroll Sheet Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resetPrintableSignatories" method="post" action="<?php echo site_url("payroll/reset_printable_signatories"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.id" />
                <div class="modal-body">
                    <h4>Are you sure you want to reset the current signatories?</h4>
                    <template v-if="count > 0">
                        <template v-for="(item, index) in row.meta_field">
                        <div class="form-group m-form__group row m--marginless" v-if="item.is_active === true">
                            <label class="col-4 col-form-label">{{item.label}}</label>
                            <label class="col-8 col-form-label m--font-bolder">{{item.value}}</label>
                        </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>
                                    NO ASSIGNED SIGNATORIES!
                                </strong>
                                Please add/update the signatory.
                            </div>
                        </div>
                    </template>
                </div>
                <template v-if="count > 0">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Reset</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>

<!-- for selecting of employees with cash advance loan -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--with-loans">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-emp-loans">
                <div class="modal-header">
                    <h5 class="modal-title">Employee(s) with loan</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div id="tempCashAdvanceLoans">
                    <div class="modal-body">
                        <template v-if="tempLoansCount > 0">
                            <div class="row mb-3">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">{{tempLoansCount}} Employee(s) with Cash Advance Loan.</div>
                            </div>
                            <div class="m-alert m-alert--outline alert alert-danger" role="alert">
                                <strong>Note!</strong> The switch action button will activate the cash advance loan deduction.
                            </div>
                            <template v-for="(tempLoansRow) in tempLoansRows">
                                <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded m--margin-bottom-5">
                                    <div class="m-portlet__body m--padding-bottom-10">
                                        <div class="m-form__group form-group row align-items-center m--margin-bottom-0">
                                            <label class="col-8 col-sm-6 col-form-label m--font-bolder">
                                                {{tempLoansRow.employee}}
                                            </label>
                                            <div class="col-4 col-sm-6 text-right">
                                                <span class="m-switch m-switch--sm">
                                                    <label data-toggle="m-tooltip" data-placement="bottom" title="" data-original-title="Activate Cash Advance Loan">
                                                        <!-- <input type="checkbox" class="loans-active" v-bind:name="'loan_activate['+tempLoansRow.id+']'"> -->
                                                        <input type="checkbox" class="loans-active" name="loan_activate[]" :value="tempLoansRow.id">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                                <div class="m-widget4">
                                                    <div class="m-widget4__item m--padding-top-5 m--padding-bottom-5">
                                                        <div class="m-widget4__ext">							 
                                                            <span class="m-widget4__icon m--font-brand">
                                                                <i class="flaticon-coins"></i>
                                                            </span>
                                                        </div>
                                                        <div class="m-widget4__info">
                                                            <p class="m-widget4__text m--marginless">
                                                                Loan Amount <span class="m-widget4__number m--font-success m--margin-left-15 m--font-boldest">{{tempLoansRow.current_loan.amount_formatted}}</span>
                                                                <span class="m-badge m-badge--wide m--margin-left-15" :class="tempLoansRow.current_loan.merged_count > 0 ?'m-badge--success':'m-badge--brand'">{{ tempLoansRow.current_loan.merged_count > 0 ? 'merged':'new' }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="m-widget4__ext">&nbsp;</div>
                                                    </div>
                                                    <div class="m-widget4__item m--padding-top-5 m--padding-bottom-5" v-if="tempLoansRow.has_previous_data === true">
                                                        <div class="m-widget4__ext">							 
                                                            <span class="m-widget4__icon m--font-brand">
                                                                <i class="flaticon-coins"></i>
                                                            </span>
                                                        </div>
                                                        <div class="m-widget4__info">
                                                            <p class="m-widget4__text m--marginless">
                                                                Current Balance <span class="m-widget4__number m--font-info m--margin-left-15 m--font-boldest">{{tempLoansRow.previous_data.balance_amt_formatted}}</span>
                                                            </p>
                                                            <p class="m-widget4__sub m--font-danger m--font-bolder m--marginless">
                                                                Merging of loans will create a new loan data!
                                                            </p> 
                                                        </div>
                                                        <div class="m-widget4__ext">
                                                            <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-danger btn btn-sm btn-danger" @click="mergeEvent(tempLoansRow.employee, tempLoansRow.current_loan, tempLoansRow.previous_data)">Merge</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <template v-else>
                            <p>Loan/s data not found!</p>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <template v-if="tempLoansCount > 0">
                            <button type="submit" id="add-emp-loans" class="btn btn-primary btnAdvance_search">Submit</button>
                        </template>
                        <button type="button" id="cancel-emp-loans" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- for selecting of employees with cash advance loan -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--existing-payroll_sheet">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="form-existing_ps" class="form">
                <div class="modal-header">
                    <h5 class="modal-title">Payroll Sheet Conflict</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div id="tempExistingPayrollSheet">
                        <template v-if="count > 0">
                        <table class="table table-striped m-table table-sm mb-5">
                            <thead>
                                <tr>
                                    <th>Pay Date</th>
                                    <th>Coverage Date / Cut-Off</th>
                                    <th class="text-center">Sequence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ filter.pay_date }}</td>
                                    <td>{{ filter.date_range }}</td>
                                    <td class="text-center">{{ renderNumberOrdinal(filter.payout_sequence) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="hidden" name="payroll_sched" v-model="filter.payout_schedule" />
                        <input type="hidden" name="payroll_seq" v-model="filter.payout_sequence" />
                        <input type="hidden" name="pay_date" v-model="filter.pay_date" />
                        <input type="hidden" name="coverage_date" v-model="filter.date_range" />
                        
                        <template v-if="conflict_payroll_sheet === 1">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                <h5 class="m-0 text-center">Existing Payroll Sheet Data</h5>
                            </div>
                            <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                <table class="table table-striped m-table">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Pay Date</th>
                                            <th>Coverage Date / Cut-off</th>
                                            <th class="text-center">Classification</th>
                                            <th class="text-center">Sequence</th>
                                            <th class="text-center">Posted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in rows">
                                            <td>{{ item.employee_name }}</td>
                                            <td>{{ dateFormatted(item.pay_date) }}</td>
                                            <td>{{ dateFormatted(item.date_start) }} - {{ dateFormatted(item.date_end) }}</td>
                                            <td class="text-center">{{ item.payroll_schedule }}</td>
                                            <td class="text-center">{{ renderNumberOrdinal(item.payroll_seq) }}</td>
                                            <td class="text-center" v-html="isPostedIcon(item.posted)">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </template>

                        </template>
                    </div>

                    <div id="toUpdatePayrollSheet" class="m--hide">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                <h5 class="m-0 text-center">Existing Payroll Sheet Data</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
                                <table class="table table-striped m-table" id="tbl--existing_ps_data" width="100%" style="width: 100%;">
                                    <col width="2%">
                                    <col width="*">
                                    <col width="15%">
                                    <col width="20%">
                                    <col width="15%">
                                    <col width="10%">
                                    <col width="10%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Employee Name</th>
                                            <th class="text-center">Pay Date</th>
                                            <th>Coverage Date / Cut-Off</th>
                                            <th class="text-center">Classification</th>
                                            <th class="text-center">Sequence</th>
                                            <th class="text-center">Posted</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="toUpdateAction"  v-if="show_action === true">
                        <button type="submit" class="btn btn-primary btnUpdate">Update</button>
                    </div>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$this->load->view("modals/payroll_parameters_modal");
$this->load->view("modals/create_adjustment_modal");
$this->load->view("modals/create_custom_adj_modal");
$this->load->view("modals/custom_rate_adjustment_modal");
$this->load->view("modals/view_timesheet_modal");
?>
