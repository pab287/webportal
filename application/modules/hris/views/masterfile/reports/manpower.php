<div class="m-content">
<div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <form id="frm-filter-hris-manpower" class="m-form" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-manpower_report">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                                <h3 class="m-portlet__head-text">Filter Options</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);"  data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                        <i class="la la-angle-down"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div id="tempFilter" class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group has-success">
                                    <label class="m--font-bolder">FILTER BY <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Active Employees )</span></label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input type="radio" id="all_employees" name="filter_by" value="all" data-validation="required" v-model="all_filter" checked/>
                                            ALL<span></span>
                                        </label>
                                        <label class="m-checkbox">
                                            <input type="radio" id="ranged_employees" name="filter_by" value="date_range" data-validation="required" v-model="all_filter" />
                                            DATE RANGE<span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" v-if="all_filter === 'date_range'">
                                <div class="form-group" id="filter-by-date-range">
                                    <label class="m--font-bolder" for="date-range">SELECT DATE RANGE *</label>
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
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">COMPANY <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                    <select name="company" id="company" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">DEPARTMENT <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                    <select name="department" id="department" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">STATION <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                    <select name="station" id="station" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot text-right">
                        <button type="button" 
                            class="btn btn-warning m-btn btnAdvance_search m-btn--sm mr-1 text-white" 
                            onclick="resetFilter(this)">
                            <span>
                                <i class="fa fa-refresh"></i>
                                <span>Reset Filter</span>
                            </span>
                        </button>
                        <button type="submit" class="m-btn btn btn-success btnAdvance_search btn-submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-9 col-md-9 col-lg-9 col-xl-9 col-sm-12">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded">
                <div id="filteredContent" class="m-portlet__body">
                <template v-if="count == 0">
                    <template v-if="loadingContent == false">
                    <div class="alert alert-danger m-alert m-alert--air m-alert--outline" role="alert">
                        <strong>Manpower Count Report</strong> No data / record(s) found.				  	
                    </div>
                    </template>
                    <template v-else>
                    <h6 class="text-left">Loading Content Data!</h6>
                    </template>
                </template>
                <template v-else>
                    <!--begin: Datatable -->
                    <div class="align-items-center">
                        <div class="row">
                            <div class="col-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 text-left mb-2">
                                <h5>{{count}} <small class="m--font-bolder">TOTAL NUMBER OF ENTRIES</small></h5>
                            </div>
                            <div class="col-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 text-left d-flex flex-row-reverse mb-2">
                                <div class="flex-grow-0 flex-shrink-0">
                                    <button type="button" class="btn btn-warning btnPrint m-btn m-btn--icon text-white"
                                    @click="exportExcelPayrollSheet(event)">
                                        <span>
                                            <i class="fa fa-download"></i>
                                            <span class="m--font-boldest">Export Excel</span>
                                        </span>
                                    </button>
                                </div>
                                <div class="flex-grow-0 flex-shrink-0 mr-2">
                                    <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                        @click="printPayrollSheet(event)">
                                        <span>
                                            <i class="fa fa-print"></i>
                                            <span class="m--font-boldest">Print</span>
                                        </span>
                                    </button>
                                </div>
                                <div class="flex-grow-0 flex-shrink-0 mr-2">
                                    <a id="to-salary-history" href="#salary-history" class="btn btn-info btnPrint m-btn m-btn--icon text-white m-scroll-bottom">
                                        <span>
                                            <span class="m--font-boldest">Salary History</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                    <table class="table table-striped table-bordered" id="table-manpower_report" width="100%">
                        <tfoot>
                            <tr>
                                <th class="text-right m--font-boldest">TOTAL</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                </template>
                </div>
            </div>
        </div>
    </div>
    <div id="renderedContent" class="row">
        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded" v-if="count > 0">
                <div class="m-portlet__body">
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item"  v-for="(item, index) in headers">
                            <a class="nav-link" :class="index == 0 ? 'active':''" data-toggle="tab" :href="'#m_tabs_'+index">{{item.title}}</a>
                        </li>
                    </ul>                    

                    <div class="tab-content">
                        <template v-for="(item1, index1) in headers">
                            <div class="tab-pane" :class="index1 == 0 ? 'active':''" :id="'m_tabs_'+index1" role="tabpanel">
                                <template v-if="loadingContent === true">
                                    <h6 class="text-center">Loading Content Data!</h6>
                                </template>
                                <div :class="loadingContent === true ? 'm--hide': ''">
                                    <div class="align-items-center" :class="getInstanceExist('regular_'+item1.id) === false ? 'm--hide':''">
                                        <div class="row">
                                            <div class="col-8 col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                                <h5 class="mt-3 ml-3">{{item1.title}} <small class="m--font-bolder">MANPOWER DATA</small></h5>
                                            </div>
                                            <div class="col-4 col-xl-4 col-lg-4 col-md-4 col-sm-12 text-left d-flex flex-row-reverse mb-2">
                                                <div class="flex-grow-0 flex-shrink-0 mr-3">
                                                    <button type="button" class="btn btn-warning btnPrint m-btn m-btn--icon text-white"
                                                    @click="exportExcelPayrollSheet(event, 'regular_'+item1.id)">
                                                    <span>
                                                        <i class="fa fa-download"></i>
                                                        <span class="m--font-boldest">Export Excel</span>
                                                    </span>
                                                </button>
                                                </div>
                                                <div class="flex-grow-0 flex-shrink-0 mr-2">
                                                    <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                                        @click="printPayrollSheet(event, 'regular_'+item1.id)">
                                                        <span>
                                                            <i class="fa fa-print"></i>
                                                            <span class="m--font-boldest">Print</span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                                                    <table class="table table-striped table-bordered table-manpower_report--by_company" :id="'table-manpower_report_'+item1.id" width="100%"></table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="align-items-center mt-5" :class="getInstanceExist('weekly_'+item1.id) === false ? 'm--hide':''">
                                        <div class="row">
                                            <div class="col-8 col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                                <h5 class="mt-3 ml-3">{{item1.title}} - WEEKLY <small class="m--font-bolder">MANPOWER DATA</small></h5>
                                            </div>
                                            <div class="col-4 col-xl-4 col-lg-4 col-md-4 col-sm-12 text-left d-flex flex-row-reverse mb-2">
                                                <div class="flex-grow-0 flex-shrink-0 mr-3">
                                                    <button type="button" class="btn btn-warning btnPrint m-btn m-btn--icon text-white"
                                                        @click="exportExcelPayrollSheet(event, 'weekly_'+item1.id)">
                                                        <span>
                                                            <i class="fa fa-download"></i>
                                                            <span class="m--font-boldest">Export Excel</span>
                                                        </span>
                                                    </button>
                                                </div>
                                                <div class="flex-grow-0 flex-shrink-0 mr-2">
                                                    <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                                        @click="printPayrollSheet(event, 'weekly_'+item1.id)">
                                                        <span>
                                                            <i class="fa fa-print"></i>
                                                            <span class="m--font-boldest">Print</span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                                                    <table class="table table-striped table-bordered" :id="'table-manpower_report_weekly_'+item1.id" width="100%"></table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="salary-history" class="align-items-center mt-5" :class="getInstanceExist('salary_history_'+item1.id) === false ? 'm--hide':''">
                                        <div class="row">
                                            <div class="col-8 col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                                <h5 class="mt-3 ml-3">{{item1.title}} - SALARY HISTORY <small class="m--font-bolder">MANPOWER DATA</small></h5>
                                            </div>
                                            <div class="col-4 col-xl-4 col-lg-4 col-md-4 col-sm-12 text-left d-flex flex-row-reverse mb-2">
                                                <div class="flex-grow-0 flex-shrink-0 mr-3">
                                                    <button type="button" class="btn btn-warning btnPrint m-btn m-btn--icon text-white"
                                                        @click="exportExcelPayrollSheet(event, 'salary_history_'+item1.id)">
                                                        <span>
                                                            <i class="fa fa-download"></i>
                                                            <span class="m--font-boldest">Export Excel</span>
                                                        </span>
                                                    </button>
                                                </div>
                                                <div class="flex-grow-0 flex-shrink-0 mr-2">
                                                    <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                                        @click="printPayrollSheet(event, 'salary_history_'+item1.id)">
                                                        <span>
                                                            <i class="fa fa-print"></i>
                                                            <span class="m--font-boldest">Print</span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                                                    <table class="table table-striped table-bordered" :id="'table-manpower_report_salary_history_'+item1.id" width="100%"></table>
                                                </div>
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
    </div>
</div>
<style>
    .nav-tabs a.nav-link.active {
        font-size: 1.25rem;
        font-weight: 500 !important;
    }
    tr.dtrg-group.dtrg-start.dtrg-level-0 {
        background-color: #716aca;
        color: #ffffff;
    }
</style>