<div class="m-portlet__body">
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="form-group m-form__group">
                <label>
                    Company <span class="m-form__help p-0" style="text-transform: none;">(Optional)</span>
                </label>
                <select class="form-control m-input m-input--square" id="companySelectLate"></select>
            </div>
            <div class="form-group m-form__group">
                <label>
                    Department <span class="m-form__help p-0" style="text-transform: none;">(Optional)</span>
                </label>
                <select class="form-control m-input m-input--square" id="departmentSelectLate"></select>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <button class="btn btn-brand m-btn m-btn--icon"
                    id="late-report-date-range-picker">
                <span>
                    <em class="fa fa-calendar"></em>
                    <span class="selected-filter pl-3 pr-2">Today</span>
                </span>
            </button>
            <button class="btn btn-warning m-btn m-btn--icon text-white"
                    id="clear-options-late">
                <span>
                    <em class="fa fa-refresh"></em>
                    <span class="selected-filter pl-3 pr-2">Reset Filter</span>
                </span>
            </button>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 offset-xl-3 offset-lg-3 offset-md-3 offset-sm-0 d-flex flex-row">
            <div class="flex-grow-1 flex-shrink-0">
                <input type="text" class="form-control" placeholder="Search..." id="search-late-report"
                       style="height: auto;">
            </div>
            <div class="flex-grow-0 flex-shrink-0">
                <div class="btn-group ml-2" role="group">
                    <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <button id="btn-export-fixed-assets" type="button" data-toggle="m-tooltip"
                                data-original-title="Export" data-skin="dark" data-delay="{&quot;show&quot;: 300}"
                                class="m-btn btn btn-success dropdown-toggle btnSave">
                            <em class="la la-external-link"></em>
                        </button>
                    </span>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1"
                         x-placement="bottom-end"
                         style="position: absolute; transform: translate3d(-62px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                        <a href="javascript:void(0);" class="dropdown-item datatable-pdf" id="ExportPDF"
                            onclick="exportAsLate('pdf');">
                            <em class="m-nav__link-icon fa fa-file-pdf-o"></em>
                            <span class="m-nav__link-text">PDF</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item datatable-excel" id="ExportExcel"
                           onclick="exportAsLate('excel');"">
                            <em class="m-nav__link-icon fa fa-file-excel-o"></em>
                            <span class="m-nav__link-text">EXCEL</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered"
               id="tbl-late-report" width="100%">
            <thead>
            <tr>
                <th>LASTNAME</th>
                <th>DATES</th>
                <th>BIOMETRIC ID</th>
                <th>EMPLOYEE</th>
                <th>LATES</th>
                <th>COUNT</th>
            </tr>
            </thead>
        </table>
    </div>
</div>