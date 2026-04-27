<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 id="header" class="m-portlet__head-text">
                        RFI Request Type
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-4">
                                <a id="addNew" href="javascript:void(0);" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle="modal" data-target="#type_modal">
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
                <table class="table table-striped table-bordered table-sm" id="rfi_type" width="100%">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Type Name</th>
                        <th>Type Code</th>
                        <th>Person In Charge</th>
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

<div class="modal fade" id="type_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    NEW RFI REQUEST TYPE
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="new_type">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="request_type" class="form-control-label required">
                                    Request Type
                                </label>
                                <input type="text" class="form-control m-input" id="request_type" name="request_type" placeholder="Enter request type" data-validation="required">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="type_code" class="form-control-label required">
                                    Request Type code
                                </label>
                                <input type="text" class="form-control m-input" id="type_code" name="type_code" placeholder="Enter request type" data-validation="required">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="person_in_charge" class="form-control-label required">
                                    Person In Charge
                                </label>
                                <select type="text" id="person_in_charge" name="person_in_charge" class="form-control m-input" data-validation="required" autocomplete="off">
                                    <option></option>
                                </select>
                            </div>
                        </div>
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

<div class="modal fade" id="edit_type_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    UPDATE RFI REQUEST TYPE
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="edit_type">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit_request_type" class="form-control-label required">
                                    Request Type
                                </label>
                                <input type="text" class="form-control m-input" id="edit_request_type" name="request_type" placeholder="Enter request type" data-validation="required">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit_type_code" class="form-control-label required">
                                    Request Type code
                                </label>
                                <input type="text" class="form-control m-input" id="edit_type_code" name="type_code" placeholder="Enter request type" data-validation="required">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit_person_in_charge" class="form-control-label required">
                                    Person In Charge
                                </label>
                                <select type="text" id="edit_person_in_charge" name="person_in_charge" class="form-control m-input" data-validation="required" autocomplete="off">
                                    <option></option>
                                </select>
                            </div>
                        </div>
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