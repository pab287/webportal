<div class="m-content">
    <div class="m-portlet m-portlet--tabs">
        <div class="m-portlet__head">
            <div class="m-portlet__head-tools">
                <ul class="nav nav-tabs m-tabs-line m-tabs-line--info m-tabs-line--2x" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a id="released_assets" onclick="assetsTab(event, 'released')" class="nav-link m-tabs__link active" data-toggle="tab" href="#m_portlet_base_demo_1_tab_content" role="tab">
                            <i class="flaticon-interface-4"></i>
                            Released Assets
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a id="returned_assets" onclick="assetsTab(event, 'returned')" class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_base_demo_1_tab_content" role="tab">
                            <i class="flaticon-interface"></i>
                            Returned Assets
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="released" role="tabpanel">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <!--<a class="dropdown-item" data-toggle="modal" data-target="#modal-advance-search" href="#">
                                                Advanced Search
                                            </a>-->
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
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-released" width="100%">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>File Under</th>
                                    <th>Reference #</th>     
                                    <th>Issued To</th>
                                    <th>Asset Code</th>
                                    <th>Item</th>
                                    <th>Amount</th>
                                    <th class="notExport">Action</th>
                                </tr>
                            </thead>
                            <tbody>	
                            </tbody>
                        </table>
                    </div>
				</div>
                <div class="tab-pane" id="returned" role="tabpanel">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <!--<a class="dropdown-item" data-toggle="modal" data-target="#modal-advance-search" href="#">
                                                Advanced Search
                                            </a>-->
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
                                                Query Builder
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch2">
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
                                        <a href="" class="dropdown-item datatable-csv" id="ExportCSV2">
                                            <i class="m-nav__link-icon la la-file-o"></i>
                                            <span class="m-nav__link-text">
                                                CSV
                                            </span>
                                        </a>
                                        <a href="" class="dropdown-item datatable-pdf" id="ExportPDF2">
                                            <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                            <span class="m-nav__link-text">
                                                PDF
                                            </span>
                                        </a>
                                        <a href="" class="dropdown-item datatable-excel" id="ExportExcel2">
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
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered" id="table-returned" width="100%">
                            <thead>
                                <tr>     
                                    <th>Reference #</th>
                                    <th>File Under</th>
                                    <th>Issued To</th>
                                    <th>Asset Code</th>
                                    <th>Item</th>
                                    <th>Amount</th>
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
					<button type="button" id="clear_query_builder" class="btn btn-danger btnAdvance_search mr-auto">Clear</button>
					<button type="button" id="query-builder-btn" class="btn btn-primary btnAdvance_search">Generate</button>
				</div>
			</div>
		</div>
	</form>
</div>