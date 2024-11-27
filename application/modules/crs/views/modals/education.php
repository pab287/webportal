<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Educational Background</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-education" method="post" action="<?php echo site_url("crs/online_registration/set_modal_education"); ?>">
<input type="hidden" name="applicant_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group">
		<label for="educ_level_type" class="form-control-label">Level Type *</label>
		<select id="educ_level_type" name="educ_level_type" data-validation="required">
            <option value="">&nbsp;</option>
            <option value="ELEMENTARY">Elementary</option>
            <option value="HIGH SCHOOL">High School</option>
            <option value="SENIOR HIGH SCHOOL">Senior High School</option>
            <option value="COLLEGE">College</option>
            <option value="MASTERAL">Masteral</option>
            <option value="DOCTORATE">Doctorate</option>
            <option value="VOCATIONAL">Vocational</option>
            <option value="OTHERS">Others</option>
        </select>
	</div>
    <div class="form-group">
		<label for="educ_degree" class="form-control-label">Degree</label>
		<input id="educ_degree" name="educ_degree" type="text" maxlength="200" size="200" autocomplete="off" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="educ_school" class="form-control-label">School *</label>
		<input id="educ_school" name="educ_school" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="educ_honors" class="form-control-label">Honors</label>
		<input id="educ_honors" name="educ_honors" type="text" maxlength="100" size="100" autocomplete="off" class="form-control m-input" />
	</div>
    <div class="form-group">
		<label for="educ_from" class="form-control-label">From Year</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="educ_from" type="text" name="educ_from" maxlength="4" size="4" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
    <div class="form-group">
		<label for="educ_to" class="form-control-label">To Year</label>
		<div class="input-group">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <input id="educ_to" type="text" name="educ_to" maxlength="4" size="4" autocomplete="off" data-validation="required" class="form-control m-input" />
        </div>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>