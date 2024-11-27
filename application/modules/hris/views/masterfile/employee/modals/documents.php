<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-plus mr-2"></i>Add document</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-documents" method="post" action="<?php echo site_url("hris/masterfile/set_modal_documents"); ?>">
<input type="hidden" name="emp_id" id="emp_id" value="<?php echo $id; ?>" />
<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<div class="modal-body">
    <div class="form-group form-row align-items-center">
        <label for="" class="form-control-label col-6 m-0">Pre-Employment Checklist</label>
        <span class="m-switch m-switch--sm m-switch--icon col-4 m-0">
            <label class="m-0">
                <input type="checkbox" id="is-checklist" name="is_checklist" value="1">
                <span></span>
            </label>
        </span>
    </div>
    <div class="form-group" id="checklist" style="display: none">
        <label for="checklist_type" class="form-control-label">Type *</label>
        <input type="hidden" name="checklist_id" id="checklistId">
        <select name="checklist_type" id="checklist_type" data-validation="required" class="form-control m-input select2"></select>
    </div>
    <div class="form-group" id="non-checklist">
		<label for="doc_type" class="form-control-label">Type *</label>
		<select id="doc_type" name="doc_type" data-validation="required" class="form-control m-input select2">
            <option value="">&nbsp;</option>
            <option value="Accountability Form">Accountability Form</option>
            <option value="Benefits">Benefits</option>
            <option value="BIR Forms">BIR Forms</option>
            <option value="Certification">Certification</option>
            <option value="Clearances">Clearances</option>
            <option value="Contract">Contract</option>
            <option value="Employee Data">Employee Data</option>
            <option value="Government Number">Government Number</option>
            <option value="Job Description">Job Description</option>
            <option value="Onboarding">Onboarding</option>
            <option value="Others">Others</option>
            <option value="Physical Exam Results/Records">Physical Exam Results/Records</option>
            <option value="School Records">School Records</option>
            <option value="Separation Documents">Separation Documents</option>
        </select>
	</div>
    <div class="form-group">
    <label for="documentupload" class="form-control-label">Upload</label>
    <div class="form-group m-form__group">
                <div class="custom-file" >
                    <input type="file" name="files" multiple id="documentupload" class="custom-file-input" accept="image/*, .pdf, .doc, .docx,">
                    <input type="hidden" id="path" name="path"/>
                    <input type="hidden" id="filename" name="filename"/>
                    <span class="custom-file-control" id="file_append"></span>
                </div>							
                            </div>
            <div class="form-group m-form__group text-center">
            <div id="picture"></div><br>
            </div>
            <input type="hidden" id="document_names" name="document_names">
		
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
    <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
</div>
</form>