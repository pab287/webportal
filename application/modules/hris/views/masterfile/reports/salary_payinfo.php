<div class="m-content">
    <div class="row">
        <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
        <div class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon">
                            <i class="flaticon-coins"></i>
                        </span>
                        <h3 class="m-portlet__head-text">
                            Salary Pay Information
                        </h3>
                    </div>
                </div>
                <div class="m-portlet__head-tools">
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 text-right d-flex flex-row align-items-end justify-content-end">
                        <div class="flex-grow-0 flex-shrink-0 mr-2">
                            <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                    onclick="printDtTable(this)">
                                <span>
                                    <i class="fa fa-print"></i>
                                    <span class="m--font-boldest">Print</span>
                                </span>
                            </button>
                        </div>
                        <div class="flex-grow-0 flex-shrink-0">
                            <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                    onclick="exportExcelDtTable(this)">
                                <span>
                                    <i class="fa fa-download"></i>
                                    <span class="m--font-boldest">Export Excel</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive-sm mt-3">
                    <table id="table-salary_payinfo-report" class="table table-hover table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Company</th>
                                <th>Department Code</th>
                                <th>Department Description</th>
                                <th>Position</th>
                                <th>Station</th>
                                <th>Salary Rate</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>