<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Licensure Exam and Certification</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-licensure" method="post" action="<?php echo site_url("hris/employee_fillout/set_modal_licensure"); ?>">
<input type="hidden" name="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="license_type" class="form-control-label">License Type *</label>
		<input id="license_type" name="license_type" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="exam_place" class="form-control-label">Exam Place</label>
		<input id="exam_place" name="exam_place" type="text" maxlength="100" size="100" autocomplete="off" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="rating" class="form-control-label">Rating</label>
		<input id="rating" name="rating" type="text" maxlength="25" size="25" autocomplete="off" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="release_date" class="form-control-label">Release Date</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="release_date" type="text" name="release_date" maxlength="12" size="12" autocomplete="off" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="exam_date" class="form-control-label">Exam Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="exam_date" type="text" name="exam_date" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="license_no" class="form-control-label">License No *</label>
		<input id="license_no" name="license_no" type="text" maxlength="50" size="50" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>