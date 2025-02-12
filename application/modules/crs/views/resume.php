<style>
    #dropdownMenuButton:after {
        display: none;
    }
    table.dataTable {
        table-layout: fixed;
        word-break: break-all;
    }
    #resume_page a:hover{
        text-decoration: none;
    }
</style>
<div id="resume_page" class="m-content">
    <!--begin::Portlet-->
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Resume Masterfile
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                        data-dropdown-toggle="hover" aria-expanded="true">
                        <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                            <i class="la la-ellipsis-h m--font-brand"></i>
                        </a>
                        <div class="m-dropdown__wrapper">
                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                    style="left: auto; right: 22.5px;"></span>
                            <div class="m-dropdown__inner">
                                <div class="m-dropdown__body">
                                    <div class="m-dropdown__content">
                                        <ul class="m-nav">
                                            <li class="m-nav__section m-nav__section--first">
                                                <span style="color: #000000;" class="m-nav__section-text">
                                                    Quick Action
                                                </span>
                                            </li>
                                            <li class="m-nav__section m-nav__section--first">
                                                <a href="#" data-toggle="modal" data-target="#query_search">
                                                    <i style="font-size: 15px;" class="m-nav__link-icon flaticon-search-1"></i>
                                                    <span style="font-size: 12px;" class="m-nav__section-text text-capitalize">
                                                        Advance Search
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="<?php echo site_url("crs/pending"); ?>" class="m-nav__link">
                                                    <i class="m-nav__link-icon flaticon-open-box"></i>
                                                    <span class="m-nav__link-text">
																Pending
															</span>
                                                </a>
                                                <a href="<?php echo site_url("crs/interview"); ?>" class="m-nav__link">
                                                    <i class="m-nav__link-icon flaticon-open-box"></i>
                                                    <span class="m-nav__link-text">
																For Interview
															</span>
                                                </a>
                                                <a href="<?php echo site_url("crs/blacklist"); ?>" class="m-nav__link">
                                                    <i class="m-nav__link-icon flaticon-open-box"></i>
                                                    <span class="m-nav__link-text">
																Blacklisted
															</span>
                                                </a>
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
                    <div class="col-xl-8 d-flex row align-items-center">
                            <div class="col-1 p1">
                                <a class="btn m-btn--pill btn-accent btnNew text-white" href="<?php echo site_url("crs/new_resume_page"); ?>">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>
                                            New
                                        </span>
                                    </span>
                                </a>
                            </div>

                            <div class="col-2 p1">
                                <span>
                                    <a class="btn m-btn--pill btn-danger btnNew text-white btnNew" href="#" data-toggle="modal" data-target="#query_search">
                                        <span>
                                            <span>
                                            Advance Search
                                            </span>
                                        </span>
                                    </a>
                                </span>
                            </div>
                            <div class="col-3 p1">
                                <span>
                                    <input type="text" name="application_dt_resume" id="application_dt_resume" placeholder="SELECT APPLICATION DATE" class="form-control m-input " autocomplete="off" readonly>
                                </span>
                            </div>
                    </div>
                    <div class="col-xl-4 d-flex">
                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
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
                        <div class="col-xl-12 order-2 order-xl-1">
                            <div style='float:right !important;' class="form-group m-form__group row">
                                <!-- <div class="col-md-4">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button class="btn btn-secondary dropdown-toggle" aria-expanded="true"><i class="la la-table"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button class="btn btn-secondary dropdown-toggle" aria-expanded="true" id="ExportExcel"><i
                                                    class="la la-external-link"></i>qweqwe</button>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--begin: Datatable -->
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                <table class="table table-hover table-bordered" id="table-resume" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Application<br> Date</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>School</th>
                        <th>Course</th>
                        <th>Desired<br>Position</th>
                        <th>Eligible<br> Position/Tag</th>
                        <th>Status</th>
                        <th>Attach Files</th>
                        <th>Recruitment</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!--end: Datatable -->
        </div>
    </div>

    <div class="modal fade" id="modal_form_delete" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <form action="#" id="form_delete" class="form-horizontal">
                    <div class="modal-body form">
                        <input type="hidden" value="" name="delete_id"/>
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <p class="mb-0 m--regular-font-size-lg1">Are you sure you want to Delete data?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="delete_resume()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew">
                            Delete
                        </button>
                        <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div>
    </div>

    <div class="modal fade" id="modal-confirm-archive-resume" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Archive Confirmation</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <form action="" id="frm-confirm-archive-resume" class="form-horizontal"
                      onsubmit="archiveResume(this); return false;">
                    <div class="modal-body form">
                        <p class="mb-0 m--regular-font-size-lg1">Are you sure to archive this resume?</p>
                    </div>
                    <div class="modal-footer">
                        <button type=submit
                                class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew">
                            Yes
                        </button>
                        <button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">
                            No
                        </button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div>
    </div>

    <?php $this->load->view("modals/new_resume_dialog") ?>
    <?php $this->load->view("modals/query_search") ?>
    <?php $this->load->view("modals/edit_resume_dialog") ?>
    <?php $this->load->view("modals/hire_resume_dialog") ?>
    <!--end::Portlet-->
</div>
<style>
table#table-resume td {
    word-break: break-word;
}
</style>