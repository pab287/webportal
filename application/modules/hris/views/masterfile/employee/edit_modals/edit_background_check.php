<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-bg-check"
              method="post" action="<?= base_url("hris/masterfile/update_bg_check"); ?>"
              enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Background Check</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="current_filename" value="<?= $data->doc_filename ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="fileupload_background_check" class="form-control-label">Attachment</label>
                    <span class="btn btn-success fileinput-button btn-sm pull-right">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select file</span>
                        <input type="file" id="fileupload_background_check" name="files" onchange="setFilename(this, '#temp_fileupload')">
                    </span>
                    <p id="temp_fileupload" style='text-overflow: ellipsis; white-space: nowrap; overflow: hidden;' class="form-control m-input m--margin-top-10" disabled="disabled"><?= $data->doc_filename ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>