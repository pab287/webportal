<div class="row">
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <form id="frm-filter-hris-late_report" class="m-form" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-late_report">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon">
                            <i class="fa fa-filter"></i>
                        </span>
                            <h3 class="m-portlet__head-text">Filter Options</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">&nbsp;</div>
                </div>
                <div class="m-portlet__body">
                    <div id="tempFilterByLateReport" class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group has-success">
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
                        <template v-if="filter_by === 'date_range'">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-animate-fade-in">
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
                        </div>
                        </template>
                        <template v-else>
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
                        </template>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group m-form__group">
                                <label for="company" class="m--font-bolder required">COMPANY</label>
                                <select name="company" id="company" class="form-control" data-validation="required">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group m-form__group">
                                <label for="department">
                                    DEPARTMENT <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                </label>
                                <select class="form-control" id="department" name="department">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group m-form__group">
                                <label for="payroll_group">
                                    PAYROLL GROUP <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                </label>
                                <select class="form-control" id="payroll_group" name="payroll_group[]" multiple></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group m-form__group">
                                <label for="employee" class="m--font-bolder">Employee <small>( Optional )</small></label>
                                <select id="employee" class="form-control" name="employee[]" multiple>
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="lateReportActions">
                    <div class="m-portlet__foot text-right" v-if="has_actions">
                        <button type="button" 
                            class="btn btn-warning m-btn btnAdvance_search m-btn--sm mr-1 text-white" 
                            onclick="resetFilterLateAbsenteeReport(this)">
                            <span>
                                <i class="fa fa-refresh"></i>
                                <span>Reset Filter</span>
                            </span>
                        </button>
                        <button type="button" 
                            class="m-btn btn btn-success btnAdvance_search btn-submit" onclick="submitLateAbsenteeFilterForm(this)">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col">

        <div class="m-portlet m-portlet--bordered m-portlet--rounded">
            <div id="filteredLateReport" class="m-portlet__body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                    <table class="table table-striped table-bordered" id="table-late_report" width="100%"></table>
                </div>
                <div class="row">
                    <div class="col-7 col-md-7 col-lg-7 col-sm-12">
                        <div class="alert alert-danger m-alert m-alert--air m-alert--outline mb-0 mt-3" role="alert">
                            <strong>Note!</strong> The late attendance record/s listed are all time sheet reference data.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLatePreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div id="modalLateContainer" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-trash mr-2"></i>Late Attendance Preview</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 col-sm-12">
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
                    <div class="col-4 col-md-4 col-lg-4 col-sm-12" v-for="log in attlogs">
                        <div class="m-alert m-alert--outline alert text-center" :class="backgroundClass(log)" role="alert">{{log}}</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                        <h6>Total Accumulated Late Attendance Record/s: <span class="m--regular-font-size-lg5 ml-3" v-text="row.late_total">0</span></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>