<style>
    .m-checkbox > span:after {
        margin-left: -3px;
        margin-top: -8px;
    }
</style>

<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Leave of Absence
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label m--margin-top-10 m--margin-bottom-10">
                        <div class="row m--margin-top-10 m--margin-bottom-10">
                            <div class="col-xl-4 order-1 order-xl-2 d-flex flex-row ml-auto">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                        <div class="m-form m-form--label m--margin-top-10 m--margin-bottom-10">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                    <a href="<?php echo site_url("eforms/loa/new_loa"); ?>" class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
                                        <span>
                                            <i class="la la-plus"></i>
                                            <span>
                                                NEW 
                                            </span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnAdvance_search" data-toggle="modal" data-target="#modal-advance-search">
                                        <span>
                                            <i class="fa fa-search"></i>
                                            <span>
                                                ADVANCE SEARCH
                                            </span>
                                        </span>
                                    </a>
                                    <button type="button" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill dropdown-toggle dropdown-toggle-split text-white btnView" data-toggle="dropdown">
                                        Export 
                                    </button>

                                    <div class="dropdown-menu">
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
                                <div class="col-xl-4 order-2 order-xl-1">
                                    <div style='float:right !important;' class="form-group m-form__group row">
                                        <div class="align-items-right col-md-12">
                                            <select class="form-control" id="choice" data-validation="false">
                                                <option></option>
                                                <option value="1">Approve Selected</option>
                                                <option value="2">Disapprove Selected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll" style="overflow-x: scroll;">
                        <table class="table table-striped table-bordered" id="table-loa" width="100%">
                            <thead>
                            <tr>
                                <th class="notExport" style="padding-right:10px!important; padding-left: 10px!important;" valign="middle">
                                    <div class="text-center">
                                        <label class="mb-0">
                                            <input type="checkbox" class="flat-red" id="selectall">
                                        </label>
                                    </div>
                                    <!--<label class="m-checkbox">
                                        <input type="checkbox">
                                        <span></span>
                                    </label>-->
                                </th>
                                <th>Status</th>
                                <th>File Under</th>
                                <th>Employee</th>
                                <th>Nature</th>
                                <th>Reason</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Date & Time</th>
                                <th>Reference#</th>
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
<div class="modal fade" tabindex="-1" role="dialog" id="modal_telegram_config_loa">
	<form id="frm-query-builder">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-telegram mr-1"></i>Telegram Notification</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="save_telegram_config">
					<div class="modal-body">
						<div class="form-group m-form__group row">
							<input type="hidden" id="config_id">
							<input type="hidden" id="module" value="loa">
							<label class="col-4 col-form-label">
								Chat Id
							</label>
							<div class="col-8">
								<input type="text" class="form-control" id="chat_id" name="chat_id">
							</div>
						</div>
						<div class="form-group m-form__group row">
							<label class="col-4 col-form-label">
								Bot Token
							</label>
							<div class="col-8">
								<textarea class="form-control" row="4" id="telegram_bot_token" name="telegram_bot_token"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" onclick="save_telegram_config()" class="btn btn-primary btnAdvance_search"><i class="la la-save mr-2"></i>Save</button>
					</div>
				</form>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="modal_form_approve" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content form">
            <div class="modal-header">
                <h3 class="modal-title">Approve Leave of Absence</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form action="#" id="form_approve" class="form-horizontal">
                <div class="modal-body ">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label class="control-label col-md-2">Remarks</label>
                        <div class="col-md-12">
                            <textarea name="remarks"  class="form-control" data-validation="required"> </textarea> 
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div>
</div>

<div class="modal fade" id="modal_form_disapprove" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content form">
            <div class="modal-header">
                <h3 class="modal-title">Disapprove Leave of Absence</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_disapprove" class="form-horizontal">
                <div class="modal-body ">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label class="control-label col-md-2">Remarks</label>
                        <div class="col-md-12">
                        <textarea name="remarks" class="form-control" data-validation="required"> </textarea> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon btnClose" style="color: #FFFFFF;" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-advance-search">
    <div class="modal-dialog" role="document">
        <form id="frm-advance-search">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Advance Search</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group pt-3">
                        <label for="company">Company</label>
                        <select class="form-control" name="company" id="company">
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group pt-3">
                        <label for="department">Department</label>
                        <select class="form-control" name="department" id="department">
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group pt-3">
                        <label for="m_daterangepicker">Date From &amp; To</label>
                        <div class="input-group date">
                            <input class="form-control m-input" type="text" name="m_daterangepicker" id="m_daterangepicker" autocomplete="off" data-validation="required"/>
                            <span class="input-group-addon">
                                <i class="la la-calendar glyphicon-th"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="refresh" class="btn btn-info btnView" data-dismiss="modal">Refresh</button>
                    <button type="submit" id="advanced_search" class="btn btn-primary btnAdvance_search">Search</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>