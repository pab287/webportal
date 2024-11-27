<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Distribution Reports (cu.m)
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white" id="btnNew" style="display: none;">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>New</span>
                                            </span>
                                        </a>

                                        <div class="d-flex align-items-center">
                                            <!-- <button class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill massPrint text-white"><i class="la la-print"></i> Print</button>         -->
                                        </div>

                                        <!-- <button class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill massPrint text-white"><i class="la la-print"></i> Print</button>
                                        <button class="btn btn-success m-btn m-btn--icon m-btn--pill btnBilling_settings" type="button">
                                            Settings
                                        </button>
                                        <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                            <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
                                                Query Builder
                                            </a>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row justify-content-end">
                                <!-- <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div> -->
                                <!-- <div class="m-btn-group btn-group" role="group">
                                    <button id="tbl-btn-share" title="Export" type="button"
                                            class="btn btnExport btn-success m-btn dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="la la-external-link"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
                                            x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                        <a href="#" class="dropdown-item datatable-csv" id="ExportCSV">
                                            <i class="m-nav__link-icon la la-file-o"></i>
                                            <span class="m-nav__link-text">
                                                CSV
                                            </span>
                                        </a>
                                        <a href="#" class="dropdown-item datatable-pdf" id="ExportPDF">
                                            <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                            <span class="m-nav__link-text">
                                                PDF
                                            </span>
                                        </a>
                                        <a href="#" class="dropdown-item datatable-excel" id="ExportExcel">
                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                            <span class="m-nav__link-text">
                                                EXCEL 	
                                            </span>
                                        </a>   
                                    </div>
                                </div> -->
                                <button class="btn btn-brand m-btn m-btn--icon me-5" id="report-date-picker">
                                    <span>
                                        <em class="fa fa-calendar"></em>
                                        <span class="selected-filter pl-3 pr-2">Pick Year</span>
                                    </span>
                                </button>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
					<!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll d-none">
                        <table class="table table-striped table-bordered table-responsive" id="table-distribution_reports" width="100%" style="display: inline-table;">
                            <thead>
                                <tr>
                                    <th>Subdivision</th>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mar</th>
                                    <th>Apr</th>
                                    <th>May</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Aug</th>
                                    <th>Sept</th>
                                    <th>Oct</th>
                                    <th>Nov</th>
                                    <th>Dec</th>
                                </tr>
                            </thead>
                            <tbody>	
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right"></td>
                                    <td class="mos_data" data-mos="1"></td>
                                    <td class="mos_data" data-mos="2"></td>
                                    <td class="mos_data" data-mos="3"></td>
                                    <td class="mos_data" data-mos="4"></td>
                                    <td class="mos_data" data-mos="5"></td>
                                    <td class="mos_data" data-mos="6"></td>
                                    <td class="mos_data" data-mos="7"></td>
                                    <td class="mos_data" data-mos="8"></td>
                                    <td class="mos_data" data-mos="9"></td>
                                    <td class="mos_data" data-mos="10"></td>
                                    <td class="mos_data" data-mos="11"></td>
                                    <td class="mos_data" data-mos="12"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
					<!--end: Datatable -->

                    <div id="distribution_report_tbl" class="w-100 d-flex">
                        <div class="col-lg-3 p-0 d-flex">
                            <div class="col-5 p-0">
                                <table class="table table-bordered  m-table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                        </tr>
                                    </thead>
                                    <tbody id="distri_report_tbl_mos"></tbody>
                                </table>
                            </div>

                            <div class="col-7 p-0">
                                <table class="table table-bordered  m-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Total Consumption</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dis_total_consumption">
                                        <template v-for="(items, index) in consumption" :key="index">
                                            <tr><td class="text-right">{{ number_with_commas(items) }}</td></tr>    
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-4 p-0 d-flex">
                            <div id="distri_subdv_col" class="distri_subdv_col d-flex w-100">
                                <template v-for="(items, index) in total_mos_subdv" :key="index">
                                    <table class="table table-bordered m-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">{{ items.subdv_name }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(v, k) in items.total_reading_per_mos" :key="k">
                                                <tr><td class="text-right">{{ number_with_commas(v) }}</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </template>
                            </div>
                        </div>

                        <div class="col-lg-5 p-0 d-flex">
                            <div class="col-4 p-0">
                                <table class="table table-bordered m-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Total Billed</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dis_total_reading">
                                        <template v-for="(items, index) in total_reading" :key="index">
                                            <tr><td class="text-right">{{ number_with_commas(items) }}</td></tr>    
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-4 p-0">
                                <table class="table table-bordered m-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Mos. Diff</th>
                                        </tr>
                                    </thead>
                                    <tbody id="distri_mos_diff">
                                        <template v-for="(items, index) in mos_diff" :key="index">
                                            <tr><td class="text-right">{{ number_with_commas(items) }}</td></tr>    
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-4 p-0">
                                <table class="table table-bordered m-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Percentage Main vs. Subdv</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dis_percentage">
                                        <template v-for="(items, index) in percentage" :key="index">
                                            <tr><td class="text-right">{{ number_with_commas(items) }}</td></tr>    
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
			<!--end::Portlet-->
		</div>
	</div>
</div>

<style>
    /* *** Hydra Billing (Distribution Report) *** */
    #distribution_report_tbl table thead tr th {
        text-overflow: ellipsis;
        width: max-content;
        overflow: hidden;
        white-space: nowrap;
    }

    .distri_subdv_col {
        width: 100%;
        white-space: normal;
        overflow: auto;
    }

    #distribution_report_tbl table {
        margin: 0 0 5px 0;
    }

    .distri_subdv_col::-webkit-scrollbar{
        height: 4px;
        width: 4px;
        background: gray;
    }

    /* Track */
    .distri_subdv_col::-webkit-scrollbar-track {
    background: #f1f1f1; 
    }
    
    /* Handle */
    .distri_subdv_col::-webkit-scrollbar-thumb {
    background: #888; 
    }

    /* Handle on hover */
    .distri_subdv_col::-webkit-scrollbar-thumb:hover {
    background: #555; 
    }

    .distri_subdv_col::-webkit-scrollbar-thumb:horizontal{
        background: #000;
        border-radius: 10px;
    }
</style>