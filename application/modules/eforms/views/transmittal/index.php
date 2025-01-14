<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Transmittal
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
                                        <a href="<?php echo site_url("eforms/transmittal/new_transmittal"); ?>"
                                           class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
                                            <span>
                                                <i class="fa fa-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
                                        <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" data-toggle="modal" data-target="#modal-advance-search" href="#">
                                                Advanced Search
                                            </a>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
                                                Query Builder
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-left d-flex flex-row">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                                <div class="m-btn-group btn-group" role="group">
                                    <button id="tbl-btn-share" title="Export" type="button"
                                            class="btn btnExport btn-success m-btn dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="la la-external-link"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
                                            x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                        <a href="" class="dropdown-item datatable-csv" id="ExportCSV">
                                            <i class="m-nav__link-icon la la-file-o"></i>
                                            <span class="m-nav__link-text">
                                                CSV
                                            </span>
                                        </a>
                                        <a href="" class="dropdown-item datatable-pdf" id="ExportPDF">
                                            <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                            <span class="m-nav__link-text">
                                                PDF
                                            </span>
                                        </a>
                                        <a href="" class="dropdown-item datatable-excel" id="ExportExcel">
                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                            <span class="m-nav__link-text">
                                                EXCEL
                                            </span>
                                        </a>   
                                    </div>
                                </div>
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
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-transmittal" width="100%">
                            <thead>
                            <tr>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Reference#</th>
                                <th>File Under</th>
                                <th>Deliver To</th>
                                <th>Contents</th>
                                <th>Contents</th>
                                <th>Delivery Date</th>
                                <th>Created By</th>
                                <th>Date Created</th>
                                <th class="notExport">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-advance-search">
        <div class="modal-dialog" role="document">
            <form action="" id="frm-advance-search">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Advance Search</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Status</label>
                            <select name="status" id="" class="form-control s2">
                                <option value=""></option>
                                <option value="Approved">Approved</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Pending">Pending</option>
                                <option value="Received">Received</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Priority</label>
                            <select name="priority" id="" class="form-control s2">
                                <option value=""></option>
                                <option value="Normal">NORMAL</option>
                                <option value="Important">Important</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Reference #</label>
                            <input type="text" class="form-control" autocomplete="off" name="reference_no">
                        </div>

                        <div class="form-group">
                            <label for="">File Under</label>
                            <select name="company_from" id=""></select>
                        </div>

                        <div class="form-group">
                            <label for="">Delivered To</label>
                            <input type="text" class="form-control" autocomplete="off" name="delivered_to">
                        </div>

                        <div class="form-group">
                            <label for="">Contents</label>
                            <input type="text" class="form-control" autocomplete="off" name="description" id="advance-search-description">
                        </div>

                        <div class="form-group">
                            <label for="">Delivery Date</label>
                            <div class="input-group date" id="ship_date_container">
                                <input type="text" class="form-control m-input"
                                       placeholder="Select Date" name="ship_date" autocomplete="off">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="">Created By</label>
                            <select name="created_by" id="" class="s2"></select>
                        </div>

                        <div class="form-group">
                            <label for="">Date Created</label>
                            <div class="input-group date" id="created_dt_container">
                                <input type="text" class="form-control m-input"
                                       placeholder="Select Date" name="created_dt" autocomplete="off">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnAdvance_search">Search</button>
                        <button type="button" class="btn btn-metal btnAdvance_search" onclick="clearAdvanceSearch()">Clear</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-query-builder">
	<form id="frm-query-builder">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Query Builder</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="query-builder"></div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="clear_query_builder()" class="btn btn-danger btnAdvance_search mr-auto">Clear</button>
					<button type="button" id="query-builder-btn" class="btn btn-primary btnAdvance_search">Generate</button>
				</div>
			</div>
		</div>
	</form>
</div>