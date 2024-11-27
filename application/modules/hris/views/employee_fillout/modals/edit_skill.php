<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-update-skill" method="post" action="<?php echo site_url("hris/employee_fillout/update_skill"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Skill</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data->id ?>">

            <div class="modal-body">
                <div class="form-group">
                        <label for="">Skill</label>
                        <input type="text" class="form-control"
                                data-validation="required" name="skills"
                                value="<?= $data->skills ?>"
                                autocomplete="off">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>