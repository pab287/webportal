<script>
    ClassicEditor
        .create( document.querySelector('#job_description') )
        .catch( error => {
            console.error( error );
        } );
</script>
<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content">
        <form id="employee-update-job-description" method="post"
              action="<?= base_url("hris/masterfile/update_employee_job_description"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Update Job Description</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <input type="hidden" name="id" value="<?= empty($data) ? "" : $data->id ?>"/>
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-control-label">Position</label>
                    <p class="form-control m-input"><?= empty($data) ? "" : $data->name ?></p>
                </div>
                <div class="form-group">
                    <label for="job_description" class="form-control-label">Job Description *</label>
                    <textarea id="job_description" name="job_desc" data-validation="required" class="form-control m-input"
                              style="min-height: 350px; resize: vertical;"><?= empty($data) ? "" : $data->job_desc ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit"
                        class="btn btn-primary btnSave" <?= empty($data) ? "disabled" : "" ?>><i class="la la-check mr-2"></i>Save
                </button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>