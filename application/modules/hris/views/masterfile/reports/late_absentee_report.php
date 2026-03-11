<style>
    li.select2-selection__choice {
        white-space: pre-line;
        max-width: 90%;
        line-height: 20px;
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
        <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-late_absentee_report">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <a href="javascript:void(0);" id="toggleCollapse" style="text-decoration: none">
                            <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                                <h3 class="m-portlet__head-text">Late and Absentee Report <small>Filter Options</small></h3>
                            </div>
                        </a>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon" title="Collapse" data-original-title="Collapse">
                                    <i class="la la-angle-down"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-bottom-30">
                        <div class="align-items-center">
                        <form id="frm-filter-hris-late_absentee_report" class="m-form" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div id="tempFilterByLateAbsenteeReport">
                                <div class="row mb-3">
                                    <div class="col-sm-12 col-md-3 col-xl-3 col-lg-3">
                                        <div class="form-group m-form__group has-success">
                                            <label class="m--font-bolder" for="">REPORT TYPE</label>
                                            <div class="m-checkbox-inline">
                                                <label class="m-checkbox">
                                                    <input type="radio" id="late_report" name="report_type" value="late" data-validation="required" v-model="report_type" />
                                                    LATE REPORT <span></span>
                                                </label>
                                                <label class="m-checkbox">
                                                    <input type="radio" id="absentee_report" name="report_type" value="absentee" data-validation="required" v-model="report_type" />
                                                    ABSENTEE REPORT <span></span>
                                                </label>
                                                <label class="m-checkbox">
                                                    <input type="radio" id="late_absentee_report" name="report_type" value="late_absentee" data-validation="required" v-model="report_type" />
                                                    Late & Absentee Report <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-2 col-xl-2 col-lg-2">
                                        <div class="form-group m-form__group has-success">
                                            <label class="m--font-bolder" for="">FILTER BY</label>
                                            <div class="m-checkbox-inline">
                                                <label class="m-checkbox">
                                                    <input type="radio" id="ranged_filter" name="filter_by" value="date_range" data-validation="required" v-model="filter_by" />
                                                    DATE RANGE<span></span>
                                                </label>
                                                <label class="m-checkbox">
                                                    <input type="radio" id="monthly_filter" name="filter_by" value="month" data-validation="required" v-model="filter_by" />
                                                    MONTH<span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-3 col-xl-3 col-lg-3">
                                        <template v-if="filter_by === 'date_range'">
                                        <div class="form-group m-form__group" id="filter-by-date-range">
                                            <label class="m--font-bolder required" for="date-range">SELECT DATE RANGE</label>
                                            <div class="input-group" id="date-picker">
                                                <input type="text" class="form-control m-input" readonly=""
                                                    placeholder="MMM DD, YYYY - MMM DD, YYYY"
                                                    id="date-range"
                                                    name="date_range" data-validation="required" />
                                                <span class="input-group-addon">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                        </template>
                                        <template v-else>
                                        <div class="row">
                                            <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 m-animate-fade-in">
                                                <label for="" class="required m--font-bolder">MONTH</label>
                                                <select class="form-control" name="filter_month" data-validation="required">
                                                    <option></option>
                                                </select>
                                            </div>
                                            <div class="form-group col-xl-5 col-lg-5 col-md-5 col-sm-12 pl-0 m-animate-fade-in">
                                                <label for="" class="required m--font-bolder">YEAR</label>
                                                <select class="form-control" name="filter_year" data-validation="required">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6">
                                        <div class="form-group m-form__group has-success">
                                            <label class="m--font-bolder" for="">Employee Status</label>
                                            <div class="m-checkbox-inline">
                                                <label class="m-checkbox">
                                                    <input type="radio" id="all_emp" name="employee_status" value="all" data-validation="required" checked/>
                                                    All Employees <span></span>
                                                </label>
                                                <label class="m-checkbox">
                                                    <input type="radio" id="active_emp" name="employee_status" value="active" data-validation="required" />
                                                    Active Employees <span></span>
                                                </label>
                                                <label class="m-checkbox">
                                                    <input type="radio" id="inactive_emp" name="employee_status" value="inactive" data-validation="required" />
                                                    Inactive Employees <span></span>
                                                </label>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 col-md-3 col-xl-3 col-lg-3">
                                        <div class="form-group m-form__group">
                                            <label for="company" class="m--font-bolder required">COMPANY</label>
                                            <select name="company" id="company" class="form-control" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-5 col-xl-5 col-lg-5">
                                        <div class="form-group m-form__group">
                                            <label for="department">
                                                DEPARTMENT <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                            </label>
                                            <select class="form-control" id="department" name="department" disabled>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 col-lg-4 col-md-4 text-right">
                                        <button type="button" onclick="submitLateAbsenteeFilterForm(this)" class="btn btn-info m-btn m-btn--icon btnAdvance_search btn-submit m--margin-top-25">
                                            <span><i class="fa fa-search"></i><span>SEARCH</span></span>
                                        </button>
                                        <button type="button" onclick="resetFilterLateAbsenteeReport(this)" class="btn btn-warning btnReset btnAdvance_search pull-right m--margin-top-25 m--margin-left-5 text-white">
                                            <span><i class="fa fa-refresh "></i> RESET FILTER</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-xl-12 col-lg-12 mb-3">
                                        <div class="form-group m-form__group">
                                            <label for="payroll_group">
                                                PAYROLL GROUP <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                            </label>
                                            <select class="form-control" id="payroll_group" name="payroll_group[]" multiple disabled></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-xl-12 col-lg-12">
                                        <div class="form-group m-form__group">
                                            <label for="employee" class="m--font-bolder">Employee <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span></label>
                                            <select id="employee" class="form-control" name="employee[]" multiple disabled>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded">
                <div id="filteredLateReport" class="m-portlet__body">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                        <table class="table table-striped table-bordered" id="table-late_absentee_report" style="width: 100%">
                            <!-- <thead>
                                <tr>
                                    <th style="width: 8%">ID Number</th>
                                    <th style="width: 30%">Employee Name</th>
                                    <th style="width: *">Position</th>
                                    <th style="width: 8%">Total</th>
                                    <th style="width: 6%">&nbsp;</th>
                                </tr>
                            </thead> -->
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-7 col-md-7 col-lg-7 col-sm-12">
                            <div class="alert alert-danger m-alert m-alert--air m-alert--outline mb-0 mt-3" role="alert">
                                <strong>Note!</strong> The late/absentee attendance record(s) listed are all time sheet reference data.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLateAbsenteePreview" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div id="modalLateAbsenteeContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-trash mr-2"></i>Attendance Preview</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-9 col-md-9 col-lg-9 col-sm-12">
                            <h4 v-text="row.employee_name">&nbsp;</h4>
                            <p v-text="row.position">&nbsp;</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3 col-md-3 col-lg-3 col-sm-12">
                            <h6>Date Hired</h6>
                            <p v-text="dateFormatted(row.date_start)">&nbsp;</p>
                        </div>
                        <div class="col-3 col-md-3 col-lg-3 col-sm-12">
                            <h6>Last Verified Date</h6>
                            <p v-text="dateFormatted(row.max_date)">&nbsp;</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12" v-for="log in attlogs">
                            <div class="m-alert m-alert--outline alert text-center" :class="backgroundClass(log)" role="alert">
                                <p v-text="log">&nbsp;</p>
                                <p class="m--regular-font-size-lg2 m--font-boldest mb-0" v-if="report_type == 'absentee'">{{getLoaReference(row.emp_id, log)}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                            <h6>Total Accumulated Attendance Record/s: <span class="m--regular-font-size-lg5 ml-3" v-text="row.reports_total">0</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="late_and_absentee_view" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div id="late_and_absentee_container" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-trash mr-2"></i>Attendance Preview</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h4>{{ row.employee_name }}</h4>
                            <p>{{ row.department + " • " + row.position }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6>Date Hired</h6>
                            <p v-text="dateFormatted(row.date_start)">&nbsp;</p>
                        </div>
                        <div class="col-6">
                            <h6>Last Verified Date</h6>
                            <p v-text="dateFormatted(row.max_date)">&nbsp;</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" v-for="log in attlogs">
                            <div class="m-alert m-alert--outline alert text-center" :class="backgroundClass(log)" role="alert">
                                <p v-text="log">&nbsp;</p>
                                <p class="m--regular-font-size-lg2 m--font-boldest mb-0" v-if="report_type == 'absentee'">{{getLoaReference(row.emp_id, log)}}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-tabs-line mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab_late" role="tab">
                                <i class="la la-clock mr-1"></i> Late Records
                                <span class="badge badge-warning ml-2">
                                    {{ row.late?.total_late || 0 }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_absent" role="tab">
                                <i class="la la-user-times mr-1"></i> Absent Records
                                <span class="badge badge-danger ml-2">
                                    {{ row.absent?.total_absent || 0 }}
                                </span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">

                        <!-- LATE TAB -->
                        <div class="tab-pane fade show active" id="tab_late" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:20%">Date</th>
                                            <th class="text-right" style="width:15%">Minutes Late</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Late Records -->
                                        <tr v-for="(item, i) in lateRecords" :key="i">
                                            <td>{{ item.date }}</td>
                                            <td class="text-right">{{ item.minutes }} mins</td>
                                        </tr>

                                        <tr v-if="lateRecords.length === 0">
                                            <td colspan="5" class="text-center text-muted">No late records</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ABSENT TAB -->
                        <div class="tab-pane fade" id="tab_absent" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:60%">Date</th>
                                            <th style="width:40%">LOA Reference</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="(date, i) in absentDates" :key="i">
                                            <td>{{ date }}</td>
                                            <td>
                                                <span v-if="getLoaByAbsentDate(date)">
                                                    {{ getLoaByAbsentDate(date) }}
                                                </span>
                                                <span v-else class="text-muted">—</span>
                                            </td>
                                        </tr>

                                        <tr v-if="absentDates.length === 0">
                                            <td colspan="2" class="text-center text-muted">No absent records</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
