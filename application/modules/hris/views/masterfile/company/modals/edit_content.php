<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><i class="la la-edit mr-2"></i>Edit Company</h5>
    <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
        <span aria-hidden="true">×</span>
    </button>
</div>
<form id="form-edit_company" method="post" action="<?php echo site_url("hris/masterfile/update_modal_company"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo ($data->id) ? $data->id : ""; ?>"/>
    <div class="modal-body">
        <div class="form-group">
            <label for="code" class="form-control-label required">Code</label>
            <input id="code" name="code" type="text" maxlength="10" size="10" autocomplete="off"
                   data-validation="required" class="form-control m-input"
                   value="<?php echo ($data->code) ? $data->code : ""; ?>"/>
        </div>
        <div class="form-group mt-4">
            <label for="description" class="form-control-label required">Company</label>
            <input id="description" name="description" type="text" maxlength="50" size="50" autocomplete="off"
                   data-validation="required" class="form-control m-input"
                   value="<?php echo ($data->description) ? $data->description : ""; ?>"/>
        </div>

        <div class="form-group mt-4">
            <label for="sss_class" class="form-control-label required">SSS Classification</label>
            <select name="sss_class" id="sss_class" class="form-control" data-validation="required">
                <option></option>
                <option value="1" <?= intval($data->sss_class) === 1 ? "selected" : "" ?>>
                    EMPLOYED, SELF-EMPLOYED & ETC.
                </option>
                <option value="2" <?= intval($data->sss_class) === 2 ? "selected" : "" ?>>
                    HOUSEHOLD EMPLOYERS & KASAMBAHAY
                </option>
            </select>
        </div>

        <div class="row">
            <div class="form-group mt-4 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <label for="work_days_in_year" class="form-control-label required">WORK DAYS IN YEAR</label>
                <input id="work_days_in_year" name="work_days_in_year" type="number" autocomplete="off"
                       value="<?= $data->work_days_in_year ?>"
                       min="0" data-validation="required" class="form-control m-input text-right"/>
            </div>
        </div>

        <div class="form-group mt-2">
            <label class="col-form-label form-control-label">
                Email To:
            </label>
                <select id="edit_email_to" name="email_to[]">
                    
                </select>
        </div>

        <div class="form-group mt-2">
            <label class="col-form-label form-control-label">
                CC To:
            </label>
                <select id="edit_cc_to" name="cc_to[]">
                    
                </select>
        </div>
        <div class="form-group mt-2">
            <label class="col-form-label form-control-label">
                BCC To:
            </label>
                <select id="edit_bcc_to" name="bcc_to[]">
                    
                </select>
        </div>

        <div class="form-group">
            <label for="edit-fileupload_logo" class="form-control-label">Attachment Logo</label>
            <span class="btn btn-success fileinput-button btn-sm pull-right btnUpload">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Select file</span>
            <input type="file" id="edit-fileupload_logo" name="files">
            <input type="hidden" id="logo_attachment" name="logo_attachment"
                   value="<?php echo ($data->logo) ? $data->logo : ""; ?>"/>
        </span>
            <p id="temp_fileupload" class="form-control m-input m--margin-top-10"
               disabled="disabled"><?php echo ($data->logo) ? $data->logo : "&nbsp;"; ?></p>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave"><i class="la la-check mr-2"></i>Save</button>
        <button class="btn btn-danger modalClose" data-dismiss="modal"><i class="la la-times mr-2"></i>Cancel</button>
    </div>
</form>

<script type="text/javascript">
    $("#sss_class").select2({
        width: "100%",
        placeholder: "SELECT"
    });
</script>