<div class="m-content">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Travel Order Report
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <div class="m-portlet__body">
                    <div class="row mb-2">
                        <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label> Company <span class="m-form__help p-0" style="text-transform: none;">(Optional)</span></label>
                                        <select class="form-control" id="companySelect" multiple></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label> Department <span class="m-form__help p-0" style="text-transform: none;">(Optional)</span></label>
                                        <select class="form-control" id="departmentSelect" multiple></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="form-group m-form__group">
                                        <label> Status <span class="m-form__help p-0" style="text-transform: none;">(Optional)</span></label>
                                        <select class="form-control" id="statusSelect">
                                            <option value=""></option>
                                            <option value="Pending">For Recommendation</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Recommend_Approved">Pending Approval</option>
                                            <option value="Accomplished">Accomplished</option>
                                            <option value="Disapproved">Disapproved</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 justify-content-between">
                        <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12" id="filter-buttons">
                            <button type="button" class="btn btn-brand m-btn m-btn--icon btnAdvance_search" id="accomplishment-report-date-range-picker">
                                <span>
                                    <em class="fa fa-calendar"></em>
                                    <span class="selected-filter pl-3 pr-2">Today</span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-success m-btn m-btn--icon text-white btnAdvance_search" id="search-report">
                                <span>
                                    <em class="fa fa-search"></em>
                                    <span class="selected-filter pl-3 pr-2">Submit</span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-warning m-btn m-btn--icon text-white btnAdvance_search" id="clear-options">
                                <span>
                                    <em class="fa fa-refresh"></em>
                                    <span class="selected-filter pl-3 pr-2">Reset Filter</span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-success m-btn m-btn--icon text-white btnAdvance_search" id="export-excel" disabled>
                                <span>
                                    <em class="fa fa-download"></em>
                                    <span class="selected-filter pl-3 pr-2">Export to Excel</span>
                                </span>
                            </button>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 d-flex flex-row">
                            <div class="flex-grow-1 flex-shrink-0">
                                <input type="text" class="form-control" placeholder="Search..." id="search-accomplishments" style="height: auto;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="report-table" width="100%">
                                    <thead>
                                        <th>Status</th>
                                        <th>Reference No</th>
                                        <th>File Under</th>
                                        <th>Department</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th>Personnel</th>
                                        <th>Destination</th>
                                        <th>Details</th>
                                        <th>Driver & Vehicle</th>
                                        <th>Personnel</th>
                                        <th>Destination</th>
                                        <th>Date Created</th>
                                        <th>Date and Time</th>
                                        <th>Accomplished By</th>
                                        <th>Accomplished By</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 

<style>
    @media screen and (max-width: 767px) {
        #filter-buttons button {
            margin-bottom: 10px;
        }
    }
</style>