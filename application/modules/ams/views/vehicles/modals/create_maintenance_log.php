<div class="modal fade" id="create-maintenance-template-log">
    <div class="modal-dialog" role="dialog"
         style="max-width: none; width: 65%;">
        <div class="modal-content">
            <form id="frm-create-maintenance-template-log"
                  action="<?= base_url("ams/vehicles/save_maintenance_template_log") ?>">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <div class="modal-header">
                    <h5 class="modal-title">ADD PREVENTIVE MAINTENANCE LOGS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row d-flex align-items-center">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="d-flex flex-row">
                                <div class="form-group m-form__group d-inline flex-grow-1 mr-3">
                                    <label>Template <span class="text-danger">*</span></label>
                                    <select class="form-control m-input" id="select2-preventive-temp-head"
                                            data-validation="required" name="preventive_temp_head"></select>
                                </div>
                                <button type="button" title="Load Template" onclick="loadPreventiveTemplateBody()"
                                        class="btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnNew align-self-center mt-3">
                                    <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="offset-xl-8 offset-lg-8 offset-md-8 offset-sm-0"></div>
                    </div>
                    <table class="table table-bordered mt-4" id="tbl-preventive-template">
                        <thead>
                        <tr>
                            <th>DESCRIPTION</th>
                            <th width="15%">LAST ODO VALUE</th>
                            <th width="15%">LAST SCHEDULE</th>
                            <th width="15%">NEXT ODO VALUE</th>
                            <th width="15%">DAYS</th>
                            <th width="15%">NEXT SCHEDULE</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnNew">
                        Save Maintenance Log
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>