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
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-dropdown__toggle ">
                                    Quick Actions &nbsp;<i class="la la-cog m--font-brand"></i> 
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 16.8047px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__item"> 
                                                            <?php 
                                                            $ses_id = $this->core_layout->getCurrentEmployeeId();   
                                                            $role = $this->core_layout->getEmployeeData($ses_id);
                                                            if($role['group_id'] == 1){ ?>
                                                            <span class="btn m-btn m-nav__section-text" id="telegram_config" data-toggle="modal" data-target="#modal_telegram_config_loa">Telegram Integration</span>
                                                            <?php } ?> 
                                                    </li>
                                                    <li class="m-nav__item"> 
                                                        <a href="<?php echo base_url("eforms/loa/registry");?>" class="text-dark btn m-btn m-nav__section-text btnRegistry p-0" > <span class="btn m-btn m-nav__section-text" data-toggle="modal" >Registry</span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a href="<?php echo site_url("eforms/loa/new_loa"); ?>" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New 
                                                </span>
                                            </span>
                                        </a>
                                        <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
                                                Query Builder
                                            </a>
                                        </div>
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
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                   &nbsp;
                                </div>
                                <div class="col-xl-4 order-2 order-xl-1">
                                    <div style='float:right !important;' class="form-group m-form__group row">
                                        <div class="align-items-right col-md-12">
                                            <select class="form-control" id="choice" data-validation="false">
                                                <option></option>
                                                <option value="1">Approve</option>
                                                <option value="2">Disapprove</option>
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