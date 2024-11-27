<?php
    $frpm = new DateTime($data->org_from);
    $from = $frpm->format("Y");
    $to = new DateTime($data->org_to);
    $to = $frpm->format("Y");
?>

<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="employee-data-update-organization" method="post" action="<?= base_url("hris/masterfile/update_organization"); ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Organization</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <input type="hidden" name="id" value="<?= $data->id ?>">
            <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">

            <div class="modal-body">
                <div class="form-group">
                    <label for="org_institution" class="form-control-label">Institution
                        <span class="text-danger">*</span>
                    </label>
                    <input id="org_institution" name="org_institution" type="text" maxlength="100" size="100" autocomplete="off"
                           data-validation="required" class="form-control m-input" value="<?= $data->org_institution ?>"/>
                </div>
                <div class="form-group">
                    <label for="org_membership_title" class="form-control-label">Membership Title
                        <span class="text-danger">*</span>
                    </label>
                    <input id="org_membership_title" name="org_membership_title" type="text" maxlength="100" size="100" autocomplete="off"
                           data-validation="required" class="form-control m-input" value="<?= $data->org_membership_title ?>"/>
                </div>
                <div class="form-group">
                    <label for="org_from" class="form-control-label">From Year
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="org_from" type="text" name="org_from" maxlength="12" size="12" autocomplete="off" data-validation="required"
                               class="form-control m-input date-year" value="<?= $from ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="org_to" class="form-control-label">To Year
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                        <input id="org_to" type="text" name="org_to" maxlength="12" size="12" autocomplete="off" data-validation="required"
                               class="form-control m-input date-year" value="<?= $to ?>"/>
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