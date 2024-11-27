<style>
    .fileinput-button {
        display: none;
    }

    .m-widget2__checkbox {
        padding-top: 0 !important;
        vertical-align: middle !important;
        padding-right: 8px !important;
    }

    .m-widget2__desc {
        width: 70% !important;
    }

    .m-widget2__actions {
        width: 100% !important;
        text-align: right !important;
    }
    
    #preview-document-dialog .modal-dialog {
        height: 80%;
    }

    #preview-document-dialog .modal-content {
        height: 100%;
    }

    #preview-document-dialog .modal-body {
        padding: 0;
    }

    #preview-document-dialog iframe, embed {
        border: 0;
        width: 100%;
        height: 100%;
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
                                Service Ticket
                            </h3>
                        </div>
                    </div>
                </div>
                <form id="frm_status_new">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-3 col-form-label">
                                        Requested by:
                                    </label>
                                    <div class="col-9">
                                        <input class="form-control m-input" type="text" name="requested_by"
                                               v-model="vm_tab1.requested_by"
                                               id="requested_by" data-validation="required" disabled/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-3 col-form-label">
                                        Date requested:
                                    </label>
                                    <div class="col-9">
                                        <input class="form-control m-input" type="text" name="requested_dt"
                                               id="requested_dt" v-model="vm_tab1.requested_dt"
                                               data-validation="required" disabled/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-3 col-form-label">
                                        Date Needed:
                                    </label>
                                    <div class="col-9">
                                        <input class="form-control m-input" type="text" name="need_dt" id="need_dt"
                                               maxlength="22" v-model="vm_tab1.need_dt"
                                               data-validation="required" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-sm-12 col-xs-12 col-md-6 col-lg-6 col-xl-6" id="uploaded_files">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div>
                        <br>
                        <div class='row'>
                            <div class='col-md-6'>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Department
                                    </label>
                                    <div class="col-10">
                                        <select id="department" name="department" data-validation="required"
                                                disabled>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Type
                                    </label>
                                    <div class="col-10">
                                        <select id="type" name="type" data-validation="required" disabled>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="webportal">
                                    <label class="col-2 col-form-label">
                                        Module
                                    </label>
                                    <div class="col-10">
                                        <select id="module" name="module" data-validation="required" disabled>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Issue
                                    </label>
                                    <div class="col-10">
                                        <textarea class="form-control m-input" id="issue" name="issue" rows="4"
                                                  v-model="vm_tab1.issue"
                                                  data-validation="required"
                                                  disabled></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Status
                                    </label>
                                    <div class="col-10">
                                        <select id="status" name="status" data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Performed by
                                    </label>
                                    <div class="col-10">
                                        <select id="performed_by" name="performed_by" data-validation="required">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Remarks
                                    </label>
                                    <div class="col-10">
                                        <textarea class="form-control m-input" id="remarks" name="remarks" rows="4"
                                                  v-model="vm_tab1.remark"
                                                  data-validation="required"></textarea>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="reopen_field">
                                    <label class="col-2 col-form-label">
                                        Reason for reopen
                                    </label>
                                    <div class="col-10">
                                        <textarea class="form-control m-input" id="reopen" name="reopen" rows="4"
                                                  data-validation="required" v-model="vm_tab1.open"
                                                  disabled></textarea>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="onhold_field">
                                    <label class="col-2 col-form-label">
                                        Reason for being put onhold
                                    </label>
                                    <div class="col-10">
                                        <textarea class="form-control m-input" id="onhold" name="onhold" rows="4"
                                                  v-model="vm_tab1.onhold"
                                                  data-validation="required"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit"
                                    class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnNew">
                                <span>
                                    <i class="la la-save"></i>
                                    <span>
                                        Save
                                    </span>
                                </span>
                            </button>
                            <a href="<?php echo base_url("ts/ticketing/masterfile"); ?>">
                                <button type="button"
                                        class="btn m-btn btn-metal text-white m-btn--custom m-btn--icon m-btn--air m-btn--box btnNew">
                                    <span>
                                        Back
                                    </span>
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="preview-document-dialog">
    <div class="modal-dialog modal-extra-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>