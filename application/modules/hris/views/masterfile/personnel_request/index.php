<?php
    $title = null;
    $filter = isset($_GET["filter"]) ? $_GET["filter"] : "";
    switch ($filter) {
        case "unresolved":
            $title = "Personnel Request Masterfile - <strong>Unresolved</strong>";
            break;
        case "overdue":
            $title = "Personnel Request Masterfile - <strong>Overdue</strong>";;
            break;
        case "overdue-seven-days":
            $title = "Personnel Request Masterfile - <strong>Overdue 7 days</strong>";
            break;
        case "overdue-thirty-days":
            $title = "Personnel Request Masterfile - <strong>Overdue 30 days</strong>";
            break;
        case "overdue-sixty-days":
            $title = "Personnel Request Masterfile - <strong>Overdue 60 days</strong>";
            break;
        case "overdue-ninety-days":
            $title = "Personnel Request Masterfile - <strong>Overdue 90 days</strong>";
            break;
        case "still-needed":
            $title = "Personnel Request Masterfile - <strong>Still Needed</strong>";
            break;
        default:
            $title = "Personnel Request Masterfile";
            break;
    }
?>

<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-user-plus"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                <?= $title ?>
                            </h3>
                        </div>

                        <input type="hidden" value="<?= isset($_GET["filter"]) ? $_GET["filter"] : "" ?>" id="filter">
                    </div>
                    <div class="m-portlet__head-tools">
                        <!-- <ul class="m-portlet__nav">
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
														<span class="m-nav__section-text">
															Quick Action
														</span>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0)" id="copy">
                                                            <i class="m-nav__link-icon la la-copy"></i>
                                                            <span class="m-nav__link-text">
																Copy
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0)" id="excels">
                                                            <i class="m-nav__link-icon flaticon-open-box"></i>
                                                            <span class="m-nav__link-text">
																Excel
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0)" id="csv">
                                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                                            <span class="m-nav__link-text">
																CSV
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0)" id="pdf" class="m-nav__link">
                                                            <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                                            <span class="m-nav__link-text">
																PDF
															</span>
                                                        </a>
                                                    </li>
                                                    
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul> -->
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-12">
                                        <a href="javascript:void(0);"
                                           class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewPersonnelRequest">
											<span>
												<i class="la la-plus"></i>
												<span>
													New Personnel Request
												</span>
											</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="m-portlet m-portlet--tabs">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-tools">
                                    <ul class="nav nav-tabs m-tabs m-tabs-line" role="tablist">
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab" aria-expanded="true">
                                                <i class="la la-refresh"></i>
                                                Requests</a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_tab_1_2" role="tab" aria-expanded="false">
                                                <i class="la la-check"></i>
                                                Completed</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="m-portlet__body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_portlet_tab_1_1">
                                        <div class="m-btn-group btn-group pull-right" role="group">
                                            <button id="tbl-btn-share" title="Export" type="button"
                                                    class="btn btnExport btn-success m-btn dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                Export As 
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
                                        <!--begin: Datatable On going -->
                                        <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                                            <table class="table table-striped table-bordered" id="table-personnel_request" width="100%">
                                                <thead>
                                                <tr>
                                                    <th>Position</th>
                                                    <th>Type</th>
                                                    <th>Needed</th>
                                                    <th>Days Overdue</th>
                                                    <th>Requested Date</th>
                                                    <th>Needed Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--end: Datatable On going -->
                                    </div>
                                    <div class="tab-pane" id="m_portlet_tab_1_2">
                                        <div class="m-btn-group btn-group pull-right" role="group">
                                            <button id="tbl-btn-share" title="Export" type="button"
                                                    class="btn btnExport btn-success m-btn dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Export As 
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
                                                    x-placement="bottom-start"
                                                    style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <a href="" class="dropdown-item datatable-csv" id="ExportCSV_comp">
                                                    <i class="m-nav__link-icon la la-file-o"></i>
                                                    <span class="m-nav__link-text">
                                                        CSV
                                                    </span>
                                                </a>
                                                <a href="" class="dropdown-item datatable-pdf" id="ExportPDF_comp">
                                                    <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                                    <span class="m-nav__link-text">
                                                        PDF
                                                    </span>
                                                </a>
                                                <a href="" class="dropdown-item datatable-excel" id="ExportExcel_comp">
                                                    <i class="m-nav__link-icon la la-file-excel-o"></i>
                                                    <span class="m-nav__link-text">
                                                        EXCEL
                                                    </span>
                                                </a>   
                                            </div>
                                        </div>
                                        <!--begin: Datatable Completed -->
                                        <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                                            <table class="table table-striped table-bordered" id="table-personnel_request_completed" width="100%">
                                                <thead>
                                                <tr>
                                                    <th>Position</th>
                                                    <th>Type</th>
                                                    <th>Needed</th>
                                                    <th>Days Overdue</th>
                                                    <th>Requested Date</th>
                                                    <th>Needed Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--end: Datatable Completed-->
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>  
                    
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
    <div class="modal fade" id="modalTempContent" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Temp Title</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">test</div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalRemovePersonnelRequest" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div id="modalTempContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Remove Personnel Request</h5>
                    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="prId" value="0"/>
                    <p>Are you sure you want to remove this salary range on the list?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btnClose" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary btn-submit btnDelete btnRemoveCurrentPersonnelRequest">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="fade modal" tabindex="-1" role="dialog" id="modal-archive-personnel-request-dialog">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('hris/masterfile/archive_personnel_request') ?>"
                  id="frm-archive-personnel-request" onsubmit="archive_personnel_request(this); return false;">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="la la-archive mr-2"></i>Archive Personnel Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group m-form__group">
                            <label for="">Remarks / Reason</label>
                            <textarea name="archive_remarks" class="form-control" rows="5" data-validation="required"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Archive</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>