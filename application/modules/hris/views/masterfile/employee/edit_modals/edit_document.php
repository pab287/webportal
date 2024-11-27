<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form action="<?= base_url('hris/masterfile/update_document') ?>" enctype="multipart/form-data"
              id="employee-data-update-document">
            <!-- <input type="hidden" name="id" value="<?//= $data->id ?>"> -->
            <input type="hidden" name="id" value="<?= $data->docId ?>">
            <input type="hidden" name="current_filename" value="<?= $data->doc_filename ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="modal-header">
                <h5 class="modal-title"><i class="la la-edit mr-2"></i>Edit Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
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
                    <input type="hidden" name="checklist_id" id="checklistId" value="0">
                    <input type="hidden" name="checklist_document_id" id="checklistedId" value="0">
                    <select name="checklist_type" id="checklist_type" data-validation="required" class="form-control m-input select2"></select>
                </div>
                <div class="form-group" id="non-checklist">
                    <label for="doc_type" class="form-control-label">Type *</label>
                    <select id="doc_type" name="doc_type"
                            data-validation="required" class="form-control m-input select2">
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
                    <label for="fileupload_document" class="form-control-label">Attachment</label>
                    <span class="btn btn-success fileinput-button btn-sm pull-right"
                          id="edit-document-select-file">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select file</span>
                        <input type="file" id="fileupload_document" name="files" onchange="setFilename(this, '#temp_file-upload')"
                               accept=".jpg, .jpeg, .png, .doc, .docx, .pdf">
                    </span>
                    <p id="temp_file-upload" class="form-control m-input m--margin-top-10" style='text-overflow: ellipsis; white-space: nowrap; overflow: hidden;' disabled="disabled"><?= $data->doc_filename ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnEdit"><i class="la la-check mr-2"></i>
                    Save
                </button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal"><i class="la la-times mr-2"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>