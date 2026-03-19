<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 id="header" class="m-portlet__head-text">
                        Projects Masterfile
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <a href="javascript:void(0);" class="m-nav__link btnArchive">
                    <span class="m-nav__link-text">
                        Archive
                    </span>
                </a>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-4">
                                <a id="addNew" href="javascript:void(0);" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle="modal" data-target="#newProjectModal">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>New</span>
                                    </span>
                                </a>
                            </div>
                            <!-- <div class="col-4">
                                <div id="filter-by-date-range" class="form-group m-0">
                                    <div id="date-picker" class="input-group">
                                        <input type="text" readonly="readonly" placeholder="SELECT DATE RANGE" id="date_range" name="date_range" data-validation="required" class="form-control m-input valid"> 
                                        <span class="input-group-addon"><i class="la la-calendar-check-o"></i></span>
                                    </div>
                                </div>
                            </div> -->
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
                    </div>
                </div>
            </div>
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll m-datatable--scroll">
                <table class="table table-striped table-bordered table-sm" id="project_table" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Project Name</th>
                            <th>Project Location</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newProjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    ADD NEW PROJECT SITE
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="project_form">
                <div class="modal-body">
                    <div class="form-group m-form__group row col">
                        <label for="project_name" class="col-12 form-label required">PROJECT NAME: </label>
                        <textarea id="project_name" name="project_name" class="col-12 form-control m-input auto-resize" rows="4" data-validation="required" style="resize:none; overflow-y:auto;"></textarea>
                    </div>
                    <div class="form-group m-form__group row col">
                        <label for="project_location" class="col-12 form-label required">PROJECT LOCATION: </label>
                        <textarea id="project_location" name="project_location" class="col-12 form-control m-input auto-resize" rows="4" data-validation="required" style="resize:none; overflow-y:auto;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        SAVE
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        CANCEL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    EDIT PROJECT SITE DETAILS
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="edit_project_form">
                <div class="modal-body">
                    <div class="form-group m-form__group row col">
                        <label for="edit_project_name" class="col-12 form-label required">PROJECT NAME: </label>
                        <textarea id="edit_project_name" name="project_name" class="col-12 form-control m-input auto-resize" rows="4" data-validation="required" style="resize:none; overflow-y:auto;"></textarea>
                    </div>
                    <div class="form-group m-form__group row col">
                        <label for="edit_project_location" class="col-12 form-label required">PROJECT LOCATION: </label>
                        <textarea id="edit_project_location" name="project_location" class="col-12 form-control m-input auto-resize" rows="4" data-validation="required" style="resize:none; overflow-y:auto;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        UPDATE
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        CANCEL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>