<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Return To Work
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-12">
                                        <a href="javascript:void(0);" id="addNewRtw" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew">
                                            <span> 
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnAdvance_search" data-toggle="modal" data-target="#modal-advance-search">
                                            <span>
                                                <i class="fa fa-search"></i>
                                                <span>
                                                    Advance Search
                                                </span>
                                            </span>
                                        </a>
                                        <a href="javascript:void(0);" id="refresh_table" onclick="reloadCurrentTable()"
                                            class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--pill btnReload_table">
                                            <span><i class="fa fa-refresh"></i><span>Refresh</span></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
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
                                        <a href="javascript:void(0);" class="dropdown-item datatable-csv" id="ExportCSV">
                                            <i class="m-nav__link-icon la la-file-o"></i>
                                            <span class="m-nav__link-text">
                                                CSV
                                            </span>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item datatable-pdf" id="ExportPDF">
                                            <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                            <span class="m-nav__link-text">
                                                PDF
                                            </span>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item datatable-excel" id="ExportExcel">
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
                    </div>
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-rtw" width="100%">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Reference No</th>
                                    <th>Employee Name</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Applied Date</th>
                                    <th class="notExport">Action</th>
                                </tr>
                            </thead>
                            <tbody>	
                            </tbody>
                        </table>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-advance-search">
    <div class="modal-dialog" role="document">
        <form id="frm-advance-search" method="post" action="<?php echo site_url("eforms/return_to_work/advanced_search_request"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
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
                        <select name="status" id="status" class="form-control">
                            <option value="">&nbsp;</option>
                            <option value="0">Pending</option>
                            <option value="1">Approved</option>
                            <option value="2">Disapproved</option>
                        </select>
                    </div>

                    <div class="form-group pt-3">
                        <label for="employee">Employee</label>
                        <select class="form-control" name="employee_id" id="employee"></select>
                    </div>
                    <div class="form-group pt-3">
                        <label for="date_time">Date From &amp; To</label>
                        <div class="input-group date">
                            <input class="form-control m-input" type="text" name="date_time" id="date_time" autocomplete="off" />
                            <span class="input-group-addon">
                                <i class="la la-calendar glyphicon-th"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="advanced_search" class="btn btn-primary btnAdvance_search">Search</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>