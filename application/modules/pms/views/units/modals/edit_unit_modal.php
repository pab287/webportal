<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form action="<?= base_url("pms/rates/edit_unit/". $id) ?>" id="frm-edit-unit">
            <input type="hidden" value="<?= $this->security->get_csrf_hash() ?>" name="csrf_token">
            <div class="modal-header">
                <h5 class="modal-title">Edit Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Unit Code</label>
                    <input type="text" class="form-control" name="uom_code" autocomplete="off"
                           data-validation="required" value="<?=$uom_code?>">
                </div>

                <div class="form-group pt-3">
                    <label for="">Description</label>
                    <input type="text" class="form-control" name="uom_desc" autocomplete="off"
                           data-validation="required" value="<?=$uom_desc?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">Save Changes</button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var validateForm = function () {
        $.validate({
            form: "#frm-edit-unit",
            lang: "en",
            scrollToTopOnError: false,
            onSuccess: function (el) {
                const form = $(el);
                const url = form.attr("action");
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    data: form.serialize(),
                    success: function (response) {
                        if (response) {
                            toastr.success("Unit was successfully update.", "Unit Updated.", 10000);
                            const uom_desc = $("input[name='uom_desc']").val();
                            $("#generalSearch").val(uom_desc);

                            dtTblUnits.ajax.reload();
                            form.resetForm();
                            modalContainer.modal("hide");
                        } else {
                            toastr.error("An error occurred.", "Error Updating Unit", 10000);
                        }
                    },
                    error: function (xhr, status, error) {
                        const err = eval("(" + xhr.responseText + ")");
                        toastr.warning(err, "Error Updating Unit", 10000);
                    }
                });
                return false;
            }
        });
    }

    jQuery(document).ready(function () {
        validateForm();
    });
</script>