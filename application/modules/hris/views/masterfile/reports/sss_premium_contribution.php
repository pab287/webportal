<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded">
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
                <div id="tempFilter" class="m-portlet__body">
                    <div  id="tempFilterBy" class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group has-success">
                                <label for="filter_by" class="m--font-bolder">FILTER BY</label>
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
                                <label for="company" class="m--font-bolder">COMPANY <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                <select name="company" id="company" class="form-control"><option></option></select>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="employee" class="m--font-bolder required">Employee</label>
                                <select name="employee" id="employee" class="form-control" data-validation="required">
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
        </div>
        <div class="col">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-list"></i>
                            </span>
                            <h3 class="m-portlet__head-text">SSS Premium Contribution <small>Report</small></h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">&nbsp;</div>
                </div>
                <div id="filteredContent" class="m-portlet__body">
                    <div class="alert alert-danger m-alert m-alert--air m-alert--outline" role="alert">
                        <strong>SSS Premium Contribution Report</strong> No data / record(s) found.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
