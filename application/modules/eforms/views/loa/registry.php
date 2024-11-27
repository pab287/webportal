 

<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Invalid Reason Registry
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a href="javascript:" data-toggle="modal" data-target="#modal_invalid_reason"  class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New 
                                                </span>
                                            </span>
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
                       
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll" style="overflow-x: scroll;">
                        <table class="table table-striped table-bordered" id="table-registry" width="100%">
                            <thead>
                            <tr>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Created by</th>
                                <th>Modified by</th>
                                <th>Date & Time Registered</th>
                                <th>Date & Time Modified</th>
                                <th class="notExport">Action</th>
                                <!-- <th>Status</th>
                                <th>File Under</th>
                                <th>Employee</th>
                                <th>Nature</th>
                                <th>Reason</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Date & Time</th>
                                <th>Reference#</th>
                                <th class="notExport">Action</th> -->
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
</div>

 
<div class="modal fade" tabindex="-1" role="dialog" id="modal_invalid_reason">
	<form id="frm-invalid-reason-add">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-list"></i>&nbsp;New Invalid Reason</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div> 
					<div class="modal-body">
						<div class="form-group m-form__group row">
							<label class="col-2 col-form-label">
								Reason
							</label>
							<div class="col-10">
								<textarea class="form-control" row="4"  name="reason"></textarea>
							</div>
						</div>
                        <div class="form-group m-form__group row">
                            <label class="col-2 col-form-label">
                                Status
                            </label>
                            <div class="col-10">
                                <select class="form-control" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
					<div class="modal-footer">
						<button type="submit"  class="btn btn-primary btnAdvance_search"><i class="la la-save mr-2"></i>Save</button>
					</div> 
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_invalid_reason_edit">
    <form id="frm-invalid-reason-update">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-list"></i>&nbsp;Update Invalid Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                    <div class="modal-body">
                        <input type="hidden" id="Uid" name="id">
                        <div class="form-group m-form__group row">
                            <label class="col-2 col-form-label">
                                Reason
                            </label>
                            <div class="col-10">
                                <textarea class="form-control" row="4" id="reason"  name="reason"></textarea>
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-2 col-form-label">
                                Status
                            </label>
                            <div class="col-10">
                                <select class="form-control" id="status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnAdvance_search"><i class="la la-save mr-2"></i>Update</button>
                    </div> 
            </div>
        </div>
    </form>
</div>