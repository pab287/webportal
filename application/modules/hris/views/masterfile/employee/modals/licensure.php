<div class="modal-header">
    <!-- <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Licensure Exam and Certification</h5> -->
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Licenses and Certifications</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-licensure" method="post" action="<?php echo site_url("hris/masterfile/set_modal_licensure"); ?>">
<input type="hidden" name="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="license_type" class="form-control-label">License or Certificate Type *</label>
		<!-- <input id="license_type" name="license_type" type="text" maxlength="100" size="100" autocomplete="off" data-validation="required" class="form-control m-input" /> -->
        <select id="license_type" name="license_type" data-validation="required" class="form-control m-input">
            <option value="">Select an Option</option>
            <option value="Certificate">Certificate</option>
        </select>
	</div>
    <div class="form-group cert-name-field d-none">
		
	</div>
    <div class="form-group">
		<label for="exam_place" class="form-control-label">Exam Place *</label>
		<input id="exam_place" name="exam_place" type="text" maxlength="100" size="100" data-validation="required" autocomplete="off" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="rating" class="form-control-label">Rating</label>
		<input id="rating" name="rating" type="text" maxlength="25" size="25" autocomplete="off" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="release_date" class="form-control-label">Release Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="release_date" type="text" name="release_date" maxlength="12" size="12" data-validation="required" autocomplete="off" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="exam_date" class="form-control-label">Exam Date</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="exam_date" type="text" name="exam_date" maxlength="12" size="12" autocomplete="off" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="license_no" class="form-control-label">License No <small style="color: red;">(Type "N/A" if not applicable)</small></label>
		<input id="license_no" name="license_no" type="text" maxlength="50" size="50" autocomplete="off" class="form-control m-input" data-validation="required"/>
	</div>
    <div class="form-group row">
        <label class="col-4 col-form-label">With Expiry Date</label>
        <div class="col-2 p-0">
            <span class="m-switch m-switch--sm m-switch--icon" id="expiry-switch">
                <label>
                    <input type="checkbox" value="1">
                    <span></span>
                </label>
            </span>
        </div>
    </div>
    <div id="with-expiry" class="form-group d-none">
		<label for="license_no" class="form-control-label">License Expiry Date *</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="expiration_date" type="text" name="expiration_date" maxlength="12" size="12" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="fileupload_liscert" class="form-control-label">Attachment</label>
        <span class="btn btn-success fileinput-button btn-sm pull-right">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Select file</span>
            <input type="file" id="fileupload_liscert" name="files" accept=".jpg, .jpeg, .png, .pdf">
            <input type="hidden" id="liscert_attachment" name="liscert_attachment" />
        </span>
        <p id="temp_fileupload" style='text-overflow: ellipsis; white-space: nowrap; overflow: hidden;' class="form-control m-input m--margin-top-10" disabled="disabled">&nbsp;</p>
    </div>
    <div class="form-group">
		<label for="remarks" class="form-control-label">Remarks</label>
        <textarea id="remarks" name="remarks" class="form-control m-input" maxlength="200" rows="3" cols="50" autocomplete="off"></textarea>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>