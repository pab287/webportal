<div id="AddFileModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="editNewFileForm" enctype="multipart/form-data">
        <div class="modal-header">
            <h5 class="modal-title">Add File</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <span class="btn btn-success fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Select file</span>
                <input type="file" id="edit_fileupload" name="addfiles" multiple>
            </span>
            <div id="editprogress" class="progress mt-2">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="edit_files" class="files"></div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success btnSave">Save changes</button>
            <button id="editfileClose" type="button" class="btn btn-danger btnCancel">Close</button>
        </div>
        </form>
    </div>
  </div>
</div>