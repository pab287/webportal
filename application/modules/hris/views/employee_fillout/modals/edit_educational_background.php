<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-educational-background"
              method="post" action="<?php echo site_url("hris/employee_fillout/update_educational_background"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Educational Background</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?= $data->id; ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="educ_level_type" class="form-control-label">Level Type *</label>
                    <select id="educ_level_type" data-validation="required" class="form-control m-input select2" name="educ_level_type">
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
                    <input id="educ_degree" name="educ_degree" type="text" maxlength="200" size="200" autocomplete="off"
                           value="<?= $data->educ_degree ?>"
                           class="form-control m-input"/>
                </div>
                <div class="form-group">
                    <label for="educ_school" class="form-control-label">School *</label>
                    <input id="educ_school" name="educ_school" type="text" maxlength="200" size="200" autocomplete="off" data-validation="required"
                           value="<?= $data->educ_school ?>"
                           class="form-control m-input"/>
                </div>
                <div class="form-group">
                    <label for="educ_honors" class="form-control-label">Honors</label>
                    <input id="educ_honors" name="educ_honors" type="text" maxlength="100" size="100" autocomplete="off"
                           value="<?= $data->educ_honors ?>"
                           class="form-control m-input"/>
                </div>
                <div class="form-group">
                    <label for="educ_from" class="form-control-label">From Year</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="educ_from" type="text" name="educ_from" maxlength="4" size="4" autocomplete="off"
                               data-validation="required" value="<?= $data->educ_from ?>"
                               class="form-control m-input date"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="educ_to" class="form-control-label">To Year</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="educ_to" type="text" name="educ_to" maxlength="4" size="4" autocomplete="off" data-validation="required"
                               value="<?= $data->educ_to ?>"
                               class="form-control m-input date"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
            </div>
        </form>
    </div>
</div>