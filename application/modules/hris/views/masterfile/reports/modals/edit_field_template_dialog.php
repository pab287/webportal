<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form id="frm-edit-field-template" action="<?= base_url('crs/update_field_template') ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">EDIT TEMPLATE</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" value="<?= $data->id ?>">

                <div class="form-group m-form m-form__group">
                    <label for="">Template Name</label>
                    <input type="text" name="description" class="form-control" autocomplete="off"
                           data-validation="required" value="<?= $data->description ?>">
                </div>

                <div class="form-group m-form m-form__group">
                    <label for="">Fields</label>
                    <select name="field[]" class="form-control " multiple="multiple" id="field"
                            data-validation="required"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btnNew">Save Changes</button>
                <button class="btn btn-danger modalClose" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>