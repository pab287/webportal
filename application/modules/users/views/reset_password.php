<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">ACTIVE USER LIST</h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="offset-xl-9 offset-lg-9 offset-md-9 offset-sm-0"></div>
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                    <div class="form-group m-form__group pb-0">
                        <div class="m-input-icon m-input-icon--left">
                                    <span class="m-input-icon__icon m-input-icon__icon--left" style="z-index: 5;">
                                        <span>
                                            <i class="la la-binoculars"></i>
                                        </span>
                                    </span>
                            <div class="input-group">
                                <input type="search" class="form-control" placeholder="Search Here..."
                                       style="height: auto;" id="generalSearch">
                                <span class="input-group-btn">
                                    <button class="btn btn-secondary" title="Clear Filter"
                                            style="border-color: #cdcdcd;"
                                            onclick="clearSearch()">
                                        <i class="la la-close"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll mt-3">
                <table class="table table-striped table-bordered" id="table-reset-password" width="100%">
                    <thead>
                    <tr>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade document-modal-container"
         data-keyboard="false" data-backdrop="static"
         modal-exempt-custom tabindex="-1"
         role="dialog"></div>
</div>