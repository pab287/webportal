<div class="row">
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <form id="frm-filter-hris-comprehensive" class="m-form" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-comprehensive_report">
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
                <div id="tempFilter" class="m-portlet__body">
                    <div  id="tempFilterBy" class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group has-success">
                                <label class="m--font-bolder">FILTER BY</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="radio" id="all_employees" name="filter_by" value="all" data-validation="required" v-model="all_filter" />
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
                            <div class="form-group has-success">
                                <label class="m--font-bolder">FILTER TYPE</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="radio" id="hired_employees" name="filter_type" data-validation="required" value="emp.date_start" checked />
                                        HIRED<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio" id="separated_employees" name="filter_type" value="emp.date_end" data-validation="required" />
                                        SEPARATED<span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="m--font-bolder">COMPANY <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                <select name="company" id="company" class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="m--font-bolder">DEPARTMENT <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                <select name="department" id="department" class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="m--font-bolder">POSITION <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                <select name="position" id="position" class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group has-success">
                                <label class="m--font-bolder">SORT BY</label>
                                <div class="m-checkbox-inline row m-0">
                                    <label class="m-checkbox col-6 col-md-6 col-lg-6 col-sm-12 m-0">
                                        <input type="radio" id="hired_sort" name="sort_by" data-validation="required" value="emp.date_start" checked />
                                        HIRED <span></span>
                                    </label>
                                    <label class="m-checkbox col-6 col-md-6 col-lg-6 col-sm-12 m-0">
                                        <input type="radio" id="separated_sort" name="sort_by" value="emp.date_end" data-validation="required" />
                                        SEPARATED <span></span>
                                    </label>
                                    <label class="m-checkbox col-6 col-md-6 col-lg-6 col-sm-12 m-0">
                                        <input type="radio" id="company_sort" name="sort_by" value="comp.code" data-validation="required" />
                                        COMPANY <span></span>
                                    </label>
                                    <label class="m-checkbox col-6 col-md-6 col-lg-6 col-sm-12 m-0">
                                        <input type="radio" id="department_sort" name="sort_by" value="dept.description" data-validation="required" />
                                        DEPARTMENT <span></span>
                                    </label>
                                    <label class="m-checkbox col-6 col-md-6 col-lg-6 col-sm-12 m-0">
                                        <input type="radio" id="position_sort" name="sort_by" value="pos.name" data-validation="required" />
                                        POSITION <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group has-success">
                                <label class="m--font-bolder">SORT ORDER</label>
                                <div class="m-checkbox-inline row m-0">
                                    <label class="m-checkbox col-6 col-md-6 col-lg-6 col-sm-12 m-0">
                                        <input type="radio" id="ascending_sort" name="sort_order" data-validation="required" value="ASC" checked />
                                        ASCENDING <span></span>
                                    </label>
                                    <label class="m-checkbox col-6 col-md-6 col-lg-6 col-sm-12 m-0">
                                        <input type="radio" id="descending_sort" name="sort_order" value="DESC" data-validation="required" />
                                        DESCENDING <span></span>
                                    </label>
                                </div>
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
    <div class="col">
        <div class="m-portlet m-portlet--bordered m-portlet--rounded">
            <div id="filteredContent" class="m-portlet__body">
                <template v-if="count == 0">
                <div class="alert alert-danger m-alert m-alert--air m-alert--outline" role="alert">
                    <strong>Hired / Separated Employees Report</strong> No data / record(s) found.				  	
                </div>
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
                                        @click="exportExcelHiredSeparated(event)">
                                    <span>
                                        <i class="fa fa-download"></i>
                                        <span class="m--font-boldest">Export Excel</span>
                                    </span>
                                </button>
                            </div>
                            <div class="flex-grow-0 flex-shrink-0 mr-2">
                                <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                        @click="printHiredSeparated(event)">
                                    <span>
                                        <i class="fa fa-print"></i>
                                        <span class="m--font-boldest">Print</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                    <table class="table table-striped table-bordered" id="table-comprehensive_report" width="100%"></table>
                </div>
                <!--end: Datatable -->
                </template>
            </div>
        </div>
    </div>
</div>