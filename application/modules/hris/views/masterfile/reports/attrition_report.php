<div class="m-content" id="attrition-report">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        ATTRITION REPORT
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabular-tab">Tabular Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#chart-tab">Chart Report</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tabular-tab" role="tabpanel">
                    <div class="row justify-content-end align-items-center mr-2">
                        <div class="col-md-4 text-right">
                            <span data-toggle="modal"
                                data-target="#generate-report-modal">
                                <button class="btn btn-success m-btn m-btn--icon"
                                        data-toggle="m-tooltip" data-original-title="Generate Attrition Report"
                                        data-skin="dark"
                                        data-delay='{"show": 500}'>
                                    <span>
                                        <i class="fa fa-gears pr-1"></i>
                                        Generate Report
                                    </span>
                                </button>
                            </span>
                            <span>
                                <button class='btn btn-primary m-btn btnPrint' @click='printReport("attrition_table", "tabular")' :disabled="!isEmpty(colmn) ? false : 'disabled'">
                                    <span>
                                        <i class="fa fa-print pr-1"></i>Print
                                    </span>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" id="attrition_table">
                            <!--::dt begin::-->
                            <div class='row'>
                                <div class="col-md-12">
                                    <template v-if="row.company && row.company != 'All'">
                                        <h4>COMPANY: <strong>{{ row.company }}</strong></h4>
                                    </template>
                                    <template v-if="row.coverage">
                                        <h5>COVERAGE DATE: <strong>{{ row.coverage }}</strong></h5>
                                    </template>
                                </div>
                            </div>
                            <div class="tbl-responsive-sm col-md-12">
                                <table class="table table-bordered table-condensed table-sm" width="100%" style="font-family: roboto; padding: 0px !important;" id="tbl-attrition-report">
                                    <thead>
                                        <tr>
                                            <th width="15%"></th>
                                            <th width="7%" class="text-center"></th>
                                            <th>January</th>
                                            <th width="5%"></th>
                                            <th>February</th>
                                            <th width="5%"></th>
                                            <th>March</th>
                                            <th width="5%"></th>
                                            <th>April</th>
                                            <th width="5%"></th>
                                            <th>May</th>
                                            <th width="5%"></th>
                                            <th>June</th>
                                            <th width="5%"></th>
                                            <th>July</th>
                                            <th width="5%"></th>
                                            <th>August</th>
                                            <th width="5%"></th>
                                            <th>September</th>
                                            <th width="5%"></th>
                                            <th>October</th>
                                            <th width="5%"></th>
                                            <th>November</th>
                                            <th width="5%"></th>
                                            <th>December</th>
                                            <th><strong>Total</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <!-- <template v-if="isEmpty(colmn) == false">
                                        <template v-if="isLabelVisible">
                                            </template>
                                        </template> -->
                                    <tfoot hidden>
                                        <tr>
                                            <th width="15%" colspan="2" class="text-right"><strong>Total</strong></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th width="5%"></th>
                                            <th></th>
                                            <th><strong></strong></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!--::dt end::-->
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="chart-tab" role="tabpanel">
                    <div class="row justify-content-end align-items-center">
                        <div class="col-md-6">
                            <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm justify-content-end mr-3" role="tablist" id="evaluation_tab">
                                <li class="nav-item dropdown m-tabs__item">
                                    <a class="nav-link m-tabs__link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">Year</a>
                                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">
                                        <template v-if="year">
                                            <template v-for="(item, index) in year">
                                                <a class="dropdown-item text-center" data-toggle="tab" href="javascript:void(0)" @click="loadAttritionByYear(item.id)">{{ item.text }}</a>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <button disabled class="dropdown-item text-center" data-toggle="tab"> No Available Year</button>
                                        </template>
                                    <div>
                                </li>
                                <li class="nav-item m-tabs__item" style="width: 190px;">
                                    <div class="form-group">
                                        <select id="chart-company" class="form-control form-control-solid">
                                            <option></option>
                                        </select>
                                    </div>
                                </li>
                                <li class="nav-item m-tabs__item">
                                    <button class='btn btn-primary m-btn btnPrint' @click='printReport("attrition_chart", "chart")' :disabled="!isEmpty(chartData) ? false : 'disabled'">
                                        <span><i class="fa fa-print pr-1"></i>Print</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12" id="attrition_chart">
                            <div class='row'>
                                <div class="col-md-12">
                                    <template v-if="chartRow.company && chartRow.company != 'All'">
                                        <h4>Company: <strong>{{ chartRow.company }}</strong></h4>
                                    </template>
                                    <template v-if="chartRow.coverage">
                                        <h5>Coverage Date: <strong>{{ chartRow.coverage }}</strong></h5>
                                    </template>
                                </div>
                            </div>
                            <div id="attrition-chart-container" class="attrition-chart-container _container" style="height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="generate-report-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="generate-journal_content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="frm-attrition-report" method="post" action="<?php echo site_url("hris/reports/generate_attrition_report"); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div  class="modal-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">GENERATE BY</label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input type="radio" id="generate-company" name="generate_group" data-validation="required" value="1" class="valid" @click="tempShowByType(1)" checked /> Company<span></span>
                                        </label>
                                        <label class="m-checkbox">
                                            <input type="radio" id="generate-department" name="generate_group" data-validation="required" value="2" class="valid" @click="tempShowByType(2)" /> Department<span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <template v-if="company_picker === true">
                                            <div class="form-group">
                                                <label for="company" class="m--font-bolder">Company</label>
                                                <select id="company" class="form-control" name="company" :data-validation="department_picker ? 'required' : false">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="col-md-12">
                                        <template v-if="department_picker === true">
                                            <div class="form-group">
                                                <label for="department" class="m--font-bolder">Department</label>
                                                <select id="department" class="form-control" name="department">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="filter_year" class="required m--font-bolder">YEAR</label>
                                            <select class="form-control" id="filter_year" name="filter_year" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12">
                                        <div class="form-group">
                                            <label class="m--font-bolder">TO GENERATE</label>
                                            <div class="m-checkbox-inline">
                                                <!-- add data-validation-qty to the first checkbox for validation; example for data-validation-qty => min1, max2, (1-3) max1 to max3 -->
                                                <label class="m-checkbox">
                                                    <input type="checkbox" id="generate-company" name="to_generate_group[]" value="Hired" data-validation="checkbox_group" checked data-validation-qty="min1" /> Hired<span></span>
                                                </label>
                                                <label class="m-checkbox">
                                                    <input type="checkbox" id="generate-department" name="to_generate_group[]" value="Seperated" data-validation="checkbox_group" /> Seperated<span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="m--font-bolder">FILTER BY</label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input type="radio" name="filter_group" data-validation="required" value="1" class="valid" @click="tempShowByDates(1)" checked /> MONTH<span></span>
                                        </label>
                                        <label class="m-checkbox">
                                            <input type="radio" name="filter_group" data-validation="required" value="2" class="valid" @click="tempShowByDates(2)" /> YEAR<span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="filter-by-month-year" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                        <div class="row mr-auto">
                                            <template v-if="month_picker === true">
                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0">
                                                    <label for="filter_month" class="required m--font-bolder">MONTH</label>
                                                    <select class="form-control" id="filter_month" name="filter_month" data-validation="required">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </template>
                                            <template v-if="year_picker === true">
                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                    <label for="filter_year" class="required m--font-bolder">YEAR</label>
                                                    <select class="form-control" id="filter_year" name="filter_year" data-validation="required">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">TO GENERATE</label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input type="checkbox" id="generate-company" name="to_generate_group[]" value="Hired" class="valid" data-validation="required" checked /> Hired<span></span>
                                        </label>
                                        <label class="m-checkbox">
                                            <input type="checkbox" id="generate-department" name="to_generate_group[]" value="Seperated" class="valid" data-validation="required" /> Seperated<span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div  class="modal-footer">
                        <button class="btn btn-info m-btn m-btn--icon btnView">
                            <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                        </button>
                        <button type="button" @click="resetFields" class="btn btn-danger m-btn m-btn--icon btnAdvance_search">
                            <span><i class="fa fa-refresh pr-2"></i>RESET FILTER</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    table.dataTable.table-sm>thead>tr>th{
        padding-right: 4px !important;
    }
    table tr.dtrg-group.dtrg-start{
        background-color: #c1c1c1 !important;
        color: #fff !important;
    }
    
    table tbody td:nth-child(2){
        text-align: center;
    }

    table tbody .dtrg-group td{
        padding: 7px !important;
    }

    /* .tab-content {
        border-right: 1px solid #ddd !important;
        border-bottom: 1px solid #ddd !important;
        border-left: 1px solid #ddd !important;
    } */

    @media print {
        table { font-size: 12px; } 
        .print-size-auto { width: auto }
        .print-size-8 { width: 8% }
        .print-size-10 { width: 10% }
        .print-size-25 { width: 25% }
        .row { display: flex; flex-wrap: wrap }
        .col-2 { flex: 0 0 25%; max-width: 25% }
        .col-8 { flex: 0 0 74%; max-width: 74% }
        table thead th:last-child { width: 15% }
        tfoot th:first-child { text-align: right !important }
        .col-md-12 { flex: 0 0 100%; max-width: 100%; }
        .m--font-bolder { font-weight: 800 }
        .col-4 { flex: 0 0 33%; max-width: 33% }
        table.dataTable { clear: both; margin-top: 6px !important; margin-bottom: 6px !important; max-width: none !important; border-collapse: separate !important; border-spacing: 0; }
        .table-bordered { border: 1px solid #f4f5f8; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f4f5f8; }
        #tbl-attrition-report .dtrg-group.dtrg-start{ background: #c1c1c1 !important; color: #fff; font-size: 14px; }
        #tbl-attrition-report .dtrg-group.dtrg-start td{ font-weight: 700 !important; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f4f5f8; }
        .m-datatable.m-datatable--default.m-datatable--loaded { display: block; }
        p { font-size: 14px; margin: 0 !important }
        .table-bordered th, .table-bordered td { border: 1px solid #f4f5f8; padding: 5px }
        .m--regular-font-size-sm2 { text-align: center }
        .text-center { text-align: center }
        table tbody .dtrg-group td{ padding: 7px !important; }

        @page { 
            size: landscape;
            -webkit-transform: rotate(-90deg); 
            -moz-transform:rotate(-90deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3)
        }

        @page land { 
            size: landscape;
            -webkit-transform: rotate(-90deg); 
            -moz-transform:rotate(-90deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3)
        }

        @page port { 
            size: landscape;
            -webkit-transform: rotate(-90deg); 
            -moz-transform:rotate(-90deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3)
        }
    }
</style>