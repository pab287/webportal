<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">New Accomplishment</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="frmNewAccomplishment" method="post" action="<?php echo site_url("pms/contract/do_post_event/set_new_accomplishment"); ?>">
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="contract_id" v-model="row.contract_id" />
<div class="modal-body">
    <div class="row">
        <div class="col-3 col-md-3 col-lg-3">
            <div class="form-group m-form__group">
                <label for="task_incharge">Week Range *</label>
                <input type="text" class="form-control" id="week_duration" name="week_duration" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <table class="m-table table table-bordered">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Lots</th>
                        <th>Unit</th>
                        <th>Unit Cost</th>
                        <th>Total Cost</th>
                        <th>10% Retention</th>
                        <th>Previous Accomplishment</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>
</form>