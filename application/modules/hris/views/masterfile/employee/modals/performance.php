<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add Performance Evaluation</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-performance" method="post" action="<?php echo site_url("hris/masterfile/set_modal_performance"); ?>">
<input type="hidden" name="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <?php if(isset($is_regular) && $is_regular == true): ?>
    <div class="form-group">
            <label for="year" class="form-control-label">Year *</label>
            <div class="col-md-6 m--padding-left-0">
                <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input id="year" name="year" type="text" class="form-control m-input" autocomplete="off" data-validation="required" data-inputmask="'alias': 'yyyy'" data-mask="" value="<?php echo date("Y"); ?>" />
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="form-group">
		<label for="quarter" class="form-control-label">Quarter *</label>
		<select id="quarter" name="quarter" autocomplete="off" data-validation="required" class="form-control m-input select2">
            <option value="" disabled selected>Select an option</option>
            <?php if(isset($quarter) && $quarter): ?>
            <?php foreach($quarter as $key => $value): ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php endforeach; ?>
            <?php endif; ?>
        </select>
	</div>
    <div class="form-group">
		<label" class="form-control-label">Date</label>
		<div class="input-group m--margin-top-5">
            <span class="input-group-addon">
                <i class="la la-calendar"></i>
            </span>
            <p id="temp_range" class="form-control m-input" disabled="disabled">&nbsp;</p>
            <input id="range" type="hidden" name="range" />
        </div>
	</div>
    <div class="form-group">
		<label for="fileupload_performance" class="form-control-label">Attachment</label>
        <span class="btn btn-success fileinput-button btn-sm pull-right">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Select file</span>
            <input type="file" id="fileupload_performance" name="files">
            <input type="hidden" id="performance_attachment" name="performance_attachment" />
        </span>
        <p id="temp_fileupload" class="form-control m-input m--margin-top-10" disabled="disabled">&nbsp;</p>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>