<style>
    #dropdownMenuButton:after {
        display: none;
    }
</style>
<div class="m-content">
    <div class="row">
        <div class="col-md-12">
    <!--begin::Portlet-->
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        FOR INTERVIEW
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
        <div class="m-portlet__body col-md-12">
            <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                                                        <!-- <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white" onclick="open_resume()"> -->
                                                        <a class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white" href="#" data-toggle="modal" data-target="#query_search">
                                <span>
                                <i class=" flaticon-search-1"></i>
                                    <span>
                                       Query Search
                                    </span>
                                </span>
                                </a>
                    </div>
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
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
									<em class="la la-external-link"></em>
								</button>
								<div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
										x-placement="bottom-start"
										style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
									<a href="" class="dropdown-item datatable-csv" id="ExportCSV">
										<em class="m-nav__link-icon la la-file-o"></em>
										<span class="m-nav__link-text">
											CSV
										</span>
									</a>
									<a href="" class="dropdown-item datatable-pdf" id="ExportPDF">
										<em class="m-nav__link-icon la la-file-pdf-o"></em>
										<span class="m-nav__link-text">
											PDF
										</span>
									</a>
									<a href="" class="dropdown-item datatable-excel" id="ExportExcel">
										<em class="m-nav__link-icon la la-file-excel-o"></em>
										<span class="m-nav__link-text">
											EXCEL
										</span>
									</a>   
								</div>
							</div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-none">
                        </div>
                    </div>
                </div>
                <!--begin: Datatable -->
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                <table class="table table-hover table-bordered" aria-label="table-blacklisted" id="table-for-interview" width="100%">
                    <thead>
                    <tr>
                        <th scope="col">Application Date</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact no.</th>
                        <th scope="col">Course</th>
                        <th scope="col">Position</th>
                        <th scope="col">Tag</th>
                        <th scope="col">Recruitment</th>
                        <th scope="col">Interview Date</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!--end: Datatable -->
            </div>

            
        </div>
    </div>
</div>
</div>
<?php $this->load->view("modals/query_search") ?>
    <!--end::Portlet-->