<div id="add_dialog" class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="frm-create-field-template" action="<?= base_url('crs/save_field_template') ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">CREATE TEMPLATE</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <div class="form-group m-form m-form__group">
                    <label for="">Template Name</label>
                    <input type="text" name="fieldname" class="form-control" autocomplete="off"
                           data-validation="required">
                </div>

                <div class="form-group m-form m-form__group">
                    <label for="">Fields</label>
                    <select name="field[]" class="form-control " multiple="multiple" id="field"
                            data-validation="required"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btnNew">Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>