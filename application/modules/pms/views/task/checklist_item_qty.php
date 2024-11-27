<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption" id="checklist_qty-content">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                            <span v-text="row.label">&nbsp</span><small>Task Quantity</small>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--primary m-tabs-line--right m-tabs-line-primary" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_portlet_tab_updated_list" role="tab">
                                    <i class="flaticon-graphic-2"></i> <small>Updated Quantity</small></a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_tab_requested_list" role="tab">
                                    <i class="flaticon-edit"></i> <small>Requested Quantity</small></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="m_portlet_tab_updated_list" role="tabpanel">
                            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                                <div class="row align-items-center">
                                    <div class="col-xl-8 order-2 order-xl-1">&nbsp;</div>
                                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                            <input type="text" class="form-control m-input m-input--solid"
                                                placeholder="Search..." id="generalSearch">
                                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                                <span>
                                                    <i class="la la-search"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--begin: Datatable -->
                            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                                <table class="table table-striped table-bordered" id="table-checklist_item_qty"
                                    width="100%">
                                    <thead>
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Parent Task</th>
                                        <th>Quantity</th>
                                        <th>Applied By</th>
                                        <th>Applied Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!--end: Datatable -->
                        </div>
                        <div class="tab-pane" id="m_portlet_tab_requested_list" role="tabpanel">
                            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                                <div class="row align-items-center">
                                    <div id="temp-actions" class="col-xl-8 order-2 order-xl-1">
                                        <button class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewRequest" v-if="count > 0">
                                            <i class="la la-plus"></i> New Request
                                        </button>
                                    </div>
                                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                            <input type="text" class="form-control m-input m-input--solid"
                                                placeholder="Search..." id="generalRequestSearch">
                                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                                <span>
                                                    <i class="la la-search"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--begin: Datatable -->
                            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                                <table class="table table-striped table-bordered" id="table-checklist_requested_qty" width="100%"></table>
                            </div>
                            <!--end: Datatable -->
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
    <div class="modal fade" modal-exempt-custom
        id="modal-container" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true"></div>
</div>